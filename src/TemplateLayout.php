<?php

namespace Mphpmaster\NovaInfoCard;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Nova\Nova;
use Mphpmaster\NovaInfoCard\Abstracts\Model;
use Mphpmaster\NovaInfoCard\Interfaces\IInfoCardSource;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

/**
 * @property $resourceClassOrUriKey
 */
class TemplateLayout extends Model implements HasMedia, IInfoCardSource
{
    use HasFactory;
    use HasTranslations;

    /**
     * @var string[]
     */
    public $translatable = [ 'main_title', 'sub_title', 'description', 'meta_data' ];

    /**
     * @var string[]
     */
    protected $fillable = [
        "page_name",
        "media_type",
        "video_link",
        "main_title",
        "sub_title",
        "description",
        "meta_data",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'page_name' => 'string',
        'media_type' => 'string',
        'video_link' => 'string',
        'main_title' => 'string',
        'sub_title' => 'string',
        'description' => 'string',
        'meta_data' => 'string',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(static::getMediaName('images'));
        $this->addMediaCollection(static::getMediaName('image'))
             ->singleFile();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function horse_men()
    {
        return $this->hasMany(HorseMen::class, 'horse_men_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sponsors()
    {
        return $this->hasMany(Sponsor::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param                                       $page_name
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPageName(Builder $builder, $page_name)
    {
        return $builder->whereIn('page_name', (array) $page_name);
    }

    /**
     * Find by uriKey
     *
     * @param array|string $page_name
     * @param array|string $columns
     *
     * @return self|\Illuminate\Database\Eloquent\Model
     */
    public static function forPageName($page_name, $columns = [ '*' ])
    {
        return static::byPageName($page_name)->limit(1)->get($columns)->first() ?? TemplateLayout::createFor($page_name, $columns);
    }

    /**
     * Find by resource::uriKey
     *
     * @param string|Closure $resource
     * @param array|string   $columns
     *
     * @return self|\Illuminate\Database\Eloquent\Builder
     */
    public static function forResource($resource, $columns = [ '*' ])
    {
        $resource = value($resource);
        $resource_uri_key = class_exists($resource) && method_exists($resource, 'uriKey') ? $resource::uriKey() : $resource;
        $result = static::forPageName($resource_uri_key, $columns);
        $result->resourceClassOrUriKey = $resource;

        return $result;
    }

    /**
     * Chose static::forResource or static::forPageName
     *
     * @param string|Closure $resource
     * @param array|string   $columns
     *
     * @return \App\Models\TemplateLayout|\Illuminate\Database\Eloquent\Builder
     */
    public static function forAny($resource, $columns = [ '*' ])
    {
        return class_exists($resource = value($resource)) && method_exists($resource, 'uriKey') ?
            static::forResource($resource, $columns) :
            static::forPageName($resource, $columns);
    }

    /**
     * @param array|mixed $columns
     *
     * @return array
     */
    public function getInfoCardLines($columns = [ 'main_title', 'sub_title', 'description' ]): array
    {
        return $this->only($columns);
    }

    /**
     * @return string|null
     */
    public function getInfoCardImage(): ?string
    {
        return $this->media_type === 'image' ?
            $this->getFirstMediaUrl(static::getMediaName('image')) :
            url("images/video.png");
    }

    /**
     * @return string|null
     */
    public function getInfoCardImageClass(): ?string
    {
        return 'absolute h-16 w-1/3 opacity-20 ' .
            (currentLocale() === 'ar' ? 'left-0 rounded-br-full' : 'right-0 rounded-bl-full');
    }

    /**
     * @param string|Closure|null $resource
     *
     * @return string
     */
    public function getEditResourceUrl($resource = null): ?string
    {
        $resource = value($resource) ?? $this->resourceClassOrUriKey;
        /** @var \Laravel\Nova\Resource $resource_class */
        $resource_class = Nova::resourceForKey($resource) ?: Nova::resourceForModel($resource) ?: $resource;

        return !$resource ? $resource : static::editUrlForResource($resource_class);
    }

    /**
     * @param string|null $key
     * @param array       $replace
     * @param string|null $locale
     *
     * @return string|array|null
     */
    public function getInfoCardTitle(?string $key = null, array $replace = [], ?string $locale = null)
    {
        return __($key ?? 'common.template_layout', $replace, $locale);
    }

    /**
     * @param $page_name
     *
     * @return string
     */
    public static function editUrlFor($page_name): string
    {
        return "/resources/" . \App\Nova\TemplateLayout::uriKey() . "/" . static::forPageName($page_name)->id . "/edit";
    }

    /**
     * @param $resource
     *
     * @return string
     */
    public static function editUrlForResource($resource): string
    {
        /** @var \Laravel\Nova\Resource $resource_class */
        $resource_class = Nova::resourceForKey($resource) ?: Nova::resourceForModel($resource) ?: $resource;

        return "/resources/" . \App\Nova\TemplateLayout::uriKey() . "/" . static::forResource($resource_class, 'id')->id . "/edit";
    }

    /**
     * @param array|string|Closure $page_name
     * @param array|string         $columns
     *
     * @return \App\Models\TemplateLayout|\Illuminate\Database\Eloquent\Model
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    public function createFor($page_name, $columns = [ '*' ])
    {
        $page_name = value($page_name);
        $attributes = is_array($page_name) && isAssocArray($page_name) ? $page_name : [
            'page_name' => $page_name,
            'media_type' => 'image',
        ];
        $en = $ar = \Str::headline(data_get($attributes, 'page_name', ''));
        $attributes[ 'main_title' ] = $attributes[ 'sub_title' ] = $attributes[ 'description_title' ] = compact('ar', 'en');

        $model = static::make($attributes)
                       ->setTranslations('main_title', compact('ar', 'en'))
                       ->setTranslations('sub_title', compact('ar', 'en'))
                       ->setTranslations('description', compact('ar', 'en'));
        $model->copyMedia(resource_path("images/no-image.png"))->toMediaCollection(static::getMediaName('image'));

        return tap($model, function($m) {
            $m->save();
        })->refresh();
    }

    // region: shortcuts

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return mixed
     */
    public function scopeForVisits(Builder $builder)
    {
        return $this->forPageName(\App\Nova\Visit::uriKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return mixed
     */
    public function scopeForNews(Builder $builder)
    {
        return $this->forPageName(\App\Nova\News::uriKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return mixed
     */
    public function scopeForCups(Builder $builder)
    {
        return $this->forPageName(\App\Nova\Cup::uriKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return mixed
     */
    public function scopeForHorseMen(Builder $builder)
    {
        return $this->forPageName(\App\Nova\HorseMen::uriKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return mixed
     */
    public function scopeForWelcomeMediaPage(Builder $builder)
    {
        return $this->forPageName(\App\Nova\WelcomeMediaPage::uriKey());
    }

    // endregion: shortcuts
}

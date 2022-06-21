<?php

namespace Mphpmaster\NovaInfoCard\Interfaces;

/**
 *
 */
interface IInfoCardSource
{

    /**
     * @param array|string $page_name
     * @param array|string $columns
     *
     * @return self|\Illuminate\Database\Eloquent\Model
     */
    public static function forPageName($page_name, $columns = [ '*' ]);

    /**
     * @param string|Closure $resource
     * @param array|string   $columns
     *
     * @return self|\Illuminate\Database\Eloquent\Builder
     */
    public static function forResource($resource, $columns = [ '*' ]);

    /**
     * Chose static::forResource or static::forPageName
     *
     * @param string|Closure $resource
     * @param array|string   $columns
     *
     * @return self|\Illuminate\Database\Eloquent\Builder
     */
    public static function forAny($resource, $columns = [ '*' ]);

    /**
     * @param array|mixed $columns
     *
     * @return array
     */
    public function getInfoCardLines($columns = [ 'main_title', 'sub_title', 'description' ]): array;

    /**
     * @return string|null
     */
    public function getInfoCardImage(): ?string;

    /**
     * @return string|null
     */
    public function getInfoCardImageClass(): ?string;

    /**
    /**
     * @param string|Closure|null $resource
     *
     * @return string
     */
    public function getEditResourceUrl($resource = null): ?string;

    /**
     * @param string|null $key
     * @param array       $replace
     * @param string|null $locale
     *
     * @return string|array|null
     */
    public function getInfoCardTitle(?string $key = null, array $replace = [], ?string $locale = null);
}

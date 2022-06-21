<?php

namespace Mphpmaster\NovaInfoCard;

use Laravel\Nova\Makeable;

/**
 *
 */
class InfoOnClickEvent
{
    use Makeable;

    public string $type = 'redirect';
    public ?string $url;

    /**
     * @param $url
     * @param $type
     */
    public function __construct($url, $type = 'redirect')
    {
        $this->setType($type)
             ->setUrl($url);
    }

    public static function redirectTo($url): InfoOnClickEvent
    {
        return static::make(value($url), 'redirect');
    }

    public static function none(): InfoOnClickEvent
    {
        return static::make('', '');
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     *
     * @return self
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function buildData()
    {
        switch( $this->getType() ) {
            case 'redirect':
                return [
                    'url' => $this->getUrl(),
                ];
        }

        return [

        ];
    }

    public function toArray(?string $key = null): array
    {
        $key ??= class_basename(static::class);

        return [
            $key => [
                'type' => $this->getType(),
                'data' => $this->buildData(),
            ],
        ];
    }
}

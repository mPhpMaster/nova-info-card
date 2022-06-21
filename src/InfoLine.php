<?php

namespace Mphpmaster\NovaInfoCard;

use Laravel\Nova\Makeable;

/**
 *
 */
class InfoLine
{
    use Makeable;

    public string $text = '';
    public string $class = 'py-1';

    /**
     * @param $text
     * @param $class
     */
    public function __construct($text, $class = 'py-1')
    {
        $this
            ->setText($text)
            ->setClass($class);
    }

    /**
     * @param $text
     * @param $class
     *
     * @return static
     */
    public static function makeDescription($text, $class = 'py-1 text-70 text-xs'): self
    {
        return static::make($text, $class);
    }

    /**
     * @param $text
     * @param $class
     *
     * @return static
     */
    public static function makeSubTitle($text, $class = 'py-1 text-80 text-xs'): self
    {
        return static::make($text, $class);
    }

    /**
     * @param $text
     * @param $class
     *
     * @return static
     */
    public static function makeTitle($text, $class = 'pt-3 text-90'): self
    {
        return static::make($text, $class);
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return self
     */
    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     *
     * @return self
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'text' => $this->getText(),
            'class' => $this->getClass(),
        ];
    }

}

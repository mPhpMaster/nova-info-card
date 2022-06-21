<?php

namespace Mphpmaster\NovaInfoCard;

use Laravel\Nova\Card;
use Laravel\Nova\Makeable;

class InfoCard extends Card
{
    use Makeable;

    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = '1/3';

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'info-card';
    }

    public function __construct($title = null, $component = null)
    {
        parent::__construct($component);

        $title && $this->title($title);
        $this->onClick(InfoOnClickEvent::none());
    }

    public function setMeta($meta): self
    {
        return $this->withMeta($meta);
    }

    public function onClick(InfoOnClickEvent $event): self
    {
        return $this->withMeta($event->toArray('onclick'));
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function onClickRedirectTo($url): self
    {
        return $this->onClick(InfoOnClickEvent::redirectTo($url));
    }

    public function title($value): self
    {
        return $this->withMeta([
                                   'title' => value($value),
                               ]);
    }

    public function image($image): self
    {
        $image = value($image);

        return $this->withMeta(compact('image'));
    }

    public function imageClass($image_class): self
    {
        $image_class = value($image_class);

        return $this->withMeta(compact('image_class'));
    }

    public function lines(array $lines): self
    {
        $this->meta[ 'lines' ] = [];

        // array_prepend($lines, $this->meta[ 'lines' ]);

        return $this->addLine(...$lines);
    }

    public function addLine(...$line): self
    {
        foreach( $line as $index => &$item ) {
            $item = $item instanceof InfoLine ? $item->toArray() : $item;
            $_item = !is_array($item) ? [ 'text' => $item ] : $item;
            $_item[ 'text' ] ??= head($_item);
            $_item[ 'class' ] ??= '';
            $item = $_item;
        }

        $this->meta[ 'lines' ] ??= [];
        $lines = data_get($this->meta, 'lines', []);
        array_push($lines, ...$line);

        return $this->withMeta(compact('lines'));
    }

    /**
     * @param array $lines
     * @param bool  $showTitle
     * @param bool  $showSubTitle
     * @param bool  $showDescription
     *
     * @return $this
     */
    public function linesFrom(array $lines, bool $showTitle = true, bool $showSubTitle = true, bool $showDescription = false): self
    {
        $main_title = array_shift($lines);
        $sub_title = array_shift($lines);
        $description = array_shift($lines);
        $results = [];
        $showTitle && array_push($results, InfoLine::makeTitle($main_title));
        $showSubTitle && array_push($results, InfoLine::makeSubTitle($sub_title));
        $showDescription && array_push($results, InfoLine::makeDescription($description));

        // return $results;
        return $this->lines($results);
    }

    /**
     * @param \Mphpmaster\NovaInfoCard\Interfaces\IInfoCardSource $source
     *
     * @return self
     */
    public static function viaInfoCardSource(Interfaces\IInfoCardSource $source): self
    {
        return InfoCard::make()
                       ->title($source->getInfoCardTitle())
                       ->image($source->getInfoCardImage())
                       ->imageClass($source->getInfoCardImageClass())
                       ->linesFrom($source->getInfoCardLines())
                       ->onClickRedirectTo($source->getEditResourceUrl());

    }
}

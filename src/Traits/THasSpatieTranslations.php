<?php

namespace Mphpmaster\NovaInfoCard\Traits;
use Spatie\Translatable\HasTranslations as BaseHasTranslations;

trait THasSpatieTranslations
{
    use BaseHasTranslations;
    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = parent::toArray();
        foreach ($this->getTranslatableAttributes() as $field) {
            $attributes[$field] = $this->getTranslation($field, \App::getLocale());
        }
        return $attributes;
    }
}
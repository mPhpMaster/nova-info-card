<?php

use Illuminate\Database\Eloquent\Model;

if ( !function_exists('getTrans') ) {
    /**
     * Translate the given message or return default.
     *
     * @param string|null $key
     * @param array       $replace
     * @param string|null $locale
     *
     * @return string|array|null
     */
    function getTrans($key = null, $default = null, $replace = [], $locale = null)
    {
        $key = value($key);
        $return = __($key, $replace, $locale);

        return $return === $key ? value($default) : $return;
    }
}

if( !function_exists('hasTrait') ) {
    /**
     * Check if given class has trait.
     *
     * @param mixed  $class     <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $traitName <p>
     *                          Trait name to check
     *                          </p>
     *
     * @return bool
     */
    function hasTrait($class, $traitName)
    {
        try {
            $traitName = str_contains($traitName, "\\") ? class_basename($traitName) : $traitName;

            $hasTraitRC = new ReflectionClass($class);
            $hasTrait = collect($hasTraitRC->getTraitNames())->map(function($name) use ($traitName) {
                    $name = str_contains($name, "\\") ? class_basename($name) : $name;

                    return $name == $traitName;
                })->filter()->count() > 0;
        } catch(ReflectionException $exception) {
            $hasTrait = false;
        } catch(Exception $exception) {
            // dd($exception->getMessage());
            $hasTrait = false;
        }

        return $hasTrait;
    }
}

if( !function_exists('localeWrap') ) {
    /**
     * Return the given value wrapped in array with locales as key.
     *
     * @param mixed      $value
     * @param array|null $locales
     *
     * @return array
     */
    function localeWrap($value = null, ?array $locales = null): array
    {
        $locales ??= array_keys(config('nova.locales'));
        $_attributes = is_array($value) ? $value : [];
        foreach( $locales as $locale ) {
            $_attributes[ $locale ] = $_attributes[ $locale ] ?? $value;
        }

        return $_attributes;
    }
}
if ( !function_exists('isModel') ) {
    /**
     * Determine if a given object is inherit Model class.
     *
     * @param object $object
     *
     * @return bool
     */
    function isModel($object)
    {
        try {
            return ($object instanceof Model) || is_a($object, Model::class) || is_subclass_of($object, Model::class);
        } catch (Exception $exception) {

        }

        return false;
    }
}
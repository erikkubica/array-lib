<?php

namespace ErikKubica\ArrayLib;

class ArrayUtil
{
    /**
     *
     * @param array $array Array to dig into
     * @param string $path Path to the target property
     * @param string $pathSeparator
     * @param mixed|null $default Value returned if the property does not exist
     * @param array|null $filters Filters for each path level, indexed by path level, example: "0.children.0.name" parent/root level is 0, children level is 1, name level is 2
     *
     * @return mixed
     */
    public static function getValueFromPath(
        array  $array,
        string $path,
        string $pathSeparator = '.',
        mixed  $default = null,
        array  $filters = null
    ): mixed
    {
        if (empty($path)) {
            return $array;
        }

        $keys = explode($pathSeparator, $path);

        foreach ($keys as $currentLevel => $key) {
            if (is_array($array) && is_array($filters) && isset($filters[$currentLevel]) && is_callable($filters[$currentLevel])) {
                $isAssoc = array_keys($array) !== range(0, count($array) - 1);
                $array = array_filter($array, $filters[$currentLevel]);

                if (!$isAssoc) {
                    $array = array_values($array);
                }
            }

            if (is_array($array) && array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return $default;
            }

        }

        return $array;
    }

    public static function createPathFilter(
        string     $path,
        mixed      $value,
        Comparison $compare = Comparison::EQ,
        string     $pathSeparator = '.',
    ): callable
    {
        return function ($object) use ($path, $value, $compare, $pathSeparator) {

            if (!is_array($object)) {
                return false;
            }

            $pathValue = self::getValueFromPath($object, $path, $pathSeparator);

            return match ($compare) {
                Comparison::EQ => $pathValue === $value,
                Comparison::NE => $pathValue !== $value,
                Comparison::GE => $pathValue >= $value,
                Comparison::GT => $pathValue > $value,
                Comparison::LE => $pathValue <= $value,
                Comparison::LT => $pathValue < $value,
            };
        };
    }
}

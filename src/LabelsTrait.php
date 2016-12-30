<?php
namespace MaDnh\LaravelModelLabels;

trait LabelsTrait
{
    public static function label($field)
    {
        if (self::useLocalLabels($field)) {
            $label = static::$labels[$field];

            return is_callable($label) ? call_user_func($label, $field) : strval($label);
        }


        return trans(self::getLabelLocalePath($field));
    }

    public static function labels()
    {
        $fields = func_num_args() ? array_flatten(func_get_args()) : true;
        $use_local = self::useLocalLabels();

        $locale_fields = trans(self::getLabelLocalePath('field'));

        $labels = array_merge($use_local ? self::$labels : [], is_array($locale_fields) ? $locale_fields : []);

        if (is_array($fields)) {
            $labels = array_merge(array_combine($fields, $fields), array_only($labels, $fields));
        }

        $result = [];
        foreach ($labels as $field => $label) {
            $result[$field] = is_callable($label) ? call_user_func($label, $field) : strval($label);
        }

        return $result;
    }

    protected static function useLocalLabels($field = null)
    {
        if (property_exists(static::class, 'labels') && is_array(static::$labels)) {
            if ($field) {
                return array_key_exists($field, static::$labels);
            }

            return !empty(static::$labels);
        }

        return false;
    }

    protected static function getLabelLocalePath($sub_path = null)
    {
        $path = 'model_' . snake_case(class_basename(static::class));

        if ($sub_path) {
            $path .= '.' . $sub_path;
        }

        return $path;
    }
}
<?php
namespace MaDnh\LaravelModelLabels;

trait LabelsTrait
{
    protected static $labels_trans_map = [
        'id' => 'ID'
    ];
    public static $label_cached = [];

    public static function label($field)
    {
        if (array_key_exists($field, static::$label_cached)) {
            return static::$label_cached[$field];
        }
        $label_i18n_path = self::getLabelI18nPath('field.' . $field);
        $trans_result = trans($label_i18n_path);

        if ($trans_result != $label_i18n_path) {
            $label = $trans_result;
        } else if (property_exists(static::class, 'labels') && is_array(static::$labels) && array_key_exists($field, static::$labels)) {
            $label = static::$labels[$field];
            $label = is_callable($label) ? call_user_func($label, $field) : strval($label);
        } else {
            $field_lower = strtolower($field);

            if (array_key_exists($field_lower, static::$labels_trans_map)) {
                $label = static::$labels_trans_map[$field_lower];
            } else {
                $label = static::makeLabelManual($field);
            }
        }

        static::$label_cached[$field] = $label;

        return $label;
    }

    /**
     * @param string $field
     * @return string
     */
    protected static function makeLabelManual($field)
    {
        return title_case(str_replace('_', ' ', strtolower($field)));
    }

    public static function labels()
    {
        $fields = func_num_args() ? array_flatten(func_get_args()) : true;
        $use_local = property_exists(static::class, 'labels') && is_array(static::$labels) && !empty(static::$labels);

        $result = static::$label_cached;

        if (is_array($fields) && !count(array_diff($fields, array_keys($result)))) {
            return array_only($result, $fields);
        }

        /**
         * Merge from i18n
         */
        $label_i18n_path = self::getLabelI18nPath('field');
        $i18n_fields = trans($label_i18n_path);
        if (is_array($i18n_fields) && $label_i18n_path !== $i18n_fields) {
            foreach ($i18n_fields as $i18n_key => $i18n_label) {
                $result[$i18n_key] = $i18n_label;
            }
        }

        /**
         * Merge from local
         */
        if ($use_local) {
            foreach (static::$labels as $local_field => $local_label) {
                if (!array_key_exists($local_field, $result)) {
                    $result[$local_field] = is_callable($local_label) ? call_user_func($local_label, $local_field) : strval($local_label);
                }
            }
        }

        /**
         * Make label for missing fields
         */
        if (is_array($fields)) {
            foreach ($fields as $field) {
                if (array_key_exists($field, $result)) {
                    $result[$field] = static::makeLabelManual($field);
                }
            }
        }

        static::$label_cached = $result;

        if (is_array($fields)) {
            return array_only($result, $fields);
        }

        return $result;
    }

    protected static function getLabelI18nPath($sub_path = null)
    {
        $path = 'model_' . snake_case(class_basename(static::class));

        if ($sub_path) {
            $path .= '.' . $sub_path;
        }

        return $path;
    }
}
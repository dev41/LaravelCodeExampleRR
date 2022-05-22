<?php

namespace App\Formatters;

class Formatter
{
    public static function getTooltipsFromMultipleParams($params)
    {
        return array_map(function ($item) {
            return [
                'name' => $item['name'],
                'tooltip' => $item['value'],
            ];
        }, $params);
    }

    public static function getNameAndIconFromMultipleParams($params)
    {
        return array_map(function ($item) {
            return [
                'name' => $item['name'],
                'icon' => $item['icon'],
            ];
        }, $params);
    }

    public static function addParamToArrayIfValueNotEmpty(array &$arr, string $newKey, array $param)
    {
        if (!empty($param['value'])) {
            $arr[$newKey] = $param;
        }
    }

    public static function formatValue($data, $key, $entity, $mulIndex = null)
    {
        $element = $entity::PARAMS[$key];

        if (empty($data[$key]['param_value']) && empty($data[$key][$mulIndex]['param_value'])) {
            return [
                'name' => '',
                'icon' => '',
                'value' => '',
                'tooltip' => '',
            ];
        }

        if ($mulIndex === null) {
            return [
                'name' => $element['name'],
                'icon' => $element['values'][$data[$key]['param_value']]['icon'],
                'value' => $element['values'][$data[$key]['param_value']]['name'],
                'tooltip' => isset($data[$key]['element_value'])
                    ? $data[$key]['element_value']
                    : '',
            ];
        } else {
            return [
                'name' => $element['name'],
                'icon' => $element['values'][$data[$key][$mulIndex]['param_value']]['icon'],
                'value' => $element['values'][$data[$key][$mulIndex]['param_value']]['name'],
                'tooltip' => isset($data[$key][$mulIndex]['element_value'])
                    ? $data[$key][$mulIndex]['element_value']
                    : '',
            ];
        }
    }

    public static function formatValue2($data, $key, $entity, $mulIndex = null)
    {
        $element = $entity::PARAMS[$key];

        if (
            ($mulIndex && (!isset($data[$key][$mulIndex]['param_value']) || !isset($element['values'][$data[$key][$mulIndex]['param_value']]))) ||
            (empty($data[$key]['param_value']) && empty($data[$key][$mulIndex]['param_value']))
        ) {
            return false;
        }

        if ($mulIndex === null) {
            return [
                'name' => $element['values'][$data[$key]['param_value']]['name'],
                'icon' => $element['values'][$data[$key]['param_value']]['icon'],
                'value' => isset($data[$key]['element_value'])
                    ? $data[$key]['element_value']
                    : '',
                'tooltip' => '',
            ];
        } else {
            return [
                'name' => $element['values'][$data[$key][$mulIndex]['param_value']]['name'],
                'icon' => $element['values'][$data[$key][$mulIndex]['param_value']]['icon'],
                'value' => isset($data[$key][$mulIndex]['element_value'])
                    ? $data[$key][$mulIndex]['element_value']
                    : '',
                'tooltip' => '',
            ];
        }
    }


    public static function formatMultiple($data, $key, $entity)
    {
        if (!isset($data[$key])) {
            return [];
        }

        $return = [];

        foreach ($data[$key] as $n => $m) {
            $formatValue = self::formatValue2($data, $key, $entity, $n);
            if ($formatValue === false) {
                continue;
            }
            $return[] = $formatValue;
        }

        return $return;
    }

    public static function formatElement($data, $key, $entity)
    {
        $element = $entity::PROPERTIES[$key];

        return [
            'name' => $element['name'],
            'icon' => $element['icon'],
            'value' => $data['el'][$key] ?? '',
            'tooltip' => '',
        ];
    }

    public static function getElementValue($data, $key)
    {
        return $data['el'][$key] ?? '';
    }
}

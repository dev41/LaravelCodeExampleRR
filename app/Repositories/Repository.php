<?php

namespace App\Repositories;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Repository
{
    const DATETIME_RESPONSE_FORMAT = '%Y-%m-%dT%T.000Z';

    public static function getSelectDateTimeColumn(string $name, string $asName = null): Expression
    {
        $template = 'DATE_FORMAT({name}, "' . self::DATETIME_RESPONSE_FORMAT . '") as {asName}';

        if (!$asName) {
            $nameParts = explode('.', $name);
            $asName = end($nameParts);
        }

        return DB::raw(strtr($template, [
            '{name}' => $name,
            '{asName}' => $asName,
        ]));
    }

    public static function selectDataToArray($data): array
    {
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }

        return array_map(function ($value) {
            return (array) $value;
        }, $data);
    }

    public static function getRealQuery($query, $dumpIt = false)
    {
        $params = array_map(function ($item) {
            return "'{$item}'";
        }, $query->getBindings());
        return str_replace_array('?', $params, $query->toSql());
    }

    public static function combineEntityWithParams($entity, $params, $availableParams, $paramsConfig)
    {
        $paramIds = array_flip($availableParams);
        $result = ['el' => (array) $entity];

        foreach ($params as $param) {
            if (!in_array($param->param_id, $paramIds)) {
                continue;
            }

            $paramName = $availableParams[$param->param_id];
            $paramConfig = $paramsConfig[$paramName];

            if ($paramConfig['type'] === 'multiple') {

                if (empty($result[$paramName])) {
                    $result[$paramName] = [];
                }

                $result[$paramName][] = [
                    'param_value' => $param->param_value,
                    'element_value' => $param->element_value,
                ];

            } else {
                $result[$paramName] = [
                    'param_value' => $param->param_value,
                    'element_value' => $param->element_value,
                ];
            }
        }

        return $result;
    }

}

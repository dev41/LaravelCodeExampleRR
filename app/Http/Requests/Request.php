<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class Request
 * @package App\Http\Requests
 */
class Request extends FormRequest
{
    /**
     * @var int
     */
    public $failedValidationResponseStatusCode = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;

    public function getTrimmedPostData()
    {
        $jsonArray = $this->post();

        array_walk_recursive($jsonArray, function(&$value) {
            if (is_string($value)) {
                $value = trim($value);
            }
        });

        return $jsonArray;
    }

    public static function getParamValue($param, $values)
    {
        $values = array_flip($values);
        return $values[$param] ?? '';
    }

    /**
     * @param Validator $validator
     * @return HttpResponseException
     */
    public function failedValidation(Validator $validator): HttpResponseException
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(response()->json([
            'status' => 'validation_error',
            'message' => $errors,
        ], $this->failedValidationResponseStatusCode));
    }

    public function getParamNames($paramArray)
    {
        return implode(',', array_column($paramArray, 'name'));
    }

    public function getMultipleParamsWithTooltips(array $params, array $tooltips): array
    {
        if (empty($params)) {
            return [];
        }

        if (!empty($tooltips)) {
            $tooltipNames = array_column($tooltips, 'name');
            $tooltips = array_combine($tooltipNames, $tooltips);
        }

        foreach ($params as $key => $param) {
            $params[$key]['tooltip'] = $tooltips[$param['name']]['tooltip'] ?? '';
        }

        return  $params;
    }

    public function getDateTimeAttribute(string $attribute = null)
    {
        return $attribute ? date('Y-m-d H:i:s', strtotime($attribute)) : '';
    }
}

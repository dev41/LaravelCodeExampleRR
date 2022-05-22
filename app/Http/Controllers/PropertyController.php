<?php

namespace App\Http\Controllers;

use App\Http\Requests\Property\UpdateRequest;
use App\Libraries\Cache;
use App\Models\Property;
use App\Services\PropertyService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PropertyController
 * @package App\Http\Controllers
 */
class PropertyController extends Controller
{
    const CACHE_KEY = 'api.property';

    /**
     * @param PropertyService $propertyService
     * @return JsonResponse
     */
    public function getAll(PropertyService $propertyService): JsonResponse
    {
        $key = self::CACHE_KEY . '.all';
        $data = Cache::getSet($key, function() use ($propertyService) {
            return $propertyService->getAll();
        });
        return response()->json($data, Response::HTTP_OK);
    }

    public function getById(Property $property, PropertyService $propertyService): JsonResponse
    {
        $key = self::CACHE_KEY . '.id.' . $property->id;
        $property = Cache::getSet($key, function() use ($propertyService, $property) {
            return $propertyService->getById($property);
        });

        return response()->json($property, Response::HTTP_OK);
    }

    public function createTemporary(PropertyService $propertyService): JsonResponse
    {
        $property = $propertyService->createTmp();
        return response()->json($property->id, Response::HTTP_CREATED);
    }

    public function delete(Property $property, PropertyService $propertyService): JsonResponse
    {
        $propertyService->delete($property);
        return response()->json(['success' => true], Response::HTTP_OK);
    }

    public function update(Property $property, UpdateRequest $request, PropertyService $propertyService): JsonResponse
    {
        $key = self::CACHE_KEY . '.id.' . $property->id;
        $property = $propertyService->update($property, $request->toArray());
        Cache::delete($key);

        return response()->json($property, Response::HTTP_OK);
    }
}

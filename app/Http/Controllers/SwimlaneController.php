<?php

namespace App\Http\Controllers;

use App\Services\SwimlaneService;

class SwimlaneController extends Controller
{
    public function getAll(SwimlaneService $swimlaneService)
    {
        $swimlane = $swimlaneService->getAll();

        return response()->json($swimlane);
    }
}

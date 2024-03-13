<?php

namespace App\Http\Controllers;
use App\Models\Designation;
use App\Service\DesignationService;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    protected $designationService;

    public function __construct(DesignationService $designationService)
    {
        $this->designationService = $designationService;
    }


    public function create(Request $request)
    {
        $result = $this->designationService->create($request);

        if ($result instanceof Designation) {
            return response()->json(['success' => true, 'data' => $result], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }
}

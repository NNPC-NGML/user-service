<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Models\department;
use App\Service\DepartmentService;
use Illuminate\Http\Request;
use App\Http\Requests\StoredepartmentRequest;
use App\Http\Requests\UpdatedepartmentRequest;

class DepartmentController extends Controller
{
    protected $departmentService;
    function __construct(DepartmentService $departmentService){
        $this->departmentService = $departmentService;
    }
   
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    
    /**
     * The `create` function processes a request to create a department and returns a JSON response
     * based on the result.
     * 
     * @param Request request The `Request ` parameter in the `create` function represents the
     * incoming HTTP request containing data that is being sent to the server. This data typically
     * includes information needed to create a new department, such as the department name,
     * description, or any other relevant details.
     * 
     * @return If the `` is an instance of `Department`, a JSON response with a success status
     * of true and the data in a `DepartmentResource` format is returned with a status code of 201
     * (Created). If the `` is not an instance of `Department`, a JSON response with a success
     * status of false and the error message in the response is returned with a status code of
     */
    public function create(Request $request)
    {
        $result = $this->departmentService->create($request);

        if ($result instanceof department) {
            return response()->json(['success' => true, 'data' => new DepartmentResource($result)], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(department $department)
    {
        //
    }

    
    /**
     * The function updates a department based on the provided request data and returns a JSON response
     * indicating success or failure.
     * 
     * @param Request request The `` parameter in the `update` function is an instance of the
     * `Illuminate\Http\Request` class in Laravel. It represents the HTTP request that is being made to
     * update a department. This request contains data such as form inputs, headers, files, etc., that
     * are sent by the client
     * @param id The `` parameter in the `update` function represents the unique identifier of the
     * department that you want to update. This identifier is typically used to locate the specific
     * department record in the database that needs to be updated.
     * 
     * @return If the `` is an instance of `Department`, a JSON response with a success status
     * of true and the updated department data in the `data` field will be returned with a status code
     * of 201. If the `` is not an instance of `Department`, a JSON response with a success
     * status of false and the error message in the `error` field will be returned with a
     */
    public function update(Request $request, $id)
    {
        $result = $this->departmentService->updateDepartment($id,$request);

        if ($result instanceof department) {
            return response()->json(['success' => true, 'data' => new DepartmentResource($result)], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(department $department)
    {
        //
    }
}

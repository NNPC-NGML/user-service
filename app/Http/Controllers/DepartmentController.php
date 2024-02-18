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
     * The index function retrieves all departments and returns a JSON response based on the result.
     * 
     * @return If the `` variable is an instance of the `Department` class, a JSON response with
     * a success status of true and the data in the form of a `DepartmentResource` will be returned
     * with a status code of 201 (Created). If `` is not an instance of the `Department` class,
     * a JSON response with a success status of false and an error message will be
     */
    public function index()
    {
        $result = $this->departmentService->viewAllDepartment();
            return response()->json(['success' => true, 'data' => new DepartmentResource($result)], 201);
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
    /* The `show` function in the `DepartmentController` class is responsible for displaying a specific
    department based on the provided department ID. Here's a breakdown of what it does: */
    public function show($id)
    {
        $result = $this->departmentService->getDepartment($id);
        if ($result instanceof department) {
            return response()->json(['success' => true, 'data' => new DepartmentResource($result)], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
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
     * The function `destroy` deletes a department based on the provided ID and returns a JSON response
     * indicating success or failure.
     * 
     * @param Request request The `` parameter in the `destroy` function is an instance of the
     * `Illuminate\Http\Request` class. It represents the HTTP request that is being made to the
     * server. This parameter allows you to access data sent in the request, such as form inputs or
     * query parameters.
     * @param id The `id` parameter in the `destroy` function represents the unique identifier of the
     * department that you want to delete. This identifier is typically used to locate and delete the
     * specific department record from the database.
     * 
     * @return If the `` variable is true, the function will return a JSON response with
     * `['success' => true]` and a status code of 201. If the `` variable is false, the function
     * will return a JSON response with `['success' => false]` and a status code of 422.
     */
    public function destroy(Request $request, $id)
    {
        $result = $this->departmentService->deleteDepartment($id);
        if ($result) {
            return response()->json(['success' => true], 201);
        } else {
            return response()->json(['success' => false], 422);
        }
    }
}

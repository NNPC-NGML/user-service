<?php

namespace App\Http\Controllers;

use App\Models\department;
use App\Service\UserService\DepartmentHelper;
use Illuminate\Http\Request;
use App\Traits\Services\UserService\DepartmentTrait;
use App\Http\Requests\StoredepartmentRequest;
use App\Http\Requests\UpdatedepartmentRequest;

class DepartmentController extends Controller
{
   
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $departmentHelper = new DepartmentHelper();
        return $departmentHelper->save($request);
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
     * Update the specified resource in storage.
     */
    public function update(UpdatedepartmentRequest $request, department $department)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(department $department)
    {
        //
    }
}

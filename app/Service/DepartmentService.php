<?php 
namespace App\Service;

use App\Models\department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Response;

class DepartmentService{

  /**
   * The function saves a department object based on the given request data and returns true if
   * successful, otherwise false.
   * 
   * @param request The `` parameter is an object that contains the data sent by the client in
   * the HTTP request. It typically includes information such as form inputs, query parameters, and
   * request headers. In this code snippet, the `` object is used to validate and save a new
   * department record.
   * 
   * @return object or a string  value. If the department is successfully saved, it will return department object.
   * Otherwise, it will return string error.
   */
    public function create(Request $request): object {

        $validator = Validator::make($request->all(), [
            'name' => 'required||max:20',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        
        $department = new department($request->all());
        if($department->save()){
            return $department;
        }
        
    }

    
    /**
     * Retrieve a department by its ID.
     *
     * @param int $id The ID of the department to be retrieve.
     *
     * @return \App\Models\department|null The retrieved department, or null if not found.
     */

    public function getDepartment(int $id):department | null {
        return department::find($id);
    }

    public function viewAllDepartment(){
        return department::all();
    }
}
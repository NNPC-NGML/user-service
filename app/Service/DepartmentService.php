<?php 
namespace App\Service;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\department;

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
   * @return bool a boolean value. If the department is successfully saved, it will return true.
   * Otherwise, it will return false.
   */
    public function create(Request $request): bool {

        $request->validate([
            'name' => 'required||max:20',
            'description' => 'required',
            
        ]);
        $department = new department($request->all());
        if($department->save()){
            return true;
        }
        
        return false;
    }

}
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

    /**
     * The function "viewAllDepartment" returns all departments.
     * 
     * @return all the departments.
     */
    public function viewAllDepartment():object | null{
        $returnArray = department::all();
        return $returnArray;
    }

    
    
    /**
     * The function updates a department record in the database based on the provided ID and request
     * data, and returns the updated department if successful.
     * 
     * @param int id The "id" parameter is the unique identifier of the department that needs to be
     * updated. It is used to find the department record in the database.
     * @param Request request The `` parameter is an instance of the `Request` class, which is
     * typically used in Laravel to handle incoming HTTP requests. It contains all the data and
     * information related to the current request, such as the request method, headers, and request
     * payload.
     * 
     * @return bool|array|department either a boolean value (true or false), an array, or an instance
     * of the "department" model.
     */
    public function updateDepartment(int $id, Request $request): bool|array|department
    {
        // validation
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|nullable|string',
            "description" => "sometimes|nullable|string",
        ]);
        if ($validator->fails()) {
            throw new \Exception($validator->errors());
        }
        
        $fetchService = $this->getDepartment($id);
        if ($fetchService) {
            if ($fetchService->update($request->all())) {
                return $fetchService;
            }
            throw new \Exception('Something went wrong.');

        }
        throw new \Exception('Something went wrong.');

    }


    /**
     * Delete a department using its id.
     *
     * @param int $id The ID of the department to be deleted.
     *
     * @return bool Return true if its deleted and false if not deleted
     */
    public function deleteDepartment(int $id): bool
    {
        $fetchService = $this->getDepartment($id);
        if ($fetchService) {
            if ($fetchService->delete()) {
                return true;
            }

        }
        return false;

    }
}
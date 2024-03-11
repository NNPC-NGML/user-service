<?php 
namespace App\Service;

use Response;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DesignationService{

    /**
   * The function saves a designation object based on the given request data and returns true if
   * successful, otherwise false.
   * 
   * @param request The `` parameter is an object that contains the data sent by the client in
   * the HTTP request. It typically includes information such as form inputs, query parameters, and
   * request headers. In this code snippet, the `` object is used to validate and save a new
   * designation record.
   * 
   * @return object or null. If the designation is successfully saved, it will return designation object.
   * Otherwise, it will return null.
   */

    public function create(Request $request): object | null {

        $validator = Validator::make($request->all(), [
            'role' => 'required||max:20',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        
        $designation = new Designation($request->all());
        if($designation->save()){
            return $designation;
        }else{
            return null;
        }
        
    }


        /**
     * Retrieve a designation by its ID.
     *
     * @param int $id The ID of the designation to be retrieve.
     *
     * @return \App\Models\Designation|null The retrieved designation, or null if not found.
     */

     public function getDesignation(int $id):Designation | null {
        return Designation::find($id);
    }


    /**
     * The function `updateDesignation` updates a designation with the provided ID using data from a
     * request, performing validation and returning the updated designation or throwing an exception if
     * an error occurs.
     * 
     * @param int id The `id` parameter in the `updateDesignation` function is used to identify the
     * specific designation that needs to be updated. It is an integer value that represents the unique
     * identifier of the designation in the database.
     * @param Request request The `updateDesignation` function you provided is responsible for updating
     * a designation based on the given ID and request data.
     * 
     * @return bool|array|Designation The `updateDesignation` function can return three different types
     * of values:
     */
    public function updateDesignation(int $id, Request $request): bool|array|Designation
    {
        // validation
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|nullable|string',
            "description" => "sometimes|nullable|string",
        ]);
        if ($validator->fails()) {
            throw new \Exception($validator->errors());
        }
        
        $fetchService = $this->getDesignation($id);
        if ($fetchService) {
            if ($fetchService->update($request->all())) {
                return $fetchService;
            }
            throw new \Exception('Something went wrong.');

        }
        throw new \Exception('Something went wrong.');

    }

    public function viewAllDesignations():Collection | null{
        $returnArray = Designation::all();
        return $returnArray;
    }

    
}

<?php 
namespace App\Service;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Response;


class LocationService{

    /**
     * The function creates a new Location object based on the data provided in the request and returns
     * the saved object if successful, otherwise it returns the validation errors.
     * 
     * @param Request request The  parameter is an instance of the Request class, which
     * represents an HTTP request made to the server. It contains information about the request, such
     * as the request method, headers, and input data. In this case, it is used to retrieve the input
     * data sent in the request.
     * 
     * @return object an object. If the validation fails, it returns the validation errors. If the
     * location is successfully saved, it returns the saved location object.
     */
    public function create(Request $request): object {

        $validator = Validator::make($request->all(), [
            'location' => 'required||max:20',
            'zone' => 'required||max:20',
            'state' => 'required||max:20',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        
        $location = new Location($request->all());
        if($location->save()){
            return $location;
        }
        
    }

    /**
     * The function "getLocation" returns a Location object or null based on the provided ID.
     * 
     * @param int id The parameter "id" is an integer that represents the unique identifier of a
     * location.
     * 
     * @return Location | null an instance of the Location class or null.
     */
    public function getLocation(int $id):Location | null {
        return Location::find($id);
    }

    public function updateLocation(int $id, Request $request): bool|array|Location
    {
        // validation
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|nullable|string',
            "description" => "sometimes|nullable|string",
        ]);
        if ($validator->fails()) {
            throw new \Exception($validator->errors());
        }
        
        $fetchService = $this->getLocation($id);
        if ($fetchService) {
            if ($fetchService->update($request->all())) {
                return $fetchService;
            }
            throw new \Exception('Something went wrong.');

        }
        throw new \Exception('Something went wrong.');

    }

    public function viewAllLocations():object | null{
        $returnArray = Location::all();
        return $returnArray;
    }

    /**
     * The function `deleteLocation` attempts to delete a location record with the given ID and returns
     * true if successful, false otherwise.
     * 
     * @param int id The parameter "id" is an integer that represents the unique identifier of the
     * location that needs to be deleted.
     * 
     * @return bool a boolean value. It returns true if the location with the given ID is successfully
     * deleted, and false otherwise.
     */
    public function deleteLocation(int $id): bool
    {
        $fetchService = $this->getLocation($id);
        if ($fetchService) {
            if ($fetchService->delete()) {
                return true;
            }

        }
        return false;

    }
}
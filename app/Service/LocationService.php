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

}
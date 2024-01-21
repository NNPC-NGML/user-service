<?php 
namespace App\Service\UserService;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\department;

class DepartmentHelper{

    public function save($request): bool {

        $request->validate([
            'name' => 'required||max:20',
            'description' => 'required',
            
        ]);
        $department = new department($request->all());
        if($department->save()){
            return true;
        }
        

    }

}
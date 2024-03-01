<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

     /**
     * @OA\Schema(
     *     schema="DepartmentResource",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="description", type="string"),  
     * )
     */

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}

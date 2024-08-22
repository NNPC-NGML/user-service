<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignationResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * 
     * @OA\Schema(
     *     schema="DesignationResource",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="role", type="string"),
     *     @OA\Property(property="description", type="string"),
     *     @OA\Property(property="status", type="boolean"),
     *     @OA\Property(property="level", type="string"),
     * )
     */

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}

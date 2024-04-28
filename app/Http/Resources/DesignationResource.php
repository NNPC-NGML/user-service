<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignationResource extends JsonResource
{

    /**
     * @OA\Schema(
     *     schema="DesignationResource",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="role", type="string"),
     *     @OA\Property(property="description", type="string"),
     *     @OA\Property(property="status", type="boolean"),
     *     @OA\Property(property="level", type="string"),
     * )
     */
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
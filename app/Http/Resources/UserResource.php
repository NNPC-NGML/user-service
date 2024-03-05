<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

     /**
     * @OA\Schema(
     *     schema="UserResource",
     *     @OA\Property(property="id", type="string"),
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="start_step_id", type="integer"),
     *     @OA\Property(property="frequency", type="string"),
     *     @OA\Property(property="status", type="boolean"),
     *     @OA\Property(property="frequency_for", type="string"),
     *     @OA\Property(property="day", type="string"),
     *     @OA\Property(property="week", type="string"),
     *     @OA\Property(
     *         property="steps",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/UserResource"),
     *     ),
     * )
     */

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}

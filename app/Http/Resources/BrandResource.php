<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
     public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'brand_name' => $this->brand_name,
            'user_id' => $this->user_id,
            'cars' => $this->cars
        ];
    }
}

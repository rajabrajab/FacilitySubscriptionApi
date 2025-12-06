<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{


    protected array $hiddenFields = [];

    public function withHidden(array $fields)
    {
        $this->hiddenFields = $fields;
        return $this;
    }

    protected function filterFields(array $data): array
    {
        return array_filter($data, fn ($_, $key) => !in_array($key, $this->hiddenFields), ARRAY_FILTER_USE_BOTH);
    }

    public function toArray(Request $request): array
    {
        $data =  [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'type' => $this->type,
            'profile_image' => $this->profile_image_url,
            'phone' => [
                'country_code' => $this->country_code,
                'iso_code' => $this->iso_code,
                'number' => $this->number
            ],
            'city' => $this->city->city
        ];

        return $this->filterFields($data);
    }
}

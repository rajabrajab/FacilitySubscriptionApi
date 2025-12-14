<?php

namespace App\Filament\Resources\Facilities\Pages;

use App\Filament\Resources\Facilities\FacilityResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateFacility extends CreateRecord
{
    protected static string $resource = FacilityResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'type' => 'facility',
            'country_code' => $data['country_code'] ?? '+963',
            'number' => $data['number'],
            'iso_code' => $data['iso_code'] ?? 'SYR',
            'profile_image' => $data['profile_image'] ?? null,
        ];

        $user = User::create($userData);

        $data['user_id'] = $user->id;

        unset($data['name'], $data['email'], $data['password'], $data['password_confirmation'],
              $data['type'], $data['country_code'], $data['number'], $data['iso_code'],
              $data['profile_image']);

        return $data;
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\Facilities\Pages;

use App\Filament\Resources\Facilities\FacilityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditFacility extends EditRecord
{
    protected static string $resource = FacilityResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load user data into form
        $user = $this->record->user;
        if ($user) {
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            $data['country_code'] = $user->country_code;
            $data['number'] = $user->number;
            $data['iso_code'] = $user->iso_code;
            $data['profile_image'] = $user->profile_image;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Update user data
        $user = $this->record->user;
        if ($user) {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'country_code' => $data['country_code'] ?? '+963',
                'number' => $data['number'],
                'iso_code' => $data['iso_code'] ?? 'SYR',
            ];

            // Update password if provided
            if (!empty($data['password'])) {
                $userData['password'] = $data['password'];
            }

            // Update profile image if provided
            if (isset($data['profile_image'])) {
                $userData['profile_image'] = $data['profile_image'];
            }

            $user->update($userData);
        }

        // Remove user fields from facility data
        unset($data['name'], $data['email'], $data['password'], $data['password_confirmation'],
              $data['country_code'], $data['number'], $data['iso_code'], $data['profile_image']);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

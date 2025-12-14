<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\Subscription;
use Filament\Tables\Filters\SelectFilter;
use App\Models\User;
use App\Models\Package;
use Filament\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteAction;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label(__('panel.userName')),
                TextColumn::make('package.name')->label(__('panel.packageName')),
                TextColumn::make('used_times')->label(__('panel.usedTimes')),
                TextColumn::make('expire_at')->label(__('panel.expireAt')),
                TextColumn::make('status')->badge()->label(__('panel.status'))->color(fn ($state) => match ($state) {
                    Subscription::STATUS_PENDING => 'warning',
                    Subscription::STATUS_CONFIRMED => 'success',
                    Subscription::STATUS_CANCELLED => 'danger',
                    Subscription::STATUS_EXPIRED => 'danger',
                    default => 'secondary',
                })->formatStateUsing(fn ($state) => __("panel.status.{$state}")),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('panel.status'))
                    ->options(Subscription::statuses())->searchable(),
                SelectFilter::make('user_id')
                    ->label(__('panel.user'))
                    ->options(User::all()->pluck('name', 'id'))->searchable(),
                SelectFilter::make('package_id')
                    ->label(__('panel.package'))
                    ->options(Package::all()->pluck('name', 'id'))->searchable(),
                Filter::make('expire_at')
                ->form([
                    DatePicker::make('from')->label('Expire From'),
                    DatePicker::make('until')->label('Expire Until'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['from'], fn ($q) => $q->whereDate('expire_at', '>=', $data['from']))
                        ->when($data['until'], fn ($q) => $q->whereDate('expire_at', '<=', $data['until']));
                })
            ])
            ->recordActions([
              DeleteAction::make('delete')
                ->icon('heroicon-m-trash')
                ->hiddenLabel()
                ->modalHeading(__('panel.deleteSubscription'))
                ->color('danger')
                ->action(function (Subscription $record) {
                    $record->delete();
                }),
              Action::make('changeStatus')
                ->icon('heroicon-m-pencil')
                ->hiddenLabel()
                ->modalHeading(__('panel.changeStatus'))
                ->color('success')
                ->form([
                    Select::make('status')
                        ->label(__('panel.status'))
                        ->options([
                            Subscription::STATUS_PENDING   => __('panel.status.pending'),
                            Subscription::STATUS_CONFIRMED => __('panel.status.confirmed'),
                            Subscription::STATUS_CANCELLED => __('panel.status.cancelled'),
                            Subscription::STATUS_EXPIRED   => __('panel.status.expired'),
                        ])
                        ->required()
                ])
                ->action(function (array $data, Subscription $record) {
                    $record->update([
                        'status' => $data['status'],
                    ]);
                }),
            ]);
    }
}

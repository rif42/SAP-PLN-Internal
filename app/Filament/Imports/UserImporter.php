<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label(__('resources.user.name'))
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('email')
                ->label(__('resources.user.email'))
                ->requiredMapping()
                ->rules(['required', 'email', 'unique:users,email']),
            ImportColumn::make('password')
                ->label(__('resources.user.password'))
                ->requiredMapping()
                ->rules(['required', 'string', 'min:8'])
                ->castToType('string'),
        ];
    }

    public function resolveRecord(): ?User
    {
        $user = new User();
        
        // Hash the password before saving
        if ($this->data['password']) {
            $this->data['password'] = Hash::make($this->data['password']);
        }
        
        return $user;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = __('resources.user.notifications.import.completed', ['count' => number_format($import->successful_rows)]);

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . __('resources.user.notifications.import.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }
} 

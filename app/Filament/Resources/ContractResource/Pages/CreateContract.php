<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    protected function beforeCreate(): void
    {
        Log::info('Attempting to create new contract', [
            'user' => auth()->user()->name ?? 'Unknown',
            'data' => $this->data
        ]);
    }

    protected function afterCreate(): void
    {
        Log::info('Contract created successfully', [
            'contract_id' => $this->record->id,
            'user' => auth()->user()->name ?? 'Unknown'
        ]);
    }

    protected function onValidationError($errors): void
    {
        Log::error('Contract creation validation failed', [
            'errors' => $errors,
            'user' => auth()->user()->name ?? 'Unknown',
            'data' => $this->data
        ]);
    }

    public function create(bool $another = false): void
    {
        try {
            parent::create($another);
        } catch (\Exception $e) {
            Log::error('Exception during contract creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => auth()->user()->name ?? 'Unknown'
            ]);
            throw $e;
        }
    }
}

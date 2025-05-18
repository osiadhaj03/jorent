<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

//    protected function beforeCreate(): void
//    {
//        // التحقق من وجود المستخدم وصلاحياته
//        if (!auth()->check()) {
//            Log::error('Unauthorized access attempt');
//            abort(403, 'Unauthorized');
//        }
//
//        // التحقق من البيانات المطلوبة
//        if (!isset($this->data['tenant_id']) || !isset($this->data['unit_id'])) {
//            Log::error('Missing required fields', $this->data);
//            abort(422, 'Missing required fields');
//        }
//
//        // تسجيل محاولة الإنشاء
//        Log::info('Attempting to create new contract', [
//            'user' => auth()->user()->name ?? 'Unknown',
//            'data' => $this->data
//        ]);
//
//        // التحقق من أن الوحدة تنتمي للعقار
//        if (isset($this->data['unit_id']) && isset($this->data['property_id'])) {
//            $unit = \App\Models\Unit::find($this->data['unit_id']);
//            if (!$unit || $unit->property_id != $this->data['property_id']) {
//                Log::error('Unit does not belong to the specified property', [
//                    'unit_id' => $this->data['unit_id'],
//                    'property_id' => $this->data['property_id']
//                ]);
//                abort(422, 'Invalid unit or property');
//            }
//        }
//    }
//
//    protected function afterCreate(): void
//    {
//        Log::info('Contract created successfully', [
//            'contract_id' => $this->record->id,
//            'user' => auth()->user()->name ?? 'Unknown'
//        ]);
//    }
//
//    protected function onValidationError($errors): void
//    {
//        Log::error('Contract creation validation failed', [
//            'errors' => $errors,
//            'user' => auth()->user()->name ?? 'Unknown',
//            'data' => $this->data
//        ]);
//    }
//
//    public function create(bool $another = false): void
//    {
//        try {
//            parent::create($another);
//        } catch (\Exception $e) {
//            Log::error('Exception during contract creation', [
//                'error' => $e->getMessage(),
//                'trace' => $e->getTraceAsString(),
//                'user' => auth()->user()->name ?? 'Unknown'
//            ]);
//            throw $e;
//        }
//    }
}

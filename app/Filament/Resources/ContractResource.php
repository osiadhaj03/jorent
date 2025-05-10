<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Resources\ContractResource\RelationManagers;
use App\Models\Contract;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Real Estate';
    protected static ?string $navigationLabel = 'Contracts';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'firstname') // يمكنك تغيير 'firstname' إلى 'full_name' إذا كان ذلك مناسبًا
                    ->required()
                    ->label('المستأجر')
                    ->reactive()
                    ->default(null) // إضافة قيمة افتراضية
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state) {
                            $tenant = Tenant::find($state);
                            $set('tenant_national_id', $tenant?->document_number);
                        } else {
                            $set('tenant_national_id', null);
                        }
                    }),

                Forms\Components\TextInput::make('tenant_national_id')
                    ->required()
                    ->label('الرقم الوطني للمستأجر')
                    ->disabled(),
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'unit_number')
                    ->required()
                    ->label('الوحدة')
                    ->reactive()
                    ->default(null) // إضافة قيمة افتراضية
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state) {
                            $unit = Unit::with('property.address')->find($state);
                            if ($unit && $unit->property) {
                                $property = $unit->property;
                                $set('property_type', $property->type2);

                                $locationParts = [];
                                if ($property->address) {
                                    if ($property->address->area_name) $locationParts[] = 'منطقة ' . $property->address->area_name;
                                    if ($property->address->street_name) $locationParts[] = 'شارع ' . $property->address->street_name;
                                    if ($property->address->building_number) $locationParts[] = 'مبنى ' . $property->address->building_number;
                                }
                                $fullLocation = implode(', ', array_filter($locationParts));
                                $set('property_location', $fullLocation ?: ($property->custom_location ?: null));

                                $set('floor_number', $unit->floor_number);
                                $set('apartment_number', $unit->apartment_number);
                                $set('land_piece_number', $property->address?->land_piece_number);
                                $set('basin_number', $property->address?->basin_number);
                                $set('area_name', $property->address?->area_name);
                                $set('street_name', $property->address?->street_name);
                                $set('building_number', $property->address?->building_number);
                                $set('building_name', $property->building_name);
                                $set('property_fixtures', $property->fixtures);
                            } else {
                                $set('property_type', 'شقة سكنية');
                                $set('property_location', null);
                                $set('floor_number', null);
                                $set('apartment_number', null);
                                $set('land_piece_number', null);
                                $set('basin_number', null);
                                $set('area_name', null);
                                $set('street_name', null);
                                $set('building_number', null);
                                $set('building_name', null);
                                $set('property_fixtures', null);
                            }
                        } else {
                            $set('property_type', 'شقة سكنية');
                            $set('property_location', null);
                            $set('floor_number', null);
                            $set('apartment_number', null);
                            $set('land_piece_number', null);
                            $set('basin_number', null);
                            $set('area_name', null);
                            $set('street_name', null);
                            $set('building_number', null);
                            $set('building_name', null);
                            $set('property_fixtures', null);
                        }
                    }),
                Forms\Components\TextInput::make('property_type')
                    ->default('شقة سكنية')
                    ->required()
                    ->label('نوع العقار')
                    ->disabled(),
                Forms\Components\TextInput::make('property_location')
                    ->required()
                    ->label('موقع العقار')
                    ->disabled(),
                Forms\Components\TextInput::make('floor_number')
                    ->required()
                    ->label('رقم الطابق')
                    ->disabled(),
                Forms\Components\TextInput::make('apartment_number')
                    ->required()
                    ->label('رقم الشقة')
                    ->disabled(),
                Forms\Components\TextInput::make('land_piece_number')
                    ->required()
                    ->label('رقم قطعة الأرض')
                    ->disabled(),
                Forms\Components\TextInput::make('basin_number')
                    ->required()
                    ->label('رقم الحوض')
                    ->disabled(),
                Forms\Components\TextInput::make('area_name')
                    ->required()
                    ->label('اسم المنطقة')
                    ->disabled(),
                Forms\Components\TextInput::make('street_name')
                    ->required()
                    ->label('اسم الشارع')
                    ->disabled(),
                Forms\Components\TextInput::make('building_number')
                    ->required()
                    ->label('رقم البناء')
                    ->disabled(),
                Forms\Components\TextInput::make('building_name')
                    ->required()
                    ->label('اسم البناء')
                    ->disabled(),
                Forms\Components\TextInput::make('usage_type')
                    ->default('للسكن فقط')
                    ->required()
                    ->label('نوع الاستخدام'),
                Forms\Components\TextInput::make('property_boundaries')
                    ->default('داخل جدران الشقة فقط')
                    ->required()
                    ->label('حدود العقار'),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->label('تاريخ بداية العقد'),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->label('تاريخ نهاية العقد'),
                Forms\Components\TextInput::make('contract_period')
                    ->required()
                    ->label('مدة العقد'),
                Forms\Components\TextInput::make('annual_rent')
                    ->numeric()
                    ->required()
                    ->label('قيمة الإيجار السنوي'),
                Forms\Components\Select::make('payment_frequency')
                    ->options([
                        'monthly' => 'شهري',
                        'quarterly' => 'ربع سنوي',
                        'semi_annual' => 'نصف سنوي',
                        'annual' => 'سنوي'
                    ])
                    ->required()
                    ->label('تكرار الدفع'),
                Forms\Components\TextInput::make('payment_amount')
                    ->numeric()
                    ->required()
                    ->label('قيمة الدفعة'),
                Forms\Components\Toggle::make('education_tax')
                    ->default(true)
                    ->label('ضريبة المعارف'),
                Forms\Components\TextInput::make('education_tax_amount')
                    ->numeric()
                    ->label('قيمة ضريبة المعارف'),
                Forms\Components\Textarea::make('property_fixtures')
                    ->required()
                    ->label('محتويات العقار')
                    ->disabled(),
                Forms\Components\Textarea::make('additional_terms')
                    ->label('شروط إضافية'),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'فعال',
                        'expired' => 'منتهي',
                        'terminated' => 'ملغي'
                    ])
                    ->default('active')
                    ->required()
                    ->label('حالة العقد')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.full_name')
                    ->sortable()
                    ->searchable()
                    ->label('المستأجر'),
                Tables\Columns\TextColumn::make('property_location')
                    ->searchable()
                    ->label('موقع العقار'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->label('تاريخ البداية'),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->label('تاريخ النهاية'),
                Tables\Columns\TextColumn::make('annual_rent')
                    ->money('jod')
                    ->sortable()
                    ->label('الإيجار السنوي'),
                Tables\Columns\TextColumn::make('payment_frequency')
                    ->label('تكرار الدفع')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'monthly' => 'شهري',
                        'quarterly' => 'ربع سنوي',
                        'semi_annual' => 'نصف سنوي',
                        'annual' => 'سنوي',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'expired' => 'danger',
                        'terminated' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'فعال',
                        'expired' => 'منتهي',
                        'terminated' => 'ملغي',
                    })
                    ->label('الحالة'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('تاريخ الإنشاء'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('تاريخ التحديث'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'view' => Pages\ViewContract::route('/{record}'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
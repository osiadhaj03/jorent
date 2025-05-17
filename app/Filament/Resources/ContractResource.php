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
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Real Estate Management ';
    protected static ?string $navigationLabel = 'Contracts';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // قسم بيانات المالك
                Forms\Components\Section::make('بيانات المالك')
                    ->schema([
                        Forms\Components\TextInput::make('landlord_name')
                            ->required()
                            ->label('Landlord Name'),
                    ]),

                // قسم بيانات المستأجر
                Forms\Components\Section::make('بيانات المستأجر')
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'firstname')
                            ->required()
                            ->label('Tenant Name'),
                    ]),

                // قسم بيانات الوحدة والعقار
                Forms\Components\Section::make('بيانات الوحدة والعقار')
                    ->schema([
                        Forms\Components\Select::make('property_id')
                            ->options(Property::all()->pluck('name', 'id'))
                            ->required()
                            ->label('Property Name')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $propertyId = $get('property_id');
                                if ($propertyId) {
                                    $property = Property::with('address')->find($propertyId);
                                    if ($property && $property->address) {
                                        $set('governorate', $property->address->governorate);
                                        $set('city', $property->address->city);
                                        $set('district', $property->address->district);
                                        $set('building_number', $property->address->building_number);
                                        $set('plot_number', $property->address->plot_number);
                                        $set('basin_number', $property->address->basin_number);
                                        $set('property_number', $property->address->property_number);
                                        $set('street_name', $property->address->street_name);
                                    } else {
                                        $set('governorate', null);
                                        $set('city', null);
                                        $set('district', null);
                                        $set('building_number', null);
                                        $set('plot_number', null);
                                        $set('basin_number', null);
                                        $set('property_number', null);
                                        $set('street_name', null);
                                    }
                                }
                                $set('unit_id', null);
                            }),
                        Forms\Components\Select::make('unit_id')
                            ->options(function (callable $get) {
                                $propertyId = $get('property_id');
                                return $propertyId ? Unit::where('property_id', $propertyId)->pluck('name', 'id') : [];
                            })
                            ->required()
                            ->label('Unit Name'),
                        Forms\Components\TextInput::make('governorate')
                            ->readOnly()
                            ->label('Governorate (المحافظة)'),
                        Forms\Components\TextInput::make('city')
                            ->readOnly()
                            ->label('City'),
                        Forms\Components\TextInput::make('district')
                            ->readOnly()
                            ->label('District(الحي)'),
                        Forms\Components\TextInput::make('building_number')
                            ->readOnly()
                            ->label('Building Number'),
                        Forms\Components\TextInput::make('plot_number')
                            ->readOnly()
                            ->label('Plot Number(رقم القطعة)'),
                        Forms\Components\TextInput::make('basin_number')
                            ->readOnly()
                            ->label('Basin Number(رقم الحوض)'),
                        Forms\Components\TextInput::make('property_number')
                            ->readOnly()
                            ->label('Property Number(رقم المبنى العقاري)'),
                        Forms\Components\TextInput::make('street_name')
                            ->readOnly()
                            ->label('Street Name'),
                    ]),

                // قسم تفاصيل العقد
                Forms\Components\Section::make('تفاصيل العقد')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->required()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $startDate = $get('start_date');
                                $endDate = $get('end_date');
                                if ($startDate && $endDate && $state) {
                                    if ($state < $startDate || $state > $endDate) {
                                        $set('due_date', null);
                                    }
                                }
                            })
                            ->visible(function (callable $get) {
                                $startDate = $get('start_date');
                                $endDate = $get('end_date');
                                $dueDate = $get('due_date');
                                if ($startDate && $endDate && $dueDate) {
                                    return $dueDate >= $startDate && $dueDate <= $endDate;
                                }
                                return true;
                            })
                            ->helperText('يجب أن يكون تاريخ الاستحقاق بين تاريخ البداية وتاريخ النهاية')
                            ->rule(function (callable $get) {
                                $startDate = $get('start_date');
                                $endDate = $get('end_date');
                                return function ($attribute, $value, $fail) use ($startDate, $endDate) {
                                    if ($startDate && $endDate && $value) {
                                        if ($value < $startDate || $value > $endDate) {
                                            $fail('تاريخ الاستحقاق يجب أن يكون بين تاريخ البداية وتاريخ النهاية.');
                                        }
                                    }
                                };
                            }),

                        Forms\Components\TextInput::make('rent_amount')
                            ->numeric()
                            ->required(),

               
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active')
                            ->reactive()
                            ->afterStateHydrated(function (callable $set, callable $get) {
                                $startDate = $get('start_date');
                                $endDate = $get('end_date');
                                $currentDate = now();
                                if ($startDate && $endDate) {
                                    if ($currentDate->between($startDate, $endDate)) {
                                        $set('status', 'active');
                                    } else {
                                        $set('status', 'inactive');
                                    }
                                }
                            }),
                    ])->columns(3),

                Forms\Components\Section::make('الشروط والأحكام')
                    ->description('Please enter the terms and conditions of the contract')
                    ->schema([
                        
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('show_terms')
                                ->label('عرض الشروط والأحكام')
                                ->action(fn () => null)
                                ->modalHeading('الشروط والأحكام')
                                ->modalContent(view('filament.custom-terms-modal')),
                        ]),
                        Forms\Components\Textarea::make('terms_and_conditions_extra')
                            ->nullable(),
                    ]),

                // Signatures Section
                Forms\Components\Section::make('Contract Signatures')
                    ->description('Click on each signature field to open the signature pad')
                    ->schema([
                        SignaturePad::make('tenant_signature')
                            ->label('Tenant Signature')
                            ->backgroundColor('rgba(0,0,0,0)')
                            ->penColor('#000')
                            ->clearable()
                            ->undoable()
                            ->confirmable()
                            ->required(),

                        SignaturePad::make('first_witness_signature')
                            ->label('First Witness Signature')
                            ->backgroundColor('rgba(0,0,0,0)')
                            ->penColor('#000')
                            ->clearable()
                            ->undoable()
                            ->confirmable()
                            ->required(),
                            
                        SignaturePad::make('second_witness_signature')
                            ->label('Second Witness Signature')
                            ->backgroundColor('rgba(0,0,0,0)')
                            ->penColor('#000')
                            ->clearable()
                            ->undoable()
                            ->confirmable()
                            ->required(),
                            
                        SignaturePad::make('landlord_signature')
                            ->label('Landlord Signature')
                            ->backgroundColor('rgba(0,0,0,0)')
                            ->penColor('#000')
                            ->clearable()
                            ->undoable()
                            ->confirmable()
                            ->required(),
                    ])->columns(4),

                // قسم معلومات إضافية
                Forms\Components\Section::make('معلومات إضافية')
                    ->schema([
                        Forms\Components\DatePicker::make('hired_date')
                            ->default(now())
                            ->readOnly()
                            ->label('Hired Date'),
                        Forms\Components\TextInput::make('hired_by')
                            ->default(Auth::check() ? Auth::user()->name : 'Guest')
                            ->readOnly()
                            ->label('Hired By'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('landlord_name')
                    ->label('Landlord Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('property.name')
                    ->label('Property Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tenant.firstname')
                    ->label('Tenant Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->sortable()
                    ->date()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->sortable()
                    ->date()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('rent_amount')
                    ->label('Rent Amount')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment_frequency')
                    ->label('Payment Frequency')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')   
                    ->label('Created At')
                    ->sortable()
                    ->dateTime()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->sortable()
                    ->dateTime()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('hired_date')
                    ->label('Hired Date')
                    ->sortable()
                    ->dateTime()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('hired_by')
                    ->label('Hired By')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
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
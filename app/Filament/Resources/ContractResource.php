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

    protected static ?string $navigationGroup = 'Real Estate';
    protected static ?string $navigationLabel = 'Contracts';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([


                Forms\Components\TextInput::make('landlord_name')
                    ->required()
                    ->label('Landlord Name'),


                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'firstname')
                    ->required()
                    ->label('Tenant Name'),

                Forms\Components\Select::make('property_id')
                    ->options(Property::all()->pluck('name', 'id'))
                    ->required()
                    ->label('Property Name')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        $propertyId = $get('property_id');
                        if ($propertyId) {
                            $property = Property::find($propertyId);
                            if ($property) {
                                $set('governorate', $property->governorate );
                                $set('city', $property->city );
                                $set('district', $property->district );
                                $set('building_number', $property->building_number );
                                $set('plot_number', $property->plot_number );
                                $set('basin_number', $property->basin_number );
                                $set('property_number', $property->property_number );
                                $set('street_name', $property->street_name );
                            }
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
                        $set('unit_id', null);
                    }),
                Forms\Components\Select::make('unit_id')
                    ->options(function (callable $get) {
                        $propertyId = $get('property_id');
                        return $propertyId ? Unit::where('property_id', $propertyId)->pluck('name', 'id') : [];
                    })
                    ->required()
                    ->label('Unit Name'),

                ///////////    
                Forms\Components\TextInput::make('governorate')
                    ->disabled()
                    ->label('Governorate (المحافظة)'),
                Forms\Components\TextInput::make('city')
                    ->disabled()
                    ->label('City'),
                Forms\Components\TextInput::make('district')
                    ->disabled()
                    ->label('District(الحي)'),
                Forms\Components\TextInput::make('building_number')
                    ->disabled()
                    ->label('Building Number'),
                Forms\Components\TextInput::make('plot_number')
                    ->disabled()
                    ->label('Plot Number(رقم القطعة)'),
                Forms\Components\TextInput::make('basin_number')
                    ->disabled()
                    ->label('Basin Number(رقم الحوض)'),
                Forms\Components\TextInput::make('property_number')
                    ->disabled()
                    ->label('Property Number(رقم المبنى العقاري)'),
                Forms\Components\TextInput::make('street_name')
                    ->disabled()
                    ->label('Street Name'),


/////////////////////////////////////////////////////////
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required(),
                Forms\Components\TextInput::make('rent_amount')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('payment_frequency')
                    ->options([
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('terms_and_conditions_extra')
                    ->nullable(),

                // Contract Status
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

                // Signatures Section
                Forms\Components\Section::make('Contract Signatures')
                    ->description('Click on each signature field to open the signature pad')
                    ->schema([
                        SignaturePad::make('tenant_signature')
                            ->label('Tenant Signature')
                            ->backgroundColor('rgba(0,0,0,0)')  // Background color on light mode
                            ->backgroundColorOnDark('#f0a')     // Background color on dark mode (defaults to backgroundColor)
                            ->exportBackgroundColor('#f00')     // Background color on export (defaults to backgroundColor)
                            ->penColor('#000')                  // Pen color on light mode
                            ->penColorOnDark('#fff')            // Pen color on dark mode (defaults to penColor)
                            ->exportPenColor('#0f0')            // Pen color on export (defaults to penColor)
                            ->clearable(true)
                            ->downloadable(false)
                            ->undoable(true)
                            ->confirmable(true)
                            ->required(),

                        SignaturePad::make('witness_signature')
                            ->label('Witness Signature')
                            ->backgroundColor('rgba(0,0,0,0)')  // Background color on light mode
                            ->backgroundColorOnDark('#f0a')     // Background color on dark mode (defaults to backgroundColor)
                            ->exportBackgroundColor('#f00')     // Background color on export (defaults to backgroundColor)
                            ->penColor('#000')                  // Pen color on light mode
                            ->penColorOnDark('#fff')            // Pen color on dark mode (defaults to penColor)
                            ->exportPenColor('#0f0')            // Pen color on export (defaults to penColor)
                            ->clearable(true)
                            ->downloadable(false)
                            ->undoable(true)
                            ->confirmable(true)
                            ->required(),
                            
                        SignaturePad::make('landlord_signature')
                            ->label('Landlord Signature')
                            ->backgroundColor('rgba(0,0,0,0)')  // Background color on light mode
                            ->backgroundColorOnDark('#f0a')     // Background color on dark mode (defaults to backgroundColor)
                            ->exportBackgroundColor('#f00')     // Background color on export (defaults to backgroundColor)
                            ->penColor('#000')                  // Pen color on light mode
                            ->penColorOnDark('#fff')            // Pen color on dark mode (defaults to penColor)
                            ->exportPenColor('#0f0')
                            ->clearable(true)                   // Pen color on export (defaults to penColor)
                            ->downloadable(false)
                            ->undoable(true)
                            ->confirmable(true)
                            ->required(),
                    ])->columns(3),

                Forms\Components\DatePicker::make('hired_date')
                    ->default(now())
                    ->disabled()
                    ->label('Hired Date'),
                Forms\Components\TextInput::make('hired_by')
                    ->default(Auth::user()->name)
                    ->disabled()
                    ->label('Hired By'),

            
            
                    ]);
                
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

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
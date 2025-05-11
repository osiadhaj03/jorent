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
                //
                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required()
                    ->label('Tenant Name'),
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->required()
                    ->label('Unit Name'),
                Forms\Components\TextInput::make('landlord_name')
                    ->required()
                    ->label('Landlord Name'),
                Forms\Components\TextInput::make('contract_number')
                    ->required()
                    ->label('Contract Number')
                    ->unique(ignoringRecord: true)
                    ->default(function (Set $set) {
                        return Contract::max('contract_number') + 1;
                    }),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->label('Start Date'),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->label('End Date'),
                Forms\Components\TextInput::make('rent_amount')
                    ->required()
                    ->label('Rent Amount')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(1000000),
                Forms\Components\Select::make('payment_frequency')
                    ->options([
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ])
                    ->required()
                    ->label('Payment Frequency'),
                Forms\Components\Textarea::make('terms_and_conditions_extra')
                    ->label('Terms and Conditions')
                    ->rows(3)
                    ->maxLength(500),
                Forms\Components\TextInput::make('status')
                    ->default('active')
                    ->label('Status')
                    ->required(),

            
            Forms\Components\Fieldset::make('Employment Information')
                ->schema([
                Forms\Components\DatePicker::make('hired_date')
                    ->default(now())
                    ->label('Hired Date')
                    ->disabled(),
                Forms\Components\TextInput::make('hired_by')
                    ->default(auth()->user()->name)
                    ->label('Hired By')
                    ->maxLength(255)
                    ->disabled(),
                ]),
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
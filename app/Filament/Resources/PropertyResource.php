<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\Select::make('type2')
                    ->options([
                        'residential' => 'Residential',
                        'commercial' => 'Commercial',
                        'industrial' => 'Industrial',
                    ])
                    ->required(),
                Forms\Components\Select::make('type1')
                    ->options([
                        'building' => 'Building',
                        'villa' => 'Villa',
                        'house' => 'House',
                        'warehouse' => 'Warehouse',
                    ])
                    ->required(),
                Forms\Components\Select::make('account_manager_id')
                    ->relationship('accountManager', 'firstname')
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('birth_date')
                    ->required()
                    ->placeholder('YYYY-MM-DD')
                    ->format('Y-m-d')
                    ->maxDate(now()),
                Forms\Components\TextInput::make('floors_count')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100),
                Forms\Components\TextInput::make('floor_area')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10000),
                Forms\Components\TextInput::make('total_area')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10000),
                Forms\Components\Repeater::make('features')
                   //>relationship('features')
                    ->schema([
                        Forms\Components\TextInput::make('feature_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('feature_value')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->createItemButtonLabel('Add Feature'),
                   //>disableItemMovementButtons()
                   //>disableItemDeletionButtons(),
                Forms\Components\TextInput::make('address.country')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address.governorate')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address.city')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address.district')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address.building_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address.plot_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address.basin_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address.property_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address.street_name')
                    ->required()
                    ->maxLength(255),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}

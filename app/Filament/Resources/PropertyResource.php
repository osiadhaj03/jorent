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


    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Real Estate';
    protected static ?string $navigationLabel = 'Properties';
    protected static ?string $label = 'Property';
    protected static ?string $pluralLabel = 'Properties';
    protected static ?string $slug = 'properties';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(65535),
                    ]),

                Forms\Components\Section::make('Type Information')
                    ->schema([
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
                    ]),

                Forms\Components\Section::make('Additional Details')
                    ->schema([
                        Forms\Components\Repeater::make('features')
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
                        Forms\Components\Repeater::make('images')
                            ->label('Images')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Image')
                                    ->image()
                                    ->directory('uploads/properties')
                                    ->maxSize(1024), // Optional size limit in KB
                            ])
                            ->columns(1)
                            ->createItemButtonLabel('Add Image'),
                    ]),

                Forms\Components\Section::make('Property Details')
                    ->schema([
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
                    ]),

                Forms\Components\Section::make('Address Information')
                    ->schema([
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
                    ]),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                    ->sortable()
                    ->searchable()
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('acc.firstname')
                    ->sortable()
                    ->searchable()
                    ->label('Account Manager')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type1')
                    ->sortable()
                    ->searchable()
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type2')
                    ->sortable()
                    ->searchable()
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->sortable()
                    ->searchable()
                    ->date('Y-m-d')
                    ->label('Birth Date')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('floors_count')
                    ->sortable()
                    ->searchable()
                    ->label('Floors Count')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('floor_area')   
                    ->sortable()
                    ->searchable()
                    ->label('Floor Area')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_area')
                    ->sortable()
                    ->searchable()
                    ->label('Total Area')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->searchable()
                    ->date('Y-m-d')
                    ->label('Created At')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()
                    ->searchable()
                    ->date('Y-m-d')
                    ->label('Updated At')
                    ->toggleable(),
            ])
        
            ->filters([
                //
Tables\Filters\Filter::make('id')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('id')),
                Tables\Filters\Filter::make('name')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('name')),
                Tables\Filters\Filter::make('description')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('description')),
                Tables\Filters\Filter::make('acc.firstname')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('acc.firstname')),
                Tables\Filters\Filter::make('type1')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('type1')),
                Tables\Filters\Filter::make('type2')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('type2')),
                Tables\Filters\Filter::make('birth_date')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('birth_date')),
                Tables\Filters\Filter::make('floors_count')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('floors_count')),
                Tables\Filters\Filter::make('floor_area')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('floor_area')),
                Tables\Filters\Filter::make('total_area')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('total_area')),
                Tables\Filters\Filter::make('created_at')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('created_at')),
                Tables\Filters\Filter::make('updated_at')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('updated_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
Tables\Actions\DeleteAction::make(),
                Tables\Actions\VIEWAction::make()

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

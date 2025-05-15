namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'address';

    protected static ?string $recordTitleAttribute = 'full_address';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('country')
                    ->label('Country')
                    ->required(),
                Forms\Components\TextInput::make('governorate')
                    ->label('Governorate')
                    ->required(),
                Forms\Components\TextInput::make('city')
                    ->label('City')
                    ->required(),
                Forms\Components\TextInput::make('district')
                    ->label('District')
                    ->required(),
                Forms\Components\TextInput::make('building_number')
                    ->label('Building Number')
                    ->required(),
                Forms\Components\TextInput::make('plot_number')
                    ->label('Plot Number')
                    ->required(),
                Forms\Components\TextInput::make('basin_number')
                    ->label('Basin Number')
                    ->required(),
                Forms\Components\TextInput::make('property_number')
                    ->label('Property Number')
                    ->required(),
                Forms\Components\TextInput::make('street_name')
                    ->label('Street Name')
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country')->label('Country'),
                Tables\Columns\TextColumn::make('governorate')->label('Governorate'),
                Tables\Columns\TextColumn::make('city')->label('City'),
                Tables\Columns\TextColumn::make('district')->label('District'),
                Tables\Columns\TextColumn::make('building_number')->label('Building Number'),
                Tables\Columns\TextColumn::make('plot_number')->label('Plot Number'),
                Tables\Columns\TextColumn::make('basin_number')->label('Basin Number'),
                Tables\Columns\TextColumn::make('property_number')->label('Property Number'),
                Tables\Columns\TextColumn::make('street_name')->label('Street Name'),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
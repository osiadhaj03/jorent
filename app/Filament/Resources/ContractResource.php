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

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Rental Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'firstname') // Assuming 'firstname' is a displayable attribute on Tenant model
                    ->required()
                    ->label('Tenant')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'name') // Assuming 'name' or similar is a displayable attribute on Unit model
                    ->required()
                    ->label('Unit')
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->label('Start Date'),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->label('End Date')
                    ->after('start_date'),
                Forms\Components\TextInput::make('rent_amount')
                    ->required()
                    ->numeric()
                    ->prefix('JOD') // Or your currency
                    ->label('Rent Amount'),
                Forms\Components\Select::make('payment_frequency')
                    ->options([
                        'monthly' => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'semi_annually' => 'Semi-Annually',
                        'annually' => 'Annually',
                    ])
                    ->required()
                    ->label('Payment Frequency'),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'terminated' => 'Terminated',
                        'pending' => 'Pending',
                    ])
                    ->required()
                    ->default('pending')
                    ->label('Status'),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('contract_document')
                    ->label('Contract Document')
                    ->directory('uploads/contracts')
                    ->maxSize(2048) // 2MB
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.firstname')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.name') // Assuming 'name' or similar for Unit
                    ->label('Unit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->label('Start Date'),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->label('End Date'),
                Tables\Columns\TextColumn::make('rent_amount')
                    ->money('JOD') // Or your currency
                    ->sortable()
                    ->label('Rent Amount'),
                Tables\Columns\TextColumn::make('payment_frequency')
                    ->searchable()
                    ->sortable()
                    ->label('Payment Frequency'),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'expired' => 'danger',
                        'terminated' => 'warning',
                        'pending' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->relationship('tenant', 'firstname')
                    ->label('Tenant'),
                Tables\Filters\SelectFilter::make('unit_id')
                    ->relationship('unit', 'name') // Assuming 'name' or similar for Unit
                    ->label('Unit'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'terminated' => 'Terminated',
                        'pending' => 'Pending',
                    ])
                    ->label('Status'),
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
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
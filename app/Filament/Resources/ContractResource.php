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

             Forms\Components\TextInput::make('tenant_national_id')
                 ->required()
                 ->label('الرقم الوطني للمستأجر')
                 ->disabled(),
             ///////

      Forms\Components\Select::make('unit_id')
          ->relationship('unit', 'unit_number')
          ->required()
          ->label('الوحدة')
          ->reactive(),

          


              Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->label('تاريخ بداية العقد'),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->label('تاريخ نهاية العقد'),

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
                //  ->required()
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
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Financial';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Invoices'; // Corrected pluralization
    protected static ?string $label = 'Invoice';
    protected static ?string $pluralLabel = 'Invoices';
    protected static ?string $slug = 'invoices'; // Conventionally lowercase and plural
    protected static ?string $recordTitleAttribute = 'invoice_number'; // Or a more descriptive attribute

    public static function form(Form $form): Form
    {
        return $form
            
            ->schema([
                Forms\Components\Section::make('Contract Details')
                    ->schema([
                         Forms\Components\Select::make('contract_id')
                            ->relationship('contract', 'id')
                            ->label('Contract')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $contract = \App\Models\Contract::find($state);
                                    if ($contract) {
                                        $set('amount', $contract->amount);
                                        $set('tenant_id', $contract->tenant_id);
                                        $set('due_date', $contract->due_date);
                                    }
                                } else {
                                    $set('amount', null);
                                    $set('tenant_id', null);
                                    $set('due_date', null);
                                }
                            

                            }),
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'name')
                            ->label('Tenant Name')
                            ->required()
                            ->disabled()
                            ->reactive()
                            ->afterStateHydrated(function ($component, $state, $set, $get) {
                                // If editing, keep the current tenant_id
                                if ($state) {
                                    return;
                                }
                                $contractId = $get('contract_id');
                                if ($contractId) {
                                    $contract = \App\Models\Contract::find($contractId);
                                    if ($contract && $contract->tenant_id) {
                                        $set('tenant_id', $contract->tenant_id);
                                    }
                                }
                            })
                    ]),
                Forms\Components\Section::make('Invoice Details')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->required(),
                //            ->default(function () {
                //                return \App\Models\Invoice::max('invoice_number') + 1;
                //            }),
                        Forms\Components\DatePicker::make('issue_date')
                            ->default(now())
                            ->label('Issue Date')
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateHydrated(function ($component, $state, $set, $get) {
                                // If editing, keep the current amount
                                if ($state) {
                                    return;
                                }
                                $contractId = $get('contract_id');
                                if ($contractId) {
                                    $contract = \App\Models\Contract::find($contractId);
                                    if ($contract) {
                                        $set('amount', $contract->amount);
                                    }
                                }
                            })
                            ->disabled(fn ($get) => $get('contract_id') ? true : false),
                       
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                        'pending' => 'Pending',
                        'canceled' => 'Canceled',
                        'on_hold' => 'On Hold',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Due Date')
                            ->required()
                            ->reactive()
                            ->afterStateHydrated(function ($component, $state, $set, $get) {
                                // If editing, keep the current due_date
                                if ($state) {
                                    return;
                                }
                                $contractId = $get('contract_id');
                                if ($contractId) {
                                    $contract = \App\Models\Contract::find($contractId);
                                    if ($contract && $contract->due_date) {
                                        $set('due_date', $contract->due_date);
                                    }
                                }
                            })
                            ->disabled(fn ($get) => $get('contract_id') ? true : false)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                // Update due_date if contract_id changes
                                $contractId = $get('contract_id');
                                if ($contractId) {
                                    $contract = \App\Models\Contract::find($contractId);
                                    if ($contract && $contract->due_date) {
                                        $set('due_date', $contract->due_date);
                                    }
                                } else {
                                    $set('due_date', null);
                                }
                            }),
                    ]),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->placeholder('Any additional notes or comments'),
                        Forms\Components\Select::make('generation_type')
                            ->label('Generation Type')
                            ->options([
                                'automatic' => 'Automatic',
                                'manual' => 'Manual',
                            ])
                            ->required(),
                    ]),


            ]);
   

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice Number')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('contract.contract_number')
                    ->label('Contract Number')
                    ->sortable()
                    ->searchable(),
            
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant Name')
                    ->sortable()
                    ->searchable(),
                   
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->sortable()
                    ->searchable(),
               
                Tables\Columns\TextColumn::make('issue_date')
                    ->label('Issue Date')
                    ->date()
                    ->sortable(),
         
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),
           
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                        'pending' => 'Pending',
                        'canceled' => 'Canceled',
                        'on_hold' => 'On Hold',
                        default => $state,
                    })
                    ->sortable(),
            //paid', 'pending', 'unpaid', 'canceled', 'on_hold'
            ])
            ->filters([
                //

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), 
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}

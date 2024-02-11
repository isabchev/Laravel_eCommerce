<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;

use Faker\Provider\Text;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

use Filament\Tables\Filters\TernaryFilter;

use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Card::make()->schema([
                        Toggle::make('status')
                            ->label('Enable Product')
                            ->default(true),

                        TextInput::make('title')
                            ->required()
                            ->autofocus(),

                        TextInput::make('slug')
                            ->unique(Product::class, 'slug', ignoreRecord: true)
                            ->afterStateUpdated(function ($get, $set, $state) {
                                if ($get('slug') == '') {
                                    $set('slug', Str::slug($get('title')));
                                }
                            }),

                        Select::make('categories')
                            ->preload()
                            ->multiple()
                            ->relationship('categories', 'title'),

                        TinyEditor::make('description')
                            ->minHeight(300)
                            ->required(),

                        TextInput::make('price')
                            ->numeric()
                            ->prefix('$')
                            ->mask(fn ($mask) => $mask->patternBlocks([
                                'money' => fn ($mask) => $mask
                                    ->numeric()
                                    ->mapToDecimalSeparator(['.'])
                                    ->decimalPlaces(2)
                                    ->padFractionalZeros()
                                 ])
                                ->pattern('money')
                            )
                            ->required(),

                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->required(),

                        TextInput::make('weight')
                            ->numeric()

                    ])
                ]
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('price')
                    ->money('USD', true)
                    ->sortable(),
                TextColumn::make('quantity')
                    ->sortable(),
                IconColumn::make('status')
                    ->sortable()
                    ->boolean()
            ])
            ->filters([
                TernaryFilter::make('status')
                    ->trueLabel('Enabled Products')
                    ->falseLabel('Disabled Products')
                    ->boolean()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

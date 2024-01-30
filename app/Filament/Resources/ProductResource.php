<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;

use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\Card;

use Filament\Forms\Components\TextInput;

use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Card::make()->schema([
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

                        TinyEditor::make('description')
                            ->minHeight(300)
                            ->required(),

                        TextInput::make('price')
                            ->numeric()
                            ->prefix('$')
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
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD', true)
                    ->sortable()
            ])
            ->filters([
                //
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

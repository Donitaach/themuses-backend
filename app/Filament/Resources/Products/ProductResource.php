<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use BackedEnum;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-sparkles';
    
    protected static UnitEnum|string|null $navigationGroup = 'Shop Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // --- DATA UTAMA ---
                Forms\Components\TextInput::make('name')
                    ->label('Nama Perhiasan')
                    ->required(),

                Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('IDR')
                    ->required(),

                Forms\Components\TextInput::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->required(),

                // --- SPESIFIKASI PERHIASAN ---
                Forms\Components\TextInput::make('material')
                    ->label('Material')
                    ->placeholder('Contoh: Emas Putih 18K'),

                Forms\Components\TextInput::make('weight')
                    ->label('Berat (gram)')
                    ->numeric()
                    ->suffix('gr'),

                Forms\Components\TextInput::make('gemstone')
                    ->label('Batu Permata')
                    ->placeholder('Contoh: Berlian VVS1'),

                Forms\Components\TextInput::make('size')
                    ->label('Ukuran')
                    ->placeholder('Contoh: 12, 14, 16'),

                // --- MEDIA & DESKRIPSI ---
                Forms\Components\FileUpload::make('image_url')
                    ->label('Foto Produk')
                    ->image()
                    ->directory('products')
                    ->disk('public'),

                Forms\Components\Textarea::make('description')
                    ->rows(5)
                    ->label('Deskripsi Lengkap'),
            ]);
    }

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\ImageColumn::make('image_url')
                ->label('Foto')
                ->circular(),
            Tables\Columns\TextColumn::make('name')
                ->label('Produk')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('price')
                ->label('Harga')
                ->money('IDR')
                ->sortable(),
        ])
        ->actions([
            ViewAction::make()
                ->icon('heroicon-o-eye'),
            EditAction::make()
                ->icon('heroicon-o-pencil'),
            DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ]) 
        ->bulkActions([]);
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
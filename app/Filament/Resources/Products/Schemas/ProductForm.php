<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Components\FileUpload;
use Filament\Schemas\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama')
                    ->description('Nama, Harga, dan Stok Perhiasan')
                    ->components([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('price')
                            ->numeric()
                            ->prefix('IDR')
                            ->required(),
                        TextInput::make('stock')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Section::make('Spesifikasi Fisik')
                    ->description('Detail teknis material perhiasan')
                    ->components([
                        TextInput::make('material')
                            ->placeholder('Contoh: Emas Putih 18K / Perak 925'),
                        TextInput::make('weight')
                            ->numeric()
                            ->suffix('gram'),
                        TextInput::make('gemstone')
                            ->placeholder('Contoh: Berlian VVS1 / Safir'),
                        TextInput::make('size')
                            ->placeholder('Contoh: 12, 14, 16 atau All Size'),
                    ])->columns(2),

                Section::make('Media & Deskripsi')
                    ->components([
                        FileUpload::make('image_url')
                            ->label('Foto Produk')
                            ->image()
                            ->imageEditor() // Memungkinkan crop gambar di admin
                            ->directory('products')
                            ->disk('public')
                            ->visibility('public')
                            ->columnSpanFull(),
                        RichEditor::make('description')
                            ->columnSpanFull(),
                    ])
            ]);
    }
}
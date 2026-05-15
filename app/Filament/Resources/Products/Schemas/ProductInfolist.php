<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Produk')
                    ->components([
                        ImageEntry::make('image_url')
                            ->label('Tampilan Perhiasan')
                            ->circular(),
                        TextEntry::make('name')->weight('bold')->size('lg'),
                        TextEntry::make('price')->money('IDR')->color('primary'),
                        TextEntry::make('stock')->suffix(' pcs'),
                    ])->columns(2),

                Section::make('Spesifikasi Material')
                    ->components([
                        TextEntry::make('material')->placeholder('-'),
                        TextEntry::make('weight')->suffix(' gram')->placeholder('-'),
                        TextEntry::make('gemstone')->placeholder('-'),
                        TextEntry::make('size')->placeholder('-'),
                        TextEntry::make('description')
                            ->html()
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }
}
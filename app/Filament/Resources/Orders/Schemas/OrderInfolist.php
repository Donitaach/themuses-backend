<?php

namespace App\Filament\Resources\Orders\OrderResource\Pages;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    // Lokasi: App\Filament\Resources\Orders\Schemas\OrderInfolist.php

public static function configure(Schema $schema): Schema
{
    return $schema
        ->components([
            Section::make('Order Items')
                ->components([
                    RepeatableEntry::make('items') // Sesuai relasi di model Order
                        ->schema([
                            TextEntry::make('product.name')->label('Produk'),
                            TextEntry::make('quantity')->label('Qty'),
                            TextEntry::make('price')->money('IDR')->label('Harga Satuan'),
                            TextEntry::make('subtotal')->money('IDR')->label('Total'),
                        ])->columns(4)
                ])
        ]);
}
}

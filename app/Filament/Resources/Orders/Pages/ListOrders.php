<?php

namespace App\Filament\Resources\Orders\OrderResource\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    /**
     * Fungsi ini mengatur tombol aksi di bagian header (pojok kanan atas).
     * Dengan mengembalikan array kosong, tombol "New order" akan dihapus.
     */
    protected function getHeaderActions(): array
    {
        return [
            // Kosongkan untuk menghilangkan tombol New Order
        ];
    }
}
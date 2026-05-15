<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\OrderResource\Pages;
use App\Models\Order;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static UnitEnum|string|null $navigationGroup = 'Shop Management';

    // --- TAMBAHKAN DI SINI ---
    public static function canCreate(): bool
    {
        return false;
    }
    // -------------------------

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Infolists\Components\Section::make('Informasi Customer')
                    ->schema([
                        Infolists\Components\TextEntry::make('customer_name')->label('Nama Pelanggan'),
                        Infolists\Components\TextEntry::make('phone')->label('Nomor Telepon'),
                        Infolists\Components\TextEntry::make('address')->label('Alamat Lengkap')->columnSpanFull(),
                    ])->columns(2),
                
                Infolists\Components\Section::make('Status Pembayaran')
                    ->schema([
                        Infolists\Components\TextEntry::make('external_id')->label('Xendit ID'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'PAID' => 'success',
                                'PENDING' => 'warning',
                                'EXPIRED' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('total_price')->money('IDR'),
                        Infolists\Components\TextEntry::make('invoice_url')
                            ->label('Link Invoice')
                            ->formatStateUsing(fn () => 'Buka Link Xendit')
                            ->url(fn ($record) => $record->invoice_url)
                            ->openUrlInNewTab()
                            ->color('primary'),
                    ])->columns(2),

                Infolists\Components\Section::make('Item Pesanan')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('product.name')->label('Produk'),
                                Infolists\Components\TextEntry::make('quantity')->label('Jumlah'),
                                Infolists\Components\TextEntry::make('price')->money('IDR')->label('Harga Satuan'),
                                Infolists\Components\TextEntry::make('subtotal')->money('IDR')->label('Subtotal'),
                            ])->columns(4)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pesan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('items.product.name')
                    ->label('Produk Dipesan')
                    ->listWithLineBreaks()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PAID' => 'success',
                        'PENDING' => 'warning',
                        'EXPIRED' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Bayar')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('id')
                    ->label('Aksi')
                    ->html()
                    ->formatStateUsing(fn () => '
                        <div class="flex items-center gap-1.5 text-primary-600 hover:text-primary-500 transition font-bold uppercase text-xs cursor-pointer">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            Lihat Detail
                        </div>
                    ')
                    ->url(fn ($record): string => static::getUrl('view', ['record' => $record])),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([]) 
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
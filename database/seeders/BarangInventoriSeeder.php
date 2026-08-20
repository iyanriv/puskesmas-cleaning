<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BarangInventori;

class BarangInventoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Bayclin 1L', 'Botol'],
            ['Baygon 600mL', 'Kaleng'],
            ['Botol Pump Hand Soap', 'Buah'],
            ['Botol Sprayer', 'Buah'],
            ['Bowl Cleaner (Pembersih Kaca)', 'Galon'],
            ["Buffing Fad 3 M Merah 16'", 'Buah'],
            ["Buffing Fad 3 M Putih 16'", 'Buah'],
            ['Celemek', 'Buah'],
            ['Detergen Rinso 46gr', 'Buah'],
            ['Ember Plastik', 'Buah'],
            ['Filter masker respirator', 'Buah'],
            ['Floor Cleaner (Pembersih Lantai)', 'Galon'],
            ['Frame Lobby Duster', 'Buah'],
            ['Gagang Pel', 'Buah'],
            ['Glass Cleaner', 'Galon'],
            ['Kain Lap Kanebo', 'Buah'],
            ['Kain Lap Kotak-kotak Brutop', 'Buah'],
            ['Kain Lobby Duster', 'Buah'],
            ['Kain Microfiber', 'Buah'],
            ['Kain Pel Biru', 'Buah'],
            ['Kain Pel Merah', 'Buah'],
            ['Kamper Bola Pingpong', 'Pak'],
            ['Kamper Bola Tenis', 'Pak'],
            ['Karbol Wangi', 'Galon'],
            ['Kispray', 'Pouch'],
            ['Masker Resporator', 'Buah'],
            ['Pengki Plastik', 'Buah'],
            ['Plastik Hitam 50 x 75', 'Pak'],
            ['Plastik Hitam 60 x 100', 'Pak'],
            ['Plastik Kresek Hitam Uk 35', 'Pak'],
            ['Plastik Kresek Kuning Uk 35', 'Pak'],
            ['Plastik Kuning Uk 60 x 100', 'Pak'],
            ['Plastik Mop Holder', 'Buah'],
            ['Pledge Spray', 'Botol'],
            ['Rinso Matic Cair 4,5 L', 'Galon'],
            ['Sabun Cream Ekonomi', 'Bungkus'],
            ['Sabun Cuci Piring', 'Galon'],
            ['Sabun Cuci Tangan (Handsoap)', 'Galon'],
            ['Sapu Ijuk', 'Buah'],
            ['Sapu Lidi', 'Buah'],
            ['Sarung Tangan Karet', 'Pasang'],
            ['Sikat gagang panjang', 'Buah'],
            ['Sikat Tangan Gagang Nagata', 'Buah'],
            ['Sikat WC Bulat', 'Buah'],
            ['SOS Pembersih lantai 4L', 'Galon'],
            ['Stella AC Gantung', 'Buah'],
            ['Stella Matic Refill', 'Kaleng'],
            ['Tapas Gagang', 'Buah'],
            ['Tapas Hijau', 'Buah'],
            ['Tapas Sponge Politex', 'Buah'],
            ['Tissu Hand Towel/24', 'Pak'],
            ['Tissu Kotak', 'Pak'],
            ['Tissu Roll (Kecil)', 'Roll'],
            ['Tissu Toilet (Besar)', 'Roll'],
            ['Tissu Wajah', 'Pak'],
            ['Vanish 750 ml', 'Pouch'],
            ['Wet Floor Sign', 'Buah'],
            ['Wifer Kaca', 'Buah'],
            ['Wifer Lantai Dragon', 'Buah']
        ];

        foreach ($data as $item) {
            BarangInventori::firstOrCreate(
                ['nama_barang' => $item[0]],
                [
                    'satuan' => $item[1],
                    'stok_saat_ini' => 0, 
                    'stok_minimum' => 5,
                ]
            );
        }
    }
}

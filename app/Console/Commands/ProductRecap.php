<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductStockRecap;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProductRecap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:recap {--date= : Tanggal rekap (format: Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create daily product stock recap';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Tentukan tanggal rekap (hari ini jika tidak ada opsi tanggal)
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        $formattedDate = $date->format('Y-m-d');

        $this->info("Membuat rekap stok produk untuk tanggal: {$formattedDate}");

        // Hapus rekap yang sudah ada untuk tanggal yang sama (jika ada)
        $deleted = ProductStockRecap::where('date', $formattedDate)->delete();
        if ($deleted > 0) {
            $this->info("Menghapus {$deleted} rekap yang sudah ada untuk tanggal {$formattedDate}");
        }

        // Ambil semua produk yang tidak dihapus
        $products = Product::whereNull('deleted_at')->get();
        $count = 0;

        // Buat progress bar
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        // Buat rekap untuk setiap produk
        foreach ($products as $product) {
            ProductStockRecap::create([
                'date' => $formattedDate,
                'product_id' => $product->id,
                'quantity' => $product->stock,
            ]);

            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Alternatif menggunakan query builder untuk performa lebih baik pada dataset besar
        // Uncomment jika diperlukan
        /*
        DB::statement("
            INSERT INTO product_stock_recaps (
                date,
                product_id,
                quantity,
                created_at,
                updated_at
            )
            SELECT
                ?,
                id as product_id,
                stock as quantity,
                NOW(),
                NOW()
            FROM products
            WHERE deleted_at IS NULL
        ", [$formattedDate]);
        */

        $this->info("Berhasil membuat {$count} rekap stok produk untuk tanggal {$formattedDate}.");
    }
}


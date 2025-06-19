<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProductRecap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:recap';

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
        DB::statement("
            INSERT INTO product_stock_recaps (
                date,
                product_id, 
                quantity,
                created_at,
                updated_at
            )
            SELECT 
                CURRENT_DATE,
                id as product_id,
                stock as quantity,
                datetime(CURRENT_TIMESTAMP, '+7 hours'),
                datetime(CURRENT_TIMESTAMP, '+7 hours')
            FROM products 
            WHERE deleted_at IS NULL
        ");

        $this->info('Product stock recap created successfully.');
    }
}

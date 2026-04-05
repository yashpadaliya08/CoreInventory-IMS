<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $lowStockCount = DB::table('products')
                    ->leftJoin('stock_ledger', 'products.id', '=', 'stock_ledger.product_id')
                    ->select('products.id', 'products.reorder_level')
                    ->groupBy('products.id', 'products.reorder_level')
                    ->havingRaw('COALESCE(SUM(stock_ledger.quantity_change), 0) < products.reorder_level')
                    ->get()
                    ->count();

                $view->with('globalLowStockCount', $lowStockCount);
            } else {
                $view->with('globalLowStockCount', 0);
            }
        });
    }
}

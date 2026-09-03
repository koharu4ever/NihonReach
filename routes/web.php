<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\InquiryController;
use App\Http\Controllers\Site\ProductCatalogController;
use Illuminate\Support\Facades\Route;

$publicRoutes = function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::get('products', [ProductCatalogController::class, 'index'])->name('products.index');
    Route::get('products/{slug}', [ProductCatalogController::class, 'show'])
        ->where('slug', '[A-Za-z0-9_-]+')
        ->name('products.show');
    Route::view('about', 'public.about')->name('about');
    Route::get('inquiries/create', [InquiryController::class, 'create'])->name('inquiries.create');
    Route::post('inquiries', [InquiryController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('inquiries.store');
    Route::get('inquiries/thanks', [InquiryController::class, 'thanks'])->name('inquiries.thanks');
};

Route::middleware('locale:ja')->group($publicRoutes);
Route::prefix('zh')->name('zh.')->middleware('locale:zh')->group($publicRoutes);

Route::middleware(['locale:zh', 'auth', 'verified', 'admin'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('categories', ProductCategoryController::class)
            ->parameters(['categories' => 'product_category'])
            ->except('show');
        Route::resource('products', ProductController::class)
            ->except('show');
        Route::resource('inquiries', AdminInquiryController::class)
            ->only(['index', 'show', 'update']);
    });
});

require __DIR__.'/settings.php';

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslation;
use App\Models\ProductTranslation;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): Response
    {
        $recentProducts = Product::query()
            ->with([
                'translations' => fn ($query) => $query
                    ->where('locale', ProductTranslation::LOCALE_CHINESE),
                'category:id,name',
                'category.translations' => fn ($query) => $query
                    ->where('locale', ProductCategoryTranslation::LOCALE_CHINESE),
            ])
            ->latest('updated_at')
            ->limit(5)
            ->get([
                'id',
                'product_category_id',
                'name',
                'sku',
                'is_active',
                'updated_at',
            ])
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'name' => (string) $product->translated(
                    'name',
                    ProductTranslation::LOCALE_CHINESE,
                ),
                'sku' => $product->sku,
                'is_active' => $product->is_active,
                'updated_at' => $product->updated_at,
                'category' => [
                    'id' => $product->category->id,
                    'name' => (string) $product->category->translated(
                        'name',
                        ProductCategoryTranslation::LOCALE_CHINESE,
                    ),
                ],
            ]);

        return Inertia::render('Dashboard', [
            'stats' => [
                'categories' => ProductCategory::query()->count(),
                'products' => Product::query()->count(),
                'publishedProducts' => Product::query()
                    ->where('is_active', true)
                    ->whereHas('category', fn ($query) => $query->where('is_active', true))
                    ->count(),
                'featuredProducts' => Product::query()->where('is_featured', true)->count(),
                'newInquiries' => Inquiry::query()->where('status', Inquiry::STATUS_NEW)->count(),
            ],
            'recentProducts' => $recentProducts,
        ]);
    }
}

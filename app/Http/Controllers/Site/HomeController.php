<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $categories = ProductCategory::query()
            ->where('is_active', true)
            ->whereHas('products', fn ($query) => $query->where('is_active', true))
            ->with('translations:id,product_category_id,locale,name,description')
            ->withCount([
                'products as active_products_count' => fn ($query) => $query->where('is_active', true),
            ])
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'description']);

        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->with([
                'translations:id,product_id,locale,name,summary,description,specifications',
                'category:id,name,slug',
                'category.translations:id,product_category_id,locale,name,description',
            ])
            ->orderBy('sort_order')
            ->limit(3)
            ->get([
                'id',
                'product_category_id',
                'name',
                'slug',
                'sku',
                'summary',
                'image_path',
                'specifications',
            ]);

        return view('public.home', compact('categories', 'featuredProducts'));
    }
}

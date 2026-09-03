<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->query('category') ?? '';
        abort_unless(is_string($category), 400);
        $selectedCategory = trim($category);

        $categories = ProductCategory::query()
            ->where('is_active', true)
            ->whereHas('products', fn ($query) => $query->where('is_active', true))
            ->with('translations:id,product_category_id,locale,name,description')
            ->withCount([
                'products as active_products_count' => fn ($query) => $query->where('is_active', true),
            ])
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        $products = Product::query()
            ->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->when(
                $selectedCategory !== '',
                fn ($query) => $query->whereHas(
                    'category',
                    fn ($categoryQuery) => $categoryQuery->where('slug', $selectedCategory),
                ),
            )
            ->with([
                'translations:id,product_id,locale,name,summary,description,specifications',
                'category:id,name,slug',
                'category.translations:id,product_category_id,locale,name,description',
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        return view('public.products.index', compact(
            'categories',
            'products',
            'selectedCategory',
        ));
    }

    public function show(string $slug): View
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->with([
                'translations:id,product_id,locale,name,summary,description,specifications',
                'category:id,name,slug',
                'category.translations:id,product_category_id,locale,name,description',
            ])
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->where('product_category_id', $product->product_category_id)
            ->whereKeyNot($product->id)
            ->where('is_active', true)
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

        return view('public.products.show', compact('product', 'relatedProducts'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslation;
use App\Models\ProductTranslation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $products = Product::query()
            ->with([
                'translations' => fn ($query) => $query
                    ->where('locale', ProductTranslation::LOCALE_CHINESE),
                'category:id,name',
                'category.translations' => fn ($query) => $query
                    ->where('locale', ProductCategoryTranslation::LOCALE_CHINESE),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'product_category_id',
                'name',
                'slug',
                'sku',
                'summary',
                'is_featured',
                'is_active',
                'sort_order',
            ])
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'name' => (string) $product->translated(
                    'name',
                    ProductTranslation::LOCALE_CHINESE,
                ),
                'slug' => $product->slug,
                'sku' => $product->sku,
                'summary' => (string) $product->translated(
                    'summary',
                    ProductTranslation::LOCALE_CHINESE,
                ),
                'is_featured' => $product->is_featured,
                'is_active' => $product->is_active,
                'sort_order' => $product->sort_order,
                'category' => [
                    'id' => $product->category->id,
                    'name' => (string) $product->category->translated(
                        'name',
                        ProductCategoryTranslation::LOCALE_CHINESE,
                    ),
                ],
            ]);

        return Inertia::render('admin/products/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/products/Create', [
            'categories' => $this->categoryOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $translation */
        $translation = $request->validated('translations.zh');
        $attributes = $request->safe()->except('translations');

        DB::transaction(function () use ($attributes, $translation): void {
            $product = Product::query()->create($attributes);

            $product->translations()->create([
                'locale' => ProductTranslation::LOCALE_CHINESE,
                ...$translation,
            ]);
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '产品已创建。',
        ]);

        return to_route('admin.products.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): Response
    {
        $translation = $product->translations()->firstOrNew([
            'locale' => ProductTranslation::LOCALE_CHINESE,
        ]);

        return Inertia::render('admin/products/Edit', [
            'product' => [
                ...$product->only([
                    'id',
                    'product_category_id',
                    'name',
                    'slug',
                    'sku',
                    'summary',
                    'description',
                    'image_path',
                    'specifications',
                    'is_featured',
                    'is_active',
                    'sort_order',
                ]),
                'translations' => [
                    ProductTranslation::LOCALE_CHINESE => [
                        'name' => (string) $translation->name,
                        'summary' => (string) $translation->summary,
                        'description' => (string) $translation->description,
                        'specifications' => $translation->specifications ?? [],
                    ],
                ],
            ],
            'categories' => $this->categoryOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        /** @var array<string, mixed> $translation */
        $translation = $request->validated('translations.zh');
        $attributes = $request->safe()->except('translations');

        DB::transaction(function () use ($product, $attributes, $translation): void {
            $product->update($attributes);
            $product->translations()->updateOrCreate(
                ['locale' => ProductTranslation::LOCALE_CHINESE],
                $translation,
            );
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '产品已更新。',
        ]);

        return to_route('admin.products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '产品已删除。',
        ]);

        return to_route('admin.products.index');
    }

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    private function categoryOptions(): Collection
    {
        return ProductCategory::query()
            ->with([
                'translations' => fn ($query) => $query
                    ->where('locale', ProductCategoryTranslation::LOCALE_CHINESE),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (ProductCategory $category): array => [
                'id' => $category->id,
                'name' => (string) $category->translated(
                    'name',
                    ProductCategoryTranslation::LOCALE_CHINESE,
                ),
            ]);
    }
}

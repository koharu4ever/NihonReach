<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductCategoryRequest;
use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $categories = ProductCategory::query()
            ->select([
                'id',
                'name',
                'slug',
                'is_active',
                'sort_order',
            ])
            ->with([
                'translations' => fn ($query) => $query
                    ->where('locale', ProductCategoryTranslation::LOCALE_CHINESE),
            ])
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (ProductCategory $category): array => [
                'id' => $category->id,
                'name' => (string) $category->translated(
                    'name',
                    ProductCategoryTranslation::LOCALE_CHINESE,
                ),
                'slug' => $category->slug,
                'is_active' => $category->is_active,
                'sort_order' => $category->sort_order,
                'products_count' => $category->products_count,
            ]);

        return Inertia::render('admin/categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/categories/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCategoryRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $translation */
        $translation = $request->validated('translations.zh');
        $attributes = $request->safe()->except('translations');

        DB::transaction(function () use ($attributes, $translation): void {
            $category = ProductCategory::query()->create($attributes);

            $category->translations()->create([
                'locale' => ProductCategoryTranslation::LOCALE_CHINESE,
                ...$translation,
            ]);
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '产品分类已创建。',
        ]);

        return to_route('admin.categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory): Response
    {
        $translation = $productCategory->translations()->firstOrNew([
            'locale' => ProductCategoryTranslation::LOCALE_CHINESE,
        ]);

        return Inertia::render('admin/categories/Edit', [
            'category' => [
                ...$productCategory->only([
                    'id',
                    'name',
                    'slug',
                    'description',
                    'is_active',
                    'sort_order',
                ]),
                'translations' => [
                    ProductCategoryTranslation::LOCALE_CHINESE => [
                        'name' => (string) $translation->name,
                        'description' => (string) $translation->description,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductCategoryRequest $request, ProductCategory $productCategory): RedirectResponse
    {
        /** @var array<string, mixed> $translation */
        $translation = $request->validated('translations.zh');
        $attributes = $request->safe()->except('translations');

        DB::transaction(function () use ($productCategory, $attributes, $translation): void {
            $productCategory->update($attributes);
            $productCategory->translations()->updateOrCreate(
                ['locale' => ProductCategoryTranslation::LOCALE_CHINESE],
                $translation,
            );
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '产品分类已更新。',
        ]);

        return to_route('admin.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        if ($productCategory->products()->exists()) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => '该分类下已有产品，无法删除。',
            ]);

            return back();
        }

        $productCategory->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '产品分类已删除。',
        ]);

        return to_route('admin.categories.index');
    }
}

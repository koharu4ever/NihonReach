<?php

namespace Tests\Feature\Models;

use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslation;
use Database\Seeders\ProductCategorySeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_category_attributes_are_mass_assignable_and_cast(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'Demo Milling Tools',
            'slug' => 'demo-milling-tools',
            'description' => 'Portfolio demo category.',
            'is_active' => false,
            'sort_order' => 15,
        ]);

        $category->refresh();

        $this->assertFalse($category->is_active);
        $this->assertSame(15, $category->sort_order);
        $this->assertSame('Portfolio demo category.', $category->description);
    }

    public function test_product_category_slugs_must_be_unique(): void
    {
        ProductCategory::factory()->create(['slug' => 'unique-demo-category']);

        $this->expectException(QueryException::class);

        ProductCategory::factory()->create(['slug' => 'unique-demo-category']);
    }

    public function test_product_category_returns_chinese_translation_and_falls_back_to_japanese(): void
    {
        $category = ProductCategory::factory()->create([
            'name' => '日本語カテゴリ',
            'description' => '日本語の説明です。',
        ]);
        $category->translations()->create([
            'locale' => ProductCategoryTranslation::LOCALE_CHINESE,
            'name' => '中文分类',
            'description' => '中文说明。',
        ]);
        $category->load('translations');

        $this->assertSame('中文分类', $category->translated('name', 'zh'));
        $this->assertSame('中文说明。', $category->translated('description', 'zh'));
        $this->assertSame('日本語カテゴリ', $category->translated('name', 'ja'));
        $this->assertSame('日本語カテゴリ', $category->translated('name', 'en'));
    }

    public function test_product_category_seeder_is_idempotent(): void
    {
        $this->seed(ProductCategorySeeder::class);
        $this->seed(ProductCategorySeeder::class);

        $this->assertDatabaseCount('product_categories', 4);
        $this->assertDatabaseCount('product_category_translations', 4);
        $this->assertDatabaseHas('product_categories', [
            'name' => '超硬エンドミル',
            'slug' => 'solid-carbide-end-mills',
            'is_active' => true,
            'sort_order' => 10,
        ]);
        $this->assertDatabaseHas('product_categories', [
            'name' => '旋削工具',
            'slug' => 'turning-tools',
            'is_active' => true,
            'sort_order' => 40,
        ]);
        $this->assertDatabaseHas('product_category_translations', [
            'locale' => ProductCategoryTranslation::LOCALE_CHINESE,
            'name' => '硬质合金立铣刀',
        ]);
    }
}

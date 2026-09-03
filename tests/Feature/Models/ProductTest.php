<?php

namespace Tests\Feature\Models;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTranslation;
use Database\Seeders\ProductCategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_a_category_and_casts_attributes(): void
    {
        $category = ProductCategory::factory()->create();
        $specifications = [
            ['label' => 'Demo diameter', 'value' => '6 mm'],
        ];

        $product = Product::factory()
            ->for($category, 'category')
            ->create([
                'specifications' => $specifications,
                'is_featured' => true,
                'is_active' => false,
                'sort_order' => 25,
            ]);

        $product->refresh();

        $this->assertTrue($product->category->is($category));
        $this->assertSame($specifications, $product->specifications);
        $this->assertTrue($product->is_featured);
        $this->assertFalse($product->is_active);
        $this->assertSame(25, $product->sort_order);
    }

    public function test_product_slugs_must_be_unique(): void
    {
        Product::factory()->create(['slug' => 'unique-demo-product']);

        $this->expectException(QueryException::class);

        Product::factory()->create(['slug' => 'unique-demo-product']);
    }

    public function test_product_translation_casts_specifications_and_falls_back_to_japanese(): void
    {
        $product = Product::factory()->create([
            'name' => '日本語製品',
            'summary' => '日本語の概要です。',
        ]);
        $specifications = [
            ['label' => '刀具直径', 'value' => '6 mm'],
        ];
        $product->translations()->create([
            'locale' => ProductTranslation::LOCALE_CHINESE,
            'name' => '中文产品',
            'summary' => '中文简介。',
            'description' => '中文说明。',
            'specifications' => $specifications,
        ]);
        $product->load('translations');

        $translation = $product->translations->sole();

        $this->assertSame($specifications, $translation->specifications);
        $this->assertSame('中文产品', $product->translated('name', 'zh'));
        $this->assertSame('日本語製品', $product->translated('name', 'ja'));
        $this->assertSame('日本語の概要です。', $product->translated('summary', 'en'));
    }

    public function test_product_skus_must_be_unique(): void
    {
        Product::factory()->create(['sku' => 'NR-DEMO-UNIQUE']);

        $this->expectException(QueryException::class);

        Product::factory()->create(['sku' => 'NR-DEMO-UNIQUE']);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $category = ProductCategory::factory()->create();
        Product::factory()->for($category, 'category')->create();

        $this->expectException(QueryException::class);

        $category->delete();
    }

    public function test_product_seeder_is_idempotent(): void
    {
        $this->seed(ProductCategorySeeder::class);
        $this->seed(ProductSeeder::class);
        $this->seed(ProductCategorySeeder::class);
        $this->seed(ProductSeeder::class);

        $this->assertDatabaseCount('product_categories', 4);
        $this->assertDatabaseCount('products', 6);
        $this->assertDatabaseCount('product_category_translations', 4);
        $this->assertDatabaseCount('product_translations', 6);
        $this->assertDatabaseHas('products', [
            'sku' => 'NR-DEMO-EM-060',
            'slug' => 'nr-demo-4-flute-end-mill-6mm',
            'is_featured' => true,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('products', [
            'sku' => 'NR-DEMO-TH-2525',
            'slug' => 'nr-demo-external-turning-holder',
            'is_featured' => false,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('product_translations', [
            'locale' => ProductTranslation::LOCALE_CHINESE,
            'name' => 'NR-Demo 四刃硬质合金立铣刀 6mm',
        ]);
    }
}

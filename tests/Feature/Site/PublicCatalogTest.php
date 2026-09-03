<?php

namespace Tests\Feature\Site;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_only_featured_products_from_active_categories(): void
    {
        $activeCategory = ProductCategory::factory()->create();
        $visibleProduct = Product::factory()
            ->for($activeCategory, 'category')
            ->featured()
            ->create([
                'name' => 'Visible Featured Demo',
                'image_path' => '/images/products/home-featured-demo.webp',
            ]);
        Product::factory()
            ->for($activeCategory, 'category')
            ->featured()
            ->inactive()
            ->create(['name' => 'Hidden Inactive Demo']);

        $inactiveCategory = ProductCategory::factory()->inactive()->create();
        Product::factory()
            ->for($inactiveCategory, 'category')
            ->featured()
            ->create(['name' => 'Hidden Category Demo']);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee($visibleProduct->name);
        $response->assertDontSee('Hidden Inactive Demo');
        $response->assertDontSee('Hidden Category Demo');
        $response->assertSee('Portfolio Demo');
        $response->assertSee('images/site/home-hero-precision-tools.webp', false);
        $response->assertSee('images/site/og-cover.webp', false);
        $response->assertSee($visibleProduct->image_path, false);
    }

    public function test_catalog_hides_inactive_products_and_categories(): void
    {
        $activeCategory = ProductCategory::factory()->create();
        Product::factory()
            ->for($activeCategory, 'category')
            ->create(['name' => 'Visible Catalog Demo']);
        Product::factory()
            ->for($activeCategory, 'category')
            ->inactive()
            ->create(['name' => 'Hidden Product Demo']);

        $inactiveCategory = ProductCategory::factory()->inactive()->create();
        Product::factory()
            ->for($inactiveCategory, 'category')
            ->create(['name' => 'Hidden Category Product']);

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('Visible Catalog Demo');
        $response->assertDontSee('Hidden Product Demo');
        $response->assertDontSee('Hidden Category Product');
    }

    public function test_catalog_can_be_filtered_by_category_slug(): void
    {
        $firstCategory = ProductCategory::factory()->create([
            'slug' => 'first-demo-category',
        ]);
        $secondCategory = ProductCategory::factory()->create([
            'slug' => 'second-demo-category',
        ]);
        Product::factory()
            ->for($firstCategory, 'category')
            ->create(['name' => 'First Category Product']);
        Product::factory()
            ->for($secondCategory, 'category')
            ->create(['name' => 'Second Category Product']);

        $response = $this->get(route('products.index', [
            'category' => $firstCategory->slug,
        ]));

        $response->assertOk();
        $response->assertSee('First Category Product');
        $response->assertDontSee('Second Category Product');
    }

    public function test_active_product_detail_can_be_viewed(): void
    {
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->for($category, 'category')->create([
            'name' => 'Detailed Demo Product',
            'image_path' => '/images/products/nr-demo-carbide-drill-8mm.webp',
            'specifications' => [
                ['label' => 'Demo diameter', 'value' => '8 mm'],
            ],
        ]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertSee('Detailed Demo Product');
        $response->assertSee('Demo diameter');
        $response->assertSee('8 mm');
        $response->assertSee($product->image_path, false);
        $response->assertSee('<title>Detailed Demo Product | NihonReach</title>', false);
        $response->assertSee('rel="canonical" href="'.route('products.show', $product->slug).'"', false);
        $response->assertSee('property="og:image" content="'.asset(ltrim($product->image_path, '/')).'"', false);
    }

    public function test_inactive_product_or_category_returns_not_found(): void
    {
        $activeCategory = ProductCategory::factory()->create();
        $inactiveProduct = Product::factory()
            ->for($activeCategory, 'category')
            ->inactive()
            ->create();

        $inactiveCategory = ProductCategory::factory()->inactive()->create();
        $hiddenByCategory = Product::factory()
            ->for($inactiveCategory, 'category')
            ->create();

        $this->get(route('products.show', $inactiveProduct->slug))
            ->assertNotFound();
        $this->get(route('products.show', $hiddenByCategory->slug))
            ->assertNotFound();
    }

    public function test_about_page_explains_that_the_site_is_a_demo(): void
    {
        $response = $this->get(route('about'));

        $response->assertOk();
        $response->assertSee('Portfolio Demo');
        $response->assertSee('商用運営中の会社や顧客案件ではありません');
    }

    public function test_chinese_site_translates_fixed_copy_catalog_data_and_links(): void
    {
        $category = ProductCategory::factory()->create([
            'name' => '日本語カテゴリ',
            'slug' => 'localized-demo-category',
        ]);
        $category->translations()->create([
            'locale' => 'zh',
            'name' => '中文分类',
            'description' => '中文分类说明',
        ]);

        $product = Product::factory()
            ->for($category, 'category')
            ->featured()
            ->create([
                'name' => '日本語製品',
                'slug' => 'localized-demo-product',
            ]);
        $product->translations()->create([
            'locale' => 'zh',
            'name' => '中文演示产品',
            'summary' => '中文产品摘要',
            'description' => '中文产品说明',
            'specifications' => [
                ['label' => '直径', 'value' => '8 mm'],
            ],
        ]);

        $response = $this->get(route('zh.home'));

        $response->assertOk();
        $response->assertSee('<html lang="zh-CN">', false);
        $response->assertSee('精密切削工具 B2B 产品目录');
        $response->assertSee('中文分类');
        $response->assertSee('中文演示产品');
        $response->assertDontSee('日本語カテゴリ');
        $response->assertDontSee('日本語製品');
        $response->assertSee('href="'.route('zh.products.show', $product->slug).'"', false);
        $response->assertSee('hreflang="ja" href="'.route('home').'"', false);
        $response->assertSee('property="og:locale" content="zh_CN"', false);
    }

    public function test_language_switch_preserves_catalog_filter_query(): void
    {
        $category = ProductCategory::factory()->create([
            'slug' => 'switchable-demo-category',
        ]);
        Product::factory()->for($category, 'category')->create();

        $response = $this->get(route('zh.products.index', [
            'category' => $category->slug,
        ]));

        $response->assertOk();
        $response->assertSee(
            'href="'.route('products.index', ['category' => $category->slug]).'"',
            false,
        );
    }
}

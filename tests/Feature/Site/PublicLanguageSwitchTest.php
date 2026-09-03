<?php

namespace Tests\Feature\Site;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicLanguageSwitchTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string}> */
    public static function locales(): array
    {
        return ['Japanese' => [''], 'Chinese' => ['zh.']];
    }

    /** @return array<string, array{string, array<string, mixed>}> */
    public static function productQueries(): array
    {
        $cases = [];

        foreach (self::locales() as $locale => [$prefix]) {
            foreach ([
                'no query' => [],
                'scalar slug' => ['slug' => 'other-product'],
                'array slug' => ['slug' => ['other-product']],
                'nested slug' => ['slug' => ['nested' => ['other-product']]],
            ] as $name => $query) {
                $cases[$locale.' '.$name] = [$prefix, $query];
            }
        }

        return $cases;
    }

    /** @param array<string, mixed> $query */
    #[DataProvider('productQueries')]
    public function test_product_language_links_keep_the_route_slug(string $prefix, array $query): void
    {
        $product = Product::factory()->create(['slug' => 'current-demo-product']);
        $url = route($prefix.'products.show', $product->slug);
        $response = $this->get($url.($query === [] ? '' : '?'.http_build_query($query)));

        $response->assertOk();

        foreach (['', 'zh.'] as $target) {
            $response->assertSee(
                'hreflang="'.($target === '' ? 'ja' : 'zh-CN').'" href="'.
                route($target.'products.show', $product->slug).'"',
                false,
            );
        }
    }

    #[DataProvider('locales')]
    public function test_catalog_language_links_keep_filter_and_page_but_drop_unknown_query(string $prefix): void
    {
        $category = ProductCategory::factory()->create(['slug' => 'demo-category']);
        Product::factory()->count(10)->for($category, 'category')->create();
        $query = ['category' => $category->slug, 'page' => '2'];

        $response = $this->get(route($prefix.'products.index', [
            ...$query,
            'unrelated' => ['value'],
        ]));

        $response->assertOk();

        foreach (['', 'zh.'] as $target) {
            $response->assertSee(
                'hreflang="'.($target === '' ? 'ja' : 'zh-CN').'" href="'.
                e(route($target.'products.index', $query)).'"',
                false,
            );
        }
    }

    #[DataProvider('locales')]
    public function test_catalog_language_links_drop_array_page_query(string $prefix): void
    {
        $response = $this->get(route($prefix.'products.index', ['page' => ['2']]));

        $response->assertOk();

        foreach (['', 'zh.'] as $target) {
            $response->assertSee(
                'hreflang="'.($target === '' ? 'ja' : 'zh-CN').'" href="'.
                route($target.'products.index').'"',
                false,
            );
        }
    }

    #[DataProvider('locales')]
    public function test_inquiry_language_links_keep_product_preselection(string $prefix): void
    {
        $product = Product::factory()->create();
        $query = ['product' => $product->slug];

        $response = $this->get(route($prefix.'inquiries.create', [
            ...$query,
            'page' => '2',
        ]));

        $response->assertOk();

        foreach (['', 'zh.'] as $target) {
            $response->assertSee(
                'hreflang="'.($target === '' ? 'ja' : 'zh-CN').'" href="'.
                route($target.'inquiries.create', $query).'"',
                false,
            );
        }
    }
}

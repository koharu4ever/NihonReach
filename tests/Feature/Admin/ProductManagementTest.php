<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslation;
use App\Models\ProductTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_cannot_access_product_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_products_with_their_categories(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create([
            'name' => 'Demo Category',
        ]);
        $category->translations()->create([
            'locale' => ProductCategoryTranslation::LOCALE_CHINESE,
            'name' => '演示分类',
            'description' => null,
        ]);
        $product = Product::factory()->for($category, 'category')->create([
            'name' => 'Demo Product',
        ]);
        $product->translations()->create([
            'locale' => ProductTranslation::LOCALE_CHINESE,
            'name' => '演示产品',
            'summary' => '演示产品简介。',
            'description' => '演示产品说明。',
            'specifications' => null,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('admin/products/Index')
            ->has('products', 1)
            ->where('products.0.name', '演示产品')
            ->where('products.0.category.name', '演示分类'));
    }

    public function test_admin_can_create_a_product_with_specifications(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create();

        $response = $this->actingAs($admin)->post(
            route('admin.products.store'),
            $this->validProductData($category),
        );

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::query()->where('sku', 'NR-DEMO-TEST-001')->sole();
        $this->assertSame([
            ['label' => 'Demo diameter', 'value' => '6 mm'],
        ], $product->specifications);
        $this->assertTrue($product->is_featured);
        $this->assertDatabaseHas('product_translations', [
            'product_id' => $product->id,
            'locale' => ProductTranslation::LOCALE_CHINESE,
            'name' => '演示产品',
        ]);
        $this->assertSame([
            ['label' => '刀具直径', 'value' => '6 mm'],
        ], $product->translations()->sole()->specifications);
    }

    public function test_product_requires_an_existing_category_and_valid_sku(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create();

        $response = $this->actingAs($admin)->post(
            route('admin.products.store'),
            $this->validProductData($category, [
                'product_category_id' => 999999,
                'sku' => 'invalid sku',
            ]),
        );

        $response->assertSessionHasErrors(['product_category_id', 'sku']);
    }

    public function test_admin_can_update_a_product_without_changing_slug_or_sku(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->for($category, 'category')->create([
            'slug' => 'demo-product',
            'sku' => 'NR-DEMO-TEST-001',
        ]);

        $response = $this->actingAs($admin)->put(
            route('admin.products.update', $product),
            $this->validProductData($category, [
                'name' => 'Updated Demo Product',
                'translations' => [
                    'zh' => [
                        'name' => '已更新的演示产品',
                        'summary' => '已更新的中文简介。',
                        'description' => '已更新的中文说明。',
                        'specifications' => [
                            ['label' => '刀具直径', 'value' => '8 mm'],
                        ],
                    ],
                ],
            ]),
        );

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Demo Product',
            'slug' => 'demo-product',
            'sku' => 'NR-DEMO-TEST-001',
        ]);
        $this->assertDatabaseHas('product_translations', [
            'product_id' => $product->id,
            'locale' => ProductTranslation::LOCALE_CHINESE,
            'name' => '已更新的演示产品',
        ]);
    }

    public function test_edit_form_receives_both_languages_and_chinese_category_options(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create(['name' => '日本語カテゴリ']);
        $category->translations()->create([
            'locale' => ProductCategoryTranslation::LOCALE_CHINESE,
            'name' => '中文分类',
            'description' => null,
        ]);
        $product = Product::factory()->for($category, 'category')->create([
            'name' => '日本語製品',
        ]);
        $product->translations()->create([
            'locale' => ProductTranslation::LOCALE_CHINESE,
            'name' => '中文产品',
            'summary' => '中文简介。',
            'description' => '中文说明。',
            'specifications' => [
                ['label' => '刀具直径', 'value' => '6 mm'],
            ],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.edit', $product))
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/products/Edit')
                ->where('product.name', '日本語製品')
                ->where('product.translations.zh.name', '中文产品')
                ->where('product.translations.zh.specifications.0.label', '刀具直径')
                ->where('categories.0.name', '中文分类'));
    }

    public function test_chinese_product_content_is_required(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create();
        $data = $this->validProductData($category);
        $data['translations']['zh']['name'] = '';
        $data['translations']['zh']['summary'] = '';

        $this->actingAs($admin)
            ->post(route('admin.products.store'), $data)
            ->assertSessionHasErrors([
                'translations.zh.name',
                'translations.zh.summary',
            ]);
    }

    public function test_admin_can_delete_a_product(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();
        $translation = $product->translations()->create([
            'locale' => ProductTranslation::LOCALE_CHINESE,
            'name' => '待删除产品',
            'summary' => '待删除产品简介。',
            'description' => '待删除产品说明。',
            'specifications' => null,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertModelMissing($product);
        $this->assertModelMissing($translation);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validProductData(ProductCategory $category, array $overrides = []): array
    {
        return [
            'product_category_id' => $category->id,
            'name' => 'Demo Product',
            'slug' => 'demo-product',
            'sku' => 'NR-DEMO-TEST-001',
            'summary' => 'Portfolio demo summary.',
            'description' => 'Portfolio demo product description.',
            'image_path' => null,
            'specifications' => [
                ['label' => 'Demo diameter', 'value' => '6 mm'],
            ],
            'translations' => [
                'zh' => [
                    'name' => '演示产品',
                    'summary' => '作品集演示产品简介。',
                    'description' => '作品集演示产品说明。',
                    'specifications' => [
                        ['label' => '刀具直径', 'value' => '6 mm'],
                    ],
                ],
            ],
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 10,
            ...$overrides,
        ];
    }
}

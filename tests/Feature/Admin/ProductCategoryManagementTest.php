<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_cannot_access_category_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.categories.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_categories_with_product_counts(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create([
            'name' => 'Demo Category',
        ]);
        $category->translations()->create([
            'locale' => ProductCategoryTranslation::LOCALE_CHINESE,
            'name' => '演示分类',
            'description' => '演示分类说明。',
        ]);
        Product::factory()->for($category, 'category')->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('admin/categories/Index')
            ->has('categories', 1)
            ->where('categories.0.name', '演示分类')
            ->where('categories.0.products_count', 1));
    }

    public function test_admin_can_create_a_category(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(
            route('admin.categories.store'),
            $this->validCategoryData(),
        );

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('product_categories', [
            'name' => 'Demo Cutting Tools',
            'slug' => 'demo-cutting-tools',
            'is_active' => true,
            'sort_order' => 15,
        ]);
        $this->assertDatabaseHas('product_category_translations', [
            'locale' => ProductCategoryTranslation::LOCALE_CHINESE,
            'name' => '演示切削刀具',
        ]);
    }

    public function test_category_slug_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        ProductCategory::factory()->create(['slug' => 'existing-category']);

        $response = $this->actingAs($admin)->post(
            route('admin.categories.store'),
            $this->validCategoryData(['slug' => 'existing-category']),
        );

        $response->assertSessionHasErrors('slug');
    }

    public function test_admin_can_update_a_category_without_changing_its_slug(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create([
            'slug' => 'demo-cutting-tools',
        ]);

        $response = $this->actingAs($admin)->put(
            route('admin.categories.update', $category),
            $this->validCategoryData([
                'name' => 'Updated Demo Tools',
                'translations' => [
                    'zh' => [
                        'name' => '已更新的演示刀具',
                        'description' => '已更新的中文说明。',
                    ],
                ],
            ]),
        );

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('product_categories', [
            'id' => $category->id,
            'name' => 'Updated Demo Tools',
            'slug' => 'demo-cutting-tools',
        ]);
        $this->assertDatabaseHas('product_category_translations', [
            'product_category_id' => $category->id,
            'locale' => ProductCategoryTranslation::LOCALE_CHINESE,
            'name' => '已更新的演示刀具',
        ]);
    }

    public function test_edit_form_receives_japanese_base_fields_and_chinese_translation(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create(['name' => '日本語カテゴリ']);
        $category->translations()->create([
            'locale' => ProductCategoryTranslation::LOCALE_CHINESE,
            'name' => '中文分类',
            'description' => '中文说明。',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.categories.edit', $category))
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/categories/Edit')
                ->where('category.name', '日本語カテゴリ')
                ->where('category.translations.zh.name', '中文分类')
                ->where('category.translations.zh.description', '中文说明。'));
    }

    public function test_chinese_category_name_is_required(): void
    {
        $admin = User::factory()->admin()->create();
        $data = $this->validCategoryData();
        $data['translations']['zh']['name'] = '';

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), $data)
            ->assertSessionHasErrors('translations.zh.name');
    }

    public function test_category_with_products_cannot_be_deleted_from_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create();
        Product::factory()->for($category, 'category')->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.categories.index'))
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertModelExists($category);
    }

    public function test_admin_can_delete_an_empty_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create();
        $translation = $category->translations()->create([
            'locale' => ProductCategoryTranslation::LOCALE_CHINESE,
            'name' => '待删除分类',
            'description' => null,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertModelMissing($category);
        $this->assertModelMissing($translation);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validCategoryData(array $overrides = []): array
    {
        return [
            'name' => 'Demo Cutting Tools',
            'slug' => 'demo-cutting-tools',
            'description' => 'Portfolio demo category.',
            'translations' => [
                'zh' => [
                    'name' => '演示切削刀具',
                    'description' => '作品集演示分类。',
                ],
            ],
            'is_active' => true,
            'sort_order' => 15,
            ...$overrides,
        ];
    }
}

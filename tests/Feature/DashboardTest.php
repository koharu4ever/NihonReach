<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCategoryTranslation;
use App\Models\ProductTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_users_cannot_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertForbidden();
    }

    public function test_unverified_admin_users_are_redirected_to_email_verification(): void
    {
        $admin = User::factory()->admin()->unverified()->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_admin_users_can_visit_the_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ProductCategory::factory()->create();
        $category->translations()->create([
            'locale' => ProductCategoryTranslation::LOCALE_CHINESE,
            'name' => '演示分类',
            'description' => null,
        ]);
        $product = Product::factory()
            ->for($category, 'category')
            ->featured()
            ->create();
        $product->translations()->create([
            'locale' => ProductTranslation::LOCALE_CHINESE,
            'name' => '演示产品',
            'summary' => '演示简介。',
            'description' => '演示说明。',
            'specifications' => null,
        ]);
        Inquiry::factory()->create();
        $this->actingAs($admin);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('stats.categories', 1)
            ->where('stats.products', 1)
            ->where('stats.publishedProducts', 1)
            ->where('stats.featuredProducts', 1)
            ->where('stats.newInquiries', 1)
            ->has('recentProducts', 1)
            ->where('recentProducts.0.name', '演示产品')
            ->where('recentProducts.0.category.name', '演示分类'));
    }

    public function test_published_product_count_uses_the_same_visibility_rule_as_the_catalog(): void
    {
        $admin = User::factory()->admin()->create();
        $activeCategory = ProductCategory::factory()->create();
        $inactiveCategory = ProductCategory::factory()->inactive()->create();

        Product::factory()->for($activeCategory, 'category')->create();
        Product::factory()->for($activeCategory, 'category')->inactive()->create();
        Product::factory()->for($inactiveCategory, 'category')->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->where('stats.products', 3)
                ->where('stats.publishedProducts', 1));
    }
}

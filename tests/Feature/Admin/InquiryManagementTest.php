<?php

namespace Tests\Feature\Admin;

use App\Models\Inquiry;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InquiryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_cannot_view_inquiries(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.inquiries.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_inquiry_list(): void
    {
        $admin = User::factory()->admin()->create();
        $inquiry = $this->inquiry();

        $response = $this->actingAs($admin)->get(route('admin.inquiries.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('admin/inquiries/Index')
            ->has('inquiries.data', 1)
            ->where('inquiries.data.0.id', $inquiry->id)
            ->where('inquiries.data.0.product.name', '演示产品'));
    }

    public function test_inquiry_list_is_paginated_with_stable_ordering(): void
    {
        $admin = User::factory()->admin()->create();
        $inquiries = Inquiry::factory()->count(21)->create(['created_at' => now()]);

        $this->actingAs($admin)->get(route('admin.inquiries.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('inquiries.data', 20)
                ->where('inquiries.total', 21)
                ->where('inquiries.last_page', 2)
                ->where('inquiries.data.0.id', $inquiries->last()->id)
                ->where('inquiries.prev_page_url', null));

        $this->get(route('admin.inquiries.index', ['page' => 2]))
            ->assertInertia(fn (Assert $page) => $page
                ->has('inquiries.data', 1)
                ->where('inquiries.current_page', 2)
                ->where('inquiries.data.0.id', $inquiries->first()->id)
                ->where('inquiries.next_page_url', null));
    }

    public function test_saving_closed_status_again_preserves_completion_time(): void
    {
        $admin = User::factory()->admin()->create();
        $this->travelTo(now()->startOfSecond());
        $inquiry = Inquiry::factory()->closed()->create(['handled_at' => now()]);
        $completedAt = $inquiry->handled_at->toDateTimeString();
        $this->travel(1)->day();

        $this->actingAs($admin)->patch(route('admin.inquiries.update', $inquiry), [
            'status' => Inquiry::STATUS_CLOSED,
        ])->assertRedirect();

        $this->assertSame($completedAt, $inquiry->fresh()->handled_at->toDateTimeString());
    }

    public function test_admin_can_view_inquiry_details(): void
    {
        $admin = User::factory()->admin()->create();
        $inquiry = $this->inquiry(['message' => '管理画面に表示する Demo のお問い合わせ本文です。']);

        $response = $this->actingAs($admin)->get(route('admin.inquiries.show', $inquiry));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('admin/inquiries/Show')
            ->where('inquiry.id', $inquiry->id)
            ->where('inquiry.message', $inquiry->message)
            ->where('inquiry.product.name', '演示产品')
            ->where('inquiry.product.sku', $inquiry->product?->sku)
            ->where('statuses', Inquiry::STATUSES));
    }

    public function test_admin_can_close_an_inquiry(): void
    {
        $admin = User::factory()->admin()->create();
        $inquiry = $this->inquiry();

        $response = $this->actingAs($admin)->patch(
            route('admin.inquiries.update', $inquiry),
            ['status' => Inquiry::STATUS_CLOSED],
        );

        $response->assertRedirect(route('admin.inquiries.show', $inquiry));
        $inquiry->refresh();
        $this->assertSame(Inquiry::STATUS_CLOSED, $inquiry->status);
        $this->assertNotNull($inquiry->handled_at);
    }

    public function test_reopening_an_inquiry_clears_the_handled_timestamp(): void
    {
        $admin = User::factory()->admin()->create();
        $inquiry = Inquiry::factory()->closed()->create();

        $this->actingAs($admin)->patch(
            route('admin.inquiries.update', $inquiry),
            ['status' => Inquiry::STATUS_IN_PROGRESS],
        )->assertRedirect();

        $inquiry->refresh();
        $this->assertSame(Inquiry::STATUS_IN_PROGRESS, $inquiry->status);
        $this->assertNull($inquiry->handled_at);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $inquiry = $this->inquiry();

        $response = $this->actingAs($admin)->patch(
            route('admin.inquiries.update', $inquiry),
            ['status' => 'archived'],
        );

        $response->assertSessionHasErrors('status');
        $this->assertSame(Inquiry::STATUS_NEW, $inquiry->fresh()->status);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function inquiry(array $attributes = []): Inquiry
    {
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->for($category, 'category')->create();
        $product->translations()->create([
            'locale' => ProductTranslation::LOCALE_CHINESE,
            'name' => '演示产品',
            'summary' => '演示简介。',
            'description' => '演示说明。',
            'specifications' => null,
        ]);

        return Inquiry::factory()
            ->for($product)
            ->create($attributes);
    }
}

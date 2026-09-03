<?php

namespace Tests\Feature\Site;

use App\Models\Inquiry;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InquirySubmissionTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string}> */
    public static function locales(): array
    {
        return ['Japanese' => [''], 'Chinese' => ['zh.']];
    }

    #[DataProvider('locales')]
    public function test_array_query_parameters_are_rejected(string $prefix): void
    {
        $this->get(route($prefix.'products.index', ['category' => ['x']]))
            ->assertBadRequest();
        $this->get(route($prefix.'inquiries.create', ['product' => ['x']]))
            ->assertBadRequest();
        $this->get(route($prefix.'products.index').'?category=')->assertOk();
        $this->get(route($prefix.'inquiries.create').'?product=')->assertOk();
    }

    #[DataProvider('locales')]
    public function test_invalid_array_fields_can_be_redisplayed_safely(string $prefix): void
    {
        $product = $this->publicProduct();
        $payload = $this->validPayload($product);
        foreach (['product_id', 'name', 'company', 'email', 'phone', 'subject', 'message', 'privacy'] as $field) {
            $payload[$field] = ['invalid'];
        }

        $response = $this->from(route($prefix.'inquiries.create'))
            ->post(route($prefix.'inquiries.store'), $payload);

        $response->assertRedirect(route($prefix.'inquiries.create'));
        $response->assertSessionHasErrors(['product_id', 'name', 'email', 'message']);
        $this->get(route($prefix.'inquiries.create'))->assertOk();
        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_form_lists_only_public_products_and_preselects_requested_product(): void
    {
        $category = ProductCategory::factory()->create(['is_active' => true]);
        $publicProduct = Product::factory()->for($category, 'category')->create([
            'name' => '公開 Demo 製品',
            'is_active' => true,
            'image_path' => '/images/products/inquiry-demo-tool.webp',
        ]);
        Product::factory()->for($category, 'category')->create([
            'name' => '非公開 Demo 製品',
            'is_active' => false,
        ]);
        $inactiveCategory = ProductCategory::factory()->create(['is_active' => false]);
        Product::factory()->for($inactiveCategory, 'category')->create([
            'name' => '停止カテゴリ製品',
            'is_active' => true,
        ]);

        $response = $this->get(route('inquiries.create', ['product' => $publicProduct->slug]));

        $response->assertOk();
        $response->assertSee('公開 Demo 製品');
        $response->assertSee('selected', false);
        $response->assertSee('src="'.asset(ltrim($publicProduct->image_path, '/')).'"', false);
        $response->assertSee('alt="'.$publicProduct->name.'"', false);
        $response->assertDontSee('非公開 Demo 製品');
        $response->assertDontSee('停止カテゴリ製品');
    }

    public function test_visitor_can_submit_a_valid_inquiry(): void
    {
        $product = $this->publicProduct();

        $response = $this->post(route('inquiries.store'), $this->validPayload($product));

        $response->assertRedirect(route('inquiries.thanks'));
        $response->assertSessionHas('inquiry_submitted', true);
        $this->assertDatabaseHas('inquiries', [
            'product_id' => $product->id,
            'name' => '山田 太郎',
            'email' => 'taro@example.test',
            'status' => Inquiry::STATUS_NEW,
        ]);
    }

    public function test_required_fields_and_privacy_consent_are_validated(): void
    {
        $response = $this->from(route('inquiries.create'))->post(route('inquiries.store'), []);

        $response->assertRedirect(route('inquiries.create'));
        $response->assertSessionHasErrors([
            'name' => 'お名前は必須です。',
            'email' => 'メールアドレスは必須です。',
            'subject' => '件名は必須です。',
            'message' => 'お問い合わせ内容は必須です。',
            'privacy' => 'データ保存への同意を確認してください。',
        ]);
    }

    public function test_validation_redirect_keeps_the_product_selected_by_the_visitor(): void
    {
        $category = ProductCategory::factory()->create(['is_active' => true]);
        $queryProduct = Product::factory()->for($category, 'category')->create([
            'image_path' => '/images/products/query-product.webp',
        ]);
        $submittedProduct = Product::factory()->for($category, 'category')->create([
            'image_path' => '/images/products/submitted-product.webp',
        ]);

        $response = $this
            ->from(route('inquiries.create', ['product' => $queryProduct->slug]))
            ->post(route('inquiries.store'), ['product_id' => $submittedProduct->id]);

        $response->assertSessionHasErrors('name');

        $this->get($response->headers->get('Location'))
            ->assertOk()
            ->assertSee('src="'.asset(ltrim($submittedProduct->image_path, '/')).'"', false);
    }

    public function test_inactive_product_cannot_be_associated_with_an_inquiry(): void
    {
        $product = $this->publicProduct(['is_active' => false]);

        $response = $this->post(route('inquiries.store'), $this->validPayload($product));

        $response->assertSessionHasErrors('product_id');
        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_thanks_page_requires_a_successful_submission(): void
    {
        $this->get(route('inquiries.thanks'))
            ->assertRedirect(route('inquiries.create'));

        $this->withSession(['inquiry_submitted' => true])
            ->get(route('inquiries.thanks'))
            ->assertOk()
            ->assertSee('実際の営業返信やメール送信は行われません');
    }

    public function test_chinese_inquiry_submission_redirects_to_chinese_thanks_page(): void
    {
        $product = $this->publicProduct();

        $response = $this->post(
            route('zh.inquiries.store'),
            $this->validPayload($product),
        );

        $response->assertRedirect(route('zh.inquiries.thanks'));
        $response->assertSessionHas('inquiry_submitted', true);

        $this->get(route('zh.inquiries.thanks'))
            ->assertOk()
            ->assertSee('提交完成')
            ->assertSee('href="'.route('inquiries.thanks').'"', false);

        $this->get(route('inquiries.thanks'))
            ->assertOk()
            ->assertSee('送信が完了しました');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function publicProduct(array $attributes = []): Product
    {
        $category = ProductCategory::factory()->create(['is_active' => true]);

        return Product::factory()
            ->for($category, 'category')
            ->create(array_merge(['is_active' => true], $attributes));
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(Product $product): array
    {
        return [
            'product_id' => $product->id,
            'name' => '山田 太郎',
            'company' => 'Demo Manufacturing',
            'email' => 'taro@example.test',
            'phone' => '03-0000-0000',
            'subject' => 'Demo 製品について',
            'message' => 'これは機能テスト用に作成した二十文字以上の Demo お問い合わせ内容です。',
            'privacy' => '1',
        ];
    }
}

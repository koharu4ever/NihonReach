<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\InquiryRequest;
use App\Models\Inquiry;
use App\Models\Product;
use App\Support\PublicSite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function create(Request $request): View
    {
        $productSlug = $request->query('product') ?? '';
        abort_unless(is_string($productSlug), 400);
        $selectedProductSlug = trim($productSlug);

        $products = Product::query()
            ->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->with([
                'translations:id,product_id,locale,name,summary,description,specifications',
                'category:id,name',
                'category.translations:id,product_category_id,locale,name,description',
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'product_category_id', 'name', 'slug', 'sku', 'image_path']);

        $oldProductId = $request->old('product_id');
        $selectedProduct = is_scalar($oldProductId) && filled($oldProductId)
            ? $products->firstWhere('id', (int) $oldProductId)
            : $products->firstWhere('slug', $selectedProductSlug);

        return view('public.inquiries.create', compact('products', 'selectedProduct'));
    }

    public function store(InquiryRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('privacy');
        $data['status'] = Inquiry::STATUS_NEW;

        Inquiry::query()->create($data);

        return redirect(PublicSite::route('inquiries.thanks'))
            ->with('inquiry_submitted', true);
    }

    public function thanks(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('inquiry_submitted')) {
            return redirect(PublicSite::route('inquiries.create'));
        }

        $request->session()->keep('inquiry_submitted');

        return view('public.inquiries.thanks');
    }
}

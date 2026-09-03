<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InquiryStatusRequest;
use App\Models\Inquiry;
use App\Models\ProductTranslation;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InquiryController extends Controller
{
    public function index(): Response
    {
        $inquiries = Inquiry::query()
            ->with([
                'product:id,name',
                'product.translations' => fn ($query) => $query
                    ->where('locale', ProductTranslation::LOCALE_CHINESE),
            ])
            ->latest()
            ->orderByDesc('id')
            ->paginate(20, [
                'id',
                'product_id',
                'name',
                'company',
                'email',
                'subject',
                'status',
                'created_at',
            ])
            ->through(fn (Inquiry $inquiry): array => [
                ...$inquiry->only([
                    'id',
                    'name',
                    'company',
                    'email',
                    'subject',
                    'status',
                    'created_at',
                ]),
                'product' => $inquiry->product === null ? null : [
                    'id' => $inquiry->product->id,
                    'name' => (string) $inquiry->product->translated(
                        'name',
                        ProductTranslation::LOCALE_CHINESE,
                    ),
                ],
            ]);

        return Inertia::render('admin/inquiries/Index', [
            'inquiries' => $inquiries,
        ]);
    }

    public function show(Inquiry $inquiry): Response
    {
        $inquiry->load([
            'product:id,name,slug,sku',
            'product.translations' => fn ($query) => $query
                ->where('locale', ProductTranslation::LOCALE_CHINESE),
        ]);
        $product = $inquiry->product;

        return Inertia::render('admin/inquiries/Show', [
            'inquiry' => [
                ...$inquiry->only([
                    'id',
                    'name',
                    'company',
                    'email',
                    'phone',
                    'subject',
                    'message',
                    'status',
                    'handled_at',
                    'created_at',
                ]),
                'product' => $product === null ? null : [
                    'id' => $product->id,
                    'name' => (string) $product->translated(
                        'name',
                        ProductTranslation::LOCALE_CHINESE,
                    ),
                    'slug' => $product->slug,
                    'sku' => $product->sku,
                ],
            ],
            'statuses' => Inquiry::STATUSES,
        ]);
    }

    public function update(InquiryStatusRequest $request, Inquiry $inquiry): RedirectResponse
    {
        $status = $request->validated('status');
        $handledAt = null;

        if ($status === Inquiry::STATUS_CLOSED) {
            $handledAt = $inquiry->status === Inquiry::STATUS_CLOSED
                ? $inquiry->handled_at
                : now();
        }

        $inquiry->update([
            'status' => $status,
            'handled_at' => $handledAt,
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '询盘状态已更新。',
        ]);

        return to_route('admin.inquiries.show', $inquiry);
    }
}

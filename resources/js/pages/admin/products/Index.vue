<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    create,
    destroy as destroyProduct,
    edit,
    index,
} from '@/routes/admin/products';

type Product = {
    id: number;
    name: string;
    slug: string;
    sku: string;
    summary: string;
    is_featured: boolean;
    is_active: boolean;
    sort_order: number;
    category: {
        id: number;
        name: string;
    };
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: '产品',
                href: index(),
            },
        ],
    },
});

defineProps<{
    products: Product[];
}>();

const remove = (product: Product) => {
    if (!window.confirm(`确定要删除“${product.name}”吗？`)) {
        return;
    }

    router.delete(destroyProduct(product.id).url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="产品管理" />

    <div class="space-y-6 p-4 md:p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
        >
            <Heading
                title="产品管理"
                description="管理前台目录中的演示产品。列表优先显示中文，缺失时回退到日文。"
            />
            <Button as-child>
                <Link :href="create()">
                    <Plus class="size-4" />
                    新建产品
                </Link>
            </Button>
        </div>

        <div
            class="overflow-hidden rounded-xl border bg-white dark:bg-neutral-950"
        >
            <div
                v-if="products.length === 0"
                class="text-muted-foreground px-6 py-16 text-center text-sm"
            >
                暂无产品。
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium">显示顺序</th>
                            <th class="px-4 py-3 font-medium">产品</th>
                            <th class="px-4 py-3 font-medium">分类</th>
                            <th class="px-4 py-3 font-medium">状态</th>
                            <th class="px-4 py-3 text-right font-medium">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="product in products" :key="product.id">
                            <td class="text-muted-foreground px-4 py-4">
                                {{ product.sort_order }}
                            </td>
                            <td class="max-w-md px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <p class="font-medium">
                                        {{ product.name }}
                                    </p>
                                    <span
                                        v-if="product.is_featured"
                                        class="rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-800"
                                    >
                                        推荐
                                    </span>
                                </div>
                                <p class="text-muted-foreground text-xs">
                                    {{ product.sku }}
                                </p>
                                <p
                                    class="text-muted-foreground mt-1 line-clamp-1 text-xs"
                                >
                                    {{ product.summary }}
                                </p>
                            </td>
                            <td class="px-4 py-4">
                                {{ product.category.name }}
                            </td>
                            <td class="px-4 py-4">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="
                                        product.is_active
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'
                                            : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300'
                                    "
                                >
                                    {{
                                        product.is_active ? '已发布' : '未发布'
                                    }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        size="icon"
                                        variant="outline"
                                        as-child
                                    >
                                        <Link
                                            :href="edit(product.id)"
                                            aria-label="编辑"
                                        >
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        size="icon"
                                        variant="outline"
                                        aria-label="删除"
                                        @click="remove(product)"
                                    >
                                        <Trash2 class="size-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

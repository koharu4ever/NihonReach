<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Boxes, CircleCheck, MessagesSquare, Star, Tags } from '@lucide/vue';
import { dashboard } from '@/routes';
import { edit as editProduct } from '@/routes/admin/products';

type DashboardProps = {
    stats: {
        categories: number;
        products: number;
        publishedProducts: number;
        featuredProducts: number;
        newInquiries: number;
    };
    recentProducts: Array<{
        id: number;
        name: string;
        sku: string;
        is_active: boolean;
        updated_at: string;
        category: {
            id: number;
            name: string;
        };
    }>;
};

defineProps<DashboardProps>();

const formatDate = (value: string) =>
    new Intl.DateTimeFormat('zh-CN', { dateStyle: 'medium' }).format(
        new Date(value),
    );

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: '仪表盘',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="仪表盘" />

    <div class="space-y-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">仪表盘</h1>
            <p class="text-muted-foreground mt-1 text-sm">
                查看 NihonReach 产品目录的当前登记情况。
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-xl border bg-white p-5 dark:bg-neutral-950">
                <div class="flex items-center justify-between">
                    <p class="text-muted-foreground text-sm">产品分类</p>
                    <Tags class="size-5 text-sky-600" />
                </div>
                <p class="mt-3 text-3xl font-semibold">
                    {{ stats.categories }}
                </p>
            </div>

            <div class="rounded-xl border bg-white p-5 dark:bg-neutral-950">
                <div class="flex items-center justify-between">
                    <p class="text-muted-foreground text-sm">全部产品</p>
                    <Boxes class="size-5 text-indigo-600" />
                </div>
                <p class="mt-3 text-3xl font-semibold">{{ stats.products }}</p>
            </div>

            <div class="rounded-xl border bg-white p-5 dark:bg-neutral-950">
                <div class="flex items-center justify-between">
                    <p class="text-muted-foreground text-sm">已发布</p>
                    <CircleCheck class="size-5 text-emerald-600" />
                </div>
                <p class="mt-3 text-3xl font-semibold">
                    {{ stats.publishedProducts }}
                </p>
            </div>

            <div class="rounded-xl border bg-white p-5 dark:bg-neutral-950">
                <div class="flex items-center justify-between">
                    <p class="text-muted-foreground text-sm">推荐产品</p>
                    <Star class="size-5 text-amber-500" />
                </div>
                <p class="mt-3 text-3xl font-semibold">
                    {{ stats.featuredProducts }}
                </p>
            </div>

            <div class="rounded-xl border bg-white p-5 dark:bg-neutral-950">
                <div class="flex items-center justify-between">
                    <p class="text-muted-foreground text-sm">新询盘</p>
                    <MessagesSquare class="size-5 text-sky-600" />
                </div>
                <p class="mt-3 text-3xl font-semibold">
                    {{ stats.newInquiries }}
                </p>
            </div>
        </div>

        <div
            class="overflow-hidden rounded-xl border bg-white dark:bg-neutral-950"
        >
            <div class="border-b px-5 py-4">
                <h2 class="font-semibold">最近更新的产品</h2>
            </div>

            <div
                v-if="recentProducts.length === 0"
                class="text-muted-foreground px-5 py-12 text-center text-sm"
            >
                暂无产品。
            </div>

            <div v-else class="divide-y">
                <Link
                    v-for="product in recentProducts"
                    :key="product.id"
                    :href="editProduct(product.id)"
                    class="hover:bg-muted/50 flex flex-col gap-2 px-5 py-4 transition-colors sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-medium">{{ product.name }}</p>
                            <span
                                class="rounded-full px-2 py-0.5 text-[10px] font-medium"
                                :class="
                                    product.is_active
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-neutral-100 text-neutral-600'
                                "
                            >
                                {{ product.is_active ? '已发布' : '未发布' }}
                            </span>
                        </div>
                        <p class="text-muted-foreground mt-1 text-xs">
                            {{ product.sku }} · {{ product.category.name }}
                        </p>
                    </div>
                    <time class="text-muted-foreground text-xs">
                        {{ formatDate(product.updated_at) }}
                    </time>
                </Link>
            </div>
        </div>
    </div>
</template>

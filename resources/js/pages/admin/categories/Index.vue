<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2 } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    create,
    destroy as destroyCategory,
    edit,
    index,
} from '@/routes/admin/categories';

type Category = {
    id: number;
    name: string;
    slug: string;
    is_active: boolean;
    sort_order: number;
    products_count: number;
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: '产品分类',
                href: index(),
            },
        ],
    },
});

defineProps<{
    categories: Category[];
}>();

const remove = (category: Category) => {
    if (!window.confirm(`确定要删除“${category.name}”吗？`)) {
        return;
    }

    router.delete(destroyCategory(category.id).url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="产品分类" />

    <div class="space-y-6 p-4 md:p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
        >
            <Heading
                title="产品分类"
                description="管理前台产品使用的分类。列表优先显示中文，缺失时回退到日文。"
            />
            <Button as-child>
                <Link :href="create()">
                    <Plus class="size-4" />
                    新建分类
                </Link>
            </Button>
        </div>

        <div
            class="overflow-hidden rounded-xl border bg-white dark:bg-neutral-950"
        >
            <div
                v-if="categories.length === 0"
                class="text-muted-foreground px-6 py-16 text-center text-sm"
            >
                暂无产品分类。
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium">显示顺序</th>
                            <th class="px-4 py-3 font-medium">分类</th>
                            <th class="px-4 py-3 font-medium">产品数</th>
                            <th class="px-4 py-3 font-medium">状态</th>
                            <th class="px-4 py-3 text-right font-medium">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="category in categories" :key="category.id">
                            <td class="text-muted-foreground px-4 py-4">
                                {{ category.sort_order }}
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-medium">{{ category.name }}</p>
                                <p class="text-muted-foreground text-xs">
                                    {{ category.slug }}
                                </p>
                            </td>
                            <td class="px-4 py-4">
                                {{ category.products_count }}
                            </td>
                            <td class="px-4 py-4">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="
                                        category.is_active
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'
                                            : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300'
                                    "
                                >
                                    {{
                                        category.is_active ? '已启用' : '未发布'
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
                                            :href="edit(category.id)"
                                            aria-label="编辑"
                                        >
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        size="icon"
                                        variant="outline"
                                        aria-label="删除"
                                        @click="remove(category)"
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

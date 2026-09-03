<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Eye } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { index, show } from '@/routes/admin/inquiries';

type Inquiry = {
    id: number;
    name: string;
    company: string | null;
    email: string;
    subject: string;
    status: 'new' | 'in_progress' | 'closed';
    created_at: string;
    product: { id: number; name: string } | null;
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: '询盘管理',
                href: index(),
            },
        ],
    },
});

defineProps<{
    inquiries: {
        data: Inquiry[];
        current_page: number;
        last_page: number;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}>();

const statusLabel = (status: Inquiry['status']) =>
    ({
        new: '新询盘',
        in_progress: '处理中',
        closed: '已完成',
    })[status];

const statusClass = (status: Inquiry['status']) =>
    ({
        new: 'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300',
        in_progress:
            'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300',
        closed: 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300',
    })[status];

const formatDateTime = (value: string) =>
    new Intl.DateTimeFormat('zh-CN', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
</script>

<template>
    <Head title="询盘管理" />

    <div class="space-y-6 p-4 md:p-6">
        <Heading
            title="询盘管理"
            description="管理通过前台表单保存的演示询盘及其处理状态。"
        />

        <div
            class="overflow-hidden rounded-xl border bg-white dark:bg-neutral-950"
        >
            <div
                v-if="inquiries.data.length === 0"
                class="text-muted-foreground px-6 py-16 text-center text-sm"
            >
                暂无询盘。
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium">收到时间</th>
                            <th class="px-4 py-3 font-medium">发件人</th>
                            <th class="px-4 py-3 font-medium">主题与产品</th>
                            <th class="px-4 py-3 font-medium">状态</th>
                            <th class="px-4 py-3 text-right font-medium">
                                详情
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="inquiry in inquiries.data" :key="inquiry.id">
                            <td
                                class="text-muted-foreground px-4 py-4 text-xs whitespace-nowrap"
                            >
                                {{ formatDateTime(inquiry.created_at) }}
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-medium">{{ inquiry.name }}</p>
                                <p class="text-muted-foreground text-xs">
                                    {{ inquiry.company || inquiry.email }}
                                </p>
                            </td>
                            <td class="max-w-md px-4 py-4">
                                <p class="line-clamp-1 font-medium">
                                    {{ inquiry.subject }}
                                </p>
                                <p class="text-muted-foreground mt-1 text-xs">
                                    {{ inquiry.product?.name || '未指定产品' }}
                                </p>
                            </td>
                            <td class="px-4 py-4">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="statusClass(inquiry.status)"
                                >
                                    {{ statusLabel(inquiry.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <Button size="icon" variant="outline" as-child>
                                    <Link
                                        :href="show(inquiry.id)"
                                        aria-label="查看详情"
                                    >
                                        <Eye class="size-4" />
                                    </Link>
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <nav
            v-if="inquiries.last_page > 1"
            aria-label="询盘分页"
            class="flex flex-wrap items-center justify-between gap-3"
        >
            <p class="text-muted-foreground text-sm">
                共 {{ inquiries.total }} 条，第 {{ inquiries.current_page }} /
                {{ inquiries.last_page }} 页
            </p>
            <div class="flex gap-2">
                <Button
                    v-if="inquiries.prev_page_url"
                    variant="outline"
                    as-child
                >
                    <Link :href="inquiries.prev_page_url">上一页</Link>
                </Button>
                <Button v-else variant="outline" disabled>上一页</Button>
                <Button
                    v-if="inquiries.next_page_url"
                    variant="outline"
                    as-child
                >
                    <Link :href="inquiries.next_page_url">下一页</Link>
                </Button>
                <Button v-else variant="outline" disabled>下一页</Button>
            </div>
        </nav>
    </div>
</template>

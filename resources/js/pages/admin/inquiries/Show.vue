<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { index, update } from '@/routes/admin/inquiries';

type InquiryStatus = 'new' | 'in_progress' | 'closed';

type Inquiry = {
    id: number;
    name: string;
    company: string | null;
    email: string;
    phone: string | null;
    subject: string;
    message: string;
    status: InquiryStatus;
    handled_at: string | null;
    created_at: string;
    product: {
        id: number;
        name: string;
        slug: string;
        sku: string;
    } | null;
};

const props = defineProps<{
    inquiry: Inquiry;
    statuses: InquiryStatus[];
}>();

const form = useForm<{ status: InquiryStatus }>({
    status: props.inquiry.status,
});

const submit = () => {
    form.patch(update(props.inquiry.id).url, {
        preserveScroll: true,
    });
};

const statusLabel = (status: InquiryStatus) =>
    ({
        new: '新询盘',
        in_progress: '处理中',
        closed: '已完成',
    })[status];

const formatDateTime = (value: string) =>
    new Intl.DateTimeFormat('zh-CN', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));

defineOptions({
    layout: {
        breadcrumbs: [
            { title: '询盘管理', href: index() },
            { title: '详情', href: index() },
        ],
    },
});
</script>

<template>
    <Head :title="`询盘 #${inquiry.id}`" />

    <div class="space-y-6 p-4 md:p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
        >
            <Heading
                :title="`询盘 #${inquiry.id}`"
                description="查看通过前台表单保存的演示询盘详情。"
            />
            <Button variant="outline" as-child>
                <Link :href="index()">
                    <ArrowLeft class="size-4" />
                    返回列表
                </Link>
            </Button>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1fr_22rem] xl:items-start">
            <div class="space-y-6">
                <section
                    class="rounded-xl border bg-white p-5 dark:bg-neutral-950"
                >
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <p class="text-muted-foreground text-xs">
                                收到时间
                            </p>
                            <p class="mt-1 text-sm">
                                {{ formatDateTime(inquiry.created_at) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs">
                                相关产品
                            </p>
                            <p class="mt-1 text-sm">
                                <a
                                    v-if="inquiry.product"
                                    :href="`/products/${inquiry.product.slug}`"
                                    class="font-medium text-sky-700 hover:underline"
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    {{ inquiry.product.name }}（{{
                                        inquiry.product.sku
                                    }}）
                                </a>
                                <span v-else>未指定</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs">姓名</p>
                            <p class="mt-1 text-sm font-medium">
                                {{ inquiry.name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs">
                                公司名称
                            </p>
                            <p class="mt-1 text-sm">
                                {{ inquiry.company || '未填写' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs">
                                电子邮箱
                            </p>
                            <a
                                :href="`mailto:${inquiry.email}`"
                                class="mt-1 block text-sm text-sky-700 hover:underline"
                                >{{ inquiry.email }}</a
                            >
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs">
                                电话号码
                            </p>
                            <a
                                v-if="inquiry.phone"
                                :href="`tel:${inquiry.phone}`"
                                class="mt-1 block text-sm text-sky-700 hover:underline"
                                >{{ inquiry.phone }}</a
                            >
                            <p v-else class="mt-1 text-sm">未填写</p>
                        </div>
                    </div>
                </section>

                <section
                    class="rounded-xl border bg-white p-5 dark:bg-neutral-950"
                >
                    <p class="text-muted-foreground text-xs">主题</p>
                    <h2 class="mt-2 text-lg font-semibold">
                        {{ inquiry.subject }}
                    </h2>
                    <p class="mt-5 text-sm leading-7 whitespace-pre-wrap">
                        {{ inquiry.message }}
                    </p>
                </section>
            </div>

            <aside class="rounded-xl border bg-white p-5 dark:bg-neutral-950">
                <h2 class="font-semibold">处理状态</h2>
                <form class="mt-4 space-y-4" @submit.prevent="submit">
                    <div>
                        <label for="status" class="text-sm font-medium"
                            >状态</label
                        >
                        <select
                            id="status"
                            v-model="form.status"
                            class="bg-background mt-2 w-full rounded-md border px-3 py-2 text-sm"
                        >
                            <option
                                v-for="status in statuses"
                                :key="status"
                                :value="status"
                            >
                                {{ statusLabel(status) }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.status"
                            class="mt-2 text-sm text-red-600"
                        >
                            {{ form.errors.status }}
                        </p>
                    </div>
                    <Button
                        type="submit"
                        class="w-full"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? '正在更新…' : '更新状态' }}
                    </Button>
                </form>
                <p
                    v-if="inquiry.handled_at"
                    class="text-muted-foreground mt-4 text-xs"
                >
                    完成时间：{{ formatDateTime(inquiry.handled_at) }}
                </p>
                <div
                    class="mt-5 rounded-lg bg-amber-50 p-3 text-xs leading-5 text-amber-900 dark:bg-amber-950 dark:text-amber-200"
                >
                    这是 Portfolio Demo，因此此页面不会实际发送邮件回复。
                </div>
            </aside>
        </div>
    </div>
</template>

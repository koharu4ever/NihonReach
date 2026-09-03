<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ProductForm from '@/pages/admin/products/Form.vue';
import { create, index } from '@/routes/admin/products';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: '产品',
                href: index(),
            },
            {
                title: '新建',
                href: create(),
            },
        ],
    },
});

defineProps<{
    categories: Array<{ id: number; name: string }>;
}>();
</script>

<template>
    <Head title="新建产品" />

    <div class="p-4 md:p-6">
        <Heading
            title="新建产品"
            description="添加用于前台产品目录的演示产品，并同时录入中日文内容。"
        />
        <ProductForm :categories="categories" />
    </div>
</template>

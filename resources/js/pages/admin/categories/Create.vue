<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import CategoryForm from '@/pages/admin/categories/Form.vue';
import { create, index } from '@/routes/admin/categories';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: '产品分类',
                href: index(),
            },
            {
                title: '新建',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="新建产品分类" />

    <div class="p-4 md:p-6">
        <Heading
            title="新建产品分类"
            description="添加用于整理前台产品的分类，并同时录入中日文内容。"
        />
        <CategoryForm />
    </div>
</template>

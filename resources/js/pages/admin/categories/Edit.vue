<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import CategoryForm, {
    type ProductCategoryFormValue,
} from '@/pages/admin/categories/Form.vue';
import { index } from '@/routes/admin/categories';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: '产品分类',
                href: index(),
            },
            {
                title: '编辑',
                href: index(),
            },
        ],
    },
});

defineProps<{
    category: ProductCategoryFormValue;
}>();
</script>

<template>
    <Head :title="`编辑 ${category.name}`" />

    <div class="p-4 md:p-6">
        <Heading
            title="编辑产品分类"
            description="更新分类的中日文内容、发布状态和显示顺序。"
        />
        <CategoryForm :category="category" />
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ProductForm, {
    type ProductFormValue,
} from '@/pages/admin/products/Form.vue';
import { index } from '@/routes/admin/products';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: '产品',
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
    product: ProductFormValue;
    categories: Array<{ id: number; name: string }>;
}>();
</script>

<template>
    <Head :title="`编辑 ${product.name}`" />

    <div class="p-4 md:p-6">
        <Heading
            title="编辑产品"
            description="更新产品的中日文内容、规格和发布状态。"
        />
        <ProductForm :product="product" :categories="categories" />
    </div>
</template>

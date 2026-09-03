<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index, store, update } from '@/routes/admin/categories';

type CategoryTranslation = {
    name: string;
    description: string;
};

export type ProductCategoryFormValue = {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    is_active: boolean;
    sort_order: number;
    translations: {
        zh: CategoryTranslation;
    };
};

const props = defineProps<{
    category?: ProductCategoryFormValue;
}>();

const form = useForm({
    name: props.category?.name ?? '',
    slug: props.category?.slug ?? '',
    description: props.category?.description ?? '',
    is_active: props.category?.is_active ?? true,
    sort_order: props.category?.sort_order ?? 0,
    translations: {
        zh: {
            name: props.category?.translations?.zh?.name ?? '',
            description: props.category?.translations?.zh?.description ?? '',
        },
    },
});

const submit = () => {
    if (props.category) {
        form.put(update(props.category.id).url, {
            preserveScroll: true,
        });

        return;
    }

    form.post(store().url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <form class="max-w-3xl space-y-8" @submit.prevent="submit">
        <section class="space-y-5">
            <div class="border-b pb-2">
                <h3 class="text-base font-semibold">日文内容</h3>
                <p class="text-muted-foreground mt-1 text-xs">
                    用于前台日文版；日文也是缺少翻译时的回退内容。
                </p>
            </div>

            <div class="grid gap-2">
                <Label for="name">分类名称（日文）</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    required
                    maxlength="100"
                    placeholder="例：超硬エンドミル"
                />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="description">分类说明（日文，可选）</Label>
                <textarea
                    id="description"
                    v-model="form.description"
                    rows="5"
                    maxlength="2000"
                    class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring min-h-28 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                    placeholder="カテゴリの用途を説明します。"
                />
                <InputError :message="form.errors.description" />
            </div>
        </section>

        <section class="space-y-5">
            <div class="border-b pb-2">
                <h3 class="text-base font-semibold">中文内容</h3>
                <p class="text-muted-foreground mt-1 text-xs">
                    用于前台中文版和中文后台列表。
                </p>
            </div>

            <div class="grid gap-2">
                <Label for="translations_zh_name">分类名称（中文）</Label>
                <Input
                    id="translations_zh_name"
                    v-model="form.translations.zh.name"
                    required
                    maxlength="100"
                    placeholder="例如：硬质合金立铣刀"
                />
                <InputError :message="form.errors['translations.zh.name']" />
            </div>

            <div class="grid gap-2">
                <Label for="translations_zh_description"
                    >分类说明（中文，可选）</Label
                >
                <textarea
                    id="translations_zh_description"
                    v-model="form.translations.zh.description"
                    rows="5"
                    maxlength="2000"
                    class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring min-h-28 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                    placeholder="说明该分类的用途。"
                />
                <InputError
                    :message="form.errors['translations.zh.description']"
                />
            </div>
        </section>

        <section class="space-y-5 border-t pt-6">
            <div>
                <h3 class="text-base font-semibold">共享设置</h3>
                <p class="text-muted-foreground mt-1 text-xs">
                    以下字段由中日文页面共同使用，只需填写一次。
                </p>
            </div>

            <div class="grid gap-2">
                <Label for="slug">Slug（网址标识）</Label>
                <Input
                    id="slug"
                    v-model="form.slug"
                    required
                    maxlength="100"
                    pattern="[A-Za-z0-9_-]+"
                    placeholder="solid-carbide-end-mills"
                />
                <p class="text-muted-foreground text-xs">
                    用于公开 URL，只能输入半角英文字母、数字、连字符和下划线。
                </p>
                <InputError :message="form.errors.slug" />
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="sort_order">显示顺序</Label>
                    <Input
                        id="sort_order"
                        v-model.number="form.sort_order"
                        type="number"
                        min="0"
                        max="65535"
                        required
                    />
                    <InputError :message="form.errors.sort_order" />
                </div>

                <label
                    class="border-input flex items-center gap-3 self-end rounded-md border px-4 py-2.5 text-sm"
                >
                    <input
                        v-model="form.is_active"
                        type="checkbox"
                        class="size-4 rounded border-neutral-300"
                    />
                    在前台启用此分类
                </label>
                <InputError :message="form.errors.is_active" />
            </div>
        </section>

        <div class="flex items-center gap-3 border-t pt-6">
            <Button type="submit" :disabled="form.processing">
                {{ category ? '保存修改' : '创建分类' }}
            </Button>
            <Button variant="outline" as-child>
                <Link :href="index()">取消</Link>
            </Button>
        </div>
    </form>
</template>

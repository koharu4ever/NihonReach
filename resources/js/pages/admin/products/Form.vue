<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index, store, update } from '@/routes/admin/products';

type CategoryOption = {
    id: number;
    name: string;
};

type Specification = {
    label: string;
    value: string;
};

type ProductTranslation = {
    name: string;
    summary: string;
    description: string;
    specifications: Specification[];
};

export type ProductFormValue = {
    id: number;
    product_category_id: number;
    name: string;
    slug: string;
    sku: string;
    summary: string;
    description: string;
    image_path: string | null;
    specifications: Specification[] | null;
    is_featured: boolean;
    is_active: boolean;
    sort_order: number;
    translations: {
        zh: ProductTranslation;
    };
};

type SpecificationLanguage = 'ja' | 'zh';

const props = defineProps<{
    categories: CategoryOption[];
    product?: ProductFormValue;
}>();

const form = useForm({
    product_category_id:
        props.product?.product_category_id ?? props.categories[0]?.id ?? 0,
    name: props.product?.name ?? '',
    slug: props.product?.slug ?? '',
    sku: props.product?.sku ?? '',
    summary: props.product?.summary ?? '',
    description: props.product?.description ?? '',
    image_path: props.product?.image_path ?? '',
    specifications: props.product?.specifications ?? [],
    is_featured: props.product?.is_featured ?? false,
    is_active: props.product?.is_active ?? true,
    sort_order: props.product?.sort_order ?? 0,
    translations: {
        zh: {
            name: props.product?.translations?.zh?.name ?? '',
            summary: props.product?.translations?.zh?.summary ?? '',
            description: props.product?.translations?.zh?.description ?? '',
            specifications:
                props.product?.translations?.zh?.specifications ?? [],
        },
    },
});

const canSubmit = computed(() => props.categories.length > 0);

const specificationsFor = (language: SpecificationLanguage) =>
    language === 'ja'
        ? form.specifications
        : form.translations.zh.specifications;

const addSpecification = (language: SpecificationLanguage) => {
    const specifications = specificationsFor(language);

    if (specifications.length >= 12) {
        return;
    }

    specifications.push({ label: '', value: '' });
};

const removeSpecification = (
    language: SpecificationLanguage,
    index: number,
) => {
    specificationsFor(language).splice(index, 1);
};

const submit = () => {
    if (!canSubmit.value) {
        return;
    }

    if (props.product) {
        form.put(update(props.product.id).url, {
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
    <form class="max-w-4xl space-y-8" @submit.prevent="submit">
        <div
            v-if="categories.length === 0"
            class="rounded-lg border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-100"
        >
            创建产品前，请先至少添加一个产品分类。
        </div>

        <section class="space-y-5">
            <div class="border-b pb-2">
                <h3 class="text-base font-semibold">共享基本信息</h3>
                <p class="text-muted-foreground mt-1 text-xs">
                    以下字段由中日文产品页面共同使用，只需填写一次。
                </p>
            </div>

            <div class="grid gap-2">
                <Label for="product_category_id">产品分类</Label>
                <select
                    id="product_category_id"
                    v-model.number="form.product_category_id"
                    required
                    class="border-input bg-background focus-visible:ring-ring h-9 w-full rounded-md border px-3 text-sm focus-visible:ring-2 focus-visible:outline-none"
                >
                    <option :value="0" disabled>请选择分类</option>
                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                    >
                        {{ category.name }}
                    </option>
                </select>
                <InputError :message="form.errors.product_category_id" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="sku">型号 / SKU</Label>
                    <Input
                        id="sku"
                        v-model="form.sku"
                        required
                        maxlength="80"
                        placeholder="NR-DEMO-EM-060"
                    />
                    <InputError :message="form.errors.sku" />
                </div>

                <div class="grid gap-2">
                    <Label for="slug">Slug（网址标识）</Label>
                    <Input
                        id="slug"
                        v-model="form.slug"
                        required
                        maxlength="160"
                        pattern="[A-Za-z0-9_-]+"
                        placeholder="nr-demo-end-mill-6mm"
                    />
                    <InputError :message="form.errors.slug" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="image_path">图片路径（可选）</Label>
                <Input
                    id="image_path"
                    v-model="form.image_path"
                    maxlength="255"
                    placeholder="/images/products/demo-tool.webp"
                />
                <p class="text-muted-foreground text-xs">
                    Phase 0 暂不提供上传功能，仅保存 public 目录中的图片路径。
                </p>
                <InputError :message="form.errors.image_path" />
            </div>
        </section>

        <section class="space-y-6 rounded-xl border p-5">
            <div class="border-b pb-3">
                <h3 class="text-base font-semibold">日文内容</h3>
                <p class="text-muted-foreground mt-1 text-xs">
                    用于前台日文版；日文也是缺少翻译时的回退内容。
                </p>
            </div>

            <div class="grid gap-2">
                <Label for="name">产品名称（日文）</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    required
                    maxlength="150"
                    placeholder="NR-Demo 4枚刃 超硬エンドミル 6mm"
                />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="summary">列表摘要（日文）</Label>
                <textarea
                    id="summary"
                    v-model="form.summary"
                    required
                    rows="3"
                    maxlength="255"
                    class="border-input bg-background focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:outline-none"
                />
                <InputError :message="form.errors.summary" />
            </div>

            <div class="grid gap-2">
                <Label for="description">详细说明（日文）</Label>
                <textarea
                    id="description"
                    v-model="form.description"
                    required
                    rows="7"
                    maxlength="10000"
                    class="border-input bg-background focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:outline-none"
                />
                <InputError :message="form.errors.description" />
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between border-b pb-2">
                    <div>
                        <h4 class="text-sm font-semibold">产品规格（日文）</h4>
                        <p class="text-muted-foreground text-xs">
                            最多可添加 12 项。
                        </p>
                    </div>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        :disabled="form.specifications.length >= 12"
                        @click="addSpecification('ja')"
                    >
                        <Plus class="size-4" />
                        添加日文规格
                    </Button>
                </div>

                <p
                    v-if="form.specifications.length === 0"
                    class="text-muted-foreground rounded-md border border-dashed p-6 text-center text-sm"
                >
                    暂无日文规格。
                </p>

                <div
                    v-for="(
                        specification, specificationIndex
                    ) in form.specifications"
                    :key="specificationIndex"
                    class="grid gap-3 rounded-lg border p-3 sm:grid-cols-[1fr_1fr_auto]"
                >
                    <div class="grid gap-1.5">
                        <Label :for="`spec-label-${specificationIndex}`"
                            >项目名（日文）</Label
                        >
                        <Input
                            :id="`spec-label-${specificationIndex}`"
                            v-model="specification.label"
                            required
                            maxlength="50"
                            placeholder="刃径"
                        />
                        <InputError
                            :message="
                                form.errors[
                                    `specifications.${specificationIndex}.label`
                                ]
                            "
                        />
                    </div>
                    <div class="grid gap-1.5">
                        <Label :for="`spec-value-${specificationIndex}`"
                            >数值（日文）</Label
                        >
                        <Input
                            :id="`spec-value-${specificationIndex}`"
                            v-model="specification.value"
                            required
                            maxlength="100"
                            placeholder="6 mm"
                        />
                        <InputError
                            :message="
                                form.errors[
                                    `specifications.${specificationIndex}.value`
                                ]
                            "
                        />
                    </div>
                    <Button
                        type="button"
                        size="icon"
                        variant="outline"
                        class="self-end"
                        aria-label="删除日文规格"
                        @click="removeSpecification('ja', specificationIndex)"
                    >
                        <Trash2 class="size-4" />
                    </Button>
                </div>
                <InputError :message="form.errors.specifications" />
            </div>
        </section>

        <section class="space-y-6 rounded-xl border p-5">
            <div class="border-b pb-3">
                <h3 class="text-base font-semibold">中文内容</h3>
                <p class="text-muted-foreground mt-1 text-xs">
                    用于前台中文版和中文后台列表。
                </p>
            </div>

            <div class="grid gap-2">
                <Label for="translations_zh_name">产品名称（中文）</Label>
                <Input
                    id="translations_zh_name"
                    v-model="form.translations.zh.name"
                    required
                    maxlength="150"
                    placeholder="NR-Demo 四刃硬质合金立铣刀 6mm"
                />
                <InputError :message="form.errors['translations.zh.name']" />
            </div>

            <div class="grid gap-2">
                <Label for="translations_zh_summary">列表摘要（中文）</Label>
                <textarea
                    id="translations_zh_summary"
                    v-model="form.translations.zh.summary"
                    required
                    rows="3"
                    maxlength="255"
                    class="border-input bg-background focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:outline-none"
                />
                <InputError :message="form.errors['translations.zh.summary']" />
            </div>

            <div class="grid gap-2">
                <Label for="translations_zh_description"
                    >详细说明（中文）</Label
                >
                <textarea
                    id="translations_zh_description"
                    v-model="form.translations.zh.description"
                    required
                    rows="7"
                    maxlength="10000"
                    class="border-input bg-background focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:outline-none"
                />
                <InputError
                    :message="form.errors['translations.zh.description']"
                />
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between border-b pb-2">
                    <div>
                        <h4 class="text-sm font-semibold">产品规格（中文）</h4>
                        <p class="text-muted-foreground text-xs">
                            最多可添加 12 项，建议与日文规格保持相同顺序。
                        </p>
                    </div>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        :disabled="
                            form.translations.zh.specifications.length >= 12
                        "
                        @click="addSpecification('zh')"
                    >
                        <Plus class="size-4" />
                        添加中文规格
                    </Button>
                </div>

                <p
                    v-if="form.translations.zh.specifications.length === 0"
                    class="text-muted-foreground rounded-md border border-dashed p-6 text-center text-sm"
                >
                    暂无中文规格。
                </p>

                <div
                    v-for="(specification, specificationIndex) in form
                        .translations.zh.specifications"
                    :key="specificationIndex"
                    class="grid gap-3 rounded-lg border p-3 sm:grid-cols-[1fr_1fr_auto]"
                >
                    <div class="grid gap-1.5">
                        <Label :for="`zh-spec-label-${specificationIndex}`"
                            >项目名（中文）</Label
                        >
                        <Input
                            :id="`zh-spec-label-${specificationIndex}`"
                            v-model="specification.label"
                            required
                            maxlength="50"
                            placeholder="刃径"
                        />
                        <InputError
                            :message="
                                form.errors[
                                    `translations.zh.specifications.${specificationIndex}.label`
                                ]
                            "
                        />
                    </div>
                    <div class="grid gap-1.5">
                        <Label :for="`zh-spec-value-${specificationIndex}`"
                            >数值（中文）</Label
                        >
                        <Input
                            :id="`zh-spec-value-${specificationIndex}`"
                            v-model="specification.value"
                            required
                            maxlength="100"
                            placeholder="6 mm"
                        />
                        <InputError
                            :message="
                                form.errors[
                                    `translations.zh.specifications.${specificationIndex}.value`
                                ]
                            "
                        />
                    </div>
                    <Button
                        type="button"
                        size="icon"
                        variant="outline"
                        class="self-end"
                        aria-label="删除中文规格"
                        @click="removeSpecification('zh', specificationIndex)"
                    >
                        <Trash2 class="size-4" />
                    </Button>
                </div>
                <InputError
                    :message="form.errors['translations.zh.specifications']"
                />
            </div>
        </section>

        <section class="space-y-5 border-t pt-6">
            <div>
                <h3 class="text-base font-semibold">发布设置</h3>
                <p class="text-muted-foreground mt-1 text-xs">
                    此设置同时作用于中文和日文前台。
                </p>
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
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
                    class="flex items-center gap-3 self-end rounded-md border px-4 py-2.5 text-sm"
                >
                    <input
                        v-model="form.is_featured"
                        type="checkbox"
                        class="size-4"
                    />
                    设为推荐产品
                </label>

                <label
                    class="flex items-center gap-3 self-end rounded-md border px-4 py-2.5 text-sm"
                >
                    <input
                        v-model="form.is_active"
                        type="checkbox"
                        class="size-4"
                    />
                    发布到前台
                </label>
            </div>

            <InputError :message="form.errors.is_featured" />
            <InputError :message="form.errors.is_active" />
        </section>

        <div class="flex items-center gap-3 border-t pt-6">
            <Button type="submit" :disabled="form.processing || !canSubmit">
                {{ product ? '保存修改' : '创建产品' }}
            </Button>
            <Button variant="outline" as-child>
                <Link :href="index()">取消</Link>
            </Button>
        </div>
    </form>
</template>

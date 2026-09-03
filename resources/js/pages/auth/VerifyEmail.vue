<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: '验证电子邮箱',
        description: '请点击我们刚刚发送到您邮箱中的链接完成验证。',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="验证电子邮箱" />

    <div
        v-if="status === 'verification-link-sent'"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        新的验证链接已发送到您的电子邮箱。
    </div>

    <Form
        v-bind="send.form()"
        class="space-y-6 text-center"
        v-slot="{ processing }"
    >
        <Button :disabled="processing" variant="secondary">
            <Spinner v-if="processing" />
            重新发送验证邮件
        </Button>

        <TextLink :href="logout()" as="button" class="mx-auto block text-sm">
            退出登录
        </TextLink>
    </Form>
</template>

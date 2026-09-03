<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Boxes, Globe2, LayoutGrid, MessagesSquare, Tags } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { index as categoryIndex } from '@/routes/admin/categories';
import { index as inquiryIndex } from '@/routes/admin/inquiries';
import { index as productIndex } from '@/routes/admin/products';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: '仪表盘',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: '产品管理',
        href: productIndex(),
        icon: Boxes,
    },
    {
        title: '询盘管理',
        href: inquiryIndex(),
        icon: MessagesSquare,
    },
    {
        title: '产品分类',
        href: categoryIndex(),
        icon: Tags,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: '前台网站',
        href: '/zh',
        icon: Globe2,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

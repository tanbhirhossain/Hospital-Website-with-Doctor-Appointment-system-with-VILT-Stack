<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <div class="admin-workspace min-h-[calc(100svh-4rem)] bg-[radial-gradient(circle_at_top_left,rgba(37,99,235,0.08),transparent_32rem),linear-gradient(180deg,#f8fafc_0%,#eef2ff_100%)] px-4 py-5 sm:px-6 lg:px-8">
            <slot />
        </div>
    </AppSidebarLayout>
</template>

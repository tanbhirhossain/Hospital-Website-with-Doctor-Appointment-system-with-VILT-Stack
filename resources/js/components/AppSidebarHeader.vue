<script setup lang="ts">
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType, SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Bell, Search, ShieldCheck } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>();

const page = usePage<SharedData>();
const user = computed(() => page.props.auth?.user);
const initials = computed(() => (user.value?.name || 'User')
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase())
    .join('') || 'U');
</script>

<template>
    <header class="sticky top-0 z-20 flex min-h-16 shrink-0 items-center justify-between gap-4 border-b border-slate-200/80 bg-white/90 px-4 backdrop-blur-xl transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:min-h-14 md:px-6">
        <div class="flex min-w-0 items-center gap-3">
            <SidebarTrigger class="size-9 rounded-xl border border-slate-200 bg-white shadow-sm hover:bg-slate-50" />
            <div class="hidden h-7 w-px bg-slate-200 md:block" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumb class="min-w-0">
                    <BreadcrumbList class="flex-nowrap">
                        <template v-for="(item, index) in breadcrumbs" :key="index">
                            <BreadcrumbItem class="min-w-0">
                                <template v-if="index === breadcrumbs.length - 1">
                                    <BreadcrumbPage class="truncate font-bold text-slate-900">{{ item.title }}</BreadcrumbPage>
                                </template>
                                <template v-else>
                                    <BreadcrumbLink :href="item.href" class="truncate text-slate-500 hover:text-blue-700">
                                        {{ item.title }}
                                    </BreadcrumbLink>
                                </template>
                            </BreadcrumbItem>
                            <BreadcrumbSeparator v-if="index !== breadcrumbs.length - 1" class="text-slate-300" />
                        </template>
                    </BreadcrumbList>
                </Breadcrumb>
            </template>
            <div v-else>
                <p class="text-sm font-black text-slate-950">Command Center</p>
                <p class="hidden text-xs text-slate-500 sm:block">Premium hospital operations dashboard</p>
            </div>
        </div>

        <div class="flex flex-1 items-center justify-end gap-3">
            <div class="relative hidden max-w-md flex-1 lg:block">
                <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input class="h-10 w-full rounded-2xl border border-slate-200 bg-slate-50/80 pl-10 pr-4 text-sm text-slate-700 shadow-inner outline-none transition focus:border-blue-300 focus:bg-white focus:ring-4 focus:ring-blue-100" placeholder="Search modules, patients, doctors..." />
            </div>

            <div class="hidden items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 xl:flex">
                <ShieldCheck class="size-4" /> Secure admin session
            </div>

            <button type="button" class="relative flex size-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:-translate-y-0.5 hover:text-slate-900 hover:shadow-md">
                <Bell class="size-4" />
                <span class="absolute right-2.5 top-2.5 size-2 rounded-full bg-rose-500 ring-2 ring-white" />
            </button>

            <Link :href="route('profile.edit')" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white py-1.5 pl-1.5 pr-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <span class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-slate-900 to-blue-700 text-xs font-black text-white shadow-inner">{{ initials }}</span>
                <span class="hidden text-left md:block">
                    <span class="block max-w-32 truncate text-sm font-black leading-4 text-slate-900">{{ user?.name || 'User' }}</span>
                    <span class="block max-w-32 truncate text-[11px] font-medium text-slate-500">{{ user?.email || 'Profile' }}</span>
                </span>
            </Link>
        </div>
    </header>
</template>

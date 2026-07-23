<script setup lang="ts">
import NavUser from '@/components/NavUser.vue';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
    SidebarRail,
    useSidebar,
} from '@/components/ui/sidebar';
import type { NavigationGroup, NavigationLink, SharedData } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    BadgePlus,
    BrainCircuit,
    Building2,
    CalendarDays,
    ChevronDown,
    Circle,
    GalleryHorizontalEnd,
    HeartPulse,
    Hospital,
    Images,
    KeyRound,
    LayoutDashboard,
    Mail,
    Megaphone,
    MessageSquareHeart,
    MessagesSquare,
    Newspaper,
    PackageCheck,
    Palette,
    Settings,
    ShieldCheck,
    Sparkles,
    Stethoscope,
    UserRoundCog,
    Users,
    type LucideIcon,
} from 'lucide-vue-next';
import { computed, ref, watchEffect } from 'vue';

const page = usePage<SharedData>();
const { state } = useSidebar();

const iconMap: Record<string, LucideIcon> = {
    BadgePlus,
    BrainCircuit,
    Building2,
    CalendarDays,
    Circle,
    GalleryHorizontalEnd,
    HeartPulse,
    Hospital,
    Images,
    KeyRound,
    LayoutDashboard,
    Mail,
    Megaphone,
    MessageSquareHeart,
    MessagesSquare,
    Newspaper,
    PackageCheck,
    Palette,
    Settings,
    ShieldCheck,
    Sparkles,
    Stethoscope,
    UserRoundCog,
    Users,
};

const fallbackGroups: NavigationGroup[] = [
    {
        id: 'workspace',
        title: 'Workspace',
        icon: 'LayoutDashboard',
        items: [
            { title: 'Dashboard', href: '/dashboard', icon: 'LayoutDashboard', exact: true },
        ],
    },
];

const groups = computed(() => page.props.navigation?.groups?.length ? page.props.navigation.groups : fallbackGroups);
const currentPath = computed(() => `/${page.url.split('?')[0].replace(/^\//, '').replace(/\/$/, '')}`.replace(/\/\/$/, '/') || '/');
const openGroups = ref<Record<string, boolean>>({});

const iconFor = (name?: string): LucideIcon => iconMap[name || 'Circle'] || Circle;

const normalize = (href: string) => {
    const path = href.split('?')[0].replace(/\/$/, '');
    return path === '' ? '/' : path;
};

const isItemActive = (item: NavigationLink) => {
    const href = normalize(item.href || '#');
    const path = normalize(currentPath.value);

    if (item.exact || href === '/dashboard') {
        return path === href;
    }

    return path === href || path.startsWith(`${href}/`);
};

const isGroupActive = (group: NavigationGroup) => group.items.some(isItemActive);

const handleGroupClick = (group: NavigationGroup) => {
    if (state.value === 'collapsed' && group.items.length > 0) {
        router.visit(group.items[0].href);
    }
};

watchEffect(() => {
    groups.value.forEach((group) => {
        if (openGroups.value[group.id] === undefined) {
            openGroups.value[group.id] = isGroupActive(group) || ['workspace', 'website', 'content'].includes(group.id);
        }
        if (isGroupActive(group)) {
            openGroups.value[group.id] = true;
        }
    });
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="border-r-0 w-10px">
        <SidebarHeader class="border-b border-slate-200/80 bg-white/95 px-3 py-4 backdrop-blur-xl">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child tooltip="Dashboard" class="h-14 rounded-2xl hover:bg-slate-100 data-[active=true]:bg-slate-100">
                        <Link href="/dashboard" class="group/logo flex items-center gap-3">
                            <div class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 via-cyan-600 to-teal-500 text-white shadow-lg shadow-blue-500/25 ring-1 ring-white/50">
                                <Hospital class="size-6" />
                            </div>
                            <div class="min-w-0 group-data-[collapsible=icon]:hidden">
                                <p class="truncate text-sm font-black tracking-tight text-slate-950">AMZ WEBSITE</p>
                                <p class="truncate text-[11px] font-medium text-slate-500">AMZ ADMIN</p>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="bg-white/95 px-2 py-4 backdrop-blur-xl">
            <!-- <div class="mb-4 rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-cyan-50 p-3 group-data-[collapsible=icon]:hidden">
                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-[0.18em] text-blue-700">
                    <Sparkles class="size-3.5" /> Premium UI
                </div>
                <p class="mt-1 text-xs leading-5 text-slate-600">Dynamic module navigation built from registered backend routes.</p>
            </div> -->

            <SidebarMenu class="space-y-2">
                <Collapsible
                    v-for="group in groups"
                    :key="group.id"
                    :open="openGroups[group.id]"
                    class="group/collapsible"
                    @update:open="(value) => (openGroups[group.id] = value)"
                >
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton
                                :tooltip="group.title"
                                :is-active="isGroupActive(group)"
                                class="h-10 rounded-2xl px-3 text-slate-600 hover:bg-slate-100 hover:text-slate-950 data-[active=true]:bg-gradient-to-r data-[active=true]:from-blue-600 data-[active=true]:to-cyan-600 data-[active=true]:font-black data-[active=true]:text-white data-[active=true]:shadow-lg data-[active=true]:shadow-blue-500/20"
                                @click="handleGroupClick(group)"
                            >
                                <component :is="iconFor(group.icon)" class="size-4" />
                                <span class="text-xs font-black uppercase tracking-[0.16em]">{{ group.title }}</span>
                                <ChevronDown class="ml-auto size-4 transition-transform duration-200 group-data-[state=open]/collapsible:rotate-180 group-data-[collapsible=icon]:hidden" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub class="mx-3.5 mt-1 border-l border-slate-200 px-2 py-1 group-data-[collapsible=icon]:mx-0 group-data-[collapsible=icon]:border-l-0 group-data-[collapsible=icon]:px-0">
                                <SidebarMenuSubItem v-for="item in group.items" :key="item.href">
                                    <SidebarMenuSubButton
                                        as-child
                                        :is-active="isItemActive(item)"
                                        :title="item.title"
                                        class="h-9 rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-950 data-[active=true]:bg-blue-50 data-[active=true]:font-bold data-[active=true]:text-blue-700 data-[active=true]:ring-1 data-[active=true]:ring-blue-100 group-data-[collapsible=icon]:h-8 group-data-[collapsible=icon]:w-8 group-data-[collapsible=icon]:px-2"
                                    >
                                        <Link :href="item.href" class="flex items-center gap-2">
                                            <component :is="iconFor(item.icon)" class="size-4" />
                                            <span class="truncate group-data-[collapsible=icon]:hidden">{{ item.title }}</span>
                                            <span v-if="item.badge" class="ml-auto rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-black text-blue-700 group-data-[collapsible=icon]:hidden">{{ item.badge }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>
            </SidebarMenu>
        </SidebarContent>

        <SidebarFooter class="border-t border-slate-200/80 bg-white/95 p-3 backdrop-blur-xl">
            <NavUser />
        </SidebarFooter>
        <SidebarRail />
    </Sidebar>
</template>

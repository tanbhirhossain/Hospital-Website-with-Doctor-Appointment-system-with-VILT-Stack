<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Save, ShieldCheck, Trash2, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Permission {
    id: number;
    name: string;
    group: string;
}

interface RolePermission {
    id: number;
    name: string;
}

interface Role {
    id: number;
    name: string;
    guard_name: string;
    permissions: RolePermission[];
    users_count: number;
    created_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedRoles {
    data: Role[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

interface Props {
    roles: PaginatedRoles;
    permissions: Permission[];
    can: {
        create: boolean;
        edit: boolean;
        delete: boolean;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/roles',
    },
];

const editingRole = ref<Role | null>(null);

const form = useForm({
    name: '',
    permissions: [] as number[],
});

const groupedPermissions = computed(() => {
    return props.permissions.reduce<Record<string, Permission[]>>((groups, permission) => {
        groups[permission.group] = groups[permission.group] ?? [];
        groups[permission.group].push(permission);

        return groups;
    }, {});
});

const isEditing = computed(() => editingRole.value !== null);
const canSubmit = computed(() => (isEditing.value ? props.can.edit : props.can.create));

const resetForm = () => {
    editingRole.value = null;
    form.reset();
    form.clearErrors();
};

const editRole = (role: Role) => {
    editingRole.value = role;
    form.name = role.name;
    form.permissions = role.permissions.map((permission) => permission.id);
    form.clearErrors();
};

const submit = () => {
    if (!canSubmit.value) {
        return;
    }

    if (editingRole.value) {
        form.put(route('roles.update', editingRole.value.id), {
            preserveScroll: true,
            onSuccess: resetForm,
        });

        return;
    }

    form.post(route('roles.store'), {
        preserveScroll: true,
        onSuccess: resetForm,
    });
};

const deleteRole = (role: Role) => {
    if (!props.can.delete || !window.confirm(`Delete role "${role.name}"?`)) {
        return;
    }

    router.delete(route('roles.destroy', role.id), {
        preserveScroll: true,
        onSuccess: () => {
            if (editingRole.value?.id === role.id) {
                resetForm();
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Roles" />

        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-normal text-foreground">Roles</h1>
                    <p class="text-sm text-muted-foreground">{{ roles.total }} total roles</p>
                </div>

                <Button v-if="isEditing" type="button" variant="outline" @click="resetForm">
                    <X class="mr-2 size-4" />
                    Cancel edit
                </Button>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
                <section class="overflow-hidden rounded-lg border bg-background">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-sm">
                            <thead class="border-b bg-muted/50 text-left text-xs font-medium uppercase text-muted-foreground">
                                <tr>
                                    <th class="px-4 py-3">Role</th>
                                    <th class="px-4 py-3">Permissions</th>
                                    <th class="px-4 py-3">Users</th>
                                    <th class="px-4 py-3">Created</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="role in roles.data" :key="role.id" class="hover:bg-muted/30">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex size-9 items-center justify-center rounded-md bg-primary/10 text-primary">
                                                <ShieldCheck class="size-4" />
                                            </div>
                                            <div>
                                                <div class="font-medium text-foreground">{{ role.name }}</div>
                                                <div class="text-xs text-muted-foreground">{{ role.guard_name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex max-w-md flex-wrap gap-1.5">
                                            <span
                                                v-for="permission in role.permissions.slice(0, 5)"
                                                :key="permission.id"
                                                class="rounded border bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                                            >
                                                {{ permission.name }}
                                            </span>
                                            <span v-if="role.permissions.length > 5" class="rounded border px-2 py-0.5 text-xs text-muted-foreground">
                                                +{{ role.permissions.length - 5 }}
                                            </span>
                                            <span v-if="role.permissions.length === 0" class="text-muted-foreground">No permissions</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-muted-foreground">{{ role.users_count }}</td>
                                    <td class="px-4 py-4 text-muted-foreground">{{ role.created_at ?? '-' }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex justify-end gap-2">
                                            <Button
                                                type="button"
                                                variant="outline"
                                                size="sm"
                                                :disabled="!can.edit"
                                                title="Edit role"
                                                @click="editRole(role)"
                                            >
                                                <Pencil class="size-4" />
                                            </Button>
                                            <Button
                                                type="button"
                                                variant="destructive"
                                                size="sm"
                                                :disabled="!can.delete"
                                                title="Delete role"
                                                @click="deleteRole(role)"
                                            >
                                                <Trash2 class="size-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="roles.data.length === 0">
                                    <td colspan="5" class="px-4 py-12 text-center text-muted-foreground">No roles found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col gap-3 border-t px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-muted-foreground">
                            Showing {{ roles.from ?? 0 }} to {{ roles.to ?? 0 }} of {{ roles.total }}
                        </p>
                        <div class="flex flex-wrap gap-1">
                            <template v-for="link in roles.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    preserve-scroll
                                    :class="[
                                        'rounded-md border px-3 py-1.5 text-sm',
                                        link.active ? 'bg-primary text-primary-foreground' : 'bg-background hover:bg-muted',
                                    ]"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="rounded-md border px-3 py-1.5 text-sm text-muted-foreground opacity-50"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </section>

                <section class="rounded-lg border bg-background p-5">
                    <form class="flex flex-col gap-5" @submit.prevent="submit">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold tracking-normal text-foreground">
                                    {{ isEditing ? 'Edit role' : 'Create role' }}
                                </h2>
                                <p class="text-sm text-muted-foreground">{{ isEditing ? editingRole?.name : 'Add a new access role' }}</p>
                            </div>
                            <div class="rounded-md bg-primary/10 p-2 text-primary">
                                <Plus v-if="!isEditing" class="size-5" />
                                <Pencil v-else class="size-5" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="role-name">Name</Label>
                            <Input id="role-name" v-model="form.name" :disabled="!canSubmit || form.processing" placeholder="Manager" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-3">
                            <Label>Permissions</Label>
                            <InputError :message="form.errors.permissions" />

                            <div class="max-h-[420px] overflow-y-auto rounded-md border">
                                <div v-if="permissions.length === 0" class="p-4 text-sm text-muted-foreground">No permissions available.</div>
                                <div v-for="(items, group) in groupedPermissions" :key="group" class="border-b p-4 last:border-b-0">
                                    <div class="mb-3 text-sm font-medium text-foreground">{{ group }}</div>
                                    <div class="grid gap-2">
                                        <label
                                            v-for="permission in items"
                                            :key="permission.id"
                                            class="flex cursor-pointer items-center gap-3 rounded-md px-2 py-1.5 hover:bg-muted"
                                        >
                                            <input
                                                v-model="form.permissions"
                                                type="checkbox"
                                                :value="permission.id"
                                                :disabled="!canSubmit || form.processing"
                                                class="size-4 rounded border-input text-primary focus:ring-primary"
                                            />
                                            <span class="text-sm text-foreground">{{ permission.name }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <Button v-if="isEditing" type="button" variant="outline" :disabled="form.processing" @click="resetForm">
                                <X class="mr-2 size-4" />
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="!canSubmit || form.processing">
                                <Save class="mr-2 size-4" />
                                {{ isEditing ? 'Update role' : 'Create role' }}
                            </Button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </AppLayout>
</template>

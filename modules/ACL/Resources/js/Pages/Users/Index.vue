<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Save, Trash2, UserRound, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Role {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    roles: Role[];
    created_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedUsers {
    data: User[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

interface Props {
    users: PaginatedUsers;
    roles: Role[];
    can: {
        create: boolean;
        edit: boolean;
        delete: boolean;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

const editingUser = ref<User | null>(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    roles: [] as number[],
});

const isEditing = computed(() => editingUser.value !== null);
const canSubmit = computed(() => (isEditing.value ? props.can.edit : props.can.create));

const resetForm = () => {
    editingUser.value = null;
    form.reset();
    form.clearErrors();
};

const editUser = (user: User) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.roles = user.roles.map((role) => role.id);
    form.clearErrors();
};

const submit = () => {
    if (!canSubmit.value) {
        return;
    }

    if (editingUser.value) {
        form.put(route('users.update', editingUser.value.id), {
            preserveScroll: true,
            onSuccess: resetForm,
        });

        return;
    }

    form.post(route('users.store'), {
        preserveScroll: true,
        onSuccess: resetForm,
    });
};

const deleteUser = (user: User) => {
    if (!props.can.delete || !window.confirm(`Delete user "${user.name}"?`)) {
        return;
    }

    router.delete(route('users.destroy', user.id), {
        preserveScroll: true,
        onSuccess: () => {
            if (editingUser.value?.id === user.id) {
                resetForm();
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Users" />

        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-normal text-foreground">Users</h1>
                    <p class="text-sm text-muted-foreground">{{ users.total }} total users</p>
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
                                    <th class="px-4 py-3">User</th>
                                    <th class="px-4 py-3">Roles</th>
                                    <th class="px-4 py-3">Created</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-muted/30">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex size-9 items-center justify-center rounded-md bg-primary/10 text-primary">
                                                <UserRound class="size-4" />
                                            </div>
                                            <div>
                                                <div class="font-medium text-foreground">{{ user.name }}</div>
                                                <div class="text-xs text-muted-foreground">{{ user.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap gap-1.5">
                                            <span
                                                v-for="role in user.roles"
                                                :key="role.id"
                                                class="rounded border bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                                            >
                                                {{ role.name }}
                                            </span>
                                            <span v-if="user.roles.length === 0" class="text-muted-foreground">No roles</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-muted-foreground">{{ user.created_at ?? '-' }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex justify-end gap-2">
                                            <Button
                                                type="button"
                                                variant="outline"
                                                size="sm"
                                                :disabled="!can.edit"
                                                title="Edit user"
                                                @click="editUser(user)"
                                            >
                                                <Pencil class="size-4" />
                                            </Button>
                                            <Button
                                                type="button"
                                                variant="destructive"
                                                size="sm"
                                                :disabled="!can.delete"
                                                title="Delete user"
                                                @click="deleteUser(user)"
                                            >
                                                <Trash2 class="size-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="users.data.length === 0">
                                    <td colspan="4" class="px-4 py-12 text-center text-muted-foreground">No users found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col gap-3 border-t px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-muted-foreground">
                            Showing {{ users.from ?? 0 }} to {{ users.to ?? 0 }} of {{ users.total }}
                        </p>
                        <div class="flex flex-wrap gap-1">
                            <template v-for="link in users.links" :key="link.label">
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
                                    {{ isEditing ? 'Edit user' : 'Create user' }}
                                </h2>
                                <p class="text-sm text-muted-foreground">{{ isEditing ? editingUser?.email : 'Add a new account' }}</p>
                            </div>
                            <div class="rounded-md bg-primary/10 p-2 text-primary">
                                <Plus v-if="!isEditing" class="size-5" />
                                <Pencil v-else class="size-5" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="user-name">Name</Label>
                            <Input id="user-name" v-model="form.name" :disabled="!canSubmit || form.processing" placeholder="Jane Doe" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="user-email">Email</Label>
                            <Input
                                id="user-email"
                                v-model="form.email"
                                type="email"
                                :disabled="!canSubmit || form.processing"
                                placeholder="jane@example.com"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="user-password">Password</Label>
                            <Input
                                id="user-password"
                                v-model="form.password"
                                type="password"
                                :disabled="!canSubmit || form.processing"
                                :placeholder="isEditing ? 'Leave blank to keep current password' : 'Set initial password'"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="grid gap-3">
                            <Label>Roles</Label>
                            <InputError :message="form.errors.roles" />

                            <div class="max-h-[320px] overflow-y-auto rounded-md border p-3">
                                <div v-if="roles.length === 0" class="text-sm text-muted-foreground">No roles available.</div>
                                <div class="grid gap-2">
                                    <label
                                        v-for="role in roles"
                                        :key="role.id"
                                        class="flex cursor-pointer items-center gap-3 rounded-md px-2 py-1.5 hover:bg-muted"
                                    >
                                        <input
                                            v-model="form.roles"
                                            type="checkbox"
                                            :value="role.id"
                                            :disabled="!canSubmit || form.processing"
                                            class="size-4 rounded border-input text-primary focus:ring-primary"
                                        />
                                        <span class="text-sm text-foreground">{{ role.name }}</span>
                                    </label>
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
                                {{ isEditing ? 'Update user' : 'Create user' }}
                            </Button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </AppLayout>
</template>

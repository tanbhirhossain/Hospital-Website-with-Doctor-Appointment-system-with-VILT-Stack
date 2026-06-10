<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{ doctors: Array<{ id: number; name: string; specialty: string; is_active: boolean; department?: { name: string } }> }>();
const form = useForm();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Doctors',
        href: '#',
    },
];

const destroyDoctor = (doctorId: number) => {
    if (!window.confirm('Are you sure you want to delete this doctor?')) {
        return;
    }

    form.delete(route('doctors.destroy', doctorId), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Doctors" />

        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-normal text-foreground">Doctors</h1>
                    <p class="text-sm text-muted-foreground">Manage doctor profiles, specialties, and availability.</p>
                </div>

                <Link
                    href="/admin/doctors/create"
                    class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition"
                >
                    New Doctor
                </Link>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Department</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Specialty</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-if="doctors.length === 0">
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">No doctors found yet.</td>
                        </tr>
                        <tr v-for="doctor in doctors" :key="doctor.id">
                            <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ doctor.name }}</td>
                            <td class="px-4 py-4 text-sm text-gray-600">{{ doctor.department?.name ?? '—' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-600">{{ doctor.specialty }}</td>
                            <td class="px-4 py-4 text-sm text-gray-600">{{ doctor.is_active ? 'Active' : 'Inactive' }}</td>
                            <td class="px-4 py-4 text-right text-sm font-medium space-x-2">
                                <Link
                                    :href="route('admin.doctors.edit', doctor.id)"
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    Edit
                                </Link>
                                <button
                                    type="button"
                                    class="text-rose-600 hover:text-rose-900"
                                    @click="destroyDoctor(doctor.id)"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

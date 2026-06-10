<script setup>
import { Link, router } from '@inertiajs/vue3';

defineProps({
    items: Array
});

const deleteItem = (id) => {
    if (confirm('Are you absolutely sure you want to delete this Center of Excellence?')) {
        router.delete(route('admin.coe.destroy', id));
    }
};
</script>

<template>
    <div class="max-w-7xl mx-auto p-6 mt-10">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Centers of Excellence</h1>
                <p class="text-sm text-gray-500 mt-1">Manage corporate domains, capabilities, and global configurations.</p>
            </div>
            <Link :href="route('admin.coe.create')" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150 shadow-sm">
                Add New COE
            </Link>
        </div>

        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-emerald-800">{{ $page.props.flash.success }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Slug</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">SEO Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Icon Tag</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="coe in items" :key="coe.id" class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ coe.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                    {{ coe.slug }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="[coe.indexable ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-amber-50 text-amber-700 border border-amber-200', 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium']">
                                    {{ coe.indexable ? 'Indexable' : 'No-Index' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                {{ coe.icon || '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                <Link :href="route('admin.coe.show', coe.id)" class="text-gray-600 hover:text-gray-900 transition font-medium">View</Link>
                                <Link :href="route('admin.coe.edit', coe.id)" class="text-indigo-600 hover:text-indigo-900 transition font-medium">Edit</Link>
                                <button @click="deleteItem(coe.id)" class="text-rose-600 hover:text-rose-900 transition font-medium cursor-pointer">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="items.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 bg-gray-50">
                                <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v5m16-5h-4.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293H10m-2 0H5.172a1 1 0 00-.707.293L2 13" />
                                </svg>
                                No records discovered within this system.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
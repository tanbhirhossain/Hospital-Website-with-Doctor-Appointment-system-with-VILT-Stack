<template>
  <AppLayout>
    <div class="m-2 p-6 rounded-2xl bg-white border border-gray-100 shadow-sm">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Center of Excellence</h1>
          <p class="text-gray-500">Manage your Center of Excellence (COE) entries here.</p>
        </div>
        <Link href="/coe/create" class="flex items-center gap-2 px-4 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-all font-medium">
          <span class="text-lg">+</span> Create New COE
        </Link>
      </div>

      <div class="overflow-hidden border border-gray-200 rounded-xl">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="coe in coes" :key="coe.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 text-sm text-gray-900 font-medium">#{{ coe.id }}</td>
              <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ coe.name }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ coe.description?.length > 100 ? coe.description.substring(0, 100) + '...' : coe.description }}
              </td>
                <td class="px-6 py-4 text-sm text-right">
                <Link 
                    :href="`/admin/coe/${coe.id}/edit`" 
                    class="text-indigo-600 hover:text-indigo-800 font-semibold hover:underline"
                >
                    Edit
                </Link>
                </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
    import { ref } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import AppLayout from '@/layouts/AppLayout.vue';

    defineProps({
        coes: {
            type: Array,
            required: true,
        },
    });

</script>

<style scoped>
  /* Add any additional styles if needed */
@layer base {
  Link, [role="button"], a { cursor: pointer; }
}
</style>
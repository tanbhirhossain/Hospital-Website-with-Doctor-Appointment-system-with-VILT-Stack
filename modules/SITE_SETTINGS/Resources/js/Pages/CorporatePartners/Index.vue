<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Handshake, Image, Pencil, Plus, Save, Trash2, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Corporate Partners', href: '/admin/corporate-partners' }];
const flash = computed(() => (usePage().props as any).flash);
const props = defineProps<{ partners: Array<any> }>();
const editingId = ref<number | null>(null);
const imagePreview = ref<string | null>(null);
const fileKey = ref(0);

const form = useForm({ name: '', website_url: '', logo: null as File | null, is_active: true, sort_order: 0 });
function resetForm() { form.reset(); form.is_active = true; editingId.value = null; imagePreview.value = null; fileKey.value++; }
function editPartner(p: any) { editingId.value = p.id; form.name = p.name; form.website_url = p.website_url || ''; form.is_active = p.is_active; form.sort_order = p.sort_order; imagePreview.value = p.logo_url || null; }
function handleImage(e: Event) { const file = (e.target as HTMLInputElement).files?.[0]; if (file) { form.logo = file; imagePreview.value = URL.createObjectURL(file); } }
function submit() {
  const url = editingId.value ? `/admin/corporate-partners/${editingId.value}` : '/admin/corporate-partners';
  form.post(url, { forceFormData: true, onSuccess: resetForm, ...(editingId.value ? { data: { _method: 'put' } } : {}) });
}
function deletePartner(id: number) { if (confirm('Delete?')) router.delete(`/admin/corporate-partners/${id}`); }
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Corporate Partners" />
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-md"><Handshake class="h-5 w-5 text-white" /></div>
          <div><h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Corporate Partners</h1><p class="text-sm text-gray-500 dark:text-gray-400">Manage homepage partner logos</p></div>
        </div>
        <Button @click="resetForm" variant="default" class="gap-2 bg-violet-600 hover:bg-violet-700"><Plus class="h-4 w-4" /> Add Partner</Button>
      </div>
      <div v-if="flash?.success" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400">{{ flash.success }}</div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
          <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700"><h2 class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white"><component :is="editingId ? Pencil : Plus" class="h-4 w-4 text-violet-500" />{{ editingId ? 'Edit Partner' : 'New Partner' }}</h2></div>
            <form @submit.prevent="submit" class="space-y-4 p-5">
              <div><Label>Name <span class="text-red-500">*</span></Label><Input v-model="form.name" placeholder="Partner name" class="mt-1" /><InputError :message="form.errors.name" /></div>
              <div><Label>Website URL</Label><Input v-model="form.website_url" placeholder="https://..." class="mt-1" /></div>
              <div>
                <Label>Logo</Label>
                <div class="mt-1 rounded-lg border-2 border-dashed border-gray-200 p-4 text-center dark:border-gray-600">
                  <div v-if="imagePreview" class="mb-2"><img :src="imagePreview" class="mx-auto max-h-20 rounded object-contain" /></div>
                  <Image v-else class="mx-auto mb-2 h-8 w-8 text-gray-300" />
                  <input :key="fileKey" type="file" accept="image/*" @change="handleImage" class="text-sm text-gray-500" />
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div><Label>Sort Order</Label><Input v-model.number="form.sort_order" type="number" class="mt-1" /></div>
                <div class="flex items-end pb-1"><label class="flex cursor-pointer items-center gap-2"><input type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" /><span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span></label></div>
              </div>
              <div class="flex gap-2 pt-2">
                <Button type="submit" :disabled="form.processing" class="flex-1 gap-2 bg-violet-600 hover:bg-violet-700"><Save class="h-4 w-4" />{{ editingId ? 'Update' : 'Create' }}</Button>
                <Button v-if="editingId" type="button" variant="outline" @click="resetForm"><X class="h-4 w-4" /></Button>
              </div>
            </form>
          </div>
        </div>
        <div class="lg:col-span-2">
          <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-800"><tr><th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Logo</th><th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Details</th><th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th><th class="px-5 py-3"></th></tr></thead>
              <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <tr v-for="p in partners" :key="p.id" class="transition hover:bg-gray-50 dark:hover:bg-gray-800/50" :class="{ 'ring-1 ring-inset ring-violet-200 bg-violet-50/30': editingId === p.id }">
                  <td class="px-5 py-3"><div class="h-12 w-16 overflow-hidden rounded-lg border border-gray-200 bg-gray-50"><img v-if="p.logo_url" :src="p.logo_url" class="h-full w-full object-contain" /><Handshake v-else class="m-auto mt-2 h-6 w-6 text-gray-300" /></div></td>
                  <td class="px-5 py-3"><p class="font-semibold text-gray-900 dark:text-white">{{ p.name }}</p><p class="text-xs text-gray-400">{{ p.website_url || 'No website' }}</p></td>
                  <td class="px-5 py-3"><span :class="p.is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-gray-100 text-gray-500 ring-1 ring-gray-200'" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">{{ p.is_active ? 'Active' : 'Inactive' }}</span></td>
                  <td class="px-5 py-3"><div class="flex justify-end gap-2"><button @click="editPartner(p)" class="rounded-lg p-1.5 text-gray-400 hover:bg-violet-100 hover:text-violet-600"><Pencil class="h-4 w-4" /></button><button @click="deletePartner(p.id)" class="rounded-lg p-1.5 text-gray-400 hover:bg-red-100 hover:text-red-600"><Trash2 class="h-4 w-4" /></button></div></td>
                </tr>
                <tr v-if="partners.length === 0"><td colspan="4" class="py-12 text-center text-sm text-gray-400">No partners yet.</td></tr>
              </tbody>
            </table></div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

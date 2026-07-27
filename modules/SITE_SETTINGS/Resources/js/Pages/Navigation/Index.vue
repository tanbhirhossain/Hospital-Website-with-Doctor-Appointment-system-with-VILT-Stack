<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ListTree, Pencil, Plus, Save, Trash2, X, GitMerge, ChevronUp, ChevronDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Navigation Menus', href: '/admin/navigation-menus' }];
const page = usePage();
const flash = computed(() => (page.props as any).flash);

const props = defineProps<{
  menus: Array<any>;
}>();

const editingId = ref<number | null>(null);
const mergeMode = ref(false);
const mergeSource = ref<number | null>(null);

const form = useForm({
  label: '', url: '', route_name: '', icon: '', target: '_self',
  parent_id: null as number | null, sort_order: 0, is_active: true, location: 'header',
  menu_type: 'link' as string, config: '', badge_text: '', badge_color: '', description: '',
});
const mergeForm = useForm({ source_id: 0, target_id: 0 });

function resetForm() { form.reset(); form.is_active = true; form.target = '_self'; form.location = 'header'; form.menu_type = 'link'; form.sort_order = 0; editingId.value = null; }

function editMenu(m: any) {
  editingId.value = m.id;
  form.label = m.label; form.url = m.url || ''; form.route_name = m.route_name || '';
  form.icon = m.icon || ''; form.target = m.target; form.parent_id = m.parent_id;
  form.sort_order = m.sort_order; form.is_active = m.is_active; form.location = m.location;
  form.menu_type = m.menu_type || 'link'; form.config = m.config ? JSON.stringify(m.config, null, 2) : '';
  form.badge_text = m.badge_text || ''; form.badge_color = m.badge_color || '';
  form.description = m.description || '';
}

function submit() {
  if (editingId.value) {
    form.put(`/admin/navigation-menus/${editingId.value}`, { onSuccess: resetForm });
  } else {
    form.post('/admin/navigation-menus', { onSuccess: resetForm });
  }
}

function deleteMenu(id: number) {
  if (!confirm('Delete this menu item?')) return;
  router.delete(`/admin/navigation-menus/${id}`);
}

function startMerge(id: number) { mergeMode.value = true; mergeSource.value = id; }
function confirmMerge(targetId: number) {
  if (!mergeSource.value || mergeSource.value === targetId) return;
  mergeForm.source_id = mergeSource.value; mergeForm.target_id = targetId;
  mergeForm.post('/admin/navigation-menus/merge', { onSuccess: () => { mergeMode.value = false; mergeSource.value = null; } });
}
function cancelMerge() { mergeMode.value = false; mergeSource.value = null; }

function moveUp(idx: number) {
  if (idx === 0) return;
  const order = props.menus.map(m => m.id);
  [order[idx - 1], order[idx]] = [order[idx], order[idx - 1]];
  router.post('/admin/navigation-menus/reorder', { order });
}
function moveDown(idx: number) {
  if (idx >= props.menus.length - 1) return;
  const order = props.menus.map(m => m.id);
  [order[idx], order[idx + 1]] = [order[idx + 1], order[idx]];
  router.post('/admin/navigation-menus/reorder', { order });
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Navigation Menus" />
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-md">
            <ListTree class="h-5 w-5 text-white" />
          </div>
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Navigation Menus</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Add, remove, merge & reorder website navigation</p>
          </div>
        </div>
      </div>

      <div v-if="flash?.success" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400">{{ flash.success }}</div>
      <div v-if="mergeMode" class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800 flex items-center justify-between dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-700">
        <span>🔀 Merge Mode: Click a menu to merge "<strong>{{ menus.find(m => m.id === mergeSource)?.label }}</strong>" into it.</span>
        <button @click="cancelMerge" class="text-amber-600 hover:text-amber-800"><X class="w-4 h-4" /></button>
      </div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Form Panel -->
        <div class="lg:col-span-1">
          <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
              <h2 class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                <component :is="editingId ? Pencil : Plus" class="h-4 w-4 text-violet-500" />
                {{ editingId ? 'Edit Menu Item' : 'New Menu Item' }}
              </h2>
            </div>
            <form @submit.prevent="submit" class="space-y-4 p-5">
              <div><Label>Label <span class="text-red-500">*</span></Label><Input v-model="form.label" placeholder="Menu label" class="mt-1" /><InputError :message="form.errors.label" /></div>
              <div><Label>URL</Label><Input v-model="form.url" placeholder="/about or https://..." class="mt-1" /></div>
              <div><Label>Route Name</Label><Input v-model="form.route_name" placeholder="front.about" class="mt-1" /></div>
              <div class="grid grid-cols-2 gap-3">
                <div><Label>Target</Label><select v-model="form.target" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-violet-400 focus:ring-2 focus:ring-violet-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white"><option value="_self">Same Tab</option><option value="_blank">New Tab</option></select></div>
                <div><Label>Parent</Label><select v-model="form.parent_id" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-violet-400 focus:ring-2 focus:ring-violet-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white"><option :value="null">— None —</option><option v-for="m in menus.filter(m => !m.parent_id)" :key="m.id" :value="m.id">{{ m.label }}</option></select></div>
              </div>
              <div>
                <Label>Menu Type</Label>
                <select v-model="form.menu_type" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-violet-400 focus:ring-2 focus:ring-violet-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                  <option value="link">Simple Link</option>
                  <option value="dropdown">Dropdown</option>
                  <option value="mega_menu">Mega Menu</option>
                </select>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div><Label>Icon</Label><Input v-model="form.icon" placeholder="fa-home" class="mt-1" /></div>
                <div><Label>Sort Order</Label><Input v-model.number="form.sort_order" type="number" class="mt-1" /></div>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div><Label>Badge Text</Label><Input v-model="form.badge_text" placeholder="New, Hot..." class="mt-1" /></div>
                <div><Label>Badge Color</Label><Input v-model="form.badge_color" placeholder="bg-red-500" class="mt-1" /></div>
              </div>
              <div v-if="form.menu_type !== 'link'">
                <Label>Description (shown in submenu)</Label>
                <Input v-model="form.description" placeholder="Short description for submenu items" class="mt-1" />
              </div>
              <div v-if="form.menu_type === 'mega_menu'">
                <Label>Mega Menu Config (JSON)</Label>
                <textarea v-model="form.config" rows="4" placeholder='{"banner_url":"","columns":2}' class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm font-mono shadow-sm focus:border-violet-400 focus:ring-2 focus:ring-violet-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                <p class="text-xs text-gray-400 mt-1">Optional: banner_url, columns, featured items</p>
              </div>
              <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500" /><span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span></label>
              <div class="flex gap-2 pt-2">
                <Button type="submit" :disabled="form.processing" class="flex-1 gap-2 bg-violet-600 hover:bg-violet-700"><Save class="h-4 w-4" /> {{ editingId ? 'Update' : 'Add' }}</Button>
                <Button v-if="editingId" type="button" variant="outline" @click="resetForm"><X class="h-4 w-4" /></Button>
              </div>
            </form>
          </div>
        </div>

        <!-- List -->
        <div class="lg:col-span-2">
          <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700 flex items-center justify-between">
              <h2 class="font-semibold text-gray-900 dark:text-white">Menu Items ({{ menus.length }})</h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
              <div v-for="(menu, idx) in menus" :key="menu.id"
                :class="['px-5 py-3 transition hover:bg-gray-50 dark:hover:bg-gray-800/50', mergeMode && mergeSource !== menu.id ? 'cursor-pointer bg-amber-50/50 dark:bg-amber-900/10' : mergeSource === menu.id ? 'ring-1 ring-inset ring-violet-200 bg-violet-50/30' : '']"
                @click="mergeMode && mergeSource !== menu.id ? confirmMerge(menu.id) : null">
                <div class="flex items-center gap-3">
                  <div class="flex flex-col gap-0.5">
                    <button @click.stop="moveUp(idx)" :disabled="idx === 0" class="text-gray-400 hover:text-gray-700 disabled:opacity-30"><ChevronUp class="w-3.5 h-3.5" /></button>
                    <button @click.stop="moveDown(idx)" :disabled="idx === menus.length - 1" class="text-gray-400 hover:text-gray-700 disabled:opacity-30"><ChevronDown class="w-3.5 h-3.5" /></button>
                  </div>
                  <span :class="menu.is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-gray-100 text-gray-500 ring-1 ring-gray-200'" class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium uppercase">
                    {{ menu.is_active ? 'Active' : 'Off' }}
                  </span>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ menu.label }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ menu.url || menu.route_name || 'No link' }}</p>
                  </div>
                  <div v-if="!mergeMode" class="flex gap-1 shrink-0">
                    <button @click="editMenu(menu)" class="rounded-lg p-1.5 text-gray-400 hover:bg-violet-100 hover:text-violet-600 transition"><Pencil class="h-4 w-4" /></button>
                    <button @click="startMerge(menu.id)" class="rounded-lg p-1.5 text-gray-400 hover:bg-amber-100 hover:text-amber-600 transition"><GitMerge class="h-4 w-4" /></button>
                    <button @click="deleteMenu(menu.id)" class="rounded-lg p-1.5 text-gray-400 hover:bg-red-100 hover:text-red-600 transition"><Trash2 class="h-4 w-4" /></button>
                  </div>
                </div>
                <!-- Children -->
                <div v-if="menu.children?.length" class="mt-2 ml-10 space-y-1">
                  <div v-for="child in menu.children" :key="child.id" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 text-xs">
                    <span :class="child.is_active ? 'text-emerald-600' : 'text-gray-400'" class="font-medium">{{ child.is_active ? '●' : '○' }}</span>
                    <span class="text-gray-700 dark:text-gray-300 flex-1 truncate">{{ child.label }}</span>
                    <span class="text-gray-400">{{ child.url || child.route_name }}</span>
                    <button v-if="!mergeMode" @click="editMenu(child)" class="text-gray-400 hover:text-violet-600"><Pencil class="w-3 h-3" /></button>
                    <button v-if="!mergeMode" @click="deleteMenu(child.id)" class="text-gray-400 hover:text-red-600"><Trash2 class="w-3 h-3" /></button>
                  </div>
                </div>
              </div>
              <div v-if="menus.length === 0" class="py-12 text-center text-sm text-gray-400">No menu items. Add your first one using the form.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

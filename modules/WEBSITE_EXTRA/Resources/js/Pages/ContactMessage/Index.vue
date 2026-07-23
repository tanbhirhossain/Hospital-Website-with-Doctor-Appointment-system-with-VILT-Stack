<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Archive, CheckCircle2, Mail, MailWarning, Search, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface ContactMessage {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    department: string | null;
    subject: string;
    message: string;
    source: string;
    status: string;
    mail_status: string;
    mail_error: string | null;
    ip_address: string | null;
    admin_notified_at: string | null;
    customer_notified_at: string | null;
    created_at: string | null;
    created_at_human: string | null;
}

interface PaginationLink { url: string | null; label: string; active: boolean }
interface Paginated<T> { data: T[]; from: number | null; to: number | null; total: number; links: PaginationLink[] }

const props = defineProps<{
    messages: Paginated<ContactMessage>;
    filters: Record<string, string | null>;
    stats: Record<string, number>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Contact Messages', href: '/admin/contact-messages' },
];

const filterForm = ref({
    search: props.filters.search || '',
    status: props.filters.status || '',
    mail_status: props.filters.mail_status || '',
    department: props.filters.department || '',
});

const applyFilters = () => router.get('/admin/contact-messages', filterForm.value, { preserveState: true, replace: true });
const resetFilters = () => {
    filterForm.value = { search: '', status: '', mail_status: '', department: '' };
    applyFilters();
};

const statusClass = (status: string) => ({
    new: 'bg-blue-50 text-blue-700 ring-blue-200',
    read: 'bg-amber-50 text-amber-700 ring-amber-200',
    resolved: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    archived: 'bg-slate-100 text-slate-600 ring-slate-200',
}[status] || 'bg-slate-100 text-slate-600 ring-slate-200');

const mailClass = (status: string) => ({
    sent: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    pending: 'bg-amber-50 text-amber-700 ring-amber-200',
    failed: 'bg-rose-50 text-rose-700 ring-rose-200',
}[status] || 'bg-slate-100 text-slate-600 ring-slate-200');

const postAction = (url: string) => router.post(url, {}, { preserveScroll: true });
const deleteMessage = (message: ContactMessage) => {
    if (!confirm(`Delete contact message from ${message.name}?`)) return;
    router.delete(`/admin/contact-messages/${message.id}`, { preserveScroll: true });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Contact Messages" />

        <div class="space-y-6">
            <section class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-blue-900 via-blue-800 to-sky-600 p-6 text-white shadow-2xl shadow-blue-900/20 md:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-100">WEBSITE_EXTRA Backend</p>
                        <h1 class="mt-2 text-3xl font-black tracking-tight md:text-5xl">Contact Messages</h1>
                        <p class="mt-3 max-w-3xl text-sm leading-7 text-blue-50">Messages submitted from the public FRONTEND contact page are saved here and email delivery is tracked for admin and customer notifications.</p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-4 lg:w-[520px]">
                        <div v-for="(value, key) in stats" :key="key" class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                            <p class="text-2xl font-black">{{ value }}</p>
                            <p class="text-[10px] font-black uppercase tracking-wider text-cyan-100">{{ key }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="admin-panel">
                <div class="grid gap-3 lg:grid-cols-[1fr_160px_160px_180px_auto]">
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                        <input v-model="filterForm.search" class="w-full pl-10" placeholder="Search name, email, phone, subject, message..." @keyup.enter="applyFilters" />
                    </div>
                    <select v-model="filterForm.status">
                        <option value="">All statuses</option>
                        <option value="new">New</option>
                        <option value="read">Read</option>
                        <option value="resolved">Resolved</option>
                        <option value="archived">Archived</option>
                    </select>
                    <select v-model="filterForm.mail_status">
                        <option value="">All mail</option>
                        <option value="pending">Pending</option>
                        <option value="sent">Sent</option>
                        <option value="failed">Failed</option>
                    </select>
                    <input v-model="filterForm.department" placeholder="Department" @keyup.enter="applyFilters" />
                    <div class="flex gap-2">
                        <button class="admin-button-primary" @click="applyFilters">Filter</button>
                        <button class="admin-button-secondary" @click="resetFilters">Reset</button>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm shadow-slate-200/60">
                <div class="overflow-x-auto">
                    <table class="min-w-[1080px]">
                        <thead>
                            <tr>
                                <th>Sender</th>
                                <th>Message</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Email</th>
                                <th>Submitted</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="message in messages.data" :key="message.id">
                                <td>
                                    <p class="font-black text-slate-950">{{ message.name }}</p>
                                    <a :href="`mailto:${message.email}`" class="text-xs font-semibold text-blue-700 hover:underline">{{ message.email }}</a>
                                    <p v-if="message.phone" class="text-xs text-slate-500">{{ message.phone }}</p>
                                </td>
                                <td class="max-w-md">
                                    <p class="font-bold text-slate-900">{{ message.subject }}</p>
                                    <p class="mt-1 line-clamp-2 text-sm leading-6 text-slate-500">{{ message.message }}</p>
                                    <details class="mt-2 text-xs text-slate-500">
                                        <summary class="cursor-pointer font-bold text-blue-700">Read full message</summary>
                                        <p class="mt-2 whitespace-pre-line rounded-2xl bg-slate-50 p-3 text-sm leading-6 text-slate-600">{{ message.message }}</p>
                                    </details>
                                </td>
                                <td>{{ message.department || 'General' }}</td>
                                <td><span :class="['rounded-full px-2.5 py-1 text-xs font-black uppercase tracking-wider ring-1', statusClass(message.status)]">{{ message.status }}</span></td>
                                <td>
                                    <span :class="['inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-black uppercase tracking-wider ring-1', mailClass(message.mail_status)]">
                                        <MailWarning v-if="message.mail_status === 'failed'" class="size-3" />
                                        <Mail v-else class="size-3" />
                                        {{ message.mail_status }}
                                    </span>
                                    <p v-if="message.mail_error" class="mt-1 max-w-[180px] truncate text-xs text-rose-600" :title="message.mail_error">{{ message.mail_error }}</p>
                                </td>
                                <td>
                                    <p class="font-semibold text-slate-700">{{ message.created_at_human }}</p>
                                    <p class="text-xs text-slate-500">{{ message.created_at }}</p>
                                </td>
                                <td>
                                    <div class="flex justify-end gap-1.5">
                                        <button class="rounded-xl border border-slate-200 p-2 text-blue-700 hover:bg-blue-50" title="Mark read" @click="postAction(`/admin/contact-messages/${message.id}/read`)"><Mail class="size-4" /></button>
                                        <button class="rounded-xl border border-slate-200 p-2 text-emerald-700 hover:bg-emerald-50" title="Resolve" @click="postAction(`/admin/contact-messages/${message.id}/resolve`)"><CheckCircle2 class="size-4" /></button>
                                        <button class="rounded-xl border border-slate-200 p-2 text-slate-600 hover:bg-slate-50" title="Archive" @click="postAction(`/admin/contact-messages/${message.id}/archive`)"><Archive class="size-4" /></button>
                                        <button class="rounded-xl border border-slate-200 p-2 text-rose-600 hover:bg-rose-50" title="Delete" @click="deleteMessage(message)"><Trash2 class="size-4" /></button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="messages.data.length === 0">
                                <td colspan="7" class="py-16 text-center text-slate-500">No contact messages found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                    <span>Showing {{ messages.from ?? 0 }} to {{ messages.to ?? 0 }} of {{ messages.total }}</span>
                    <div class="flex flex-wrap gap-1">
                        <template v-for="link in messages.links" :key="link.label">
                            <Link v-if="link.url" :href="link.url" preserve-scroll :class="['rounded-xl border px-3 py-1.5 text-xs font-black', link.active ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-blue-50']" v-html="link.label" />
                            <span v-else class="rounded-xl border border-slate-100 px-3 py-1.5 text-xs text-slate-300" v-html="link.label" />
                        </template>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>

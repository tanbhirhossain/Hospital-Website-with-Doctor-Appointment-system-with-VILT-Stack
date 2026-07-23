<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { Activity, Copy, Edit3, Eye, FileText, GripVertical, Mail, Megaphone, MousePointerClick, Plus, Send, Sparkles, Trash2, Upload, Users, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface PaginationLink { url: string | null; label: string; active: boolean }
interface Paginated<T> { data: T[]; from: number | null; to: number | null; total: number; links: PaginationLink[] }
interface Subscriber { id: number; name: string | null; email: string; phone: string | null; source: string | null; isActive: boolean; status: string; audience_type: string | null; tags: string[]; country: string | null; subscribed_at: string | null; created_at: string | null }
interface EmailBlock { id?: string; type: string; data: Record<string, any> }
interface EmailTemplate { id: number; name: string; slug: string; category: string; subject: string; preheader: string | null; builder_json: EmailBlock[]; html_content: string | null; text_content: string | null; status: string; campaigns_count: number; creator: string | null; created_at: string | null; updated_at: string | null }
interface TemplateOption { id: number; name: string; category: string; subject: string; preheader: string | null; status: string }
interface Campaign { id: number; name: string; type: string; subject: string; preheader: string | null; email_template_id: number | null; template: { id: number; name: string; category: string } | null; recipient_filters: Record<string, any>; sender_name: string | null; sender_email: string | null; reply_to: string | null; status: string; scheduled_at: string | null; scheduled_at_human: string | null; sent_at: string | null; total_recipients: number; sent_count: number; failed_count: number; opened_count: number; clicked_count: number; open_rate: number; click_rate: number; recipients_count: number; creator: string | null; created_at: string | null; updated_at: string | null }

const props = defineProps<{
    stats: Record<string, number>;
    subscribers: Paginated<Subscriber>;
    campaigns: Paginated<Campaign>;
    templates: Paginated<EmailTemplate>;
    templateOptions: TemplateOption[];
    segments: { audienceTypes: string[]; sources: string[]; countries: string[]; tags: string[] };
    recentBlogs: { id: number; name: string; slug: string; url: string; created_at: string | null }[];
    filters: Record<string, string | null>;
    campaignTypes: Record<string, string>;
    can: { create: boolean; edit: boolean; delete: boolean; send: boolean };
    mail: { default: string; from_address: string | null; from_name: string | null };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Email Marketing', href: '/email-marketing' }];
const page = usePage();
const flashSuccess = computed(() => (page.props as any).flash?.success as string | undefined);
const flashError = computed(() => (page.props as any).flash?.error as string | undefined);
const queryTab = new URLSearchParams(window.location.search).get('tab');
const activeTab = ref(queryTab || 'overview');
const tabs = [
    { id: 'overview', label: 'Overview', icon: Activity },
    { id: 'campaigns', label: 'Campaigns', icon: Megaphone },
    { id: 'templates', label: 'Templates', icon: FileText },
    { id: 'subscribers', label: 'Subscribers', icon: Users },
];

const filterForm = ref({
    subscriber_search: props.filters.subscriber_search || '',
    subscriber_status: props.filters.subscriber_status || '',
    subscriber_audience_type: props.filters.subscriber_audience_type || '',
    subscriber_source: props.filters.subscriber_source || '',
    subscriber_country: props.filters.subscriber_country || '',
    campaign_search: props.filters.campaign_search || '',
    campaign_status: props.filters.campaign_status || '',
    campaign_type: props.filters.campaign_type || '',
    template_search: props.filters.template_search || '',
    template_category: props.filters.template_category || '',
    template_status: props.filters.template_status || '',
});

const campaignTypeLabel = (type: string) => props.campaignTypes[type] || type.replaceAll('_', ' ');
const tagText = (tags: string[] | null | undefined) => (tags || []).join(', ');
const splitTags = (value: string) => value.split(',').map((tag) => tag.trim()).filter(Boolean);
const safeId = () => Math.random().toString(36).slice(2, 11);

const statusClass = (status: string) => {
    const map: Record<string, string> = {
        active: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        subscribed: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        sent: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        sending: 'bg-blue-50 text-blue-700 ring-blue-200',
        scheduled: 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        draft: 'bg-slate-50 text-slate-700 ring-slate-200',
        failed: 'bg-rose-50 text-rose-700 ring-rose-200',
        cancelled: 'bg-amber-50 text-amber-700 ring-amber-200',
        unsubscribed: 'bg-rose-50 text-rose-700 ring-rose-200',
        bounced: 'bg-orange-50 text-orange-700 ring-orange-200',
    };
    return map[status] || 'bg-slate-50 text-slate-700 ring-slate-200';
};

const applyFilters = (tab: string) => {
    activeTab.value = tab;
    router.get(route('emailmarketing.index'), { ...filterForm.value, tab }, { preserveState: true, replace: true });
};

const resetFilters = (tab: string) => {
    Object.keys(filterForm.value).forEach((key) => ((filterForm.value as any)[key] = ''));
    applyFilters(tab);
};

const subscriberTagsInput = ref('');
const editingSubscriber = ref<Subscriber | null>(null);
const subscriberForm = useForm({
    name: '',
    email: '',
    phone: '',
    source: 'website',
    isActive: true,
    status: 'subscribed',
    audience_type: 'patients',
    tags: [] as string[],
    country: 'Bangladesh',
});

const resetSubscriberForm = () => {
    editingSubscriber.value = null;
    subscriberTagsInput.value = '';
    subscriberForm.reset();
    subscriberForm.clearErrors();
    subscriberForm.source = 'website';
    subscriberForm.status = 'subscribed';
    subscriberForm.isActive = true;
    subscriberForm.audience_type = 'patients';
    subscriberForm.country = 'Bangladesh';
};

const editSubscriber = (subscriber: Subscriber) => {
    activeTab.value = 'subscribers';
    editingSubscriber.value = subscriber;
    subscriberForm.name = subscriber.name || '';
    subscriberForm.email = subscriber.email;
    subscriberForm.phone = subscriber.phone || '';
    subscriberForm.source = subscriber.source || 'website';
    subscriberForm.isActive = subscriber.isActive;
    subscriberForm.status = subscriber.status;
    subscriberForm.audience_type = subscriber.audience_type || '';
    subscriberForm.country = subscriber.country || '';
    subscriberForm.tags = [...(subscriber.tags || [])];
    subscriberTagsInput.value = tagText(subscriber.tags);
};

const saveSubscriber = () => {
    subscriberForm.tags = splitTags(subscriberTagsInput.value);
    if (editingSubscriber.value) {
        subscriberForm.put(route('emailmarketing.subscribers.update', editingSubscriber.value.id), { preserveScroll: true, onSuccess: resetSubscriberForm });
        return;
    }
    subscriberForm.post(route('emailmarketing.subscribers.store'), { preserveScroll: true, onSuccess: resetSubscriberForm });
};

const deleteSubscriber = (subscriber: Subscriber) => {
    if (!props.can.delete || !confirm(`Delete subscriber ${subscriber.email}?`)) return;
    router.delete(route('emailmarketing.subscribers.destroy', subscriber.id), { preserveScroll: true });
};

const importForm = useForm({ csv_file: null as File | null, default_source: 'csv-import', default_audience_type: '', default_country: 'Bangladesh' });
const onImportFile = (event: Event) => {
    importForm.csv_file = (event.target as HTMLInputElement).files?.[0] || null;
};
const importCsv = () => importForm.post(route('emailmarketing.subscribers.import'), { preserveScroll: true, forceFormData: true, onSuccess: () => importForm.reset('csv_file') });

const createBlock = (type: string): EmailBlock => {
    const common = { id: safeId(), type, data: {} as Record<string, any> };
    if (type === 'hero') common.data = { eyebrow: 'Hospital update', title: 'Your health matters, {{ first_name }}', subtitle: 'Helpful guidance from our care team.', image_url: '', button_label: 'Learn more', button_url: '{{ app_url }}' };
    if (type === 'text') common.data = { content: 'Write your email message here. Use {{ first_name }} or {{ email }} for personalization.', alignment: 'left' };
    if (type === 'image') common.data = { image_url: '', alt: '', caption: '' };
    if (type === 'button') common.data = { label: 'Book appointment', url: '{{ app_url }}', alignment: 'center' };
    if (type === 'divider') common.data = {};
    if (type === 'tips') common.data = { title: 'Helpful tips', items: 'Drink enough water\nKeep routine checkups\nContact a doctor for severe symptoms' };
    if (type === 'cta') common.data = { title: 'Need medical support?', text: 'Our team can help you choose the right doctor and appointment slot.', button_label: 'Book appointment', button_url: '{{ app_url }}/appointment', phone: '' };
    if (type === 'footer') common.data = { address: '{{ hospital_name }}\nThank you for staying connected with us.' };
    return common;
};

const starterBlocks = (category = 'custom'): EmailBlock[] => {
    if (category === 'blog_notification') {
        return [
            { ...createBlock('hero'), data: { eyebrow: 'New blog post', title: '{{ campaign_name }}', subtitle: 'Read the latest health article from our hospital team.', image_url: '', button_label: 'Read blog', button_url: '{{ app_url }}/blog' } },
            { ...createBlock('text'), data: { content: 'Hello {{ first_name }},\n\nWe published a new blog post that may help you and your family make better healthcare decisions.', alignment: 'left' } },
            createBlock('cta'),
        ];
    }
    if (category === 'health_tip') {
        return [createBlock('hero'), { ...createBlock('tips'), data: { title: 'This week’s health tips', items: 'Eat balanced meals\nWalk 20–30 minutes if your doctor allows\nSleep on time\nDo routine checkups' } }, createBlock('cta')];
    }
    if (category === 'awareness_tip') {
        return [{ ...createBlock('hero'), data: { eyebrow: 'Awareness', title: 'Prevention starts with awareness', subtitle: 'Recognize symptoms early and seek professional care when needed.', image_url: '', button_label: 'Learn more', button_url: '{{ app_url }}' } }, { ...createBlock('tips'), data: { title: 'Remember', items: 'Do screening based on age and risk\nFollow medical advice\nCall emergency support for severe symptoms' } }];
    }
    if (category === 'tips_tricks') {
        return [{ ...createBlock('hero'), data: { eyebrow: 'Tips & tricks', title: 'Make healthcare easier', subtitle: 'Simple ways to prepare for appointments and manage your care.', image_url: '', button_label: 'Visit website', button_url: '{{ app_url }}' } }, { ...createBlock('tips'), data: { title: 'Before your appointment', items: 'Bring previous reports\nWrite down symptoms and questions\nArrive early\nKeep your emergency contact updated' } }];
    }
    return [createBlock('hero'), createBlock('text'), createBlock('tips'), createBlock('cta'), createBlock('footer')];
};

const templateMode = ref<'builder' | 'html'>('builder');
const editingTemplate = ref<EmailTemplate | null>(null);
const selectedBlockIndex = ref(0);
const draggedBlockIndex = ref<number | null>(null);
const templateForm = useForm({
    name: 'Custom Email Template',
    slug: '',
    category: 'custom',
    subject: 'A health update from {{ hospital_name }}',
    preheader: 'Helpful healthcare news and tips.',
    builder_json: starterBlocks('custom') as EmailBlock[],
    html_content: '',
    text_content: '',
    status: 'active',
});

const selectedBlock = computed(() => templateForm.builder_json[selectedBlockIndex.value] || null);
const blockPalette = [
    { type: 'hero', label: 'Hero', desc: 'Header with CTA' },
    { type: 'text', label: 'Text', desc: 'Message copy' },
    { type: 'image', label: 'Image', desc: 'Full width image' },
    { type: 'button', label: 'Button', desc: 'Tracked CTA' },
    { type: 'tips', label: 'Tips List', desc: 'Health tips' },
    { type: 'cta', label: 'Care CTA', desc: 'Appointment box' },
    { type: 'divider', label: 'Divider', desc: 'Separator' },
    { type: 'footer', label: 'Footer', desc: 'Address text' },
];

const applyTemplatePreset = (category: string) => {
    templateForm.category = category;
    templateForm.builder_json = starterBlocks(category);
    selectedBlockIndex.value = 0;
    if (category !== 'custom') {
        templateForm.name = campaignTypeLabel(category);
        templateForm.subject = category === 'blog_notification' ? 'New blog post: {{ campaign_name }}' : `${campaignTypeLabel(category)} from {{ hospital_name }}`;
    }
};

const resetTemplateForm = () => {
    editingTemplate.value = null;
    templateMode.value = 'builder';
    templateForm.reset();
    templateForm.clearErrors();
    templateForm.name = 'Custom Email Template';
    templateForm.category = 'custom';
    templateForm.subject = 'A health update from {{ hospital_name }}';
    templateForm.preheader = 'Helpful healthcare news and tips.';
    templateForm.builder_json = starterBlocks('custom');
    templateForm.status = 'active';
    selectedBlockIndex.value = 0;
};

const editTemplate = (template: EmailTemplate) => {
    activeTab.value = 'templates';
    editingTemplate.value = template;
    templateMode.value = template.html_content ? 'html' : 'builder';
    templateForm.name = template.name;
    templateForm.slug = template.slug;
    templateForm.category = template.category;
    templateForm.subject = template.subject;
    templateForm.preheader = template.preheader || '';
    templateForm.builder_json = template.builder_json?.length ? JSON.parse(JSON.stringify(template.builder_json)) : starterBlocks(template.category);
    templateForm.html_content = template.html_content || '';
    templateForm.text_content = template.text_content || '';
    templateForm.status = template.status;
    selectedBlockIndex.value = 0;
};

const addBlock = (type: string) => {
    templateForm.builder_json.push(createBlock(type));
    selectedBlockIndex.value = templateForm.builder_json.length - 1;
};

const removeBlock = (index: number) => {
    templateForm.builder_json.splice(index, 1);
    selectedBlockIndex.value = Math.max(0, Math.min(selectedBlockIndex.value, templateForm.builder_json.length - 1));
};

const duplicateBlock = (index: number) => {
    const copy = JSON.parse(JSON.stringify(templateForm.builder_json[index]));
    copy.id = safeId();
    templateForm.builder_json.splice(index + 1, 0, copy);
    selectedBlockIndex.value = index + 1;
};

const onDragStart = (index: number) => { draggedBlockIndex.value = index; };
const onDropBlock = (index: number) => {
    if (draggedBlockIndex.value === null || draggedBlockIndex.value === index) return;
    const [block] = templateForm.builder_json.splice(draggedBlockIndex.value, 1);
    templateForm.builder_json.splice(index, 0, block);
    selectedBlockIndex.value = index;
    draggedBlockIndex.value = null;
};

const saveTemplate = () => {
    if (templateMode.value === 'builder') templateForm.html_content = '';
    if (editingTemplate.value) {
        templateForm.put(route('emailmarketing.templates.update', editingTemplate.value.id), { preserveScroll: true, onSuccess: resetTemplateForm });
        return;
    }
    templateForm.post(route('emailmarketing.templates.store'), { preserveScroll: true, onSuccess: resetTemplateForm });
};

const deleteTemplate = (template: EmailTemplate) => {
    if (!props.can.delete || !confirm(`Delete template "${template.name}"?`)) return;
    router.delete(route('emailmarketing.templates.destroy', template.id), { preserveScroll: true });
};
const duplicateTemplate = (template: EmailTemplate) => router.post(route('emailmarketing.templates.duplicate', template.id), {}, { preserveScroll: true });
const testTemplate = (template: EmailTemplate) => {
    const email = prompt('Send test template to email:');
    if (email) router.post(route('emailmarketing.templates.test', template.id), { email }, { preserveScroll: true });
};

const escapeHtml = (value: string) => String(value || '').replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[char] as string));
const replaceVars = (value: string) => String(value || '').replaceAll('{{ first_name }}', 'Amina').replaceAll('{{ name }}', 'Amina Rahman').replaceAll('{{ email }}', 'amina@example.com').replaceAll('{{ hospital_name }}', props.mail.from_name || 'Hospital').replaceAll('{{ campaign_name }}', 'Your campaign title').replaceAll('{{ app_url }}', window.location.origin);
const nl2br = (value: string) => escapeHtml(replaceVars(value)).replace(/\n/g, '<br>');
const previewBlock = (block: EmailBlock) => {
    const d = block.data || {};
    if (block.type === 'hero') return `<section style="margin:0 0 18px;border-radius:24px;background:linear-gradient(135deg,#0f766e,#2563eb);color:#fff;text-align:center;padding:34px 28px;"><div style="font-size:12px;text-transform:uppercase;letter-spacing:.08em;opacity:.9">${escapeHtml(replaceVars(d.eyebrow || 'Health update'))}</div><h1 style="font-size:30px;line-height:1.15;margin:14px 0 10px">${escapeHtml(replaceVars(d.title || 'Your health matters'))}</h1><p style="font-size:15px;line-height:1.7;color:#dbeafe">${nl2br(d.subtitle || '')}</p>${d.button_label ? `<a style="display:inline-block;margin-top:10px;background:#fff;color:#2563eb;padding:12px 20px;border-radius:999px;font-weight:700;text-decoration:none">${escapeHtml(replaceVars(d.button_label))}</a>` : ''}</section>`;
    if (block.type === 'text') return `<div style="padding:10px 4px;text-align:${d.alignment || 'left'};font-size:15px;line-height:1.75;color:#334155">${nl2br(d.content || '')}</div>`;
    if (block.type === 'image') return d.image_url ? `<figure style="margin:18px 0"><img src="${escapeHtml(replaceVars(d.image_url))}" style="width:100%;border-radius:18px;border:1px solid #e2e8f0"><figcaption style="font-size:12px;color:#64748b;text-align:center;margin-top:8px">${escapeHtml(replaceVars(d.caption || ''))}</figcaption></figure>` : `<div style="border:1px dashed #cbd5e1;border-radius:18px;padding:28px;text-align:center;color:#94a3b8">Image block</div>`;
    if (block.type === 'button') return `<div style="text-align:${d.alignment || 'center'};padding:14px"><a style="display:inline-block;background:#2563eb;color:#fff;padding:12px 22px;border-radius:999px;font-weight:700;text-decoration:none">${escapeHtml(replaceVars(d.label || 'Learn more'))}</a></div>`;
    if (block.type === 'divider') return '<hr style="border:0;border-top:1px solid #e2e8f0;margin:18px 0">';
    if (block.type === 'tips') return `<div style="background:#f0fdfa;border:1px solid #99f6e4;border-radius:20px;padding:22px;margin:18px 0"><h2 style="margin:0 0 12px;color:#0f766e">${escapeHtml(replaceVars(d.title || 'Helpful tips'))}</h2><ul style="margin:0;padding-left:20px;color:#334155;line-height:1.7">${String(d.items || '').split('\n').filter(Boolean).map((item) => `<li>${escapeHtml(replaceVars(item))}</li>`).join('')}</ul></div>`;
    if (block.type === 'cta') return `<div style="border:1px solid #dbeafe;border-radius:20px;padding:24px;text-align:center;box-shadow:0 10px 30px rgba(37,99,235,.08)"><h2 style="margin:0 0 8px;color:#0f172a">${escapeHtml(replaceVars(d.title || 'Need medical support?'))}</h2><p style="color:#475569;line-height:1.7">${nl2br(d.text || '')}</p><a style="display:inline-block;background:#2563eb;color:#fff;padding:12px 22px;border-radius:999px;font-weight:700;text-decoration:none">${escapeHtml(replaceVars(d.button_label || 'Book appointment'))}</a></div>`;
    if (block.type === 'footer') return `<div style="text-align:center;font-size:13px;color:#64748b;line-height:1.7;padding:18px">${nl2br(d.address || '')}</div>`;
    return '';
};

const emailPreview = computed(() => {
    const inner = templateMode.value === 'html' && templateForm.html_content ? replaceVars(templateForm.html_content) : templateForm.builder_json.map(previewBlock).join('');
    return `<!doctype html><html><body style="margin:0;background:#f1f5f9;font-family:Arial,sans-serif;padding:24px"><main style="max-width:680px;margin:auto;background:#fff;border-radius:28px;padding:24px;box-shadow:0 18px 60px rgba(15,23,42,.12)"><div style="text-align:center;font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#0f766e;font-weight:800;margin-bottom:18px">${escapeHtml(props.mail.from_name || 'Hospital')}</div>${inner}<div style="text-align:center;color:#94a3b8;font-size:12px;margin-top:24px">Unsubscribe • Visit website</div></main></body></html>`;
});

const campaignTagsInput = ref('');
const editingCampaign = ref<Campaign | null>(null);
const campaignForm = useForm({
    name: '',
    type: 'custom',
    subject: '',
    preheader: '',
    email_template_id: '' as number | string,
    recipient_filters: { audience_type: '', source: '', country: '', tags: [] as string[] },
    sender_name: props.mail.from_name || '',
    sender_email: props.mail.from_address || '',
    reply_to: '',
    scheduled_at: '',
});

const resetCampaignForm = () => {
    editingCampaign.value = null;
    campaignTagsInput.value = '';
    campaignForm.reset();
    campaignForm.clearErrors();
    campaignForm.type = 'custom';
    campaignForm.sender_name = props.mail.from_name || '';
    campaignForm.sender_email = props.mail.from_address || '';
    campaignForm.recipient_filters = { audience_type: '', source: '', country: '', tags: [] };
};

const chooseTemplateForCampaign = () => {
    const id = Number(campaignForm.email_template_id);
    const template = props.templateOptions.find((item) => item.id === id);
    if (!template) return;
    campaignForm.type = template.category;
    campaignForm.subject = template.subject;
    campaignForm.preheader = template.preheader || '';
    if (!campaignForm.name) campaignForm.name = template.name;
};

const startCampaignFromTemplate = (template: EmailTemplate | TemplateOption) => {
    activeTab.value = 'campaigns';
    resetCampaignForm();
    campaignForm.email_template_id = template.id;
    campaignForm.type = template.category;
    campaignForm.name = template.name;
    campaignForm.subject = template.subject;
    campaignForm.preheader = template.preheader || '';
};

const editCampaign = (campaign: Campaign) => {
    activeTab.value = 'campaigns';
    editingCampaign.value = campaign;
    campaignForm.name = campaign.name;
    campaignForm.type = campaign.type;
    campaignForm.subject = campaign.subject;
    campaignForm.preheader = campaign.preheader || '';
    campaignForm.email_template_id = campaign.email_template_id || '';
    campaignForm.recipient_filters = {
        audience_type: campaign.recipient_filters?.audience_type || '',
        source: campaign.recipient_filters?.source || '',
        country: campaign.recipient_filters?.country || '',
        tags: campaign.recipient_filters?.tags || [],
    };
    campaignTagsInput.value = tagText(campaignForm.recipient_filters.tags);
    campaignForm.sender_name = campaign.sender_name || props.mail.from_name || '';
    campaignForm.sender_email = campaign.sender_email || props.mail.from_address || '';
    campaignForm.reply_to = campaign.reply_to || '';
    campaignForm.scheduled_at = campaign.scheduled_at || '';
};

const saveCampaign = () => {
    campaignForm.recipient_filters.tags = splitTags(campaignTagsInput.value);
    if (editingCampaign.value) {
        campaignForm.put(route('emailmarketing.campaigns.update', editingCampaign.value.id), { preserveScroll: true, onSuccess: resetCampaignForm });
        return;
    }
    campaignForm.post(route('emailmarketing.campaigns.store'), { preserveScroll: true, onSuccess: resetCampaignForm });
};

const sendCampaign = (campaign: Campaign) => {
    if (!props.can.send || !confirm(`Send campaign "${campaign.name}" to ${campaign.total_recipients || campaign.recipients_count} recipient(s)?`)) return;
    router.post(route('emailmarketing.campaigns.send', campaign.id), {}, { preserveScroll: true });
};
const scheduleCampaign = (campaign: Campaign) => {
    const value = prompt('Schedule date/time (YYYY-MM-DDTHH:MM)', campaign.scheduled_at || '');
    if (value) router.post(route('emailmarketing.campaigns.schedule', campaign.id), { scheduled_at: value }, { preserveScroll: true });
};
const cancelCampaign = (campaign: Campaign) => router.post(route('emailmarketing.campaigns.cancel', campaign.id), {}, { preserveScroll: true });
const duplicateCampaign = (campaign: Campaign) => router.post(route('emailmarketing.campaigns.duplicate', campaign.id), {}, { preserveScroll: true });
const deleteCampaign = (campaign: Campaign) => {
    if (!props.can.delete || !confirm(`Delete campaign "${campaign.name}"?`)) return;
    router.delete(route('emailmarketing.campaigns.destroy', campaign.id), { preserveScroll: true });
};
const testCampaign = (campaign: Campaign) => {
    const email = prompt('Send test campaign to email:');
    if (email) router.post(route('emailmarketing.campaigns.test', campaign.id), { email }, { preserveScroll: true });
};

const statCards = computed(() => [
    { label: 'Active subscribers', value: props.stats.subscribers_active, detail: `${props.stats.subscribers_total} total`, icon: Users, color: 'from-emerald-500 to-teal-600' },
    { label: 'Emails sent', value: props.stats.emails_sent, detail: `${props.stats.campaigns_sent} campaigns sent`, icon: Send, color: 'from-blue-500 to-indigo-600' },
    { label: 'Open rate', value: `${props.stats.open_rate || 0}%`, detail: `${props.stats.click_rate || 0}% click rate`, icon: Eye, color: 'from-violet-500 to-fuchsia-600' },
    { label: 'Templates', value: props.stats.templates_total, detail: `${props.stats.campaigns_scheduled} scheduled`, icon: FileText, color: 'from-amber-500 to-orange-600' },
]);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Email Marketing" />

        <div class="min-h-screen bg-slate-50/70 p-4 md:p-6">
            <div class="mb-6 overflow-hidden rounded-[2rem] bg-gradient-to-br from-slate-950 via-blue-950 to-teal-900 p-6 text-white shadow-2xl shadow-blue-950/20 md:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-3xl">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-100">
                            <Sparkles class="size-3.5" /> Complete Email Marketing Suite
                        </div>
                        <h1 class="text-3xl font-black tracking-tight md:text-5xl">Campaigns, bulk email, and drag-and-drop templates</h1>
                        <p class="mt-3 text-sm leading-7 text-slate-200 md:text-base">Send blog notifications, health tips, awareness emails, tips & tricks, newsletters, and custom marketing messages with tracked opens, clicks, segments, test emails, and unsubscribe handling.</p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 lg:w-[420px]">
                        <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                            <p class="text-xs uppercase tracking-wider text-cyan-100">Mail driver</p>
                            <p class="mt-1 text-2xl font-black">{{ mail.default }}</p>
                            <p class="text-xs text-slate-300">From: {{ mail.from_address || 'not configured' }}</p>
                        </div>
                        <button class="rounded-2xl bg-white px-4 py-4 text-left font-bold text-slate-950 shadow-xl transition hover:-translate-y-0.5" @click="activeTab = 'templates'">
                            <Plus class="mb-2 size-5 text-blue-600" /> Create Template
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="flashSuccess" class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ flashSuccess }}</div>
            <div v-if="flashError" class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">{{ flashError }}</div>

            <div class="mb-6 flex flex-wrap gap-2 rounded-2xl border bg-white p-2 shadow-sm">
                <button v-for="tab in tabs" :key="tab.id" type="button" :class="['inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold transition', activeTab === tab.id ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-600 hover:bg-slate-100']" @click="activeTab = tab.id">
                    <component :is="tab.icon" class="size-4" /> {{ tab.label }}
                </button>
            </div>

            <section v-if="activeTab === 'overview'" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div v-for="card in statCards" :key="card.label" class="overflow-hidden rounded-3xl border bg-white p-5 shadow-sm">
                        <div :class="['mb-4 flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br text-white shadow-lg', card.color]"><component :is="card.icon" class="size-6" /></div>
                        <p class="text-sm font-medium text-slate-500">{{ card.label }}</p>
                        <p class="mt-1 text-3xl font-black text-slate-950">{{ card.value }}</p>
                        <p class="mt-2 text-xs text-slate-400">{{ card.detail }}</p>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-[1.15fr_.85fr]">
                    <div class="rounded-3xl border bg-white p-5 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-black text-slate-950">Recent campaigns</h2>
                                <p class="text-sm text-slate-500">Performance snapshot and quick actions</p>
                            </div>
                            <button class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white" @click="activeTab = 'campaigns'">Manage</button>
                        </div>
                        <div class="space-y-3">
                            <div v-for="campaign in campaigns.data.slice(0, 5)" :key="campaign.id" class="rounded-2xl border border-slate-100 p-4 transition hover:border-blue-200 hover:bg-blue-50/40">
                                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2"><h3 class="font-bold text-slate-950">{{ campaign.name }}</h3><span :class="['rounded-full px-2 py-0.5 text-xs font-bold ring-1', statusClass(campaign.status)]">{{ campaign.status }}</span></div>
                                        <p class="text-sm text-slate-500">{{ campaignTypeLabel(campaign.type) }} • {{ campaign.total_recipients }} recipients</p>
                                    </div>
                                    <div class="flex gap-4 text-sm font-semibold text-slate-600">
                                        <span><Eye class="mr-1 inline size-4" />{{ campaign.open_rate }}%</span>
                                        <span><MousePointerClick class="mr-1 inline size-4" />{{ campaign.click_rate }}%</span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="campaigns.data.length === 0" class="rounded-2xl border border-dashed p-8 text-center text-slate-500">No campaigns yet. Create one from the Campaigns tab.</div>
                        </div>
                    </div>
                    <div class="rounded-3xl border bg-white p-5 shadow-sm">
                        <h2 class="text-xl font-black text-slate-950">Blog notification helper</h2>
                        <p class="mb-4 text-sm text-slate-500">Use recent blog posts as campaign ideas.</p>
                        <div class="space-y-3">
                            <div v-for="blog in recentBlogs" :key="blog.id" class="rounded-2xl bg-slate-50 p-4">
                                <p class="font-bold text-slate-900">{{ blog.name }}</p>
                                <p class="text-xs text-slate-500">{{ blog.created_at }}</p>
                                <button class="mt-3 rounded-xl bg-white px-3 py-1.5 text-xs font-bold text-blue-700 ring-1 ring-blue-100" @click="activeTab = 'campaigns'; campaignForm.type = 'blog_notification'; campaignForm.name = blog.name; campaignForm.subject = `New blog post: ${blog.name}`">Create blog email</button>
                            </div>
                            <div v-if="recentBlogs.length === 0" class="rounded-2xl border border-dashed p-8 text-center text-slate-500">No blog posts found.</div>
                        </div>
                    </div>
                </div>
            </section>

            <section v-if="activeTab === 'campaigns'" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_430px]">
                <div class="space-y-4">
                    <div class="rounded-3xl border bg-white p-4 shadow-sm">
                        <div class="grid gap-3 lg:grid-cols-5">
                            <input v-model="filterForm.campaign_search" class="rounded-xl border-slate-200 text-sm lg:col-span-2" placeholder="Search campaigns..." @keyup.enter="applyFilters('campaigns')" />
                            <select v-model="filterForm.campaign_status" class="rounded-xl border-slate-200 text-sm"><option value="">All statuses</option><option value="draft">Draft</option><option value="scheduled">Scheduled</option><option value="sending">Sending</option><option value="sent">Sent</option><option value="failed">Failed</option></select>
                            <select v-model="filterForm.campaign_type" class="rounded-xl border-slate-200 text-sm"><option value="">All types</option><option v-for="(label, key) in campaignTypes" :key="key" :value="key">{{ label }}</option></select>
                            <div class="flex gap-2"><button class="flex-1 rounded-xl bg-slate-950 px-3 py-2 text-sm font-bold text-white" @click="applyFilters('campaigns')">Filter</button><button class="rounded-xl border px-3 py-2 text-sm font-bold" @click="resetFilters('campaigns')">Reset</button></div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-3xl border bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[980px] text-sm">
                                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-4">Campaign</th><th class="px-5 py-4">Audience</th><th class="px-5 py-4">Performance</th><th class="px-5 py-4">Status</th><th class="px-5 py-4 text-right">Actions</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="campaign in campaigns.data" :key="campaign.id" class="hover:bg-slate-50/70">
                                        <td class="px-5 py-4"><p class="font-black text-slate-950">{{ campaign.name }}</p><p class="text-xs text-slate-500">{{ campaignTypeLabel(campaign.type) }} • {{ campaign.template?.name || 'No template' }}</p><p class="mt-1 text-xs text-slate-400">{{ campaign.subject }}</p></td>
                                        <td class="px-5 py-4"><p class="font-bold text-slate-700">{{ campaign.total_recipients }} recipients</p><p class="text-xs text-slate-500">Sent {{ campaign.sent_count }} • Failed {{ campaign.failed_count }}</p></td>
                                        <td class="px-5 py-4"><div class="flex gap-4"><span><Eye class="mr-1 inline size-4 text-blue-600" />{{ campaign.open_rate }}%</span><span><MousePointerClick class="mr-1 inline size-4 text-teal-600" />{{ campaign.click_rate }}%</span></div></td>
                                        <td class="px-5 py-4"><span :class="['rounded-full px-2.5 py-1 text-xs font-bold ring-1', statusClass(campaign.status)]">{{ campaign.status }}</span><p v-if="campaign.scheduled_at_human" class="mt-1 text-xs text-slate-400">{{ campaign.scheduled_at_human }}</p></td>
                                        <td class="px-5 py-4"><div class="flex justify-end gap-1.5"><button class="rounded-lg border p-2 text-slate-600 hover:bg-white" @click="testCampaign(campaign)" title="Test"><Mail class="size-4" /></button><button class="rounded-lg border p-2 text-slate-600 hover:bg-white" @click="editCampaign(campaign)" title="Edit"><Edit3 class="size-4" /></button><button class="rounded-lg border p-2 text-slate-600 hover:bg-white" @click="duplicateCampaign(campaign)" title="Duplicate"><Copy class="size-4" /></button><button class="rounded-lg border p-2 text-indigo-600 hover:bg-white" @click="scheduleCampaign(campaign)" title="Schedule"><Activity class="size-4" /></button><button class="rounded-lg bg-blue-600 p-2 text-white" @click="sendCampaign(campaign)" title="Send"><Send class="size-4" /></button><button class="rounded-lg border p-2 text-amber-600 hover:bg-white" @click="cancelCampaign(campaign)" title="Cancel"><X class="size-4" /></button><button class="rounded-lg border p-2 text-rose-600 hover:bg-white" @click="deleteCampaign(campaign)" title="Delete"><Trash2 class="size-4" /></button></div></td>
                                    </tr>
                                    <tr v-if="campaigns.data.length === 0"><td colspan="5" class="px-5 py-12 text-center text-slate-500">No campaigns found.</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-3 border-t px-5 py-4 text-sm text-slate-500"><span>Showing {{ campaigns.from ?? 0 }} to {{ campaigns.to ?? 0 }} of {{ campaigns.total }}</span><div class="flex flex-wrap gap-1"><template v-for="link in campaigns.links" :key="link.label"><Link v-if="link.url" :href="link.url + (link.url.includes('?') ? '&' : '?') + 'tab=campaigns'" preserve-scroll :class="['rounded-lg border px-3 py-1.5', link.active ? 'bg-blue-600 text-white' : 'bg-white hover:bg-slate-50']" v-html="link.label" /><span v-else class="rounded-lg border px-3 py-1.5 text-slate-300" v-html="link.label" /></template></div></div>
                    </div>
                </div>

                <aside class="rounded-3xl border bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between"><div><h2 class="text-xl font-black text-slate-950">{{ editingCampaign ? 'Edit campaign' : 'Create campaign' }}</h2><p class="text-sm text-slate-500">Bulk email to selected segments.</p></div><button v-if="editingCampaign" class="rounded-xl border p-2" @click="resetCampaignForm"><X class="size-4" /></button></div>
                    <div class="space-y-3">
                        <input v-model="campaignForm.name" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Campaign name" />
                        <select v-model="campaignForm.type" class="w-full rounded-xl border-slate-200 text-sm"><option v-for="(label, key) in campaignTypes" :key="key" :value="key">{{ label }}</option></select>
                        <select v-model="campaignForm.email_template_id" class="w-full rounded-xl border-slate-200 text-sm" @change="chooseTemplateForCampaign"><option value="">Choose template</option><option v-for="template in templateOptions" :key="template.id" :value="template.id">{{ template.name }} ({{ template.category }})</option></select>
                        <input v-model="campaignForm.subject" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Subject line" />
                        <input v-model="campaignForm.preheader" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Preheader text" />
                        <div class="grid gap-2 sm:grid-cols-2"><input v-model="campaignForm.sender_name" class="rounded-xl border-slate-200 text-sm" placeholder="Sender name" /><input v-model="campaignForm.sender_email" class="rounded-xl border-slate-200 text-sm" placeholder="Sender email" /></div>
                        <input v-model="campaignForm.reply_to" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Reply-to email" />
                        <div class="rounded-2xl bg-slate-50 p-4"><p class="mb-3 text-sm font-black text-slate-800">Audience filters</p><div class="grid gap-2"><input v-model="campaignForm.recipient_filters.audience_type" class="rounded-xl border-slate-200 text-sm" placeholder="Audience e.g. patients" list="audiences" /><input v-model="campaignForm.recipient_filters.source" class="rounded-xl border-slate-200 text-sm" placeholder="Source e.g. website" list="sources" /><input v-model="campaignForm.recipient_filters.country" class="rounded-xl border-slate-200 text-sm" placeholder="Country" list="countries" /><input v-model="campaignTagsInput" class="rounded-xl border-slate-200 text-sm" placeholder="Tags comma separated" /></div></div>
                        <datalist id="audiences"><option v-for="value in segments.audienceTypes" :key="value" :value="value" /></datalist><datalist id="sources"><option v-for="value in segments.sources" :key="value" :value="value" /></datalist><datalist id="countries"><option v-for="value in segments.countries" :key="value" :value="value" /></datalist>
                        <input v-model="campaignForm.scheduled_at" type="datetime-local" class="w-full rounded-xl border-slate-200 text-sm" />
                        <button :disabled="campaignForm.processing || (!can.create && !editingCampaign)" class="w-full rounded-xl bg-blue-600 px-4 py-3 font-black text-white disabled:opacity-50" @click="saveCampaign"><Send class="mr-2 inline size-4" />{{ editingCampaign ? 'Update campaign' : 'Save campaign' }}</button>
                    </div>
                </aside>
            </section>

            <section v-if="activeTab === 'templates'" class="space-y-6">
                <div class="grid gap-6 2xl:grid-cols-[420px_minmax(0,1fr)_420px]">
                    <aside class="rounded-3xl border bg-white p-5 shadow-sm">
                        <div class="mb-4 flex items-center justify-between"><div><h2 class="text-xl font-black text-slate-950">{{ editingTemplate ? 'Edit template' : 'Template builder' }}</h2><p class="text-sm text-slate-500">Drag blocks and preview instantly.</p></div><button v-if="editingTemplate" class="rounded-xl border p-2" @click="resetTemplateForm"><X class="size-4" /></button></div>
                        <div class="space-y-3">
                            <input v-model="templateForm.name" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Template name" />
                            <input v-model="templateForm.slug" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Slug (optional)" />
                            <select v-model="templateForm.category" class="w-full rounded-xl border-slate-200 text-sm" @change="applyTemplatePreset(templateForm.category)"><option v-for="(label, key) in campaignTypes" :key="key" :value="key">{{ label }}</option></select>
                            <input v-model="templateForm.subject" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Default subject" />
                            <input v-model="templateForm.preheader" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Preheader" />
                            <select v-model="templateForm.status" class="w-full rounded-xl border-slate-200 text-sm"><option value="draft">Draft</option><option value="active">Active</option><option value="archived">Archived</option></select>
                            <div class="grid grid-cols-2 gap-2 rounded-2xl bg-slate-100 p-1"><button :class="['rounded-xl px-3 py-2 text-sm font-bold', templateMode === 'builder' ? 'bg-white shadow' : 'text-slate-500']" @click="templateMode = 'builder'">Builder</button><button :class="['rounded-xl px-3 py-2 text-sm font-bold', templateMode === 'html' ? 'bg-white shadow' : 'text-slate-500']" @click="templateMode = 'html'">Custom HTML</button></div>
                            <textarea v-if="templateMode === 'html'" v-model="templateForm.html_content" rows="11" class="w-full rounded-xl border-slate-200 font-mono text-xs" placeholder="Paste full custom HTML or an email body here."></textarea>
                            <textarea v-model="templateForm.text_content" rows="3" class="w-full rounded-xl border-slate-200 text-xs" placeholder="Optional plain text fallback"></textarea>
                            <button :disabled="templateForm.processing || (!can.create && !editingTemplate)" class="w-full rounded-xl bg-slate-950 px-4 py-3 font-black text-white disabled:opacity-50" @click="saveTemplate"><FileText class="mr-2 inline size-4" />{{ editingTemplate ? 'Update template' : 'Save template' }}</button>
                        </div>
                    </aside>

                    <div class="rounded-3xl border bg-white p-5 shadow-sm">
                        <div class="mb-4 flex items-center justify-between"><div><h2 class="text-xl font-black text-slate-950">Drag & drop blocks</h2><p class="text-sm text-slate-500">Click a block to edit its content.</p></div></div>
                        <div v-if="templateMode === 'builder'" class="grid gap-5 lg:grid-cols-[220px_minmax(0,1fr)]">
                            <div class="space-y-2"><button v-for="block in blockPalette" :key="block.type" class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-3 text-left transition hover:border-blue-200 hover:bg-blue-50" @click="addBlock(block.type)"><p class="font-black text-slate-800"><Plus class="mr-1 inline size-4" />{{ block.label }}</p><p class="text-xs text-slate-500">{{ block.desc }}</p></button></div>
                            <div class="space-y-3">
                                <div v-for="(block, index) in templateForm.builder_json" :key="block.id || index" draggable="true" :class="['rounded-2xl border p-3 transition', selectedBlockIndex === index ? 'border-blue-400 bg-blue-50 ring-4 ring-blue-100' : 'border-slate-200 bg-white']" @click="selectedBlockIndex = index" @dragstart="onDragStart(index)" @dragover.prevent @drop="onDropBlock(index)">
                                    <div class="flex items-center justify-between gap-3"><div class="flex items-center gap-2"><GripVertical class="size-4 text-slate-400" /><span class="rounded-lg bg-slate-100 px-2 py-1 text-xs font-black uppercase text-slate-600">{{ block.type }}</span><span class="text-sm font-bold text-slate-800">{{ block.data.title || block.data.label || block.data.eyebrow || 'Content block' }}</span></div><div class="flex gap-1"><button class="rounded-lg border p-1.5" @click.stop="duplicateBlock(index)"><Copy class="size-3.5" /></button><button class="rounded-lg border p-1.5 text-rose-600" @click.stop="removeBlock(index)"><Trash2 class="size-3.5" /></button></div></div>
                                </div>
                                <div v-if="templateForm.builder_json.length === 0" class="rounded-2xl border border-dashed p-8 text-center text-slate-500">Add blocks from the left palette.</div>
                            </div>
                        </div>
                        <div v-if="templateMode === 'builder' && selectedBlock" class="mt-5 rounded-2xl bg-slate-50 p-4">
                            <h3 class="mb-3 font-black text-slate-900">Edit {{ selectedBlock.type }} block</h3>
                            <div class="grid gap-3">
                                <template v-if="selectedBlock.type === 'hero'"><input v-model="selectedBlock.data.eyebrow" class="rounded-xl border-slate-200 text-sm" placeholder="Eyebrow" /><input v-model="selectedBlock.data.title" class="rounded-xl border-slate-200 text-sm" placeholder="Title" /><textarea v-model="selectedBlock.data.subtitle" rows="3" class="rounded-xl border-slate-200 text-sm" placeholder="Subtitle"></textarea><input v-model="selectedBlock.data.image_url" class="rounded-xl border-slate-200 text-sm" placeholder="Image URL" /><div class="grid gap-2 sm:grid-cols-2"><input v-model="selectedBlock.data.button_label" class="rounded-xl border-slate-200 text-sm" placeholder="Button label" /><input v-model="selectedBlock.data.button_url" class="rounded-xl border-slate-200 text-sm" placeholder="Button URL" /></div></template>
                                <template v-else-if="selectedBlock.type === 'text'"><textarea v-model="selectedBlock.data.content" rows="6" class="rounded-xl border-slate-200 text-sm" placeholder="Text content"></textarea><select v-model="selectedBlock.data.alignment" class="rounded-xl border-slate-200 text-sm"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select></template>
                                <template v-else-if="selectedBlock.type === 'image'"><input v-model="selectedBlock.data.image_url" class="rounded-xl border-slate-200 text-sm" placeholder="Image URL" /><input v-model="selectedBlock.data.alt" class="rounded-xl border-slate-200 text-sm" placeholder="Alt text" /><input v-model="selectedBlock.data.caption" class="rounded-xl border-slate-200 text-sm" placeholder="Caption" /></template>
                                <template v-else-if="selectedBlock.type === 'button'"><input v-model="selectedBlock.data.label" class="rounded-xl border-slate-200 text-sm" placeholder="Button label" /><input v-model="selectedBlock.data.url" class="rounded-xl border-slate-200 text-sm" placeholder="Button URL" /><select v-model="selectedBlock.data.alignment" class="rounded-xl border-slate-200 text-sm"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select></template>
                                <template v-else-if="selectedBlock.type === 'tips'"><input v-model="selectedBlock.data.title" class="rounded-xl border-slate-200 text-sm" placeholder="Tips title" /><textarea v-model="selectedBlock.data.items" rows="6" class="rounded-xl border-slate-200 text-sm" placeholder="One tip per line"></textarea></template>
                                <template v-else-if="selectedBlock.type === 'cta'"><input v-model="selectedBlock.data.title" class="rounded-xl border-slate-200 text-sm" placeholder="CTA title" /><textarea v-model="selectedBlock.data.text" rows="3" class="rounded-xl border-slate-200 text-sm" placeholder="CTA text"></textarea><div class="grid gap-2 sm:grid-cols-2"><input v-model="selectedBlock.data.button_label" class="rounded-xl border-slate-200 text-sm" placeholder="Button label" /><input v-model="selectedBlock.data.button_url" class="rounded-xl border-slate-200 text-sm" placeholder="Button URL" /></div><input v-model="selectedBlock.data.phone" class="rounded-xl border-slate-200 text-sm" placeholder="Phone" /></template>
                                <template v-else-if="selectedBlock.type === 'footer'"><textarea v-model="selectedBlock.data.address" rows="4" class="rounded-xl border-slate-200 text-sm" placeholder="Footer text"></textarea></template>
                                <p class="text-xs text-slate-500">Personalization variables: <code>&#123;&#123; first_name &#125;&#125;</code>, <code>&#123;&#123; email &#125;&#125;</code>, <code>&#123;&#123; hospital_name &#125;&#125;</code>, <code>&#123;&#123; app_url &#125;&#125;</code>, <code>&#123;&#123; campaign_name &#125;&#125;</code></p>
                            </div>
                        </div>
                    </div>

                    <aside class="rounded-3xl border bg-white p-5 shadow-sm">
                        <h2 class="mb-3 text-xl font-black text-slate-950">Live email preview</h2>
                        <iframe :srcdoc="emailPreview" class="h-[680px] w-full rounded-2xl border bg-slate-100"></iframe>
                    </aside>
                </div>

                <div class="rounded-3xl border bg-white p-5 shadow-sm">
                    <div class="mb-4 grid gap-3 lg:grid-cols-5"><input v-model="filterForm.template_search" class="rounded-xl border-slate-200 text-sm lg:col-span-2" placeholder="Search templates..." @keyup.enter="applyFilters('templates')" /><select v-model="filterForm.template_category" class="rounded-xl border-slate-200 text-sm"><option value="">All categories</option><option v-for="(label, key) in campaignTypes" :key="key" :value="key">{{ label }}</option></select><select v-model="filterForm.template_status" class="rounded-xl border-slate-200 text-sm"><option value="">All statuses</option><option value="draft">Draft</option><option value="active">Active</option><option value="archived">Archived</option></select><div class="flex gap-2"><button class="flex-1 rounded-xl bg-slate-950 px-3 py-2 text-sm font-bold text-white" @click="applyFilters('templates')">Filter</button><button class="rounded-xl border px-3 py-2 text-sm font-bold" @click="resetFilters('templates')">Reset</button></div></div>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <div v-for="template in templates.data" :key="template.id" class="rounded-2xl border border-slate-200 p-4 transition hover:border-blue-200 hover:shadow-lg"><div class="mb-3 flex items-start justify-between gap-3"><div><span :class="['rounded-full px-2 py-0.5 text-xs font-bold ring-1', statusClass(template.status)]">{{ template.status }}</span><h3 class="mt-2 font-black text-slate-950">{{ template.name }}</h3><p class="text-xs text-slate-500">{{ campaignTypeLabel(template.category) }} • used {{ template.campaigns_count }} times</p></div><FileText class="size-8 text-blue-500" /></div><p class="line-clamp-2 text-sm text-slate-600">{{ template.subject }}</p><div class="mt-4 flex flex-wrap gap-2"><button class="rounded-lg border px-2.5 py-1.5 text-xs font-bold" @click="startCampaignFromTemplate(template)">Campaign</button><button class="rounded-lg border px-2.5 py-1.5 text-xs font-bold" @click="testTemplate(template)">Test</button><button class="rounded-lg border px-2.5 py-1.5 text-xs font-bold" @click="editTemplate(template)">Edit</button><button class="rounded-lg border px-2.5 py-1.5 text-xs font-bold" @click="duplicateTemplate(template)">Copy</button><button class="rounded-lg border px-2.5 py-1.5 text-xs font-bold text-rose-600" @click="deleteTemplate(template)">Delete</button></div></div>
                        <div v-if="templates.data.length === 0" class="rounded-2xl border border-dashed p-8 text-center text-slate-500 md:col-span-2 xl:col-span-3">No templates found.</div>
                    </div>
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t pt-4 text-sm text-slate-500"><span>Showing {{ templates.from ?? 0 }} to {{ templates.to ?? 0 }} of {{ templates.total }}</span><div class="flex flex-wrap gap-1"><template v-for="link in templates.links" :key="link.label"><Link v-if="link.url" :href="link.url + (link.url.includes('?') ? '&' : '?') + 'tab=templates'" preserve-scroll :class="['rounded-lg border px-3 py-1.5', link.active ? 'bg-blue-600 text-white' : 'bg-white hover:bg-slate-50']" v-html="link.label" /><span v-else class="rounded-lg border px-3 py-1.5 text-slate-300" v-html="link.label" /></template></div></div>
                </div>
            </section>

            <section v-if="activeTab === 'subscribers'" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
                <div class="space-y-4">
                    <div class="rounded-3xl border bg-white p-4 shadow-sm"><div class="grid gap-3 lg:grid-cols-6"><input v-model="filterForm.subscriber_search" class="rounded-xl border-slate-200 text-sm lg:col-span-2" placeholder="Search subscribers..." @keyup.enter="applyFilters('subscribers')" /><select v-model="filterForm.subscriber_status" class="rounded-xl border-slate-200 text-sm"><option value="">All statuses</option><option value="subscribed">Subscribed</option><option value="unsubscribed">Unsubscribed</option><option value="bounced">Bounced</option></select><input v-model="filterForm.subscriber_audience_type" class="rounded-xl border-slate-200 text-sm" placeholder="Audience" list="audiences" /><input v-model="filterForm.subscriber_source" class="rounded-xl border-slate-200 text-sm" placeholder="Source" list="sources" /><div class="flex gap-2"><button class="flex-1 rounded-xl bg-slate-950 px-3 py-2 text-sm font-bold text-white" @click="applyFilters('subscribers')">Filter</button><button class="rounded-xl border px-3 py-2 text-sm font-bold" @click="resetFilters('subscribers')">Reset</button></div></div></div>
                    <div class="overflow-hidden rounded-3xl border bg-white shadow-sm"><div class="overflow-x-auto"><table class="w-full min-w-[860px] text-sm"><thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-4">Subscriber</th><th class="px-5 py-4">Segment</th><th class="px-5 py-4">Source</th><th class="px-5 py-4">Status</th><th class="px-5 py-4 text-right">Actions</th></tr></thead><tbody class="divide-y divide-slate-100"><tr v-for="subscriber in subscribers.data" :key="subscriber.id" class="hover:bg-slate-50/70"><td class="px-5 py-4"><p class="font-black text-slate-950">{{ subscriber.name || 'Subscriber' }}</p><p class="text-xs text-slate-500">{{ subscriber.email }} <span v-if="subscriber.phone">• {{ subscriber.phone }}</span></p></td><td class="px-5 py-4"><p class="font-bold text-slate-700">{{ subscriber.audience_type || 'General' }}</p><p class="text-xs text-slate-500">{{ tagText(subscriber.tags) || 'No tags' }}</p></td><td class="px-5 py-4"><p>{{ subscriber.source || 'website' }}</p><p class="text-xs text-slate-500">{{ subscriber.country || '—' }}</p></td><td class="px-5 py-4"><span :class="['rounded-full px-2.5 py-1 text-xs font-bold ring-1', statusClass(subscriber.status)]">{{ subscriber.status }}</span><p class="mt-1 text-xs text-slate-400">{{ subscriber.subscribed_at }}</p></td><td class="px-5 py-4"><div class="flex justify-end gap-2"><button class="rounded-lg border p-2" @click="editSubscriber(subscriber)"><Edit3 class="size-4" /></button><button class="rounded-lg border p-2 text-rose-600" @click="deleteSubscriber(subscriber)"><Trash2 class="size-4" /></button></div></td></tr><tr v-if="subscribers.data.length === 0"><td colspan="5" class="px-5 py-12 text-center text-slate-500">No subscribers found.</td></tr></tbody></table></div><div class="flex flex-wrap items-center justify-between gap-3 border-t px-5 py-4 text-sm text-slate-500"><span>Showing {{ subscribers.from ?? 0 }} to {{ subscribers.to ?? 0 }} of {{ subscribers.total }}</span><div class="flex flex-wrap gap-1"><template v-for="link in subscribers.links" :key="link.label"><Link v-if="link.url" :href="link.url + (link.url.includes('?') ? '&' : '?') + 'tab=subscribers'" preserve-scroll :class="['rounded-lg border px-3 py-1.5', link.active ? 'bg-blue-600 text-white' : 'bg-white hover:bg-slate-50']" v-html="link.label" /><span v-else class="rounded-lg border px-3 py-1.5 text-slate-300" v-html="link.label" /></template></div></div></div>
                </div>
                <aside class="space-y-4">
                    <div class="rounded-3xl border bg-white p-5 shadow-sm"><div class="mb-4 flex items-center justify-between"><div><h2 class="text-xl font-black text-slate-950">{{ editingSubscriber ? 'Edit subscriber' : 'Add subscriber' }}</h2><p class="text-sm text-slate-500">Manage marketing recipients.</p></div><button v-if="editingSubscriber" class="rounded-xl border p-2" @click="resetSubscriberForm"><X class="size-4" /></button></div><div class="space-y-3"><input v-model="subscriberForm.name" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Name" /><input v-model="subscriberForm.email" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Email" /><input v-model="subscriberForm.phone" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Phone" /><div class="grid gap-2 sm:grid-cols-2"><input v-model="subscriberForm.audience_type" class="rounded-xl border-slate-200 text-sm" placeholder="Audience" /><input v-model="subscriberForm.source" class="rounded-xl border-slate-200 text-sm" placeholder="Source" /></div><input v-model="subscriberForm.country" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Country" /><input v-model="subscriberTagsInput" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Tags comma separated" /><div class="grid gap-2 sm:grid-cols-2"><select v-model="subscriberForm.status" class="rounded-xl border-slate-200 text-sm"><option value="subscribed">Subscribed</option><option value="unsubscribed">Unsubscribed</option><option value="bounced">Bounced</option><option value="complained">Complained</option></select><label class="flex items-center gap-2 rounded-xl border px-3 py-2 text-sm"><input v-model="subscriberForm.isActive" type="checkbox" /> Active</label></div><button :disabled="subscriberForm.processing || (!can.create && !editingSubscriber)" class="w-full rounded-xl bg-blue-600 px-4 py-3 font-black text-white disabled:opacity-50" @click="saveSubscriber"><Users class="mr-2 inline size-4" />{{ editingSubscriber ? 'Update subscriber' : 'Add subscriber' }}</button></div></div>
                    <div class="rounded-3xl border bg-white p-5 shadow-sm"><h2 class="mb-1 text-xl font-black text-slate-950">CSV import</h2><p class="mb-4 text-sm text-slate-500">Columns: email, name, phone, audience_type, source, country, tags</p><div class="space-y-3"><input type="file" accept=".csv,.txt" class="w-full rounded-xl border border-slate-200 p-2 text-sm" @input="onImportFile" /><input v-model="importForm.default_source" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Default source" /><input v-model="importForm.default_audience_type" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Default audience" /><input v-model="importForm.default_country" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Default country" /><button :disabled="importForm.processing" class="w-full rounded-xl bg-slate-950 px-4 py-3 font-black text-white disabled:opacity-50" @click="importCsv"><Upload class="mr-2 inline size-4" />Import CSV</button></div></div>
                </aside>
            </section>
        </div>
    </AppLayout>
</template>

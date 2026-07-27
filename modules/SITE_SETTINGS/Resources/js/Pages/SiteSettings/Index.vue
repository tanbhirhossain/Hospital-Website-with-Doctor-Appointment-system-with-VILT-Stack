<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import {
  Phone, Image, Info, MessageSquare, MapPin, Layout, Video,
  Award, HeartPulse, Handshake, Save, Upload, ChevronRight
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Site Settings', href: '/admin/site-settings' }];
const page = usePage();
const flash = computed(() => (page.props as any).flash);

const props = defineProps<{
  settings: Record<string, any>;
}>();

const tabs = [
  { key: 'topbar', label: 'Topbar Info', icon: Phone },
  { key: 'logo', label: 'Logo', icon: Image },
  { key: 'about', label: 'About Us Page', icon: Info },
  { key: 'contact_page', label: 'Contact Us Page', icon: MessageSquare },
  { key: 'contact_info', label: 'Contact Info', icon: MapPin },
  { key: 'footer', label: 'Footer', icon: Layout },
  { key: 'home_about', label: 'Home About', icon: Video },
  { key: 'why_choose', label: 'Why Choose Us', icon: Award },
  { key: 'services', label: 'Services Section', icon: HeartPulse },
  { key: 'partners', label: 'Partners Section', icon: Handshake },
];
const activeTab = ref('topbar');

// ── Forms ──
const topbarForm = useForm({ topbar_phone: props.settings.topbar_phone || '', topbar_email: props.settings.topbar_email || '', topbar_notice: props.settings.topbar_notice || '', topbar_emergency: props.settings.topbar_emergency || '' });
const logoForm = useForm({ logo: null as File | null, favicon: null as File | null, footer_logo: null as File | null });
const aboutPageForm = useForm({ about_title: props.settings.about_title || '', about_content: props.settings.about_content || '', about_video_url: props.settings.about_video_url || '', about_video_title: props.settings.about_video_title || '', about_image: null as File | null });
const contactPageForm = useForm({ contact_title: props.settings.contact_title || '', contact_content: props.settings.contact_content || '', contact_map_embed: props.settings.contact_map_embed || '' });
const contactInfoForm = useForm({ contact_phone_primary: props.settings.contact_phone_primary || '', contact_phone_secondary: props.settings.contact_phone_secondary || '', contact_email_primary: props.settings.contact_email_primary || '', contact_email_secondary: props.settings.contact_email_secondary || '', contact_hotline: props.settings.contact_hotline || '', contact_address: props.settings.contact_address || '', contact_city: props.settings.contact_city || '' });
const footerForm = useForm({ footer_description: props.settings.footer_description || '', footer_phone: props.settings.footer_phone || '', footer_email: props.settings.footer_email || '', footer_address: props.settings.footer_address || '', footer_facebook: props.settings.footer_facebook || '', footer_twitter: props.settings.footer_twitter || '', footer_linkedin: props.settings.footer_linkedin || '', footer_youtube: props.settings.footer_youtube || '', footer_instagram: props.settings.footer_instagram || '', footer_copyright: props.settings.footer_copyright || '' });
const homeAboutForm = useForm({ home_about_title: props.settings.home_about_title || '', home_about_content: props.settings.home_about_content || '', home_about_video_url: props.settings.home_about_video_url || '', home_about_image: null as File | null });
const whyChooseForm = useForm({ why_choose_title: props.settings.why_choose_title || '', why_choose_subtitle: props.settings.why_choose_subtitle || '' });
const servicesForm = useForm({ services_title: props.settings.services_title || '', services_subtitle: props.settings.services_subtitle || '' });
const partnersForm = useForm({ partners_title: props.settings.partners_title || '', partners_subtitle: props.settings.partners_subtitle || '' });

// ── Previews ──
const logoPreview = ref(props.settings.logo_url || null);
const faviconPreview = ref(props.settings.favicon_url || null);
const footerLogoPreview = ref(props.settings.footer_logo_url || null);
const aboutImagePreview = ref(props.settings.about_image_url || null);
const homeAboutImagePreview = ref(props.settings.home_about_image_url || null);

function handleFile(e: Event, previewRef: any, form: any, field: string) {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (file) { form[field] = file; previewRef.value = URL.createObjectURL(file); }
}

function submitTopbar() { topbarForm.put('/admin/site-settings/topbar'); }
function submitLogo() { logoForm.post('/admin/site-settings/logo', { preserveScroll: true, forceFormData: true, data: { _method: 'put' } }); }
function submitAboutPage() { aboutPageForm.put('/admin/site-settings/about-page', { preserveScroll: true }); }
function submitContactPage() { contactPageForm.put('/admin/site-settings/contact-page'); }
function submitContactInfo() { contactInfoForm.put('/admin/site-settings/contact-info'); }
function submitFooter() { footerForm.put('/admin/site-settings/footer'); }
function submitHomeAbout() { homeAboutForm.put('/admin/site-settings/home-about', { preserveScroll: true }); }
function submitWhyChoose() { whyChooseForm.put('/admin/site-settings/why-choose-section'); }
function submitServices() { servicesForm.put('/admin/site-settings/services-section'); }
function submitPartners() { partnersForm.put('/admin/site-settings/partners-section'); }
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Site Settings" />
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-md">
            <Info class="h-5 w-5 text-white" />
          </div>
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Site Settings</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Manage topbar, logo, footer, about, contact & homepage sections</p>
          </div>
        </div>
      </div>

      <!-- Flash -->
      <div v-if="flash?.success" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400">{{ flash.success }}</div>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        <!-- Tab Sidebar -->
        <div class="lg:col-span-1">
          <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 p-2">
            <nav class="space-y-0.5">
              <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key"
                :class="['flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-sm font-medium transition-colors text-left',
                  activeTab === tab.key ? 'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white']">
                <component :is="tab.icon" class="h-4 w-4 shrink-0" />
                {{ tab.label }}
              </button>
            </nav>
          </div>
        </div>

        <!-- Content Panel -->
        <div class="lg:col-span-3">
          <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
              <h2 class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                <component :is="tabs.find(t => t.key === activeTab)?.icon" class="h-4 w-4 text-violet-500" />
                {{ tabs.find(t => t.key === activeTab)?.label }}
              </h2>
            </div>

            <div class="p-5">
              <!-- ═══ TOPBAR ═══ -->
              <form v-if="activeTab === 'topbar'" @submit.prevent="submitTopbar" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div><Label>Phone Number</Label><Input v-model="topbarForm.topbar_phone" placeholder="+880 184 733 1047" class="mt-1" /></div>
                  <div><Label>Email</Label><Input v-model="topbarForm.topbar_email" placeholder="info@amzhospitalbd.com" class="mt-1" /></div>
                </div>
                <div><Label>Notice / Scrolling Text</Label><Input v-model="topbarForm.topbar_notice" placeholder="Important notice..." class="mt-1" /></div>
                <div><Label>Emergency Number</Label><Input v-model="topbarForm.topbar_emergency" placeholder="10699" class="mt-1" /></div>
                <Button type="submit" :disabled="topbarForm.processing" class="gap-2 bg-violet-600 hover:bg-violet-700"><Save class="h-4 w-4" /> Save Topbar</Button>
              </form>

              <!-- ═══ LOGO ═══ -->
              <form v-if="activeTab === 'logo'" @submit.prevent="submitLogo" class="space-y-5">
                <div v-for="(item, key) in [{label:'Main Logo',ref:logoPreview,field:'logo'},{label:'Favicon',ref:faviconPreview,field:'favicon'},{label:'Footer Logo',ref:footerLogoPreview,field:'footer_logo'}]" :key="key">
                  <Label>{{ item.label }}</Label>
                  <div class="mt-1 flex items-center gap-4">
                    <div v-if="item.ref.value" class="h-16 w-24 overflow-hidden rounded-lg border border-gray-200 bg-gray-50"><img :src="item.ref.value" class="h-full w-full object-contain" /></div>
                    <div v-else class="flex h-16 w-24 items-center justify-center rounded-lg border-2 border-dashed border-gray-200 text-xs text-gray-400">No image</div>
                    <input type="file" accept="image/*" @change="handleFile($event, item.ref, logoForm, item.field)" class="text-sm text-gray-500" />
                  </div>
                </div>
                <Button type="submit" :disabled="logoForm.processing" class="gap-2 bg-violet-600 hover:bg-violet-700"><Upload class="h-4 w-4" /> Upload Logos</Button>
              </form>

              <!-- ═══ ABOUT PAGE ═══ -->
              <form v-if="activeTab === 'about'" @submit.prevent="submitAboutPage" class="space-y-4">
                <div><Label>Page Title</Label><Input v-model="aboutPageForm.about_title" placeholder="About AMZ Hospital" class="mt-1" /></div>
                <div><Label>Content</Label><textarea v-model="aboutPageForm.about_content" rows="6" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-violet-400 focus:ring-2 focus:ring-violet-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white" /></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div><Label>Video URL</Label><Input v-model="aboutPageForm.about_video_url" placeholder="https://youtube.com/watch?v=..." class="mt-1" /></div>
                  <div><Label>Video Title</Label><Input v-model="aboutPageForm.about_video_title" placeholder="Our Story" class="mt-1" /></div>
                </div>
                <div>
                  <Label>Page Image</Label>
                  <div class="mt-1 flex items-center gap-4">
                    <div v-if="aboutImagePreview" class="h-24 w-40 overflow-hidden rounded-lg border border-gray-200"><img :src="aboutImagePreview" class="h-full w-full object-cover" /></div>
                    <div v-else class="flex h-24 w-40 items-center justify-center rounded-lg border-2 border-dashed border-gray-200 text-xs text-gray-400">No image</div>
                    <input type="file" accept="image/*" @change="handleFile($event, aboutImagePreview, aboutPageForm, 'about_image')" class="text-sm text-gray-500" />
                  </div>
                </div>
                <Button type="submit" :disabled="aboutPageForm.processing" class="gap-2 bg-violet-600 hover:bg-violet-700"><Save class="h-4 w-4" /> Save About Page</Button>
              </form>

              <!-- ═══ CONTACT PAGE ═══ -->
              <form v-if="activeTab === 'contact_page'" @submit.prevent="submitContactPage" class="space-y-4">
                <div><Label>Page Title</Label><Input v-model="contactPageForm.contact_title" placeholder="Get in Touch" class="mt-1" /></div>
                <div><Label>Content</Label><textarea v-model="contactPageForm.contact_content" rows="5" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-violet-400 focus:ring-2 focus:ring-violet-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white" /></div>
                <div><Label>Google Maps Embed Code</Label><textarea v-model="contactPageForm.contact_map_embed" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm font-mono shadow-sm focus:border-violet-400 focus:ring-2 focus:ring-violet-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white" /></div>
                <Button type="submit" :disabled="contactPageForm.processing" class="gap-2 bg-violet-600 hover:bg-violet-700"><Save class="h-4 w-4" /> Save Contact Page</Button>
              </form>

              <!-- ═══ CONTACT INFO ═══ -->
              <form v-if="activeTab === 'contact_info'" @submit.prevent="submitContactInfo" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div><Label>Primary Phone</Label><Input v-model="contactInfoForm.contact_phone_primary" class="mt-1" /></div>
                  <div><Label>Secondary Phone</Label><Input v-model="contactInfoForm.contact_phone_secondary" class="mt-1" /></div>
                  <div><Label>Primary Email</Label><Input v-model="contactInfoForm.contact_email_primary" class="mt-1" /></div>
                  <div><Label>Secondary Email</Label><Input v-model="contactInfoForm.contact_email_secondary" class="mt-1" /></div>
                  <div><Label>Hotline</Label><Input v-model="contactInfoForm.contact_hotline" class="mt-1" /></div>
                  <div><Label>City</Label><Input v-model="contactInfoForm.contact_city" class="mt-1" /></div>
                </div>
                <div><Label>Full Address</Label><textarea v-model="contactInfoForm.contact_address" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-violet-400 focus:ring-2 focus:ring-violet-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white" /></div>
                <Button type="submit" :disabled="contactInfoForm.processing" class="gap-2 bg-violet-600 hover:bg-violet-700"><Save class="h-4 w-4" /> Save Contact Info</Button>
              </form>

              <!-- ═══ FOOTER ═══ -->
              <form v-if="activeTab === 'footer'" @submit.prevent="submitFooter" class="space-y-4">
                <div><Label>Footer Description</Label><textarea v-model="footerForm.footer_description" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-violet-400 focus:ring-2 focus:ring-violet-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white" /></div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div><Label>Phone</Label><Input v-model="footerForm.footer_phone" class="mt-1" /></div>
                  <div><Label>Email</Label><Input v-model="footerForm.footer_email" class="mt-1" /></div>
                  <div><Label>Address</Label><Input v-model="footerForm.footer_address" class="mt-1" /></div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                  <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Social Media Links</p>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div><Label>Facebook</Label><Input v-model="footerForm.footer_facebook" placeholder="https://facebook.com/..." class="mt-1" /></div>
                    <div><Label>Twitter / X</Label><Input v-model="footerForm.footer_twitter" placeholder="https://twitter.com/..." class="mt-1" /></div>
                    <div><Label>LinkedIn</Label><Input v-model="footerForm.footer_linkedin" placeholder="https://linkedin.com/..." class="mt-1" /></div>
                    <div><Label>YouTube</Label><Input v-model="footerForm.footer_youtube" placeholder="https://youtube.com/..." class="mt-1" /></div>
                    <div><Label>Instagram</Label><Input v-model="footerForm.footer_instagram" placeholder="https://instagram.com/..." class="mt-1" /></div>
                  </div>
                </div>
                <div><Label>Copyright Text</Label><Input v-model="footerForm.footer_copyright" placeholder="© 2026 AMZ Hospital Ltd." class="mt-1" /></div>
                <Button type="submit" :disabled="footerForm.processing" class="gap-2 bg-violet-600 hover:bg-violet-700"><Save class="h-4 w-4" /> Save Footer</Button>
              </form>

              <!-- ═══ HOME ABOUT ═══ -->
              <form v-if="activeTab === 'home_about'" @submit.prevent="submitHomeAbout" class="space-y-4">
                <div><Label>Section Title</Label><Input v-model="homeAboutForm.home_about_title" class="mt-1" /></div>
                <div><Label>Content</Label><textarea v-model="homeAboutForm.home_about_content" rows="5" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-violet-400 focus:ring-2 focus:ring-violet-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white" /></div>
                <div><Label>Video URL</Label><Input v-model="homeAboutForm.home_about_video_url" placeholder="https://youtube.com/watch?v=..." class="mt-1" /></div>
                <div>
                  <Label>Section Image</Label>
                  <div class="mt-1 flex items-center gap-4">
                    <div v-if="homeAboutImagePreview" class="h-24 w-40 overflow-hidden rounded-lg border border-gray-200"><img :src="homeAboutImagePreview" class="h-full w-full object-cover" /></div>
                    <div v-else class="flex h-24 w-40 items-center justify-center rounded-lg border-2 border-dashed border-gray-200 text-xs text-gray-400">No image</div>
                    <input type="file" accept="image/*" @change="handleFile($event, homeAboutImagePreview, homeAboutForm, 'home_about_image')" class="text-sm text-gray-500" />
                  </div>
                </div>
                <Button type="submit" :disabled="homeAboutForm.processing" class="gap-2 bg-violet-600 hover:bg-violet-700"><Save class="h-4 w-4" /> Save</Button>
              </form>

              <!-- ═══ WHY CHOOSE US ═══ -->
              <form v-if="activeTab === 'why_choose'" @submit.prevent="submitWhyChoose" class="space-y-4">
                <div><Label>Section Title</Label><Input v-model="whyChooseForm.why_choose_title" class="mt-1" /></div>
                <div><Label>Subtitle</Label><Input v-model="whyChooseForm.why_choose_subtitle" class="mt-1" /></div>
                <p class="text-xs text-gray-500">Manage individual feature items from the <a href="/admin/why-choose-us" class="text-violet-600 hover:underline font-medium">Why Choose Us panel →</a></p>
                <Button type="submit" :disabled="whyChooseForm.processing" class="gap-2 bg-violet-600 hover:bg-violet-700"><Save class="h-4 w-4" /> Save</Button>
              </form>

              <!-- ═══ SERVICES ═══ -->
              <form v-if="activeTab === 'services'" @submit.prevent="submitServices" class="space-y-4">
                <div><Label>Section Title</Label><Input v-model="servicesForm.services_title" class="mt-1" /></div>
                <div><Label>Subtitle</Label><Input v-model="servicesForm.services_subtitle" class="mt-1" /></div>
                <Button type="submit" :disabled="servicesForm.processing" class="gap-2 bg-violet-600 hover:bg-violet-700"><Save class="h-4 w-4" /> Save</Button>
              </form>

              <!-- ═══ PARTNERS ═══ -->
              <form v-if="activeTab === 'partners'" @submit.prevent="submitPartners" class="space-y-4">
                <div><Label>Section Title</Label><Input v-model="partnersForm.partners_title" class="mt-1" /></div>
                <div><Label>Subtitle</Label><Input v-model="partnersForm.partners_subtitle" class="mt-1" /></div>
                <p class="text-xs text-gray-500">Manage partner logos from the <a href="/admin/corporate-partners" class="text-violet-600 hover:underline font-medium">Corporate Partners panel →</a></p>
                <Button type="submit" :disabled="partnersForm.processing" class="gap-2 bg-violet-600 hover:bg-violet-700"><Save class="h-4 w-4" /> Save</Button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

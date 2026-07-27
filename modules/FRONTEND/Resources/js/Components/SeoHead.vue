<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type BreadcrumbEntry = {
    name: string;
    url?: string;
};

type StructuredData = Record<string, unknown> | Record<string, unknown>[];

const DEFAULT_SITE_NAME = 'AMZ Hospital';
const DEFAULT_TITLE = 'AMZ Hospital | Trusted Healthcare & Doctor Appointments in Dhaka';
const DEFAULT_DESCRIPTION =
    'AMZ Hospital in Dhaka provides specialist doctors, departments, centers of excellence, diagnostics, emergency care, and easy online doctor appointment booking.';
const DEFAULT_KEYWORDS = [
    'AMZ Hospital',
    'hospital in Dhaka',
    'doctor appointment Dhaka',
    'Bangladesh hospital',
    'specialist doctors',
    'medical services',
    'healthcare Bangladesh',
    'emergency hospital',
    'diagnostic services',
    'online doctor booking',
];
const DEFAULT_IMAGE = 'https://amzhospitalbd.com/storage/AMZ.jpg';
const DEFAULT_LOCALE = 'en_US';
const DEFAULT_TWITTER_SITE = '@amzhospitalbd';
const DEFAULT_ADDRESS = 'Cha-80/3, Shadhinota Sarani, Progati Sarani Road, Uttar Badda, Dhaka-1212, Bangladesh';
const DEFAULT_PHONE = '+8801847331047';
const DEFAULT_EMAIL = 'info@amzhospitalbd.com';

const props = withDefaults(
    defineProps<{
        title?: string;
        description?: string;
        keywords?: string[] | string;
        canonical?: string;
        type?: string;
        image?: string | null;
        imageAlt?: string;
        imageWidth?: number | string;
        imageHeight?: number | string;
        siteName?: string;
        locale?: string;
        author?: string;
        publisher?: string;
        robots?: string;
        noindex?: boolean;
        nofollow?: boolean;
        publishedTime?: string | null;
        modifiedTime?: string | null;
        expirationTime?: string | null;
        section?: string;
        tags?: string[];
        twitterCard?: 'summary' | 'summary_large_image' | 'app' | 'player';
        twitterSite?: string;
        twitterCreator?: string;
        schemaType?: string;
        breadcrumbs?: BreadcrumbEntry[];
        structuredData?: StructuredData;
    }>(),
    {
        title: DEFAULT_TITLE,
        description: DEFAULT_DESCRIPTION,
        type: 'website',
        image: DEFAULT_IMAGE,
        imageAlt: DEFAULT_SITE_NAME,
        imageWidth: 1200,
        imageHeight: 630,
        siteName: DEFAULT_SITE_NAME,
        locale: DEFAULT_LOCALE,
        author: DEFAULT_SITE_NAME,
        publisher: DEFAULT_SITE_NAME,
        noindex: false,
        nofollow: false,
        twitterCard: 'summary_large_image',
        twitterSite: DEFAULT_TWITTER_SITE,
        twitterCreator: DEFAULT_TWITTER_SITE,
        schemaType: 'MedicalWebPage',
    },
);

const page = usePage();

const stripHtml = (value?: string | null): string => {
    if (!value) {
        return '';
    }

    return String(value)
        .replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi, ' ')
        .replace(/<style[\s\S]*?>[\s\S]*?<\/style>/gi, ' ')
        .replace(/<[^>]+>/g, ' ')
        .replace(/&nbsp;/gi, ' ')
        .replace(/&amp;/gi, '&')
        .replace(/&quot;/gi, '"')
        .replace(/&#039;/gi, "'")
        .replace(/\s+/g, ' ')
        .trim();
};

const truncate = (value: string, limit = 160): string => {
    if (value.length <= limit) {
        return value;
    }

    return `${value.slice(0, limit - 1).replace(/[\s,.;:!?-]+$/g, '')}…`;
};

const normalizeKeywords = (keywords?: string[] | string): string => {
    if (Array.isArray(keywords)) {
        return keywords.filter(Boolean).join(', ');
    }

    return keywords || DEFAULT_KEYWORDS.join(', ');
};

const currentUrl = computed(() => {
    const ziggy = (page.props as Record<string, any>)?.ziggy;

    return ziggy?.location || (typeof window !== 'undefined' ? window.location.href : 'https://amzhospitalbd.com/');
});

const baseUrl = computed(() => {
    const ziggy = (page.props as Record<string, any>)?.ziggy;
    const candidate = ziggy?.url || currentUrl.value || 'https://amzhospitalbd.com/';

    try {
        return new URL(candidate).origin;
    } catch {
        return 'https://amzhospitalbd.com';
    }
});

const absoluteUrl = (value?: string | null): string => {
    if (!value) {
        return '';
    }

    if (/^https?:\/\//i.test(value)) {
        return value;
    }

    return new URL(value.startsWith('/') ? value : `/${value}`, baseUrl.value).toString();
};

const canonicalUrl = computed(() => absoluteUrl(props.canonical || currentUrl.value));
const imageUrl = computed(() => absoluteUrl(props.image || DEFAULT_IMAGE));
const imageMimeType = computed(() => {
    const path = imageUrl.value.split('?')[0].toLowerCase();

    if (path.endsWith('.png')) return 'image/png';
    if (path.endsWith('.webp')) return 'image/webp';
    if (path.endsWith('.gif')) return 'image/gif';
    if (path.endsWith('.svg')) return 'image/svg+xml';

    return 'image/jpeg';
});
const cleanTitle = computed(() => stripHtml(props.title) || DEFAULT_TITLE);
const browserTitle = computed(() => (cleanTitle.value.includes(props.siteName) ? cleanTitle.value : `${cleanTitle.value} | ${props.siteName}`));
const metaDescription = computed(() => truncate(stripHtml(props.description) || DEFAULT_DESCRIPTION, 160));
const metaKeywords = computed(() => normalizeKeywords(props.keywords));
const robotsContent = computed(() => {
    if (props.robots) {
        return props.robots;
    }

    return [
        props.noindex ? 'noindex' : 'index',
        props.nofollow ? 'nofollow' : 'follow',
        'max-snippet:-1',
        'max-image-preview:large',
        'max-video-preview:-1',
    ].join(', ');
});
const articleTags = computed(() => props.tags?.filter(Boolean) || []);

const defaultStructuredData = computed(() => {
    const organization = {
        '@type': 'Hospital',
        '@id': `${baseUrl.value}/#organization`,
        name: props.siteName,
        alternateName: 'AMZ Hospital Ltd.',
        url: baseUrl.value,
        logo: absoluteUrl('/logo.svg'),
        image: imageUrl.value,
        telephone: DEFAULT_PHONE,
        email: DEFAULT_EMAIL,
        address: {
            '@type': 'PostalAddress',
            streetAddress: 'Cha-80/3, Shadhinota Sarani, Progati Sarani Road, Uttar Badda',
            addressLocality: 'Dhaka',
            postalCode: '1212',
            addressCountry: 'BD',
        },
        geo: {
            '@type': 'GeoCoordinates',
            latitude: 23.7806,
            longitude: 90.4257,
        },
        areaServed: ['Dhaka', 'Bangladesh'],
        medicalSpecialty: ['Emergency', 'Cardiology', 'Medicine', 'Surgery', 'Diagnostics', 'Women Health'],
        openingHoursSpecification: [
            {
                '@type': 'OpeningHoursSpecification',
                dayOfWeek: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                opens: '00:00',
                closes: '23:59',
            },
        ],
        contactPoint: [
            {
                '@type': 'ContactPoint',
                telephone: DEFAULT_PHONE,
                contactType: 'customer support',
                areaServed: 'BD',
                availableLanguage: ['English', 'Bengali'],
            },
            {
                '@type': 'ContactPoint',
                telephone: '10699',
                contactType: 'emergency',
                areaServed: 'BD',
                availableLanguage: ['English', 'Bengali'],
            },
        ],
        sameAs: [
            'https://www.facebook.com/amzhospitalbd',
            'https://www.linkedin.com/company/amz-hospital',
            'https://www.youtube.com/@amzhospitalbd',
        ],
    };

    const website = {
        '@type': 'WebSite',
        '@id': `${baseUrl.value}/#website`,
        url: baseUrl.value,
        name: props.siteName,
        description: DEFAULT_DESCRIPTION,
        publisher: { '@id': `${baseUrl.value}/#organization` },
        potentialAction: {
            '@type': 'SearchAction',
            target: `${baseUrl.value}/?search={search_term_string}`,
            'query-input': 'required name=search_term_string',
        },
        inLanguage: 'en-US',
    };

    const webPage = {
        '@type': props.schemaType,
        '@id': `${canonicalUrl.value}#webpage`,
        url: canonicalUrl.value,
        name: browserTitle.value,
        headline: cleanTitle.value,
        description: metaDescription.value,
        image: imageUrl.value,
        isPartOf: { '@id': `${baseUrl.value}/#website` },
        about: { '@id': `${baseUrl.value}/#organization` },
        primaryImageOfPage: {
            '@type': 'ImageObject',
            url: imageUrl.value,
            width: Number(props.imageWidth) || 1200,
            height: Number(props.imageHeight) || 630,
            caption: props.imageAlt || cleanTitle.value,
        },
        breadcrumb: props.breadcrumbs?.length ? { '@id': `${canonicalUrl.value}#breadcrumb` } : undefined,
        datePublished: props.publishedTime || undefined,
        dateModified: props.modifiedTime || props.publishedTime || undefined,
        inLanguage: 'en-US',
    };

    const graph: Record<string, unknown>[] = [organization, website, webPage];

    if (props.breadcrumbs?.length) {
        graph.push({
            '@type': 'BreadcrumbList',
            '@id': `${canonicalUrl.value}#breadcrumb`,
            itemListElement: props.breadcrumbs.map((crumb, index) => ({
                '@type': 'ListItem',
                position: index + 1,
                name: crumb.name,
                item: absoluteUrl(crumb.url || (index + 1 === props.breadcrumbs?.length ? canonicalUrl.value : '/')),
            })),
        });
    }

    return graph;
});

const jsonLd = computed(() => {
    const customData = props.structuredData ? (Array.isArray(props.structuredData) ? props.structuredData : [props.structuredData]) : [];

    return JSON.stringify(
        {
            '@context': 'https://schema.org',
            '@graph': [...defaultStructuredData.value, ...customData],
        },
        null,
        2,
    );
});
</script>

<template>
    <Head>
        <title head-key="title">{{ browserTitle }}</title>

        <!-- Primary SEO -->
        <meta head-key="description" name="description" :content="metaDescription" />
        <meta head-key="keywords" name="keywords" :content="metaKeywords" />
        <meta head-key="robots" name="robots" :content="robotsContent" />
        <meta head-key="googlebot" name="googlebot" :content="robotsContent" />
        <meta head-key="bingbot" name="bingbot" :content="robotsContent" />
        <meta head-key="author" name="author" :content="author" />
        <meta head-key="publisher" name="publisher" :content="publisher" />
        <meta head-key="copyright" name="copyright" :content="`© ${new Date().getFullYear()} ${siteName}`" />
        <meta head-key="application-name" name="application-name" :content="siteName" />
        <meta head-key="generator" name="generator" content="Laravel, Inertia.js, Vue.js" />
        <meta head-key="referrer" name="referrer" content="strict-origin-when-cross-origin" />
        <meta head-key="format-detection" name="format-detection" content="telephone=yes, address=yes, email=yes" />
        <meta head-key="rating" name="rating" content="general" />
        <meta head-key="distribution" name="distribution" content="global" />
        <meta head-key="coverage" name="coverage" content="worldwide" />
        <meta head-key="revisit-after" name="revisit-after" content="7 days" />
        <meta head-key="language" name="language" content="English" />
        <meta head-key="theme-color" name="theme-color" content="#1e40af" />
        <meta head-key="msapplication-TileColor" name="msapplication-TileColor" content="#1e40af" />
        <meta head-key="color-scheme" name="color-scheme" content="light dark" />
        <meta head-key="mobile-web-app-capable" name="mobile-web-app-capable" content="yes" />
        <meta head-key="apple-mobile-web-app-capable" name="apple-mobile-web-app-capable" content="yes" />
        <meta head-key="apple-mobile-web-app-title" name="apple-mobile-web-app-title" :content="siteName" />
        <meta head-key="apple-mobile-web-app-status-bar-style" name="apple-mobile-web-app-status-bar-style" content="default" />
        <meta head-key="geo.region" name="geo.region" content="BD-13" />
        <meta head-key="geo.placename" name="geo.placename" content="Dhaka, Bangladesh" />
        <meta head-key="geo.position" name="geo.position" content="23.7806;90.4257" />
        <meta head-key="ICBM" name="ICBM" content="23.7806, 90.4257" />
        <meta v-if="publishedTime" head-key="date" name="date" :content="publishedTime" />
        <meta v-if="modifiedTime" head-key="last-modified" name="last-modified" :content="modifiedTime" />

        <!-- Canonical and language alternates -->
        <link head-key="canonical" rel="canonical" :href="canonicalUrl" />
        <link head-key="alternate-en" rel="alternate" hreflang="en" :href="canonicalUrl" />
        <link head-key="alternate-en-bd" rel="alternate" hreflang="en-BD" :href="canonicalUrl" />
        <link head-key="alternate-x-default" rel="alternate" hreflang="x-default" :href="canonicalUrl" />

        <!-- Open Graph / Facebook / LinkedIn -->
        <meta head-key="og:type" property="og:type" :content="type" />
        <meta head-key="og:site_name" property="og:site_name" :content="siteName" />
        <meta head-key="og:title" property="og:title" :content="browserTitle" />
        <meta head-key="og:description" property="og:description" :content="metaDescription" />
        <meta head-key="og:url" property="og:url" :content="canonicalUrl" />
        <meta head-key="og:image" property="og:image" :content="imageUrl" />
        <meta head-key="og:image:secure_url" property="og:image:secure_url" :content="imageUrl" />
        <meta head-key="og:image:alt" property="og:image:alt" :content="imageAlt || cleanTitle" />
        <meta head-key="og:image:type" property="og:image:type" :content="imageMimeType" />
        <meta head-key="og:image:width" property="og:image:width" :content="String(imageWidth)" />
        <meta head-key="og:image:height" property="og:image:height" :content="String(imageHeight)" />
        <meta head-key="og:locale" property="og:locale" :content="locale" />
        <meta head-key="og:determiner" property="og:determiner" content="the" />
        <meta v-if="modifiedTime" head-key="og:updated_time" property="og:updated_time" :content="modifiedTime" />
        <meta v-if="publishedTime" head-key="article:published_time" property="article:published_time" :content="publishedTime" />
        <meta v-if="modifiedTime" head-key="article:modified_time" property="article:modified_time" :content="modifiedTime" />
        <meta v-if="expirationTime" head-key="article:expiration_time" property="article:expiration_time" :content="expirationTime" />
        <meta v-if="author" head-key="article:author" property="article:author" :content="author" />
        <meta v-if="publisher" head-key="article:publisher" property="article:publisher" :content="publisher" />
        <meta v-if="section" head-key="article:section" property="article:section" :content="section" />
        <meta v-for="tag in articleTags" :key="`article-tag-${tag}`" property="article:tag" :content="tag" />

        <!-- Twitter / X Cards -->
        <meta head-key="twitter:card" name="twitter:card" :content="twitterCard" />
        <meta head-key="twitter:site" name="twitter:site" :content="twitterSite" />
        <meta head-key="twitter:creator" name="twitter:creator" :content="twitterCreator" />
        <meta head-key="twitter:title" name="twitter:title" :content="browserTitle" />
        <meta head-key="twitter:description" name="twitter:description" :content="metaDescription" />
        <meta head-key="twitter:image" name="twitter:image" :content="imageUrl" />
        <meta head-key="twitter:image:alt" name="twitter:image:alt" :content="imageAlt || cleanTitle" />
        <meta head-key="twitter:domain" name="twitter:domain" :content="baseUrl.replace(/^https?:\/\//, '')" />
        <meta head-key="twitter:url" name="twitter:url" :content="canonicalUrl" />

        <!-- Dublin Core -->
        <meta head-key="dc.title" name="DC.title" :content="browserTitle" />
        <meta head-key="dc.description" name="DC.description" :content="metaDescription" />
        <meta head-key="dc.creator" name="DC.creator" :content="author" />
        <meta head-key="dc.publisher" name="DC.publisher" :content="publisher" />
        <meta head-key="dc.language" name="DC.language" content="en" />
        <meta head-key="dc.coverage" name="DC.coverage" content="Bangladesh" />
        <meta head-key="dc.rights" name="DC.rights" :content="`© ${new Date().getFullYear()} ${siteName}`" />
        <meta v-if="publishedTime" head-key="dcterms.issued" name="DCTERMS.issued" :content="publishedTime" />
        <meta v-if="modifiedTime" head-key="dcterms.modified" name="DCTERMS.modified" :content="modifiedTime" />

        <!-- Healthcare business hints -->
        <meta head-key="business-contact-data.street_address" name="business:contact_data:street_address" :content="DEFAULT_ADDRESS" />
        <meta head-key="business-contact-data.locality" name="business:contact_data:locality" content="Dhaka" />
        <meta head-key="business-contact-data.region" name="business:contact_data:region" content="Dhaka Division" />
        <meta head-key="business-contact-data.postal_code" name="business:contact_data:postal_code" content="1212" />
        <meta head-key="business-contact-data.country_name" name="business:contact_data:country_name" content="Bangladesh" />
        <meta head-key="business-contact-data.email" name="business:contact_data:email" :content="DEFAULT_EMAIL" />
        <meta head-key="business-contact-data.phone_number" name="business:contact_data:phone_number" :content="DEFAULT_PHONE" />

        <component :is="'script'" head-key="seo-json-ld" type="application/ld+json" v-text="jsonLd" />
    </Head>
</template>

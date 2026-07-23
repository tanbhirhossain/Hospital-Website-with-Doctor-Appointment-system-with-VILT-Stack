<template>
    <FrontendLayout>
        <section class="p-32">
            <ul>
                <a v-for="value in services" :key="value.slug" :href="'/services/' + value.slug">
                <li>{{ value.title }}</li>
                </a>
            </ul>
        </section>
    </FrontendLayout>
</template>

<script setup>
    import FrontendLayout from '../../Layout/FrontendLayout.vue';

    defineProps({
        services: Array
    })
</script>
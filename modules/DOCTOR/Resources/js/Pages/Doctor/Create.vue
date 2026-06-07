<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-50 p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-5xl rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                <form>
                <div>
                    <label>Doctor Name</label>
                    <input 
                        type="text" 
                        v-model="form.name" 
                        placeholder="Enter Doctor Name"
                        class="border border-2 border-black-800  p-2 w-full mb-4"
                        />
                </div>

                <div>
                    <label>Slug</label>
                    <input type="text" v-model="form.slug" placeholder="Enter doctor name to generate slug"
                        class="border border-2 border-black-800  p-2 w-full mb-4"
                    />
                </div>

                <div>
                    <lebel>Department</lebel>
                    <select
                            v-model="form.department"

                            class="border border-2 border-black-600  p-2 w-full mb-4"
                    >
                        <option v-for="(dept, index) in departments">
                            
                            {{ dept.name }}</option>
                    </select>
                </div>
                </form>           
            </div>
        </div>
            
    </AppLayout>
</template>

<script setup>
    import AppLayout from '@/layouts/AppLayout.vue';
    import { reactive, watch } from 'vue'; 

    const form = reactive({
        name: '',
        slug: '',
        specialty: '',
        qualifications: '',
        designations: '',
        institute: '',
        biography: '',
        chamber_location: '',
        visit_fee: '',
        report_fee: '',
        phone: '',
        email: '',
        mis_code: '',
        skills: '',
        awards: '',
        serials: '',
        is_active: '',
        is_home_page: '',
        is_fetured: '',
        meta_title: '',
        meta_description: '',
        canonical_url: '',
        og_title: '',
        og_description: '',
        indexable: ''

    });

    defineProps({
        departments: Array
    })


    watch(()=> form.name, (newName)=>{
        form.slug = newName
                .toLowerCase()
                .replace(/ /g, '-')
                .replace(/[^\w-]+/g, '')
    })



</script>
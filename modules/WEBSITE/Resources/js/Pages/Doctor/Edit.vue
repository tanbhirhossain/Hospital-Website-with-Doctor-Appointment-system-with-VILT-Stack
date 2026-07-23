<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between py-2">
        <div>
          <h2 class="font-bold text-2xl text-gray-900 tracking-tight">Edit Doctor Profile</h2>
          <p class="text-sm text-gray-500 mt-1">Modify public credentials, clinical fees, and weekly consulting hours.</p>
        </div>
      </div>
    </template>

    <Head :title="`Edit ${form.name}`" />

    <div class="py-10 bg-gray-50 min-h-screen">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <form @submit.prevent="submit" class="space-y-8 pb-24">
          <!-- Basic Information -->
          <div class="bg-white rounded-xl border border-gray-300 shadow-sm p-6 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
              <div class="lg:col-span-4">
                <h3 class="text-base font-bold text-gray-900">Basic Information</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs leading-relaxed">
                  Primary public identity data, classification structures, and deep-link generation.
                </p>
              </div>

              <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">
                    Doctor Name <span class="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    v-model="form.name"
                    placeholder="e.g., Dr. Jane Oliver"
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                    :class="{ 'border-red-500': form.errors.name }"
                  />
                  <InputError :message="form.errors.name" class="mt-1" />
                </div>

                <div class="sm:col-span-2">
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Slug URL</label>
                  <div class="flex h-11 rounded-lg shadow-sm overflow-hidden border border-gray-300">
                    <span class="inline-flex items-center px-4 bg-gray-100 border-r border-gray-300 text-gray-500 text-xs font-mono">
                      /dr/
                    </span>
                    <input
                      type="text"
                      v-model="form.slug"
                      placeholder="jane-oliver"
                      class="flex-1 min-w-0 block w-full px-4 bg-gray-50 text-gray-500 text-sm font-mono outline-none"
                      readonly
                    />
                  </div>
                  <p class="text-xs text-gray-400 mt-1">Auto-generated from doctor name</p>
                </div>

                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">
                    Department <span class="text-red-500">*</span>
                  </label>
                  <select
                    v-model="form.department_id"
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                    :class="{ 'border-red-500': form.errors.department_id }"
                  >
                    <option value="" disabled>Select Department</option>
                    <option v-for="dept in departmentsArray" :key="dept.id" :value="dept.id">
                      {{ dept.name }}
                    </option>
                  </select>
                  <InputError :message="form.errors.department_id" class="mt-1" />
                </div>

                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Specialty</label>
                  <input
                    type="text"
                    v-model="form.specialty"
                    placeholder="e.g., Pediatric Cardiology"
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Contact Details -->
          <div class="bg-white rounded-xl border border-gray-300 shadow-sm p-6 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
              <div class="lg:col-span-4">
                <h3 class="text-base font-bold text-gray-900">Contact Details</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs leading-relaxed">
                  Communication channels used for admin tracking.
                </p>
              </div>

              <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Phone Number</label>
                  <input
                    type="tel"
                    v-model="form.phone"
                    placeholder="e.g., +1 (555) 019-2834"
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                  />
                </div>
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Email Address</label>
                  <input
                    type="email"
                    v-model="form.email"
                    placeholder="e.g., doctor@hospital.com"
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                  />
                </div>
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">MIS Code</label>
                  <input
                    type="text"
                    v-model="form.mis_code"
                    placeholder="e.g., MIS-9921"
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Media & Documents -->
          <div class="bg-white rounded-xl border border-gray-300 shadow-sm p-6 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
              <div class="lg:col-span-4">
                <h3 class="text-base font-bold text-gray-900">Media & Documents</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs leading-relaxed">
                  Professional profile image, gallery photos, and certification documents.
                </p>
              </div>

              <div class="lg:col-span-8 space-y-8">
                <!-- Profile Image -->
                <div class="border border-dashed border-gray-300 rounded-lg p-6 bg-gray-50">
                  <h4 class="text-sm font-bold text-gray-900 mb-2">Profile Picture</h4>
                  <p class="text-xs text-gray-600 mb-4">Upload a professional headshot (JPG, PNG, WEBP - Max 5MB)</p>
                  <div class="flex items-center gap-4 flex-wrap">
                    <div v-if="profileImagePreview" class="relative group">
                      <img :src="profileImagePreview" alt="Profile preview" class="w-20 h-20 rounded-lg object-cover border border-gray-200 shadow-sm" />
                      <button
                        type="button"
                        @click="removeProfileImage"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition"
                      >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                      </button>
                    </div>
                    <div class="flex-1">
                      <label class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 cursor-pointer transition-all shadow-sm">
                        <input type="file" accept="image/jpeg,image/png,image/webp" @change="setProfileImage" class="hidden" />
                        {{ profileImagePreview ? 'Replace Photo' : 'Choose Photo' }}
                      </label>
                    </div>
                  </div>
                  <InputError :message="form.errors.profile_image" class="mt-2" />
                </div>

                <!-- Gallery Images -->
                <div class="border border-dashed border-gray-300 rounded-lg p-6 bg-gray-50">
                  <h4 class="text-sm font-bold text-gray-900 mb-2">Gallery Images</h4>
                  <p class="text-xs text-gray-600 mb-4">Upload multiple photos for the doctor's public gallery (JPG, PNG, WEBP - Max 5MB each, up to 10 images)</p>
                  <label class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 cursor-pointer transition-all shadow-sm mb-4">
                    <input type="file" multiple accept="image/jpeg,image/png,image/webp" @change="setGalleryFiles" class="hidden" />
                    Add Gallery Images
                  </label>

                  <!-- New gallery previews -->
                  <div v-if="galleryPreviews.length" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <div v-for="(preview, idx) in galleryPreviews" :key="'new'+idx" class="relative group rounded-lg overflow-hidden border border-gray-200 bg-white shadow-sm">
                      <img :src="preview" class="w-full h-24 object-cover" />
                      <button type="button" @click="removeGalleryFile(idx)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                      </button>
                      <span class="absolute bottom-0 left-0 right-0 text-xs bg-black bg-opacity-60 text-white truncate px-1">{{ galleryFiles[idx]?.name }}</span>
                    </div>
                  </div>

                  <!-- Existing gallery with delete option -->
                  <div v-if="existingGallery.length" class="mt-6 space-y-2">
                    <p class="text-sm font-semibold text-gray-700">Current Gallery ({{ existingGallery.length }})</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                      <div v-for="img in existingGallery" :key="img.id" class="relative group rounded-lg overflow-hidden border border-gray-200 bg-white shadow-sm">
                        <img :src="img.url" class="w-full h-24 object-cover" />
                        <button type="button" @click="removeExistingGalleryImage(img.id)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <span class="absolute bottom-0 left-0 right-0 text-xs bg-black bg-opacity-60 text-white truncate px-1">{{ img.name }}</span>
                      </div>
                    </div>
                  </div>
                  <InputError :message="form.errors.gallery" class="mt-2" />
                </div>

                <!-- Certificates -->
                <div class="border border-dashed border-gray-300 rounded-lg p-6 bg-gray-50">
                  <h4 class="text-sm font-bold text-gray-900 mb-2">Certificates & Qualifications</h4>
                  <p class="text-xs text-gray-600 mb-4">Upload professional certificates (JPG, PNG, PDF - Max 10MB each, up to 5 files)</p>
                  <label class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 cursor-pointer transition-all shadow-sm mb-4">
                    <input type="file" multiple accept="image/jpeg,image/png,application/pdf" @change="setCertificateFiles" class="hidden" />
                    Add Certificates
                  </label>

                  <!-- New certificates list -->
                  <div v-if="certificateFiles.length" class="mt-4 space-y-2">
                    <p class="text-xs font-semibold text-gray-700">New files to upload ({{ certificateFiles.length }})</p>
                    <div v-for="(file, idx) in certificateFiles" :key="'newCert'+idx" class="flex items-center justify-between rounded-lg bg-white px-3 py-2 border border-gray-200">
                      <div class="flex items-center gap-2">
                        <svg v-if="file.type === 'application/pdf'" class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                        <svg v-else class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                        <span class="text-sm text-gray-700 truncate max-w-[200px]">{{ file.name }}</span>
                      </div>
                      <button type="button" @click="removeCertificateFile(idx)" class="text-rose-600 hover:text-rose-900 text-sm font-bold transition">Remove</button>
                    </div>
                  </div>

                  <!-- Existing certificates with delete -->
                  <div v-if="existingCertificates.length" class="mt-6 space-y-2">
                    <p class="text-sm font-semibold text-gray-700">Current Certificates ({{ existingCertificates.length }})</p>
                    <div v-for="cert in existingCertificates" :key="cert.id" class="flex items-center justify-between rounded-lg bg-white px-3 py-2 border border-gray-200">
                      <div class="flex items-center gap-2">
                        <svg v-if="cert.url.match(/\.pdf$/i)" class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                        <svg v-else class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                        <span class="text-sm text-gray-700">{{ cert.name }}</span>
                      </div>
                      <div class="flex gap-3">
                        <a :href="cert.url" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm font-bold transition">View</a>
                        <button type="button" @click="removeExistingCertificate(cert.id)" class="text-rose-600 hover:text-rose-900 text-sm font-bold transition">Delete</button>
                      </div>
                    </div>
                  </div>
                  <InputError :message="form.errors.certificates" class="mt-2" />
                </div>
              </div>
            </div>
          </div>

          <!-- Professional Background -->
          <div class="bg-white rounded-xl border border-gray-300 shadow-sm p-6 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
              <div class="lg:col-span-4">
                <h3 class="text-base font-bold text-gray-900">Professional Background</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs leading-relaxed">
                  Academic qualifications, designations, and background histories.
                </p>
              </div>

              <div class="lg:col-span-8 space-y-5">
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Qualifications</label>
                  <input
                    type="text"
                    v-model="form.qualifications"
                    placeholder="e.g., MBBS, MD, PhD, FACS"
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                  />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Designation</label>
                    <input
                      type="text"
                      v-model="form.designation"
                      placeholder="e.g., Senior Consultant"
                      class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                    />
                  </div>
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Institute</label>
                    <input
                      type="text"
                      v-model="form.institute"
                      placeholder="e.g., Johns Hopkins Hospital"
                      class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                    />
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Biography</label>
                  <div class="rounded-lg border border-gray-300 overflow-hidden shadow-sm bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500">
                    <RichTextEditor v-model="form.biography" min-height-class="min-h-32" />
                  </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Skills</label>
                    <div class="rounded-lg border border-gray-300 overflow-hidden shadow-sm bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500">
                      <RichTextEditor v-model="form.skills" min-height-class="min-h-24" />
                    </div>
                  </div>
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Awards</label>
                    <div class="rounded-lg border border-gray-300 overflow-hidden shadow-sm bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500">
                      <RichTextEditor v-model="form.awards" min-height-class="min-h-24" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Logistics & Financials -->
          <div class="bg-white rounded-xl border border-gray-300 shadow-sm p-6 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
              <div class="lg:col-span-4">
                <h3 class="text-base font-bold text-gray-900">Logistics & Financials</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs leading-relaxed">
                  Physical workspace assignments and pricing tiers.
                </p>
              </div>

              <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Chamber Location</label>
                  <input
                    type="text"
                    v-model="form.chamber_location"
                    placeholder="e.g., Room 402, Block B"
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                  />
                </div>
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Visit Fee ($)</label>
                  <input
                    type="number"
                    step="0.01"
                    v-model="form.visit_fee"
                    placeholder="150"
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                  />
                </div>
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Report Review Fee ($)</label>
                  <input
                    type="number"
                    step="0.01"
                    v-model="form.report_fee"
                    placeholder="75"
                    class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Operational Timetable (same as Create) -->
          <div class="bg-white rounded-xl border border-gray-300 shadow-sm p-6 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
              <div class="lg:col-span-4">
                <h3 class="text-base font-bold text-gray-900">Operational Timetable</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs leading-relaxed">
                  Build dynamic time blocks indicating structured session slots.
                </p>
              </div>

              <div class="lg:col-span-8 space-y-4">
                <div v-if="form.timetables.length > 0" class="overflow-hidden border border-gray-300 rounded-xl shadow-sm">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Day</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Start Time</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">End Time</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Max Patients</th>
                        <th class="relative px-4 py-3 text-right"></th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <tr v-for="(item, idx) in form.timetables" :key="idx" class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ getDayName(item.day_of_week) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ item.start_time }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ item.end_time }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ item.max_patient ?? '∞' }}</td>
                        <td class="px-4 py-3 text-right text-sm">
                          <button type="button" @click="removeTimetableRow(idx)" class="font-bold text-rose-600 hover:text-rose-900 transition">Remove</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 p-4 rounded-xl bg-gray-50 border border-dashed border-gray-300 items-end">
                  <div class="sm:col-span-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Day</label>
                    <select v-model="newTimetable.day_of_week" class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none">
                      <option value="" disabled>Select Day</option>
                      <option value="0">Sunday</option>
                      <option value="1">Monday</option>
                      <option value="2">Tuesday</option>
                      <option value="3">Wednesday</option>
                      <option value="4">Thursday</option>
                      <option value="5">Friday</option>
                      <option value="6">Saturday</option>
                    </select>
                  </div>
                  <div class="sm:col-span-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Start</label>
                    <input type="time" v-model="newTimetable.start_time" class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none" />
                  </div>
                  <div class="sm:col-span-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">End</label>
                    <input type="time" v-model="newTimetable.end_time" class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none" />
                  </div>
                  <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Max Patients</label>
                    <input type="number" v-model="newTimetable.max_patient" placeholder="Unlimited" class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none" />
                  </div>
                  <div class="sm:col-span-1">
                    <button type="button" @click="addTimetableRow" class="w-full h-11 inline-flex justify-center items-center rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow font-bold text-lg transition-all">+</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- SEO & Visibility -->
          <div class="bg-white rounded-xl border border-gray-300 shadow-sm p-6 sm:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
              <div class="lg:col-span-4">
                <h3 class="text-base font-bold text-gray-900">SEO & Visibility</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-xs leading-relaxed">
                  Control search engine indexing, social media sharing, and meta information.
                </p>
              </div>
              <div class="lg:col-span-8 space-y-4">
                <div class="flex items-center gap-6 flex-wrap">
                  <label class="inline-flex items-center">
                    <input type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <span class="ml-2 text-sm text-gray-700">Active (visible on site)</span>
                  </label>
                  <label class="inline-flex items-center">
                    <input type="checkbox" v-model="form.is_featured" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <span class="ml-2 text-sm text-gray-700">Featured on homepage</span>
                  </label>
                  <label class="inline-flex items-center">
                    <input type="checkbox" v-model="form.indexable" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <span class="ml-2 text-sm text-gray-700">Allow search indexing</span>
                  </label>
                </div>
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Meta Title</label>
                  <input type="text" v-model="form.meta_title" placeholder="SEO title (max 60 chars)" class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm" />
                </div>
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Meta Description</label>
                  <textarea v-model="form.meta_description" rows="2" placeholder="Brief description for search results (max 160 chars)" class="w-full block rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm"></textarea>
                </div>
                <div>
                  <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Canonical URL</label>
                  <input type="url" v-model="form.canonical_url" placeholder="https://example.com/doctor/jane-oliver" class="w-full h-11 block rounded-lg border border-gray-300 bg-white px-4 text-sm" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">OG Title</label>
                    <input type="text" v-model="form.og_title" class="w-full h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm" />
                  </div>
                  <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">OG Description</label>
                    <input type="text" v-model="form.og_description" class="w-full h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end items-center gap-3 bg-white border border-gray-300 rounded-xl p-4 shadow-sm mt-8">
            <Link :href="route('admin.doctors.index')" class="h-11 px-6 border border-gray-300 rounded-lg text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 transition-all shadow-sm focus:ring-2 focus:ring-indigo-500/20 inline-flex items-center justify-center">
              Cancel
            </Link>
            <button type="submit" :disabled="form.processing" class="h-11 px-6 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-70 disabled:cursor-not-allowed inline-flex items-center gap-2">
              <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Update Doctor Profile
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '@/components/InputError.vue';
import RichTextEditor from '../../Components/RichTextEditor.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, reactive, watch } from 'vue';

interface DepartmentOption { id: number; name: string; }
interface GalleryImage { id: number; url: string; name: string; }
interface TimetableItem {
  day_of_week: number;
  start_time: string;
  end_time: string;
  location: string;
  max_patient: number | null;
  is_active: boolean;
}
interface Doctor {
  id: number;
  department_id: number;
  name: string;
  slug: string;
  specialty: string;
  qualifications: string;
  designation: string;
  institute: string;
  biography: string | null;
  chamber_location: string | null;
  visit_fee: string | null;
  report_fee: string | null;
  phone: string | null;
  email: string | null;
  mis_code: string | null;
  skills: string | null;
  awards: string | null;
  serial: number;
  is_active: boolean | number;
  is_home_page: boolean | number;
  is_featured: boolean | number;
  meta_title: string | null;
  meta_description: string | null;
  canonical_url: string | null;
  og_title: string | null;
  og_description: string | null;
  indexable: boolean | number;
  timetables: TimetableItem[];
}

const props = defineProps<{
  doctor: Doctor;
  departments: DepartmentOption[] | Record<string, DepartmentOption>;
  profileImage: GalleryImage | null;
  gallery: GalleryImage[];
  certificates: GalleryImage[];
}>();

// Normalize departments to array
const departmentsArray = computed<DepartmentOption[]>(() => {
  if (Array.isArray(props.departments)) return props.departments.filter(d => d?.id);
  return Object.values(props.departments || {}).filter(d => d?.id);
});

// Filter out null/invalid gallery items
const existingGallery = computed(() => {
  if (!Array.isArray(props.gallery)) return [];
  return props.gallery.filter(img => img && typeof img === 'object' && 'id' in img);
});

const existingCertificates = computed(() => {
  if (!Array.isArray(props.certificates)) return [];
  return props.certificates.filter(cert => cert && typeof cert === 'object' && 'id' in cert);
});

// Form state
const slugManuallyEdited = ref(false);
const profileImagePreview = ref<string>(props.profileImage?.url ?? '');
const galleryFiles = ref<File[]>([]);
const galleryPreviews = ref<string[]>([]);
const certificateFiles = ref<File[]>([]);
const deletedGalleryIds = ref<number[]>([]);
const deletedCertificateIds = ref<number[]>([]);
const deleteProfileImage = ref(false);

const newTimetable = reactive({
  day_of_week: '',
  start_time: '',
  end_time: '',
  max_patient: null as number | null,
});

const form = useForm({
  _method: 'PUT',
  department_id: props.doctor.department_id,
  name: props.doctor.name,
  slug: props.doctor.slug,
  specialty: props.doctor.specialty,
  qualifications: props.doctor.qualifications,
  designation: props.doctor.designation,
  institute: props.doctor.institute,
  biography: props.doctor.biography ?? '',
  chamber_location: props.doctor.chamber_location ?? '',
  visit_fee: props.doctor.visit_fee ? parseFloat(props.doctor.visit_fee) : null,
  report_fee: props.doctor.report_fee ? parseFloat(props.doctor.report_fee) : null,
  phone: props.doctor.phone ?? '',
  email: props.doctor.email ?? '',
  mis_code: props.doctor.mis_code ?? '',
  skills: props.doctor.skills ?? '',
  awards: props.doctor.awards ?? '',
  serial: props.doctor.serial,
  is_active: !!props.doctor.is_active,
  is_home_page: !!props.doctor.is_home_page,
  is_featured: !!props.doctor.is_featured,
  meta_title: props.doctor.meta_title ?? '',
  meta_description: props.doctor.meta_description ?? '',
  canonical_url: props.doctor.canonical_url ?? '',
  og_title: props.doctor.og_title ?? '',
  og_description: props.doctor.og_description ?? '',
  indexable: !!props.doctor.indexable,
  profile_image: null as File | null,
  gallery: [] as File[],
  certificates: [] as File[],
  timetables: (props.doctor.timetables || []).map(t => ({
    day_of_week: t.day_of_week,
    start_time: t.start_time.substring(0, 5),
    end_time: t.end_time.substring(0, 5),
    location: t.location ?? '',
    max_patient: t.max_patient,
    is_active: !!t.is_active,
  })),
  deleted_gallery_ids: [] as number[],
  deleted_certificate_ids: [] as number[],
  delete_profile_image: false,
});

watch(deletedGalleryIds, val => { form.deleted_gallery_ids = val; });
watch(deletedCertificateIds, val => { form.deleted_certificate_ids = val; });
watch(deleteProfileImage, val => { form.delete_profile_image = val; });

watch(() => form.name, (newName) => {
  if (!slugManuallyEdited.value) {
    form.slug = newName
      ? newName.toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-')
      : '';
  }
});

const handleSlugInput = () => { slugManuallyEdited.value = true; };

// Profile image handlers
const setProfileImage = (event: Event) => {
  const files = (event.target as HTMLInputElement)?.files;
  if (!files || files.length === 0) return;
  const file = files[0];
  if (file.size > 5 * 1024 * 1024) {
    alert('File too large (max 5MB)');
    return;
  }
  if (!file.type.startsWith('image/')) {
    alert('Only image files are allowed');
    return;
  }
  form.profile_image = file;
  deleteProfileImage.value = false;
  const reader = new FileReader();
  reader.onload = (e) => { profileImagePreview.value = e.target?.result as string; };
  reader.readAsDataURL(file);
};

const removeProfileImage = () => {
  if (props.profileImage) deleteProfileImage.value = true;
  profileImagePreview.value = '';
  form.profile_image = null;
};

// Gallery handlers
const setGalleryFiles = (event: Event) => {
  const files = (event.target as HTMLInputElement)?.files;
  if (!files) return;
  const maxNew = 10 - (existingGallery.value.length - deletedGalleryIds.value.length + galleryFiles.value.length);
  for (let i = 0; i < files.length && galleryFiles.value.length < maxNew; i++) {
    const file = files[i];
    if (file.size > 5 * 1024 * 1024) continue;
    if (!file.type.startsWith('image/')) continue;
    galleryFiles.value.push(file);
    const reader = new FileReader();
    reader.onload = (e) => galleryPreviews.value.push(e.target?.result as string);
    reader.readAsDataURL(file);
  }
  form.gallery = [...galleryFiles.value];
};

const removeGalleryFile = (index: number) => {
  galleryFiles.value.splice(index, 1);
  galleryPreviews.value.splice(index, 1);
  form.gallery = [...galleryFiles.value];
};

const removeExistingGalleryImage = (id: number) => {
  deletedGalleryIds.value.push(id);
};

// Certificates handlers
const setCertificateFiles = (event: Event) => {
  const files = (event.target as HTMLInputElement)?.files;
  if (!files) return;
  const maxNew = 5 - (existingCertificates.value.length - deletedCertificateIds.value.length + certificateFiles.value.length);
  for (let i = 0; i < files.length && certificateFiles.value.length < maxNew; i++) {
    const file = files[i];
    const maxSize = file.type === 'application/pdf' ? 10 : 5;
    if (file.size > maxSize * 1024 * 1024) continue;
    if (!(file.type.startsWith('image/') || file.type === 'application/pdf')) continue;
    certificateFiles.value.push(file);
  }
  form.certificates = [...certificateFiles.value];
};

const removeCertificateFile = (index: number) => {
  certificateFiles.value.splice(index, 1);
  form.certificates = [...certificateFiles.value];
};

const removeExistingCertificate = (id: number) => {
  deletedCertificateIds.value.push(id);
};

// Timetable handlers
const addTimetableRow = () => {
  if (newTimetable.day_of_week === '' || !newTimetable.start_time || !newTimetable.end_time) {
    alert('Please fill day, start and end time');
    return;
  }
  if (newTimetable.start_time >= newTimetable.end_time) {
    alert('End time must be after start time');
    return;
  }
  form.timetables.push({
    day_of_week: Number(newTimetable.day_of_week),
    start_time: newTimetable.start_time,
    end_time: newTimetable.end_time,
    location: '',
    max_patient: newTimetable.max_patient,
    is_active: true,
  });
  newTimetable.day_of_week = '';
  newTimetable.start_time = '';
  newTimetable.end_time = '';
  newTimetable.max_patient = null;
};

const removeTimetableRow = (index: number) => {
  form.timetables.splice(index, 1);
};

const getDayName = (dayValue: string | number) => {
  const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
  return days[Number(dayValue)] || '';
};

// Submit
const submit = () => {
  form.post(route('admin.doctors.update', props.doctor.id), {
    preserveScroll: true,
    onError: (errors) => {
      console.error('Validation errors:', errors);
    },
  });
};
</script>
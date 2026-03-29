<script setup lang="ts">
import HomeLayout from '@/layouts/HomeLayout.vue';
import { computed } from 'vue';
import { Calendar, Clock, CheckCircle2 } from 'lucide-vue-next';

const props = defineProps<{
    kegiatan: Array<any>;
}>();

const upcoming = computed(() => props.kegiatan.filter(k => new Date(k.tanggal) > new Date()));
const past = computed(() => props.kegiatan.filter(k => new Date(k.tanggal) <= new Date()));

const formatDate = (dateStr: string) => new Intl.DateTimeFormat('id-ID', { dateStyle: 'full' }).format(new Date(dateStr));
const formatTime = (dateStr: string) => new Intl.DateTimeFormat('id-ID', { timeStyle: 'short' }).format(new Date(dateStr));
</script>

<template>
    <HomeLayout title="Kegiatan Warga">
        <div class="min-h-screen bg-amber-50/30 py-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-16">
                    <p class="text-xs font-black text-amber-600 uppercase tracking-[0.3em] mb-4">Program Komunitas</p>
                    <h1 class="text-5xl font-black text-stone-900 leading-tight">Kegiatan <span class="text-amber-600">Warga</span></h1>
                    <p class="text-stone-500 font-medium mt-4 text-lg">Forum dan aktivitas bersama warga Perumahan Suryo Green Village.</p>
                </div>

                <!-- Upcoming Events -->
                <div v-if="upcoming.length > 0" class="mb-16">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-2 bg-blue-100 rounded-xl"><Clock class="w-5 h-5 text-blue-600" /></div>
                        <h2 class="text-xl font-black text-stone-900">Kegiatan Mendatang</h2>
                        <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-[10px] font-black rounded-full uppercase tracking-widest">{{ upcoming.length }} Acara</span>
                    </div>

                    <div class="space-y-6">
                        <div v-for="k in upcoming" :key="k.id"
                             class="bg-white rounded-[2rem] border border-amber-100 overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row group">
                            <div class="md:w-56 h-48 md:h-auto shrink-0 bg-amber-50 overflow-hidden">
                                <img v-if="k.gambar" :src="'/storage/' + k.gambar" :alt="k.judul" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <Calendar class="w-12 h-12 text-amber-200" />
                                </div>
                            </div>
                            <div class="p-8 flex flex-col justify-between flex-1">
                                <div>
                                    <span class="inline-block px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100 mb-4">Mendatang</span>
                                    <h3 class="text-xl font-black text-stone-900">{{ k.judul }}</h3>
                                    <p v-if="k.deskripsi" class="text-stone-500 mt-2 leading-relaxed">{{ k.deskripsi }}</p>
                                </div>
                                <div class="flex items-center gap-4 mt-6 pt-6 border-t border-amber-50">
                                    <div class="flex items-center gap-2 text-sm font-bold text-stone-600">
                                        <Calendar class="w-4 h-4 text-amber-500" />
                                        {{ formatDate(k.tanggal) }}
                                    </div>
                                    <div class="flex items-center gap-2 text-sm font-bold text-stone-600">
                                        <Clock class="w-4 h-4 text-amber-500" />
                                        {{ formatTime(k.tanggal) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Past Events -->
                <div v-if="past.length > 0">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-2 bg-stone-100 rounded-xl"><CheckCircle2 class="w-5 h-5 text-stone-500" /></div>
                        <h2 class="text-xl font-black text-stone-700">Kegiatan Selesai</h2>
                        <span class="px-2.5 py-1 bg-stone-100 text-stone-500 text-[10px] font-black rounded-full uppercase tracking-widest">{{ past.length }} Kegiatan</span>
                    </div>

                    <div class="space-y-4">
                        <div v-for="k in past" :key="k.id"
                             class="bg-white/70 rounded-2xl border border-stone-100 overflow-hidden p-6 flex items-start gap-5 opacity-80 hover:opacity-100 transition-opacity">
                            <div class="w-16 h-16 rounded-xl overflow-hidden bg-stone-100 shrink-0">
                                <img v-if="k.gambar" :src="'/storage/' + k.gambar" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <CheckCircle2 class="w-7 h-7 text-stone-300" />
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-black text-stone-700">{{ k.judul }}</h3>
                                <p v-if="k.deskripsi" class="text-stone-400 text-sm mt-1 line-clamp-2">{{ k.deskripsi }}</p>
                                <p class="text-xs text-stone-400 font-bold mt-2">{{ formatDate(k.tanggal) }}</p>
                            </div>
                            <span class="px-2.5 py-1 bg-stone-100 text-stone-400 text-[9px] font-black rounded-lg uppercase tracking-widest border border-stone-200 shrink-0">Selesai</span>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="kegiatan.length === 0" class="bg-white rounded-[3rem] border border-amber-100 p-24 text-center shadow-sm">
                    <Calendar class="w-16 h-16 text-amber-200 mx-auto mb-4" />
                    <h3 class="text-xl font-black text-stone-700 mb-2">Belum Ada Kegiatan</h3>
                    <p class="text-stone-400">Kegiatan komunitas akan diumumkan di sini.</p>
                </div>
            </div>
        </div>
    </HomeLayout>
</template>

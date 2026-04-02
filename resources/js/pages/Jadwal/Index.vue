<script setup lang="ts">
import HomeLayout from '@/layouts/HomeLayout.vue';
import { Calendar, Clock, CheckCircle2, CalendarX } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';

const websetting = usePage().props.websetting as any;

defineProps<{
    upcoming: Array<any>;
    past: Array<any>;
}>();

const formatDate = (dateStr: string) =>
    new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(dateStr));

const formatTime = (dateStr: string) =>
    new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit' }).format(new Date(dateStr));
</script>

<template>
    <HomeLayout title="Jadwal Kegiatan">
        <div class="min-h-screen bg-amber-50/30 py-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Page Header -->
                <div class="text-center mb-16">
                    <p class="text-xs font-black text-amber-600 uppercase tracking-[0.3em] mb-4">Agenda Komunitas</p>
                    <h1 class="text-5xl font-black text-stone-900 leading-tight">
                        Jadwal <span class="text-amber-600">Kegiatan</span>
                    </h1>
                    <p class="text-stone-500 mt-4 text-lg font-medium max-w-xl mx-auto">
                        Program dan agenda bersama warga Perumahan {{ websetting.website_name }}.
                    </p>
                </div>

                <!-- Empty State -->
                <div v-if="upcoming.length === 0 && past.length === 0"
                     class="bg-white rounded-[3rem] border border-amber-100 p-24 text-center shadow-sm">
                    <CalendarX class="w-14 h-14 text-amber-100 mx-auto mb-4" />
                    <h3 class="text-xl font-black text-stone-700 mb-2">Belum Ada Kegiatan</h3>
                    <p class="text-stone-400">Agenda komunitas akan ditampilkan di sini.</p>
                </div>

                <!-- ═══ Upcoming Events ═══ -->
                <section v-if="upcoming.length > 0" class="mb-16">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-100 rounded-full">
                            <Clock class="w-4 h-4 text-blue-600" />
                            <span class="text-xs font-black text-blue-700 uppercase tracking-widest">Mendatang</span>
                        </div>
                        <span class="text-xs text-stone-400 font-bold">{{ upcoming.length }} Kegiatan</span>
                    </div>

                    <div class="space-y-5">
                        <article v-for="k in upcoming" :key="k.id"
                                 class="bg-white rounded-[2rem] border border-amber-100 overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col md:flex-row group">

                            <!-- Image -->
                            <div class="md:w-52 md:h-auto h-48 shrink-0 overflow-hidden bg-amber-50">
                                <img v-if="k.gambar" :src="'/storage/' + k.gambar" :alt="k.judul"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <Calendar class="w-10 h-10 text-amber-200" />
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex flex-col justify-between p-7 flex-1">
                                <div>
                                    <!-- Status Badge -->
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-full text-[10px] font-black uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                            MENDATANG
                                        </span>
                                    </div>
                                    <h2 class="text-xl font-black text-stone-900">{{ k.judul }}</h2>
                                    <p v-if="k.deskripsi" class="text-stone-500 mt-2 leading-relaxed text-sm">{{ k.deskripsi }}</p>
                                </div>

                                <!-- Date -->
                                <div class="flex flex-wrap items-center gap-4 mt-5 pt-5 border-t border-stone-100">
                                    <div class="flex items-center gap-2 text-sm font-bold text-stone-600">
                                        <Calendar class="w-4 h-4 text-amber-500 shrink-0" />
                                        {{ formatDate(k.tanggal) }}
                                    </div>
                                    <div class="flex items-center gap-2 text-sm font-bold text-amber-600">
                                        <Clock class="w-4 h-4 shrink-0" />
                                        {{ formatTime(k.tanggal) }} WIB
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <!-- ═══ Past Events ═══ -->
                <section v-if="past.length > 0">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex items-center gap-2 px-4 py-2 bg-stone-100 border border-stone-200 rounded-full">
                            <CheckCircle2 class="w-4 h-4 text-stone-500" />
                            <span class="text-xs font-black text-stone-500 uppercase tracking-widest">Selesai</span>
                        </div>
                        <span class="text-xs text-stone-400 font-bold">{{ past.length }} Kegiatan</span>
                    </div>

                    <div class="space-y-3">
                        <div v-for="k in past" :key="k.id"
                             class="bg-white/70 rounded-2xl border border-stone-100 p-5 flex items-center gap-5 opacity-70 hover:opacity-100 transition-opacity">

                            <!-- Thumbnail -->
                            <div class="w-14 h-14 rounded-xl overflow-hidden bg-stone-100 shrink-0">
                                <img v-if="k.gambar" :src="'/storage/' + k.gambar" class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <CheckCircle2 class="w-6 h-6 text-stone-300" />
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="font-black text-stone-700 truncate">{{ k.judul }}</h3>
                                <div class="flex items-center gap-2 mt-1 text-xs text-stone-400 font-medium">
                                    <Calendar class="w-3 h-3 shrink-0" />
                                    {{ formatDate(k.tanggal) }} · {{ formatTime(k.tanggal) }}
                                </div>
                            </div>

                            <!-- Badge -->
                            <span class="shrink-0 px-2.5 py-1 bg-stone-100 text-stone-400 border border-stone-200 rounded-lg text-[9px] font-black uppercase tracking-wider">
                                SELESAI
                            </span>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </HomeLayout>
</template>

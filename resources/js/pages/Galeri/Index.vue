<script setup lang="ts">
import HomeLayout from '@/layouts/HomeLayout.vue';
import { ref, computed } from 'vue';
import { X } from 'lucide-vue-next';

const props = defineProps<{
    galeri: Array<any>;
}>();

const selectedImage = ref<any>(null);

const formatDate = (dateStr: string) =>
    new Intl.DateTimeFormat('id-ID', { dateStyle: 'long' }).format(new Date(dateStr));
</script>

<template>
    <HomeLayout title="Galeri Komunitas">
        <div class="min-h-screen bg-amber-50/30 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-16">
                    <p class="text-xs font-black text-amber-600 uppercase tracking-[0.3em] mb-4">Dokumentasi Komunitas</p>
                    <h1 class="text-5xl font-black text-stone-900 leading-tight">Galeri <span class="text-amber-600">Warga</span></h1>
                    <p class="text-stone-500 font-medium mt-4 text-lg max-w-xl mx-auto">Abadikan setiap momen kebersamaan warga Perumahan Suryo Green Village.</p>
                </div>

                <!-- Empty state -->
                <div v-if="galeri.length === 0" class="bg-white rounded-[3rem] border border-amber-100 p-24 text-center shadow-sm">
                    <div class="text-6xl mb-4">📷</div>
                    <h3 class="text-xl font-black text-stone-700 mb-2">Galeri Masih Kosong</h3>
                    <p class="text-stone-400">Foto-foto komunitas akan ditampilkan di sini.</p>
                </div>

                <!-- Masonry-style Grid -->
                <div v-else class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
                    <div v-for="item in galeri" :key="item.id"
                         @click="selectedImage = item"
                         class="break-inside-avoid cursor-pointer group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 bg-white border border-amber-50">
                        <img :src="'/storage/' + item.gambar"
                             :alt="item.judul"
                             class="w-full object-cover group-hover:scale-[1.03] transition-transform duration-500" />
                        <!-- Caption overlay -->
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <p class="text-white font-black text-sm">{{ item.judul }}</p>
                            <p v-if="item.deskripsi" class="text-white/70 text-xs mt-0.5 line-clamp-1">{{ item.deskripsi }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lightbox -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="selectedImage"
                     @click.self="selectedImage = null"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
                    <button @click="selectedImage = null" class="absolute top-5 right-5 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                        <X class="w-5 h-5 text-white" />
                    </button>
                    <div class="max-w-4xl max-h-[90vh] w-full flex flex-col items-center gap-4">
                        <img :src="'/storage/' + selectedImage.gambar"
                             :alt="selectedImage.judul"
                             class="max-h-[75vh] w-auto rounded-2xl object-contain shadow-2xl" />
                        <div class="text-center">
                            <p class="text-white font-black text-lg">{{ selectedImage.judul }}</p>
                            <p v-if="selectedImage.deskripsi" class="text-white/60 text-sm mt-1">{{ selectedImage.deskripsi }}</p>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </HomeLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

<script setup lang="ts">
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    title?: string;
}>();
</script>

<template>
    <div class="min-h-screen bg-[#FCFAF2] font-sans text-stone-900 selection:bg-amber-100 selection:text-amber-900">
        <Head :title="title ? `${title} - Komunitas Suryo Green Village` : 'Suryo Green Village - Portal Warga'" />
        
        <!-- Navigation -->
        <Navbar />

        <!-- Page Content -->
        <main class="relative">
            <slot />
        </main>

        <!-- Footer -->
        <Footer />

        <!-- Floating Action Button for Important Info -->
        <a href="#" 
           class="fixed bottom-6 right-6 z-40 bg-amber-600 text-white p-4 rounded-2xl shadow-2xl hover:bg-amber-700 hover:scale-110 active:scale-95 transition-all"
           aria-label="Info Warga">
            <svg class="w-6 h-6 fill-none stroke-current stroke-2" viewBox="0 0 24 24">
                <path d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                <path d="M12 8v4" />
                <path d="M12 16h.01" />
            </svg>
        </a>
    </div>
</template>

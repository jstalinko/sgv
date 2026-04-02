<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Menu, X, Calendar, CreditCard, DollarSign, Image as ImageIcon, Info, Bell, LogIn, LogOut } from 'lucide-vue-next';
import type { SharedData } from '@/types';

const isMenuOpen = ref(false);
const page = usePage<SharedData>();
const websetting = page.props.websetting;
const navigation = [
    { name: 'Kegiatan', href: '/jadwal', icon: Calendar },
    { name: 'Iuran', href: route('iuran.index'), icon: CreditCard },
    { name: 'Kas', href: route('kas.index'), icon: DollarSign },
    { name: 'Galeri', href: route('galeri.index'), icon: ImageIcon },
    { name: 'Tentang', href: '#tentang', icon: Info },
];
</script>

<template>
    <nav class="sticky top-0 z-50 bg-[#FCFAF2]/80 backdrop-blur-md border-b border-amber-100/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <Link href="/" class="flex flex-col">
                        <span class="text-xl font-extrabold text-amber-800 tracking-tight leading-none uppercase">{{ websetting.website_name }}</span>
                        <span class="text-[9px] text-amber-600/80 font-bold tracking-[0.2em] uppercase mt-1">Portal Komunitas Warga</span>
                    </Link>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <Link v-for="item in navigation" 
                          :key="item.name" 
                          :href="item.href"
                          class="text-sm font-bold text-stone-600 hover:text-amber-600 transition-colors duration-200">
                        {{ item.name }}
                    </Link>
                    <div class="flex items-center space-x-4 border-l border-amber-100 pl-8 ml-4">
                      
                        
                      

                        <Link href="/laporan" class="bg-amber-600 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-amber-200 hover:bg-amber-700 transition-all active:scale-95">
                            Hubungi
                        </Link>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="isMenuOpen = !isMenuOpen" 
                            class="text-stone-500 hover:text-amber-600 p-2 rounded-lg transition-colors focus:outline-none">
                        <Menu v-if="!isMenuOpen" class="w-6 h-6" />
                        <X v-else class="w-6 h-6" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div v-show="isMenuOpen" class="md:hidden bg-[#FCFAF2] border-b border-amber-100 animate-in slide-in-from-top-4 duration-300">
            <div class="px-4 pt-4 pb-6 space-y-2">
                <Link v-for="item in navigation" 
                      :key="item.name" 
                      :href="item.href"
                      @click="isMenuOpen = false"
                      class="flex items-center space-x-3 px-4 py-3 text-base font-bold text-stone-700 hover:bg-amber-50 hover:text-amber-600 rounded-xl transition-all">
                    <component :is="item.icon" class="w-5 h-5 opacity-70" />
                    <span>{{ item.name }}</span>
                </Link>
                <div class="pt-4 px-4 space-y-3 border-t border-amber-50 pt-4">
                  

                    <Link href="/laporan" class="flex justify-center bg-amber-600 text-white px-5 py-3 rounded-xl text-sm font-bold shadow-lg shadow-amber-200 hover:bg-amber-700 w-full transition-all">
                        Kontak Pengurus
                    </Link>
                </div>
            </div>
        </div>
    </nav>
</template>

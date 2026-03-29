<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Facebook, Instagram, Mail, MapPin, Phone, Users } from 'lucide-vue-next';

const currentYear = new Date().getFullYear();

const footerLinks = [
    {
        title: 'Layanan Warga',
        links: [
            { name: 'Kegiatan Sosial', href: '#kegiatan' },
            { name: 'Iuran Bulanan', href: '#iuran' },
            { name: 'Laporan Kas', href: '#kas' },
        ],
    },
    {
        title: 'Komunitas',
        links: [
            { name: 'Tentang Lingkungan', href: '#tentang' },
            { name: 'Galeri Warga', href: '#galeri' },
            { name: 'Informasi Pengurus', href: '/contact' },
        ],
    },
];
</script>

<template>
    <footer class="bg-stone-900 text-amber-50/80 pt-16 pb-8 border-t border-amber-900/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <!-- Brand Info -->
                <div class="space-y-6">
                    <div>
                        <Link href="/" class="flex flex-col">
                            <span class="text-2xl font-black text-amber-500 tracking-tight uppercase leading-none">Suryo Green Village</span>
                            <span class="text-[10px] text-amber-200/50 font-bold tracking-[0.2em] uppercase mt-1">Portal Komunitas Warga</span>
                        </Link>
                    </div>
                    <p class="text-sm leading-relaxed text-stone-400">
                        Media informasi dan komunikasi resmi warga Perumahan Suryo Green Village. Mewujudkan lingkungan yang harmonis, aman, dan saling peduli.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="p-2 bg-stone-800 rounded-xl hover:bg-amber-600 hover:text-white transition-all">
                            <Instagram class="w-5 h-5" />
                        </a>
                        <a href="#" class="p-2 bg-stone-800 rounded-xl hover:bg-amber-600 hover:text-white transition-all">
                            <Users class="w-5 h-5" />
                        </a>
                    </div>
                </div>

                <!-- Footer Links -->
                <div v-for="section in footerLinks" :key="section.title">
                    <h3 class="font-bold text-amber-500 mb-6 uppercase tracking-widest text-xs">{{ section.title }}</h3>
                    <ul class="space-y-4">
                        <li v-for="link in section.links" :key="link.name">
                            <Link :href="link.href" class="text-sm hover:text-amber-400 transition-colors">
                                {{ link.name }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="font-bold text-amber-500 mb-6 uppercase tracking-widest text-xs">Sekretariat</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start space-x-3 text-sm">
                            <MapPin class="w-5 h-5 shrink-0 text-amber-500 mt-0.5" />
                            <span>Balai Warga SGV, Jl. Utama Perumahan Suryo Green Village, No. 01</span>
                        </li>
                        <li class="flex items-center space-x-3 text-sm">
                            <Phone class="w-5 h-5 text-amber-500" />
                            <span>Emergency: +62 812-3333-4444</span>
                        </li>
                        <li class="flex items-center space-x-3 text-sm">
                            <Mail class="w-5 h-5 text-amber-500" />
                            <span>warga@suryogreenvillage.id</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-10 border-t border-stone-800 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-xs text-stone-500">
                    &copy; {{ currentYear }} SURYO GREEN VILLAGE. 
                </p>
                <div class="flex space-x-6 text-xs text-stone-500 font-medium">
                  <p>PROVIDED BY:</p>
                  <a href="https://javara.digital" target="_blank">
                    <img src="https://javara.digital/javaradigital-logo.png" alt="Javara Digital" class="h-6">
                  </a>
                </div>
            </div>
        </div>
    </footer>
</template>

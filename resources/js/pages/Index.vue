<script setup lang="ts">
import HomeLayout from '@/layouts/HomeLayout.vue';
import { ArrowRight, Heart, ShieldCheck, Gem, Users, Calendar, DollarSign, Image as ImageIcon } from 'lucide-vue-next';

const communityFeatures = [
    {
        title: 'Kegiatan Warga',
        description: 'Jadwal rutin gotong royong, senam bersama, dan pertemuan warga untuk mempererat silaturahmi.',
        icon: Calendar,
    },
    {
        title: 'Transparansi Kas',
        description: 'Laporan iuran bulanan dan pengeluaran kas Warga Perumahaan yang dapat diakses oleh seluruh warga secara terbuka.',
        icon: DollarSign,
    },
    {
        title: 'Keamanan Bersama',
        description: 'Sistem keamanan CCTV 24 Jam dan terpadu dengan koordinasi petugas dan partisipasi aktif warga menjaga lingkungan.',
        icon: ShieldCheck,
    },
    {
        title: 'Fasilitas Sosial',
        description: 'Informasi penggunaan fasilitas bersama seperti Balai Warga, Taman Bermain, dan sarana olahraga.',
        icon: Users,
    },
];
</script>

<template>
    <HomeLayout title="Beranda">
        <!-- Hero Section -->
        <section class="relative pt-12 pb-20 md:pt-24 md:pb-32 overflow-hidden bg-[#FCFAF2]">
            <!-- Decorative Gold/Cream Background -->
            <div class="absolute top-0 right-0 w-2/5 h-full bg-amber-50/50 -z-10 rounded-l-[120px] hidden lg:block"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <!-- Hero Content (Left) -->
                    <div class="space-y-8 max-w-2xl">
                        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-100/50 border border-amber-200 text-amber-700 text-[10px] font-bold uppercase tracking-[0.2em]">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Portal Resmi Warga SGV</span>
                        </div>
                        
                        <h1 class="text-5xl md:text-7xl font-black text-stone-900 leading-[1.1] tracking-tight">
                            Harmony dalam <span class="text-amber-600 italic">Kebersamaan</span>.
                        </h1>
                        
                        <p class="text-lg md:text-xl text-stone-600 leading-relaxed max-w-xl">
                            Selamat datang di portal informasi Perumahan Suryo Green Village. Tempat di mana kenyamanan bertemu dengan kehangatan bertangga.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <a href="#kegiatan" class="flex items-center justify-center space-x-2 bg-amber-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-xl shadow-amber-200 hover:bg-amber-700 hover:-translate-y-1 transition-all active:scale-95 group">
                                <span>Informasi Warga</span>
                                <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                            </a>
                            <a href="#kas" class="flex items-center justify-center space-x-2 bg-white text-stone-700 border-2 border-amber-100 px-8 py-4 rounded-2xl font-bold text-lg hover:border-amber-400 hover:bg-amber-50/30 transition-all active:scale-95">
                                <span>Laporan Kas</span>
                            </a>
                        </div>

                        <!-- Community Stats -->
                        <div class="grid grid-cols-3 gap-8 pt-6 border-t border-amber-100">
                            <div>
                                <p class="text-3xl font-black text-stone-900">150+</p>
                                <p class="text-sm text-stone-500 font-medium tracking-tight">Kepala Keluarga</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-stone-900">12</p>
                                <p class="text-sm text-stone-500 font-medium tracking-tight">Program Aktif</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-stone-900">0%</p>
                                <p class="text-sm text-stone-500 font-medium tracking-tight">Tingkat Insiden</p>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Image (Right) -->
                    <div class="relative group">
                        <!-- Golden Glow -->
                        <div class="absolute -inset-4 bg-amber-200/20 rounded-[40px] blur-3xl group-hover:bg-amber-200/30 transition-colors"></div>
                        <div class="relative rounded-[40px] overflow-hidden shadow-2xl shadow-amber-200/50 aspect-[4/5] md:aspect-[4/3] lg:aspect-square ring-4 ring-white">
                            <img src="/banner.jpg" 
                                 alt="Lingkungan Suryo Green Village" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                 loading="lazy" />
                            
                            <!-- Floating Community Card -->
                            <div class="absolute bottom-6 left-6 right-6 p-6 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-amber-50 space-y-3">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-amber-100 rounded-lg">
                                        <Heart class="w-5 h-5 text-amber-600 fill-amber-600" />
                                    </div>
                                    <div>
                                        <p class="text-stone-900 font-black leading-none">Gotong Royong</p>
                                        <p class="text-[10px] text-amber-600 font-bold uppercase tracking-wider mt-1">Minggu Depan • 07:00 WIB</p>
                                    </div>
                                </div>
                                <p class="text-xs text-stone-500 leading-relaxed italic border-t border-stone-100 pt-3">
                                    "Membangun lingkungan asri dimulai dari kepedulian setiap warga."
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Community Features Section -->
        <section id="kegiatan" class="py-24 bg-white relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                    <div class="max-w-2xl space-y-4">
                        <h2 class="text-xs font-bold text-amber-600 uppercase tracking-[0.3em]">Program & Layanan</h2>
                        <p class="text-4xl md:text-5xl font-black text-stone-900 tracking-tight">Kehidupan Bertangga yang <span class="text-amber-500">Berdaya</span>.</p>
                    </div>
                    <div class="hidden md:block">
                        <a href="/jadwal" class="text-sm font-bold text-stone-400 hover:text-amber-600 flex items-center space-x-2 transition-colors">
                            <span>Lihat Kalender Lengkap</span>
                            <ArrowRight class="w-4 h-4" />
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="feature in communityFeatures" 
                         :key="feature.title" 
                         class="bg-[#FCFAF2] p-8 rounded-[40px] border border-transparent hover:border-amber-200 hover:bg-white hover:shadow-2xl hover:shadow-amber-100/50 transition-all duration-500 group">
                        <div class="w-14 h-14 bg-white text-amber-600 rounded-2xl flex items-center justify-center mb-6 shadow-sm group-hover:bg-amber-600 group-hover:text-white transition-all duration-300">
                            <component :is="feature.icon" class="w-7 h-7" />
                        </div>
                        <h3 class="text-xl font-black text-stone-900 mb-4 group-hover:text-amber-700 transition-colors">{{ feature.title }}</h3>
                        <p class="text-stone-500 leading-relaxed text-sm">
                            {{ feature.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Social Section / Gallery Preview -->
        <section id="galeri" class="py-24 bg-[#FCFAF2] overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="order-2 lg:order-1 relative">
                        <!-- Overlapping Images Placeholder -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-4 pt-12">
                                <div class="aspect-square rounded-3xl bg-amber-100/50 border-2 border-white shadow-xl overflow-hidden relative group">
                                    <div class="absolute inset-0 bg-stone-200 animate-pulse" v-if="false"></div>
                                    <div class="absolute inset-0 flex items-center justify-center text-amber-300 p-8">
                                        <ImageIcon class="w-12 h-12" />
                                    </div>
                                </div>
                                <div class="aspect-[3/4] rounded-3xl bg-amber-200 shadow-xl overflow-hidden"></div>
                            </div>
                            <div class="space-y-4">
                                <div class="aspect-[3/4] rounded-3xl bg-stone-800 shadow-xl overflow-hidden flex items-end p-6">
                                    <p class="text-white font-bold text-sm">Dokumentasi QR <br/>Warga 2025</p>
                                </div>
                                <div class="aspect-square rounded-3xl bg-amber-50 border-2 border-amber-100 shadow-xl"></div>
                            </div>
                        </div>
                    </div>
                    <div class="order-1 lg:order-2 space-y-8">
                        <div class="p-2 bg-amber-100 rounded-lg inline-block">
                            <Gem class="w-6 h-6 text-amber-600" />
                        </div>
                        <h2 class="text-4xl md:text-5xl font-black text-stone-900 tracking-tight">Galeri Kebersamaan</h2>
                        <p class="text-lg text-stone-500 leading-relaxed">
                            Momen-momen berharga saat gotong royong, perayaan hari besar, hingga keceriaan anak-anak di taman bermain. Koleksi dokumentasi visual perjalanan komunitas kita.
                        </p>
                        <div class="pt-4 flex flex-wrap gap-4">
                             <a href="/galeri" class="bg-amber-600 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-amber-700 transition-colors shadow-lg shadow-amber-200">
                                Jelajahi Foto
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section / Announcement -->
        <section id="tentang" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-stone-900 rounded-[50px] p-8 md:p-16 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-amber-600/10 rounded-full blur-[100px]"></div>
                    <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-stone-800 rounded-full blur-[100px]"></div>
                    
                    <div class="relative z-10 text-center max-w-3xl mx-auto space-y-8">
                        <div class="flex justify-center">
                            <span class="px-4 py-1.5 rounded-full bg-stone-800 border border-stone-700 text-amber-500 text-xs font-bold uppercase tracking-widest">Wajib Diketahui</span>
                        </div>
                        <h2 class="text-3xl md:text-5xl font-black text-white leading-tight">
                            Mari Ikut Serta Membangun <br />
                            Suryo Green Village <br />
                            <span class="text-amber-500">Menjadi Lebih Baik</span>
                        </h2>
                        <p class="text-stone-400 text-lg leading-relaxed">
                            Punya saran atau laporan terkait lingkungan? Tim pengurus siap membantu dan menampung aspirasi setiap warga untuk kenyamanan bersama.
                        </p>
                        <div class="flex flex-wrap justify-center gap-4 pt-4">
                            <a href="https://wa.me/6281233334444" 
                               class="bg-amber-600 text-white px-10 py-5 rounded-3xl font-bold text-lg hover:bg-amber-700 hover:scale-105 transition-all shadow-xl shadow-amber-900/40">
                                WhatsApp Pengurus
                            </a>
                            <a href="/pengumuman" class="bg-stone-800 text-white border-2 border-stone-700 px-10 py-5 rounded-3xl font-bold text-lg hover:bg-stone-700 transition-all">
                                Lihat Pengumuman
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </HomeLayout>
</template>
e>
<script setup>
import HomeLayout from '@/layouts/HomeLayout.vue';
import { humanizeNumber } from '../lib/utils';
import { ArrowRight, Heart, ShieldCheck, Gem, Users, Calendar, DollarSign, Image as ImageIcon } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
const prop = defineProps({props:Object});
const kegiatan = prop.props.kegiatan;
const kegiatanMendatang = prop.props.kegiatan_mendatang;
const warga = prop.props.warga;
const galeris = prop.props.galeri;
const totalKas = prop.props.total_kas;

const websetting = usePage().props.websetting;
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
                            <span>Portal Resmi Warga {{ websetting.website_name }}</span>
                        </div>
                        
                        <h1 class="text-5xl md:text-7xl font-black text-stone-900 leading-[1.1] tracking-tight">
                            Harmony dalam <span class="text-amber-600 italic">Kebersamaan</span>.
                        </h1>
                        
                        <p class="text-lg md:text-xl text-stone-600 leading-relaxed max-w-xl">
                            Selamat datang di portal informasi Perumahan {{ websetting.website_name }}. Tempat di mana kenyamanan bertemu dengan kehangatan bertangga.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <a href="/iuran" class="flex items-center justify-center space-x-2 bg-amber-600 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-xl shadow-amber-200 hover:bg-amber-700 hover:-translate-y-1 transition-all active:scale-95 group">
                                <span>Iuran Warga</span>
                                <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                            </a>
                            <a href="/kas" class="flex items-center justify-center space-x-2 bg-white text-stone-700 border-2 border-amber-100 px-8 py-4 rounded-2xl font-bold text-lg hover:border-amber-400 hover:bg-amber-50/30 transition-all active:scale-95">
                                <span>Laporan Kas</span>
                            </a>
                        </div>

                        <!-- Community Stats -->
                        <div class="grid grid-cols-3 gap-8 pt-6 border-t border-amber-100">
                            <div>
                                <p class="text-3xl font-black text-stone-900">{{ warga }}</p>
                                <p class="text-sm text-stone-500 font-medium tracking-tight">Rumah</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-stone-900">{{ kegiatan }}</p>
                                <p class="text-sm text-stone-500 font-medium tracking-tight">Kegiatan</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-stone-900">{{ humanizeNumber(totalKas) }}</p>
                                <p class="text-sm text-stone-500 font-medium tracking-tight">Total Kas</p>
                            </div>
                            
                        </div>
                    </div>

                    <!-- Hero Image (Right) -->
                    <div class="relative group">
                        <!-- Golden Glow -->
                        <div class="absolute -inset-4 bg-amber-200/20 rounded-[40px] blur-3xl group-hover:bg-amber-200/30 transition-colors"></div>
                        <div class="relative rounded-[40px] overflow-hidden shadow-2xl shadow-amber-200/50 aspect-[4/5] md:aspect-[4/3] lg:aspect-square ring-4 ring-white">
                            <img src="/banner.jpg" 
                                 :alt="'Lingkungan ' + websetting.website_name" 
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

        <!-- Galeri Slider Marquee -->
        <section v-if="galeris && galeris.length > 0" class="py-8 bg-white overflow-hidden w-full relative group shadow-sm z-10 border-b border-stone-100">
            <!-- Fade Edges -->
            <div class="absolute left-0 top-0 w-24 md:w-48 h-full bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
            <div class="absolute right-0 top-0 w-24 md:w-48 h-full bg-gradient-to-l from-white to-transparent z-10 pointer-events-none"></div>
            
            <div class="flex flex-nowrap shrink-0 overflow-hidden w-full">
                <!-- Track 1 -->
                <div class="flex flex-nowrap gap-4 md:gap-8 items-center min-w-full animate-marquee hover:[animation-play-state:paused] group-hover:[animation-play-state:paused] pr-4 md:pr-8">
                    <div v-for="g in galeris" :key="'a-'+g.id" class="relative w-60 h-40 md:w-80 md:h-56 rounded-3xl overflow-hidden shrink-0 shadow-lg cursor-pointer transform hover:-translate-y-2 transition-all duration-300">
                        <img :src="'/storage/' + g.gambar" :alt="g.judul" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-4 md:p-6 pt-12">
                            <p class="text-white font-black text-sm md:text-base leading-tight truncate drop-shadow-md">{{ g.judul }}</p>
                        </div>
                    </div>
                </div>
                <!-- Track 2 (Clone for infinite effect) -->
                <div class="flex flex-nowrap gap-4 md:gap-8 items-center min-w-full animate-marquee hover:[animation-play-state:paused] group-hover:[animation-play-state:paused] pr-4 md:pr-8" aria-hidden="true">
                    <div v-for="g in galeris" :key="'b-'+g.id" class="relative w-60 h-40 md:w-80 md:h-56 rounded-3xl overflow-hidden shrink-0 shadow-lg cursor-pointer transform hover:-translate-y-2 transition-all duration-300">
                        <img :src="'/storage/' + g.gambar" :alt="g.judul" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-4 md:p-6 pt-12">
                            <p class="text-white font-black text-sm md:text-base leading-tight truncate drop-shadow-md">{{ g.judul }}</p>
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
                        <p class="text-4xl md:text-5xl font-black text-stone-900 tracking-tight">Kehidupan Bertetangga yang <span class="text-amber-500">Berdaya</span>.</p>
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
                    <!-- Photo Grid -->
                    <div class="order-2 lg:order-1 relative">
                        <!-- Has photos from DB -->
                        <div v-if="galeris && galeris.length > 0" class="grid grid-cols-2 gap-4">
                            <!-- Column A — offset down -->
                            <div class="space-y-4 pt-10">
                                <div class="aspect-square rounded-3xl overflow-hidden shadow-xl ring-4 ring-white group">
                                    <img :src="'/storage/' + galeris[0].gambar" :alt="galeris[0].judul"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                </div>
                                <div v-if="galeris[2]" class="aspect-[3/4] rounded-3xl overflow-hidden shadow-xl ring-4 ring-white group">
                                    <img :src="'/storage/' + galeris[2].gambar" :alt="galeris[2].judul"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                </div>
                                <div v-else class="aspect-[3/4] rounded-3xl bg-amber-100 ring-4 ring-white shadow-xl"></div>
                            </div>
                            <!-- Column B -->
                            <div class="space-y-4">
                                <div v-if="galeris[1]" class="aspect-[3/4] rounded-3xl overflow-hidden shadow-xl ring-4 ring-white group">
                                    <img :src="'/storage/' + galeris[1].gambar" :alt="galeris[1].judul"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                </div>
                                <div v-else class="aspect-[3/4] rounded-3xl bg-stone-200 ring-4 ring-white shadow-xl"></div>
                                <div v-if="galeris[3]" class="aspect-square rounded-3xl overflow-hidden shadow-xl ring-4 ring-white group">
                                    <img :src="'/storage/' + galeris[3].gambar" :alt="galeris[3].judul"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                </div>
                                <div v-else class="aspect-square rounded-3xl bg-amber-50 border-2 border-amber-100 ring-4 ring-white shadow-xl"></div>
                            </div>
                        </div>

                        <!-- Empty State - no photos yet -->
                        <div v-else class="grid grid-cols-2 gap-4">
                            <div class="space-y-4 pt-10">
                                <div class="aspect-square rounded-3xl bg-amber-100/50 border-2 border-white shadow-xl flex items-center justify-center">
                                    <ImageIcon class="w-10 h-10 text-amber-300" />
                                </div>
                                <div class="aspect-[3/4] rounded-3xl bg-amber-200/40 shadow-xl ring-4 ring-white"></div>
                            </div>
                            <div class="space-y-4">
                                <div class="aspect-[3/4] rounded-3xl bg-stone-200/50 shadow-xl ring-4 ring-white"></div>
                                <div class="aspect-square rounded-3xl bg-amber-50 border-2 border-amber-100 shadow-xl ring-4 ring-white"></div>
                            </div>
                        </div>

                        <!-- Photo count badge -->
                        <div v-if="galeris && galeris.length > 4"
                             class="absolute bottom-4 right-4 px-4 py-2 bg-stone-900/80 backdrop-blur-sm text-white text-xs font-black rounded-full">
                            +{{ galeris.length - 4 }} Foto Lainnya
                        </div>
                    </div>

                    <!-- Text Content -->
                    <div class="order-1 lg:order-2 space-y-8">
                        <div class="p-2 bg-amber-100 rounded-lg inline-block">
                            <Gem class="w-6 h-6 text-amber-600" />
                        </div>
                        <h2 class="text-4xl md:text-5xl font-black text-stone-900 tracking-tight">Galeri Kebersamaan</h2>
                        <p class="text-lg text-stone-500 leading-relaxed">
                            Momen-momen berharga saat gotong royong, perayaan hari besar, hingga keceriaan anak-anak di taman bermain. Koleksi dokumentasi visual perjalanan komunitas kita.
                        </p>
                        <div v-if="galeris && galeris.length > 0" class="flex items-center gap-3">
                            <div class="flex -space-x-2">
                                <img v-for="g in galeris.slice(0,3)" :key="g.id"
                                     :src="'/storage/' + g.gambar"
                                     class="w-8 h-8 rounded-full object-cover ring-2 ring-white" />
                            </div>
                            <p class="text-sm font-bold text-stone-500">
                                <span class="text-stone-900">{{ galeris.length }}</span> foto telah diunggah
                            </p>
                        </div>
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
                            {{ websetting.website_name }} <br />
                            <span class="text-amber-500">Menjadi Lebih Baik</span>
                    </h2>
                        <p class="text-stone-400 text-lg leading-relaxed">
                            Punya saran atau laporan terkait lingkungan? Tim pengurus siap membantu dan menampung aspirasi setiap warga untuk kenyamanan bersama.
                        </p>
                        <div class="flex flex-wrap justify-center gap-4 pt-4">
                            <a :href="websetting.contact_center" 
                               class="bg-amber-600 text-white px-10 py-5 rounded-3xl font-bold text-lg hover:bg-amber-700 hover:scale-105 transition-all shadow-xl shadow-amber-900/40">
                                WhatsApp Pengurus
                            </a>
                            <a href="/galeri" class="bg-stone-800 text-white border-2 border-stone-700 px-10 py-5 rounded-3xl font-bold text-lg hover:bg-stone-700 transition-all">
                                Lihat Galeri
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </HomeLayout>
</template>

<style scoped>
@keyframes marquee {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-100%);
    }
}
.animate-marquee {
    animation: marquee 30s linear infinite;
}
</style>

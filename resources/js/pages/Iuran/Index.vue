<script setup lang="ts">
import HomeLayout from '@/layouts/HomeLayout.vue';
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { Check, X, ChevronLeft, ChevronRight, ChevronDown, Search, CreditCard, User, Home, FileText, Image as ImageIcon } from 'lucide-vue-next';
import html2canvas from 'html2canvas';

const props = defineProps<{
    warga: Array<any>;
    tahun: number;
    amount: number;
    can_edit: boolean;
}>();
const websetting = usePage().props.websetting;
const currentMonth = new Date().getMonth() + 1;
const currentYear = new Date().getFullYear();

const isCurrentMonth = (monthId: number) => {
    return props.tahun === currentYear && monthId === currentMonth;
};

const months = [
// ... (keep current months)
    { id: 1, name: 'Januari' },
    { id: 2, name: 'Februari' },
    { id: 3, name: 'Maret' },
    { id: 4, name: 'April' },
    { id: 5, name: 'Mei' },
    { id: 6, name: 'Juni' },
    { id: 7, name: 'Juli' },
    { id: 8, name: 'Agustus' },
    { id: 9, name: 'September' },
    { id: 10, name: 'Oktober' },
    { id: 11, name: 'November' },
    { id: 12, name: 'Desember' },
];

const activeMobileMonth = ref(currentMonth);

const searchQuery = ref('');
const filteredWarga = computed(() => {
    if (!searchQuery.value) return props.warga;
    const q = searchQuery.value.toLowerCase();
    return props.warga.filter(w => 
        w.nama.toLowerCase().includes(q) || 
        w.no_rumah.toLowerCase().includes(q)
    );
});

const isPaid = (wargaId: number, monthId: number) => {
    const wargaObj = props.warga.find(w => w.id === wargaId);
    if (!wargaObj) return false;
    return wargaObj.iurans.some((i: any) => i.bulan === monthId);
};

const togglePayment = (wargaId: number, monthId: number) => {
    router.post(route('iuran.toggle'), {
        warga_id: wargaId,
        bulan: monthId,
        tahun: props.tahun
    }, {
        preserveScroll: true,
    });
};

const changeYear = (offset: number) => {
    router.get(route('iuran.index'), { tahun: props.tahun + offset });
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
};

// ── EXPORT LOGIC ────────────────────────────────────────────────────────────────
const generateExportText = () => {
    const monthName = months.find(m => m.id === currentMonth)?.name.toUpperCase();
    let result = `IURAN PERUMAHAN SGV BULAN ${monthName}\n\n`;
    
    props.warga.forEach((w, index) => {
        const isPaidStatus = isPaid(w.id, currentMonth);
        const check = isPaidStatus ? '✅' : '';
        result += `${index + 1}. ${w.nama} (${w.no_rumah}) ${check}\n`;
    });
    
    return result;
};

const exportToText = async () => {
    const text = generateExportText();
    try {
        await navigator.clipboard.writeText(text);
        alert('Teks berhasil disalin ke clipboard!');
    } catch (err) {
        console.error('Failed to copy text: ', err);
        alert('Gagal menyalin teks ke clipboard.');
    }
};

const isExporting = ref(false);

const exportToImage = async () => {
    isExporting.value = true;
    
    // Slight delay to ensure v-if element renders
    setTimeout(async () => {
        const el = document.getElementById('export-image-container');
        if (el) {
            try {
                const canvas = await html2canvas(el, { 
                    backgroundColor: '#ffffff', 
                    scale: 2,
                    logging: false
                });
                
                const url = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.href = url;
                link.download = `Iuran_SGV_Bulan_${currentMonth}_${currentYear}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (error) {
                console.error('Error generating image:', error);
                alert('Gagal membuat gambar export.');
            } finally {
                isExporting.value = false;
            }
        }
    }, 100);
};

</script>

<template>
    <HomeLayout title="Iuran Warga">
        <div class="py-12 bg-amber-50/30 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <h1 class="text-4xl font-black text-stone-900 tracking-tight">Iuran Bulanan <span class="text-amber-600">Warga</span></h1>
                        <p class="text-stone-500 font-medium">Monitoring pembayaran iuran rutin perumahan {{ websetting.website_name }}.</p>
                    </div>
                    
                    <div class="flex items-center bg-white p-2 rounded-2xl shadow-sm border border-amber-100">
                        <button @click="changeYear(-1)" class="p-2 hover:bg-amber-50 rounded-xl transition-colors">
                            <ChevronLeft class="w-5 h-5 text-stone-400" />
                        </button>
                        <span class="px-6 text-xl font-black text-stone-800">{{ tahun }}</span>
                        <button @click="changeYear(1)" class="p-2 hover:bg-amber-50 rounded-xl transition-colors">
                            <ChevronRight class="w-5 h-5 text-stone-400" />
                        </button>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-amber-50 flex items-center space-x-4">
                        <div class="p-3 bg-amber-100 rounded-2xl text-amber-600">
                            <CreditCard class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-amber-600 uppercase tracking-widest">Besar Iuran</p>
                            <p class="text-2xl font-black text-stone-900">{{ formatCurrency(amount) }} / Bln</p>
                        </div>
                    </div>
                    <!-- Add more stats if needed -->
                </div>

                <!-- Table Controls -->
                <div class="bg-white rounded-t-[2.5rem] border-x border-t border-amber-100 p-4 md:p-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex flex-col md:flex-row w-full md:w-auto gap-4">
                        <div class="relative w-full md:w-80">
                            <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-stone-400" />
                            <input v-model="searchQuery" 
                                   type="text" 
                                   placeholder="Cari Nama atau No. Rumah..." 
                                   class="w-full pl-12 pr-4 py-3 bg-stone-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 font-medium text-stone-700" />
                        </div>
                        
                        <!-- Mobile Month Selector -->
                        <div class="md:hidden w-full relative">
                            <select v-model="activeMobileMonth" class="w-full pl-4 pr-10 py-3 bg-amber-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 font-bold text-amber-700 appearance-none">
                                <option v-for="m in months" :key="m.id" :value="m.id">Bulan: {{ m.name }}</option>
                            </select>
                            <ChevronDown class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-amber-600 pointer-events-none" />
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto pb-2 md:pb-0 hide-scrollbar">
                        <button @click="exportToText" class="whitespace-nowrap px-4 md:px-5 py-3 bg-stone-100 text-stone-600 font-bold rounded-2xl hover:bg-stone-200 transition-colors flex items-center gap-2 text-sm md:text-base">
                            <FileText class="w-5 h-5 text-stone-500" /> Salin Teks
                        </button>
                        <button @click="exportToImage" :disabled="isExporting" class="whitespace-nowrap px-4 md:px-5 py-3 bg-amber-100 text-amber-700 font-bold rounded-2xl hover:bg-amber-200 transition-colors flex items-center gap-2 shadow-sm shadow-amber-200/50 text-sm md:text-base">
                            <span v-if="isExporting" class="w-5 h-5 border-4 border-amber-600 border-t-transparent rounded-full animate-spin"></span>
                            <ImageIcon v-else class="w-5 h-5 text-amber-600" /> Export Gambar
                        </button>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="bg-white rounded-b-[2.5rem] border border-amber-100 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-stone-50 border-y border-amber-100">
                                    <th class="p-4 md:p-6 text-[10px] md:text-xs font-black text-stone-500 uppercase tracking-widest sticky left-0 bg-stone-50 z-10 border-r border-amber-50">Rumah</th>
                                    <th class="p-4 md:p-6 text-[10px] md:text-xs font-black text-stone-500 uppercase tracking-widest sticky left-[80px] md:left-[150px] bg-stone-50 z-10 border-r border-amber-100">Nama</th>
                                    <th v-for="month in months" :key="month.id" 
                                        class="p-4 text-xs font-black uppercase tracking-widest text-center min-w-[80px] md:min-w-[100px]"
                                        :class="[
                                            isCurrentMonth(month.id) ? 'text-amber-600 bg-amber-50/50' : 'text-stone-500',
                                            month.id !== activeMobileMonth ? 'hidden md:table-cell' : ''
                                        ]">
                                        {{ month.name.substring(0, 3) }}
                                        <div v-if="isCurrentMonth(month.id)" class="text-[8px] mt-1 text-amber-500">INI BULAN INI</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-50">
                                <tr v-for="w in filteredWarga" :key="w.id" class="hover:bg-amber-50/30 transition-colors group">
                                    <td class="p-4 md:p-6 text-xs md:text-sm font-black text-amber-700 sticky left-0 bg-white group-hover:bg-amber-50 transition-colors z-10 border-r border-amber-50">
                                        <div class="whitespace-nowrap">{{w.blok}} / {{ w.no_rumah }}</div>
                                    </td>
                                    <td class="p-4 md:p-6 text-xs md:text-sm font-bold text-stone-700 sticky left-[80px] md:left-[150px] bg-white group-hover:bg-amber-50 transition-colors z-10 border-r border-amber-100 uppercase">
                                        <div class="whitespace-nowrap">{{ w.nama }}</div>
                                    </td>
                                    <td v-for="month in months" :key="month.id" 
                                        class="p-4 text-center"
                                        :class="[
                                            isCurrentMonth(month.id) ? 'bg-amber-400' : '',
                                            month.id !== activeMobileMonth ? 'hidden md:table-cell' : ''
                                        ]">
                                        <button v-if="can_edit"
                                                @click="togglePayment(w.id, month.id)" 
                                                class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto transition-all active:scale-90"
                                                :class="isPaid(w.id, month.id) 
                                                    ? 'bg-amber-600 text-white shadow-lg shadow-amber-200' 
                                                    : 'bg-stone-100 text-stone-300 hover:bg-stone-200'">
                                            <Check v-if="isPaid(w.id, month.id)" class="w-6 h-6 stroke-[3]" />
                                            <X v-else class="w-4 h-4 opacity-30 text-red-600" />
                                        </button>
                                        <div v-else class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto"
                                             :class="isPaid(w.id, month.id) ? 'text-amber-600 bg-amber-100' : 'text-red-600'">
                                            <Check v-if="isPaid(w.id, month.id)" class="w-6 h-6 stroke-[3]" />
                                            <X v-else class="w-4 h-4 opacity-30 text-red-600" />
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredWarga.length === 0">
                                    <td colspan="14" class="p-20 text-center space-y-4">
                                        <div class="p-4 bg-stone-50 rounded-full inline-block">
                                            <User class="w-12 h-12 text-stone-200" />
                                        </div>
                                        <p class="text-stone-400 font-bold">Data warga tidak ditemukan.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Legend -->
                <div class="mt-8 flex items-center space-x-6">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-amber-600"></div>
                        <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Sudah Bayar</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-stone-200"></div>
                        <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Belum Bayar</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden DOM Element for Image Export -->
        <div v-if="isExporting" class="fixed top-[-9999px] left-[-9999px] z-[-1]">
            <div id="export-image-container" class="bg-white p-12 w-[600px] border border-stone-200 font-sans">
                <!-- Watermark Logo SGV (Optional context) -->
                <div class="text-center mb-8 border-b-2 border-stone-100 pb-6">
                    <h2 class="text-3xl font-black text-stone-900 tracking-tight">IURAN PERUMAHAN {{ websetting.website_name }}</h2>
                    <p class="text-xl font-bold text-amber-600 mt-2">
                        BULAN {{ months.find(m => m.id === currentMonth)?.name.toUpperCase() }} {{ currentYear }}
                    </p>
                </div>
                
                <div class="flex flex-col space-y-3">
                    <div v-for="(w, index) in warga" :key="w.id" class="flex justify-between items-center text-lg">
                        <span class="font-medium text-stone-800">
                            {{ index + 1 }}. {{ w.nama }} ({{ w.no_rumah }})
                        </span>
                        <span class="text-xl" v-if="isPaid(w.id, currentMonth)">✅</span>
                    </div>
                </div>
                <br>
                <p>
                    Iuran bisa di bayarkan melalui transfer ke rekening
                    <b>BNI - 1802199289 an Febi Gardian Setiawan.
</b>
Paling lambat tgl 12 nggih Bpk Ibu 🙏
                </p>
                <div class="mt-10 pt-6 border-t font-medium border-stone-100 text-stone-400 text-sm text-center">
                    Diunduh dari Sistem Informasi Perumahan {{ websetting.website_name }}
                </div>
            </div>
        </div>

    </HomeLayout>
</template>

<style scoped>
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}
.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #fbbf24;
    border-radius: 10px;
}
.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #d97706;
}
</style>

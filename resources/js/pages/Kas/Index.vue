<script setup lang="ts">
import HomeLayout from '@/layouts/HomeLayout.vue';
import { ref, watch } from 'vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import { TrendingUp, TrendingDown, Wallet, Calendar, Plus, X, Search, Filter, ArrowUpRight, ArrowDownLeft, Image as ImageIcon, FileText } from 'lucide-vue-next';

const props = defineProps<{
    kas: {
        data: Array<any>;
        links: Array<any>;
    };
    totalMasuk: number;
    totalKeluar: number;
    saldo: number;
    can_edit: boolean;
    filterBulan: number | null;
    filterTahun: number | null;
    availableYears: number[];
}>();

const websetting = usePage().props.websetting as any;

const showAddExpense = ref(false);

const form = useForm({
    kategori: '',
    jumlah: '',
    keterangan: '',
    tanggal: new Date().toISOString().substr(0, 10),
    bukti: null as File | null,
});

const handleFileUpload = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.bukti = target.files[0];
    }
};

const submitExpense = () => {
    form.post(route('kas.store'), {
        onSuccess: () => {
            showAddExpense.value = false;
            form.reset();
        },
    });
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
};

const formatDate = (dateStr: string) => {
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(new Date(dateStr));
};

// ── Filter ────────────────────────────────────────────────────────────────────
const selectedBulan = ref<string>(props.filterBulan ? String(props.filterBulan) : '');
const selectedTahun = ref<string>(props.filterTahun ? String(props.filterTahun) : '');

const bulanNames = [
    '', 'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
];

const applyFilter = () => {
    router.get(route('kas.index'), {
        bulan: selectedBulan.value || undefined,
        tahun: selectedTahun.value || undefined,
    }, { preserveState: true, replace: true });
};

const resetFilter = () => {
    selectedBulan.value = '';
    selectedTahun.value = '';
    router.get(route('kas.index'), { all: 1 }, { preserveState: false, replace: true });
};

const isFiltered = () => !!selectedBulan.value || !!selectedTahun.value;

const isExporting = ref(false);

const exportToImage = async () => {
    isExporting.value = true;
    
    setTimeout(async () => {
        const el = document.getElementById('export-kas-container');
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
                link.download = `Kas_SGV_${selectedBulan.value || 'All'}_${selectedTahun.value || 'All'}.png`;
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

const exportToPdf = async () => {
    isExporting.value = true;
    
    setTimeout(async () => {
        const el = document.getElementById('export-kas-container');
        if (el) {
            try {
                const canvas = await html2canvas(el, { 
                    backgroundColor: '#ffffff', 
                    scale: 2,
                    logging: false,
                    useCORS: true,
                    allowTaint: true,
                    width: el.scrollWidth,
                    height: el.scrollHeight,
                });
                
                const imgData = canvas.toDataURL('image/png');
                // A4 dimensions in mm
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4',
                });
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                const margin = 10;
                const contentWidth = pageWidth - margin * 2;
                const contentHeight = (canvas.height / canvas.width) * contentWidth;
                
                if (contentHeight <= pageHeight - margin * 2) {
                    pdf.addImage(imgData, 'PNG', margin, margin, contentWidth, contentHeight);
                } else {
                    // Scale to fit page height if needed
                    const scale = Math.min(contentWidth / (canvas.width / 2), (pageHeight - margin * 2) / (canvas.height / 2));
                    const w = (canvas.width / 2) * scale;
                    const h = (canvas.height / 2) * scale;
                    pdf.addImage(imgData, 'PNG', margin, margin, w, h);
                }
                pdf.save(`Kas_${selectedBulan.value || 'All'}_${selectedTahun.value || 'All'}.pdf`);
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Gagal membuat PDF export.');
            } finally {
                isExporting.value = false;
            }
        }
    }, 100);
};
</script>

<template>
    <HomeLayout title="Kas Perumahan">
        <div class="py-12 bg-amber-50/30 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <h1 class="text-4xl font-black text-stone-900 tracking-tight">Kas <span class="text-amber-600">Perumahan</span></h1>
                        <p class="text-stone-500 font-medium">Laporan keuangan transparan warga Perumahan {{ websetting.website_name }}.</p>
                    </div>
                    
                    <button v-if="can_edit"
                            @click="showAddExpense = true" 
                            class="bg-stone-900 text-white px-8 py-4 rounded-2xl font-bold flex items-center justify-center space-x-3 hover:bg-stone-800 transition-all active:scale-95 shadow-xl shadow-stone-200">
                        <Plus class="w-6 h-6" />
                        <span>Catat Pengeluaran</span>
                    </button>
                </div>

                <!-- Financial Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <!-- Total Balance -->
                    <div class="bg-amber-600 p-8 rounded-[3rem] shadow-2xl shadow-amber-200 text-white relative overflow-hidden group">
                        <div class="absolute -right-8 -top-8 w-32 h-32 bg-amber-500 rounded-full blur-3xl opacity-50 group-hover:scale-150 transition-transform"></div>
                        <div class="relative z-10 space-y-4">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-amber-500/50 backdrop-blur-sm rounded-2xl">
                                    <Wallet class="w-6 h-6" />
                                </div>
                                <span class="text-xs font-black uppercase tracking-widest opacity-80">Saldo Kas Saat Ini</span>
                            </div>
                            <p class="text-4xl font-black tracking-tight">{{ formatCurrency(saldo) }}</p>
                        </div>
                    </div>

                    <!-- Total Income -->
                    <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-amber-100 flex items-center space-x-6">
                        <div class="p-4 bg-green-50 rounded-3xl text-green-600">
                            <ArrowUpRight class="w-8 h-8" />
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-bold text-stone-400 uppercase tracking-widest">Total Pemasukkan</p>
                            <p class="text-2xl font-black text-green-600">{{ formatCurrency(totalMasuk) }}</p>
                        </div>
                    </div>

                    <!-- Total Expense -->
                    <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-amber-100 flex items-center space-x-6">
                        <div class="p-4 bg-red-50 rounded-3xl text-red-600">
                            <ArrowDownLeft class="w-8 h-8" />
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-bold text-stone-400 uppercase tracking-widest">Total Pengeluaran</p>
                            <p class="text-2xl font-black text-red-600">{{ formatCurrency(totalKeluar) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="bg-white rounded-[3rem] border border-amber-100 overflow-hidden shadow-sm">
                    <div class="p-8 border-b border-amber-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <h3 class="text-xl font-black text-stone-900">Riwayat Transaksi</h3>
                            <span v-if="isFiltered()" class="px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-black rounded-full uppercase tracking-wider">
                                Filter Aktif
                            </span>
                        </div>
                        <!-- Filter Controls -->
                        <div class="flex flex-wrap items-center gap-3">
                            <select v-model="selectedBulan"
                                    class="h-10 px-4 bg-stone-50 border-none rounded-xl text-sm font-bold text-stone-600 focus:ring-2 focus:ring-amber-500 cursor-pointer">
                                <option value="">Semua Bulan</option>
                                <option v-for="i in 12" :key="i" :value="String(i)">{{ bulanNames[i] }}</option>
                            </select>
                            <select v-model="selectedTahun"
                                    class="h-10 px-4 bg-stone-50 border-none rounded-xl text-sm font-bold text-stone-600 focus:ring-2 focus:ring-amber-500 cursor-pointer">
                                <option value="">Semua Tahun</option>
                                <option v-for="y in availableYears" :key="y" :value="String(y)">{{ y }}</option>
                            </select>
                            <button @click="applyFilter"
                                    class="h-10 px-5 bg-stone-900 text-white rounded-xl text-sm font-bold hover:bg-stone-800 transition-colors flex items-center gap-2">
                                <Filter class="w-3.5 h-3.5" /> Terapkan
                            </button>
                            <button v-if="isFiltered()" @click="resetFilter"
                                    class="h-10 px-4 bg-stone-100 text-stone-500 rounded-xl text-sm font-bold hover:bg-stone-200 transition-colors">
                                Reset
                            </button>

                            <div class="flex items-center gap-2 border-l border-amber-100 pl-3 ml-1">
                                <button @click="exportToImage" :disabled="isExporting" class="h-10 px-4 bg-amber-100 text-amber-700 rounded-xl text-sm font-bold hover:bg-amber-200 transition-colors flex items-center gap-2 shadow-sm shadow-amber-200/50">
                                    <span v-if="isExporting" class="w-3.5 h-3.5 border-2 border-amber-600 border-t-transparent rounded-full animate-spin"></span>
                                    <ImageIcon v-else class="w-3.5 h-3.5" /> <span class="hidden md:inline">Image</span>
                                </button>
                                <button @click="exportToPdf" :disabled="isExporting" class="h-10 px-4 bg-rose-100 text-rose-700 rounded-xl text-sm font-bold hover:bg-rose-200 transition-colors flex items-center gap-2 shadow-sm shadow-rose-200/50">
                                    <span v-if="isExporting" class="w-3.5 h-3.5 border-2 border-rose-600 border-t-transparent rounded-full animate-spin"></span>
                                    <FileText v-else class="w-3.5 h-3.5" /> <span class="hidden md:inline">PDF</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-stone-50 border-b border-amber-50">
                                <tr>
                                    <th class="p-6 text-xs font-black text-stone-400 uppercase tracking-widest">Tanggal</th>
                                    <th class="p-6 text-xs font-black text-stone-400 uppercase tracking-widest">Kategori</th>
                                    <th class="p-6 text-xs font-black text-stone-400 uppercase tracking-widest">Keterangan</th>
                                    <th class="p-6 text-xs font-black text-stone-400 uppercase tracking-widest text-center">Bukti</th>
                                    <th class="p-6 text-xs font-black text-stone-400 uppercase tracking-widest text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-50">
                                <tr v-for="t in kas.data" :key="t.id" class="hover:bg-amber-50/50 transition-colors">
                                    <td class="p-6">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-stone-100 rounded-lg text-stone-500">
                                                <Calendar class="w-4 h-4" />
                                            </div>
                                            <span class="text-sm font-bold text-stone-600">{{ formatDate(t.tanggal) }}</span>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <span class="px-3 py-1 bg-stone-100 rounded-full text-[10px] font-black uppercase tracking-widest text-stone-500 border border-stone-200">
                                            {{ t.kategori }}
                                        </span>
                                    </td>
                                    <td class="p-6">
                                        <p class="text-sm font-medium text-stone-600">{{ t.keterangan || '-' }}</p>
                                    </td>
                                    <td class="p-6 text-center">
                                        <a v-if="t.bukti" :href="'/storage/' + t.bukti" target="_blank" class="inline-block group/btn">
                                            <div class="w-12 h-12 rounded-xl border-2 border-amber-100 p-1 group-hover/btn:border-amber-400 transition-colors">
                                                <img :src="'/storage/' + t.bukti" class="w-full h-full object-cover rounded-lg" />
                                            </div>
                                        </a>
                                        <span v-else class="text-[10px] font-black text-stone-300 uppercase italic tracking-widest">No Proof</span>
                                    </td>
                                    <td class="p-6 text-right">
                                        <span :class="t.type === 'masuk' ? 'text-green-600 font-black' : 'text-red-600 font-bold'" class="text-lg tracking-tight">
                                            {{ t.type === 'masuk' ? '+' : '-' }} {{ formatCurrency(t.jumlah) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="kas.data.length === 0">
                                    <td colspan="4" class="p-20 text-center text-stone-400 font-bold">Belum ada transaksi tercatat.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Expense Modal -->
        <div v-if="showAddExpense" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-sm animate-in fade-in duration-300">
            <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl p-10 relative animate-in zoom-in-95 duration-300">
                <button @click="showAddExpense = false" class="absolute right-8 top-8 p-2 hover:bg-stone-50 rounded-xl">
                    <X class="w-6 h-6 text-stone-400" />
                </button>

                <h2 class="text-3xl font-black text-stone-900 mb-8 tracking-tight">Catat <span class="text-red-500">Pengeluaran</span></h2>

                <form @submit.prevent="submitExpense" class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-stone-400 uppercase tracking-widest mb-3">Kategori</label>
                        <select v-model="form.kategori" 
                                class="w-full px-5 py-4 bg-stone-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 font-bold text-stone-700"
                                required>
                            <option value="">Pilih Kategori...</option>
                            <option value="Listrik">Listrik</option>
                            <option value="Air">Air</option>
                            <option value="Kebersihan">Kebersihan</option>
                            <option value="Keamanan">Keamanan</option>
                            <option value="Kegiatan Warga">Kegiatan Warga</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-stone-400 uppercase tracking-widest mb-3">Jumlah (Rp)</label>
                            <input v-model="form.jumlah" 
                                   type="number" 
                                   class="w-full px-5 py-4 bg-stone-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 font-bold text-stone-700"
                                   placeholder="0"
                                   required />
                        </div>
                        <div>
                            <label class="block text-xs font-black text-stone-400 uppercase tracking-widest mb-3">Tanggal</label>
                            <input v-model="form.tanggal" 
                                   type="date" 
                                   class="w-full px-5 py-4 bg-stone-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 font-bold text-stone-700 uppercase"
                                   required />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-stone-400 uppercase tracking-widest mb-3">Keterangan</label>
                        <textarea v-model="form.keterangan" 
                                  class="w-full px-5 py-4 bg-stone-50 border-none rounded-2xl focus:ring-2 focus:ring-amber-500 font-bold text-stone-700" 
                                  rows="2"
                                  placeholder="Contoh: Pembayaran listrik balai warga januari"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-stone-400 uppercase tracking-widest mb-3">Bukti Transaksi (Pilihan)</label>
                        <div class="relative group/upload">
                            <input type="file" 
                                   @change="handleFileUpload"
                                   accept="image/*"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                            <div class="w-full px-5 py-4 bg-stone-50 border-2 border-dashed border-stone-200 rounded-2xl flex items-center justify-between group-hover/upload:border-amber-400 transition-colors">
                                <span class="text-sm font-bold text-stone-400">
                                    {{ form.bukti ? form.bukti.name : 'Pilih gambar bukti (JPG/PNG)...' }}
                                </span>
                                <Plus class="w-5 h-5 text-stone-300" />
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-stone-900 text-white py-5 rounded-2xl font-black text-lg hover:bg-stone-800 transition-all active:scale-95 shadow-xl shadow-stone-200 mt-4 flex items-center justify-center space-x-3"
                            :disabled="form.processing">
                        <span v-if="form.processing" class="w-6 h-6 border-4 border-white border-t-transparent rounded-full animate-spin"></span>
                        <span v-else>Simpan Transaksi</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Hidden DOM Element for Export -->
        <div v-if="isExporting" class="fixed top-[-9999px] left-[-9999px] z-[-1]">
            <div id="export-kas-container" style="width: 1050px; padding: 48px; background: #ffffff; font-family: system-ui, -apple-system, sans-serif; box-sizing: border-box;">
                <!-- Header -->
                <div style="text-align: center; margin-bottom: 32px; border-bottom: 2px solid #f5f5f4; padding-bottom: 24px;">
                    <h2 style="font-size: 24px; font-weight: 900; color: #1c1917; letter-spacing: -0.5px; margin: 0;">LAPORAN KAS PERUMAHAN {{ websetting.website_name }}</h2>
                    <p style="font-size: 18px; font-weight: 700; color: #d97706; margin-top: 8px;" v-if="selectedBulan && selectedTahun">
                        PERIODE {{ bulanNames[Number(selectedBulan)].toUpperCase() }} {{ selectedTahun }}
                    </p>
                    <p style="font-size: 18px; font-weight: 700; color: #d97706; margin-top: 8px;" v-else-if="selectedTahun">
                        TAHUN {{ selectedTahun }}
                    </p>
                    <p style="font-size: 18px; font-weight: 700; color: #d97706; margin-top: 8px;" v-else>
                        LAPORAN KESELURUHAN
                    </p>
                </div>
                
                <!-- Financial Summary -->
                <div style="display: flex; gap: 16px; margin-bottom: 32px;">
                    <div style="flex: 1; padding: 16px; background: #fafaf9; border-radius: 12px; text-align: center;">
                        <p style="font-size: 10px; font-weight: 700; color: #a8a29e; text-transform: uppercase; letter-spacing: 1.5px; margin: 0;">Total Pemasukkan</p>
                        <p style="font-size: 18px; font-weight: 900; color: #16a34a; margin: 8px 0 0;">{{ formatCurrency(totalMasuk) }}</p>
                    </div>
                    <div style="flex: 1; padding: 16px; background: #fafaf9; border-radius: 12px; text-align: center;">
                        <p style="font-size: 10px; font-weight: 700; color: #a8a29e; text-transform: uppercase; letter-spacing: 1.5px; margin: 0;">Total Pengeluaran</p>
                        <p style="font-size: 18px; font-weight: 900; color: #dc2626; margin: 8px 0 0;">{{ formatCurrency(totalKeluar) }}</p>
                    </div>
                    <div style="flex: 1; padding: 16px; background: #fffbeb; border-radius: 12px; text-align: center;">
                        <p style="font-size: 10px; font-weight: 700; color: #d97706; text-transform: uppercase; letter-spacing: 1.5px; margin: 0;">Saldo Kas</p>
                        <p style="font-size: 18px; font-weight: 900; color: #1c1917; margin: 8px 0 0;">{{ formatCurrency(saldo) }}</p>
                    </div>
                </div>

                <!-- Transaction Table -->
                <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                    <thead>
                        <tr style="background: #fafaf9; border-top: 1px solid #e7e5e4; border-bottom: 1px solid #e7e5e4;">
                            <th style="width: 18%; padding: 10px 12px; font-size: 10px; font-weight: 900; color: #a8a29e; text-transform: uppercase; letter-spacing: 1.5px; text-align: left;">Tanggal</th>
                            <th style="width: 17%; padding: 10px 12px; font-size: 10px; font-weight: 900; color: #a8a29e; text-transform: uppercase; letter-spacing: 1.5px; text-align: left;">Kategori</th>
                            <th style="width: 40%; padding: 10px 12px; font-size: 10px; font-weight: 900; color: #a8a29e; text-transform: uppercase; letter-spacing: 1.5px; text-align: left;">Keterangan</th>
                            <th style="width: 25%; padding: 10px 12px; font-size: 10px; font-weight: 900; color: #a8a29e; text-transform: uppercase; letter-spacing: 1.5px; text-align: right;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="t in kas.data" :key="'exp-' + t.id" style="border-bottom: 1px solid #f5f5f4;">
                            <td style="padding: 10px 12px; font-size: 13px; font-weight: 700; color: #57534e;">{{ formatDate(t.tanggal) }}</td>
                            <td style="padding: 10px 12px; font-size: 11px; font-weight: 700; color: #78716c; text-transform: uppercase;">{{ t.kategori }}</td>
                            <td style="padding: 10px 12px; font-size: 13px; font-weight: 500; color: #57534e; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ t.keterangan || '-' }}</td>
                            <td style="padding: 10px 12px; font-size: 13px; font-weight: 900; text-align: right;" :style="{ color: t.type === 'masuk' ? '#16a34a' : '#dc2626' }">
                                {{ t.type === 'masuk' ? '+' : '-' }} {{ formatCurrency(t.jumlah) }}
                            </td>
                        </tr>
                        <tr v-if="kas.data.length === 0">
                            <td colspan="4" style="padding: 40px; text-align: center; color: #a8a29e; font-weight: 700;">Tidak ada transaksi.</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Lampiran Bukti Transaksi -->
                <div v-if="kas.data.some(t => t.bukti)" style="margin-top: 40px;">
                    <h3 style="font-size: 18px; font-weight: 900; color: #1c1917; margin-bottom: 24px; border-bottom: 2px solid #f5f5f4; padding-bottom: 8px;">Lampiran Bukti Transaksi</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <template v-for="t in kas.data" :key="'bukti-' + t.id">
                            <div v-if="t.bukti" style="background: #fafaf9; padding: 16px; border-radius: 12px; border: 1px solid #e7e5e4;">
                                <div style="width: 100%; height: 180px; background: #ffffff; border-radius: 8px; border: 1px solid #f5f5f4; margin-bottom: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center; padding: 8px;">
                                    <img :src="'/storage/' + t.bukti" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 6px;" />
                                </div>
                                <div style="text-align: center;">
                                    <p style="font-size: 12px; font-weight: 700; color: #44403c; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">
                                        {{ t.type }} - {{ t.kategori }}
                                    </p>
                                    <p style="font-size: 11px; font-weight: 500; color: #78716c; padding: 0 8px; margin: 4px 0;">
                                        {{ t.keterangan || '-' }}
                                    </p>
                                    <p style="font-size: 13px; font-weight: 900; margin: 4px 0 0;" :style="{ color: t.type === 'masuk' ? '#16a34a' : '#dc2626' }">
                                        {{ formatCurrency(t.jumlah) }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div style="margin-top: 40px; padding-top: 24px; border-top: 1px solid #f5f5f4; color: #a8a29e; font-size: 12px; text-align: center; font-weight: 500;">
                    Diunduh dari Sistem Informasi Perumahan {{ websetting.website_name }} pada {{ new Date().toLocaleString('id-ID') }}
                </div>
            </div>
        </div>
    </HomeLayout>
</template>

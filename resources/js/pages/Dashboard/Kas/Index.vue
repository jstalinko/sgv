<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { 
    Wallet, Calendar, Plus, X, Search, Filter,
    ArrowUpRight, ArrowDownLeft, Pencil, 
    Trash2, MoreHorizontal, Image as ImageIcon,
    FileText, Tag, Banknote
} from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

const props = defineProps<{
    kas: {
        data: Array<any>;
        links: Array<any>;
    };
    totalMasuk: number;
    totalKeluar: number;
    saldo: number;
    filterBulan: number | null;
    filterTahun: number | null;
    availableYears: number[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manajemen Kas',
        href: '/dashboard/kas',
    },
];

// CRUD Logic
const showModal = ref(false);
const editingTransaction = ref<any>(null);

const form = useForm({
    type: 'keluar',
    kategori: '',
    jumlah: '',
    keterangan: '',
    tanggal: new Date().toISOString().substr(0, 10),
    bukti: null as File | null,
});

const openCreateModal = (type: 'masuk' | 'keluar' = 'keluar') => {
    editingTransaction.value = null;
    form.reset();
    form.type = type;
    showModal.value = true;
};

const openEditModal = (transaction: any) => {
    editingTransaction.value = transaction;
    form.type = transaction.type;
    form.kategori = transaction.kategori;
    form.jumlah = transaction.jumlah.toString();
    form.keterangan = transaction.keterangan || '';
    form.tanggal = transaction.tanggal;
    form.bukti = null;
    showModal.value = true;
};

const handleFileUpload = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.bukti = target.files[0];
    }
};

const submit = () => {
    if (editingTransaction.value) {
        // Inertia requirement for multipart/form-data with PUT: use POST and append _method: 'PUT'
        // We use router.post directly to have better control over transform/data
        router.post(route('dashboard.kas.update', editingTransaction.value.id), {
            _method: 'PUT',
            ...form.data(),
            bukti: form.bukti, // Ensure file is included
        }, {
            forceFormData: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
            onBefore: () => form.processing = true,
            onFinish: () => form.processing = false,
        });
    } else {
        form.post(route('dashboard.kas.store'), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    }
};

// Delete Logic
const showDeleteConfirm = ref(false);
const transactionToDelete = ref<any>(null);

const confirmDelete = (transaction: any) => {
    transactionToDelete.value = transaction;
    showDeleteConfirm.value = true;
};

const deleteTransaction = () => {
    if (transactionToDelete.value) {
        router.delete(route('dashboard.kas.destroy', transactionToDelete.value.id), {
            onSuccess: () => {
                showDeleteConfirm.value = false;
                transactionToDelete.value = null;
            },
        });
    }
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
    router.get(route('dashboard.kas.index'), {
        bulan: selectedBulan.value || undefined,
        tahun: selectedTahun.value || undefined,
    }, { preserveState: true, replace: true });
};

const resetFilter = () => {
    selectedBulan.value = '';
    selectedTahun.value = '';
    router.get(route('dashboard.kas.index'), {}, { preserveState: false, replace: true });
};

const isFiltered = () => !!selectedBulan.value || !!selectedTahun.value;
</script>

<template>
    <Head title="Manajemen Kas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-foreground">Arus Kas Perumahan</h1>
                    <p class="text-muted-foreground mt-1 font-medium">Monitoring dan kelola transparansi dana finansial warga.</p>
                </div>
                
                <div class="flex gap-3">
                    <Button @click="openCreateModal('keluar')" variant="outline" class="border-red-200 text-red-600 hover:bg-red-50 gap-2 font-bold rounded-xl px-6">
                        <ArrowDownLeft class="w-4 h-4" />
                        Catat Pengeluaran
                    </Button>
                    <Button @click="openCreateModal('masuk')" class="bg-stone-900 hover:bg-stone-800 text-white gap-2 font-bold rounded-xl px-6 shadow-lg shadow-stone-100">
                        <ArrowUpRight class="w-4 h-4" />
                        Catat Pemasukan
                    </Button>
                </div>
            </div>

            <!-- Financial Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Balance -->
                <div class="bg-amber-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-amber-100 relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500 rounded-full blur-2xl opacity-40 group-hover:scale-150 transition-transform"></div>
                    <div class="relative z-10 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-amber-500/50 backdrop-blur-sm rounded-xl">
                                <Wallet class="w-5 h-5 text-white" />
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80">Saldo Kas Saat Ini</span>
                        </div>
                        <p class="text-3xl font-black tracking-tight">{{ formatCurrency(saldo) }}</p>
                    </div>
                </div>

                <!-- Total Income -->
                <div class="bg-card border rounded-[2.5rem] p-8 shadow-sm flex items-center gap-6">
                    <div class="p-4 bg-green-50 rounded-3xl text-green-600">
                        <ArrowUpRight class="w-8 h-8" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-muted-foreground uppercase tracking-widest">Total Pemasukan</p>
                        <p class="text-xl font-black text-green-600">{{ formatCurrency(totalMasuk) }}</p>
                    </div>
                </div>

                <!-- Total Expense -->
                <div class="bg-card border rounded-[2.5rem] p-8 shadow-sm flex items-center gap-6">
                    <div class="p-4 bg-red-50 rounded-3xl text-red-600">
                        <ArrowDownLeft class="w-8 h-8" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-muted-foreground uppercase tracking-widest">Total Pengeluaran</p>
                        <p class="text-xl font-black text-red-600">{{ formatCurrency(totalKeluar) }}</p>
                    </div>
                </div>
            </div>

            <!-- Transaction Table -->
            <div class="bg-card border rounded-[2.5rem] shadow-sm overflow-hidden mt-8">
                <div class="p-8 border-b bg-muted/30 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-black text-foreground tracking-tight">Riwayat Transaksi Lengkap</h3>
                        <span v-if="isFiltered()" class="px-2.5 py-1 bg-amber-100 text-amber-700 text-[9px] font-black rounded-full uppercase tracking-wider">
                            Filter Aktif
                        </span>
                    </div>
                    <!-- Filter Controls -->
                    <div class="flex flex-wrap items-center gap-2">
                        <select v-model="selectedBulan"
                                class="h-10 px-4 bg-background border border-border rounded-xl text-sm font-bold text-foreground focus:ring-2 focus:ring-amber-500 cursor-pointer">
                            <option value="">Semua Bulan</option>
                            <option v-for="i in 12" :key="i" :value="String(i)">{{ bulanNames[i] }}</option>
                        </select>
                        <select v-model="selectedTahun"
                                class="h-10 px-4 bg-background border border-border rounded-xl text-sm font-bold text-foreground focus:ring-2 focus:ring-amber-500 cursor-pointer">
                            <option value="">Semua Tahun</option>
                            <option v-for="y in availableYears" :key="y" :value="String(y)">{{ y }}</option>
                        </select>
                        <Button @click="applyFilter" variant="outline" class="h-10 px-4 gap-2 rounded-xl border-amber-200 text-amber-700 hover:bg-amber-50 font-bold text-xs">
                            <Filter class="w-3.5 h-3.5" /> Terapkan
                        </Button>
                        <Button v-if="isFiltered()" @click="resetFilter" variant="ghost" class="h-10 px-4 rounded-xl font-bold text-xs text-muted-foreground">
                            Reset
                        </Button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-muted/50 border-b">
                                <th class="p-6 text-[10px] font-black text-muted-foreground uppercase tracking-widest pl-10">Tanggal</th>
                                <th class="p-6 text-[10px] font-black text-muted-foreground uppercase tracking-widest">Kategori</th>
                                <th class="p-6 text-[10px] font-black text-muted-foreground uppercase tracking-widest">Keterangan</th>
                                <th class="p-6 text-[10px] font-black text-muted-foreground uppercase tracking-widest text-center">Bukti</th>
                                <th class="p-6 text-[10px] font-black text-muted-foreground uppercase tracking-widest text-right pr-10">Jumlah</th>
                                <th class="p-6 text-[10px] font-black text-muted-foreground uppercase tracking-widest text-center w-20 px-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="t in kas.data" :key="t.id" class="hover:bg-muted/30 transition-colors group">
                                <td class="p-6 pl-10">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-muted rounded-lg text-muted-foreground">
                                            <Calendar class="w-4 h-4" />
                                        </div>
                                        <span class="text-sm font-bold text-foreground">{{ formatDate(t.tanggal) }}</span>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span class="px-3 py-1 bg-background border border-border rounded-full text-[10px] font-black uppercase tracking-widest text-muted-foreground">
                                        {{ t.kategori }}
                                    </span>
                                </td>
                                <td class="p-6">
                                    <p class="text-sm font-medium text-foreground max-w-md truncate">{{ t.keterangan || '-' }}</p>
                                    <span v-if="t.ref_type === 'App\\Models\\Iuran'" class="text-[9px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-100 flex items-center w-fit mt-1">
                                        LINKED TO IURAN
                                    </span>
                                </td>
                                <td class="p-6 text-center">
                                    <a v-if="t.bukti" :href="'/storage/' + t.bukti" target="_blank" class="inline-block group/btn">
                                        <div class="w-12 h-12 rounded-xl border-2 border-amber-100 p-1 group-hover/btn:border-amber-400 transition-colors">
                                            <img :src="'/storage/' + t.bukti" class="w-full h-full object-cover rounded-lg" />
                                        </div>
                                    </a>
                                    <span v-else class="text-[9px] font-black text-muted-foreground/30 uppercase italic tracking-widest">No Proof</span>
                                </td>
                                <td class="p-6 text-right pr-10">
                                    <div class="flex flex-col items-end">
                                        <span :class="t.type === 'masuk' ? 'text-green-600' : 'text-red-700'" class="text-lg font-black tracking-tight">
                                            {{ t.type === 'masuk' ? '+' : '-' }} {{ formatCurrency(t.jumlah) }}
                                        </span>
                                        <span class="text-[9px] font-black uppercase text-muted-foreground/50 tracking-widest">{{ t.type }}</span>
                                    </div>
                                </td>
                                <td class="p-6 px-8">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" class="h-8 w-8 p-0 hover:bg-amber-50 hover:text-amber-600 rounded-lg">
                                                <MoreHorizontal class="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end" class="w-48 rounded-xl border-amber-100">
                                            <DropdownMenuLabel class="text-[10px] text-muted-foreground uppercase tracking-widest p-3">Opsi Transaksi</DropdownMenuLabel>
                                            <DropdownMenuSeparator />
                                            <template v-if="t.ref_type !== 'App\\Models\\Iuran'">
                                                <DropdownMenuItem @click="openEditModal(t)" class="gap-2 p-3 font-medium cursor-pointer focus:bg-amber-50 focus:text-amber-700">
                                                    <Pencil class="w-4 h-4" /> Edit Transaksi
                                                </DropdownMenuItem>
                                                <DropdownMenuItem @click="confirmDelete(t)" class="gap-2 p-3 font-medium cursor-pointer text-red-600 focus:bg-red-50 focus:text-red-700">
                                                    <Trash2 class="w-4 h-4" /> Hapus Catatan
                                                </DropdownMenuItem>
                                            </template>
                                            <template v-else>
                                                <DropdownMenuItem disabled class="gap-2 p-3 font-medium opacity-50 italic">
                                                    Manage via Iuran Menu
                                                </DropdownMenuItem>
                                            </template>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>
                            <tr v-if="kas.data.length === 0">
                                <td colspan="6" class="p-32 text-center text-muted-foreground font-black uppercase tracking-widest opacity-30">
                                    Belum ada transaksi tercatat.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Dialog v-model:open="showModal">
            <DialogContent class="sm:max-w-[500px] rounded-[2.5rem] p-10 border-amber-100">
                <DialogHeader>
                    <DialogTitle class="text-3xl font-black text-foreground tracking-tight">
                        {{ editingTransaction ? 'Perbarui Transaksi' : (form.type === 'keluar' ? 'Catat Pengeluaran' : 'Tambah Pemasukan') }}
                    </DialogTitle>
                    <DialogDescription class="font-medium text-muted-foreground">
                        Lengkapi rincian keuangan dengan benar untuk laporan transparan warga.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submit" class="space-y-6 mt-4">
                    <div class="grid grid-cols-2 gap-4 bg-muted/30 p-2 rounded-2xl border">
                        <Button type="button" 
                                @click="form.type = 'keluar'"
                                :variant="form.type === 'keluar' ? 'default' : 'ghost'"
                                class="rounded-xl font-black text-xs uppercase transition-all"
                                :class="form.type === 'keluar' ? 'bg-red-600 hover:bg-red-700 shadow-md shadow-red-100' : 'text-muted-foreground'">
                            PENGELUARAN
                        </Button>
                        <Button type="button" 
                                @click="form.type = 'masuk'"
                                :variant="form.type === 'masuk' ? 'default' : 'ghost'"
                                class="rounded-xl font-black text-xs uppercase transition-all"
                                :class="form.type === 'masuk' ? 'bg-green-600 hover:bg-green-700 shadow-md shadow-green-100' : 'text-muted-foreground'">
                            PEMASUKAN
                        </Button>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Kategori</Label>
                            <div class="relative">
                                <Tag class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                                <select v-model="form.kategori" class="w-full pl-10 pr-4 h-12 bg-muted/30 border-none rounded-xl font-bold appearance-none focus:ring-2 focus:ring-amber-500" required>
                                    <option value="">Pilih...</option>
                                    <template v-if="form.type === 'keluar'">
                                        <option value="Listrik">Listrik</option>
                                        <option value="Air">Air</option>
                                        <option value="Kebersihan">Kebersihan</option>
                                        <option value="Keamanan">Keamanan</option>
                                        <option value="Kegiatan Warga">Kegiatan Warga</option>
                                        <option value="Perbaikan">Perbaikan</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </template>
                                    <template v-else>
                                        <option value="Donasi">Donasi</option>
                                        <option value="Hibah">Hibah</option>
                                        <option value="Sewa Fasilitas">Sewa Fasilitas</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Tanggal</Label>
                            <div class="relative">
                                <Calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                                <Input v-model="form.tanggal" type="date" class="pl-10 h-12 bg-muted/30 border-none rounded-xl font-bold uppercase" required />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Jumlah (IDR)</Label>
                        <div class="relative">
                            <Banknote class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                            <Input v-model="form.jumlah" type="number" placeholder="0" class="pl-10 h-12 bg-muted/30 border-none rounded-xl font-bold text-lg" required />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Keterangan</Label>
                        <div class="relative">
                            <FileText class="absolute left-3 top-4 w-4 h-4 text-muted-foreground" />
                            <textarea v-model="form.keterangan" rows="2" placeholder="Detail transaksi..." class="w-full pl-10 pr-4 py-3 bg-muted/30 border-none rounded-xl font-medium focus:ring-2 focus:ring-amber-500 resize-none"></textarea>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Bukti File (Pilihan)</Label>
                        <div class="relative group/upload">
                            <input type="file" @change="handleFileUpload" accept="image/*,.heic,.heif" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                            <div class="w-full px-5 py-4 bg-muted/10 border-2 border-dashed border-muted rounded-2xl flex items-center justify-between group-hover/upload:border-amber-400 transition-colors">
                                <div class="flex items-center gap-3">
                                    <ImageIcon class="w-5 h-5 text-muted-foreground" />
                                    <span class="text-xs font-bold text-muted-foreground truncate max-w-[200px]">
                                        {{ form.bukti ? form.bukti.name : (editingTransaction?.bukti ? 'Ganti bukti lama' : 'Unggah gambar/foto bukti...') }}
                                    </span>
                                </div>
                                <Plus class="w-4 h-4 text-muted-foreground" />
                            </div>
                        </div>
                        <p class="text-[10px] text-muted-foreground ml-1 opacity-70 italic">Mendukung JPG, PNG, HEIC, WEBP — Maks. 10MB — Kompresi otomatis ke WebP</p>
                    </div>

                    <DialogFooter class="pt-6">
                        <Button type="button" variant="ghost" @click="showModal = false" class="rounded-xl font-bold h-12 px-6">Batal</Button>
                        <Button type="submit" 
                                :disabled="form.processing"
                                class="bg-stone-900 hover:bg-stone-800 text-white rounded-xl font-black h-12 px-10 shadow-lg shadow-stone-100 min-w-[160px]">
                            {{ form.processing ? 'Menyimpan...' : (editingTransaction ? 'Simpan Perubahan' : 'Catat Transaksi') }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation -->
        <Dialog v-model:open="showDeleteConfirm">
            <DialogContent class="sm:max-w-[400px] rounded-[2.5rem] p-8 border-red-100">
                <DialogHeader>
                    <DialogTitle class="text-2xl font-black text-foreground tracking-tight">Hapus Transaksi?</DialogTitle>
                    <DialogDescription class="font-bold text-muted-foreground pt-2">
                        Data keuangan akan dihapus permanen dari riwayat kas perumahan.
                    </DialogDescription>
                </DialogHeader>
                <div class="bg-red-50 p-4 rounded-2xl border border-red-100 flex flex-col items-center justify-center my-4">
                    <span class="text-xs font-black text-red-400 uppercase tracking-widest">Jumlah Terhapus</span>
                    <span class="text-2xl font-black text-red-600 tracking-tight">{{ formatCurrency(transactionToDelete?.jumlah || 0) }}</span>
                </div>
                <DialogFooter class="pt-6 gap-2">
                    <Button variant="ghost" @click="showDeleteConfirm = false" class="rounded-xl font-bold h-12 flex-1">Batal</Button>
                    <Button @click="deleteTransaction" class="bg-red-600 hover:bg-red-700 text-white rounded-xl font-block h-12 flex-1 shadow-lg shadow-red-100">
                        Ya, Hapus Data
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<style scoped>
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
}
</style>

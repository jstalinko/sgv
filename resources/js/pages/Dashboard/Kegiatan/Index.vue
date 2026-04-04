<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import {
    Plus, Search, Pencil, Trash2, MoreHorizontal,
    Calendar, ImageIcon, FileText, Clock
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle
} from '@/components/ui/dialog';
import {
    DropdownMenu, DropdownMenuContent, DropdownMenuItem,
    DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';

const websetting = usePage().props.websetting as any;

const props = defineProps<{
    kegiatan: {
        data: Array<any>;
        links: Array<any>;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Manajemen Kegiatan', href: '/dashboard/kegiatan' }];

const searchQuery = ref('');
const filteredKegiatan = computed(() => {
    if (!searchQuery.value) return props.kegiatan.data;
    const q = searchQuery.value.toLowerCase();
    return props.kegiatan.data.filter(k => k.judul.toLowerCase().includes(q));
});

const showModal = ref(false);
const editingItem = ref<any>(null);

const form = useForm({
    judul: '',
    deskripsi: '',
    tanggal: new Date().toISOString().substr(0, 16),
    gambar: null as File | null,
});

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    form.tanggal = new Date().toISOString().substr(0, 16);
    showModal.value = true;
};

const openEditModal = (item: any) => {
    editingItem.value = item;
    form.judul = item.judul;
    form.deskripsi = item.deskripsi || '';
    form.tanggal = item.tanggal?.substring(0, 16) || '';
    form.gambar = null;
    showModal.value = true;
};

const handleFileUpload = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) form.gambar = target.files[0];
};

const submit = () => {
    if (editingItem.value) {
        router.post(route('dashboard.kegiatan.update', editingItem.value.id), {
            _method: 'PUT',
            ...form.data(),
            gambar: form.gambar,
        }, {
            forceFormData: true,
            onSuccess: () => { showModal.value = false; form.reset(); },
            onBefore: () => form.processing = true,
            onFinish: () => form.processing = false,
        });
    } else {
        form.post(route('dashboard.kegiatan.store'), {
            onSuccess: () => { showModal.value = false; form.reset(); },
        });
    }
};

const showDeleteConfirm = ref(false);
const itemToDelete = ref<any>(null);

const confirmDelete = (item: any) => { itemToDelete.value = item; showDeleteConfirm.value = true; };
const deleteItem = () => {
    if (itemToDelete.value) {
        router.delete(route('dashboard.kegiatan.destroy', itemToDelete.value.id), {
            onSuccess: () => { showDeleteConfirm.value = false; itemToDelete.value = null; },
        });
    }
};

const formatDateTime = (dateStr: string) => {
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'long', timeStyle: 'short' }).format(new Date(dateStr));
};

const isUpcoming = (tanggal: string) => new Date(tanggal) > new Date();
</script>

<template>
    <Head title="Manajemen Kegiatan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-foreground">Manajemen Kegiatan</h1>
                    <p class="text-muted-foreground mt-1">Kelola program dan aktivitas komunitas {{ websetting.website_name }}.</p>
                </div>
                <Button @click="openCreateModal" class="bg-amber-600 hover:bg-amber-700 text-white gap-2 px-6 py-6 rounded-xl shadow-lg shadow-amber-100 font-bold">
                    <Plus class="w-5 h-5" /> Tambah Kegiatan
                </Button>
            </div>

            <div class="bg-card border rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-muted/30 flex items-center gap-4">
                    <div class="relative flex-1 max-w-sm">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                        <Input v-model="searchQuery" placeholder="Cari kegiatan..." class="pl-10 h-11 bg-background border-none focus-visible:ring-amber-500 rounded-xl" />
                    </div>
                </div>

                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-muted/50 border-b">
                            <th class="p-4 pl-6 text-[10px] font-black text-muted-foreground uppercase tracking-widest w-20">Foto</th>
                            <th class="p-4 text-[10px] font-black text-muted-foreground uppercase tracking-widest">Judul & Deskripsi</th>
                            <th class="p-4 text-[10px] font-black text-muted-foreground uppercase tracking-widest">Tanggal</th>
                            <th class="p-4 text-[10px] font-black text-muted-foreground uppercase tracking-widest text-center">Status</th>
                            <th class="p-4 pr-6 text-[10px] font-black text-muted-foreground uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="k in filteredKegiatan" :key="k.id" class="hover:bg-muted/30 transition-colors">
                            <td class="p-4 pl-6">
                                <div class="w-16 h-16 rounded-xl border border-border overflow-hidden bg-muted flex items-center justify-center">
                                    <img v-if="k.gambar" :src="'/storage/' + k.gambar" class="w-full h-full object-cover" />
                                    <ImageIcon v-else class="w-6 h-6 text-muted-foreground opacity-30" />
                                </div>
                            </td>
                            <td class="p-4 max-w-xs">
                                <p class="font-bold text-foreground">{{ k.judul }}</p>
                                <p class="text-xs text-muted-foreground mt-1 line-clamp-2">{{ k.deskripsi || '-' }}</p>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2 text-xs font-medium text-foreground">
                                    <Calendar class="w-3.5 h-3.5 text-amber-600" />
                                    {{ formatDateTime(k.tanggal) }}
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <span :class="isUpcoming(k.tanggal)
                                    ? 'bg-blue-50 text-blue-700 border-blue-100'
                                    : 'bg-stone-100 text-stone-500 border-stone-200'"
                                    class="px-2.5 py-1 rounded-lg text-[10px] font-black border uppercase tracking-wider">
                                    {{ isUpcoming(k.tanggal) ? 'Mendatang' : 'Selesai' }}
                                </span>
                            </td>
                            <td class="p-4 pr-6 text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" class="h-8 w-8 p-0 hover:bg-amber-50 hover:text-amber-600 rounded-lg">
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end" class="w-44 rounded-xl border-amber-100">
                                        <DropdownMenuLabel class="text-[10px] text-muted-foreground uppercase tracking-widest p-3">Opsi</DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem @click="openEditModal(k)" class="gap-2 p-3 font-medium cursor-pointer focus:bg-amber-50 focus:text-amber-700">
                                            <Pencil class="w-4 h-4" /> Edit Kegiatan
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="confirmDelete(k)" class="gap-2 p-3 font-medium cursor-pointer text-red-600 focus:bg-red-50 focus:text-red-700">
                                            <Trash2 class="w-4 h-4" /> Hapus Kegiatan
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </td>
                        </tr>
                        <tr v-if="filteredKegiatan.length === 0">
                            <td colspan="5" class="p-24 text-center text-muted-foreground">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="p-4 bg-muted rounded-full"><Calendar class="w-8 h-8 opacity-20" /></div>
                                    <p class="font-medium">Belum ada kegiatan terdaftar.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Dialog v-model:open="showModal">
            <DialogContent class="sm:max-w-[480px] rounded-[2rem] p-8 border-amber-100">
                <DialogHeader>
                    <DialogTitle class="text-2xl font-black">{{ editingItem ? 'Edit Kegiatan' : 'Tambah Kegiatan' }}</DialogTitle>
                    <DialogDescription>Isi detail program atau aktivitas komunitas.</DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submit" class="space-y-5 mt-4">
                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Judul Kegiatan</Label>
                        <Input v-model="form.judul" placeholder="Contoh: Kerja Bakti Bersama" class="h-12 bg-muted/30 border-none rounded-xl font-bold" required />
                    </div>
                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Tanggal & Waktu</Label>
                        <div class="relative">
                            <Clock class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                            <Input v-model="form.tanggal" type="datetime-local" class="pl-10 h-12 bg-muted/30 border-none rounded-xl font-bold" required />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Deskripsi</Label>
                        <div class="relative">
                            <FileText class="absolute left-3 top-4 w-4 h-4 text-muted-foreground" />
                            <textarea v-model="form.deskripsi" rows="3" placeholder="Keterangan singkat tentang kegiatan..."
                                class="w-full pl-10 pr-4 py-3 bg-muted/30 border-none rounded-xl font-medium focus:ring-2 focus:ring-amber-500 resize-none text-sm"></textarea>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Foto Kegiatan (Pilihan)</Label>
                        <div class="relative group/upload cursor-pointer">
                            <input type="file" @change="handleFileUpload" accept="image/*,.heic,.heif" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                            <div class="px-5 py-4 bg-muted/10 border-2 border-dashed border-muted rounded-2xl flex items-center gap-3 group-hover/upload:border-amber-400 transition-colors">
                                <ImageIcon class="w-5 h-5 text-muted-foreground shrink-0" />
                                <span class="text-xs font-bold text-muted-foreground truncate">
                                    {{ form.gambar ? (form.gambar as File).name : (editingItem?.gambar ? 'Ganti foto lama' : 'Pilih foto kegiatan...') }}
                                </span>
                            </div>
                        </div>
                        <p class="text-[10px] text-muted-foreground ml-1 opacity-70 italic">Mendukung JPG, PNG, HEIC, WEBP — Maks. 10MB — Kompresi otomatis ke WebP</p>
                    </div>
                    <DialogFooter class="pt-2">
                        <Button type="button" variant="ghost" @click="showModal = false" class="rounded-xl font-bold h-11">Batal</Button>
                        <Button type="submit" :disabled="form.processing" class="bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-black h-11 px-8 shadow-lg shadow-amber-100 min-w-[140px]">
                            {{ form.processing ? 'Menyimpan...' : (editingItem ? 'Simpan Perubahan' : 'Tambah Kegiatan') }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirm -->
        <Dialog v-model:open="showDeleteConfirm">
            <DialogContent class="sm:max-w-[380px] rounded-[2rem] p-8 border-red-100">
                <DialogHeader>
                    <DialogTitle class="text-2xl font-black">Hapus Kegiatan?</DialogTitle>
                    <DialogDescription class="pt-2">
                        Kegiatan <span class="font-bold text-foreground">{{ itemToDelete?.judul }}</span> akan dihapus permanen beserta fotonya.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="pt-6 gap-2">
                    <Button variant="ghost" @click="showDeleteConfirm = false" class="rounded-xl font-bold h-11 flex-1">Batal</Button>
                    <Button @click="deleteItem" class="bg-red-600 hover:bg-red-700 text-white rounded-xl font-black h-11 flex-1 shadow-lg shadow-red-100">Ya, Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { 
    Plus, Search, Pencil, Trash2, 
    MoreHorizontal, UserPlus, Phone, 
    Home, Hash 
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
    warga: Array<any>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Data Warga',
        href: '/warga',
    },
];

const searchQuery = ref('');
const filteredWarga = computed(() => {
    if (!searchQuery.value) return props.warga;
    const q = searchQuery.value.toLowerCase();
    return props.warga.filter(w => 
        w.nama.toLowerCase().includes(q) || 
        w.no_rumah.toLowerCase().includes(q) ||
        w.blok.toLowerCase().includes(q)
    );
});

// Create/Edit logic
const showModal = ref(false);
const editingWarga = ref<any>(null);

const form = useForm({
    no_rumah: '',
    blok: '',
    nama: '',
    telp: '',
});

const openCreateModal = () => {
    editingWarga.value = null;
    form.reset();
    showModal.value = true;
};

const openEditModal = (warga: any) => {
    editingWarga.value = warga;
    form.no_rumah = warga.no_rumah;
    form.blok = warga.blok;
    form.nama = warga.nama;
    form.telp = warga.telp || '';
    showModal.value = true;
};

const submit = () => {
    if (editingWarga.value) {
        form.put(route('warga.update', editingWarga.value.id), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    } else {
        form.post(route('warga.store'), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    }
};

// Delete logic
const showDeleteConfirm = ref(false);
const wargaToDelete = ref<any>(null);

const confirmDelete = (warga: any) => {
    wargaToDelete.value = warga;
    showDeleteConfirm.value = true;
};

const deleteWarga = () => {
    if (wargaToDelete.value) {
        router.delete(route('warga.destroy', wargaToDelete.value.id), {
            onSuccess: () => {
                showDeleteConfirm.value = false;
                wargaToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Data Warga" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Data Warga</h1>
                    <p class="text-muted-foreground mt-1">Kelola informasi penghuni Perumahan Suryo Green Village.</p>
                </div>
                
                <Button @click="openCreateModal" class="bg-amber-600 hover:bg-amber-700 text-white gap-2 px-6 py-6 rounded-xl shadow-lg shadow-amber-100 transition-all font-bold">
                    <UserPlus class="w-5 h-5" />
                    Tambah Warga
                </Button>
            </div>

            <div class="bg-card border rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-muted/30 flex items-center gap-4">
                    <div class="relative flex-1 max-w-sm">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                        <Input v-model="searchQuery" 
                               placeholder="Cari warga..." 
                               class="pl-10 h-11 bg-background border-none focus-visible:ring-amber-500 rounded-xl" />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-muted/50 text-muted-foreground text-xs uppercase tracking-wider font-bold">
                                <th class="p-4 pl-6">Blok</th>
                                <th class="p-4">Rumah</th>
                                <th class="p-4">Nama Lengkap</th>
                                <th class="p-4">Telepon</th>
                                <th class="p-4 pr-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="w in filteredWarga" :key="w.id" class="hover:bg-muted/30 transition-colors group">
                                <td class="p-4 pl-6">
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-black border border-amber-100 uppercase tracking-tighter">
                                        {{ w.blok }}
                                    </span>
                                </td>
                                <td class="p-4 font-bold text-foreground">{{ w.no_rumah }}</td>
                                <td class="p-4 text-foreground font-medium uppercase">{{ w.nama }}</td>
                                <td class="p-4 text-muted-foreground font-medium">
                                    {{ w.telp || '-' }}
                                </td>
                                <td class="p-4 pr-6 text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" class="h-8 w-8 p-0 hover:bg-amber-50 hover:text-amber-600 rounded-lg">
                                                <MoreHorizontal class="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end" class="w-40 rounded-xl border-amber-100">
                                            <DropdownMenuLabel class="text-xs text-muted-foreground uppercase tracking-widest p-3">Opsi Warga</DropdownMenuLabel>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem @click="openEditModal(w)" class="gap-2 p-3 font-medium cursor-pointer focus:bg-amber-50 focus:text-amber-700">
                                                <Pencil class="w-4 h-4" /> Edit Data
                                            </DropdownMenuItem>
                                            <DropdownMenuItem @click="confirmDelete(w)" class="gap-2 p-3 font-medium cursor-pointer text-red-600 focus:bg-red-50 focus:text-red-700">
                                                <Trash2 class="w-4 h-4" /> Hapus Warga
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>
                            <tr v-if="filteredWarga.length === 0">
                                <td colspan="5" class="p-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="p-4 bg-muted rounded-full">
                                            <UserPlus class="w-8 h-8 text-muted-foreground opacity-20" />
                                        </div>
                                        <p class="text-muted-foreground font-medium">Belum ada data warga terdaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Dialog v-model:open="showModal">
            <DialogContent class="sm:max-w-[450px] rounded-[2rem] p-8 border-amber-100">
                <DialogHeader>
                    <DialogTitle class="text-2xl font-black text-foreground">
                        {{ editingWarga ? 'Edit Data Warga' : 'Tambah Warga Baru' }}
                    </DialogTitle>
                    <DialogDescription class="font-medium text-muted-foreground">
                        Pastikan informasi nomor rumah dan blok sesuai dengan sertifikat.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submit" class="space-y-6 mt-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="blok" class="text-xs font-bold uppercase tracking-widest text-muted-foreground ml-1">Blok</Label>
                            <div class="relative">
                                <Home class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                                <Input v-model="form.blok" 
                                       id="blok" 
                                       placeholder="Contoh: A" 
                                       class="pl-10 h-12 bg-muted/30 border-none rounded-xl font-bold uppercase" 
                                       required />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label for="no_rumah" class="text-xs font-bold uppercase tracking-widest text-muted-foreground ml-1">No. Rumah</Label>
                            <div class="relative">
                                <Hash class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                                <Input v-model="form.no_rumah" 
                                       id="no_rumah" 
                                       placeholder="Contoh: A1" 
                                       class="pl-10 h-12 bg-muted/30 border-none rounded-xl font-bold" 
                                       required />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="nama" class="text-xs font-bold uppercase tracking-widest text-muted-foreground ml-1">Nama Lengkap</Label>
                        <Input v-model="form.nama" 
                               id="nama" 
                               placeholder="Masukkan nama lengkap warga" 
                               class="h-12 bg-muted/30 border-none rounded-xl font-bold uppercase" 
                               required />
                    </div>

                    <div class="space-y-2">
                        <Label for="telp" class="text-xs font-bold uppercase tracking-widest text-muted-foreground ml-1">No. Telepon (WhatsApp)</Label>
                        <div class="relative">
                            <Phone class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                            <Input v-model="form.telp" 
                                   id="telp" 
                                   placeholder="0812..." 
                                   class="pl-10 h-12 bg-muted/30 border-none rounded-xl font-bold" />
                        </div>
                    </div>

                    <DialogFooter class="pt-4">
                        <Button type="button" variant="ghost" @click="showModal = false" class="rounded-xl font-bold h-12 px-6">Batal</Button>
                        <Button type="submit" 
                                :disabled="form.processing"
                                class="bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold h-12 px-8 min-w-[120px] shadow-lg shadow-amber-100">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Data' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation -->
        <Dialog v-model:open="showDeleteConfirm">
            <DialogContent class="sm:max-w-[400px] rounded-[2rem] p-8 border-red-100">
                <DialogHeader>
                    <DialogTitle class="text-2xl font-black text-foreground">Hapus Warga?</DialogTitle>
                    <DialogDescription class="font-medium text-muted-foreground pt-2">
                        Tindakan ini tidak dapat dibatalkan. Seluruh data iuran terkait warga <span class="font-bold text-foreground uppercase">{{ wargaToDelete?.nama }}</span> juga mungkin akan terpengaruh.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="pt-6 gap-2">
                    <Button variant="ghost" @click="showDeleteConfirm = false" class="rounded-xl font-bold h-12 flex-1">Batal</Button>
                    <Button @click="deleteWarga" class="bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold h-12 flex-1 shadow-lg shadow-red-100">
                        Ya, Hapus Data
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

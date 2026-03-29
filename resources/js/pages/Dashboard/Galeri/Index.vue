<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import {
    Upload, Trash2, Image as ImageIcon, Pencil, X, Plus, CheckCircle2
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle
} from '@/components/ui/dialog';

const props = defineProps<{
    galeri: { data: Array<any>; links: Array<any>; };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Manajemen Galeri', href: '/dashboard/galeri' }];

// ── Multi-Upload State ──────────────────────────────────────────────────────
interface UploadItem {
    file: File;
    preview: string;
    judul: string;
    deskripsi: string;
}

const showUploadModal = ref(false);
const uploadItems = ref<UploadItem[]>([]);
const isUploading = ref(false);

const openUploadModal = () => {
    uploadItems.value = [];
    showUploadModal.value = true;
};

const handleMultiFileSelect = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (!target.files) return;

    Array.from(target.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = (ev) => {
            uploadItems.value.push({
                file,
                preview: ev.target?.result as string,
                judul: file.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' '),
                deskripsi: '',
            });
        };
        reader.readAsDataURL(file);
    });

    // Reset so the same files can be re-selected if needed
    target.value = '';
};

const removeUploadItem = (index: number) => {
    uploadItems.value.splice(index, 1);
};

const submitUpload = () => {
    if (!uploadItems.value.length) return;

    isUploading.value = true;

    const formData = new FormData();
    uploadItems.value.forEach((item, i) => {
        formData.append(`items[${i}][gambar]`, item.file);
        formData.append(`items[${i}][judul]`, item.judul);
        formData.append(`items[${i}][deskripsi]`, item.deskripsi);
    });

    router.post(route('dashboard.galeri.store'), formData, {
        forceFormData: true,
        onSuccess: () => {
            showUploadModal.value = false;
            uploadItems.value = [];
            isUploading.value = false;
        },
        onError: () => { isUploading.value = false; },
        onFinish: () => { isUploading.value = false; },
    });
};

// ── Edit State ──────────────────────────────────────────────────────────────
const showEditModal = ref(false);
const editingItem = ref<any>(null);
const editJudul = ref('');
const editDeskripsi = ref('');
const editFile = ref<File | null>(null);
const isEditSaving = ref(false);

const openEditModal = (item: any) => {
    editingItem.value = item;
    editJudul.value = item.judul;
    editDeskripsi.value = item.deskripsi || '';
    editFile.value = null;
    showEditModal.value = true;
};

const handleEditFile = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files?.[0]) editFile.value = target.files[0];
};

const submitEdit = () => {
    if (!editingItem.value) return;
    isEditSaving.value = true;

    const payload: Record<string, any> = {
        _method: 'PUT',
        judul: editJudul.value,
        deskripsi: editDeskripsi.value,
    };
    if (editFile.value) payload.gambar = editFile.value;

    router.post(route('dashboard.galeri.update', editingItem.value.id), payload, {
        forceFormData: true,
        onSuccess: () => { showEditModal.value = false; isEditSaving.value = false; },
        onFinish: () => { isEditSaving.value = false; },
    });
};

// ── Delete State ────────────────────────────────────────────────────────────
const showDeleteConfirm = ref(false);
const itemToDelete = ref<any>(null);

const confirmDelete = (item: any) => { itemToDelete.value = item; showDeleteConfirm.value = true; };
const deleteItem = () => {
    if (itemToDelete.value) {
        router.delete(route('dashboard.galeri.destroy', itemToDelete.value.id), {
            onSuccess: () => { showDeleteConfirm.value = false; itemToDelete.value = null; },
        });
    }
};

// ── Drag-and-Drop ────────────────────────────────────────────────────────────
const isDragging = ref(false);

const onDrop = (e: DragEvent) => {
    isDragging.value = false;
    const files = e.dataTransfer?.files;
    if (!files) return;

    Array.from(files)
        .filter(f => f.type.startsWith('image/'))
        .forEach(file => {
            const reader = new FileReader();
            reader.onload = (ev) => {
                uploadItems.value.push({
                    file,
                    preview: ev.target?.result as string,
                    judul: file.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' '),
                    deskripsi: '',
                });
            };
            reader.readAsDataURL(file);
        });
};
</script>

<template>
    <Head title="Manajemen Galeri" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-foreground">Galeri Komunitas</h1>
                    <p class="text-muted-foreground mt-1">Kelola foto dan momen bersama warga. Upload multiple foto sekaligus.</p>
                </div>
                <Button @click="openUploadModal" class="bg-amber-600 hover:bg-amber-700 text-white gap-2 px-6 py-6 rounded-xl shadow-lg shadow-amber-100 font-bold">
                    <Upload class="w-5 h-5" /> Unggah Foto
                </Button>
            </div>

            <!-- Empty State -->
            <div v-if="galeri.data.length === 0" class="flex flex-col items-center justify-center py-40 bg-card border-2 border-dashed border-muted rounded-2xl">
                <div class="p-5 bg-muted rounded-full mb-4">
                    <ImageIcon class="w-8 h-8 text-muted-foreground opacity-20" />
                </div>
                <p class="font-bold text-muted-foreground">Belum ada foto di galeri.</p>
                <p class="text-sm text-muted-foreground/60 mt-1">Klik "Unggah Foto" untuk menambahkan momen komunitas.</p>
            </div>

            <!-- Gallery Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <div v-for="item in galeri.data" :key="item.id"
                     class="group relative rounded-2xl overflow-hidden border border-border bg-muted aspect-square shadow-sm hover:shadow-lg transition-all duration-200">
                    <img :src="'/storage/' + item.gambar" :alt="item.judul" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col justify-between p-3">
                        <div class="flex justify-end gap-1.5">
                            <button @click.stop="openEditModal(item)" class="w-7 h-7 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center hover:bg-white/40 transition-colors">
                                <Pencil class="w-3 h-3 text-white" />
                            </button>
                            <button @click.stop="confirmDelete(item)" class="w-7 h-7 rounded-lg bg-red-500/70 backdrop-blur-sm flex items-center justify-center hover:bg-red-500 transition-colors">
                                <Trash2 class="w-3 h-3 text-white" />
                            </button>
                        </div>
                        <p class="text-white font-bold text-xs leading-tight line-clamp-2">{{ item.judul }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ Multi-Upload Modal ═══ -->
        <Dialog v-model:open="showUploadModal">
            <DialogContent class="sm:max-w-[700px] max-h-[90vh] overflow-y-auto rounded-[2rem] p-8 border-amber-100">
                <DialogHeader>
                    <DialogTitle class="text-2xl font-black">Unggah Foto ke Galeri</DialogTitle>
                    <DialogDescription>Pilih satu atau banyak foto sekaligus. Anda dapat mengubah judul masing-masing foto sebelum mengunggah.</DialogDescription>
                </DialogHeader>

                <!-- Drop Zone -->
                <div
                    class="mt-4 rounded-2xl border-2 border-dashed transition-all duration-200 cursor-pointer relative"
                    :class="isDragging ? 'border-amber-500 bg-amber-50' : 'border-muted bg-muted/10 hover:border-amber-400 hover:bg-amber-50/50'"
                    @dragover.prevent="isDragging = true"
                    @dragleave="isDragging = false"
                    @drop.prevent="onDrop"
                >
                    <input
                        type="file"
                        multiple
                        accept="image/*"
                        @change="handleMultiFileSelect"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                    />
                    <div class="flex flex-col items-center gap-3 py-10 pointer-events-none">
                        <div class="p-4 rounded-2xl" :class="isDragging ? 'bg-amber-100' : 'bg-muted'">
                            <Upload class="w-8 h-8" :class="isDragging ? 'text-amber-600' : 'text-muted-foreground'" />
                        </div>
                        <div class="text-center">
                            <p class="font-black text-sm text-foreground">{{ isDragging ? 'Lepaskan foto di sini' : 'Seret & lepas atau klik untuk pilih foto' }}</p>
                            <p class="text-xs text-muted-foreground mt-1">Pilih banyak foto sekaligus — JPG, PNG, WEBP — Maks. 5MB/foto</p>
                        </div>
                        <div v-if="uploadItems.length > 0" class="flex items-center gap-2 px-3 py-1.5 bg-amber-100 text-amber-700 rounded-full text-xs font-black">
                            <CheckCircle2 class="w-3.5 h-3.5" />
                            {{ uploadItems.length }} foto dipilih — klik untuk tambah lagi
                        </div>
                    </div>
                </div>

                <!-- Preview Items -->
                <div v-if="uploadItems.length > 0" class="mt-6 space-y-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-black text-muted-foreground uppercase tracking-widest">{{ uploadItems.length }} Foto Dipilih</p>
                        <button @click="uploadItems = []" class="text-xs font-bold text-red-500 hover:text-red-700 flex items-center gap-1">
                            <X class="w-3.5 h-3.5" /> Hapus Semua
                        </button>
                    </div>

                    <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                        <div v-for="(item, index) in uploadItems" :key="index"
                             class="flex items-start gap-4 p-3 bg-muted/30 rounded-2xl border border-border">
                            <!-- Thumbnail -->
                            <img :src="item.preview" :alt="item.judul" class="w-16 h-16 rounded-xl object-cover shrink-0 border border-border" />

                            <!-- Form Fields -->
                            <div class="flex-1 space-y-2">
                                <Input
                                    v-model="item.judul"
                                    :placeholder="`Judul foto ${index + 1}`"
                                    class="h-9 bg-background border-none rounded-lg text-sm font-bold"
                                />
                                <input
                                    v-model="item.deskripsi"
                                    :placeholder="`Deskripsi singkat... (opsional)`"
                                    class="w-full px-3 py-1.5 bg-background border-none rounded-lg text-xs text-muted-foreground focus:ring-2 focus:ring-amber-500"
                                />
                            </div>

                            <!-- Remove -->
                            <button @click="removeUploadItem(index)" class="p-1.5 rounded-lg hover:bg-red-50 hover:text-red-500 transition-colors shrink-0">
                                <X class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <DialogFooter class="pt-6 gap-3">
                    <Button type="button" variant="ghost" @click="showUploadModal = false" class="rounded-xl font-bold h-11">Batal</Button>
                    <Button
                        @click="submitUpload"
                        :disabled="uploadItems.length === 0 || isUploading"
                        class="bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-black h-11 px-8 shadow-lg shadow-amber-100 min-w-[180px]"
                    >
                        <span v-if="isUploading" class="flex items-center gap-2">
                            <span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            Mengunggah...
                        </span>
                        <span v-else class="flex items-center gap-2">
                            <Upload class="w-4 h-4" />
                            Unggah {{ uploadItems.length > 0 ? uploadItems.length + ' Foto' : '' }}
                        </span>
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- ═══ Edit Single Photo Modal ═══ -->
        <Dialog v-model:open="showEditModal">
            <DialogContent class="sm:max-w-[420px] rounded-[2rem] p-8 border-amber-100">
                <DialogHeader>
                    <DialogTitle class="text-2xl font-black">Edit Foto</DialogTitle>
                    <DialogDescription>Ubah judul atau ganti foto yang sudah ada.</DialogDescription>
                </DialogHeader>
                <div class="space-y-5 mt-4">
                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Judul Foto</Label>
                        <Input v-model="editJudul" placeholder="Judul foto..." class="h-12 bg-muted/30 border-none rounded-xl font-bold" />
                    </div>
                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Deskripsi (Pilihan)</Label>
                        <textarea v-model="editDeskripsi" rows="2" placeholder="Keterangan singkat foto..."
                            class="w-full px-4 py-3 bg-muted/30 border-none rounded-xl font-medium focus:ring-2 focus:ring-amber-500 resize-none text-sm w-full"></textarea>
                    </div>
                    <div class="space-y-2">
                        <Label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Ganti Foto (Pilihan)</Label>
                        <div class="relative group/upload cursor-pointer">
                            <input type="file" @change="handleEditFile" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                            <div class="px-5 py-4 bg-muted/10 border-2 border-dashed border-muted rounded-2xl flex items-center gap-3 group-hover/upload:border-amber-400 transition-colors">
                                <ImageIcon class="w-5 h-5 text-muted-foreground shrink-0" />
                                <span class="text-xs font-bold text-muted-foreground truncate">
                                    {{ editFile ? editFile.name : 'Klik untuk ganti foto...' }}
                                </span>
                            </div>
                        </div>
                        <div v-if="editingItem?.gambar && !editFile" class="mt-2 rounded-xl overflow-hidden h-28 border border-border">
                            <img :src="'/storage/' + editingItem.gambar" class="w-full h-full object-cover" />
                        </div>
                    </div>
                </div>
                <DialogFooter class="pt-6">
                    <Button type="button" variant="ghost" @click="showEditModal = false" class="rounded-xl font-bold h-11">Batal</Button>
                    <Button @click="submitEdit" :disabled="isEditSaving" class="bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-black h-11 px-8 shadow-lg shadow-amber-100 min-w-[140px]">
                        {{ isEditSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- ═══ Delete Confirm ═══ -->
        <Dialog v-model:open="showDeleteConfirm">
            <DialogContent class="sm:max-w-[360px] rounded-[2rem] p-8 border-red-100">
                <DialogHeader>
                    <DialogTitle class="text-2xl font-black">Hapus Foto?</DialogTitle>
                    <DialogDescription class="pt-2">Foto <span class="font-bold text-foreground">"{{ itemToDelete?.judul }}"</span> akan dihapus permanen.</DialogDescription>
                </DialogHeader>
                <DialogFooter class="pt-6 gap-2">
                    <Button variant="ghost" @click="showDeleteConfirm = false" class="rounded-xl font-bold h-11 flex-1">Batal</Button>
                    <Button @click="deleteItem" class="bg-red-600 hover:bg-red-700 text-white rounded-xl font-black h-11 flex-1 shadow-lg shadow-red-100">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

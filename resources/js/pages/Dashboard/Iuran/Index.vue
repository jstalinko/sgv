<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Check, X, ChevronLeft, ChevronRight, Search, CreditCard } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const props = defineProps<{
    warga: Array<any>;
    tahun: number;
    amount: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manajemen Iuran',
        href: '/dashboard/iuran',
    },
];

const months = [
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

const currentMonth = new Date().getMonth() + 1;
const currentYear = new Date().getFullYear();

const isCurrentMonth = (monthId: number) => {
    return props.tahun === currentYear && monthId === currentMonth;
};

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
    router.post(route('dashboard.iuran.toggle'), {
        warga_id: wargaId,
        bulan: monthId,
        tahun: props.tahun
    }, {
        preserveScroll: true,
    });
};

const changeYear = (offset: number) => {
    router.get(route('dashboard.iuran.index'), { tahun: props.tahun + offset });
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
};
</script>

<template>
    <Head title="Manajemen Iuran" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Manajemen Iuran</h1>
                    <p class="text-muted-foreground mt-1">Status pembayaran rutin iuran bulanan warga.</p>
                </div>
                
                <div class="flex items-center bg-muted/30 p-1 rounded-xl border border-border">
                    <Button variant="ghost" size="icon" @click="changeYear(-1)" class="h-8 w-8 rounded-lg hover:bg-background">
                        <ChevronLeft class="w-4 h-4" />
                    </Button>
                    <span class="px-6 text-sm font-black text-foreground">{{ tahun }}</span>
                    <Button variant="ghost" size="icon" @click="changeYear(1)" class="h-8 w-8 rounded-lg hover:bg-background">
                        <ChevronRight class="w-4 h-4" />
                    </Button>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-card border rounded-2xl shadow-sm overflow-hidden">
                <div class="p-4 border-b bg-muted/30 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="relative w-full md:w-80">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                        <Input v-model="searchQuery" 
                               placeholder="Cari warga..." 
                               class="pl-10 h-10 bg-background border-none focus-visible:ring-amber-500 rounded-lg" />
                    </div>
                    
                    <div class="flex items-center gap-2 px-4 py-2 bg-amber-50/50 rounded-xl border border-amber-100/50">
                        <CreditCard class="w-4 h-4 text-amber-600" />
                        <span class="text-xs font-bold text-amber-700">BESAR IURAN: <span class="font-black">{{ formatCurrency(amount) }}</span></span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-muted/50 border-b">
                                <th class="p-4 pl-6 text-[10px] font-black text-muted-foreground uppercase tracking-widest sticky left-0 bg-muted z-10 border-r w-[80px]">Rumah</th>
                                <th class="p-4 text-[10px] font-black text-muted-foreground uppercase tracking-widest sticky left-[80px] bg-muted z-10 border-r w-[150px]">Nama</th>
                                <th v-for="month in months" :key="month.id" 
                                    class="p-4 text-[10px] font-black uppercase tracking-widest text-center min-w-[70px]"
                                    :class="isCurrentMonth(month.id) ? 'text-amber-600 bg-amber-50/30' : 'text-muted-foreground'">
                                    {{ month.name.substring(0, 3) }}
                                    <div v-if="isCurrentMonth(month.id)" class="text-[7px] mt-0.5 font-black">BULAN INI</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="w in filteredWarga" :key="w.id" class="hover:bg-muted/30 transition-colors group">
                                <td class="p-4 pl-6 text-sm font-black text-amber-700 sticky left-0 bg-card group-hover:bg-muted/50 transition-colors z-10 border-r">
                                    {{ w.no_rumah }}
                                </td>
                                <td class="p-4 text-xs font-bold text-foreground sticky left-[80px] bg-card group-hover:bg-muted/50 transition-colors z-10 border-r uppercase truncate max-w-[150px]">
                                    {{ w.nama }}
                                </td>
                                <td v-for="month in months" :key="month.id" 
                                    class="p-2 text-center"
                                    :class="{ 'bg-amber-50/10': isCurrentMonth(month.id) }">
                                    <button @click="togglePayment(w.id, month.id)" 
                                            class="w-8 h-8 rounded-lg flex items-center justify-center mx-auto transition-all active:scale-90"
                                            :class="isPaid(w.id, month.id) 
                                                ? 'bg-amber-600 text-white shadow-md shadow-amber-200' 
                                                : 'bg-stone-100 text-stone-300 hover:bg-stone-200'">
                                        <Check v-if="isPaid(w.id, month.id)" class="w-5 h-5 stroke-[3]" />
                                        <X v-else class="w-3 h-3 opacity-30" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredWarga.length === 0">
                                <td colspan="14" class="p-20 text-center text-muted-foreground font-medium">Data tidak ditemukan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}
.overflow-x-auto::-webkit-scrollbar-track {
    background: transparent;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}
.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}
</style>

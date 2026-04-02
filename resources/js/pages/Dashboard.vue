<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

defineProps<{
    name?: string;
    totalWarga?: number;
    totalPemasukan?: number;
    totalPengeluaran?: number;
    totalKas?: number;
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6 flex flex-col justify-center bg-white dark:bg-sidebar">
                    <div class="text-sm font-medium text-muted-foreground">Total Kas</div>
                    <div class="text-2xl font-bold mt-2">Rp {{ totalKas?.toLocaleString('id-ID') || 0 }}</div>
                </div>
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6 flex flex-col justify-center bg-white dark:bg-sidebar">
                    <div class="text-sm font-medium text-muted-foreground">Total Pemasukan</div>
                    <div class="text-2xl font-bold mt-2 text-green-600 dark:text-green-400">Rp {{ totalPemasukan?.toLocaleString('id-ID') || 0 }}</div>
                </div>
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6 flex flex-col justify-center bg-white dark:bg-sidebar">
                    <div class="text-sm font-medium text-muted-foreground">Total Pengeluaran</div>
                    <div class="text-2xl font-bold mt-2 text-red-600 dark:text-red-400">Rp {{ totalPengeluaran?.toLocaleString('id-ID') || 0 }}</div>
                </div>
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6 flex flex-col justify-center bg-white dark:bg-sidebar">
                    <div class="text-sm font-medium text-muted-foreground">Total Warga</div>
                    <div class="text-2xl font-bold mt-2">{{ totalWarga || 0 }}</div>
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>

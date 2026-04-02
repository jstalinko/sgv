<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

const props = defineProps<{
    settings: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Web Setting',
        href: '/dashboard/web-setting',
    },
];

const form = useForm({
    website_name: props.settings?.website_name || '',
    short_description: props.settings?.short_description || '',
    website_url: props.settings?.website_url || '',
    contact_center: props.settings?.contact_center || '',
    alamat: props.settings?.alamat || '',
    phone: props.settings?.phone || '',
    email: props.settings?.email || '',
    instagram: props.settings?.instagram || '',
    rekening_type: props.settings?.rekening_type || 'Transfer',
    rekening_tujuan: props.settings?.rekening_tujuan || '',
    qris_image: null as File | null,
    iuran_bulanan: props.settings?.iuran_bulanan || 0,
});

const fileInput = ref<HTMLInputElement | null>(null);
const imagePreview = ref(props.settings?.qris_image_path || '');

function handleFileUpload(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.qris_image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
}

function submit() {
    form.post('/dashboard/web-setting', {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Web Setting" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="max-w-4xl mx-auto w-full">
                <CardHeader>
                    <CardTitle>Website Settings</CardTitle>
                    <CardDescription>Update your website information and configuration here.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="form.recentlySuccessful || $page.props.flash?.success" class="mb-6 rounded-md bg-green-50 p-4 border border-green-200 dark:bg-green-900/20 dark:border-green-900/50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800 dark:text-green-300">
                                    {{ $page.props.flash?.success || 'Settings saved successfully.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="website_name">Website Name</Label>
                                <Input id="website_name" v-model="form.website_name" />
                            </div>
                            <div class="space-y-2">
                                <Label for="short_description">Short Description</Label>
                                <Input id="short_description" v-model="form.short_description" />
                            </div>
                            <div class="space-y-2">
                                <Label for="website_url">Website URL</Label>
                                <Input id="website_url" v-model="form.website_url" type="url" placeholder="https://..." />
                            </div>
                            <div class="space-y-2">
                                <Label for="contact_center">Contact Center (WhatsApp Link)</Label>
                                <Input id="contact_center" v-model="form.contact_center" type="url" placeholder="https://wa.me/..." />
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <Label for="alamat">Alamat</Label>
                                <Input id="alamat" v-model="form.alamat" />
                            </div>
                            <div class="space-y-2">
                                <Label for="phone">Phone</Label>
                                <Input id="phone" v-model="form.phone" />
                            </div>
                            <div class="space-y-2">
                                <Label for="email">Email</Label>
                                <Input id="email" v-model="form.email" type="email" />
                            </div>
                            <div class="space-y-2">
                                <Label for="instagram">Instagram Username</Label>
                                <Input id="instagram" v-model="form.instagram" placeholder="@username" />
                            </div>
                            <div class="space-y-2">
                                <Label for="iuran_bulanan">Iuran Bulanan (Rp)</Label>
                                <Input id="iuran_bulanan" v-model="form.iuran_bulanan" type="number" />
                            </div>
                            
                            <div class="space-y-2 md:col-span-2">
                                <Label for="rekening_type">Rekening Iuran Type</Label>
                                <select id="rekening_type" v-model="form.rekening_type" class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="Transfer">Transfer Bank</option>
                                    <option value="QRIS">QRIS</option>
                                </select>
                            </div>

                            <div v-if="form.rekening_type === 'Transfer'" class="space-y-2 md:col-span-2 border p-4 rounded-lg bg-slate-50 dark:bg-sidebar">
                                <Label for="rekening_tujuan">Rekening Tujuan (Bank, No. Rekening, Atas Nama)</Label>
                                <Input id="rekening_tujuan" v-model="form.rekening_tujuan" placeholder="Contoh: BCA 123456789 a/n Nama" />
                            </div>

                            <div v-if="form.rekening_type === 'QRIS'" class="space-y-2 md:col-span-2 border p-4 rounded-lg bg-slate-50 dark:bg-sidebar">
                                <Label for="qris_image">Upload QRIS Image</Label>
                                <Input id="qris_image" type="file" accept="image/*" @change="handleFileUpload" ref="fileInput" class="cursor-pointer" />
                                
                                <div v-if="imagePreview" class="mt-4">
                                    <p class="text-sm font-medium mb-2">QRIS Preview:</p>
                                    <img :src="imagePreview" alt="QRIS Preview" class="max-h-64 object-contain rounded-md" />
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2">
                            <Button type="submit" :disabled="form.processing">
                                Save Settings
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
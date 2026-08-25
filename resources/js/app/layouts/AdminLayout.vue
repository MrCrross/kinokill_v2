<script setup lang="ts">
import MainLayout from '@/app/layouts/MainLayout.vue';
import AdminSidebar from '@/widgets/admin-sidebar/ui/AdminSidebar.vue';
</script>

<template>
    <MainLayout>
        <div class="grid gap-6 lg:grid-cols-[12rem_1fr]">
            <AdminSidebar />

            <div>
                <slot />
            </div>
        </div>
    </MainLayout>
</template>

<script setup lang="ts">
import type { SessionListItem } from '@/entities/session/model/types';
import SessionTimeSlot from '@/entities/session/ui/SessionTimeSlot.vue';

withDefaults(
    defineProps<{
        sessions: SessionListItem[];
        emptyText?: string;
    }>(),
    {
        emptyText: 'Сеансов пока не запланировано.',
    },
);
</script>

<template>
    <p v-if="sessions.length === 0" class="text-neutral-400">{{ emptyText }}</p>

    <div v-else class="flex flex-wrap gap-3">
        <SessionTimeSlot
            v-for="session in sessions"
            :key="session.id"
            :id="session.id"
            :starts-at="session.startsAt"
            :hall-name="session.hallName"
        />
    </div>
</template>

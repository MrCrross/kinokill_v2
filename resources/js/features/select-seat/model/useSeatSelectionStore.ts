import { defineStore } from 'pinia';

export const useSeatSelectionStore = defineStore('seatSelection', {
    state: () => ({
        sessionId: null as number | null,
        selectedSeatIds: [] as number[],
    }),
    actions: {
        /**
         * Reset selection whenever a different session's seat map mounts,
         * so leftover selections from a previous booking screen don't leak
         * into this one (the Pinia instance persists across Inertia visits).
         */
        enterSession(sessionId: number) {
            if (this.sessionId !== sessionId) {
                this.sessionId = sessionId;
                this.selectedSeatIds = [];
            }
        },
        toggle(seatId: number) {
            const index = this.selectedSeatIds.indexOf(seatId);
            if (index === -1) {
                this.selectedSeatIds.push(seatId);
            } else {
                this.selectedSeatIds.splice(index, 1);
            }
        },
        clear() {
            this.selectedSeatIds = [];
        },
    },
});

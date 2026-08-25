import { createPinia, setActivePinia } from 'pinia';
import { beforeEach, describe, expect, it } from 'vitest';
import { useSeatSelectionStore } from './useSeatSelectionStore';

describe('useSeatSelectionStore', () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });

    it('toggles a seat into and out of the selection', () => {
        const store = useSeatSelectionStore();

        store.toggle(1);
        expect(store.selectedSeatIds).toEqual([1]);

        store.toggle(1);
        expect(store.selectedSeatIds).toEqual([]);
    });

    it('tracks multiple seats independently', () => {
        const store = useSeatSelectionStore();

        store.toggle(1);
        store.toggle(2);
        expect(store.selectedSeatIds).toEqual([1, 2]);

        store.toggle(1);
        expect(store.selectedSeatIds).toEqual([2]);
    });

    it('clears the whole selection', () => {
        const store = useSeatSelectionStore();

        store.toggle(1);
        store.toggle(2);
        store.clear();

        expect(store.selectedSeatIds).toEqual([]);
    });

    it('resets the selection when entering a different session', () => {
        const store = useSeatSelectionStore();

        store.enterSession(10);
        store.toggle(1);
        expect(store.selectedSeatIds).toEqual([1]);

        store.enterSession(11);
        expect(store.selectedSeatIds).toEqual([]);
    });

    it('keeps the selection when re-entering the same session', () => {
        const store = useSeatSelectionStore();

        store.enterSession(10);
        store.toggle(1);

        store.enterSession(10);
        expect(store.selectedSeatIds).toEqual([1]);
    });
});

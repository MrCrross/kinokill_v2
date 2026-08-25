import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import FilmPoster from './FilmPoster.vue';

describe('FilmPoster', () => {
    it('renders an img when a poster url is provided', () => {
        const wrapper = mount(FilmPoster, {
            props: { posterUrl: '/storage/poster.jpg', title: 'Матрица' },
        });

        const img = wrapper.get('img');
        expect(img.attributes('src')).toBe('/storage/poster.jpg');
        expect(img.attributes('alt')).toBe('Матрица');
    });

    it('renders a placeholder when there is no poster', () => {
        const wrapper = mount(FilmPoster, {
            props: { posterUrl: null, title: 'Матрица' },
        });

        expect(wrapper.find('img').exists()).toBe(false);
        expect(wrapper.text()).toContain('Нет постера');
    });
});

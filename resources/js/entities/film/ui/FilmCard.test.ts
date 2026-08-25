import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import type { FilmListItem } from '../model/types';
import FilmCard from './FilmCard.vue';

const LinkStub = {
    props: ['href'],
    template: '<a :href="href"><slot /></a>',
};

function makeFilm(overrides: Partial<FilmListItem> = {}): FilmListItem {
    return {
        id: 7,
        title: 'Матрица',
        country: 'США',
        ageLimit: 16,
        posterUrl: null,
        ...overrides,
    };
}

describe('FilmCard', () => {
    it('links to the film detail page', () => {
        const wrapper = mount(FilmCard, {
            props: { film: makeFilm({ id: 7 }) },
            global: { stubs: { Link: LinkStub } },
        });

        expect(wrapper.get('a').attributes('href')).toBe('/films/7');
    });

    it('shows the title, country and age limit', () => {
        const wrapper = mount(FilmCard, {
            props: {
                film: makeFilm({ title: 'Дюна', country: 'США', ageLimit: 16 }),
            },
            global: { stubs: { Link: LinkStub } },
        });

        expect(wrapper.text()).toContain('Дюна');
        expect(wrapper.text()).toContain('США');
        expect(wrapper.text()).toContain('16+');
    });
});

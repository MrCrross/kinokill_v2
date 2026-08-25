import type { Meta, StoryObj } from '@storybook/vue3-vite';
import type { FilmListItem } from '../model/types';
import FilmCard from './FilmCard.vue';

const film: FilmListItem = {
    id: 7,
    title: 'Дюна: Часть вторая',
    country: 'США',
    ageLimit: 16,
    posterUrl: 'https://picsum.photos/seed/dune/400/600',
};

const meta: Meta<typeof FilmCard> = {
    title: 'Entities/Film/FilmCard',
    component: FilmCard,
    tags: ['autodocs'],
    decorators: [
        () => ({ template: '<div style="width: 16rem;"><story /></div>' }),
    ],
};

export default meta;
type Story = StoryObj<typeof FilmCard>;

export const Default: Story = {
    args: { film },
};

export const NoPoster: Story = {
    args: { film: { ...film, posterUrl: null } },
};

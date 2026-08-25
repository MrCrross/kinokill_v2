import type { Meta, StoryObj } from '@storybook/vue3-vite';
import FilmPoster from './FilmPoster.vue';

const meta: Meta<typeof FilmPoster> = {
    title: 'Entities/Film/FilmPoster',
    component: FilmPoster,
    tags: ['autodocs'],
};

export default meta;
type Story = StoryObj<typeof FilmPoster>;

export const WithPoster: Story = {
    args: {
        posterUrl: 'https://picsum.photos/seed/kinokill/400/600',
        title: 'Матрица',
    },
};

export const NoPoster: Story = {
    args: {
        posterUrl: null,
        title: 'Матрица',
    },
};

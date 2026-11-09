<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Director;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReferenceAdminController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Reference/Index', [
            'countries' => Country::query()->orderBy('name')->pluck('name')->all(),
            'directors' => Director::query()->orderBy('name')->pluck('name')->all(),
            'genres' => Genre::query()
                ->orderBy('name')
                ->withCount('films')
                ->get()
                ->map(fn (Genre $genre) => [
                    'id' => $genre->id,
                    'name' => $genre->name,
                    'filmsCount' => $genre->films_count,
                ])
                ->all(),
        ]);
    }

    public function storeCountry(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:countries,name'],
        ]);

        Country::query()->create(['name' => $data['name']]);

        return back()->with('success', 'Страна добавлена.');
    }

    public function destroyCountry(Country $country): RedirectResponse
    {
        $country->delete();

        return back()->with('success', 'Страна удалена.');
    }

    public function storeDirector(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:directors,name'],
        ]);

        Director::query()->create(['name' => $data['name']]);

        return back()->with('success', 'Режиссёр добавлен.');
    }

    public function destroyDirector(Director $director): RedirectResponse
    {
        $director->delete();

        return back()->with('success', 'Режиссёр удалён.');
    }

    public function storeGenre(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:genres,name'],
        ]);

        Genre::query()->create(['name' => $data['name']]);

        return back()->with('success', 'Жанр добавлен.');
    }

    public function destroyGenre(Genre $genre): RedirectResponse
    {
        if ($genre->films()->exists()) {
            return back()->with('error', "Жанр «{$genre->name}» привязан к фильмам и не может быть удалён.");
        }

        $genre->delete();

        return back()->with('success', 'Жанр удалён.');
    }
}

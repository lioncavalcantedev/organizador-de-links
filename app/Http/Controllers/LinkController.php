<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoveLinkRequest;
use App\Http\Requests\StoreLinkRequest;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function index(): View
    {
        return view('links.index', [
            'links' => request()->user()->links()->orderBy('position')->get(),
        ]);
    }

    public function store(StoreLinkRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $imagePath = $validated['image']->store('links', 'public');

        DB::transaction(function () use ($request, $validated, $imagePath): void {
            $position = $request->user()->links()->lockForUpdate()->max('position') + 1;

            $request->user()->links()->create([
                'title' => $validated['title'],
                'url' => $validated['url'],
                'image_url' => $imagePath,
                'category' => $validated['platform'],
                'category_variant' => 'blue',
                'position' => $position,
            ]);
        });

        return to_route('links.index')->with('message', 'Link adicionado com sucesso.');
    }

    public function move(Link $link, MoveLinkRequest $request): JsonResponse|RedirectResponse
    {
        $result = DB::transaction(function () use ($link, $request): array {
            $currentLink = Link::query()->lockForUpdate()->findOrFail($link->id);
            $currentPosition = $currentLink->position;

            $adjacentLink = $currentLink->user->links()
                ->lockForUpdate()
                ->when(
                    $request->string('direction')->value() === 'up',
                    fn ($query) => $query->where('position', '<', $currentPosition)->orderByDesc('position'),
                    fn ($query) => $query->where('position', '>', $currentPosition)->orderBy('position'),
                )
                ->first();

            if (! $adjacentLink) {
                return ['moved' => false, 'links' => []];
            }

            $adjacentPosition = $adjacentLink->position;

            $currentLink->update(['position' => 0]);
            $adjacentLink->update(['position' => $currentPosition]);
            $currentLink->update(['position' => $adjacentPosition]);

            return [
                'moved' => true,
                'links' => [
                    ['id' => $currentLink->id, 'position' => $adjacentPosition],
                    ['id' => $adjacentLink->id, 'position' => $currentPosition],
                ],
            ];
        });

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return to_route('links.index');
    }
}

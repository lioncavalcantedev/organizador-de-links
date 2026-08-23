<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoveLinkRequest;
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

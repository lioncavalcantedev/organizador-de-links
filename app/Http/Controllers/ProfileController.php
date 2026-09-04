<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit', [
            'user' => request()->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->safe()->except('image');
        $previousImagePath = $user->image_url;
        $newImagePath = null;

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('profiles', 'public');
            $data['image_url'] = $newImagePath;
        }

        try {
            $user->update($data);
        } catch (Throwable $exception) {
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if ($newImagePath && $previousImagePath) {
            Storage::disk('public')->delete($previousImagePath);
        }

        return response()->json([
            'message' => 'Perfil atualizado com sucesso.',
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'bio' => $user->bio,
                'image_url' => $user->image_url ? Storage::disk('public')->url($user->image_url) : null,
            ],
        ]);
    }
}

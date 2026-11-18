<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\RequestHistory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user()->load('zone');

        return view('profile.edit', [
            'user' => $user,
            'activity' => RequestHistory::with('portalRequest:id,request_no')->where('user_id', $user->id)->latest('id')->limit(6)->get(),
            'raised' => \App\Models\PortalRequest::where('user_id', $user->id)->whereNotNull('request_no')->count(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated())->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /** Square-crops the upload to 400px and keeps it on the private disk. */
    public function photo(Request $request): RedirectResponse
    {
        $request->validate(['photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072']], [
            'photo.max' => 'Photo must be under 3 MB.',
            'photo.image' => 'Please choose a JPG, PNG or WebP image.',
            'photo.mimes' => 'Please choose a JPG, PNG or WebP image.',
        ]);

        $user = $request->user();
        $file = $request->file('photo');
        $path = 'avatars/' . $user->id . '.jpg';

        $image = function_exists('imagecreatefromstring') ? @imagecreatefromstring(file_get_contents($file->getRealPath())) : false;
        if ($image) {
            [$w, $h] = [imagesx($image), imagesy($image)];
            $side = min($w, $h);
            $square = imagecreatetruecolor(400, 400);
            imagefill($square, 0, 0, imagecolorallocate($square, 255, 255, 255));
            imagecopyresampled($square, $image, 0, 0, intdiv($w - $side, 2), intdiv($h - $side, 2), 400, 400, $side, $side);
            ob_start();
            imagejpeg($square, null, 86);
            Storage::disk('local')->put($path, ob_get_clean());
        } else {
            $path = $file->storeAs('avatars', $user->id . '.' . $file->getClientOriginalExtension(), 'local');
        }

        $user->forceFill(['profile_photo' => $path])->save();

        return redirect()->route('profile.edit')->with('status', 'photo-updated');
    }

    public function removePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->profile_photo) {
            Storage::disk('local')->delete($user->profile_photo);
            $user->forceFill(['profile_photo' => null])->save();
        }

        return redirect()->route('profile.edit')->with('status', 'photo-removed');
    }

    /** Photos are private: only signed-in users can load them. */
    public function showPhoto(User $user)
    {
        abort_unless($user->profile_photo && Storage::disk('local')->exists($user->profile_photo), 404);

        return Storage::disk('local')->response($user->profile_photo, null, [
            'Cache-Control' => 'private, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

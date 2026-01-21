<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

function generateRandom(int $lenght = 12, bool $type = false): string
{
    if ($type) {
        $words = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle(str_repeat($words, $lenght)), 0, $lenght);
    } else {
        return rand(10 ** ($lenght - 1), (10 ** $lenght) - 1);
    }
}

function storeImage($image, $folder): string
{
    if (app()->isProduction() && env('IMAGE_ENV') !== 'local') {
        $path = $image->store($folder, 's3');
        return $path;
    } else {
        $imageName = time() . '_' . generateRandom() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/' . $folder), $imageName);
        return 'uploads/' . $folder . '/' . $imageName;
    }
}

function deleteImage(string $path): void
{
    if (app()->isProduction() && env('IMAGE_ENV') !== 'local') {
        Storage::disk('s3')->delete($path);
    } else {
        if (file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }
}

function authUser(): ?\App\Models\User
{
    return auth()->guard(API)
        ->user();
    // return Auth::guard(API)->user();
}

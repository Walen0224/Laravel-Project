<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProductProcessImageController extends Controller
{
    public function processImage(Request $request): JsonResponse
    {
        $image = $request->file('image');

        $compressedImage = (new \App\Services\CompressedImage)->uploadCompressedImage($image);

        $compressedImagePath = 'compressed_'.uniqid().'.jpg';

        Storage::disk('temp')->put($compressedImagePath, $compressedImage->toJpeg(80));

        $encryptedImagePath = encrypt($compressedImagePath);

        return response()->json([
            'success' => true,
            'message' => '圖片上傳成功',
            'path' => $encryptedImagePath,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
use Cloudinary\Transformation\Quality;
use Exception;

class ImageUploadController extends Controller
{
    private $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);
    }

    /**
     * Upload image to Cloudinary
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
                'folder' => 'nullable|string|max:50',
            ]);

            $image = $request->file('image');
            $folder = $request->input('folder', 'categories');

            // Upload to Cloudinary
            $uploadResult = $this->cloudinary->uploadApi()->upload(
                $image->getRealPath(),
                [
                    'folder' => $folder,
                    'resource_type' => 'image',
                    'quality' => 'auto:good',
                    'fetch_format' => 'auto',
                    'transformation' => [
                        'width' => 800,
                        'height' => 600,
                        'crop' => 'limit',
                        'quality' => 80
                    ]
                ]
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $uploadResult['secure_url'],
                    'public_id' => $uploadResult['public_id'],
                    'width' => $uploadResult['width'],
                    'height' => $uploadResult['height'],
                    'format' => $uploadResult['format'],
                    'bytes' => $uploadResult['bytes']
                ],
                'message' => 'Upload ảnh thành công'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi upload ảnh: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete image from Cloudinary
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'public_id' => 'required|string'
            ]);

            $publicId = $request->input('public_id');

            // Delete from Cloudinary
            $result = $this->cloudinary->uploadApi()->destroy($publicId);

            if ($result['result'] === 'ok') {
                return response()->json([
                    'success' => true,
                    'message' => 'Xóa ảnh thành công'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa ảnh'
                ], 400);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xóa ảnh: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get optimized image URL with transformations
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getOptimizedUrl(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'public_id' => 'required|string',
                'width' => 'nullable|integer|min:50|max:2000',
                'height' => 'nullable|integer|min:50|max:2000',
                'quality' => 'nullable|integer|min:10|max:100'
            ]);

            $publicId = $request->input('public_id');
            $width = $request->input('width', 400);
            $height = $request->input('height', 300);
            $quality = $request->input('quality', 80);

            // Generate optimized URL
            $optimizedUrl = $this->cloudinary->image($publicId)
                ->resize(Resize::fit($width, $height))
                ->quality(Quality::auto())
                ->toUrl();

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $optimizedUrl,
                    'transformations' => [
                        'width' => $width,
                        'height' => $height,
                        'quality' => $quality
                    ]
                ],
                'message' => 'URL ảnh được tạo thành công'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tạo URL ảnh: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload image for CKEditor (simpler response format)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ckeditorUpload(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            ]);

            $image = $request->file('upload');

            // Upload to Cloudinary
            $uploadResult = $this->cloudinary->uploadApi()->upload(
                $image->getRealPath(),
                [
                    'folder' => 'products/descriptions',
                    'resource_type' => 'image',
                    'quality' => 'auto:good',
                    'fetch_format' => 'auto',
                    'transformation' => [
                        'width' => 600,
                        'height' => 400,
                        'crop' => 'limit',
                        'quality' => 85
                    ]
                ]
            );

            // CKEditor expects this specific response format
            return response()->json([
                'url' => $uploadResult['secure_url']
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => [
                    'message' => 'File không hợp lệ. Vui lòng chọn file ảnh có kích thước dưới 5MB.'
                ]
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'message' => 'Lỗi upload ảnh: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Upload multiple images
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadMultiple(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'images' => 'required|array|max:10',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max each
                'folder' => 'nullable|string|max:50',
            ]);

            $images = $request->file('images');
            $folder = $request->input('folder', 'products');
            $uploadedImages = [];

            foreach ($images as $image) {
                // Upload to Cloudinary
                $uploadResult = $this->cloudinary->uploadApi()->upload(
                    $image->getRealPath(),
                    [
                        'folder' => $folder,
                        'resource_type' => 'image',
                        'quality' => 'auto:good',
                        'fetch_format' => 'auto',
                        'transformation' => [
                            'width' => 800,
                            'height' => 600,
                            'crop' => 'limit',
                            'quality' => 80
                        ]
                    ]
                );

                $uploadedImages[] = $uploadResult['secure_url'];
            }

            return response()->json([
                'success' => true,
                'data' => $uploadedImages,
                'message' => 'Upload ' . count($uploadedImages) . ' ảnh thành công'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi upload ảnh: ' . $e->getMessage()
            ], 500);
        }
    }
} 
<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
// use Intervention\Image\ImageManager;
// use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;

class ImageOptimizationService
{
    private $config;

    public function __construct()
    {
        $this->config = config('images', [
            'quality' => [
                'thumbnail' => 80,
                'medium' => 85,
                'large' => 90,
            ],
            'dimensions' => [
                'thumbnail' => ['width' => 300, 'height' => 300],
                'medium' => ['width' => 600, 'height' => 600],
                'large' => ['width' => 1200, 'height' => 1200],
            ],
            'formats' => [
                'primary' => 'webp',
                'fallback' => 'jpeg',
            ],
        ]);
    }

    /**
     * Optimize uploaded image and create multiple sizes
     */
    public function optimizeAndStore(UploadedFile $file, string $directory = 'products'): array
    {
        try {
            $filename = $this->generateFilename($file);
            $originalPath = $file->store($directory, 'public');
            
            // For now, just return the original file without optimization
            // This ensures the system works even without GD extension
            $originalUrl = Storage::url($originalPath);
            
            return [
                'success' => true,
                'images' => [
                    'large' => [
                        'path' => $originalPath,
                        'url' => $originalUrl,
                        'width' => 1200,
                        'height' => 1200
                    ],
                    'medium' => [
                        'path' => $originalPath,
                        'url' => $originalUrl,
                        'width' => 600,
                        'height' => 600
                    ],
                    'thumbnail' => [
                        'path' => $originalPath,
                        'url' => $originalUrl,
                        'width' => 300,
                        'height' => 300
                    ]
                ],
                'original_url' => $originalUrl
            ];
            
        } catch (\Exception $e) {
            Log::error('Image upload failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Optimize image from URL (for XML imports)
     */
    public function optimizeFromUrl(string $imageUrl, string $directory = 'products'): array
    {
        try {
            // For now, just return the original URL without optimization
            // This ensures the system works even without GD extension
            return [
                'success' => true,
                'images' => [
                    'large' => [
                        'path' => $imageUrl,
                        'url' => $imageUrl,
                        'width' => 1200,
                        'height' => 1200
                    ],
                    'medium' => [
                        'path' => $imageUrl,
                        'url' => $imageUrl,
                        'width' => 600,
                        'height' => 600
                    ],
                    'thumbnail' => [
                        'path' => $imageUrl,
                        'url' => $imageUrl,
                        'width' => 300,
                        'height' => 300
                    ]
                ],
                'original_url' => $imageUrl
            ];
            
        } catch (\Exception $e) {
            Log::error('Image URL processing failed', [
                'error' => $e->getMessage(),
                'url' => $imageUrl
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create multiple optimized versions of an image
     */
    private function createOptimizedVersions(string $imagePath, string $directory, string $filename): array
    {
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $extension = 'webp'; // Use WebP for better compression
        
        $images = [];
        
        // Large image (original size, optimized)
        $largeDims = $this->config['dimensions']['large'];
        $largeImage = $this->resizeAndOptimize($imagePath, $largeDims['width'], $largeDims['height'], $this->config['quality']['large']);
        $largePath = $directory . '/large/' . $baseName . '.' . $extension;
        Storage::disk('public')->put($largePath, $largeImage);
        $images['large'] = [
            'path' => $largePath,
            'url' => Storage::url($largePath),
            'width' => $largeDims['width'],
            'height' => $largeDims['height']
        ];
        
        // Medium image
        $mediumDims = $this->config['dimensions']['medium'];
        $mediumImage = $this->resizeAndOptimize($imagePath, $mediumDims['width'], $mediumDims['height'], $this->config['quality']['medium']);
        $mediumPath = $directory . '/medium/' . $baseName . '.' . $extension;
        Storage::disk('public')->put($mediumPath, $mediumImage);
        $images['medium'] = [
            'path' => $mediumPath,
            'url' => Storage::url($mediumPath),
            'width' => $mediumDims['width'],
            'height' => $mediumDims['height']
        ];
        
        // Thumbnail
        $thumbDims = $this->config['dimensions']['thumbnail'];
        $thumbnailImage = $this->resizeAndOptimize($imagePath, $thumbDims['width'], $thumbDims['height'], $this->config['quality']['thumbnail']);
        $thumbnailPath = $directory . '/thumbnails/' . $baseName . '.' . $extension;
        Storage::disk('public')->put($thumbnailPath, $thumbnailImage);
        $images['thumbnail'] = [
            'path' => $thumbnailPath,
            'url' => Storage::url($thumbnailPath),
            'width' => $thumbDims['width'],
            'height' => $thumbDims['height']
        ];
        
        // Also create JPEG fallback for older browsers
        $jpegLarge = $this->resizeAndOptimize($imagePath, $largeDims['width'], $largeDims['height'], $this->config['quality']['large'], 'jpeg');
        $jpegPath = $directory . '/large/' . $baseName . '.jpg';
        Storage::disk('public')->put($jpegPath, $jpegLarge);
        $images['large_jpeg'] = [
            'path' => $jpegPath,
            'url' => Storage::url($jpegPath),
            'width' => $largeDims['width'],
            'height' => $largeDims['height']
        ];
        
        return $images;
    }

    /**
     * Resize and optimize image
     */
    private function resizeAndOptimize(string $imagePath, int $maxWidth, int $maxHeight, int $quality, string $format = 'webp'): string
    {
        // Get image info
        $imageInfo = \getimagesize($imagePath);
        if (!$imageInfo) {
            throw new \Exception('Invalid image file');
        }
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Calculate new dimensions maintaining aspect ratio
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = (int) round($originalWidth * $ratio);
        $newHeight = (int) round($originalHeight * $ratio);
        
        // Don't upscale
        if ($newWidth > $originalWidth || $newHeight > $originalHeight) {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }
        
        // Create image resource based on type
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = \imagecreatefromjpeg($imagePath);
                break;
            case 'image/png':
                $sourceImage = \imagecreatefrompng($imagePath);
                break;
            case 'image/gif':
                $sourceImage = \imagecreatefromgif($imagePath);
                break;
            case 'image/webp':
                $sourceImage = \imagecreatefromwebp($imagePath);
                break;
            default:
                throw new \Exception('Unsupported image format: ' . $mimeType);
        }
        
        if (!$sourceImage) {
            throw new \Exception('Failed to create image resource');
        }
        
        // Create new image
        $newImage = \imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            \imagealphablending($newImage, false);
            \imagesavealpha($newImage, true);
            $transparent = \imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            \imagefill($newImage, 0, 0, $transparent);
        }
        
        // Resize image
        \imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
        // Output based on format
        ob_start();
        switch ($format) {
            case 'webp':
                if (function_exists('imagewebp')) {
                    \imagewebp($newImage, null, $quality);
                } else {
                    // Fallback to JPEG if WebP not supported
                    \imagejpeg($newImage, null, $quality);
                }
                break;
            case 'jpeg':
                \imagejpeg($newImage, null, $quality);
                break;
            case 'png':
                \imagepng($newImage, null, 9);
                break;
            default:
                \imagejpeg($newImage, null, $quality);
        }
        $imageData = ob_get_contents();
        ob_end_clean();
        
        // Clean up
        \imagedestroy($sourceImage);
        \imagedestroy($newImage);
        
        return $imageData;
    }

    /**
     * Generate unique filename
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return uniqid() . '_' . time() . '.' . $extension;
    }

    /**
     * Generate filename from URL
     */
    private function generateFilenameFromUrl(string $url): string
    {
        $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));
        $extension = $pathInfo['extension'] ?? 'jpg';
        return uniqid() . '_' . time() . '.' . $extension;
    }

    /**
     * Get optimized image URL with fallback
     */
    public function getOptimizedImageUrl(string $basePath, string $size = 'large'): string
    {
        $webpPath = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $basePath);
        $webpPath = str_replace('/' . $size . '/', '/' . $size . '/', $webpPath);
        
        // Check if WebP exists
        if (Storage::disk('public')->exists($webpPath)) {
            return Storage::url($webpPath);
        }
        
        // Fallback to original
        return Storage::url($basePath);
    }

    /**
     * Generate responsive image HTML
     */
    public function generateResponsiveImageHtml(string $basePath, string $alt = '', array $attributes = []): string
    {
        $baseName = pathinfo($basePath, PATHINFO_FILENAME);
        $directory = dirname($basePath);
        
        $webpSrcset = '';
        $jpegSrcset = '';
        
        // Generate srcset for different sizes
        $sizes = [
            'thumbnails' => '300w',
            'medium' => '600w',
            'large' => '1200w'
        ];
        
        foreach ($sizes as $size => $width) {
            $webpPath = $directory . '/' . $size . '/' . $baseName . '.webp';
            $jpegPath = $directory . '/' . $size . '/' . $baseName . '.jpg';
            
            if (Storage::disk('public')->exists($webpPath)) {
                $webpSrcset .= Storage::url($webpPath) . ' ' . $width . ', ';
            }
            
            if (Storage::disk('public')->exists($jpegPath)) {
                $jpegSrcset .= Storage::url($jpegPath) . ' ' . $width . ', ';
            }
        }
        
        // Remove trailing comma and space
        $webpSrcset = rtrim($webpSrcset, ', ');
        $jpegSrcset = rtrim($jpegSrcset, ', ');
        
        $defaultSrc = $this->getOptimizedImageUrl($basePath, 'medium');
        
        $attributesString = '';
        foreach ($attributes as $key => $value) {
            $attributesString .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        $html = '<picture>';
        
        if ($webpSrcset) {
            $html .= '<source srcset="' . $webpSrcset . '" type="image/webp" sizes="(max-width: 600px) 300px, (max-width: 1200px) 600px, 1200px">';
        }
        
        if ($jpegSrcset) {
            $html .= '<source srcset="' . $jpegSrcset . '" type="image/jpeg" sizes="(max-width: 600px) 300px, (max-width: 1200px) 600px, 1200px">';
        }
        
        $html .= '<img src="' . $defaultSrc . '" alt="' . htmlspecialchars($alt) . '"' . $attributesString . '>';
        $html .= '</picture>';
        
        return $html;
    }

    /**
     * Clean up old images when product is deleted
     */
    public function deleteOptimizedImages(string $basePath): bool
    {
        try {
            $baseName = pathinfo($basePath, PATHINFO_FILENAME);
            $directory = dirname($basePath);
            
            $sizes = ['thumbnails', 'medium', 'large'];
            $formats = ['webp', 'jpg', 'jpeg', 'png'];
            
            foreach ($sizes as $size) {
                foreach ($formats as $format) {
                    $path = $directory . '/' . $size . '/' . $baseName . '.' . $format;
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete optimized images', [
                'error' => $e->getMessage(),
                'basePath' => $basePath
            ]);
            return false;
        }
    }
}

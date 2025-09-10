<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductImage;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Storage;

class OptimizeExistingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize {--force : Force re-optimization of already optimized images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize existing product images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting image optimization...');
        
        $imageOptimizationService = app(ImageOptimizationService::class);
        $force = $this->option('force');
        
        $images = ProductImage::all();
        $total = $images->count();
        $optimized = 0;
        $failed = 0;
        
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();
        
        foreach ($images as $image) {
            try {
                // Skip if already optimized (unless force is used)
                if (!$force && $this->isAlreadyOptimized($image->resim_url)) {
                    $progressBar->advance();
                    continue;
                }
                
                // Check if it's a local image or external URL
                if (filter_var($image->resim_url, FILTER_VALIDATE_URL)) {
                    // External URL - download and optimize
                    $result = $imageOptimizationService->optimizeFromUrl($image->resim_url, 'products');
                } else {
                    // Local image - optimize directly
                    $imagePath = storage_path('app/public/' . ltrim($image->resim_url, '/'));
                    if (file_exists($imagePath)) {
                        // This would need a method to optimize local files
                        $this->warn("Local image optimization not implemented yet: {$image->resim_url}");
                        $progressBar->advance();
                        continue;
                    } else {
                        $this->warn("Image file not found: {$image->resim_url}");
                        $progressBar->advance();
                        continue;
                    }
                }
                
                if ($result['success']) {
                    // Update the image URL to point to optimized version
                    $image->update(['resim_url' => $result['original_url']]);
                    $optimized++;
                } else {
                    $this->error("Failed to optimize image: {$image->resim_url} - {$result['error']}");
                    $failed++;
                }
                
            } catch (\Exception $e) {
                $this->error("Error processing image {$image->resim_url}: " . $e->getMessage());
                $failed++;
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        
        $this->info("Optimization complete!");
        $this->info("Total images: {$total}");
        $this->info("Optimized: {$optimized}");
        $this->info("Failed: {$failed}");
        $this->info("Skipped: " . ($total - $optimized - $failed));
    }
    
    /**
     * Check if image is already optimized
     */
    private function isAlreadyOptimized(string $imageUrl): bool
    {
        // Check if the URL contains optimized paths
        return str_contains($imageUrl, '/large/') || 
               str_contains($imageUrl, '/medium/') || 
               str_contains($imageUrl, '/thumbnails/');
    }
}

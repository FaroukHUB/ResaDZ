<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Loueur;
use App\Models\Vehicle;
use App\Services\ImageService;
use Illuminate\Console\Command;

class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:convert-webp {--dry-run : Show what would be converted without actually converting}';

    protected $description = 'Convert all existing images to WebP format';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN - No files will be modified');
        }

        $this->info('Converting images to WebP...');

        $converted = 0;
        $failed = 0;

        // Vehicles
        $this->info('Processing vehicles...');
        $vehicles = Vehicle::whereNotNull('image')->get();
        foreach ($vehicles as $vehicle) {
            if ($vehicle->image && !str_ends_with($vehicle->image, '.webp')) {
                if ($dryRun) {
                    $this->line("  Would convert: {$vehicle->image}");
                } else {
                    $newPath = ImageService::convertToWebp($vehicle->image);
                    if ($newPath && $newPath !== $vehicle->image) {
                        $vehicle->updateQuietly(['image' => $newPath]);
                        $this->line("  Converted: {$vehicle->image} -> {$newPath}");
                        $converted++;
                    } else {
                        $failed++;
                    }
                }
            }
        }

        // Brands
        $this->info('Processing brands...');
        $brands = Brand::whereNotNull('logo')->get();
        foreach ($brands as $brand) {
            if ($brand->logo && !str_ends_with($brand->logo, '.webp')) {
                if ($dryRun) {
                    $this->line("  Would convert: {$brand->logo}");
                } else {
                    $newPath = ImageService::convertToWebp($brand->logo);
                    if ($newPath && $newPath !== $brand->logo) {
                        $brand->updateQuietly(['logo' => $newPath]);
                        $this->line("  Converted: {$brand->logo} -> {$newPath}");
                        $converted++;
                    } else {
                        $failed++;
                    }
                }
            }
        }

        // Categories
        $this->info('Processing categories...');
        $categories = Category::whereNotNull('image')->get();
        foreach ($categories as $category) {
            if ($category->image && !str_ends_with($category->image, '.webp')) {
                if ($dryRun) {
                    $this->line("  Would convert: {$category->image}");
                } else {
                    $newPath = ImageService::convertToWebp($category->image);
                    if ($newPath && $newPath !== $category->image) {
                        $category->updateQuietly(['image' => $newPath]);
                        $this->line("  Converted: {$category->image} -> {$newPath}");
                        $converted++;
                    } else {
                        $failed++;
                    }
                }
            }
        }

        // Hero Slides
        $this->info('Processing hero slides...');
        $slides = HeroSlide::whereNotNull('image')->get();
        foreach ($slides as $slide) {
            if ($slide->image && !str_ends_with($slide->image, '.webp')) {
                if ($dryRun) {
                    $this->line("  Would convert: {$slide->image}");
                } else {
                    $newPath = ImageService::convertToWebp($slide->image);
                    if ($newPath && $newPath !== $slide->image) {
                        $slide->updateQuietly(['image' => $newPath]);
                        $this->line("  Converted: {$slide->image} -> {$newPath}");
                        $converted++;
                    } else {
                        $failed++;
                    }
                }
            }
        }

        // Loueurs
        $this->info('Processing loueurs...');
        $loueurs = Loueur::query()->get();
        foreach ($loueurs as $loueur) {
            if ($loueur->logo && !str_ends_with($loueur->logo, '.webp')) {
                if ($dryRun) {
                    $this->line("  Would convert: {$loueur->logo}");
                } else {
                    $newPath = ImageService::convertToWebp($loueur->logo);
                    if ($newPath && $newPath !== $loueur->logo) {
                        $loueur->updateQuietly(['logo' => $newPath]);
                        $this->line("  Converted: {$loueur->logo} -> {$newPath}");
                        $converted++;
                    } else {
                        $failed++;
                    }
                }
            }

            if ($loueur->cover_image && !str_ends_with($loueur->cover_image, '.webp')) {
                if ($dryRun) {
                    $this->line("  Would convert: {$loueur->cover_image}");
                } else {
                    $newPath = ImageService::convertToWebp($loueur->cover_image);
                    if ($newPath && $newPath !== $loueur->cover_image) {
                        $loueur->updateQuietly(['cover_image' => $newPath]);
                        $this->line("  Converted: {$loueur->cover_image} -> {$newPath}");
                        $converted++;
                    } else {
                        $failed++;
                    }
                }
            }
        }

        $this->newLine();
        if ($dryRun) {
            $this->info('Dry run complete. Use without --dry-run to convert.');
        } else {
            $this->info("Conversion complete: {$converted} converted, {$failed} failed/skipped");
        }

        return Command::SUCCESS;
    }
}

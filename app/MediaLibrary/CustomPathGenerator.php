<?php

namespace App\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Illuminate\Support\Str;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        logger('PathGenerator', ['model_id' => $media->model_id, 'model_type' => $media->model_type]);
        return $this->getModelFolder($media) . $media->model_id . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'responsive-images/';
    }

    private function getModelFolder(Media $media): string
    {
        return Str::snake(class_basename($media->model_type)) . 's/';
    }
}

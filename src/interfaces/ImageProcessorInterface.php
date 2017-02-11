<?php
namespace LegiaiFenix\Arcadia\interfaces;

interface ImageProcessorInterface
{
    
    public function uploadImage($folder_path, $path);
    public function deleteImage($image_path);
    public function listImages($folder_path, $amount, $type);    
    
}
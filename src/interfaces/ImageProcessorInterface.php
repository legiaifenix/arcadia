<?php

interface ImageProcessorInterface
{
    
    public function uploadImage($folder_path);
    public function deleteImage($image_path);
    public function listImages($folder_path, $amount, $type);    
    
}
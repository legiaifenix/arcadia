<?php
namespace LegiaiFenix\Arcadia\model;


use LegiaiFenix\Arcadia\interfaces\ImageProcessorInterface;
use LegiaiFenix\Arcadia\services\ImageSupporter;

final class ArcadiaL implements ImageProcessorInterface
{
    protected $folder_path, $max_size;
    protected $permission = 0775;

    public function __construct($folder_path, $max_size)
    {
        $this->folder_path  = $folder_path;
        $this->max_size     = $max_size;
    }

    /**
     * ADD - Adds image file to folder strucutre.
     * Also creates new folder structure if none detected
     *
     * @param $field_name
     * @return bool|string
     */
    public function uploadImage($field_name)
    {
        if( empty($field_name) ){
            //todo throw empty exception
            return "No field name provided";
        }

        if( empty($_FILES[$field_name]) ){
            return "Could not find the file";
        }

        if($this->folderStructure() ) {
            return $this->addImage($field_name);
        }


        return false;
    }

    /**
     * FOLDERS - Checks folder structure and tries to create one if none exists
     * Returns result of it as it checks if they actually exists
     *
     * @return bool
     */
    private function folderStructure()
    {
        $folder_structure = [date('Y'), date('m')];
        foreach ($folder_structure as $folder) {
            $this->folder_path .= '/'.$folder;
            if( !file_exists($this->folder_path) ) {
                mkdir($this->folder_path, $this->permission);
            }
        }

        return file_exists($this->folder_path);
    }

    /**
     * GUARDIAN - Checks if path points to image. Any other file it does not allow further
     * process
     *
     * @param $file_path
     * @return bool
     */
    private function fileGuardian( $file_path )
    {
        if( strpos($file_path, '.png')
            || strpos($file_path, '.jpg')
            || strpos($file_path, '.jpeg')
            || strpos($file_path, '.svg')
            || strpos($file_path, '.gif')){
            return true;
        }
        return false;
    }

    private function isImage($field_name)
    {
        $allowed = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/svg',
        ];

        $type = $_FILES[$field_name]['type'];
        if( in_array($type, $allowed) ){
            $check = getimagesize($_FILES[$field_name]["tmp_name"]);
            if($check !== false) {
                if( $_FILES[$field_name]["size"] <= $this->max_size ){
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * UPLOAD - Uploads posted file to the folder
     *
     * @param $field_name
     * @return bool|string
     */
    private function addImage($field_name)
    {
        if( $this->isImage($field_name) ){
            $filename = ImageSupporter::parseFileName($_FILES[$field_name]['name']);
            if (move_uploaded_file($_FILES[$field_name]["tmp_name"], $this->folder_path.'/'.$filename)) {
                return $this->folder_path.'/'.$filename;
            }

            return false;
        }

        return false;
    }

    /**
     * DELETE - Pass image path to delete it. It checks if it is an image
     * and if it exists before deleting
     *
     * @param $image_path
     * @return bool
     */
    public function deleteImage($image_path)
    {
        return $this->deleteSingleFile($image_path);
    }

    /**
     * DELETE - Deletes single file
     *
     * @param $image_path
     * @return bool
     */
    private function deleteSingleFile($image_path)
    {
        if( file_exists($image_path) ) {
            if( $this->fileGuardian($image_path) ){
                return unlink($image_path);
            }
        } else {

            if( file_exists($this->folder_path.$image_path) ) {
                $image_path = $this->folder_path.$image_path;
            } else if( file_exists($this->folder_path.'/'.$image_path) ) {
                $image_path = $this->folder_path.'/'.$image_path;
            }

            if( file_exists($image_path) ) {
                if( $this->fileGuardian($image_path) ) {
                    return unlink($image_path);
                }
            }
        }
        return false;
    }

    /**
     * LIST - returns array of files in the folder
     * Allows choice of files to be added to the array
     *
     * @param $folder_path
     * @param int $amount
     * @param string $type
     */
    public function listImages($folder_path, $amount = -1, $type = "")
    {
        $files = [];

        if( substr($folder_path, -1) != "/" ) {
            $folder_path .= '/';
        }

        if (is_dir(realpath($_SERVER['DOCUMENT_ROOT'].$folder_path))){
            if( @empty($type) ) {
                $files = glob($_SERVER['DOCUMENT_ROOT'].$folder_path . "*");
            } else if( is_array($type) ) {
                foreach ($type as $t) {
                    $files = glob($_SERVER['DOCUMENT_ROOT'].$folder_path . "*.".str_replace('.', '', $t));
                }
            } else {
                $files = glob($_SERVER['DOCUMENT_ROOT'].$folder_path . "*.".str_replace('.', '', $type));
            }
        }
        
        if( $amount != -1 ) {
           $files = array_slice($files, 0, $amount);
        }
        
        return $files;
    }
    
    
    /**
     * @return mixed
     */
    public function getMaxSize()
    {
        return $this->max_size;
    }

    /**
     * @param mixed $max_size
     */
    public function setMaxSize($max_size)
    {
        $this->max_size = $max_size;
    }

    /**
     * @return mixed
     */
    public function getFolderPath()
    {
        return $this->folder_path;
    }

    /**
     * @param mixed $folder_path
     */
    public function setFolderPath($folder_path)
    {
        $this->folder_path = $folder_path;
    }
    
    
    
    

}
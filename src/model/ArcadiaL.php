<?php
namespace LegiaiFenix\Arcadia\model;


use LegiaiFenix\Arcadia\interfaces\ImageProcessorInterface;
use LegiaiFenix\Arcadia\services\ImageSupporter;

final class ArcadiaL implements ImageProcessorInterface
{
    protected $folder_path, $targetFolder,$max_size;
    protected $permission   = 0775;
    protected $WPStyle      = true;

    public function __construct($folder_path, $safe,  $max_size)
    {
        $this->folder_path  = $folder_path;
        $this->targetFolder = $safe;
        $this->max_size     = $max_size;
    }

    /**
     * GET MAIN PATH - Glues together the root path and the target desired dir
     *
     * @return string
     */
    private function getMainPath()
    {
        return $this->folder_path.'/'.$this->targetFolder;
    }

    /**
     * ADD - Adds image file to folder strucutre.
     * Also creates new folder structure if none detected
     *
     * @param $field_name
     * @return bool|string
     */
    public function uploadImage($field_name, $path = "")
    {
        if( empty($field_name) ){
            //todo throw empty exception
            return "No field name provided";
        }

        if( empty($_FILES[$field_name]) ){
            return "Could not find the file";
        }

        $this->parseUserAddedFolder($path);


        if($this->folderStructure() ) {
            //uses WP folder structure
            return $this->addImage($field_name);
        }

        return false;
    }

    private function parseUserAddedFolder($path)
    {
        if( @!empty($path) ){
            $this->WPStyle = false;
            //makes sure root folde rwas not also passed and parses name sent
            $path = ImageSupporter::parseFolderStructure(ImageSupporter::removeWordFromString($this->folder_path, $path));
            $path = $this->checkPathExistence($path);
        }
        return $path;
    }

    /**
     * VALIDATION - checks if given path exists to know if needs creating
     * If it does not pass any validation sense then FORCES WP structure!
     *
     * @param $path
     * @return bool
     */
    public function checkPathExistence($path)
    {
        if( !file_exists($path) ) {
            
            if( file_exists($this->getMainPath().'/'.$path) ){
                $this->targetFolder .= '/'.$path;
                return true;
            }

            //check if main root folder was forgotten
            if( file_exists($this->folder_path.'/'.$path) ){
                $this->targetFolder = $path;
                return true;
            }

            if( file_exists($this->folder_path) && !file_exists($this->folder_path.'/'.$path) ) {
                mkdir($this->folder_path.'/'.$path, $this->permission);
                $this->targetFolder = $path;
                return true;
            }

            //could not pass the numerous validations so forces WP structure
            $this->WPStyle = true;
            return false;
        }

        return true;
    }

    /**
     * FOLDERS - Checks folder structure and tries to create one if none exists
     * Returns result of it as it checks if they actually exists
     *
     * @return bool
     */
    private function folderStructure()
    {
        if( $this->WPStyle ){
            $folder_structure = [date('Y'), date('m')];
            foreach ($folder_structure as $folder) {
                $this->targetFolder .= '/'.$folder;
                if( !file_exists($this->getMainPath()) ) {
                    mkdir($this->getMainPath(), $this->permission);
                }
            }
        }

        return file_exists($this->getMainPath());
    }

    /**
     * VALIDATION - Checks if path points to image. Any other file it does not allow further
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
            || strpos($file_path, '.gif')
            || strpos($file_path, '.ico') ){
            return true;
        }
        return false;
    }

    /**
     * VALIDATION - checks if item owns an image type extention
     *
     * @param $field_name
     * @return bool
     */
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

            if (move_uploaded_file($_FILES[$field_name]["tmp_name"], $this->getMainPath().'/'.$filename)) {
                $this->WPStyle = true;
                return $this->getMainPath().'/'.$filename;
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

            if( file_exists($this->getMainPath().$image_path) ) {
                $image_path = $this->getMainPath().$image_path;
            } else if( file_exists($this->getMainPath().'/'.$image_path) ) {
                $image_path = $this->getMainPath().'/'.$image_path;
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


    public function removeWPFolderStructure()
    {
        $this->WPStyle = false;
    }

    public function activateWPFolderStructure()
    {
        $this->WPStyle = true;
    }
    
    
    

}
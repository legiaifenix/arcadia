<?php
namespace LegiaiFenix\Arcadia;

use LegiaiFenix\Arcadia\model\ArcadiaL;
use LegiaiFenix\Arcadia\services\ImageSupporter;

class ArcadiaFactory
{
    protected $folder_path, $targetFolder;
    protected $factory;


    public function __construct( $main_folder = "public", $safe = "uploads", $max_size = 2000)
    {
        $this->environmentPreparation($main_folder, $safe);
        $this->factory = new ArcadiaL( $this->folder_path, $safe, $max_size);
    }

    /**
     * INIT - Prepares the environment for the variables going to be used by the models
     * sets folder structure for main path
     * 
     * @param $main_folder
     * @param $safe
     */
    private function environmentPreparation($main_folder, $safe)
    {
        $this->folder_path = str_replace( $main_folder, '', $_SERVER['DOCUMENT_ROOT']);
        $this->folder_path .= ImageSupporter::parseFileName($main_folder);
        $this->targetFolder = ImageSupporter::parseFileName($safe);
    }

    /**
     * @return ArcadiaL
     */
    public function getFactory()
    {
        return $this->factory;
    }


    
    

}
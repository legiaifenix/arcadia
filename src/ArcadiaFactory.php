<?php

class ArcadiaFactory
{
    protected $folder_path, $main_folder;
    protected $factory;


    public function __construct( $architecture = "", $main_folder = "public", $safe = "uploads", $max_size = 2000)
    {
        $this->environmentPreparation($main_folder, $safe);
        $this->factory = $this->chooseArchitecture($architecture, $max_size);
        
        if( @empty($this->factory) ){
            throw new \ArchitectureNotSupportedException($architecture." architecture not supported", "the provided architecture is not supported at this moment. Please select only supported architectures: Laravel");
        }
    }

    private function chooseArchitecture($architecture, $max_size)
    {
        switch ($architecture) {
            case "laravel":
                return new ArcadiaL($this->folder_path, $max_size);
            default:
                return new ArcadiaL($this->folder_path, $max_size);
        }
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
        $this->folder_path = str_replace( 'public', '', $_SERVER['DOCUMENT_ROOT']);
        $this->folder_path .= str_replace('/', '', $main_folder).'/'.str_replace('/', '', $safe);
    }

    /**
     * @return ArcadiaL
     */
    public function getFactory()
    {
        return $this->factory;
    }
    
    

}
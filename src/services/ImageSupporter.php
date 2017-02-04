<?php

class ImageSupporter
{

    public static function parseFileName($filename)
    {
        $specialChars = ['|', '!', '"', '#', '$', '%', '&', '/', '(', ')', '=', '?', '«', '»', '{', '[', ']', '}'
            , '*', '\'', '+', '^', 'ª', 'º', ',', '..', ';', ':', '<', '>'];
        
        $filename = strtolower($filename);
        $filename = str_replace($specialChars, '', $filename);
        $filename = str_replace(' ', '-', $filename);
        $filename = date('d-m-Y').'_'.date('h-i-s').'_'.$filename;
        
        return $filename;
    }

}
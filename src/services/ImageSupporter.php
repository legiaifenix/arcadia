<?php
namespace LegiaiFenix\Arcadia\services;

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

    /**
     * REMOVE - Deletes word from string
     * 
     * @param $word
     * @param $sample
     * @return mixed
     */
    public static function removeWordFromString($word, $sample)
    {
        return str_replace($word, '', $sample);
    }

}
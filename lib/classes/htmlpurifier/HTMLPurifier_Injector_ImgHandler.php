<?php

/**
 * Injector to convert included img's to studip-files
 */
class HTMLPurifier_Injector_ImgHandler extends HTMLPurifier_Injector
{
    public $name = 'ImgHandler';
    public $needed = array('img' => array('src'));

    public function handleElement(&$token)
    {
        if ($token->name === 'img' && isset($token->attr['src'])) {

            //Trigger fileupload

            $test = "dostuff";
        }
    }
}

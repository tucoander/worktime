<?php

namespace Apontamentos;

class Template {
    
    public function __construct() {
        
    }

    public function header($css_path, $page)
    {
        $header = '
        <!doctype html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            ';
        $header .= '
            <title> '.$page.' </title>';
        foreach ($css_path as $key=> $value){
            $header .= '
            <link rel="stylesheet" href="'.$value.'">';
        }
        $header .= '
        </head>
        ';

        return $header;
    }
    public function levelFolder($url){
        $path_string = '';
        $path_level = substr_count(str_replace("/src/", "", $url), '/');

            for($i=0;$i< $path_level;$i++){
                $path_string .= '../';
            }
        return $path_string;
    }

    public function navbar($menu)
    {
        $navbar = '';
        $navbar .= '
        <header>
            <ul>';

        foreach($menu as $page => $path) {
            if(is_array($path)) {
                $navbar .= '
                <li>
                    <a href="#">'.$page. '</a>';
                $navbar .= '
                    <ul>';

                foreach($path as $paged => $pathd){
                    $navbar .= '
                        <li>
                            <a href="'.$pathd.'">'.$paged. '</a>
                        </li>';
                }
                $navbar .= '
                    </ul>
                </li>';
            }
            else{
                $navbar .= '
                <li>
                    <a href="'.$path.'">'.$page. '</a>';
            }
        }
        $navbar .= '
            </ul>
        </header>
        ';

        return $navbar;
    }
    
}

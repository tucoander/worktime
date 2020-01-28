<?php

namespace Apontamentos;

class Acesso
{
    public function __construct() {
        session_start();
    }

    public function loginUser($usr, $page)
    {
        $_SESSION['usr_id'] = $usr;
        $_SESSION['usrlgd'] = 1;
        $_SESSION['lstpge'] = $page;
    }

    public function logoutUser()
    {
        session_destroy();
    }

    public function checkUsrLogged($status, $path)
    {
        if($status == 1){
            return 1;
        }
        else{
            header('Location: '.$path);
        }
    }

    public function checkMenusAllow($acesso, $pages)
    {
        if(($acesso['usrrol'] == 'ADM' && ($acesso['fun_id'] == 1 || $acesso['fun_id'] == 2) ) || $acesso['usr_id'] == 'FAA5LOV' ){
            return $pages;
        }
        else{
            unset($pages['Home']);
            unset($pages['Dashboard']);
            unset($pages['Gestor']);
            return $pages;
        }
    }
    
    public function checkPagesAllow($path, $pages)
    {
        $contador = 0;
        $path_procurado = substr($path, strpos($path, '/src'));

        foreach($pages as $key => $values){
            if(is_array($values)){
                foreach($values as $k => $value){
                    if($path_procurado ==  substr($value, strpos($value, '/src')) ) {
                        $contador++;
                    }
                }
            }
        }
        
        return $contador > 0 ? 1 : 0;
        
    }



    
}

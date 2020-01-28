<?php
    namespace Apontamentos;
    require('../../../../../vendor/autoload.php');

    //Classes
    $template = new Template();
    $rotas = new Routes($template->levelFolder($_SERVER['PHP_SELF']));
    $sql = new Database($rotas->paths['Database']);
    
    if(!isset($_GET['id'])){
        echo 'Parametro não enviado';
    }
    else{
        $excluido = $sql->excluirApontamento($_GET['id']);
        if($excluido == 1) {
            echo 'Apontamento '.$_GET['id'].' excluído com sucesso.';
        }
        else{
            echo 'Erro na exclusão.';
        }
    }
<?php
    namespace Apontamentos;
    require('../../../../../vendor/autoload.php');

    //Classes
    $template = new Template();
    $rotas = new Routes($template->levelFolder($_SERVER['PHP_SELF']));
    $sql = new Database($rotas->paths['Database']);
    $fechamento = new Fechamento($rotas->paths['Config']);
    $regras = new Regras();
    $limite = $fechamento->getLastDayAvailable();
    $validacao = $regras->checkTimeDelete($_GET, $limite);
    var_dump($_GET);
    if(!isset($_GET['id'])){
        echo 'Parametro não enviado';
    }
    else{
        if( $validacao == 1) {
            $excluido = $sql->excluirApontamento($_GET['id']);
            if($excluido == 1) {
                echo 'Apontamento '.$_GET['id'].' excluído com sucesso.';
            }
            else{
                echo 'Erro na exclusão.';
            }
        }
        else{
            print_r($validacao);
        }
    }
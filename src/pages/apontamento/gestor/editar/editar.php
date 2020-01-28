<?php
    namespace Apontamentos;
    require('../../../../../vendor/autoload.php');

    //Classes
    $template = new Template();
    $rotas = new Routes($template->levelFolder($_SERVER['PHP_SELF']));
    $sql = new Database($rotas->paths['Database']);
    $fechamento = new Fechamento($rotas->paths['Config']);
    $regras = new Regras();

    $indisponibilidade = $sql->semanaIndisponivel($fechamento->dia_limite, $fechamento->horario_limite);
    $apontamento = $sql->checkWeek($_POST['adddte']);
    $validacao = $regras->checkTimeUpdate($indisponibilidade, $_POST, $apontamento);
    

    if(!isset($_POST['id'])){
        echo 'Parametro não enviado';
    }
    else{
        if( $validacao == 1) {
            $editado = $sql->editarApontamento($_POST);
            if($editado == 1) {
                echo 'Apontamento '.$_POST['id'].' atualizado com sucesso.';
            }
            else{
                echo 'Erro na edição.';
            }
        }
        else{
            print_r($validacao);
        }
    }
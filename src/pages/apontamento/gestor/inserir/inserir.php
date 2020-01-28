<?php
    namespace Apontamentos;
    require('../../../../../vendor/autoload.php');

    //Classes
    $template = new Template();
    $rotas = new Routes($template->levelFolder($_SERVER['PHP_SELF']));
    $sql = new Database($rotas->paths['Database']);
    $fechamento = new Fechamento($rotas->paths['Config']);
    $regras = new Regras();

    //Variaveis de apoio
    $campos = $regras->checkFormInsert($_POST);

    //execução
    if($campos != 1){
        echo $campos;
    }else{
        $indisponibilidade = $sql->semanaIndisponivel($fechamento->dia_limite, $fechamento->horario_limite);
        $apontamento = $sql->checkWeek($_POST['adddte']);
        
        if($regras->checkTimeInsert($indisponibilidade, $_POST, $apontamento) == 1) {
            
            $sql->inserirApontamento($_POST); 
            echo '
            <div class="alert alert-success" role="alert">
                Apontamento lançado!
            </div>
            <script>
            jQuery("#inserir").each (function(){
                this.reset();
              });
            </script>
            ';
        }
        else {
            echo $regras->checkTimeInsert($indisponibilidade, $_POST, $apontamento);
        }
    }

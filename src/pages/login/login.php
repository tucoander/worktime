<?php

namespace Apontamentos;

require('../../../vendor/autoload.php');

$sql = new Database('../../database/apontamentos.sqlite');
$acesso = new Acesso();


if(isset($_POST['usr_id']) && isset($_POST['usrpsw'])){
    if($sql->checkUsrPsw(strtoupper($_POST['usr_id']), $_POST['usrpsw']) == 1){
        $acesso->loginUser(strtoupper($_POST['usr_id']), $_POST['uri']);
        echo '
        <div class="loader">
            Loading...
        </div>
        '; 
        echo '
        <script>
            setTimeout(function () {
                window.location = "../apontamento/usuario/inserir/";
            }, 3000);
        </script>
        ';
    }
    else{
        echo '
        <div>
            Usuário ou senha incorreta, tente novamente!!!
        </div>
        ';
    }
    
}



?>
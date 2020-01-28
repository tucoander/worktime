<?php

namespace Apontamentos;

require('../../../../../vendor/autoload.php');

$template = new Template();
$rotas = new Routes($template->levelFolder($_SERVER['PHP_SELF']));
$sql = new Database($rotas->paths['Database']);
$acesso = new Acesso();

$page = "Excluir Apontamento";
$css_path =  array(
    "0" => $rotas->paths['CSS']['navbar'],
    "1" => $rotas->paths['CSS']['main'],
);
$menus = $rotas->pages;

$perfil = $sql->checkUserAcess($_SESSION['usr_id']);
$perfil = $perfil[0];
$menus = $acesso->checkMenusAllow($perfil, $menus);
$acesso->checkUsrLogged($_SESSION['usrlgd'], $template->levelFolder($_SERVER['PHP_SELF']));

// Header da página
echo $template->header($css_path, $page);

//Body da página
echo '
    <body class="container">
    ';
echo $template->navbar($menus);


if (!isset($_GET['id'])) {
    echo '
    <div>
        Erro no envio dos parametros
    </div>
    ';
} else {
    $deletar = $sql->apontamento($_GET['id']);
    $deletar = $deletar[0];
    if($deletar['usr_id'] == $_SESSION['usr_id']){
?>
    <div class="main">
        <div class="titulo-area">
            <div class="titulo-pag">
                <h1>Excluir Apontamento</h1>
            </div>
        </div>
        <div class="form" id="form-inserir">
            <form id="excluir">
                <div class="inputs-group">
                    <div class="input-label">
                        <label for="adddte">Data</label>
                        <input type="date" id="adddte" value="<?php echo $deletar['logdte']; ?>" disabled>
                    </div>
                    <div class="input-label">
                        <label for="fr_tim">Hora Início</label>
                        <input id="fr_tim" type="time" value="<?php echo $deletar['fr_logtim']; ?>" disabled>
                    </div>
                    <div class="input-label">
                        <label for="to_tim">Hora Fim</label>
                        <input id="to_tim" type="time" value="<?php echo $deletar['to_logtim']; ?>" disabled>
                    </div>
                </div>
                <div class="inputs-group">
                    <div class="input-label">
                        <label for="prd_id">Produto</label>
                        <input id="to_tim" type="text" value="<?php echo $deletar['prd_id']; ?>" disabled>
                    </div>
                    <div class="input-label">
                        <label for="opr_id">Operações</label>
                        <input id="to_tim" type="text" value="<?php echo $deletar['opr_id']; ?>" disabled>
                    </div>
                    <div class="input-label">
                        <label for="usrask">Solicitante</label>
                        <input type="text" id="usrask" placeholder="Nome" value="<?php echo $deletar['to_usr_id']; ?>" disabled>
                    </div>
                </div>
                <div class="inputs-group">
                    <div class="input-label">
                        <label for="usrobs">Observação</label>
                        <textarea rows="3" id="usrobs" placeholder="Observações." disabled><?php echo $deletar['usrobs']; ?></textarea>
                    </div>
                </div>
                <div>
                    <input type="hidden" id="uri" name="uri" value="<?php echo $_SERVER['PHP_SELF'] ?>">
                    <input type="hidden" id="id" name="id" value="<?php echo $_GET['id']; ?>">
                    <input type="hidden" id="usr_id" name="usr_id" value="DEV">
                    <input type="hidden" id="logged_usr_id" name="logged_usr_id" value="DEV_L">
                </div>
                <div class="buttons-group">
                    <div>
                        <button type="submit" id="bt-cancelar">Cancelar</button>
                        <button type="submit" id="bt-excluir">Confirmar Exclusão</button>
                    </div>
                    <div>
            </form>
        </div>

        <div class="inputs-group">
            <div id="response"  class="response">
                <br>
            </div>
        </div>
        <div class="footer"></div>
    </div>

<?php
    }
    else{
        echo '
        <div>
            <p>Você tentou acessar um apontamento de um outro usuário.</p>
        </div>
        ';
    }
}
?>
</body>
<script src="../../../../assets/js/jquery.js"></script>
<script src="./excluir.js"></script>

</html>
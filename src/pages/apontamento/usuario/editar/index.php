<?php

namespace Apontamentos;

require('../../../../../vendor/autoload.php'); 

$template = new Template();
$rotas = new Routes($template->levelFolder($_SERVER['PHP_SELF']));
$sql = new Database($rotas->paths['Database']);
$acesso = new Acesso();

$page = "Editar Apontamento";
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
$produtos = ($sql->checkProduto());
$operacao = ($sql->checkOperacao());


if(!isset($_GET['id'])){
    echo '
    <div>
        Erro no envio dos parametros
    </div>
    ';
}
else {
    $editar = $sql->apontamento($_GET['id']);
    $editar = $editar[0];
    if($editar['usr_id'] == $_SESSION['usr_id']){
?>
            <div class="main">
                <div class="titulo-area">
                    <div class="titulo-pag">
                        <p>Bem-vindo, <?php echo $perfil['usrnme'] ?>.</p>
                    </div>
                </div>
                <div class="titulo-area">
                    <div class="titulo-pag">
                        <h1>Editar Apontamento</h1>
                    </div>
                </div>
                <div class="form" id="form-inserir">
                    <form id="editar">
                    <div class="inputs-group">
                        <div class="input-label">
                            <label for="adddte">Data</label>
                            <input type="date" id="adddte" value="<?php echo $editar['logdte']; ?>">
                        </div>
                        <div class="input-label">
                            <label for="fr_tim">Hora Início</label>
                            <input id="fr_tim" type="time" value="<?php echo $editar['fr_logtim']; ?>">
                        </div>
                        <div class="input-label">
                            <label for="to_tim">Hora Fim</label>
                            <input id="to_tim" type="time"  value="<?php echo $editar['to_logtim']; ?>">
                        </div>
                    </div>
                    <div class="inputs-group">
                        <div class="input-label">
                            <label for="prd_id">Produto</label>
                            <select id="prd_id">
                                <option value="">Selecione...</option>
                                <?php
                                foreach ($produtos as $key => $value) {
                                    if($editar['prd_id'] == $value['prd_id']){
                                        echo '<option value="' . $value['prd_id'] . '" selected>' . $value['prdnme'] . '</option>';
                                    }
                                    else{
                                        echo '<option value="' . $value['prd_id'] . '">' . $value['prdnme'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="input-label">
                            <label for="opr_id">Operações</label>
                            <select id="opr_id">
                                <option value="">Selecione...</option>
                                <?php
                                foreach ($operacao as $key => $value) {
                                    if($editar['opr_id'] == $value['opr_id']){
                                        echo '<option value="' . $value['opr_id'] . '" selected>' . $value['oprnme'] . ' - ' . $value['ctynme'] . '</option>';
                                    }
                                    else{
                                        echo '<option value="' . $value['opr_id'] . '">' . $value['oprnme'] . ' - ' . $value['ctynme'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="input-label">
                            <label for="usrask">Solicitante</label>
                            <div>
                                <input type="text" id="usrask" placeholder="Nome"  value="<?php echo $editar['to_usr_id']; ?>" >
                            </div>
                        </div>
                    </div>
                    <div class="inputs-group">
                        <div class="input-label">
                            <label for="usrobs">Observação</label>
                            <textarea  rows="3" id="usrobs" placeholder="Observações você pode colocar aqui"><?php echo $editar['usrobs']; ?></textarea>
                        </div>
                    </div>
                    <div>
                        <input type="hidden" id="id" name="id" value="<?php echo $_GET['id']; ?>">
                        <input type="hidden" id="uri" name="uri" value="<?php echo $_SERVER['PHP_SELF'] ?>">
                        <input type="hidden" id="usr_id" name="usr_id" value="<?php echo $_SESSION['usr_id'] ?>">
                        <input type="hidden" id="logged_usr_id" name="logged_usr_id" value="<?php echo $_SESSION['usr_id'] ?>">
                    </div>
                    <div  class="buttons-group">
                        <button type="submit" id="bt-cancelar">Cancelar</button>
                        <button type="submit" id="bt-editar">Editar</button>
                    </div>
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
<script src="./editar.js"></script>

</html>
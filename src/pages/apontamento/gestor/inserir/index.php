<?php

namespace Apontamentos;

require('../../../../../vendor/autoload.php');

$template = new Template();
$rotas = new Routes($template->levelFolder($_SERVER['PHP_SELF']));
$sql = new Database($rotas->paths['Database']);
$acesso = new Acesso();

$page = "Inserir Apontamento";
$css_path =  array(
    "0" => $rotas->paths['CSS']['navbar'],
    "1" => $rotas->paths['CSS']['main'],
);
$menus = $rotas->pages;


$perfil = $sql->checkUserAcess($_SESSION['usr_id']);
$perfil = $perfil[0];
$menus = $acesso->checkMenusAllow($perfil, $menus);
$acesso->checkUsrLogged($_SESSION['usrlgd'], $template->levelFolder($_SERVER['PHP_SELF']));
$autorizado = $acesso->checkPagesAllow($_SERVER['REQUEST_URI'], $menus);

echo $template->header($css_path, $page);

//Body da página
echo '
    <body class="container">
    ';
echo $template->navbar($menus);
$produtos = ($sql->checkProduto());
$operacao = ($sql->checkOperacao());
$users = ($sql->listUsers());
if ($autorizado == 1) {
?>
    <div class="main">
        <div class="titulo-area">
            <div class="titulo-pag">
                <p>Bem-vindo, <?php echo $perfil['usrnme'] ?>.</p>
            </div>
        </div>
        <div class="titulo-area">
            <div class="titulo-pag">
                <h1>Apontamentos</h1>
            </div>
        </div>
        <div class="form" id="form-inserir">
            <form id="inserir">
                <div class="inputs-group">
                    <div class="input-label">
                        <label for="adddte">Data</label>
                        <input type="date" id="adddte">
                    </div>
                    <div class="input-label">
                        <label for="fr_tim">Hora Início</label>
                        <input id="fr_tim" type="time">
                    </div>
                    <div class="input-label">
                        <label for="to_tim">Hora Fim</label>
                        <input id="to_tim" type="time">
                    </div>
                </div>

                <div class="inputs-group">
                    <div class="input-label">
                        <label for="usr_id">Usuário</label>
                        <select id="usr_id">
                            <option value="">Selecione...</option>
                            <?php
                            foreach ($users as $key => $value) {
                                echo '<option value="' . $value['usr_id'] . '">' . $value['usrnme'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-label">
                        <label for="prd_id">Produto</label>
                        <select id="prd_id">
                            <option value="">Selecione...</option>
                            <?php
                            foreach ($produtos as $key => $value) {
                                echo '<option value="' . $value['prd_id'] . '">' . $value['prdnme'] . '</option>';
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
                                echo '<option value="' . $value['opr_id'] . '">' . $value['oprnme'] . ' - ' . $value['ctynme'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-label">
                        <label for="usrask">Solicitante</label>
                        <input type="text" id="usrask" placeholder="Nome">
                    </div>
                </div>
                <div class="inputs-group">

                    <div class="input-label">
                        <label for="usrobs">Observação</label>
                        <textarea rows="3" id="usrobs" placeholder="Observações você pode colocar aqui"></textarea>
                    </div>
                </div>
                <div class="buttons-group">
                    <div>
                        <button type="submit" id="bt-inserir">Inserir</button>
                    </div>
                </div>

                <div>
                    <input type="hidden" id="uri" name="uri" value="<?php echo $_SERVER['PHP_SELF'] ?>">
                    <input type="hidden" id="usr_id" name="usr_id" value="<?php echo $_SESSION['usr_id'] ?>">
                    <input type="hidden" id="logged_usr_id" name="logged_usr_id" value="<?php echo $_SESSION['usr_id'] ?>">
                </div>
                <div class="inputs-group">
                    <div id="response" class="response">
                        <br>

                    </div>
                </div>
            </form>
        </div>

    </div>

    <div class="footer">
    </div>
<?php
} else {
    echo '
    <div class="main">
        <div class="titulo-area">
            <div class="titulo-pag">
                <h1>Apontamentos</h1>
            </div>
        </div>
        <div class="form" id="form-inserir">
            <p>Você não possui acesso a essa página. Favor utilize os menus para navegação.</p>.
        </div>
    ';
}
?>
</body>
<script src="../../../../assets/js/jquery.js"></script>
<script src="./inserir.js"></script>

</html>
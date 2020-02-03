<?php

namespace Apontamentos;

require('../../../../vendor/autoload.php');

$template = new Template();
$rotas = new Routes($template->levelFolder($_SERVER['PHP_SELF']));
$sql = new Database($rotas->paths['Database']);
$acesso = new Acesso();

$page = "Gráfico Ponderado Apontamento";
$css_path =  array(
    "0" => $rotas->paths['CSS']['navbar'],
    "1" => $rotas->paths['CSS']['main'],
    "2" => $rotas->libs['ChartJS']['css'],
);
$menus = $rotas->pages;

$perfil = $sql->checkUserAcess($_SESSION['usr_id']);
$perfil = $perfil[0];
$menus = $acesso->checkMenusAllow($perfil, $menus);
$acesso->checkUsrLogged($_SESSION['usrlgd'], $template->levelFolder($_SERVER['PHP_SELF']));
$autorizado = $acesso->checkPagesAllow($_SERVER['REQUEST_URI'], $menus);

// Header da página
echo $template->header($css_path, $page);

//Body da página
echo '
    <body class="container">
    ';
echo $template->navbar($menus);

$users = ($sql->listUsers());
// if ($autorizado == 1) {
?>
<div class="main">
    <div class="titulo-area">
        <div class="titulo-pag">
            <h1>Gráfico Operações</h1>
        </div>
    </div>
    <div class="form" id="form-consultar">
        <form method="get" id="consultar">
            <div class="inputs-group">
                <div class="input-label">
                    <label for="de">De:</label>
                    <input type="date" id="de" name="de">
                </div>
                <div class="input-label">
                    <label for="para">Até:</label>
                    <input type="date" id="para" name="para">
                </div>
                
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
                <div class="buttons-group">
                    <button type="button" id="bt-consultar">Pesquisar</button>
                    <button type="button" id="bt-limpar">Limpar</button>
                </div>
            </div>
            <div>
                <input type="hidden" id="uri" name="uri" value="<?php echo $_SERVER['PHP_SELF'] ?>">
                <input type="hidden" id="usr_id" name="usr_id" value="<?php echo $_SESSION['usr_id'] ?>">
                <input type="hidden" id="logged_usr_id" name="logged_usr_id" value="<?php echo $_SESSION['usr_id'] ?>">
            </div>
        </form>
    </div>    
    <div id="response" >
    </div>
<?php
// } else {
//     echo '
//     <div class="main">
//         <div class="titulo-area">
//             <div class="titulo-pag">
//                 <h1>Apontamentos</h1>
//             </div>
//         </div>
//         <div class="form" id="form-inserir">
//             <p>Você não possui acesso a essa página. Favor utilize os menus para navegação.</p>
//         </div>
//     ';
// }
?>
<script src="../../../assets/js/jquery.js"></script>
<script src="<?php echo $rotas->libs['ChartJS']['js'];?>"></script>
<script src="./standard.js"></script>
</body>


</html>



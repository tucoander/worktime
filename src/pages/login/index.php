<?php

    namespace Apontamentos;
    require('../../../vendor/autoload.php');

    //Classes
    $template = new Template();
    $acesso = new Acesso();
    $acesso->logoutUser();

    //Variaveis de apoio
    $css_path =  array(
        "0"=> "./login.css", 
        "1"=> "../../assets/css/loader.css"
    );
    $page = "Login";

    echo $template->header($css_path, $page);
?>
        <div class="container">
            <div>
                <img src="../../assets/img/logo.png" alt="logo" style="height: 64px;">
            </div>
            <form id="form-login" class="form">
                <label for="usr_id">Usuário</label>
                <input type="text" id="usr_id" style="text-transform:uppercase;">
                <label for="usrpsw">Senha</label>
                <input type="password" id="usrpsw">
                <input type="hidden" id="uri" name="uri" value="<?php echo $_SERVER['PHP_SELF'] ?>">
                <button id="bt-login" type="submit">Login</button>
                <div id="response">
                </div>
            </form>
        </div>
    </body>
    <script src="../../assets/js/jquery.js"></script>
    <script src="./login.js"></script>
</html>
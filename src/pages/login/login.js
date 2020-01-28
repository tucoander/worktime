jQuery(document).ready(function(){
    jQuery("#form-login").submit(function(){
        return false;
    });   
    // carregando a função para o envio
    jQuery("#bt-login").click(function(){
        login();
    });
    // limpando a div antes de um novo envio
    function login() {
        jQuery("#response").empty();
        // pegando os campos do formulário
        var usr_id = jQuery("#usr_id").val();
        var usrpsw = jQuery("#usrpsw").val();
        var uri = jQuery("#uri").val();
 
        jQuery.ajax({
            type: "POST",
            url: "login.php",
            dataType: "html",
            data: {
                usr_id: usr_id,
                usrpsw: usrpsw,
                uri: uri
            },
        // enviado com sucesso
            success: function(response){
                jQuery("#response").append(response);
                
            },
            // quando houver erro
            error: function(){
                alert("Erro no Ajax");
            }
        });
    }
});
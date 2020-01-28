jQuery(document).ready(function(){
    jQuery("#consultar").submit(function(){
        return false;
    });   
    // carregando a função para o envio
    jQuery("#bt-consultar").click(function(){
        consultar_apontamento();
    });

    jQuery("#bt-limpar").click(function(){
        limpar_consulta();
    });

    // limpando a div antes de um novo envio
    function consultar_apontamento() {
        jQuery("#response").empty();
        // pegando os campos do formulário
        var from = jQuery("#de").val();
        var to = jQuery("#para").val();
        var usr_id = jQuery("#usr_id").val();
        var logged_usr_id = jQuery("#logged_usr_id").val();
        var uri = jQuery("#uri").val();
 
        jQuery.ajax({
            type: "GET",
            url: "consultar.php",
            dataType: "html",
            data: {
                from: from, 
                to: to,
                usr_id: usr_id, 
                logged_usr_id: logged_usr_id , 
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

    function limpar_consulta(){
        jQuery("#response").empty();
    }
});
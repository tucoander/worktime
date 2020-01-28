jQuery(document).ready(function(){
    jQuery("#excluir").submit(function(){
        return false;
    });   
    // carregando a função para o envio
    jQuery("#bt-excluir").click(function(){
        excluir_apontamento();
    });

    jQuery("#bt-cancelar").click(function(){
        cancelar();
    });

    // limpando a div antes de um novo envio
    function excluir_apontamento() {
        jQuery("#response").empty();
        // pegando os campos do formulário
        var id = jQuery("#id").val();
        var usr_id = jQuery("#usr_id").val();
        var logged_usr_id = jQuery("#logged_usr_id").val();
        var uri = jQuery("#uri").val();
 
        jQuery.ajax({
            type: "GET",
            url: "excluir.php",
            dataType: "html",
            data: {
                id: id, 
                usr_id: usr_id, 
                logged_usr_id: logged_usr_id , 
                uri: uri
            },
        // enviado com sucesso
            success: function(response){
                jQuery("#response").append(response);
                setTimeout(function(){ window.history.go(-1); }, 3000);         
            },
            // quando houver erro
            error: function(){
                alert("Erro no Ajax");
            }
        });
    }

    function cancelar(){
        window.history.go(-1);
    }
});
jQuery(document).ready(function(){
    jQuery("#editar").submit(function(){
        return false;
    });   
    // carregando a função para o envio
    jQuery("#bt-editar").click(function(){
        editar_apontamento();
    });

    jQuery("#bt-cancelar").click(function(){
        cancelar();
    });

    // limpando a div antes de um novo envio
    function editar_apontamento() {
        jQuery("#response").empty();
        // pegando os campos do formulário
        var adddte = jQuery("#adddte").val();
        var fr_tim = jQuery("#fr_tim").val();
        var to_tim = jQuery("#to_tim").val();
        var prd_id = jQuery("#prd_id").val();
        var opr_id = jQuery("#opr_id").val();
        var usrask = jQuery("#usrask").val();
        var usrobs = jQuery("#usrobs").val();
        var id = jQuery("#id").val();
        var usr_id = jQuery("#usr_id").val();
        var logged_usr_id = jQuery("#logged_usr_id").val();
        var uri = jQuery("#uri").val();
 
        jQuery.ajax({
            type: "POST",
            url: "editar.php",
            dataType: "html",
            data: {
                id: id, 
                adddte: adddte, 
                fr_tim: fr_tim,
                to_tim: to_tim, 
                prd_id: prd_id , 
                opr_id: opr_id,
                usrask: usrask,
                usrobs: usrobs,
                usr_id: usr_id, 
                logged_usr_id: logged_usr_id , 
                uri: uri
            },
        // enviado com sucesso
            success: function(response){
                jQuery("#response").append(response);
                setTimeout(function(){ window.history.go(-1); }, 13000);         
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
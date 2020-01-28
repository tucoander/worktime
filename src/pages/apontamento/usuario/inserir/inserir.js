jQuery(document).ready(function(){
    jQuery("#inserir").submit(function(){
        return false;
    });   
    // carregando a função para o envio
    jQuery("#bt-inserir").click(function(){
        inserir_apontamento();
    });
    // limpando a div antes de um novo envio
    function inserir_apontamento() {
        jQuery("#response").empty();
        // pegando os campos do formulário
        var adddte = jQuery("#adddte").val();
        var fr_tim = jQuery("#fr_tim").val();
        var to_tim = jQuery("#to_tim").val();
        var prd_id = jQuery("#prd_id").val();
        var opr_id = jQuery("#opr_id").val();
        var usrask = jQuery("#usrask").val();
        var usrobs = jQuery("#usrobs").val();
        var usr_id = jQuery("#logged_usr_id").val();
        var logged_usr_id = jQuery("#logged_usr_id").val();
        var uri = jQuery("#uri").val();
 
        jQuery.ajax({
            type: "POST",
            url: "inserir.php",
            dataType: "html",
            data: {
                adddte: adddte, 
                fr_tim: fr_tim,
                to_tim: to_tim, 
                prd_id: prd_id , 
                opr_id: opr_id,
                usrask: usrask,
                usrobs: usrobs,
                usr_id: usr_id,
                logged_usr_id: logged_usr_id,
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
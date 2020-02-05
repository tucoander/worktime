jQuery(document).ready(function(){
    jQuery("#consultar").submit(function(){
        return false;
    });
    load_page_grafico_ponderado_apontamento();
    // carregando a função para o envio
    jQuery("#bt-consultar").click(function(){
        grafico_ponderado_apontamento();
    });

    jQuery("#bt-limpar").click(function(){
        limpar_consulta();
    });

    // limpando a div antes de um novo envio
    function grafico_ponderado_apontamento() {
        limpar_consulta();
        var month = getCurrentMonth();
        // pegando os campos do formulário

        if(jQuery("#de").val() == "" && jQuery("#para").val() == "" ){
            var from = month['primeiroDia'];
            var to = month['ultimoDia'];
        }
        else{
            var from = jQuery("#de").val();
            var to = jQuery("#para").val();
        }
        
        var usr_id = jQuery("#usr_id").val();
        var logged_usr_id = jQuery("#logged_usr_id").val();
        var uri = jQuery("#uri").val();
        console.log(from);
        console.log(to);
        jQuery.ajax({
            type: "GET",
            url: "standard.php",
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
    function load_page_grafico_ponderado_apontamento(){
        limpar_consulta();
        var month = getCurrentMonth();

        // pegando os campos do formulário
        var from = month['primeiroDia'];
        var to = month['ultimoDia'];
        var usr_id = jQuery("#usr_id").val();
        var logged_usr_id = jQuery("#logged_usr_id").val();
        var uri = jQuery("#uri").val();
 
        jQuery.ajax({
            type: "GET",
            url: "standard.php",
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

    function getCurrentMonth(){
        // pega o mês atual e retorna o primeiro e o último dia
        var date = new Date();
        var primeiroDia = new Date(date.getFullYear(), date.getMonth(), 1);
        var ultimoDia = new Date(date.getFullYear(), date.getMonth() + 1, 0);

        var primeiroDia = primeiroDia.toJSON().substring(0, 10);
        var ultimoDia = ultimoDia.toJSON().substring(0, 10);

        var intervalo = [];
        intervalo['primeiroDia'] = primeiroDia;
        intervalo['ultimoDia'] = ultimoDia;
        console.log(intervalo);
        return intervalo;
    }
});
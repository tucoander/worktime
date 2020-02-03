<?php

namespace Apontamentos;

require('../../../../vendor/autoload.php');

// Declaração das Classes utilizadas 
$template = new Template();
$rotas = new Routes($template->levelFolder($_SERVER['PHP_SELF']));
$sql = new Database($rotas->paths['Database']);
$fechamento = new Fechamento($rotas->paths['Config']);
$regras = new Regras();
$grafico = new Dashboard();

//-----------------------------------------------------------------------
// Listar todos usuarios
$users = ($sql->listUsers());
$usr_arr = array();
$conta_pessoas = 0;
foreach ($users as $k => $usr) {
    $usr_arr[$usr['usr_id']] = array();
    $conta_pessoas++;
}

//-----------------------------------------------------------------------
// Listar intervalo
if(date_create($_GET['to']) > date_create(date('Y-m-d'))){
    $ultima_data = date_format(date_create(date('Y-m-d')),'Y-m-d');
}
else{
    $ultima_data = $_GET['to'];
}
$date_interval = ($regras->generateDateInterval($_GET['from'],  $ultima_data));

$dte_arr = array();
foreach ($date_interval as $i => $dte) {
    $dte_arr[$dte] = 0;
}


$key_data['from'] = min($date_interval);
$key_data['to'] =  max($date_interval);

//-----------------------------------------------------------------------
// listar operações 
$soma_opr = 0;
$operations = ($sql->checkOperacao());
$opr_arr = array();
foreach ($operations as $i => $opr) {
    $opr_arr[$opr['oprnme']] = array();
}
$opr_arr['Indisponivel'] = array();
$opr_arr['Disponibilidade'] = array();
$opr_arr['peso'] = array();
//-----------------------------------------------------------------------
// Arrumando o array de usuario para ter 
// o usuário e a operação como chaves de um array
foreach ($usr_arr as $key => $value) {
    foreach ($opr_arr as $k => $v) {
        $usr_arr[$key][$k] = 0;
    }
}

// Soma teste
$soma_min = 0;
$disponibilidade = (8*60+24);
$linhas =0;
// Select do banco
// traz todos os dados
$res = ($sql->listAllRecords($key_data));

// abre por usuario
foreach ($usr_arr as $key => $value) {
    // abre por operação
    $usr_arr[$key]['Disponibilidade'] =  $dte_arr;
    foreach ($opr_arr as $k => $v) {
        // verifica se o select retornou algo
        if ($res['status'] === 1) {
            // verifica linha linha do resultado select
            foreach ($res['data'] as $key_base => $value_base) {
                // se fornecido o usuário aplica filtro de usuário
                if(isset($_GET['usr_id']) && !empty($_GET['usr_id'])){
                    // filtro de usuário
                    if($_GET['usr_id'] == $value_base['usr_id']){
                        if ($key == $value_base['usr_id'] && $k == $value_base['oprnme']) {
                            if($usr_arr[$key]['Disponibilidade'][$value_base['logdte']] == 15120){ echo $linha;}
                            // cálculo de minutos trabalhados agrupando por opr e usr
                            $datetime1 = date_create($value_base['logdte'] . ' ' . $value_base['fr_logtim']);
                            $datetime2 = date_create($value_base['logdte'] . ' ' . $value_base['to_logtim']);
                            $interval = date_diff($datetime1, $datetime2);
                            $min = $interval->h;
                            $min = $min * 60;
                            $min = $min + $interval->i;
                            $usr_arr[$key][$k] = $usr_arr[$key][$k] + $min;
                            $soma_min = $soma_min + $min;   
                            $usr_arr[$key]['Disponibilidade'][$value_base['logdte']] = $usr_arr[$key]['Disponibilidade'][$value_base['logdte']] - $min;
                            
                        }
                    }
                }
                else{
                    if ($key == $value_base['usr_id'] && $k == $value_base['oprnme']) {
                        // cálculo de minutos trabalhados agrupando por opr e usr
                        $datetime1 = date_create($value_base['logdte'] . ' ' . $value_base['fr_logtim']);
                        $datetime2 = date_create($value_base['logdte'] . ' ' . $value_base['to_logtim']);
                        $interval = date_diff($datetime1, $datetime2);
                        $min = $interval->h;
                        $min = $min * 60;
                        $min = $min + $interval->i;
                        $usr_arr[$key][$k] = $usr_arr[$key][$k] + $min;
                        $soma_min = $soma_min + $min;
                        $usr_arr[$key]['Disponibilidade'][$value_base['logdte']] = $usr_arr[$key]['Disponibilidade'][$value_base['logdte']] - $min;
                    }
                }
            }
        }
    }
}

$linhas= 0;

foreach($usr_arr as $usr => $val_usr){
    if(is_array($val_usr)){
        foreach($val_usr as $opr => $val_opr){
            if(is_array($val_opr)){
                $soma = 0;
                foreach($val_opr as $dia => $val_dia){
                    if((date_format(date_create(date($dia)),'w') == 0) || (date_format(date_create(date($dia)),'w') == 6)){ }
                    else{
                        $soma = $soma + ($disponibilidade + $val_dia);
                        $linhas++;
                    }
                }
                $usr_arr[$usr]['Disponibilidade'] = $soma;
            }
        }
    }
}

// instancia o array de operações com zero no lugar do array vazio
foreach($opr_arr as $key_opr => $val_opr){
        $opr_arr[$key_opr] = 0;
}
// declaração variáveis usadas no gráfico
$soma = 0;

$labels = array();
$data = array();

$disponibilidade = (8*60 + 24) * $regras->getDateInterval($_GET['from'], $ultima_data ) * sizeof($usr_arr);

// cálcula a porcentagem que será exibida no gráfico
foreach($opr_arr as $key_opr => $val_opr){
    foreach($usr_arr as $key_usr => $val_usr){
        $opr_arr[$key_opr] = $opr_arr[$key_opr] + $val_usr[$key_opr];
    }
}

$indisponibilidade = array();


// se a soma for zero significa que não houve registros no periodo
if($soma_min != 0){
    // alimenta o array parametro do gráfico
    foreach($opr_arr as $key_opr => $val_opr){
        if($key_opr == 'peso' || $key_opr == 'Indisponivel'){
        }
        else{
            $labels[] = $key_opr;
            $data[] = @number_format(($val_opr/$disponibilidade)*100, 2, '.', '') ;
        }
    }

    $labels = implode("', '",$labels);
    $data = implode(', ', $data);

    echo $grafico->printGraph($labels, $data);
}
else{
    echo '
    <div style="text-align: center;">
        Não existem dados nesse filtro.
    </div>';
}




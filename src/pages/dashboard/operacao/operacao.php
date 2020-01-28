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
$date_interval = ($regras->generateDateInterval($_GET['from'], $_GET['to']));
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
$opr_arr['Indisponibilidade'] = array();
$opr_arr['peso'] = array();

//-----------------------------------------------------------------------
// Arrumando o array de usuario para ter 
// o usuário e a operação como chaves de um array
foreach ($usr_arr as $key => $value) {
    foreach ($opr_arr as $k => $v) {
        $usr_arr[$key][$k] = 0;
    }
}

// Select do banco
// traz todos os dados
$res = ($sql->listAllRecords($key_data));
// abre por usuario
foreach ($usr_arr as $key => $value) {
    // abre por operação
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
                            // cálculo de minutos trabalhados agrupando por opr e usr
                            $datetime1 = date_create($value_base['logdte'] . ' ' . $value_base['fr_logtim']);
                            $datetime2 = date_create($value_base['logdte'] . ' ' . $value_base['to_logtim']);
                            $interval = date_diff($datetime1, $datetime2);
                            $min = $interval->h;
                            $min = $min * 60;
                            $min = $min + $interval->i;
                            $usr_arr[$key][$k] = $usr_arr[$key][$k] + $min;
                        }
                    }
                }else{
                    if ($key == $value_base['usr_id'] && $k == $value_base['oprnme']) {
                        // cálculo de minutos trabalhados agrupando por opr e usr
                        $datetime1 = date_create($value_base['logdte'] . ' ' . $value_base['fr_logtim']);
                        $datetime2 = date_create($value_base['logdte'] . ' ' . $value_base['to_logtim']);
                        $interval = date_diff($datetime1, $datetime2);
                        $min = $interval->h;
                        $min = $min * 60;
                        $min = $min + $interval->i;
                        $usr_arr[$key][$k] = $usr_arr[$key][$k] + $min;
                    }
                }
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

// cálcula a porcentagem que será exibida no gráfico
foreach($opr_arr as $key_opr => $val_opr){
    foreach($usr_arr as $key_usr => $val_usr){
        $opr_arr[$key_opr] = $opr_arr[$key_opr] + $val_usr[$key_opr];
        $soma = $soma + $val_usr[$key_opr];
    }
}
// se a soma for zero significa que não houve registros no periodo
if($soma != 0){
    // alimenta o array parametro do gráfico
    foreach($opr_arr as $key_opr => $val_opr){
        if($key_opr == 'peso' || $key_opr == 'Indisponibilidade'){
        }
        else{
            $labels[] = $key_opr;
            $data[] = @number_format(($val_opr/$soma)*100, 2, '.', '') ;
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
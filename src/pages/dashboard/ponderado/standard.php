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

$date = array(
    'from'=> $_GET['from'],
    'to'=> $regras->validateIntervalDate($_GET['to'])
);

$interval = $regras->generateDateInterval($date['from'], $date['to']);

$date['interval'] = $regras->generateDateArray($interval);
$users = $regras->generateUserArray($sql->listUsers());
$oper = $regras->generateOperArray($sql->checkOperacao());

// simplificando usuarios e operações
foreach ($users as $k_u => $v_u) {
    foreach ($oper as $k_o => $v_o) {
        $users[$k_u][$k_o] = 0;
        $oper[$k_o] = 0;
    }
}
// verifica se tem registros naquela pesquisa
$registros = $sql->listAllGroupedRecords($date);
if($registros['status'] == 1){

    if(isset($_GET['usr_id']) && !empty($_GET['usr_id'])){
        foreach($users as $usr_id => $values){
            if($usr_id != $_GET['usr_id']){
                unset($users[$usr_id]);
            }
        }
    }

    foreach($registros['data'] as $k => $v){
        if (array_key_exists($v['usr_id'], $users)) {
            $users[$v['usr_id']][$v['oprnme']] = $users[$v['usr_id']][$v['oprnme']] + $v['t_p_wrkday'];
            $users[$v['usr_id']]['Disponibilidade'] = $users[$v['usr_id']]['Disponibilidade'] - $v['t_p_wrkday'];
        }
    }
    $somaWorkDay = 0;
    // create table com horario de trabalho
    $workDay = $sql->listWorkDayUser();

    foreach($users as $usr_id => $values){
        foreach($date['interval'] as $dte => $dte_values){
            foreach($workDay['data'] as $k => $w_values){
                if($w_values['usr_id'] == $usr_id){
                    $users[$usr_id]['Disponibilidade'] = $users[$usr_id]['Disponibilidade'] + $w_values['pon_wrkday'] * (1 - $regras->fadiga);
                    $somaWorkDay = $somaWorkDay + $w_values['pon_wrkday'];
                }  
            }
        }
        $users[$usr_id]['total'] = $somaWorkDay;
        $users[$usr_id]['total'] = $users[$usr_id]['total'] * (1 - $regras->fadiga);
        $somaWorkDay = 0;
    }
    // formatar dados para o gráfico
    $graph = array(
        "labels" => array(),
        "data" => array(),
    );

    foreach($users as $usr_id => $opr_values){
        foreach($opr_values as $opr_id => $time){
            $oper[$opr_id] = $oper[$opr_id] + $time;
        }
    }

    foreach($oper as $oprnme => $time){
        if($oprnme != 'total'){
            $graph['labels'][] = $oprnme;
            $graph['data'][] = @number_format(($time/$oper['total'])*100, 2, '.', '') ;
        }
        
    }

    $graph['labels'] = implode("', '",$graph['labels']);
    $graph['data'] = implode(', ', $graph['data']);

    echo $grafico->printGraph($graph['labels'], $graph['data']);
}
else{

}

 


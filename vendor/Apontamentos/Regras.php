<?php 

namespace Apontamentos;

class Regras {
    public $jornada = 8 + (1/60)*24;
    public $fadiga = 0.145;

    public function checkFormInsert($post)
    {
        $requerido = '';
        $contador_requerido = 0;

        if(empty($_POST['adddte'])){
            $requerido .= 'O campo Data é obrigatório<br>';
            $contador_requerido++;
        }

        if(empty($_POST['fr_tim'])){
            $requerido .= 'O campo Hora Início é obrigatório<br>';
            $contador_requerido++;
        }

        if(empty($_POST['to_tim'])){
            $requerido .= 'O campo Hora Fim é obrigatório<br>';
            $contador_requerido++;
        }

        if(empty($_POST['prd_id'])){
            $requerido .= 'O campo Produto é obrigatório<br>';
            $contador_requerido++;
        }

        if(empty($_POST['opr_id'])){
            $requerido .= 'O campo Operação é obrigatório<br>';
            $contador_requerido++;
        }
        if($contador_requerido > 0){
            $requerido .= 'Campos obrigatórios não preenchidos: '.$contador_requerido.'<br>';
        }
        
        return $contador_requerido == 0 ? 1: $requerido;
    }

    public function checkTimeInsert($apontamento, $semana)
    { 
        $page = str_replace('/index.php', '', substr( $_POST['uri'] , strpos($_POST['uri'],'/pages') ));

        if($page == '/pages/apontamento/usuario/inserir' ){
            if($apontamento['adddte'] <= $semana['domingo'] ){
                $mensagem = 'O fechamento semanal já ocorreu ';
                $mensagem .= 'e você está realizando o lançamento em ';
                $mensagem .= $apontamento['adddte'].' '.$apontamento['fr_tim'];
                $mensagem .= ' até ';
                $mensagem .= $apontamento['adddte'].' '.$apontamento['to_tim'];
                return $mensagem;
            }
            else{
                return 1;
            }
        }else if($page  == '/pages/apontamento/gestor/inserir'){
            if($apontamento['adddte'] > $semana['domingo'] ){
                $mensagem = 'O fechamento semanal ainda não ocorreu ';
                $mensagem .= 'e você está realizando o lançamento em ';
                $mensagem .= $apontamento['adddte'].' '.$apontamento['fr_tim'];
                $mensagem .= ' até ';
                $mensagem .= $apontamento['adddte'].' '.$apontamento['to_tim'];
                $mensagem .= '. Gestores podem realizar lançamentos após o fechamento.';
                return $mensagem;
            }
            else{
                return 1;
            }
        }
        else{
            return 0;
        }

        
    }

    public function checkTimeUpdate($apontamento, $semana)
    { 
        $page = str_replace('/index.php', '', substr( $apontamento['uri'] , strpos($apontamento['uri'],'/pages') ));
        
        if($page == '/pages/apontamento/usuario/editar' ){
            if($apontamento['adddte'] <= $semana['domingo'] ){
                $mensagem = 'O fechamento semanal já ocorreu ';
                $mensagem .= 'e você está realizando o lançamento em ';
                $mensagem .= $apontamento['adddte'].' '.$apontamento['fr_tim'];
                $mensagem .= ' até ';
                $mensagem .= $apontamento['adddte'].' '.$apontamento['to_tim'];
                return $mensagem;
            }
            else{
                return 1;
            }
        }else if($page  == '/pages/apontamento/gestor/editar'){
            if($apontamento['adddte'] > $semana['domingo']){
                $mensagem = 'O fechamento semanal ainda não ocorreu ';
                $mensagem .= 'e você está realizando o lançamento em ';
                $mensagem .= $apontamento['adddte'].' '.$apontamento['fr_tim'];
                $mensagem .= ' até ';
                $mensagem .= $apontamento['adddte'].' '.$apontamento['to_tim'];
                $mensagem .= '. Gestores podem realizar lançamentos após o fechamento.';
                return $mensagem;
            }
            else{
                return 1;
            }
        }
        else{
            return 0;
        }

        
    }

    public function checkTimeDelete($apontamento, $semana)
    { 
        $page = str_replace('/index.php', '', substr( $apontamento['uri'] , strpos($apontamento['uri'],'/pages') ));
        
        if($page == '/pages/apontamento/usuario/excluir' ){
            if($apontamento['adddte'] <= $semana['domingo'] ){
                $mensagem = 'O fechamento semanal já ocorreu ';
                $mensagem .= 'e você está realizando o lançamento em ';
                $mensagem .= $apontamento['adddte'].' '.$apontamento['fr_tim'];
                $mensagem .= ' até ';
                $mensagem .= $apontamento['adddte'].' '.$apontamento['to_tim'];
                return $mensagem;
            }
            else{
                return 1;
            }
        }else if($page  == '/pages/apontamento/gestor/excluir'){
            if($apontamento['adddte'] > $semana['domingo']){
                $mensagem = 'O fechamento semanal ainda não ocorreu ';
                $mensagem .= 'e você está realizando o lançamento em ';
                $mensagem .= $apontamento['adddte'].' '.$apontamento['fr_tim'];
                $mensagem .= ' até ';
                $mensagem .= $apontamento['adddte'].' '.$apontamento['to_tim'];
                $mensagem .= '. Gestores podem realizar lançamentos após o fechamento.';
                return $mensagem;
            }
            else{
                return 1;
            }
        }
        else{
            return 0;
        }

        
    }
    public function checkBlockedWeek($indisponibilidade)
    { 
        if(date(strtotime($indisponibilidade['fechamento'])) > date('now')){
            $semanaBloqueada = $indisponibilidade['semana_indisponivel'] - 1;
        }
        else {
            $semanaBloqueada = $indisponibilidade['semana_indisponivel'];
        }
        return $semanaBloqueada;
    }

    public function checkAno($indisponibilidade, $apontamento, $semana)
    {
        var_dump($indisponibilidade);
        var_dump($apontamento);
        var_dump($semana);

        echo date('Y', strtotime($indisponibilidade['fechamento']));

        echo date('Y', strtotime($apontamento['adddte']));
    }

    public function generateDateInterval($from, $to)
    {
        if(!empty($from) && !empty($to)){
            $intervalo =  date_diff(date_create($from), date_create($to));
            $intervalo =  $intervalo->d;
            $data_form = $from;
        }
        else if(!empty($from) && empty($to)){
            $intervalo =  0;
            $data_form = $from;
        }else if (empty($from) && !empty($to)) {
            $intervalo =  0;
            $data_form = $to;
        }
        else{
            $intervalo =  0;
            $data_form = date('Y-m-d');
        }
        
        for ($i = 0; $i <= $intervalo; $i++) {
            $response[] = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($data_form)));
        }

        return $response;
    }

    public function getDateInterval($from, $to)
    {
        if(!empty($from) && !empty($to)){
            $intervalo =  date_diff(date_create($to), date_create($from));
            $intervalo =  $intervalo->d;
            $data_form = $from;
        }
        else if(!empty($from) && empty($to)){
            $intervalo =  0;
            $data_form = $from;
        }else if (empty($from) && !empty($to)) {
            $intervalo =  0;
            $data_form = $to;
        }
        else{
            $intervalo =  0;
            $data_form = date('Y-m-d');
        }
        $semfds = 0;
        for ($i = 0; $i <= $intervalo; $i++) {
            $response[] = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($data_form)));
            $temp = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($data_form)));
            $temp = date_format(date_create($temp), 'w');
            if($temp > 0 && $temp < 6){
                $semfds++;
            }
        }

        return $semfds;
    }

    public function generateUserArray($select)
    {
        $temp = array();
        foreach ($select as $k => $usr) {
            $temp[$usr['usr_id']] = array();
        }
        return $temp;
    }
    public function validateIntervalDate($to)
    {
        $temp = '';
        if(date_create($to) > date_create(date('Y-m-d'))){
            $temp = date_format(date_create(date('Y-m-d')),'Y-m-d');
        }
        else{
            $temp = $to;
        }
        return $temp;
    }

    public function generateDateArray($intervalo)
    {
        $temp = array();
        foreach ($intervalo as $k => $date) {
            $weekday = date_format(date_create($date), 'w') ;
            if($weekday > 0 && $weekday < 6){
                $temp[$date] = array();
            }             
        }
        return $temp;
    }

    public function generateOperArray($select)
    {
        $temp = array();
        foreach ($select as $k => $opr) {
            $temp[$opr['oprnme']] = array();
        }
        $temp['Indisponivel'] = array();
        $temp['Disponibilidade'] = array();
        $temp['total'] = array();

        return $temp;
    }
}

?>

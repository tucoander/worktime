<?php

namespace Apontamentos;

class Fechamento {
    public $dia_limite;
    public $horario_limite;
    
    public function __construct($config_path) {
        $arquivo = file_get_contents($config_path);
        $json = json_decode($arquivo, true);
        $this->dia_limite = $json['fechamento']['dia'];
        $this->horario_limite = $json['fechamento']['horario'];
    }

    public function getLastDayAvailable()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $data = new \DateTime();
        $hoje = $data->format('Y-m-d H:i:s');

        $contador = 20;
        $array_datas = array();
        $data->modify('-10 day');

        while($contador != 0){
            $contador = $contador - 1;
            $data->modify('+1 day');
            $dados['data'] = $data->format('Y-m-d');
            $dados['semana'] = $data->format('W');
            $dados['dia'] = $data->format('w');
            $array_datas[] = $dados;
        }

        $fechamento = array();
        $temp = array();
        $response = array();

        foreach($array_datas as $values){
            if($values['dia'] == $this->dia_limite){
                $temp['domingo'] = date_create($values['data'])->modify('-2 day')->format('Y-m-d');
                $temp['terca'] =  date_create($values['data'])->format('Y-m-d');
                $temp['sabado'] = date_create($values['data'])->modify('+4 day')->format('Y-m-d');
                $fechamento[] = $temp;
            }
        }

        foreach($fechamento as $value){
            $terca = date_create($value['terca'].' '.$this->horario_limite);
            if($hoje > $terca->format('Y-m-d')){
                $response = $value;
            }
        }

        return $response;
    }

    
}
?>
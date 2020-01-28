<?php

namespace Apontamentos;

class Dashboard
{
    public function __construct() {
        
    }

    public function graphOpr($resumo) {
        $soma = 0;
        $labels = array();
        $percent = array();

        foreach($resumo as $value){
            $soma = $soma + $value['work'];
        }

        foreach($resumo as $value){
            $labels[] = $value['oprnme'];
            $percent[] = $value['work']/$soma;
        }

        $labels = implode("','", $labels);
        $percent = implode("','", $percent);

        $retorno = array();
        $retorno['labels'] = $labels;
        $retorno['percent'] = $percent;

        return $retorno;
    }

    public function getHourFormat($horas)
    {
        
        $hora = $horas > 0 ? floor($horas): ceil($horas); 
        $minutos = round(($horas - $hora)*60);
        $string = $hora < 9 ? '0'.$hora: $hora;
        $string .= ':';
        $string .= $minutos < 9 ? '0'.$minutos: $minutos;

        return $string;
    }

    public function getPercentFormat($number, $dec)
    {   
        $string = round($number * 100, $dec);
        $string = $string.' %';
        return $string;
    }

    public function tableOpr($resumo) {
        $soma = 0;
        foreach($resumo as $value){
            $soma = $soma + $value['work'];
        }
        $table = '
        <table class="tabela-grafico">
            <thead>
                <tr>
                    <th>Operação</th>
                    <th>Horas</th>
                    <th>Percentual</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach($resumo as $value){
            $table .= '<tr>';
            $table .= '<td>'.$value['oprnme'].'</td>';
            $table .= '<td>'.self::getHourFormat($value['work']).'</td>';
            $table .= '<td>'.self::getPercentFormat($value['work']/$soma, 3).'</td>';
            $table .= '</tr>';

        }

        $table .= '
            <tbody>
        </table>
        ';

        return $table;
    }

    public function printGraph($labels, $data)
    {
        echo "
        <div style='margin: 10px;'>
            <canvas id='graph' width='450' height='450'></canvas>
        </div>
        <script>
            var ctx = document.getElementById('graph');
            var chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: [ '".$labels."'],
                    datasets: [{
                        label: 'Operações',
                        backgroundColor: [
                            'rgb(0, 86, 145)',
                            'rgb(0, 142, 207)',
                            'rgb(0, 168, 176)',
                            'rgb(120, 190, 32)',
                            'rgb(0, 98, 73)',
                            'rgb(185, 2, 118)',
                            'rgb(80, 35, 127)',
                            'rgb(82, 95, 107)',
                            'rgb(0, 96, 155)',
                            'rgb(0, 152, 217)',
                            'rgb(0, 178, 186)',
                            'rgb(120, 200, 42)',
                            'rgb(0, 108, 83)',
                            'rgb(185, 12, 128)',
                            'rgb(80, 45, 137)',
                            'rgb(82, 105, 117)',
                            'rgb(0, 106, 165)',
                            'rgb(0, 162, 227)',
                            'rgb(0, 188, 196)',
                            'rgb(120, 210, 52)',
                            'rgb(0, 118, 93)',
                            'rgb(185, 22, 138)',
                            'rgb(80, 55, 147)',
                            'rgb(82, 115, 127)',
                            
                        ],
                        borderColor: 'rgb(255, 255, 255)',
                        data: [ ".$data."
                        ]
                    }]
                },

                // Configuration options go here
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    aspectRatio: 1.2
                }
            });
        </script>
        ";
    }
    
}

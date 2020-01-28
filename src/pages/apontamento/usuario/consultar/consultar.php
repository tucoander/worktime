<?php
    namespace Apontamentos;
    require('../../../../../vendor/autoload.php');

    //Classes
    $template = new Template();
    $rotas = new Routes($template->levelFolder($_SERVER['PHP_SELF']));
    $sql = new Database($rotas->paths['Database']);
    $fechamento = new Fechamento($rotas->paths['Config']);
    $regras = new Regras();
    $validacao = $fechamento->getLastDayAvailable();

    $indisponibilidade = $sql->semanaIndisponivel($fechamento->dia_limite, $fechamento->horario_limite);
    
    $lista = $sql->listRecords($_GET);
    
    $tabela = '
    <table class="tabela-consultar">
        <thead >
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Operação</th>
                <th>País</th>
                <th>Solicitante</th>
                <th>Data</th>
                <th>Hora Início</th>
                <th>Hora Fim</th>
                <th>Observações</th>
                <th style="width: 250px;">Ação</th>
            </tr>
        </thead>
        <tbody>
    ';
    foreach($lista as $key => $value){
        $tabela .= '
            <tr>
                <td>'.$value['log_id'].'</td>
                <td>'.$value['prdnme'].'</td>
                <td>'.$value['oprnme'].'</td>
                <td>'.$value['ctynme'].'</td>
                <td>'.$value['to_usr_id'].'</td>
                <td>'.$value['logdte'].'</td>
                <td>'.$value['fr_logtim'].'</td>
                <td>'.$value['to_logtim'].'</td>
                <td>'.$value['usrobs'].'</td>';
        if($value['logdte'] >= $validacao['domingo'] ){
            $tabela .= '
            <td>
                <div>
                    <a href="../editar/?id='.$value['log_id'].'"><button>Editar</button></a>
                    <a href="../excluir/?id='.$value['log_id'].'"><button>Excluir</button></a>
                </div>
            </td>';
        }else{
            $tabela .= '
            <td>
                <div>
                    <div class="tooltip">
                        <button disabled >Editar</button>
                        <span class="tooltiptext">Não é possível editar, verificar semana de fechamento</span>
                    </div>
                    <div class="tooltip">
                        <button disabled >Excluir</button>
                        <span class="tooltiptext">Não é possível excluir, verificar semana de fechamento</span>
                    </div>
                </div>
            </td>';
        }
        
        $tabela .= '
            </tr>
        ';
    }
    $tabela .= '
        </tbody>
    </table>
    ';
echo $tabela;

    ?>

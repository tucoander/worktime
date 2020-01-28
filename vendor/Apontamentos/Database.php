<?php

namespace Apontamentos;

use SQLite3;

class Database
{
    protected $database;

    public function __construct($url)
    {
        $this->database = new SQLite3($url);
    }

    public function checkUsrPsw($usr, $psw)
    {
        $select =
            "SELECT * 
            FROM USRSYS 
        WHERE USR_ID = :USR_ID 
        AND USRPSW = :USRPSW
        ";
        $cmd = $this->database->prepare($select);
        $cmd->bindValue(":USR_ID", $usr);
        $cmd->bindValue(":USRPSW", md5($psw));
        $res = $cmd->execute();
        return $res->fetchArray() ? 1 : 0;
    }

    public function semanaIndisponivel($dia_semana, $hora)
    {
        try {
            $select =
                "SELECT 
                (yy.yr_dte||'-'||yy.mn_dte||'-'||yy.dy_dte||' '||:HORA) as fechamento,
                strftime('%W',(yy.yr_dte||'-'||yy.mn_dte||'-'||yy.dy_dte||' '||:HORA)) - 1 as semana_indisponivel,
                yy.*
                from
                    yr_idx yy
                WHERE 
                    strftime('%W', (yy.yr_dte||'-'||yy.mn_dte||'-'||yy.dy_dte)) = strftime('%W', 'now') 
                    and  strftime('%Y', (yy.yr_dte||'-'||yy.mn_dte||'-'||yy.dy_dte)) = strftime('%Y', 'now')
                    and yy.wk_day = :DIA_SEMANA
            ";
            $this->database->enableExceptions(true);
            $cmd = $this->database->prepare($select);
            $cmd->bindValue(":DIA_SEMANA", $dia_semana);
            $cmd->bindValue(":HORA", $hora);
            $res = $cmd->execute();
            $array_response = array();

            while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
                $array_response['fechamento'] = $row['fechamento'];
                $array_response['semana_indisponivel'] = $row['semana_indisponivel'];
            }
            return $array_response;
        } catch (\Exception $e) {
            return 'Caught exception: ' . $e->getMessage();
        }
    }

    public function checkWeek($apontamento)
    {
        try {
            $select =
                "SELECT 
            strftime('%W',(yy.yr_dte||'-'||yy.mn_dte||'-'||yy.dy_dte)) semana,
            yy.*
            from
                yr_idx yy
            WHERE 
                (yy.yr_dte||'-'||yy.mn_dte||'-'||yy.dy_dte) = :dia
            ";
            $this->database->enableExceptions(true);
            $cmd = $this->database->prepare($select);
            $cmd->bindValue(":dia", $apontamento);
            $res = $cmd->execute();
            $response = '';

            while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
                $response = $row['semana'];
            }
            return intval($response);
        } catch (\Exception $e) {
            return 'Caught exception: ' . $e->getMessage();
        }
    }

    public function checkCountry($opr)
    {
        $select = "
            SELECT * FROM usropr WHERE opr_id = :opr_id
        ";
        $comando = $this->database->prepare($select);
        $comando->bindValue('opr_id', $opr);
        $resultado = $comando->execute();
        $country = '';
        $contador = 0;

        while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $country = $row['cty_id'];
            $contador++;
        }
        return $contador > 0 ? $country : 0;
    }

    public function inserirApontamento($post)
    {
        try {
            $insert = "
                INSERT INTO usrlog ( usr_id , prd_id , opr_id, cty_id , to_usr_id , logdte , fr_logtim, to_logtim, usrobs, insdte, insusr  ) 
                    VALUES ( :usr_id , :prd_id , :opr_id, :cty_id , :to_usr_id , :logdte , :fr_logtim, :to_logtim, :usrobs, :insdte, :insusr);
            ";
            date_default_timezone_set('America/Sao_Paulo');
            $agora = new \DateTime('now');

            $comando = $this->database->prepare($insert);
            $comando->bindValue('usr_id', $post['logged_usr_id']);
            $comando->bindValue('prd_id', $post['prd_id']);
            $comando->bindValue('opr_id', $post['opr_id']);
            $comando->bindValue('cty_id', self::checkCountry($post['opr_id']));
            $comando->bindValue('to_usr_id', $post['usrask']);
            $comando->bindValue('logdte', $post['adddte']);
            $comando->bindValue('fr_logtim', $post['fr_tim']);
            $comando->bindValue('to_logtim', $post['to_tim']);
            $comando->bindValue('usrobs', $post['usrobs']);

            $comando->bindValue('insdte', $agora->format('Y-m-d H:i'));
            $comando->bindValue('insusr', $post['logged_usr_id']);

            $resultado = $comando->execute();

            if ($resultado) {
                return 1;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            return 'Caught exception: ' . $e->getMessage();
        }
    }

    public function listRecords($get)
    {
        if (empty($get['from']) && empty($get['to'])) {
            
            $select = "
            SELECT * 
            FROM usrlog ul
                inner join  usrprd up 
                on (ul.prd_id = up.prd_id)
                inner join usrcty uc
                on (ul.cty_id = uc.cty_id)
                inner join usropr uo
                on (ul.opr_id = uo.opr_id)
            WHERE ul.usr_id = :usr_id
            order by ul.logdte asc
            ";
            $comando = $this->database->prepare($select);
            $comando->bindValue(':usr_id', $get['logged_usr_id']);
            
        } else if (empty($get['from']) && !empty($get['to'])) {
            $select = "
            SELECT * 
            FROM usrlog ul
                inner join  usrprd up 
                on (ul.prd_id = up.prd_id)
                inner join usrcty uc
                on (ul.cty_id = uc.cty_id)
                inner join usropr uo
                on (ul.opr_id = uo.opr_id)
            WHERE ul.usr_id = :usr_id
                AND ul.logdte <= :to
            order by ul.logdte asc
            ";
            $comando = $this->database->prepare($select);
            $comando->bindValue(':usr_id', $get['logged_usr_id']);
            $comando->bindValue(':to', $get['to']);
           

        } else if (!empty($get['from']) && empty($get['to'])) {
            $select = "
            SELECT * 
            FROM usrlog ul
                inner join  usrprd up 
                on (ul.prd_id = up.prd_id)
                inner join usrcty uc
                on (ul.cty_id = uc.cty_id)
                inner join usropr uo
                on (ul.opr_id = uo.opr_id)
            WHERE ul.usr_id = :usr_id
                AND ul.logdte >= :from
            order by ul.logdte asc
            ";
            $comando = $this->database->prepare($select);
            $comando->bindValue(':usr_id', $get['logged_usr_id']);
            $comando->bindValue(':from', $get['from']);
           

        } else if (!empty($get['from']) && !empty($get['to'])) {
            $select = "
            SELECT * 
            FROM usrlog ul
                inner join  usrprd up 
                on (ul.prd_id = up.prd_id)
                inner join usrcty uc
                on (ul.cty_id = uc.cty_id)
                inner join usropr uo
                on (ul.opr_id = uo.opr_id)
            WHERE ul.usr_id = :usr_id
                AND ul.logdte >= :from 
                AND ul.logdte <= :to
            order by ul.logdte asc
            ";
            $comando = $this->database->prepare($select);
            $comando->bindValue(':usr_id', $get['logged_usr_id']);
            $comando->bindValue(':from', $get['from']);
            $comando->bindValue(':to', $get['to']);
            
        } else {
            return 0;
        }

        $response = array();
        $resultado = $comando->execute();
        
        while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $response[] = $row;
        }

        return $response;

    }

    public function listRecordsManager($get)
    {
        $select = "
            SELECT * 
            FROM usrlog ul
                inner join  usrprd up 
                on (ul.prd_id = up.prd_id)
                inner join usrcty uc
                on (ul.cty_id = uc.cty_id)
                inner join usropr uo
                on (ul.opr_id = uo.opr_id)
            
            ";
        $where = "WHERE 1 = 1 
        ";
        $orderby = "
            order by ul.logdte asc
        ";

        if(empty($get['from'])){
            $where .= "
            ";
        }
        else{
            $where .= " AND ul.logdte >= :from";
        }

        if(empty($get['to'])){
            $where .= "
            ";
        }
        else{
            $where .= " AND ul.logdte <= :to";
        }

        if(empty($get['usr_id'])){
            $where .= "
            ";
        }
        else{
            $where .= " AND ul.usr_id = :usr_id";
        }

        $comando = $this->database->prepare($select.$where.$orderby);

        if(!empty($get['from'])){
            $comando->bindValue('from', $get['from']);
        }

        if(!empty($get['to'])){
            $comando->bindValue('to', $get['to']);
        }

        if(!empty($get['usr_id'])){
            $comando->bindValue('usr_id', $get['usr_id']);
        }
        
        $response = array();
        $resultado = $comando->execute();
        
        while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $response[] = $row;
        }

        return $response;

    }

    public function apontamento($id)
    {
        $select = "
            SELECT * FROM usrlog WHERE log_id = :log_id
        ";
        $comando = $this->database->prepare($select);
        $comando->bindValue('log_id', $id);
        $resultado = $comando->execute();
        $response = array();

        while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $response[] = $row;
        }
        return $response;
    }

    public function excluirApontamento($id)
    {
        $select = "
            DELETE FROM usrlog WHERE log_id = :log_id
        ";
        $comando = $this->database->prepare($select);
        $comando->bindValue('log_id', $id);
        $resultado = $comando->execute();
        return $this->database->changes();
    }

    public function editarApontamento($post)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $agora = new \DateTime('now');
        $apontamento = self::apontamento($post['id']);
        $apontamento = $apontamento[0];

        if(isset($post['adddte']) && $apontamento['logdte'] != $post['adddte']){
            $update = "
                UPDATE usrlog 
                    SET logdte = :logdte 
                    WHERE log_id = :log_id
            ";
            $comando = $this->database->prepare($update);
            $comando->bindValue('log_id', $post['id']);
            $comando->bindValue('logdte', $post['adddte']);
            $resultado = $comando->execute();
        }

        if(isset($post['fr_tim']) && $apontamento['fr_logtim'] != $post['fr_tim']){
            $update = "
                UPDATE usrlog 
                    SET fr_logtim = :fr_logtim 
                    WHERE log_id = :log_id
            ";
            $comando = $this->database->prepare($update);
            $comando->bindValue('log_id', $post['id']);
            $comando->bindValue('fr_logtim', $post['fr_tim']);
            $resultado = $comando->execute();
        }

        if(isset($post['to_tim']) && $apontamento['to_logtim'] != $post['to_tim']){
            $update = "
                UPDATE usrlog 
                    SET to_logtim = :to_logtim 
                    WHERE log_id = :log_id
            ";
            $comando = $this->database->prepare($update);
            $comando->bindValue('log_id', $post['id']);
            $comando->bindValue('to_logtim', $post['to_tim']);
            $resultado = $comando->execute();
        }

        if(isset($post['prd_id']) && $apontamento['prd_id'] != $post['prd_id']){
            $update = "
                UPDATE usrlog 
                    SET prd_id = :prd_id 
                    WHERE log_id = :log_id
            ";
            $comando = $this->database->prepare($update);
            $comando->bindValue('log_id', $post['id']);
            $comando->bindValue('prd_id', $post['prd_id']);
            $resultado = $comando->execute();
        }

        if(isset($post['opr_id']) && $apontamento['opr_id'] != $post['opr_id']){
            $update = "
                UPDATE usrlog 
                    SET opr_id = :opr_id 
                    WHERE log_id = :log_id
            ";
            $comando = $this->database->prepare($update);
            $comando->bindValue('log_id', $post['id']);
            $comando->bindValue('opr_id', $post['opr_id']);
            $resultado = $comando->execute();
        }

        if(isset($post['usrask']) && $apontamento['to_usr_id'] != $post['usrask']){
            $update = "
                UPDATE usrlog 
                    SET to_usr_id = :to_usr_id 
                    WHERE log_id = :log_id
            ";
            $comando = $this->database->prepare($update);
            $comando->bindValue('log_id', $post['id']);
            $comando->bindValue('to_usr_id', $post['usrask']);
            $resultado = $comando->execute();
        }

        if(isset($post['usrobs']) && $apontamento['usrobs'] != $post['usrobs']){
            $update = "
                UPDATE usrlog 
                    SET usrobs = :usrobs 
                    WHERE log_id = :log_id
            ";
            $comando = $this->database->prepare($update);
            $comando->bindValue('log_id', $post['id']);
            $comando->bindValue('usrobs', $post['usrobs']);
            $resultado = $comando->execute();
        }

        $update = "
                UPDATE usrlog 
                    SET lstusr =  :lstusr,
                        lstdte = :lstdte
                    WHERE log_id = :log_id
            ";
            $comando = $this->database->prepare($update);
            $comando->bindValue('log_id', $post['id']);
            $comando->bindValue('lstusr', $post['logged_usr_id']);
            $comando->bindValue('lstdte', $agora->format('Y-m-d H:i'));
            $resultado = $comando->execute();
        
            return 1;
    }

    public function checkProduto()
    {
        $select = "
            SELECT prd_id, prdnme FROM usrprd GROUP BY prd_id, prdnme
        ";
        $comando = $this->database->prepare($select);
        $resultado = $comando->execute();
        $produtos = '';
        $contador = 0;

        while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $produtos[] = $row;
            $contador++;
        }
        return $contador > 0 ? $produtos : 0;
    }

    public function checkOperacao()
    {
        $select = "
        SELECT  *
            FROM usropr uo
                inner join usrcty uc 
                on(uo.cty_id = uc.cty_id)
            GROUP BY 
                uo.opr_id, 
                uo.oprnme,
                uc.ctysgl
        ";
        $comando = $this->database->prepare($select);
        $resultado = $comando->execute();
        $operacao = '';
        $contador = 0;

        while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $operacao[] = $row;
            $contador++;
        }
        return $contador > 0 ? $operacao : 0;
    }

    public function checkUserAcess($usr)
    {
        $select = "
        SELECT 
            us.usr_id,
            us.usrnme,
            uf.fun_id,
            uf.fundsc,
            ua.usrrol 
        FROM
            usrsys us 
            inner join
            funusr fu
            on (us.usr_id = fu.usr_id)
            inner join 
            usrfun uf
            on (fu.fun_id = uf.fun_id)
            inner join 
            usraut ua
            on (ua.usr_id = us.usr_id) 
        WHERE 
            us.usr_id = :usr_id
                ";
        $comando = $this->database->prepare($select);
        $comando->bindValue('usr_id', $usr);
        $resultado = $comando->execute();
        $usuario = '';
        $contador = 0;

        while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $usuario[] = $row;
            $contador++;
        }
        return $contador > 0 ? $usuario : 0;
    }

    public function listUsers()
    {
        $select = "
            SELECT usr_id, usrnme FROM usrsys GROUP BY usr_id, usrnme order by usrnme
        ";
        $comando = $this->database->prepare($select);
        $resultado = $comando->execute();
        $users = '';
        $contador = 0;

        while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $users[] = $row;
            $contador++;
        }
        return $contador > 0 ? $users : 0;
    }

    public function listUserWorkOpr($get)
    {
        $select = "
            SELECT
                uo.oprnme,
                sum(julianday(ul.logdte||' '||ul.to_logtim) - julianday(ul.logdte||' '||ul.fr_logtim))*24 as work
            FROM usrlog ul
                left join usrprd up on (ul.prd_id = up.prd_id)
                left join usropr uo on (ul.opr_id = uo.opr_id)";
        $where = "WHERE 1 = 1 ";
        $orderby = "
            GROUP BY 
                uo.oprnme
            HAVING uo.oprnme IS NOT NULL
            ORDER BY
                uo.oprnme
           ";

        if(!empty($get['from'])){
            $where .= " AND ul.logdte >= :from";
        }
        if(!empty($get['to'])){
            $where .= " AND ul.logdte <= :to";
        }
        if(!empty($get['usr_id'])){
            $where .= " AND ul.usr_id = :usr_id";
        }
        $comando = $this->database->prepare($select.$where.$orderby);
        if(!empty($get['from'])){
            $comando->bindValue('from', $get['from']);
        }
        if(!empty($get['to'])){
            $comando->bindValue('to', $get['to']);
        }
        if(!empty($get['usr_id'])){
            $comando->bindValue('usr_id', $get['usr_id']);
        }
        $response = array();
        $resultado = $comando->execute();
        
        while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $response[] = $row;
        }

        return $response;
    }

    
    public function workUsrOprDaily($usr, $dte, $opr)
    {
        $select = "
        SELECT
            ul.opr_id,
            sum(julianday(ul.logdte||' '||ul.to_logtim) - julianday(ul.logdte||' '||ul.fr_logtim))*24 as work
        FROM usrlog ul
            left join usrprd up on (ul.prd_id = up.prd_id)
            left join usropr uo on (ul.opr_id = uo.opr_id)
        WHERE  ul.usr_id = :usr_id
            and ul.logdte = :logdte
            and ul.opr_id = :opr_id
        GROUP BY ul.opr_id
                ";
        $comando = $this->database->prepare($select);
        $comando->bindValue('usr_id', $usr);
        $comando->bindValue('logdte', $dte);
        $comando->bindValue('opr_id', $opr);
        $resultado = $comando->execute();
        $horas = 0;
        $contador = 0;

        while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $horas = $row['work'];
            $contador++;
        }

        return $contador > 0 ? $horas : 0;
    }
    public function workUsrDaily($opr, $dte_from, $dte_to)
    {
        $select = "
        SELECT
            sum(julianday(ul.logdte||' '||ul.to_logtim) - julianday(ul.logdte||' '||ul.fr_logtim))*24 as work
        FROM usrlog ul
            left join usrprd up on (ul.prd_id = up.prd_id)
            left join usropr uo on (ul.opr_id = uo.opr_id)
        WHERE  ul.opr_id = :opr_id
            and ul.logdte between :from and :to
        group by ul.opr_id
                ";
        $comando = $this->database->prepare($select);
        $comando->bindValue('opr_id', $opr);
        $comando->bindValue('from', $dte_from);
        $comando->bindValue('to', $dte_to);
        $resultado = $comando->execute();
        $horas = 0;
        $contador = 0;

        while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $horas = $row['work'];
            $contador++;
        }
        
        return $contador > 0 ? $horas : 0;
    }

    public function checkIndisponibilidade($usr, $dte)
    {
        $select = "
        SELECT
            count(ul.ind_id) as contagem
        FROM usrind ul
        WHERE  ul.inddte = :logdte
        and ul.usr_id = :usr_id
        group by ul.usr_id
                ";
        $comando = $this->database->prepare($select);
        $comando->bindValue('logdte', $dte);
        $comando->bindValue('usr_id', $usr);
        $resultado = $comando->execute();
        $existe = 0;
        $contador = 0;

        while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $existe = $row['contagem'];
            $contador++;
        }
        
        return $contador > 0 ? 1 : 0;
    }

    public function somaUsrOprDay($data, $opr, $usr)
    {
        $select = "
        SELECT
            sum(julianday(ul.logdte||' '||ul.to_logtim) - julianday(ul.logdte||' '||ul.fr_logtim))*24 as work
        FROM usrlog ul
            left join usrprd up on (ul.prd_id = up.prd_id)
            left join usropr uo on (ul.opr_id = uo.opr_id)
        WHERE  uo.oprnme = :oprnme
            and ul.logdte between :from and :to
            and ul.usr_id = :usr_id
        group by ul.opr_id
                ";
        $comando = $this->database->prepare($select);
        $comando->bindValue('oprnme', $opr);
        $comando->bindValue('from', $data['from']);
        $comando->bindValue('to', $data['to']);
        $comando->bindValue('usr_id', $usr);
        $resultado = $comando->execute();
        $horas = 0;
        $contador = 0;

        while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
            $horas = $row['work'];
            $contador++;
        }
        
        return $contador > 0 ? $horas : 0;
    }

    public function listAllRecords($data){
        $select = "
            SELECT
            ul.log_id as log_id,
            ul.usr_id as usr_id,
            up.prdnme as prdnme,
            uo.oprnme as oprnme,
            uc.ctynme as ctynme,
            ul.logdte as logdte,
            ul.fr_logtim as fr_logtim,
            ul.to_logtim as to_logtim,
            ul.usrobs    
        FROM usrlog ul
            left join usrprd up on (ul.prd_id = up.prd_id)
            left join usropr uo on (ul.opr_id = uo.opr_id)
            left join usrcty uc on (ul.cty_id = uc.cty_id)
        WHERE  ul.logdte between :from and :to
        UNION
        select 
            ui.ind_id,
            ui.usr_id,
            'Indisponivel' as prdnme,
            'Indisponivel' as oprnme,
        'Indisponivel' as ctynme,
            ui.inddte,
            ui.fr_logtim,
            ui.to_logtim,
            ui.indobs   
        from usrind ui
            where ui.inddte between :from and :to
        ";
        $response = array(
            "status"=> "",
            "message"=> "",
            "data"=> array()
        );
        try {
            $comando = $this->database->prepare($select);
            $comando->bindValue('from', $data['from']);
            $comando->bindValue('to', $data['to']);
            $resultado = $comando->execute();

            while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
                $response['data'][] = $row;
            }
            $response['status'] = 1;
            return $response;
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['message'] = 'Caught exception: ' . $e->getMessage();
            return $response;
        }
    }

}


<?php

/**
 * @class AtualizacaoBaseCorreiosDAO
 * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
 * @since 08/05/2013
 * Camada de regras de persistência de dados.
 */
class AtualizacaoBaseCorreiosDAO {
    
    private $conn;
    
    public function gravaLog($mensagem, $tipo) {
        
        $sql = "
            UPDATE
                historico_atualizacao_correios 
            SET
                hacdt_atualizacao = NOW(),
                hacstatus = '$tipo',
                hacobservacao = '$mensagem'
            WHERE
                hacdt_atualizacao = (
                    SELECT
                        hacdt_atualizacao
                    FROM
                        historico_atualizacao_correios
                    ORDER BY
                        hacdt_atualizacao DESC
                    LIMIT 1                    
                )
            ";
        
        return pg_affected_rows(pg_query($this->conn, $sql));
        
    }
    
    public function getIdsLocalidades() {
        
        $sql = "
            SELECT
                clcoid
            FROM 
                correios_localidades;
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $ids_localidades = array();
        
        if(!$rs) {
            return false;
        }
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $ids_localidades[] = pg_fetch_result($rs, $i, 'clcoid');
        }
        
        return $ids_localidades;
        
    }
    
    public function atualizaLocalidade($localidade) {
        
        $sql = "
            UPDATE
                correios_localidades 
            SET
                clcuf_sg = '{$localidade['uf']}',
                clcnome = '{$localidade['nome_oficial']}',
                clccep = '{$localidade['cep']}',
                clcsituacao = '{$localidade['situacao']}',
                clctipo = '{$localidade['tipo']}',
                clcoid_sub = {$localidade['chave_subordinacao']},
                clcmun_ibge = {$localidade['codigo_ibge_municipio']},
                clcestoid = {$this->getIdEstadoPelaSigla($localidade['uf'])}
            WHERE
                clcoid = {$localidade['localidade_DNE']}
            ";
                
        return pg_affected_rows(pg_query($this->conn, $sql));
        
    }
    
    public function insereLocalidade($localidade) {
        
        $sql = "
            INSERT INTO
                correios_localidades 
            (   
                clcoid,
                clcuf_sg,
                clcnome,
                clccep,
                clcsituacao,
                clctipo,
                clcoid_sub,
                clcmun_ibge,
                clcestoid
            )
            VALUES
            (
                {$localidade['localidade_DNE']},
                '{$localidade['uf']}',
                '{$localidade['nome_oficial']}',
                '{$localidade['cep']}',
                '{$localidade['situacao']}',
                '{$localidade['tipo']}',
                {$localidade['chave_subordinacao']},
                {$localidade['codigo_ibge_municipio']},
                {$this->getIdEstadoPelaSigla($localidade['uf'])}
            )";
        
        return pg_affected_rows(pg_query($this->conn, $sql));
        
    }
    
    private function getIdEstadoPelaSigla($sigla) {
       
        $sql = "
            SELECT
                estoid  
            FROM
                estado
            WHERE
                estuf = '$sigla'                
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $id_estado = 0;
        
        if(pg_num_rows($rs) > 0) {
            $id_estado = pg_fetch_result($rs, 0, 'estoid');
        }
        
        return $id_estado;
        
    }
    
    public function limpaTabelaBairros() {
        $sql = "DELETE FROM correios_bairros";

        $res = pg_query($this->conn, $sql);
        if(!is_resource($res)) return false;
        
        return true;
    }  

     public function getIdsBairros() {
        
        $sql = "
            SELECT
                cbaoid
            FROM 
                correios_bairros;
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $ids_bairros = array();
        
        if(!$rs) {
            return false;
        }
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $ids_bairros[] = pg_fetch_result($rs, $i, 'cbaoid');
        }
        
        return $ids_bairros;
        
    }
    
    public function atualizaBairro($bairro) {
        
        $sql = "
            UPDATE
                correios_bairros 
            SET
                cbauf_sg = '{$bairro['uf']}',
                cbaclcoid = {$bairro['id_DNE']},
                cbanome = '{$bairro['nome_oficial']}'
            WHERE
                cbaoid = {$bairro['id_bairro']}
            ";
                
        return pg_affected_rows(pg_query($this->conn, $sql));
        
    }
    
    public function insereBairro($bairro) {
        
        $sql = "
            INSERT INTO
                correios_bairros
            (   
                cbaoid,
                cbauf_sg,
                cbaclcoid,
                cbanome                
            )
            VALUES
            (
                {$bairro['id_bairro']},
                '{$bairro['uf']}',
                {$bairro['id_DNE']},
                '{$bairro['nome_oficial']}'                
            )";
        
        return pg_affected_rows(pg_query($this->conn, $sql));
        
    }
    
    public function deletaLogradouros($sascar = false) {
        
        $str_sascar = $sascar ? 'TRUE' : 'FALSE';
        
        $sql = "DELETE FROM correios_logradouros WHERE clgreg_sascar = $str_sascar";
        
        $res = pg_query($this->conn, $sql);
        if(!is_resource($res)) return false;
        
        return true;      
    }
    
    public function getDadosUltimoHistorico() {
        
        $sql = "
           SELECT
                nm_usuario, usuemail, TO_CHAR(hacdt_atualizacao, 'dd/mm/yyyy HH24:MI:SS') AS hacdt_atualizacao
            FROM
                historico_atualizacao_correios
            INNER JOIN
                usuarios ON hacusuoid = cd_usuario
            ORDER BY
                hacdt_atualizacao DESC
            LIMIT 1";
        
        $rs = pg_query($this->conn, $sql);
        
        $dados = array();
        
        if(pg_num_rows($rs) > 0) {
            $dados['nm_usuario'] = pg_fetch_result($rs, 0, 'nm_usuario');
            $dados['usuemail'] = pg_fetch_result($rs, 0, 'usuemail');
            $dados['hacdt_atualizacao'] = pg_fetch_result($rs, 0, 'hacdt_atualizacao');
        }
        
        return $dados;
        
    }
    
    public function reinsereRegistrosSascar() {
        
        $sql = "
            SELECT
                *
            FROM 
                correios_logradouros
            WHERE
                clgreg_sascar = TRUE";
        
        $rs = pg_query($this->conn, $sql);
        
        $logradouros = array();
        
        if(pg_num_rows($rs) > 0) {
            
            for($i = 1; $i <= pg_num_rows($rs); $i++) {
                
                $k = $i - 1;
                
                $logradouros[$k]['clgoid'] = $i;
                $logradouros[$k]['clguf_sg'] = pg_fetch_result($rs, $k, 'clguf_sg');
                $logradouros[$k]['clgclcoid'] = pg_fetch_result($rs, $k, 'clgclcoid');
                $logradouros[$k]['clgcbaoid_ini'] = pg_fetch_result($rs, $k, 'clgcbaoid_ini');
                $logradouros[$k]['clgcbaoid_fim'] = pg_fetch_result($rs, $k, 'clgcbaoid_fim');
                $logradouros[$k]['clgnome'] = pg_fetch_result($rs, $k, 'clgnome');
                $logradouros[$k]['clgcomplemento'] = pg_fetch_result($rs, $k, 'clgcomplemento');
                $logradouros[$k]['clgcep'] = pg_fetch_result($rs, $k, 'clgcep');
                $logradouros[$k]['clgtipo'] = pg_fetch_result($rs, $k, 'clgtipo');
                $logradouros[$k]['clgsta_tipo'] = pg_fetch_result($rs, $k, 'clgsta_tipo');
                $logradouros[$k]['clgreg_sascar'] = pg_fetch_result($rs, $k, 'clgreg_sascar');

            }
          
        }
        
        if(!$this->deletaLogradouros(true)) {
            return false;
        }
        
        
        foreach($logradouros as $i => $logradouro) {
            
            $is_inserted = $this->insereLogradouro($logradouro);
            
            if(!$is_inserted) {
                return false;
            }
            
        }
        
        return true;    
    }
    
    public function insereLogradouro($logradouro) {

	// substitui aspas simples para nao dar erro de insert no postgres
	$logradouro['clgnome'] = str_replace('\'', '\'\'', $logradouro['clgnome']);

        $sql = "
                INSERT INTO
                    correios_logradouros   
                (
                clgoid,
                clguf_sg,
                clgclcoid,
                clgcbaoid_ini,
                clgcbaoid_fim,
                clgnome,
                clgcomplemento,
                clgcep,
                clgtipo,
                clgsta_tipo,
                clgreg_sascar
                )
                VALUES
                (
                {$logradouro['clgoid']},
                '{$logradouro['clguf_sg']}',
                {$logradouro['clgclcoid']},
                {$logradouro['clgcbaoid_ini']},
                {$logradouro['clgcbaoid_fim']},
                '{$logradouro['clgnome']}',
                '".(isset($logradouro['clgcomplemento']) ? $logradouro['clgcomplemento'] : "")."',
                '{$logradouro['clgcep']}',
                '{$logradouro['clgtipo']}',
                '{$logradouro['clgsta_tipo']}',
                '{$logradouro['clgreg_sascar']}'
                )";
                
	echo $sql;
                 
        $res = pg_query($this->conn, $sql);
        return pg_affected_rows($res);
        
    }
    
    public function getUltimoIdLogradouro() {
        
        $sql = "
            SELECT
                MAX(clgoid) as clgoid
            FROM
                correios_logradouros
        ";
        
        $rs = pg_query($this->conn, $sql);
        
        return pg_fetch_result($rs, 0, 'clgoid');
        
    }
    
    public function getCepsInseridos() {
        
        $sql = "
            SELECT
                clgcep
            FROM
                correios_logradouros";
        
        $rs = pg_query($this->conn, $sql);
        
        $ceps = array();
        
        if(pg_num_rows($rs) > 0) {
           for($i = 0; $i < pg_num_rows($rs); $i++) {
              $ceps[] = pg_fetch_result($rs, $i, 'clgcep'); 
           } 
        }
        
        return $ceps;
        
    }
           
    public function __construct() {

        global $conn;

        $this->conn = $conn;
    }
    
}

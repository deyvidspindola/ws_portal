<?php

class SendLayoutEmailsDAO
{
    protected $_adapter;
    
    public function __construct()
    {
        global $conn;        
        $this->_adapter = $conn;
    }
    
    /**
     * Executa uma query
     * @param    string        $sql        SQL a ser executado
     * @return    resource
     */
    protected function _query($sql)
    {
        // Suprime erros para lançar exceção ao invés de E_WARNING
        $result = @pg_query($this->_adapter, $sql);
        
        if ($result === false)
        {
            throw new Exception(pg_last_error($this->_adapter));
        }
        
        return $result;
    }
    
    /**
     * Conta os resultados de uma consulta
     * @param    resource    $results
     * @return    int
     */
    protected function _count($results)
    {
        return pg_num_rows($results);
    }
    
    /**
     * Retorna os resultados de uma consulta num array associativo (hash-like)
     * @param    resource    $results
     * @return    array
     */
    protected function _fetchAll($results)
    {
        return pg_fetch_all($results);
    }
    
    /**
     * Retorna o resultado de uma coluna num array associativo (hash-like)
     * @param    resource    $results
     * @return    array
     */
    protected function _fetchAssoc($result)
    {
        return pg_fetch_assoc($result);
    }
    
    /**
      * Retorna o resultado como um vetor de objetos
      * @param  resource     $results
      * @return  array
      */
    public function _fetchObj($results)
    {
        $rows = array_map(function($item) { 
            return (object) $item; 
        }, $this->_fetchAll($results)); 
        
        return $rows;
    }
    
    /**
     * Insere valores numa tabela
     * @param    string    $table
     * @param    array    $values
     * @return    boolean
     */
    protected function _insert($table, $arr)
    {
        // Suprime erros para lançar exceção ao invés de E_WARNING
        $result = @pg_insert($this->_adapter, $table, $arr);
        
        if ($result === false)
        {
            throw new Exception(pg_last_error($this->_adapter));
        }
        
        return $result;
    }
    
    /**
     * Escapa os elementos de um vetor
     * @param    array    $arr
     * @return    array
     */
    protected function _escapeArray($arr)
    {
        array_walk($arr, function(&$item, $key) {
            $item = pg_escape_string($item);
        });
        
        return $arr;
    }
    
    /**
     * Busca a lista de funcionalidades ativas
     * @return	array
     */
    public function getOcorrencias($ids, $limit=false)
    {
    	$sql = "select 
    				ocooid,
					clioid,
					clinome as NOMECLIENTE,
					cliemail,
    				cliemail_nfe,
					mcaoid,
					mcamarca as MARCA,
					mlooid,
					mlomodelo as MODELO,
					veioid,
					veiplaca as PLACA,
					veichassi as CHASSI,
					ocodata_recup as DTRECUPERACAO,
					ocohora_recup as HRRECUPERACAO,
					ocolocal_recup||' - '||ococid_recup as LCRECUPERACAO,
					ocovl_veiculo as VLRECUPERACAO
				from ocorrencia o
					join clientes c on c.clioid = o.ococlioid
					join veiculo v on v.veioid = o.ocoveioid
					join modelo mo on mo.mlooid = v.veimlooid 
					join marca ma on ma.mcaoid = mo.mlomcaoid
				where ocooid in ($ids)
				order by ocodata_comunic, ocohora_comunic
    			".($limit ? "limit $limit" : "");
    	return $this->_fetchAll($this->_query($sql));
    }
    
    public function registrarLogOcorrencia($ocorrencia, $sucesso){
    	$sql = "insert into log_envio_email_ocorrencia(leeoocooid, leeodt_cadastro, leeosucesso_envio) values(".intval($ocorrencia).", now(), ".($sucesso ? "true" : "false" ).")";
    	return $this->_query($sql);    	
    }    
}

<?php
require_once _MODULEDIR_ . 'Atendimento/Exception/ExceptionDAO.php';
require_once _MODULEDIR_ . 'Atendimento/VO/AtendimentoVO.php';

/**
 * AtendimentoDAO.php
 * 
 * Classe de persistência dos dados padrão do Atendimento
 * 
 * @author	Alex Sandro Médice <alex.medice@meta.com.br>
 * @since   22/04/2013
 * @package Atendimento
 */
abstract class AtendimentoDAO {
		
    protected $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function transactionBegin() {
    	pg_query($this->conn, "BEGIN;");
    }
    
    public function transactionCommit() {
    	pg_query($this->conn, "COMMIT");
    }
    
    public function transactionRollback() {
    	pg_query($this->conn, "ROLLBACK;");
    }
    
    /**
     * Executa uma consulta (query)
     * 
     * @param string $sql
     * @throws ExceptionDAO
     * @return resource
     */
    public function query($sql) {
    	$rs = pg_query($this->conn, $sql);
    	 
    	if (!$rs) {
    		throw new ExceptionDAO('Falha na execução da query: ' . $sql);
    	}
    	
    	return $rs;
    }
    
    /**
     * Retorna linha (registro) como um objeto
     * 
     * @param resource $rs
     * @return AtendimentoVO 
     */
    public function fetchObject($rs) {
    	$row = pg_fetch_object($rs);
    	    	    	
    	return new AtendimentoVO($row);
    }
    
    /**
     * Retorna linhas (registros) como um objetos
     * 
     * @param resource $rs
     * @return AtendimentoVO 
     */
    public function fetchObjects($rs) {
    	
    	$rows = array();
    	while($row = pg_fetch_object($rs)) {
    		$rows[] = new AtendimentoVO($row);
    	}
    	
    	return $rows;
    }
    
    /**
     * Efetua um escape nos atributos do VO
     * @param AtendimentoVO $vo
     * @return AtendimentoVO
     */
    public function escape(AtendimentoVO $vo) {
    	foreach ($vo as $key => $value) {
    		$vo->$key = addslashes(pg_escape_string($value));
    	}
    	
    	return $vo;
    }
    
    /**
     * Retorna o número de linhas do resultSet
     * @param resource $rs
     * @return number
     */
    public function numRows($rs) {
    	
    	if (!$rs) {
    		return 0;
    	} else {
    		return pg_num_rows($rs);
    	}
    }
}
<?php
require_once _MODULEDIR_ . 'Cron/Exception/ExceptionDAO.php';
require_once _MODULEDIR_ . 'Cron/VO/CronVO.php';

/**
 * CronDAO.php
 * 
 * Classe de persistência dos dados padrão do Cron
 * 
 * @author	Alex Sandro Médice <alex.medice@meta.com.br>
 * @since   20/11/2012
 * @package Cron
 */
abstract class CronDAO {
		
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
     * @return CronVO 
     */
    public function fetchObject($rs) {
    	$row = pg_fetch_object($rs);
    	    	    	
    	return new CronVO($row);
    }
    
    /**
     * Retorna linhas (registros) como um objetos
     * 
     * @param resource $rs
     * @return CronVO 
     */
    public function fetchObjects($rs) {
    	
    	$rows = array();
    	while($row = pg_fetch_object($rs)) {
    		$rows[] = new CronVO($row);
    	}
    	
    	return $rows;
    }
    
    public function escape(CronVO $vo) {
    	foreach ($vo as $key => $value) {
    		$vo->$key = addslashes(pg_escape_string($value));
    	}
    	
    	return $vo;
    }
    
    /**
     * Recupera o código do usuário do CRON
     * @return int
     */
    public function buscarCodigoUsuarioCron() {
    		
    	$sql = "
			SELECT 	cd_usuario
			FROM 	usuarios
			WHERE 	nm_usuario ILIKE 'automatico%'
			AND		dt_exclusao IS NULL
			LIMIT 1
		";
    
    	$rs = $this->query($sql);
    
    	$row = pg_fetch_object($rs);
    		
    	$cd_usuario = isset($row->cd_usuario) ? $row->cd_usuario : 0;
    
    	return $cd_usuario;
    }
}
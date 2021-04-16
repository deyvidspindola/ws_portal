<?php
/**
 * SASCAR (http://www.sascar.com.br/)
 *
 * Módulo para Cadastro de Planos Satelitais - DAO (Data Access Object)
 *
 * @author Jorge A. D. Kautzmann <jorge.kautzmann@sascar.com.br>
 * @description	Módulo para Cadastro de Planos Satelitais - DAO
 * @version 28/03/2013 [1.0]
 * @package SASCAR Intranet
*/

class CadPlanoSatelitalDAO {
	private $conn;
	/**
	  * __construct()
	  *
	  * @param none
	  * @return none
	  * @description	Método construtor da classe
	*/
	public function __construct(){
		global $conn;
		$this->conn = $conn;
	}


	/**
	 * getDadosPlano()
	 *
	 * @param $asapoid (ID do Plano)
	 * @return $vData (array com dados da consulta de um plano)
	 */
	public function getDadosPlano($asapoid = 0) {
	    $vData = array();
	    $asapoid = (int) $asapoid;
	    $sqlQuery = "
			SELECT
				asapoid,
				asapdescricao,
				asapdt_cadastro,
				asapdt_exclusao
	        FROM antena_satelital_plano
			WHERE asapoid = " . $asapoid . "
		";
	    $rs = pg_query($this->conn, $sqlQuery);
	    if(pg_num_rows($rs) > 0){
	        $vData = pg_fetch_array($rs, 0, PGSQL_ASSOC);
	    }
	    return $vData;
	}
	
	
	/**
	 * confirmarNovoPlano()
	 *
	 * @param none
	 * @return $vData (array com dados do resultado da operação)
	 */
	
	public function confirmarNovoPlano() {
	    $vData = array();
	    $vData['action_st'] = 'nok';
	    $vData['action_msg'] = 'Plano Satelital incluído com sucesso!';
	    $vData = array_merge($vData, $_POST);
	    $asapdescricao = trim($_POST['asapdescricao']);
	
	    // Verifica se já não incluiu registro com os mesmos dados
	    $sqlQuery = "SELECT asapoid FROM antena_satelital_plano
	    WHERE trim(upper(asapdescricao)) = trim(upper('$asapdescricao'))
	    AND asapdt_exclusao is null";
	    $rs = pg_query($this->conn, $sqlQuery);
	    if(pg_num_rows($rs) == 0) {
	    // Inicia Transação
	        $stQuery = pg_query($this->conn, "BEGIN");
	        if(!$stQuery){
	        $vData['action_st'] = 'nok';
				$vData['action_msg'] = 'Erro ao inciar a transação!';
	            return $vData;
	        }
	        // Monta a query
	        $sqlUpdate = " INSERT INTO antena_satelital_plano (
	        asapdescricao,
	        asapdt_cadastro) VALUES (
	        '" . strtoupper($asapdescricao) . "',
	        now())
	        ";
	        $stQuery = pg_query($this->conn, $sqlUpdate);
	        // Testa resultado
	        if(!$stQuery){
	            // Commit Transação
	            pg_query($this->conn, "ROLLBACK");
	            $vData['action_st'] = 'nok';
	            $vData['action_msg'] = 'Erro ao gravar novo plano!';
	            return $vData;
    	    }
			// Nenhum erro no processo
			// Commit Transação
	        pg_query($this->conn, "COMMIT");
	        $vData['action_st'] = 'ok';
	        return $vData;
	    }else{
	        $vData['action_st'] = 'nok';
	        $vData['action_msg'] = 'Existe outro plano com os mesmos dados!';
			return $vData;
	    }
	}
	
	
	/**
	* excluirPlano()
	*
	* @param none
	* @return $vData (array com dados do resultado da operação)
	*/
	
	public function excluirPlano() {
	    $vData = array();
	    $vData['action_st'] = 'nok';
	    $vData['action_msg'] = 'Plano excluído com sucesso!';
	    $asapoid = (int) $_POST['asapoid'];
	    
	    // Inicia Transação
    	$stQuery = pg_query($this->conn, "BEGIN");
    	if(!$stQuery){
    	    $vData['action_st'] = 'nok';
    		$vData['action_msg'] = 'Erro ao inciar a transação!';
    		return $vData;
    	}
    	$sqlDelete = "
    	    UPDATE antena_satelital_plano
    	        SET asapdt_exclusao = now()
    	    WHERE asapoid=$asapoid
    	";
    	$stQuery = pg_query($this->conn, $sqlDelete);
    	// Testa resultado
    	if(!$stQuery){
    	    // Commit Transação
    	    pg_query($this->conn, "ROLLBACK");
    	    $vData['action_st'] = 'nok';
    	    $vData['action_msg'] = 'Erro ao deletar Plano!';
    	    return $vData;
    	}
	    // Nenhum erro no processo
	    // Commit Transação
	    pg_query($this->conn, "COMMIT");
	    $vData['action_st'] = 'ok';
		return $vData;
	}
	
}
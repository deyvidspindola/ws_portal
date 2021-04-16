<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
 * @version 29/08/2013
 * @since 29/08/2013
 * @package Core
 * @subpackage Superclasse Model de Acesso a Dados
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace infra;

use infra\Helper\Mascara as Mascara;

abstract class ComumDAO{
    
    private $conn;
    private $debug;
    private $querySTR;
    private $queryStatus;
    private $resultSet;
    private $xml;
    
	/**
	 * Contrutor da classe
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param none
	 * @return none
     */
    public function __construct() {
         $this->xml = _SITEDIR_.'modulos/core/infra/config.xml';
         $this->connect();
         $this->setDebug();
    }
    
	/**
	 * Habilita/desabilita o modo debug
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @return none
     */
    public function setDebug() {
    	$xml  = simplexml_load_file($this->xml);
    	$modo = (strtolower($xml->debug) == 'true') ? true : false;
        $this->debug = $modo;
    }
    
	/**
	 * Connect
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param none
	 * @return none
    */
    public function connect() {
        global $conn;
        $this->conn = $conn;
    }

    /**
	 * Disconnect
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param none
	 * @return none
    */
    public function disconnect() {
        unset($this->conn);
    }
    
	/**
	 * Método principal de execução de querys
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param $strQuery (SQL query)
	 * @return resultSet
    */
    public function queryExec($strQuery = '') {
        $this->querySTR = $strQuery;
        $this->resultSet = false;
        
        if(strlen($this->querySTR) > 0){
            if($this->debug){
                echo '
					<table width="100%" cellspacing="1" cellpadding="3" border="0" bgcolor="#165480">
						<tr>
							<td bgcolor="#E7EBEE">
								<font size=1 face="verdana, arial, helvetica">
									<b>DEBUG</b>
								</font>
							</td>
						</tr>
						<tr>
							<td bgcolor="#FFFFCC">
								<font face="verdana, arial, helvetica" size=1>
									' .$this->querySTR. '
								</font>
							</td>
						</tr>
					</table>
					</br>';
            }
            
            $this->resultSet = pg_query($this->conn, $this->querySTR);
            
            if($this->resultSet === false) {
               throw new \Exception('Erro ao executar Query: ' . pg_last_error($this->conn),123);
            }
            
            $this->queryStatus = pg_result_status($this->resultSet);
        }
        return $this->resultSet;
    }

	/**
	 * Método que retorna total de linhas afetadas
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param $resultSet
	 * @return Total de registros afetados
    */
    public function getAffectedRows($resultSet=false){
        if($resultSet){
            return pg_affected_rows($resultSet);
        }else{
            return pg_affected_rows($this->resultSet);
        }
    }
    
	/**
	 * Total de registros retornados no recordset
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param $resultSet
	 * @return Total de registros retornados
    */
    public function getNumRows($resultSet=false){
        if($resultSet){
            return pg_num_rows($resultSet);
        }else{
            return pg_num_rows($this->resultSet);
        }
    }
    
	/**
	 * Retorna uma linha do recordset como objeto 
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param $resultSet
	 * @return Object da linha especificada
    */
    public function getObject($pos=0, $resultSet=false){
        if($resultSet){
            return pg_fetch_object($resultSet, $pos);
        }else{
            return pg_fetch_object($this->resultSet, $pos);
        }
    }

    /**
	 * Retorna uma linha do recordset como array (associativo + numérico)  
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param $resultSet
	 * @return Array da linha especificada
    */
    public function getArray($pos=0, $resultSet=false){
        if($resultSet){
            return pg_fetch_array($resultSet, $pos);
        }else{
            return pg_fetch_array($this->resultSet, $pos);
        }
    }

    /**
	 * Retorna uma linha do recordset como array (numérico)  
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param $resultSet
	 * @return Array numerico da linha especificada
    */
    public function getRow($pos=0, $resultSet=false){
        if($resultSet){
            return pg_fetch_row($resultSet, $pos);
        }else{
            return pg_fetch_row($this->resultSet, $pos);
        }
    }

    /**
	 * Retorna uma linha do recordset como array (associativo) 
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param $resultSet
	 * @return Array associativo da linha especificada
    */
    public function getAssoc($lin=0, $resultSet=false){
        if($resultSet){
            return  pg_fetch_assoc($resultSet, $lin);
        }else{
            return  pg_fetch_assoc($this->resultSet, $lin);
        }
    }
    
    /**
	 * Retorna um array com todo o recordset 
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param $resultSet
	 * @return Array com todo o recordset
    */
    public function getAll($resultSet=false){
        if($resultSet){
            return pg_fetch_all($resultSet);
        }else{
            return pg_fetch_all($this->resultSet);
        }
    }
    
    /**
	 * Retorna uma célula do recordset pecificada por (lin, col) onde col pode ser numerica ou associativa
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param $lin=0,$col=0, $resultSet
	 * @return valor de uma célula ou coluna
	 * 
    */
    public function getCell($lin=0,$col=0, $resultSet=false){
        if($resultSet){
            return pg_fetch_result($resultSet, $lin, $col);
        }else{
            return pg_fetch_result($this->resultSet,  $lin, $col);
        }
    }
    
   /**
	 * Abre uma transação
	 * 
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
	 * @param none
	 * @return resource ou FALSE em caso de falha
	 * 
    */
    public function startTransaction(){
        return pg_query($this->conn, 'BEGIN');
    }
    
    /**
	 * Retorna o status da transação corrente
	 * 
	 * @author Leandro A. Ivanaga <leandroivanaga@brq.com>
     * @version 26/11/2013
	 * @param none
	 * @return 
	 *		PGSQL_TRANSACTION_IDLE = 0
	 * 		PGSQL_TRANSACTION_ACTIVE = 1
	 *		PGSQL_TRANSACTION_INTRANS = 2
	 *		PGSQL_TRANSACTION_INERROR = 3
	 *		PGSQL_TRANSACTION_UNKNOWN = 4
	 * 
    */
    public function statusTransaction(){
    	return pg_transaction_status($this->conn);
    }
    
    /**
     * Commita uma transação
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
     * @param none
     * @return resource ou FALSE em caso de falha
     *
     */
    public function commitTransaction(){
        return pg_query($this->conn, 'COMMIT');
    }
    
    /**
     * Desfaz/retorna uma transação
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 29/08/2013
     * @param none
     * @return resource ou FALSE em caso de falha
     *
     */
    public function rollbackTransaction(){
        return pg_query($this->conn, 'ROLLBACK');
    }
    
    /**
     * Aplica CAST conforme campos do BD.
     * 
     * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
     * @version 11/09/2013
     * @param array $arrayList [array associativo de Dados]
     * @param array $arrayIntList [array com campos do tipo INT do BD]
     * @param array $arrayFloatList [array com campos do tipo FLOAT do BD]
     * @param array $arrayFkList [array que contem as FOREIGN KEY]
     * @return array $arrayCast (array com o cast aplicado)
     */
    public function applyCast($arrayList, $arrayIntList=array(), $arrayFloatList=array(), $arrayFkList=array()){
    	$arrayCast = array();    	
    	foreach ($arrayList as $key => $value){
    		if(in_array($key, $arrayIntList)){
    			$arrayCast[$key] = Mascara::inteiro($value);
    		} elseif(in_array($key, $arrayFloatList)){
    			$arrayCast[$key] = (float) $value;
    		} elseif(in_array($key, $arrayFkList)){
    			$arrayCast[$key] = Mascara::inteiro($value);
    			if($arrayCast[$key] == 0){
    				$arrayCast[$key] = 'NULL';// Precisa ficar entre aspas pois caso contrário o PHP destroi a variável
    			}
    		} else{
    			$value = preg_replace("/[\']/", '', $value);
    			$arrayCast[$key] = "'".trim($value)."'";
    		}
    	}
    	
    	return $arrayCast;
    }    

    /**
     * Monta sql UPDATE com base em um array associativo.
     * OBS: valores TEXT/STRING/CHARs já devem se passados com aspas simples
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 19/11/2013
     * @param array $sqlArray [array associativo com campos e dados]
     * @return string $stringSQL (string SQL do tipo: campo = valor)
     */
    public function getSQLUpdateByArray($sqlArray=array()){
        $stringSQL = '';
        $strSeparador = '';
        foreach ($sqlArray as $key => $value){
            $stringSQL .= $strSeparador . $key.' = '.$value;
            $strSeparador = ', ';
        }
        return $stringSQL;
    }
    /**
     * Monta sql INSERT com base em um array associativo.
     * OBS: valores TEXT/STRING/CHARs já devem se passados com aspas simples
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 19/11/2013
     * @param array $sqlArray [array associativo com campos e dados]
     * @return string $stringSQL (string SQL do tipo: campo = valor)
     */
    public function getSQLInsertByArray($sqlArray=array()){
        $stringSQL = '';
        $strSeparador = '';
        $campos = '';
        $valores = '';
        foreach ($sqlArray as $key => $value){
            $campos  .= $strSeparador . $key;
            $valores .= $strSeparador . $value;
            $strSeparador = ',';
        }
        $stringSQL = " (".$campos.") VALUES (".$valores.") ";
        return $stringSQL;
    }
    
    /**
     * Busca todos os dados de um registro de uma tabela qualquer.
     *     OBS: retorna apenas um registro
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 19/11/2013
     * @param string $table (nome da tabela)
     * @param string  $cpName (nome da chave principal)
     * @return string $cpValue (valor da chave principal)
     * @return array $vRecord (array associativo do registro)
     */
    public function getDataRecordByID($table='', $cpName='', $cpValue=0){
        $sqlString = "
            SELECT
                *
            FROM
                " . $table . "
            WHERE
                " . $cpName . " = " . Mascara::inteiro($cpValue);
        
        $this->queryExec($sqlString);
        if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
    }

    /**
     * Executa uma operação de INSERT com base no nome da tabela e num array de dados.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 27/11/2013
     * @param string $tableName [nome da tabela]
     * @param string $returnColumnName [nome do campo/coluna a ser retornada]
     * @param array $dataArray [array associativo com campos e dados]
     *                 OBS: deve conter aspas quando necessário
     * @return mixed $returnColumnValue (valor da coluna de retorno) / false
     */
    public function pgInsert($tableName='', $returnColumnName='', $dataArray=array()){
        $valueString = '';
        $separString = '';
        $camposString = '';
        $valoresString = '';
        foreach ($dataArray as $key => $value){
            $camposString  .= $separString . $key;
            $valoresString .= $separString . $value;
            $separString = ', ';
        }
        
        $sqlString = "INSERT INTO " . $tableName . " (".$camposString.") VALUES (".$valoresString.") ";
        if(trim($returnColumnName) != '') {
            $sqlString .= " RETURNING " . $returnColumnName . ";";
        }
        $this->queryExec($sqlString);
        if($this->getAffectedRows() > 0){
            return $this->getAssoc();
        } else{
            return $this->queryStatus;
        }
    }
    
   /**
     * Executa uma operação de UPDATE com base no nome da tabela e num array de dados.
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 27/11/2013
     * @param string $tableName [nome da tabela]
     * @param string $whereClause [cláusula WHERE]
     * @param string $returnColumnName [nome do campo/coluna a ser retornada]
     * @param array $dataArray [array associativo com campos e dados]
     *                 OBS: deve conter aspas quando necessário
     * @return mixed $returnColumnValue (valor da coluna de retorno) / true
     */
    public function pgUpdate($tableName='', $whereClause='', $returnColumnName='', $dataArray=array()){
        $valueString = '';
        $sqlString = '';
        $separString = '';
        foreach ($dataArray as $key => $value){
            $valueString .= $separString . $key.' = '.$value;
            $separString = ', ';
        }
        $sqlString = "UPDATE " . $tableName . " SET " . $valueString;
        $sqlString .= " WHERE " . $whereClause;
        if(trim($returnColumnName) != '') {
            $sqlString .= " RETURNING " . $returnColumnName . ";";
        }
        $this->queryExec($sqlString);
        if($this->getAffectedRows() > 0){
            return $this->getAssoc();
        } else{
            return $this->queryStatus;
        }
    }

    /**
     * Executa uma operação de DELETE com base no nome da tabela e uma cláusula WHERE.
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 27/11/2013
     * @param string $tableName [nome da tabela]
     * @param string $whereClause [cláusula WHERE]
     * @return mixed $return true=ok/false=erro
     */
    public function pgDelete($tableName='', $whereClause=''){
        $sqlString = "DELETE FROM $tableName WHERE $whereClause;";
        $this->queryExec($sqlString);
        if($this->getAffectedRows() > 0){
            return true;
        } else{
            return $this->queryStatus;
        }
    }
    
    /**
     * Verifica se um registro existe em dada tabela com base em uma cláusula where.
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 19/11/2013
     * @param string $tableName [nome da tabela]
     * @param string $whereClause [cláusula WHERE]
     * @return boolean $return true=existe/false=não existe
     */
    public function pgExists($tableName='', $whereClause=''){
        $sqlString = "SELECT 1 FROM $tableName WHERE $whereClause;";
        $this->queryExec($sqlString);
        if($this->getNumRows() > 0){
            return true;
        } else{
            return false;
        }
    }
        
}
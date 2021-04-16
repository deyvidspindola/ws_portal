<?php
/**
 * INPC
 * 
 * @author Kleber Goto Kihara
 * @package Cadastro
 * @since 18/07/2013 10:35
 */

class CadInpcDAO {
	
	/**
	 * Conexão do Banco de Dados
	 * 
	 * @var resource
	 */
	private $conn;
	
	/**
	 * Método construtor
	 * 
	 * @return boolean
	 */
	public function __construct() {
		global $conn;
		
		$this->conn = $conn;
		
		return true;
	}
	
    /*
     * Método responsável por pesquisar registros
     * @params object $filtros
     * @return Array
     */
	public function pesquisar(stdClass $filtros) {
		try{
             $this->begin();
             $retorno = array();
             
             
             $query = "SELECT 
                              TO_CHAR(inpdt_referencia,'MM/YYYY') AS data,
                              inpvl_referencia AS valor
                         FROM
                              inpc
                         WHERE
                              inpdt_exclusao IS NULL ";
             
             if (!empty($filtros->inpdt_inicial) && !empty($filtros->inpdt_final)) {
                 $query .= "AND
                              inpdt_referencia BETWEEN '$filtros->inpdt_inicial 00:00:00' AND '$filtros->inpdt_final 23:59:59' ";
             }
             
             if (!empty($filtros->inpdt_referencia)) {
                 $query .= "AND
                              inpdt_referencia = '$filtros->inpdt_referencia' ";
             }
               
             $query .= "ORDER BY inpdt_referencia ASC";
             
               if (!$rs = pg_query($this->conn,$query)) {
                 $this->rollback();
                 return false;
               }
               
               if (pg_num_rows($rs) > 0) {
                    while ($row = pg_fetch_object($rs)) {
                         $retorno[] = $row;
                    }
               }
               
               $this->commit();
               
               return $retorno;
             
        } catch (Exception $e) {
              $this->rollback();
              return false;
        }
	}
	
     /*
     * Método responsável por salvar registros
     * @params object $dados
     * @return boolean
     */
	public function cadastrar(stdClass $dados) {
		
         try{
          $this->begin();
          $query = "INSERT 
                         INTO inpc
                         (inpdt_referencia,
                         inpvl_referencia)
                    VALUES
                         ('$dados->inpdt_referencia',
                         $dados->inpvl_referencia)";
          
          if (!$rs = pg_query($this->conn,$query)){
               $this->rollback();
               return false;
          }
          
          if(pg_affected_rows($rs)){
               $this->commit();
               return true;
          }
              
         } catch (Exception $e) {
              $this->rollback();
              return false;
         }
	}
	
    
    /*
     * Método responsável por alterar registros
     * @params object $dados
     * @return boolean
     */
	public function alterar(stdClass $dados) {
		try{
          $this->begin();
          $query = "UPDATE
                              inpc
                    SET
                              inpvl_referencia = $dados->inpvl_referencia
                    WHERE
                              inpdt_referencia = '$dados->inpdt_referencia'";
          
          if (!$rs = pg_query($this->conn,$query)){
               $this->rollback();
               return false;
          }
          
          if(pg_affected_rows($rs)){
               $this->commit();
               return true;
          }
              
         } catch (Exception $e) {
              $this->rollback();
              return false;
         }
	}
	
    /*
     * Método responsável por desabilitar registros
     * @params object $dados
     * @return boolean
     */
	public function excluir(stdClass $dados) {
		try{
          $this->begin();
          $query = "UPDATE
                         inpc
                    SET
                         inpdt_exclusao = 'NOW()',
                         inpusuoid_exclusao = $dados->usuario
                    WHERE
                         inpdt_referencia = '$dados->inpdt_referencia'";
          
          if (!$rs = pg_query($this->conn,$query)){
               $this->rollback();
               return false;
          }
          
          if(pg_affected_rows($rs)){
               $this->commit();
               return true;
          }
              
         } catch (Exception $e) {
              $this->rollback();
              return false;
         }
	}
    
    /*
     * BEGIN
     */
    private function begin(){
         pg_query($this->conn, "BEGIN");
    }
    
    
     /*
     * COMMIT
     */
    private function commit(){
         pg_query($this->conn, "COMMIT");
    }
    
     /*
     * ROLLBACK
     */
    private function rollback(){
         pg_query($this->conn, "ROLLBACK");
    }
            
	
}
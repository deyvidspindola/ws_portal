<?php

class CadOperadoraDiaDAO {

     private $conn;

     public function __construct() {
         global $conn;
         $this->conn = $conn;
     }

     /*
      * Método que realiza busca de operadoras
      * @return array $resultado
      */
     public function buscarOperadoras(){

          $resultado = array();

          try{
               $this->abrirTransacao();
               $query = "SELECT
                               opeoid AS id,
                               opeoperadora AS operadora
                         FROM
                              operadora
                         WHERE
                              opedt_exclusao IS NULL
                         ORDER BY opeoperadora ASC";

               if (!$rs = pg_query($this->conn,$query)){
                    $this->abortarTransacao();
                    return $resultado;
               }

               if (pg_num_rows($rs) > 0) {
                    while ($row = pg_fetch_object($rs)) {
                         $resultado[] = $row;
                    }
               }

               $this->fecharTransacao();
               return $resultado;

          } catch (Exception $e) {
               $this->abortarTransacao();
               return $resultado;
          }
     }

     /*
      * Busca operadora do dia por ID
      * @param int $id
      * @return stdClass
      */
     public function buscarOperadora($id) {
         try{
             $query = "SELECT
                            opdoid,
                            opdopeoid,
                            TO_CHAR(opddt_inivigencia,'DD/MM/YYYY') AS opddt_inivigencia,
                            TO_CHAR(opddt_fimvigencia,'DD/MM/YYYY') AS opddt_fimvigencia
                       FROM
                            operadora_dia
                       WHERE
                            opdoid = " . $id . "
                       LIMIT 1";

        if (!$rs = pg_query($this->conn,$query)){
            $this->abortarTransacao();
            return false;
        }

        if (pg_num_rows($rs) > 0) {
             return pg_fetch_object($rs);
         }

        } catch (Exception $e) {
            return false;
        }
     }

     /*
      * Método de realizar pesquisar
      * @param stdClass $parametros
      * @return array $resultado
      */
     public function pesquisar (stdClass $parametros) {

          $resultado  = array();

          $filtro = "";
          
          if (isset($parametros->opdopeoid) && !empty($parametros->opdopeoid)){
              $filtro .= "AND
                              opdopeoid = " . $parametros->opdopeoid;
          }
          
          if (isset($parametros->opddt_inivigencia) && isset($parametros->opddt_fimvigencia) && !empty($parametros->opddt_inivigencia) && !empty($parametros->opddt_fimvigencia)){
              $filtro .= "AND
                              (
                                  opddt_inivigencia BETWEEN '" . $parametros->opddt_inivigencia . "' AND '" . $parametros->opddt_fimvigencia . "'
                          OR
                                  opddt_fimvigencia BETWEEN '" . $parametros->opddt_inivigencia . "' AND '" . $parametros->opddt_fimvigencia . "'
                              )";
          }
          
          

          try{
               $this->abrirTransacao();
               $query = "SELECT
                              opdoid AS id,
                              opeoperadora AS operadora,
                              TO_CHAR(opddt_inivigencia,'DD/MM/YYYY') AS data_inicio,
                              TO_CHAR(opddt_fimvigencia,'DD/MM/YYYY') AS data_fim
                          FROM
                              operadora_dia
                          INNER JOIN
                              operadora ON opeoid = opdopeoid
                          WHERE
                              opddt_exclusao IS NULL
                              
                          " . $filtro . "
                              
                          ORDER BY
                              opddt_inivigencia, opddt_fimvigencia, opeoperadora ASC";

               if (!$rs = pg_query($this->conn, $query)){
                    $this->abortarTransacao();
                    return false;
               }

               if (pg_num_rows($rs) > 0) {
                    while ($row = pg_fetch_object($rs)) {
                         $resultado[] = $row;
                    }
               }

               $this->fecharTransacao();
               return $resultado;

          } catch (Exception $e) {
               $this->abortarTransacao();
               return false;
          }

     }

      /*
      * Método responsável por salvar
      * @param stdClass $dados
      * @return boolean
      */
     public function salvar(stdClass $dados) {

         try{
             $this->abrirTransacao();
             $query = "INSERT INTO
                                operadora_dia
                                (opdopeoid,opddt_inivigencia,opddt_fimvigencia)
                       VALUES
                                (" . $dados->opdopeoid . ",'" . $dados->opddt_inivigencia . "','" . $dados->opddt_fimvigencia . "') returning opdoid";

        if (!$rs = pg_query($this->conn, $query)){
               $this->abortarTransacao();
               return false;
        }

        if (pg_affected_rows($rs)) {
            
            $id = pg_fetch_result($rs, '0', 'opdoid');
            $this->fecharTransacao();
            return $id;
        }

        } catch (Exception $e) {
            $this->abortarTransacao();
            return false;
        }

     }

     /*
      * Método responsável por atualizar
      * @param stdClass $dados
      * @return boolean
      */
     public function atualizar(stdClass $dados) {

         try{
             $this->abrirTransacao();
             $query = "UPDATE
                            operadora_dia
                        SET
                            opdopeoid = " . $dados->opdopeoid . ",
                            opddt_inivigencia = '" . $dados->opddt_inivigencia . "',
                            opddt_fimvigencia = '" . $dados->opddt_fimvigencia . "'
                        WHERE
                            opdoid = " . $dados->opdoid;

        if (!$rs = pg_query($this->conn, $query)){
               $this->abortarTransacao();
               return false;
        }

        if (pg_affected_rows($rs)) {
            $this->fecharTransacao();
            return true;
        }

        } catch (Exception $e) {
            $this->abortarTransacao();
            return false;
        }

     }


      /*
      * Método responsável por deletar
      * @param int $id
      * @return boolean
      */
     public function deletar ($id) {
         try{
             $this->abrirTransacao();
             $query = "UPDATE
                            operadora_dia
                        SET
                            opddt_exclusao = 'NOW()'
                        WHERE
                            opdoid = " . $id;
            if (!$rs = pg_query($this->conn, $query)){
                $this->abortarTransacao();
                return false;
            }

            if (pg_affected_rows($rs)) {
                $this->fecharTransacao();
                return true;
            }

        } catch (Exception $e) {
            $this->abortarTransacao();
            return false;
        }
     }


      /*
      * Método responsável por deletar
      * @param int $id
      * @return boolean
      */
     public function verificarOperadoraVigencia($id) {
         try{
             $this->abrirTransacao();
             $query = "SELECT
                              TO_CHAR(opddt_inivigencia,'YYYY-MM-DD') AS data_inicio,
                              TO_CHAR(opddt_fimvigencia,'YYYY-MM-DD') AS data_fim
                       FROM
                              operadora_dia
                       WHERE
                              opdoid = " . $id . "
                       LIMIT 1";


            if (!$rs = pg_query($this->conn, $query)){
                $this->abortarTransacao();
                return false;
            }

            if (pg_num_rows($rs)) {
                $this->fecharTransacao();

                $dt_inicio = strtotime(pg_fetch_result($rs, 0, 'data_inicio'));
                $dt_fim = strtotime(pg_fetch_result($rs, 0, 'data_fim'));

                if ($dt_inicio <= strtotime(date('Y-m-d')) || $dt_fim <= strtotime(date('Y-m-d')) ) {
                    return 'N';
                }else{
                    return 'S';
                }

            }


        } catch (Exception $e) {
             $this->abortarTransacao();
             return false;
        }
     }

      /**
      * Verifica se o periodo já foi cadastrado.
      * $parametros->opddt_inivigencia Data de inicio
      * $parametros->opddt_fimvigencia Data final
      * $parametros->opdoid id do registro (opcional, passar apenas quando for edição)
      * @param stdClass $parametros
      * @return boolean se o periodo já existe no banco de dados
      */
     public function validarPeriodo(stdClass $parametros){

         try{

             //Filtro
             $where = "";

             //Verifica se o periodo foi informado
             if ( ( isset($parametros->opddt_inivigencia) && !empty($parametros->opddt_inivigencia) ) &&
                    ( isset($parametros->opddt_fimvigencia) && !empty($parametros->opddt_fimvigencia) ) ){

                 $where .= " AND
                     (
                        ('" . $parametros->opddt_inivigencia . "' BETWEEN opddt_inivigencia AND opddt_fimvigencia) OR
                        ('" . $parametros->opddt_fimvigencia . "' BETWEEN opddt_inivigencia AND opddt_fimvigencia)
                    )";

             } else {
                 throw new Exception('Período não informado.');
             }


             //Verifica o ID do registro, caso seja uma alteração
             if ( isset($parametros->opdoid) && !empty($parametros->opdoid) ){
                 $where .= " AND opdoid != " . $parametros->opdoid;
             }

             $query = "
                 SELECT EXISTS(
                    SELECT
                        1
                    FROM
                        operadora_dia
                    WHERE opddt_exclusao IS NULL
                    " . $where . "
                 ) as existe";


            $this->abrirTransacao();

            if (!$rs = pg_query($this->conn, $query)){
                $this->abortarTransacao();
                return false;
            }

            $resultado = pg_fetch_object($rs);

            $this->fecharTransacao();

            return ($resultado->existe == 't');

         } catch (Exception $e) {
               $this->abortarTransacao();
               throw new Exception($e->getMessage());
           }
     }



     /**
	 * Inicia uma transação com o banco de dados
	 */
	public function abrirTransacao() {
		pg_query($this->conn, "BEGIN;");
	}

	/**
	 * Comita uma transação com o banco de dados
	 */
	public function fecharTransacao() {
		pg_query($this->conn, "COMMIT");
	}

	/**
	 * Aborta uma transação com o banco de dados
	 */
	public function abortarTransacao() {
		pg_query($this->conn, "ROLLBACK;");
	}

}
<?php

/**
 * description: Persistir altera��o de senha e buscar o usu�rio.
 * @author denilson.sousa
 *
 */

/** Importa��es das Classes */
require 'modulos/TI/Model/TIUsuarioModel.class.php';

class TIVerificaObrigacaoDuplicadaDAO {
	
	/** @var string conx�o com o banco  */
	private $conn;
	
	/**
	 * Injeta a string de conex�o da intranet
	 * do config.php
	 */
	public function __construct() {
		global $conn;
		$this->conn = $conn;
	}
	
	/**
	 * 
	 * @param string $connumero - numero do contrato 
	 * @throws Exception
	 * @return resource
	 */
	public function pesquisar($connumero = '') {
		try {
			$sql = "SELECT tcs.max,cs.consconoid,cs.consobroid,cs.consoid,conssituacao,consdt_validade,cs.consinstalacao,obrobrigacao from
                        (
                                select consobroid, consconoid, count(*), max(consoid),obrobrigacao from contrato_servico join obrigacao_financeira on consobroid = obroid where
                                obrtag_pacote <> ''
                                and (consiexclusao is null)

                                group by consobroid, consconoid, obrobrigacao
                                having count(3) > 1
                                order by 3
                        ) tcs
                        join contrato_servico cs on (cs.consconoid = tcs.consconoid and cs.consobroid = tcs.consobroid)
                        where cs.consiexclusao is null 		
					";
			
			if ($connumero != '') {
				$sql .= " AND cs.consconoid = $connumero";
								
			}

			$sql .= " order by consconoid, consobroid";

			$result = pg_query($this->conn, $sql);
			
			if (! $result) {
				throw new Exception ( 'ERRO: <b>Falha ao listar dados do Usuário.</b>' );
			} else {
				return $result;
			}
		} catch ( Exception $e ) {
			return $e->getMessage ();
		}
	}
	public function corrigirObrigacao ($connumero = ''){
		try {
			$sql = "SELECT tcs.max,cs.consconoid,cs.consobroid,cs.consoid,conssituacao,consdt_validade,cs.consinstalacao,obrobrigacao from
                        (
                                select consobroid, consconoid, count(*), max(consoid),obrobrigacao from contrato_servico join obrigacao_financeira on consobroid = obroid where
                                obrtag_pacote <> ''
                                and (consiexclusao is null)
                                
				group by consobroid, consconoid, obrobrigacao
                                having count(3) > 1
                                order by 3
                        ) tcs
                        join contrato_servico cs on (cs.consconoid = tcs.consconoid and cs.consobroid = tcs.consobroid)
                        where cs.consiexclusao is null
                                        ";

                        if ($connumero != '') {
                                $sql .= " AND cs.consconoid = $connumero";

                        }

                        $sql .= " order by consconoid, consobroid";
 
			$result = pg_query($this->conn, $sql);	
                        
			if (! $result) {
                                throw new Exception ( 'ERRO: Nao foi possivel carregar os resultados.</b>' );
                        } else {
				$update = '';

                                for($i = 0; $i < pg_num_rows($result); $i++) {
                                        $resultado = pg_fetch_array($result);
					
					$subsql = "select * from contrato_servico where consconoid = ".$resultado['consconoid']." and consobroid = ".$resultado['consobroid'] . " and consoid != " . $resultado['consoid'] . " and consiexclusao is null";
					
					switch($resultado['conssituacao'])
					{
						/*case 'C':
							$subsql .= " and (conssituacao = 'D' or conssituacao = 'M' or conssituacao = 'B')";
						break;
						case 'D':
							$subsql .= " and (conssituacao = 'D' and (consdt_validade < '".$resultado['consdt_validade']."' and consdt_validade is not null) or (consdt_validade is not null and consdt_validade < now()))";
						break;
						case 'M':
							$subsql .= " and (conssituacao = 'D')";
                                                break;
						case 'B':
                                                        $subsql .= " and (conssituacao = 'D' or conssituacao = 'M')";
                                                break;
						default:
							$subsql .= " and FALSE";		
						break;*/
						
						case 'C':
							$subsql .= " and (false";
                                                break;
                                                case 'D':
                                                        $subsql .= " and (((consdt_validade is not null and consdt_validade > now()) or conssituacao = 'C' or conssituacao = 'M' or conssituacao = 'B')";
                                                break;
                                                case 'M':
                                                        $subsql .= " and ((conssituacao = 'C' or conssituacao = 'B')";
                                                break;
                                                case 'B':
                                                        $subsql .= " and ((conssituacao = 'C')";
                                                break;
                                                default:
                                                        $subsql .= " and (FALSE";
                                                break;
					}
					if($resultado['consinstalacao'] != '')
						$subsql .= " or (conssituacao = '".$resultado['conssituacao']."' and consinstalacao > '".$resultado['consinstalacao']."'))"; // Verifica se tem mesma obrigaccao com mesma situacao mas com data de instalacao diferentes
					else
						$subsql .= " or (conssituacao = '".$resultado['conssituacao']."' and consinstalacao is not null))";	
					//echo($subsql . "<br />");	
					$subresult = pg_query($this->conn, $subsql);
					if(pg_num_rows($subresult) > 0){
						//echo("<br /> <br />-- consconoid = ".$resultado['consconoid']." and consobroid = ".$resultado['consobroid'] . " -- conssituacao = ". $resultado['conssituacao'] ." <br />" );
						//echo("UPDATE contrato_servico set consiexclusao = now() , consusuoid_excl = 3 where consoid = " . $resultado['consoid'] . "; <br />");
						$update .= "UPDATE contrato_servico set consiexclusao = now() , consusuoid_excl = 4 where consoid = " . $resultado['consoid'] . "; ";
					}else{
                                                $subsql_2 = "select consoid from contrato_servico where consconoid = ".$resultado['consconoid']." and consobroid = ".$resultado['consobroid'] . " and consoid != " . $resultado['consoid'] . " and consiexclusao is null and conssituacao = '".$resultado['conssituacao']."'";
 // Query para verificar se existe obrigacao com o mesmo obroid e tambem com a mesma situacao

                                                if($resultado['consinstalacao'] != '')
                                                        $subsql_2 .= " and consinstalacao = '".$resultado['consinstalacao']."'"; // Caso tenha data de instalacao valida se eh a mesma

                                                $subresult_2 = pg_query($this->conn, $subsql_2);
						//echo("<br /> ----- ". $subsql_2 . "<br /> Max: ".$resultado['max']);

                                                if((pg_num_rows($subresult_2) > 0) and $resultado['max'] != $resultado['consoid'] ){
							//echo("<br /> <br />-- consconoid = ".$resultado['consconoid']." and consobroid = ".$resultado['consobroid'] . " -- conssituacao = ". $resultado['conssituacao'] ." <br />" );
							//echo("UPDATE contrato_servico set consiexclusao = now() , consusuoid_excl = 4 where consoid = " . $resultado['consoid'] . "; <br />");
							$update .= "UPDATE contrato_servico set consiexclusao = now() , consusuoid_excl = 4 where consoid = " . $resultado['consoid'] . "; ";
                                                }
                                        }
					
				}
				//echo($update);
				$this->executarQuery($update);
				return 'Itens corrigidos!'; 
                        }
                } catch ( Exception $e ) {
                        return $e->getMessage ();
                }	
	}
	
	 /** Abre a transacao */
        public function begin(){
                pg_query($this->conn, 'BEGIN');
        }

        /** Finaliza um transacao */
        public function commit(){
                pg_query($this->conn, 'COMMIT');
        }

        /** Aborta uma transacao */
        public function rollback(){
                pg_query($this->conn, 'ROLLBACK');
        }

        private function executarQuery($query) {
		$this->begin();
        	if(!$rs = pg_query($this->conn, $query)) {
			$this->rollback();
            		throw new Exception('Erro ao processar a query!');
        	}
		$this->commit();
        	return $rs;
    	}
}

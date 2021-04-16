<?php
/**
 * @author Angelo Frizzo Junior / Kleber Goto Kihara <angelo.frizzo@meta.com.br, kleber.kihara@meta.com.br>
 * @description	Módulo para Parametrização gestor Crédito - DAO
*/
class CadParametrizacaoConsultaGestorCreditoDAO {

	private $conn;
	private $usuarioLogado;
	
	/**
	 * __construct()
	 *
	 * @param none
	 * @return none
	 * @description	Método construtor da classe
	 */
	function __construct() {
		global $conn;
		
		$this->conn = $conn;
		
		$this->usuarioLogado = Sistema::getUsuarioLogado();
	}
	
	/**
	 * buscarTiposContratos()
	 *
	 * @param none
	 * @return $tipos (array com dados do tipo de contrato)
	 */
	public function buscarTiposContratos() {
		$tipos = array();
		$sql = "SELECT 
						tpcoid, 
						tpcdescricao 
				FROM 
						tipo_contrato 
				ORDER BY 
						tpcdescricao";
		
		
		if ($query = pg_query($this->conn, $sql)) {
			if (pg_num_rows($query) > 0) {
				while ($row = pg_fetch_object($query)) {
					array_push($tipos, $row);
				}
				return $tipos;
			} else {
				return $tipos;
			}
		}		
	}
    
	/**
	 * buscarTiposContratos()
	 *
	 * @param $argumentos (array com filtros para montagem de sql)
	 * @return $retorno (array com dados do tipo de propostas)
	 */
	public function buscarTiposPropostas($argumentos = array()) {
		$filtro  = count($argumentos) ? 'WHERE '.implode(' AND ', $argumentos) : '';
		$retorno = array();
		
		$sql = "
			SELECT
				tppoid,
				tppdescricao,
				tppcodigo
			FROM tipo_proposta
			$filtro
		";
		if($resultado = pg_query($this->conn, $sql)) {
			if(pg_num_rows($resultado) > 0) {
				while($row = pg_fetch_object($resultado)) {
					array_push($retorno, $row);
				}
			}
		}
		
		return $retorno;
	}
 	
	/**
	 * pesquisar()
	 *
	 * @param $filtro (array com filtros para montagem de sql)
	 * @return $retorno (array com resultado da pesquisa)
	 */
	public function pesquisar($filtro) {
		
		
        $condicoesSql = array();
        
        if(!empty($filtro->tipoPessoa)) {
            array_push($condicoesSql, " AND upper(gestor_credito_parametrizacao.gcptipopessoa) ='".strtoupper($filtro->tipoPessoa)."'");
        }
        

       if(trim($filtro->tipoContrato) != ""){
            array_push($condicoesSql, " AND gestor_credito_parametrizacao.gcptipocontrato = ".$filtro->tipoContrato);
        }
        
		if(!empty($filtro->tipoProposta)) {
			$filtro->tipoProposta = explode('-', $filtro->tipoProposta);
			$filtro->tipoProposta = $filtro->tipoProposta[0];
			
			array_push($condicoesSql, " AND gestor_credito_parametrizacao.gcptppoid = '".$filtro->tipoProposta."'");
		}
		
		if(!empty($filtro->subtipoProposta)) {
			$filtro->subtipoProposta = explode('-', $filtro->subtipoProposta);
			$filtro->subtipoProposta = $filtro->subtipoProposta[0];
				
			array_push($condicoesSql, " AND gestor_credito_parametrizacao.gcptppoid_sub = '".$filtro->subtipoProposta."'");
		}
		
		if(trim($filtro->vaiGestor) != ""){
			array_push($condicoesSql, " AND gestor_credito_parametrizacao.gcpindica_gestor = '".$filtro->vaiGestor."'");
		}

        $condicaoSql = implode('', $condicoesSql);
        
		$sql = "
			SELECT
				gestor_credito_parametrizacao.gcpoid,
				CASE
					WHEN upper(gestor_credito_parametrizacao.gcptipopessoa) = 'F' THEN 'Física'
					WHEN upper(gestor_credito_parametrizacao.gcptipopessoa) = 'J' THEN 'Jurídica'
				END AS tipoPessoa,
				tipo_proposta.tppdescricao AS tipoProposta,
				tipo_proposta.tppcodigo AS codigoTipoProposta,
				subtipo_proposta.tppdescricao AS subtipoProposta,
				subtipo_proposta.tppcodigo AS codigoSubtipoProposta,
				tipo_contrato.tpcdescricao AS tipoContrato,
				CASE
					WHEN gestor_credito_parametrizacao.gcpindica_gestor = 't' THEN 'Sim'
					WHEN gestor_credito_parametrizacao.gcpindica_gestor = 'f' THEN 'Não'
				END AS vaigestor,
				gestor_credito_parametrizacao.gcpconlimite AS limite
			FROM gestor_credito_parametrizacao
				INNER JOIN tipo_contrato ON gestor_credito_parametrizacao.gcptipocontrato = tipo_contrato.tpcoid
				LEFT JOIN tipo_proposta ON gestor_credito_parametrizacao.gcptppoid = tipo_proposta.tppoid
				LEFT JOIN tipo_proposta AS subtipo_proposta ON gestor_credito_parametrizacao.gcptppoid_sub = subtipo_proposta.tppoid
			WHERE gcpdt_exclusao IS NULL
			".$condicaoSql."
			ORDER BY tipo_contrato.tpcdescricao, tipo_proposta.tppdescricao, gestor_credito_parametrizacao.gcptipopessoa";
		
		$rs = pg_query($this->conn, $sql);
        
        $resultado = array();
        
        if(pg_num_rows($rs) > 0) {
            while($gestor_credito = pg_fetch_object($rs)) {
                array_push($resultado, $gestor_credito);
            }
		}
        
        return $resultado;
    }
    
	/**
	 * buscarPorId()
	 *
	 * @param $id (ID do registro a ser pesquisado)
	 * @return $retorno (objeto com resultado da pesquisa)
	 */
    public function buscarPorId($id) {
		$id = (int)$id;
		$sql = "SELECT 
						* 
				FROM 
						gestor_credito_parametrizacao
				WHERE 
						gcpoid = ". $id;
		if ($query = pg_query($this->conn, $sql)) {
			if (pg_num_rows($query) > 0) {
				return pg_fetch_object($query);
			} else {
				return "";
			}
		}
	}
	
	/**
	 * salvar()
	 *
	 * @param $pessoa (tipo pessoa)
	 * @param $proposta (tipo proposta antigo)
	 * @param $propostaid (tipo proposta novo)
	 * @param $subpropostaid (tipo subproposta novo)
	 * @param $contrato (contrato)
	 * @param $gestor (indica gestor)
	 * @param $limite (limite contrato)
	 * @return $retorno (boleano com sucesso ou insucesso da operação)
	 */
	public function salvar($pessoa, $proposta, $propostaid, $subpropostaid, $contrato, $gestor, $limite, $id=0) {
		
		$sql = "";
		if ($id == 0) { 
			$sql = "INSERT INTO 
								gestor_credito_parametrizacao
					
								(
									gcptipopessoa, 
							 		gcptipoproposta,
									gcptppoid,
					 				gcptppoid_sub,
								 	gcptipocontrato,
									gcpindica_gestor,
									gcpconlimite
								)
						VALUES
								(
									'".$pessoa."', 
									'".$proposta."', 
									".$propostaid.",
									".$subpropostaid.",											
									".$contrato.",
									'".$gestor."',
									".$limite."
								)";
			
		} else {
			$sql = "UPDATE 
							gestor_credito_parametrizacao
					SET 
							gcptipopessoa = '".$pessoa."', 
							gcptipoproposta = '".$proposta."',
							gcptppoid = ".$propostaid.",
			 				gcptppoid_sub = ".$subpropostaid.",
						 	gcptipocontrato = ".$contrato.",
							gcpindica_gestor = '".$gestor."',
							gcpconlimite = '".$limite."'
					WHERE 
							gcpoid = ". $id;
		}

		if ($query = pg_query($this->conn, $sql)) {
			if (pg_affected_rows($query) > 0) {
				return true;
			} else {
				return false;
			}
		}		
	}
	
	/**
	 * verificarExistenciaParametrizacao()
	 *
	 * @param $pessoa (tipo pessoa)
	 * @param $proposta (tipo proposta antigo)
	 * @param $propostaid (tipo proposta novo)
	 * @param $subpropostaid (tipo subproposta novo)
	 * @param $contrato (contrato)
	 * @param $gestor (indica gestor)
	 * @param $limite (limite contrato)
	 * @param $id (ID do registro a ser pesquisado)
	 * @return $retorno (boleano com sucesso ou insucesso da operação)
	 */
	 public function verificarExistenciaParametrizacao($pessoa, $proposta, $propostaid, $subpropostaid, $contrato, $gestor, $limite, $id) {
		$sql = "SELECT
						COUNT(1)
				FROM 
						gestor_credito_parametrizacao
				WHERE
						gcptipopessoa = '".$pessoa."'
				AND 	
						gcptipoproposta = '".$proposta."'
				AND 	
						gcptppoid = ".$propostaid."
				AND 	
						gcptppoid_sub = ".$subpropostaid."
				AND 	
						gcptipocontrato = ".$contrato."
				AND 	
						gcpindica_gestor = '".$gestor."'
				AND 	
						gcpconlimite = ".$limite."
								
				AND     gcpoid != ".$id."
				
				AND 	
						gcpdt_exclusao IS NULL";
		
		if ($query = pg_query($this->conn, $sql)) {
			if (pg_num_rows($query) > 0) {
				$row = pg_fetch_row($query);
				return $row[0];	
			}				
		}
	}
    
	/**
	 * excluir()
	 *
	 * @param $id (ID do registro a ser excluído)
	 * @return $retorno (boleano com sucesso ou insucesso da operação)
	 */
	public function excluir($id) {
                
        $sql = "
            UPDATE 
                	gestor_credito_parametrizacao
            SET
                	gcpdt_exclusao = NOW(),
                	gcpusuoidexclusao = ". $this->usuarioLogado->cd_usuario ."
            WHERE
                	gcpoid = ". $id;
            
        return pg_affected_rows(pg_query($this->conn, $sql));
    }
	
}
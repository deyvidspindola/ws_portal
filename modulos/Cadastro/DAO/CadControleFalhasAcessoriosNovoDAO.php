<?php

/**
 * Classe de persistência de dados para Nova Info. Controle de Falhas
 */
class CadControleFalhasAcessoriosNovoDAO {
	
	protected $conn;
	
	/**
	 * Construtor da Classe
	 * @param $conn
	 */
	public function __construct($conn) {
		 $this->conn = $conn;
	}
	
	/**
	 * Método que realiza a pesquisa no banco de dados pelos parâmetros informados.
	 * @param string $tipo
	 * @param string $serial
	 * @return array
	 */
	public function pesquisar($tipo, $serial) {
		$sql = "SELECT 
						imoboid,
						imobimsoid, 
						imobserial, 
						prdproduto, 
						ifddescricao, 
						ifadescricao, 
						ifcdescricao, 
						to_char(cfadt_entrada, 'dd/mm/YYYY') as cfadt_entrada,
						cfaoid
				FROM
						controle_falhas_acessorio
				INNER JOIN
						imobilizado ON imoboid = cfaimoboid
				INNER JOIN
						produto ON prdoid = imobprdoid
				INNER JOIN
						item_falha_acao ON cfaifaoid = ifaoid
				INNER JOIN
						item_falha_defeito ON cfaifdoid = ifdoid
				INNER JOIN
						item_falha_componente ON ifcoid = cfaifcoid
				WHERE
						imobimotoid = '".pg_escape_string($tipo)."'
				AND
						imobserial = '".pg_escape_string($serial)."'
				AND
						cfadt_exclusao IS NULL
				ORDER BY 
						cfadt_entrada";
		
		$resultado=array();
		if ($query = pg_query($sql)) {
			if (pg_num_rows($query) > 0) {
				while($row=pg_fetch_object($query)) {
					array_push($resultado, $row);
				}
			} 
		}
		return $resultado;
	}
	
	public function pesquisarImobilizado($tipo, $serial){
		$sql = "SELECT
					imoboid,
					imobimsoid,
					imobserial,
					prdproduto
				FROM imobilizado
				INNER JOIN
					produto ON prdoid = imobprdoid
				WHERE
					imobimotoid = '$tipo'
				AND
					imobserial = '".pg_escape_string($serial)."' ";
			
		$resultado=array();
		if ($query = pg_query($sql)) {
			if (pg_num_rows($query) > 0) {
				while($row=pg_fetch_object($query)) {
					array_push($resultado, $row);
				}
			} 
		}
		return $resultado;
	}
	
	
	/**
	 * Busca registros pelo identificador (cfaoid)
	 * @param int $cfaoid
	 * @return stdClass
	 */
	public function buscarPorCfaoid($cfaoid) {
		$sql = "SELECT
							imoboid,
							imobimsoid,
							imobserial,
							prdproduto,
							ifdoid,
							ifddescricao,
							ifaoid,
							ifadescricao,
							ifcoid,
							ifcdescricao,
							to_char(cfadt_entrada, 'dd/mm/YYYY') as cfadt_entrada,
							cfaoid
				FROM
							controle_falhas_acessorio
				INNER JOIN
							imobilizado ON imoboid = cfaimoboid
				INNER JOIN
							produto ON prdoid = imobprdoid
				INNER JOIN
							item_falha_acao ON cfaifaoid = ifaoid
				INNER JOIN
							item_falha_defeito ON cfaifdoid = ifdoid
				INNER JOIN
							item_falha_componente ON ifcoid = cfaifcoid
				WHERE
							cfaoid = ".intval($cfaoid)."
				AND
							cfadt_exclusao IS NULL";
		
		if ($query = pg_query($sql)) {
			if (pg_num_rows($query) > 0) {
				return pg_fetch_object($query);
			} else {
				return new stdClass();
			}
		}
	}
    
    /**
	 * Busca registros pelo identificador (imoboid)
	 * @param int $imoboid
	 * @return stdClass
	 */
	public function buscarPorImoboid($imoboid) {
		$sql = "SELECT
							imoboid,
							imobimsoid,
							imobserial,
							prdproduto							
                FROM							
							imobilizado
				INNER JOIN
							produto ON prdoid = imobprdoid				
				WHERE
							imoboid = ".intval($imoboid)."	";
		
		if ($query = pg_query($sql)) {
			if (pg_num_rows($query) > 0) {
				return pg_fetch_object($query);
			} else {
				return new stdClass();
			}
		}
	}
	
	/**
	 * Busca a data de Entrada no Laboratório do registro imobilizado
	 * @param int $imoboid
	 * @return string
	 */
	public function buscarDataEntradaLab($imoboid) {
		$resultado = new stdClass();
		$resultado->data = "";
		$resultado->imobhoid = "";
		$sql = "SELECT						
						to_char(MAX(imobhcadastro), 'dd/mm/YYYY') as data,
						imobhoid
				FROM
						imobilizado_historico
				WHERE
						imobhimsoid = 22
				AND
						imobhimoboid = ".intval($imoboid)."
				GROUP BY
						imobhoid
				ORDER BY 
						imobhoid DESC";
		
		if ($query = pg_query($sql)) {
			if (pg_num_rows($query) > 0) {
				$resultado = pg_fetch_object($query);
			}
		}
		return $resultado;
	}
	
	/**
	 * Buscar Dados de OS de Assistência/Retirada por Contrato
	 * @param int $connumero
	 * @return stdClass
	 */
	public function buscarDadosOsAssistenciaRetirada($connumero, $imoboid) {
		$sql = "SELECT
							imobserial,
							osdfdescricao,
							otcdescricao,
							otodescricao,
							otsdescricao,
							otadescricao,
							otidescricao
				FROM
							ordem_servico
				INNER JOIN
							ordem_servico_item 			ON ordoid 		= ositordoid
				INNER JOIN
							os_tipo_item 				ON otioid 		= ositotioid
				INNER JOIN
							os_tipo 	 				ON ostoid 		= otiostoid 
				INNER JOIN
							obrigacao_financeira 		ON obroid 	    = otiobroid
				INNER JOIN
							imobilizado					ON imobprdoid 	= obrprdoid
				INNER JOIN
							ordem_servico_defeito 		ON osdfoid 		= ositosdfoid_analisado
				LEFT  JOIN
							os_tipo_causa				ON otcoid 		= ositotcoid
				LEFT  JOIN
							os_tipo_ocorrencia			ON otooid		= ositotooid
				LEFT  JOIN
							os_tipo_solucao				ON otsoid		= ositotsoid
				LEFT  JOIN
							os_tipo_componente_afetado 	ON otaoid 		= ositotaoid
				WHERE
							ostoid IN (3, 4) 
				AND
							imoboid = ".intval($imoboid)."
				AND
							ordconnumero = ".intval($connumero);
		
		$registro = false;
		if ($query = pg_query($sql)) {
			if (pg_num_rows($query) > 0) {
				$registro=pg_fetch_object($query);
			}
		}
		return $registro;
	}
	
	/**
	 * Busca o número do último contrato cadastro para o acessório
	 * @param int $imoboid
	 * @return string
	 */
	public function buscarUltimoContratoCadastrado($imoboid) {
		$sql = "SELECT
						MAX(imobhcadastro) as imobhcadastro,
						imobhconoid AS connumero
				FROM
						imobilizado_historico
				INNER JOIN
						imobilizado ON imoboid = imobhimoboid
				WHERE
						imobhconoid IS NOT NULL
				AND
						imoboid = ".intval($imoboid)."
				GROUP BY
						imobhcadastro, connumero";
		
		if ($query = pg_query($sql)) {
			if (pg_num_rows($query) > 0) {
				$registro = pg_fetch_object($query);
				return $registro->connumero;	
			}
		}
		return "";
	}
	
	/**
	 * Método para inserir registros
	 * @param stdClass $parametros
	 * @return boolean
	 */
	public function inserirRegistro($parametros) {
		if($parametros->cfaoid > 0) {
			$sql = "UPDATE 
						controle_falhas_acessorio 
					SET 
						cfaifdoid=".$parametros->defeito_lab.", 
						cfaifaoid=".$parametros->acao_lab.", 
						cfaifcoid=".$parametros->componente_lab."
					WHERE 
						cfaoid=".$parametros->cfaoid."	
					RETURNING cfaoid";
		} 
		else {
			$sql = "INSERT INTO
								controle_falhas_acessorio
												   (
														cfaimoboid, 
														cfaimotoid, 
														cfaifdoid, 
														cfaifaoid, 
														cfaifcoid, 
														cfadt_cadastro, 
														cfadt_entrada, 
														cfaimopbhoid
													)
								VALUES
													(
														".$parametros->imoboid.",
														".$parametros->tipo.",
														".$parametros->defeito_lab.",
														".$parametros->acao_lab.",
														".$parametros->componente_lab.",
														NOW(),
														'".$parametros->dataEntradaLab."',
														".$parametros->imobhoid."			
													)
								RETURNING cfaoid";
		}
		
		if ($query = pg_query($sql)) {
			if (pg_affected_rows($query) > 0) {
				$reg = pg_fetch_object($query, "cfaoid");
				return $reg->cfaoid;
			}
		} 
		return false;
	}
	
	/**
	 * Busca todos os registros que possuem o mesmo Imoboid e Data de Entrada Lab
	 * @param int $imoboid
	 * @param string $dtEntradaLab
	 * @return array:stdClass
	 */
	public function buscarRegistrosSerialEntradaLab($imoboid, $dtEntradaLab) {
		$sql = "SELECT 
						cfaoid
				FROM
						controle_falhas_acessorio
				WHERE
						cfaimoboid = ".$imoboid."
				AND
						cfadt_entrada = '".$dtEntradaLab."' ";
		$resultado = array();
		if ($query = pg_query($sql)) {
			if (pg_num_rows($query) > 0) {
				while ($registro = pg_fetch_object($query)) {
					array_push($resultado, $registro);
				}
			}
		}
		return $resultado;
	}
	
	/**
	 * Método para editar registros
	 * @param stdClass $parametros
	 * @return boolean
	 */
	public function editarRegistro($parametros) {
		$sql = "UPDATE
						controle_falhas_acessorio
				SET
						ifdoid = ".$parametros->defeito_lab.",
						ifaoid = ".$parametros->acao_lab.",
						ifcoid = ".$parametros->componente_lab."
				WHERE
						cfaoid = ".$parametros->cfaoid;
		if ($query = pg_query($sql)) {
			if (pg_affected_rows($query) > 0) {
				return true;
			}
		} 
		return false;
	}
	
	/**
	 * Método para excluir registros
	 * @param int $cfaoid
	 * @return boolean
	 */
	public function excluirRegistro($cfaoid) {
		$sql = "UPDATE
						controle_falhas_acessorio
				SET
						cfadt_exclusao = NOW()
				WHERE
						cfaoid = ".intval($cfaoid);
		
		if ($query = pg_query($sql)) {
			if (pg_affected_rows($query) > 0) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Método que busca o numero de registros com o mesmo imoboid e defeito informados
	 * @param stdClass $parametros
	 * @return int
	 */
	public function buscarDefeitosImoboid($parametros) {
		$sql = "SELECT
						cfaifdoid, COUNT(1) AS resultado
				FROM
						controle_falhas_acessorio
				WHERE
						cfaimoboid = ".$parametros->imoboid."
				AND
						cfadt_exclusao IS NULL
				GROUP BY
						cfaifdoid";
		if ($query = pg_query($sql)) {
			if (pg_num_rows($query) > 0) {
				$registro = pg_fetch_object($query);
				return $registro->resultado;
			}
		}
		return 0;
	}
	
	/**
	 * Busca os defeitos
	 * @param int $tipo
	 * @return array:stdClass
	 */
	public function buscarDefeitosLab($tipo) {
		$sql = "SELECT
						*
				FROM
						item_falha_defeito
				WHERE
					ifddt_exclusao is null
				AND
					ifdimotoid = ".$tipo;
		$resultado=array();
		if ($query = pg_query($sql)) {
			if (pg_num_rows($query) > 0) {
				while($row=pg_fetch_object($query)) {
					array_push($resultado, $row);
				}
			}
		}
		return $resultado;
	}
	
	/**
	 * Busca as ações
	 * @param int $tipo
	 * @return array:stdClass
	 */
	public function buscarAcoesLab($tipo) {
		$sql = "SELECT
						*
				FROM
						item_falha_acao
				WHERE
					ifadt_exclusao is null
				AND
					ifaimotoid = ".$tipo;
		$resultado=array();
		if ($query = pg_query($sql)) {
			if (pg_num_rows($query) > 0) {
				while($row=pg_fetch_object($query)) {
					array_push($resultado, $row);
				}
			}
		}
		return $resultado;
	}
	
	/**
	 * Busca os componentes
	 * @param int $tipo
	 * @return array:stdClass
	 */
	public function buscarComponentesLabs($tipo) {
		$sql = "SELECT
						*
				FROM
						item_falha_componente
				WHERE
					ifcdt_exclusao is null
				AND
					ifcimotoid = ".$tipo;
		$resultado=array();
		if ($query = pg_query($sql)) {
			if (pg_num_rows($query) > 0) {
				while($row=pg_fetch_object($query)) {
					array_push($resultado, $row);
				}
			}
		}
		return $resultado;
	}
	
}

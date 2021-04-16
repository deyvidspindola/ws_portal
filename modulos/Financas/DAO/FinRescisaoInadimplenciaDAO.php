<?php

/**
 * Classe de persistência de dados
*
* @author Willian Menegali <willian.menegali@meta.com.br>
*
*/
class FinRescisaoInadimplenciaDAO {

	private $conn;
	private $usuario;

	/**
	 * Metodo Construtor
	 * @param $conn
	 */
	function __construct($conn) {

		$this->conn = $conn;
		
		$this->usuario = Sistema::getUsuarioLogado();
		
	}

	/**
	 * Metodo BEGIN para transações de persistência de dados
	 */
	public function begin() {
		pg_query($this->conn, "BEGIN;");
	}

	/**
	 * Metodo COMMIT para transações de persistência de dados
	 */
	public function commit() {
		pg_query($this->conn, "COMMIT");
	}

	/**
	 * Metodo ROLLBACK para transações de persistência de dados
	 */
	public function rollback() {
		pg_query($this->conn, "ROLLBACK;");
	}
	
	/**
	 * Verifica se o contrato existe
	 * @param $connumero
	 * @return boolean $row->existe (0 or 1)
	 */
	public function verificarExistenciaContrato($connumero) {
		$sql = "SELECT COUNT(1) AS existe FROM contrato WHERE connumero = $connumero";
		
		ob_start();
        $query = pg_query($sql);
        ob_end_clean();
        
		if ($query) {
			$row = pg_fetch_object($query);
			return $row->existe;
		} else {
			return 0;
        }
        
        
	}
	
	/**
	 * Consulta veiculo/equipamento do contrato 
	 * @param $connumero
	 * @return boolean $row->existe (0 or 1)
	 */
	public function verificarExistenciaEquipamento($connumero) {
		$sql = "SELECT 
					connumero, equoid, concsioid, veioid 	 
				FROM  contrato 
				INNER JOIN 
					veiculo ON veioid = conveioid AND veidt_exclusao IS NULL
				LEFT JOIN
					equipamento ON equoid = conequoid AND equdt_exclusao IS NULL
				WHERE
					connumero = ".intval($connumero);
	
		$query = pg_query($sql);
	
		if (pg_num_rows($query)>0) {
			return pg_fetch_object($query);
		} else {
			return false;
		}
	}
	
	/**
	 * Verifica o status do contrato
	 * @param $connumero
	 * @return boolean $row->existe (0 or 1)
	 */
	public function verificarStatusContrato($connumero) {
		$sql = "SELECT COUNT(1) as existe FROM contrato WHERE concsioid = 38 AND connumero = $connumero";
		
		ob_start();
        $query = pg_query($sql);
        ob_end_clean();
        
		if ($query) {
			$row = pg_fetch_object($query);
			return $row->existe;
		}
	}
	
	/**
	 * Verifica o tipo do contrato
	 * @param $connumero
	 * @return object $query
	 */
	public function verificarTipoContrato($connumero) {
		$sql = "SELECT COUNT(1) AS descartar, tpcdescricao
				FROM contrato
				INNER JOIN tipo_contrato ON tpcoid = conno_tipo
				WHERE   connumero = $connumero 
				AND	(                                    
                       OR	tipo_contrato.tpcdescricao ILIKE 'Ex-%'                                
                     )
				GROUP BY tpcdescricao";
		
		ob_start();
        $query = pg_query($sql);
        ob_end_clean();
        
		if ($query) {
			return pg_fetch_object($query);		
		} else 
			return 0;
	}
	
	/**
	 * Altera a situação do contrato para Rescisão por Inadimplênia conforme regra de negocio
	 * @param $contrato
	 * @return boolean
	 */
	public function alterarSituacaoContrato($contrato) {
		
		$usuario = (int) $this->usuario->cd_usuario; 
		
		$sql = "UPDATE contrato 
				SET concsioid = 38, 
					conusualteracaooid = $usuario, 
					conambiente = TRUE
				WHERE connumero = $contrato";
		
		ob_start();
        $query = pg_query($sql);
        ob_end_clean();
        
		if ($query) {
			if (pg_affected_rows($query) > 0)
				return true;
			else
				return false;
		} else {
			return false;
		}
	}
	
	/**
	 * Verifica se há registros na tabela ordem_servico_item
	 * @param int $ordoid
	 * @return boolean
	 */
	private function verificarExistenciaItemOs($ordoid){
		
		$ordoid = (int)$ordoid;
		$retorno = false;
		
		if(empty($ordoid)){
			return $retorno;
		}
		
		$sql = "
				SELECT 
						COUNT(ositordoid) AS total
				FROM
						ordem_servico_item
				WHERE 
						ositstatus <> 'X' 
				AND 
						ositeqcoid IS NOT NULL
				AND 
						ositordoid = ". $ordoid ."
				
				";
			
		$rs = pg_query($sql);
		$row = pg_fetch_object($rs);		
		
		$retorno = isset($row->total) ? $row->total : 0;
		
		return (boolean)$retorno;
		
	}
	
	/**
	 * Cancela os itens e as OS do contrato informado.
	 * RN 7.11, 7.12, 7.13
	 * @param integer $contrato
	 * @return 1 - Erro ao cancelar OS, 2 - Erro ao Cancelar Itens, 3 - Sucesso, 4 - Erro ao inserir histórico, 5 - Erro ao cancelar agendamentos
	 */
	public function cancelarOS($contrato) {
		
		$usuario = (int) $this->usuario->cd_usuario;
		
		$sql = "SELECT ordoid FROM ordem_servico
				INNER JOIN ordem_servico_item ON ositordoid = ordoid
				INNER JOIN os_tipo_item ON otioid = ositotioid
				WHERE ordconnumero = $contrato 
				AND ordstatus NOT IN (3,9)
				AND otioid <> 8";
		ob_start();
        $query = pg_query($sql);
        ob_end_clean();
        
        
       /**
        * Evitar erro na trigger ordem_servico_item_webservice_t (AND ositeqcoid is not null )
        **/
		if ($query) {
			if (pg_num_rows($query) > 0) {
				while ($row = pg_fetch_object($query)) {
					
					if($this->verificarExistenciaItemOs($row->ordoid)){					
						
						$sqlCancelarItens = "UPDATE 
												ordem_servico_item 
											 SET
												 ositstatus = 'X' 
											 WHERE 
												ositstatus <> 'X' 
												AND ositeqcoid is not null 
												AND ositordoid = ".$row->ordoid;
	                    ob_start();
	                    $queryCancelarItens = pg_query($sqlCancelarItens);
	                    ob_end_clean();
	
	                    if ($queryCancelarItens) {
							if (!(pg_affected_rows($queryCancelarItens) > 0))
								return 2;
						}
						
					}						
						
					$sqlCancelar = "UPDATE ordem_servico
							SET ordstatus = 9
							WHERE ordoid = ".$row->ordoid;
                    ob_start();
                    $queryCancelar = pg_query($sqlCancelar);
                    ob_end_clean();

                    if ($queryCancelar) {
						if (!(pg_affected_rows($queryCancelar) > 0))
								return 1;
					}
					
					$qtdAgendas = $this->verificarAgendamentosOS($os);
					
					if($qtdAgendas > 0) {
					
						if (!$this->cancelarAgendamentosOS($row->ordoid, $usuario))
							return 5;
					}
					
					//Inserir Histórico
					$sqlHistorico = "INSERT INTO ordem_situacao
									 (orsordoid, orssituacao, orsusuoid, orsstatus) VALUES 
									($row->ordoid, 'Cancelada por motivo de rescisão por inadimplência.', ".$usuario.", 9)";
                    ob_start();
                    $queryHistorico = pg_query($sqlHistorico);
                    ob_end_clean();

                    if ($queryHistorico) {
						if (!(pg_affected_rows($queryHistorico) > 0))
							return 4;
					}

				}
				//Fim While
			}
			return 3;
		} else {
			return 1;
		}
	}
	
	/**
	 * Verificar agendamento da OS
	 * @param $os, $usuario
	 * @return boolean
	 */
	public function verificarAgendamentosOS($os) {
	
		$sql = "SELECT
					osaordoid	
				 FROM ordem_servico_agenda			
				WHERE 
					osaordoid = $os
					and osaexclusao is null";
	
	
		if ($query) {
			return pg_fetch_object($query);
		} else
			return 0;
		}
	

	
	/**
	 * Cancela o agendamento da OS
	 * @param $os, $usuario
	 * @return boolean
	 */
	public function cancelarAgendamentosOS($os, $usuario) {
		
		$sql = "UPDATE ordem_servico_agenda 
				SET osaexclusao = NOW(), 
					osausuoid_excl = $usuario, 
					osamotivo_excl = 'OS cancelada por motivo de rescisão por inadimplência.'
				WHERE osaordoid = $os";
		
		
		ob_start();
	
		
		
        $query = pg_query($sql);
        ob_end_clean();
        
		if ($query) {
			return true;
		} else {
			return false;	
		}	
	}
	
	/**
	 * RN 7.15, 7.16
	 * @param $contrato
	 * @return 1 - Não existe OS de retirada, 2 - Erro, 3 - Sucesso
	 */
	public function tratarOSRetirada($contrato) {
		$sql = "SELECT ordoid FROM ordem_servico
				INNER JOIN ordem_servico_item ON ositordoid = ordoid
				INNER JOIN os_tipo_item ON otioid = ositotioid
				WHERE ordconnumero = $contrato 
				AND ordstatus NOT IN (3, 9)
				AND otioid = 8";
		ob_start();
        $query = pg_query($sql);
        ob_end_clean();
        
		if ($query) {
			if (pg_num_rows($query) > 0) {
				while ($os = pg_fetch_object($query)) {
					$sql = "UPDATE ordem_servico 
							SET orddesc_problema = 'Rescisão por Inadimplência'
							WHERE ordoid = ".$os->ordoid;
                    
					ob_start();
                    $query = pg_query($sql);
                    ob_end_clean();

                    if ($query) {
						if (pg_affected_rows($query) > 0)
						return 3;	
					}
				}
			} else
				return 1;
		} else
			return 2;
	}
	
	
	/**
	 * RN 7.15, 7.16
	 * @param $contrato
	 * @return int - numrows
	 */
	public function verificarExistenciaOSRetiradaConcluida($contrato) {
		$sql = "SELECT 
					ordoid 
				FROM 
					ordem_servico
				INNER JOIN 
					ordem_servico_item ON ositordoid = ordoid
				INNER JOIN 
					os_tipo_item ON otioid = ositotioid
				WHERE 
					ordconnumero = $contrato
				AND 
					ordstatus = 3
				AND 
					otioid = 8";
		
		$query = pg_query($sql);

		if ($query) {
			return pg_num_rows($query);				
		}
		
		return 0;
	}
	
	public function cancelarQualquerOS($contrato) {
		$sql = "UPDATE
					ordem_servico
				SET
					ordstatus = 9
				WHERE 
					ordconnumero = $contrato";
		
		return pg_affected_rows(pg_query($sql));
		
	}
	
	
	
	/**
	 * Verifica se o equipamento_classe do contrato não é SASMOBILE
	 * @param integer $contrato
	 * @return boolean $retorno->res
	 */
	public function verificarTipoContratoSASMOBILE($contrato) {
		$sql = "SELECT 
					COUNT(1) AS res
				FROM 
					contrato
				INNER JOIN 
					equipamento_classe ON coneqcoid = eqcoid
				WHERE 
					eqcdescricao NOT LIKE 'SASMOBILE%'
				AND
					connumero = $contrato";
		ob_start();
        $query = pg_query($sql);
        ob_end_clean();
        
		if ($query) {
			$retorno = pg_fetch_object($query);
			return $retorno->res;
		}
	}
	
	/**
	 * Gera ordem de serviço de retirada
	 * @param $contrato
	 * @return 1 - Não Gerou OS Retirada, 2 - Não gerou histórico, 3 - Não inseriu serviço na OS
	 */
	public function gerarOSRetirada($contrato) {
		
		$usuario = (int) $this->usuario->cd_usuario;
		
		$sql = "INSERT INTO ordem_servico
				(ordveioid, ordclioid, ordequoid, ordeveoid,ordstatus, 
				ordmtioid, orddesc_problema, ordusuoid, ordconnumero, ordrelroid)
				SELECT conveioid, conclioid, conequoid, equeveoid, 4, 
				5, 'Rescisão por Inadimplência', ".$usuario.", connumero, 752
				FROM contrato
				INNER JOIN equipamento ON conequoid = equoid 
				WHERE connumero = $contrato
				returning ordoid";
		
        $query = pg_query($sql);
        
		if ($query) {
			if (pg_num_rows($query) > 0) {
				$row = pg_fetch_row($query); // Recupera ID da OS inserida
				$idOS = $row[0];
				
				$sql = "INSERT INTO ordem_situacao
						(orsordoid, orssituacao, orsusuoid, orsstatus)
						VALUES
						($idOS, 'Retirada por motivo de rescisão por inadimplência.', $usuario, 4)";
                
				ob_start();
                $query = pg_query($sql);
                ob_end_clean();

                if ($query) {
					if (pg_affected_rows($query) > 0) {
						$sql = "             
							INSERT INTO
								ordem_servico_item (
                                ositotioid,
                                ositordoid,
                                ositeqcoid,
                                ositobs,
                                ositstatus
                            ) VALUES (
                            	8,
                                $idOS,
                                (SELECT coneqcoid FROM contrato WHERE connumero = $contrato LIMIT 1),
                                'Retirada por motivo de rescisão por inadimplência',
                                'P'
                           )";
                        
                        ob_start();
                        $query = pg_query($sql);
                        ob_end_clean();

                        if (!$query) {
							return 3;
						}
						
						//Serviço de RETIRADA ACESSÓRIOS
						$sql= "SELECT 
									otioid, 
									otidescricao
								FROM
									contrato_servico, 
									os_tipo_item
								WHERE
									--consrefioid>0 --Mantis nº7062 - Autorizado pelo Thadeu Rocha
								otidt_exclusao IS NULL
								AND  otiobroid=consobroid
								AND  otiostoid=3
								AND  otitipo = 'A'
								AND  consiexclusao IS NULL
								AND  consconoid=$contrato;";
						
						$rs = pg_query($sql);						
						if(pg_num_rows($rs) > 0){
							
							for ($i=0;pg_num_rows($rs) > $i; $i++){

								$servico = pg_fetch_result($rs, $i, 'otioid');
								$descricao_servico = pg_fetch_result($rs, $i, 'otidescricao');
									
								$sql = "INSERT INTO 
											ordem_servico_item
											(
												ositotioid,
												ositordoid,
												ositeqcoid,
												ositobs,
												ositstatus
											)
											(SELECT 
												$servico,
												$idOS,
												(SELECT coneqcoid FROM contrato WHERE connumero = $contrato LIMIT 1),
												'Retirada de Acessório ($descricao_servico) por motivo de rescisão por inadimplência',
												'P'
											);";
								
								ob_start();
								$query = pg_query($sql);
								ob_end_clean();
								if (!$query) {
									return 3;
								}
							}
						}
					}
				} else {
					return 2;					
				}
				
			} else
				return 1;
		} else
			return 1;
	}
	
}
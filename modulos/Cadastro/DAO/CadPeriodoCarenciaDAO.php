<?php
/**
 * Willian Menegali
 * Classe de persistência de dados
 * 27/03/2013
 */
class CadPeriodoCarenciaDAO {

	private $conn;

	function __construct($conn) {

		$this->conn = $conn;
	}

	public function begin() {
		pg_query($this->conn, "BEGIN;");
	}

	public function commit() {
		pg_query($this->conn, "COMMIT");
	}

	public function rollback() {
		pg_query($this->conn, "ROLLBACK;");
	}
	
	function buscarUltimoReativacaoCobranca() {
		$sql = "SELECT pcrdt_vigencia, pcrperiodo 
				FROM periodo_carencia_reinstalacao 
				WHERE pcrdt_exclusao IS NULL";
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception();
		} else if (pg_num_rows($res) > 0) { 
			return $res;
		} 
		return false;
	}
	
	function buscarNomeUsuario($codigo) {
		$sql = "SELECT nm_usuario FROM usuarios WHERE cd_usuario = '$codigo'";
		$query = pg_query($this->conn, $sql);
		$row = pg_fetch_row($query);
		return $row[0];
	}
	
	function salvarReativacaoCobranca($periodo, $vigencia, $usuario) {
		//Recupera ID do último registro inserido na tabela
		$sql1 = "SELECT MAX(pcroid) FROM periodo_carencia_reinstalacao";
		if ($r = pg_query($this->conn, $sql1)) {
			$max = pg_fetch_row($r);
			//verifica se há um registro inserido
			if ($max[0] != null && $max[0] != "") :
				$sqlExc = "UPDATE periodo_carencia_reinstalacao
						SET pcrdt_exclusao = NOW()
						WHERE pcroid = $max[0]";
				//Exclui último registro
				$excluir = pg_query($this->conn, $sqlExc);
			endif;
			
			//Busca os dados do último registro
			$row = pg_fetch_row($this->buscarUltimoReativacaoCobrancaRow());
			$row[3] = date('d/m/y', strtotime($row[3]));
			//Verifica se os dados que estão sendo inseridos não são os mesmos do último registro
			if (($row[3] != $vigencia) || ($row[4] != $periodo)) {
				$sqlSalvar = "INSERT INTO periodo_carencia_reinstalacao
					(pcrdt_cadastro, pcrusuoid, pcrdt_vigencia, pcrperiodo)
					VALUES	(NOW(), '$usuario', '$vigencia', '$periodo')";
					$query = pg_query($this->conn, $sqlSalvar);
					if (!$query) {
						return false;
					}
			}
                        
                        $sql = "delete from reativacao_cobranca_monitoramento";
                        $query = pg_query($this->conn, $sql);
			if (!$query) {
                            return false;
                        }
			
			$sql = "INSERT INTO reativacao_cobranca_monitoramento (rcmconoid, rcmordoid, rcmorddt_conclusao)
					SELECT DISTINCT
						connumero, 
						ordoid,
						(SELECT MAX(orsdt_situacao) 
						FROM ordem_situacao 
						WHERE orsordoid = os.ordoid)::date AS data_conclusao_os
						
					FROM contrato con
					INNER JOIN ordem_servico os ON connumero = ordconnumero
					INNER JOIN ordem_servico_item osi ON (osi.ositordoid = os.ordoid AND osi.ositotioid = 11) -- os de retirada de equipamento para troca de veículo

					WHERE os.ordstatus = 3 -- os concluída

					AND NOT EXISTS (SELECT 1
									FROM ordem_servico
									WHERE ordconnumero = con.connumero
									AND orddt_ordem > os.orddt_ordem
									AND ordstatus = 3) -- que não possua os concluída posteriormente

					AND (	SELECT MAX(orsdt_situacao) 
							FROM ordem_situacao 
							WHERE orsordoid = os.ordoid)::date > (	SELECT pcrdt_vigencia
																	FROM periodo_carencia_reinstalacao 
																	WHERE pcrdt_exclusao IS NULL)::date -- posterior a data de início de vigência";
			
			$query = pg_query($this->conn, $sql);
			if ($query) {
				return true;
			} else {
				return false;
			}
			
		} else {
			return false;
		}		
	}
	
	function buscarUltimoReativacaoCobrancaRow() {
		$sql = "SELECT * FROM periodo_carencia_reinstalacao ORDER BY pcrdt_cadastro DESC LIMIT 1";
		$query = pg_query($this->conn, $sql);
		if (!$query) {
			return false;
		} else {
			return $query;
		}
	}
	
	function buscarHistoricoReativacaoCobranca() {
		$sql = "SELECT * FROM periodo_carencia_reinstalacao ORDER BY pcrdt_cadastro DESC";
		$query = pg_query($this->conn, $sql);
		if (!$query) {
			return false;
		} else {
			return $query;
		}
	}
	
	function buscarModelosEmail() {
		$sql = "SELECT * FROM email_carencia_reinstalacao WHERE ecrdt_exclusao IS NULL";
		$query = pg_query($this->conn, $sql);
		if (!$query) {
			return false;
		} else {
			return $query;
		}
	}
	
	function salvarModeloEmail($carencia, $assunto, $modelo, $usuario) {
		$res = $this->buscarModeloCarencia($carencia);
		$sql = "";
		if (pg_num_rows($res) > 0) {
			$row = pg_fetch_row($res);
			$sql = "UPDATE email_carencia_reinstalacao set ecrdt_alteracao = NOW(), ecrusuoid = '$usuario', ecrfim_carencia = '$carencia', 
															ecrassunto = '$assunto', ecrmensagem = '$modelo'
					WHERE ecroid = '$row[0]'";
		} else {		
			$sql = "INSERT INTO email_carencia_reinstalacao 
					(ecrdt_alteracao, ecrusuoid, ecrfim_carencia, ecrassunto, ecrmensagem)
					VALUES
					(NOW(), '$usuario', '$carencia', '$assunto', '$modelo')";
		}
		$query = pg_query($this->conn, $sql);
		if (!$query) {
			return false;
		} else {
			return $query;
		}
	}
	
	function buscarModeloCarencia($carencia) {
		$sql = "SELECT * FROM email_carencia_reinstalacao WHERE ecrfim_carencia = '$carencia' AND ecrdt_exclusao IS NULL";
		$query = pg_query($this->conn, $sql);
		if (!$query) {
			return false;
		} else {
			return $query;
		}
	}
	
	function verificarEmailsPorLimitePeriodo($periodo) {
		$sql = "SELECT ecroid FROM email_carencia_reinstalacao WHERE ecrfim_carencia > $periodo AND ecrdt_exclusao IS NULL";
		$ids = "";
		$query = pg_query($this->conn, $sql);
		if (!$query) {
			return false;
		} else {
			while ($id = pg_fetch_array($query)) {
				$ids .= $id[0].",";
			}
			return $ids;
		}
		
	}
	
	function excluirModeloEmail($id) {
		$usuario = Sistema::getUsuarioLogado();
		$sql = "UPDATE email_carencia_reinstalacao
				SET ecrdt_exclusao = NOW(), ecrusuoid_exclusao = '$usuario->cd_usuario'  
				WHERE ecroid = '$id'";
		$query = pg_query($this->conn, $sql);
		if (!$query) {
			return false;
		}
		return $query;
	}
}
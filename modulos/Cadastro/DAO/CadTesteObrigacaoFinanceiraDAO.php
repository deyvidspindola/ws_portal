<?php

	/**
	 * @author	Felipe F. de Souza Carvalho
	 * @email	fscarvalho@brq.com
	 * @since	14/01/2013
	 **/

	require_once 'lib/config.php';
	require_once 'lib/init.php';

	class CadTesteObrigacaoFinDAO {

		public function listarTestesCadastrados(){
			
			global $conn;
			
			try {
				
				$testesCadastrados = array();
				
				$query = " SELECT epttoid AS oid, epttdescricao AS desc
						   FROM equipamento_projeto_tipo_teste_planejado 
						   WHERE epttdt_exclusao IS NULL
						   ORDER BY epttdescricao;";
				
				$result = pg_query($conn, $query);
				
				if($result) {
					while(($row = pg_fetch_assoc($result)) != false){
						
						//Trata caracteres não utf8 da descrição ...
						$row['desc'] = utf8_encode($row['desc']);
						
						//Adiciona a linha no array de retorno
						array_push($testesCadastrados, $row);
					}
				}
				
			} catch (Exception $e) {
				throw new Exception('Não foi possível recuperar os testes cadastrados.');
			}
						
			return $testesCadastrados;
		}
		
		
		public function listarObrigacoesCadastradas(){
				
			global $conn;
				
			try {
		
				$obrigCadastradas = array();
		
				$query = " SELECT obroid AS oid, formata_str(obrobrigacao) AS desc
    					   FROM obrigacao_financeira
					       WHERE obrdt_exclusao IS NULL
					       AND (obroftoid NOT IN(1,6) OR obroftoid IS NULL)
					       ORDER BY obrobrigacao;";
		
				$result = pg_query($conn, $query);
		
				if($result) {
					while(($row = pg_fetch_assoc($result)) != false){
		
						//Trata caracteres não utf8 da descrição ...
						$row['desc'] = utf8_encode($row['desc']);
		
						//Adiciona a linha no array de retorno
						array_push($obrigCadastradas, $row);
					}
				}
		
			} catch (Exception $e) {
				throw new Exception('Não foi possível recuperar as obrigações cadastradas.');
			}
		
			return $obrigCadastradas;
		}
		
		
		public function pesquisar($idTeste, $idObrigacao){
			
			global $conn;
			
			try {
				
				$testesObrigacoes = array();
				
				$query = " SELECT eptotoid, epttoid AS oid_teste, epttdescricao AS desc_teste, 
						   obroid AS oid_obrig, formata_str(obrobrigacao) AS desc_obrig
						   FROM equipamento_projeto_tipo_obrigacao_testes 
						   INNER JOIN equipamento_projeto_tipo_teste_planejado  ON (epttoid = eptotepttoid)
						   INNER JOIN obrigacao_financeira ON (obroid = eptotobroid)
						   WHERE eptotdt_exclusao IS NULL";
				
				//ID do teste (se informado)
				if(!empty($idTeste)){
					$query .= " AND epttoid = $idTeste";
				}
				
				//ID da obrigação (se informada)
				if(!empty($idObrigacao)){
					$query .= " AND obroid = $idObrigacao";
				}

				$query .= " ORDER BY desc_teste, desc_obrig;";
			
				$result = pg_query($conn, $query);
				
				if($result) {
					while(($row = pg_fetch_assoc($result)) != false){
												
						//Trata caracteres não utf8 das colunas de descrição ...
						$row['desc_teste'] = utf8_encode($row['desc_teste']);
						$row['desc_obrig'] = utf8_encode($row['desc_obrig']);
						
						//Adiciona a linha no array de retorno
						array_push($testesObrigacoes, $row);
					}
				}
				
			} catch (Exception $e) {
				throw new Exception('Erro ao realizar a pesquisa');
			}
			
			return $testesObrigacoes;
		}
		
		public function salvar($idTeste, $idObrigacao, $idUsuario){
			
			global $conn;

			if(!empty($idTeste) && !empty($idObrigacao) && !empty($idUsuario)){
					
				/*
				 * Realiza a verificação se a obrigação já não está
				* vinculada ao teste
				*/
				$sqlVerificaItem = " SELECT COUNT(*) AS registros
				FROM equipamento_projeto_tipo_obrigacao_testes
				WHERE eptotepttoid = $idTeste
				AND eptotobroid = $idObrigacao
				AND eptotdt_exclusao IS NULL;";
					
				$resVerificaItem = pg_query($conn, $sqlVerificaItem);
					
				if($resVerificaItem){
			
					$numRegistros = pg_fetch_result($resVerificaItem, 0, 'registros');
			
					if($numRegistros > 0){
						throw new Exception('A obrigação informada já está vinculada ao teste');
					}
			
					pg_free_result($resVerificaItem);
				}
			}
		
			try {
				
				$eptotoid = 0;
				
				/*
				 * Insere vinculo obrigação x teste
				 */
				$query = " INSERT INTO equipamento_projeto_tipo_obrigacao_testes
						   (eptotepttoid, eptotobroid, eptotdt_cadastro, eptotusuoid_cadastro)
						   VALUES($idTeste, $idObrigacao, NOW(), $idUsuario) RETURNING eptotoid";
		
				$result = pg_query($conn, $query);
		
				if($result) {
					if(pg_affected_rows($result) > 0){
						$eptotoid = pg_fetch_result($result, 0, 'eptotoid');
					}
				}
				
			} catch(Exception $e) {
				throw new Exception('Erro ao gravar o registro');
			}
			
			return $eptotoid;
		}
		
		
		public function excluirObrigacaoTeste($eptotoid, $idUsuario){
		
			global $conn;
		
			try {
				if(!empty($eptotoid) && !empty($idUsuario)){		
					
					$query = " UPDATE equipamento_projeto_tipo_obrigacao_testes
							   SET eptotdt_exclusao = NOW(), eptotusuoid_exclusao = $idUsuario
							   WHERE eptotoid = $eptotoid;";
			
					$result = pg_query($conn, $query);
			
					if($result) {
						if(pg_affected_rows($result) > 0){
							return true;
						}
					}
				}
			} catch (Exception $e) {
				throw new Exception('Erro ao excluir o registro');
			}
		
			return false;
		}
		
	}

?>

<?php
/**
 * Classe para recuperar e persistir dados de débito automático
 * 
 * @file PrnDaDO.php
 * @author marcioferreira
 * @version 06/03/2013 16:57:08
 * @since 06/03/2013 16:57:08
 * @package SASCAR PrnDaDO.php 
 */

class PrnDaDO{
	
	private $conn;
	
	/**
	 * Construtor
	 *
	 * @autor Márcio Sampaio Ferreira
	 * @email marcioferreira@brq.com
	 */
	public function __construct($conn) {
	
		$this->conn = $conn;
	}
	
	
	/**
	 * Método que insere o historico de débito automático
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function inserirHistoricoDebAutomatico($historicoDa, $dadosConfirma) {
		
		try{
			// Dados  anteriores
			$forma_cobranca_anterior = (!empty($historicoDa->forma_cobranca_anterior)) ? $historicoDa->forma_cobranca_anterior : 'null';
			$banco_anterior = (!empty($historicoDa->banco_anterior)) ? $historicoDa->banco_anterior : 'null';
			$agencia_anterior = (!empty($historicoDa->agencia_anterior)) ? "'" . $historicoDa->agencia_anterior . "'" : 'null';
			$conta_corrente_anterior = (!empty($historicoDa->conta_corrente_anterior)) ? "'" . $historicoDa->conta_corrente_anterior . "'" : 'null';
	
			// Dados posteriores
			$banco_posterior = (!empty($historicoDa->banco_posterior)) ? $historicoDa->banco_posterior : 'null';
			$agencia_posterior = (!empty($historicoDa->agencia_posterior)) ? $historicoDa->agencia_posterior : '';
			$conta_corrente_posterior = (!empty($historicoDa->conta_corrente_posterior)) ? $historicoDa->conta_corrente_posterior : '';
	
			$sql = "INSERT INTO
						historico_debito_automatico
							( hdaclioid,
							hdausuoid_cadastro,
							hdamsdaoid,
							hdaprotocolo,
							hdadt_cadastro,
							hdaentrada,
							hdatipo_operacao,
							hdaforcoid_posterior,
							hdabanoid_posterior,
							hdaagencia_posterior,
							hdacc_posterior,
							hdaforcoid_anterior,
							hdabanoid_anterior,
							hdaagencia_anterior,
							hdacc_anterior )
						VALUES (
							{$dadosConfirma->id_cliente},
							{$dadosConfirma->id_usuario},
							{$historicoDa->motivoAlteraDebito},
							{$dadosConfirma->protocolo},
							NOW(),
							'{$dadosConfirma->entrada}',
							UPPER('{$historicoDa->tipo_operacao}'),
							{$dadosConfirma->forma_cobranca_posterior},
							$banco_posterior,
							'$agencia_posterior',
							'$conta_corrente_posterior',
							$forma_cobranca_anterior,
							$banco_anterior,
							$agencia_anterior,
							$conta_corrente_anterior ) ";
														
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao inserir histórico de débito automático.</b>');
			}

			return true;
			
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Insere historico do contrato
	 * Através da funcao existente no banco historico_termo_i
	 * @params
	 * 		connumero integer
	 * 		usuoid integer
	 * 		obs text
	 * 		protocolo text
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function inserirHistoricoContrato($params){
		
		try{
			$sql = "SELECT 	historico_termo_i(
								{$params->numero_contrato},
								{$params->id_usuario},
								'{$params->texto_alteracao}',
								'{$params->protocolo}'
							); ";
				
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao inserir histórico de contrato.</b>');
			}

			return true;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	

	/**
	 * Insere historico do contrato
	 * @params
	 * 		prphprpoid integer -- Id da proposta
	 * 		prphusuoid integer -- Usuário que gravou o histórico
	 * 		prphobs text - Observação
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function inserirHistoricoProposta($params){

		try{
			$sql ="INSERT INTO proposta_historico
								( prphprpoid,
								prphusuoid,
								prphobs )
								VALUES (
								{$params['id_proposta']},
								{$params['id_usuario']},
								'{$params['texto_alteracao']}' ) ";
				
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao inserir histórico da proposta.</b>');
			}

			return true;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	

	/**
	 * Busca os contratos ativos do cliente
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function getContratosAtivosByCliente($id_cliente) {
	
		try {
			$sql =" SELECT connumero
					FROM contrato
					WHERE concsioid = 1
					AND	condt_exclusao IS NULL
					AND	conclioid = $id_cliente";
			 
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao recuperar contratos ativos do cliente.</b>');
			}
		
			return (pg_num_rows($rs) > 0) ? pg_fetch_all($rs) : array();
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}

	/**
	 * Busca o texto do termo para envio de email
	 * através da descricao (gctdescricao)
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function getModeloTexto($descricao) {
	
		try{
			$sql = "SELECT gcttexto as texto_mensagem
					FROM gerador_contrato_texto
					WHERE gctdescricao = '$descricao' ";
			 
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao recuperar modelo texto do termo de débito automático.</b>');
			}
		
			return pg_fetch_object($rs);
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	* Método que busca e popula o combo de motivos de troca de débito automático
	* para outra forma de pagamento
	*
	* @autor Willian Ouchi
	*/
	public function getDadosMotivos() {
	
		try{
			$sql =" SELECT msdaoid, msdadescricao
		            FROM motivo_susp_debito_automatico
		            WHERE msdadt_exclusao IS NULL ";
		
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao recuperar motivos de troca de débito automático.</b>');
			}
		
			$resultado = array();
		
			$cont = 0;
			while ($rmotivos = pg_fetch_assoc($rs)) {
		
				$resultado[$cont]['msdaoid'] = $rmotivos['msdaoid'];
				$resultado[$cont]['msdadescricao'] = utf8_encode($rmotivos['msdadescricao']);
				$cont++;
			}
		
			return $resultado;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
}
//fim arquivo
?>
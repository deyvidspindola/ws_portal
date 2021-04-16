<?php
/**
 * Classe que mantêm somente dados do cliente
 * 
 * @file PrnCliente.php
 * @author marcioferreira
 * @version 06/03/2013 16:56:27
 * @since 06/03/2013 16:56:27
 * @package SASCAR PrnCliente.php 
 */

// require para persistência de dados - classe DAO
require _MODULEDIR_ . 'Principal/DAO/PrnClienteDAO.php';

require_once _MODULEDIR_ . 'Principal/Action/PrnDadosCobranca.php';

class PrnCliente{
	
	/**
	 * Atributo para acesso a persistência de dados
	 */
	private $dao;
	private $conn;
	
	
	/**
	 * Construtor
	 * @autor Márcio Sampaio Ferreira
	 * @email marcioferreira@brq.com
	 */
	public function __construct(){
	
		global $conn;
	
		$this->conn = $conn;
	
		// Objeto  - DAO
		$this->dao = new PrnClienteDAO($conn);
	}
	
	
	/**
	 * Atualiza dados do cliente
	 * Email, Email_nfe e forma de cobranca
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function atualizarCliente($dadosConfirma) {

		try{
			
			$campos_update_cliente = array("cliformacobranca = $dadosConfirma->forma_cobranca_posterior ");

			 // Usuario alteração
			 // Caso exista id do usuario que está alterando o cliente, inserimos seu id
			 // caso não exista inserimos o id do usuário automático 2750

			if(!empty($dadosConfirma->id_usuario)) {
				array_push($campos_update_cliente, "cliusuoid_alteracao = ".$dadosConfirma->id_usuario." ");
			} else {
				array_push($campos_update_cliente, "cliusuoid_alteracao = 2750 ");
			}

			$clienteemail = (!empty($dadosConfirma->email)) ? "'".$dadosConfirma->email."'" : 'null';

			array_push($campos_update_cliente, "cliemail = $clienteemail");

			$clienteemail_nfe =  (!empty($dadosConfirma->emailNfe)) ? "'".$dadosConfirma->emailNfe."'" : 'null';

			array_push($campos_update_cliente, "cliemail_nfe = $clienteemail_nfe");

			$campos = implode(', ', $campos_update_cliente);
			
			if(!$this->dao->atualizarCliente($dadosConfirma->id_cliente, $campos)){
				throw new Exception('ERRO: Falha ao atualizar cliente.');
			}
			
			return true;
			
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	* Insere o histórico do cliente através da function do banco de dados cliente_historico_i
	*
	* @autor Renato Teixeira Bueno
	* @email renato.bueno@meta.com.br
	*/
	public function inserirHistoricoCliente($historicoCliente, $dadosConfirma){

		try{

		 	// Tipo da ação executada (A = Alteração cadastral)
			$tipo_acao = 'A';

			$id_atendimento = 'null';

			//instância da classe dados de cobrança
			$prnDadosCobranca = new PrnDadosCobranca();

			// Busca dados da forma de cobranca posterior
			$dados_posteriores = $prnDadosCobranca->getDadosFormaCobranca($dadosConfirma->forma_cobranca_posterior);
			$descricao_forma_cobranca_posterior = $dados_posteriores->descricao_forma_cobranca;

			// Se houver protocolo informado insere no histórico do cliente
			$protocolo_cliente = "";
			if(!empty($dadosConfirma->protocolo)){
				$protocolo_cliente = "N° Protocolo: $dadosConfirma->protocolo;";
			}

			// Texto para inserir no historico do cliente de: para:
			$texto_alteracao = "Alteração: forma de cobrança de: $historicoCliente->descricao_forma_cobranca_anterior para: $descricao_forma_cobranca_posterior ";
			$texto_alteracao .= "banco de: $historicoCliente->nome_banco_anterior para: $historicoCliente->nome_banco_posterior ";
			$texto_alteracao .= "agência de: $historicoCliente->agencia_anterior para:  $historicoCliente->agencia_posterior ";
			$texto_alteracao .= "conta corrente de: $historicoCliente->conta_corrente_anterior para: $historicoCliente->conta_corrente_posterior ";
			$texto_alteracao .= "$protocolo_cliente ";

			$dadosHistorico->texto_alteracao = $texto_alteracao;
			$dadosHistorico->tipo            = $tipo_acao;
			$dadosHistorico->id_atendimento  = $id_atendimento;

			if(!$this->dao->inserirHistoricoCliente($dadosHistorico, $dadosConfirma)){
				throw new Exception('ERRO: Falha ao atualizar cliente.');
			}
			
			return true;

		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	/**
	 * Retorna email do cliente
	 *
	 * @autor Márcio Sampaio Ferreira
	 * @email marcioferreira@brq.com
	 */
	public function getEmailCliente($id_cliente){

		return $this->dao->getEmailCliente($id_cliente);

	}

	/**
	 * Pesquisa os clientes de acordo com os parametros informados
	 * @return	Array com os itens filtrados pela pesquisa
	 * */
	public function getClientes($clinome,$clitipo, $clioid, $clino_documento ) {

		return $this->dao->getClientes($clinome, $clitipo, $clioid, $clino_documento);

	}
	
	/**
	 * Pesquisa dados do cliente escolhido na lista de pesquisa
	 * 
	 * @return	Array com os dados do cliente
	 * */
	public function getDadosCliente($clioid) {

		return $this->dao->getDadosCliente($clioid);

	}
	
	
	/**
	 * Verifica se existe algum contrato ativo do cliente informado
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function contratoAtivoCliente($id_cliente) {

		return $this->dao->contratoAtivoCliente($id_cliente);

	}

}
//fim arquivo
?>
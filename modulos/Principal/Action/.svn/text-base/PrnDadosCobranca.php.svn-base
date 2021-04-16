<?php
/**
 * Classe que gerencia dados de conbrança do cliente
 * 
 * @file PrnDadosCobranca.php
 * @author marcioferreira
 * @version 05/03/2013 09:06:04
 * @since 05/03/2013 09:06:04
 * @package SASCAR PrnDadosCobranca.php 
 */

// require para persistência de dados - classe DAO
require _MODULEDIR_ . 'Principal/DAO/PrnDadosCobrancaDAO.php';

class PrnDadosCobranca {
	
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
		$this->dao = new PrnDadosCobrancaDAO($conn);
	}
		
	/**
	 * Atualiza somente os dados do endereço que foram informados para atualizar
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 * 
	 * @param stdClass $dadosConfirma
	 * $dadosConfirma->id_cliente
	 * $dadosConfirma->numero 		
	 * $dadosConfirma->cep 		
	 * $dadosConfirma->complemento 
	 * $dadosConfirma->logradouro 	
	 * $dadosConfirma->pais		
	 * $dadosConfirma->estado		
	 * $dadosConfirma->cidade		
	 * $dadosConfirma->bairro		
	 * $dadosConfirma->emailCobranca
	 * $dadosConfirma->uf
	 * $dadosConfirma->endddd		
	 * $dadosConfirma->telefone 	
	 * $dadosConfirma->endfone_array[]
	 * 
	 * 
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	public function atualizarEnderecoCobranca($dadosConfirma) {

		try{		
	
			// Monta um array com os campos que serão atualizados na tabela endereço
			$campos_update_endereco = array();
	
			$cep = (!empty($dadosConfirma->cep)) ? (int) preg_replace('/[^\d]/', '', $dadosConfirma->cep) : 'null';
			array_push($campos_update_endereco, "endno_cep = $cep");
			
			array_push($campos_update_endereco, "endcep = $cep");
	
			$pais = (!empty($dadosConfirma->pais)) ?(int) preg_replace('/[^\d]/', '', $dadosConfirma->pais) : 'null';
			array_push($campos_update_endereco, "endpaisoid = $pais");
			
			$estado = (!empty($dadosConfirma->estado)) ? (int) preg_replace('/[^\d]/', '', $dadosConfirma->estado) : 'null';
			array_push($campos_update_endereco, "endestoid = $estado");
			
			$email = (!empty($dadosConfirma->emailCobranca)) ? "'".$dadosConfirma->emailCobranca."'" : 'null';
			array_push($campos_update_endereco, "endemail = $email");
			
			if ($dadosConfirma->pais && $dadosConfirma->estado){
				$siglaArray  = $this->dao->getDadosEstados($dadosConfirma->pais, $dadosConfirma->estado);
				$sigla = $siglaArray[0]['estuf'];
			}else{
				$sigla = $dadosConfirma->uf;
			}
				
			$sigla_estado = (!empty($sigla)) ? "'".$sigla."'" : 'null';
			array_push($campos_update_endereco, "enduf = $sigla_estado");
			
			$endereco_cidade = (!empty($dadosConfirma->cidade)) ? "'".utf8_decode($dadosConfirma->cidade)."'" : 'null';
			array_push($campos_update_endereco, "endcidade = $endereco_cidade");
	
			$endereco_bairro = (!empty($dadosConfirma->bairro)) ? "'".utf8_decode($dadosConfirma->bairro)."'" : 'null';
			array_push($campos_update_endereco, "endbairro = $endereco_bairro");
	
			$endereco_logradouro = (!empty($dadosConfirma->logradouro)) ? "'".utf8_decode($dadosConfirma->logradouro)."'" : 'null';
			array_push($campos_update_endereco, "endlogradouro =$endereco_logradouro");
	
			$numero =  (!empty($dadosConfirma->numero)) ? (int) preg_replace('/[^\d]/', '', $dadosConfirma->numero) : 'null';
			array_push($campos_update_endereco, "endno_numero = $numero");
	
			$endereco_complemento = (!empty($dadosConfirma->complemento)) ? "'".$dadosConfirma->complemento."'" : 'null';
			array_push($campos_update_endereco, "endcomplemento = $endereco_complemento");
	
			$ddd = (!empty($dadosConfirma->telefoneDdd)) ? (int) preg_replace('/[^\d]/', '', $dadosConfirma->telefoneDdd) : 'null';
			array_push($campos_update_endereco, "endddd = $ddd");
	
			$endereco_telefone = (!empty($dadosConfirma->telefone)) ? "'".preg_replace("([^0-9])","",$dadosConfirma->telefone)."'" : 'null';	
			array_push($campos_update_endereco, "endfone = $endereco_telefone");
				
			if(! empty($dadosConfirma->endfone_array)){
				foreach ($dadosConfirma->endfone_array as $key => $value) {
					$i = $key+1;
					array_push($campos_update_endereco, "endfone_array[$i] = $value");
				}
			}
			
			// Trata o array para enviar para a classe DAO
			$campos = (!empty($campos_update_endereco)) ? implode(', ', $campos_update_endereco) : '';
			
			if(!$this->dao->atualizarEnderecoCobranca($dadosConfirma->id_cliente, $campos)){
				throw new Exception('ERRO: Falha ao atualizar endereco de cobranca.');
			}
				
			return true;
		
		}catch (Exception $e){
			return $e->getMessage();	
		}
	}
	
	public function getEnderecoCobrancaPorCliente($id_cliente){
		return $this->dao->getEnderecoCobrancaPorCliente($id_cliente);
	}
	
	/**
	 * 
	 * @see Prepara os campos e valores para Inserir na
	 * tabela de Endereços de Cobrança
	 * 
	 * @example $prnDadosCobranca->inserirEnderecoCobranca($dados)
	 * 
	 * @param stdClass $dados
	 * O Array deve conter as seguintes chaves abaixo
	 * $dados->id_cliente
	 * $dados->numero
	 * $dados->cep
	 * $dados->complemento
	 * $dados->logradouro
	 * $dados->pais
	 * $dados->estado
	 * $dados->cidade
	 * $dados->bairro
	 * $dados->emailCobranca
	 * $dados->uf
	 * $dados->endddd
	 * $dados->telefone
	 * $dados->endfone_array[]
	 * 
	 * @return boolean
	 */
	public function inserirEnderecoCobranca($dados){
		
		$campos = "";
		$values = "";
		
		if(isset($dados->numero) && $dados->numero !== ""){
			$campos .= "endno_numero";
			$values .= "'$dados->numero'";
		}
		
		if(isset($dados->cep) && $dados->cep !== ""){
			/** A tabela endereço tem dois campos de CEP */
			$campos .= ", endno_cep";
			$values .= ", '$dados->cep'";
			
			
			$campos .= ", endcep";
			$values .= ", '$dados->cep'";
		}
		
		if(isset($dados->complemento) && $dados->complemento !== ""){
			$campos .= ", endcomplemento";
			$values .= ", '$dados->complemento'";
		}
		
		if(isset($dados->logradouro) && $dados->logradouro !== ""){
			$campos .= ", endlogradouro";
			$values .= ", '$dados->logradouro'";
		}
		
		if(isset($dados->cidade) && $dados->cidade !== ""){
			$campos .= ", endcidade";
			$values .= ", '$dados->cidade'";
		}
		
		if(isset($dados->bairro) && $dados->bairro !== ""){
			$campos .= ", endbairro";
			$values .= ", '$dados->bairro'";
		}
		
		if(isset($dados->emailCobranca) && $dados->emailCobranca !== ""){
			$campos .= ", endemail";
			$values .= ", '$dados->emailCobranca'";
		}
		
		if(isset($dados->pais) && $dados->pais !== ""){
			$campos .= ", endpaisoid";
			$values .= ", '$dados->pais'";
		}
				
		if(isset($dados->uf) && $dados->uf !== ""){
			$campos .= ", enduf";
			$values .= ", '$dados->uf'";
		}
		
		if(isset($dados->estado) && $dados->estado !== ""){
			$campos .= ", endestoid";
			$values .= ", '$dados->estado'";
			
			/** Encontrar a Sigla UF do Estado */
			if(isset($dados->uf)== false || $dados->uf !== ""){
				$siglaArray  = $this->dao->getDadosEstados($dados->pais, $dados->estado);
				$sigla = $siglaArray[0]['estuf'];
				
				$campos .= ", enduf";
				$values .= ", '$sigla'";
			}
		}
		
		if(isset($dados->telefoneDdd) && $dados->telefoneDdd !== ""){
			$campos .= ", endddd";
			$values .= ", '$dados->telefoneDdd'";
		}
		
		if(isset($dados->telefone) && $dados->telefone !== ""){
			$campos .= ", endfone";
			$values .= ", '$dados->telefone'";
		}
		
		if(! empty($dados->endfone_array)){
			foreach ($dados->endfone_array as $key => $value) {
				$i = $key+1;
				$campos .= ", endfone_array[$i]";
				$values .= ", '$value'";
			}
		}
						
		return $this->dao->inserirEnderecoCobranca($dados->id_cliente, $campos, $values);
	}
	
	/**
	* Retorna os dados da forma de cobrança do cliente
	* @autor Márcio Sampaio Ferreira
	* @email marcioferreira@brq.com
	* 
	* @param $id_cliente
	* 
	* @return objeto
	*/
	public function getFormaCobrancaAnterior($id_cliente){

		return $this->dao->getFormaCobrancaAnterior($id_cliente);

	}
	
	/**
	 * Retorna bancos por forma de cobrança
	 * @autor Márcio Sampaio Ferreira
	 * @email marcioferreira@brq.com
	 *
	 * @param $forma_cobranca_posterior
	 *
	 * @return objeto
	 */
	public function getBancoPorFormaCobranca($forcoid) {
			
		return $this->dao->getBancoPorFormaCobranca($forcoid);

	}
	
	/**
	 * Retorna dados da forma de cobrança informada
	 * @autor Márcio Sampaio Ferreira
	 * @email marcioferreira@brq.com
	 *
	 * @param $forma_cobranca_posterior
	 *
	 * @return objeto
	 */
	public function getDadosFormaCobranca($pforma_cobranca = null, $pid_proposta = null) {

		return $this->dao->getDadosFormaCobranca($pforma_cobranca, $pid_proposta );

	}
	
	/**
	 * Retorna a forma de cobrança atual, se houver, do cliente selecionado
	 * @return array $formaPagamentoAtual
	 */
	public function getFormaCobrancaAtual($clioid){

		return $this->dao->getFormaCobrancaAtual($clioid);

	}
	
	/**
	 * Retorna as formas de pagamento disponíveis
	 * @return Ambigous <multitype:, string>
	 */
	public function getFormaCobranca(){

		return $this->dao->getFormaCobranca();

	}
	
	/**
	 * @author	Willian Ouchi
	 * @email	willian.ouchi@meta.com.br
	 * @return	String json com o endereço
	 * */
	public function getEnderecoCep($cep) {

		$cep = (isset($cep)) ? $cep : null;

		$informacoes = array();
		$informacoes['dadosEndereco'] = $this->dao->getDadosEndereco($cep);
		$informacoes['estados'] = $this->dao->getDadosEstados(1);

		return $informacoes;
	}
	
	
	/**
	 * Método que busca e popula a combo de países
	 *
	 * @autor Willian Ouchi
	 */
	public function getDadosPaises() {
		
		return $this->dao->getDadosPaises();
		
	}
	
	/**
	 * @author	Willian Ouchi
	 * @email	willian.ouchi@meta.com.br
	 * @return	String json com a listagem de estados
	 * */
	public function listarEstados($pais) {

		$pais = (isset($pais)) ? $pais : null;

		$informacoes = array();
		$informacoes['ufs'] = $this->dao->getDadosEstados($pais);

		return $informacoes;
	}
	
	/**
	 * @author	Willian Ouchi
	 * @email	willian.ouchi@meta.com.br
	 * @return	String json com a listagem de cidades
	 * */
	public function listarCidades($estado) {

		$estado = (isset($estado)) ? $estado : null;
		$informacoes = array();
		$informacoes['listaCidades'] = $this->dao->getDadosCidades($estado);

		return $informacoes;
	}
	
	
	/**
	 * @author	Willian Ouchi
	 * @email	willian.ouchi@meta.com.br
	 * @return	String json com a listagem de Bairros
	 * */
	public function listarBairros($estado, $cidade) {

		$estado = (isset($estado)) ? $estado : null;
		$cidade = (isset($cidade)) ? $cidade : null;
		$informacoes = array();
		$informacoes['listaBairros'] = $this->dao->getDadosBairros($estado, $cidade);

		return $informacoes;
	}
	
	/**
	 * Retorna os dias disponíveis para pagamento
	 * @return array - Array de datas
	 */
	public function getDiaCobranca($exibeDataVencimento = null, $codDiaVencimento = null, $dataVencimento = null){

		return $this->dao->getDiaCobranca($exibeDataVencimento, $codDiaVencimento, $dataVencimento);

	}
	
	
	/**
	 * Retorna a data de cobrança atual do cliente
	 * @return object
	 */
	public function getDataCobrancaCliente($clioid){
		
		return $this->dao->getDataCobrancaCliente($clioid);
		
	}
	
	
	/**
	* Atualiza dados da cobrança relacionada ao cliente
	* OBS: Deve ser o ultimo método a ser chamado para garantir que os historicos sejam inseridos corretamentos
	* com as informações de antes e depois (de: para:)
	*
	* @autor Renato Teixeira Bueno
	* @email renato.bueno@meta.com.br
	*/
	public function atualizarCobranca($dadosConfirma){

		try{
			// Trata a variável $agencia e $conta_corrente para não enviar para o banco com traço ("-")
			// Ex: conta corrente: 32758-1 => 327581
			// agencia: 3218-3 => 32183
	
			$agencia = ($dadosConfirma->debitoAgencia) ? substr(preg_replace('/[^\d]/', '', $dadosConfirma->debitoAgencia), 0, 4) : 'null';
			$conta_corrente = ($dadosConfirma->debitoConta) ? preg_replace('/[^\d]/', '', $dadosConfirma->debitoConta) : 'null' ;
			
			if(!$this->dao->atualizarCobranca($dadosConfirma, $agencia, $conta_corrente)){
				throw new Exception('ERRO: Falha ao atualizar cobranca.');
			}
			
			return true;
			
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Retorna dados da forma cobrança cliente Cargo Tracck
	 */
	public function getDadosClienteCargoTracck($dados){
		
		return $this->dao->getDadosClienteCargoTracck($dados);
		
	}
	
}
//fim arquivo
?>
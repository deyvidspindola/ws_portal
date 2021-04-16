<?php

header('Content-Type: text/html; charset=ISO-8859-1');

/**
 * Classe de persistência de dados
*/
require (_MODULEDIR_ . "Financas/DAO/FinRescisaoInadimplenciaDAO.php");


/**
 * FinRescisaoInadimplencia.php
 *
 * @author Willian Menegali <willian.menegali@meta.com.br>
 * @package Finanças
 * @since 10/05/2013
 *
 */
class FinRescisaoInadimplencia {
	
	private $dao;
	private $arquivoImportado = null;
	private $path_file;
	private $upload;
	private $relatorio;
	
	/**
	 * Construtor
	 */
	public function __construct() {
	
		global $conn;
		$this->path_file = '/var/www/docs_temporario/fin_rescisao_inadimplencia.csv';
		$this->dao = new FinRescisaoInadimplenciaDAO($conn);
		$this->upload = array();
		$this->relatorio = array (
					'total' => array(),
					'naoLocalizados' => array(),
					'naoAlterados' => array(),
					'alterados' => array(),
					'retiradaGeradas' => array(),
					'canceladas' => array()
				);
	}
	
	/**
	 * Metodo principal
	 * Chama a view principal do modulo
	 */
	public function index() {
		
		include(_MODULEDIR_ . 'Financas/View/fin_rescisao_inadimplencia/rescisao_inadimplencia.php');
	}
	
	/**
	 * Metodo chamado pelo click do botao
	 * Importa o arquivo e chama a view responsável pela apresentação de resultados da importação
	 * @author Renato Teixeira Bueno
	 */
	public function importarArquivo(){
	
		try{
			
			if ($_POST) {
					
				$this->importarCSV($_FILES['arquivo_csv']);
			}
						
			$this->msg = array(
				'tipo' => 'sucesso',
				'mensagem' => 'Arquivo processado com sucesso.'
			);

			$this->index();
				
			require _MODULEDIR_ . 'Financas/View/fin_rescisao_inadimplencia/relatorio_importacao.php';
		
		} catch(Exception $e){
				
			$this->msg = array(
					'tipo' => 'erro',
					'mensagem' => $e->getMessage()
			);
		
			$this->index();
	
		}
	
	}
	
	/**
	 * Metodo que valida o CSV, importa os dados e aplica as regras aos contratos
	 * @param $csv
	 * @author Willian Menegali
	 */
	public function importarCSV($csv){
		
		/*
		 * Se a váriavel csv for um array então $_FILES
		* retornou um arquivo.
		*/
		if ( is_array( $csv ) ){
			//
			/*
			 * Verifica se o arquivo é do tipo CSV.
			*/
			if ($this->buscaExtensao($csv['name']) != 'CSV'){
				throw new Exception('Formato do arquivo inválido.');
			}
	
			/*
			 * Faz o upload do arquivo para a pasta definida
			* no contrutor da classe.
			*/
			$this->upload = $this->uploadAnexo($csv);
			if ($this->upload['erro'] == true){
				throw new Exception($this->upload['mensagem']);
			}
	
			/*
			 * Lê o arquivo e retonar um array com os valores
			* do csv.
			*/
			$contratos = $this->CSVtoArray();
			if ($contratos['erro'] == true){
				throw new Exception($contratos['mensagem']);
			}

			$this->tratarContratos($contratos['linhas']);
			
		}
		
	}
	
	/**
	 * Retorna a extensão de um arquivo.
	 * @parameter string file_name
	 */
	public function buscaExtensao($nome_arquivo){
	
		$extensao = explode('.', $nome_arquivo);
	
		return strtoupper(array_pop($extensao));
	}
	
	/**
	 * Move o arquivo importado para o destino especificado em  $this->path_file
	 * @author Willian Menegali
	 */
	public function uploadAnexo($csv) {
	
		$enviado = move_uploaded_file($csv['tmp_name'], $this->path_file);

		if(!$enviado) {
			
			return array(
				'erro'      => true,
				'mensagem'  => 'Houve um erro no upload do arquivo!'
			);
		}

		return array(
				'erro'      => false,
				'mensagem'  => ''
		);
	
	}
	
	/**
	 * Transforma o CSV importado em array para validações
	 */
	public function CSVtoArray(){
	
		$linha = array();
		$linhas = array();
		
		$leitura = fopen($this->path_file, 'r');

		if (!$leitura){
			return array(
					'erro'     => true,
					'mensagem' => 'Erro na atualização dos registros verifique o arquivo!'
			);
			
		}
		
		while ($linha = fgets($leitura, 2048)){
			$linha = trim($linha);
			if (strpos($linha, ',') > 0 || strpos($linha, ';') > 0 || !preg_match("/[0-9]/", $linha) ) {
					return array(
							'erro'     => true,
							'mensagem' => 'Conteúdo do arquivo inválido.'
					);
				}
			$linhas[] = $linha;
		}
		if (!count($linhas)) {
			return array(
					'erro'     => true,
					'mensagem' => 'Conteúdo do arquivo inválido.'
			);
		}

		return array(
				'erro'      => false,
				'mensagem'  => '',
				'linhas'    => $linhas
		);

	}
	
	/**
	 * Faz o tratamento dos contrato de acordo com as regras de negocio
	 * @param $contratos
	 */
	public function tratarContratos($contratos) {
		
		foreach ($contratos as $contrato) {
			
			$this->relatorio['total'][] = $contrato;
			
			//Verifica se contrato existe.
			if ($this->dao->verificarExistenciaContrato($contrato)) {
				if (!$this->dao->verificarStatusContrato($contrato)) {
					//Verifica por tipo de contrato.
					$tipo = $this->dao->verificarTipoContrato($contrato);
					if ($tipo->descartar == 1) {
						$this->relatorio['naoAlterados'][] = array(
															'contrato' => $contrato,
															'motivo' => "Contrato do tipo " . $tipo->tpcdescricao);	
					} else {
						$this->aplicarAlteracoesContrato($contrato);
					}
				} else {
					$this->relatorio['naoAlterados'][] = array(
							'contrato' => $contrato,
							'motivo' => "Contrato já rescindido por inadimplência.");
				}
			} else {
				$this->relatorio['naoLocalizados'][] = $contrato;
			}
		}
	}
	
	/**
	 * Após validação esse metodo aplica as alterações nos contrato e nas ordens de serviço relacionadas aos contratos
	 * @param $contrato
	 */
	public function aplicarAlteracoesContrato($contrato) {
		$motivo = "";
		$motivoContratoAlterado = "";
		
		$existeOsRetirada = $this->dao->verificarExistenciaOSRetiradaConcluida($contrato);
		$equipamento = $this->dao->verificarExistenciaEquipamento($contrato);
		
		$this->dao->begin();
		//RN 7.10 - Altera situação do contrato, caso algo dê errado, ir direto para o rollback
		if (!$this->dao->alterarSituacaoContrato($contrato)) {
			$motivo = "Erro ao alterar situação do contrato.";
		} else {
			//RN 7.11, 7.12, 7.13
			$res =$this->dao->cancelarOS($contrato);
			if ($res != 3) { //3 é sucesso, se não for 3, é porque algo deu errado.
				switch ($res) {
					case 2:
						$motivo = "Erro ao cancelar itens/ serviços.";
						break;
					case 4:
						$motivo = "Erro ao inserir histórico da ordem de serviço cancelada.";
						break;
					case 5:
						$motivo = "Erro ao cancelar agendamento.";
						break;
					default:
						$motivo = "Erro ao cancelar ordem de serviço.";
						break;
				}
			}else { // Sucesso em todos os processos, continuar..
				$this->relatorio['canceladas'][] = $contrato; // RN 7.14 Adiciona aos quadro Ordens de Serviço Canceladas
				
				//Busca e trata OS de Retirada do contrato
				$osRetirada = $this->dao->tratarOSRetirada($contrato);
				
				//há OS de retirada
				if ($osRetirada == 1) { 
					//veiculo possui equipamento, gera os retirada.
					if($equipamento!==false && !empty($equipamento->equoid)) { 
						//gera os de retirada se equipamento nao foi roubado.
						if($equipamento->concsioid!=7){
					$retorno = $this->gerarOSRetirada($contrato);
					if ($retorno == 1) {
						$motivo = "Erro ao gerar ordem de serviço de retirada.";
					} else if ($retorno == 2) {
						$motivo = "Erro ao gerar histórico da ordem de serviço.";
					} else if ($retorno == 3) {
						$motivo = "Erro ao inserir serviço na ordem de serviço.";
					} else if ($retorno == 4) {
						$motivo = "";
					} else {
						$this->relatorio['retiradaGeradas'][] = $contrato;
					}
						}
					}
					else if(!in_array((int)$equipamento->concsioid, array(1, 7))) {											
						$this->dao->cancelarQualquerOS($contrato);
					}
				} else if ($osRetirada == 2) { // ocorreu algum erro, descartar
					$motivo = "Erro ao alterar motivo da ordem de serviço de retirada.";
				}
								
				if($existeOsRetirada && $equipamento!==false && $equipamento->concsioid==1 && empty($equipamento->equoid)){					
					$motivoContratoAlterado='Status alterado, O.S não criada, termo já consta com O.S de retirada';
			}
		}
		}

		if ($motivo != "") {
			//quando algo dá errado
			$this->dao->rollback();
			$this->relatorio['naoAlterados'][] = array(
					'contrato' => $contrato,
					'motivo' => $motivo
					);
			return;
		} else {
			$this->relatorio['alterados'][] =  array(
					'contrato' => $contrato,
					'motivo' => $motivoContratoAlterado
				);
			$this->dao->commit();
			return;
		}
	}
	
	/**
	 * Metodo para gerar ordem de serviço de retirada
	 * @param $contrato
	 */
	public function gerarOSRetirada($contrato) {
		$tipo = $this->dao->verificarTipoContratoSASMOBILE($contrato);
		if ($tipo == "1") {
			$retorno = $this->dao->gerarOSRetirada($contrato);
			return $retorno;
		} else {
			return 4;
		}
	}
	
}
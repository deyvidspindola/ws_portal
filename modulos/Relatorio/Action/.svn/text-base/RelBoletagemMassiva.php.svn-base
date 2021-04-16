<?php
/**
 * @file RelBotetagemMassiva.php
 * @author marcio.ferreira
 * @version 01/09/2015 10:12:59
 * @since 01/09/2015 10:12:59
 * @package SASCAR RelBotetagemMassiva.php 
 */

ini_set('display_errors', 0 );

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/rel_boletagem_massiva_'.date('d-m-Y').'.txt');


//manipula os dados no BD
require(_MODULEDIR_ . "Relatorio/DAO/RelBoletagemMassivaDAO.php");


// classe para gerar arquivo csv
include_once 'lib/Components/CsvWriter.php';


class RelBoletagemMassiva{

	private $id_usuario;
	
	private $pasta_arquivo;
	
	private $nome_arquivo_csv;
	
	private $path_arquivo;

	/**
	 * Construtor, configura acesso a dados e parâmetros iniciais do módulo
	 */
	public function __construct()  {

		global $conn;

		$this->dao  = new RelBoletagemMassivaDAO($conn);
		$this->id_usuario = $_SESSION['id_usuario'];
		
		$this->pasta_arquivo = '/var/www/docs_temporario/';

	}

	
	/**
	 *
	 *
	 * @param string $param
	 */
	public function index($param=NULL, $tipo= NULL){
	
	
		if($param['tipo'] == 'erro'){
			//$tipo = $param['tipo'];
			$msg = $param['msg'];
			$classe = 'mensagem erro';
			
		}else{
			
			if($tipo == 'pesquisa'){
				$titulos = $param;
			}
			
			$arquivo_csv = $this->path_arquivo;
			$nomeArquivo = $this->nome_arquivo_csv;
		}
	
		include (_MODULEDIR_ . 'Relatorio/View/rel_boletagem_massiva/index.php');
	}
	
	

	/**
	 * Retorna um  array com os dados de uma pesquisa filtrando pelos dados recebidos do post
	 *
	 * @return Ambigous <multitype:, boolean, multitype:>|boolean
	 */
	public function pesquisar(){

		try {
		
			$pesquisa = new stdClass();
					
			$pesquisa->nome_campanha   = trim(isset($_POST['nome_campanha']) && $_POST['nome_campanha'] != '' ? $_POST['nome_campanha'] : '');
			$pesquisa->data_ini        = isset($_POST['data_ini']) && $_POST['data_ini'] != '' ? $_POST['data_ini'] : 'NULL';
			$pesquisa->data_fim        = isset($_POST['data_fim']) && $_POST['data_fim'] != '' ? $_POST['data_fim'] : 'NULL';
			$pesquisa->data_vencimento = isset($_POST['data_vencimento']) && $_POST['data_vencimento'] != '' ? $_POST['data_vencimento'] : '';
			$pesquisa->apenas_pagos    = isset($_POST['apenas_pagos']) && $_POST['apenas_pagos'] != '' ? $_POST['apenas_pagos'] : 0;
				
			if ($pesquisa->data_ini == 'NULL' ) {
				throw new Exception ( 'A data inicial deve ser informada.' );
			}
				
			if ($pesquisa->data_fim == 'NULL' ) {
				throw new Exception ( 'A data final deve ser informada.' );
			}
				
			$titulos = $this->dao->getTitulosUnificados($pesquisa);
			
			
			if(is_array($titulos)){
			
				$this->nome_arquivo_csv = "Relatorio_boletagem_massiva_".date("d-m-Y").".csv";
				
				//nome do arquivo csv
				$this->path_arquivo = $this->pasta_arquivo.$this->nome_arquivo_csv;
				
				if (is_dir ( $this->pasta_arquivo )) {
						
					// Gera CSV
					$csvWriter = new CsvWriter( $this->path_arquivo, ';', '', true);
						
					//Gera o cabeçalho
					$cabecalho = array(
							"Nome da Campanha",
							"Nome do Cliente",
							"Aging da Divida",
							"% Desconto",
							"Tipo de Pessoa",
							"Tipo de Cliente",
							"UF",
							"Valor da Divida Unificada",
							"Vencimento do Boleto",
							"Data de Pagamento",
							"Forma de Envio"
					);
						
					$csvWriter->addLine( $cabecalho );
						
				} else {
					throw new Exception ( 'Diretório -->  '.$this->pasta_arquivo.' não existe.');
				}
				
				$arquivo = file_exists($this->path_arquivo);
				
				if ($arquivo === false) {
					throw new Exception("O arquivo --> ".$this->path_arquivo." não pode ser gerado.");
				}
				
				
				foreach ($titulos AS $dados_titulo){
	
					//gerar .csv
					$linha_csv = array(
							$dados_titulo['nome_campanha'],
							$dados_titulo['clinome'],
							$dados_titulo['aging_divida'],
							$dados_titulo['desconto'],
							$dados_titulo['tipo_pessoa'],
							$dados_titulo['tipo_cliente'],
							$dados_titulo['uf'],
							$dados_titulo['valor_divida'],
							$dados_titulo['data_vencimento'],
							$dados_titulo['data_pagamento'],
							$dados_titulo['forma_envio']
					);
					
					//adiciona a linha ao arquivo
					$csvWriter->addLine($linha_csv);
				}
				
				//fecha o arquivo .csv
				$csvWriter->closeFile();
			
			}
			
			$this->index($titulos, 'pesquisa');
			

		} catch (Exception $e) {
			
			
			$erro['msg']  =  $e->getMessage();
			$erro['tipo'] =  'erro';
				
			$this->index($erro, 'pesquisa');
			
		}
	}

	
}	

?>
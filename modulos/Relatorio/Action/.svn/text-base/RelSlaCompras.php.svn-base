<?php

require _MODULEDIR_ . 'Relatorio/DAO/RelSlaComprasDAO.php';
require _SITEDIR_ . 'lib/Components/Data.php';

class RelSlaCompras {
	
	private $dao;
	private $dados;
	private $caminhoArquivo;
	private $mensagem;
	private $visualizacao;
	private $frmPesquisa;
    private $arquivoGerado = FALSE;
	
	/**
	 * Método Construtor
	 */
	public function __construct() {
		global $conn;
		$this->dao = new RelSlaComprasDAO($conn);
		$this->caminhoArquivo = '/var/www/docs_temporario/rel_sla_compras.csv';
		$this->dados = false;
		$this->mensagem = "";
		$this->visualizacao = "T";
	}
	
	/**
	 * Index
	 * Método para exibição da página de início.
	 */
	public function index() {		
		
		$dataObj = new stdClass();
		$dataObj->valorDataInicial	= isset($_POST['data_inicio']) 	? $_POST['data_inicio'] 	: "";
		$dataObj->valorDataFinal 	= isset($_POST['data_fim']) 	? $_POST['data_fim']		: "";
		
		$componenteData = Data::getComponente();
		$htmlPeriodo = $componenteData->exibirCamposPeriodo('data_inicio', 'data_fim', $dataObj);
		
		require _MODULEDIR_ . 'Relatorio/View/rel_sla_compras/index.php';
	}
	
	/**
	 * Pesquisar
	 * Método para execução da pesquisa.
	 */
	public function pesquisar() {
		
		$this->frmPesquisa = new stdClass();
		$this->frmPesquisa->data_inicio	= isset($_POST['data_inicio']) 	? $_POST['data_inicio'] 	: "";
		$this->frmPesquisa->data_fim 		= isset($_POST['data_fim']) 	? $_POST['data_fim']		: "";
		$this->frmPesquisa->visualizacao	= isset($_POST['visualizacao'])	? $_POST['visualizacao'] 	: "";
		$this->frmPesquisa->rms		 	= isset($_POST['rms']) 	 		? $_POST['rms'] 			:  0;
		$this->frmPesquisa->cotacao	 	= isset($_POST['cotacao'])		? $_POST['cotacao']			:  0;

		if (empty($this->frmPesquisa->rms)) {
			$this->frmPesquisa->rms = 0;
		}
		if (empty($this->frmPesquisa->cotacao)) {
			$this->frmPesquisa->cotacao = 0;
		}
		
		$this->visualizacao = $this->frmPesquisa->visualizacao;
		try {
			$this->dados = $this->dao->buscarCotacoesPorPeriodo($this->frmPesquisa);
            if ($this->visualizacao == "C") {

                $this->gerarCsv();
            }
		} catch (Exception $e) {
			$this->mensagem = "Houve um erro ao realizar a pesquisa.";
		}		
		

		
		$this->index();
	}
	
	/**
	 * Gerar CSV
	 * Método para geração do arquivo CSV
	 */
	public function gerarCsv() {
		
		$csv = "Cotação; RMS; Data de Abertura RMS; Data de Autorização RMS; Data Cotação; Dias; Comprador; Situação \n";
		$this->dados = (array)$this->dados;
        if (count($this->dados) > 0){
            foreach ($this->dados as $linha) {
                $status = ($linha->media_por_cotacao > 9) ? "Em Atraso" : "No Prazo";
                $csv .= $linha->cotoid . "; " . $linha->reqmoid . "; " . $linha->reqmcadastro . "; " . $linha->rmapdt_aprovacao
                                . "; " . $linha->cotcadastro . "; " . round($linha->media_por_cotacao) . "; " . $linha->nm_usuario . ";" . $status . "\n";
            }

            if ($arquivo = fopen($this->caminhoArquivo, 'w')) {
                fwrite($arquivo, $csv);
                fclose($arquivo);
                $this->arquivoGerado = TRUE;
            }
        }
		
		return;
	}
}
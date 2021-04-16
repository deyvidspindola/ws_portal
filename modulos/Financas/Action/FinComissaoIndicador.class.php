<?php
/**
* @author	Gabriel Luiz Pereira
* @email	gabriel.pereira@meta.com.br
* @since	05/09/2012
* */
require 'modulos/Financas/DAO/FinComissaoIndicadorDAO.class.php';
require 'modulos/Financas/DAO/FinParametrosComissaoIndicadorDAO.class.php';

/**
 * Trata requisições do módulo de calculo de comissão
 * @author Gabriel Luiz Pereira
 */
class FinComissaoIndicador {
	
	/**
	 * Fornece acesso aos dados necessarios para o módulo
	 * @property FinComissaoIndicadorDAO
	 */
	private $dao;
	
	/**
	 * Fornece acesso aos dados necessarios para o módulo
	 * @property FinParametrosComissaoIndicadorDAO
	 */
	private $daoParametros;
	
	private $dataInicial;
	private $dataFinal;
	private $indicadorNegocio;
	private $numeroNf;
	private $serieNf;
	private $cotrato;
	private $statusComissao;
	private $somenteComissionaveis;
	private $comissionavel;
	private $notasPendentes;
	private $notasGeradas;
	private $notasPendentesEGeradas;
	private $notasPagas;
	private $coindt_pagamento;
	private $coinobs;
	
	/**
	 * Lista de séries de nota fiscal
	 * @var array
	 */
	public $seriesNf;
	
	/**
	 * Lista de Corretores do tipo I - Indicador
	 * @var array
	 */
	public $listaIndicadorNegocio;
	
	/**
	 * Mensagem de retorno da tela
	 * @var string
	 */
	public $msg;
	
	/**
	 * Itens a confirmar pagamento
	 * @var array
	 */
	public $itensConfirmar;
	
	
	/**
	 * Construtor, configura acesso a dados e parâmetros iniciais do módulo
	 */
	public function __construct() {
		
		global $conn;
		$this->dao = new FinComissaoIndicadorDAO($conn);
		$this->daoParametros = new FinParametrosComissaoIndicadorDAO($conn);
		
		$this->seriesNf 				= $this->dao->getSeriesNf();
		$this->listaIndicadorNegocio 	= $this->dao->getIndicadorNegocio();
		
		// Recebe campos do formulário
		$this->dataInicial 				= isset($_POST['data_inicial']) ? 			$_POST['data_inicial'] 				: '';
		$this->dataFinal   				= isset($_POST['data_final']) ? 			$_POST['data_final'] 				: '';
		$this->indicadorNegocio   		= isset($_POST['indicador_negocio']) ? 		$_POST['indicador_negocio'] 		: '';
		$this->numeroNf					= isset($_POST['numero_nf']) ? 				$_POST['numero_nf'] 				: '';
		$this->serieNf					= isset($_POST['serie_nf']) ? 				$_POST['serie_nf'] 					: '';
		$this->cotrato					= isset($_POST['contrato']) ? 				$_POST['contrato'] 					: '';
		$this->statusComissao			= isset($_POST['status_comissao']) ? 		$_POST['status_comissao'] 			: '';
		$this->somenteComissionaveis  	= isset($_POST['somente_comissionaveis']) ? $_POST['somente_comissionaveis'] 	: '';		
		$this->comissionavel			= isset($_POST['comissionavel']) ? 			$_POST['comissionavel'] 			: '';
		$this->coindt_pagamento		= isset($_POST['coindt_pagamento']) ? 		$_POST['coindt_pagamento'] 		: '';
		$this->coinobs					= isset($_POST['coinobs']) ? 				$_POST['coinobs'] 					: '';
		
		$this->notasPendentes 			= array();
		$this->notasGeradas 			= array();
			
		if (is_array($this->comissionavel)) {
			foreach ($this->comissionavel as $key => $value) {
				$arrItem = explode("|",$value);
				//arrItem => status_comissao|identificação da nota(numero+serie)|nflvl_total|eqcoid|connumero|obroid|corroid|nflno_numero|nflserie|valor comissao
				if ($arrItem[0]=="PENDENTE") {
					$this->notasPendentes[$arrItem[1]] = $arrItem[2]."|".$arrItem[3]."|".$arrItem[4]."|".$arrItem[5]."|".$arrItem[6]."|".$arrItem[7]."|".$arrItem[8]."|".$arrItem[9];
				}
				if ($arrItem[0]=="GERADA") {
					$this->notasGeradas[$arrItem[1]] = $arrItem[2]."|".$arrItem[3]."|".$arrItem[4]."|".$arrItem[5]."|".$arrItem[6]."|".$arrItem[7]."|".$arrItem[8]."|".$arrItem[9];
				}			
			}
			$this->notasPendentesEGeradas = array_merge($this->notasPendentes,$this->notasGeradas);
		}
	}
	
	//
	public function getNomeIndicador($corroid) {
		return $this->dao->getNomeIndicador($corroid);
	}
	
	//
	public function index() {
		
	}
		
	public function pesquisar() {
		try {
			// Efetua a pesquisa pelas notas fiscais
			$rsPesquisaNotasFiscais = $this->dao->getResultadoPesquisa(
					$this->dataInicial, 
					$this->dataFinal,
					$this->indicadorNegocio, 
					$this->numeroNf, 
					$this->serieNf, 
					$this->cotrato, 
					$this->statusComissao, 
					$this->somenteComissionaveis
			);
			
			// Chama a view
			require 'modulos/Financas/View/fin_comissao_indicador/resultado_pesquisa.view.php';
		}
		catch (Exception $e) {
			
			$this->msg = $e->getCode().': '.$e->getMessage();
		}
	}
	
	// Req. 2.5 - Cálculo de comissão conforme regras de negócio.
	public function gerarComissao() {
		try {			
			
			$txt = "<script>";
			if (is_array($this->notasPendentesEGeradas)) {
				foreach ($this->notasPendentesEGeradas as $key => $value){
					//$txt .= "console.log('".$key." => ".$value."')\n";
					$arrayItem = explode("|",$value);
					
					//Calculo da comissão
					$parametrosComissao = $this->daoParametros->getParametrosClasse($arrayItem[1]);
					
					//parametro: item comissionavel - boleano
					$pciitem_comissao 		= $parametrosComissao['pciitem_comissao'];			
					//parametro: valor fixo da comissao
					$pcivl_comissao 		= $parametrosComissao['pcivl_comissao'];
					//parametro: percentual de comissao
					$pcivl_perc_comissao 	= $parametrosComissao['pcivl_perc_comissao'];
					//parametro: tipo de comissao
					$pcitipo_comissao 		= $parametrosComissao['pcitipo_comissao'];
					//parametro: valor minimo de comissao
					$pcivl_minimo_comissao 	= $parametrosComissao['pcivl_minimo_comissao'];
					//parametro: valor maximo de comissao
					$pcivl_maximo_comissao 	= $parametrosComissao['pcivl_maximo_comissao'];
					
					//contrato
					$coinconoid				= (!empty($arrayItem[2])) 	? $arrayItem[2] 	: 0;
					//obrigacao financeira
					$coinobroid 				= (!empty($arrayItem[3]))	? $arrayItem[3] 	: 0;
					//valor da nota
					$coinvl_item 				= (!empty($arrayItem[0]))	? $arrayItem[0] 	: 0;
					//corretor
					$coincorroid 				= (!empty($arrayItem[4]))	? $arrayItem[4] 	: 0;
					//numero da nota
					$coinnflno_numero 			= (!empty($arrayItem[5]))	? $arrayItem[5] 	: 0;
					//serie
					$coinnflserie 				= (!empty($arrayItem[6]))	? $arrayItem[6] 	: '';
					
					//Calculo do valor da comissão
					if (trim($pcitipo_comissao) == 'V') {
						$coinvl_comissao_bruta = $coinvl_item * $pcivl_perc_comissao / 100;
												
						if ($coinvl_item < $pcivl_minimo_comissao) {
							$coinvl_comissao = 0;
						} 
						else if ($pcivl_maximo_comissao > 0 && $coinvl_comissao_bruta > $pcivl_maximo_comissao) {
							$coinvl_comissao = $pcivl_maximo_comissao;
						}
						else {
							$coinvl_comissao = $coinvl_comissao_bruta;
						}
					} else {
						$coinvl_comissao_bruta = $pcivl_comissao;
						
						if ($coinvl_item < $pcivl_minimo_comissao) {
							$coinvl_comissao = 0;
						}
						else{
							$coinvl_comissao = $coinvl_comissao_bruta;
						}
					}
					$coinvl_comissao = round($coinvl_comissao, 2);
					
					/* $txt .= "console.log('classe => ".$arrayItem[1]."')\n";
					$txt .= "console.log('key => ".$key."')\n";
					$txt .= "console.log('item comissionavel => ".$pciitem_comissao."')\n";
					$txt .= "console.log('valor fixo da comissao => ".$pcivl_comissao."')\n";
					$txt .= "console.log('percentual de comissao => ".$pcivl_perc_comissao."')\n";
					$txt .= "console.log('tipo de comissao => ".$pcitipo_comissao."')\n";
					$txt .= "console.log('valor minimo de comissao => ".$pcivl_minimo_comissao."')\n";
					$txt .= "console.log('valor maximo de comissao => ".$pcivl_maximo_comissao."')\n";
					$txt .= "console.log('contrato => ".$coinconoid."')\n";
					$txt .= "console.log('obrigacao financeira => ".$coinobroid."')\n";
					$txt .= "console.log('valor da nota => ".$coinvl_item."')\n";
					$txt .= "console.log('corretor => ".$coincorroid."')\n";
					$txt .= "console.log('numero da nota => ".$coinnflno_numero."')\n";
					$txt .= "console.log('serie => ".$coinnflserie."')\n";
					$txt .= "console.log('Comissão bruta => ".$coinvl_comissao_bruta."')\n";
					$txt .= "console.log('Calculo do valor da comissão => ".$coinvl_comissao."')\n";
					$txt .= "console.log('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~')\n"; */
					
					//Persistencia de dados
					$setComissao = $this->dao->setComissao($coinconoid,
						$coinobroid,
						$coinvl_item,
						$coinvl_comissao,
						$coincorroid,
						$coinnflno_numero,
						$coinnflserie);
					
					$msg = $setComissao['msg'];	
					
					if ($setComissao['code']==1) {
						throw new Exception($setComissao['msg'],1);
					}	
				}
			}
			if (!empty($msg)) $txt .= "alert('".$msg."')\n";
			echo $txt .= "</script>";
			
			$this->pesquisar();
		}
		catch (Exception $e) {
						
			$txt .= "alert('".$e->getMessage()."')\n";
			echo $txt .= "</script>";
			
			$this->pesquisar();
		}
	}
	
	// Req. 2.7 - 
	public function efetuarPagamento() {
		
		$txt = "<script>";
		$this->itensConfirmar = array();
		$this->coindt_pagamento 	= (!empty($_POST['coindt_pagamento'])) 	? $_POST['coindt_pagamento'] 	: '';
		$this->coinobs 			= (!empty($_POST['coinobs'])) 				? $_POST['coinobs'] 			: '';		
		
		if (is_array($this->notasGeradas)) {
			foreach ($this->notasGeradas as $key => $value){
					
				$arrayItem = explode("|",$value);
					
				//corretor
				$coincorroid 				= (!empty($arrayItem[4]))	? $arrayItem[4] 	: 0;
				//valor da comissao
				$coinvl_comissao			= (!empty($arrayItem[7]))	? $arrayItem[7] 	: 0;
					
				/* $txt .= "console.log('indicador (corretor) => ".$coincorroid."')\n";
				$txt .= "console.log('valor da comissao => ".$coinvl_comissao."')\n";
				$txt .= "console.log('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~')\n"; */
	
				//montar comissão dos indicadores (corretores)
				$this->itensConfirmar[$coincorroid] += $coinvl_comissao;
			}
		}
		echo $txt .= "</script>";
		
		$this->pesquisar();
	}
	
	// Req. 2.7
	public function confirmarPagamento() {
	try {
				
			$txt = "<script>";
			if (is_array($this->notasGeradas)) {
				foreach ($this->notasGeradas as $key => $value){
					
					$arrayItem = explode("|",$value);
					
					//contrato
					$coinconoid				= (!empty($arrayItem[2])) 	? $arrayItem[2] 	: 0;
					//numero da nota
					$coinnflno_numero 			= (!empty($arrayItem[5]))	? $arrayItem[5] 	: 0;
					//serie
					$coinnflserie 				= (!empty($arrayItem[6]))	? $arrayItem[6] 	: '';
					//data pagto informada
					$coindt_pagamento 			= $this->coindt_pagamento;
					//observacao
					$coinobs 					= $this->coinobs;
					
					/* $txt .= "console.log('contrato => ".$coinconoid."')\n";
					$txt .= "console.log('numero da nota => ".$coinnflno_numero."')\n";
					$txt .= "console.log('serie => ".$coinnflserie."')\n";
					$txt .= "console.log('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~')\n"; */
						
					//Persistencia de dados
					$setPagos = $this->dao->setPagos($coinconoid,$coinnflno_numero,$coinnflserie,$coindt_pagamento,$coinobs);
						
					$msg = $setPagos['msg'];
						
					if ($setPagos['code']==1) {
						throw new Exception($setPagos['msg'],1);
					}
				}
				if (!empty($msg)) $txt .= "alert('".$msg."')\n";
			}			
			echo $txt .= "</script>";
				
			$this->pesquisar();
		}
		catch (Exception $e) {
		
			$txt .= "alert('".$e->getMessage()."')\n";
			echo $txt .= "</script>";
				
			$this->pesquisar();
		}
	}
	
	// Req. 2.6
	public function excluirComissao() {
		try {
				
			$txt = "<script>";
			if (is_array($this->notasGeradas)) {
				foreach ($this->notasGeradas as $key => $value){
					
					$arrayItem = explode("|",$value);
					
					//contrato
					$coinconoid				= (!empty($arrayItem[2])) 	? $arrayItem[2] 	: 0;
					//numero da nota
					$coinnflno_numero 			= (!empty($arrayItem[5]))	? $arrayItem[5] 	: 0;
					//serie
					$coinnflserie 				= (!empty($arrayItem[6]))	? $arrayItem[6] 	: '';
					
					/* $txt .= "console.log('contrato => ".$coinconoid."')\n";
					$txt .= "console.log('numero da nota => ".$coinnflno_numero."')\n";
					$txt .= "console.log('serie => ".$coinnflserie."')\n";
					$txt .= "console.log('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~')\n"; */
						
					//Persistencia de dados
					$setExcluidos = $this->dao->setExcluidos($coinconoid,$coinnflno_numero,$coinnflserie);

					$msg = $setExcluidos['msg'];
						
					if ($setExcluidos['code']==1) {
						throw new Exception($setExcluidos['msg'],1);
					}
				}
				if (!empty($msg)) {
					$txt .= "alert('".$msg."')\n";
				}
			}
			echo $txt .= "</script>";
				
			$this->pesquisar();
		}
		catch (Exception $e) {
		
			$txt .= "alert('".$e->getMessage()."')\n";
			echo $txt .= "</script>";
				
			$this->pesquisar();
		}
	}
}
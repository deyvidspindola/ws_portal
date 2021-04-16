<?php
/**
 * Classe responsável pelo cálculo de juros, multa e desconto  dos títulos acionados pela cobrança, 
 * essa estrutura deverá também gerar as informações para o boleto unificado (boletagem massiva) de acordo
 * a política de desconto
 * 
 * @file FinCalculoDivida.php
 * @author marcio.ferreira
 * @version 06/07/2015 15:01:07
 * @since 06/07/2015 15:01:07
 * @package SASCAR FinCalculoDivida.php 
 */


//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/log_calculo_divida_'.date('d-m-Y').'.txt');


//manipula os dados no BD
require(_MODULEDIR_ . "Financas/DAO/FinCalculoDividaDAO.php");

//módulo para gerenciar registro de boletos com registro
use module\Boleto\BoletoService as Boleto;
use module\RoteadorBoleto\RoteadorBoletoService as RoteadorBoleto;

class FinCalculoDivida {

	//atributos privados
	private $dao;
	
	private $id_titulo;
	private $clioid;
	private $dias_atraso;
	private $tipo_calculo; // 1 - calculo normal   2 - politica de desconto
	private $nova_data_vencimento;
	private $percento_desconto;
	private $percento_multa;
	private $percento_juros;
	private $id_usuario;
	private $tit_obs_historio;
	private $tipo_politica; // J - Juros / M - Multa / JM - Juros e Multa / VJM - Valor, Juros e Multa
	

	public function __construct() {
		
		global $conn;
		
		$this->dao  = new FinCalculoDividaDAO($conn);
	}
	
	
	public function index(){
	
		print('Chamada Inválida');
	
		die;
	
	}
	
	
    /**
     * Efetua os cáculos dos títulos vencidos ou aplica a politica de desconto
     * 
     * @param boolean $viaParam - Informar se os dados estão vindo via atributos ou post
     * @throws Exception
     */
	public function calcular($viaParam = false){
		
		$dadosTitulo        = new stdClass();
		$dadosTituloCalculo = new stdClass();
		$totalValores       = new stdClass();
		$dados_retorno      = array();
		
		//Objeto que guarda os dados para recalcular o valor final do titulo e retorna os resultados
		$dadosValorRecalculado = new stdClass();
		$dadosValorRecalculado->valor_desconto_cobranca = 0.00;
			
		$dadosTitulo->id_titulo            = isset($_POST['id_titulo']) ? $_POST['id_titulo'] : $this->getIdTitulo();
		$dadosTitulo->tipo_calculo         = isset($_POST['tipo_calculo']) ? $_POST['tipo_calculo'] : $this->getTipoCalculo();
		$dadosTitulo->nova_data_vencimento = isset($_POST['nova_data_vencimento']) ? $_POST['nova_data_vencimento'] : $this->getNovaDataVencimento();
		$dadosTitulo->percento_desconto    = isset($_POST['percento_desconto']) ? $_POST['percento_desconto'] : $this->getPercentoDesconto();
		$dadosTitulo->percento_multa       = isset($_POST['percento_multa']) ? $_POST['percento_multa'] : $this->getPercentoMulta();
		$dadosTitulo->percento_juros       = isset($_POST['percento_juros']) ? $_POST['percento_juros'] : $this->getPercentoJuros();
		
		
		try {
			
			
			if(empty($dadosTitulo->id_titulo)){
				throw new Exception('O(s) título(s) deve(m) ser informado(s).');
			}
			
			
			if(is_array($dadosTitulo->id_titulo)){
				$dadosTitulo->id_titulo = implode($dadosTitulo->id_titulo, ',');
			}
			
			
			if(empty($dadosTitulo->tipo_calculo)){
				throw new Exception('O tipo de cálculo deve ser informado.');
			}
			
			if(empty($dadosTitulo->nova_data_vencimento)){
				throw new Exception('A nova data de vencimento deve ser informada.');
			}
			
			//pesquisar o dados (valores) dos títulos informados
			$dados_titulo = $this->dao->pesquisarDadosTitulo($dadosTitulo->id_titulo);
			
			if(count($dados_titulo) == 0){
				throw new Exception('Título(s) informado(s) não econtrado(s).');
			}

			//se o tipo de cálculo for politica de desconto
			if($dadosTitulo->tipo_calculo == 2){
				
				//verifica em qual tipo de politica entra os títulos informados
				$politica_aplicada = $this->verificarAplicacaoPoliticaDesconto($dadosTitulo->id_titulo);
				
				$this->tipo_politica = $politica_aplicada[0]['podaplicacao'];
				
			}
			
		
			//percorre o array com os dados de cada título  para manipular os dados para cálculos
			foreach($dados_titulo as $key => $valores){
				
				$dadosTituloCalculo->num_nota[$key]         = $valores['nota'];
				$dadosTituloCalculo->id_titulo[$key]        = $valores['titoid'];
				$dadosTituloCalculo->titdt_vencimento[$key] = $valores['titdt_vencimento'];
				
				//realiza o cálculo somente se o título não tiver data de pagamento
				if($valores['titdt_pagamento'] == ''){
					
					 //calcula os dias de atraso entre a data antiga atrasada e a nova data de vencimento informada na tela
					 $this->dias_atraso = $this->calcularDiasAtraso($dadosTituloCalculo->titdt_vencimento[$key], $dadosTitulo->nova_data_vencimento);
					 
					 //calcula os valores apenas se a data para calculo for maior que a data do titulo
					 if($this->dias_atraso > 0){
					 	
						 $dadosTituloCalculo->titvl_titulo[$key]     = $valores['titvl_titulo'];
						 $dadosTituloCalculo->titvl_desconto[$key]   = $valores['titvl_desconto'];
						 $dadosTituloCalculo->outrasDeducoes[$key]   = $valores['titvl_ir'] + $valores['titvl_iss'] + $valores['titvl_piscofins'];
						 
						 //pega os dados para calcular a multa
						 $dadosMulta = new stdClass();
						 $dadosMulta->percento_multa = $dadosTitulo->percento_multa;
						 $dadosMulta->valor_titulo   = $dadosTituloCalculo->titvl_titulo[$key];
						 //envia os dados para calcular a multa
						 $dadosTituloCalculo->titvl_multa[$key] = $this->calcularMulta($dadosMulta);
						 
						 
						 //pega os dados para calcular o juros
						 $dadosJuros = new stdClass();
						 $dadosJuros->percento_juros = $dadosTitulo->percento_juros;
						 $dadosJuros->valor_titulo   = $dadosTituloCalculo->titvl_titulo[$key];
						 $dadosJuros->dias_atraso    = $this->dias_atraso;
						 //envia os dados para calcular o juros
						 $dadosTituloCalculo->titvl_juros[$key] = $this->calcularJuros($dadosJuros);
						 
						 
						 //calcula o desconto administrativo somente para calculo normal
						 if($dadosTitulo->tipo_calculo == 1){
						 	
							 //pega os dados para calcular o desconto administrativo
							 $dadosDescAdm = new stdClass();
							 $dadosDescAdm->valor_multa            = $dadosTituloCalculo->titvl_multa[$key];
							 $dadosDescAdm->valor_juros            = $dadosTituloCalculo->titvl_juros[$key];
							 $dadosDescAdm->pecento_desconto       = $dadosTitulo->percento_desconto;
						     //envia os dados para calcular o desconto administrativo		 
							 $dadosTituloCalculo->desconto_administrativo[$key] = $this->calcularDescontoAdministrativo($dadosDescAdm);
							 
							 //se não houver o desconto administrativo, então exibe na tela o desconto que vem do banco 
							 if($dadosTituloCalculo->desconto_administrativo[$key] == ''){
							       $dadosTituloCalculo->desconto_administrativo[$key] = $dadosTituloCalculo->titvl_desconto[$key];
							       
							 }
							 
							 //se der percentual de desconto, aplica o desconto do banco mais o valor do calculo administrativo
							 if($dadosDescAdm->pecento_desconto != '' && $dadosDescAdm->valor_multa != '' && $dadosDescAdm->valor_multa != ''){
							 		$dadosValorRecalculado->valor_desconto_banco_adm = $dadosTituloCalculo->titvl_desconto[$key];
							 }
							 
						 }
						 
						 $dadosValorRecalculado->valor_titulo    = $dadosTituloCalculo->titvl_titulo[$key];
						 $dadosValorRecalculado->valor_multa     = $dadosTituloCalculo->titvl_multa[$key];
						 $dadosValorRecalculado->valor_juros     = $dadosTituloCalculo->titvl_juros[$key];
						 $dadosValorRecalculado->valor_desconto  = $dadosTituloCalculo->desconto_administrativo[$key];
						 $dadosValorRecalculado->outras_deducoes = $dadosTituloCalculo->outrasDeducoes[$key];
						 
						 //efetua os cálculos da política de desconto
						 if($dadosTitulo->tipo_calculo == 2){
						 	
						 	//objeto com os dados do título 
						 	$dadosTituloPolitica = new stdClass();
						 	
						 	$dadosTituloPolitica->valor_titulo       = $dadosTituloCalculo->titvl_titulo[$key];
						 	$dadosTituloPolitica->valor_multa        = $dadosTituloCalculo->titvl_multa[$key];
						 	$dadosTituloPolitica->valor_juros        = $dadosTituloCalculo->titvl_juros[$key];
						 	$dadosTituloPolitica->valor_desconto     = $valores['titvl_desconto'];
						 	$dadosTituloPolitica->outras_deducoes    = $dadosTituloCalculo->outrasDeducoes[$key];
						 	$dadosTituloPolitica->percento_desconto  = $dadosTitulo->percento_desconto;
						 
						 	///efetua o cálculo politica e retorna o resultado dos cálculo
						 	$calculo_politica = $this->calcularPoliticaDesconto($dadosTituloPolitica);
						 	
						 	//recebe o valores do cálculo da politica
						 	$dadosValorRecalculado->valor_multa             = $calculo_politica->valor_multa;
						 	$dadosValorRecalculado->valor_juros             = $calculo_politica->valor_juros;
						 	
						 	//valor exibido em tela
						 	$dadosValorRecalculado->valor_desconto_cobranca = $calculo_politica->valor_desconto_cobranca;
						 	
							//para politica de desconto, o valor de desconto, valor desconto administrativo fica com o valor de desconto que vem da tabela titulo
						 	$dadosValorRecalculado->valor_desconto             = $valores['titvl_desconto'];
						 	$dadosTituloCalculo->desconto_administrativo[$key] = $valores['titvl_desconto'];
						 	
						 }
						 
						 
						 //envia os dados para recalcular o valor total dos títulos
						 $dadosTituloCalculo->valor_recalculado[$key] = $this->recalcularValorTotal($dadosValorRecalculado);
						 
						 // para tipo de politica Total, Multa e Juros quando o desconto for 100% zera o valor do titulo recalculado
						 if($dadosTitulo->percento_desconto == 100.00 && $this->tipo_politica == 'TJM'){
						 	$dadosTituloCalculo->valor_recalculado[$key] = 0.00;
						 }
						 
						 //monta array dos dados do título para retornar para o jason
						 $dados_retorno[$key]['nota']                    = $dadosTituloCalculo->num_nota[$key];
						 $dados_retorno[$key]['titoid']                  = $dadosTituloCalculo->id_titulo[$key]; 
						 $dados_retorno[$key]['titdt_vencimento']        = $dadosTituloCalculo->titdt_vencimento[$key];
						 $dados_retorno[$key]['valor_titulo']            = $dadosValorRecalculado->valor_titulo;
						 $dados_retorno[$key]['valor_multa']             = $dadosValorRecalculado->valor_multa;
						 $dados_retorno[$key]['valor_juros']             = $dadosValorRecalculado->valor_juros;
						 //desconto da politica valor exibido em tela
						 $dados_retorno[$key]['valor_desconto_cobranca'] = $dadosValorRecalculado->valor_desconto_cobranca;
						 
						 //se houve rateio de juros e multa, zera valor de desconto em banco
						 if($calculo_politica->rateio_juros_multas){
						 	
						 	$dados_retorno[$key]['valor_desconto_banco'] = 0.00;
							
						 //(Politica de desconto) valor que será armazenado no banco = valor do titulo sem descontos menos o valor recalculado com todos os descontos e acréscimos
						 }else{
						 	
						 	### MANTIS 7765
						 	// se o tipo de política for para Total, Juros e Multa, é feito cálculo para verificar a diferença do desconto aplicado sobre o valor de face para gravar em banco
						 	if($this->tipo_politica == 'TJM'){
						 		$dados_retorno[$key]['valor_desconto_banco'] =  $dadosTituloCalculo->titvl_titulo[$key] - $dadosTituloCalculo->valor_recalculado[$key] - $dadosValorRecalculado->valor_desconto;
						 	}
						 	
						 	// se a diferença for valor negativo, então retorna 0,00, pois não é um desconto
						 	if($dados_retorno[$key]['valor_desconto_banco'] < 0){
						 		$dados_retorno[$key]['valor_desconto_banco'] = 0.00;
						 	}
						 	
						 }
						 
						 
						 //valor do desconto administrativo (vem da tabela título)
						 $dados_retorno[$key]['valor_desconto']          = $dadosValorRecalculado->valor_desconto;
						 $dados_retorno[$key]['outras_deducoes']         = $dadosValorRecalculado->outras_deducoes;
						 $dados_retorno[$key]['valor_recalculado']       = $dadosTituloCalculo->valor_recalculado[$key];
						 $dados_retorno[$key]['rateio_juros_multas']     = $calculo_politica->rateio_juros_multas;
						 
						 
						 //efetua o cálculo dos totais
						 $totalValores->vlr_titulo                  += $dadosTituloCalculo->titvl_titulo[$key];
						 $totalValores->vlr_outras_deducoes         += $dadosTituloCalculo->outrasDeducoes[$key];
						 $totalValores->vlr_multa                   += $dadosValorRecalculado->valor_multa;
						 $totalValores->vlr_juros                   += $dadosValorRecalculado->valor_juros;
						 $totalValores->vlr_desconto_adm            += $dadosTituloCalculo->desconto_administrativo[$key];
						 //desconto da politica valor exibido em tela
						 $totalValores->vlr_desconto_cobranca       += $dadosValorRecalculado->valor_desconto_cobranca ;
						 $totalValores->vlr_desconto_cobranca_banco += $dados_retorno[$key]['valor_desconto_banco'] ;
						 $totalValores->vlr_recalculado             += $dadosTituloCalculo->valor_recalculado[$key];
						 
					 //fim if dias atraso
					 }else{
					 	
					 	 
					 	 $dados_retorno[$key]['nota']                    = $dados_titulo[$key]['nota'];
					 	 $dados_retorno[$key]['titoid']                  = $dados_titulo[$key]['titoid'];
					 	 $dados_retorno[$key]['titdt_vencimento']        = $dados_titulo[$key]['titdt_vencimento'];
					 	 $dados_retorno[$key]['valor_titulo']            = $dados_titulo[$key]['titvl_titulo'];
					 	 $dados_retorno[$key]['valor_multa']             = $dados_titulo[$key]['titvl_multa'];
					 	 $dados_retorno[$key]['valor_juros']             = $dados_titulo[$key]['titvl_juros'];
					 	 $dados_retorno[$key]['valor_desconto']          = $dados_titulo[$key]['titvl_desconto'];
					 	 $dados_retorno[$key]['outras_deducoes']         = $dados_titulo[$key]['titvl_ir'] +  $dados_titulo[$key]['titvl_iss'] +  $dados_titulo[$key]['titvl_piscofins'];
					 	 $dados_retorno[$key]['valor_recalculado']       = $dados_titulo[$key]['titvl_titulo'] +  $dados_retorno[$key]['valor_multa'] +  $dados_retorno[$key]['valor_juros'] - $dados_retorno[$key]['outras_deducoes'] - $dados_retorno[$key]['valor_desconto'];
					 	 
					 	 
					 	 //efetua o cálculo dos totais
					 	 $totalValores->vlr_titulo                  += $dados_retorno[$key]['valor_titulo'];
					 	 $totalValores->vlr_outras_deducoes         += $dados_retorno[$key]['outras_deducoes'];
					 	 $totalValores->vlr_multa                   += $dados_retorno[$key]['valor_multa'];
					 	 $totalValores->vlr_juros                   += $dados_retorno[$key]['valor_juros'];
					 	 $totalValores->vlr_desconto_adm            += $dados_retorno[$key]['valor_desconto'];
					 	 //desconto da politica valor exibido em tela
					 	 $totalValores->vlr_desconto_cobranca       += 0.00;
					 	 $totalValores->vlr_desconto_cobranca_banco += 0.00;
					 	 $totalValores->vlr_recalculado             += $dados_retorno[$key]['valor_recalculado']; 
					 	 
					 	 
					 }
					 
				}//fim if dt de pagamento
			}//fim foreach
			
			
			$total[0]['total_vlr_titulo']                   = $totalValores->vlr_titulo;
			$total[1]['total_vlr_outras_deducoes']          = $totalValores->vlr_outras_deducoes;
			$total[2]['total_vlr_multa']                    = $totalValores->vlr_multa;
			$total[3]['total_vlr_juros']                    = $totalValores->vlr_juros;
			$total[4]['total_vlr_desconto_adm']             = $totalValores->vlr_desconto_adm;
			//desconto da politica valor exibido em tela
			$total[5]['total_vlr_desconto_cobranca']        = $totalValores->vlr_desconto_cobranca;
			$total[6]['total_vlr_recalculado']              = $totalValores->vlr_recalculado;
			
			//para tratar o total_vlr_recalculado mais de 3 casas depois do ponto flutuante (estava imprimindo valor errado no boleto)
			$valorFluante = explode('.', $totalValores->vlr_recalculado);
			$tamanhoFlutuante = strlen($valorFluante[1]);
			//se for maior que duas casas decimais depois do ponto, então faz o tratamento para pegar apenas a centena depois do ponto (.)	
			if($tamanhoFlutuante > 2){
				$vlr_flutante = substr($valorFluante[1], 0, 2);
				$total[6]['total_vlr_recalculado'] = $valorFluante[0].'.'.$vlr_flutante;
			}
			
			// para tipo de politica Tota,  Multa e Juros quando o desconto for 100% zera o valor total recalculado
			if($dadosTitulo->percento_desconto== 100.00 && $this->tipo_politica == 'TJM'){
				$total[6]['total_vlr_recalculado'] = 0.00;
			}
			
			$total[7]['total_vlr_desconto_cobranca_banco']  = $totalValores->vlr_desconto_cobranca_banco;
			
			//agrupa o dados em um mesmo array
			$dados = array_merge($dados_retorno, $total);
			
			//retorna o array se a chamada está sendo feita pela classe
			if($viaParam){
			
				return $dados;
			
			}else{
				
				//retorna jason se a chamada está sendo feita via ajax
				echo json_encode($dados);
				exit();
			}
			
			
			
		} catch (Exception $e) {
			
			//retorna array com mensagem de erro se a chamada está sendo feita pela classe
			if($viaParam){
				
				return array('error' => true, 'message' => $e->getMessage());
				
			}else{
				
				//retorna mensagem de erro se a chamada está sendo feita via ajax
				echo utf8_encode($e->getMessage());
				exit();
			}
		}
	}
		

	/**
	 * Efetua o cálculo da politica de desconto de acordo com o tipo de regra aplicada
	 * 
	 * @param object $dados
	 * @throws Exception
	 * @return object
	 */
	private function calcularPoliticaDesconto($dados){
		
		if(!is_object($dados)){
			throw new Exception('Objeto inválido para calcular a política de desconto.');
		}
		
		//cálculo de desconto de politica sobre a multa ou para juros e multa
		if($this->tipo_politica == 'M' ||  $this->tipo_politica == 'JM'){
			
			$desconto_multa = $this->calcularDescontoPoliticaMultas($dados);
			$dados->valor_multa = $desconto_multa;
		}
		
		
		//cálculo de desconto de politica sobre o juros ou para juros e multa
		if($this->tipo_politica == 'J' || $this->tipo_politica == 'JM'){
			
			$desconto_juros = $this->calcularDescontoPoliticaJuros($dados);
			$dados->valor_juros = $desconto_juros;
		}
		
		
		//cálculo de desconto de politica sobre o Total, Juros e Multa
		if($this->tipo_politica == 'TJM' && $dados->percento_desconto >= 0){
			
			$desconto_total_juros_multa = $this->calcularDescontoPoliticaTotalJurosMulta($dados);
		
			//valor exibido só em tela
			$dados->valor_desconto_cobranca = $desconto_total_juros_multa['valor_desconto_tela'];
			
			## rateio
			//total da multa e juros sem o desconto da politica
			$vlr_nulta_juros = $dados->valor_multa + $dados->valor_juros;
			
			//se o valor total da soma de multa e juros for maior que o valor do desconto da politica, faz o rateio da multa e dos juros 
			if($vlr_nulta_juros > $dados->valor_desconto_cobranca){
				
				$dados->rateio_juros_multas = true;
				
				//subtrai da soma da multa com o juros o valor do desconto da politica
				$dif_multa_juros_e_desconto = $vlr_nulta_juros - $dados->valor_desconto_cobranca;
				
				//se o valor da multa for maior que o restante da diferença calculada para o valor do juros, então, não recebe juros
				if($dados->valor_multa > $dif_multa_juros_e_desconto){
					
					$dados->valor_juros = '0.00';
					
					$dados->valor_multa = $dif_multa_juros_e_desconto;

				//senão, o valor do juros recebe o restante do novo cáculo de juros subtraindo o valor da multa
				}else{
					$dados->valor_juros = $dif_multa_juros_e_desconto - $dados->valor_multa;
					
				}
				
				//se entra nessa regra não exibe o desconto da cobrança
				$dados->valor_desconto_cobranca = '0.00';
				
			}
		}
		
		return $dados;
		
	}
	
	
	
	/**
	 * Calcula o valor de desconto sobre a Multa
	 * 
	 * @param float $valor_multa
	 * @return number
	 */
	private function calcularDescontoPoliticaMultas($dados){

		$desconto_multa = round($dados->valor_multa - $dados->valor_multa * $dados->percento_desconto / 100, 2);
		
		return $desconto_multa;
		
	}
	
	
	/**
	 * Calcula o valor de desconto sobe o Juros
	 * 
	 * @param object $dados
	 * @return unknown
	 */
	private function calcularDescontoPoliticaJuros($dados){
	
		$desconto_juros = round($dados->valor_juros - $dados->valor_juros * $dados->percento_desconto / 100, 2);
	
		return $desconto_juros;
	
	}
	
	
	/**
	 * Calcula os valores para desconto da regra para Total, Juros e Multa
	 * 
	 * @param object $dados
	 * @return unknown
	 */
	private function calcularDescontoPoliticaTotalJurosMulta($dados){
	
		
		if(!is_object($dados)){
			throw new Exception('Objeto inválido para calcular a política de desconto para Total, Juros e Multa.');
		}
		
		//calcula o valores sem o desconto da política
		$valor_total = $dados->valor_titulo - $dados->outras_deducoes + $dados->valor_multa + $dados->valor_juros - $dados->valor_desconto;
			
		//aplica o desconto da política
		$valor_com_desconto_politica = $valor_total - $valor_total / 100 * $dados->percento_desconto;
		
		//valor do desconto real exibido só em tela
		$valor_desconto_tela = round($valor_total - $valor_com_desconto_politica , 2);
		
		$valor_desconto['valor_desconto_tela']  = $valor_desconto_tela;
		
		return $valor_desconto;
	
	}
	
	
	/**
	 * Recebe o percentual da multa e valor original do título e retorna o valor da multa
	 * 
	 * @param object $dadosMulta
	 * @throws Exception
	 * @return number
	 */
	private function calcularMulta($dadosMulta){
		
		if(!is_object($dadosMulta)){
			throw new Exception('Objeto inválido para calcular a multa.');
		}
		
		if(empty($dadosMulta->percento_multa)){
			throw new Exception('O percentual da multa deve ser informado.');
		}
		
		if(empty($dadosMulta->valor_titulo)){
			throw new Exception('O valor do título deve ser informado.');
		}
		
		//calcula a multa com regra de arredondamento
		$valor_multa = round(($dadosMulta->valor_titulo * $dadosMulta->percento_multa) / 100 , 2);
		
		return $valor_multa;
	}
	
	
	
	/**
	 * Calcula a quantidade de dias entre o período de datas informado
	 * 
	 * @param date $data_inicio - Formato DD/MM/AAAA
	 * @param date $data_fim    - Formato DD/MM/AAAA
	 * @throws Exception
	 * @return number
	 */
	public function calcularDiasAtraso($data_inicio, $data_fim){
		
		
		if(empty($data_inicio)){
			throw new Exception('Informe a data de início para calcular os dias em atraso.');
		}
		
		if(empty($data_fim)){
			throw new Exception('Informe a data fim para calcular os dias em atraso.');
		}
		
		list($from_day, $from_month, $from_year) = explode("/", $data_inicio);
		list($to_day, $to_month, $to_year) = explode("/", $data_fim);
		
		$from_date = mktime(0,0,0,$from_month,$from_day,$from_year);
		$to_date = mktime(0,0,0,$to_month,$to_day,$to_year);
		
		$days = ($to_date - $from_date)/86400;
		
		return ceil($days);
			
	}
	
	/**
	 * Calcula o juros com base na quantidade de dias em atraso
	 * 
	 * @param object $dadosJuros
	 * @throws Exception
	 * @return number
	 */
	private function calcularJuros($dadosJuros){
			
		if(!is_object($dadosJuros)){
			throw new Exception('Objeto inválido para calcular o juros.');
		}
		
		if(empty($dadosJuros->percento_juros)){
			throw new Exception('O percentual de juros deve ser informado.');
		}
			
		if(empty($dadosJuros->valor_titulo)){
			throw new Exception('O valor do título deve ser informado.');
		}
		
		if(empty($dadosJuros->dias_atraso)){
			throw new Exception('Os dias de atraso deve ser informado.');
		}
		
		//calcula o valor do juros com regra de arredondamento
		$valor_juros = round((($dadosJuros->valor_titulo * $dadosJuros->percento_juros)  * $dadosJuros->dias_atraso) / 100 , 2);
		
		return $valor_juros;
	
	}
	
	
	
	/**
	 * Calcula o desconto adminstrativo para exibir em tela sobre o valor de desconto que vem do banco, neste caso,
	 * ignora o valor do banco e exibe o novo valor calculado
	 * 
	 * A principio não é usado para politica de desconto
	 * 
	 * @param float $valor_multa
	 * @param float $valor_juros
	 * @param float $percento_desconto
	 * @throws Exception
	 * @return number
	 */
	private function calcularDescontoAdministrativo($dadosDescAdm){
			
		if(!is_object($dadosDescAdm)){
			throw new Exception('Objeto inválido para calcular o desconto administrativo.');
		}
		
		//calcula o desconto com regra de arredondamento
		$vlr_desconto =  round((($dadosDescAdm->valor_multa + $dadosDescAdm->valor_juros) * $dadosDescAdm->pecento_desconto) / 100 , 2) ;
		
		return $vlr_desconto;
			
	}
	
	
	/**
	 * Realiza o cálculo final do título
	 * 
	 * @param Object $dadosValorRecalculado
	 */
	private function recalcularValorTotal($dados){

		if(!is_object($dados)){
			throw new Exception('Objeto inválido para recalcular o valor total do título.');
		}
		
		if(empty($dados->valor_titulo)){
			throw new Exception('O valor do título deve ser informado para recalcular o valor total do título.');
		}
		
		
		$valor_final = $dados->valor_titulo - $dados->outras_deducoes + $dados->valor_multa + $dados->valor_juros - $dados->valor_desconto - $dados->valor_desconto_banco_adm - $dados->valor_desconto_cobranca ; 

		
		return $valor_final;
			
	}
	
	
	/**
	 * Busca todos os titulos vencidos na tabela titulo do cliente pesquisado ou por um título informado
	 * 
	 * @param Object $dados
	 * @return unknown
	 */
	public function pesquisarTitulosVencidosCliente($dados){
		
		$ret_titulos = $this->dao->pesquisarTitulosVencidosCliente($dados);
		
		return $ret_titulos;
		
	}
	
	
	/**
	 * Verifica em qual politica de desconto se enquadra 
	 * 
	 * @param array $titulos //pode ser um array ou uma variável separando os títulos por ',' vírgula
	 * @return Ambigous <multitype:, boolean>
	 */
	public function verificarAplicacaoPoliticaDesconto($titulos){
		
		if(is_array($titulos)){
			$titulos = implode($titulos, ',');
		}
		
		$dados_titulo = $this->dao->pesquisarDadosTitulo($titulos);
		
		//verifica o título mais antigo e retorna a quantidade de dias vencido
		$quant_dias_vencido = $this->getMenorDataVencimentoTitulos($dados_titulo);

		//veficar qual a politica de desconto que será aplicada
		$politica_aplicar = $this->dao->verificarAplicacaoPoliticaDesconto($quant_dias_vencido);
			
		return $politica_aplicar;
		
	}
	
	
	/**
	 * Verifica no array a data de veciemtno mais antiga
	 * 
	 * @param array $dados_titulo
	 * @return unknown
	 */
	public function getMenorDataVencimentoTitulos($dados_titulo){
		
		//insere em um novo array todas as datas encontradas
		foreach ($dados_titulo as $data_venc){
			$datas_titulos_vencidos[] = $data_venc['titdt_vencimento_poli'];
		}
		
		//ordena as datas de vencdimento da menor para a maior
		$datas_vencidos = $this->ordenarDataVencimento($datas_titulos_vencidos);
		
		//pega a menor data encontrada para base de cálculo para a politica de desconto
		$menor_data_venc_politica = $datas_vencidos[0];
		
		$dt_hoje = date('Y-m-d');
		
		$data_vencimento = new DateTime($menor_data_venc_politica);
		$data_hoje       = new DateTime($dt_hoje);
			
		$intervalo = $data_vencimento->diff($data_hoje);
			
		//quantidade de dias do boleto com a MENOR DATA de vencimento(boleto mais atrasado)
		$quant_dias_vencido = $intervalo->days;
		
		return $quant_dias_vencido;
		
	}
	
	
	
	/**
	 * Ordena as datas da menor para a maior
	 * 
	 * @param array $datas_titulos
	 * @return number|unknown
	 */
     public function ordenarDataVencimento($datas_titulos){
     	
     	if(!function_exists('cmp')){
     		
     		function cmp($a, $b){
     		
     			$a = strtotime(str_replace('/','-',$a));
     			$b = strtotime(str_replace('/','-',$b));
     			if ($a == $b) {
     				return 0;
     			}
     			return ($a < $b) ? -1 : 1;
     		}
     	}
     	
		usort($datas_titulos, 'cmp');
		
		return $datas_titulos;
		
	}
	
	
	/**
	 * 
	 * @param object $dadosTitulo
	 * @throws Exception
	 * @return boolean
	 */
	public function consolidarTitulos($dadosTitulo){
		
		try {
			
			if(!is_array($dadosTitulo)){
				throw new Exception('Dados inválidos para consolidação dos títulos.');
			}
	
			//$this->dao->begin();
			
			//criar objeto com dados de títulos filhos para atualizar
			$dadosTitulosFilhos = new stdClass ();
			
			## MANTIS 7671
			//recupera o id do motivo do desconto para inserir no titulo
			$id_motivo_desconto = $this->dao->getMotivoDescontoTituloFilho();
			
			if(empty($id_motivo_desconto)){
				throw new Exception('O id do motivo de desconto do titulo filho deve ser informado.');
			}
			
			$dadosTitulosFilhos->id_motivo_desconto = 'NULL';

			//percorre os títulos
			foreach ($dadosTitulo as $dados){
				
				/**
				 * STI 86728 - 3.3 - Cancelar boletos registrados que fazem parte do título que será consolidado
				 * @author marcelo.burkard.ext
				 * @since 01/09/2017
				 */
				if (!empty($dados['titoid'])){
					$registro = RoteadorBoleto::isTituloRegistrado($dados['titoid'], 'titulo_consolidado');
					if ($registro) {
						RoteadorBoleto::cancelarTituloCnab($dados['titoid'], 'titulo_consolidado');
					}
				}
				
				// seja o tipo de cálculo é para TJM, então., zera o valor de juros e multa
				if($this->tipo_politica == 'TJM'){
			
					if (!empty($dados['titoid'])){
				
						$insere_desconto_total_real = true;
			
						$dadosTitulosFilhos->titoid               = $dados['titoid'];
						$dadosTitulosFilhos->valor_desconto_banco = $dados['valor_desconto_banco'];
							
						// se houve rateio seta os valores do cálculo de rateio de juros e multa
						if ($dados['rateio_juros_multas']) {
							
							$dadosTitulosFilhos->valor_juros = $dados['valor_juros'];
							$dadosTitulosFilhos->valor_multa = $dados['valor_multa'];

						//se não houve rateio zera juros e multa
						} else {
							
							$dadosTitulosFilhos->valor_juros = 0.0;
							$dadosTitulosFilhos->valor_multa = 0.0;
						}
			
						//insere o motivo de desconto somente se houver valor de desconto para gravar
						if($dadosTitulosFilhos->valor_desconto_banco != 0.00 && $dadosTitulosFilhos->valor_desconto_banco != ''){
							$dadosTitulosFilhos->id_motivo_desconto = $id_motivo_desconto;
						}
						
						//seta os valores sem os cálculos de juros , multas, com desconto de cada título filho
						$ret_update = $this->setValoresTituloFilho($dadosTitulosFilhos);
					}
			
				//se não for para tipo de politica TJM, então zera o desconto da politivca
				}else{
			
					if (!empty($dados['titoid'])){
			
						$dadosTitulosFilhos->titoid               = $dados['titoid'];
						$dadosTitulosFilhos->valor_juros          = $dados['valor_juros'];
						$dadosTitulosFilhos->valor_multa          = $dados['valor_multa'];
						
						$dadosTitulosFilhos->valor_desconto_banco = 0.00;
							
						//seta os valores com os cálculos de juros , multas, sem desconto de cada título filho
						$ret_update = $this->setValoresTituloFilho($dadosTitulosFilhos);
					}
				}
				
				
				//recupera os totais
				if(isset($dados['total_vlr_titulo'])){
					$total_vlr_titulo  = $dados['total_vlr_titulo'];
				}
				
				//exibido em tela
				if(isset($dados['total_vlr_desconto_cobranca'])){
					$total_vlr_desconto_cobranca = $dados['total_vlr_desconto_cobranca'];
				}

				//gravado em banco
				if(isset($dados['total_vlr_desconto_cobranca_banco'])){
					$total_vlr_desconto_cobranca_banco = $dados['total_vlr_desconto_cobranca_banco'];
				}
				
				if(isset($dados['total_vlr_desconto_adm'])){
					$total_vlr_desconto_adm = $dados['total_vlr_desconto_adm'];
				}
				
				if(isset($dados['total_vlr_juros'])){
					$total_vlr_juros  = $dados['total_vlr_juros'];
				}
				
				if(isset($dados['total_vlr_multa'])){
					$total_vlr_multa  = $dados['total_vlr_multa'];
				}
				
				if(isset($dados['total_vlr_recalculado'])){
					$total_vlr_recalculado  = $dados['total_vlr_recalculado'];
				}
				
				//recupera os ids dos títulos
				if (!empty($dados['titoid'])){
				    $titulosFilhos[] = $dados['titoid'];
				}
				
				
			}//fim foreach
			
			
			//recupera os dados no objeto para gerar o título consolidado
			$consolidado = new stdClass();
			
			$consolidado->titcclioid          = $this->getCodigoCliente();
			$consolidado->titcvl_titulo       = $total_vlr_titulo;
			$consolidado->titcvl_desconto     = $total_vlr_desconto_cobranca_banco + $total_vlr_desconto_adm;
			$consolidado->titcvl_juros        = $total_vlr_juros;
			$consolidado->titcvl_multa        = $total_vlr_multa;
			$consolidado->titcvl_desc_cobranca = $total_vlr_desconto_cobranca;
			$consolidado->titcvl_recalculado   = $total_vlr_recalculado;
			$consolidado->titcdt_vencimento   = $this->getNovaDataVencimento();
			
			$tipoTitulo = new stdClass();
			$tipoTitulo->tipo       = 'PD';
			$tipoTitulo->descricao  = 'Politica de Desconto';
			
			$consolidado->titctittoid        = $this->getTipoTitulo($tipoTitulo);///tipo do título da politica de desconto;
			$consolidado->titcobs_historico  = $this->getObsTituloConsolidado();
			$consolidado->titcusuoid         = $this->getIdUsuario();
			

			/**
			 * STI 86728 - 3.1.4 - Sempre inserir um novo registro na tabela “titulo_consolidado”
			 * @author marcelo.burkard.ext
			 * @since 01/09/2017
			 */

			//verifica o valor do título e se está no prazo estipulado pela febraban
			$explode_data_vencimento = explode('/', $consolidado->titcdt_vencimento);
			$format_data_vencimento = $explode_data_vencimento[2] . '-' . $explode_data_vencimento[1] .'-'. $explode_data_vencimento[0];
			
			$consolidado->titcformacobranca   = 84;// Cobrança Registrada Santander

			$titoid_pai = $this->setTituloPai($consolidado);
			
			//faz update nos títulos filhos referenciando o título pai
			$this->atualizarTituloFilho($titulosFilhos, $titoid_pai, $this->getObsTituloConsolidado());
			
			//grava log na tabela titulo_politica de desconto
			$dados_log = new stdClass();
			
			$dados_log->tpdusuoid_cadastro     = $this->getIdUsuario();
			$dados_log->tpdtitcoid             = $titoid_pai;
			$dados_log->tpddt_vencimento       = $this->getNovaDataVencimento();
			$dados_log->tpdvlr_cobrado         = $total_vlr_recalculado;
			$dados_log->tpddesc_aplicado       = $this->getPercentoDesconto();
			
			//grava o log
			$this->setLogPoliticaDesconto($dados_log);
			
			///recupera e retorna o dados para enviar para impressão do boleto
			$retorno_titulo['titulo']         = $titoid_pai;
			$retorno_titulo['valor_titulo']	  = $total_vlr_titulo;
			$retorno_titulo['valor_recalc']   = $total_vlr_recalculado;
			$retorno_titulo['valor_multa']    = $total_vlr_multa;
			$retorno_titulo['valor_juros']    = $total_vlr_juros;
			$retorno_titulo['valor_desconto'] = $total_vlr_desconto_cobranca;
			$retorno_titulo['data_emissao'] 	= date('Y-m-d');
			
			//$this->dao->commit();
			
			return $retorno_titulo;
			
		} catch (Exception $e) {
			
			$this->dao->rollback();
			echo $e->getMessage();
			return false;
		}
		
		
	}
	
	
	/**
	 * Atualiza os valores dos títulos filhos
	 * 
	 * @param object $dados
	 * @throws Exception
	 * @return boolean
	 */
	private function setValoresTituloFilho($dados){
		
		if(!is_object($dados)){
			throw new Exception('Objeto inválido para atualizar valores dos títulos filhos.');
		}
		
		$retorno = $this->dao->setValoresTituloFilho($dados);
		return $retorno;
	}
	
	
	/**
	 *  atualiza os título filhos referenciando o titulo pai na coluna tittitcoid
	 * 
	 * @param array $titulo_filho
	 * @param int $titulo_pai
	 * @param string $pesq_titobshistorico
	 */
	private function atualizarTituloFilho($titulo_filho, $titulo_pai, $pesq_titobshistorico){
		
		$retorno = $this->dao->atualizarTituloFilho($titulo_filho, $titulo_pai, $pesq_titobshistorico);
		return $retorno;
	}
	
	
	
	/**
	 * Retorna o id do tipo do titulo para inserir na tabela titulo
	 * 
	 * @param unknown $dados
	 * @throws Exception
	 * @return Ambigous <multitype:, boolean>
	 */
	private function getTipoTitulo($dados){
	
		$retorno = $this->dao->getTipoTitulo($dados);
		return $retorno;
	}
	
	
	/**
	 * 
	 * @return boolean
	 */
	private function getTituloConsolidado(){
		
		if(is_array($this->getIdTitulo())){
			$titulos_filhos = implode($this->getIdTitulo(), ',');
		}
		
		$retorno = $this->dao->getTituloConsolidado($titulos_filhos);
		return $retorno;
	}
	
	
	
	/**
	 * 
	 * @param object $dados
	 * @return boolean
	 */
	private function atualizarTituloPai($dados){
		
		$retorno = $this->dao->atualizarTituloPai($dados);
		return $retorno;
	}
	
	/**
	 * Insere um novo título pai com novos dados na tabela titulo_consolidado
	 * 
	 * @param object $dados
	 * @return boolean
	 */
	private function setTituloPai($dados){
		
		$retorno = $this->dao->setTituloPai($dados);
		return $retorno;
	}
	
	
	/**
	 * Insere log da politica de desconto aplicada
	 * 
	 * @param object $dados_log
	 * @return boolean
	 */
	private function setLogPoliticaDesconto($dados_log){
		
		$retorno = $this->dao->setLogPoliticaDesconto($dados_log);
		return $retorno;
	}
	
	public function pesquisarHistoricoRecalculo(){
		$retorno = $this->dao->pesquisarHistoricoRecalculo($_POST['id_titulo']);
		if($retorno){
			$retorno = array('possuiRecalculo' => true, 'titcformacobranca' => $retorno['titcformacobranca'], 'tpddesc_aplicado' => $retorno['tpddesc_aplicado'], 'tpddt_vencimento' => date('d/m/Y', strtotime($retorno['tpddt_vencimento'])));
		}else{
			$retorno = array('possuiRecalculo' => false);
		}
		echo json_encode($retorno);
	}
	
	//gets e sets
	public function setIdTitulo($valor){
		$this->id_titulo = $valor;
	}
	
	public function getIdTitulo(){
		return $this->id_titulo;
	}
	
	public function setTipoCalculo($valor){
		$this->tipo_calculo = $valor;
	}
	
	public function getTipoCalculo(){
		return $this->tipo_calculo;
	}
	
	public function setNovaDataVencimento($valor){
		$this->nova_data_vencimento = $valor;
	}
	
	public function getNovaDataVencimento(){
		return $this->nova_data_vencimento;
	}
	
	public function setPercentoDesconto($valor){
		$this->percento_desconto = $valor;
	}
	
	public function getPercentoDesconto(){
		return $this->percento_desconto;
	}
	
	public function setPercentoMulta($valor){
		$this->percento_multa = $valor;
	}
	
	public function getPercentoMulta(){
		return $this->percento_multa;
	}
	
	public function setPercentoJuros($valor){
		$this->percento_juros = $valor;
	}
	
	public function getPercentoJuros(){
		return $this->percento_juros;
	}
	
	public function setIdUsuario($valor){
		$this->id_usuario = $valor;
	}
	
	public function getIdUsuario(){
		return $this->id_usuario;
	}
	
	public function setObsTituloConsolidado($valor){
		$this->tit_obs_historio = $valor;
	}
	
	public function getObsTituloConsolidado(){
		return $this->tit_obs_historio;
	}
	
	public function setCodigoCliente($valor){
		$this->clioid = $valor;
	}
	
	public function getCodigoCliente(){
		return $this->clioid;
	}
	
	
	
}





?>
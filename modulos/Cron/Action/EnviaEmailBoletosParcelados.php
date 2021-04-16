<?php

/**
 * Classe para persistência de dados deste modulo
 */
require_once _MODULEDIR_.'Cron/DAO/EnviaEmailBoletosParceladosDAO.php';
/**
 * Classe para geração dos boletos
 */
require_once _MODULEDIR_.'Principal/Action/PrnBoletoSeco.class.php';
/**
 * Classe para buscas na tabela auxiliar parametros_siggo
 */
require_once _MODULEDIR_.'Principal/Action/PrnParametrosSiggo.class.php';
/**
 * Classe responsavel pelos envios de e-mail's
 */
require_once _MODULEDIR_.'Principal/Action/ServicoEnvioEmail.php';

/**
 * @author 	Leandro Alves Ivanaga
 * @email   leandroivanaga@brq.com
 * @version 19/09/2013
 * @since   19/09/2013
 * 
**/

class EnviaEmailBoletosParcelados {

    private $dao;
    private $boletoSecoAction;
    private $parametrosSiggo;
    private $servicoEmail;
    
    public function __construct() {
    	$this->dao = new EnviaEmailBoletosParceladosDAO();
    	$this->boletoSecoAction = new PrnBoletoSeco();
    }
    
    public function enviaBoletosParcelados(){
    	
    	$msg = "";
    	
    	try {
    		$erro = false;
    		$msg_erro = '';
    		$msgErroRegistro = '';
    		
	    	// Busca os titulos, que devem ser gerados as parcelas
	    	$boletosTitulos = $this->dao->boletosSecosPagos();
	    	
	    	if (count($boletosTitulos) > 0 || !empty($boletosTitulos)){
	    		
	    		// Formata as parcelas de acordo com resultado da busca anterior
	    		$titulosParcelas = $this->formataParcelas($boletosTitulos);
	    		
	    		// Gerar parcelas, dos titulos formatados com os devidos campos -> contrato por contrato
	    		foreach ($titulosParcelas AS $contrato => $parcelamento){
	    			$resultado = $this->dao->salvarParcelas($parcelamento);
	    			
	    			if ($resultado['erro'] == 1){
	    				$msg .= "As parcelas do contrato numero: $contrato não foram geradas.";
	    			}
	    		}
	    	}else {
	    		$msg .= "Nenhum título encontrado para ser gerado as parcelas. \n";
	    	}
	    	
	    	// Busca os titulos, que devem gerar o arquivo e/ou enviar aos clientes
	    	$boletosTitulosOficiais = $this->dao->boletosOficiais();

	    	if (count($boletosTitulosOficiais) > 0 || !empty($boletosTitulosOficiais)){
	    		
	    		$boletosTitulosOficiais = $this->formataParcelasContrato($boletosTitulosOficiais);
	    		
	    		// Loop dentro dos cliente
	    		foreach ($boletosTitulosOficiais AS $clioid => $dadosTitulo){
	    			
	    			$arquivosContrato = array();
	    			$prpoid			= null;
	    			$titoidParcela	= null;
	    			$titoidTodasParcelas = null;
	    			$contrato = null;
	    			
	    			// Loop dentro das parcelas de cada cliente -> Gerando os arquivos
	    			foreach ($dadosTitulo AS $key => $titulo){
	    				
	    				$arquivo = $this->boletoSecoAction->gerarBoletoSeco($titulo->tcetitoid, $titulo->tceconoid, "titulos_oficiais","CRON");
	    				
	    				//se o registro do titulo retornar Ok, o array recebe o boleto HTML gerado
	    				if(is_string($arquivo)){
	    					
	    					$arquivosContrato[] = $arquivo;
	    					
	    				}else{
	    
	    					$msgErroRegistro .= 'O titulo '. $titulo->tcetitoid .' de valor '. $titulo->titvl_titulo .', contrato '. $titulo->tceconoid .' do cliente '. $titulo->clinome .' possui um erro de registro --> '. $arquivo->mensagem;
	    					$msgErroRegistro .='</br>';
	    				}
	    				
	    				$prpoid = $titulo->prpoid;
	    				$contrato = $titulo->tceconoid;
	    				$titoidParcela = $titulo->tcetitoid;
	    				$titoidTodasParcelas[] = $titulo->tcetitoid;
	    				
	    			}
	    	
	    			//se tiver arquivo gerado envia para os clientes
	    			if(count($arquivosContrato) > 0){
	    				
	    				
			    			// Envia todos os arquivos referentes as parcelas do cliente
			    			$retEnvio = $this->boletoSecoAction->enviarParcelas($prpoid, $titoidParcela, $arquivosContrato);
			    			
			    			// Se houve sucesso no envio - Atualiza a tabela titulo_controle_envio
			    			if (empty($retEnvio['erro'])){
			    				
			    				$titoidTodasParcelas = implode(",", $titoidTodasParcelas);
			    				$this->boletoSecoAction->parcelasEnviadas($titoidTodasParcelas);
			    				
			    				$msg .= "Email com as parcelas enviado com sucesso para o contrato: $contrato. \n";
			    			
			    			}else {
			    				
			    				$erro = true;
			    				$msg_erro .= "Email com as parcelas não foi enviado para o contrato: $contrato. \n";
			    				$msg_erro .= "Erro retornado pelo serviço de envio: " . $retEnvio['msg'];
			    				$msg_erro .= "\n";
			    			}
	    			
	    			}//fim da veriicação se existe arquivo de boleto gerado
	    			
	    			if($msgErroRegistro != ''){
	    				$erro = true;
	    			}
	    			
	    			
	    		}// FIM LOOP DOS CLIENTES
	    	
	    		// Caso houve erro no envio de algum e-mail para os clientes
	    		// Envia para o e-mail parametrizado na tabela parametros_siggo o erro
	    		if ($erro == true){
	    			
	    			$this->parametrosSiggo = new PrnParametrosSiggo();
	    		
	    			$paramsPesquisa = array(
	    					'id_tipo_proposta'		=>	0,
	    					'id_subtipo_proposta'	=>	0,
	    					'id_tipo_contrato'		=>	0,
	    					'id_equipamento_classe'	=> 	0,
	    					'nome_parametro'		=> 	'EMAIL_RESP_ERRO_ENVIO'
	    			);
	    		
	    			$retornoValor = $this->parametrosSiggo->getValorParametros($paramsPesquisa);
	    		
	    			$email_dest = $retornoValor['valor'];
	    			
	    			$dadosContratoLayout = $this->boletoSecoAction->dadosProposta($prpoid);
	    			$layout = $this->boletoSecoAction->recuperaLayout($dadosContratoLayout);
	    			
	    			// Dados do email
	    			$assunto = "Problema Envio Parcelas para os Clientes";
	    			
	    			$servidor = $layout['server'];
	    			$arquivoBoleto = null;
	    			$email_copia = null;
	    			$email_copia_oculta = null;
	    			
	    			$email_desenv = "teste_desenv@sascar.com.br";
	    			 
	    			$corpo_email = "Ocorreu um erro no processo Cron de envio de emails com as parcelas da taxa de instalação. <br>";
	    			$corpo_email .= "Data: " . date("d-m-Y") . ". <br><br>";
	    			$corpo_email .= $msg_erro .'<BR>' ;
	    			
	    			$corpo_email .='Erro(s) no registro do titulo(s): <BR><BR>';
	    			
	    			$corpo_email .= $msgErroRegistro;
	    			
	    			$this->servicoEmail = new ServicoEnvioEmail();
	    			$this->servicoEmail->enviarEmail(
	    					$email_dest,
	    					$assunto,
	    					$corpo_email,
	    					$arquivoBoleto,
	    					$email_copia,
	    					$email_copia_oculta,
	    					$servidor,
	    					$email_desenv);
	    		}
	    	}
	    	
	    	return $msg . $msg_erro. $msgErroRegistro;
	    	
    	}catch (Exception $e){
    		return $e->getMessage();
    	}
    }
    
    /**
     * Formata os dados das parcelas
     * Agrupando as parcelas de acordo com o contrato
     * Gerando o numero da parcela em questão e a data do vencimento
     */
    private function formataParcelas($boletosTitulos){
    	
    	$titulosParcelas = array();

    	foreach ($boletosTitulos AS $titulo){
    		if ($titulo->ppagadesao_parcela > 0){
    			
    			for ($parcela = 2; $parcela <= $titulo->ppagadesao_parcela; $parcela++){
    				
    				$parcelamento['contrato'] = $titulo->tceconoid;
    				$parcelamento['valor_titulo'] = $titulo->titvl_titulo;
    				$parcelamento['clioid'] = $titulo->titclioid;
    				$parcelamento['titnfloid'] =  $titulo->titnfloid;
    				$parcelamento['no_parcela'] = $parcela;
    				$parcelamento['dt_vencimento'] = $this->dataVencimento($titulo->titdt_pagamento, $parcela);
    				$parcelamento['forma_cobranca'] = 1;

    				// Armazena em um array
    				$titulosParcelas[$titulo->tceconoid][] = $parcelamento;
    			}
    		}
    	}
    	
    	return $titulosParcelas;
    }
    
    /**
     * Formata os dados das parcelas
     * Agrupando as parcelas de acordo com o contrato
     */
    private function formataParcelasContrato($boletosTitulosOficiais){
    	$parcelasContrato = array();
    
    	foreach ($boletosTitulosOficiais AS $titulo){
    		$parcelasContrato[$titulo->tceconoid][] = $titulo;
    	}
    	return $parcelasContrato;
    }
    
    /**
     * Pega a data de vencimento para o titulo, de acordo com a data de pagamento da primeira parcela
     */
    private function dataVencimento($data_pagamento, $parcela){
    	$diasVencimento = 30 * ($parcela - 1);
    	
    	$data_venc = $data_pagamento;
    	$data_venc = date("d/m/Y", strtotime("$data_venc +$diasVencimento days"));

    	// Busca a data em um dia util para os 30 dias seguintes da parcela anterior
    	$data_venc = $this->boletoSecoAction->isUtil($data_venc);
    	$data_venc = explode("/",$data_venc);
    	$data_venc = array_reverse($data_venc);
		$data_venc = implode("-", $data_venc);
    	
    	return $data_venc;
    }
}

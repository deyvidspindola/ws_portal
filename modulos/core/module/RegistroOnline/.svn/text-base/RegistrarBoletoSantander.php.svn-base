<?php

namespace module\RegistroOnline;

use module\BoletoRegistrado\BoletoRegistradoModel;
use module\Parametro\ParametroCobrancaRegistrada;

define('__local_cert', _MODULEDIR_."core/module/RegistroOnline/Certificado/sascar.com.br.2017.pem");

class RegistrarBoletoSantander {

	const CODIGO_RETORNO_TICKET_OK = 0;
	const MSG_SOLICITACAO_TICKET_RETCODE_0 = 'Ticket validado ok';
	const MSG_SOLICITACAO_TICKET_RETCODE_1 = 'Erro, dados de entrada inválidos';
	const MSG_SOLICITACAO_TICKET_RETCODE_2 = 'Erro interno de criptografia';
	const MSG_SOLICITACAO_TICKET_RETCODE_3 = 'Erro, Ticket já utilizado anteriormente';
	const MSG_SOLICITACAO_TICKET_RETCODE_4 = 'Erro, Ticket gerado para outro sistema';
	const MSG_SOLICITACAO_TICKET_RETCODE_5 = 'Erro, Ticket expirado';
	const MSG_SOLICITACAO_TICKET_RETCODE_6 = 'Erro interno (dados)';
	const MSG_SOLICITACAO_TICKET_RETCODE_7 = 'Erro interno (timestamp)';

	const CODIGO_INCLUSAO_TITULO_OK = '00000';

	public $boleto;

	public function __construct(){}


	public function criarXMLSolicitacaoTicket($arr){


		$arrXml = array();

		foreach($arr as $key => $value){
			$arrXml[] = array(
				'key' => $key,
				'value' => utf8_encode($value)
			);
		}

		return array(
			'TicketRequest' => array(
				'dados' => $arrXml,
				'expiracao' => 100,
				'sistema' => 'YMB'
			)
		);

	}

	public function criarXMLInclusaoTitulo($arr){

		return array(
			'dto' => $arr
		);

	}

	public function getParametrosXMLSolicitacaoTicket(){

		$numeroDocumento = $this->boleto->getTipoDocumento() === BoletoRegistradoModel::TIPO_DOCUMENTO_CPF ? $this->boleto->getCpf() : $this->boleto->getCnpj();
		$tipoDesconto = $this->boleto->getValorDesconto() == 0 ? 0 : 1;
		$dataLimiteDesconto = !is_null($this->boleto->getDataLimiteDesconto()) ? $this->boleto->getDataLimiteDesconto('dmY') : '00000000';

		$parametrosXmlTicket = array(
			'CONVENIO.COD-BANCO' => '0033',
			'CONVENIO.COD-CONVENIO' => ParametroCobrancaRegistrada::getWebserviceCodigoConvenio(),
			'PAGADOR.TP-DOC' => $this->boleto->getTipoDocumento(),
			'PAGADOR.NUM-DOC' => $numeroDocumento,
			'PAGADOR.NOME' => $this->boleto->getNome(),
			'PAGADOR.ENDER' => $this->boleto->getEndereco(),
			'PAGADOR.BAIRRO' => $this->boleto->getBairro(),
			'PAGADOR.CIDADE' => $this->boleto->getCidade(),
			'PAGADOR.UF' => $this->boleto->getUf(),
			'PAGADOR.CEP' => $this->boleto->getCep(),
			'TITULO.NOSSO-NUMERO' => str_pad('', 13, '0', STR_PAD_LEFT),
			'TITULO.SEU-NUMERO' => $this->boleto->getSeuNumero(),
			'TITULO.DT-VENCTO' => $this->boleto->getDataVencimento('dmY'),
			'TITULO.DT-EMISSAO' => $this->boleto->getDataEmissao('dmY'),
			'TITULO.ESPECIE' => str_pad(BoletoRegistradoModel::CODIGO_ESPECIE_DM, 2, '0', STR_PAD_LEFT),
			'TITULO.VL-NOMINAL' => STR_PAD(number_format($this->boleto->getValorNominal(), 2, '',''), 13, '0', STR_PAD_LEFT),
			'TITULO.PC-MULTA' => number_format($this->boleto->co, 2, '', ''),
			'TITULO.QT-DIAS-MULTA' => STR_PAD($this->boleto->getQuantidadeDiasMulta(), 2, '0', STR_PAD_LEFT),
			'TITULO.PC-JURO' => number_format($this->boleto->getPercentualJuros(), 2, '', ''),
			'TITULO.TP-DESC' => $tipoDesconto,
			'TITULO.VL-DESC' => STR_PAD(number_format($this->boleto->getValorDesconto(), 2, '',''), 13, '0', STR_PAD_LEFT),
			'TITULO.DT-LIMI-DESC' => $dataLimiteDesconto,
			'TITULO.VL-ABATIMENTO' => STR_PAD(number_format($this->boleto->getValorAbatimento(), 2, '',''), 13, '0', STR_PAD_LEFT),
			'TITULO.TP-PROTESTO' => $this->boleto->getTipoProtesto(),
			'TITULO.QT-DIAS-PROTESTO' => STR_PAD($this->boleto->getQuantidadeDiasProtesto(), 2, '0', STR_PAD_LEFT),
			'TITULO.QT-DIAS-BAIXA' => $this->boleto->getQuantidadeDiasBaixa(),
			'MENSAGEM' => $this->boleto->getMensagem()
		);
		
//                    $fp=fopen(_SITEDIR_. 'arq_financeiro/log_XMLSolicitacaoTicket.txt',"w");
//                    fwrite($fp,print_r($parametrosXmlTicket));
//                    fclose($fp);

		return $parametrosXmlTicket;

	}

	public function getParametrosXMLInclusaoTitulo($ticket){

		$nsu = $this->boleto->getId() . date('dmY');

		if(_AMBIENTE_ === 'DESENVOLVIMENTO' || _AMBIENTE_ === 'HOMOLOGACAO' || _AMBIENTE_ === 'TESTE'){
			$nsu = 'TST' . $nsu;
			$tipoAmbiente = 'T';
		}else{
			$tipoAmbiente = 'P';
		}

//                    $fp2=fopen(_SITEDIR_. 'arq_financeiro/log_XMLInclusaoTitulo.txt',"w");
//		    $log_XMLSolicitacaoTicket2 =  array(
//			'dtNsu' => date('dmY'),
//			'estacao' => ParametroCobrancaRegistrada::getWebserviceSiglaEstacao(),
//			'nsu' => $nsu,
//			'ticket' => $ticket,
//			'tpAmbiente' => $tipoAmbiente
//		);
//
//                    
//                    fwrite($fp2,print_r($log_XMLSolicitacaoTicket2));
//                    fclose($fp2);
                
                
                
                
                
		return array(
			'dtNsu' => date('dmY'),
			'estacao' => ParametroCobrancaRegistrada::getWebserviceSiglaEstacao(),
			'nsu' => $nsu,
			'ticket' => $ticket,
			'tpAmbiente' => $tipoAmbiente
		);

	}

	public function getSoapOptions(){

		$options = array(
			'keep_alive' => false,
			'trace' => true,
			'local_cert' => __local_cert,
			'exceptions' => true,
			'cache_wsdl' => WSDL_CACHE_NONE
		);

		// Necessário para teste local
		// $options['proxy_host'] = 'proxy-gvt.sascar.local';
		// $options['proxy_port'] = 8080;


//            $rs = fopen(_SITEDIR_ . 'arq_financeiro/log_boleto.txt', 'a+');
//            fwrite($rs,  print_r($options, true));
//            fclose($rs);

                
                
		return $options;

	}

	public function stringErrorToArray($string){

		$arrError = array();

		$arrString = explode(PHP_EOL, $string);

		foreach($arrString as $error){

			$explodeError = explode('-', $error);

			$arrError[] = array(
				'codigo' => trim($explodeError[0]),
				'descricao' => trim($explodeError[1])
			);
		}

		return $arrError;

	}

	/*
		Retorna o código equivalente ao código CNAB a partir de um código de retorno XML;
		Consultar nota 14 da documentação XML e nota 41 da documentação CNAB;
	*/

	public function solicitarTicket($xmlTicket){
		
		$FILE  = fopen(_SITEDIR_ . 'arq_financeiro/log_xml.txt','a+');			
		$FILE2 = fopen(_SITEDIR_ . 'arq_financeiro/log_res.txt','a+');			
		$objData1 = serialize($xmlTicket);
//		$objData2 = serialize($this->getSoapOptions());

                
                
                
		try{
			$soapClient = new \SoapClient(ParametroCobrancaRegistrada::getWebserviceTicket(), $this->getSoapOptions());
		}catch(\Exception $e){
			throw new \Exception("Falha de comunicação com a instituição financeira.");
		}

		$res = $soapClient->create($xmlTicket);
		$objData2 = serialize($res);

		$retCode = $res->TicketResponse->retCode;

		if($retCode !== 0){
			$msg = constant("self::MSG_SOLICITACAO_TICKET_RETCODE_{$retCode}");
			throw new \Exception($msg, $retCode);			
		}
		
		fwrite($FILE,$objData1);
		fclose($FILE);	

		fwrite($FILE2,$objData2);
		fclose($FILE2);	


		return $res->TicketResponse->ticket;

	}

	public function incluirTitulo($xmlInclusao){

		$FILE = fopen(_SITEDIR_ . 'arq_financeiro/log_xml_inclusao.txt','a+');			
		$objData1 = serialize($xmlInclusao);
		$objData2 = serialize($this->getSoapOptions());
		

		try{
			$soapClient = new \SoapClient(ParametroCobrancaRegistrada::getWebserviceCobranca(), $this->getSoapOptions());
		}catch(\Exception $e){
			throw new \Exception("Falha de comunicação com a instituição financeira.");
		}

		$res = $soapClient->registraTitulo($xmlInclusao);
		$objData3 = serialize($res);

		$arrErrors = $this->stringErrorToArray($res->return->descricaoErro);

		if(!empty($arrErrors) && $arrErrors[0]['codigo'] !== self::CODIGO_INCLUSAO_TITULO_OK){

			$descricaoErro = isset($arrErrors[0]['descricao']) ? $arrErrors[0]['descricao'] : "Falha na inclusão de título. Erro não identificado.";
			$codigoErro = isset($arrErrors[0]['codigo']) ? $arrErrors[0]['codigo'] : 99;

			throw new \Exception($descricaoErro, $codigoErro);
			
		}
		fwrite($FILE,'inclusao de titulo');
		fwrite($FILE,$objData1);
		fwrite($FILE,$objData2);
		fwrite($FILE,$objData3);
		fclose($FILE);	

		return $res->return;

	}

	public function registrarBoleto($boleto){

		$this->boleto = $boleto;
		$parametrosXmlSolicitacaoTicket = $this->getParametrosXMLSolicitacaoTicket();
		$xmlSolicitacaoTicket = $this->criarXMLSolicitacaoTicket($parametrosXmlSolicitacaoTicket);
                
//                $fp33 = fopen('/tmp/registraboleto_'.date('d-m-Y').'.log','a+');
//
//		fwrite($fp33,print_r($xmlSolicitacaoTicket, TRUE));
//		fclose($fp33);

		$ticket = $this->solicitarTicket($xmlSolicitacaoTicket);
		
		$parametrosXmlInclusaoTitulo = $this->getParametrosXMLInclusaoTitulo($ticket);
		
		$xmlInclusaoTitulo = $this->criarXMLInclusaoTitulo($parametrosXmlInclusaoTitulo);
		

		$titulo = $this->incluirTitulo($xmlInclusaoTitulo);
		
		return $titulo;

	}

}
<?php

namespace module\Parametro;

use infra\ParametroDAO as ParametroDAO;

class ParametroCobrancaRegistrada  {
	
	private static $parametroDAO;

	public static function getDAO(){
		if(empty(self::$parametroDAO)){
			self::$parametroDAO = new ParametroDAO('COBRANCA_REGISTRADA');
		}
		return self::$parametroDAO;
	}

	public static function getCodigosMovimentoEnvioParaRegistro(){
		return array_map("intval", explode(',', self::getDAO()->getParametro('COD_MOVIMENTO_ENVIO_PARA_REGISTRO')));
	}

	public static function getCodigosMovimentoNaoPermiteAlteracao(){
		return array_map("intval", explode(',', self::getDAO()->getParametro('COD_MOVIMENTO_NAO_PERMITE_ATERACAO')));
	}

	public static function getCodigosMovimentoPermiteAlteracao(){
		return array_map("intval", explode(',', self::getDAO()->getParametro('COD_MOVIMENTO_PERMITE_ATERACAO')));
	}

	public static function getCodigosMovimentoRegistrado(){
		return array_map("intval", explode(',', self::getDAO()->getParametro('COD_MOVIMENTO_REGISTRADO')));
	}

	public static function getCodigosMovimentoRemessaEnvio(){
		return array_map("intval", explode(',', self::getDAO()->getParametro('COD_MOVIMENTO_REMESSA_ENVIO')));
	}

	public static function getCodigosMovimentoRetornoAceito(){
		return array_map("intval", explode(',', self::getDAO()->getParametro('COD_MOVIMENTO_RETORNO_ACEITO')));
	}

	public static function getCodigosMovimentoRetornoBaixa(){
		return array_map("intval", explode(',', self::getDAO()->getParametro('COD_MOVIMENTO_RETORNO_BAIXA')));
	}

	public static function getCodigosMovimentoRetornoLiquidacao(){
		return array_map("intval", explode(',', self::getDAO()->getParametro('COD_MOVIMENTO_RETORNO_LIQUIDACAO')));
	}

	public static function getCodigosMovimentoRetornoRejeitado(){
		return array_map("intval", explode(',', self::getDAO()->getParametro('COD_MOVIMENTO_RETORNO_REJEITADO')));
	}

	public static function getNumeroDiasBaixaDevolucao(){
		return (int)self::getDAO()->getParametro('DIAS_BAIXA_DEVOLUCAO');
	}

	public static function getNumeroDiasBaixaDevolucaoBoletoSeco(){
		return (int)self::getDAO()->getParametro('DIAS_BAIXA_DEVOLUCAO_BOLETO_SECO');
	}

	public static function getFormasCobrancaParaRegistro(){
		return array_map("intval", explode(',', self::getDAO()->getParametro('FORMAS_COBRANCA_PARA_REGISTRO')));
	}

	public static function getInstrucoesBoleto(){
		return self::getDAO()->getParametro('INSTRUCOES_BOLETO');
	}

	public static function getInstrucoesBoletoSeco(){
		return self::getDAO()->getParametro('INSTRUCOES_BOLETO_SECO');
	}

	public static function getIPAFTProducao(){
		return self::getDAO()->getParametro('IP_AFT_PRODUCAO');
	}

	public static function getIPAFTTeste(){
		return self::getDAO()->getParametro('IP_AFT_TESTE');
	}

	public static function getNumeroMaximoTentativasPagamentoCartaoCredito(){
		return (int)self::getDAO()->getParametro('NUM_MAX_TENTATIVAS_PGTO_CARTAO_CREDITO');
	}

	public static function getNumeroMaximoTentativasPagamentoDebitoAutomatico(){
		return (int)self::getDAO()->getParametro('NUM_MAX_TENTATIVAS_PGTO_DEBITO_AUTOMATICO');
	}

	public static function getPastaArquivoRemessa(){
		return self::getDAO()->getParametro('PASTA_ARQUIVO_REMESSA');
	}

	public static function getPercentoJurosAoDia(){
		return (float)self::getDAO()->getParametro('PERCENTO_JUROS_AO_DIA');
	}

	public static function getPercentoJurosAoMes(){
		return (float)self::getDAO()->getParametro('PERCENTO_JUROS_AO_MES');
	}

	public static function getPercentoMultaAposVencer(){
		return (float)self::getDAO()->getParametro('PERCENTO_MULTA_APOS_VENCER');
	}

	public static function getPrazosFebraban(){
		return self::getDAO()->getParametro('PRAZOS_FEBRABAN');
	}

	public static function getSenhaAFTProducao(){
		return self::getDAO()->getParametro('SENHA_AFT_PRODUCAO');
	}

	public static function getSenhaAFTTeste(){
		return self::getDAO()->getParametro('SENHA_AFT_TESTE');
	}

	public static function getUsuarioAFTProducao(){
		return self::getDAO()->getParametro('USUARIO_AFT_PRODUCAO');
	}

	public static function getUsuarioAFTTeste(){
		return self::getDAO()->getParametro('USUARIO_AFT_TESTE');
	}

	public static function getWebserviceCobranca(){
		return self::getDAO()->getParametro('WEBSERVICE_COBRANCA');
	}

	public static function getWebserviceCodigoConvenio(){
		return self::getDAO()->getParametro('WEBSERVICE_COD_CONVENIO');
	}
	
	public static function getWebserviceSiglaEstacao(){
		return self::getDAO()->getParametro('WEBSERVICE_SIGLA_ESTACAO');
	}

	public static function getWebserviceTicket(){
		return self::getDAO()->getParametro('WEBSERVICE_TICKET');
	}

	public static function getCodigoTransmissaoCnab(){
		return self::getDAO()->getParametro('COD_TRANSMISSAO_CNAB');
	}

	public static function getCaixaPostalAftInbox(){
		return explode(',', self::getDAO()->getParametro('CAIXA_POSTAL_AFT_INBOX'));
	}

	public static function getCaixaPostalAftOutbox(){
		return self::getDAO()->getParametro('CAIXA_POSTAL_AFT_OUTBOX');
	}

	public static function getValorMinimoRegistroBoletoFebraban(){
		return (float)self::getDAO()->getParametro('VALOR_MINIMO_REGISTRO_BOLETO_FEBRABAN');
	}
	
	public static function getInstrucaoBoletoVencimentoSantander(){
		return self::getDAO()->getParametro('INSTRUCAO_BOLETO_VENCIMENTO_SANTANDER');
	}
	
	public static function getInstrucaoBoletoVencimentoOutrosBancos(){
		return self::getDAO()->getParametro('INSTRUCAO_BOLETO_VENCIMENTO_OUTROS_BANCOS');
	}
	
	public static function getInstrucaoBoletoMoraJuros(){
		return self::getDAO()->getParametro('INSTRUCAO_BOLETO_MORA_JUROS');
	}
	
	public static function getInstrucaoBoletoJuros(){
		return self::getDAO()->getParametro('INSTRUCAO_BOLETO_JUROS');
	}
	
	public static function getInstrucaoBoletoSegundaVia(){
		return self::getDAO()->getParametro('INSTRUCAO_BOLETO_SEGUNDA_VIA');
	}

	public static function getCodigoTransmissaoXml(){
		return self::getDAO()->getParametro('COD_TRANSMISSAO_XML');
	}

	public static function getCaixaPostalAftOutboxCnab(){
		return self::getDAO()->getParametro('CAIXA_POSTAL_AFT_OUTBOX_CNAB');
	}

	public static function getCaixaPostalAftOutboxXml(){
		return self::getDAO()->getParametro('CAIXA_POSTAL_AFT_OUTBOX_XML');
	}
        
        public static function getProcessamentoCobrRegistradaAutomatico() {
        return (self::getDAO()->getParametro('PROCESSAMENTO_COBR_REGISTRADA_AUTOMATICO')) == 'true' ? true : false;
    }

}
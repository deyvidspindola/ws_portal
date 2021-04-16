<?php

namespace module\TituloCobranca;

use infra\Validacao,
    module\TituloCobranca\TituloCobrancaDAO AS DAO,
    infra\Helper\Mascara;

use module\Parametro\ParametroIntegracaoTotvs;

class TituloCobrancaModel{
    // Atributos
    public $prpDAO; // Acesso a dados da Proposta
    public $tituloCobrancaDAO; // Acesso a dados do Contrato

    // Campos no BD taxa de instalação proposta_pagamento
    private $taxaInstalacaoFieldList = array('ppagadesao', 'ppagadesao_parcela', 'taxaInstalacaoParcelamento');

    const TIPO_TITULO = 1;
    const TIPO_TITULO_RETENCAO = 2;
    const TIPO_TITULO_CONSOLIDADO = 3;
    const TIPO_EVENTO_ENTRADA_CONFIRMADA = 25;
    const TIPO_EVENTO_ENTRADA_TITULO = 11;
    const TIPO_EVENTO_PEDIDO_BAIXA = 12;
    const TIPO_EVENTO_BAIXA = 30;

    public function __construct() {
	    $this->tituloCobrancaDAO = new DAO();
    }


	public function insertTituloRetencao($prpoid=0, $usuoid=0, $clioid=0, $numContratos=array(), $dadosTaxa=array()){
		return $this->tituloCobrancaDAO->insertTituloRetencao($prpoid, $usuoid, $clioid, $numContratos, $dadosTaxa);
	}


	public function updateNossoNumeroTituloRetencao($titoid=0, $nossonum_com_DV=0){
		return $this->tituloCobrancaDAO->updateNossoNumeroTituloRetencao($titoid, $nossonum_com_DV);
	}


	public function insertTituloControle($titoid=0, $connoid=0){
		return $this->tituloCobrancaDAO->insertTituloControle($titoid, $connoid);
	}

	public function insertTitulo($prpoid=0, $usuoid=0, $clioid=0, $numContratos=array(), $dadosTaxa=array()){
		return $this->tituloCobrancaDAO->insertTitulo($prpoid, $usuoid, $clioid, $numContratos, $dadosTaxa);
	}

	public function rollbackTitulo($retTitoid=0){
		return $this->tituloCobrancaDAO->rollbackTitulo($retTitoid);
	}

    public function insertPagamentoParceladoHistorico($clioid, $titoid, $resposta){
        return $this->tituloCobrancaDAO->insertPagamentoParceladoHistorico($clioid, $titoid, $resposta);
    }

    public function getTipoTitulo($titoid){
        return $this->tituloCobrancaDAO->getDadosTitulo($titoid);
    }

    public static function getTituloById($tituloId)
    {

        $tituloCobrancaDAO = new DAO();
        $tipoTitulo = $tituloCobrancaDAO->getDadosTitulo($tituloId);

        $arrayTituloIndiceUnico = array();
        switch ($tipoTitulo) {
            case self::TIPO_TITULO_RETENCAO:
                $arrayTitulo = $tituloCobrancaDAO->getTituloRetencaoById($tituloId);
                $arrayTituloIndiceUnico['tituloId'] = $arrayTitulo['titoid'];
                $arrayTituloIndiceUnico['dataInclusao'] = $arrayTitulo['titdt_inclusao'];
                $arrayTituloIndiceUnico['usuarioInclusao'] = null;
                $arrayTituloIndiceUnico['notaFiscal'] = $arrayTitulo['titnfloid'];
                $arrayTituloIndiceUnico['dataReferencia'] = $arrayTitulo['titdt_referencia'];
                $arrayTituloIndiceUnico['dataVencimento'] = $arrayTitulo['titdt_vencimento'];
                $arrayTituloIndiceUnico['numeroParcela'] = $arrayTitulo['titno_parcela'];
                $arrayTituloIndiceUnico['valorTitulo'] = $arrayTitulo['titvl_titulo_retencao'];
                $arrayTituloIndiceUnico['valorPagamento'] = $arrayTitulo['titvl_pagamento'];
                $arrayTituloIndiceUnico['valorDesconto'] = $arrayTitulo['titvl_desconto'];
                $arrayTituloIndiceUnico['valorAcrescimo'] = $arrayTitulo['titvl_acrescimo'];
                $arrayTituloIndiceUnico['valorJuros'] = $arrayTitulo['titvl_juros'];
                $arrayTituloIndiceUnico['valorMulta'] = $arrayTitulo['titvl_multa'];
                $arrayTituloIndiceUnico['valorTarifaBanco'] = $arrayTitulo['titvl_tarifa_banco'];
                $arrayTituloIndiceUnico['dataPagamento'] = $arrayTitulo['titdt_pagamento'];
                $arrayTituloIndiceUnico['dataCredito'] = $arrayTitulo['titdt_credito'];
                $arrayTituloIndiceUnico['dataCancelamento'] = $arrayTitulo['titdt_cancelamento'];
                $arrayTituloIndiceUnico['observacaoCancelamento'] = $arrayTitulo['titobs_cancelamento'];
                $arrayTituloIndiceUnico['recebimento'] = $arrayTitulo['titrecebimento'];
                $arrayTituloIndiceUnico['observacaoRecebimento'] = $arrayTitulo['titobs_recebimento'];
                $arrayTituloIndiceUnico['observacaoDesconto'] = $arrayTitulo['titobs_desconto'];
                $arrayTituloIndiceUnico['dataAgendamento'] = $arrayTitulo['titdt_agendamento'];
                $arrayTituloIndiceUnico['dataAgenda'] = $arrayTitulo['titdt_agenda'];
                $arrayTituloIndiceUnico['formaCobranca'] = $arrayTitulo['titformacobranca'];
                $arrayTituloIndiceUnico['emissao'] = $arrayTitulo['titemissao'];
                $arrayTituloIndiceUnico['numeroRemessa'] = $arrayTitulo['titno_remessa'];
                $arrayTituloIndiceUnico['taxaAdministrativa'] = $arrayTitulo['tittaxa_administrativa'];
                $arrayTituloIndiceUnico['observacaoEstorno'] = $arrayTitulo['titobs_estorno'];
                $arrayTituloIndiceUnico['motivoDesconto'] = $arrayTitulo['titmdescoid'];
                $arrayTituloIndiceUnico['clienteId'] = $arrayTitulo['titclioid'];
                $arrayTituloIndiceUnico['devolucaoCheque'] = $arrayTitulo['titdev_cheque'];
                $arrayTituloIndiceUnico['configuracaoBanco'] = $arrayTitulo['titcfbbanco'];
                $arrayTituloIndiceUnico['autorizacaoCartao'] = $arrayTitulo['titautoriz_cartao'];
                $arrayTituloIndiceUnico['codigoBancoCheque'] = $arrayTitulo['titbanco_cheque'];
                $arrayTituloIndiceUnico['agenciaCheque'] = $arrayTitulo['titagencia_cheque'];
                $arrayTituloIndiceUnico['numeroCheque'] = $arrayTitulo['titno_cheque'];
                $arrayTituloIndiceUnico['numeroCartao'] = $arrayTitulo['titno_cartao'];
                $arrayTituloIndiceUnico['valorIr'] = $arrayTitulo['titvl_ir'];
                $arrayTituloIndiceUnico['valorPiscofins'] = $arrayTitulo['titvl_piscofins'];
                $arrayTituloIndiceUnico['impresso'] = $arrayTitulo['titimpresso'];
                $arrayTituloIndiceUnico['notaPromissoria'] = $arrayTitulo['titnota_promissoria'];
                $arrayTituloIndiceUnico['remessaFechamento'] = $arrayTitulo['titremessa_fechamento'];
                $arrayTituloIndiceUnico['movimentoRegularizacaoDeposito'] = $arrayTitulo['titmbcooid'];
                $arrayTituloIndiceUnico['movimentoRegularizacao'] = $arrayTitulo['titmbcooid_reg'];
                $arrayTituloIndiceUnico['valorIss'] = $arrayTitulo['titvl_iss'];
                $arrayTituloIndiceUnico['aux'] = $arrayTitulo['aux'];
                $arrayTituloIndiceUnico['dataProrrogacao'] = $arrayTitulo['titdt_prorrogacao'];
                $arrayTituloIndiceUnico['isCobrancaTerceirizada'] = $arrayTitulo['titcobr_terceirizada'];
                $arrayTituloIndiceUnico['dataEnvioCobrancaTerceirizada'] = $arrayTitulo['titcobrterc_envio'];
                $arrayTituloIndiceUnico['valorComissaoCobrancaTerceirizada'] = $arrayTitulo['titcobrterc_comissao'];
                $arrayTituloIndiceUnico['descontoOriginal'] = $arrayTitulo['titdesconto_orig'];
                $arrayTituloIndiceUnico['cobrancaTerceirizada'] = $arrayTitulo['titcteroid'];
                $arrayTituloIndiceUnico['taxaCobrancaTerceirizada'] = $arrayTitulo['tittaxa_cobrterc'];
                $arrayTituloIndiceUnico['credipar'] = $arrayTitulo['titseq_credipar'];
                $arrayTituloIndiceUnico['contaCorrente'] = $arrayTitulo['titconta_corrente'];
                $arrayTituloIndiceUnico['observacaoHistorico'] = $arrayTitulo['titobs_historico'];
                $arrayTituloIndiceUnico['usuarioAlteracao'] = $arrayTitulo['titusuoid_alteracao'];
                $arrayTituloIndiceUnico['numeroAvulso'] = $arrayTitulo['titno_avulso'];
                $arrayTituloIndiceUnico['cheque'] = $arrayTitulo['titchsoid'];
                $arrayTituloIndiceUnico['motivoInadimplencia'] = $arrayTitulo['titmotioid'];
                $arrayTituloIndiceUnico['tituloStatus'] = $arrayTitulo['tittitsoid'];
                $arrayTituloIndiceUnico['naoCobravel'] = $arrayTitulo['titnao_cobravel'];
                $arrayTituloIndiceUnico['valorComissaoTerceirizada'] = $arrayTitulo['titvlr_comissao_ch_terc'];
                $arrayTituloIndiceUnico['motctoid'] = $arrayTitulo['titmotctoid'];
                $arrayTituloIndiceUnico['valorDescontoRescisao'] = $arrayTitulo['titvl_desc_rescisao'];
                $arrayTituloIndiceUnico['movimentacaoBancaria'] = $arrayTitulo['titmbcooid_chdev'];
                $arrayTituloIndiceUnico['heranca'] = $arrayTitulo['tittitoid_heranca'];
                $arrayTituloIndiceUnico['tipoTitulo'] = $arrayTitulo['tittittoid'];
                $arrayTituloIndiceUnico['tituloPortador'] = $arrayTitulo['tittitpoid'];
                $arrayTituloIndiceUnico['faturamento'] = $arrayTitulo['titfaturamento_variavel'];
                $arrayTituloIndiceUnico['remessa'] = $arrayTitulo['titrtcroid'];
                $arrayTituloIndiceUnico['retorno'] = $arrayTitulo['titcod_retorno_cobr_reg'];
                $arrayTituloIndiceUnico['mensagemRetorno'] = $arrayTitulo['titmsg_retorno_cobr_reg'];
                $arrayTituloIndiceUnico['baixaAutomatica'] = $arrayTitulo['titbaixa_automatica_banco'];
                $arrayTituloIndiceUnico['numeroRegistroBanco'] = $arrayTitulo['titnumero_registro_banco'];
                $arrayTituloIndiceUnico['transacaoCartao'] = null;
                $arrayTituloIndiceUnico['transacaoRealizada'] = null;
                $arrayTituloIndiceUnico['tituloSubstituto'] = null;
                $arrayTituloIndiceUnico['faturaUnica'] = null;
                $arrayTituloIndiceUnico['codigoBarras'] = null;
                $arrayTituloIndiceUnico['linhaDigitavel'] = null;
                $arrayTituloIndiceUnico['caminhadoParceiro'] = null;
                $arrayTituloIndiceUnico['dataAlteracao'] = null;
                $arrayTituloIndiceUnico['codigoRetornoDebitoAutomatico'] = null;
                $arrayTituloIndiceUnico['tituloConsolidado'] = null;
                $arrayTituloIndiceUnico['valorJurosDesconto'] = null;
                $arrayTituloIndiceUnico['valorMultaDesconto'] = null;
                $arrayTituloIndiceUnico['valorDesconto'] = null;
                $arrayTituloIndiceUnico['motivoAlteracaoDataVencimento'] = null;
                $arrayTituloIndiceUnico['tipoEventoTitulo'] = $arrayTitulo['tittpetoid'];
                $arrayTituloIndiceUnico['arquivoBoletagem'] = null;
                $arrayTituloIndiceUnico['valorRecalculado'] = null;
                $arrayTituloIndiceUnico['natureza'] = $arrayTitulo['titnatureza'];
                $arrayTituloIndiceUnico['serie'] = $arrayTitulo['titserie'];
                $arrayTituloIndiceUnico['tipoBoleto'] = $arrayTitulo['tittboid'];
                break;

            case self::TIPO_TITULO_CONSOLIDADO:
                $arrayTitulo = $tituloCobrancaDAO->getTituloConsolidadoById($tituloId);

                $arrayTituloIndiceUnico['tituloId'] = $arrayTitulo['titcoid'];
                $arrayTituloIndiceUnico['dataInclusao'] = $arrayTitulo['titcdt_inclusao'];
                $arrayTituloIndiceUnico['usuarioInclusao'] = $arrayTitulo['titcusuoid_inclusao'];
                $arrayTituloIndiceUnico['notaFiscal'] = null;
                $arrayTituloIndiceUnico['dataReferencia'] = null;
                $arrayTituloIndiceUnico['dataVencimento'] = $arrayTitulo['titcdt_vencimento'];
                $arrayTituloIndiceUnico['numeroParcela'] = null;
                $arrayTituloIndiceUnico['valorTitulo'] = $arrayTitulo['titcvl_titulo'];
                $arrayTituloIndiceUnico['valorPagamento'] = $arrayTitulo['titcvl_pagamento'];
                $arrayTituloIndiceUnico['valorDesconto'] = $arrayTitulo['titcvl_desconto'];
                $arrayTituloIndiceUnico['valorAcrescimo'] = null;
                $arrayTituloIndiceUnico['valorJuros'] = $arrayTitulo['titcvl_juros'];
                $arrayTituloIndiceUnico['valorMulta'] = $arrayTitulo['titcvl_multa'];
                $arrayTituloIndiceUnico['valorTarifaBanco'] = null;
                $arrayTituloIndiceUnico['dataPagamento'] = $arrayTitulo['titcdt_pagamento'];
                $arrayTituloIndiceUnico['dataCredito'] = $arrayTitulo['titcdt_credito'];
                $arrayTituloIndiceUnico['dataCancelamento'] = $arrayTitulo['titcdt_cancelamento'];
                $arrayTituloIndiceUnico['observacaoCancelamento'] = $arrayTitulo['titcobs_cancelamento'];
                $arrayTituloIndiceUnico['recebimento'] = null;
                $arrayTituloIndiceUnico['observacaoRecebimento'] = null;
                $arrayTituloIndiceUnico['observacaoDesconto'] = null;
                $arrayTituloIndiceUnico['dataAgendamento'] = null;
                $arrayTituloIndiceUnico['dataAgenda'] = null;
                $arrayTituloIndiceUnico['formaCobranca'] = $arrayTitulo['titcformacobranca'];
                $arrayTituloIndiceUnico['emissao'] = $arrayTitulo['titcemissao'];
                $arrayTituloIndiceUnico['numeroRemessa'] = null;
                $arrayTituloIndiceUnico['taxaAdministrativa'] = null;
                $arrayTituloIndiceUnico['observacaoEstorno'] = null;
                $arrayTituloIndiceUnico['motivoDesconto'] = null;
                $arrayTituloIndiceUnico['clienteId'] = $arrayTitulo['titcclioid'];;
                $arrayTituloIndiceUnico['devolucaoCheque'] = null;
                $arrayTituloIndiceUnico['configuracaoBanco'] = $arrayTitulo['titccfbbanco'];
                $arrayTituloIndiceUnico['autorizacaoCartao'] = null;
                $arrayTituloIndiceUnico['codigoBancoCheque'] = null;
                $arrayTituloIndiceUnico['agenciaCheque'] = null;
                $arrayTituloIndiceUnico['numeroCheque'] = null;
                $arrayTituloIndiceUnico['numeroCartao'] = null;
                $arrayTituloIndiceUnico['valorIr'] = null;
                $arrayTituloIndiceUnico['valorPiscofins'] = null;
                $arrayTituloIndiceUnico['impresso'] = null;
                $arrayTituloIndiceUnico['notaPromissoria'] = null;
                $arrayTituloIndiceUnico['remessaFechamento'] = null;
                $arrayTituloIndiceUnico['movimentoRegularizacaoDeposito'] = null;
                $arrayTituloIndiceUnico['movimentoRegularizacao'] = null;
                $arrayTituloIndiceUnico['valorIss'] = null;
                $arrayTituloIndiceUnico['aux'] = null;
                $arrayTituloIndiceUnico['dataProrrogacao'] = null;
                $arrayTituloIndiceUnico['isCobrancaTerceirizada'] = null;
                $arrayTituloIndiceUnico['dataEnvioCobrancaTerceirizada'] = null;
                $arrayTituloIndiceUnico['valorComissaoCobrancaTerceirizada'] = null;
                $arrayTituloIndiceUnico['descontoOriginal'] = null;
                $arrayTituloIndiceUnico['cobrancaTerceirizada'] = null;
                $arrayTituloIndiceUnico['taxaCobrancaTerceirizada'] = null;
                $arrayTituloIndiceUnico['credipar'] = null;
                $arrayTituloIndiceUnico['contaCorrente'] = null;
                $arrayTituloIndiceUnico['observacaoHistorico'] = $arrayTitulo['titcobs_historico'];
                $arrayTituloIndiceUnico['usuarioAlteracao'] = $arrayTitulo['titcusuoid_alteracao'];
                $arrayTituloIndiceUnico['numeroAvulso'] = null;
                $arrayTituloIndiceUnico['cheque'] = null;
                $arrayTituloIndiceUnico['motivoInadimplencia'] = null;
                $arrayTituloIndiceUnico['tituloStatus'] = null;
                $arrayTituloIndiceUnico['naoCobravel'] = null;
                $arrayTituloIndiceUnico['valorComissaoTerceirizada'] = null;
                $arrayTituloIndiceUnico['motctoid'] = null;
                $arrayTituloIndiceUnico['valorDescontoRescisao'] = null;
                $arrayTituloIndiceUnico['movimentacaoBancaria'] = null;
                $arrayTituloIndiceUnico['heranca'] = null;
                $arrayTituloIndiceUnico['tipoTitulo'] = $arrayTitulo['titctittoid'];
                $arrayTituloIndiceUnico['tituloPortador'] = null;
                $arrayTituloIndiceUnico['faturamento'] = null;
                $arrayTituloIndiceUnico['remessa'] = $arrayTitulo['titcrtcroid'];
                $arrayTituloIndiceUnico['retorno'] = null;
                $arrayTituloIndiceUnico['mensagemRetorno'] = null;
                $arrayTituloIndiceUnico['baixaAutomatica'] = $arrayTitulo['titcbaixa_automatica_banco'];
                $arrayTituloIndiceUnico['numeroRegistroBanco'] = $arrayTitulo['titcnumero_registro_banco'];
                $arrayTituloIndiceUnico['transacaoCartao'] = null;
                $arrayTituloIndiceUnico['transacaoRealizada'] = null;
                $arrayTituloIndiceUnico['tituloSubstituto'] = null;
                $arrayTituloIndiceUnico['faturaUnica'] = null;
                $arrayTituloIndiceUnico['codigoBarras'] = null;
                $arrayTituloIndiceUnico['linhaDigitavel'] = null;
                $arrayTituloIndiceUnico['caminhadoParceiro'] = null;
                $arrayTituloIndiceUnico['dataAlteracao'] = null;
                $arrayTituloIndiceUnico['codigoRetornoDebitoAutomatico'] = null;
                $arrayTituloIndiceUnico['tituloConsolidado'] = null;
                $arrayTituloIndiceUnico['valorJurosDesconto'] = null;
                $arrayTituloIndiceUnico['valorMultaDesconto'] = null;
                $arrayTituloIndiceUnico['valorDesconto'] = $arrayTitulo['titcvl_desc_cobranca'];
                $arrayTituloIndiceUnico['motivoAlteracaoDataVencimento'] = null;
                $arrayTituloIndiceUnico['tipoEventoTitulo'] = $arrayTitulo['titctpetoid'];
                $arrayTituloIndiceUnico['arquivoBoletagem'] = $arrayTitulo['titcabooid'];
                $arrayTituloIndiceUnico['valorRecalculado'] = $arrayTitulo['titcvl_recalculado'];
                $arrayTituloIndiceUnico['natureza'] = null;
                $arrayTituloIndiceUnico['serie'] = null;
                $arrayTituloIndiceUnico['tipoBoleto'] = null;
                break;

            case self::TIPO_TITULO:
                $arrayTitulo = $tituloCobrancaDAO->getTituloById($tituloId);

                $arrayTituloIndiceUnico['tituloId'] = $arrayTitulo['titoid'];
                $arrayTituloIndiceUnico['dataInclusao'] = $arrayTitulo['titdt_inclusao'];
                $arrayTituloIndiceUnico['usuarioInclusao'] = null;
                $arrayTituloIndiceUnico['notaFiscal'] = $arrayTitulo['titnfloid'];
                $arrayTituloIndiceUnico['dataReferencia'] = $arrayTitulo['titdt_referencia'];
                $arrayTituloIndiceUnico['dataVencimento'] = $arrayTitulo['titdt_vencimento'];
                $arrayTituloIndiceUnico['numeroParcela'] = $arrayTitulo['titno_parcela'];
                $arrayTituloIndiceUnico['valorTitulo'] = $arrayTitulo['titvl_titulo'];
                $arrayTituloIndiceUnico['valorPagamento'] = $arrayTitulo['titvl_pagamento'];
                $arrayTituloIndiceUnico['valorDesconto'] = $arrayTitulo['titvl_desconto'];
                $arrayTituloIndiceUnico['valorAcrescimo'] = $arrayTitulo['titvl_acrescimo'];
                $arrayTituloIndiceUnico['valorJuros'] = $arrayTitulo['titvl_juros'];
                $arrayTituloIndiceUnico['valorMulta'] = $arrayTitulo['titvl_multa'];
                $arrayTituloIndiceUnico['valorTarifaBanco'] = $arrayTitulo['titvl_tarifa_banco'];
                $arrayTituloIndiceUnico['dataPagamento'] = $arrayTitulo['titdt_pagamento'];
                $arrayTituloIndiceUnico['dataCredito'] = $arrayTitulo['titdt_credito'];
                $arrayTituloIndiceUnico['dataCancelamento'] = $arrayTitulo['titdt_cancelamento'];
                $arrayTituloIndiceUnico['observacaoCancelamento'] = $arrayTitulo['titobs_cancelamento'];
                $arrayTituloIndiceUnico['recebimento'] = $arrayTitulo['titrecebimento'];
                $arrayTituloIndiceUnico['observacaoRecebimento'] = $arrayTitulo['titobs_recebimento'];
                $arrayTituloIndiceUnico['observacaoDesconto'] = $arrayTitulo['titobs_desconto'];
                $arrayTituloIndiceUnico['dataAgendamento'] = $arrayTitulo['titdt_agendamento'];
                $arrayTituloIndiceUnico['dataAgenda'] = $arrayTitulo['titdt_agenda'];
                $arrayTituloIndiceUnico['formaCobranca'] = $arrayTitulo['titformacobranca'];
                $arrayTituloIndiceUnico['emissao'] = $arrayTitulo['titemissao'];
                $arrayTituloIndiceUnico['numeroRemessa'] = $arrayTitulo['titno_remessa'];
                $arrayTituloIndiceUnico['taxaAdministrativa'] = $arrayTitulo['tittaxa_administrativa'];
                $arrayTituloIndiceUnico['observacaoEstorno'] = $arrayTitulo['titobs_estorno'];
                $arrayTituloIndiceUnico['motivoDesconto'] = $arrayTitulo['titmdescoid'];
                $arrayTituloIndiceUnico['clienteId'] = $arrayTitulo['titclioid'];
                $arrayTituloIndiceUnico['devolucaoCheque'] = $arrayTitulo['titdev_cheque'];
                $arrayTituloIndiceUnico['configuracaoBanco'] = $arrayTitulo['titcfbbanco'];
                $arrayTituloIndiceUnico['autorizacaoCartao'] = $arrayTitulo['titautoriz_cartao'];
                $arrayTituloIndiceUnico['codigoBancoCheque'] = $arrayTitulo['titbanco_cheque'];
                $arrayTituloIndiceUnico['agenciaCheque'] = $arrayTitulo['titagencia_cheque'];
                $arrayTituloIndiceUnico['numeroCheque'] = $arrayTitulo['titno_cheque'];
                $arrayTituloIndiceUnico['numeroCartao'] = $arrayTitulo['titno_cartao'];
                $arrayTituloIndiceUnico['valorIr'] = $arrayTitulo['titvl_ir'];
                $arrayTituloIndiceUnico['valorPiscofins'] = $arrayTitulo['titvl_piscofins'];
                $arrayTituloIndiceUnico['impresso'] = $arrayTitulo['titimpresso'];
                $arrayTituloIndiceUnico['notaPromissoria'] = $arrayTitulo['titnota_promissoria'];
                $arrayTituloIndiceUnico['remessaFechamento'] = $arrayTitulo['titremessa_fechamento'];
                $arrayTituloIndiceUnico['movimentoRegularizacaoDeposito'] = $arrayTitulo['titmbcooid'];
                $arrayTituloIndiceUnico['movimentoRegularizacao'] = $arrayTitulo['titmbcooid_reg'];
                $arrayTituloIndiceUnico['valorIss'] = $arrayTitulo['titvl_iss'];
                $arrayTituloIndiceUnico['aux'] = $arrayTitulo['aux'];
                $arrayTituloIndiceUnico['dataProrrogacao'] = $arrayTitulo['titdt_prorrogacao'];
                $arrayTituloIndiceUnico['isCobrancaTerceirizada'] = $arrayTitulo['titcobr_terceirizada'];
                $arrayTituloIndiceUnico['dataEnvioCobrancaTerceirizada'] = $arrayTitulo['titcobrterc_envio'];
                $arrayTituloIndiceUnico['valorComissaoCobrancaTerceirizada'] = $arrayTitulo['titcobrterc_comissao'];
                $arrayTituloIndiceUnico['descontoOriginal'] = $arrayTitulo['titdesconto_orig'];
                $arrayTituloIndiceUnico['cobrancaTerceirizada'] = $arrayTitulo['titcteroid'];
                $arrayTituloIndiceUnico['taxaCobrancaTerceirizada'] = $arrayTitulo['tittaxa_cobrterc'];
                $arrayTituloIndiceUnico['credipar'] = $arrayTitulo['titseq_credipar'];
                $arrayTituloIndiceUnico['contaCorrente'] = $arrayTitulo['titconta_corrente'];
                $arrayTituloIndiceUnico['observacaoHistorico'] = $arrayTitulo['titobs_historico'];
                $arrayTituloIndiceUnico['usuarioAlteracao'] = $arrayTitulo['titusuoid_alteracao'];
                $arrayTituloIndiceUnico['numeroAvulso'] = $arrayTitulo['titno_avulso'];
                $arrayTituloIndiceUnico['cheque'] = $arrayTitulo['titchsoid'];
                $arrayTituloIndiceUnico['motivoInadimplencia'] = $arrayTitulo['titmotioid'];
                $arrayTituloIndiceUnico['tituloStatus'] = $arrayTitulo['tittitsoid'];
                $arrayTituloIndiceUnico['naoCobravel'] = $arrayTitulo['titnao_cobravel'];
                $arrayTituloIndiceUnico['valorComissaoTerceirizada'] = $arrayTitulo['titvlr_comissao_ch_terc'];
                $arrayTituloIndiceUnico['motctoid'] = $arrayTitulo['titmotctoid'];
                $arrayTituloIndiceUnico['valorDescontoRescisao'] = $arrayTitulo['titvl_desc_rescisao'];
                $arrayTituloIndiceUnico['movimentacaoBancaria'] = $arrayTitulo['titmbcooid_chdev'];
                $arrayTituloIndiceUnico['heranca'] = $arrayTitulo['tittitoid_heranca'];
                $arrayTituloIndiceUnico['tipoTitulo'] = $arrayTitulo['tittittoid'];
                $arrayTituloIndiceUnico['tituloPortador'] = $arrayTitulo['tittitpoid'];
                $arrayTituloIndiceUnico['faturamento'] = $arrayTitulo['titfaturamento_variavel'];
                $arrayTituloIndiceUnico['remessa'] = $arrayTitulo['titrtcroid'];
                $arrayTituloIndiceUnico['retorno'] = $arrayTitulo['titcod_retorno_cobr_reg'];
                $arrayTituloIndiceUnico['mensagemRetorno'] = $arrayTitulo['titmsg_retorno_cobr_reg'];
                $arrayTituloIndiceUnico['baixaAutomatica'] = $arrayTitulo['titbaixa_automatica_banco'];
                $arrayTituloIndiceUnico['numeroRegistroBanco'] = $arrayTitulo['titnumero_registro_banco'];
                $arrayTituloIndiceUnico['transacaoCartao'] = $arrayTitulo['tittransacao_cartao'];
                $arrayTituloIndiceUnico['transacaoRealizada'] = $arrayTitulo['titccchoid'];
                $arrayTituloIndiceUnico['tituloSubstituto'] = $arrayTitulo['tittitoid_substituto'];
                $arrayTituloIndiceUnico['faturaUnica'] = $arrayTitulo['titfatura_unica'];
                $arrayTituloIndiceUnico['codigoBarras'] = $arrayTitulo['titcodigo_barras'];
                $arrayTituloIndiceUnico['linhaDigitavel'] = $arrayTitulo['titlinha_digitavel'];
                $arrayTituloIndiceUnico['caminhadoParceiro'] = $arrayTitulo['titencaminhado_parceiro'];
                $arrayTituloIndiceUnico['dataAlteracao'] = $arrayTitulo['titdt_alteracao'];
                $arrayTituloIndiceUnico['codigoRetornoDebitoAutomatico'] = $arrayTitulo['titcod_retorno_deb_automatico'];
                $arrayTituloIndiceUnico['tituloConsolidado'] = $arrayTitulo['tittitcoid'];
                $arrayTituloIndiceUnico['valorJurosDesconto'] = $arrayTitulo['titvl_juros_desc_cobranca'];
                $arrayTituloIndiceUnico['valorMultaDesconto'] = $arrayTitulo['titvl_multa_desc_cobranca'];
                $arrayTituloIndiceUnico['valorDesconto'] = $arrayTitulo['titvl_desc_cobranca'];
                $arrayTituloIndiceUnico['motivoAlteracaoDataVencimento'] = $arrayTitulo['tittmavoid'];
                $arrayTituloIndiceUnico['tipoEventoTitulo'] = $arrayTitulo['tittpetoid'];
                $arrayTituloIndiceUnico['arquivoBoletagem'] = null;
                $arrayTituloIndiceUnico['valorRecalculado'] = null;
                $arrayTituloIndiceUnico['natureza'] = null;
                $arrayTituloIndiceUnico['serie'] = null;
                $arrayTituloIndiceUnico['tipoBoleto'] = null;
                $arrayTituloIndiceUnico['tipoBoleto'] = null;
                $arrayTituloIndiceUnico['titpref_protheus'] = $arrayTitulo['titpref_protheus'];
                  
                break;
        }
        return !empty($arrayTituloIndiceUnico) ? (object) $arrayTituloIndiceUnico : false;
    }

    public static function atualizarStatusTitulo($tituloId, $status){

        $tituloCobrancaDAO = new DAO();
        $tipoTitulo = $tituloCobrancaDAO->getDadosTitulo($tituloId);

        return $tituloCobrancaDAO->atualizarStatusTitulo($tituloId, $tipoTitulo, $status);

    }

    public static function atualizarNumeroRegistroBanco($tituloId, $nossoNumero){

        $tituloCobrancaDAO = new DAO();
        $tipoTitulo = $tituloCobrancaDAO->getDadosTitulo($tituloId);

        return $tituloCobrancaDAO->atualizarNumeroRegistroBanco($tituloId, $tipoTitulo, $nossoNumero);

    }

    public static function isFormaCobrancaDebitoAutomatico($tituloId){
        $dao = new DAO();
        return $dao->isFormaCobrancaDebitoAutomatico($tituloId);
    }

    public static function isFormaCobrancaCartaoDeCredito($tituloId){
        $dao = new DAO();
        return $dao->isFormaCobrancaCartaoDeCredito($tituloId);
    }

    public static function isFormaCobrancaBoleto($tituloId){
        $dao = new DAO();
        return $dao->isFormaCobrancaBoleto($tituloId);
    }

    public static function freezingGoLiveTotvsAtivo()
    {
        return ParametroIntegracaoTotvs::getIntegracao('INTEGRACAO_FREEZING_GO_LIVE_TOTVS');
    }

    public static function exibirLink($tituloId){

        $isFormaCobrancaBoleto = self::isFormaCobrancaBoleto($tituloId);
        $titulo = self::getTituloById($tituloId);

        if($titulo->tipoEventoTitulo == self::TIPO_EVENTO_PEDIDO_BAIXA || $titulo->tipoEventoTitulo == self::TIPO_EVENTO_BAIXA){
            return false;
        }

        if($isFormaCobrancaBoleto){

            if (!in_array(intval($titulo->tipoEventoTitulo), array(self::TIPO_EVENTO_ENTRADA_CONFIRMADA, self::TIPO_EVENTO_ENTRADA_TITULO))) {
                return false;
            }

            if (!$titulo->dataPagamento) {
                return true;
            }

            return false;

        } else {

            if (self::isFormaCobrancaDebitoAutomatico($tituloId)) {
                return self::emPeriodoValidacaoDebitoAutomaticoExpirado($tituloId);
            } elseif (self::isFormaCobrancaCartaoDeCredito($tituloId)) {
                return self::emPeriodoValidacaoCartaoDeCreditoExpirado($tituloId);
            }

            return false;
        }
    }

    public static function registrarEventoTituloXML($tituloId, $codigoRetornoXML){

        $tituloCobrancaDAO = new DAO();

        $tipoEventoTituloId = $tituloCobrancaDAO->getTipoEventoTituloXML($codigoRetornoXML);

        if(!$tipoEventoTituloId){
            return false;
        }

        $usuarioId = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : 2750;

        $tituloCobrancaDAO->insertHistoricoOnlineTitulo($tituloId, $usuarioId, $tipoEventoTituloId);
        $tituloCobrancaDAO->insertEventoTitulo($tituloId, $tipoEventoTituloId, $codigoRetornoXML);

        return true;

    }

    public static function emPeriodoValidacaoDebitoAutomaticoExpirado($tituloId){
        $dao = new DAO();
        return $dao->emPeriodoValidacaoDebitoAutomaticoExpirado($tituloId);
    }

    public static function emPeriodoValidacaoCartaoDeCreditoExpirado($tituloId){
        $dao = new DAO();
        return $dao->emPeriodoValidacaoCartaoDeCreditoExpirado($tituloId);
    }

    public static function getNumeroNotaFiscalByNotaFiscalId($notaFiscalId){
        $dao = new DAO();
        return $dao->getNumeroNotaFiscalByNotaFiscalId($notaFiscalId);
    }

}

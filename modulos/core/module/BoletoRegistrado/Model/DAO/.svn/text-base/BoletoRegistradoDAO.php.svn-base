<?php

namespace module\BoletoRegistrado;

use infra\ComumDAO;
use module\Parametro\ParametroCobrancaRegistrada;
use module\BoletoRegistrado\BoletoRegistradoModel;

class BoletoRegistradoDAO extends ComumDAO {
    
	public function __construct(){
		parent::__construct();
	}

	public function getById($id){

		$sql = "SELECT * FROM titulo_boleto_registro WHERE tbreoid = $id LIMIT 1;";
		$this->queryExec($sql);
		return $this->getNumRows() > 0 ? $this->getAssoc() : null;

	}

	public function getByNossoNumero($nossoNumero){

		// Ã© apenas 1 realmente?

		$sql = "SELECT * FROM titulo_boleto_registro WHERE tbrenosso_numero = $nossoNumero LIMIT 1;";
		$this->queryExec($sql);
		return $this->getNumRows() > 0 ? $this->getAssoc() : null;

	}

	public function getByTitulo($tituloId){

		$sql = "SELECT * FROM titulo_boleto_registro WHERE tbretitoid = $tituloId;";
		$this->queryExec($sql);
		return $this->getNumRows() > 0 ? $this->getAssoc() : null;

	}

	public function atualizarStatus(
		$id,
		$valorEncargosRetorno,
		$valorDescontoRetorno,
		$valorAbatimentoRetorno,
		$valorIofRetorno,
		$valorPago,
		$valorLiquidoCreditado,
		$valorOutrasDespesas,
		$valorOutrosCreditos,
		$dataOcorrencia,
		$dataEfetivacaoCredito,
		$codigoOcorrenciaPagador,
		$dataOcorrenciaPagador,
		$valorOcorrenciaPagador,
		$complementoOcorrenciaPagador,
		$valorTarifas
	){

		$sql = "UPDATE titulo_boleto_registro SET
			tbrevl_encargos_retorno = $valorEncargosRetorno,
			tbrevl_desconto_retorno = $valorDescontoRetorno,
			tbrevl_abatimento_retorno = $valorAbatimentoRetorno,
			tbrevl_iof_retorno = $valorIofRetorno,
			tbrevl_pago = $valorPago,
			tbrevl_liquido_creditado  = $valorLiquidoCreditado,
			tbrevl_outras_despesas = $valorOutrasDespesas,
			tbrevl_outros_creditos = $valorOutrosCreditos,
			tbredt_ocorrencia = $dataOcorrencia,
			tbredt_efetivacao_credito = $dataEfetivacaoCredito,
			tbrecd_ocorrencia_pagador = $codigoOcorrenciaPagador,
			tbredt_ocorrencia_pagador = $dataOcorrenciaPagador,
			tbrevl_ocorrencia_pagador = $complementoOcorrenciaPagador,
			tbrecomplemento_ocorrencia_pagador = $complementoOcorrenciaPagador,
			tbrevl_tarifas = $valorTarifas
		WHERE tbreoid = $id;";
		//print($sql);
                
		$this->queryExec($sql);
		 
		return !!$this->getAffectedRows();

	}

	public function inserir(
		$tituloId,
		$codigoOrigem,
		$codigoBanco,
		$tipoDocumento,
		$cpf,
		$cnpj,
		$nome,
		$endereco,
		$bairro,
		$cidade,
		$uf,
		$cep,
		$dataVencimento,
		$dataEmissao,
		$especieTitulo,
		$valorNominal,
		$valorMulta,
		$numeroDiasMulta,
		$valorMora,
		$codigoDesconto,
		$valorDesconto,
		$dataDesconto,
		$valorAbatimento,
		$codProtesto,
		$numeroDiasProtesto,
		$numeroDiasBaixa,
		$mensagem,
		$valorFace,
		$valorDescontoNegociadoDescritivo,
		$valorAbatimentoDescritivo,
		$valorMoraNegociadaDescritivo,
		$valorOutrosAcrescimosDescritivo
	){
		$cpf = !is_null($cpf) ? "'$cpf'" : "Null";
		$cnpj = !is_null($cnpj) ? "'$cnpj'" : "Null";
		$tipoDocumento = !is_null($tipoDocumento) ? "'$tipoDocumento'" : "Null";
		$cep = !is_null($cep) ? "'$cep'" : "Null";
		$codigoDesconto = !is_null($codigoDesconto) ? "'$codigoDesconto'" : "Null";
		$codProtesto = !is_null($codProtesto) ? "'$codProtesto'" : "Null";
		$nome = substr($nome,0,40);
		$endereco = substr($endereco,0,40);
		$bairro = substr($bairro,0,30);
		$cidade = substr($cidade,0,20);
		$numeroDiasMulta = !is_null($numeroDiasMulta) ? $numeroDiasMulta : "null";
		$dataDesconto = !is_null($dataDesconto) ? "'$dataDesconto'" : "null";
		$mensagem = !is_null($mensagem) ? "'$mensagem'" : "null";

		$valorDescontoNegociadoDescritivo = !is_null($valorDescontoNegociadoDescritivo) ? $valorDescontoNegociadoDescritivo : "Null";
		$valorAbatimentoDescritivo = !is_null($valorAbatimentoDescritivo) ? $valorAbatimentoDescritivo : "Null";
		$valorMoraNegociadaDescritivo = !is_null($valorMoraNegociadaDescritivo) ? $valorMoraNegociadaDescritivo : "Null";
		$valorOutrosAcrescimosDescritivo = !is_null($valorOutrosAcrescimosDescritivo) ? $valorOutrosAcrescimosDescritivo : "Null";

		$sql = "INSERT INTO titulo_boleto_registro (
			tbretitoid,
			tbrecd_origem,
			tbrecod_banco,
			tbretipo_documento,
			tbrecpf,
			tbrecnpj,
			tbrenome,
			tbreendereco,
			tbrebairro,
			tbrecidade,
			tbreuf,
			tbrecep,
			tbredt_vencimento,
			tbredt_emissao,
			tbrecd_especie,
			tbrevl_nominal,
			tbrepct_multa,
			tbreqtd_dias_multa,
			tbrepct_juros,
			tbretp_desconto,
			tbrevl_desconto,
			tbredt_limite_desconto,
			tbrevl_abatimento,
			tbretp_protesto,
			tbreqtd_dias_protesto,
			tbreqtd_dias_baixa,
			tbremensagem,
			tbrecriado_em,
			tbrevl_face,
			tbrevl_desconto_negociado_descritivo,
			tbrevl_abatimento_descritivo,
			tbrevl_mora_negociada_descritivo,
			tbrevl_outros_acrescimos_descritivo
		) VALUES (
			$tituloId,
			$codigoOrigem,
			$codigoBanco,
			$tipoDocumento,
			$cpf,
			$cnpj,
			'$nome',
			'$endereco',
			'$bairro',
			'$cidade',
			'$uf',
			$cep,
			'$dataVencimento',
			'$dataEmissao',
			$especieTitulo,
			$valorNominal,
			$valorMulta,
			$numeroDiasMulta,
			$valorMora,
			$codigoDesconto,
			$valorDesconto,
			$dataDesconto,
			$valorAbatimento,
			$codProtesto,
			$numeroDiasProtesto,
			$numeroDiasBaixa,
			$mensagem,
			now(),
			$valorFace,
			$valorDescontoNegociadoDescritivo,
			$valorAbatimentoDescritivo,
			$valorMoraNegociadaDescritivo,
			$valorOutrosAcrescimosDescritivo
		) RETURNING tbreoid;";
		//print($sql);

		$this->queryExec($sql);
    	if($this->getNumRows() > 0){
			$resultado = $this->getAssoc();
			$retornoUpdate = $this->updateSeuNumero($resultado["tbreoid"]);
			if($retornoUpdate){
				return $resultado["tbreoid"];
			}else{
				throw new Exception("Ocorreu um erro ao atualizar o Seu Número", 1);
			}
    	} else{
    		throw new Exception("Ocorreu um erro ao inserir um novo boleto");    		
		}
	}

	public function atualizarInformacoesCNAB(
		$id,
		$nossoNumero,
		$codigoConvenio,
		$codigoMovimento,
		$valorEncargos,
		$valorDesconto,
		$valorAbatimento,
		$valorIOF,
		$valorPago,
		$valorLiquidoCreditado,
		$valorOutrasDespesas,
		$valorOutrosCreditos,
		$dataEfetivacaoCredito,
		$dataOcorrencia,
		$codigoOcorrenciaPagador,
		$dataOcorrenciaPagador,
		$valorOcorrenciaPagador,
		$complementoOcorrenciaPagador,
		$valorTarifas,
		$codigoBarras,
		$linhaDigitavel
	){

		$nossoNumero = !is_null($nossoNumero) ? "'$nossoNumero'" : "null";
		$codigoConvenio = !is_null($codigoConvenio) ? "'$codigoConvenio'" : "null";
		$codigoMovimento = !is_null($codigoMovimento) ? "'$codigoMovimento'" : "null";
		$valorEncargos = !is_null($valorEncargos) ? $valorEncargos : "null";
		$valorDesconto = !is_null($valorDesconto) ? $valorDesconto : "null";
		$valorAbatimento = !is_null($valorAbatimento) ? $valorAbatimento : "null";
		$valorIOF = !is_null($valorIOF) ? $valorIOF : "null";
		$valorPago = !is_null($valorPago) ? $valorPago : "null";
		$valorLiquidoCreditado = !is_null($valorLiquidoCreditado) ? $valorLiquidoCreditado : "null";
		$valorOutrasDespesas = !is_null($valorOutrasDespesas) ? $valorOutrasDespesas : "null";
		$valorOutrosCreditos = !is_null($valorOutrosCreditos) ? $valorOutrosCreditos : "null";
		$valorOcorrenciaPagador = !is_null($valorOcorrenciaPagador) ? $valorOcorrenciaPagador : "null";
		$valorTarifas = !is_null($valorTarifas) ? $valorTarifas : "null";
		$dataEfetivacaoCredito = !is_null($dataEfetivacaoCredito) ? "'$dataEfetivacaoCredito'" : "null";
		$dataOcorrencia = !is_null($dataOcorrencia) ? "'$dataOcorrencia'" : "null";
		$codigoOcorrenciaPagador = !is_null($codigoOcorrenciaPagador) ? $codigoOcorrenciaPagador : "null";
		$dataOcorrenciaPagador = !is_null($dataOcorrenciaPagador) ? "'$dataOcorrenciaPagador'" : "null";
		$complementoOcorrenciaPagador = !is_null($complementoOcorrenciaPagador) ? "'$complementoOcorrenciaPagador'" : "null";
		
		$codigoBarras = !is_null($codigoBarras) ? "'$codigoBarras'" : "null";
		$linhaDigitavel = !is_null($linhaDigitavel) ? "'$linhaDigitavel'" : "null";

		$sql = "UPDATE
							titulo_boleto_registro
						SET
							tbrenosso_numero = $nossoNumero,
							tbrecod_convenio = $codigoConvenio,
							tbrecd_movimento = $codigoMovimento,
							tbrevl_encargos_retorno = $valorEncargos,
							tbrevl_desconto_retorno = $valorDesconto,
							tbrevl_abatimento_retorno = $valorAbatimento,
							tbrevl_iof_retorno = $valorIOF,
							tbrevl_pago = $valorPago,
							tbrevl_liquido_creditado = $valorLiquidoCreditado,
							tbrevl_outras_despesas = $valorOutrasDespesas,
							tbrevl_outros_creditos = $valorOutrosCreditos,
							tbredt_efetivacao_credito = $dataEfetivacaoCredito,
							tbredt_ocorrencia = $dataOcorrencia,
							tbrecd_ocorrencia_pagador = $codigoOcorrenciaPagador,
							tbredt_ocorrencia_pagador = $dataOcorrenciaPagador,
							tbrevl_ocorrencia_pagador = $valorOcorrenciaPagador,
							tbrecomplemento_ocorrencia_pagador = $complementoOcorrenciaPagador,
							tbrevl_tarifas = $valorTarifas,
							tbrecd_barras = $codigoBarras,
							tbrelinha_digitavel = $linhaDigitavel
						WHERE
							tbreoid = $id;";

		//print($sql);
		return !!$this->queryExec($sql);

	}

	public function atualizarInformacoesXML(
		$id,
		$nossoNumero,
		$codigoConvenio,
		$codigoMovimento,
		$codigoBarras,
		$linhaDigitavel,
		$nsu
	){

		$nossoNumero = !empty($nossoNumero) ? $nossoNumero : "null";
		$codigoConvenio = !empty($codigoConvenio) ? $codigoConvenio : "null";
		$codigoBarras = !empty($codigoBarras) ? "'$codigoBarras'" : "null";
		$linhaDigitavel = !empty($linhaDigitavel) ? "'$linhaDigitavel'" : "null";
		$nsu = !empty($nsu) ? "'$nsu'" : "null";

		$sql = "
			UPDATE
				titulo_boleto_registro
			SET
				tbrenosso_numero = $nossoNumero,
				tbrecod_convenio = $codigoConvenio,
				tbrecd_movimento = $codigoMovimento,
				tbrecd_barras = $codigoBarras,
				tbrelinha_digitavel = $linhaDigitavel,
				tbrensu = $nsu
			WHERE
				tbreoid = $id;
		";
		//print($sql);

		return !!$this->queryExec($sql);

	}

	public function updateSeuNumero($seuNumero){
		$sql = "UPDATE titulo_boleto_registro SET tbreseu_numero = $seuNumero WHERE tbreoid = $seuNumero";
		$this->queryExec($sql);
    	if($this->getAffectedRows() > 0){
			return true;
    	} else{
    		return false;
		}
	}

	public function getUltimoBoletoValido($tituloId){
		$codigosMovimentoRegistrado = ParametroCobrancaRegistrada::getCodigosMovimentoRegistrado();
		$stringCodigoMovimentoRegistrados = "'".implode("','", array_map('strval', $codigosMovimentoRegistrado))."'";
	
		$sql = "SELECT tbreoid AS boletoid
				FROM titulo_boleto_registro 
				WHERE tbrecd_movimento in (". $stringCodigoMovimentoRegistrados .")
				AND tbretitoid = $tituloId
				ORDER BY tbreoid DESC
				LIMIT 1";

		$this->queryExec($sql);
		
		if ($this->getNumRows() > 0 ) {
			$result = $this->getAssoc();
			return  $result['boletoid'];
		} else {
			return null;
		}		
	}
	
	public function getStatusIntegracaoTotvsAtiva(){
		$sql = "SELECT pcsidescricao FROM parametros_configuracoes_sistemas_itens WHERE pcsipcsoid = 'INTEGRACAO_TOTVS' AND pcsioid = 'INTEGRACAO_ATIVA'";
		$this->queryExec($sql);
		$resultSet = $this->getAssoc();
		return $resultSet['pcsidescricao'];	
	}

	public function buscarBoletosFilaCancelamento($numRows = null, $origem = null){

		$sql = "
			SELECT
				*
			FROM
				titulo_boleto_registro
			WHERE
				tbresolicitacao_cancelamento = TRUE
				AND
				tbreenvio_cancelamento IS NOT TRUE
		";

		if(!empty($origem)){
			$sql .= "AND tbrecd_origem = $origem ";
		}

		if(!is_null($numRows)){
			$sql .= "LIMIT $numRows";
		}

		$this->queryExec($sql);

		return $this->getNumRows() > 0 ? $this->getAll() : array();

	}

	public function solicitarCancelamentoBoletosByTitulo($tituloId){

		$codigoMovimento = BoletoRegistradoModel::CODIGO_MOVIMENTO_ENTRADA_CONFIRMADA;

		$sql = "
			UPDATE
				titulo_boleto_registro
			SET
				tbresolicitacao_cancelamento = TRUE
			WHERE
				tbretitoid = $tituloId AND tbrecd_movimento = '$codigoMovimento' 
			RETURNING tbreoid
		";

		$this->queryExec($sql);
		//print($sql);

		return $this->getAll();

	}

	public function atualizarEnviadoParaCancelamento($boletoId){

		$sql = "
			UPDATE
				titulo_boleto_registro
			SET
				tbreenvio_cancelamento = TRUE
			WHERE
				tbreoid = $boletoId
		";

		$this->queryExec($sql);

		return !!$this->getAffectedRows();

	}

} 

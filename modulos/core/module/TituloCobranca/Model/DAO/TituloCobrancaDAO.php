<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Leandro A. Ivanaga <leandroivanaga@brq.com>
 * @version 25/11/2013
 * @since 25/11/2013
 * @package Core
 * @subpackage Classe de Acesso a Dados de Taxa de Instalação
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\TituloCobranca;

use infra\ComumDAO,
    infra\Helper\Mascara,
	module\TituloCobranca\TituloCobrancaModel,
	module\Parametro\ParametroCobrancaRegistrada;

class TituloCobrancaDAO extends ComumDAO{

    public function __construct() {
        parent::__construct();
    }


	/**
     * Insert na tabela titulo_retencao e titulo_retencao_item (BOLETO).
     *
     * @author Leandro A. Ivanaga <leandroivanaga@brq.com>
     * @version 25/11/2013
     * @param int $prpoid (ID da proposta)
	 * @param int $usuoid (ID do usuario)
	 * @param int $clioid (ID do cliente)
	 * @param array $numContratos (array associativo tipo chave -> valor, numero dos contratos)
     *     OBS-> campos obrigatórios do $numContratos[]:
     *     		int contrato -> numero do contrato
     * @param array $dadosTaxa (array associativo tipo chave -> valor, dados da taxa titulo_retencao, titulo_retencao_item)
     *     OBS-> campos obrigatórios do $dadosTaxa[]:
     *     	 float taxa_valor_total -> valor total do titulo ou valor total da parcela (tabela titulo_rentecao)
     *     	 float taxa_valor_item -> valor para cada item (tabela titulo_retencao_item)
     *     	 int taxa_qntd_parcelas -> quantidade total de parcelas
     *       int taxa_id_obrigacao -> ID da obrigação financeira
     *       string taxa_descricao_obrigacao -> descricao da obrigacao financeira
     *       int taxa_forma_pagamento -> ID da forma de pagamento
     *       string taxa_data_vencimento -> data de vencimento do titulo formato (dd-mm-YYYY, ex: 03-09-2014)
     *       int taxa_num_parcela -> numero da parcela em questão
     *     OBS-> campos NÃO obrigatórios do $dadosTaxa[]:
     *       N/A
     * @return mixed $idTitulo/false
     */
    public function insertTituloRetencao($prpoid=0, $usuoid=0, $clioid=0, $numContratos=array(), $dadosTaxa=array()){
    	// Insere na tabela titulo_retencao
		$sqlTitulo = "
				INSERT INTO titulo_retencao
					(
						titdt_inclusao,
						titclioid,
						titusuoid_alteracao,
						titformacobranca,
						titvl_titulo_retencao,
						titnatureza,
						tittboid,
						titdt_vencimento,
						titno_parcela,
						titserie
					)
					VALUES
					(
						NOW(),
						{$clioid},
						{$usuoid},
						{$dadosTaxa['taxa_forma_pagamento']},
						{$dadosTaxa['taxa_valor_total']},
						'{$dadosTaxa['taxa_descricao_obrigacao']}',
						{$dadosTaxa['taxa_tipo_boleto']},
						'{$dadosTaxa['taxa_data_vencimento']}',
						{$dadosTaxa['taxa_num_parcela']},
						'{$dadosTaxa['taxa_serie']}'
					)
					RETURNING titoid";

		$this->queryExec($sqlTitulo);
    	$resultSet = $this->getAssoc();

		if($this->getAffectedRows() < 1){
			return false;
		}
		$titoid = Mascara::inteiro($resultSet['titoid']);

    	// Insere na tabela titulo_retencao_item
		foreach ($numContratos as $contrato){

			$sqlTituloItem = "
					INSERT INTO titulo_retencao_item
						(
						titridt_cadastro,
						titrititoid,
						titriconoid,
						titriobroid,
						titrivl_item
						)
					VALUES
						(
						NOW(),
						{$titoid},
						{$contrato},
						{$dadosTaxa['taxa_id_obrigacao']},
						{$dadosTaxa['taxa_valor_item']}
					)";

			$this->queryExec($sqlTituloItem);
			if($this->getAffectedRows() < 1){
	            return false;
			}
		}

		return $titoid;
    }

	/**
     * Update na tabela titulo_retencao salvando o nosso numero gerado.
     *
     * @author Leandro A. Ivanaga <leandroivanaga@brq.com>
     * @version 25/11/2013
     * @param int $titoid (ID do titulo)
     * @param int $nossonum_com_DV (nosso numero gerado para o titulo)
     * @return mixed $idTitulo/false
     */
    public function updateNossoNumeroTituloRetencao($titoid=0, $nossonum_com_DV=0){
		// Atualiza o titulo com o nosso numetro gerado
		$sqlTituloNossoNumero = "
				UPDATE
					titulo_retencao
				SET
					titnumero_registro_banco = $nossonum_com_DV
				WHERE
					titoid = $titoid
				RETURNING titoid";

		$this->queryExec($sqlTituloNossoNumero);
    	$resultSet = $this->getAssoc();

		if($this->getAffectedRows() < 1){
			return false;
		}
		$titoid = Mascara::inteiro($resultSet['titoid']);
		return $titoid;
    }


    /**
     * Salvar na tabela titulo controle envio, tabela auxiliar indicando titulo que devem/foram enviado as clientes(BOLETO).
     *
     * @author Leandro A. Ivanaga <leandroivanaga@brq.com>
     * @version 25/11/2013
     * @param int $titoid (ID do titulo)
     * @param int $connoid (numero de contrato)
     * @return mixed $idTituloControle/false
     */
    public function insertTituloControle($titoid=0, $connoid=0){
    	$sqlControleEnvio = "
				INSERT INTO titulo_controle_envio
					(
					tcedata_criacao,
					tcetitoid,
					tceconoid,
					tcetipo,
					tcestatus_envio
					)
				VALUES
					(
					NOW(),
    				{$titoid},
					{$connoid},
					'boleto_seco',
					'false'
					)
				RETURNING tceoid
				";

		$this->queryExec($sqlControleEnvio);

		if($this->getAffectedRows() > 0){
			return $this->getAssoc();
		} else {
			return false;
		}
    }


	/**
     * Insert na tabela titulo, nota_fiscal, nota_fiscal_item (Cartão).
     *
     * @author Leandro A. Ivanaga <leandroivanaga@brq.com>
     * @version 25/11/2013
     * @param int $prpoid (ID da proposta)
	 * @param int $usuoid (ID do usuario)
	 * @param int $clioid (ID do cliente)
	 * @param array $numContratos (array associativo tipo chave -> valor, numero dos contratos)
     *     OBS-> campos obrigatórios do $numContratos[]:
     *     		int contrato -> numero do contrato
     * @param array $dadosTaxa (array associativo tipo chave -> valor, dados da taxa nota_fical, nota_fiscal_item, titulo)
     *     OBS-> campos obrigatórios do $dadosTaxa[]:
     *     	 float taxa_valor_total -> valor total do titulo ou valor total da parcela (tabela titulo, nota_fiscal)
     *     	 float taxa_valor_item -> valor para cada item (tabela nota_fiscal_item)
     *     	 int taxa_qntd_parcelas -> quantidade total de parcelas
     *       int taxa_id_obrigacao -> ID da obrigação financeira
     *       string taxa_descricao_obrigacao -> descricao da obrigacao financeira
     *       int taxa_forma_pagamento -> ID da forma de pagamento
     *       string taxa_data_vencimento -> data de vencimento do titulo formato (dd-mm-YYYY, ex: 03-09-2014)
     *       int taxa_num_parcela -> numero da parcela em questão
     *       string taxa_num_cartao -> numero do cartão de crédito do cliente
     *       string taxa_data_validade_cartao -> mes e ano de vencimento do cartão (mm/YY, ex: 03/15)
     *       int taxa_codigo_seguranca -> numero do codigo de segurança do cartão
     *     OBS-> campos NÃO obrigatórios do $dadosTaxa[]:
     *       N/A
     * @return mixed $idTitulo/false
     */
    public function insertTitulo($prpoid=0, $usuoid=0, $clioid=0, $numContratos=array(), $dadosTaxa=array()){

    	// Insere na tabela nota_fiscal
		$sqlNotaFiscal = "
			INSERT INTO nota_fiscal
				(
					nflno_numero,
					nfldt_inclusao,
					nfldt_nota,
					nfldt_emissao,
					nflclioid,
					nflusuoid,
					nflnatureza,
					nflserie,
					nflvl_total
				)
				VALUES
				(
					(SELECT MAX(nflno_numero) + 1 AS no_numero FROM nota_fiscal n WHERE n.nflserie = 'A'),
					NOW(),
					NOW(),
					NOW(),
					{$clioid},
					{$usuoid},
					'{$dadosTaxa['taxa_descricao_obrigacao']}',
					'{$dadosTaxa['taxa_serie']}',
					{$dadosTaxa['taxa_valor_total']}
			)
			RETURNING nfloid";

    	$this->queryExec($sqlNotaFiscal);
    	$resultSet = $this->getAssoc();

		if($this->getAffectedRows() < 1){
			return false;
		}
		$nfloid = Mascara::inteiro($resultSet['nfloid']);

    	// Insere na tabela nota_fiscal_item
		foreach ($numContratos as $contrato){
			$sqlNotaFiscalItem = "
				INSERT INTO nota_fiscal_item
					(
						nfino_numero,
						nfidt_inclusao,
						nfinfloid,
						nficonoid,
						nfiserie,
						nfiobroid,
						nfids_item,
						nfivl_item
					)
				VALUES
					(
						(SELECT nflno_numero FROM nota_fiscal WHERE nfloid = {$nfloid}),
						NOW(),
						{$nfloid},
						{$contrato},
						'{$dadosTaxa['taxa_serie']}',
						{$dadosTaxa['taxa_id_obrigacao']},
						'{$dadosTaxa['taxa_descricao_obrigacao']}',
						'{$dadosTaxa['taxa_valor_item']}'
					);
				";

			$this->queryExec($sqlNotaFiscalItem);
			if($this->getAffectedRows() < 1){
	            return false;
			}
		}

		// Inserir o titulo
		$sqlTitulo = "
				INSERT INTO titulo
				(
					titdt_inclusao,
					titemissao,
					titdt_vencimento,
					titnfloid,
					titclioid,
					titno_parcela,
					titvl_titulo,
					titformacobranca
				)
				VALUES
				(
					NOW(),
					NOW(),
					NOW(),
					{$nfloid},
					{$clioid},
					{$dadosTaxa['taxa_num_parcela']},
					{$dadosTaxa['taxa_valor_total']},
					{$dadosTaxa['taxa_forma_pagamento']}
				)
				RETURNING titoid";

		$this->queryExec($sqlTitulo);
    	$resultSet = $this->getAssoc();

		if($this->getAffectedRows() < 1){
			return false;
		}
		$titoid = Mascara::inteiro($resultSet['titoid']);
		return $titoid;
    }

    /**
     * Remover titulo(Cartão).
     *
     * @author Leandro A. Ivanaga <leandroivanaga@brq.com>
     * @version 25/11/2013
     * @param int $retTitoid (ID do titulo)
     * @return bool true/false
     */
    public function rollbackTitulo($retTitoid=0){

    	$status = $this->statusTransaction();

		// Caso não esteja com nenhuma transação em aberto, então realiza o delete na tabela
		// Deve remover o titulo não efetuado pagamento, pois o mesmo não deve permanecer na base.
    	if ($status !== PGSQL_TRANSACTION_ACTIVE && $status !== PGSQL_TRANSACTION_INTRANS) {

			$sqlRollback = "
					DELETE FROM cliente_cobranca_credito_historico WHERE ccchtitoid = {$retTitoid};
					DELETE FROM controle_transacao_cartao WHERE ctctitoid = {$retTitoid};
					DELETE FROM nota_fiscal_item WHERE nfinfloid = (SELECT titnfloid FROM titulo WHERE titoid = {$retTitoid});
					DELETE FROM nota_fiscal WHERE nfloid = (SELECT titnfloid FROM titulo WHERE titoid = {$retTitoid});
					DELETE FROM titulo WHERE titoid = {$retTitoid};";

	    	$this->queryExec($sqlRollback);

			if($this->getAffectedRows() > 0){
				return true;
			} else {
				return false;
			}
    	}
	}

	public function getDadosTitulo($titoid){

		$sql = "
				SELECT
				CASE WHEN $titoid in (SELECT titoid FROM titulo WHERE titoid = $titoid) THEN ".TituloCobrancaModel::TIPO_TITULO."
					WHEN $titoid in (SELECT titoid FROM titulo_retencao WHERE titoid = $titoid) THEN ".TituloCobrancaModel::TIPO_TITULO_RETENCAO."
					WHEN $titoid in (SELECT titcoid FROM titulo_consolidado INNER JOIN titulo_tipo ON titulo_tipo.tittoid = titulo_consolidado.titctittoid WHERE titulo_consolidado.titcoid = $titoid AND titulo_tipo.titttipo = 'PD') THEN ".TituloCobrancaModel::TIPO_TITULO_CONSOLIDADO."
					ELSE 0
				END AS tipo_titulo";

		$this->queryExec($sql);
		$result = $this->getAssoc();

		return $result['tipo_titulo'];

	}


    public function getTituloById($tituloId)
    {
		$sql = "SELECT * FROM titulo WHERE titoid = $tituloId";

		$this->queryExec($sql);
		return $this->getAssoc();
	}

    public function getTituloRetencaoById($tituloId)
    {
		$sql = "SELECT * FROM titulo_retencao WHERE titoid = $tituloId";

		$this->queryExec($sql);
		return $this->getAssoc();
	}

    public function getTituloConsolidadoById($tituloId)
    {
		$sql = "SELECT * FROM titulo_consolidado WHERE titcoid = $tituloId";

		$this->queryExec($sql);
		return $this->getAssoc();
	}

	public function atualizarStatusTitulo($tituloId, $tipoTitulo, $status){

		switch ($tipoTitulo) {
			case TituloCobrancaModel::TIPO_TITULO_RETENCAO:
				$sql = "UPDATE titulo_retencao SET tittpetoid = $status WHERE titoid = $tituloId;";
				break;
			case TituloCobrancaModel::TIPO_TITULO_CONSOLIDADO:
				$sql = "UPDATE titulo_consolidado SET titctpetoid = $status WHERE titcoid = $tituloId;";
				break;
			case TituloCobrancaModel::TIPO_TITULO:
				$sql = "UPDATE titulo SET tittpetoid = $status WHERE titoid = $tituloId;";
				break;
			default:
				throw new Exception("Tipo do título inválido");
				break;				
		}

		$this->queryExec($sql);

		return !!$this->getAffectedRows();
	}

	public function atualizarNumeroRegistroBanco($tituloId, $tipoTitulo, $nossoNumero){

		switch ($tipoTitulo) {
			case TituloCobrancaModel::TIPO_TITULO_RETENCAO:
				$sql = "UPDATE titulo_retencao SET titnumero_registro_banco = $nossoNumero WHERE titoid = $tituloId;";
				break;
			case TituloCobrancaModel::TIPO_TITULO_CONSOLIDADO:
				$sql = "UPDATE titulo_consolidado SET titcnumero_registro_banco = $nossoNumero WHERE titcoid = $tituloId;";
				break;
			case TituloCobrancaModel::TIPO_TITULO:
				$sql = "UPDATE titulo SET titnumero_registro_banco = $nossoNumero WHERE titoid = $tituloId;";
				break;
			default:
				throw new Exception("Tipo do título inválido");
				break;				
		}

		$this->queryExec($sql);

		return !!$this->getAffectedRows();
	}


	public function isFormaCobrancaDebitoAutomatico($tituloId){

		$tipoTitulo = self::getDadosTitulo($tituloId);
		switch ((int) $tipoTitulo) {
			case (int) TituloCobrancaModel::TIPO_TITULO_RETENCAO:
				$sql = "SELECT titoid 
						FROM titulo_retencao
						WHERE titformacobranca IN (SELECT forcoid
													FROM forma_cobranca 
													WHERE forcvenda IS TRUE 
													AND forcexclusao IS NULL 
													AND forccobranca_cartao_credito IS FALSE 
													AND forcdebito_conta IS TRUE)
						AND titoid = $tituloId;";
				break;
			case (int) TituloCobrancaModel::TIPO_TITULO_CONSOLIDADO:
				$sql = "SELECT titcoid 
						FROM titulo_consolidado 
						WHERE titcformacobranca IN (SELECT forcoid
													FROM forma_cobranca 
													WHERE forcvenda IS TRUE 
													AND forcexclusao IS NULL 
													AND forccobranca_cartao_credito IS FALSE 
													AND forcdebito_conta IS TRUE)
						AND titcoid = $tituloId;";
				break;
			case (int) TituloCobrancaModel::TIPO_TITULO:
				$sql = "SELECT titoid
						FROM titulo 
						WHERE  titformacobranca IN (SELECT forcoid
													FROM forma_cobranca 
													WHERE forcvenda IS TRUE 
													AND forcexclusao IS NULL 
													AND forccobranca_cartao_credito IS FALSE 
													AND forcdebito_conta IS TRUE)
						AND titoid = $tituloId;";
				break;
			default:
				throw new Exception("Tipo do título inválido");
				break;				
		}

		$this->queryExec($sql);
		if($this->getNumRows() > 0){
			return true;
		} else {
			return false;
		}
    }

    public function isFormaCobrancaCartaoDeCredito($tituloId){
		
		$tipoTitulo = self::getDadosTitulo($tituloId);
		switch ((int) $tipoTitulo) {
			case (int) TituloCobrancaModel::TIPO_TITULO_RETENCAO:
				$sql = "SELECT titoid 
						FROM titulo_retencao
						WHERE titformacobranca IN (SELECT forcoid
													FROM forma_cobranca 
													WHERE forccobranca_cartao_credito = 't' 
													AND forcexclusao IS NULL 
													AND forcdebito_conta IS FALSE)
						AND titoid = $tituloId;";
				break;
			case (int) TituloCobrancaModel::TIPO_TITULO_CONSOLIDADO:
				$sql = "SELECT titcoid 
						FROM titulo_consolidado 
						WHERE titcformacobranca IN (SELECT forcoid
													FROM forma_cobranca 
													WHERE forccobranca_cartao_credito = 't' 
													AND forcexclusao IS NULL 
													AND forcdebito_conta IS FALSE)
						AND titcoid = $tituloId;";
				break;
			case (int) TituloCobrancaModel::TIPO_TITULO:
				$sql = "SELECT titoid
						FROM titulo 
						WHERE  titformacobranca IN (SELECT forcoid
													FROM forma_cobranca 
													WHERE forccobranca_cartao_credito = 't' 
													AND forcexclusao IS NULL 
													AND forcdebito_conta IS FALSE)
						AND titoid = $tituloId;";
				break;
			default:
				throw new \Exception("Tipo do título inválido");
				break;				
		}
		
		$this->queryExec($sql);
		if($this->getNumRows() > 0){
			return true;
		} else {
			return false;
		}
	}
	
    public function isFormaCobrancaBoleto($tituloId){
		
		$tipoTitulo = self::getDadosTitulo($tituloId);
		switch ((int) $tipoTitulo) {
			case (int) TituloCobrancaModel::TIPO_TITULO_RETENCAO:
				$sql = "SELECT titoid 
						FROM titulo_retencao
						WHERE titformacobranca IN (SELECT forcoid
													FROM forma_cobranca 
													WHERE forccobranca_registrada IS TRUE)
						AND titoid = $tituloId;";
				break;
			case (int) TituloCobrancaModel::TIPO_TITULO_CONSOLIDADO:
				$sql = "SELECT titcoid 
						FROM titulo_consolidado 
						WHERE titcformacobranca IN (SELECT forcoid
													FROM forma_cobranca 
													WHERE forccobranca_registrada IS TRUE)
						AND titcoid = $tituloId;";
				break;
			case (int) TituloCobrancaModel::TIPO_TITULO:
				$sql = "SELECT titoid
						FROM titulo 
						WHERE  titformacobranca IN (SELECT forcoid
													FROM forma_cobranca 
													WHERE forccobranca_registrada IS TRUE)
						AND titoid = $tituloId;";
				break;
			default:
				throw new \Exception("Tipo do título inválido");
				break;				
		}
		
		$this->queryExec($sql);
		if($this->getNumRows() > 0){
			return true;
		} else {
			return false;
		}
    }

	public function getTipoEventoTituloXML($codigoRetornoXml){

		$sql = "
			SELECT
				tpetoid
			FROM
				tipo_evento_titulo
			WHERE
				tpetcodigo = $codigoRetornoXml
				AND tpettipo_evento = 'Registro_OnLine_Ticket'
				AND tpetcfbbanco = 33
				AND tpetcob_registrada IS TRUE
			LIMIT 1
		";

		$this->queryExec($sql);

		return $this->getNumRows() > 0 ? $this->getObject(0)->tpetoid : false;

	}

	public function insertHistoricoOnlineTitulo($tituloId, $usuarioId, $tipoEventoTituloId){

		$dataCadastro = date('Y-m-d H:i:s');

        $sql = "
            INSERT INTO
                titulo_historico_online (
                    thotitoid,
                    thousuoid,
                    thodt_cadastro,
                    thoticket_banco,
                    thocod_retorno,
                    thonsu,
                    thodt_nsu
                )
            VALUES (
                $tituloId,
                $usuarioId,
                '$dataCadastro',
                '',
                '$tipoEventoTituloId',
                '',
                ''
            )
        ";

		$this->queryExec($sql);

		return !!$this->getAffectedRows() > 0;

	}

	public function insertEventoTitulo($tituloId, $tipoEventoTituloId, $codigoRetornoXML){

        $dataGeracao = date('Y-m-d H:i:s');

        $sql = "
            INSERT INTO
                evento_titulo (
                    evtititoid,
                    evtitpetoid,
                    evtidt_geracao,
                    evticod_retorno_cobr_reg
                )
            VALUES (
                $tituloId,
                $tipoEventoTituloId,
                '$dataGeracao',
                $codigoRetornoXML
            );
        ";

		$this->queryExec($sql);

		return !!$this->getAffectedRows() > 0;

	}

	public function emPeriodoValidacaoDebitoAutomaticoExpirado($tituloId){
		
		$sql = "SELECT pcsidescricao FROM parametros_configuracoes_sistemas_itens WHERE pcsipcsoid = 'INTEGRACAO_TOTVS' AND pcsioid = 'INTEGRACAO_ATIVA'";
		$this->queryExec($sql);
		$resultSet = $this->getAssoc();
		$INTEGRACAO_TOTVS_ATIVA = $resultSet['pcsidescricao'];

		if($INTEGRACAO_TOTVS_ATIVA == "true"){
			/*
			* ORGMKTOTVS-100
			* Com a implementação do PROTHEUS, as conciliações dos pagamentos por débito automático e as tentativas de pagamento ficarão todas no PROTHEUS 
			* e as baixas/liquidações serão enviadas para o ERP. Quanto aos débitos que não puderam ser efetuados serão gerenciados pelo PROTHEUS, 
			* essas informações não serão retornadas para o ERP, logo, não teremos os logs com as tentativas para liberar a geração de boletos no ERP, 
			* então, será necessário alterar a regra existente para contar os dias que o titulo com débito automático está vencido.
			*/
			$sql = "SELECT (
						SELECT (case when count(dia) > 0 then count(dia) else 0 end) as dias  
						FROM (
							SELECT (date(titdt_vencimento)+s.a*'5 day'::interval) AS dia 
							FROM generate_series(0, date(CURRENT_DATE) - date(titdt_vencimento), 1 
							) AS s(a)) foo 
						WHERE EXTRACT(DOW FROM dia) BETWEEN 1 AND 5
					) as quantidatentativas 
					FROM titulo 
					WHERE titulo.titdt_vencimento < CURRENT_DATE 
					AND titulo.titdt_pagamento IS NULL 
					AND titulo.titoid = $tituloId";
		}else {
			$sql = "SELECT count(ldaacodigo_retorno) AS quantidatentativas 
					FROM log_debito_automatico_arquivo AS logda 
					JOIN titulo ON logda.ldaatitoid = titulo.titoid 
					WHERE logda.ldaacodigo_retorno <> '00' 
					AND logda.ldaacodigo_retorno <> '31' 
					AND logda.ldaatitoid = titulo.titoid 
					AND titulo.titdt_vencimento < CURRENT_DATE 
					AND titulo.titdt_pagamento IS NULL 
					AND titulo.titoid = $tituloId 
					GROUP BY logda.ldaatitoid, logda.ldaacodigo_retorno LIMIT 1";			
		}	
		
		$this->queryExec($sql);
		if($this->getNumRows() > 0){
			$resultSet = $this->getAssoc();
			$quantidaTentativas = (int) $resultSet['quantidatentativas'];

			if($quantidaTentativas > ParametroCobrancaRegistrada::getNumeroMaximoTentativasPagamentoDebitoAutomatico()){
				return true;
			}else{
				return false;
			}

		} else {
			return false;
		}
	}

	public function emPeriodoValidacaoCartaoDeCreditoExpirado($tituloId){
		$sql = "SELECT count(ctcoid) AS quantidatentativas
				FROM controle_transacao_cartao AS logcc
				JOIN titulo
				ON logcc.ctctitoid = titulo.titoid
				WHERE titulo.titdt_pagamento IS NULL
				AND titulo.titdt_vencimento < CURRENT_DATE
				AND titulo.titoid = $tituloId LIMIT 1";
		
		$this->queryExec($sql);
		if($this->getNumRows() > 0){
			$resultSet = $this->getAssoc();
			$quantidaTentativas = (int) $resultSet['quantidatentativas'];
			
			if($quantidaTentativas > ParametroCobrancaRegistrada::getNumeroMaximoTentativasPagamentoCartaoCredito()){
				return true;
			}else{
				return false;
			}
		} else {
			return false;
		}
	}


	public function getNumeroNotaFiscalByNotaFiscalId($notaFiscalId){
		if(isset($notaFiscalId)){
			$sql = "SELECT concat(concat(nflno_numero, '-' ),nflserie) AS numero_nota FROM nota_fiscal WHERE nfloid = $notaFiscalId";
			$this->queryExec($sql);
			if($this->getNumRows() > 0){
				$resultSet = $this->getAssoc();
				return $resultSet['numero_nota'];
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

}
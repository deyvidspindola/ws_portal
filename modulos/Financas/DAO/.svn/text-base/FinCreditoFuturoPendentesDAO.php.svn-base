<?php

class FinCreditoFuturoPendentesDAO extends FinCreditoFuturoDAO {

	
    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    /**
     * Método construtor.
     * 
     * @param resource $conn conexão
     */
    public function __construct($conn) {
        //Seta a conexão na classe
        $this->conn = $conn;
    }


    /**
     * Método buscarCreditosFuturosPendentes()
     *
     * @return array $retorno
     */
    public function buscarCreditosFuturosPendentes(stdClass $parametros) {

    	$tiposMotivosCredito = $this->buscarTiposMotivosCreditoAprovador($parametros->usuario_aprovador);

    	$retorno = array();

    	if (count($tiposMotivosCredito)) {

    		$sql =  "SELECT

    					cfooid,

						TO_CHAR(cfodt_inclusao,'DD/MM/YYYY') AS dt_inclusao,

						--informação de status
						cfostatus AS status_id,	
						CASE WHEN cfostatus = 1 THEN 'Aprovado'
						         WHEN cfostatus = 2 THEN 'Concluído'
						         WHEN cfostatus = 3 THEN 'Pendente'
						         WHEN cfostatus = 4 THEN 'Reprovado'
						    END AS status_descricao,

					    --informação de cliente
					    cfoclioid AS cliente_id,

					    clinome AS cliente_nome,

					    CASE WHEN clitipo = 'J' THEN clino_cgc
						ELSE
							clino_cpf
						END AS doc,

						clitipo AS tipo,

                        CASE WHEN clitipo = 'F' THEN cliuf_res
                             ELSE cliuf_com
                        END AS cliente_uf,


						--protocolo
						cfoancoid AS protocolo,

						--motivo de credito
						cfocfmcoid AS motivo_credito_id,
						cfmcdescricao AS motivo_credito_descricao,

						--Valor
						cfovalor AS valor,

						--tipo desconto
						--1 - Percentual. 2 - Valor
						cfotipo_desconto AS tipo_desconto,
                        CASE WHEN cfotipo_desconto = 1 THEN 'Percentual'
                             WHEN cfotipo_desconto = 2 THEN 'Valor'
                        END AS tipo_desconto_descricao,

						--Forma de aplicação
						cfoforma_aplicacao AS forma_aplicacao_id,
						CASE WHEN cfoforma_aplicacao = 1 THEN 'Integral'
						     WHEN cfoforma_aplicacao = 2 THEN 'Parcelado'
						END AS forma_aplicacao_descricao,

					    --Usuario de inclusão
					    cfousuoid_inclusao AS usuario_inclusao_id,
					    (SELECT nm_usuario FROM usuarios WHERE cd_usuario = cfousuoid_inclusao) AS usuario_inclusao_nome,

					    --Usuario de análise
					    cfousuoid_avaliador AS usuario_avaliador_id,
					    (SELECT nm_usuario FROM usuarios WHERE cd_usuario = cfousuoid_avaliador) AS usuario_avaliador_nome,

                        --Data de avaliação (Para condicional)
                        cfodt_avaliacao AS avaliado
					    
					FROM
						credito_futuro
					INNER JOIN
						clientes ON (clioid = cfoclioid)
					INNER JOIN
						credito_futuro_motivo_credito ON (cfocfmcoid = cfmcoid)
					WHERE
                        (
                            (cfostatus IN (1,4) AND cfodt_avaliacao IS NOT NULL)
                            OR (cfostatus = 3 AND cfodt_avaliacao IS NULL)
                        )
                    AND
						cfodt_exclusao IS NULL
					AND	
                        cfodt_encerramento IS NULL
                    AND
						--conforme o tipo de motivo de credito
						cfmctipo IN (" . implode(', ', $tiposMotivosCredito) . ")
					AND
						--data de inclusao de crédito (obrigatório)
                    	cfodt_inclusao BETWEEN '" . $parametros->cfodt_inclusao_de . " 00:00:01'
                        AND '" . $parametros->cfodt_inclusao_ate . " 23:59:59' ";

                    //Filtro por data de avaliação (Não obrigatorio)
                    if ((isset($parametros->cfodt_avaliacao_de) && isset($parametros->cfodt_avaliacao_ate)) && (trim($parametros->cfodt_avaliacao_de) != '' && trim($parametros->cfodt_avaliacao_ate) != '')) {
                    	$sql .= "AND
                    				cfodt_avaliacao BETWEEN '" . $parametros->cfodt_avaliacao_de . " 00:00:01'
                        			AND '" . $parametros->cfodt_avaliacao_ate . " 23:59:59' ";
                    }

                    //Filtro por cliente (Não obrigatório)
                    if ( isset($parametros->cliente_id) && trim($parametros->cliente_id) != '') {
                    	$sql .= "AND
                    				cfoclioid = " . intval($parametros->cliente_id) . " ";
                    }

                    //Filtro por contrato (Não obrigatório)
                    if ( isset($parametros->contrato) && trim($parametros->contrato) != '') {
                    	$sql .= "AND
                    				cfoancoid = " . intval($parametros->contrato) . " ";
                    }

                    //Filtro por status (Não obrigatório)
                    if ( isset($parametros->cfostatus) && trim($parametros->cfostatus) != '-1') {

                        $sql .= "AND
                                    cfostatus = " . intval($parametros->cfostatus) . " ";
                    }



                    //Filtro por usuario de inclusao (Não obrigatório)
                    if ( isset($parametros->cfousuoid_inclusao) && trim($parametros->cfousuoid_inclusao) != '') {
                    	$sql .= "AND
                    				cfousuoid_inclusao = " . intval($parametros->cfousuoid_inclusao) . " ";
                    }

                    $sql .= " ORDER BY cfodt_inclusao DESC, clinome ASC, cfmcdescricao ASC";

                    //data de inclusão, cliente e motivo do crédito


            if (!$rs = pg_query($this->conn, $sql)) {
            	throw new Exception(MENSAGEM_ERRO_PROCESSAMENTO . ' - Pesquisar créditos futuros.');
            }

            while ($row = pg_fetch_object($rs)) {

            	if ($row->tipo == 'J') {
                	$row->doc = $this->formatarDados('cnpj', $row->doc);
            	} else if ($row->tipo == 'F') {
                	$row->doc = $this->formatarDados('cpf', $row->doc);
            	}

                $row->valor_numerico = $row->valor;

            	$row->valor = $this->formatarValorTipoDesconto($row->tipo_desconto,number_format($row->valor, 2, ',', '.'));

                $row->ja_avaliado = is_null($row->avaliado) ? false : true;

            	$retorno[] = $row;
            }
    	}

    	return $retorno;

    }

    /**
     * Método buscarTiposMotivosCreditoAprovador()
     *
     * @return array $retorno
     */
    public function buscarTiposMotivosCreditoAprovador($usuario_aprovador_id) {

    	$sql = "SELECT
					cfmptipomotivo
				FROM
					credito_futuro_motivo_responsavel
				WHERE
					cfmpcfeusuoid =" . intval($usuario_aprovador_id) . "";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new Exception(MENSAGEM_ERRO_PROCESSAMENTO . ' - Buscar Tipos de Motivos Aprovador.');
		}

		$retorno = array();

		while($row = pg_fetch_object($rs)) {
			$retorno[] = $row->cfmptipomotivo;
		}


		return $retorno;

    }

}
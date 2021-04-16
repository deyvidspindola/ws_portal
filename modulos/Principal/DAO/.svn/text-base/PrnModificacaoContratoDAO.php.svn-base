<?php

/**
 * Classe PrnModificacaoContratoDAO.
 * Camada de persistencia de dados.
 *
 * @package  Principal
 * @author   Andre Zilz
 *
 */
class PrnModificacaoContratoDAO {

	//Objeto de conexao com o banco de dados
	private $conn;
	//usuario logado
	private $usuoid;

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	public function __construct($conn) {
		//Seta a conexao na classe
		$this->conn = $conn;
		$this->usuoid = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

		//Se nao tiver nada na sessao, a execucao vem do CRON
		if(empty($this->usuoid)) {
			$this->usuoid = 2750;
		}
	}


	/**
	 * Metodo para realizar a pesquisa de varios registros
	 * @author   Andre Zilz
	 * @param stdClass $parametros Filtros da pesquisa
	 * @param array $paginacao | LIMIT e OFFSET
	 * @param string $ordenacao | campos do ORDER BY
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarModificacao(stdClass $parametros, $paginacao = null, $ordenacao = null){

		$retorno = array();
		$sql = '';

		if(!isset($paginacao)) {
			$select = "COUNT(mdfoid) AS total_registros";
			$orderBy = "";

		} else {

			$select = "
				TO_CHAR(mdfdt_cadastro,'dd/mm/yyyy hh24:mi')AS data_cadastro,
		        mdfoid,
		        mdfstatus,
		        mdfstatus_financeiro,
		        cmgdescricao AS grupo,
		        cmtdescricao AS tipo,
		        (SELECT clinome FROM clientes WHERE clioid = mdfclioid) AS cliente,
		        (SELECT clinome FROM clientes WHERE clioid = cmfclioid_destino) AS cliente_destino,
		        cmfconnumero AS connumero,
		        cmfconnumero_novo,
		        cmfordoid,
		        (SELECT cvgvigencia FROM contrato_vigencia WHERE cvgoid = cmfcvgoid)AS vigencia,
		        (SELECT ossdescricao FROM ordem_servico, ordem_servico_status WHERE ordoid = cmfordoid AND ordstatus = ossoid) AS ordstatus,
		        (SELECT nm_usuario FROM usuarios WHERE cd_usuario = mdfusuoid_cadastro) AS usuario,
		        (SELECT tpcdescricao FROM tipo_contrato WHERE tpcoid = cmftpcoid_origem) AS tipo_contrato_origem,
		        (SELECT tpcdescricao FROM tipo_contrato WHERE tpcoid = cmftpcoid_destino) AS tipo_contrato_destino
			";

			$orderBy = " ORDER BY ";
			$orderBy .= (!empty($ordenacao)) ? $ordenacao :  "mdfdt_cadastro, mdfoid";
		}

		$sql = "
			SELECT
			".$select."
			FROM
		        modificacao
			INNER JOIN
		        contrato_modificacao ON (cmfmdfoid = mdfoid)
			INNER JOIN
		        contrato_modificacao_tipo ON (cmtoid = mdfcmtoid)
			INNER JOIN
		        contrato_modificacao_grupo ON (cmgoid = cmtcmgoid)
		    ";

		//Para melhorar desempenho, omitido este JOIN quando nao necessario
		if ( !empty($parametros->chassi) || !empty($parametros->placa) ) {
			$sql .= "LEFT JOIN
		        		veiculo ON (veioid = cmfveioid)";
		}

		$sql .= " WHERE	TRUE ";

		$sql .=  $this->montarFiltrosPesquisaModificacao($parametros);

	    $sql .= $orderBy;


    	if (isset($paginacao->limite) && isset($paginacao->offset)) {
            $sql.= "
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "
            ";
        }

		//echo "<pre>";var_dump($sql);echo "</pre>";
		$rs = $this->executarQuery($sql);

		while($row = pg_fetch_object($rs)){
			$retorno[] = $row;
		}

		return $retorno;
	}


	/**
	 * Metodo para realizar a pesquisa de varios registros para planilha CSV
	 * @author   Andre Zilz
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarModificacaoCsv(stdClass $parametros){

		$retorno = array();
		$sql = '';

		$sql = "
			SELECT
				TO_CHAR(mdfdt_cadastro,'dd/mm/yyyy hh24:mi')AS data_cadastro,
		        mdfoid,
		        mdfstatus,
		        mdfstatus_financeiro,
		        cmgdescricao AS grupo,
		        cmtdescricao AS tipo,
		        (SELECT clinome FROM clientes WHERE clioid = mdfclioid) AS cliente,
		        (SELECT clinome FROM clientes WHERE clioid = cmfclioid_destino) AS cliente_destino,
		        cmfconnumero AS connumero,
		        cmfconnumero_novo,
		        cmfordoid,
		        (SELECT cvgvigencia FROM contrato_vigencia WHERE cvgoid = cmfcvgoid)AS vigencia,
		        (SELECT ossdescricao FROM ordem_servico, ordem_servico_status WHERE ordoid = cmfordoid AND ordstatus = ossoid) AS ordstatus,
		        (SELECT nm_usuario FROM usuarios WHERE cd_usuario = mdfusuoid_cadastro) AS usuario,
		        (SELECT tpcdescricao FROM tipo_contrato WHERE tpcoid = cmftpcoid_origem) AS tipo_contrato_origem,
		        (SELECT tpcdescricao FROM tipo_contrato WHERE tpcoid = cmftpcoid_destino) AS tipo_contrato_destino,
		        cmdfpvlr_monitoramento_negociado AS monitoramento,
		        cmdfpvlr_locacao_negociado AS locacao,
		        cmdfpvlr_taxa_negociado AS taxa,
		        (SELECT obrobrigacao FROM obrigacao_financeira WHERE obroid = cmdfpobroid) AS obrigacao,
		        (SELECT forcnome FROM forma_cobranca WHERE forcoid = cmdfpforcoid) AS forma_pgto,
		        veiplaca,
		        veichassi
			FROM
		        modificacao
			INNER JOIN
		        contrato_modificacao ON (cmfmdfoid = mdfoid)
			INNER JOIN
		        contrato_modificacao_tipo ON (cmtoid = mdfcmtoid)
			INNER JOIN
		        contrato_modificacao_grupo ON (cmgoid = cmtcmgoid)
		    LEFT JOIN
		        		veiculo ON (veioid = cmfveioid)
			LEFT JOIN
	        	contrato_modificacao_pagamento ON (cmdfpmdfoid = mdfoid)
			WHERE
				TRUE
			";

		$sql .=  $this->montarFiltrosPesquisaModificacao($parametros);

	    $sql .= "ORDER BY mdfdt_cadastro, mdfoid";

		//echo "<pre>";var_dump($sql);echo "</pre>";
		$rs = $this->executarQuery($sql);

		while($row = pg_fetch_object($rs)){
			$retorno[] = $row;
		}

		return $retorno;
	}
	/**
	 * Montar a string com as condicionais da quey de pesquisa
	 * @param  stdClass $parametros
	 * @return string
	 */
	private function montarFiltrosPesquisaModificacao($parametros) {

		$sql = '';

		//ID modificacao
	    if (!empty($parametros->mdfoid_pesq)) {
	        $sql .= "
	        		AND mdfoid =" . intval($parametros->mdfoid_pesq);
	    }

		//Periodo
	    if (!empty($parametros->data_inicial) && !empty($parametros->data_final)) {
	        $sql .= "
	        		AND mdfdt_cadastro BETWEEN '" . $parametros->data_inicial . " 00:00:00' AND '" . $parametros->data_final . " 23:59:59'";
	    }

	    //Status
	    if (!empty($parametros->status)) {
	        $sql .= "
	        		AND mdfstatus ='" . $parametros->status . "'";
	    }

	    //Status Financeiro
	    if (!empty($parametros->status_financeiro)) {
	        $sql .= "
	        		AND mdfstatus_financeiro ='" . $parametros->status_financeiro . "'";
	    }

	    //Motivo Substituicao
	    if (!empty($parametros->mdfmsuboid)) {
	        $sql .= "
	        		AND mdfmsuboid =" . intval($parametros->mdfmsuboid);
	    }

	    //Usuario / Departamento
	    if (!empty($parametros->mdfusuoid_cadastro)) {
	        $sql .= " AND mdfusuoid_cadastro =" . intval($parametros->mdfusuoid_cadastro);

	    } else  if (!empty($parametros->depoid)) {
	        $sql .= "
	        		AND mdfusuoid_cadastro IN
	        			(
						SELECT
							cd_usuario
						FROM
							usuarios
						WHERE
							usudepoid = ". intval($parametros->depoid)."
						)";
	    }

	    //Cliente
	    if (!empty($parametros->clioid_pesq)) {
	        $sql .= "
	        		AND mdfclioid = " . intval($parametros->clioid_pesq);
	    }

	    //Contrato
	    if (!empty($parametros->connumero)) {
	        $sql .= "
	        		AND cmfconnumero =" . intval($parametros->connumero);
	    }

	    //Tipo Modificacao
	    if (!empty($parametros->cmtoid)) {
	        $sql .= "
	        		AND cmtoid =" . intval($parametros->cmtoid);
	    }

	    //Grupo modificacao
	    if (!empty($parametros->cmgoid)) {
	        $sql .= "
	        		AND cmgoid =" . intval($parametros->cmgoid);
	    }

		//Placa
	    if (!empty($parametros->placa)) {
		     $sql .= "
		     		AND
	        			veiplaca ILIKE  '%".$parametros->placa."%'";
		}

		//Chassi
		if (!empty($parametros->chassi)) {
	        $sql .= "
	        		AND veichassi ILIKE  '%".$parametros->chassi."%'";
		}

		return $sql;
	}


	/**
	 * Metodo para realizar a pesquisa de contratos a vencer
	 * @author   Andre Zilz
	 * @param stdClass $parametros Filtros da pesquisa
	 * @param array $paginacao | LIMIT e OFFSET
	 * @param string $ordenacao | campos do ORDER BY
	 * @return array
	 * @throws ErrorException
	 */
	 public function pesquisarContratosVencer(stdClass $parametros, $paginacao = null, $ordenacao = null) {

	 	$retorno = array();
		$sql = '';
		$fimVigencia = '';

		if(!isset($paginacao)) {
			$selectContratos = "COUNT(connumero) AS total_registros";
			$selectFidelizados = $selectContratos;
			$orderBy = "";

		} else {

			if($parametros->cmtoid == '8') {
				$fimVigencia = ",TO_CHAR((condt_ini_vigencia + (INTERVAL '1 DAY' * condias_demonstracao)), 'dd/mm/yyyy') AS fim_vigencia";

			} else {
				$fimVigencia = ",TO_CHAR((condt_ini_vigencia + (INTERVAL '1 MONTH' * conprazo_contrato)), 'dd/mm/yyyy') AS fim_vigencia";

			}

			$selectContratos = "
				connumero,
				TO_CHAR(condt_ini_vigencia, 'dd/mm/yyyy') AS inicio_vigencia,
				(SELECT clinome FROM clientes WHERE clioid = conclioid) AS cliente,
				tpcdescricao AS tipo_contrato,
				eqcdescricao
			";
			$selectContratos .= $fimVigencia;

			$selectFidelizados = "
				connumero,
				TO_CHAR(hfcdt_fidelizacao,'dd/mm/yyyy') AS inicio_vigencia,
				(SELECT clinome FROM clientes WHERE clioid = conclioid) AS cliente,
				tpcdescricao AS tipo_contrato,
				eqcdescricao,
				TO_CHAR((hfcdt_fidelizacao + (INTERVAL '1 MONTH' * hfcprazo)),'dd/mm/yyyy') AS fim_vigencia
			";

			$orderBy = " ORDER BY ";
			$orderBy .= (!empty($ordenacao)) ? $ordenacao :  "inicio_vigencia::DATE, connumero";
		}

		$sql = "
			SELECT * FROM (
				(SELECT
				".$selectContratos."
				FROM
					contrato
				INNER JOIN
					veiculo ON (veioid = conveioid)
				INNER JOIN
					tipo_contrato ON (tpcoid = conno_tipo)
				INNER JOIN
					equipamento_classe ON (eqcoid = coneqcoid)
				WHERE
					condt_exclusao IS NULL
				AND
					NOT EXISTS (
								SELECT
									1
								FROM
									historico_fidelizacao_contrato
								WHERE
									hfcconnumero = connumero
								)
				".$this->montarFiltroContratosVencer($parametros, 'contrato')."

				) UNION (

				SELECT
				".$selectFidelizados."
				FROM
					contrato
				INNER JOIN
					tipo_contrato ON (tpcoid = conno_tipo)
				INNER JOIN
					(
						SELECT
							DISTINCT ON (ultimo_lancamento) ultimo_lancamento,
							hfcconnumero,
							hfcdt_fidelizacao,
							hfcprazo
						FROM (
					        	SELECT
					        		FIRST_VALUE(hfcoid) OVER(PARTITION BY hfcconnumero ORDER BY hfcoid DESC) AS ultimo_lancamento,
					        		hfcconnumero,
									hfcdt_fidelizacao,
									hfcprazo
				        		FROM
				        			historico_fidelizacao_contrato
							 ) AS query_in
					) AS query_out
					ON (hfcconnumero = connumero)
				INNER JOIN
					veiculo ON (veioid = conveioid)
				INNER JOIN
					equipamento_classe ON (eqcoid = coneqcoid)
				WHERE
					condt_exclusao IS NULL
				".$this->montarFiltroContratosVencer($parametros, 'historico_fidelizacao_contrato')."
				)
			) AS query_principal
			";

	    $sql .= $orderBy;

    	if (isset($paginacao->limite) && isset($paginacao->offset)) {
            $sql.= "
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "
            ";
        }

		//echo "<pre>";var_dump($sql);echo "</pre>";
		$rs = $this->executarQuery($sql);

		while($row = pg_fetch_object($rs)){
			$retorno[] = $row;
		}

		return $retorno;
	 }

	 /**
	 * Monta uma string com os filtros condicionais para compor a query
	 * de pesquisa de contratos a vencer
	 *
	 * @author Andre L. Zilz
	 * @param stdClass | dados informados em tela
	 * @return string
	 *
	 */
	 private function montarFiltroContratosVencer($parametros, $tabelaChave) {

	 	$where = '';
	 	$dataPesquisa = '';

 		if($tabelaChave == 'contrato') {

 			 if (!empty($parametros->cmtoid)) {

			 	if ($parametros->cmtoid == '8') {
			 		$dataPesquisa = "(condt_ini_vigencia + (INTERVAL '1 DAY' * condias_demonstracao))";
 			 	} else {
 			 		$dataPesquisa = "(condt_ini_vigencia + (INTERVAL '1 MONTH' * conprazo_contrato))";
 			 	}
 			 }

 		} else {
 			$dataPesquisa = "(hfcdt_fidelizacao + (INTERVAL '1 MONTH' * hfcprazo))";
 		}


	    //Tipo Modificacao
	    if (!empty($parametros->cmtoid)) {

	    	if ($parametros->cmtoid == '8') {
	    		$where .= "
        			AND tpcdemonstracao IS TRUE";
	    	} else {
	    		$where .= "
        			AND coneqcoid IN (select eqcoid from equipamento_classe where eqcdescricao ILIKE 'SIGGO SEGURO%')
        			AND connumero = (
        							SELECT
        								psaconnumero
    								FROM
    									produto_seguro_apolice
									WHERE
										psaconnumero = connumero
									AND
										psadt_exclusao IS NULL
									AND
										(psaretapolice IS NOT NULL AND psaretapolice > 0)
									LIMIT 1
									)";
        	}
	    }

	    //Periodo
	    if (!empty($parametros->data_inicial) && !empty($parametros->data_final)) {
	        $where .= "
	        		AND ".$dataPesquisa. " BETWEEN '" . $parametros->data_inicial . "' AND '" . $parametros->data_final . "'";
	    }

	    //Cliente
	    if (!empty($parametros->clioid_pesq)) {
	        $where .= "
	        		AND conclioid = " . intval($parametros->clioid_pesq);
	    }

	    //Contrato
	    if (!empty($parametros->connumero)) {
	        $where .= "
	        		AND connumero = " . intval($parametros->connumero);
	    }

	    //Chassi
		if (!empty($parametros->chassi)) {
	        $where .= "
	        		AND veichassi ILIKE  '%".$parametros->chassi."%'";
		}

		//Placa
	    if (!empty($parametros->placa)) {
		     $where .= "
		     		AND
	        			veiplaca ILIKE  '%".$parametros->placa."%'";
		}

	 	return $where;
	 }


	/**
	* Busca dados da modifcacao pelo ID
	*
	* @author Andre L. Zilz
	* @param int mdfoid
	* @param int $totalContratos | total de contratos que a modificacao possui
	* @return array stdCLass
	*/
	public function pesquisarModificacaoPorID($mdfoid, $totalContratos){

		$retorno = new stdCLass();

		if($totalContratos == 1) {
			$sqlAdicional = "
					(SELECT veiplaca FROM veiculo WHERE veioid = cmfveioid) AS veiplaca,
					(SELECT veiplaca FROM veiculo WHERE veioid = cmfveioid_novo) AS veiplaca_novo,
					cmfveioid_novo,
					cmfveioid,
					cmfconnumero,
				";
		}

		$sql = "
			SELECT
				--modificacao
				mdfoid,
				mdfmsuboid AS msuboid,
				mdfcmtoid AS cmtoid,
				mdfobservacao_modificacao AS observacao,
				mdfobservacao_analise_credito AS observacao_serasa,
				mdfstatus,

				--contrato_modificacao
				cmftpcoid_origem,
				cmfcvgoid,
				cmffunoid_executivo,
				cmfrczoid,
				cmfveioid_novo,
				cmfveioid,
				cmftppoid,
				cmftppoid_subtitpo,
				cmfclioid_destino,

				--contrato_modificacao_pagamento
				cmdfpforcoid,
				cmdfpcpvoid,
				cmdfpnum_parcela,
				cmdfpvencimento_fatura,
				cmdfpvlr_monitoramento_negociado,
				cmdfpvlr_monitoramento_tabela,
				cmdfpvlr_locacao_negociado,
				cmdfpvlr_locacao_tabela,
				cmdfpobroid_taxa,
				cmdfpvlr_taxa_tabela,
				cmdfpvlr_taxa_negociado,
				cmdfpcartao,
				cmdfpnome_portador,
				cmdfpcartao_vencimento,
				cmdfpdebito_banoid,
				cmdfpdebito_agencia,
				cmdfpdebito_cc,
				cmdfpisencao_taxa,
				cmdfpisencao_locacao,

				".$sqlAdicional."

				--forma de cobranca
				(SELECT
			        (CASE WHEN forccobranca_cartao_credito IS TRUE THEN
			                'credito'
			        WHEN forcdebito_conta IS TRUE THEN
			                'debito'
			        ELSE
			                'outra'
			        END) AS forma
				FROM
			        forma_cobranca
				WHERE
			        forcoid = cmdfpforcoid
		        ) AS forma_pgto,

				--Dados pagamento
				(SELECT
			        tpivalor
				FROM
			        tabela_preco_item
				INNER JOIN
			        tabela_preco ON (tpitproid=tproid)
				WHERE
			        tpiexclusao IS NULL
				AND
			        tprstatus='A'
				AND
			        tpicpvoid = (
			                        SELECT
			                                cpvoid
			                        FROM
			                                cond_pgto_venda
			                        WHERE
			                                cpvexclusao IS NULL
			                        AND
			                                cpvdescricao NOT ILIKE '%juros%'
			                        AND
			                                cpvparcela = cmdfpnum_parcela
			                        LIMIT
			                                1
			                        )
				AND
			        tpiobroid = eqcobroid

				) AS tpivalor_minimo,
				eqcvlr_minimo_mens,
				eqcvlr_maximo_mens,

				--Dados cliente
				clinome,
				clitipo AS tipo_pessoa,
		        (CASE clitipo
		        WHEN 'F' THEN
		                clino_cpf
		        ELSE
		                clino_cgc
		        END) AS cpf_cnpj

			FROM
				modificacao
			INNER JOIN
		        contrato_modificacao ON (cmfmdfoid = mdfoid)
			LEFT JOIN
		        contrato_modificacao_pagamento ON (cmdfpmdfoid = mdfoid)
		    INNER JOIN
		    	clientes ON (clioid = cmfclioid_destino)
		    INNER JOIN
		    	equipamento_classe ON (eqcoid = cmfeqcoid_origem)
			WHERE
		       mdfoid = ".intval($mdfoid)."
		    LIMIT 1
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();
		$rs = $this->executarQuery($sql);

		if(pg_num_rows($rs) > 0) {
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

	/**
	* conta o total de contratos de uma modificacao
	* @author   Andre Zilz
	* @param int $mdfoid | ID da modificacao
	* @return int
	*/
	public function contarContratoMoficacao($mdfoid) {

		$sql = "
			SELECT
				COUNT(cmfmdfoid) AS total_contratos
			FROM
				contrato_modificacao
			WHERE
				cmfmdfoid = ".intval($mdfoid)."
			";

		$rs = $this->executarQuery($sql);

		$registro = pg_fetch_object($rs);
		$retorno = isset($registro->total_contratos) ? $registro->total_contratos : 0;

		return $retorno;
	}


	/**
	* Verifica se o peridodo de vigencia do contrato
	* e superior a um ano + cinco dias
	*
	* @author Andre L. Zilz
	* @param int $connumero
	*/
	public function isVigenciaContratoVencida($connumero) {

		$sql = "
			SELECT
				(CASE WHEN condt_ini_vigencia IS NOT NULL THEN
        			(NOW() > (condt_ini_vigencia + INTERVAL '12 month') + INTERVAL '5 days')
				ELSE
	        		FALSE
				END)  AS is_mais_cinco_dias
			FROM
				contrato
			WHERE
				connumero = ".intval($connumero)."
			";

		$rs = $this->executarQuery($sql);

		$registro = pg_fetch_object($rs);
		$retorno = isset($registro->is_mais_cinco_dias) ? $registro->is_mais_cinco_dias : 'f';
		$retorno = ($retorno == 't') ? TRUE : FALSE;

		return $retorno;

	}


	/**
	* Recupera os dados de departamentos
	* @author   Andre Zilz
	* @return array/stdClass
	*/
	public function recuperarDadosDepartamento() {

		$retorno = array();

		$sql = "
			SELECT
				depoid,
				depdescricao
			FROM
				departamento
			WHERE
				depexclusao IS NULL
			ORDER BY
				depdescricao";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera os dados de usuarios
	* Request: AJAX
	* @author   Andre Zilz
	* @param int $depoid | id do departamento
	* @return array/stdClass
	*/
	public function recuperarDadosUsuario($depoid) {

		$retorno = array();
		 $i = 0;

		$sql = "
			SELECT
				cd_usuario AS usuoid,
				nm_usuario
			FROM
			    usuarios
			WHERE
			    usudepoid = ".intval($depoid)."
			AND
			    dt_exclusao IS NULL
			ORDER BY
			    nm_usuario";


		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $registro->usuoid;
            $retorno[$i]['descricao'] = utf8_encode($registro->nm_usuario);
            $i++;
		}

		return $retorno;
	}

	/**
	* Recupera os dados de usuarios
	* Request: AJAX
	* @author   Andre Zilz
	* @param int $veiplaca | Placa do veiculo
	* @return array/stdClass
	*/
	public function recuperarDadosVeiculoAjax($veiplaca) {

		$retorno = array();
		$i = 0;

		$sql = "
			SELECT
				veioid,
				veiplaca
			FROM
			    veiculo
			WHERE
			    veiplaca ILIKE '".$veiplaca."%'
			AND
			    veidt_exclusao IS NULL
			AND NOT EXISTS
  				(SELECT veioid FROM contrato WHERE conveioid = veioid)
			ORDER BY
			    veiplaca
		    LIMIT 10";

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

        while ($tupla = pg_fetch_object($rs)) {
            $retorno[$i]['id'] = $tupla->veioid;
            $retorno[$i]['label'] = utf8_encode($tupla->veiplaca);
            $retorno[$i]['value'] = utf8_encode($tupla->veiplaca);
            $i++;
        }

		return $retorno;
	}

	/**
	* Recupera os dados de grupos de modificacao
	* @author   Andre Zilz
	* @return array/stdClass
	*/
	public function recuperarGrupoModificacao() {

		$retorno = array();

		$sql = "
			SELECT
				cmgoid,
				cmgdescricao
			FROM
				contrato_modificacao_grupo
			WHERE
				cmgdt_exclusao IS NULL
			ORDER BY
				cmgdescricao
			";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera os dados de Tipos de Modificacao
	* Request: AJAX
	* @author   Andre Zilz
	* @param int $cmgoid | id do grupo modificacao
	* @return array/stdClass
	*/
	public function recuperarTipoModificacaoAjax($cmgoid) {

		$retorno = array();
		$i = 0;

		$sql = "
			SELECT
				cmtoid,
				cmtdescricao
			FROM
				contrato_modificacao_tipo
			WHERE
				cmtdt_exclusao IS NULL
			AND
				cmtcmgoid = ".intval($cmgoid)."
			ORDER BY
				cmtdescricao";

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $registro->cmtoid;
            $retorno[$i]['descricao'] = utf8_encode($registro->cmtdescricao);
            $i++;
		}

		return $retorno;
	}


	/**
	* Recupera os dados de Tipos de Modificacao
	* @author   Andre Zilz
	* @param int $tipos | id dos tipos de modificacao
	* @return array/stdClass
	*/
	public function recuperarTipoModificacaoContratoVencer($tipos) {

		$retorno = array();

		$sql = "
			SELECT
				cmtoid,
				cmtdescricao
			FROM
				contrato_modificacao_tipo
			WHERE
				cmtdt_exclusao IS NULL
			AND
				cmtoid IN (". implode(',', $tipos).")
			ORDER BY
				cmtdescricao";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

		while($registros = pg_fetch_object($rs)) {
			$retorno[] = $registros;
		}

		return $retorno;
	}

	/**
	* Recupera os dados de Tipos de Modificacao
	* @author   Andre Zilz
	* @return array/stdClass
	*/
	public function recuperarTipoModificacao($cmtoid = '') {

		$retorno = array();

		$sql = "
			SELECT
				cmtoid,
				cmtdescricao,
				cmtcmgoid,
				COALESCE(cmptgera_contrato_automatico, false) AS cmptgera_contrato_automatico,
				COALESCE(cmptanalise_credito, false) AS cmptanalise_credito,
				COALESCE(cmpttroca_cliente,false) AS cmpttroca_cliente,
				COALESCE(cmptleitura_arquivo, false) AS cmptleitura_arquivo,
				COALESCE(cmptrecebe_dados_financeiro, false) AS cmptrecebe_dados_financeiro,
				COALESCE(cmpttaxa, false) AS cmpttaxa,
				(SELECT
                        cmpxobroid
                FROM
                        contrato_modificacao_particularidade_taxa
                WHERE
                        cmpxcmtoid =  cmtoid
                AND
                        cmpxdt_exclusao IS NULL) AS cmpxobroid,
				(
					CASE WHEN cmptproduto_siggo IS TRUE THEN
						TRUE
					WHEN cmptproduto_siggo_seguro IS TRUE THEN
						TRUE
					ELSE
						FALSE
					END
				) AS produto_siggo,
				COALESCE(cmptproduto_siggo_seguro, FALSE) AS produto_siggo_seguro,
				COALESCE((
                                SELECT
                                        cmpgmodificacao_lote
                                FROM
                                        contrato_modificacao_particularidade_grupo
                                WHERE
                                        cmpgcmgoid = cmgoid
                        ),false) AS cmpgmodificacao_lote
			FROM
				contrato_modificacao_tipo
			INNER JOIN
        		contrato_modificacao_grupo ON (cmgoid = cmtcmgoid)
        	LEFT JOIN
        		contrato_modificacao_particularidade_tipo ON (cmptcmtoid = cmtoid)
			WHERE
				TRUE";

			if(!empty($cmtoid)) {
				$sql .= " AND cmtoid =" . intval($cmtoid) ;

			} else {

				$sql .= " AND
							cmtdt_exclusao IS NULL
						ORDER BY
							cmtdescricao";
			}

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();
		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera os dados de Motivos de Modificacao
	* Request: AJAX
	* @author   Andre Zilz
	* @param int $texto | Texto para pesquisa
	* @return array
	*/
	public function recuperarMotivoSubstituicaoAjax($texto) {

		$retorno = array();
		$i = 0;

		$sql = "
			SELECT
		        msuboid,
		        msubdescricao,
		        msubeqcoid,
		        msubtrocaveiculo
			FROM
				motivo_substituicao_modificacao_tipo
			INNER JOIN
		        motivo_substituicao ON (msuboid = msubmmsuboid)
			WHERE
				msubmdt_exclusao IS NULL
 			AND
 				msubdescricao ILIKE '%". $texto . "%'
 			ORDER BY
 				msubdescricao
			LIMIT 20
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

        while ($tupla = pg_fetch_object($rs)) {
            $retorno[$i]['id'] = $tupla->msuboid;
            $retorno[$i]['label'] = utf8_encode($tupla->msubdescricao);
            $retorno[$i]['value'] = utf8_encode($tupla->msubdescricao);
            $i++;
        }


		return $retorno;
	}

	/**
	* Recupera os dados de Motivos de Modificacao conforme alteracao de classe
	* @author   Andre Zilz
	* @param int $cmtoid | ID do Tipo de modificacao
	* @return array
	*/
	public function recuperarMotivoSubstituicaoClasse($cmtoid, $ajax) {

		$retorno = array();
		$i = 0;

		if(empty($cmtoid)){
			return $retorno;
		}

		$sql = "
			SELECT
				msuboid,
				msubdescricao,
				msubtrocaveiculo,
				msubeqcoid
			FROM
				motivo_substituicao
			INNER JOIN
				motivo_substituicao_modificacao_tipo ON (msubmmsuboid = msuboid)
			WHERE
				msubmcmtoid = ".intval($cmtoid)."
			AND
				msubexclusao IS NULL
			AND
				msubmdt_exclusao IS NULL
			AND
				EXISTS
					(
				        SELECT
				        	cmptrecebe_motivo
			        	FROM
			        		contrato_modificacao_particularidade_tipo
		        		WHERE
		        			cmptcmtoid = ".intval($cmtoid)."
	        			AND
	        				cmptrecebe_motivo IS TRUE
					)
			ORDER BY
				msubdescricao
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

        while ($tupla = pg_fetch_object($rs)) {

        	if($ajax){
        		$retorno[$i]['msuboid'] = $tupla->msuboid;
        		$retorno[$i]['msubdescricao'] = utf8_encode($tupla->msubdescricao);
        		$retorno[$i]['msubtrocaveiculo'] = $tupla->msubtrocaveiculo;
        		$retorno[$i]['msubeqcoid'] = $tupla->msubeqcoid;
        		$i++;

        	} else {
        		$retorno[] = $tupla;
        	}
        }

		return $retorno;
	}

	/**
	* Recupera os dados Cliente
	* Request: AJAX
	* @author   Andre Zilz
	* @param stdClass $parametros
	* @return array
	*/
	public function recuperarCliente($parametros) {

		$retorno = array();
		$subQuery = '';
		$join = '';

		if(!empty($parametros->tipo_pessoa)){
			$subQuery = "
				,clicformacobranca
				,clicagencia
        		,clicconta
        		,cliccartao
				,forccfbbanco
				,clidia_vcto
				";

			$join = "
					LETF JOIN
						cliente_cobranca ON (clicclioid = clioid AND clicexclusao IS NULL)
					LEFT JOIN
				  		forma_cobranca ON (forcoid = clicformacobranca AND forcexclusao IS NULL)
					";
		}

		$sql = "
			SELECT
		        clioid,
		        clinome,
		        (CASE WHEN clitipo = 'J' THEN
		        	clino_cgc
		        ELSE
		        	clino_cpf
		        END) as cpf_cnpj
				".$subQuery."
			FROM
				clientes
			".$join."
			WHERE
				clidt_exclusao IS NULL
 			AND";

		if(empty($parametros->tipo_pessoa)) {
			$sql .= " clinome ILIKE '". $parametros->texto . "%'";
		} else {

			if($parametros->tipo_pessoa == 'J'){
				$sql .= " clino_cgc::VARCHAR ILIKE '". floatval($parametros->texto) . "%'";
			} else {
				$sql .= " clino_cpf::VARCHAR ILIKE '". floatval($parametros->texto) . "%'";
			}
		}

		$sql .= "
 			ORDER BY
 				clinome
			LIMIT 10
			";

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

	 	if ($rs = pg_query($this->conn, $sql)) {

            $i = 0;
            while ($tupla = pg_fetch_object($rs)) {
                $retorno[$i]['id'] = $tupla->clioid;
                if(empty($parametros->tipo_pessoa)) {
                	$retorno[$i]['label'] 	= utf8_encode($tupla->clinome);
                	$retorno[$i]['value'] 	= utf8_encode($tupla->clinome);
            	} else {
            		$retorno[$i]['label'] 	= utf8_encode($tupla->clinome);
                	$retorno[$i]['value'] 	= $tupla->cpf_cnpj;
                	$retorno[$i]['nome'] 	= utf8_encode($tupla->clinome);
                	$retorno[$i]['forma'] 	= $tupla->clicformacobranca;
                	$retorno[$i]['banco'] 	= $tupla->forccfbbanco;
                	$retorno[$i]['agencia'] = $tupla->clicagencia;
                	$retorno[$i]['conta'] 	= $tupla->clicconta;
                	$retorno[$i]['cartao'] 	= $tupla->cliccartao;
                	$retorno[$i]['dia_vcto']= $tupla->clidia_vcto;
            	}

                $i++;
            }

        }

		return $retorno;
	}

	/**
	* Recupera os dados de Banco
	* Request: AJAX
	* @author   Andre Zilz
	* @param int $cmdfpforcoid | ID da forma de pgto
	* @return array
	*/
	public function recuperarBanco($cmdfpforcoid) {

		$retorno = array();
		 $i = 0;

		$sql = "
			SELECT
		        bancodigo,
		        bannome
			FROM
				forma_cobranca
			INNER JOIN
				banco ON (bancodigo = forccfbbanco)
			WHERE
				forcoid = ".intval($cmdfpforcoid)."
			ORDER BY
				bannome	";

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $registro->bancodigo;
            $retorno[$i]['descricao'] = utf8_encode($registro->bannome);
            $i++;
		}

		return $retorno;
	}


	/**
	* Recupera os dados de Tipo Contrato
	* @author   Andre Zilz
	* @param boolean $ajax | requisicao ajax?
	* @param  string $filtro | 14: MIGRACAO PARA EX-SEGURADO / 16: MIGRACAO COM REATIVACAO EX PARA SEGURADO / 0 = normal
	* @return array/stdClass
	*/
	public function recuperarTipoContrato($ajax, $filtro = '') {

		$retorno = array();
		$i = 0;

		switch ($filtro) {
			case '14':
				//"MIGRACAO PARA EX-SEGURADO"
				$where = " AND TRIM(tpcdescricao) ILIKE 'EX-%'";
				break;
			case '16':
				//"MIGRACAO COM REATIVACAO EX PARA SEGURADO"
				$where = " AND (tpcseguradora IS TRUE OR tpcassociacao IS TRUE ) ";
				break;
			case '8':
				//"EFETIVACAO DEMO"
				$where = " AND tpcdemonstracao IS FALSE ";
				break;
			default:
				$where = "";
				break;
		}

		$sql = "
			SELECT
				tpcoid,
				tpcdescricao,
				COALESCE(tpcanalise_credito, FALSE) AS tpcanalise_credito,
				COALESCE(tpcclioid_pagador, 0) AS tpcclioid_pagador,
				COALESCE(
					(
					SELECT
						clicformacobranca
					FROM
						cliente_cobranca
					WHERE
						clicclioid = tpcclioid_pagador
					AND
						clicexclusao IS NULL
					LIMIT 1
					)
				,0) AS clicformacobranca
			FROM
				tipo_contrato
			WHERE
				tpcativo IS TRUE
			".$where."
			ORDER BY
				tpcdescricao
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();
		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {

			if($ajax){
				$retorno[$i]['id'] = $registro->tpcoid;
				$retorno[$i]['descricao'] = utf8_encode($registro->tpcdescricao);
				$retorno[$i]['analise_credito'] = $registro->tpcanalise_credito;
				$retorno[$i]['cliente_pagador'] = $registro->tpcclioid_pagador;
				$retorno[$i]['forma_pgto'] = $registro->clicformacobranca;
				$i++;

			} else {
				$retorno[] = $registro;
			}

		}

		return $retorno;
	}

	/**
	* Recupera os dados de Classe Contrato
	* @author   Andre Zilz
	* @return array/stdClass
	*/
	public function recuperarClasseContrato() {

		$retorno = array();

		$sql = "
			SELECT
				eqcoid,
				eqcdescricao
			FROM
				equipamento_classe
			WHERE
				eqcinativo IS NULL
			AND
				eqcobroid > 0
			ORDER BY
				eqcdescricao
			";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera os dados de Classe Contrato
	* @author   Andre Zilz
	* @param  int $coneqcoid | ID classe do equipamento
	* @return array/stdClass
	*/
	public function isClasseCargaSASTM($coneqcoid) {

		$sql = "
			SELECT EXISTS
				(
				SELECT
					eqcoid
				FROM
					equipamento_classe
				WHERE
					eqcoid IN
					        (SELECT
					                eqcoid
					        FROM
					                equipamento_classe
					        WHERE
					                eqcinativo IS NULL
					        AND
					        	eqcdescricao ILIKE '%SASTM%'
					        OR
					        	eqcdescricao ILIKE '%CARGA%'
					        )
				AND
					eqcoid = ".intval($coneqcoid)."
			) AS is_carga_sastm
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		$registro = pg_fetch_object($rs);

		return ($registro->is_carga_sastm == 't') ? TRUE : FALSE;
	}

	/**
	* Recupera os dados de Contrato Vigencia
	* @author   Andre Zilz
	* @param int $cvgvigencia
	* @return array/stdClass
	*/
	public function recuperarContratoVigencia($cvgvigencia = '') {

		$retorno = array();

		if($cvgvigencia != '') {
			$filtro = " AND cvgvigencia =" . intval($cvgvigencia);
		}


		$sql = "
			SELECT
				cvgoid,
				cvgvigencia
			FROM
				contrato_vigencia
			WHERE
				cvgdt_exclusao IS NULL
			".$filtro."
			ORDER BY
				cvgvigencia;
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera dados de forma de pagamento
	* @author   Andre Zilz
	* @return array/stdClass
	*/
	public function recuperarFormaPagamento() {

		$retorno = array();

		$sql = "
			SELECT
				forcoid,
			    forcnome,
		         COALESCE(forcaccoid, 0) AS accrecodautorizadora,
		        (
	        		CASE WHEN forccobranca_cartao_credito IS TRUE THEN
		        		'credito'
		        	WHEN forcdebito_conta IS TRUE THEN
		        		'debito'
		        	ELSE
		        		'outra'
		        	END
	        	) AS forma
			FROM
				forma_cobranca
			WHERE
				forcvenda IS TRUE
			AND
				forcexclusao IS NULL
			AND (
					forccobranca_cartao_credito IS FALSE
    			OR
    				(forccobranca_cartao_credito IS TRUE AND forcaccoid IS NOT NULL)
     			)
			ORDER BY
				forcnome;
			";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera dados de parcelamento
	* @author   Andre Zilz
	* @return array/stdClass
	*/
	public function recuperarParcelamento() {

		$retorno = array();

		$sql = "
			SELECT
				cpvoid,
				cpvdescricao,
				cpvparcela
			FROM
				cond_pgto_venda
			WHERE
				cpvexclusao IS NULL
			AND
				cpvdescricao NOT ILIKE '%juros%'
			ORDER BY
				cpvdescricao;
			";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera dados de acessorios
	* @author Andre Zilz
	* @param int $cpvoid | ID da parcela
	* @return array/stdClass
	*/
	public function recuperarAcessorio($cpvoid) {

		$retorno = array();
		$i = 0;

		$sql = "
			SELECT
		        obroid,
		        tpivalor,
		        obrobrigacao
			FROM
		        obrigacao_financeira
			INNER JOIN
		        tabela_preco_item ON (obroid = tpiobroid)
			INNER JOIN
		        tabela_preco ON (tproid = tpitproid)
			WHERE
				tprstatus = 'A'
			AND
				tpraprovacao IS NOT NULL
			AND
				obrdt_exclusao IS NULL
			AND
				obroftoid IN (3,4,5,9)
			AND
				tpiexclusao IS NULL
			AND
				tpicpvoid = ".intval($cpvoid)."
			ORDER BY
				obrobrigacao
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $registro->obroid;
			$retorno[$i]['valor'] = $registro->tpivalor;
            $retorno[$i]['descricao'] = utf8_encode($registro->obrobrigacao);
            $i++;
		}

		return $retorno;
	}

	/**
	* Recupera dados de acessorios associados a uma modificacao
	* @author Andre Zilz
	* @param int $mdfoid | ID da Modificacao
	* @return array/stdClass
	*/
	public function recuperarAcessorioPorModificacao($mdfoid) {

		$retorno = array();

		$sql = "
			SELECT
				DISTINCT
		        obroid,
		        obrobrigacao
			FROM
		        contrato_modificacao_servico
			INNER JOIN
		        contrato_modificacao ON (cmfoid = cmscmfoid)
			INNER JOIN
		        obrigacao_financeira ON (obroid = cmsobroid)
			WHERE
		        cmfmdfoid = ".intval($mdfoid)."
			ORDER BY
		        obrobrigacao
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera dados de acessorios associados a um contrato/modificacao
	* @author Andre Zilz
	* @param int $cmfoid | ID da contrato_modificacao
	* @return array/stdClass
	*/
	public function recuperarAcessorioPorContratoModificacao($cmfoid) {

		$retorno = array();

		$sql = "
			SELECT
		        cmsoid,
				cmscmfoid,
				cmssituacao,
				cmsobroid,
				cmsqtde,
				cmsvalor_tabela,
				cmsvalor_negociado,
				cmscpvoid
			FROM
		        contrato_modificacao_servico
			INNER JOIN
		        contrato_modificacao ON (cmfoid = cmscmfoid)
			WHERE
		        cmscmfoid = ".intval($cmfoid)."
		    AND
		    	cmsdt_exclusao IS NULL
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera valor de monitoramento
	* @author  Andre Zilz
	* @param  int $eqcoid | ID de classe do equipamento
	* @return array/stdClass
	*/
	public function recuperarValorMonitoramento($eqcoid) {

		$retorno = new stdClass();
		$sql = "
			SELECT
				eqcvlr_mens,
				eqcvlr_minimo_mens,
				eqcvlr_maximo_mens
			FROM
				equipamento_classe
			WHERE
				eqcoid = ".intval($eqcoid)."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();
		$rs = $this->executarQuery($sql);

		if(pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

	/**
	* Recupera os dados de mensalidade e locacao do contrato
	* @author   Andre Zilz
	* @param int $connumero
	* @return array/stdClass
	*/
	public function recuperarMonitoramentoLocacaoContrato($connumero) {

		$retorno = new stdClass();

		$sql = "
			SELECT
        		cpagmonitoramento,
        		cpagcpvoid,
        		cpagvl_servico
			FROM
				contrato_pagamento
			WHERE
				cpagconoid = ". intval($connumero)."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		if (pg_num_rows($rs) > 0) {
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

	/**
	* Recupera valor de locacao
	* @author   Andre Zilz
	* @param  int $parametros | objeto de dados
	* @return array/stdClass
	*/
	public function recuperarValorLocacao($parametros) {

		$retorno = new stdClass();

		$sql = "
			SELECT
				tpivalor,
				tpivalor_minimo
			FROM
				tabela_preco_item
			INNER JOIN
				tabela_preco ON (tpitproid=tproid)
			WHERE
				tpiexclusao IS NULL
			AND
				tprstatus='A'
			AND
				tpicpvoid = ".intval($parametros->cmdfpcpvoid)."
			AND
				tpiobroid = (
							SELECT
								eqcobroid
							FROM
								equipamento_classe
							WHERE
								eqcoid = ".intval($parametros->eqcoid)."
							)
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		if(pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}
		return $retorno;
	}

	/**
	* Recupera valor de taxas
	* @author  Andre Zilz
	* @return array/stdClass
	*/
	public function recuperarTaxas() {

		$retorno = array();

		$sql = "
			SELECT
				obroid,
				obrobrigacao,
				obrvl_obrigacao
			FROM
				obrigacao_financeira
			WHERE
				obroftoid IN (2,6) --Locação de Upgrade / Taxas
			AND
				obrdt_exclusao IS NULL
			ORDER BY
				obrobrigacao
			";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}


	/**
	* Recupera os dias de vencimentos
	* @author  Andre Zilz
	* @return array/stdClass
	*/
	public function recuperarDiasVencimento() {

		$retorno = array();

		$sql = "
			SELECT
				cdvoid,
				cdvdia
			FROM
				cliente_dia_vcto
			WHERE
				cdvdt_exclusao IS NULL
			ORDER BY
				cdvdia
			";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera os dados de Executivos
	* @author   Andre Zilz
	* @return array/stdClass
	*/
	public function recuperarExecutivo() {

		$retorno = array();

		$sql = "
			SELECT
				funoid,
				funnome,
				COALESCE(funrczoid::VARCHAR, '') AS funrczoid
			FROM
				funcionario
			WHERE
				funcargo IN (
							SELECT
								UNNEST(STRING_TO_ARRAY(sisexecutivo_vendas,','))::INT
							FROM
								sistema
							)
			AND
				funexclusao IS NULL
			AND
				fundemissao IS NULL
			ORDER BY
				funnome
			";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Recupera o ID da proposta original do contrato
	 * @param  int $connumero
	 * @return int
	 */
	public function recuperarTipoPropostaContrato($connumero) {

		$retorno = 0;

		$sql = "SELECT
			        tppoid
				FROM
			        proposta
				INNER JOIN
			        tipo_proposta ON (prptppoid = tppoid)
				WHERE
			        (
				        prptermo = ".intval($connumero)."
				        OR
				        prpoid = (SELECT pritprpoid FROM proposta_item WHERE pritprptermo = ".intval($connumero).")
			        )
				LIMIT 1
				";

		$rs = $this->executarQuery($sql);

		if(pg_num_rows($rs) > 0) {
			$retorno = pg_fetch_result($rs, 0, 'tppoid');
		}

		return $retorno;
	}

	/**
	* Recupera os dados de Tipo Proposta
	* @author   Andre Zilz
	* @return array/stdClass
	*/
	public function recuperarTipoProposta() {

		$retorno = array();

		$sql = "
			SELECT
				tppoid,
				tppdescricao
			FROM
				tipo_proposta
			WHERE
				tppdt_exclusao IS NULL
			AND
				tppoid IN (
							SELECT DISTINCT
								tppoid_supertipo
							FROM
								tipo_proposta
							WHERE
								tppoid_supertipo IS NOT NULL
							)
			";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera dados de um subtipo de proposta
	*
	* @author   Andre Zilz
	* @param int $cmftppoid | ID do tipo proposta
	* @param boolean $ajax
	* @return array
	*/
	public function recuperarSubTipoProposta($cmftppoid, $ajax) {

		$retorno = array();
		 $i = 0;

		$sql = "
			SELECT
				tppoid,
				tppdescricao
			FROM
				tipo_proposta
			WHERE
				tppdt_exclusao IS NULL
			AND
				tppoid_supertipo =" . intval($cmftppoid);

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

		if($ajax){

			while($registro = pg_fetch_object($rs)) {
				$retorno[$i]['id'] = $registro->tppoid;
	            $retorno[$i]['descricao'] = utf8_encode($registro->tppdescricao);
	            $i++;
			}
		} else {
			while($registro = pg_fetch_object($rs)) {
				$retorno[] = $registro;
			}
		}

		return $retorno;
	}


	/**
	* Verifica o status de aprovacao de credito de um determinado cliente
	*
	* @author Andre L. Zilz
	* @param int $clioid
	*/
	public function verificarAprovacaoCredito($clioid) {

		$retorno = new stdClass();

		$sql = "
			SELECT
				cmacoid,
				cmacstatus,
				(cmacdt_liberacao_limite >= NOW()::DATE) AS is_data_limite,
				cmacliberado_periodo_indeterminado
			FROM
				cliente_modificacao_analise_credito
			WHERE
				cmacclioid = ".intval($clioid)."
			ORDER BY
				cmacoid DESC
			LIMIT 1
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

		$registro = pg_fetch_object($rs);

		$retorno->status 				= isset($registro->cmacstatus) 							? $registro->cmacstatus 						: '';
		$retorno->is_data_limite 		= isset($registro->is_data_limite) 						? $registro->is_data_limite 					: '';
		$retorno->periodo_indeterminado = isset($registro->cmacliberado_periodo_indeterminado) 	? $registro->cmacliberado_periodo_indeterminado : '';
		$retorno->cmacoid 				= isset($registro->cmacoid) 							? $registro->cmacoid 							: '';

		return $retorno;

	}

	/**
	* Verifica se o contrato ja esta associado a uma modificacao ativa
	*
	* @author Andre L. Zilz
	* @param int $connumero
	*/
	public function verificarContratoDisponivel($connumero) {

		$sql = "
			SELECT EXISTS(
					SELECT
						1
					FROM
				        modificacao
					INNER JOIN
				        contrato_modificacao ON (cmfmdfoid = mdfoid)
					WHERE
				        cmfconnumero = ".intval($connumero)."
					AND
				        (mdfstatus = 'E' OR  mdfstatus = 'P' OR  mdfstatus = 'A')
					) AS nao_liberado
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		$row = pg_fetch_object($rs);
		return ($row->nao_liberado == 't') ? TRUE : FALSE;

	}

	/**
	* Insere dados de analise de credito para um cliente
	* @author   Andre Zilz
	* @param int $clioid | ID do cliente
	* @param string $observacao
	* @return boolean
	*/
	public function persistirAnaliseCredito($clioid, $observacao) {

		$sql = "
			INSERT INTO
				cliente_modificacao_analise_credito
			(
				cmacclioid,
				cmacusuoid_solicitante,
				cmacobservacao
			)
			VALUES
			(
				".intval($clioid).",
				".intval($this->usuoid).",
				'".$observacao."'
			)
			RETURNING
				cmacoid
		";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		return pg_fetch_result($rs, 0, 'cmacoid');

	}

	/**
	* Recupera os dados da modificacao contrato
	*
	* @author   Andre Zilz
	* @param int $connumero | numero do contrato
	* @param int $mdfoid | numero da modificacao
	* @return array
	*/
	public function recuperarModificacaoContrato($connumero, $mdfoid = '') {

		$retorno = array();

		if(!empty($mdfoid)){
			$where = " AND mdfoid =" . intval($mdfoid) . "";
		}

		$sql = "
			SELECT
				TO_CHAR(mdfdt_cadastro, 'dd/mm/yyyy hh24:mi') AS data,
				mdfoid,
				mdfstatus AS status,
				mdfobservacao_modificacao AS observacao,
				cmtdescricao AS tipo,
				cmfoid,
				cmfordoid AS os,
				cmfconnumero,
				cmfeqcoid_origem,
				cmftpcoid_origem,
				cmfconnumero_novo AS contrato_novo,
				(SELECT msubdescricao FROM motivo_substituicao WHERE mdfmsuboid = msuboid) AS motivo,
				(SELECT tpcdescricao FROM tipo_contrato WHERE tpcoid = cmftpcoid_origem) AS tipo_contrato_original,
				(SELECT tpcdescricao FROM tipo_contrato WHERE tpcoid = cmftpcoid_destino) AS tipo_contrato_novo,
				(CASE WHEN
					cmfmodificacao_desfeita IS NULL
				THEN 'Não'
				ELSE 'Sim'
				END) AS revertido,
				nm_usuario as ususario
			FROM
				modificacao
			INNER JOIN
				contrato_modificacao ON (cmfmdfoid = mdfoid)
			INNER JOIN
				contrato_modificacao_tipo ON (mdfcmtoid = cmtoid)
			INNER JOIN
				usuarios ON (mdfusuoid_cadastro = cd_usuario)
			WHERE
				cmfconnumero = ".intval($connumero)."
			".$where."
			ORDER BY
				mdfdt_cadastro";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera os dados da modificacao contrato
	*
	* @author Andre Zilz
	* @param int $mdfoid | ID da modificacao
	* @param boolean $limit | Define se usara limitador de resultados
	* @param array $campos | campos de pesquisa
	* @return array
	*/
	public function recuperarContratoModificacao($mdfoid, $limit = false, $campos = '') {

		$retorno = array();

		if(empty($campos)) {
			$campos = array(
				"cmfoid",
				"cmfclioid_origem",
				"cmfclioid_destino",
				"cmfconnumero",
				"cmfconnumero_novo",
				"cmfordoid",
				"cmfprpoid",
				"cmfveioid",
				"(SELECT veiplaca FROM veiculo WHERE veioid = cmfveioid)  AS placa",
				"(SELECT veichassi FROM veiculo WHERE veioid = cmfveioid)  AS chassi",
                "(SELECT eqcdescricao FROM equipamento_classe WHERE eqcoid = cmfeqcoid_origem) classe_original",
				"(SELECT eqcdescricao FROM equipamento_classe WHERE eqcoid = cmfeqcoid_destino) classe_nova",
				"(SELECT tpcdescricao FROM tipo_contrato WHERE tpcoid = cmftpcoid_destino) AS tipo_contrato_novo",
				"(SELECT tpcdescricao FROM tipo_contrato WHERE tpcoid = cmftpcoid_origem) AS tipo_contrato_original");
		}

		$sql = "
			SELECT
				".implode(',', $campos)."
			FROM
				contrato_modificacao
			WHERE
				cmfmdfoid = ".intval($mdfoid)."
			";

		if($limit) {
			$sql .= " LIMIT 1";
		}

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();
		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;

	}

	/**
	* Recupera os dados da modificacao contrato
	*
	* @author   Andre Zilz
	* @param int $cmfoid | ID da tabela
	* @param array $campos | campos para pesquisa
	* @return stdClass
	*/
	public function recuperarContratoModificacaoPorID($cmfoid, $campos) {

		$retorno = new stdClass();

		$sql = "
			SELECT
				".implode(',', $campos)."
			FROM
				contrato_modificacao
			INNER JOIN
				veiculo ON veioid = (SELECT conveioid FROM contrato WHERE connumero = cmfconnumero)
			WHERE
				cmfoid = ".intval($cmfoid)."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		if(pg_num_rows($rs) > 0) {
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;

	}

	/**
	* Recupera os dados do historico de uma determinada modificacao
	*
	* @author   Andre Zilz
	* @param int $mdfoid | ID da modificacao
	* @return array
	*/
	public function recuperarHistoricoModificacao($mdfoid) {

        $retorno = array();

		$sql = "
			SELECT
				hmoid,
			  	hmmdfoid,
			  	hmobs,
			  	hmusuoid,
			  	TO_CHAR(hmdt_cadastro, 'dd/mm/yyyy hh24:mi') AS hmdt_cadastro,
			  	nm_usuario AS usuario
			FROM
				historico_modificacao
			INNER JOIN
				usuarios ON (cd_usuario = hmusuoid)
			WHERE
				hmmdfoid = ".intval($mdfoid)."
			ORDER BY
				hmoid DESC
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		while($tupla = pg_fetch_object($rs)) {
			$retorno[] = $tupla;
		}

		return $retorno;

    }

    /**
	* Insere o histórico da modificacao
	*
	* @author Andre L. Zilz
	* @param int $mdfoid | ID da modificacao
	* @param string $obs
	*/
	public function inserirHistoricoModificacao($mdfoid, $obs) {

		$sql = "
			INSERT INTO
				historico_modificacao".($mdfoid % 10)."
			(
				hmmdfoid,
				hmobs,
				hmusuoid
			)
			VALUES
			(
				".intval($mdfoid).",
				'".$obs."',
				".intval($this->usuoid)."
			)
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

	}

	/**
	 * Relaciona um modificacao com a solicitacaod e analise de credito
	 * @param  int $mdfoid | ID da modificacao
	 * @param  int  $cmacoid | ID da tabela cliente_modificacao_analise_credito
	 * @return  void
	 */
	public function vincularAnaliseCreditoModificacao($mdfoid, $cmacoid) {

		$sql = "
			UPDATE
				modificacao
			SET
				mdfcmacoid = ".intval($cmacoid)."
			WHERE
				mdfoid = ".intval($mdfoid)."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

	}

	/**
	* Verifica se um contrato e do tipo SIGGO
	*
	* @author   Andre Zilz
	* @param int $connumero | ID do contrato
	* @return boolean
	*/
	public function isContratoSiggo($connumero) {

		$sql = "
			SELECT EXISTS(
						SELECT
							1
						FROM
							proposta
						INNER JOIN
						        tipo_proposta ON (prptppoid = tppoid)
						WHERE
						        tppoid_supertipo = 12
						AND
							(
						        prptermo = ".intval($connumero)."
					        	OR
						        prpoid = (SELECT pritprpoid FROM proposta_item WHERE pritprptermo = ".intval($connumero).")
							)
						) AS is_siggo
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		$row = pg_fetch_object($rs);

		return ($row->is_siggo == 't') ? TRUE : FALSE;
	}


	/**
	* Recupera os dados de pagamento da modificacao contrato
	*
	* @author   Andre Zilz
	* @param int $mdfoid | ID do Contrato Modificacao
	* @return array
	*/
	public function recuperarDadosPagamentoModificacao($mdfoid) {

		$retorno = new stdClass();

		$sql = "
			SELECT
                cmdfpvlr_monitoramento_negociado AS monitoramento,
                cmdfpvlr_locacao_negociado AS locacao,
                cmdfpvlr_taxa_negociado AS taxa,
                cmdfpisencao_taxa AS taxa_isencao,
                cmdfppagar_cartao,
                (SELECT obrobrigacao FROM obrigacao_financeira WHERE obroid = cmdfpobroid_taxa) AS taxa_descricao,
                (SELECT forcnome FROM forma_cobranca WHERE forcoid = cmdfpforcoid) AS forma_pgto
			FROM
				contrato_modificacao_pagamento
			WHERE
				cmdfpmdfoid = ".intval($mdfoid)."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

		$retorno = pg_fetch_object($rs);

		return $retorno;

	}

	/**
	* Recupera os dados de pagamento da modificacao contrato
	*
	* @author   Andre Zilz
	* @param int $mdfoid | ID do Contrato Modificacao
	* @return array
	*/
	public function recuperarDadosPagamentoCartao($mdfoid) {

		$retorno = new stdClass();

		$sql = "
			SELECT
				cmdfpcartao,
				cmdfpnome_portador,
	            cmdfpcartao_vencimento,
	            cmdfpvlr_taxa_negociado,
	            cmdfpobroid_taxa,
	            cmdfpnum_parcela,
	            cmdfpforcoid,
	            cmdfpvencimento_fatura,
	            obrobrigacao
			FROM
				contrato_modificacao_pagamento
			INNER JOIN
        		obrigacao_financeira ON (obroid = cmdfpobroid_taxa)
			WHERE
				cmdfpmdfoid = ".intval($mdfoid)."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

		$retorno = pg_fetch_object($rs);

		return $retorno;

	}

	/**
	* Recupera o numero de proposta de um contrato SIGGO
	*
	* @author   Andre Zilz
	* @param int $connumero
	* @return int
	*/
	public function recuperarPropostaContrato($connumero) {

		$retorno = new stdClass();
		$proposta = 0;

		$sql = "
		        SELECT
		        	prpoid
	        	FROM
	        		proposta
        		WHERE
        			prptermo = ".intval($connumero) ;

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		if(pg_num_rows($rs) > 0) {

			$retorno = pg_fetch_object($rs);
			$proposta = $retorno->prpoid;

		} else {

			$sql = "
		       	SELECT
		       		pritprpoid
	       		FROM
	       			proposta_item
       			WHERE
       				pritprptermo =  ".intval($connumero) ;

			//echo "<pre>";var_dump($sql);echo "</pre>";exit();

			$rs = $this->executarQuery($sql);

			if(pg_num_rows($rs) > 0) {
				$retorno = pg_fetch_object($rs);
				$proposta = $retorno->pritprpoid;
			}

		}

		return $proposta;

	}

	/**
	* Recupera os dados da Ordem Servico
	*
	* @author Andre Zilz
	* @param int $ordoid | Numero do contrato
	* @param array $campos | campos para pesquisa
	* @return array
	*/
	public function recuperarDadosOrdemServico($ordoid, $campos, $where = '') {

		$retorno = array();

		$sql = "
			SELECT
		        ".implode(',',$campos)."
			FROM
                ordem_servico
		    INNER JOIN
		        ordem_servico_item ON (ositordoid = ordoid)
		    INNER JOIN
		        os_tipo_item ON (otioid = ositotioid)
		    INNER JOIN
		        os_tipo ON (ostoid = otiostoid)
		    INNER JOIN
        		ordem_servico_status ON (ossoid = ordstatus AND ordstatus NOT IN (6,9,18))
		    WHERE
		        ordoid = ".intval($ordoid)."
		    ".$where."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera os dados de contratos para Downgrade / Upgrade
	*
	* @author   Andre Zilz
	* @param stdClass $parametros | dados tratados do formulario
	* @return array
	*/
	public function recuperarContratoDowngradeUpgrade($parametros) {

		$retorno = array();

		$sql = "
			SELECT
	           connumero,
	           veiplaca,
	           veichassi,

	           (SELECT eqcdescricao FROM equipamento_classe WHERE coneqcoid = eqcoid) AS classe_original,

	           (SELECT eqcdescricao FROM equipamento_classe WHERE msubeqcoid = eqcoid) AS classe_nova,

	           (
	           	SELECT
	                (SELECT eveprojeto=ANY(eqciprojeto) FROM equipamento_classe_instalacao WHERE eqcieqcoid = eqcoid AND eqciexclusao IS NULL)
	           	FROM
	           		equipamento_classe
	       		INNER JOIN
	           		equipamento ON (equoid = conequoid)
	           	INNER JOIN
	           		equipamento_versao ON (eveoid = equeveoid)
	           	WHERE
	           		msubeqcoid = eqcoid
	       		) AS compativel,

	           (
	           	SELECT
	           		STRING_AGG(obrobrigacao, '; ') AS acessorios
	           	FROM
	           		obrigacao_financeira
	       		INNER JOIN
	           		obrigacao_financeira_item ON (ofiservico = obroid)
	       		INNER JOIN
	           		equipamento_classe ON (eqcoid = msubeqcoid AND eqcobroid = ofiobroid)
	           WHERE
	           		ofiexclusao IS NULL
	           	AND
	           		ofiservico NOT IN (
	           							SELECT
	           								consobroid
	       								FROM
	       									contrato_servico
	                   					WHERE
	                   						consconoid = connumero
	               						AND
	               							consobroid = ofiservico
	           							AND
	           								consiexclusao IS NOT NULL
	       								AND
	       									consinstalacao IS NULL
	   									)
				) AS acessorios
			FROM
			           contrato
			INNER JOIN
			           clientes ON (clioid = conclioid)
			INNER JOIN
			           veiculo ON (veioid = conveioid)
			INNER JOIN
			           motivo_substituicao ON (msubeqcoid_orig = coneqcoid)
			WHERE
			        condt_exclusao IS NULL
			AND
			        conequoid > 0
			AND
			        concsioid NOT IN (
			        					SELECT
			        						cmtrscsioid
		        						FROM
		        							contrato_modificacao_tipo_restricao_status
	        							WHERE
	        								cmtrscsioid = concsioid
        								AND
        									cmtrsdt_exclusao IS NULL
    									AND
    										cmtrscmtoid = ".intval($parametros->cmtoid)."
										)
			AND
			        clioid = ".intval($parametros->cmfclioid_destino)."
			AND
			        msuboid = ".intval($parametros->msuboid)."

			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {

			$registro->compativel = ($registro->compativel == 't') ? 'Sim' : 'Não';

			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera os dados do contrato
	* @author   Andre Zilz
	* @param int $connumero | Numero do contrato
	* @param array $campos | campos para pesquisa
	* @return array
	*/
	public function recuperarDadosContrato($connumero, $campos) {

		$retorno = new stdclass();

		$sql = "
			SELECT
		        ".implode(',',$campos)."
			FROM
				contrato
			WHERE
				connumero = ".intval($connumero)."
			AND
				condt_exclusao IS NULL
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

		if(pg_num_rows($rs) > 0) {
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

	/**
	* Verifica se ha restricao para a stituacao do contrato
	* @author   Andre Zilz
	* @param int $connumero | Numero do contrato
	* @return string
	*/
	public function verificarRestricaoContrato($csioid, $cmtoid) {

		$sql = "
			SELECT
				csidescricao
			FROM
				contrato_modificacao_tipo_restricao_status
			INNER JOIN
				contrato_situacao ON (csioid = cmtrscsioid)
			WHERE
			    cmtrscsioid = ".intval($csioid)."
			AND
				cmtrscmtoid = ".intval($cmtoid)."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		$registro = pg_fetch_object($rs);
		$retorno = isset($registro->csidescricao) ? $registro->csidescricao : '';

		return $retorno;
	}

	/**
	* Verifica se contrato informado existe na base
	* @author   Andre Zilz
	* @param int $connumero | Numero do contrato
	* @return boolean
	*/
	public function isExisteContrato($connumero) {

		$retorno = false;

		$sql = "
			SELECT EXISTS
					(
						SELECT
					        1
						FROM
							contrato
						WHERE
							connumero = ".intval($connumero)."
						AND
							condt_exclusao IS NULL
					) AS existe
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		$registro = pg_fetch_object($rs);

		$retorno = ($registro->existe == 't') ? true : false;

		return $retorno;
	}

	/**
	* Verifica se o contrato e roubado nao recuperado
	* @author   Andre Zilz
	* @param int $connumero | Numero do contrato
	* @return array
	*/
	public function isContratoRoubado($connumero) {

		$retorno = false;

		$sql = "
			SELECT EXISTS (
					SELECT
						1
					FROM
						contrato
					INNER JOIN
						contrato_situacao ON (csioid = concsioid )
					WHERE
						csiroubado_nao_recuperado IS TRUE
					AND
						(
							connumero_novo_rnr IS NULL
							OR
							(connumero_novo_rnr NOT IN (
														SELECT
															c.connumero
														FROM
															contrato c
														WHERE
															c.connumero = connumero_novo_rnr
														AND
															c.condt_exclusao IS NULL
														)
							)
						)
					AND
						connumero = ".intval($connumero)."
					) AS is_roubado
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		$registro = pg_fetch_object($rs);

		$retorno = ($registro->is_roubado == 't') ? true : false;

		return $retorno;
	}

	/**
	* Recupera os dados da modificacao
	* @author   Andre Zilz
	* @param int $mdfoid | ID da modificacao
	* @param array $campos | campos para pesquisa
	* @return array
	*/
	public function recuperarDadosModificacao($mdfoid, $campos) {

		$retorno = new stdClass();

		$sql = "
			SELECT
		        ".implode(',',$campos)."
			FROM
				modificacao
			INNER JOIN
				contrato_modificacao_tipo ON (cmtoid = mdfcmtoid)
			WHERE
				mdfoid = ".intval($mdfoid)."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

		$retorno = pg_fetch_object($rs);

		return $retorno;
	}

	/**
	* recupera informacoes de particularidades de cliente
	* @author Andre L. Zilz
	* @param array $campos | campos para pesquisa
	* @param int $clioid | ID do cliente
	* @return array | stdClass
	*/
	public function recuperarClienteParticularidade($campos, $clioid){

		$retorno = new stdClass();

		$sql = "
			SELECT
		        ".implode(',',$campos)."
			FROM
				cliente_particularidade
			WHERE
				clipclioid = ".intval($clioid)."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

		$retorno = pg_fetch_object($rs);

		return $retorno;
	}

	/**
	* recupera informacoes de anexos do contrato
	* @author Andre L. Zilz
	* @param array $campos | campos para pesquisa
	* @param int $clioid | ID do cliente
	* @return array | stdClass
	*/
	public function recuperarNovosAnexos($mdfoid){

		$retorno = array();

		$sql = "
			SELECT
		        cmalocal AS local,
		        cmanome_arquivo AS nome_arquivo
			FROM
				contrato_modificacao_anexo
			WHERE
				cmamdfoid = ".intval($mdfoid)."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* recupera informacoes de anexos do contrato
	* @author Andre L. Zilz
	* @param array $connumero | Numero do contrato
	* @return array | stdClass
	*/
	public function recuperarAnexosContratoOrigem($connumero){

		$retorno = array();

		$sql = "
			SELECT
				cnxlocal AS local,
				cnxdescricao AS nome_arquivo
			FROM
				contrato_anexo
			WHERE
				cnxconnumero = ".intval($connumero)."
			AND
				cnxexclusao IS NULL
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}


	/**
	* Inativa um registro na tabela contrato_anexo[X]
	*
	* @author Andre L. Zilz
	* @param int $connumero | Numero do contrato
	*/
	public function inativarDadosAnexoContrato($connumero) {

		$sql = "
			UPDATE
				contrato_anexo".($connumero % 10)."
			SET
				cnxexclusao = NOW(),
				cnxusuexclusao = ".intval($this->usuoid)."
			WHERE
				cnxconnumero = ".intval($connumero)."
			AND
				cnxusuexclusao IS NULL
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

	}

	/**
	* Insere um registro na tabela contrato_anexo[X]
	*
	* @author Andre L. Zilz
	* @param stdClass $anexos | Dados dos anexos
	* @param int $connumero | Numero do contrato
	*/
	public function inserirDadosAnexoContrato($anexos, $connumero) {

		foreach ($anexos as $anexo) {

			$sql = "
				INSERT INTO
					contrato_anexo".($connumero % 10)."
				(
					cnxconnumero,
					cnxusuinclusao,
					cnxlocal,
					cnxdescricao
				)
				VALUES
				(
					".intval($connumero).",
					".intval($this->usuoid).",
					'".addslashes($anexo->local)."',
					'".addslashes($anexo->nome_arquivo)."'
				)
				";

			//echo "<pre>";var_dump($sql);echo "</pre>";exit();

			$rs = $this->executarQuery($sql);

		}

	}

	/**
	* Insere um registro na tabela [contrato_modificacao_anexo]
	*
	* @author Andre L. Zilz
	* @param stdClass $parametros
	*/
	public function inserirDadosAnexoModificacao($parametros) {

		$sqlPrepare = "
			PREPARE
				insert_contrato_modificacao_anexo AS
			INSERT INTO
				contrato_modificacao_anexo
			(
				cmamdfoid,
				cmaconnumero,
				cmalocal,
				cmanome_arquivo
			)
			VALUES
			($1, $2, $3, $4)
			";

		//echo "<pre>";var_dump($sqlPrepare);echo "</pre>";exit();

		$rs = $this->executarQuery($sqlPrepare);

		//Insert
		foreach ($parametros->anexos as $anexo) {

			$sqlExecute = "
				EXECUTE
					insert_contrato_modificacao_anexo
				(
					".intval($parametros->mdfoid).",
					".intval($parametros->cmfconnumero).",
					'".$anexo['local']."',
					'".$anexo['nome']."'
				)
				";

			//echo "<pre>";var_dump($sqlExecute);echo "</pre>";exit();

			$rs = $this->executarQuery($sqlExecute);

		}

		unset($parametros);

	}


	/**
	* Excluiu registros na tabela [contrato_modificacao_anexo]
	*
	* @author Andre L. Zilz
	* @param int $mdfoid
	*/
	public function excluirDadosAnexo($mdfoid) {

		$sql = "
			DELETE FROM
				contrato_modificacao_anexo
			WHERE
				cmamdfoid = ".intval($mdfoid)."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

	}


	/**
	* Insere um registro na tabela modificacao
	*
	* @author Andre L. Zilz
	* @param stdClass $parametros
	* @return int
	*/
	public function inserirModificacao($parametros) {

		$retorno = 0;

		$sql = "
			INSERT INTO
				modificacao
			(
				mdfusuoid_cadastro,
				mdfcmtoid,
				mdfmsuboid,
				mdfobservacao_modificacao,
				mdfobservacao_analise_credito,
				mdfclioid
			)
			VALUES
			(
				".intval($this->usuoid).",
				".intval($parametros->cmtoid).",
				".$parametros->msuboid.",
				'".$parametros->observacao."',
				'".$parametros->observacao_serasa."',
				".$parametros->cmfclioid_destino."
			)
			RETURNING
				mdfoid
			";

		$rs = $this->executarQuery($sql);

		if(pg_affected_rows($rs) > 0) {
			$registro = pg_fetch_object($rs);
			$retorno = $registro->mdfoid;
		}

		unset($parametros);
		return $retorno;

	}

	/**
	* Insere um registro na tabela contrato_modificacao
	*
	* @author Andre L. Zilz
	* @param stdClass $parametros
	* @return array
	*/
	public function inserirContratoModificacao($parametros) {

		$idsInseridos = array();

		$sqlPrepare = "
			PREPARE
				insert_contrato_modificacao AS
			INSERT INTO
				contrato_modificacao
			(
				cmfmdfoid,
				cmfconnumero,
				cmfeqcoid_origem,
				cmfeqcoid_destino,
				cmftpcoid_origem,
				cmftpcoid_destino,
				cmfclioid_origem,
				cmfclioid_destino,
				cmfcvgoid,
				cmfrczoid,
				cmffunoid_executivo,
				cmftppoid,
				cmftppoid_subtitpo,
				cmfveioid,
				cmfequoid,
				cmfveioid_novo,
				cmfdt_primerira_instalacao
			)
			VALUES
			($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16, $17)
			RETURNING
				cmfoid
			";

		//echo "<pre>";var_dump($sqlPrepare);echo "</pre>";exit();

		$rs = $this->executarQuery($sqlPrepare);

		//Insert
		foreach ($parametros->contrato as $contrato) {

			//define qual dado vai usar
			$cmfconnumero 		= empty($parametros->cmfconnumero) 		? $contrato->connumero : $parametros->cmfconnumero;
			$cmfclioid_destino	= (intval($parametros->cmfclioid_destino) == 0) ? $contrato->conclioid : $parametros->cmfclioid_destino;
			$veioid 			= empty($parametros->cmfveioid) ? $contrato->conveioid : $parametros->cmfveioid;

			$sqlExecute = "
				EXECUTE
					insert_contrato_modificacao
				(
					".intval($parametros->mdfoid).",
					".intval($cmfconnumero).",
					".$contrato->coneqcoid.",
					".intval($parametros->cmfeqcoid_destino).",
					".intval($contrato->conno_tipo).",
					".$parametros->cmftpcoid_destino.",
					".intval($contrato->conclioid).",
					".intval($cmfclioid_destino).",
					".$parametros->cmfcvgoid.",
					".$parametros->cmfrczoid.",
					".$parametros->cmffunoid_executivo.",
					".$parametros->cmftppoid.",
					".$parametros->cmftppoid_subtitpo.",
					".$veioid.",
					".$contrato->conequoid.",
					".$parametros->cmfveioid_novo .",
					".$contrato->condt_primeira_instalacao."
				)
				";

			//echo "<pre>";var_dump($sqlExecute);echo "</pre>";exit();

			$rs = $this->executarQuery($sqlExecute);

			if(pg_affected_rows($rs) > 0) {
				$registro = pg_fetch_object($rs);
				$idsInseridos[] = $registro->cmfoid;
			}


		}

		unset($parametros);
		return $idsInseridos;

	}

	/**
	* Insere um registro na tabela contrato_modificacao_pagamento
	*
	* @author Andre L. Zilz
	* @param stdClass $parametros
	* @return boolean
	*/
	public function inserirModificacaoPagamento($parametros) {

		$retorno = false;

		$sql = "
			INSERT INTO
				contrato_modificacao_pagamento
			(
				cmdfpmdfoid,
				cmdfpforcoid,
				cmdfpnum_parcela,
				cmdfpvencimento_fatura,
				cmdfpcartao,
				cmdfpnome_portador,
				cmdfpcartao_vencimento,
				cmdfpdebito_banoid,
				cmdfpdebito_agencia,
				cmdfpdebito_cc,
				cmdfpvlr_monitoramento_tabela,
				cmdfpvlr_monitoramento_negociado,
				cmdfpcpvoid,
				cmdfpusuoid,
				cmdfpobroid,
				cmdfpvlr_locacao_tabela,
				cmdfpvlr_locacao_negociado,
				cmdfpobroid_taxa,
				cmdfpvlr_taxa_tabela,
				cmdfpvlr_taxa_negociado,
				cmdfpisencao_taxa,
				cmdfpisencao_locacao,
				cmdfppagar_cartao
			)
			VALUES
			(
				".intval($parametros->mdfoid).",
				".$parametros->cmdfpforcoid.",
				".$parametros->cmdfpnum_parcela.",
				".$parametros->cmdfpvencimento_fatura.",
				'".$parametros->cmdfpcartao."',
				'".$parametros->cmdfpnome_portador."',
				'".$parametros->cmdfpcartao_vencimento."',
				".$parametros->cmdfpdebito_banoid.",
				".$parametros->cmdfpdebito_agencia.",
				".$parametros->cmdfpdebito_cc.",
				".$parametros->cmdfpvlr_monitoramento_tabela.",
				".$parametros->cmdfpvlr_monitoramento_negociado.",
				".$parametros->cmdfpcpvoid.",
				".intval($this->usuoid).",
				".$parametros->contrato[0]->eqcobroid.",
				".$parametros->cmdfpvlr_locacao_tabela.",
				".$parametros->cmdfpvlr_locacao_negociado.",
				".$parametros->cmdfpobroid_taxa .",
				".$parametros->cmdfpvlr_taxa_tabela.",
				".$parametros->cmdfpvlr_taxa_negociado.",
				'".$parametros->cmdfpisencao_taxa."',
				'".$parametros->cmdfpisencao_locacao."',
				'".$parametros->cmdfppagar_cartao."'
			)
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		if(pg_affected_rows($rs) > 0) {
			$retorno = true;
		}

		unset($parametros);
		return $retorno;

	}


	/**
	* Insere registros na tabela contrato_modificacao_contato
	*
	* @author Andre L. Zilz
	* @param stdClass $parametros
	* @return boolean
	*/
	public function inserirContatoModificacao($parametros) {

		$sqlPrepare = "
			PREPARE
				insert_contrato_modificacao_contato AS
			INSERT INTO
				contrato_modificacao_contato
			(
				cmctmdfoid,
				cmctnome,
				cmctcpf,
				cmctrg,
				cmctfone_res,
				cmctfone_cel,
				cmctfone_com,
				cmctfone_nextel,
				cmctautorizada,
				cmctinstalacao,
				cmctemergencia,
				cmctobservacao
			)
			VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)";

		//echo "<pre>";var_dump($sqlPrepare);echo "</pre>";exit();

		$rs = $this->executarQuery($sqlPrepare);

		//Executa as insercoes
		foreach ($parametros->contatos as $contato) {

			//Tratamento especifico de dados
			$contato->cmctnome			= ucwords($contato->cmctnome);
			$contato->cmctcpf 			= empty($contato->cmctcpf)  		? 'NULL' : preg_replace('/\D/', '', $contato->cmctcpf);
			$contato->cmctfone_res 		= empty($contato->cmctfone_res)  	? '' : preg_replace('/\D/', '', $contato->cmctfone_res);
			$contato->cmctfone_cel 		= empty($contato->cmctfone_cel)  	? '' : preg_replace('/\D/', '', $contato->cmctfone_cel);
			$contato->cmctfone_com 		= empty($contato->cmctfone_com)  	? '' : preg_replace('/\D/', '', $contato->cmctfone_com);
			$contato->cmctrg 			= empty($contato->cmctrg)  			? 'NULL' : floatval($contato->cmctrg);
			$contato->cmctfone_nextel 	= empty($contato->cmctfone_nextel)  ? 'NULL' : intval($contato->cmctfone_nextel);
			$contato->cmctautorizada 	= ($contato->cmctautorizada == 't') ? 'TRUE' : 'FALSE';
			$contato->cmctinstalacao 	= ($contato->cmctinstalacao == 't') ? 'TRUE' : 'FALSE';
			$contato->cmctemergencia 	= ($contato->cmctemergencia == 't') ? 'TRUE' : 'FALSE';

			$sqlExecute = "
				EXECUTE
					insert_contrato_modificacao_contato
				(
					".intval($parametros->mdfoid).",
					'".$contato->cmctnome."',
					".$contato->cmctcpf.",
					".$contato->cmctrg.",
					'".$contato->cmctfone_res."',
					'".$contato->cmctfone_cel."',
					'".$contato->cmctfone_com."',
					".$contato->cmctfone_nextel.",
					".$contato->cmctautorizada.",
					".$contato->cmctinstalacao.",
					".$contato->cmctemergencia.",
					'".$contato->cmctobservacao."'
				)";

			//echo "<pre>";var_dump($sqlExecute);echo "</pre>";exit();

			$rs = $this->executarQuery($sqlExecute);
		}
		//Fim foreach

		unset($parametros);
		return true;

	}

	/**
	* Insere registros na tabela contrato_modificacao_servico
	*
	* @author Andre L. Zilz
	* @param stdClass $acessorios | dados dos acessorios
	* @param array $cmfoidArray | IDs da contrato_modificacao
	* @return boolean
	*/
	public function inserirAcessorioModificacao($acessorios, $cmfoidArray) {

		$sqlPrepare = "
			PREPARE
				insert_contrato_modificacao_servico AS
			INSERT INTO
				contrato_modificacao_servico
			(
				cmscmfoid,
				cmsusuoid_cadastro,
				cmssituacao,
				cmsobroid,
				cmsqtde,
				cmsvalor_tabela,
				cmsvalor_negociado,
				cmscpvoid
			)
			VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";

		//echo "<pre>";var_dump($sqlPrepare);echo "</pre>";exit();

		$rs = $this->executarQuery($sqlPrepare);

		//Executa as insercoes
		foreach ($acessorios as $acessorio) {

			foreach ($cmfoidArray as $cmfoid) {

				$sqlExecute = "
				EXECUTE
					insert_contrato_modificacao_servico
				(
					".intval($cmfoid).",
					".intval($this->usuoid).",
					'".$acessorio->cmssituacao."',
					".intval($acessorio->cmsobroid).",
					".intval($acessorio->cmsqtde).",
					".floatval($acessorio->cmsvalor_tabela).",
					".floatval($acessorio->cmsvalor_negociado).",
					".intval($acessorio->cmscpvoid)."
				)";

				//echo "<pre>";var_dump($sqlExecute);echo "</pre>";

				$rs = $this->executarQuery($sqlExecute);
			}
		}
		//Fim foreach

		unset($dados);

	}

	/**
	* Recupera os dados de acessorios da modificacao
	* @author   Andre Zilz
	* @param array $cmfoidMarcados | ID dos contrato_modificacao marcados em tela
	* @param int $cmsobroid | ID do acessorio (obrigacao financeira)
	* @return void
	*/
	public function inativarContratoModificacaoAcessorio($acessoriosMarcados) {

		$sqlPrepare = "
			PREPARE
				update_contrato_modificacao_servico AS
			UPDATE
				contrato_modificacao_servico
			SET
				cmsdt_exclusao = $1,
				cmsusuoid_exclusao = $2
			WHERE
				cmsoid = $3
			";

		//echo "<pre>";var_dump($sqlPrepare);echo "</pre>";exit();

		$rs = $this->executarQuery($sqlPrepare);

		foreach ($acessoriosMarcados as $cmsoid) {

			$sqlExecute = "
				EXECUTE
					update_contrato_modificacao_servico
				(
					NOW(),
					".intval($this->usuoid).",
					".intval($cmsoid)."
				)
				";

			//echo "<pre>";var_dump($sqlExecute);echo "</pre>";exit();

			$rs = $this->executarQuery($sqlExecute);
		}
	}

	/**
	* Recupera os dados de acessorios da modificacao
	* @author   Andre Zilz
	* @param stdClass filtros
	* @return array
	*/
	public function pesquisarContratoModificacaoAcessorio($filtros) {

		$retorno = array();

		$sql = "
			SELECT
		        cmsoid,
		        cmscmfoid,
		        cmssituacao,
		        cmsobroid,
		        cmsqtde,
		        cmsvalor_negociado,
		        cmscpvoid,
		        cmfoid,
		        cmfconnumero,
		        veiplaca,
		        veichassi,
		        obrobrigacao
			FROM
		        contrato_modificacao_servico
			INNER JOIN
		        contrato_modificacao ON (cmfoid = cmscmfoid)
			INNER JOIN
		        modificacao ON (mdfoid = cmfmdfoid)
			INNER JOIN
		        veiculo ON (veioid = cmfveioid)
			INNER JOIN
		        obrigacao_financeira ON (obroid = cmsobroid)
			WHERE
		        mdfoid = ".intval($filtros->mdfoid)."
			AND
		        cmsdt_exclusao IS NULL
			";

		//Contrato
	    if (!empty($filtros->connumero)) {
	        $sql .= "
	        		AND cmfconnumero = " . intval($filtros->connumero);
	    }

	    //Placa
	    if (!empty($filtros->veiplaca)) {
	        $sql .= "
	        		AND veiplaca ILIKE '" . $filtros->veiplaca . "%'";
	    }

	    //Chassi
	    if (!empty($filtros->veichassi)) {
	        $sql .= "
	        		AND veichassi ILIKE '" . $filtros->veichassi . "%'";
	    }

	    //Acessorio
	    if (!empty($filtros->obroid)) {
	        $sql .= "
	        		AND cmsobroid = " . intval($filtros->obroid);
	    }

	    $sql .= "
	    	ORDER BY
		        cmfconnumero,
		        veiplaca";

		 //echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Recupera os dados de contatos
	* @author   Andre Zilz
	* @param int $mdfoid | ID da modificacao
	* @return array
	*/
	public function pesquisarContatos($mdfoid) {

		$retorno = array();

		$sql = "
			SELECT
				cmctoid,
				cmctnome,
				cmctcpf,
				cmctrg,
				cmctfone_res,
				cmctfone_cel,
				cmctfone_com,
				cmctfone_nextel,
				cmctautorizada,
				cmctinstalacao,
				cmctemergencia,
				cmctobservacao,
				STRING_AGG(cmctoid::VARCHAR,',') OVER() AS lista_cmctoid
			FROM
				contrato_modificacao_contato
			WHERE
				cmctmdfoid = ".intval($mdfoid)."
			AND
				cmctdt_exclusao IS NULL
			ORDER BY
				cmctnome
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Altera o status de uma modificacao
	*
	* @author Andre L. Zilz
	* @param string $mdfstatus | status
	* @param int $mdfoid | ID da modificacao
	*/
	public function atualizarStatusModificacao($mdfstatus, $mdfoid) {


		$sql = "
			UPDATE
				modificacao
			SET
				mdfstatus = '". $mdfstatus. "'
			WHERE
				mdfoid = " . intval( $mdfoid);

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

      	$rs = $this->executarQuery($sql);

		if(pg_affected_rows($rs) == 0){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
	}

	/**
	* Altera o status financeiro de uma modificacao
	*
	* @author Andre L. Zilz
	* @param string $mdfstatusFinanceiro | status
	* @param int $mdfoid | ID da modificacao
	* @param  int $cmacoid | ID da analise de credito
	*/
	public function atualizarStatusFinanceiroPorModificacao($mdfstatusFinanceiro, $mdfoid, $cmacoid) {


		$sql = "
			UPDATE
				modificacao
			SET
				mdfstatus_financeiro = '". $mdfstatusFinanceiro. "'
				,mdfcmacoid = ".intval($cmacoid)."
			WHERE
				mdfoid = " . intval( $mdfoid);

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

      	$rs = $this->executarQuery($sql);

		if(pg_affected_rows($rs) == 0){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
	}

	/**
	* Altera o status financeiro de modificacoes
	*  com stauts financeiro pendente
	*
	* @author Andre L. Zilz
	* @param string $mdfstatusFinanceiro | status
	* @param int $cmacoid | ID da analise de credito
	*/
	public function atualizarStatusFinanceiroPorID($mdfstatusFinanceiro, $cmacoid) {

		$retorno = array();

		if($mdfstatusFinanceiro == 'N'){
			$mudarStatusModificacao = ",mdfstatus = 'X'";
		} else {
			$mudarStatusModificacao = '';
		}


		$sql = "
			UPDATE
				modificacao
			SET
				mdfstatus_financeiro = '". $mdfstatusFinanceiro. "'
				".$mudarStatusModificacao."
			WHERE
				mdfcmacoid = ".intval($cmacoid)."
			RETURNING
				mdfoid
			";
		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

      	$rs = $this->executarQuery($sql);

      	while ($row = pg_fetch_object($rs)) {
      		$retorno[] = $row;
      	}

      	return $retorno;

	}

	/**
	* Insere um historico no contrato
	*
	* @author Andre L. Zilz
	* @param int $connumero
	* @param string $observacao
	*/
	public function inserirHistoricoContrato($connumero, $observacao) {

		$sql = "SELECT
					historico_termo_i
					(
						".intval($connumero).",
						".intval($this->usuoid).",
						'".$observacao."'
					)";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);
	}

	/**
	* Recupera dados de contrato pelo codigo do Chassi
	*
	* @author Andre L. Zilz
	* @param array $chassi
	* @param int $cmtoid | ID tipo modificacao
	*/
	public function recuperarContratoPorChassi($chassi, $cmtoid) {

		$retorno = array();

		$sql = "
			SELECT
		    	connumero,
		    	concsioid,
		    	TO_CHAR (condt_quarentena_seg, 'dd/mm/yyyy') AS quarentena,
		    	veichassi,
		    	veiplaca,
		    	tpc_out.tpcoid,
		    	tpc_out.tpcdescricao,
		    	(
	    			SELECT
	    				tpc_in.tpcdescricao
    				FROM
    					tipo_contrato tpc_in
					WHERE
						tpc_in.tpcoid = tpc_out.tpcoidcorrespondente_ex
				) AS tpcdescricao_correspondente,
    			(
    				SELECT
    					ordoid::VARCHAR || ' / ' || ostdescricao
					FROM
						ordem_servico
                    INNER JOIN
                    	ordem_servico_item ON (ositordoid = ordoid)
                    INNER JOIN
                    	os_tipo_item ON (otioid = ositotioid)
                    INNER JOIN
                    	os_tipo ON (ostoid = otiostoid)
                    WHERE
                    	ordconnumero = connumero
                    AND
                    	ordstatus NOT IN (6,9,18)
                    ORDER BY
                        orddt_ordem ASC, ositdt_cadastro DESC
                    LIMIT 1
                ) AS os_tipo,
				(
					SELECT EXISTS
					(
						SELECT
							1
						FROM
							contrato_modificacao_tipo_restricao_status
						WHERE
							cmtrsdt_exclusao IS NULL
						AND
							cmtrscmtoid = ". intval($cmtoid)."
						AND
							cmtrscsioid = concsioid
					)
				) AS bloqueia_migracao
    		FROM
    			contrato
    		INNER JOIN
    			veiculo ON (veioid = conveioid)
    		INNER JOIN
    			tipo_contrato tpc_out ON (tpcoid = conno_tipo)
    		WHERE
    			veichassi IN (".implode(',', $chassi).")
    		AND
    			conveioid IS NOT NULL
	    	ORDER BY
	    		connumero
    	";


    	//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	* Troca o contrato original da nota fiscal pleo novo contrato
	*
	* @author Andre L. Zilz
	* @param int $setConnumero | ID contrato que sera o novo valor
	* @param int $getConnumero | ID contrato a ser pesquisado
	*
	*/
	public function trocarContratoNotaFiscalItem($getConnumero, $setConnumero) {

		$sql = "
			UPDATE
				nota_fiscal_item
			SET
				nficonoid = ".intval($setConnumero)."
			FROM
				nota_fiscal
			INNER JOIN
				titulo ON (titnfloid = nfloid)
			WHERE
				nfino_numero = nflno_numero
			AND
				nfiserie = nflserie
			AND
				nflnatureza = 'LOCAÇÃO DE EQUIPAMENTOS'
			AND
				nflserie = 'SL'
			AND
				nficonoid = ".intval($getConnumero)."
			AND
				nfldt_vencimento::DATE > NOW()::DATE
			AND
				titdt_vencimento > NOW()::DATE
			AND
				titdt_pagamento IS NULL
			AND
				titdt_cancelamento IS NULL";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);
	}


	/**
	 * Busca informacoes da modificacao
	 *
	 * @author Vinicius Senna
	 * @param int $cmfoid id tabela contrato_modificacao
	 *
	 * @return array
	 */
	public function recuperaInformacoesModificacao($cmfoid) {

		$sql = "SELECT
					modificacao.*,
					contrato_modificacao.*,
					cmdfpcpvoid,
					cmtdescricao
				FROM
					modificacao
				INNER JOIN
					contrato_modificacao ON (cmfmdfoid = mdfoid)
				INNER JOIN
					contrato_modificacao_tipo ON (cmtoid = mdfcmtoid)
				LEFT JOIN
					contrato_modificacao_pagamento ON mdfoid = cmdfpmdfoid
				WHERE
					cmfoid = " . intval($cmfoid);
				
	 	$rs = $this->executarQuery($sql);

        if($rs && pg_num_rows($rs) > 0) {

        	return pg_fetch_object($rs);

        } else {

        	throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);

        }
	}


	/**
	 * Atualiza o parcelamento dos serviços do contrato
	 *
	 * @author Ricardo Marangoni da Mota
	 * @param int $connumero numero do contrato
	 * @param int $cpvoid ID da tabela cond_pgto_venda
	 *
	 * @return null
	 */
	public function atualizarDadosParcelamento($connumero, $cpvoid) {

		$sql = "
				UPDATE
					contrato_servico
				SET
					conscpvoid = " . intval($cpvoid) . "					
				WHERE
					conssituacao  = 'L'
				AND
					constadoid  IS NULL
				AND
					consconoid = " . intval($connumero) . ";

				UPDATE contrato SET condt_inicio_parcela = NOW() WHERE connumero = " . intval($connumero);

	 	$rs = $this->executarQuery($sql);

        if(!$rs) {

        	throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);

        }

	}


	public function getTitulosByContrato($contrato) {

        $sql = "
        	SELECT 
	            SUM(nfivl_item) / cpvparcela as valorTotal, 
	            nfloid, 
	            titoid as codigo,
	            COALESCE(to_char(titdt_pagamento,'dd/mm/yyyy'), '') as titdt_pagamento, 
	            to_char(titdt_vencimento,'dd/mm/yyyy') as titdt_vencimento,
	            ('now'::date-titdt_vencimento) as atraso,
	            connumero 
	        FROM 
	            nota_fiscal 
	        INNER JOIN 
	            clientes ON clioid = nflclioid 
	        INNER JOIN
	            contrato ON conclioid = clioid 
            INNER JOIN 
            	contrato_pagamento ON cpagconoid = connumero
        	INNER JOIN
            	cond_pgto_venda ON cpvoid = cpagcpvoid
	        INNER JOIN 
	            nota_fiscal_item ON nfino_numero = nflno_numero AND nfiserie = nflserie AND nficonoid = connumero 			
	        LEFT JOIN 
	            titulo ON titnfloid = nfloid 	        
	        WHERE  
	            connumero = $contrato 
	        AND 
	            titformacobranca <> 62 
	        AND 
	            (nfiserie = 'F' OR nfiserie = 'SL') 
	        AND 
            	(
            		Extract('Month' From current_date) <> Extract('Month' From titdt_vencimento) 
            		AND
            		Extract('Year' From titdt_vencimento) >= Extract('Year' From current_date) 
        		)
    		AND 
    			(current_date - titdt_vencimento) <= 0			
			AND 
				nfldt_cancelamento is null 	
			AND 
            	titdt_pagamento IS NULL
	        GROUP BY 
	            nfloid,
	            titdt_pagamento,
	            titdt_vencimento,
	            titoid,
	            connumero,
	            cpvparcela
            ";
                    
        $result = pg_fetch_all($this->executarQuery($sql));

        return $result; 
    }

    /**
     * STI 84189
     * Retorna um titulo através de seu ID
     * @param $titoid
     * @return  int
     */
    public function getTituloById($titoid) {

        $sql = "
        SELECT
            titoid, 
            titdt_vencimento,
            nfloid,
            titvl_titulo, 
            titnfloid,
            titdt_referencia,
            titno_parcela,
            titclioid,
            titdt_pagamento
        FROM
            titulo
        JOIN
            nota_fiscal
        ON
            titnfloid = nfloid
        WHERE
            titoid = $titoid
			AND nfldt_cancelamento is null ";

        return pg_fetch_assoc($this->executarQuery($sql));
    }


    /**     
     * Faz a baixa dos títulos 
     * @param array $titulosBaixa
     * @param string $notaFiscalSerie
     * @param boolean $isWebService
     * @return array
     */
    public function efetuarBaixa($titulosBaixa) {

        $arrRescisaoBaixa           = array();
        $arrRescisoesBaixa          = array();
        $tituloFormaCobranca        = 37;
        $tituloValorPagamento       = 0;
        $tituloValorDesconto        = 0;
        $tituloValorAcrescimo       = 0;
        $tituloValorJuros           = 0;
        $tituloValorMulta           = 0;
        $tituloValorTarifaBanco     = 0;
        $tituloTaxaAdministrativa   = 0;
        $tituloValorIR              = 0;
        $tituloValorPisCofins       = 0;
        $tituloValorISS             = 0;
        $tituloUsuarioAlteracao     = $_SESSION['usuario']['oid'];
        $tituloImpresso             = "'f'";
        $tituloNaoCobravel          = "'f'";
        $tituloFaturamentoVariavel  = "'f'";
        $tituloBaixaAutomaticaBanco = "'f'";
        $tituloTransacaoCartao      = "'f'";
        $tituloFaturaUnica          = "'f'";
        $tituloDataAlteracao        = date("'Y-m-d'");
        $tituloDataPagamento        = date("'Y-m-d'");
        $tituloDataCredito          = date("'Y-m-d'");
        $tituloDataInclusao         = date("'Y-m-d'");

        //var_dump($titulosBaixa);

        if(empty($titulosBaixa)) {
        	return $arrRescisoesBaixa;
        }

        foreach($titulosBaixa as $tituloBaixa) {

            // echo "\n<pre>notaFiscalSerie:: "; print_r($notaFiscalSerie); "</pre>\n\n";
            // echo "\n<pre>isWebService:: "; print_r($isWebService); "</pre>\n\n";
            
            $tituloBD = $this->getTituloById($tituloBaixa['codigo']);

            //var_dump($tituloBD);

            // echo "\n<pre>tituloBaixa:: "; print_r($tituloBaixa); "</pre>\n\n";

            // echo "\n<pre>tituloBD:: "; print_r($tituloBD); "</pre>\n\n";
           
            if(!empty($tituloBD)) {

                if(empty($tituloBD['titdt_pagamento'])) {

                	//echo 'entrou no titdt_pagamento';

                    $tituloBaixa['valorTotal'] = $tituloBaixa['valorTotal'] ? $tituloBaixa['valorTotal'] : $tituloBaixa['valortotal'];

                    /*if($notaFiscalSerie == 'A') {

                        $mesAnoVencimento = date('Y-m', strtotime(implode("-",array_reverse(explode("/", $tituloBD['titdt_vencimento'])))));
                        $timeMesAnoVencimento = strtotime($mesAnoVencimento);
                        
                        $mesAnoRescisao = date('Y-m', strtotime(implode("-",array_reverse(explode("/", $tituloBaixa['dataRescisao'])))));
                        $timeMesAnoRescisao = strtotime($mesAnoRescisao);
                        
                        if($timeMesAnoVencimento == $timeMesAnoRescisao) {
                            
                            $ultimoDiaMesRescisao = date('t', $timeMesAnoRescisao);
                            $diaRescisao = date('d', strtotime(implode("-",array_reverse(explode("/", $tituloBaixa['dataRescisao'])))));
                            // echo "\nvalorTotal = " . $tituloBaixa['valorTotal'] . " - (" . $tituloBaixa['valorTotal'] . " - ((" . $tituloBaixa['valorTotal'] . "/" . $ultimoDiaMesRescisao . ") * (" . $ultimoDiaMesRescisao . " - " . $diaRescisao . ")))\n";
                            $tituloBaixa['valorTotal'] = round( $tituloBaixa['valorTotal'] - ($tituloBaixa['valorTotal'] - (($tituloBaixa['valorTotal'] / $ultimoDiaMesRescisao) * ($ultimoDiaMesRescisao - $diaRescisao))), 2);                      
                        }

                        $arrRescisaoBaixa['connumero'] = $tituloBaixa['connumero'];
                    }*/

                    if($tituloBD['titvl_titulo'] == $tituloBaixa['valorTotal']) {

                    	//echo 'entrou na baixa integral';
                        
                        // Baixa integral
                        $sqlBaixaIntegral = "
	                        UPDATE 
	                            titulo 
	                        SET
	                            titdt_credito       = $tituloDataCredito,
	                            titdt_pagamento     = $tituloDataPagamento,
	                            titdt_alteracao     = $tituloDataAlteracao, 
	                            titformacobranca    = $tituloFormaCobranca,
	                            titusuoid_alteracao = $tituloUsuarioAlteracao 
	                        WHERE
	                            titoid = {$tituloBaixa['codigo']}";

                        // echo "\n<pre>sqlBaixaIntegral:: "; print_r($sqlBaixaIntegral); "</pre>\n\n";
                        $this->executarQuery($sqlBaixaIntegral);
                                                
                        $arrRescisaoBaixa['resbaititoid'] = $tituloBaixa['codigo'];
                        $arrRescisaoBaixa['resbaitipo'] = 'I';
                        $arrRescisaoBaixa['resbaivl_titulo'] = $tituloBaixa['valorTotal'];
                    }
                    else {

                    	//echo 'entrou na baixa parcial';

                        // Baixa parcial
                        $tituloDataVencimento = $notaFiscalSerie == 'A' ? implode("-",array_reverse(explode("/", $_POST['vencimento']))) : $tituloBD['titdt_vencimento'];
                        $tituloDataVencimento = $tituloDataVencimento ? "'" . $tituloDataVencimento . "'" : 'NULL';
                        
                        $sqlBaixaParcial = "
	                        UPDATE 
	                            titulo 
	                        SET
	                            titvl_titulo        =  titvl_titulo - {$tituloBaixa['valorTotal']},
	                            titdt_vencimento    =  $tituloDataVencimento, 
	                            titdt_alteracao     =  $tituloDataAlteracao,
	                            titusuoid_alteracao =  $tituloUsuarioAlteracao 
	                        WHERE 
	                            titoid = {$tituloBaixa['codigo']}";

                        // echo "\n<pre>sqlBaixaParcial:: "; print_r($sqlBaixaParcial); "</pre>\n\n";                        
                        $this->executarQuery($sqlBaixaParcial);

                        $arrRescisaoBaixa['resbaititoid'] = $tituloBaixa['codigo'];
                        $arrRescisaoBaixa['resbaitipo'] = 'P';
                        $arrRescisaoBaixa['resbaivl_titulo'] = $tituloBD['titvl_titulo'];

                        $tituloDataReferencia = $tituloBD['titdt_referencia'] ? "'" . $tituloBD['titdt_referencia'] . "'": 'NULL';

                        // Inserir um novo título
                        $sqlInsertTitulo = "
	                        INSERT INTO titulo (
	                            titnfloid,
	                            titclioid,
	                            titno_parcela,
	                            titvl_titulo,
	                            titvl_pagamento,
	                            titvl_desconto,
	                            titvl_acrescimo,   
	                            titvl_juros,
	                            titvl_multa,
	                            titvl_tarifa_banco,
	                            titvl_ir,
	                            titvl_piscofins,
	                            titvl_iss,
	                            tittaxa_administrativa,
	                            titformacobranca,
	                            titusuoid_alteracao,
	                            titimpresso,
	                            titnao_cobravel,
	                            titfaturamento_variavel,
	                            titbaixa_automatica_banco,
	                            tittransacao_cartao,
	                            titfatura_unica,
	                            titdt_referencia,
	                            titdt_vencimento,
	                            titdt_credito,
	                            titdt_pagamento,
	                            titdt_inclusao,
	                            titdt_alteracao
	                        ) 
	                        VALUES (
	                            {$tituloBD['titnfloid']},
	                            {$tituloBD['titclioid']},
	                            {$tituloBD['titno_parcela']},
	                            {$tituloBaixa['valorTotal']},
	                             $tituloValorPagamento,
	                             $tituloValorDesconto,
	                             $tituloValorAcrescimo,
	                             $tituloValorJuros,
	                             $tituloValorMulta,
	                             $tituloValorTarifaBanco,
	                             $tituloValorIR,
	                             $tituloValorPisCofins,
	                             $tituloValorISS,
	                             $tituloTaxaAdministrativa,
	                             $tituloFormaCobranca,
	                             $tituloUsuarioAlteracao,
	                             $tituloImpresso,
	                             $tituloNaoCobravel,
	                             $tituloFaturamentoVariavel,
	                             $tituloBaixaAutomaticaBanco,
	                             $tituloTransacaoCartao,
	                             $tituloFaturaUnica,
	                             $tituloDataReferencia,
	                             $tituloDataVencimento,
	                             $tituloDataCredito,
	                             $tituloDataPagamento,
	                             $tituloDataInclusao,
	                             $tituloDataAlteracao          
	                        )";
                        
                        // echo "\n<pre>sqlInsertTitulo:: "; print_r($sqlInsertTitulo); "</pre>\n\n";
                        
                        $this->executarQuery($sqlInsertTitulo);
                    }
                }
            }

            $arrRescisoesBaixa[] = $arrRescisaoBaixa;
            
        }

        //var_dump($arrRescisoesBaixa);

        return $arrRescisoesBaixa;
    }

    public function verificarParalizacaoFaturamento($contrato) {
    	$sql = "
	        SELECT 
	        	1
			FROM 
				parametros_faturamento
			WHERE 
				parfconoid = $contrato
			AND 
				parfativo is true
			AND 
				NOW() BETWEEN parfdt_ini_cobranca AND parfdt_fin_cobranca";

		$rs = $this->executarQuery($sql);

		/*
		 *	Não permitir realizar upgrade/downgrade no momento que informa-se o contrato, 
		 *	se houver uma paralisação de faturamento
		*/
		if(pg_num_rows($rs) > 0) {
			throw new Exception("Não é possível realizar upgrade/downgrade, o veículo encontra-se em paralisação do faturamento.");
			
		}

        
    }	/**
	 * Realiza insercao da alteracao na tabela historico_termo
	 *
	 * @author Vinicius Senna
	 * @param int $connumero | Numero do contrato
	 * @param int $cd_usuario | id do usuario
	 * @param string $hitobs | Observacoes acrescentadas a esse registro
	 * @param string hitprotprotocolo | Numero do protocolo URA (protprotocolo) associado ao historico - tabela protocolo_ura
	 *
	 * @return boolean (true=OK /false = falha)
	 */
	public function insereHistoricoTermoMigracao($connumero, $cd_usuario, $hitobs, $hitprotprotocolo = null) {

		$sql = "
				SELECT
					historico_termo_i
					(
						" . intval($connumero) . ",
						" . intval($cd_usuario) . ",
						'" . $hitobs . "'";

		if(!is_null($hitprotprotocolo)) {
			$sql .= ",'" . $hitprotprotocolo . "'";
		}

		$sql .= ")";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit;
		$rs = $this->executarQuery($sql);

		return true;
	}

	/**
	 * Altera contrato original no processo de migracao
	 *
	 * @author Vinicius Senna
	 * @param int $connumero | Numero do contrato
	 *
	 * @return boolean (true=OK /false = falha)
	 */
	public function alteraContratoOriginalMigracao($connumero, $conveioid, $conequoid) {

		$sql = "UPDATE
					contrato
				SET
					conveioid_antigo=conveioid,
					conequoid_antigo=conequoid,
					conequoid = null,
					conveioid = null
				WHERE connumero = ". intval($connumero);

		$rs = $this->executarQuery($sql);

		return true;
	}

	/**
	 * Cancela contrato original no processo de migracao
	 *
	 * @author Vinicius Senna
	 * @param int $connumero | Numero do contrato
	 *
	 * @return boolean (true=OK /false = falha)
	 */
	public function cancelaContratoOriginalMigracao($connumero,$cd_usuario) {

		$sql = "UPDATE
					contrato
					SET
					condt_exclusao = now(),
					conusuoid_exclusao = " . intval($cd_usuario) . ",
	    			condt_alteracao = now() ,
	              	concmcoid = 39
				WHERE connumero = " . intval($connumero);

		$rs = $this->executarQuery($sql);

		return true;
	}

	/**
	 * Gera contrato novo no processo de migracao
	 *
	 * @author Vinicius Senna
	 * @param $cd_usuario int chave primaria tabela usuarios
	 * @param $cmfoid int chave primaria da tabela contrato_modificacao
	 *
	 * @return integer
	 */
	public function geraContratoNovoMigracao($cd_usuario,$cmfoid) {

		$retorno = 0;

		$sql = "INSERT INTO
					contrato
					(
		              	conusuoid,
						condt_cadastro,
						condt_ini_vigencia,
						condt_instalacao,
		  				condt_primeira_instalacao,
						conveioid,
						conclioid,
						coneqcoid,
						conno_tipo,
						conequoid,
						conrczoid,
						conregcoid,
						concsioid,
						connumero_migrado,
						conmodalidade
					)
					(
						SELECT
			               	" . intval($cd_usuario) . ",
							now(),
							now(),
							now(),
							cmfdt_primerira_instalacao,
							cmfveioid,
							cmfclioid_destino,
							cmfeqcoid_origem,
							cmftpcoid_destino,
							cmfequoid,
							cmfrczoid,
							(SELECT rczregcoid FROM regiao_comercial_zona WHERE rczoid = cmfrczoid),
							1,
							cmfconnumero,
							cmfmodalidade
				  		FROM
							contrato_modificacao
						WHERE
			               cmfoid = " . intval($cmfoid) . "
					) RETURNING connumero;";

		if($rs = $this->executarQuery($sql)) {

            if(pg_affected_rows($rs) > 0) {
                $id = pg_fetch_object($rs);
                $retorno = $id->connumero;
            } else {
        		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        	}

        } else {
        	throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $retorno;
	}

	/**
	 * Atualiza novo numero do contrato no processo de migracao
	 *
	 * @author Vinicius Senna
	 * @param int $connumero_novo | id do contrato novo
	 * @param int $cmfoid | id da modificacao do contrato
	 * @param int $ordoid | id da ordem de servico
	 *
	 * @return boolean (true=OK /false = falha)
	 */
	public function atualizaContratoModificacao($connumero_novo, $cmfoid, $ordoid = null) {

		// FAZ UPDATE na contrato_modificacao gravando $connumero_novo em cmfconnumero_novo;
		$sql = "UPDATE
					contrato_modificacao
				SET
					cmfconnumero_novo = " . intval($connumero_novo);


		if(is_null($ordoid) == FALSE) {
			$sql .= ", cmfordoid = " . $ordoid;
		}

		$sql .=	"WHERE
					cmfoid = " . intval($cmfoid);

		$rs = $this->executarQuery($sql);

		return true;
	}

	/**
	 * Atualiza a contrato modificacao com o ID da proposta
	 *
	 * @author Andre L. Zilz
	 * @param int $prpoid | id da proposta
	 */
	public function atualizaContratoModificacaoProposta($prpoid, $cmfoid) {

		$sql = "
			UPDATE
				contrato_modificacao
			SET
				cmfprpoid = " . intval($prpoid) ."
			WHERE
				cmfoid = " . intval($cmfoid);

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();
		$rs = $this->executarQuery($sql);
	}

	/**
	 * Grava numero de contrato novo na tabela termo_numero
	 *
	 * @author Vinicius Senna
	 * @param $connumero_novo
	 * @param $terntnsoid
	 * @param $ternrelroid
	 * @param $connumero_novo
	 *
	 * @return boolean (true=OK /false = falha)
	 */
	public function geraTermoNumeroMigracao($connumero_novo,$terntnsoid, $ternrelroid,$connumero_novo) {

		$sql = "INSERT INTO
					termo_numero
        			(
	        			ternnumero,
	        			terntnsoid,
	        			ternrelroid,
	        			ternbloco
    				)
 				VALUES
        			(
    					" . intval($connumero_novo) . ",
    					" . intval($terntnsoid) . ",
    					" . intval($ternrelroid) . ",
    					" . intval($connumero_novo) . "
					);
				";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

	}

	/**
	 * Insere historico na tabela termo_numero_historico
	 *
	 * @author Vinicius Senna
	 * @param $connumero_novo novo numero do contrato
	 *
	 * @return boolean (true=OK /false = falha)
	 */
	public function insereHistoricoTermoNumeroMigracao($connumero_novo,$cd_usuario) {

		$sql = "INSERT INTO
					termo_numero_historico
					(
						tnhternoid,
						thntnsoid,
						thnusuoid,
						thnrelroid,
						thnobservacao
					)
  					(
  						SELECT
  							ternoid,
  							2,
  							" . intval($cd_usuario) . ",
  							1,
  							'Termo cadastrado pela tela de modificação de contrato.'
						FROM
							termo_numero
						WHERE
							ternnumero = " . intval($connumero_novo) . "
					)";


		$rs = $this->executarQuery($sql);
	}

	/**
	 * Insere historico na tabela historico_equipamento
	 *
	 * @author Vinicius Senna
	 * @param int $connumero
	 * @param int $connumeroNovo
	 * @param int
	 *
	 * @return boolean (true=OK /false = falha)
	 */
	public function insereHistoricoEquipamento($connumeroNovo, $cd_usuario,$observacao) {

		$connumeroNovo = intval($connumeroNovo);
		$usuario = intval($cd_usuario);

		$sql = "INSERT INTO
					historico_equipamento
						(
							hieequoid,
							hieconnumero,
							hieeqsoid,
							hiemotivo,
                        	hieobservacao,
                        	hieusuoid,
                        	hieeveoid,
                        	hieplacaoid,
                        	hieesn,
                        	hientc,
                        	hiearaoid,
                        	hierelroid
                    	)
                        SELECT
                        	conequoid,
                        	" . $connumeroNovo . ",
                        	equeqsoid,
                        	'MIGRAÇÃO',
                        	'" . $observacao . "',
                        	" . $usuario . ",
                        	equeveoid,
                        	equno_placa,
                        	equesn,
                        	equno_fone,
                        	equaraoid,
                        	equrelroid
                        FROM
                        	contrato
                    	INNER JOIN
                    		equipamento ON
                    		conequoid_antigo = equoid
                        WHERE
                        	connumero = " . $connumeroNovo . ";";

    	$rs = $this->executarQuery($sql);

		return true;
	}

	/**
	 * Migra contatos do contrato
	 *
	 * @author Vinicius Senna
	 * @param int $getConnumero | Numero do contrato condicional
	 * @param int $setConnumero |  Numero do contrato para update
	 *
	 */
	public function migraContatos($getConnumero, $setConnumero) {

		$sql = "
			UPDATE
				telefone_contato
            SET
            	tctconnumero = " . intval($setConnumero) . "
            WHERE
            	tctconnumero = " . intval($getConnumero) ;


    	//echo "<pre>";var_dump($sql);echo "</pre>";exit();

      	$rs = $this->executarQuery($sql);
	}

	/**
	 * Atualiza dados tabela cliente_perfil
	 *
	 * @author Vinicius Senna
	 * @param int $novoTermo | numero do novo termo
	 * @param int $cd_usuario | id do usuario
	 * @param int $connumero | numero do contrato
	 *
	 * @return boolean  (true=OK /false = falha)
	 */
	public function atualizaClientePerfil($novoTermo, $cd_usuario, $connumero) {

		$novoTermo = intval($novoTermo);
		$usuario = intval($cd_usuario);
		$connumero = intval($connumero);

		$sql  = "UPDATE
					cliente_perfil
                SET
                    clipfconnumero = " . $novoTermo. ",
                    clipfusuoid = " . $usuario . "
                WHERE
                	clipfconnumero = " . $connumero . "
                AND
                	clipfexclusao IS NULL;";

    	$rs = $this->executarQuery($sql);

		return true;
	}

	/**
	* Persiste dados de pagamento para o contrato
	* @author Erika Parolim
	* @param int $cmfoid | ID do contrato modificacao
	*/
	public function ContratoPagamento($cmfoid) {
		/*Pega o numero do contrato a atualizar */
		$sql = "
			SELECT
                (CASE WHEN cmfconnumero_novo > 0 THEN
             		cmfconnumero_novo
     			ELSE
     				cmfconnumero
     			END) AS contrato_novo,
				cmfconnumero AS contrato
			 FROM
		         contrato_modificacao
			 WHERE
			 	cmfoid = ". intval($cmfoid) . "
		 	";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0) {

			/*Identifica contrato e contrato novo*/
			$contrato   	= pg_fetch_result ($rs, 0, 'contrato');
			$contrato_novo 	= pg_fetch_result ($rs, 0, 'contrato_novo');

			$sql = "
					SELECT EXISTS
						(SELECT
			                 cpagoid
					     FROM
					         contrato_pagamento
						 WHERE
						 	cpagconoid = ". intval($contrato_novo). "
					 	) AS existe
						 ";

			//echo "<pre>";var_dump($sql);echo "</pre>";exit();

			$rs = $this->executarQuery($sql);

			$existeRegistro = pg_fetch_object($rs);

			if($existeRegistro->existe == 't') {

				// Indica que o registro já existe, então atualiza

				$sql = "
						SELECT EXISTS
							(SELECT
				               	1
						     FROM
					         	contrato_modificacao
					         INNER JOIN
					         	contrato_modificacao_pagamento ON (cmdfpmdfoid = cmfmdfoid)
					         WHERE
					         	cmfoid =" . intval($cmfoid) ."
				         	) AS existe";

				//echo "<pre>";var_dump($sql);echo "</pre>";exit();

				$rs = $this->executarQuery($sql);

				$existeRegistro = pg_fetch_object($rs);

				if($existeRegistro->existe == 't') {

					//Se retornou valor é porque os dados da contrato_modificacao_pagamento estão preenchidos

						$sql = "
							UPDATE
								contrato_pagamento
							SET
								cpagsituacao        = 'L',
								cpagnum_parcela     = cmdfpnum_parcela,
								cpagcartao          = cmdfpcartao,
								cpagcartao_validade = cmdfpcartao_vencimento,
								cpagdebito_agencia  = cmdfpdebito_agencia,
								cpagdebito_cc       = cmdfpdebito_cc,
								cpagmonitoramento   = cmdfpvlr_monitoramento_negociado,
								cpagcpvoid          = cmdfpcpvoid,
								cpagbancodigo       = cmdfpdebito_banoid,
								cpagobroid_servico  = eqcobroid,
								cpagvl_servico      = cmdfpvlr_locacao_negociado,
								cpagusuoid          = " . intval($this->usuoid) . "
							FROM
								 contrato_modificacao
							INNER JOIN
								 contrato_modificacao_pagamento ON (cmdfpmdfoid = cmfmdfoid)
							INNER JOIN
								 equipamento_classe ON (cmfeqcoid_destino = eqcoid)
							WHERE
								cpagconoid = cmfconnumero
							 AND
								cmfoid =" . intval($cmfoid);

						//echo "<pre>";var_dump($sql);echo "</pre>";exit();

						$rs = $this->executarQuery($sql);

				} else {
                  /*Neste caso, deve verificar se o contrato original e novo não são o mesmo,
				   se não for, atualiza com os dados do contrato original, se houver*/

				   if($contrato != $contrato_novo) {

					   $sql = "
					   		SELECT
							    cpagsituacao,
								cpagnum_parcela,
								cpagcartao,
								cpagcartao_validade,
								cpagdebito_agencia,
								cpagdebito_cc,
								cpagmonitoramento,
								cpagcpvoid,
								cpagbancodigo,
								cpagobroid_servico,
								cpagvl_servico
							FROM
								 contrato_pagamento
							WHERE
								cpagconoid =" . intval($contrato);

						//cho "<pre>";var_dump($sql);echo "</pre>";
						//exit();

						$rs = $this->executarQuery($sql);

						if(pg_num_rows($rs) > 0) {

							/*Pega os dados a atualizar*/
							$cpagsituacao  	 	= pg_fetch_result ($rs, 0, 'cpagsituacao');
							$cpagnum_parcela 	= pg_fetch_result ($rs, 0, 'cpagnum_parcela');
							$cpagcartao 		= pg_fetch_result ($rs, 0, 'cpagcartao');
							$cpagcartao_validade = pg_fetch_result ($rs, 0, 'cpagcartao_validade');
							$cpagdebito_agencia = pg_fetch_result ($rs, 0, 'cpagdebito_agencia');
							$cpagdebito_cc 		= pg_fetch_result ($rs, 0, 'cpagdebito_cc');
							$cpagmonitoramento 	= pg_fetch_result ($rs, 0, 'cpagmonitoramento');
							$cpagcpvoid 		= pg_fetch_result ($rs, 0, 'cpagcpvoid');
							$cpagbancodigo 		= pg_fetch_result ($rs, 0, 'cpagbancodigo');
							$cpagobroid_servico = pg_fetch_result ($rs, 0, 'cpagobroid_servico');
							$cpagvl_servico 	= pg_fetch_result ($rs, 0, 'cpagvl_servico');

							$sql = "
								UPDATE
									contrato_pagamento
								SET
									cpagsituacao 		= '" . $cpagsituacao . "',
									cpagnum_parcela 	= " . $cpagnum_parcela . ",
									cpagcartao 			=" . $cpagcartao . ",
									cpagcartao_validade = '" . $cpagcartao_validade . "',
									cpagdebito_agencia 	= " . $cpagdebito_agencia . ",
									cpagdebito_cc 		= " . $cpagdebito_cc . ",
									cpagmonitoramento 	= " . $cpagmonitoramento . ",
									cpagcpvoid 			= " . $cpagcpvoid . ",
									cpagbancodigo 		= " . $cpagbancodigo . ",
									cpagobroid_servico 	= " . $cpagobroid_servico . ",
									cpagvl_servico 		= " . $cpagvl_servico . ",
									cpagusuoid 			= " . $this->usuoid . "
								 WHERE
									 cpagconoid 		= " . intval($contrato_novo);

								//echo "<pre>";var_dump($sql);echo "</pre>";exit();

								$rs = $this->executarQuery($sql);
						}
				   }

				}

			} else {

				// INSERT PORQUE O REGISTRO AINDA NÃO EXISTE

				$sql = "
					SELECT EXISTS
					(SELECT
		               1
				     FROM
			         	contrato_modificacao
			         INNER JOIN
			         	contrato_modificacao_pagamento ON (cmdfpmdfoid = cmfmdfoid)
			         WHERE
			         	cmfoid=" . intval($cmfoid) ."
		         	) AS existe";

			    //echo "<pre>";var_dump($sql);echo "</pre>";
			    //exit();

				$rs = $this->executarQuery($sql);

				$existeRegistro = pg_fetch_object($rs);

				if($existeRegistro->existe == 't') {
					// Grava na contrato_pagamento com os dados da contrato_modificacao_pagamento

						$sql = "
							INSERT INTO
									contrato_pagamento
								(
						         	cpagsituacao,
									cpagnum_parcela,
									cpagcartao,
									cpagcartao_validade,
									cpagdebito_agencia,
									cpagdebito_cc,
									cpagmonitoramento,
									cpagcpvoid,
									cpagbancodigo,
									cpagobroid_servico,
									cpagvl_servico,
									cpagusuoid,
									cpagconoid
								)
								(
									SELECT
										'L',
										cmdfpnum_parcela,
										cmdfpcartao,
										cmdfpcartao_vencimento,
										cmdfpdebito_agencia,
										cmdfpdebito_cc,
										cmdfpvlr_monitoramento_negociado,
										cmdfpcpvoid,
										cmdfpdebito_banoid,
										eqcobroid,
										cmdfpvlr_locacao_negociado,
										" . intval($this->usuoid) . ",
										".intval($contrato_novo)."
									FROM
								 		contrato_modificacao
								 	INNER JOIN
								 		contrato_modificacao_pagamento ON (cmdfpmdfoid = cmfmdfoid)
							 		INNER JOIN
								 		equipamento_classe ON (cmfeqcoid_destino = eqcoid)
								 	WHERE
								  	 	cmfoid = " . intval($cmfoid). "
						  	 	)";

					//echo "<pre>";var_dump($sql);echo "</pre>";
					//exit();

					$rs = $this->executarQuery($sql);

			    } else {

					/*Busca os dados do contrato_pagamento original*/

					if($contrato != $contrato_novo){

					   $sql = "
					   		SELECT
							    cpagsituacao,
								cpagnum_parcela,
								cpagcartao,
								cpagcartao_validade,
								cpagdebito_agencia,
								cpagdebito_cc,
								cpagmonitoramento,
								cpagcpvoid,
								cpagbancodigo,
								cpagobroid_servico,
								cpagvl_servico,
								cpagconoid
							FROM
								 contrato_pagamento
							WHERE
								cpagconoid =" . intval($contrato) . "
							LIMIT 1
							";

						//echo "<pre>";var_dump($sql);echo "</pre>";
						//exit();

						$rs = $this->executarQuery($sql);

						if(pg_num_rows($rs) > 0) {


							$sql = "
									INSERT INTO
										contrato_pagamento
										(
											cpagsituacao,
									        cpagnum_parcela,
									        cpagcartao,
									        cpagcartao_validade,
									        cpagdebito_agencia,
									        cpagdebito_cc,
									        cpagmonitoramento,
									        cpagcpvoid,
									        cpagbancodigo,
									        cpagobroid_servico,
									        cpagvl_servico,
									        cpagconoid,
									        cpagusuoid
										)
										(
										 	SELECT
											    cpagsituacao,
												cpagnum_parcela,
												cpagcartao,
												cpagcartao_validade,
												cpagdebito_agencia,
												cpagdebito_cc,
												cpagmonitoramento,
												cpagcpvoid,
												cpagbancodigo,
												cpagobroid_servico,
												cpagvl_servico,
												cpagconoid,
												".intval($this->usuoid)."
											FROM
												 contrato_pagamento
											WHERE
												cpagconoid =" . intval($contrato_novo) . "
											LIMIT 1
								        )";

							//echo "<pre>";var_dump($sql);echo "</pre>";exit();

							$rs = $this->executarQuery($sql);

						}

				   	}

				}

			}

     	} else {
     		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
     	}

	}

	/**
	 * Busca dados da tabela contrato_modificacao_pagamento
	 * p/ gravar na tabela contrato_modificacao
	 *
	 * @author Vinicius Senna
	 * @param int $cmdfpoid | id da tabela contrato_modificacao_pagamento
	 * @return array
	 * @throws ErrorException
	 */
	public function recuperaContratoModificacaoPagamento($cmfoid,$connumeroNovo) {

		$sql = "INSERT INTO
					contrato_pagamento
					(
						cpagconoid,
						cpagforcoid,
						cpagnum_parcela,
						cpagcartao,
						cpagcartao_validade,
						cpagdebito_agencia,
						cpaghabilitacao,
						cpagmonitoramento,
						cpagusuoid,
						cpagcpvoid,
						cpagobroid_servico
					)
					(
					SELECT
						" . intval($connumeroNovo) . ",
						cmdfpforcoid,
						cmdfpnum_parcela,
						cmdfpcartao,
						cmdfpcartao_vencimento,
						cmdfpdebito_agencia,
						cmdfpvlr_locacao_negociado,
						cmdfpvlr_monitoramento_negociado,
						cmdfpusuoid,
						cmdfpcpvoid,
						1
					FROM
						contrato_modificacao_pagamento
					WHERE
						cmdfpmdfoid = ". intval($cmfoid) . ")";

		$rs = $this->executarQuery($sql);

		return true;
	}

	/**
	 * Recupera contrato_pagamento do contrato original
	 *
	 * @author Vinicius Senna
	 */
	public function recuperaContratoPagamento($connumero) {

		$sql = "SELECT
					cpagconoid,
					cpagforcoid,
					cpagnum_parcela,
					cpagcartao,
					cpagcartao_validade,
					cpagdebito_agencia,
					cpaghabilitacao,
					cpagmonitoramento,
					cpagusuoid,
					cpagcpvoid,
					cpagobroid_servico,
					cpagvl_servico
				FROM
					contrato_pagamento
				WHERE
					cpagconoid = " . intval($connumero);

		$rs = pg_query($this->conn,$sql);

		if(!$rs || pg_num_rows($rs) == 0) {
			return false;
		}

		$cmdfpforcoid      					= pg_fetch_result ($rs, 0, 'cpagforcoid');
        $cmdfpnum_parcela   				= pg_fetch_result ($rs, 0, 'cpagnum_parcela');
        $cmdfpcartao   						= pg_fetch_result ($rs, 0, 'cpagcartao');
        $cmdfpcartao_vencimento   			= pg_fetch_result ($rs, 0, 'cpagcartao_validade');
        $cmdfpdebito_agencia  				= pg_fetch_result ($rs, 0, 'cpagdebito_agencia');
        $cpaghabilitacao 					= pg_fetch_result ($rs, 0, 'cpaghabilitacao');
        $cmdfpvlr_monitoramento_tabela      = pg_fetch_result ($rs, 0, 'cpagmonitoramento');
        $cmdfpusuoid      					= pg_fetch_result ($rs, 0, 'cpagusuoid');
        $cmdfpcpvoid     					= pg_fetch_result ($rs, 0, 'cpagcpvoid');
        $cmdfpobroid      					= pg_fetch_result ($rs, 0, 'cpagobroid_servico');
        $cmdfpvlr_taxa_tabela      			= pg_fetch_result ($rs, 0, 'cpagvl_servico');

        return array(
	        'cmdfpforcoid'					=>	 $cmdfpforcoid,
		    'cmdfpnum_parcela' 				=>	 $cmdfpnum_parcela,
		    'cmdfpcartao' 					=>   $cmdfpcartao,
		    'cmdfpcartao_vencimento' 		=>   $cmdfpcartao_vencimento,
		    'cmdfpdebito_agencia' 			=>   $cmdfpdebito_agencia,
		    'cpaghabilitacao' 				=>   $cpaghabilitacao,
		    'cmdfpvlr_monitoramento_tabela' =>   $cmdfpvlr_monitoramento_tabela,
		    'cmdfpusuoid' 					=>   $cmdfpusuoid,
		    'cmdfpcpvoid' 					=>   $cmdfpcpvoid,
		    'cmdfpobroid' 					=>   $cmdfpobroid,
		    'cmdfpvlr_taxa_tabela' 			=>   $cmdfpvlr_taxa_tabela
    	);
	}


	/**
	 * Atualiza a tabela contrato_servico, caso migra os acessorios
	 *
	 * @author Vinicius Senna
	 * @param int $consconoid | id do contrato
	 * @param int $conumeroNovo | id do contrato novo
	 */
	public function atualizaContratoServico($consconoid, $conumeroNovo) {

		$sql = "
			UPDATE
				contrato_servico
			SET
				consconoid = " . intval($conumeroNovo) . "
			WHERE
				consconoid = " . intval($consconoid);

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

      	$rs = $this->executarQuery($sql);

	}

	/**
	* Insere novos servicos na contrato_servico
	*
	* @author Andre L. Zilz
	*
	*/
	public function inserirAcessoriosContratoServico($dados, $connumero, $cmfoid) {


		foreach ($dados as $dado) {

			if($dado->cmssituacao == 'L') {
				$conscpvoid = intval($dado->cmscpvoid);
			} else {
				$conscpvoid = 'NULL';
			}

			$sql = "
				INSERT INTO
					contrato_servico
				(
					consconoid,
					conssituacao,
					consusuoid,
					consobroid,
					consqtde,
					consvalor_tabela,
					consvalor,
					conscpvoid,
					conscmfoid
				)
				VALUES
				(
					".intval($connumero).",
					'".$dado->cmssituacao."',
					".intval($this->usuoid).",
					".intval($dado->cmsobroid).",
					".intval($dado->cmsqtde).",
					".intval($dado->cmsvalor_tabela).",
					".intval($dado->cmsvalor_negociado).",
					".$conscpvoid.",
					".intval($cmfoid)."
				)
				";

			//echo "<pre>";var_dump($sql);echo "</pre>";

			$rs = $this->executarQuery($sql);

		}

		unset($dados);
	}

	/**
	 * Busca dados da tabela contrato_gerenciadora
	 *
	 * @author Vinicius Senna
	 * @param int $connumero | numero do contrato
	 *
	 * @return array
	 */
	public function recuperarDadosGerenciadora($connumero) {

		$retorno = new stdClass();

		$sql = "SELECT
					*
				FROM
					contrato_gerenciadora
				WHERE
					congconnumero = " . intval($connumero);

		if(!$rs = pg_query($this->conn,$sql)) {
			return $retorno;
		}

		$retorno = pg_fetch_object($rs);

		return $retorno;
	}

	public function migraDadosGerenciadora ($connumero,$connumeroNovo) {

		$sql = "SELECT
						conggeroid1,
						conggeroid2,
						conggeroid3,
						congperiodo_ind1,
						congdt_limite1,
						conghr_limite1,
						congperiodo_ind2,
						congdt_limite2,
						conghr_limite2,
						congperiodo_ind3,
						congdt_limite3,
						conghr_limite3,
						congdirecionado1,
						congdirecionado2,
						congdirecionado3
						FROM
							contrato_gerenciadora
						WHERE
							congconnumero = " . intval($connumero);

		$rs = pg_query($this->conn,$sql);

		if(!$rs && pg_num_rows($rs) == 0) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$congDados = pg_fetch_object($rs);

		$arrParametrosUpdate = array (
			'conggeroid1' => $congDados->conggeroid1,
			'conggeroid2' => $congDados->conggeroid2,
			'conggeroid3' => $congDados->conggeroid3,
			'congperiodo_ind1' => $congDados->congperiodo_ind1,
			'congdt_limite1' => $congDados->congdt_limite1,
			'conghr_limite1' => $congDados->conghr_limite1,
			'congperiodo_ind2' => $congDados->congperiodo_ind2,
			'congdt_limite2' => $congDados->congdt_limite2,
			'conghr_limite2' => $congDados->conghr_limite2,
			'congperiodo_ind3' => $congDados->congperiodo_ind3,
			'congdt_limite3' => $congDados->congdt_limite3,
			'conghr_limite3' => $congDados->conghr_limite3,
			'congdirecionado1' => $congDados->congdirecionado1,
			'congdirecionado2' => $congDados->congdirecionado1,
			'congdirecionado3' => $congDados->congdirecionado1
		);

		$valoresUpdate = '';
		$strSeparador = '';
		$contadorCampos = 0;

		foreach ($arrParametrosUpdate as $key => $value){

			if(is_null($value) == FALSE) {
	            $valoresUpdate .= $strSeparador . $key." = '".$value."'";
	            $strSeparador = ', ';
	            $contadorCampos++;
	        }

        }

        if($contadorCampos > 0) {
        	$sql = "UPDATE
					contrato_gerenciadora
					SET " .$valoresUpdate. " WHERE
						congconnumero = " . intval($connumeroNovo);

			$rs = $this->executarQuery($sql);
        }

		return true;

	}


	/**
	 * Atualiza id da modificacao na tabela contrato
	 *
	 * @author Vinicius Senna
	 * @param int connumero | id do contrato
	 * @param int cmfoid | id da modificacao
	 *
	 * @return boolean  (true=OK /false = falha)
	 */
	public function atualizaModificacaoContratoNovo($connumero, $cmfoid) {

		$sql = "UPDATE
					contrato
				SET
					concmfoid = " . intval($cmfoid) ."
				WHERE
					connumero = " . intval($connumero);

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		return true;
	}

	/**
	 * Atualiza tabela ordem_servico com as modificacoes
	 *
	 * @author Vinicius Senna
	 * @param int $connumero | id do contrato
	 * @param int $ordcmtoid | id da modificacao
	 *
	 * @return boolean  (true=OK /false = falha)
	 */
	public function atualizaOrdemServicoModificacao($connumero, $ordcmfoid) {

		$sql = "UPDATE
					ordem_servico
				SET
					ordcmfoid = " . intval($ordcmfoid) ."
				WHERE
					ordconnumero = " . intval($connumero) ."
				AND
					ordstatus != 3
				AND
					ordcmfoid IS NULL
				";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		return true;
	}


	/**
	 * Insere os dados da mensalidade na tabela contrato_obrigacao_financeira
	 *
	 * @author Vinicius Senna
	 * @param int $connumero | id do contrato
	 *
	 * @return boolean  (true=OK /false = falha)
	 */
	public function insereDadosMensalidade ($connumero,$valor,$eqcoid) {

		$retorno = 0;
		//TODO: buscar valor mensalidade (contrato_modificacao_pagamento)
		$sql = "INSERT INTO
					contrato_obrigacao_financeira
					(
						cofconoid,
						cofobroid,
						cofvl_obrigacao,
						cofdt_inicio,
						cofeqcoid
					)
					VALUES
					(
						" . intval($connumero). ",
						1,
						" . number_format($valor, 2, '.', '') .",
						NOW(),
						" . intval($eqcoid). "
					) RETURNING cofoid;";

		$rs = $this->executarQuery($sql);

		if(pg_affected_rows($rs) > 0) {
            $id = pg_fetch_object($rs);
            $retorno = $id->cofoid;
        }

		return $retorno;
	}

	/**
	 * Identifica a categoria de alteracao
	 *
	 * @author Vinicius Senna
	 * @param int $idContratoModificacao | id da modificacao do contrato
	 *
	 * @return array
	 * @throws ErrorException
	 */
	public function identificaCategoriaAlteracao($idContratoModificacao) {

        $sql = "SELECT

                  CASE WHEN
                     mdfcmtoid = 14 THEN 1
                  WHEN
                     mdfcmtoid = 16 THEN 2
                  WHEN
                     mdfcmtoid = 15 THEN 3  END AS categoria
                  FROM

                    contrato_modificacao,modificacao

                  WHERE cmfmdfoid = mdfoid AND  cmfoid = " . intval($idContratoModificacao) . ";";

        $rs = pg_query($this->conn,$sql);

        if(!$rs || pg_num_rows($rs) == 0) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $row = pg_fetch_object($rs);
        return $row;
    }

	/**
	 * Cancela ordem de servico
	 *
	 * @author Vinicius Senna
	 *
	 * @param int $valor | valor da pesquisa
	 * @param int $cd_usuario | id do usuario
	 * @param string $descricao_hist | descricao do historico
	 * @param string $campo | 'cmfoid' / 'connumero'
	 *
	 * @throws ErrorException
	 */
	public function cancelaOrdemServico($valor, $cd_usuario, $descricaoHist, $campo) {

		if($campo == 'connumero') {
			$where = "ordconnumero = " . intval($valor);
		} else {
			$where = "ordcmfoid = " . intval($valor);
		}

		$sql = "
			SELECT
				ordoid
			FROM
				ordem_servico
			WHERE
				".$where."
				";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		if (pg_num_rows($rs) > 0) {

			while ($row = pg_fetch_object($rs)) {

				$sqlCancelar = "
					UPDATE
						ordem_servico
					SET
						ordstatus = 9
					WHERE
						ordoid = ".$row->ordoid;

				// echo "<pre>";var_dump($sqlCancelar);echo "</pre>";exit();
				//$rs = 
				$this->executarQuery($sqlCancelar);

				$this->cancelarAgendamentosOS($row->ordoid, $cd_usuario, $descricaoHist);
				$this->insereOrdemSituacao($row->ordoid, $cd_usuario, $descricaoHist);

			}
			//Fim While
		}

	}

	/**
	* Inativa os servicos de um contrato relacionado a uma modificacao
	* @param int $cmfoid | ID da contrato_modificacao
	*/
	public function inativarContratoServico($cmfoid) {

		$sql = "
			UPDATE
				contrato_servico
			SET
				consiexclusao = NOW(),
				consusuoid_excl = ".intval($this->usuoid)."
			WHERE
				conscmfoid IS NOT NULL
			AND
				conscmfoid = ".intval($cmfoid)."
			";

		// echo "<pre>";var_dump($sql);echo "</pre>";exit();
        $rs = $this->executarQuery($sql);
	}

	/**
	 * Cancela o agendamento da OS
	 * @param $ordoid, $usuario, $descricaoHist
	 * @return boolean
	 */
	public function cancelarAgendamentosOS($ordoid, $usuario, $descricaoHist) {

		$sql = "
			UPDATE
				ordem_servico_agenda
			SET
				osaexclusao = NOW(),
				osausuoid_excl = " . intval($usuario) . ",
				osamotivo_excl = '" . $descricaoHist . "'
			WHERE
				osaordoid = " . intval($ordoid)."
			AND
				osaexclusao IS NULL";

		$rs = $this->executarQuery($sql);
	}


	/**
	 * Insere na tabela ordem_situacao
	 *
	 * @author Vinicius Senna
	 */
	public function insereOrdemSituacao($ordoid, $cd_usuario, $descricaoHist, $ordstatus = null) {

		if(is_null($ordstatus)) {

			$status = "(SELECT ordstatus FROM ordem_servico WHERE ordoid = ".intval($ordoid).")";

		} else {
			$status = $ordstatus;
		}

		$sql = "INSERT INTO
					ordem_situacao
                   	(
               			orsordoid,
               			orsusuoid,
               			orssituacao
           			)
				VALUES
	    	        (

						" . intval($ordoid) . ",
						" . intval($cd_usuario) .",
						'" . $descricaoHist . "'
					);";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

	}

	/**
	 * Cria ordem de servico
	 *
	 * @author Vinicius Senna
	 *
	 * @param int $mtioid |
	 * @param string $descricao |
	 * @param int $cd_usuario |
	 */
	public function criaOrdemServicoRetirada($mtioid,$descricao,$cd_usuario, $cmfoid,$connumeroNovo) {

		$retorno = 0;

		$sql = "INSERT INTO
					ordem_servico (
                          	ordoid,
                          	ordveioid,
                          	ordclioid,
       						ordequoid,
                          	ordeveoid,
                          	ordstatus,
	           				ordmtioid,
                          	orddesc_problema,
                          	ordusuoid,
	           				ordconnumero,
                           	ordrelroid,
                           	ordeqcoid,
                           	ordotsoid,
                           	ordcmfoid
                   )
				(
                          SELECT numero_ordem_servico (NEXTVAL('ordem_servico_ordoid_seq'::text)),
            				conveioid,
                           	conclioid,
                           	conequoid,
	            			(
	            				SELECT
	            					equeveoid
            					FROM
            						equipamento
            					WHERE equoid=conequoid
        					),
	            			4,
	            			'" . intval($mtioid) . "',
	            			'" . $descricao . "',
	            			'" . intval($cd_usuario) . "',
	            			connumero,
	            			752,
	            			coneqcoid,
	            			5,
	            			" . intval($cmfoid) . "
				FROM
						contrato
		    	WHERE
		    		connumero = '". intval($connumeroNovo) ."') RETURNING ordoid;";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		if($rs = pg_query($this->conn, $sql)) {

            if(pg_affected_rows($rs) > 0) {
                $id = pg_fetch_object($rs);
                $retorno = $id->ordoid;
            } else {
            	throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
        } else {
        	throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $retorno;
	}

	/**
	 * Insere servicos da OS de retirada
	 *
	 * @author Vinicius Senna
	 *
	 * @param int $ordoid | id da ordem de servico
	 */
	public function insereServicosOS($ordoid, $ositotioid, $ositobs, $ositstatus) {

		$sql = "INSERT INTO
					ordem_servico_item (
						ositotioid,
						ositordoid,
						ositeqcoid,
						ositobs,
						ositstatus
					)

                	(
                		SELECT
				     		" . intval($ositotioid) . ",
							". intval($ordoid) . ",
							ordeqcoid,
							'" . $ositobs . "',
		               		'". $ositstatus ."'
	               		FROM ordem_servico WHERE ordoid = " . intval($ordoid). "
	               	);";
		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		return true;
	}

	/**
	 * Insere retirada de acessorios da ordem de servico
	 *
	 * @author Vinicius Senna
	 * @param int $ordoid | id da ordem de servico
	 *
	 * @return boolean  (true=OK /false = falha)
	 */
	public function insereRetiradaAcessorios($ordoid) {

		$sql = "INSERT INTO
					ordem_servico_item
					(
                       	ositordoid,
                       	ositstatus,
                       	ositotioid,
						ositeqcoid,
						ositobs
					)

                	(
                		SELECT
     							" . intval($ordoid) . ",
                               'P',
                               	otioid,
                              	coneqcoid,
								otidescricao
						FROM
                               contrato,contrato_servico,os_tipo_item,ordem_servico
						WHERE
                               ordconnumero=connumero
						AND consconoid=connumero
						AND consiexclusao IS NULL
						AND (consrefioid>0  OR consinstalacao IS NOT NULL)
						AND   otiobroid=consobroid
						AND  otitipo = 'A'
						AND otiostoid=3
                       	AND ordoid= " . intval($ordoid) . "
					);";

		$rs = $this->executarQuery($sql);

		return true;
	}


	/**
	 * Atualiza OS na tabela contrato_modificacao
	 *
	 * @author Vinicius Senna
	 * @param int $ordoid | id da ordem de servico
	 * @param int $cmfoid | id da tabela contrato_modificacao
	 *
	 * @return boolean  (true=OK /false = falha)
	 */
	public function atualizaOSContratoModificacao($ordoid, $cmfoid) {

		$sql = "UPDATE
					contrato_modificacao
				SET
					cmfordoid = " .intval($ordoid). "
				WHERE
					cmfoid = " . intval($cmfoid);

		$rs = $this->executarQuery($sql);

		return true;
	}

	/**
	 * Habilita linha
	 *
	 * @author Vinicius Senna
	 * @param int $tlinoid
	 * @param string $observacao
	 */
	public function habilitaLinha($tlinoid, $observacao,$tlinaraoid,$tlinnumero) {

		$sql = "UPDATE
					linha
            	SET lincsloid = 1
                WHERE linoid = " . intval($tlinoid) . ";

	            INSERT INTO
	                celular_historico
	                (
	                    celhlinoid,
	                    celhusuoid,
	                    celharaoid,
	                    celhfone,
	                    celhobs,
	                    celhcsloid
	                )
	                VALUES (
	                    "  . intval($tlinoid)      . ",
	                    "  . intval($this->usuoid) . ",
	                    "  . intval($tlinaraoid)   . ",
	                    "  . intval($tlinnumero)   . ",
	                    '" . $observacao           . "',
	                    1
	                );";

		$rs = $this->executarQuery($sql);

		return true;
	}

	/**
	 * Recupera o status da linha
	 *
	 * @author Vinicius Senna
	 * @param int connumero | numero do contrato
	 *
	 * @return array
	 */
	public function recuperaStatusLinha($connumero) {

		$sql = "SELECT connumero, conclioid, linoid, lincsloid, cslstatus, linaraoid, linnumero, equno_fone, equno_serie, equoid, clsoid 
				FROM contrato 
				INNER JOIN equipamento ON equoid = conequoid 
				LEFT JOIN linha ON equno_fone = linnumero AND equaraoid = linaraoid 
				LEFT JOIN celular_linha_canc ON clslinha = linnumero AND clsaraoid = linaraoid 
				LEFT JOIN celular_status_linha ON csloid = lincsloid 
				WHERE linexclusao IS NULL 
				AND connumero = " . intval($connumero);

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0) {

        	$tlinoid      = pg_fetch_result ($rs, 0, 'linoid');
            $tlincsloid   = pg_fetch_result ($rs, 0, 'lincsloid');
			$tcslstatus   = pg_fetch_result ($rs, 0, 'cslstatus');
            $tlinaraoid   = pg_fetch_result ($rs, 0, 'linaraoid');
            $tlinnumero   = pg_fetch_result ($rs, 0, 'linnumero');
            $tequno_fone  = pg_fetch_result ($rs, 0, 'equno_fone');
            $tequno_serie = pg_fetch_result ($rs, 0, 'equno_serie');
			$equoid 	  = pg_fetch_result ($rs, 0, 'equoid');
            $tclsoid      = pg_fetch_result ($rs, 0, 'clsoid');
			$tconclioid   = pg_fetch_result ($rs, 0, 'conclioid');

        	return array(
        		'linoid'      	=> $tlinoid,
        		'lincsloid'   	=> $tlincsloid,
				'cslstatus'		=> $tcslstatus,
        		'linaraoid'  	=> $tlinaraoid,
        		'linnumero'   	=> $tlinnumero,
        		'equno_fone'  	=> $tequno_fone,
        		'equno_serie' 	=> $tequno_serie,
				'equoid'	  	=> $equoid,
        		'clsoid'      	=> $tclsoid,
				'conclioid'		=> $tconclioid
        	);

        } else {

        	throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);

        }
	}

	/**
	 * Recupera ordem de servico pelo numero do contrato
	 *
	 * @author Vinicius Senna
	 * @param int $ordconnumero | id do contrato
	 */
	public function recuperaOrdemServicoPorContrato($ordconnumero) {

		$sql = "SELECT
                    ordoid
                FROM
                    ordem_servico
                WHERE
                    ordconnumero = " . intval($ordconnumero);

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0) {

        	$ordoid = pg_fetch_result ($rs, 0, 'ordoid');

    		return $ordoid;
        } else {

        	return 0;

        }

	}

	/**
	 * Recupera parametrizacao de tipo e grupo
	 *
	 * @author Vinicius Senna
	 */
	public function recuperaParametrizacaoTipo($cmtoid) {

		$sql = "SELECT
				  *
				FROM
				  contrato_modificacao_tipo AS cmt

				INNER JOIN contrato_modificacao_particularidade_tipo AS cmpt
				ON cmpt.cmptcmtoid = cmt.cmtoid

				WHERE
					cmt.cmtoid = " . intval($cmtoid);

		$rs = $this->executarQuery($sql);

      	if(pg_num_rows($rs) == 0) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$row = pg_fetch_object($rs);
		return $row;
	}

	/**
	 * Recupera parametrizacao do grupo
	 *
	 * @author Vinicius Senna
	 */
	public function recuperaParametrizacaoGrupo($cmtoid) {

		$sql = "SELECT
				  *
				FROM
				  contrato_modificacao_tipo AS cmt

				INNER JOIN contrato_modificacao_grupo AS cmg
				ON cmg.cmgoid = cmt.cmtcmgoid

				INNER JOIN contrato_modificacao_particularidade_grupo AS cmpg
				ON cmpg.cmpgcmgoid = cmg.cmgoid

				WHERE
					cmt.cmtoid = " . intval($cmtoid);

		$rs = $this->executarQuery($sql);

      	if(pg_num_rows($rs) == 0) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$row = pg_fetch_object($rs);
		return $row;
	}

	/**
	 * Grava contato na tabela telefone_contato
	 *
	 *
	 */
	public function gravaContato($arrContato) {

		if($arrContato['cmctnome'] == 'NULL' || is_null($arrContato['cmctnome'])) {
			$arrContato['cmctnome'] = '';
		}

		$sql = "INSERT INTO
					telefone_contato
					(
						tctno_ddd_res,
						tctconnumero,
						tctno_fone_res,
						tctno_ddd_com,
						tctno_fone_com,
						tctno_ddd_cel,
						tctno_fone_cel,
						tctrg,
						tctcpf,
						tctorigem,
						tctcontato
					)
					VALUES
					(
						'". $arrContato['tctno_ddd_res']."',
						'". $arrContato['tctconnumero'] ."',
						'". $arrContato['tctno_fone_res'] ."',
						'". $arrContato['tctno_ddd_com'] ."',
						'". $arrContato['tctno_fone_com'] ."',
						'". $arrContato['tctno_ddd_cel'] ."',
						'". $arrContato['tctno_fone_cel'] ."',
						'". $arrContato['tctrg'] ."',
						'". $arrContato['tctcpf'] ."',
						'". $arrContato['tctorigem'] ."',
						'". $arrContato['cmctnome'] ."'
					)";

		$rs = $this->executarQuery($sql);

		return true;
	}

	public function gravaClienteContato($arrContato) {

		if($arrContato['cmctnome'] == 'NULL' || is_null($arrContato['cmctnome'])) {
			$arrContato['cmctnome'] = '';
		}

		$arrContato['tctno_ddd_res'] = substr($arrContato['tctno_ddd_res'], 1);

		$clicfone_array = '{';

		if(strlen($arrContato['tctno_ddd_res'].$arrContato['tctno_fone_res']) > 0) {
			$clicfone_array .= $arrContato['tctno_ddd_res'].$arrContato['tctno_fone_res'];

			if(
				strlen($arrContato['tctno_ddd_cel'].$arrContato['tctno_fone_cel']) > 0
				|| strlen($arrContato['tctno_ddd_cel'].$arrContato['tctno_fone_cel']) > 0){
				$clicfone_array .= ',';
			}
		}

		if(strlen($arrContato['tctno_ddd_com'].$arrContato['tctno_fone_com']) > 0) {
			$clicfone_array .= $arrContato['tctno_ddd_com'].$arrContato['tctno_fone_com'];

			if(strlen($arrContato['tctno_ddd_cel'].$arrContato['tctno_fone_cel']) > 0){
				$clicfone_array .= ',';
			}
		}

		if(strlen($arrContato['tctno_ddd_cel'].$arrContato['tctno_fone_cel']) > 0) {
			$clicfone_array .= $arrContato['tctno_ddd_cel'].$arrContato['tctno_fone_cel'];
		}

		$clicfone_array .= '}';

		$sql = "INSERT INTO
					cliente_contato
					(
						clicconnumero,
						clicfone,
						clicrg,
						cliccpf,
						clicnome,
						clicusuoid,
						clicclioid,
						clicobs,
						clicfone_array,
						clicid_nextel
					)
					VALUES
					(
						". intval($arrContato['tctconnumero']) .",
						'". $arrContato['tctno_ddd_res'].$arrContato['tctno_fone_res'] ."',
						'". $arrContato['tctrg'] ."',
						". floatval($arrContato['tctcpf']) .",
						'". $arrContato['cmctnome'] ."',
						". intval($this->usuoid) .",
						". intval($arrContato['clioid']) .",
						'". $arrContato['clicobs'] ."',
						'". $clicfone_array ."',
						'". $arrContato['nextel'] ."'
					)";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();
		$rs = $this->executarQuery($sql);

		return true;

	}

	/**
	 * Atualiza tipo do contrato
	 *
	 * @author Vinicius Senna
	 */
	public function atualizaTipoContrato($cmftpcoid_destino,$connumero) {

		$sql = "UPDATE contrato SET conno_tipo = ".intval($cmftpcoid_destino)." WHERE connumero=".intval($connumero);

		$rs = $this->executarQuery($sql);

		return true;
	}


	/**
	 * Refideliza contrato
	 *
	 * @author Vinicius Senna
	 */
	public function refidelizaContrato($connumero,$usuario,$cmfcvgoid,$eqcoid) {
	 	//INSERE NA TABELA historico_fidelizacao_contrato,  de acordo com o id da contrato_modificacao:
		$sql = "SELECT
					cofoid
				FROM
					contrato_obrigacao_financeira
				WHERE
					cofconoid=".intval($connumero)."
				and cofobroid=1
				and cofdt_termino IS NULL;";

		$rs = $this->executarQuery($sql);

		$res = pg_fetch_object($rs);
		$cofoid = $res->cofoid;

		$sql = "UPDATE contrato_obrigacao_financeira SET cofdt_termino=now() WHERE cofoid=" . intval($cofoid);

		$rs = $this->executarQuery($sql);

		$taxa = $this->recuperarMonitoramentoLocacaoContrato($connumero);
		$cofoidNovo = $this->insereDadosMensalidade($connumero,$taxa->cpagmonitoramento,$eqcoid);


		$sql = "INSERT INTO
					historico_fidelizacao_contrato
					(
					  	hfcconnumero, -- ID da tabela contrato (FK)
  						hfcgctoid, -- ID da tabela gerador_contrato_texto (FK)
  						hfcdt_fidelizacao, -- Data de fidelizacao
  						hfcprazo, -- quantidade de meses
  						hfcusuoid_inclusao, -- usuario que fez o cadastro
  						hfccof_anterior, -- Valor Anterior da Obrigacao Financeira
  						hfccof_atual
  					)
  					VALUES
  					(
  						" . intval($connumero) . ",
  						3,
  						NOW(),
						(SELECT cvgvigencia FROM contrato_vigencia WHERE cvgoid = " . intval($cmfcvgoid) . "),
						" . intval($usuario) .",
						" . intval($cofoid).",
						" .intval($cofoidNovo) . "
					) ";

		$rs = $this->executarQuery($sql);

		return true;
	}

	/**
	 *
	 *
	 * @author Vinicius Senna
	 */
	public function geraContratoRnr($cmfoid,$usuario) {

		$sql = "INSERT INTO
					contrato
					(
	              		conusuoid,
						conveioid,
						conclioid,
						coneqcoid,
						conno_tipo,
						conrczoid,
						conregcoid,
						concsioid,
						conmodalidade

					) ( SELECT
						".intval($usuario) . ",
						cmfveioid_novo,
						cmfclioid_destino,
						cmfeqcoid_origem,
						cmftpcoid_destino,
						cmfrczoid,
						(SELECT rczregcoid FROM regiao_comercial_zona WHERE rczoid = cmfrczoid),
						1,
						cmfmodalidade
					  	FROM
			              	contrato_modificacao
						WHERE
			               cmfoid=" . intval($cmfoid) . "

					) RETURNING connumero;";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		if($rs = $this->executarQuery($sql)) {

            if(pg_affected_rows($rs) > 0) {
                $id = pg_fetch_object($rs);
                $retorno = $id->connumero;
            } else {
            	throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
        } else {
        	throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $retorno;
	}

	public function alteraContratoOriginalRnr($connumero, $connumeroNovo) {

		$sql ="
			UPDATE
				contrato
			SET
				condt_alteracao = now(),
              	connumero_novo_rnr = " . intval($connumeroNovo). "
			WHERE
				connumero = " . intval($connumero);

		$rs = $this->executarQuery($sql);
	}

	/**
	 * Gera OS de instalacao
	 *
	 * @author Vinicius Senna
	 */
	public function geraOSInstalacao($cmfoid, $usuario,$connumeroNovo) {

		$sql = "INSERT INTO ordem_servico (
								ordoid,
								ordveioid,
								ordclioid,
								ordstatus,
								ordmtioid,
								orddesc_problema,
								ordusuoid,
								ordconnumero,
								ordrelroid,
								ordeqcoid,
								ordcmfoid,
								ordostoid,
								ordotsoid
							)
                      		(SELECT
                      			numero_ordem_servico (NEXTVAL('ordem_servico_ordoid_seq'::text)),
        						conveioid,
                   				conclioid,
                   			 	4,
                   			 	2,
                   			 	'INSTALAÇÃO',
                   			 	'" . intval($usuario) . "',
                   			 	connumero,
                   			 	752,
                   			 	coneqcoid,
                   			 	" . intval($cmfoid) . ",
                   			 	1,
                   			 	5
							FROM
								contrato
					    	WHERE
					    		connumero = ".intval($connumeroNovo).")
							RETURNING
								ordoid
					    	";
	    //echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

		if(pg_affected_rows($rs) > 0) {
			$registro = pg_fetch_object($rs);
			$ordoid = isset($registro->ordoid)	? $registro->ordoid : '';
		} else {
			 throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		return $ordoid;

	}

	/**
	 *
	 *
	 * @author Vinicius Senna
	 */
	public function insereKitBasico($connumeroNovo,$usuario){

		$sql = "
			INSERT INTO
				contrato_servico
			(
				consconoid,
				conssituacao,
				consusuoid,
				consqtde,
				consvalor,
				consinstalar,
				consobroid
			)
			(
				SELECT
					" . intval($connumeroNovo) . ",
					'B',
					" . intval($usuario) . ",
					1,
					0,
					't',
					ofiservico
				 FROM
				 	contrato
				 INNER JOIN
				 	equipamento_classe ON (coneqcoid = eqcoid)
				 INNER JOIN
				 	obrigacao_financeira_item ON (eqcobroid = ofiobroid)
				WHERE
					ofiexclusao IS NULL
				AND
					connumero = ". intval($connumeroNovo)."
			);
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		$rs = $this->executarQuery($sql);

	}

	public function adicionaServicoTrocaEqpto($ordoid, $usuario, $eqcoidServ) {

		 $sql = "INSERT INTO
                    ordem_situacao
                    (
                        orsordoid,
                        orsusuoid,
                        orssituacao
                    )
                    SELECT
                        ordoid,
                        " . intval($usuario) . ",
                        'Migracao do contrato. Equipamento Nº $tequno_serie consta sem linha Habilitada.'
                    FROM
                        ordem_servico
                    WHERE
                        ordoid = $ordoid;

                SELECT ordem_servico_item_i('{  \"104\"
                                                \"".$ordoid."\"
                                                \"\"
                                                \"".$eqcoidServ."\"
                                                \"MIGRAÇÃO DE CONTRATO COM LINHA CANCELADA.\"
                                                \"P\" }') as ositoid;";

        if(!$rs = pg_query($this->conn,$sql)){
            throw new exception ('Houve um erro ao adicionar o serviço de troca de equipamento.');
        }
	}

	public function geraOSTrocaEquipamento($usuario, $connumero) {

		$sql = "INSERT INTO
                    ordem_servico
                    (
                        ordveioid,
                        ordclioid,
                        ordequoid,
                        ordeveoid,
                        ordstatus,
                        ordmtioid,
                        orddesc_problema,
                        ordusuoid,
                        ordconnumero,
                        ordrelroid,
                        ordostoid,
                        ordotsoid
                    )
                    (
                        SELECT
                        conveioid,
                        conclioid,
                        conequoid,
                        (
                            SELECT
                                equeveoid
                            FROM
                                equipamento
                            WHERE
                                equoid = conequoid
                        ),
                        1,
                        16,
                        'MIGRAÇÃO DE CONTRATO COM LINHA CANCELADA',
                        " . intval($usuario) . ",
                        connumero,
                        752,
                        4,
                        5
                        FROM
                            contrato
                        WHERE
                            connumero = " . intval($connumero) . "
                    ) RETURNING ordoid;";

        $rs = pg_query($this->conn,$sql);

        if(!$rs) {
        	 throw new exception ('Houve um erro ao gerar a O.S. de troca de equipamento.');
        }

        $row = pg_fetch_object ($rs);

        return $row->ordoid;

	}

	public function atualizaContratoPgto($cmfoid) {


		$sql = "UPDATE
				contrato_pagamento
				SET
					cpagsituacao='L',
					cpagnum_parcela=cmdfpnum_parcela,
					cpagcartao=cmdfpcartao,
					cpagcartao_validade=cmdfpcartao_vencimento,
					cpagdebito_agencia=cmdfpdebito_agencia,
					cpagdebito_cc=cmdfpdebito_cc,
					cpagmonitoramento=cmdfpvlr_monitoramento_negociado,
					cpagcpvoid=cmdfpcpvoid,
					cpagbancodigo=cmdfpdebito_banoid,
					cpagobroid_servico=eqcobroid,
					cpagvl_servico=cmdfpvlr_locacao_negociado,
					cpagusuoid = " . intval($this->usuoid) . "
				FROM
			         contrato_modificacao,
			         contrato_modificacao_pagamento,
			         equipamento_classe
			         WHERE
			         cmdfpmdfoid=cmfmdfoid
			         AND cpagconoid = cmfconnumero
			         AND cmfeqcoid_destino = eqcoid
			         AND cmfoid=" . intval($cmfoid);

		$rs = $this->executarQuery($sql);

		return true;
	}

	/**
	 *
	 *
	 * @author  Vinicius Senna
	 */
	public function recuperaVeiculoDuplicacaoContrato($conveioid) {

		$sql = "SELECT
					veiplaca
				FROM
					veiculo
				WHERE
					veioid = " . intval($conveioid);

		$rs = $this->executarQuery($sql);

		if(pg_num_rows($rs) == 0) {
			 throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$veiplaca = pg_fetch_object ($rs);

		return $veiplaca->veiplaca;

	}

	/**
	 *
	 *
	 * @author Vinicius Senna
	 *
	 */
	public function geraVeiculoNovo($placaNova, $veioidAntigo) {


		$sql = "INSERT INTO
					veiculo
					(
						veino_renavan,
						veiplaca,
						veichassi,
						veimlooid,
						veisegoid,
						veicor,
						veino_ano,
						veicombustivel,
						veiapolice,
						veiutilizacao,
						veidt_ini_apolice,
						veidt_fim_apolice,
						veiproprietario,
						veiprop_endereco,
						veiprop_cidade,
						veiprop_uf,
						veiprop_fone,
						veidt_exclusao,
						veidt_cadastro,
						veiprop_cep,
						veidt_pacote,
						veiusuoid,
						veino_item,
						veino_adiantamento,
						veicod_cia,
						veicod_ramo,
						veino_proposta,
						veidt_alteracao,
						veifrota,
						veino_endosso,
						veidt_alteracao_frota,
						veitipo_doc_seg,
						veiprazo_inst,
						veicod_unid_emis,
						veino_motor,
						veino_serie,
						veicod_subramo,
						veifipe_codigo,
						veifipe_digito,
						veinovo_prazo,
						veichassi_seguradora,
						veiplaca_seguradora,
						veicod_veiculo,
						veiano_fabric,
						veinum_portas,
						veium_eixos,
						veiuso_veiculo,
						veiusuexclusao,
						veiatualizado_itau,
						veialterdo_bradesco,
						veiusuoid_alteracao,
						veiatualizado_bradesco,
						veichave_geral,
						veiid_agendamento ,
						veiid_recibo ,
						veifiloid ,
						veiveipoid ,
						veivisualizacao_sasweb,
						veidt_alteracao_veiculo,
						veiauto_utilizacao,
						veipsuvoid,
						veipsfuoid,
						veipscoid,
						veicarro_zero
					)
					(
						SELECT
							veino_renavan,
							'". $placaNova ."',
							veichassi,
							veimlooid,
							veisegoid,
							veicor,
							veino_ano,
							veicombustivel,
							veiapolice,
							veiutilizacao,
							veidt_ini_apolice,
							veidt_fim_apolice,
							veiproprietario,
							veiprop_endereco,
							veiprop_cidade,
							veiprop_uf,
							veiprop_fone,
							veidt_exclusao,
							veidt_cadastro,
							veiprop_cep,
							veidt_pacote,
							veiusuoid,
							veino_item,
							veino_adiantamento,
							veicod_cia,
							veicod_ramo,
							veino_proposta,
							veidt_alteracao,
							veifrota,
							veino_endosso,
							veidt_alteracao_frota,
							veitipo_doc_seg,
							veiprazo_inst,
							veicod_unid_emis,
							veino_motor,
							veino_serie,
							veicod_subramo,
							veifipe_codigo,
							veifipe_digito,
							veinovo_prazo,
							veichassi_seguradora,
							veiplaca_seguradora,
							veicod_veiculo,
							veiano_fabric,
							veinum_portas,
							veium_eixos,
							veiuso_veiculo,
							veiusuexclusao,
							veiatualizado_itau,
							veialterdo_bradesco,
							veiusuoid_alteracao,
							veiatualizado_bradesco,
							veichave_geral,
							veiid_agendamento ,
							veiid_recibo ,
							veifiloid ,
							veiveipoid ,
							veivisualizacao_sasweb,
							veidt_alteracao_veiculo,
							veiauto_utilizacao,
							veipsuvoid,
							veipsfuoid,
							veipscoid,
							veicarro_zero
						FROM
							veiculo
						WHERE
							veioid = " . intval($veioidAntigo) . " ) RETURNING veioid;";

		$rs = $this->executarQuery($sql);

		$veioid = pg_fetch_object ($rs);

		return $veioid->veioid;
	}

	public function atualizaPlacaVeiculo($veioid, $placa) {

		$sql = "UPDATE
					veiculo
				SET
					veiplaca = '" . $placa . "'
				WHERE veioid = " . intval($veioid);

		$rs = $this->executarQuery($sql);

        return true;
	}


	/**
	 *
	 *
	 * @author Vinicius Senna
	 */
	public function insereContratoVeiculoNovo($veioidNovo,$usuario,$cmfoid) {

		$sql = "INSERT INTO
					CONTRATO
					(
						conusuoid,
						condt_cadastro,
						condt_ini_vigencia,
						condt_instalacao,
						condt_primeira_instalacao,
						conveioid,
						conclioid,
						coneqcoid,
						conno_tipo,
						conequoid,
						conrczoid,
						conregcoid,
						concsioid,
						connumerodup_orig,
						conmodalidade
					) ( SELECT
							" . intval($usuario) . ",
							NOW(),
							NULL,
							NULL,
							NULL,
							" . intval($veioidNovo) . ",
							cmfclioid_destino,
							cmfeqcoid_destino,
							cmftpcoid_destino,
							NULL,
							cmfrczoid,
							(SELECT rczregcoid FROM regiao_comercial_zona WHERE rczoid = cmfrczoid),
							1,
							cmfconnumero,
							cmfmodalidade
							FROM
								contrato_modificacao
							WHERE
				               cmfoid=" . intval($cmfoid) . "

					) RETURNING connumero;";

		$rs = $this->executarQuery($sql);

		$connumero = pg_fetch_object ($rs);

		return $connumero->connumero;

	}

	/**
	 *
	 *
	 * @author Vinicius Senna
	 */
	public function migraTelefonesContratoOriginal($connumero,$connumeroNovo) {

		$sql = "INSERT INTO
					telefone_contato
					(
						tctno_ddd_res,
						tctconnumero,
						tctno_fone_res,
						tctno_ddd_com,
						tctno_fone_com,
						tctno_ddd_cel,
						tctno_fone_cel,
						tctrg,
						tctcpf,
						tctorigem,
						tctcontato
					)
					(
						SELECT
							tctno_ddd_res,
							".intval($connumeroNovo).",
							tctno_fone_res,
							tctno_ddd_com,
							tctno_fone_com,
							tctno_ddd_cel,
							tctno_fone_cel,
							tctrg,
							tctcpf,
							tctorigem,
							(CASE WHEN tctcontato IS NULL THEN ''
								ELSE
								tctcontato END)
							FROM telefone_contato
						WHERE
							tctconnumero = " . intval($connumero) . ")";

		$rs = $this->executarQuery($sql);
	}


	/**
	 *
	 *
	 * @author Vinicius Senna
	 */
	public function geraOSReinstalacao($usuario, $cmfoid, $connumeroNovo,$conveioidNovo) {

		$sql = "INSERT INTO ordem_servico
					(
						ordoid,
						ordveioid,
						ordclioid,
						ordstatus,
						ordmtioid,
						orddesc_problema,
						ordusuoid,
						ordconnumero,
						ordrelroid,
						ordeqcoid,
						ordcmfoid,
						ordotsoid
					)
					(
                        SELECT
	                        numero_ordem_servico (NEXTVAL('ordem_servico_ordoid_seq'::text)),
							".intval($conveioidNovo).",
							conclioid,
							4,
							7,
							'TROCA DE VEICULO - INSTALACAO (TRC)' ,
							".intval($usuario).",
							connumero,
							752,
							coneqcoid,
							" . intval($cmfoid) . ",
							5
						FROM
							contrato
	    				WHERE
	    					connumero = " . intval($connumeroNovo) ."
					) RETURNING ordoid;";



		$rs = $this->executarQuery($sql);

        $ordoid = pg_fetch_result ($rs, 0, 'ordoid');

		return $ordoid;
	}

	public function insereOrdemServicoEspera($connumero, $ordoidRetirada,$cmfclioid_destino,$cmfveioidNovo = null) {

		$sql = "INSERT INTO
					ordem_servico_espera
					(
						oseconnumero,
						oseordoid_retirada,
						oseclioid,
						oseusuoid";

		if($cmfveioidNovo != null) {
			$sql .= ",oseveioid";
		}

		$sql .= ")
				VALUES
				(
					".intval($connumero).",
					".intval($ordoidRetirada).",
					".intval($cmfclioid_destino).",
					".intval($this->usuoid);

		if($cmfveioidNovo != null) {
			$sql .= "," . intval($cmfveioidNovo);
		}

		$sql .= ")";

		$rs = $this->executarQuery($sql);

        return true;
	}

	public function verificaCompatibilidadeEqptoUp($connumero) {

		$sql = "SELECT
					eveprojeto as projeto_eqpto,
					eqciprojeto as projetos_permitidos
				FROM
					equipamento_classe_instalacao,
					equipamento_classe,
					motivo_substituicao,
					contrato,
					equipamento_versao,
					equipamento
				WHERE
				       conmsuboid=msuboid
				       AND conequoid=equoid
				       AND equeveoid=eveoid
				       AND msubeqcoid=eqcoid
				       AND eqcieqcoid = eqcoid
				       AND eqciexclusao IS NULL
				       AND connumero='" . intval($connumero) . "'
				       AND eveprojeto = ANY (eqciprojeto)
				;
				";

		$rs = $this->executarQuery($sql);

		if(pg_num_rows($rs) > 0) {
			return true;
		}

		return false;
	}

	public function verificaCompatibilidadeAcessoriosUp($connumero) {

		$sql = "SELECT
					ofiservico
                 FROM
                 	contrato,
                 	motivo_substituicao,
                 	obrigacao_financeira_item,
                 	equipamento_classe
                 WHERE
                     conmsuboid=msuboid
                 AND msubeqcoid=eqcoid
                 AND eqcobroid=ofiobroid
                 AND ofiexclusao is null
                 AND ofiservico IN(
                 					SELECT
                 						otiobroid
             						FROM
             						os_tipo,
             						os_tipo_item
             						WHERE
             							otiobroid=ofiservico
             							AND otiostoid = ostoid
             							AND ostoid = 1
             							AND otitipo='A' )
                 AND ofiservico NOT IN (
                 						SELECT
                 						consobroid
                 						FROM
                 						contrato_servico
                 						WHERE
                 						consobroid=ofiservico
                 						AND consinstalacao IS NOT NULL
                 						AND consiexclusao IS NULL
                 						AND consconoid=connumero
                 						)
                 AND connumero=" . intval($connumero);

		$rs = $this->executarQuery($sql);

		if(pg_num_rows($rs) == 0) {
			return true;
		}

		return false;
	}

	public function insereServicosNovos($connumero) {

		$sql = "INSERT INTO contrato_servico(consconoid,conssituacao,consusuoid,consqtde,consvalor,consobroid)
                    (SELECT  " . intval($connumero) . ",'B',2750,1,0,ofiservico
                     FROM
                     contrato,motivo_substituicao,obrigacao_financeira_item,equipamento_classe
                     WHERE
                     conmsuboid=msuboid
                     AND msubeqcoid=eqcoid
                     AND eqcobroid=ofiobroid
                     AND ofiexclusao is null
                     AND ofiservico NOT IN (SELECT consobroid FROM contrato_servico WHERE consobroid=ofiservico AND consinstalacao IS NOT NULL AND consiexclusao IS NULL AND consconoid=connumero)
                     AND connumero=" . intval($connumero). "
                     )";

		$rs = $this->executarQuery($sql);

		return true;
	}

	public function atualizaClasseContrato($connumero, $classeNova) {

		$sql = "UPDATE
					contrato
				SET
					coneqcupgoid = NULL,
					coneqcoid = " . intval($classeNova) . " WHERE connumero = " . intval($connumero);

		$rs = $this->executarQuery($sql);

		return true;
	}


	public function atualizaClasseContratoIncompativel($connumero, $classeNova) {

		$sql = "UPDATE
					contrato
				SET
					coneqcupgoid = " . intval($classeNova) . "
					WHERE connumero = " . intval($connumero) ;

		$rs = $this->executarQuery($sql);

		return true;
	}

	public function recuperaDadosVeiculoSeguro($connumero) {

        $sql = "SELECT DISTINCT
					psa.psaretapolicecd as apolice_anterior
				FROM
					produto_seguro_proposta psp
				INNER JOIN
					produto_seguro_cotacao psc ON (psp.psppscoid = psc.pscoid)
				INNER JOIN
					empresa_beneficio emp ON (emp.emboid = psp.pspemboid)
				INNER JOIN
					contrato con ON (con.connumero = psp.pspconnumero)
				INNER JOIN
					produto_seguro_apolice psa ON (psa.psapspoid = psp.pspoid)
				WHERE
					psp.pspconnumero = " . intval($connumero) . "
				AND
					psp.pspretcodigo = '0'
				AND
					psp.pspretproposta IS NOT NULL
				AND
					psp.pspretproposta <> '0'
				LIMIT 1";

		$rs = $this->executarQuery($sql);
		$row = pg_fetch_object($rs);
		return $row;

    }

    public function recuperaDadosVeiculoSiggo($connumero,$conveioid = null) {


        $sql = "SELECT DISTINCT
					mod.mlofipe_codigo AS codigo_fipe_veiculo,
					vei.veicarro_zero AS id_zero_km,
					vei.veipscoid AS combustivel,
					vei.veipsuvoid AS utilizacao_do_veiculo,
					vei.veino_ano AS ano_modelo,
					mar.mcamarca as marca,
					mod.mlomodelo as modelo,
					vei.veichassi AS chassi,
					vei.veiplaca AS placa,
					cli.clidia_vcto AS dia_de_pagamento,
					con.connumero AS numero_contrato,
					to_char(con.condt_exclusao, 'DD/MM/YYYY') AS data_exclusao_contrato,
					cli.clitipo as tipo_cliente,
					con.coneqcoid as equipamento_classe,
					cli.clifone_res,
					cli.clifone_cel,
					cli.cliemail,
					cli.clirua_res,
					cli.clino_res,
					cli.clicompl_res,
					cli.clicidade_res,
					cli.cliuf_res,
					cli.cliformacobranca,
					cli.clisexo,
					cli.cliestado_civil,
					cli.clidt_nascimento,
					cli.clino_cpf AS cpf_cnpj,
					cli.clino_cep_res AS cep,
					cli.clinome
				FROM
					contrato con
				INNER JOIN
					veiculo vei ON (con.conveioid = vei.veioid)
				INNER JOIN
					modelo mod ON (mod.mlooid = vei.veimlooid)
				INNER JOIN
					marca mar ON (mar.mcaoid = mod.mlomcaoid)
				INNER JOIN
					clientes cli ON (con.conclioid = cli.clioid)
				WHERE";

		if(is_null($conveioid)) {
			$sql .= " con.connumero =" . intval($connumero);
		} else {
			$sql .= " vei.veioid =" . intval($conveioid);
		}

		$sql .= " LIMIT 1";


		$rs = $this->executarQuery($sql);
		$row = pg_fetch_array($rs);
		return $row;

    }

    public function recuperaDadosVeiculoSiggo2($conveioid,$clioid) {


        $sql = "SELECT DISTINCT
					mod.mlofipe_codigo AS codigo_fipe_veiculo,
					vei.veicarro_zero AS id_zero_km,
					vei.veipscoid AS combustivel,
					vei.veipsuvoid AS utilizacao_do_veiculo,
					vei.veino_ano AS ano_modelo,
					mar.mcamarca as marca,
					mod.mlomodelo as modelo,
					vei.veichassi AS chassi,
					vei.veiplaca AS placa
				FROM
					veiculo vei
				INNER JOIN
					modelo mod ON (mod.mlooid = vei.veimlooid)
				INNER JOIN
					marca mar ON (mar.mcaoid = mod.mlomcaoid)
				WHERE vei.veioid =" . intval($conveioid) . "
				AND vei.veipsuvoid IN (1,2,3,9)
				 LIMIT 1";

		$rs = $this->executarQuery($sql);

		$rowVei = pg_fetch_assoc($rs);


		$sql = "SELECT
					cli.clidia_vcto AS dia_de_pagamento,
					cli.clitipo as tipo_cliente,
					cli.clifone_res,
					cli.clifone_cel,
					cli.cliemail,
					cli.clirua_res,
					cli.clino_res,
					cli.clicompl_res,
					cli.clicidade_res,
					cli.cliuf_res,
					cli.cliformacobranca,
					cli.clisexo,
					cli.cliestado_civil,
					cli.clidt_nascimento,
					cli.clino_cpf AS cpf_cnpj,
					cli.clino_cep_res AS cep,
					cli.clinome
					FROM
						clientes AS cli
					WHERE cli.clioid = " . intval($clioid) . " LIMIT 1";

		$rs = $this->executarQuery($sql);

		$rowCLi = pg_fetch_assoc($rs);


		return array_merge($rowVei,$rowCLi);
    }

     public function recuperaDadosVeiculoSiggo3($connumero,$conveioid = null) {


        $sql = "SELECT DISTINCT
					mod.mlofipe_codigo AS codigo_fipe_veiculo,
					vei.veicarro_zero AS id_zero_km,
					vei.veipscoid AS combustivel,
					vei.veipsuvoid AS utilizacao_do_veiculo,
					vei.veino_ano AS ano_modelo,
					mar.mcamarca as marca,
					mod.mlomodelo as modelo,
					vei.veichassi AS chassi,
					vei.veiplaca AS placa,
					cli.clidia_vcto AS dia_de_pagamento,
					con.connumero AS numero_contrato,
					to_char(con.condt_exclusao, 'DD/MM/YYYY') AS data_exclusao_contrato,
					cli.clitipo as tipo_cliente,
					con.coneqcoid as equipamento_classe,
					cli.clifone_res,
					cli.clifone_cel,
					cli.cliemail,
					cli.clirua_res,
					cli.clino_res,
					cli.clicompl_res,
					cli.clicidade_res,
					cli.cliuf_res,
					cli.cliformacobranca,
					cli.clisexo,
					cli.cliestado_civil,
					cli.clidt_nascimento,
					cli.clino_cpf AS cpf_cnpj,
					cli.clino_cep_res AS cep,
					cli.clinome
				FROM
					contrato con
				INNER JOIN
					veiculo vei ON (con.conveioid = vei.veioid)
				INNER JOIN
					modelo mod ON (mod.mlooid = vei.veimlooid)
				INNER JOIN
					marca mar ON (mar.mcaoid = mod.mlomcaoid)
				INNER JOIN
					clientes cli ON (con.conclioid = cli.clioid)
				WHERE";

		if(is_null($conveioid)) {
			$sql .= " con.connumero =" . intval($connumero);
		} else {
			$sql .= " vei.veioid =" . intval($conveioid);
		}

		$sql .= " LIMIT 1";

		$rs = $this->executarQuery($sql);
		$row = pg_fetch_object($rs);
		return $row;

    }

	public function recuperaDadosCliente($clioid) {

		$sql = "SELECT
					*
				FROM clientes
			 	WHERE clioid = " . intval($clioid);

	 	$rs = $this->executarQuery($sql);

      	if(pg_num_rows($rs) == 0) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$row = pg_fetch_object($rs);
		return $row;
	}

	public function recuperaCorretorPadrao() {

		$sql = "SELECT
						A.pscoid,
						A.psccorroid,
						A.psccodseg,
						B.corrtipo,
						B.corrnome,
						B.corrsusep
					FROM
						produto_seguro_corretor AS A
					INNER JOIN
						corretor AS B
						ON A.psccorroid = B.corroid
					WHERE
						A.pscativo = TRUE
					AND
						A.pscdt_exclusao IS NULL
					AND
						B.corrtipo  = 'C'
					AND LENGTH(B.corrsusep) > 0
					AND pscdefault = TRUE";

		$rs = $this->executarQuery($sql);

      	if(pg_num_rows($rs) == 0) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$row = pg_fetch_object($rs);
		return $row;

	}

	public function cobraTaxaUpgradeDowngrade($cmfoid, $data,$mdfoid) {

		$sql = "INSERT INTO
					faturamento_unificado_taxas
					(
						futdt_referencia,
						futclioid,
						futobroid,
						futvalor,
						futconnumero,
						futstatus
					)
					(
						SELECT
							'" . $data . "',
							cmfclioid_destino,
							cmdfpobroid_taxa,
							cmdfpvlr_taxa_negociado,
							CASE WHEN cmfconnumero_novo > 0 THEN cmfconnumero_novo ELSE cmfconnumero END,
							'P'
						FROM contrato_modificacao,contrato_modificacao_pagamento
						WHERE
							cmdfpmdfoid = " .intval($mdfoid)."
						AND
							cmfoid = " . intval($cmfoid) . "
						AND 
							cmdfpobroid_taxa IS NOT NULL
					)";


		$rs = $this->executarQuery($sql);

		return true;
	}

	/**
     * Insere/cria uma proposta nova
     *
     * @return response $response ($response->dados: $prpoid/false)
    */
	public function criaProposta($tipoProposta,$prptipoProposta, $usuario) {

		$sql = "INSERT INTO proposta (prptppoid, prptipo_proposta, prpusuoid)
                      VALUES (". $tipoProposta . ", '". $prptipoProposta . "' , " . intval($usuario) . ") RETURNING prpoid";

      	$rs = $this->executarQuery($sql);

      	if(pg_num_rows($rs) == 0) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$row = pg_fetch_object($rs);
		return $row->prpoid;
	}


 	/*Criar mais uma função que atualiza o motivo de substituicao do contrato */
	public function atualizaMotivoSubContrato($connumero, $motivoSubstituicao) {

		$sql = "UPDATE
					contrato
				SET
					conmsuboid = " . intval($motivoSubstituicao) . " WHERE connumero = " . intval($connumero);

		$rs = $this->executarQuery($sql);

		return true;
	}

	public function atualizaCorretorProposta($prpoid,$prpcorroid) {

		$sql = "UPDATE proposta SET prpcorroid = " . intval($prpcorroid) . " WHERE prpoid = " . intval($prpoid);

		$rs = $this->executarQuery($sql);

		return true;

	}

	public function atualizaPropostaModificacao($prpoid,$cmfoid) {

		$sql = "UPDATE
					contrato_modificacao
				SET
					cmfprpoid = " . intval($prpoid) . " WHERE cmfoid = " . intval($cmfoid);

		$rs = $this->executarQuery($sql);

		return true;
	}

	public function recuperaInformacaoServico($connumero) {

		$sql = "SELECT
					cs.consconoid,
					cs.consobroid,
					con.conclioid,
					obr.obrobrigacao
				FROM
					contrato_servico AS cs
				INNER JOIN
					contrato AS con
					ON cs.consconoid = con.connumero
				INNER JOIN
					obrigacao_financeira AS obr
					ON cs.consobroid = obr.obroid
				WHERE
					cs.consconoid = " . $connumero;

		$rs = $this->executarQuery($sql);

      	if(pg_num_rows($rs) == 0) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$row = pg_fetch_object($rs);
		return $row;

	}


	public function recuperaDadosAnaliseCredito() {

		$sql = "SELECT
					cmacoid,
					cmacclioid,
					cmacobservacao,
					cmacdt_cadastro,
					clinome
				FROM
					cliente_modificacao_analise_credito
				INNER JOIN
					clientes
					ON clioid = cmacclioid
				WHERE
					cmacstatus = 'P'";

		$retorno = array();
		$rs = $this->executarQuery($sql);

        while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}


		return $retorno;

	}

	public function atualizaDadosAnalise($arrDados) {

		$sql = "UPDATE
					cliente_modificacao_analise_credito
				SET
					cmacusuoid_aprovador = " . intval($this->usuoid). " ,
					cmacmotivo_status = '" . $arrDados['cmacmotivo_status'] . "',
					cmacstatus = '". $arrDados['cmacstatus'] ."' " ;

		if(isset($arrDados['cmacdt_liberacao_limite']) && !isset($arrDados['cmacliberado_periodo_indeterminado'])) {
			$sql .= ",cmacdt_liberacao_limite = '" . $arrDados['cmacdt_liberacao_limite'] . "'";
		} else if(!isset($arrDados['cmacdt_liberacao_limite']) && isset($arrDados['cmacliberado_periodo_indeterminado'])) {
			$sql .= ", cmacliberado_periodo_indeterminado = true";
		}

		$sql .= " WHERE cmacoid = " . intval($arrDados['cmacoid']);
		$sql .= " RETURNING cmacclioid";

		$rs = $this->executarQuery($sql);

		$tupla = pg_fetch_object($rs);
		$clioid = isset($tupla->cmacclioid) ? $tupla->cmacclioid : '';

		return $clioid;
	}

	public function buscaEmailUsuario($usuario) {

		$sql = "SELECT
					nm_usuario,
					usuemail
				FROM
					usuarios
				WHERE
					cd_usuario = " . intval($usuario);

		$rs = $this->executarQuery($sql);

        $row = pg_fetch_object($rs);
		return $row;
	}

	public function recuperaDadosAprovacao($cmacoid) {

		$sql = "SELECT
					cmacclioid,
					cmacusuoid_solicitante
				FROM
					cliente_modificacao_analise_credito
				WHERE
					cmacoid = " . intval($cmacoid);

		$rs = $this->executarQuery($sql);

        $row = pg_fetch_object($rs);
		return $row;

	}


	public function desfazContratoOriginal($dados, $connumero) {

		$sql = "
			UPDATE
				contrato
			SET
				coneqcupgoid = NULL,
				coneqcoid = ". intval($dados->cmfeqcoid_origem).",
				conno_tipo = ".intval($dados->cmftpcoid_origem).",
				conmsuboid = NULL
			WHERE
				connumero = ".intval($connumero)."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

      	$rs = $this->executarQuery($sql);

		if(pg_affected_rows($rs) == 0){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
	}

	/**
	* Seta o numero de contrato nos telefones de contato
	*
	* @author Andre L. Zilz
	* @param int $setConnumero | contrato a ser inputado
	* @param int $getConnumero | contrato a ser buscado
	*/
	public function desfazerContratoTelefone($getConnumero, $setConnumero) {

		$sql = "
			UPDATE
				telefone_contato
			SET
				tctconnumero = ".intval($setConnumero)."
			WHERE
				tctconnumero = ".intval($getConnumero)."
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

      	$rs = $this->executarQuery($sql);

	}

	public function desfazMigracaoContrato($connumero_novo,$conveioid,$conequoidAntigo) {

		$sql = "
			UPDATE
				contrato
			SET
				conveioid = " . intval($conveioid).",
				conequoid = " . intval($conequoidAntigo)."
			WHERE
				connumero = " . intval($connumero_novo);

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

      	$rs = $this->executarQuery($sql);

		if(pg_affected_rows($rs) == 0){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
	}

	/**
	* Atualiza os dados da acao de desfazer do contrato
	*
	* @param int cmfoid | ID da contrato_modificacao
	* @param string $obervacao | obervacao do desfazer
	*/
	public function atualizarDesfazerModificacao($cmfoid, $obervacao) {

		$sql = "
			UPDATE
				contrato_modificacao
			SET
				cmfmodificacao_desfeita = NOW(),
				cmfmodificacao_desfeita_motivo = '" . $obervacao . "'
			WHERE
				cmfoid = " . intval($cmfoid);

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

      	$rs = $this->executarQuery($sql);

		if(pg_affected_rows($rs) == 0){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
	}


	/**
	* Retira veiculo, equipamento e cancela contrato
	* @param int $connumero
	*
	*/
	public function cancelaEqpVeiculoContrato($connumero) {

		//Cancelamento automático decorrente da Modificação de Contrato
		$concmcoid = 39;

		$sql = "
			UPDATE
                contrato
            SET
            	conveioid_antigo = conveioid,
            	conequoid_antigo = conequoid,
            	conequoid = NULL,
            	conveioid = NULL,
                condt_exclusao = NOW(),
                conmsuboid = NULL,
               	concmcoid = ".$concmcoid."
  			WHERE
  				connumero = " . intval($connumero);

  		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

      	$rs = $this->executarQuery($sql);

		if(pg_affected_rows($rs) == 0){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

	}

	/**
	* Volta Veiculo, equipamento e acessorios no contrato
	* @param int $connumero
	*
	*/
	public function reverteVeiculo($connumero) {

		$sql = 	"
			UPDATE
                contrato
            SET
            	conveioid = conveioid_antigo,
            	conequoid = conequoid_antigo,
            	conveioid_antigo = NULL,
            	conequoid_antigo = NULL
          	WHERE
          		connumero = " .  intval($connumero);

        //echo "<pre>";var_dump($sql);echo "</pre>";exit();

      	$rs = $this->executarQuery($sql);

		if(pg_affected_rows($rs) == 0){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
	}

	public function buscaFipe($veioid) {
		$sql = 	"SELECT mlofipe_codigo FROM veiculo,modelo WHERE veimlooid=mlooid AND veioid=" . intval($veioid);

		$rs = $this->executarQuery($sql);
		$row = pg_fetch_object($rs);
		return (string) $row->mlofipe_codigo;


		return true;
	}

	public function atualizaVigenciaContrato($connumero) {

		$sql = "UPDATE contrato SET condt_ini_vigencia = NOW() WHERE connumero = " . intval($connumero);

		$rs = $this->executarQuery($sql);

		return true;
	}

	public function atualizaMonitoramento($cmfoid) {

		$sql = "UPDATE
					contrato_obrigacao_financeira
				    SET cofdt_termino=NOW()
				FROM
				    contrato_modificacao
                    WHERE
					cofdt_termino IS NULL
					AND cofconoid = cmfconnumero
					AND cofobroid = 1
					AND cmfoid = " . intval($cmfoid);

		$rs = $this->executarQuery($sql);


		$sql = "INSERT INTO contrato_obrigacao_financeira(cofobroid, cofno_periodo_mes,cofdt_inicio,cofconoid,cofvl_obrigacao,cofeqcoid)
                    (SELECT  1,1,now(),cmfconnumero,cmdfpvlr_monitoramento_negociado,cmfeqcoid_destino
                     FROM
                     contrato_modificacao,
					 contrato_modificacao_pagamento
					 WHERE
                     cmdfpmdfoid=cmfmdfoid
                     AND cmfoid=" . intval($cmfoid). "
                     )";

		$rs = $this->executarQuery($sql);

		return true;
	}

	public function verificaCompatibilidadeEqptoDown($connumero) {

		$sql = "SELECT eveprojeto as projeto_eqpto,eqciprojeto as projetos_permitidos
				from equipamento_classe_instalacao,equipamento_classe,motivo_substituicao,contrato,equipamento_versao,equipamento
				WHERE
				       conmsuboid=msuboid
				       AND conequoid=equoid
				       AND equeveoid=eveoid
				       AND msubeqcoid=eqcoid
				       AND eqcieqcoid = eqcoid
				       AND eqciexclusao IS NULL
				       AND connumero='" . intval($connumero) . "'
				       AND eveprojeto = ANY (eqciprojeto)
				;
				";

		$rs = $this->executarQuery($sql);

		if(pg_num_rows($rs) > 0) {
			return true;
		}

		return false;
	}


	public function verificaCompatibilidadeAcessoriosDown($connumero) {

        $sql = "SELECT
        			consobroid
                 	FROM
						contrato,motivo_substituicao,equipamento_classe,contrato_servico,os_tipo,os_tipo_item
					WHERE
						consconoid=connumero
						AND otiostoid = ostoid
						AND ostoid = 3
						AND otitipo='A'
						AND consobroid = otiobroid
						AND conmsuboid=msuboid
						AND msubeqcoid=eqcoid
						AND consinstalacao IS NOT NULL
						AND consiexclusao IS NULL
						AND consobroid NOT IN (SELECT ofiservico FROM obrigacao_financeira_item WHERE consobroid=ofiservico AND ofiexclusao is null AND eqcobroid=ofiobroid)
						AND connumero=" . intval($connumero);

		$rs = $this->executarQuery($sql);

		if(pg_num_rows($rs) == 0) {
			return true;
		}

		return false;
	}


	public function insereServicosRetiradaDowngradeOS($ordoid) {

		$sql = "INSERT INTO
					ordem_servico_item (
					    ositordoid,
						ositstatus,
						ositobs,
						ositotioid,
						ositeqcoid
					)

                	(
                		SELECT
							$ordoid,
							'P',
							'Retirada Downgrade - '||otidescricao,
							otioid,
							msubeqcoid
						FROM
							contrato,motivo_substituicao,equipamento_classe,contrato_servico,ordem_servico,os_tipo,os_tipo_item
						WHERE
							consconoid=connumero
							AND otiostoid = ostoid
							AND ostoid = 3
							AND otitipo='A'
							AND consobroid = otiobroid
							AND conmsuboid=msuboid
							AND msubeqcoid=eqcoid
							AND consinstalacao IS NOT NULL
							AND consiexclusao IS NULL
							AND consobroid NOT IN (SELECT ofiservico FROM obrigacao_financeira_item WHERE consobroid=ofiservico AND ofiexclusao is null AND eqcobroid=ofiobroid)
							AND ordconnumero=connumero
							AND conssituacao='B'
					        AND ordoid = " . intval($ordoid). "
	               	);";

		$rs = $this->executarQuery($sql);

		return true;
	}
	
	/**
	 * Verifca se a classe necessita ser validada
	 * @param  int $eqcoid
	 * @return string
	 */
	public function verificarClasseCAN($classe) {
	
		$sql = "SELECT
					valvalor,
					(SELECT eqcdescricao FROM equipamento_classe WHERE eqcoid = $classe) AS eqcdescricao
				FROM
					dominio
					INNER JOIN registro ON regdomoid = domoid
					INNER JOIN valor ON valregoid = regoid
				WHERE
					domativo = 1 
					AND domnome ILIKE 'EQUIPAMENTO CLASSE TELEMETRIA CAN';
				";
	
		$rs = $this->executarQuery($sql);
		$row = pg_fetch_object($rs);
		return $row;
		
	}
	
	/**
	 * Busca o status  de compatibilidade CAN de determinado Ano/ Modelo do veículo
	 * @param int $classe
	 * 		  stdClass $veiculo
	 * @return boolean
	 */
	public function validarCompatibilidade($classe,$veiculo) {

		$sql = "SELECT
					cavoid, 
					cavstatus,
					cavano,
					(SELECT mlomodelo FROM modelo WHERE mlooid = cavmlooid) AS mlomodelo,
					(SELECT eqcdescricao FROM equipamento_classe WHERE eqcoid = $classe) AS eqcdescricao
				FROM
					compatibilidade_acessorio_veiculo
				WHERE
					cavdt_exclusao IS NULL
					AND cavano = ".$veiculo->dados['veino_ano']."
					AND cavmlooid = ".$veiculo->dados['veimlooid'].";
				";
		/* 
		true
		--AND cavano = 2013
		--AND cavmlooid = 2969;
		
		false
		--AND cavano = 2013
		--AND cavmlooid = 3190;
		
		null
		--AND cavano = 2013
		--AND cavmlooid = 4208;
		
		retorna mais de um registro
		--AND cavano = 2013
		--AND cavmlooid = 2969;
		 */

		$rs = $this->executarQuery($sql); 
		
        while($registro = pg_fetch_object($rs)) {
        	$retorno[] = $registro;
        }
        
		return $retorno;
		
	}
	
	/**
	 * Busca a descrição da Classe do equipamento e do Ano/ Modelo do veículo 
	 * nos casos em que não possuem a compatibilidade CAN cadastrada.
	 * @param int $classe
	 * 		  stdClass $veiculo
	 * @return boolean
	 */
	public function buscarDadosMsgCAN($classe,$veiculo) {
	
		$sql = "SELECT
					NULL AS cavoid, 
					NULL AS cavstatus, 
					".$veiculo->dados['veino_ano']." AS cavano,	
					mlomodelo, 
					(SELECT eqcdescricao FROM equipamento_classe WHERE eqcoid = $classe) AS eqcdescricao 
				FROM
					modelo
				WHERE
					mlooid = ".$veiculo->dados['veimlooid'].";
				";

		$rs = $this->executarQuery($sql); 
		
        while($registro = pg_fetch_object($rs)) {
        	$retorno[] = $registro;
        }
        
		return $retorno;
		
	}


	/**
	* Insere uma linha no relatório de linhas para reativação
	* @author Thomas de Lima
	* @param Array $linha | Array com informações sobre a linha que será incluída no relatório
	*/
	public function addLinhaRelatorioReativacao($linha) {

		$sql = "INSERT INTO linha_reativacao (  
							linreaclioid, 
							linreausuoid, 
							linrealinnumero, 
							linreaaraoid, 
							linreaobs
							) VALUES ( 
								'{$linha->conclioid}', 
								'{$_SESSION['usuario']['oid']}', 
								'{$linha->linnumero}', 
								'{$linha->linaraoid}', 
								'MODIFICAÇÃO DE CONTRATO: Reativação de Ex'
								)
								RETURNING linreaoid";

		$rs = $this->executarQuery($sql);
		return (int) pg_fetch_result($rs, 0, 'linreaoid');
	}

	/**
	* Busca as particularidades dos status da linha
	* @author Thomas de Lima <thomas.lima.ext@sascar.com.br>
	* @param 
	**/
	public function recuperaParticularidadesStatusLinha()
	{
		$sql = "SELECT cslpoid, cslpdominio, cslpchave, cslpcsloid, cslpbloqueia_acao, cslpmensagem, cslpcallback
				FROM celular_status_linha_particularidade 
				WHERE cslpdt_exclusao IS NULL AND cslpdominio = 'MODIFICACAO_CONTRATO'";
		$rs = $this->executarQuery($sql);
      	if(pg_num_rows($rs) == 0) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
		$resultados = pg_fetch_all($rs);

		/**
		* Montando o array com as regras
		* @return array $retorno - $retorno[ID_TIPO_MODIFICACAO][ID_STATUS_LINHA]
		**/
		$retorno = array();
		foreach ($resultados as $r) {

			$tipoModificacao = $r['cslpchave'];
			$statusLinha = $r['cslpcsloid'] == '' ? 0 : $r['cslpcsloid'];

			$retorno[$tipoModificacao][$statusLinha] = array(
				'trava' => ($r['cslpbloqueia_acao'] == 't' ? true : false),
				'alerta' => $r['cslpmensagem'],
				'callback' => $r['cslpcallback']
			);

		}

		return $retorno;

	}

	/**
	* STI 86713
	* Método para verificar se já existe registro de histórico para a compatibilidade
	**/
	public function verificaHistoricoNotificacao($modelo, $ano, $executivo)
	{
		$executivo = ($executivo > 0 ? ' = '. $executivo : 'IS NULL');

		$sql = "SELECT * FROM compatibilidade_acessorio_historico_notificacao 
				WHERE cahnmlooid = $modelo
				AND cahnano = $ano 
				AND cahnusuoid_cadastro = {$_SESSION['usuario']['oid']} 
				AND cahnexecutivo {$executivo}";
				
		$res = $this->executarQuery($sql);
		if (pg_num_rows($res) > 0) {
			return true;
		}else{
			return false;
		}
	}

	/**
	* STI 86713
	* Método para registrar uma compatibilidade sempre que está não for encontrada na tabela de compatibilidade
	* @param stdClass $params
	**/
	public function registraCompatibilidadeAcessorio($params)
	{
		$cavoid = 0;

		# INSERIR REGISTRO NA TABELA DE COMPATIBILIDADE
		$sql = "INSERT INTO compatibilidade_acessorio_veiculo(cavusuoid_cadastro, cavano, cavmlooid) 
				VALUES (". $_SESSION['usuario']['oid'] .", ". $params->dados['veino_ano'] .",". $params->dados['veimlooid'] .") 
				RETURNING cavoid;";
		$res = $this->executarQuery($sql);
		if (pg_num_rows($res) > 0) {
			$cavoid = pg_fetch_result($res, 0, 'cavoid');
		}

		return $cavoid;

	}

	/**
	* STI 86713
	* Método para inserir um registro na tabela compatibilidade_acessorio_historico_notificacao
	*
	**/
	public function registraHistoricoNotificacao($cavoid, $mlooid, $ano, $modeloVei, $executivo=null)
	{
		// tratando as informações do executivo
		$executivo = ($executivo > 0 ? $executivo : 'NULL');

		$sql = "INSERT INTO compatibilidade_acessorio_historico_notificacao(
							cahnusuoid_cadastro, 
							cahnexecutivo, 
							cahncavoid,
							cahnmlooid,
							cahnano) 
						VALUES (
							". $_SESSION['usuario']['oid'] .", 
							". $executivo .", 
							". $cavoid .",
							". $mlooid .",
							". $ano .") 
						RETURNING cahnoid;";
		
		$rs = $this->executarQuery($sql);

		return true;
	}


	/**
	 * submete uma query
	 * @param  string $query
	 * @return recordset
	 */
    private function executarQuery($query) {

	    if(!$rs = pg_query($this->conn, $query)) {
	        throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	    }
	    return $rs;
    }

	/**
	 * Abre a transação
	 */
	public function begin(){
		pg_query($this->conn, 'BEGIN');
	}

	/**
	 * Finaliza um transacao
	 */
	public function commit(){
		pg_query($this->conn, 'COMMIT');
	}

	/**
	 * Aborta uma transacao
	 */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK');
	}

}
<?php

/**
 * Classe FinCobrancaRegistradaDAO.
 * Camada de modelagem de dados.
 *
 * @package  Financas
 * @author   Gustavo Molitor Porcides <gustavo.porcides.ext@sascar.com.br>
 * 
 */

//Classe para controle dos registro e geração de boleto no Core
use module\Boleto\BoletoService as Boleto;


class FinCobrancaRegistradaDAO {

	/**
	 * Conexão com o banco de dados
	 * @var resource
	 */
	private $conn;

	/**
	 * Mensagem de erro para o processamentos dos dados
	 * @const String
	 */
	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";


	public function __construct($conn) {
		//Seta a conexão na classe
		$this->conn = $conn;
	}

	/**
	 * Busca formas de cobrança registrada
	 * @return [type] [description]
	 */
	public function buscaFormasCobranca() {

		$retorno = array();
		// No momento trazendo apenas a do Santander
		$sql = "SELECT
					forcoid,
					forcnome
				FROM
					forma_cobranca
				WHERE
					forccobranca_registrada = TRUE
					AND forcoid = 84";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Busca resultados da pesquisa de remessa
	 * @return [type] [description]
	 */
	public function pesquisaRemessa($dados, $paginacao = null) {

		$retorno = array();
		$sql = "";

		if($dados->ddl_forma_cobranca_remessa != 0) {
			//$filtros .= ' AND titformacobranca = ' . $dados->ddl_forma_cobranca_remessa;
			//$filtrosConsolidado .= ' AND titcformacobranca = ' . $dados->ddl_forma_cobranca_remessa;
		}

		if(isset($dados->txt_num_remessa) && !empty($dados->txt_num_remessa)) {
			$filtros .= ' AND rtcrnumero_remessa = ' . $dados->txt_num_remessa;
			$filtrosConsolidado .= ' AND rtcrnumero_remessa = ' . $dados->txt_num_remessa;
		}

		if((bool)$dados->chk_titulos_sem_remessa) {
			$filtros .= ' AND titrtcroid IS NULL';
			$filtrosConsolidado .= ' AND titcrtcroid IS NULL';
		}

		if (isset($paginacao->limite) && isset($paginacao->offset)) {
			$pagina = " 
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "
            ";
		}

		$groupOrder = " GROUP BY
							nome_banco,
							numero_remessa,
							data_cadastro,
							id_remessa,
							rtcrarquivo_remessa,
							remessa_titulo_cobr_reg.rtcrtipo_envio,
							usuarios.nm_usuario
						ORDER BY
							data_cadastro DESC
						$pagina";

		$sqlProcessada = "SELECT DISTINCT
							rtcroid AS id_remessa,
                            cfbnome AS nome_banco, 
							rtcrnumero_remessa AS numero_remessa,
							rtcrdt_primeiro_envio AS data_cadastro,
							'Processada' AS status,
							rtcrarquivo_remessa as arquivo,
							/* STI 86972 */
							remessa_titulo_cobr_reg.rtcrtipo_envio tipo_envio,
							usuarios.nm_usuario
						FROM
							titulo 
							INNER JOIN remessa_titulo_cobr_reg ON titrtcroid = rtcroid
							/* STI 86972 */
							INNER JOIN usuarios ON remessa_titulo_cobr_reg.rtcrusuoid = usuarios.cd_usuario
							INNER JOIN forma_cobranca ON forcoid = titformacobranca
							INNER JOIN config_banco ON cfbbanco = forccfbbanco
						WHERE
							rtcrdt_ultimo_envio IS NOT NULL
							AND rtcrdt_exclusao IS NULL
							AND rtcrdt_primeiro_envio BETWEEN '$dados->dt_ini_remessa 00:00:00' AND '$dados->dt_fim_remessa 23:59:59'
							$filtros 
						UNION
						SELECT DISTINCT
							rtcroid AS id_remessa,
							cfbnome AS nome_banco,
							rtcrnumero_remessa AS numero_remessa,
							rtcrdt_primeiro_envio AS data_cadastro,
							'Processada' AS status,
							rtcrarquivo_remessa as arquivo,
							/* STI 86972 */
							remessa_titulo_cobr_reg.rtcrtipo_envio tipo_envio,
							usuarios.nm_usuario
						FROM
							titulo_retencao
							INNER JOIN remessa_titulo_cobr_reg ON titrtcroid = rtcroid
							/* STI 86972 */
							INNER JOIN usuarios ON remessa_titulo_cobr_reg.rtcrusuoid = usuarios.cd_usuario
							INNER JOIN forma_cobranca ON forcoid = titformacobranca
							INNER JOIN config_banco ON cfbbanco = forccfbbanco
						WHERE
							rtcrdt_ultimo_envio IS NOT NULL
							AND rtcrdt_exclusao IS NULL
							AND rtcrdt_primeiro_envio BETWEEN '$dados->dt_ini_remessa 00:00:00' AND '$dados->dt_fim_remessa 23:59:59'
							$filtros 
						UNION
						SELECT DISTINCT
							rtcroid AS id_remessa,
							cfbnome AS nome_banco, 
							rtcrnumero_remessa AS numero_remessa,
							rtcrdt_primeiro_envio AS data_cadastro,
							'Processada' AS status,
							rtcrarquivo_remessa as arquivo,
							/* STI 86972 */
							remessa_titulo_cobr_reg.rtcrtipo_envio tipo_envio,
							usuarios.nm_usuario
						FROM
							titulo_consolidado
							INNER JOIN remessa_titulo_cobr_reg ON titcrtcroid = rtcroid
							/* STI 86972 */
							INNER JOIN usuarios ON remessa_titulo_cobr_reg.rtcrusuoid = usuarios.cd_usuario
							INNER JOIN forma_cobranca ON forcoid = titcformacobranca
							INNER JOIN config_banco ON cfbbanco = forccfbbanco
						WHERE
							rtcrdt_ultimo_envio IS NOT NULL
							AND rtcrdt_exclusao IS NULL
							AND rtcrdt_primeiro_envio BETWEEN '$dados->dt_ini_remessa 00:00:00' AND '$dados->dt_fim_remessa 23:59:59'
							$filtrosConsolidado";

		$sqlAguardando = "SELECT DISTINCT
							rtcroid AS id_remessa,
							 cfbnome AS nome_banco,
							rtcrnumero_remessa AS numero_remessa,
							rtcrdt_primeiro_envio AS data_cadastro,
							'Aguardando Registro' AS status,
							rtcrarquivo_remessa AS arquivo,
							/* STI 86972 */
							remessa_titulo_cobr_reg.rtcrtipo_envio tipo_envio,
							usuarios.nm_usuario
						FROM
							titulo 
							INNER JOIN remessa_titulo_cobr_reg ON titrtcroid = rtcroid
							/* STI 86972 */
							INNER JOIN usuarios ON remessa_titulo_cobr_reg.rtcrusuoid = usuarios.cd_usuario
							INNER JOIN forma_cobranca ON forcoid = titformacobranca
							INNER JOIN config_banco ON cfbbanco = forccfbbanco
						WHERE
							rtcrdt_ultimo_envio IS NULL
							AND rtcrdt_exclusao IS NULL
							AND rtcrdt_primeiro_envio BETWEEN '$dados->dt_ini_remessa 00:00:00' AND '$dados->dt_fim_remessa 23:59:59'
							$filtros 
						UNION
						SELECT DISTINCT					
							rtcroid AS id_remessa,
							cfbnome AS nome_banco,
							rtcrnumero_remessa AS numero_remessa,
							rtcrdt_primeiro_envio AS data_cadastro,
							'Aguardando Registro' AS status,
							rtcrarquivo_remessa AS arquivo,
							/* STI 86972 */
							remessa_titulo_cobr_reg.rtcrtipo_envio tipo_envio,
							usuarios.nm_usuario
						FROM
							titulo_retencao
							INNER JOIN remessa_titulo_cobr_reg ON titrtcroid = rtcroid
							/* STI 86972 */
							INNER JOIN usuarios ON remessa_titulo_cobr_reg.rtcrusuoid = usuarios.cd_usuario
							INNER JOIN forma_cobranca ON forcoid = titformacobranca
							INNER JOIN config_banco ON cfbbanco = forccfbbanco
						WHERE
							rtcrdt_ultimo_envio IS NULL
							AND rtcrdt_exclusao IS NULL
							AND rtcrdt_primeiro_envio BETWEEN '$dados->dt_ini_remessa 00:00:00' AND '$dados->dt_fim_remessa 23:59:59'
							$filtros
						UNION
						SELECT DISTINCT
							rtcroid AS id_remessa,
							cfbnome AS nome_banco,  
							rtcrnumero_remessa AS numero_remessa,
							rtcrdt_primeiro_envio AS data_cadastro,
							'Aguardando Registro' AS status,
							rtcrarquivo_remessa AS arquivo,
							/* STI 86972 */
							remessa_titulo_cobr_reg.rtcrtipo_envio tipo_envio,
							usuarios.nm_usuario
						FROM
							titulo_consolidado
							INNER JOIN remessa_titulo_cobr_reg ON titcrtcroid = rtcroid
							/* STI 86972 */
							INNER JOIN usuarios ON remessa_titulo_cobr_reg.rtcrusuoid = usuarios.cd_usuario
							INNER JOIN forma_cobranca ON forcoid = titcformacobranca
							INNER JOIN config_banco ON cfbbanco = forccfbbanco
						WHERE
							rtcrdt_ultimo_envio IS NULL
							AND rtcrdt_exclusao IS NULL
							AND rtcrdt_primeiro_envio BETWEEN '$dados->dt_ini_remessa 00:00:00' AND '$dados->dt_fim_remessa 23:59:59'
							$filtrosConsolidado";

		$sqlCancelamento = "
						SELECT DISTINCT
							rtcroid AS id_remessa,
							'BANCO SANTANDER S/A' AS nome_banco, 
							rtcrnumero_remessa AS numero_remessa,
							rtcrdt_primeiro_envio AS data_cadastro,
							'Enviado para cancelamento' AS status,
							rtcrarquivo_remessa as arquivo,
							rtcrtipo_envio tipo_envio,
							usuarios.nm_usuario
						FROM 
							remessa_titulo_cobr_reg
							INNER JOIN usuarios ON rtcrusuoid = usuarios.cd_usuario
						WHERE
							rtcrdt_ultimo_envio IS NULL
							AND rtcrtipo_envio = 'Cron'
							AND rtcrarquivo_remessa LIKE '%rem_cancel%'
							AND rtcrdt_exclusao IS NULL
							AND rtcrdt_primeiro_envio BETWEEN '$dados->dt_ini_remessa 00:00:00' AND '$dados->dt_fim_remessa 23:59:59'
						";
					
		if($dados->ddl_status_remessa == 'TO') {
			$sql = $sqlProcessada . ' UNION ALL ' . $sqlAguardando . ' UNION ALL ' . $sqlCancelamento . $groupOrder;
		} else if($dados->ddl_status_remessa == 'AG') {
			$sql = $sqlAguardando . $groupOrder;
		} else if($dados->ddl_status_remessa == 'PA') {
			$sql = $sqlProcessada . ' UNION ALL ' . $sqlCancelamento . $groupOrder;
		}
		
		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Busca resultados da pesquisa de rejeitado
	 * @return [type] [description]
	 */
	public function pesquisaRejeitado($dados, $tipos, $paginacao = null) {
		$retorno = array();

		if($dados->ddl_forma_cobranca_rejeitado != 0) {
			$filtros .= ' AND titformacobranca = ' . $dados->ddl_forma_cobranca_rejeitado;
			$filtrosConsolidado .= ' AND titcformacobranca = ' . $dados->ddl_forma_cobranca_rejeitado;
		}

		if(isset($dados->txt_num_titulo_rejeitado) && !empty($dados->txt_num_titulo_rejeitado)) {
			$filtros .= ' AND titoid = ' . $dados->txt_num_titulo_rejeitado;
			$filtrosConsolidado .= ' AND titcoid = ' . $dados->txt_num_titulo_rejeitado;
		}

		if(isset($dados->txt_num_remessa) && !empty($dados->txt_num_remessa)) {
			$filtros .= ' AND rtcrnumero_remessa = ' . $dados->txt_num_remessa;
			$filtrosConsolidado .= ' AND rtcrnumero_remessa = ' . $dados->txt_num_remessa;
		}

		if(isset($dados->txt_nome_cliente_rejeitado) && !empty($dados->txt_nome_cliente_rejeitado)) {
			$filtros .= ' AND clinome ILIKE \'%' . $dados->txt_nome_cliente_rejeitado . '%\'';
			$filtrosConsolidado .= ' AND clinome ILIKE \'%' . $dados->txt_nome_cliente_rejeitado . '%\'';
		}

		if(isset($dados->dt_ini_rejeitado) && !empty($dados->dt_ini_rejeitado) &&
			isset($dados->dt_fim_rejeitado) && !empty($dados->dt_fim_rejeitado)) {
			$filtros .= " AND rtcrdt_primeiro_envio BETWEEN '$dados->dt_ini_rejeitado 00:00:00' AND '$dados->dt_fim_rejeitado 23:59:59'";
			$filtrosConsolidado .= " AND rtcrdt_primeiro_envio BETWEEN '$dados->dt_ini_rejeitado 00:00:00' AND '$dados->dt_fim_rejeitado 23:59:59'";
		}

		if (isset($paginacao->limite) && isset($paginacao->offset)) {
			$pagina = " 
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "
            ";
		}

		$sql = "SELECT
					'titulo' AS tipo,
					rtcroid AS id_remessa,
					cfbnome AS nome_banco,
			        rtcrnumero_remessa AS numero_remessa,
			        clinome AS nome_cliente,
			        titoid AS numero_titulo,           
			        rtcrdt_primeiro_envio AS data_cadastro,
			        array_agg(DISTINCT evticod_rejeicao) AS cod_retorno,  
					array_agg(DISTINCT (
						SELECT
							tet.tpetdescricao
	                   	FROM
	                   		tipo_evento_titulo tet
	                   	WHERE
	                   		tipo_evento_titulo.tpetcodigo = ANY(tet.tpetcodigo_detalhe) 
	                    	AND tet.tpetcodigo = evticod_rejeicao
	                    	AND tet.tpetcfbbanco = 33 
	                    	AND tet.tpetcob_registrada IS TRUE
	                )) AS msg_retorno
				FROM
	                titulo 
	                INNER JOIN tipo_evento_titulo ON tpetoid = tittpetoid
	                INNER JOIN clientes ON titclioid = clioid     
	                INNER JOIN remessa_titulo_cobr_reg ON titrtcroid = rtcroid
	                INNER JOIN evento_titulo ON evtititoid = titoid
                	INNER JOIN forma_cobranca ON forcoid = titformacobranca
                	INNER JOIN config_banco ON cfbbanco = forccfbbanco
				WHERE
					tittpetoid IN ($tipos)
				    AND rtcrdt_exclusao IS NULL
				    $filtros
				GROUP BY 
									id_remessa, 
									nome_banco, 
									numero_remessa, 
									nome_cliente, 
									numero_titulo
				UNION ALL
				SELECT
					'retencao' AS tipo,
					rtcroid AS id_remessa,
					cfbnome AS nome_banco,
			        rtcrnumero_remessa AS numero_remessa,
			        clinome AS nome_cliente,
			        titoid AS numero_titulo,           
			        rtcrdt_primeiro_envio AS data_cadastro,
			        array_agg(DISTINCT evticod_rejeicao) AS cod_retorno,  
					array_agg(DISTINCT (
						SELECT
							tet.tpetdescricao
	                   	FROM
	                   		tipo_evento_titulo tet
	                   	WHERE
	                   		tipo_evento_titulo.tpetcodigo = ANY(tet.tpetcodigo_detalhe) 
	                    	AND tet.tpetcodigo = evticod_rejeicao
	                    	AND tet.tpetcfbbanco = 33 
	                    	AND tet.tpetcob_registrada IS TRUE
	                )) AS msg_retorno
				FROM
	                titulo_retencao
	                INNER JOIN tipo_evento_titulo ON tpetoid = tittpetoid
	                INNER JOIN clientes ON titclioid = clioid     
	                INNER JOIN remessa_titulo_cobr_reg ON titrtcroid = rtcroid
	                INNER JOIN evento_titulo ON evtititoid = titoid
                	INNER JOIN forma_cobranca ON forcoid = titformacobranca
                	INNER JOIN config_banco ON cfbbanco = forccfbbanco
				WHERE
					tittpetoid IN ($tipos)
				    AND rtcrdt_exclusao IS NULL
				    $filtros
				GROUP BY 
									id_remessa, 
									nome_banco, 
									numero_remessa, 
									nome_cliente, 
									numero_titulo
				UNION ALL
				SELECT
					'consolidado' AS tipo,
					rtcroid AS id_remessa,
					cfbnome AS nome_banco,
			        rtcrnumero_remessa AS numero_remessa,
			        clinome AS nome_cliente,
			        titcoid AS numero_titulo,           
			        rtcrdt_primeiro_envio AS data_cadastro,
			        array_agg(DISTINCT evticod_rejeicao) AS cod_retorno,  
					array_agg(DISTINCT (
						SELECT
							tet.tpetdescricao
	                   	FROM
	                   		tipo_evento_titulo tet
	                   	WHERE
	                   		tipo_evento_titulo.tpetcodigo = ANY(tet.tpetcodigo_detalhe) 
	                    	AND tet.tpetcodigo = evticod_rejeicao
	                    	AND tet.tpetcfbbanco = 33 
	                    	AND tet.tpetcob_registrada IS TRUE
	                )) AS msg_retorno
				FROM
	                titulo_consolidado
	                INNER JOIN tipo_evento_titulo ON tpetoid = titctpetoid
	                INNER JOIN clientes ON titcclioid = clioid     
	                INNER JOIN remessa_titulo_cobr_reg ON titcrtcroid = rtcroid
	                INNER JOIN evento_titulo ON evtititoid = titcoid
                	INNER JOIN forma_cobranca ON forcoid = titcformacobranca
                	INNER JOIN config_banco ON cfbbanco = forccfbbanco
				WHERE
					titctpetoid IN ($tipos)
				    AND rtcrdt_exclusao IS NULL
				    $filtrosConsolidado
				GROUP BY 
									id_remessa, 
									nome_banco, 
									numero_remessa, 
									nome_cliente, 
									numero_titulo
				ORDER BY
	                nome_banco,
	                numero_remessa,
	                data_cadastro,
	                nome_cliente
				$pagina";
		

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Exclui remessa e desvincula títulos
	 * @return [type] [description]
	 */
	public function excluirRemessa($idRemessa) {

		$this->begin();

		$sql = "UPDATE
					remessa_titulo_cobr_reg
				SET
					rtcrdt_exclusao = NOW()
				WHERE
					rtcroid = $idRemessa";

		if(!pg_query($sql)) {
			$this->rollback();
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$sql = "UPDATE
					titulo
				SET
					titrtcroid = NULL, titnumero_registro_banco = NULL
				WHERE
					titrtcroid = $idRemessa";

		if(!pg_query($sql)) {
			$this->rollback();
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$sql = "UPDATE
					titulo_retencao
				SET
					titrtcroid = NULL, titnumero_registro_banco = NULL
				WHERE
					titrtcroid = $idRemessa";

		if(!pg_query($sql)) {
			$this->rollback();
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$sql = "UPDATE
					titulo_consolidado
				SET
					titcrtcroid = NULL, titcnumero_registro_banco = NULL
				WHERE
					titcrtcroid = $idRemessa";

		if(!pg_query($sql)) {
			$this->rollback();
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$this->commit();

		return;
	}

	
	
	public function getDadosParametro($descricao = NULL ,$tipo = NULL){
	
		if(empty($descricao)){
			throw new ErrorException('A descrição do parâmetro deve ser informada.');
		}
		
		if(empty($tipo)){
			throw new ErrorException('Infome o tipo do evento do título.');
		}
		
		 $sql = "SELECT
						pcsidescricao
					FROM
						parametros_configuracoes_sistemas
						INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
					WHERE
						pcsipcsoid = 'COBRANCA_REGISTRADA'
					AND
						pcsioid = '$descricao' ";
		 
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
			}
	
			while($registro = pg_fetch_object($rs)) {
				$retorno[] = $registro;
			}
	
			$tipos = $retorno[0]->pcsidescricao;
	
			$sql = "SELECT
						string_agg(tpetoid::TEXT, ',') AS tipos
					FROM
						tipo_evento_titulo
					WHERE
						tpetcfbbanco = 33 
						AND tpetcob_registrada IS TRUE
						AND tpettipo_evento = '$tipo'
						AND tpetcodigo IN ($tipos)";
			
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
			}
	
			while($registro = pg_fetch_object($rs)) {
				$retorno2[] = $registro;
			}

		return $retorno2;
		
	}
	
	
	/**
	 * Desvincula título rejeitado
	 * @return [type] [description]
	 */
	public function desvinculaRejeitado($idTitulo, $tipo) {
		
		$ids_tipo = $this->getDadosParametro('COD_MOVIMENTO_ENVIO_PARA_REGISTRO','Remessa');
		$ids_tipo = $ids_tipo[0]->tipos;
		
		switch($tipo) {
			case 'titulo':
				$sql = "UPDATE
							titulo
						SET
							titrtcroid = NULL,
							tittpetoid = $ids_tipo
						WHERE
							titoid = $idTitulo";
				break;
			case 'retencao':
				$sql = "UPDATE
							titulo_retencao
						SET
							titrtcroid = NULL,
							tittpetoid = $ids_tipo 
						WHERE
							titoid = $idTitulo";
				break;
			case 'consolidado':
				$sql = "UPDATE
							titulo_consolidado
						SET
							titcrtcroid = NULL,
							titctpetoid = $ids_tipo
						WHERE
							titcoid = $idTitulo";
				break;
		}


		if(!pg_query($sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
		
		//insere o evento do título (log)
		$sql_evento = " INSERT INTO evento_titulo (evtititoid,
		                                           evtitpetoid,
		                                           evtidt_geracao)
                                            VALUES($idTitulo,
		                                           $ids_tipo/*id da tabela tipo evento*/,
		                                           NOW()); ";
		
		if(!pg_query($sql_evento)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		return;
	}

	
	/**
	 * Retorna caminho da pasta onde serão salvos os arquivos
	 * @return [type] [description]
	 */
	public function retornaCaminhoPasta() {
		$sql = "SELECT
					pcsidescricao
				FROM
					parametros_configuracoes_sistemas
					INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
				WHERE
					pcsipcsoid = 'COBRANCA_REGISTRADA'
				        AND pcsioid = 'PASTA_ARQUIVO_REMESSA'";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Busca dados CSV remessa
	 * @return [type] [description]
	 */
	public function buscaDadosCSVRemessa($dados, $tiposPermitidos) {
		if($dados->ddl_forma_cobranca_remessa != 0) {
			
			$formaCobranca = $this->buscaFormasCobrancaArquivo();
			$formaCobranca = $formaCobranca[0]->pcsidescricao;
			
			$filtros .= ' AND titformacobranca IN('.$formaCobranca.')';
			$filtrosConsolidado .= ' AND titcformacobranca IN ('.$formaCobranca.')';
		}

		if(isset($dados->dt_ini_remessa) && !empty($dados->dt_ini_remessa) &&
			isset($dados->dt_fim_remessa) && !empty($dados->dt_fim_remessa)) {
			$filtros .= " AND titdt_vencimento BETWEEN '$dados->dt_ini_remessa 00:00:00' AND '$dados->dt_fim_remessa 23:59:59'";
			$filtrosConsolidado .= " AND titcdt_vencimento BETWEEN '$dados->dt_ini_remessa 00:00:00' AND '$dados->dt_fim_remessa 23:59:59'";
		}
		
		$tpetoidBaixaRegistro = $this->getStatusBaixaTituloSantander();

		$sql = "SELECT
                    forcnome AS forma_cobranca,
					tpetdescricao AS tipo_operacao,
					CASE 
                      WHEN cfbnome IS NULL THEN 'S/ BANCO'
                      ELSE cfbnome END AS nome_banco, 
					titoid AS numero_titulo,
					clinome AS nome_cliente,
					titdt_vencimento AS data_vencimento,
                    TO_CHAR(titdt_inclusao, 'YYYY-MM-DD') AS data_emissao,                    
					titvl_titulo AS valor
				FROM
					titulo
					INNER JOIN forma_cobranca ON forcoid = titformacobranca
					INNER JOIN tipo_evento_titulo ON tpetoid=tittpetoid
					LEFT JOIN config_banco ON cfbbanco = forccfbbanco
					INNER JOIN clientes ON clioid = titclioid
				WHERE
					titrtcroid IS NULL
					AND titdt_pagamento IS NULL
					AND titdt_vencimento::DATE >= NOW()::DATE
					
					--AND tittpetoid IN ($tiposPermitidos)

					AND tittpetoid IN (11,15,17,20,23)
                   
				 AND titdt_cancelamento IS NULL
					AND tpetcfbbanco = 33 
		            AND tpetcob_registrada IS TRUE
                	$filtros
                   -- OR (tittpetoid = $tpetoidBaixaRegistro --títulos que serão enviados para dar baixa no registro
		                --AND titrtcroid IS NULL -- sem remessa 
						--AND titdt_pagamento IS NULL )                	

				UNION ALL
				SELECT
					forcnome AS forma_cobranca,
					tpetdescricao AS tipo_operacao,
					CASE 
                      WHEN cfbnome IS NULL THEN 'S/ BANCO'
                      ELSE cfbnome END AS nome_banco, 
					titoid AS numero_titulo,
					clinome AS nome_cliente,
					titdt_vencimento AS data_vencimento,
                    TO_CHAR(titdt_inclusao, 'YYYY-MM-DD') AS data_emissao,
					titvl_titulo_retencao AS valor
				FROM
					titulo_retencao
					INNER JOIN forma_cobranca ON forcoid = titformacobranca
					INNER JOIN tipo_evento_titulo ON tpetoid=tittpetoid
					LEFT JOIN config_banco ON cfbbanco = forccfbbanco
					INNER JOIN clientes ON clioid = titclioid
				WHERE
					titrtcroid IS NULL
					AND titdt_pagamento IS NULL
					AND titdt_vencimento::DATE >= NOW()::DATE
					
					--AND tittpetoid IN ($tiposPermitidos)
					AND tittpetoid IN (11,15,17,20,23)
 					
					AND titdt_cancelamento IS NULL
					AND tpetcfbbanco = 33 
					AND tpetcob_registrada IS TRUE
					$filtros
					--OR (tittpetoid = $tpetoidBaixaRegistro --títulos que serão enviados para dar baixa no registro
		                --AND titrtcroid IS NULL -- sem remessa 
						--AND titdt_pagamento IS NULL )  
				UNION ALL
				SELECT
					forcnome AS forma_cobranca,
					tpetdescricao AS tipo_operacao,
					CASE 
                      WHEN cfbnome IS NULL THEN 'S/ BANCO'
                      ELSE cfbnome END AS nome_banco, 
					titcoid AS numero_titulo,
					clinome AS nome_cliente,
					titcdt_vencimento AS data_vencimento,
                    TO_CHAR(titcdt_inclusao, 'YYYY-MM-DD') AS data_emissao,
					titcvl_titulo AS valor
				FROM
					titulo_consolidado
					INNER JOIN forma_cobranca ON forcoid = titcformacobranca
					INNER JOIN tipo_evento_titulo ON tpetoid=titctpetoid
					LEFT JOIN config_banco ON cfbbanco = forccfbbanco
					INNER JOIN clientes ON clioid = titcclioid
				WHERE
					titcrtcroid IS NULL
					AND titcdt_pagamento IS NULL
					AND titcdt_vencimento::DATE >= NOW()::DATE
					
                    AND titctpetoid IN (11,15,17,20,23)
					--AND titctpetoid IN ($tiposPermitidos)

					AND titcdt_cancelamento IS NULL
					AND tpetcfbbanco = 33 
					AND tpetcob_registrada IS TRUE
           			$filtrosConsolidado
           			--OR (titctpetoid = $tpetoidBaixaRegistro --títulos que serão enviados para dar baixa no registro
		                --AND titcrtcroid IS NULL -- sem remessa 
						--AND titcdt_pagamento IS NULL )  
				GROUP BY nome_cliente,data_vencimento,nome_banco,valor, titcoid, forcnome,tpetdescricao	
				ORDER BY
					nome_cliente,
					nome_banco,
					valor";
           			
		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			//verifica o valor do título e se está no prazo estipulado pela febraban
			$prazosFebraban = Boleto::getPrazosFebraban($registro->valor, $registro->data_emissao);
			
			// e tiver dentro do prazo da Febraban manda os dados no arquivo para registro
			if($prazosFebraban->dados == 1){
				$retorno[] = $registro;
			}
		}

		return $retorno;
	}

	/**
	 * Busca dados header remessa
	 * @return [type] [description]
	 */
	public function buscaDadosHeaderRemessaSantander() {
		$sql = "SELECT
					'33' AS cod_banco,
					'0000' AS lote_servico,
					'0' AS tipo_registro,
					'2' AS tipo_inscricao_empresa,
					(
						SELECT
							teccnpj
						FROM
							tectran
						WHERE
							tecurl_sistema = 'PUBLIC'
					) AS inscricao_empresa,
					'210200008144958' AS cod_transmissao,
					(
						SELECT
							tecrazao
						FROM
							tectran
						WHERE
							tecurl_sistema = 'PUBLIC'
					) AS nome_empresa,
					'Banco Santander' AS nome_banco,
					'1' AS cod_remessa,
					TO_CHAR(NOW(), 'DDMMYYYY') AS data_geracao_arquivo,
					'' AS num_sequencial_arquivo,
					'040' AS versao_layout";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Busca dados header lote
	 * @return [type] [description]
	 */
	public function buscaDadosHeaderLoteSantander() {
		$sql = "SELECT
					'33' AS cod_banco,
					'0001' AS lote_servico,
					'1' AS tipo_registro,
					'R' AS tipo_operacao,
					'01' AS tipo_servico,
					'030' AS versao_layout_lote,
					'2' AS tipo_inscricao_empresa,
					(
						SELECT
							teccnpj
						FROM
							tectran
						WHERE
							tecurl_sistema = 'PUBLIC'
					) AS inscricao_empresa,
					'210200008144958' AS cod_transmissao,
					(
						SELECT
							tecrazao
						FROM
							tectran
						WHERE
							tecurl_sistema = 'PUBLIC'
					) AS nome_beneficiario,
					'' AS mensagem1,
					'' AS mensagem2,
					'' AS numero_remessa_retorno,
					TO_CHAR(NOW(), 'DDMMYYYY') AS data_gravacao";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Busca dados do trailer do lote
	 * @return [type] [description]
	 */
	public function buscaDadosTrailerLoteSantander() {
		$sql = "SELECT
					'33' AS cod_banco,
					'0001' AS numero_lote,
					'5' AS tipo_registro,
					'' AS quantidade_registros_lote";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Busca dados do trailer do arquivo
	 * @return [type] [description]
	 */
	public function buscaDadosTrailerArquivoSantander() {
		$sql = "SELECT
					'33' AS cod_banco,
					'9999' AS numero_lote,
					'9' AS tipo_registro,
					'1' AS quantidade_lotes,
					'' AS quantidade_registros_arquivo";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Busca dados do trailer do arquivo
	 * @return [type] [description]
	 */
	public function buscaTitulosRemessa($limite, $tiposPermitidos, $formaCobranca) {

		if(is_array($formaCobranca)){
			$formaCobranca = implode(',', $formaCobranca);
		}
		
		$tpetoidBaixaRegistro = $this->getStatusBaixaTituloSantander();
		
		$sqlTitulo = "SELECT
						'titulo' AS tipo,
						clioid AS id_cliente,
						'33' AS cod_banco,
						'0001' AS numero_lote,
						'3' AS tipo_registro_p,
						'' AS sequencial_detalhe_p,
						'P' AS cod_segmento_p,
						tpetcodigo AS cod_movimento_p,
						'2102' AS agencia_fidc,
						'' AS digito_agencia_fidc,
						'' AS numero_conta_corrente,
						'' AS digito_conta,
						'' AS conta_fidc,
						'' AS digito_conta_fidc,
						titnumero_registro_banco AS nosso_numero,
						'1' AS tipo_cobranca,
						'1' AS forma_cadastramento,
						'1' AS tipo_documento,
						titoid AS numero_documento,
						TO_CHAR(titdt_vencimento, 'DDMMYYYY') AS data_vencimento,
						titvl_titulo AS valor_nominal,
						'' AS agencia_cobranca,
						'' AS digito_agencia_beneficiario,
						'02' AS especie_titulo,
						'N' AS identificador_aceite,
						TO_CHAR(titdt_inclusao, 'DDMMYYYY') AS data_emissao,
						TO_CHAR(titdt_inclusao, 'YYYY-MM-DD') AS data_emissao_prazo,
						'2' AS cod_juros_mora,
						TO_CHAR(titdt_vencimento+1, 'DDMMYYYY') AS data_juros_mora,
						(
							SELECT
								pcsidescricao::NUMERIC
							FROM
								parametros_configuracoes_sistemas
								INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
							WHERE
								pcsipcsoid = 'COBRANCA_REGISTRADA'
							    AND pcsioid = 'PERCENTO_JUROS_AO_MES'
						) AS valor_mora,
						(
							CASE WHEN titvl_desconto IS NULL THEN
								0
							ELSE
								1
							END
						) AS cod_desconto1,
						(
							CASE WHEN titvl_desconto IS NULL THEN
								''
							ELSE
								TO_CHAR(titdt_vencimento, 'DDMMYYYY')
							END
						) AS data_desconto1,
						titvl_desconto AS valor_desconto1,
						'' AS valor_iof,
						'' AS valor_abatimento,
						titoid AS identificacao_titulo,
						'0' AS cod_protesto,
						'' AS numero_dias_protesto,
						'1' AS cod_baixa,
						(
							SELECT
								pcsidescricao
							FROM
								parametros_configuracoes_sistemas
								INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
							WHERE
								pcsipcsoid = 'COBRANCA_REGISTRADA'
								AND pcsioid = 'DIAS_BAIXA_DEVOLUCAO'
						) AS numero_dias_baixa,
						'00' AS cod_moeda,
						
						'3' AS tipo_registro_q,
						'' AS sequencial_detalhe_q,
						'Q' AS cod_segmento_q,
						tpetcodigo AS cod_movimento_q,
						(
							CASE WHEN clitipo = 'F' THEN
								1
							WHEN clitipo = 'J' THEN
								2
							ELSE
								NULL
							END
						) AS tipo_inscricao,
						(
							CASE WHEN clitipo = 'F' THEN
								clino_cpf
							WHEN clitipo = 'J' THEN
								clino_cgc
							ELSE
								NULL
							END
						) AS inscricao,
						clinome AS nome,
						endlogradouro AS endereco,
						endbairro AS bairro,
						endcep AS cep,
						'' AS sufixo_cep,
						endcidade AS cidade,
						enduf AS uf,
						'2' AS tipo_avalista,
						(
							SELECT
								teccnpj
							FROM
								tectran
							WHERE
								tecurl_sistema = 'PUBLIC'
						) AS inscricao_avalista,
						(
							SELECT
								tecrazao
							FROM
								tectran
							WHERE
								tecurl_sistema = 'PUBLIC'
						) AS nome_avalista,
						'000' AS identificador_carne,
						'' AS sequencial_parcela,
						'' AS total_parcelas,
						'' AS numero_plano,
						
						'3' AS tipo_registro_r,
						'' AS sequencial_detalhe_r,
						'R' AS cod_segmento_r,
						tpetcodigo AS cod_movimento_r,
						'0' AS cod_desconto2,
						'' AS data_desconto2,
						'' AS valor_desconto2,
						'2' AS cod_multa,
						'' AS data_multa,
						(
							SELECT
								pcsidescricao::NUMERIC
							FROM
								parametros_configuracoes_sistemas
								INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
							WHERE
								pcsipcsoid = 'COBRANCA_REGISTRADA'
								AND pcsioid = 'PERCENTO_MULTA_APOS_VENCER'
						) AS valor_multa,
						'' AS mensagem3,
						'' AS mensagem4,
						TO_CHAR(titdt_vencimento, 'YYYY-MM-DD') AS vencimento_classe_boleto
					FROM
						titulo
						INNER JOIN clientes ON titclioid=clioid
						INNER JOIN endereco ON cliend_cobr=endoid
						INNER JOIN tipo_evento_titulo ON tpetoid=tittpetoid
						LEFT JOIN nota_fiscal ON titnfloid = nfloid
					WHERE
						tpetcfbbanco = 33 
						AND tpetcob_registrada IS TRUE
						AND titrtcroid IS NULL
						AND titdt_pagamento IS NULL
						AND titdt_vencimento::DATE >= NOW()::DATE
						AND titformacobranca IN ($formaCobranca) 
                        AND tittpetoid IN (11,15,17,20,23)
 						AND titdt_cancelamento IS NULL ";

		$sqlRetencao = "SELECT
						'retencao' AS tipo,
						clioid AS id_cliente,
						'33' AS cod_banco,
						'0001' AS numero_lote,
						'3' AS tipo_registro_p,
						'' AS sequencial_detalhe_p,
						'P' AS cod_segmento_p,
						tpetcodigo AS cod_movimento_p,
						'2102' AS agencia_fidc,
						'' AS digito_agencia_fidc,
						'' AS numero_conta_corrente,
						'' AS digito_conta,
						'' AS conta_fidc,
						'' AS digito_conta_fidc,
						titnumero_registro_banco AS nosso_numero,
						'1' AS tipo_cobranca,
						'1' AS forma_cadastramento,
						'1' AS tipo_documento,
						titoid AS numero_documento,
						TO_CHAR(titdt_vencimento, 'DDMMYYYY') AS data_vencimento,
						titvl_titulo_retencao AS valor_nominal,
						'' AS agencia_cobranca,
						'' AS digito_agencia_beneficiario,
						'02' AS especie_titulo,
						'N' AS identificador_aceite,
						TO_CHAR(titdt_inclusao, 'DDMMYYYY') AS data_emissao,
                        TO_CHAR(titdt_inclusao, 'YYYY-MM-DD') AS data_emissao_prazo,
						'2' AS cod_juros_mora,
						TO_CHAR(titdt_vencimento+1, 'DDMMYYYY') AS data_juros_mora,
						(
							SELECT
								pcsidescricao::NUMERIC
							FROM
								parametros_configuracoes_sistemas
								INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
							WHERE
								pcsipcsoid = 'COBRANCA_REGISTRADA'
							    AND pcsioid = 'PERCENTO_JUROS_AO_MES'
						) AS valor_mora,
						(
							CASE WHEN titvl_desconto IS NULL THEN
								0
							ELSE
								1
							END
						) AS cod_desconto1,
						(
							CASE WHEN titvl_desconto IS NULL THEN
								''
							ELSE
								TO_CHAR(titdt_vencimento, 'DDMMYYYY')
							END
						) AS data_desconto1,
						titvl_desconto AS valor_desconto1,
						'' AS valor_iof,
						'' AS valor_abatimento,
						titoid AS identificacao_titulo,
						'0' AS cod_protesto,
						'' AS numero_dias_protesto,
						'1' AS cod_baixa,
						(
							SELECT
								pcsidescricao
							FROM
								parametros_configuracoes_sistemas
								INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
							WHERE
								pcsipcsoid = 'COBRANCA_REGISTRADA'
								AND pcsioid = 'DIAS_BAIXA_DEVOLUCAO'
						) AS numero_dias_baixa,
						'00' AS cod_moeda,
						
						'3' AS tipo_registro_q,
						'' AS sequencial_detalhe_q,
						'Q' AS cod_segmento_q,
						tpetcodigo AS cod_movimento_q,
						(
							CASE WHEN clitipo = 'F' THEN
								1
							WHEN clitipo = 'J' THEN
								2
							ELSE
								NULL
							END
						) AS tipo_inscricao,
						(
							CASE WHEN clitipo = 'F' THEN
								clino_cpf
							WHEN clitipo = 'J' THEN
								clino_cgc
							ELSE
								NULL
							END
						) AS inscricao,
						clinome AS nome,
						endlogradouro AS endereco,
						endbairro AS bairro,
						endcep AS cep,
						'' AS sufixo_cep,
						endcidade AS cidade,
						enduf AS uf,
						'2' AS tipo_avalista,
						(
							SELECT
								teccnpj
							FROM
								tectran
							WHERE
								tecurl_sistema = 'PUBLIC'
						) AS inscricao_avalista,
						(
							SELECT
								tecrazao
							FROM
								tectran
							WHERE
								tecurl_sistema = 'PUBLIC'
						) AS nome_avalista,
						'000' AS identificador_carne,
						'' AS sequencial_parcela,
						'' AS total_parcelas,
						'' AS numero_plano,
						
						'3' AS tipo_registro_r,
						'' AS sequencial_detalhe_r,
						'R' AS cod_segmento_r,
						tpetcodigo AS cod_movimento_r,
						'0' AS cod_desconto2,
						'' AS data_desconto2,
						'' AS valor_desconto2,
						'2' AS cod_multa,
						'' AS data_multa,
						(
							SELECT
								pcsidescricao::NUMERIC
							FROM
								parametros_configuracoes_sistemas
								INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
							WHERE
								pcsipcsoid = 'COBRANCA_REGISTRADA'
								AND pcsioid = 'PERCENTO_MULTA_APOS_VENCER'
						) AS valor_multa,
						'' AS mensagem3,
						'' AS mensagem4,
						TO_CHAR(titdt_vencimento, 'YYYY-MM-DD') AS vencimento_classe_boleto
					FROM
						titulo_retencao
						INNER JOIN clientes ON titclioid=clioid
						INNER JOIN endereco ON cliend_cobr=endoid
						INNER JOIN tipo_evento_titulo ON tpetoid=tittpetoid
						LEFT JOIN nota_fiscal ON titnfloid = nfloid
					WHERE
						tpetcfbbanco = 33 
						AND tpetcob_registrada IS TRUE
						AND titrtcroid IS NULL
						AND titdt_pagamento IS NULL
						AND titdt_vencimento::DATE >= NOW()::DATE
						AND titformacobranca IN ($formaCobranca) 
						AND tittpetoid IN (11,15,17,20,23)						
                        AND titdt_cancelamento IS NULL ";
		
		$sqlConsolidado = "SELECT
							'consolidado' AS tipo,
							clioid AS id_cliente,
							'33' AS cod_banco,
							'0001' AS numero_lote,
							'3' AS tipo_registro_p,
							'' AS sequencial_detalhe_p,
							'P' AS cod_segmento_p,
							tpetcodigo AS cod_movimento_p,
							'2102' AS agencia_fidc,
							'' AS digito_agencia_fidc,
							'' AS numero_conta_corrente,
							'' AS digito_conta,
							'' AS conta_fidc,
							'' AS digito_conta_fidc,
							titcnumero_registro_banco AS nosso_numero,
							'1' AS tipo_cobranca,
							'1' AS forma_cadastramento,
							'1' AS tipo_documento,
							titcoid AS numero_documento,
							TO_CHAR(titCdt_vencimento, 'DDMMYYYY') AS data_vencimento,
							titcvl_titulo AS valor_nominal,
							'' AS agencia_cobranca,
							'' AS digito_agencia_beneficiario,
							'02' AS especie_titulo,
							'N' AS identificador_aceite,
							TO_CHAR(titcdt_inclusao, 'DDMMYYYY') AS data_emissao,
							TO_CHAR(titcdt_inclusao, 'YYYY-MM-DD') AS data_emissao_prazo,
							'2' AS cod_juros_mora,
							TO_CHAR(titcdt_vencimento+1, 'DDMMYYYY') AS data_juros_mora,
							(
								SELECT
									pcsidescricao::NUMERIC
								FROM
									parametros_configuracoes_sistemas
									INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
								WHERE
									pcsipcsoid = 'COBRANCA_REGISTRADA'
								    AND pcsioid = 'PERCENTO_JUROS_AO_MES'
							) AS valor_mora,
							(
								CASE WHEN titcvl_desconto IS NULL THEN
									0
								ELSE
									1
								END
							) AS cod_desconto1,
							(
								CASE WHEN titcvl_desconto IS NULL THEN
									''
								ELSE
									TO_CHAR(titcdt_vencimento, 'DDMMYYYY')
								END
							) AS data_desconto1,
							titcvl_desconto AS valor_desconto1,
							'' AS valor_iof,
							'' AS valor_abatimento,
							titcoid AS identificacao_titulo,
							'0' AS cod_protesto,
							'' AS numero_dias_protesto,
							'1' AS cod_baixa,
							(
								SELECT
									pcsidescricao
								FROM
									parametros_configuracoes_sistemas
									INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
								WHERE
									pcsipcsoid = 'COBRANCA_REGISTRADA'
									AND pcsioid = 'DIAS_BAIXA_DEVOLUCAO'
							) AS numero_dias_baixa,
							'00' AS cod_moeda,

							'3' AS tipo_registro_q,
							'' AS sequencial_detalhe_q,
							'Q' AS cod_segmento_q,
							tpetcodigo AS cod_movimento_q,
							(
								CASE WHEN clitipo = 'F' THEN
									1
								WHEN clitipo = 'J' THEN
									2
								ELSE
									NULL
								END
							) AS tipo_inscricao,
							(
								CASE WHEN clitipo = 'F' THEN
									clino_cpf
								WHEN clitipo = 'J' THEN
									clino_cgc
								ELSE
									NULL
								END
							) AS inscricao,
							clinome AS nome,
							endlogradouro AS endereco,
							endbairro AS bairro,
							endcep AS cep,
							'' AS sufixo_cep,
							endcidade AS cidade,
							enduf AS uf,
							'2' AS tipo_avalista,
							(
								SELECT
									teccnpj
								FROM
									tectran
								WHERE
									tecurl_sistema = 'PUBLIC'
							) AS inscricao_avalista,
							(
								SELECT
									tecrazao
								FROM
									tectran
								WHERE
									tecurl_sistema = 'PUBLIC'
							) AS nome_avalista,
							'000' AS identificador_carne,
							'' AS sequencial_parcela,
							'' AS total_parcelas,
							'' AS numero_plano,

							'3' AS tipo_registro_r,
							'' AS sequencial_detalhe_r,
							'R' AS cod_segmento_r,
							tpetcodigo AS cod_movimento_r,
							'0' AS cod_desconto2,
							'' AS data_desconto2,
							'' AS valor_desconto2,
							'2' AS cod_multa,
							'' AS data_multa,
							(
								SELECT
									pcsidescricao::NUMERIC
								FROM
									parametros_configuracoes_sistemas
									INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
								WHERE
									pcsipcsoid = 'COBRANCA_REGISTRADA'
									AND pcsioid = 'PERCENTO_MULTA_APOS_VENCER'
							) AS valor_multa,
							'' AS mensagem3,
							'' AS mensagem4,
							TO_CHAR(titcdt_vencimento, 'YYYY-MM-DD') AS vencimento_classe_boleto
						FROM
							titulo_consolidado
							INNER JOIN clientes ON titcclioid=clioid
							INNER JOIN endereco ON cliend_cobr=endoid
							INNER JOIN tipo_evento_titulo ON tpetoid=titctpetoid 
						WHERE
							tpetcfbbanco = 33 
							AND tpetcob_registrada IS TRUE
							AND titcrtcroid IS NULL
							AND titcdt_pagamento IS NULL
							AND titcdt_vencimento::DATE >= NOW()::DATE
							AND titcformacobranca IN ($formaCobranca)     
							AND titctpetoid IN (11,15,17,20,23)
 							AND titcdt_cancelamento IS NULL  ";

		$sql = $sqlTitulo . ' UNION ALL ' . $sqlRetencao . ' UNION ALL ' . $sqlConsolidado . ' ORDER BY data_emissao_prazo DESC LIMIT ' . $limite;
			
		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
		
		while($registro = pg_fetch_object($rs)) {
			
			//verifica o valor do título e se está no prazo estipulado pela febraban
			$prazosFebraban = Boleto::getPrazosFebraban($registro->valor_nominal, $registro->data_emissao_prazo);
			
			// e tiver dentro do prazo da Febraban manda os dados no arquivo para registro
			if($prazosFebraban->dados == 1){
				$retorno[] = $registro;
			}
		}
					
		return $retorno;
	}

	/**
	 * Recuperar o ID do status que corresponde com o envio para dar baixa no registro do título no Santander
	 * 
	 * @throws ErrorException
	 * @return unknown|boolean
	 */
	private function getStatusBaixaTituloSantander(){
		
		$sql = "SELECT tpetoid
			      FROM tipo_evento_titulo
			     WHERE tpetcfbbanco = 33 
			       AND tpetcob_registrada IS TRUE
			       AND tpettipo_evento = 'Remessa'
			       AND tpetcodigo = 02";
		
		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
		
		if(pg_num_rows($rs) > 0){
			$tpetoid= pg_fetch_result( $rs,0,'tpetoid');
			
			return $tpetoid;
			
		}else{
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
	
		return false;
	}
	
	
	/**
	 * Insere remessa na tabela
	 * @return [type] [description]
	 */
	public function insereNossoNumero($titulo, $nosso_numero, $tipo) {

		switch($tipo) {
			case 'titulo':
				$sql = "UPDATE
							titulo
						SET
							titnumero_registro_banco = $nosso_numero
						WHERE
							titoid = $titulo";
				break;
			case 'retencao':
				$sql = "UPDATE
							titulo_retencao
						SET
							titnumero_registro_banco = $nosso_numero
						WHERE
							titoid = $titulo";
				break;
			case 'consolidado':
				$sql = "UPDATE
							titulo_consolidado
						SET
							titcnumero_registro_banco = $nosso_numero
						WHERE
							titcoid = $titulo";
				break;
		}
		
		if(!$res = pg_query($this->conn, $sql)) {
			throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		return;
	}

	/**
	 * Insere remessa na tabela e altera a forma de cobrança do título para 84 = Cobrança Registrada Samtamder
	 * @return [type] [description]
	 */
	public function insereRemessa($forma_cobranca, $cd_usuario, $banco, $id_titulos = null, $id_titulos_retencao = null, $id_titulos_consolidado = null) {
		
		$cd_usuario = (trim($cd_usuario) != '') ? trim($cd_usuario) : 2750;
		
		$sql = "INSERT INTO remessa_titulo_cobr_reg(
					rtcrnumero_remessa,
	                rtcrcfbbanco,
	                rtcrusuoid,
	                rtcrdt_primeiro_envio)
				VALUES(
					(SELECT
						COALESCE(MAX(rtcrnumero_remessa), 0) + 1 AS max_remessa
					 FROM
					 	remessa_titulo_cobr_reg
					 WHERE
					 	rtcrdt_exclusao IS NULL
					 	AND rtcrcfbbanco = $banco
					 ),
                     $banco,
                     $cd_usuario,
                     NOW()
                )
				RETURNING
					rtcroid,
					rtcrnumero_remessa";
		
		if(!$res = pg_query($this->conn, $sql)) {
			throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$numero_remessa = pg_fetch_result($res,0,'rtcrnumero_remessa');
        $rtcroid = pg_fetch_result($res,0,'rtcroid');

        if(!is_null($id_titulos) && strlen($id_titulos) > 0) {
    		
        	$sql = "UPDATE titulo 
					   SET titrtcroid = $rtcroid,
					       titformacobranca = subquery.forma_cobranca
					  FROM (
					       SELECT titoid,
					              CASE 
					                WHEN titformacobranca IN (1,73,74,63) THEN 84 -- se a forma de cobrança for: Boleto, Cobrança Registrada HSBC, Cobrança Registrada Itaú ou Titulo Avulso, altera para Santander
					                ELSE titformacobranca
					              END AS forma_cobranca
					         FROM titulo 
					        WHERE titoid IN ($id_titulos) 
					       ) AS subquery
					 WHERE titulo.titoid = subquery.titoid;";

			if(!$res = pg_query($this->conn, $sql)) {
				$this->rollback();
				throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
			}
		}

		if(!is_null($id_titulos_retencao) && strlen($id_titulos_retencao) > 0) {
			
			$sql = "UPDATE titulo_retencao 
					   SET titrtcroid = $rtcroid,
					       titformacobranca = subquery.forma_cobranca
					  FROM (
					        SELECT titoid,
					               CASE 
					                WHEN titformacobranca IN (1,73,74,63) THEN 84 -- se a forma de cobrança for: Boleto, Cobrança Registrada HSBC, Cobrança Registrada Itaú ou Titulo Avulso, altera para Santander
					                ELSE titformacobranca
					               END AS forma_cobranca
					         FROM titulo_retencao 
					        WHERE titoid IN ($id_titulos_retencao) 
					       ) AS subquery
					 WHERE titulo_retencao.titoid = subquery.titoid; ";

			if(!$res = pg_query($this->conn, $sql)) {
				throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
			}
		}

		if(!is_null($id_titulos_consolidado) && strlen($id_titulos_consolidado) > 0) {
			
			$sql = "UPDATE titulo_consolidado 
					   SET titcrtcroid = $rtcroid,
					       titcformacobranca = subquery.forma_cobranca
					  FROM (
						SELECT titcoid,
						       CASE 
							WHEN titcformacobranca IN (1,73,74,63) THEN 84 -- se a forma de cobrança for: Boleto, Cobrança Registrada HSBC, Cobrança Registrada Itaú ou Titulo Avulso, altera para Santander
							ELSE titcformacobranca
						       END AS forma_cobranca
						 FROM titulo_consolidado 
						WHERE titcoid IN ($id_titulos_consolidado) 
					       ) AS subquery
					 WHERE titulo_consolidado.titcoid = subquery.titcoid; ";
			
			if(!$res = pg_query($this->conn, $sql)) {
				$this->rollback();
				throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
			}
		}

		// if(!$res = pg_query($this->conn, $sql)) {
		// 	throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
		// }

		return array($numero_remessa, $rtcroid);
	}

	/**
	 * Insere remessa na tabela
	 * @return [type] [description]
	 */
	public function atualizaRemessaArquivo($arquivo, $rtcroid, $tipoenvio = '', $rtcrusuoid = '') {
		$sql = "UPDATE
					remessa_titulo_cobr_reg
				SET
					rtcrarquivo_remessa = '$arquivo',
					rtcrtipo_envio = '".$tipoenvio."',
					rtcrusuoid = '".$rtcrusuoid."'
				WHERE
					rtcroid = $rtcroid";

		if(!$res = pg_query($this->conn, $sql)) {
			throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}
	}

	/**
	 * Busca banco
	 * @return [type] [description]
	 */
	public function buscaBanco($forma_cobranca) {
		$sql = "SELECT
					cfbbanco,
					CASE WHEN forccfbbanco='341' THEN
						cfbagencia_convenio
					ELSE
						cfbagencia
					END AS cfbagencia,
                    CASE WHEN forccfbbanco='341' THEN
                    	cfbconta_corrente_convenio
                    ELSE
                    	cfbconta_corrente
                    END AS cfbconta_corrente
				FROM
					forma_cobranca
					INNER JOIN config_banco ON cfbbanco = forccfbbanco
				WHERE
					forcoid = $forma_cobranca";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	public function buscaTiposPermitidos() {
		$sql = "SELECT
					pcsidescricao
				FROM
					parametros_configuracoes_sistemas
					INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
				WHERE
					pcsipcsoid = 'COBRANCA_REGISTRADA'
				AND
					pcsioid = 'COD_MOVIMENTO_PERMITE_ATERACAO'";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		$tipos = $retorno[0]->pcsidescricao;

		$sql = "SELECT
					string_agg(tpetoid::TEXT, ',') AS tipos
				FROM
					tipo_evento_titulo
				WHERE
					tpetcfbbanco = 33 
					AND tpetcob_registrada IS TRUE
					AND tpettipo_evento = 'Remessa'
					AND tpetcodigo IN ($tipos)";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno2[] = $registro;
		}

		return $retorno2;
	}

	public function buscaTiposRejeitado() {
		$sql = "SELECT
					pcsidescricao
				FROM
					parametros_configuracoes_sistemas
					INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
				WHERE
					pcsipcsoid = 'COBRANCA_REGISTRADA'
				AND
					pcsioid = 'COD_MOVIMENTO_RETORNO_REJEITADO'";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		$tipos = $retorno[0]->pcsidescricao;

		$sql = "SELECT
					string_agg(tpetoid::TEXT, ',') AS tipos
				FROM
					tipo_evento_titulo
				WHERE
					tpetcfbbanco = 33 
					AND tpetcob_registrada IS TRUE
					AND tpettipo_evento = 'Retorno'
					AND tpetcodigo IN ($tipos)";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno2[] = $registro;
		}

		return $retorno2;
	}

	public function buscaFormasCobrancaArquivo() {
		$sql = "SELECT
					pcsidescricao
				FROM
					parametros_configuracoes_sistemas
					INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid
				WHERE
					pcsipcsoid = 'COBRANCA_REGISTRADA'
					AND pcsioid = 'FORMAS_COBRANCA_PARA_REGISTRO'";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	public function getNomeEmpresa() {

		$sql = "
			SELECT
				tecrazao
			FROM
				tectran
			WHERE
				tecurl_sistema = 'PUBLIC'";

		$result = pg_query($this->conn, $sql);

		if(pg_num_rows($result) > 0){
			$registro = pg_fetch_object($result, 0);
			return $registro->tecrazao;
		}

		return null;

	}

	public function getCnpjEmpresa(){

		$sql = "
			SELECT
				teccnpj
			FROM
				tectran
			WHERE
				tecurl_sistema = 'PUBLIC'";

		$result = pg_query($this->conn, $sql);

		if(pg_num_rows($result) > 0){
			$registro = pg_fetch_object($result, 0);
			return $registro->teccnpj;
		}

		return null;

	}


	/**
	 * Abre a transação
	 */
	public function begin(){
		pg_query($this->conn, 'BEGIN');
	}

	/**
	 * Finaliza um transação
	 */
	public function commit(){
		pg_query($this->conn, 'COMMIT');
	}

	/**
	 * Aborta uma transação
	 */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK');
	}


}
?>

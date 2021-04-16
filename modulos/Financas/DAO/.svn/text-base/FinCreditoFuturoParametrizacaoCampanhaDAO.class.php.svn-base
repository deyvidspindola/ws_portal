<?php

/**
 * Classe de persistência de dados
 *
 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
 *
 */
class FinCreditoFuturoParametrizacaoCampanhaDao {

    private $conn;
    private $parametros;

    function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Lista de tipos de campanha
     * @return array:StdClass
     */
    public function pesquisarTiposCampanha() {
        $sql = "SELECT
				    cftpoid,
				    cftpdescricao
				FROM
				    credito_futuro_tipo_campanha
				WHERE
				    cftpdt_exclusao IS NULL
				ORDER BY
				    cftpdescricao";

        $results = $this->query($sql);
        if ($this->count($results) > 0) {
            return $this->fetchAllObject($results);
        }
        return array();
    }

    /**
     * Lista de motivos de credito
     * @return array:StdClass
     */
    public function pesquisarMotivosCredito() {
        $sql = "SELECT	
					cfmcoid,
					cfmcdescricao
				FROM
					credito_futuro_motivo_credito
               WHERE                     
                    cfmctipo in (2,4,5)
					AND cfmcdt_exclusao IS NULL
				ORDER BY
					cfmcdescricao";

        $results = $this->query($sql);
        if ($this->count($results) > 0) {
            return $this->fetchAllObject($results);
        }
        return array();
    }

    /**
     * Resultado para tela de pesquisa.
     * @param  stdClass $filtros
     * @return array:StdClass
     */
    public function pesquisar(stdClass $filtros) {

        $condicoes = array("cfcpdt_exclusao IS NULL AND (
									(c.cfcpdt_inicio_vigencia between '" . $filtros->cfcpdt_inicio_vigencia . "' and '" . $filtros->cfcpdt_fim_vigencia . "')
								OR 	(c.cfcpdt_fim_vigencia    between '" . $filtros->cfcpdt_inicio_vigencia . "' and '" . $filtros->cfcpdt_fim_vigencia . "')
							   )");

        if (isset($filtros->cfcpcftpoid) && !empty($filtros->cfcpcftpoid)) {
            $condicoes[] = "AND (c.cfcpcftpoid=" . intval($filtros->cfcpcftpoid) . ")";
        }
        if (isset($filtros->cfcpcfmccoid) && !empty($filtros->cfcpcfmccoid)) {
            $condicoes[] = "AND (c.cfcpcfmccoid=" . intval($filtros->cfcpcfmccoid) . ")";
        }
        if (isset($filtros->cfcptipo_desconto) && !empty($filtros->cfcptipo_desconto)) {
            $condicoes[] = "AND (c.cfcptipo_desconto='" . pg_escape_string($filtros->cfcptipo_desconto) . "')";
            if ($filtros->cfcptipo_desconto == 'P' && isset($filtros->cfcpdesconto_percentual_de) && isset($filtros->cfcpdesconto_percentual_ate)) {
                $condicoes[] = "AND (c.cfcpdesconto >= " . $filtros->cfcpdesconto_percentual_de . " AND c.cfcpdesconto <= " . $filtros->cfcpdesconto_percentual_ate . ")";
            } else if ($filtros->cfcptipo_desconto == 'V' && isset($filtros->cfcpdesconto_valor_de) && isset($filtros->cfcpdesconto_valor_ate)) {
                $condicoes[] = "AND (c.cfcpdesconto >= " . $filtros->cfcpdesconto_valor_de . " AND c.cfcpdesconto <= " . $filtros->cfcpdesconto_valor_ate . ")";
            }
        }

        if (isset($filtros->cfcpaplicacao) && !empty($filtros->cfcpaplicacao)) {
            $condicoes[] = "AND (c.cfcpaplicacao='" . pg_escape_string($filtros->cfcpaplicacao) . "')";
        }


        $sql = "SELECT 
					c.cfcpoid, 
					TO_CHAR(c.cfcpdt_inicio_vigencia,'DD/MM/YYYY') AS cfcpdt_inicio_vigencia,
					TO_CHAR(c.cfcpdt_fim_vigencia,'DD/MM/YYYY')    AS cfcpdt_fim_vigencia,
					c.cfcpcftpoid, 
					c.cfcpcfmccoid, 
					c.cfcpdesconto, 
					c.cfcptipo_desconto, 
					c.cfcpaplicacao, 
					c.cfcpqtde_parcelas, 
					c.cfcpobroid, 
					c.cfcpobservacao, 
					c.cfcpusuoid_exclusao, 
					c.cfcpdt_exclusao, 
					c.cfcpaplicar_sobre, 
					c.cfcpdt_inclusao, 
					c.cfcpusuoid_inclusao, 
					tc.cftpdescricao, 
					tc.cftpoid, 
					mc.cfmcdescricao, 
					mc.cfmcoid, 
					u.nm_usuario, 
					u.cd_usuario, 
					u.usuemail, 
					o.obrobrigacao, 
					o.obroid
				FROM 
					credito_futuro_campanha_promocional c
				JOIN 
					credito_futuro_tipo_campanha tc ON  tc.cftpoid = c.cfcpcftpoid
				JOIN 
					credito_futuro_motivo_credito mc ON  mc.cfmcoid = c.cfcpcfmccoid 
				JOIN 
					usuarios u ON u.cd_usuario = c.cfcpusuoid_inclusao
				LEFT JOIN 
					obrigacao_financeira o ON c.cfcpobroid = o.obroid
				" . ((count($condicoes) > 0) ? "WHERE " . implode(" ", $condicoes) : "") . "
				ORDER BY 
					c.cfcpdt_inicio_vigencia";

        $results = $this->query($sql);
        if ($this->count($results) > 0) {
            return $this->fetchAllObject($results);
        }
        return array();
    }

    public function pesquisarHistorico($idCampanha) {
        $sql = "SELECT
					cfchoid,
					cfchcfcpoid,
					cfchobservacao,
					TO_CHAR(cfchdt_registro,'DD/MM/YYYY HH24:mi:ss') AS cfchdt_registro,
					cfchusuoid,
					nm_usuario
				FROM
					credito_futuro_campanha_historico
				LEFT JOIN 
					usuarios ON cd_usuario = cfchusuoid
				WHERE cfchcfcpoid=" . intval($idCampanha) . "
				ORDER BY cfchcfcpoid,cfchoid DESC";

        $results = $this->query($sql);
        if ($this->count($results) > 0) {
            return $this->fetchAllObject($results);
        }
        return array();
    }

    /**
     * Recupera registro da Campanha.
     * @param  (int) $idCampanha
     * @return stdClass
     */
    public function pesquisarCampanha($idCampanha) {

        $sql = "SELECT
					c.cfcpoid,
					TO_CHAR(c.cfcpdt_inicio_vigencia,'DD/MM/YYYY') AS cfcpdt_inicio_vigencia,
					TO_CHAR(c.cfcpdt_fim_vigencia,'DD/MM/YYYY')    AS cfcpdt_fim_vigencia,
					c.cfcpcftpoid,
					c.cfcpcfmccoid,
					c.cfcpdesconto,
					c.cfcptipo_desconto,
					c.cfcpaplicacao,
					c.cfcpqtde_parcelas,
					c.cfcpobroid,
					c.cfcpobservacao,
					c.cfcpusuoid_exclusao,
					c.cfcpdt_exclusao,
					c.cfcpaplicar_sobre,
					c.cfcpdt_inclusao,
					c.cfcpusuoid_inclusao,
					tc.cftpdescricao,
					tc.cftpoid,
					mc.cfmcdescricao,
					mc.cfmcoid,
					u.nm_usuario,
					u.cd_usuario,
					u.usuemail,
					o.obrobrigacao,
					o.obroid
				FROM
					credito_futuro_campanha_promocional c
				JOIN
					credito_futuro_tipo_campanha tc ON  tc.cftpoid = c.cfcpcftpoid
				JOIN
					credito_futuro_motivo_credito mc ON  mc.cfmcoid = c.cfcpcfmccoid
				JOIN
					usuarios u ON u.cd_usuario = c.cfcpusuoid_inclusao
				LEFT JOIN
					obrigacao_financeira o ON c.cfcpobroid = o.obroid
				WHERE cfcpoid=" . intval($idCampanha) . "";

        $results = $this->query($sql);
        if ($this->count($results) > 0) {
            return $this->fetchObject($results);
        }
        return null;
    }

    /**
     * Exclui registro da Campanha.
     * @param  (int) $idCampanha
     * @return stdClass
     */
    public function excluirCampanha(stdClass $filtro) {
        $sql = "UPDATE
					credito_futuro_campanha_promocional 
				SET 
					cfcpdt_exclusao = NOW(),
					cfcpusuoid_exclusao = " . intval($filtro->usuario) . "				
				WHERE cfcpoid=" . intval($filtro->cfcpoid) . "";

        $results = $this->query($sql);
        if (pg_affected_rows($results) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Método que verifica a relacionamento de uma campanha
     *
     * @param String $parametros
     *
     * @return Boolean
     * @throws ErrorException
     */
    public function verificarUsoCampanhaPromocional($parametros) {

        $sql = "SELECT
                        cfocfcpoid
                    FROM
                        credito_futuro  
                    WHERE
                        cfocfcpoid = " . intval($parametros->cfcpoid) . "";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Salva novo registro, e apaga o registro anterior
     * @param stdClass $registroNovo
     * @throws Exception
     * @return boolean
     */
    public function salvar(stdClass $registro) {
        try {
            $this->begin();

            $this->parametros->registro->editar = (isset($registro->cfcpoid) && $registro->cfcpoid > 0) ? true : false;
            $this->parametros->registro->cfcpoid = (isset($registro->cfcpoid) && $registro->cfcpoid > 0) ? intval($registro->cfcpoid) : null;
            $this->parametros->registro->cfcpdt_inicio_vigencia = isset($registro->cfcpdt_inicio_vigencia) ? $registro->cfcpdt_inicio_vigencia : null;
            $this->parametros->registro->cfcpdt_fim_vigencia = isset($registro->cfcpdt_fim_vigencia) ? $registro->cfcpdt_fim_vigencia : null;
            $this->parametros->registro->cfcpcftpoid = isset($registro->cfcpcftpoid) ? $registro->cfcpcftpoid : null;
            $this->parametros->registro->cfcpcfmccoid = isset($registro->cfcpcfmccoid) ? $registro->cfcpcfmccoid : null;
            $this->parametros->registro->cfcptipo_desconto = isset($registro->cfcptipo_desconto) ? $registro->cfcptipo_desconto : null;
            $this->parametros->registro->cfcpdesconto = isset($registro->cfcpdesconto) ? $registro->cfcpdesconto : null;
            $this->parametros->registro->cfcpaplicacao = isset($registro->cfcpaplicacao) ? $registro->cfcpaplicacao : null;
            $this->parametros->registro->cfcpqtde_parcelas = isset($registro->cfcpqtde_parcelas) ? $registro->cfcpqtde_parcelas : null;
            $this->parametros->registro->cfcpobroid = isset($registro->cfcpobroid) ? $registro->cfcpobroid : null;
            $this->parametros->registro->cfcpobservacao = isset($registro->cfcpobservacao) ? $registro->cfcpobservacao : null;
            $this->parametros->registro->cfcpusuoid_inclusao = isset($registro->usuario) ? $registro->usuario : null;
            $this->parametros->registro->cfcpaplicar_sobre = isset($registro->cfcpaplicar_sobre) ? $registro->cfcpaplicar_sobre : null;

            if (empty($this->parametros->registro->cfcpdt_inicio_vigencia) ||
                    empty($this->parametros->registro->cfcpdt_fim_vigencia) ||
                    empty($this->parametros->registro->cfcpcftpoid) ||
                    empty($this->parametros->registro->cfcpcfmccoid) ||
                    empty($this->parametros->registro->cfcptipo_desconto) ||
                    empty($this->parametros->registro->cfcpdesconto) ||
                    empty($this->parametros->registro->cfcpaplicacao) ||
                    empty($this->parametros->registro->cfcpobroid) ||
                    empty($this->parametros->registro->cfcpaplicar_sobre) ||
                    empty($this->parametros->registro->cfcpusuoid_inclusao)) {

                throw new Exception('Existem campos obrigatórios não preenchidos.');
            }

            $this->parametros->registroAnterior = null;
            if ($this->parametros->registro->editar) {
                $this->parametros->registroAnterior = $this->pesquisarCampanha($this->parametros->registro->cfcpoid);
            }

            $id = $this->gravaParametro($this->parametros->registro);
            if (!$id) {
                throw new Exception('Houve um erro no processamento dos dados.');
            }

            $this->parametros->registro->cfcpoid = $id;
            $this->parametros->registroAtual = $this->pesquisarCampanha($id);

            $this->gravaHistorico();

            $this->commit();

            return $this->parametros->registro;
        } catch (Exception $e) {

            $this->rollback();
            throw new Exception($e->getMessage());
            return false;
        }
    }

    private function gravaHistorico() {
        $sql = "INSERT INTO 
						credito_futuro_campanha_historico
						(
            				cfchcfcpoid, 
            				cfchobservacao, 
            				cfchdt_registro, 
            				cfchusuoid
            			)
    			VALUES (
    						" . intval($this->parametros->registro->cfcpoid) . ", 
    						'" . pg_escape_string($this->textoObservacaoHistorico()) . "', 
    						NOW(), 
    						" . intval($this->parametros->registro->cfcpusuoid_inclusao) . "
    					)
    			RETURNING cfchoid";

        $resultado = $this->query($sql);
        $obj = $this->fetchObject($resultado);
        return $obj->cfchoid;
    }

    private function textoObservacaoHistorico() {
        $arrText = array();

        if ($this->parametros->registro->editar) {
            if (($this->parametros->registroAnterior->cfcpdt_inicio_vigencia != $this->parametros->registroAtual->cfcpdt_inicio_vigencia) || ($this->parametros->registroAnterior->cfcpdt_fim_vigencia != $this->parametros->registroAtual->cfcpdt_fim_vigencia)) {
                $arrText[] = "<b>Período de Vigência de: </b> " . $this->parametros->registroAnterior->cfcpdt_inicio_vigencia . " a " . $this->parametros->registroAnterior->cfcpdt_fim_vigencia . "  
							<b>para: </b> " . $this->parametros->registroAtual->cfcpdt_inicio_vigencia . " a " . $this->parametros->registroAtual->cfcpdt_fim_vigencia . "";
            }
            if ($this->parametros->registroAnterior->cfcpcftpoid != $this->parametros->registroAtual->cfcpcftpoid) {
                $arrText[] = "<b>Tipo de Campanha Promocional de:</b>  " . $this->parametros->registroAnterior->cftpdescricao . "  
							<b>para: </b>  " . $this->parametros->registroAtual->cftpdescricao . " ";
            }
            if ($this->parametros->registroAnterior->cfcpcfmccoid != $this->parametros->registroAtual->cfcpcfmccoid) {
                $arrText[] = "<b>Motivo do Crédito de:</b>  " . $this->parametros->registroAnterior->cfmcdescricao . "  
							<b>para: </b>  " . $this->parametros->registroAtual->cfmcdescricao . " ";
            }
            if ($this->parametros->registroAnterior->cfcptipo_desconto != $this->parametros->registroAtual->cfcptipo_desconto) {
                $arrText[] = "<b>Tipo do Desconto de:</b>  " . ($this->parametros->registroAnterior->cfcptipo_desconto == "V" ? " Valor " : " Percentual ") . " 
							<b>para: </b> " . ($this->parametros->registroAtual->cfcptipo_desconto == "V" ? " Valor " : " Percentual ") . " ";
            }
            if ($this->parametros->registroAnterior->cfcpdesconto != $this->parametros->registroAtual->cfcpdesconto) {

                $arrText[] = "<b>Desconto de:</b>  " . ($this->parametros->registroAnterior->cfcptipo_desconto == "V" ? " R$" . number_format($this->parametros->registroAnterior->cfcpdesconto, 2, ",", ".") : number_format($this->parametros->registroAnterior->cfcpdesconto, 2, ",", ".") . "% ") . "
							<b>para: </b> " . ($this->parametros->registroAtual->cfcptipo_desconto == "V" ? " R$" . number_format($this->parametros->registroAtual->cfcpdesconto, 2, ",", ".") : number_format($this->parametros->registroAtual->cfcpdesconto, 2, ",", ".") . "% ");
            }
            if ($this->parametros->registroAnterior->cfcpaplicacao != $this->parametros->registroAtual->cfcpaplicacao) {
                $arrText[] = "<b>Forma de Aplicação de:</b>  " . ($this->parametros->registroAnterior->cfcpaplicacao == "I" ? " Integral " : " Parcela ") . " 
							<b>para: </b> " . ($this->parametros->registroAtual->cfcpaplicacao == "I" ? " Integral " : " Parcela ") . " ";
            }
            if ($this->parametros->registroAnterior->cfcpqtde_parcelas != $this->parametros->registroAtual->cfcpqtde_parcelas) {
                $arrText[] = "<b>Parcela de:</b>  " . number_format($this->parametros->registroAnterior->cfcpqtde_parcelas, 0, ",", ".") . "
							<b>para: </b> " . number_format($this->parametros->registroAtual->cfcpqtde_parcelas, 0, ",", ".");
            }
            if ($this->parametros->registroAnterior->cfcpaplicar_sobre != $this->parametros->registroAtual->cfcpaplicar_sobre) {
                $arrText[] = "<b>Aplicar o desconto sobre o valor total de:</b>  " . ($this->parametros->registroAnterior->cfcpaplicar_sobre == "M" ? " Monitoramento " : " Locação ") . " 
							<b>para: </b> " . ($this->parametros->registroAtual->cfcpaplicar_sobre == "M" ? " Monitoramento " : " Locação ") . " ";
            }
            if ($this->parametros->registroAnterior->cfcpobroid != $this->parametros->registroAtual->cfcpobroid) {
                $arrText[] = "<b>Obrigação Financeira de Desconto de:</b> " . $this->parametros->registroAnterior->cfcpobroid . ' - ' . $this->parametros->registroAnterior->obrobrigacao . "  
							<b>para: </b>  " . $this->parametros->registroAtual->cfcpobroid . ' - ' . $this->parametros->registroAtual->obrobrigacao . " ";
            }
            if ($this->parametros->registroAnterior->cfcpobservacao != $this->parametros->registroAtual->cfcpobservacao) {
                $arrText[] = "<b>Observação de:</b>  " . (trim($this->parametros->registroAnterior->cfcpobservacao) == "" ? '-' : wordwrap(trim($this->parametros->registroAnterior->cfcpobservacao), 60, "<br />", true)) . "
						   <b>para: </b> " . (trim($this->parametros->registroAtual->cfcpobservacao) == "" ? '-' : wordwrap(trim($this->parametros->registroAtual->cfcpobservacao), 60, "<br />", true)) . "";
            }
        } else {

            $arrText[] = "<b>Período de Vigência: </b>" . $this->parametros->registroAtual->cfcpdt_inicio_vigencia . " a " . $this->parametros->registroAtual->cfcpdt_fim_vigencia . "";

            $arrText[] = "<b>Tipo de Campanha Promocional:</b> " . $this->parametros->registroAtual->cftpdescricao . " ";

            $arrText[] = "<b>Motivo do Crédito:</b> " . $this->parametros->registroAtual->cfmcdescricao . " ";

            $arrText[] = "<b>Tipo do Desconto:</b> " . ($this->parametros->registroAtual->cfcptipo_desconto == "V" ? " Valor " : " Percentual ") . " ";

            $arrText[] = "<b>Desconto:</b> " . ($this->parametros->registroAtual->cfcptipo_desconto == "V" ? " R$" . number_format($this->parametros->registroAtual->cfcpdesconto, 2, ",", ".") : number_format($this->parametros->registroAtual->cfcpdesconto, 2, ",", ".") . "% ");

            $arrText[] = "<b>Forma de Aplicação:</b> " . ($this->parametros->registroAtual->cfcpaplicacao == "I" ? " Integral " : " Parcela ") . " ";

            $arrText[] = "<b>Parcela:</b> " . number_format($this->parametros->registroAtual->cfcpqtde_parcelas, 0, ",", ".");

            $arrText[] = "<b>Aplicar o desconto sobre o valor total de:</b> " . ($this->parametros->registroAtual->cfcpaplicar_sobre == "M" ? " Monitoramento " : " Locação ") . " ";

            $arrText[] = "<b>Obrigação Financeira de Desconto de:</b> " . $this->parametros->registroAtual->cfcpobroid . ' - ' . $this->parametros->registroAtual->obrobrigacao . " ";

            if (trim($this->parametros->registroAtual->cfcpobservacao) == "") {
                $arrText[] = "<b>Observação:</b> -";
            } else {
                $arrText[] = "<b>Observação:</b> " . wordwrap(trim($this->parametros->registroAtual->cfcpobservacao), 60, "<br />", true) . "";
            }
        }

        return implode(" <br />", $arrText);
    }

    /**
     * Incluir novo parametro, usado na funcao salvar
     * @param stdClass $parametros - (int) cd_usuario
     * @return boolean
     */
    private function gravaParametro(stdClass $registro) {
        if (!isset($registro->cfcpoid) || intval($registro->cfcpoid) == 0) {
            $sql = "INSERT INTO 
						credito_futuro_campanha_promocional
						(
	            			cfcpdt_inicio_vigencia, 
							cfcpdt_fim_vigencia, 
							cfcpcftpoid,
							cfcpcfmccoid, 
							cfcptipo_desconto, 
							cfcpdesconto, 
							cfcpaplicacao, 
	            			cfcpqtde_parcelas, 
							cfcpobroid, 
							cfcpobservacao, 
							cfcpaplicar_sobre, 
							cfcpdt_inclusao, 
							cfcpusuoid_inclusao
					)
				    VALUES 
					(
							'" . $registro->cfcpdt_inicio_vigencia . "', 
							'" . $registro->cfcpdt_fim_vigencia . "', 
							" . intval($registro->cfcpcftpoid) . ",
							" . intval($registro->cfcpcfmccoid) . ",
							'" . pg_escape_string($registro->cfcptipo_desconto) . "', 
							" . $registro->cfcpdesconto . ",
							'" . pg_escape_string($registro->cfcpaplicacao) . "',
	            			" . intval($registro->cfcpqtde_parcelas) . ", 
							" . intval($registro->cfcpobroid) . ",
							'" . pg_escape_string($registro->cfcpobservacao) . "', 
							'" . pg_escape_string($registro->cfcpaplicar_sobre) . "', 
							NOW(), 
							" . intval($registro->cfcpusuoid_inclusao) . "
					)
	               	RETURNING cfcpoid";
        } else {
            $sql = "UPDATE 
						credito_futuro_campanha_promocional
					SET 
						cfcpdt_inicio_vigencia='" . $registro->cfcpdt_inicio_vigencia . "', 
						cfcpdt_fim_vigencia='" . $registro->cfcpdt_fim_vigencia . "', 
						cfcpcftpoid=" . intval($registro->cfcpcftpoid) . ",
						cfcpcfmccoid=" . intval($registro->cfcpcfmccoid) . ", 
						cfcptipo_desconto='" . pg_escape_string($registro->cfcptipo_desconto) . "', 
						cfcpdesconto=" . $registro->cfcpdesconto . ", 
						cfcpaplicacao='" . pg_escape_string($registro->cfcpaplicacao) . "',
						cfcpqtde_parcelas=" . intval($registro->cfcpqtde_parcelas) . ", 
						cfcpobroid=" . intval($registro->cfcpobroid) . ", 
						cfcpobservacao='" . pg_escape_string($registro->cfcpobservacao) . "',
						cfcpaplicar_sobre='" . pg_escape_string($registro->cfcpaplicar_sobre) . "'
					WHERE cfcpoid=" . intval($registro->cfcpoid) . "
					RETURNING cfcpoid ";
        }

        $resultado = $this->query($sql);
        $obj = $this->fetchObject($resultado);
        return $obj->cfcpoid;
    }

    /**
     * Pesquisa as obrigacoes financeiras do grupo 'Desconto'
     * @return array:object
     */
    public function pesquisarListaObrigacaoFinanceira() {
        $resultadoPesquisa = array();
        $sql = "SELECT
					obroid,
					obrobrigacao
				FROM
					obrigacao_financeira
				JOIN
					obrigacao_financeira_grupo
						ON ofgoid = obrofgoid
				WHERE
					obrdt_exclusao IS NULL
				AND
					ofgdescricao ILIKE '%Desconto%'
				ORDER BY
					obrobrigacao";

        if ($resultado = $this->query($sql)) {
            if ($this->count($resultado) > 0) {
                $resultadoPesquisa = $this->fetchAllObject($resultado);
            }
        }
        return $resultadoPesquisa;
    }

    /**
     * Verifica se existe cadastro de parametrizacao de email.
     * @return boolean
     */
    public function existeParametroEmail() {

        $sql = "SELECT
    					cfeaoid,
						cfeavalor_credito_futuro,
						cfeavalor_percentual_desconto,
						cfeaparcelas,
						cfeaobroid_contestacao,
						cfeaobroid_contas,
						cfeaobroid_campanha,
						cfeacabecalho,
						cfeacorpo
    			FROM
    					credito_futuro_email_aprovacao
    			WHERE
                        cfeadt_exclusao IS NULL
    			LIMIT 1";

        $results = $this->query($sql);
        if ($this->count($results) > 0) {
            return $this->fetchObject($results);
        }
        return false;
    }

    /**
     * Verifica se existe cadastro de campanha no mesmo periodo ativa.
     * @return boolean
     */
    public function existeTipoCampanhaPeriodo(stdClass $parametros) {

        $sql = "SELECT
					cfcpoid
				FROM 
					credito_futuro_campanha_promocional
				WHERE 
				((
					'" . $parametros->cfcpdt_inicio_vigencia . "'::timestamp BETWEEN (to_char(cfcpdt_inicio_vigencia, 'YYYY-MM-DD')||' 00:00:00')::timestamp AND (to_char(cfcpdt_fim_vigencia, 'YYYY-MM-DD')||' 23:59:59')::timestamp
				OR 
					'" . $parametros->cfcpdt_fim_vigencia . "'::timestamp BETWEEN (to_char(cfcpdt_inicio_vigencia, 'YYYY-MM-DD')||' 00:00:00')::timestamp AND (to_char(cfcpdt_fim_vigencia, 'YYYY-MM-DD')||' 23:59:59')::timestamp
				)
				OR
				(
					'" . $parametros->cfcpdt_inicio_vigencia . "'::timestamp < (to_char(cfcpdt_inicio_vigencia, 'YYYY-MM-DD')||' 00:00:00')::timestamp 
				AND 
					'" . $parametros->cfcpdt_fim_vigencia . "'::timestamp > (to_char(cfcpdt_inicio_vigencia, 'YYYY-MM-DD')||' 00:00:00')::timestamp 
				))
				AND cfcpcftpoid=" . intval($parametros->cfcpcftpoid) . "
				AND cfcpoid!=" . intval($parametros->cfcpoid) . "
				AND cfcpdt_exclusao IS NULL";

        $results = $this->query($sql);
        if ($this->count($results) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Executa uma query
     * @param    string        $sql        SQL a ser executado
     * @return    resource
     */
    private function query($sql) {
        // Suprime erros para lançar exceção ao invés de E_WARNING
        $result = pg_query($this->conn, $sql);

        if (!is_resource($result) && pg_affected_rows($result) == 0) {
            throw new Exception('Houve um erro no processamento dos dados.');
        }
        return $result;
    }

    /**
     * Conta os resultados de uma consulta
     * @param    resource    $results
     * @return    int
     */
    private function count($results) {
        return pg_num_rows($results);
    }

    /**
     * Retorna os resultados de uma consulta num array associativo (hash-like)
     * @param    resource    $results
     * @return    array
     */
    private function fetchAll($results) {
        return pg_fetch_all($results);
    }

    /**
     * Retorna o resultado como um vetor de objetos
     * @param  resource $results
     * @return  array
     */
    private function fetchAllObject($results) {
        $rows = array_map(function($item) {
                    return (object) $item;
                }, $this->fetchAll($results));

        return $rows;
    }

    /**
     * Retorna o resultado como um vetor de objetos
     * @param  resource $results
     * @return  array
     */
    private function fetchObject($result, $row = NULL, $result_type = NULL) {
        return pg_fetch_object($result);
    }

    /**
     * Controle de transações, inicia nova transacao
     */
    private function begin() {
        pg_query($this->conn, "BEGIN;");
    }

    /**
     * Controle de transações, confirma transacao
     */
    private function commit() {
        pg_query($this->conn, "COMMIT;");
    }

    /**
     * Controle de transações, cancela transacao
     */
    private function rollback() {
        pg_query($this->conn, "ROLLBACK;");
    }

}


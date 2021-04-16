<?php

/**
 * Classe de persistência de dados
 *
 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
 */
class FinCreditoFuturoParametrizacaoEmailAprovacaoDAO {

    /**
     * Conexão com o 
     * @var connection  
     */
    private $conn;
    private $parametros;

    public function __construct($conn) {
        $this->parametros = new stdClass();
        $this->conn = $conn;
    }

    /**
     * Carrega parametros para exibição na tela
     * @param  stdClass $parametros
     * @return stdClass
     */
    public function pesquisarParametro() {

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

        if ($resultado = pg_query($sql)) {
            if (pg_num_rows($resultado) > 0) {
                $res = pg_fetch_object($resultado);
                return $res;
            }
        }
        return false;
    }

    /**
     * Retorna lista de emails cadastrados para a parametrizacao
     * @param  stdClass $parametros
     * @return array:stdClass
     */
    public function pesquisarListaEmailResponsavel(stdClass $parametros = null, $ultimoRegistro = false) {
        $condicoes = array();
        $resultadoPesquisa = array();

        $this->parametros->usuario = isset($parametros->usuario) ? $parametros->usuario : false;
        $this->parametros->email = isset($parametros->email) ? $parametros->email : false;
        if ($this->parametros->usuario) {
            $condicoes[] = "nm_usuario ILIKE ('" . $this->parametros->usuario . "%')";
        }
        if ($this->parametros->email) {
            $condicoes[] = "usuemail = '" . $this->parametros->email . "' ";
        }

        $sql = "SELECT
    					cferoid,
    					cd_usuario,
    					nm_usuario,
                        CASE 
                            WHEN usuemail IS NOT NULL THEN
                                usuemail
                            ELSE
                                ''
                        END as usuemail
    					
    			FROM
    					credito_futuro_email_responsavel
    			JOIN 
    					usuarios ON cd_usuario = cferusuoid
                ";
        if (count($condicoes) > 0) {
            $sql .=" WHERE " . implode(" AND ", $condicoes);
        }

        $sql .= $ultimoRegistro ? " ORDER BY cferoid DESC LIMIT 1" : " ORDER BY nm_usuario ASC";

        if ($resultado = pg_query($sql)) {

            if (pg_num_rows($resultado) == 1) {
                
                $usuario = pg_fetch_object($resultado);
                
                $retorno->usuario = $usuario;
                $retorno->motivo = $this->buscarMotivoCreditoResponsavel($usuario->cd_usuario);
                return $retorno;
            }

            if (pg_num_rows($resultado) > 0) {
                
                $i = 0;
                
                while ($objeto = pg_fetch_object($resultado)) {                    
                    $resultadoPesquisa[$i]['usuario'] = $objeto;
                    $resultadoPesquisa[$i]['motivo'] = $this->buscarMotivoCreditoResponsavel($objeto->cd_usuario);
                    $i++;
                }
            }
        }

        return $resultadoPesquisa;
    }
    
    /**
     * Método buscarMotivoCreditoResponsavel()
     * 
     * @param  string $cd_usuario
     * @return array $retorno
     * 
     * @throws Exception
     */
    public function buscarMotivoCreditoResponsavel($cd_usuario) {
        
        $retorno = array();
        
        $sql = "SELECT
                    CASE WHEN cfmptipomotivo=0 THEN 'Outros'
                         WHEN cfmptipomotivo=1 THEN 'Contestação'
                         WHEN cfmptipomotivo=2 THEN 'Indicação de Amigo'
                         WHEN cfmptipomotivo=3 THEN 'Isenção'
                         WHEN cfmptipomotivo=4 THEN 'Débito Automatico'
                         WHEN cfmptipomotivo=5 THEN 'Cartão de Crédito'
                    END AS motivo_credito     
             FROM
                   credito_futuro_motivo_responsavel
             WHERE
                    cfmpcfeusuoid = " . $cd_usuario . "
             ORDER BY motivo_credito ASC";
        
        if (!$rs = pg_query($sql)) {
            throw new Exception('Houve um erro no processamento dos dados.');
        }
        
        while($row = pg_fetch_object($rs)){
            $retorno[] = $row;
        }
        
        return $retorno;
        
    }

    /**
     * Pesquisa Histórico s
     * @return array:object
     */
    public function pesquisarHistorico() {
        $resultadoPesquisa = array();

        $sql = "SELECT
					cfeaoid,
					cfeavalor_credito_futuro,
					cfeavalor_percentual_desconto,
					cfeaparcelas,
					cfeaobroid_contestacao,
					cfeaobroid_contas,
					cfeaobroid_campanha,
					co.obrobrigacao as obrcontestacao,
					ct.obrobrigacao as obrcontas,
					ca.obrobrigacao as obrcampanha,
					cfeacabecalho,
					cfeacorpo,
					TO_CHAR(cfeadt_exclusao,'DD/MM/YYYY') AS cfeadt_exclusao,
					cfeausuoid_exclusao,
					ue.nm_usuario as usuario_exclusao,
					ue.usuemail as usuemail_exclusao,
					TO_CHAR(cfeadt_inclusao,'DD/MM/YYYY HH24:mi:ss') AS cfeadt_inclusao,
                    cfeadt_inclusao AS data_para_ordernacao,
					cfeausuoid_inclusao,
					ui.nm_usuario as usuario_inclusao,
					ui.usuemail as usuemail_inclusao
				FROM
					credito_futuro_email_aprovacao
				JOIN 
					obrigacao_financeira co ON co.obroid = cfeaobroid_contestacao
				JOIN 
					obrigacao_financeira ct ON ct.obroid = cfeaobroid_contas
				JOIN 
					obrigacao_financeira ca ON ca.obroid = cfeaobroid_campanha
				LEFT JOIN 
					usuarios ue ON ue.cd_usuario = cfeausuoid_exclusao
				LEFT JOIN 
					usuarios ui ON ui.cd_usuario = cfeausuoid_inclusao
				--WHERE
				--	cfeadt_exclusao IS NOT NULL 
				ORDER BY 
					data_para_ordernacao ASC";

        if ($resultado = pg_query($sql)) {
            if (pg_num_rows($resultado) > 0) {
                while ($objeto = pg_fetch_object($resultado)) {
                    $resultadoPesquisa[] = $objeto;
                }
            }
        }

//        

        return $resultadoPesquisa;
    }

    /**
     * Retorna os usuários a serem escolhidos na parametrização
     * @param  stdClass
     * @return array:stdClass
     */
    public function buscarResponsavel(stdClass $filtros) {

        $resultadoPesquisa = array();
        $condicoes = array();

        if (!empty($filtros->nome)) {
            $condicoes[] = "AND nm_usuario ILIKE '" . $filtros->nome . "%'";
        }

        if (!empty($filtros->cd_usuario)) {
            $condicoes[] = "AND cd_usuario = " . intval($filtros->cd_usuario);
        }

        if (empty($condicoes)) {
            return $resultadoPesquisa;
        }

        $sql = "SELECT
                    cd_usuario,
                    nm_usuario,
    				usuemail
    			FROM
                    usuarios
    			WHERE
                    dt_exclusao IS NULL  
                AND
                    cd_usuario NOT IN (
                        SELECT
                            cferusuoid
                        FROM
                            credito_futuro_email_responsavel
                    )
                " . implode(" ", $condicoes) . "
                ORDER BY 
                    nm_usuario ASC
                ";

        if ($resultado = pg_query($sql)) {
            if (pg_num_rows($resultado) > 0) {

                $i = 0;

                while ($objeto = pg_fetch_object($resultado)) {
                    $resultadoPesquisa[$i]['id'] = $objeto->cd_usuario;
                    $resultadoPesquisa[$i]['label'] = utf8_encode($objeto->nm_usuario);
                    $resultadoPesquisa[$i]['value'] = utf8_encode($objeto->nm_usuario);
                    $resultadoPesquisa[$i]['email'] = utf8_encode($objeto->usuemail);
                    $i++;
                }
            }
        }

        return $resultadoPesquisa;
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

        if ($resultado = pg_query($sql)) {
            if (pg_num_rows($resultado) > 0) {
                while ($objeto = pg_fetch_object($resultado)) {
                    $resultadoPesquisa[] = $objeto;
                }
            }
        }
        return $resultadoPesquisa;
    }

    /**
     * Excluir e-mail responsavel
     * @param stdClass $parametros - (int) cferoid
     * @return boolean
     */
    public function excluirEmailResponsavel(stdClass $parametros) {

        $this->parametros->cferoid = isset($parametros->cferoid) ? (int) $parametros->cferoid : 0;
        $this->parametros->usuarioid = isset($parametros->usuarioid) ? (int) $parametros->usuarioid : 0;


        $sql = "
            DELETE 
                FROM 
                    credito_futuro_email_responsavel
                WHERE
                    cferoid = " . $this->parametros->cferoid;

        $excluido =  pg_affected_rows(pg_query($sql));
        
        if ($excluido) {
            
            $sql = "
            DELETE 
                FROM 
                    credito_futuro_motivo_responsavel
                WHERE
                    cfmpcfeusuoid = " . $this->parametros->usuarioid;
            
            if (pg_affected_rows(pg_query($sql)) == 0){
                throw new Exception('Houve um erro no processamento dos dados.');
            }
            
        }
        
        return $excluido;
    }

    /**
     * Incluir e-mail responsavel
     * @param stdClass $parametros - (int) cd_usuario
     * @return boolean
     */
    public function incluirEmailResponsavel(stdClass $parametros) {
        $this->parametros->cd_usuario = isset($parametros->cd_usuario) ? (int) $parametros->cd_usuario : null;

        if (!$this->parametros->cd_usuario || $this->parametros->cd_usuario == 0) {
            return false;
        } else {
            $sql = "INSERT
    				INTO
    						credito_futuro_email_responsavel
                        (cferusuoid)
      				VALUES
                        (" . $this->parametros->cd_usuario . ") 
                    RETURNING cferoid";

            if ($resultado = pg_query($sql)) {
                return pg_fetch_result($resultado, 0, 'cferoid');
            }
            return false;
        }
    }

    /**
     * Método incluirMotivoCreditoResponsavel()
     * Inclui os motivos de créditos atrelado a um usuario responsável.
     * 
     * @param string $usuario
     * @param string $motivo
     * @return boolean
     */
    public function incluirMotivoCreditoResponsavel($usuario, $motivo) {

        $sql = "INSERT INTO
                        credito_futuro_motivo_responsavel
                        (cfmpcfeusuoid, cfmptipomotivo)
                 VALUES
                        (" . $usuario . "," . $motivo . ")";
                
        if (!$rs = pg_query($this->conn,$sql)) {
            return false;
        }
        
        if (pg_affected_rows($rs) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Salva novo registro, e apaga o registro anterior
     * @param stdClass $registroNovo
     * @throws Exception
     * @return boolean
     */
    public function salvar(stdClass $registroNovo) {
        try {
            pg_query($this->conn, "BEGIN;");

            $this->parametros->novoRegistro->cfeavalor_credito_futuro = isset($registroNovo->cfeavalor_credito_futuro) ? $registroNovo->cfeavalor_credito_futuro : 0;
            $this->parametros->novoRegistro->cfeavalor_percentual_desconto = isset($registroNovo->cfeavalor_percentual_desconto) ? $registroNovo->cfeavalor_percentual_desconto : 0;
            $this->parametros->novoRegistro->cfeaparcelas = isset($registroNovo->cfeaparcelas) ? $registroNovo->cfeaparcelas : 0;
            $this->parametros->novoRegistro->cfeaobroid_contestacao = isset($registroNovo->cfeaobroid_contestacao) ? $registroNovo->cfeaobroid_contestacao : 0;
            $this->parametros->novoRegistro->cfeaobroid_contas = isset($registroNovo->cfeaobroid_contas) ? (int) $registroNovo->cfeaobroid_contas : 0;
            $this->parametros->novoRegistro->cfeaobroid_campanha = isset($registroNovo->cfeaobroid_campanha) ? (int) $registroNovo->cfeaobroid_campanha : 0;
            $this->parametros->novoRegistro->cfeacabecalho = isset($registroNovo->cfeacabecalho) ? $registroNovo->cfeacabecalho : "";
            $this->parametros->novoRegistro->cfeacorpo = isset($registroNovo->cfeacorpo) ? $registroNovo->cfeacorpo : "";
            $this->parametros->novoRegistro->usuario = isset($registroNovo->usuario) ? $registroNovo->usuario : 0;

            if (empty($this->parametros->novoRegistro->cfeavalor_credito_futuro) ||
                    empty($this->parametros->novoRegistro->cfeavalor_percentual_desconto) ||
                    empty($this->parametros->novoRegistro->cfeaparcelas) ||
                    empty($this->parametros->novoRegistro->cfeaobroid_contestacao) ||
                    empty($this->parametros->novoRegistro->cfeaobroid_contas) ||
                    empty($this->parametros->novoRegistro->cfeaobroid_campanha) ||
                    empty($this->parametros->novoRegistro->cfeacabecalho) ||
                    empty($this->parametros->novoRegistro->cfeacorpo) ||
                    empty($this->parametros->novoRegistro->usuario)) {
                throw new Exception('Existem campos obrigatórios não preenchidos.');
            }

            $this->parametros->registroAtual = $this->pesquisarParametro();
            if ($this->parametros->registroAtual) {
                $this->parametros->registroAtual->usuario = $this->parametros->novoRegistro->usuario;
                $this->excluirParametro($this->parametros->registroAtual);
            }

            $id = $this->incluirParametro($this->parametros->novoRegistro);

            if (!$id) {
                throw new Exception('Houve um erro no processamento dos dados.');
            }

            $this->parametros->novoRegistro->cfeaoid = $id;

            pg_query($this->conn, "COMMIT;");

            return $this->parametros->novoRegistro;
        } catch (Exception $e) {

            pg_query($this->conn, "ROLLBACK;");
            throw new Exception($e->getMessage());
            return false;
        }
    }

    /**
     * Incluir novo parametro, usado na funcao salvar
     * @param stdClass $parametros - (int) cd_usuario
     * @return boolean
     */
    private function incluirParametro(stdClass $novoRegistro) {
        $sql = "INSERT INTO 
    				credito_futuro_email_aprovacao
    				(   		
						cfeavalor_credito_futuro,
						cfeavalor_percentual_desconto,
						cfeaparcelas,
						cfeaobroid_contestacao,
						cfeaobroid_contas,
						cfeaobroid_campanha,
						cfeacabecalho,
						cfeacorpo,
    					cfeausuoid_inclusao,
    					cfeadt_inclusao
    				)
    			VALUES
    				(
						" . $novoRegistro->cfeavalor_credito_futuro . ",
						" . $novoRegistro->cfeavalor_percentual_desconto . ",
						" . intval($novoRegistro->cfeaparcelas) . ",
						" . intval($novoRegistro->cfeaobroid_contestacao) . ",
						" . intval($novoRegistro->cfeaobroid_contas) . ",
						" . intval($novoRegistro->cfeaobroid_campanha) . ",
						'" . pg_escape_string($novoRegistro->cfeacabecalho) . "',
						'" . pg_escape_string($novoRegistro->cfeacorpo) . "',
						" . intval($novoRegistro->usuario) . ",
						NOW()
    				) 
               	RETURNING cfeaoid";

        if ($resultado = pg_query($sql)) {
            $obj = pg_fetch_object($resultado);
            return $obj->cfeaoid;
        }

        return false;
    }

    /**
     * Exclui parametro, usado na funcao salvar
     * @param stdClass $parametros
     * @throws Exception
     * @return boolean
     */
    private function excluirParametro(stdClass $parametros) {
        $this->parametros->cfeaoid = isset($parametros->cfeaoid) ? (int) $parametros->cfeaoid : null;
        $this->parametros->usuario = isset($parametros->usuario) ? $parametros->usuario : null;

        if (!$this->parametros->cfeaoid || $this->parametros->cfeaoid == 0
                || !$this->parametros->usuario || $this->parametros->usuario == 0) {

            throw new Exception('Parametro não informado.');
        } else {
            $sql = "UPDATE  
    					credito_futuro_email_aprovacao
    				SET 
    					cfeadt_exclusao = NOW(),
    					cfeausuoid_exclusao = " . intval($this->parametros->usuario) . "
      				WHERE
    					cfeaoid = " . $this->parametros->cfeaoid;

            if ($resultado = pg_query($sql)) {
                return true;
            }
            return false;
        }
    }

}


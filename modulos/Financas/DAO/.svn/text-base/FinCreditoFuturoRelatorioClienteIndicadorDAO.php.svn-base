<?php
/**
 * Relatório Cliente Indicador Crédito Futuro
 *
 * @package Finanças
 * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
 */
class FinCreditoFuturoRelatorioClienteIndicadorDAO {


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
     * Método buscarClientesIndicadores()
     * 
     * @param array $parametros =>  Parâmetros para pesquisa. 
     *  
     * @return array $retorno
     */
    public function buscarClientesIndicadores(stdClass $parametros) {

        $retorno = array();

        $sql = "(
                SELECT
					TO_CHAR(cfcidt_inclusao,'DD/MM/YYYY') AS dt_inclusao,

				    --informação de cliente indicador
				    cfciclioid AS cliente_id,
		            clinome AS cliente_nome,
		            clitipo,
				    CASE WHEN clitipo = 'J' THEN clino_cgc
					ELSE
						clino_cpf
					END AS doc,

					--contrato
					cfcitermo AS contrato,

		        	--informação de cliente indicado
				    conclioid AS cliente_id_indicado,
		            (SELECT clitipo FROM clientes WHERE clioid = conclioid) AS clitipo_indicado,
		            (SELECT clinome FROM clientes WHERE clioid = conclioid) AS cliente_nome_indicado,
				    (SELECT CASE WHEN clitipo = 'J' THEN clino_cgc
					ELSE
						clino_cpf
					END AS doc_indicado
		            FROM clientes WHERE clioid = conclioid) AS doc_indicado,

		            --campanha promocional
		            cftpdescricao AS nome_campanha,
		            TO_CHAR(cfcpdt_inicio_vigencia,'DD/MM/YYYY') AS dt_inicio_vigencia,
		            TO_CHAR(cfcpdt_fim_vigencia,'DD/MM/YYYY') AS dt_fim_vigencia,
		                
		            --equipamento instalado
					cfcieqpto_instalado AS cfcieqpto_instalado_id,
                    CASE WHEN cfcieqpto_instalado = true THEN 'Sim'
                         WHEN cfcieqpto_instalado = false THEN 'Não'
                    END AS cfcieqpto_instalado_descricao,

					--forma de inclusão
					cfciforma_inclusao AS cfciforma_inclusao_id,
					CASE WHEN cfciforma_inclusao = 'A' THEN 'Automática'
					     WHEN cfciforma_inclusao = 'M' THEN 'Manual'
					END AS cfciforma_inclusao_descricao,

				    --Usuario de inclusão
				    cfciusuoid_inclusao AS usuario_inclusao_id,
				    (SELECT nm_usuario FROM usuarios WHERE cd_usuario = cfciusuoid_inclusao) AS usuario_inclusao_nome
				FROM
		            credito_futuro_cliente_indicador
		        INNER JOIN
					contrato ON (connumero = cfcitermo)
		        LEFT JOIN
					credito_futuro ON (cfoconnum_indicado = cfcitermo)
				INNER JOIN
					clientes ON (clioid = cfciclioid)
		        INNER JOIN
		            credito_futuro_campanha_promocional ON (cfcpoid = cfcicfcpoid ) 
		        INNER JOIN
		            credito_futuro_tipo_campanha ON (cftpoid = cfcpcftpoid)     
		        WHERE
					--data de inclusao de cliente indicador (obrigatório)
                	cfcidt_inclusao BETWEEN '" . $parametros->cfcidt_inclusao_de . " 00:00:01'
                    AND '" . $parametros->cfcidt_inclusao_ate . " 23:59:59' ";

        //Filtro por cliente indicador(Não obrigatório)
        if ( isset($parametros->cliente_id) && trim($parametros->cliente_id) != '') {
            $sql .= "AND cfciclioid = " . intval($parametros->cliente_id) . " ";
        }

        //Filtro por cliente indicado (Não obrigatório)
        if ( isset($parametros->cliente_id_indicado) && trim($parametros->cliente_id_indicado) != '') {
            $sql .= "AND conclioid = " . intval($parametros->cliente_id_indicado) . " ";
        }
        
        //Filtro por contrato (Não obrigatório)
        if ( isset($parametros->cfcitermo) && trim($parametros->cfcitermo) != '') {
            $sql .= "AND cfcitermo = " . intval($parametros->cfcitermo) . " ";
        }

        //Filtro por usuario de inclusao (Não obrigatório)
        if ( isset($parametros->cfciusuoid_inclusao) && trim($parametros->cfciusuoid_inclusao) != '') {
            $sql .= "AND cfciusuoid_inclusao = " . intval($parametros->cfciusuoid_inclusao) . " ";
        }

        //Filtro por equipamento instalado (Não obrigatório)
        if ( isset($parametros->cfcieqpto_instalado) && trim($parametros->cfcieqpto_instalado) != '') {
            $sql .= "AND cfcieqpto_instalado = '" . $parametros->cfcieqpto_instalado . "' ";
        }
        
        //Filtro por Forma Inclusão (Não obrigatório)
        if ( isset($parametros->cfciforma_inclusao) && trim($parametros->cfciforma_inclusao) != '') {
            $sql .= "AND cfciforma_inclusao = '" . $parametros->cfciforma_inclusao . "' ";
        }
        
        $sql .= " )
            UNION
                
            (SELECT
                TO_CHAR(cfodt_inclusao,'DD/MM/YYYY') AS dt_inclusao,

                --informação de cliente indicador
                cfoclioid AS cliente_id,
                clinome AS cliente_nome,
                clitipo,
                CASE WHEN clitipo = 'J' THEN clino_cgc
                    ELSE
                    clino_cpf
                END AS doc,
                
                --contrato
                cfoconnum_indicado AS contrato,
                
                --informação de cliente indicado
                conclioid AS cliente_id_indicado,
                (SELECT clitipo FROM clientes WHERE clioid = conclioid) AS clitipo_indicado,
                (SELECT clinome FROM clientes WHERE clioid = conclioid) AS cliente_nome_indicado,
                (SELECT CASE WHEN clitipo = 'J' THEN clino_cgc
                    ELSE
                    clino_cpf
                END AS doc_indicado
                FROM clientes WHERE clioid = conclioid) AS doc_indicado,
                
                --campanha promocional
                '' AS nome_campanha,
                '' AS dt_inicio_vigencia,
                '' AS dt_fim_vigencia,
                
                --equipamento instalado
                CASE WHEN conequoid IS NOT NULL THEN CAST ('t' AS BOOLEAN)
                     WHEN conequoid IS NULL THEN CAST ('f' AS BOOLEAN)
                END AS cfcieqpto_instalado_id,
                
                CASE WHEN conequoid IS NOT NULL THEN 'Sim'
                     WHEN conequoid IS NULL THEN 'Não'
                END AS cfcieqpto_instalado_descricao,
                
                --forma de inclusão
                CASE WHEN cfoforma_inclusao = 1 THEN CAST('M' AS VARCHAR(1))
                     WHEN cfoforma_inclusao = 2 THEN CAST('A' AS VARCHAR(1))
                END AS cfciforma_inclusao_id,
                CASE WHEN cfoforma_inclusao = 1 THEN 'Manual'
                     WHEN cfoforma_inclusao = 2 THEN 'Automática'
                END AS cfciforma_inclusao_descricao,
            
    	        --Usuario de inclusão
            	cfousuoid_inclusao AS usuario_inclusao_id,
            	(SELECT nm_usuario FROM usuarios WHERE cd_usuario = cfousuoid_inclusao) AS usuario_inclusao_nome
            	FROM
            	    contrato
            	INNER JOIN
            	    credito_futuro ON (cfoconnum_indicado = connumero)
            	INNER JOIN
            	    clientes ON (clioid = cfoclioid)
            	WHERE
            	    cfoconnum_indicado IS NOT NULL
            	    AND cfoconnum_indicado NOT IN (SELECT cfcitermo FROM credito_futuro_cliente_indicador)
					AND cfodt_inclusao BETWEEN '" . $parametros->cfcidt_inclusao_de . " 00:00:01'
                    AND '" . $parametros->cfcidt_inclusao_ate . " 23:59:59' ";
        
        //Filtro por cliente indicador(Não obrigatório)
        if ( isset($parametros->cliente_id) && trim($parametros->cliente_id) != '') {
            $sql .= "AND cfoclioid = " . intval($parametros->cliente_id) . " ";
        }
        
        //Filtro por cliente indicado (Não obrigatório)
        if ( isset($parametros->cliente_id_indicado) && trim($parametros->cliente_id_indicado) != '') {
            $sql .= "AND conclioid = " . intval($parametros->cliente_id_indicado) . " ";
        }
        
        //Filtro por contrato (Não obrigatório)
        if ( isset($parametros->cfcitermo) && trim($parametros->cfcitermo) != '') {
            $sql .= "AND cfoconnum_indicado = " . intval($parametros->cfcitermo) . " ";
        }
        
        //Filtro por usuario de inclusao (Não obrigatório)
        if ( isset($parametros->cfciusuoid_inclusao) && trim($parametros->cfciusuoid_inclusao) != '') {
            $sql .= "AND cfousuoid_inclusao = " . intval($parametros->cfciusuoid_inclusao) . " ";
        }
        
        //Filtro por equipamento instalado (Não obrigatório)
        if ( isset($parametros->cfcieqpto_instalado) && trim($parametros->cfcieqpto_instalado) != '') {
            
            switch ($parametros->cfcieqpto_instalado){
                case "t":
                    $sql .= "AND conequoid IS NOT NULL ";
                    break;
                case "f":
                    $sql .= "AND conequoid IS NULL ";
                    break;
            }    
            
        }
        
        //Filtro por Forma Inclusão (Não obrigatório)
        if ( isset($parametros->cfciforma_inclusao) && trim($parametros->cfciforma_inclusao) != '') {
            
            switch ($parametros->cfciforma_inclusao){
                case "M":
                    $sql .= "AND cfoforma_inclusao = 1 ";
                    break;
                case "A":
                    $sql .= "AND cfoforma_inclusao = 2 ";
                    break;
            }
            
        }
        
        $sql .= " ) ORDER BY dt_inclusao DESC, cliente_nome ASC ";
        
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception(MENSAGEM_ERRO_PROCESSAMENTO . ' - Pesquisar clientes indicadores.');
        }

        while ($row = pg_fetch_object($rs)) {

            if ($row->clitipo == 'J') {
                $row->doc = $this->formatarDados('cnpj', $row->doc);
            } else if ($row->clitipo == 'F') {
                $row->doc = $this->formatarDados('cpf', $row->doc);
            }

            if ($row->clitipo_indicado == 'J') {
                $row->doc_indicado = $this->formatarDados('cnpj', $row->doc_indicado);
            } else if ($row->clitipo_indicado == 'F') {
                $row->doc_indicado = $this->formatarDados('cpf', $row->doc_indicado);
            }

            $retorno[] = $row;
        }
        //echo "<pre>";print_r($retorno); echo "</pre>";
        return $retorno;

    }
    
    /**
     * Buscar usuarios inclusao credito futuro
     *
     * @return array $retorno
     */
    public function buscarUsuarioInclusaoRelatorioClienteIndicador() {
    
        $sql = "SELECT
                    DISTINCT cd_usuario,
                    nm_usuario
                FROM
                    credito_futuro_cliente_indicador
                INNER JOIN
                    usuarios ON cd_usuario = cfciusuoid_inclusao
                -- UNION
                -- SELECT
                --    DISTINCT cd_usuario,
                --    nm_usuario
                -- FROM
                --    credito_futuro
                -- INNER JOIN
                --    usuarios ON cd_usuario = cfousuoid_inclusao
                -- ORDER BY
                --    nm_usuario ASC";
    
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    
        $retorno = array();
        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }
    
        return $retorno;
    }

    /**
     * Buscar usuarios inclusao credito futuro por ID
     *
     * @param integer $id => Id do Cliente Indicador
     * 
     * @return array  $retorno
     */
    public function buscarUsuarioInclusaoRelatorioClienteIndicadorPorId($id) {
        
        $id = isset($id) ? $id : "";
        
        if ($id !== "") {
            
            $sql = "SELECT
                        DISTINCT cd_usuario,
                        nm_usuario
                    FROM
                        usuarios 
                    WHERE cd_usuario = ".$id;
    
            if (!$rs = pg_query($this->conn, $sql)) {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
        
        } else {
            return false;
        }    
        
        if (pg_num_rows($rs) > 0) {
            return pg_fetch_result($rs, 0, 'nm_usuario');
        }
            
        return false;
    }
    
    /**
     * Buscar cliente por nome sendo ele PJ || PF
     *
     * @param stdClass $parametros parametros para busca.
     *
     * @return array $retorno
     */
    public function buscarClienteNome($parametros) {
    
        $retorno = array();
    
    
        if (trim($parametros->nome) === '') {
            echo json_encode($retorno);
            exit;
        }
    
        $sql = "SELECT
    
                        clioid,
                        clinome,
                        CASE WHEN clitipo = 'J' THEN
                            clino_cgc
                        ELSE
                            clino_cpf
                        END AS doc,
                        clitipo AS tipo
    
               FROM
                        clientes
               WHERE
                        clidt_exclusao IS NULL ";
    
        if (trim($parametros->tipo) != '') {
            $sql  .= " AND
                        clitipo = '" . pg_escape_string($parametros->tipo) . "' ";
        }
         
    
        $sql .= " AND
                        clinome ILIKE '" . pg_escape_string($parametros->nome) . "%'
    
               ORDER BY
                        clinome
               LIMIT 100";
    
        if ($rs = pg_query($this->conn, $sql)) {
            if (pg_num_rows($rs) > 0) {
                $i = 0;
                while ($objeto = pg_fetch_object($rs)) {
                    $retorno[$i]['id'] = $objeto->clioid;
                    $retorno[$i]['label'] = utf8_encode($objeto->clinome);
                    $retorno[$i]['value'] = utf8_encode($objeto->clinome);
                    $retorno[$i]['tipo'] = utf8_encode($objeto->tipo);
                    if ($objeto->tipo == 'J') {
                        $retorno[$i]['doc'] = utf8_encode($this->formatarDados('cnpj', $objeto->doc));
                    } else if ($objeto->tipo == 'F') {
                        $retorno[$i]['doc'] = utf8_encode($this->formatarDados('cpf', $objeto->doc));
                    }
                    $i++;
                }
            }
        }
    
        return $retorno;
    }
    
    /**
     * Buscar cliente por documento (CPF/CNPJ)
     *
     * @param stdClass $parametros tipo de cliente e numero de documento
     *
     * @return array $retorno
     */
    public function buscarClienteDoc($parametros) {
    
        $retorno = array();
    
        if (trim($parametros->documento) === '') {
            echo json_encode($retorno);
            exit;
        }
    
        $sql = "SELECT
    
                        clioid,
                        clinome,
                        CASE WHEN clitipo = 'J' THEN
                            clino_cgc
                        ELSE
                            clino_cpf
                        END AS doc
    
               FROM
                        clientes
               WHERE
                        clidt_exclusao IS NULL
               AND
                        clitipo = '" . pg_escape_string($parametros->tipo) . "' ";
    
        if ($parametros->tipo == 'J') {
            $sql .= " AND
                                lpad(clino_cgc::TEXT, 14,'0') LIKE '" . pg_escape_string($parametros->documento) . "%' ";
        } else if ($parametros->tipo == 'F') {
            $sql .= " AND
                                lpad(clino_cpf::TEXT, 11,'0') LIKE '" . pg_escape_string($parametros->documento) . "%' ";
        }
    
        $sql .= "
                  ORDER BY
                        clinome
                    LIMIT 100 ";
    
        if ($rs = pg_query($this->conn, $sql)) {
            if (pg_num_rows($rs) > 0) {
                $i = 0;
                while ($objeto = pg_fetch_object($rs)) {
                    $retorno[$i]['id'] = $objeto->clioid;
                    $retorno[$i]['nome'] = utf8_encode($objeto->clinome);
                    if ($parametros->tipo == 'J') {
                        $retorno[$i]['label'] = utf8_encode($this->formatarDados('cnpj', $objeto->doc));
                        $retorno[$i]['value'] = utf8_encode($this->formatarDados('cnpj', $objeto->doc));
                        $retorno[$i]['doc'] = utf8_encode($this->formatarDados('cnpj', $objeto->doc));
                    } else if ($parametros->tipo == 'F') {
                        $retorno[$i]['label'] = utf8_encode($this->formatarDados('cpf', $objeto->doc));
                        $retorno[$i]['value'] = utf8_encode($this->formatarDados('cpf', $objeto->doc));
                        $retorno[$i]['doc'] = utf8_encode($this->formatarDados('cpf', $objeto->doc));
                    }
                    $i++;
                }
            }
        }
    
        return $retorno;
    }
    
    /**
     * Formatar dados (CPF||CNPJ)
     * 
     * @param string $tipo  tipo doc
     * @param string $valor valor do doc
     * 
     * @return string $valor
     */
    public function formatarDados($tipo, $valor) {

        if ($tipo == "cpf" && $valor != "") {
            $valor = str_pad($valor, 11, "0", STR_PAD_LEFT);
            return $valor = substr($valor, 0, 3) . "." . substr($valor, 3, 3) . "." . substr($valor, 6, 3) . "-" . substr($valor, 9, 2);
        }

        if ($tipo == "cnpj" && $valor != "") {
            $valor = str_pad($valor, 14, "0", STR_PAD_LEFT);
            return $valor = substr($valor, 0, 2) . "." . substr($valor, 2, 3) . "." . substr($valor, 5, 3) . "/" . substr($valor, 8, 4) . "-" . substr($valor, 12, 2);
        }
    }

}
<?php
/**
 * DAO - <PrnTermoAditivoServicoDAO.class.php>
 * @author Bruno Bonfim Affonso - <bruno.bonfim@sascar.com.br>
 * @package Principal
 * @version 1.0
 * @since 03/04/2013
 */
class PrnTermoAditivoServicoDAO{
    private $conn = null;

    function __construct(){	
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Inicia uma transação
     */
    public function beginTransaction(){
        pg_query($this->conn, "BEGIN;");
    }
    
    /**
     * Confirma uma transação
     */
    public function commitTransaction(){
        pg_query($this->conn, "COMMIT;");
    }
    
    /**
     * Reverte uma transação
     */
    public function rollbackTransaction(){
        pg_query($this->conn, "ROLLBACK;");
    }

    /**
     * Retorna um array com os tipos
     * de status do Termo Aditivo.
     * 
     * @return Array
     */
    public function getStatus(){
        $sql = "SELECT
                    tasesoid,
                    trim(tasesdescricao) as tasesdescricao 
                FROM
                    termo_aditivo_servico_status
                WHERE
                    tasesdt_exclusao IS NULL
                ORDER BY
                    tasesdescricao;";

        $sql    = pg_query($this->conn, $sql);		
        $result = null;
        
        while($rs = pg_fetch_array($sql)){
            $result[] = array("tasesoid" => $rs["tasesoid"], "tasesdescricao" => $rs["tasesdescricao"]);
        }

        return $result;
    }

    /**
     * Retorna um array com os tipos
     * de serviços do Termo Aditivo.
     * @param int $id_servico
     * @return Array
     */
    public function getServico($id_servico = null, $tipo = null, $pacote = null, $listaItems = false){
        $sql = "";

        if(!$listaItems) {
            if($tipo === 'P' || ($tipo === 'F' && $pacote == null)) {
                return array();
            }

            if($tipo === 'A' || $tipo == null) {
                $sql = "SELECT
                            obroid,
                            TRIM(obrobrigacao) AS obrobrigacao,
                            CASE WHEN obrtipo_obrigacao = 'A' THEN 'Assistência'
                                 WHEN obrtipo_obrigacao = 'P' THEN 'Pacote'
                                 WHEN obrtipo_obrigacao = 'V' THEN 'Serviço'
                                 WHEN obrtipo_obrigacao = 'S' THEN 'Software'
                            END AS modalidade
                        FROM
                            obrigacao_financeira
                        WHERE
                            obrdt_exclusao is NULL
                            AND obrtipo_obrigacao IN ('P', 'V', 'S')
                            AND (obrorigem <> 'G' OR obrorigem IS NULL)
                        ORDER BY
                            obrobrigacao; ";

            } else if($tipo === 'F') {
                    $sql = "SELECT
                        ofiservico AS obroid,
                        trim(obrobrigacao) as obrobrigacao,
                        CASE WHEN obrtipo_obrigacao = 'A' THEN 'Assistência'
                                 WHEN obrtipo_obrigacao = 'P' THEN 'Pacote'
                                 WHEN obrtipo_obrigacao = 'V' THEN 'Serviço'
                                 WHEN obrtipo_obrigacao = 'S' THEN 'Software'
                        END as modalidade
                    FROM
                        obrigacao_financeira
                        INNER JOIN obrigacao_financeira_item ON obroid = ofiservico
                    WHERE
                        ofiobroid = $pacote
                        AND obrtipo_obrigacao IN ('V', 'S')
                        AND obrorigem = 'G'
                        AND ofiexclusao IS NULL
                        AND obroid IN
                                    (
                                        SELECT
                                            ofiservico
                                        FROM
                                            obrigacao_financeira_item
                                        WHERE
                                            ofiservico = obroid
                                            AND ofiobroid = $pacote
                                            AND ofiexclusao IS NULL
                                    )
                        ORDER BY obrobrigacao;";
            }
        } else {
           $sql = "SELECT
                        obroid,
                        trim(obrobrigacao) as obrobrigacao,
                        CASE WHEN obrtipo_obrigacao = 'A' THEN 'Assistência'
                             WHEN obrtipo_obrigacao = 'P' THEN 'Pacote'
                             WHEN obrtipo_obrigacao = 'V' THEN 'Serviço'
                             WHEN obrtipo_obrigacao = 'S' THEN 'Software'
                        END as modalidade
                    FROM
                        obrigacao_financeira
                    WHERE
                        obrdt_exclusao is null ";

            if($id_servico == null) { 
                $sql .= "AND obrtipo_obrigacao in('V','S')";
            }

            if($id_servico != null){
                $sql .= "AND obroid = $id_servico";
            }
            
            $sql .= "ORDER BY
                        obrobrigacao;";
        }

        $sql    = pg_query($this->conn, $sql);        
        $result = null;

        while($rs = pg_fetch_array($sql)){
            $result[] = array("obroid" => $rs["obroid"], "obrobrigacao" => $rs["obrobrigacao"], "modalidade" => $rs["modalidade"]);
        }

        return $result;
    }
    
    public function getPacote($id_servico = null) {
        $sql = "SELECT
                    obroid,
                    TRIM(obrobrigacao) AS obrobrigacao,
                    CASE WHEN obrtipo_obrigacao = 'A' THEN 'Assistência'
                         WHEN obrtipo_obrigacao = 'P' THEN 'Pacote'
                         WHEN obrtipo_obrigacao = 'V' THEN 'Serviço'
                         WHEN obrtipo_obrigacao = 'S' THEN 'Software'
                    END AS modalidade
                FROM
                    obrigacao_financeira
                WHERE
                    obrdt_exclusao is NULL
                    AND obrtipo_obrigacao = 'P'
                    AND obrorigem = 'G'
                ORDER BY
                    obrobrigacao";

        $sql    = pg_query($this->conn, $sql);        
        $result = null;

        while($rs = pg_fetch_array($sql)){
            $result[] = array("obroid" => $rs["obroid"], "obrobrigacao" => $rs["obrobrigacao"], "modalidade" => $rs["modalidade"]);
        }

        return $result;
    }
    /**
     * Retorna um array com a pesquisa realizada
     * dos termos aditivos de serviço.
     *
     * @param Array $dados
     * @return Array
     */
    public function pesquisarTermoAditivoServico($dados){
        $sql = "SELECT
                    taseoid as numero,
                    to_char(tasedt_cadastro,'dd/mm/yyyy hh24:mi') as data,
                    clinome as cliente,
                    nm_usuario as usuario,
                    CASE WHEN tasetasesoid = 1 THEN 'Pendente'
                         WHEN tasetasesoid = 2 THEN 'Aprovado'
                         WHEN tasetasesoid = 3 THEN 'Cancelado/Não Autorizado'
                         END as status,
                    CASE WHEN tasesituacao = 'M' THEN 'Faturamento Mensal'
                         WHEN tasesituacao = 'C' THEN 'Cortesia'
                         WHEN tasesituacao = 'D' THEN 'Demonstração'
                         END as situacao,
                    (SELECT
                        sum(taseivalor_negociado)
                     FROM
                        termo_aditivo_servico_item
                     WHERE
                        taseidt_exclusao IS NULL
                     AND
                        taseitaseoid = taseoid) as valor
                FROM
                    termo_aditivo_servico,
                    termo_aditivo_servico_status,
                    clientes,
                    usuarios,
        			veiculo, 
        			contrato, 
        			termo_aditivo_servico_item
                WHERE
                    tasedt_exclusao IS NULL 
                AND
                    taseclioid = clioid 
                AND
                    tasetasesoid = tasesoid   
                AND
                    taseusuoid = cd_usuario
                AND
                    connumero = taseiconnumero
                AND
		    		conveioid = veioid
	        	AND
		    		taseitaseoid = taseoid";
        
        //Filtros
        if($dados['data_inicio'] != "" && $dados['data_fim'] != ""){
            //Formato BD
            list($dia, $mes, $ano) = explode("/",$dados['data_inicio']);
            $inicio = $ano."-".$mes."-".$dia;
            
            list($dia, $mes, $ano) = explode("/",$dados['data_fim']);
            $fim = $ano."-".$mes."-".$dia;
            
            $sql .= " AND to_char(tasedt_cadastro, 'YYYY-mm-dd') BETWEEN '".$inicio."' AND '".$fim."'";
        }
        
        if($dados['cliente'] != ""){
            $sql .= " AND clinome ILIKE '%".$dados['cliente']."%'";
        }
        
        if($dados['placa'] != ""){
            $sql .= " AND veiplaca = '".$dados['placa']."'";
        }
        
        if($dados['numero_aditivo'] != ""){
            $sql .= " AND taseoid = ".$dados['numero_aditivo'];        
        }
        
        if($dados['status'] != ""){
            $sql .= " AND tasetasesoid = ".$dados['status'];
        }
        
        if($dados['servico'] != ""){
            $sql .= " AND taseoid IN (SELECT
                                        taseitaseoid
                                    FROM
                                        termo_aditivo_servico_item
                                    LEFT OUTER JOIN
                                        obrigacao_financeira ON obroid = taseiobroid
                                    WHERE
                                        taseiobroid = ".$dados['servico'].")";
        }
        
        $sql .= " ORDER BY clinome, tasedt_cadastro desc;";
        
        $sql    = pg_query($this->conn, $sql);
        $result = null;
        
        while($rs = pg_fetch_array($sql)){
            $result[] = array("numero" => $rs["numero"], "data" => $rs["data"], "cliente" => $rs["cliente"],
                              "usuario" => $rs["usuario"], "status" => $rs["status"], "situacao" => $rs["situacao"],
                              "valor" => $rs["valor"]);
        }

        return $result;
    }
    
    /**
     * Retorna o valor da Obrigação Financeira.
     * @param int $servico
     * @return float
     */
    public function getValorObrigacaoFinanceira($servico){
        $sql = "SELECT
                    obrvl_obrigacao
                FROM
                    obrigacao_financeira
                WHERE
                    obroid = $servico;";
                    
        $sql    = pg_query($this->conn, $sql);
        $result = null;
        
        if(pg_num_rows($sql) > 0){
            $result = pg_fetch_result($sql, 0, 'obrvl_obrigacao');
        }
        
        return $result;
    }
    
    /**
     * Retorna um array com os tipos
     * de contratos compativeis com o cliente.
     * @param int $servico
     * @param int $cliente
     * @return Array
     */
    public function getContrato($servico, $cliente){
        $sql = "SELECT
                    connumero
                    ,trim(clinome) as clinome
                FROM
                    contrato, clientes
                WHERE
                    conclioid = clioid
                AND
                    condt_exclusao IS NULL
                AND
                    (coneqcoid IN (SELECT
                                        obrceqcoid
                                    FROM
                                        obrigacao_financeira_classe
                                    WHERE
                                        obrceqcoid = coneqcoid
                                    AND
                                        obrcdt_exclusao IS NULL
                                    AND
                                        obrcobroid = $servico
                                    )
                    OR (SELECT
                                        count(*)
                                        FROM
                                            obrigacao_financeira_classe
                                        WHERE
                                            obrcdt_exclusao IS NULL
                                        AND
                                            obrcobroid = $servico
                                        )=0
                    )
                AND
                    (conno_tipo IN (SELECT
                                        obrttpcoid
                                    FROM
                                        obrigacao_financeira_tipo_contrato
                                    WHERE
                                        obrttpcoid = conno_tipo
                                    AND
                                        obrtdt_exclusao IS NULL
                                    AND
                                        obrtobroid = $servico
                                    )
                    OR  (SELECT count(*) 
                                            FROM
                                                obrigacao_financeira_tipo_contrato
                                            WHERE
                                                obrtdt_exclusao IS NULL
                                            AND
                                                obrtobroid = $servico
                                        )=0
                    )
                AND
                    concsioid = 1
                AND
                    conclioid = $cliente 
                AND
                    (CASE WHEN conequoid > 0 THEN
                        (SELECT
                            true
                        FROM
                            obrigacao_financeira_projeto, equipamento, equipamento_versao
                        WHERE
                            conequoid = equoid
                        AND
                            equeveoid = eveoid
                        AND
                            eveprojeto IN(obrpeproid)
                        AND
                            obrpdt_exclusao IS NULL
                        AND
                            obrpobroid = $servico
                        ) 
                        OR ((SELECT count(*) FROM obrigacao_financeira_projeto WHERE obrpdt_exclusao IS NULL AND obrpobroid = $servico) = 0)
                    ELSE
                        (SELECT true) END
                    ) 
                   AND connumero::text ilike '$contrato_digitado%'
                    
                ORDER BY
                    connumero;";
                    
        $sql    = pg_query($this->conn, $sql);        
        $result = null;

        while($rs = pg_fetch_array($sql)){
            $result[] = array("connumero" => $rs["connumero"], "clinome" => $rs["clinome"]);
        }

        return $result;
    }
    
    /**
     * Retorna os dados do cliente.
     * @param String $cliente 
     * @param int $cpf_cnpj
     * @return array ou null
     */
    public function getClientes($cliente, $cpf_cnpj = null){
    	
    	$cliente = addslashes($cliente);
        $sql = "SELECT
                    clioid,
                    trim(clinome) as clinome,
                    clino_cpf,
                    clino_cgc,
                    clitipo
                FROM 
                    clientes
                WHERE
                    clinome ILIKE '%$cliente%'
		AND clidt_exclusao is null 
";
        
        if($cpf_cnpj != null){
            $sql .= "AND
                        (clino_cpf = $cpf_cnpj OR clino_cgc = $cpf_cnpj);";
        } else{
            $sql .= " LIMIT 100;";
        }        
                    
        $sql    = pg_query($this->conn, $sql);
        $result = null;
        
        while($rs = pg_fetch_array($sql)){
            $result[] = array("clioid" => $rs["clioid"], "clinome" => $rs["clinome"], "clino_cpf" => $rs["clino_cpf"],
                              "clino_cgc" => $rs["clino_cgc"], "clitipo" => $rs["clitipo"]);
        }
        
        return $result;        
    }
    
    /**
     * Retorna true/false se a Obrigação
     * Financeira é do Tipo Cliente.
     * @param int $servico
     * @return boolean
     */
    public function verificarObrigacaoFinanceira($servico){
        $sql = "SELECT
                    obrcliente                    
                FROM
                    obrigacao_financeira
                WHERE
                    obroid = $servico;";
        
        $sql    = pg_query($this->conn, $sql);
        $result = false;
        
        if(pg_num_rows($sql) > 0){
            $result = pg_fetch_result($sql, 0, 'obrcliente');
            $result = ($result == 't') ? true : false;
        }
        
        return $result;
    }
    
    /**
     * Dados do Termo Aditivo.
     * @param int $termo_aditivo
     * @return array ou null
     */
    public function getDadosTermoAditivo($termo_aditivo){
        $sql = "SELECT
                    taseoid,
                    tasetasesoid,
                    clinome,
                    clitipo,
                    clino_cpf,
                    clino_cgc,
                    taseobservacao,
                    tasesituacao,
                    tasedt_validade
                FROM
                    termo_aditivo_servico
                LEFT OUTER JOIN
                    clientes ON clioid = taseclioid
                WHERE
                    taseoid = $termo_aditivo
                AND
                    tasedt_exclusao IS NULL;";
        
        $sql    = pg_query($this->conn, $sql);
        $result = null;
        
        if(pg_num_rows($sql) > 0){
            $result = array("taseoid" => pg_fetch_result($sql, 0, 'taseoid'), "tasetasesoid" => pg_fetch_result($sql, 0, 'tasetasesoid'),
                            "clinome" => pg_fetch_result($sql, 0, 'clinome'), "clitipo" => pg_fetch_result($sql, 0, 'clitipo'),
                            "clino_cpf" => pg_fetch_result($sql, 0, 'clino_cpf'), "clino_cgc" => pg_fetch_result($sql, 0, 'clino_cgc'),
                            "taseobservacao" => pg_fetch_result($sql, 0, 'taseobservacao'), "tasesituacao" => pg_fetch_result($sql, 0, 'tasesituacao'),
                            "validade" => pg_fetch_result($sql, 0, 'tasedt_validade'));
        }
        
        return $result;
    }
    
    /**
     * @param int $termo_aditivo
     * @return array ou null
     */
    public function getItensTermoAditivo($termo_aditivo){
        $sql = "SELECT
                    taseioid,
                    taseitaseoid,
                    taseiconnumero,
                    taseiobroid,
                    taseivalor_tabela,
                    taseivalor_negociado,
                    taseivalor_desconto,
                    taseireajuste
                FROM
                    termo_aditivo_servico_item
                WHERE
                    taseitaseoid = $termo_aditivo
                AND
                    taseidt_exclusao IS NULL;";
        
        $sql    = pg_query($this->conn, $sql);
        $result = null;
        
        while($rs = pg_fetch_array($sql)){
            $result[] = array("taseioid"             => $rs["taseioid"], 
            		          "taseitaseoid"         => $rs["taseitaseoid"], 
            		          "taseiconnumero"       => $rs["taseiconnumero"],
                              "taseiobroid"          => $rs["taseiobroid"], 
            		          "taseivalor_tabela"    => $rs["taseivalor_tabela"], 
            		          "taseivalor_negociado" => $rs["taseivalor_negociado"],
                              "taseivalor_desconto"  => $rs["taseivalor_desconto"],
            		          "taseireajuste"        => $rs["taseireajuste"]);
        }
        
        return $result;
    }
    
    /**
     * Incluí um novo termo aditivo de serviço
     * @param int $id_status
     * @param int $id_cliente
     * @param int $id_usuario
     * @param String $observacao
     * @param String $situacao
     * @return int primary_key
     */
    public function inserirTermoAditivoServico($id_status, $id_cliente, $id_usuario, $observacao, $situacao, $validade){
        $sql = "INSERT INTO termo_aditivo_servico
                    (tasedt_cadastro, tasetasesoid, taseclioid, taseusuoid, taseobservacao, tasesituacao, tasedt_validade)
                VALUES
                    (now(), $id_status, $id_cliente, $id_usuario, '$observacao', '$situacao', $validade)
                RETURNING
                    taseoid;";
                    
        $sql    = pg_query($this->conn, $sql);
        $result = pg_fetch_result($sql,"taseoid");
        //pg_result_error($result);
                    
        //retorna a primary key que acabou de ser criada. 
        return (int) $result; 
    }
    

    public function verificaContratoExiste($servico, $cliente, $contrato) {
        $sql = "SELECT
                    count(*) as existe
                FROM
                    contrato, clientes
                WHERE
                    conclioid = clioid
                AND
                    condt_exclusao IS NULL
                AND
                    (coneqcoid IN (SELECT
                                        obrceqcoid
                                    FROM
                                        obrigacao_financeira_classe
                                    WHERE
                                        obrceqcoid = coneqcoid
                                    AND
                                        obrcdt_exclusao IS NULL
                                    AND
                                        obrcobroid = $servico
                                    )
                    OR (SELECT
                                        count(*)
                                        FROM
                                            obrigacao_financeira_classe
                                        WHERE
                                            obrcdt_exclusao IS NULL
                                        AND
                                            obrcobroid = $servico
                                        )=0
                    )
                AND
                    (conno_tipo IN (SELECT
                                        obrttpcoid
                                    FROM
                                        obrigacao_financeira_tipo_contrato
                                    WHERE
                                        obrttpcoid = conno_tipo
                                    AND
                                        obrtdt_exclusao IS NULL
                                    AND
                                        obrtobroid = $servico
                                    )
                    OR  (SELECT count(*) 
                                            FROM
                                                obrigacao_financeira_tipo_contrato
                                            WHERE
                                                obrtdt_exclusao IS NULL
                                            AND
                                                obrtobroid = $servico
                                        )=0
                    )
                AND
                    concsioid = 1
                AND
                    conclioid = $cliente 
                AND
                    (CASE WHEN conequoid > 0 THEN
                        (SELECT
                            true
                        FROM
                            obrigacao_financeira_projeto, equipamento, equipamento_versao
                        WHERE
                            conequoid = equoid
                        AND
                            equeveoid = eveoid
                        AND
                            eveprojeto IN(obrpeproid)
                        AND
                            obrpdt_exclusao IS NULL
                        AND
                            obrpobroid = $servico
                        ) 
                        OR ((SELECT count(*) FROM obrigacao_financeira_projeto WHERE obrpdt_exclusao IS NULL AND obrpobroid = $servico) = 0)
                    ELSE
                        (SELECT true) END
                    ) 
                   AND connumero = $contrato;";
                    
        $sql = pg_query($this->conn, $sql);        
        
        $existe = 0;

        if(pg_num_rows($sql)) {
            $existe = pg_fetch_result($sql, 0, 'existe');
        }

        return $existe;
    }

    /**
     * Incluí um novo item aditivo.
     * @param int $id_termo
     * @param int $num_contrato
     * @param int $id_servico
     * @param float $valor_tabela
     * @param float $valor_negociado
     * @param float $desconto
     * @return int primary_key
     */
    public function inserirItemAditivo($id_termo, $num_contrato, $id_servico, $valor_tabela, $valor_negociado, $desconto, $tipo_reajuste){



        $sql = "INSERT INTO termo_aditivo_servico_item
                    (taseitaseoid, taseiconnumero, taseiobroid, taseidt_cadastro, taseivalor_tabela, taseivalor_negociado, taseivalor_desconto, taseireajuste)
                VALUES
                    ($id_termo, $num_contrato, $id_servico, now(), '$valor_tabela', '$valor_negociado', '$desconto', $tipo_reajuste)
                RETURNING
                    taseioid;";
                    
        $sql    = pg_query($this->conn, $sql);
        $result = pg_fetch_result($sql, "taseioid");
        
        return (int) $result;
    }

    public function getValoresLimiteObrigacao($idServico) {
        $sql = "SELECT
                    obrvl_minimo,
                    obrvl_maximo
                FROM
                    obrigacao_financeira
                WHERE
                    obroid = $idServico";

        $res = pg_query($this->conn, $sql);
        
        $retorno = null;

        if(pg_num_rows($res) > 0) {
            $retorno = array('obrvl_minimo' => pg_fetch_result($res, 0, 'obrvl_minimo'),
                'obrvl_maximo' => pg_fetch_result($res, 0, 'obrvl_maximo'));
        }

        return $retorno;
    }
    
    /**
     * Retorna a PLACA e o CHASSI do veículo.
     * @param int $contrato
     * @return array ou null
     */
    public function getDadosVeiculo($contrato){
        $sql = "SELECT
                    veiplaca,
                    veichassi
                FROM
                    veiculo
                INNER JOIN
                    contrato ON conveioid = veioid
                WHERE
                    connumero = $contrato;";
                    
        $sql    = pg_query($this->conn, $sql);
        $result = null;
        
        if(pg_num_rows($sql) > 0){
            $result = array("veiplaca" => pg_fetch_result($sql, 0, 'veiplaca'), "veichassi" => pg_fetch_result($sql, 0, 'veichassi'));
        }
        
        return $result;
    }
    
    /**
     * Remove o item aditivo
     * @param int $id_item
     * @return affected_rows
     */
    public function removerItemAditivo($id_item){
        $sql = "UPDATE
                    termo_aditivo_servico_item
                SET
                    taseidt_exclusao = now()
                WHERE
                    taseioid = $id_item;";
                    
        $result = pg_query($this->conn, $sql);
        $result = pg_affected_rows($result);
        
        return (int) $result;
    }
    
    /**
     * Exclui o Termo Aditivo.
     * @param int $id_termo
     * @return affected_rows
     */
    public function excluirTermoAditivoServico($id_termo, $id_usuario){
        $sql = "UPDATE
                    termo_aditivo_servico
                SET
                    tasedt_exclusao = now(),
                    taseusuoid_exclusao = $id_usuario
                WHERE
                    taseoid = $id_termo;";
                    
        $result = pg_query($this->conn, $sql);
        $result = pg_affected_rows($result);
        
        return (int) $result;
    }
    
    /**
     * Altera as informações do Termo Aditivo de Serviço.
     * @param int $id_status
     * @param int $id_cliente
     * @param int $id_usuario
     * @param String $observacao
     * @param String $situacao
     * @param int $id_usuario
     * @return affected_rows
     */
    public function alterarTermoAditivoServico($id_status, $id_usuario, $id_termo, $id_cliente = null, $observacao = "", $situacao = "", $validade = 'null'){
        $sql = "UPDATE
                    termo_aditivo_servico
                SET
                    tasetasesoid = $id_status,
                    tasedt_validade = $validade,
                    taseusuoid = $id_usuario";
        
        if($id_cliente != null){
            $sql .= " ,taseclioid = $id_cliente";
        }
        if($observacao != ""){
            $sql .= " ,taseobservacao = '$observacao'";
        }
        if($situacao != ""){
            $sql .= " ,tasesituacao = '$situacao'";
        }             
                    
        $sql .= " WHERE
                    taseoid = $id_termo;";
                    
        $result = pg_query($this->conn, $sql);
        
        $result = pg_affected_rows($result);
        
        return (int) $result; 
    }
    
    /**
     * @param int $id_cliente
     * @param int $id_servico
     * @param float $valor_negociado
     * @param String $demonstracao
     * @param String $cortesia
     * @return primary_key
     */
    public function inserirClienteObrigacaoFinanceira($id_cliente, $id_servico, $valor_negociado, $demonstracao, $cortesia){
        $sql = "INSERT INTO cliente_obrigacao_financeira
                    (clioclioid, clioobroid, cliovl_obrigacao, cliodt_inicio, cliono_periodo_mes, cliodemonstracao, cliodemonst_validade, cliocortesia)
                VALUES
                    ($id_cliente, $id_servico, $valor_negociado, now(), 1, '$demonstracao', now()::date + 90, '$cortesia')
                RETURNING
                    cliooid;";
        
        $sql    = pg_query($this->conn, $sql);
        $result = pg_fetch_result($sql, "cliooid");
        
        return (int) $result;
    }
    
    /**
     * @param int $contrato
     * @param int $id_servico
     * @param float $valor_negociado
     * @param int $id_equipamento_classe
     * @return primary_key
     */
    public function inserirContratoObrigacaoFinanceira($contrato, $id_servico, $valor_negociado, $id_equipamento_classe){
        $sql = "INSERT INTO contrato_obrigacao_financeira 
                    (cofconoid, cofobroid, cofvl_obrigacao, cofdt_inicio , cofno_periodo_mes, cofeqcoid)
                VALUES
                    ($contrato, $id_servico, '$valor_negociado', now(), 1, $id_equipamento_classe)
                RETURNING
                    cofoid;";
                    
        $sql    = pg_query($this->conn, $sql);
        $result = pg_fetch_result($sql, "cofoid");
        
        return (int) $result;
    }
    
    /**
     * @param int $contrato
     * @return int
     */
    public function getIdEquipamentoClasse($contrato){
        $sql = "SELECT
                    coneqcoid
                FROM
                    contrato
                INNER JOIN
                    equipamento_classe ON eqcoid = coneqcoid
                WHERE
                    connumero = $contrato;";
                    
        $sql    = pg_query($this->conn, $sql);
        $result = pg_fetch_result($sql, 0, "coneqcoid");
        
        return (int) $result;
    }
    
    /**
     * @param int $id_servico
     * @return array ou null
     */
    public function getTipoObrigacao($id_servico){
        $sql = "SELECT
                    obroid
                    ,trim(obrtipo_obrigacao) as obrtipo_obrigacao
                    ,obrebtoid
                FROM
                    obrigacao_financeira
                WHERE
                    obrdt_exclusao IS NULL
                AND
                        obroid = $id_servico";
        
        $sql    = pg_query($this->conn, $sql);
        $result = null;
        
        if(pg_num_rows($sql) > 0){
             $result = array("obroid" => pg_fetch_result($sql, 0, "obroid"), "obrtipo_obrigacao" => pg_fetch_result($sql, 0, "obrtipo_obrigacao"),
                             "obrebtoid" => pg_fetch_result($sql, 0, "obrebtoid"));
        }
        
        return $result;
    }
    
    /**
     * @param int $beneficio
     * @param int $id_cliente
     * @return array ou null
     */
    public function getClienteBeneficio($beneficio, $id_cliente){
        $sql = "SELECT
                    clboid,
                    clbebtoid
                FROM
                    cliente_beneficio
                WHERE
                    clbdt_exclusao IS NULL
                AND
                    clbclioid = $id_cliente
                AND
                    clbebtoid = $beneficio;";
        
        $sql    = pg_query($this->conn, $sql);
        $result = null;
        
        if(pg_num_rows($sql) > 0){
             $result = array("clboid" => pg_fetch_result($sql, 0, "clboid"), "clbebtoid" => pg_fetch_result($sql, 0, "clbebtoid"));
        }
        
        return $result;
    }
    
    /**
     * @param int $beneficio
     * @param int $id_cliente
     * @return primary_key
     */
    public function inserirClienteBeneficio($beneficio, $id_cliente){
        $sql = "INSERT INTO cliente_beneficio
                    (clbebtoid, clbbstoid, clbclioid)
                VALUES
                    ($beneficio, 1, $id_cliente)
                RETURNING
                    clboid;";
        
        $sql    = pg_query($this->conn, $sql);
        $result = pg_fetch_result($sql, "clboid");
        
        return (int) $result;
    }
    
    /**
     * @param int $id_usuario
     * @param int $id_termo
     * @return primary_key
     */
    public function inserirContratoServico($id_usuario, $id_termo, $validade, $situacao){
        $sql = "INSERT INTO contrato_servico
                    (consconoid, consinstalacao, conssituacao, consusuoid, consobroid, consqtde, consvalor, consdt_validade)
                    (SELECT
                        taseiconnumero, now(), '$situacao', $id_usuario, taseiobroid, 1, taseivalor_negociado, $validade
                     FROM
                        termo_aditivo_servico_item
                     WHERE
                        taseidt_exclusao IS NULL
                     AND
                        taseiconnumero > 0
                     AND
                        taseitaseoid = $id_termo
                    )
                RETURNING
                    consoid;";
                    
        $sql    = pg_query($this->conn, $sql);
        $result = pg_fetch_result($sql, "consoid");
        
        return (int) $result;
    }
    

    /**
     * STI 81607 - 81607   Reajuste IGPM ou INPC 
     * 
     * @param INT $num_contrato
     * @param INT $tipo_reajuste 1-IGPM ;   2-INPC
     * @return number
     */
    public function atualizarTipoReajusteContrato($num_contrato, $tipo_reajuste){
    	
    	$sql = "UPDATE
				    contrato
				SET
				    conreajuste = $tipo_reajuste
			    WHERE
				    connumero = $num_contrato ";
    	
    	$result = pg_query($this->conn, $sql);
    	
    	$result = pg_affected_rows($result);
    	
    	return (int) $result;
    	 
    }
    
}
?>

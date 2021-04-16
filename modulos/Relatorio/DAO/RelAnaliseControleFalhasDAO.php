<?php

/**
 * 
 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
 * @since 08-02-2013
 * 
 */
class RelAnaliseControleFalhasDAO {

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function pesquisar() {

        /*
         * captura dados enviados via POST
         * armazena todos os campos de filtros em um array para validação
         */
        $serial = $_POST['serial'] ? $_POST['serial'] : null;
        $data_abertura_os_inicial = $_POST['data_abertura_os_inicial'] ? $_POST['data_abertura_os_inicial'] : null;
        $data_abertura_os_final = $_POST['data_abertura_os_final'] ? $_POST['data_abertura_os_final'] : null;
        $data_conclusao_os_inicial = $_POST['data_conclusao_os_inicial'] ? $_POST['data_conclusao_os_inicial'] : null;
        $data_conclusao_os_final = $_POST['data_conclusao_os_final'] ? $_POST['data_conclusao_os_final'] : null;
        $defeito_alegado = $_POST['defeito_alegado'] ? $_POST['defeito_alegado'] : null;
        $defeito_constatado = $_POST['defeito_constatado'] ? $_POST['defeito_constatado'] : null;
        $placa = $_POST['placa'] ? $_POST['placa'] : null;
        $tipo_os = $_POST['tipo_os'] ? $_POST['tipo_os'] : null;
        $data_entrada_lote_inicial = $_POST['data_entrada_lote_inicial'] ? $_POST['data_entrada_lote_inicial'] : null;
        $data_entrada_lote_final = $_POST['data_entrada_lote_final'] ? $_POST['data_entrada_lote_final'] : null;
        $nota_fiscal = $_POST['nota_fiscal'] ? $_POST['nota_fiscal'] : null;
        $data_emissao_nf_inicial = $_POST['data_emissao_nf_inicial'] ? $_POST['data_emissao_nf_inicial'] : null;
        $data_emissao_nf_final = $_POST['data_emissao_nf_final'] ? $_POST['data_emissao_nf_final'] : null;
        $defeito_lab = $_POST['defeito_lab'] ? $_POST['defeito_lab'] : null;
        $acao_lab = $_POST['acao_lab'] ? $_POST['acao_lab'] : null;
        $componente_afetado_lab = $_POST['componente_afetado_lab'] ? $_POST['componente_afetado_lab'] : null;
        $modelo_equipamento = $_POST['modelo_equipamento'] ? $_POST['modelo_equipamento'] : null;
        $versao_equipamento = $_POST['versao_equipamento'] ? $_POST['versao_equipamento'] : null;
        $data_entrada_lab_inicial = $_POST['data_entrada_lab_inicial'] ? $_POST['data_entrada_lab_inicial'] : null;
        $data_entrada_lab_final = $_POST['data_entrada_lab_final'] ? $_POST['data_entrada_lab_final'] : null;
        $data_saida_lab_inicial = $_POST['data_saida_lab_inicial'] ? $_POST['data_saida_lab_inicial'] : null;
        $data_saida_lab_final = $_POST['data_saida_lab_final'] ? $_POST['data_saida_lab_final'] : null;
        $modalidade = $_POST['modalidade'] ? $_POST['modalidade'] : null;
        $primeira_instalacao = $_POST['primeira_instalacao'] ? $_POST['primeira_instalacao'] : null;
        $telefone = $_POST['telefone'] ? $_POST['telefone'] : null;

        /*
         * RN6.5
         * Se os campos “Serial”, “Placa”, “Nota Fiscal” ou “Telefone” não forem preenchidos,
         * o usuário terá que selecionar o campo Data Entrada Lab.
         */
        if 
        ( 
            is_null($serial) 
            && is_null($placa) 
            && is_null($nota_fiscal) 
            && is_null($telefone)            
            && is_null($data_abertura_os_inicial)    
            && is_null($data_abertura_os_final)                
            && is_null($data_conclusao_os_inicial)
            && is_null($data_conclusao_os_final)                
            && is_null($data_emissao_nf_inicial)
            && is_null($data_emissao_nf_final)
        ) {
            if (is_null($data_entrada_lab_inicial) || is_null($data_entrada_lab_final)) {
                throw new RuntimeException('Preencher o campo Data Entrada Lab.');
            }
        }

        $sql = "
			SELECT 
				equoid 
				, equno_serie AS serial 
				, equno_fone AS telefone 
				, entnota AS nota_fiscal 
				, TO_CHAR(imobentrada, 'dd/mm/yyyy') AS emissao_nf 
				
				, ordoid AS ordem 
				, TO_CHAR(orddt_ordem, 'dd/mm/yyyy') AS data_abertura 
				, TO_CHAR(cmidata, 'dd/mm/yyyy') AS data_conclusao
				, otidescricao AS motivo_os 
				, ositobs AS obs 
			
				, connumero AS contrato	
				, eqcdescricao AS classe_contrato 
				, CASE WHEN conmodalidade = 'V' THEN 'Revenda' ELSE 'Locação' END AS modalidade 
				, TO_CHAR(condt_ini_vigencia, 'dd/mm/yyyy') AS inicio_vigencia 
				, clinome AS cliente 
				, CASE WHEN clitipo = 'F' THEN cliuf_res ELSE cliuf_com END AS uf_cliente 
				, CASE WHEN clitipo = 'F' THEN clicidade_res ELSE clicidade_com END AS cidade_cliente 
				, repnome AS representante 
				, endvuf AS uf_representante 
				, itlnome AS instalador 
			
				, tipvdescricao AS tipo_veiculo 
				, mcamarca AS marca_veiculo 
				, mlomodelo AS modelo_veiculo 
				, veino_ano AS ano_veiculo 
				, veichassi AS chassi 
				, veiplaca AS placa 
				, eprnome AS modelo_equipamento 
				, eveversao AS versao_equipamento 
				
				, otcdescricao AS causa
				, otodescricao AS ocorrencia
				, otsdescricao AS solucao
				, otadescricao AS componente
				, ifddescricao AS defeito_lab
				, ifadescricao AS acao_lab
				, ifcdescricao AS componente_afetado_lab
			
				, (SELECT osdfdescricao FROM ordem_servico_defeito WHERE ositosdfoid_alegado = osdfoid) AS defeito_alegado
				, (SELECT osdfdescricao FROM ordem_servico_defeito WHERE ositosdfoid_analisado = osdfoid) AS defeito_constatado
				, (SELECT oploperadora FROM operadora_linha INNER JOIN linha ON oploid = linoploid AND linnumero = equno_fone AND linaraoid = equaraoid AND linexclusao IS NULL) AS operadora
				, (SELECT TO_CHAR(hiedt_historico, 'dd/mm/yyyy') FROM historico_equipamento WHERE hieequoid = equoid AND hiemotivo = 'Entrada'  ORDER BY hiedt_historico DESC LIMIT 1) AS data_entrada_lote
                		, (SELECT TO_CHAR(hiedt_historico, 'dd/mm/yyyy') FROM historico_equipamento WHERE hieequoid = equoid AND hieeqsoid = 6 AND hieconnumero = connumero ORDER BY hiedt_historico DESC LIMIT 1) AS data_instalacao
				, (SELECT TO_CHAR(hiedt_historico, 'dd/mm/yyyy') FROM historico_equipamento WHERE hieequoid = equoid AND hieeqsoid = 10 AND hieconnumero = connumero ORDER BY hiedt_historico DESC LIMIT 1) AS data_retirada
				, CASE WHEN connumero = ( SELECT hieconnumero FROM historico_equipamento WHERE hieequoid = equoid AND hieeqsoid IN (6,10) ORDER BY hiedt_historico LIMIT 1) THEN 'Sim' ELSE 'Não' END AS primeira_instalacao
				
			FROM equipamento 
			INNER JOIN imobilizado ON imobpatrimonio = equpatrimonio 
			LEFT JOIN entrada ON entoid = imobentoid 
			
			INNER JOIN
                        (
                            SELECT 
                                ordequoid,
                                ordoid,
                                ordconnumero,
                                ordclioid,
                                ordveioid,
                                ordrelroid,
                                orditloid,
                                orddt_ordem,
                                connumero,
                                conmodalidade,
                                condt_ini_vigencia,
                                coneqcoid

                                FROM ordem_servico 
                                INNER JOIN contrato ON ordconnumero = connumero 
                        WHERE 
                        ordoid IN (
                              SELECT 
                                      ordoid

                              FROM
                                      contrato 
                              LEFT JOIN (
                                      SELECT 
                                              ordequoid,
                                              ordconnumero,
                                              MAX(ordoid) as ordoid
                                      FROM 
                                              ordem_servico
                                      GROUP BY 
                                              ordequoid, ordconnumero
                                      ) as os ON connumero = ordconnumero 
                        )
                              ) AS os ON ordequoid = equoid
                        
                        LEFT JOIN ( SELECT cmiord_serv, MAX(cmidata) as cmidata FROM comissao_instalacao GROUP BY cmiord_serv ) AS comissao_instalacao ON cmiord_serv = ordoid
                        
			INNER JOIN ordem_servico_item ON ositordoid = ordoid 
			INNER JOIN os_tipo_item ON otioid = ositotioid 
			
			INNER JOIN clientes ON clioid  = ordclioid 
			INNER JOIN equipamento_classe ON eqcoid = coneqcoid 
			INNER JOIN equipamento_versao ON eveoid = equeveoid 
			INNER JOIN equipamento_projeto ON eproid = eveprojeto 
			
			INNER JOIN veiculo ON veioid = ordveioid 
			INNER JOIN modelo ON mlooid = veimlooid 
			INNER JOIN marca ON mcaoid = mlomcaoid 
			INNER JOIN tipo_veiculo ON tipvoid = mlotipveioid 
			
			LEFT JOIN os_tipo_causa ON otcoid = ositotcoid 
			LEFT JOIN os_tipo_ocorrencia ON otooid = ositotooid 
			LEFT JOIN os_tipo_solucao ON otsoid = ositotsoid 
			LEFT JOIN os_tipo_componente_afetado ON otaoid = ositotaoid 
			
			LEFT JOIN controle_falha ON ctfeproid = eproid AND ctfositoid = ositoid 
			LEFT JOIN item_falha_defeito ON ifdoid = ctfifdoid 
			LEFT JOIN item_falha_acao ON ifaoid = ctfifaoid 
			LEFT JOIN item_falha_componente ON ifcoid = ctfifcoid 
			
			LEFT JOIN relacionamento_representante ON relroid = ordrelroid 
			LEFT JOIN representante ON repoid = relrrep_terceirooid 
			LEFT JOIN endereco_representante ON endvrepoid = repoid 
			
			LEFT JOIN instalador ON itloid = orditloid 
				
			WHERE 
                 otitipo = 'E'
                            
		";

        if (!is_null($serial)) {
            if (!is_numeric($serial))
                throw new RuntimeException('O campo Serial deve ser númerico');
            if (strlen($serial) > 15)
                throw new RuntimeException('O campo Serial deve conter no máximo 15 dígitos');
            $sql .= " AND equno_serie = '$serial'";
        }

        if (!is_null($data_abertura_os_inicial) && !is_null($data_abertura_os_final)) {
            if (!compara_duas_datas($data_abertura_os_inicial, $data_abertura_os_final, 1))
                throw new RuntimeException('A data inicial deve ser menor do que a data Final');
            if (diferencaDias($data_abertura_os_inicial, $data_abertura_os_final) > 90)
                throw new RuntimeException('O período da pesquisa não deve ultrapassar 3 meses');
            $sql .= " AND orddt_ordem BETWEEN '$data_abertura_os_inicial 00:00:00' AND '$data_abertura_os_final 23:59:59'";
        }

        if (!is_null($data_conclusao_os_inicial) && !is_null($data_conclusao_os_final)) {
            if (!compara_duas_datas($data_conclusao_os_inicial, $data_conclusao_os_final, 1))
                throw new RuntimeException('A data inicial deve ser menor do que a data Final');
            if (diferencaDias($data_conclusao_os_inicial, $data_conclusao_os_final) > 90)
                throw new RuntimeException('O período da pesquisa não deve ultrapassar 3 meses');
            $sql .= " AND cmidata BETWEEN '$data_conclusao_os_inicial 00:00:00' AND '$data_conclusao_os_final 23:59:59'";
        }

        if (!is_null($defeito_alegado)) {
            if (!is_numeric($defeito_alegado))
                throw new RuntimeException('O campo Defeito Alegado deve ser númerico');
            $sql .= " AND (SELECT osdfotdoid FROM ordem_servico_defeito WHERE osdfoid = ositosdfoid_alegado ) = $defeito_alegado";
        }

        if (!is_null($defeito_constatado)) {
            if (!is_numeric($defeito_constatado))
                throw new RuntimeException('O campo Defeito Constatado deve ser númerico');
            $sql .= " AND (SELECT osdfotdoid FROM ordem_servico_defeito WHERE osdfoid = ositosdfoid_analisado ) = $defeito_constatado";
        }

        if (!is_null($placa)) {
            if (strlen($nota_fiscal) > 15)
                throw new RuntimeException('O campo Placa deve conter no máximo 15 dígitos');
            $sql .= " AND veiplaca = '$placa'";
        }

        /*
         * RN6.3
         * Trazer na combo Tipo OS os valores: Ambos, Retirada e Assistência Técnica.
         * <os_tipo> ostdescricao where ostoid = 3 e 4.
         */
        if (!is_null($tipo_os)) {
            $sql .= " AND otiostoid = $tipo_os";
        } else {
            $sql .= " AND otiostoid IN (3, 4)";
        }

        if (!is_null($data_entrada_lote_inicial) && !is_null($data_entrada_lote_final)) {
            if (!compara_duas_datas($data_entrada_lote_inicial, $data_entrada_lote_final, 1))
                throw new RuntimeException('A data inicial deve ser menor do que a data Final');
            if (diferencaDias($data_entrada_lote_inicial, $data_entrada_lote_final) > 90)
                throw new RuntimeException('O período da pesquisa não deve ultrapassar 3 meses');
            $sql .= " AND (SELECT COUNT(1) FROM historico_equipamento WHERE hiedt_historico BETWEEN '$data_entrada_lote_inicial 00:00:00' AND '$data_entrada_lote_final 23:59:59' AND hieequoid = equoid AND hiemotivo = 'Entrada' ) > 0";
            $sql .= " AND (SELECT COUNT(1) FROM historico_equipamento WHERE hiedt_historico > '$data_entrada_lote_final 23:59:59' AND hieequoid = equoid AND hiemotivo = 'Entrada' ) = 0";
        }

        if (!is_null($nota_fiscal)) {
            if (strlen($nota_fiscal) > 15)
                throw new RuntimeException('O campo Nota Fiscal deve conter no máximo 15 dígitos');
            $sql .= " AND entnota ILIKE '%$nota_fiscal%'";
        }

        if (!is_null($data_emissao_nf_inicial) && !is_null($data_emissao_nf_final)) {
            if (!compara_duas_datas($data_emissao_nf_inicial, $data_emissao_nf_final, 1))
                throw new RuntimeException('A data inicial deve ser menor do que a data Final');
            if (diferencaDias($data_emissao_nf_inicial, $data_emissao_nf_final) > 90)
                throw new RuntimeException('O período da pesquisa não deve ultrapassar 3 meses');
            $sql .= " AND imobentrada BETWEEN '$data_emissao_nf_inicial 00:00:00' AND '$data_emissao_nf_final 23:59:59'";
        }

        if (!is_null($defeito_lab)) {
            if (!is_numeric($defeito_lab))
                throw new RuntimeException('O campo Defeito Lab deve ser númerico');
            $sql .= " AND ifdoid = $defeito_lab";
        }

        if (!is_null($acao_lab)) {
            if (!is_numeric($acao_lab))
                throw new RuntimeException('O campo Ação Lab deve ser númerico');
            $sql .= " AND ifaoid = $acao_lab";
        }

        if (!is_null($componente_afetado_lab)) {
            if (!is_numeric($componente_afetado_lab))
                throw new RuntimeException('O campo Componente Afetado Lab deve ser númerico');
            $sql .= " AND ifcoid = $componente_afetado_lab";
        }

        if (!is_null($modelo_equipamento)) {
            if (!is_numeric($modelo_equipamento))
                throw new RuntimeException('O campo Modelo Equipamento deve ser númerico');
            $sql .= " AND eproid = $modelo_equipamento";
        }

        if (!is_null($versao_equipamento)) {
            if (!is_numeric($versao_equipamento))
                throw new RuntimeException('O campo Versão Equipamento deve ser númerico');
            $sql .= " AND eveoid = $versao_equipamento";
        }


        if (!is_null($data_entrada_lab_inicial) && !is_null($data_entrada_lab_final)) {
            if (!compara_duas_datas($data_entrada_lab_inicial, $data_entrada_lab_final, 1))
                throw new RuntimeException('A data inicial deve ser menor do que a data Final');
            if (diferencaDias($data_entrada_lab_inicial, $data_entrada_lab_final) > 90)
                throw new RuntimeException('O período da pesquisa não deve ultrapassar 3 meses');
            
            $result = $this->buscaEquipamentosDataEntradaLab($data_entrada_lab_inicial, $data_entrada_lab_final);
            unset($equoids); 
            if(pg_num_rows($result) > 0){
                while ($equoid = pg_fetch_assoc($result)){
                    $equoids[] = $equoid['hieequoid'];
                }            
                $equoids = implode(', ', $equoids);
                $sql .= " AND equoid IN ($equoids) ";
            }         
        }

        if (!is_null($data_saida_lab_inicial) && !is_null($data_saida_lab_final)) {
            if (!compara_duas_datas($data_saida_lab_inicial, $data_saida_lab_final, 1))
                throw new RuntimeException('A data inicial deve ser menor do que a data Final');
            if (diferencaDias($data_saida_lab_inicial, $data_saida_lab_final) > 90)
                throw new RuntimeException('O período da pesquisa não deve ultrapassar 3 meses');
            unset($equoids);

            $result = $this->buscaEquipamentosDataSaidaLab($data_saida_lab_inicial, $data_saida_lab_final);
            if(pg_num_rows($result) > 0){
                while ($equoid = pg_fetch_assoc($result)){
                    $equoids[] = $equoid['hieequoid'];
                }          
                $equoids = implode(', ', $equoids);
                $sql .= " AND equoid IN ($equoids) ";
            }

            //$sql .= " AND (SELECT COUNT(*) FROM historico_equipamento WHERE hiedt_historico BETWEEN '$data_saida_lab_inicial 00:00:00' AND '$data_saida_lab_final 23:59:59' AND hieequoid = equoid AND hieeqsoid = 3 AND hiemotivo <> 'Entrada') > 0";
            //$sql .= " AND (SELECT COUNT(1) FROM historico_equipamento WHERE hiedt_historico > '$data_saida_lab_final 23:59:59' AND hieequoid = equoid AND hieeqsoid = 3 AND hiemotivo <> 'Entrada') = 0";
        }

        if (!is_null($modalidade)) {
            if ($modalidade == 'V') {
                $sql .= " AND conmodalidade  = 'V'";
            } else {
                $sql .= " AND conmodalidade  <> 'V'";
            }
        }

        /*
         * RN6.1
         * Validar se é a primeira instalação do equipamento. Verificar se o contrato vinculado ao equipamento é o primeiro contrato. 
         * Caso seja, 1º Instalação = “Sim”, senão = “Não”.
         * Para validar essa informação-> Ir em cadastro-> Equipamento.
         * Digitar Serial. Verificar se existe mais de um contrato para esse equipamento no grid termo de adesão. 
         */
        if (!is_null($primeira_instalacao)) {
            $operador = $primeira_instalacao == 'S' ? '=' : '<>';
            $sql .= " AND connumero $operador ( SELECT hieconnumero FROM historico_equipamento WHERE hieequoid = equoid AND hieeqsoid IN (6,10) ORDER BY hiedt_historico LIMIT 1)";
        }

        if (!is_null($telefone)) {
            if (!is_numeric($telefone))
                throw new RuntimeException('O campo Telefone deve ser númerico');
            if (strlen($telefone) > 12)
                throw new RuntimeException('O campo Telefone deve conter no máximo 12 dígitos');
            $sql .= " AND equno_fone = '$telefone'";
        }

        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao pesquisar" . $sql);
        }

        return pg_fetch_all($res);
    }

    /**
     * RN 6.3
     */
    public function listarTiposOS() {

        $sql = "
		SELECT
			ostoid AS id, 
			ostdescricao AS descricao
		FROM os_tipo 
		WHERE ostoid IN (3, 4)
		ORDER BY ostdescricao
		";

        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao pesquisar tipo OS");
        }

        return $retorno = pg_fetch_all($res);
    }

    /**
     * RN 6.8
     */
    public function listarDefeitosAlegados() {

        $sql = "
		SELECT 
			otdoid AS id,
			otddescricao AS descricao
		FROM os_tipo_defeito
		WHERE otddt_exclusao IS NULL
		AND otdalegado = TRUE
		ORDER BY otddescricao
		";

        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao pesquisar defeitos alegados");
        }

        return $retorno = pg_fetch_all($res);
    }

    /**
     * RN 6.9
     */
    public function listarDefeitosConstatados() {

        $sql = "
		SELECT 
			otdoid AS id,
			otddescricao AS descricao
		FROM os_tipo_defeito
		WHERE otddt_exclusao IS NULL
		AND otdconstatado = TRUE
		ORDER BY otddescricao
		";

        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao pesquisar defeitos constatados");
        }

        return $retorno = pg_fetch_all($res);
    }

    /**
     * RN 6.11
     */
    public function listarDefeitosLab() {

        $sql = "
		SELECT 
			ifdoid AS id,
			ifddescricao AS descricao 
		FROM item_falha_defeito
		WHERE ifddt_exclusao IS NULL
		ORDER BY ifddescricao
		";

        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao pesquisar defeitos lab");
        }

        return $retorno = pg_fetch_all($res);
    }

    /**
     * RN 6.12
     */
    public function listarAcaoLab() {

        $sql = "
		SELECT 
			ifaoid AS id,
			ifadescricao AS descricao
		FROM item_falha_acao
		WHERE ifadt_exclusao IS NULL
		ORDER BY ifadescricao
		";

        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao pesquisar ação lab");
        }

        return $retorno = pg_fetch_all($res);
    }

    /**
     * RN 6.13
     */
    public function listarComponentesAfetadosLab() {

        $sql = "
		SELECT 
			ifcoid AS id,
			ifcdescricao AS descricao
		FROM item_falha_componente
		WHERE ifcdt_exclusao IS NULL
		ORDER BY ifcdescricao
		";

        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao pesquisar componentes afetados");
        }

        return $retorno = pg_fetch_all($res);
    }

    /**
     * RN 6.14
     */
    public function listarModelosEquipamentos() {

        $sql = "
		SELECT 
			eproid AS id,
			eprnome AS descricao
		FROM equipamento_projeto
		ORDER BY eprnome
		";

        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao pesquisar modelos de equipamentos");
        }

        return $retorno = pg_fetch_all($res);
    }

    /**
     * RN 6.15
     */
    public function listarVersoesEquipamentos($modelo) {

        if (!is_numeric($modelo))
            throw new ErrorException('O modelo informado deve ser numérico');

        $sql = "
		SELECT 
			eveoid as id,
			eveversao as descricao
		FROM equipamento_versao
		WHERE evedt_exclusao IS NULL
		AND eveprojeto = $modelo
		ORDER BY eveversao
		";

        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao pesquisar versões equipamentos");
        }

        return $retorno = pg_fetch_all($res);
    }


    public function buscaDataEntradaLab($equipamento = null, $contrato = null, $data_ini = null, $data_fin = null) {

        $filtro = array();

        if (!empty($contrato)) {
            $filtro['contrato'] = " AND hieconnumero = $contrato ";
        }

        if (!empty($data_ini) && !empty($data_fin)) {
            $filtro['data'] = " AND hiedt_historico BETWEEN '$data_ini 00:00:00' AND '$data_fin 23:59:59' ";
        }

        $sql = "
                SELECT
                    dt_entrada_lab
                FROM 
                (
                    SELECT
                        hieeqsoid AS status,
                        LEAD(hieeqsoid) OVER (window_retirada) AS status_anterior,
                        to_char(hiedt_historico, 'DD/MM/YYYY') dt_entrada_lab,
                        LEAD(to_char(hiedt_historico, 'DD/MM/YYYY')) OVER (window_retirada) AS dt_retirada_anterior,
                        hieequoid
                    FROM 
                    (
                        SELECT 
                            hieequoid, 
                            hiedt_historico, 
                            hieeqsoid
                        FROM 
                            historico_equipamento 
                        WHERE 
                            hieequoid = $equipamento 
                            AND ( ( hieeqsoid IN (6, 10) " . $filtro['contrato'] . " ) OR ( hieeqsoid IN (19) " . $filtro['data'] . " ) )					
                        ORDER BY 
                            hiedt_historico DESC
                    ) as n 
                    WINDOW window_retirada AS (PARTITION BY n.hieequoid ORDER BY n.hiedt_historico DESC)
                    ORDER BY n.hiedt_historico DESC
                ) as aux1
                INNER JOIN equipamento ON (hieequoid = equoid)   
                WHERE 
                    status = 19
                    AND status_anterior IN (6, 10) 
                LIMIT 1
            ";
        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao pesquisar Data Entrada Lab.");
        }

        return $retorno = pg_fetch_assoc($res);
    }

    public function buscaDataSaidaLab($equipamento = null, $data_entrada_lab = null, $data_ini = null, $data_fin = null) {

        $filtro = array();

        if (!empty($data_ini) && !empty($data_fin)) {
            $filtro['data'] = " AND dt_saida_lab BETWEEN '$data_ini' AND '$data_fin' ";
        }

        $sql = "
                SELECT
                    dt_saida_lab
                FROM 
                (
                    SELECT
                        hieeqsoid AS status,
                        LEAD(hieeqsoid) OVER (window_retirada) AS status_anterior,
                        to_char(hiedt_historico, 'DD/MM/YYYY') dt_saida_lab,
                        LEAD(to_char(hiedt_historico, 'DD/MM/YYYY')) OVER (window_retirada) AS dt_retirada_anterior,
                        hieequoid
                    FROM 
                    (
                        SELECT 
                            hieequoid, 
                            hiedt_historico, 
                            hieeqsoid
                        FROM 
                            historico_equipamento 
                        WHERE 
                            hieequoid = $equipamento 
                            AND ( ( hieeqsoid IN (3, 20, 24) AND hiedt_historico >= '$data_entrada_lab') )                                                                        
                        ORDER BY 
                            hiedt_historico ASC
                    ) AS n 
                    WINDOW window_retirada AS (PARTITION BY n.hieequoid ORDER BY n.hiedt_historico DESC)
                    ORDER BY n.hiedt_historico ASC
                ) as aux1
                INNER JOIN equipamento ON (hieequoid = equoid)   
                WHERE 
                    status = 3
                    AND status_anterior IN (3, 20, 24) 
                    " . $filtro['data'] . "
                LIMIT 1
            ";
        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao pesquisar Data Saída Lab.");
        }

        return $retorno = pg_fetch_assoc($res);
    }

    public function buscaEquipamentosDataEntradaLab($data_entrada_lab_inicial, $data_entrada_lab_final) {

        $sql = "
            DROP TABLE IF EXISTS temp_table_hisequ;

            CREATE TEMPORARY TABLE temp_table_hisequ AS
            SELECT
                aux_historico_equipamento.hieequoid
            FROM
            (
                SELECT
                    sub_historico_equipamento.hieeqsoid AS status,
                    LEAD(sub_historico_equipamento.hieeqsoid) OVER (window_retirada) AS status_anterior,
                    TO_CHAR(sub_historico_equipamento.hiedt_historico, 'DD/MM/YYYY') AS dt_entrada_lab,
                    LEAD(TO_CHAR(sub_historico_equipamento.hiedt_historico, 'DD/MM/YYYY')) OVER (window_retirada) AS dt_retirada_anterior,
                    sub_historico_equipamento.hieequoid
                FROM
                    (
                        SELECT
                            historico_equipamento.hieequoid,
                            historico_equipamento.hiedt_historico,
                            historico_equipamento.hieeqsoid
                        FROM
                            historico_equipamento
                        WHERE
                        (
                            (
                                historico_equipamento.hieeqsoid IN (6, 10)
                                AND historico_equipamento.hieconnumero IS NOT NULL
                            ) OR (
                                historico_equipamento.hieeqsoid = 19
                                AND historico_equipamento.hiedt_historico BETWEEN '$data_entrada_lab_inicial 00:00:00' AND '$data_entrada_lab_final 23:59:59'
                            )
                        )
                    ) sub_historico_equipamento
                WINDOW
                    window_retirada AS (PARTITION BY sub_historico_equipamento.hieequoid ORDER BY sub_historico_equipamento.hiedt_historico DESC)

            ) aux_historico_equipamento
            WHERE
                aux_historico_equipamento.status = 19
                AND aux_historico_equipamento.status_anterior IN (6, 10);

            SELECT hieequoid FROM temp_table_hisequ;
        ";
        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao pesquisar Equipamentos.");
        }

        return $res;
    }
    
    
    public function buscaEquipamentosDataSaidaLab($data_saida_lab_inicial, $data_saida_lab_final) {
        
        $sql = "
            DROP TABLE IF EXISTS temp_table_hisequ;

            CREATE TEMPORARY TABLE temp_table_hisequ AS
            SELECT
                aux_historico_equipamento.hieequoid
            FROM
            (
                SELECT
                    sub_historico_equipamento.hieeqsoid AS status,
                    LEAD(sub_historico_equipamento.hieeqsoid) OVER (window_retirada) AS status_anterior,
                    TO_CHAR(sub_historico_equipamento.hiedt_historico, 'DD/MM/YYYY') AS dt_entrada_lab,
                    LEAD(TO_CHAR(sub_historico_equipamento.hiedt_historico, 'DD/MM/YYYY')) OVER (window_retirada) AS dt_retirada_anterior,
                    sub_historico_equipamento.hieequoid
                FROM
                    (
                        SELECT
                            historico_equipamento.hieequoid,
                            historico_equipamento.hiedt_historico,
                            historico_equipamento.hieeqsoid
                        FROM
                            historico_equipamento
                        WHERE
                        (
                            (
                                historico_equipamento.hieeqsoid IN (20, 24)                              
                            ) OR (
                                historico_equipamento.hieeqsoid = 3
                                AND historico_equipamento.hiedt_historico BETWEEN '$data_saida_lab_inicial 00:00:00' AND '$data_saida_lab_final 23:59:59'
                            )
                        )
                    ) sub_historico_equipamento
                WINDOW
                    window_retirada AS (PARTITION BY sub_historico_equipamento.hieequoid ORDER BY sub_historico_equipamento.hiedt_historico DESC)

            ) aux_historico_equipamento
            WHERE
                aux_historico_equipamento.status = 3
                AND aux_historico_equipamento.status_anterior IN (20, 24);

            SELECT hieequoid FROM temp_table_hisequ;    
        ";
        
        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao pesquisar Equipamentos.");
        }

        return $res;
    }

}
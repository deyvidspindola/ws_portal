<?php

/**
 * Classe CadRequisicaoViagemDAO.
 * Camada de modelagem de dados.
 *
 * @package  Principal
 * @author   Ricardo Bonfim <ricardo.bonfim@meta.com.br>
 *
 */
class CadRequisicaoViagemDAO {

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
     * Método para realizar a pesquisa de varios registros
     * @param stdClass $parametros Filtros da pesquisa
     * @return array
     * @throws ErrorException
     */
    public function pesquisar(stdClass $parametros, $paginacao = null, $ordenacao = 'adicadastro') {

        $retorno = array();

        $sql = "
            SELECT DISTINCT
                adioid,
                adicadastro,
                to_char(adicadastro,'DD/MM/YYYY') AS data,
                (CASE
					WHEN adittipo_solicitacao = 'L'
					THEN COALESCE(SUM(adigvalor_unitario),0)::NUMERIC(10, 2)
					ELSE COALESCE(adivalor,0)::NUMERIC(10, 2)
				END) AS adivalor,
                CASE
                    WHEN adittipo_solicitacao = 'A' THEN 'Adiantamento'
                    WHEN adittipo_solicitacao = 'C' THEN 'Combustível - ticket car'
                    WHEN adittipo_solicitacao = 'L' THEN 'Reembolso'
                END AS adittipo_solicitacao,
                adistatus_solicitacao as status,
                cd_usuario,
                nm_usuario,
                forfornecedor,
                adiusuoid_aprovacao,
                adicntoid
            FROM
                adiantamento
                INNER JOIN adiantamento_tipo ON adioid = aditadioid AND aditexclusao IS NULL
                LEFT JOIN adiantamento_gastos ON adigadioid = adioid
                INNER JOIN fornecedores ON adiforoid = foroid
                INNER JOIN usuarios ON usuforoid = foroid
            WHERE
                adiexclusao IS NULL";

        if (isset($parametros->empresa) && !empty($parametros->empresa)) {
            $sql .= " AND
                        adiempresa = " . intval($parametros->empresa);
        }

        if (isset($parametros->centroCusto) && !empty($parametros->centroCusto)) {
            $sql .= " AND
                        adicntoid = " . intval($parametros->centroCusto);
        }

        if (isset($parametros->statusSolicitacao) && !empty($parametros->statusSolicitacao)) {
            $sql .= " AND
                        adistatus_solicitacao = '" . pg_escape_string($parametros->statusSolicitacao) . "'";
        } else {
            $sql .= " AND
                        adistatus_solicitacao in ('A','C','P','S','R','F','D')";
        }

        if (isset($parametros->tipoRequisicao) && !empty($parametros->tipoRequisicao)) {
            $sql .= " AND
                        adittipo_solicitacao = '" . pg_escape_string($parametros->tipoRequisicao) . "'";
        } else {
            $sql .= " AND
                        adittipo_solicitacao in ('A','C', 'L') ";
        }

        if (isset($parametros->numeroRequisicao) && trim($parametros->numeroRequisicao) != '') {
            $sql .= " AND
                        adioid = " . intval($parametros->numeroRequisicao);
        }

        if (isset($parametros->solicitante) && trim($parametros->solicitante) != '') {
            $sql .= " AND
                        forfornecedor ilike '%" . pg_escape_string($parametros->solicitante) . "%'";
        }

        if (empty($ordenacao)) {
            $ordenacao = 'adicadastro';
        }
		
        $sql .= "
			GROUP BY
				adioid, adittipo_solicitacao, cd_usuario, nm_usuario, forfornecedor
            ORDER BY
                " . $ordenacao . ", adioid
        ";

        if (isset($paginacao->limite) && isset($paginacao->offset)) {
            $sql.= "
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "
            ";
        }
        
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    /**
     * Método para realizar a pesquisa de apenas um registro.
     *
     * @param int $id Identificador único do registro
     * @return stdClass
     * @throws ErrorException
     */
    public function pesquisarPorID($idRequisicao) {

        $retorno = new stdClass();

        $sql = "
            SELECT
                aditoid AS id_tipo_requisicao,
                adiusuoid AS solicitante,
                (SELECT nm_usuario FROM usuarios WHERE cd_usuario = adiusuoid) AS nome_solicitante,
                adiempresa AS empresa,
                adicntoid AS centro_custo,
                (SELECT cntconta FROM centro_custo WHERE cntoid = adicntoid) AS nome_centro_custo,
                adimotivo AS justificativa,
                adittipo_solicitacao AS tipo_requisicao,
                aditrproid AS projeto,
                aditviagem_idavolta AS ida_volta,
                TO_CHAR(adidtsaida,'DD/MM/YYYY') AS dt_partida,
                TO_CHAR(adidtchegada,'DD/MM/YYYY') AS dt_retorno,
                aditveiplaca AS placa,
                (SELECT cidestoid FROM cidade WHERE cidoid = aditviagem_origem) AS estado_origem,
                aditviagem_origem AS cidade_origem,
                (SELECT ciddescricao FROM cidade WHERE cidoid = aditviagem_origem) AS nome_cidade_origem,
                (SELECT cidestoid FROM cidade WHERE cidoid = aditviagem_destino) AS estado_destino,
                aditviagem_destino AS cidade_destino,
                (SELECT ciddescricao FROM cidade WHERE cidoid = aditviagem_destino) AS nome_cidade_destino,
                adikm AS distancia,
                TO_CHAR(adidt_limite_pagamento,'DD/MM/YYYY') AS dt_credito,
                adivalor AS valor_adiantamento,
                adiusuoid_aprovacao AS aprovador,
                (SELECT nm_usuario FROM usuarios WHERE cd_usuario = adiusuoid_aprovacao) AS nome_aprovador,
                adistatus_solicitacao as status_requisicao
            FROM
                adiantamento
                INNER JOIN adiantamento_tipo ON aditadioid = adioid
            WHERE
                adioid = " . intval($idRequisicao);

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        // Campos principais
        $retorno->centroCusto = $retorno->centro_custo;
        $retorno->nomeCentroCusto = $retorno->nome_centro_custo;
        $retorno->tipoRequisicao = $retorno->tipo_requisicao;
        // Campos do tipo da requisição = 'Adiantamento'
        $retorno->dataCredito = $retorno->dt_credito;
        // Campos do tipo da requisição = 'Combustivel'
        $retorno->idaVolta = $retorno->ida_volta;
        $retorno->dtPartida = $retorno->dt_partida;
        $retorno->dtRetorno = $retorno->dt_retorno;
        $retorno->placaVeiculo = $retorno->placa;
        $retorno->estadoOrigem = $retorno->estado_origem;
        $retorno->estadoDestino = $retorno->estado_destino;
        $retorno->cidadeOrigem = $retorno->cidade_origem;
        $retorno->nomeCidadeOrigem = $retorno->nome_cidade_origem;
        $retorno->cidadeDestino = $retorno->cidade_destino;
        $retorno->nomeCidadeDestino = $retorno->nome_cidade_destino;
        // Campos para ambos os tipos de requisição
        $retorno->idRequisicao = $idRequisicao;
        $retorno->idTipoRequisicao = $retorno->id_tipo_requisicao;
        $retorno->valorAdiantamento = $retorno->valor_adiantamento;
        $retorno->statusRequisicao = $retorno->status_requisicao;
        $retorno->nomeSolicitante = $retorno->nome_solicitante;
        $retorno->nomeAprovador = $retorno->nome_aprovador;

        
        
        return $retorno;
    }

    /**
     * Responsável para inserir um registro no banco de dados.
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function inserirRequisicao(stdClass $dados) {


        if (isset($dados->tipoRequisicaoReembolso) && $dados->tipoRequisicaoReembolso == true) {
            $statusSolicitacao = 'R';
        } else {
            $statusSolicitacao = 'P';
        }
        
        $sql = "
            INSERT INTO adiantamento (
                aditipo,
                adivalor,
                adinome,
                adicpf,
                adimotivo,
                adibanco,
                adiagencia,
                adidigito_agencia,
                adicc,
                adidigito_cc,
                adiusuoid,
                aditipo_pessoa,
                adidtsaida,
                adidtchegada,
                adikm,
                adistatus_solicitacao,
        ";
        if (!empty($dados->fornecedor->foroid) && !empty($dados->fornecedor->usudepoid)) {
            $sql.= "
                    adiforoid,
                    adidepoid,
            ";
        }
        $sql.= "
                adiempresa,
                adicntoid,
                adicadastro,
                adidt_limite_pagamento,
                adiusuoid_aprovacao
            ) VALUES (
                '" . 'V' . "',
                " . $dados->valorAdiantamento . ",
                '" . $dados->fornecedor->forfornecedor . "',
                '" . $dados->fornecedor->fordocto . "',
                '" . $dados->justificativa . "',
                " . intval($dados->fornecedor->forbanco) . ",
                '" . intval($dados->fornecedor->foragencia) . "',
                '" . substr($dados->fornecedor->fordigito_agencia, 0, 2) . "',
                " . intval($dados->fornecedor->forconta) . ",
                '" . substr($dados->fornecedor->fordigito_conta, 0, 2) . "',
                " . $dados->solicitante . ",
                '" . $dados->fornecedor->fortipo . "',
                " . ($dados->dtPartida != '' ? ("'" . $dados->dtPartida . "'") : 'NULL') . ",
                " . ($dados->dtRetorno != '' ? ("'" . $dados->dtRetorno . "'") : 'NULL') . ",
                " . ($dados->distancia != '' ? $dados->distancia : 'NULL') . ",
                '" . $statusSolicitacao . "',
        ";
        if (!empty($dados->fornecedor->foroid) && !empty($dados->fornecedor->usudepoid)) {
            $sql.= "
                    " . $dados->fornecedor->foroid . ",
                    " . $dados->fornecedor->usudepoid . ",
            ";
        }
        $sql.= "
                " . $dados->empresa . ",
                " . $dados->centroCusto . ",
                " . 'NOW()' . ",
                " . ($dados->dataCredito != '' ? ("'" . $dados->dataCredito . "'") : 'NULL') . ",
                " . $dados->aprovador . "
            ) RETURNING adioid"; 



        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno->adioid;
    }

    public function inserirTipoRequisicao($dados) {

        $sql = "
            INSERT INTO adiantamento_tipo (
                aditadioid,
                adittipo_solicitacao,
                aditviagem_dt_partida,
                aditviagem_dt_retorno,
                aditviagem_origem,
                aditviagem_destino,
                aditveiplaca,
                aditadiantamento_banco,
                aditadiantamento_agencia,
                aditadiantamento_digito_agencia,
                aditadiantamento_cc,
                aditadiantamento_digito_cc,
                aditviagem_idavolta,
                aditkm_calculada,
                aditrproid
            ) VALUES (
                " . $dados->idRequisicao . ",
                '" . $dados->tipoRequisicao . "',
                " . ($dados->dtPartida != '' ? ("'" . $dados->dtPartida . "'") : 'NULL') . ",
                " . ($dados->dtRetorno != '' ? ("'" . $dados->dtRetorno . "'") : 'NULL') . ",
                " . ($dados->cidadeOrigem != '' ? ("'" . $dados->cidadeOrigem . "'") : 'NULL') . ",
                " . ($dados->cidadeDestino != '' ? ("'" . $dados->cidadeDestino . "'") : 'NULL') . ",
                " . ($dados->placaVeiculo != '' ? ("'" . $dados->placaVeiculo . "'") : 'NULL') . ",
                " . intval($dados->fornecedor->forbanco) . ",
                " . intval($dados->fornecedor->foragencia) . ",
                " . intval($dados->fornecedor->fordigito_agencia) . ",
                " . intval($dados->fornecedor->forconta) . ",
                " . intval($dados->fornecedor->fordigito_conta) . ",
                " . ($dados->idaVolta != '' ? ("'" . $dados->idaVolta . "'") : 'NULL') . ",
                " . ($dados->distancia != '' ? ("'" . $dados->distancia . "'") : 'NULL') . ",
                " . ($dados->projeto != '' ? $dados->projeto : 'NULL') . "
            )";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    /**
     * Responsável por atualizar os registros
     * @param stdClass $dados Dados a serem gravados
     * @return boolean
     * @throws ErrorException
     */
    public function atualizarRequisicao(stdClass $dados) {

        $sql = "
            UPDATE adiantamento SET
                aditipo = '" . 'V' . "',
                adivalor = " . $dados->valorAdiantamento . ",
                adinome = '" . $dados->fornecedor->forfornecedor . "',
                adicpf = '" . $dados->fornecedor->fordocto . "',
                adimotivo = '" . $dados->justificativa . "',
                adibanco = " . $dados->fornecedor->forbanco . ",
                adiagencia = '" . $dados->fornecedor->foragencia . "',
                adidigito_agencia = '" . $dados->fornecedor->fordigito_agencia . "',
                adicc = " . $dados->fornecedor->forconta . ",
                adidigito_cc = '" . $dados->fornecedor->fordigito_conta . "',
                adiusuoid = " . $dados->solicitante . ",
                aditipo_pessoa = '" . $dados->fornecedor->fortipo . "',
                adidtsaida = " . ($dados->dtPartida != '' ? ("'" . $dados->dtPartida . "'") : 'NULL') . ",
                adidtchegada = " . ($dados->dtRetorno != '' ? ("'" . $dados->dtRetorno . "'") : 'NULL') . ",
                adikm = " . ($dados->distancia != '' ? ($dados->distancia) : 'NULL') . ",
                adistatus_solicitacao = '" . 'P' . "',
                adiforoid = " . $dados->fornecedor->foroid . ",
                adidepoid = " . $dados->fornecedor->usudepoid . ",
                adiempresa = " . $dados->empresa . ",
                adicntoid = " . $dados->centroCusto . ",
                adidt_limite_pagamento = " . ($dados->dataCredito != '' ? ("'" . $dados->dataCredito . "'") : 'NULL') . ",
                adiusuoid_aprovacao = " . $dados->aprovador . "
            WHERE
                adioid = " . $dados->idRequisicao;

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    public function atualizarTipoRequisicao(stdClass $dados) {
        $sql = "
            UPDATE adiantamento_tipo SET
                adittipo_solicitacao = '" . $dados->tipoRequisicao . "',
                aditviagem_dt_partida = " . ($dados->dtPartida != '' ? ("'" . $dados->dtPartida . "'") : 'NULL') . ",
                aditviagem_dt_retorno = " . ($dados->dtRetorno != '' ? ("'" . $dados->dtRetorno . "'") : 'NULL') . ",
                aditviagem_origem = " . ($dados->cidadeOrigem != '' ? $dados->cidadeOrigem : 'NULL') . ",
                aditviagem_destino = " . ($dados->cidadeDestino != '' ? $dados->cidadeDestino : 'NULL') . ",
                aditveiplaca = " . ($dados->placaVeiculo != '' ? ("'" . $dados->placaVeiculo . "'") : 'NULL') . ",
                aditadiantamento_banco = " . $dados->fornecedor->forbanco . ",
                aditadiantamento_agencia = " . $dados->fornecedor->foragencia . ",
                aditadiantamento_digito_agencia = " . ($dados->fornecedor->fordigito_agencia != '' ? ("'" . $dados->fornecedor->fordigito_agencia . "'") : 'NULL') . ",
                aditadiantamento_cc = " . $dados->fornecedor->forconta . ",
                aditadiantamento_digito_cc = " . $dados->fornecedor->fordigito_conta . ",
                aditviagem_idavolta = " . ($dados->idaVolta != '' ? ("'" . $dados->idaVolta . "'") : 'NULL') . ",
                aditkm_calculada = " . ($dados->distancia != '' ? $dados->distancia : 'NULL') . ",
                aditrproid = " . ($dados->projeto != '' ? $dados->projeto : 'NULL') . "
            WHERE
                aditoid = " . $dados->idTipoRequisicao;

        
        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    public function inserirAprovacao(stdClass $dados) {
        
        $sql = "
            UPDATE adiantamento SET
                adistatus_solicitacao = '" . $dados->statusSolicitacao . "',
                adistatus_aprovacao = '" . $dados->statusAprovacaoRequisicao . "',
                adiobs_aprovacao = '" . $dados->observacoesAprovacaoRequisicao . "',
                adivl_liberado = " . $dados->valorAprovacaoRequisicao . ",
                adivalor = " . $dados->valorAdiantamento . ",
                adivl_adiantamento_solicitado = " . $dados->valorSolicitado . "
            WHERE 
                adioid = " . $dados->idRequisicao . "
                AND adistatus_solicitacao = 'P'";

        $resultado = pg_query($this->conn, $sql);

        if (!pg_affected_rows($resultado)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }
    
    public function inserirAprovacaoReembolso(stdClass $dados) {
    	
    	if (intval($dados->valorAprovacaoReembolso) > 0) {
    		$valor = str_replace('.', '', $dados->valorAprovacaoReembolso);
    		$valor = str_replace(',', '.', $valor);
    	} else {
    		$valor = str_replace(',', '.', $dados->valorAprovacaoReembolso);
    	}
    	
    
    	$sql = "UPDATE 
    				adiantamento 
    			SET
		            adivlr_reembolso = " . $valor . ",
		            adiobs_reembolso = '" . $dados->observacoesAprovacaoReembolso . "',
		            adistatus_reembolso = '" . $dados->statusAprovacaoReembolso . "',
		            adistatus_solicitacao = '" . $dados->statusSolicitacao . "'
		        WHERE
                    adioid = " . $dados->idRequisicao . "
                    AND adistatus_solicitacao = 'R'";

        $resultado = pg_query($this->conn, $sql);
    
    	if (!pg_affected_rows($resultado)) {
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}
    
    	return true;
    }
    
    public function buscarPrestacaoContas($adioid){
    
    	$sql = "
	        SELECT
	            adigtdpoid AS id_tipo_despesa,
	            adigtipo AS tipo_despesa,
	            adigvalor_unitario AS valor_despesa,
	            adignota AS numero_nota,
	            adigobs AS observacao,
	            adivalor as valor_adiantamento,
	            to_char(adigdt_despesa,'DD/MM/YYYY') AS data_despesa,
	            adicntoid as centro_custo,
	            adistatus_solicitacao as status_solicitacao
	        FROM
	            adiantamento
	        INNER JOIN
	            adiantamento_gastos ON adigadioid = adioid
	        WHERE
	            adigadioid = " . $adioid;
    	
    	return pg_query($this->conn, $sql);
    }
    
    public function buscaDadosUsuario($idusuario){
    	
    	$sql = "
	            SELECT
	                usuemail,
	                forfornecedor
	            FROM
	                usuarios
	                INNER JOIN fornecedores ON usuforoid = foroid
	            WHERE
	                cd_usuario = " . $idusuario;
    	
    	$rs = pg_query($this->conn, $sql);
    	
        $dados = new stdClass();
    	$dados->email = "";
    	
    	if ($rs && pg_num_rows($rs) > 0) {
    		$dados->email = pg_fetch_result($rs, 0, 'usuemail');
    		$dados->solicitante = pg_fetch_result($rs, 0, 'forfornecedor');
    	}
    	
    	return $dados;
    }
    
    public function alterarStatusSolicitacao($adioid, $status) {
    	
    	$sql = "
	            UPDATE
	                adiantamento
	            SET
	                adistatus_solicitacao = '" . $status ."'
	            WHERE
	                adioid = " . $adioid;
    	pg_query($this->conn, $sql);
    }
    
    public function inserirItensPrestacaoContas($adioid, $item) {
    	
    	$sql = "INSERT INTO adiantamento_gastos (
                        adigadioid,
                        adigcadastro,
                        adigtipo,
                        adigvalor_unitario,
                        adignota,
                        adigobs,
                        adigqtde,
                        adigdt_despesa,
                        adigtdpoid
   
                    ) VALUES (
                        " . intval($adioid) . ",
                        NOW(),
                        '" . $item['tipo_despesa'] . "',
                        " . $item['valor_despesa'] . ",
                        " . $item['numero_nota'] . ",
                        '" . $item['observacao_prestacao_contas'] . "',
                        1,
                        '" . $item['data_despesa'] . "',
                        " . $item['chave_tipo_despesa'] . "
                    );";
    	if (!pg_query($this->conn, $sql)) {
    		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
    	}
    	
    	return true;
    }
    
    public function deletarItens($adioid){
    	$sql = "DELETE FROM adiantamento_gastos WHERE adigadioid = " . intval($adioid);
    	pg_query($this->conn, $sql);
    }

    /**
     * Exclui (UPDATE) um registro da base de dados.
     * @param int $id Identificador do registro
     * @return boolean
     * @throws ErrorException
     */
    public function excluir($id) {

        $sql = "UPDATE
					adiantamento
				SET
					adiexclusao = NOW()
				WHERE
					adioid = " . intval($id) . "";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    public function buscarEstabelecimentoUsuario($idUsuario) {
        $retorno = new stdClass();

        $sql = "
            SELECT
                etboid
            FROM
                usuarios
                INNER JOIN departamento ON depoid = usudepoid
                INNER JOIN tectran ON deptecoid = tecoid
                INNER JOIN estabelecimento ON etbtecoid = tecoid
            WHERE
                etbcodigo = 1
                AND cd_usuario = " . $idUsuario;

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno->etboid;
    }

    public function buscarDadosDespesa($idRequisicao) {

        $retorno = new stdClass();

        $sql = "
            SELECT
                SUM(adigvalor_unitario) AS valor_despesa,
                adivalor AS valor_adiantamento
            FROM
                adiantamento_gastos
                INNER JOIN adiantamento ON adigadioid = adioid
            WHERE
                adigadioid = " . $idRequisicao . "
            GROUP BY
                adivalor";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        $retorno->valorDespesas = $retorno->valor_despesa;

        return $retorno;
    }

    public function gerarProduto(stdClass $dados) {

        $retorno = new stdClass();

        $sql = "
            INSERT INTO entrada_item (
                entientoid,
                entiprdoid,
                enticntoid,
                entiqtde,
                entivlr_unit,
                entiplcoid
            ) VALUES (
                " . $dados->idEntrada . ",
                '" . $entiprdoid . "',
                " . $enticntoid . ",
                1,
                " . $enttotal . ",
                " . $entiplcoid . "
            ) RETURNING entioid";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    public function buscarDadosProdutos($idRequisicao) {

        $retorno = array();

        $sql = "
            SELECT
                tdpprdoid AS id_produto,
                prdplcoid AS plano_contabil,
                SUM(adigvalor_unitario) as soma_valor
            FROM
                adiantamento_gastos
                INNER JOIN tipo_despesa ON adigtdpoid = tdpoid
                INNER JOIN produto ON prdoid = tdpprdoid
            WHERE
                adigadioid = " . $idRequisicao . "
            GROUP BY
                tdpprdoid,
                prdplcoid";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    public function buscarCondicaoPagamento($idRegistro) {

        $sql = "SELECT 
                    valvalor as condicao_pagamento 
                FROM 
                    valor 
                WHERE 
                    valregoid = $idRegistro
                AND
                    valtpvoid = 1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        $condPgto = "";
        if (pg_num_rows($rs) > 0) {
            $condPgto = pg_fetch_result($rs, 0, 'condicao_pagamento');
        }

        return $condPgto;
    }

    public function gerarEntrada(stdClass $dados) {

        $retorno = new stdClass();
        
        $empresa                = $dados->empresa;
        $estabelecimento        = $dados->idEstabelecimento;
        $fornecedor             = $dados->fornecedor->foroid;
        $requisicao             = $dados->idRequisicao;
        $valorDespesas          = $dados->dadosDespesa->valorDespesas;
        $parcelas               = $dados->numeroParcelas;
        $tipoRequisicao         = ($dados->tipoRequisicao == 'L') ? 2289 : 1965;
        $solicitante            = $dados->solicitante;
        $condicao_pagamento     = $dados->condicao_pagamento;

        $sql = "
            INSERT INTO entrada (
                enttecoid,
                entetboid,
                enttnfoid,
                entemtoid,
                entforoid,
                entdt_entrada,
                entdt_emissao,
                entnota,
                enttotal,
                entno_parcela,
                entplcoidcontra_partida,
                entusuoid,
                entconpoid
            ) VALUES (
                $empresa,
                $estabelecimento,
                36,
                1,
                $fornecedor,
                NOW(),
                NOW(),
                $requisicao,
                $valorDespesas,
                $parcelas,
                $tipoRequisicao,
                $solicitante,
                $condicao_pagamento
            ) RETURNING entoid, entplcoidcontra_partida;";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    public function gerarEntradaItem(stdClass $dados, $produto) {
        $id_entrada   = $dados->idEntrada;
        $id_produto   = $produto->id_produto;
        $centro_custo = $dados->centroCusto;
        $qtde         = 1;
        $soma_valor     = number_format($produto->soma_valor, 2, '.', '');
        $plano_contabil = $produto->plano_contabil;
                
        $sql = "
            INSERT INTO entrada_item
                (entientoid, entiprdoid, enticntoid, entiqtde, entivlr_unit, entiplcoid)
            VALUES
                ($id_entrada, $id_produto, $centro_custo, $qtde, $soma_valor, $plano_contabil);";
                
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }



    public function buscarEmpresas() {

        $retorno = array();

        $sql = "
            SELECT
                tecoid,
                tecrazao
            FROM
                tectran
            WHERE
                tecexclusao IS NULL
            ORDER BY
                tecrazao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    public function buscarCentroCusto( $idCentroCusto ) {

        $sql = "
            SELECT
                cntoid,
                cntconta,
                cntno_centro, 
                coalesce(vmccc_nivel,4) as nivel, 
                coalesce(vmccc_dig_nivel1,1) as dig1, 
                coalesce(vmccc_dig_nivel2,2) as dig2, 
                coalesce(vmccc_dig_nivel3,2) as dig3, 
                coalesce(vmccc_dig_nivel4,2) as dig4, 
                coalesce(vmccc_dig_nivel5,2) as dig5, 
                coalesce(vmccc_dig_nivel6,3) as dig6
            FROM
                centro_custo
                INNER JOIN vigencia_movim_banco ON cntvmcoid=vmcoid
            WHERE
                cntoid = " . intval( $idCentroCusto );

        $resultado = pg_query($this->conn, $sql);

        if($resultado && pg_num_rows($resultado) > 0) {
            return pg_fetch_object($resultado);
        } else {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function buscarCentrosCusto($idEmpresa) {

        $retorno = array();

        $sql = "
            SELECT
                cntoid,
                cntconta,
                cntno_centro, 
                coalesce(vmccc_nivel,4) as nivel, 
                coalesce(vmccc_dig_nivel1,1) as dig1, 
                coalesce(vmccc_dig_nivel2,2) as dig2, 
                coalesce(vmccc_dig_nivel3,2) as dig3, 
                coalesce(vmccc_dig_nivel4,2) as dig4, 
                coalesce(vmccc_dig_nivel5,2) as dig5, 
                coalesce(vmccc_dig_nivel6,3) as dig6
            FROM
                centro_custo
            INNER JOIN 
                vigencia_movim_banco ON cntvmcoid=vmcoid
            WHERE
                cntdt_exclusao IS NULL
                AND cnttipo = 5
                AND cnttecoid = " . intval($idEmpresa) . "
            ORDER BY
                cntconta";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    public function buscarCentroCustoUsuario($idDepartamento, $reembolso = false) {

        $retorno = new stdClass();

        $sql = "
            SELECT
                cntoid,
                cntconta
            FROM
                centro_custo
                INNER JOIN departamento ON depcntoid = cntoid
            WHERE
                depoid = " . $idDepartamento;

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        if ($reembolso) {
            return $retorno;
        }

        return $retorno->cntoid;
    }

    public function buscarUsuariosCentroCusto($idCentroCusto) {

        $retorno = array();

        $sql = "
            SELECT
                cd_usuario,
                nm_usuario
            FROM
                usuarios
            WHERE
                usudepoid in (SELECT depoid FROM departamento INNER JOIN centro_custo ON depcntoid = cntoid AND cntoid = " . $idCentroCusto . ")
                AND usuarios.dt_exclusao IS NULL
            ORDER BY
                nm_usuario";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    public function buscarAprovadoresCentroCusto($idCentroCusto) {

        $retorno = array();

        $sql = "
            SELECT DISTINCT
                cd_usuario,
                nm_usuario,
        		usuemail
            FROM
                funcionario
                INNER JOIN rms_aprovador ON rmsafunoid = funoid
                INNER JOIN rms_aprovador_item ON rmsaoid = rmsairmsaoid
                INNER JOIN usuarios ON usufunoid = funoid
            WHERE
                rmsatipo = 'G'
                AND rmsadt_exclusao IS NULL
                AND rmsaidt_exclusao IS NULL
                AND funexclusao IS NULL
                AND rmsaicntoid = " . intval($idCentroCusto) . "
            ORDER BY
                nm_usuario";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    public function buscarDadosUsuario($idUsuario) {

        $retorno = new stdClass();

        $sql = "
            SELECT
                cd_usuario,
                nm_usuario,
                usudepoid,
                usuforoid,
                usuemail
            FROM
                usuarios
            WHERE
                cd_usuario = " . intval($idUsuario);

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    public function buscarFeriados() {

        $retorno = array();

        $sql = "
            SELECT
                TO_CHAR(ferdata,'DD/MM/YYYY') AS ferdata
            FROM
                feriado
            WHERE
                ferexclusao IS NULL
                AND fertipo = 'F'
                AND ferdata >= NOW()";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row->ferdata;
        }

        return $retorno;
    }

    public function buscarProjetos() {

        $sql = "
            SELECT
                rproid,
                rprnome
            FROM
                req_projeto
            WHERE
                rprrpsoid != (SELECT rpsoid FROM req_projeto_status WHERE rpsdescricao ILIKE 'CONCLU_DO' LIMIT 1)
                AND rprdt_exclusao IS NULL
            ORDER BY
                rprnome";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    public function buscarEstados() {

        $sql = "
            SELECT
                estoid,
                estnome
            FROM
                estado
            WHERE
                estexclusao IS NULL
                AND estpaisoid = (SELECT paisoid FROM paises WHERE paisnome ilike 'Brasil')
            ORDER BY
                estnome";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    public function buscarCidadesEstado($idEstado) {

        $sql = "
            SELECT
                cidoid,
                ciddescricao
            FROM
                cidade
            WHERE
                cidestoid = " . intval($idEstado) . "
                AND cidexclusao IS NULL
            ORDER BY
            	ciddescricao";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($row = pg_fetch_object($rs)) {
            $retorno[] = $row;
        }

        return $retorno;
    }

    public function buscarDadosConsumoCombustivel() {

        $retorno = new stdClass();

        $sql = "
            SELECT
                acckmlitro,
                accvalorlitro
            FROM
                adiantamento_consumo_combustivel
            WHERE
                accdt_exclusao IS NULL
            LIMIT 1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    public function inserirContaPagar(stdClass $dados) {

        $sql = "
            INSERT INTO apagar (
                apgdt_entrada,
                apgtecoid,
                apgforoid,
                apgcntoid,
                apgtctoid,
                apgno_notafiscal,
                apgvl_apagar,
                apgplcoid,
                apgtipo_docto,
                apgdt_vencimento
            ) VALUES (
                " . "NOW()" . ",
                " . intval($dados->empresa) . ",
                " . intval($dados->idFornecedor) . ",
                " . intval($dados->centroCusto) . ",
                " . intval($dados->tipoConta) . ",
                " . intval($dados->idRequisicao) . ",
                " . $dados->valorAdiantamento . ",
                " . intval($dados->contaContabil) . ",
                '" . '05' . "',
                '" . $dados->dataCredito . "'
            )";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    public function inserirParcelaTipoReembolso(stdClass $dados) {

        $sql = "
            INSERT INTO apagar (
                apgtecoid,
                apgforoid,
                apgdt_vencimento,
                apgcntoid,
                apgforma_pgto,
                apgcfbbanco,
                apgtnfoid,
                apgtctoid,
                apgobs,
                apgdt_entrada,
                apgentoid,
                apgvl_apagar,
                apgno_notafiscal,
                apgplcoid
             ) VALUES (
                " . $dados->empresa . ",
                " . $dados->fornecedor->foroid . ",
                '" . $dados->vencimentoParcela . "',
                " . $dados->centroCusto . ",
                49,
                104,
                36,
                31,
                'REEMBOLSO DE DESPESAS DE VIAGEM - PARCELA 1/" . $dados->numeroParcelas . "',
                NOW(),
                " . $dados->idEntrada . ",
                " . number_format($dados->valorParcela, 2, '.', '') . ",
                " . $dados->idRequisicao . ",
                " . $dados->contaContabil . "
            )";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    
    public function inserirPrimeiraParcela(stdClass $dados) {

        $sql = "
            INSERT INTO apagar (
                apgtecoid,
                apgforoid,
                apgdt_vencimento,
                apgcntoid,
                apgforma_pgto,
                apgcfbbanco,
                apgdt_pgto,
                apgvl_pago,
                apgtnfoid,
                apgtctoid,
                apgobs,
                apgdt_entrada,
                apgentoid,
                apgvl_apagar,
                apgno_notafiscal,
                apgplcoid
             ) VALUES (
                " . $dados->empresa . ",
                " . $dados->fornecedor->foroid . ",
                NOW(),
                " . $dados->centroCusto . ",
                49,
                104,
                NOW(),
                " . number_format($dados->valorAdiantamento, 2, '.', '') . ",
                36,
                31,
                'ADIANTAMENTO P/ VIAGEM - PARCELA 1/" . $dados->numeroParcelas . "',
                NOW(),
                " . $dados->idEntrada . ",
                " . number_format($dados->valorPrimeiraParcela, 2, '.', '') . ",
                " . $dados->idRequisicao . ",
                " . $dados->contaContabil . "
            )";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    public function inserirSegundaParcela(stdClass $dados) {

        $sql = "
            INSERT INTO apagar (
                apgtecoid,
                apgforoid,
                apgdt_vencimento,
                apgcntoid,
                apgforma_pgto,
                apgcfbbanco,
                apgtnfoid,
                apgtctoid,
                apgobs,
                apgdt_entrada,
                apgentoid,
                apgvl_apagar,
                apgno_notafiscal,
                apgplcoid
            ) VALUES (
                " . $dados->empresa . ",
                " . $dados->fornecedor->foroid . ",
                '" . $dados->vencimentoSegundaParcela . "',
                " . $dados->centroCusto . ",
                49,
                104,
                36,
                31,
                'ADIANTAMENTO P/ VIAGEM - PARCELA 2/2',
                NOW(),
                " . $dados->idEntrada . ",
                " . number_format($dados->valorSegundaParcela, 2, '.', '') . ",
                " . $dados->idRequisicao . ",
                " . $dados->contaContabil . "
            )";

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }

    public function inserirDadosConferencia(stdClass $dados) {
        $sql = "
            UPDATE
                adiantamento
            SET
                adistatus_solicitacao = '" . $dados->statusRequisicao ."',
                adistatus_autoriz_conf = '" . $dados->statusConferencia ."',
                adiautorizacao_conf = '" . $dados->dataChegadaRelatorio ."',
                adivlr_devolucao = " . (isset($dados->valorDevolucao) ? number_format($dados->valorDevolucao, 2, '.', '') : 'NULL') .",
                adivlr_reembolso = " . (isset($dados->valorReembolso) ? number_format($dados->valorReembolso, 2, '.', '') : 'NULL') .",
                adiobs_conferencia = '" . $dados->justificativaConferencia ."'
            WHERE
                adioid = " . $dados->idRequisicao;

        if (!pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return true;
    }
    
    public function buscarContaContabil($idEmpresa, $descricao) {

        $sql = "
            SELECT
                plcoid
            FROM
                plano_contabil
            WHERE
                plcexclusao IS NULL
                AND plcdescricao ilike '" . $descricao . "'
                AND plctipo = 6
                AND plctecoid = " . intval($idEmpresa) . "
            LIMIT
                1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    public function buscarTipoContaPagar() {

        $sql = "
            SELECT
                tctoid
            FROM
                tipo_ctpagar
            WHERE
                tctdescricao ilike 'adiantamento para viagem'
            LIMIT 1";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    public function buscarDadosFornecedorUsuario($idUsuario) {

        $retorno = new stdClass();

        $sql = "
            SELECT
                foroid,
                forfornecedor,
                fordocto,
                forbanco,
                foragencia,
                fordigito_agencia,
                forconta,
                fordigito_conta,
                fortipo,
                cd_usuario,
                usudepoid
            FROM
                usuarios
                INNER JOIN fornecedores ON foroid = usuforoid
            WHERE
                fordt_exclusao IS NULL
                AND cd_usuario = " . intval($idUsuario);

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        if (pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_object($rs);
        }

        return $retorno;
    }

    /**
     * Busca os tipos de despesas para preencimento de combo
     *
     * @return array
     * @throws ErrorException
     */
    public function buscarTipoDespesa() {

        $retorno = array();

        $sql = "
            SELECT
                tdpoid,
                tdpdescricao
            FROM
               tipo_despesa
            WHERE
                tdpexclusao IS NULL
            ";

        if (!$rs = pg_query($this->conn, $sql)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        while ($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
        }

        return $retorno;

    }
    
    public function buscarPrestacaoContasParaImprimir($adioid){
    	
    	$sql = "
                SELECT
                    adinome as nome_solicitante,
                    adicpf as cpf,
                    adimotivo as motivo,
                    to_char(adicadastro, 'DD/MM/YYYY') as data_solicitacao,
                    cntconta as centro_custo,
                    cntno_centro,
                    adivalor as valor_recebido,
                    tecrazao as empresa,
                    coalesce(vmccc_nivel,4) as nivel, 
                    coalesce(vmccc_dig_nivel1,1) as dig1, 
                    coalesce(vmccc_dig_nivel2,2) as dig2, 
                    coalesce(vmccc_dig_nivel3,2) as dig3, 
                    coalesce(vmccc_dig_nivel4,2) as dig4, 
                    coalesce(vmccc_dig_nivel5,2) as dig5, 
                    coalesce(vmccc_dig_nivel6,3) as dig6
                FROM
                    adiantamento
                INNER JOIN
                    centro_custo ON adicntoid = cntoid
                INNER JOIN 
                    vigencia_movim_banco ON cntvmcoid=vmcoid
                INNER JOIN
                    tectran ON cnttecoid = tecoid
                WHERE
                    adioid = " . $adioid;
    	return pg_query($this->conn, $sql);
    	
    }
    
    public function somarValoresItens($adioid){
    	$sql = "SELECT
	            		SUM(adigvalor_unitario) AS valor_itens
	            	FROM
	            		adiantamento_gastos
	            	WHERE
	            		adigadioid = " . $adioid;
    	$rs = pg_query($this->conn, $sql);
    	return $rs;
    }


    /**
     * Abre a transação
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transação
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transação
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

}

?>

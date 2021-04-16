<?php

/**
 * @class MasterViagemDAO
 * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
 * @since 31/08/2012
 * Camada de regras de persistência de dados.
 */
class MasterViagemDAO {

    private $conn;
    
    /**
     * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * Tráz a data do último registro inserido, caso exista
     */
    public function getLastSolicitationDate() {
        $sql = "
    			SELECT 
    				MAX(solvdt_chamada) AS last_date
    			FROM
    				solicitacao_viagem";

        $rs = pg_query($this->conn, $sql);

        $last_date = (pg_num_rows($rs) > 0) ? pg_fetch_result($rs, 0, 'last_date') : null;
        
        return $last_date;
    }
    
    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * Lista as solicitações a serem integradas na base da sascar
     */
    public function getSolicitationsByNoProcess() {
        echo '\n\n';
        echo "****** QUERY DAS SOLICITAÇÕES LISTADAS PELA BASE DA SASCAR ******\n";
        echo $sql = "
	    	SELECT
	    		solvoid AS solicitacao_id,
	    		solvnumero_solicitacao AS numero_solicitacao,
	    		solvdt_chamada AS data_solicitacao,
	    		solvchave_solicitacao AS chave_solicitacao,
	    		solvtipo_solicitacao AS tipo_solicitacao
	    	FROM
	    		solicitacao_viagem
	    	WHERE
	    		solvprocessado IS FALSE ";	//--solvdt_chamada > '$last_date'
		
		$rs = pg_query($this->conn, $sql);

        $solicitacoes = array();

        for ($i = 0; $i < pg_num_rows($rs); $i++) {
            $solicitacoes[$i]['solicitacao_id'] = pg_fetch_result($rs, $i, 'solicitacao_id');
            $solicitacoes[$i]['numero_solicitacao'] = pg_fetch_result($rs, $i, 'numero_solicitacao');
            $solicitacoes[$i]['tipo_solicitacao'] = pg_fetch_result($rs, $i, 'tipo_solicitacao');
            $solicitacoes[$i]['chave_solicitacao'] = pg_fetch_result($rs, $i, 'chave_solicitacao');
        }

        return $solicitacoes;
    }
    
    /**
     * @author Angelo Frizzo <angelo.frizzo@meta.com.br>
     * @param xmlObject $solicitation => Objeto contendo a solicitação
     * @param String $tipo => Indica 'A' para adiantamento ou 'R' para reembolso
     * Retorna um booleano indicando se o registro já foi inserido ou não.
     */
    public function verifyDuplicSolicitation($solicitation, $tipo) {
        $sql = "
    			SELECT 
    				COUNT(*) AS qtde_duplicados
    			FROM
    				solicitacao_viagem
    			WHERE
    				solvchave_solicitacao = '$solicitation->SolicitacaoId' AND solvtipo_solicitacao = '$tipo'
    		  ";

        $rs = pg_query($this->conn, $sql);

        $qtde_duplicados = pg_fetch_result($rs, 0, 'qtde_duplicados');

        return $qtde_duplicados;
    }

    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   $solicitations => XML com as solicitações
     * @param   $tipo => Tipo da solicitação, 'A' para adiantamento e 'R' para 
     *          reembolso.
     * Grava na base de dados as solicitações trazidas pelo método listarSolicitacao
     * do WS da Master Viagem, para futuramente realizar a integração.
     */
    public function saveListSolicitation($solicitation, $tipo) {

        echo "****** SQL PARA GRAVAR NA SOLICITAÇÃO VIAGEM ******\n";        
        echo $sql = "
    			INSERT INTO 
					solicitacao_viagem 
					(solvchave_solicitacao, solvnumero_solicitacao, solvtipo_solicitacao)
				VALUES 
					('$solicitation->SolicitacaoId', $solicitation->NroSolic, '$tipo');";

        $exec = pg_query($this->conn, $sql);

        $is_ok = pg_affected_rows($exec) ? true : false;
        
        return $is_ok;
    }

    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   Array  $params => Dados a serem gravados na tabela
     * Insere a solicitação na tabela apagar
     */
    public function saveAdvanceSolicitation($params, $apgentoid = 'NULL') {
        echo "\n\n";
        echo "****** SQL QUE INSERE NA TABLE APAGAR ******\n";
        
        $data_vencimento = !empty($params['data_vencimento']) ? "'".$params['data_vencimento']."'" : 'null';
        
        $data_vencimento = ($apgentoid == 'NULL') ? 'NOW()' : $data_vencimento;
        
        ECHO $sql = "
		    	INSERT INTO
		    		apagar 
		    		(
		    			apgdt_entrada,
                        apgdt_vencimento,
		    			apgtecoid, 
		    			apgforoid, 
		    			apgcntoid, 
		    			apgtctoid, 
		    			apgtnfoid, 
		    			apgno_notafiscal, 
		    			apgvl_apagar,
		    			apgobs,
        				apgentoid  
		    		)
		    	VALUES
		    		(
    					NOW(),
                        ".$data_vencimento. ", 
    					1, 
    					" . $params['viajante_cpf'] . ", 
    					" . $params['centro_custo'] . ", 
    					" . $params['tipo_ct_pagar'] . ", 
    					" . $params['tipo_documento'] . ", 
    					" . $params['numero_solicitacao'] . ", 
    					" . $params['solAdiantamento_valor'] . ",
    					'" . $params['observacao'] . "',
    					" . $apgentoid . "		 
    				)";

        return pg_affected_rows(pg_query($this->conn, $sql));
    }

    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   Array  $params => Dados a serem gravados na tabela
     * Insere a solicitação na tabela entrada
     */
    public function saveRepaymentSolicitation($params) {

       ECHO $sql = "
		    	INSERT INTO
		    		entrada 
		    		(
		    			enttecoid, 
		    			entetboid, 
		    			enttnfoid, 
		    			entemtoid, 
		    			entforoid, 
		    			entdt_entrada,
    			        entdt_emissao,
    					entusuoid,  
		    			entnota, 
		    			enttotal, 
		    			entno_parcela,
		    			entplcoidcontra_partida 
		    		)
		    	VALUES
		    		(
    					1,
    					6, 
    					36, 
    					1, 
    					" . $params['viajante_cpf'] . ", 
    					NOW(),
    					NOW(),
    					2750,		 
    					" . $params['numero_solicitacao'] . ", 
    					0, --SERA ATUALIZADO COM VALOR TOTAL DOS ITENS
    					0, --SERA ATUALIZADO COM A QUANTIDADE DE PARCELAS
    					" . $params['contra_partida'] . "
    				 ) 
                     RETURNING entoid";

        $rs = pg_query($this->conn, $sql);

        if (pg_num_rows($rs) > 0) {
            return pg_fetch_result($rs, 0, 'entoid');
        }

        return false;
    }

    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   Array  $params => Dados a serem gravados na tabela
     * Insere a solicitação na tabela entrada_item
     */
    public function saveRepaymentSolicitationItem($params) {

    	ECHO $sql = "
		    	INSERT INTO
		    		entrada_item
			    	(
				    	entientoid,
                        entiprdoid,
				    	entiplcoid,
				    	enticntoid,
				    	entiqtde,
				    	entivlr_unit 
				    )
		    	VALUES
			    	(
				    	" . $params['entoid'] . ",
                        " . $params['produto_id'] . ",
				    	" . $params['plano_contabil_id'] . ",
				    	" . $params['centro_custo'] . ",
				    	" . $params['reembolso_quantidade'] . ",
				    	" . $params['reembolso_valor'] . "
				    )";

        return pg_affected_rows(pg_query($this->conn, $sql));
    }

    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   Array  $params => Dados a serem gravados na tabela
     * @param   String $mensagem => descrição do motivo da gravação do log
     * @param   String $motivo => sigla do motivo da gravação do log
     * Insere a solicitação que falhou na tabela log_solicita_viagem
     */
    public function saveLog($params, $mensagem, $motivo) {
        
        $sql = "
		    	INSERT INTO
			    	log_solicita_viagem
			    	(
						  lsvchave_solicitacao,
						  lsvnumero_solicitacao,
						  lsvdt_chamada,
						  lsvempresa,
						  lsvdocumento,
						  lsvcentro_custo,
						  lsvtipo_conta_pagar,
						  lsvtipo_documento,
						  lsvvalor,
						  lsvestabelecimento,
						  lsvgrupo_documento,
						  lsvtipo_movimentacao,
						  lsvparcela,
						  lsvcontra_partida,
						  lsvproduto,
						  lsvconta_contabil,
						  lsvquantidade,
						  lsvmensagem,
        				  lsvmotivo
					)
		    	VALUES
			    	(
				    	'" . $params['id_solicitacao'] . "',
				    	" . $params['numero_solicitacao'] . ",
				    	NOW(),
				    	1,
				    	" . $params['viajante_cpf'] . ",
				    	" . $params['centro_custo'] . ",
				    	" . $params['lsvtipo_conta_pagar'] . ",
				    	" . $params['lsvtipo_documento'] . ",
				    	" . $params['lsvvalor'] . ",
				    	" . $params['lsvestabelecimento'] . ",
				    	" . $params['lsvgrupo_documento'] . ",
				    	" . $params['lsvtipo_movimentacao'] . ",
				    	" . $params['lsvparcela'] . ",
				    	" . $params['lsvcontra_partida'] . ",
				    	" . $params['lsvproduto'] . ",
				    	" . $params['lsvconta_contabil'] . ",
				    	" . $params['solReembolso_qtde'] . ",
				    	'" . utf8_encode(strip_tags($mensagem)) . "',
				    	'" . $motivo . "'		
			    	)";
        
        return pg_affected_rows(pg_query($this->conn, $sql));
    }
    
    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   String  $solicitation_key => chave da solicitacao
     * @param   Integer $solicitation_number => numero da solicitacao
     * @param   String $mensagem => descrição do motivo da gravação do log
     * @param   String $motivo => sigla do motivo da gravação do log
     * Insere a solicitação que falhou na tabela log_solicita_viagem
     */
    public function saveLogWSRepuraSolicitacao($solicitation_key, $solicitation_number, $mensagem, $motivo) {
        $sql = "
		    	INSERT INTO
			    	log_solicita_viagem
			    	(
						  lsvchave_solicitacao,
						  lsvnumero_solicitacao,
						  lsvdt_chamada,
                          lsvmensagem,
                          lsvmotivo
					)
		    	VALUES
			    	(
				    	'$solicitation_key',
				    	$solicitation_number,
				    	NOW(),				    	
				    	'" . utf8_encode(strip_tags($mensagem)) . "',
				    	'$motivo'		
			    	)";
        
        pg_query($this->conn, $sql);
    }
    
    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   Integer  $entoid => ID da tabela entrada
     * @param   Double $valor_total => valor total da soma dos itens
     * @param   Integer $no_parcela => numero de títulos para o pagamento do documento.
     * Atualiza o valor total da entrada com o valor da soma dos itens e o numero de títulos para o pagamento do documento
     */
    public function updateValorTotal($entoid, $valor_total, $no_parcela) {
       ECHO $sql = "UPDATE
                    entrada
                SET 
                    enttotal = $valor_total,
                    entno_parcela = $no_parcela
                WHERE
                    entoid = $entoid;";
        
        return pg_affected_rows(pg_query($this->conn, $sql));
    }

    /**
     * @author  Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
     * @param   String  $cpf => CPF a ser usado no filtro     
     * Busca o id do funcionário de acordo com o seu CPF
     */
    public function getIdCpf($cpf) {
        $sql = "
		    	SELECT
		    		foroid
		    	FROM
		    		fornecedores  
		    	WHERE
                    fordocto = '$cpf'
    		  ";

        $rs = pg_query($this->conn, $sql);

        $foroid = (pg_num_rows($rs) > 0) ? pg_fetch_result($rs, 0, 'foroid') : null;

        return $foroid;
    }

    /**
     * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
     * @param   Integer  $entoid => ID da tabela entrada
     * Atualiza campos da tabela entrada confirmando os lançamentos de reembolso
     */
    public function updateConfirmaLanctoEntrada($entoid) {
        ECHO $sql = "UPDATE
                    entrada
                SET 
                    entstatus = 'C',
                    entdt_autorizacao = NOW(),
                    entusu_autorizacao = 2750
                WHERE
                    entoid = $entoid";
        
        return pg_affected_rows(pg_query($this->conn, $sql));
    }

    /**
     * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
     * @param   Integer  $entoid => ID da tabela entrada
     * Atualiza campos da tabela apagar confirmando os lançamentos de reembolso
     */
    public function updateConfirmaLanctoApagar($entoid) {
    	ECHO $sql = "UPDATE
			    	apagar
		    	SET
			    	apgprevisao = TRUE
		    	WHERE
		    		apgentoid = $entoid";
    	
    	return pg_affected_rows(pg_query($this->conn, $sql));
    }
    
    /**
     * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
     * @param   String  $chave_solicitacao => Chave da solicitação
     * @param   Integer  $numero_solicitacao => Número da solicitação
     * @param   string $tipo_solicitacao => Tipo solicitação 
     * Atualiza campos da tabela solicitacao_viagem confirmando os lançamentos de reembolso
     */
    public function updateConfirmaLanctoSolViagem($chave_solicitacao, $numero_solicitacao, $tipo_solicitacao) {
    	ECHO $sql = "UPDATE
			    	solicitacao_viagem
		    	SET
			    	solvprocessado = TRUE
		    	WHERE
		    		solvchave_solicitacao = '$chave_solicitacao'
		    		AND solvnumero_solicitacao = $numero_solicitacao
		    		AND solvtipo_solicitacao = '$tipo_solicitacao'	
    			";
    
    	return pg_affected_rows(pg_query($this->conn, $sql));
    }
    
    public function __construct() {

        global $conn;

        $this->conn = $conn;
    }
    
    public function __get($var) {
        return $this->$var;
    }

}
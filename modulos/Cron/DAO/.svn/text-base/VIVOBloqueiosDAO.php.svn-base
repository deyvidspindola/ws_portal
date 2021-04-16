<?php

/**
 * @class Vivo Bloqueios
 * @author Vanessa Rabelo <vanessa.rabelo@meta.com.br>,Angelo Frizzo <angelo.frizzo@meta.com.br>
 * @since 01/10/2013
 * Camada de regras de persist?ncia de dados.
 */
class VivoBloqueiosDAO  {

    private $conn;


    /**
    * M?todo  gera Log de processamento
    *	@param 	varchar $arquivo => Nome do arquivo TXT
    *  @param boolean $resultado => Indica se o arquivo foi processado (TRUE), ou n?o (FALSE).
    *  @return ( TRUE ou FALSE)
    *
    */
    public function gravarLog($arquivo, $resultado) {
         
        $arquivo = isset($arquivo) ? $arquivo : '';
        $resultado = ($resultado == 1) ? 't' : 'f';
        
        $sql = "
                INSERT INTO
                    arquivo_parceiro_bloqueio_historico
                    (
                      apbharquivo,
                      apbhresultado,
                      apbhdt_cadastro
                     )
                     VALUES
                     (
                       '" .$arquivo."',
                       '" .$resultado. "',
                        NOW()
                      )";

        return pg_affected_rows(pg_query($this->conn, $sql));

    }




    /**
    * Busca os veiculo parceiro na tabela relacionado ao n?mero  da conta.
    *
    * @param varchar $conta Conta de servi?o na linha do pedido relacionada ao registro.
    *
    * @return array
    */
    public function buscarVeiculo($conta) {

        $veiculos = array();
                 
        if (isset($conta) || !empty($conta)){
            $sql = "
	            SELECT DISTINCT
	                vppaveioid,
                    vppaveioid AS veioid,
	                vppaconoid AS connumero,
	                conclioid AS clioid,
	                conequoid as equoid,
                    vppapedido AS pedido,
                    vppasubscription AS idAssinatura,
                    conmodalidade AS modalidade,
                    conequoid AS equipamento,
                    coneqcoid AS classe,
                    equeveoid AS versao

	            FROM
	                veiculo_pedido_parceiro
				INNER JOIN 
					contrato ON vppaconoid = connumero
                INNER JOIN
                    equipamento ON conequoid = equoid 
	            WHERE
	                vppaconta = '$conta'
	            ";                     
	        $rs = pg_query($this->conn, $sql);
	        
	        // for ($i = 0; $i < pg_num_rows($rs); $i++) {
	        // 	$veiculos[$i]['vppaveioid'] = pg_fetch_result($rs, $i, 'vppaveioid');
	        // 	$veiculos[$i]['connumero'] 	= pg_fetch_result($rs, $i, 'connumero');
	        // 	$veiculos[$i]['clioid'] 	= pg_fetch_result($rs, $i, 'clioid');
	        // 	$veiculos[$i]['equoid'] 	= pg_fetch_result($rs, $i, 'equoid');
	        // }

            while ($linha = pg_fetch_assoc($rs)) {
                $veiculos[] = $linha;
            }
		}
		
    	return $veiculos;
         
    }



    /**
    * M?todo que atualiza flag sasweb na tabela veiculos ap?s chamada do WS
    *	@param int $veiculo => ID da tabela veiculo, referente ao codigo do veiculo cadastrado na base da sascar
    *  @param boolean $acao => Indica se o ve?culo ser? visualizado (TRUE) ou n?o (FALSE) no SASWEB
    *  @return ( TRUE ou FALSE)
    *
    */
    public function atualizarVeiculoSasweb($veiculo, $acao) {

        $veiculo = isset($veiculo) ? $veiculo : '';
        $acao = ($acao == true) ? 't' : 'f';
                 
         $sql = "
            UPDATE
                veiculo
            SET
                veivisualizacao_sasweb = '$acao'
            WHERE
                veioid = ".$veiculo;
    
    return pg_affected_rows(pg_query($this->conn, $sql));
}


    /**
    * M?todo grava a??es na tabela veiculo_parceiro_bloqueio
    *	@param int $veioid => ID da tabela veiculo, referente ao codigo do veiculo cadastrado na base da sascar
    *  @param boolean $acao => Indica se o ve?culo ser? visualizado (TRUE) ou n?o (FALSE) no SASWEB
    *  @return ( 1)
    *
    */
    public function gravarVeiculoBloqueio($veioid, $acao) {
    
        $veioid = isset($veioid) ? $veioid : '';
        $acao = ($acao == true) ? 't' : 'f';
        
        $sql = "
            INSERT INTO
                veiculo_parceiro_bloqueio
            (
                vpbveioid,
                vpbacao,
                vpbdt_cadastro
            )
            VALUES
            (
                " .$veioid. ",
                '" .$acao. "',
                NOW()
            )";
        
        return pg_affected_rows(pg_query($this->conn, $sql));        
    }
    
    public function inserirHistoricoContrato($contrato, $obs) {
    	 
    	$sql = "SELECT
    				historico_termo_i (". intval($contrato) .", 2750, '" . utf8_encode($obs) . "', '', 38) ";
    	return (pg_query($this->conn, $sql)) ? true : false;
    
    }
    
    public function alterarStatusEquipamento($idEquipamento) {
    	 
    	$sql = "UPDATE
    				equipamento
    			SET
    				equeqsoid = 13
    			WHERE
    				equoid = " . intval($idEquipamento);
    	$rs = pg_query($this->conn, $sql);
    	return  ($rs && pg_affected_rows($rs) > 0) ? true : false;
    }
    
    public function cancelarContrato($contrato) {
    	 
    	$sql = "UPDATE
    				contrato
    			SET
    				concsioid = 38,
    				condt_exclusao = NOW(),
    				conusuoid = 2750,
    				concmcoid = 2
    			WHERE
    				connumero = " . intval($contrato);
    	$rs = pg_query($this->conn, $sql);
    	return  ($rs && pg_affected_rows($rs) > 0) ? true : false;
    }
    
    public function inserirHistoricoRescisao($idRescisaoGerada, $obs) {
    	 
    	if (intval($idRescisaoGerada) == 0) {
    		return false;
    	}
    	 
    	$sql = "INSERT INTO
   					pre_rescisao_hist (
   						prehpresoid,
   						prehstatus,
   						prehobservacao,
   						prehusuoid
   					) VALUES (
   						". $idRescisaoGerada .",
   						'R',
   						'". $obs ."',
   						2750
   					) ";
    	$rs = pg_query($this->conn, $sql);
    	return  ($rs && pg_affected_rows($rs) > 0) ? true : false;
    
    }
    
    public function gerarRescisao($veiculo, $obs) {
    	 
    	if (intval($veiculo['connumero']) == 0 || intval($veiculo['clioid']) == 0) {
    		return false;
    	}
    	 
    	$sql = "INSERT INTO
   					pre_rescisao (
   						presconoid,
   						presclioid,
   						presdata,
   						presusuoid,
   						presstatus,
   						presobs,
   						presenv_resc,
   						presmproid
   					) VALUES (
   						". intval($veiculo['connumero']) .",
   						". intval($veiculo['clioid']) .",
   						NOW(),
   						2750,
   						'R',
   						'". utf8_encode($obs) ."',
   						NOW(),
   						202
   					) RETURNING presoid";
    	$rs = pg_query($this->conn, $sql);
    	 
    	if ($rs && pg_affected_rows($rs)) {
    		return pg_fetch_result($rs, 0, 'presoid');
    	}
    	
    	return false;
    }


    public function buscarModalidadeContrato($contrato) {

        $sql =  "SELECT 
                        conmodalidade 
                 FROM 
                        contrato 
                 WHERE 
                        connumero = " . intval($contrato) . "
                 LIMIT 1";

        $rs = pg_query($this->conn, $sql);

        if (pg_num_rows($rs) > 0) {
            return pg_fetch_result($rs, 0, 'conmodalidade');
        }

        return false;

    }

    
    public function verificarExistenciaOS($connumero){
        
        $retorno = new stdClass();
        
        $sql = "
            SELECT 
                ordoid 
            FROM 
                ordem_servico 
            WHERE 
                ordconnumero = " . intval($connumero)." 
            AND
                ordstatus IN (1,4,7) 
            AND 
                ordoid > 0      
        ";
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Erro ao verificar se a OS existe');
        }
        
        if (pg_num_rows($rs) > 0){
            $retorno = pg_fetch_object($rs);
        } 
        
        
        return $retorno;
    }
    
    
    public function geraOSRetirada($dados){
        
        $sql = "
            INSERT INTO 
                ordem_servico (
                    ordmtioid, ordequoid, ordclioid, ordstatus,
                    ordusuoid, ordconnumero, orddesc_problema, orddescr_motivo, 
                    ordveioid, ordeveoid)
            VALUES (
                5, " . intval($dados['equipamento']) . ", " . intval($dados['clioid']) . ", 4, 
                2750," . intval($dados['connumero']) . ", 'Retirada por Rescisão', 'Retirada por Rescisão',
                " . intval($dados['veioid']) . ", ". intval($dados['versao']) .")
            RETURNING ordoid;";

        
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Erro ao gerar a OS');
        }
        
        if (pg_num_rows($rs)){
            $retorno = pg_fetch_object($rs);
            return $retorno->ordoid;
        }
        
        return false;
        
    }
    
    public function atualizarOSRetirada($ordoid){
        $sql = "
            UPDATE 
                ordem_servico
            SET
                ordmtioid = 5,
                ordstatus = 4,
                orddesc_problema  = 'Retirada por Rescisão', 
                orddescr_motivo = 'Retirada por Rescisão'
            WHERE 
                ordoid = " . intval($ordoid);
        if (!pg_query($this->conn, $sql)) {
            throw new Exception('Erro ao atualizar a OS');
        }
        return true;
    }
    
    
    public function cancelarItensOSNRetirada($ordoid){
        
        $sql = "
            UPDATE 
                ordem_servico_item
            SET
                ositstatus = 'X'
            WHERE 
                ositordoid = " . intval($ordoid) . "
            AND 
                ositotioid <> 110 
            AND 
                ositexclusao IS NULL
            ";
        
        if (!pg_query($this->conn, $sql)) {
            throw new Exception('Erro ao cancelar o item da OS');
        }
        return true;
    }
    
    
    public function buscarItensContratoServico($connumero){
        
        $retorno = array();
        
        $sql = "
            SELECT 
                otioid, 
                otidescricao AS descricao
            FROM 
                os_tipo_item 
            INNER JOIN 
                os_tipo ON ostoid = otiostoid
            INNER JOIN 
                contrato_servico ON consobroid=otiobroid 
                AND consiexclusao IS NULL 
                AND conssituacao IN('L','B')
                AND consinstalacao IS NOT NULL
            WHERE 
                otiostoid = 3 
            AND 
                otidt_exclusao IS NULL
            AND 
                consconoid = " . intval($connumero);
        
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Erro ao buscar os serviços do contrato');
        }
        
        if (pg_num_rows($rs) > 0){
            while($linha = pg_fetch_object($rs)){
                $retorno[] = $linha;
            }
        } 
        
        return $retorno;
        
        
    }
    
    
    
    public function verificarExistenciaItemRetirada($ordoid){
        
        $retorno = null;
        
        $sql = "
            SELECT EXISTS(
                SELECT
                    ositoid
                FROM 
                    ordem_servico_item
                WHERE 
                    ositordoid = " . intval($ordoid) . "
                AND 
                    ositotioid = 110
                AND
                    ositstatus IN ('A', 'P')
                AND
                    ositexclusao IS NULL
            ) AS existe";
        
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Erro ao verificar se a OS existe');
        }
        
        if (pg_num_rows($rs) > 0){
            $retorno = pg_fetch_object($rs);
            return ($retorno->existe == 't');
            
        } 
        
        return false;
    }
    
    
    public function inserirItemOS($ordoid, $tipo,  $classe){
        
        $sql = "
            SELECT ordem_servico_item_i('{
                \"" . intval($tipo) . "\"
                \"" . intval($ordoid). "\"
                \"\" 
                \"" . intval($classe) . "\" 
                \"Retirada por Rescisão\" 
                \"A\"}') as ositoid;";
        if (!pg_query($this->conn, $sql)) {
            throw new Exception('Erro ao inserir o item de retirada na OS');
        }
        return true;
        
    }


    public function incluirHistoricoOsRetirada($ordemServico){

        /**
         * Número da OS gerada/ atualizada; 
         * Código do usuário: Automático (2750); 
         * Situação: 'Retirada por Rescisão'
         */

        $sql = "INSERT INTO 
                    ordem_situacao
                    (
                        orsordoid,
                        orsusuoid,
                        orssituacao,
                        orsdt_situacao
                    )
                VALUES
                    (
                        " . intval($ordemServico) . ",
                        2750,
                        'Retirada por Rescisão',
                        NOW()
                    )";

        if (!pg_query($this->conn, $sql)) {
            throw new Exception('Erro ao inserir historico da OS de retirada');
        }

        return true;
    }

    

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function __get($var) {
        return $this->$var;
    }


    /**
     * Abre a transa??o
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transa??o
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transa??o
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

}


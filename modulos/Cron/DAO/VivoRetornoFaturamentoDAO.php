<?php

/**
 * Retorno Faturamento Vivo
 *
 * @package Cron
 * @author  Vanessa Rabelo <vanessa.rabelo@meta.com.br>
 */
class RetornoFaturamentoDAO  {
    
    /**
     * Objeto Parâmetros.
     *
     * @var stdClass
    */  
    private $conn;

    /**
    * Método gera Log de processamento
    * 
    * @param int $arquivo   => Nome do arquivo TXT.    
    * @param int $descricao => Descrição do motivo de histórico
    * 
    * @return void
    *
    */
    public function gravarLog($arquivo, $descricao) {
         
        $arquivo = isset($arquivo) ? $arquivo : '';
        
        $descricao = isset($descricao) ? $descricao : '';
               
        $sql = "
                INSERT INTO
                    arquivo_parceiro_evento_historico
                    (
                      apeharquivo,
                      apehdescricao,
                      apehdt_cadastro
                     )
                     VALUES
                     (
                       '" .$arquivo."',
                       '" .$descricao. "',
                        NOW()
                      )";

        return pg_affected_rows(pg_query($this->conn, $sql));

    }


    /**
    * Método verifica Subscrition ID
    * 
    * @param varchar $subscriptionId => Código de assinatura do registro.  
    * 
    * @return int
    *
    */
    public function verificarSubscriptionId($subscriptionId) {
        echo "\n\n";
        echo "****** SQL VERIFICA SUBSCRIPTION ID ******\n";
        
        $subscriptionId = isset($subscriptionId) ? $subscriptionId : '';
        
        echo $sql = "
                SELECT
                    vppaoid
                FROM
                    veiculo_pedido_parceiro
                WHERE
                    vppasubscription = '".$subscriptionId."'
        ";
    
        $rs = pg_query($this->conn, $sql);
    
        $vppaoid = (pg_num_rows($rs) > 0) ? pg_fetch_result($rs, 0, 'vppaoid') : null;
    
        return $vppaoid;
    }
    
    /**
     * Método verifica Parcela
     *
     * @param varchar $subscriptionId        => Código de assinatura do registro.
     * @param int     $dataReferenciaParcela => data de referencia da parcela
     *
     * @return array 
     *
     */
    public function buscarParcelaCadastrada($subscriptionId, $dataReferenciaParcela) {
        echo "\n\n";
        echo "****** SQL VERIFICA PARCELA ******\n";        

        $retorno = array();
        
        $subscriptionId = isset($subscriptionId) ? $subscriptionId : '';
        
        $dataReferenciaParcela = isset($dataReferenciaParcela) ? $dataReferenciaParcela : '';
        
        // divide Data Referência para cláusula where
        $ano = substr($dataReferenciaParcela, 0, 4);

        $mes = str_pad(substr($dataReferenciaParcela, 4, 2), "0", STR_PAD_LEFT);
        
        echo $sql = "
                SELECT
                    titoid,  
		            titno_parcela,
                    nfloid
                FROM
                    veiculo_pedido_parceiro
                    INNER JOIN nota_fiscal_item ON nficonoid = vppaconoid
                    INNER JOIN nota_fiscal ON nfloid = nfinfloid   
                    INNER JOIN titulo ON titnfloid = nfloid
                WHERE
                    vppasubscription = '".$subscriptionId."'  
                    AND EXTRACT(YEAR FROM nfidt_referencia) = ".$ano."
                    AND EXTRACT(MONTH FROM nfidt_referencia) = ".$mes."  
                ";
    
        $rs = pg_query($this->conn, $sql);

        if ($rs && pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_array($rs);
        }
    
        return $retorno;
    }
        
    
    

     /**
     * Método grava evento na tabela veiculo_parceiro_evento
     * 
     *  @param Array $evento =>  Dados a serem gravados na tabela
     * 
     * @return vpeoid
    */
    public function gravarEvento($evento) {
        echo "\n\n";
        echo "****** SQL QUE INSERE NA TABLE veiculo_parceiro_evento ******\n";

        $evento['subscription_id'] 		= isset($evento['subscription_id']) ? "'".$evento['subscription_id']."'" : "NULL";
        $evento['status'] 				= isset($evento['status']) ? "'".$evento['status']."'"  : "NULL";
        $evento['codigo_motivo'] 		= isset($evento['codigo_motivo']) ? $evento['codigo_motivo'] : "NULL";
        $eventoDataEvento 				= isset($evento['data_evento']) ? "'".$evento['data_evento']."'" : "NULL";
        $evento['valor_liquido'] 		= isset($evento['valor_liquido']) ? $evento['valor_liquido'] : 0;
        $evento['valor_bruto'] 			= isset($evento['valor_bruto']) ? $evento['valor_bruto'] : 0;
        $eventoDataVencimento 			= isset($evento['data_vencimento']) ? "'".$evento['data_vencimento']."'" : "NULL";
        $eventoDataEmissao 				= isset($evento['data_emissao']) ? "'".$evento['data_emissao']."'" : "NULL";
        $evento['ciclo_faturamento'] 	= isset($evento['ciclo_faturamento']) ? $evento['ciclo_faturamento'] : "NULL";
        $evento['numero_parcela'] 		= isset($evento['numero_parcela']) ? $evento['numero_parcela'] : "NULL";
        $evento['arquivo_remessa'] 		= isset($evento['arquivo_remessa']) ? "'".$evento['arquivo_remessa']."'" : "NULL";
        $evento['arquivo_retorno']  	= isset($evento['arquivo_retorno']) ? "'".$evento['arquivo_retorno']."'" : "NULL";
        $evento['nfioid']  				= empty($evento['nfioid']) ? 'NULL' : $evento['nfioid'];
        
        echo $sql = "
		    	INSERT INTO
		    		veiculo_parceiro_evento
		    		(
                        vpedt_importacao,
		    			vpesubscription,
                        vpevpescodigo,
                        vpevpemcodigo,
                        vpedt_evento,
                        vpevl_liquido,
                        vpevl_bruto,
                        vpedt_vencimento,
                        vpedt_emissao,
                        vpeciclo,
                        vpeno_parcela,
                        vpearquivo_remessa,
                        vpearquivo_retorno,
                        vperegistro_lote,
                        vpenfioid
		    		)
		    	VALUES
		    		(
                        NOW(),
                        " . $evento['subscription_id'] . ",
                        " . $evento['status'] . ",   
                        " . $evento['codigo_motivo'] . ",    
                        " . $eventoDataEvento .",  
                        " . $evento['valor_liquido'] . ",   
                        " . $evento['valor_bruto'] . ",   
                        " . $eventoDataVencimento .",
                        " . $eventoDataEmissao .",
                        " . $evento['ciclo_faturamento'] . ",    
                        " . $evento['numero_parcela'] . ",   
                        " . $evento['arquivo_remessa'] . ", 
                        " . $evento['arquivo_retorno'] . ",
                        " . $evento['lote'] . ",
                        " . $evento['nfioid'] . "
    				 )
                     RETURNING vpeoid";
        
            return pg_affected_rows(pg_query($this->conn, $sql));
    }
    
    /**
     * Método atualiza a data de vencimento do Titulo conforme Status da Fatura
     *
     *  @param int     $titoid         =>  Codigo do titulo a ser atualizado
     *  @param varchar $dataVencimento =>  Data Vencimento Titulo     
     *    
     * @return void
    */   
    public function atualizarDataVencimentoTitulo( $titoid, $dataVencimento) {
        echo "\n\n";
        echo "****** SQL ATUALIZA DATA VENCIMENTO TITULO ******\n";
        
        $titoid = isset($titoid) ? $titoid : '';
        
        $dataVencimento = isset($dataVencimento) ? $dataVencimento : '';
        
        ECHO $sql = "
                     UPDATE
                        titulo
                      SET
                        titdt_vencimento = '".$dataVencimento."'
                     WHERE
                        titoid = ".$titoid.";";
    
        return pg_affected_rows(pg_query($this->conn, $sql));
    }

    public function atualizarDataVencimentoNotaFiscal ( $nfloid, $dataVencimento ) {
        echo "\n\n";
        echo "****** SQL ATUALIZA DATA VENCIMENTO NOTA FISCAL ******\n";
        
        echo $sql = "
                     UPDATE
                        nota_fiscal
                      SET
                        nfldt_vencimento = '" . $dataVencimento . "'
                     WHERE
                        nfloid = " . $nfloid . ";";
    
        return pg_affected_rows(pg_query($this->conn, $sql));
    }

    public function buscarIdItemNotaFiscal ( $lote, $arquivoRemessa ) {
        echo "\n\n";
        echo "****** SQL BUSCA ID ITEM DA NOTA FISCAL ******\n";        

        echo $sql = "
                SELECT
                    henvnfioid
                FROM
                    historico_nf_envio_vivo
                    INNER JOIN nota_fiscal_item ON henvnfioid = nfioid
                WHERE
                    henvregistro_lote = " . $lote . "
                    AND henvnome_arquivo = '" . $arquivoRemessa . "';";

        $resultado = pg_query($this->conn, $sql);

        if ($resultado && pg_num_rows($resultado) > 0) {
            return pg_fetch_result($resultado, 0, 0);
        } else {
            return 0;
        }

    }

    /**
     * Método atualiza o Campo Ciclo da tabela veículo parceiro
     *
     * @param varchar $subscriptionId => Código de assinatura do registro.
     * @param var     $ciclo          => Dados a serem gravados na tabela
     *
     * @return void
     */   
    public function atualizarCicloVeiculoParceiro($subscriptionId, $ciclo) {
        echo "\n\n";
        echo "****** SQL ATUALIZA CICLO ******\n";
        
        $ciclo = isset($ciclo) ? $ciclo : '';
        
        $subscriptionId = isset($subscriptionId) ? $subscriptionId : '';
        
        ECHO $sql = "
                    UPDATE
                        veiculo_pedido_parceiro
                    SET
                        vppaciclo = '".$ciclo."'
                    WHERE
                        vppasubscription = '".$subscriptionId."'";
       
        return pg_affected_rows(pg_query($this->conn, $sql));
    }
        
    /**
    * Metodo Construtor
    * 
    * @return $conn
     */
    public function __construct() {
        
        global $conn;
        
        $this->conn = $conn;
    }
    
    /**
    * Abre a transação
    * 
    * @return $this->conn
    */
    public function begin() {
        
        pg_query($this->conn, 'BEGIN');
        
        return $this->conn;
    }
        
    /**
    * Finaliza um transação
    * 
    * @return $this->conn
    */
    public function commit() {
        
        pg_query($this->conn, 'COMMIT');
        
        return $this->conn;
    }
    
    /**
    * Aborta uma transação
    * 
    * @return $this->conn
    */
    public function rollback() {
        
        pg_query($this->conn, 'ROLLBACK');
        
        return $this->conn;
    }
        
}


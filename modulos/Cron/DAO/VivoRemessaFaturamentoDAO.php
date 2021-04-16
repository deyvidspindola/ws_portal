<?php

/**
 * Retorno Faturamento Vivo
 *
 * @package Cron
 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
 */
class RemessaFaturamentoDAO  {
    
    /**
     * Objeto Parâmetros.
     *
     * @var stdClass
    */
    private $conn;
    
    /** RN001
    * Pesquisar o próximo título,
    * ordenado por número de parcelas, relacionado à nota,
    * cuja natureza seja “VENDA DE EQUIPAMENTOS”
    * e esteja a vencer (
    * nota não cancelada,
    * sem a data de pagamento informada
    * e cuja data de vencimento seja maior que a data atual)
    * e cujos itens, estejam relacionados a contratos do tipo VIVO
    * e em que o veículo possua um subscription ID vinculado (obrigatoriamente),
    * cujo número da parcela não esteja informado em evento do veículo parceiro
    * e que não tenha sido encaminhado ao parceiro.
    *
     * @return array
     */
    public function buscarItensFaturavais() {
        
        echo $sql = "
                SELECT * FROM (
                        SELECT DISTINCT
                                nfloid,
                                titoid,
                                veiculo_pedido_parceiro.vppasubscription,
                                equipamento_classe.eqcoid,
                                equipamento_classe.eqcdescricao,
                                contrato.condt_ini_vigencia,
                                titulo_venda.titno_parcela,
                                min(titno_parcela) OVER (PARTITION BY nfloid) AS proxima_parcela,
                                contrato_pagamento.cpagnum_parcela ,
                                titulo_venda.titdt_referencia,
                                titulo_venda.titvl_pagamento,
                                titulo_venda.titvl_titulo_venda,
                                (SELECT count(1) FROM veiculo_parceiro_evento WHERE vpesubscription=vppasubscription and vpeno_parcela=titno_parcela) AS existe_evento,
                                (SELECT max(CASE WHEN vpeno_parcela IS NULL THEN 0 ELSE vpeno_parcela END) FROM veiculo_parceiro_evento WHERE vpesubscription=vppasubscription ) AS parcela_retorno
                        FROM
                                nota_fiscal_venda
                        JOIN
                                titulo_venda ON nfloid = titnfloid
                        JOIN
                                nota_fiscal_item_venda ON nflserie = nfiserie AND nfino_numero=nflno_numero
                        JOIN
                                contrato ON connumero = nficonoid
                        JOIN
                                tipo_contrato ON tpcoid = conno_tipo
                        JOIN
                                equipamento_classe ON eqcoid = coneqcoid
                        JOIN
                                contrato_pagamento ON connumero = cpagconoid
                        JOIN
                                veiculo_pedido_parceiro ON vppaconoid = connumero
                        WHERE
                                vppasubscription IS NOT NULL
                        AND
                                tpcdescricao ILIKE 'VIVO%'
                        AND
                                (titencaminhado_parceiro is null OR titencaminhado_parceiro=false )
                        AND
                                nflnatureza ILIKE 'VENDA DE EQUIPAMENTOS'
                        AND
                                titdt_pagamento IS NULL
                        AND
                                titdt_vencimento > now()
                        AND
                                nfldt_cancelamento IS NULL
                        ORDER BY
                            nfloid, titno_parcela
                ) cons
                WHERE
			        (cons.parcela_retorno+1 = cons.titno_parcela)
		        AND
			        cons.existe_evento=0
                AND
			        cons.titno_parcela = cons.proxima_parcela
                ORDER BY
			        cons.nfloid, cons.titno_parcela
                ";
    
        $rs = pg_query($this->conn, $sql);
    
        if(!$rs || pg_num_rows($rs)==0) return null;
         
        return $rs;
    }
        
    /**
     * Atualizar título	para cada registro retornado na busca [RN001],
     * atualizar FLAG identificando que o título foi encaminhado para faturamento do parceiro.
     *
     * @param String $subscription
     * @param Integer $titoid
     * @param String $arquivo
     *
     * @return boolean
     */
    public function gravaHistorico($subscription, $titoid, $arquivo){
        
        $titoid=intval($titoid);
        echo $sql = "
                     INSERT INTO veiculo_parceiro_remessa_faturamento
                            (vprfdt_cadastro, vprfsubscription, vprftitoid, vprfarquivo)
                     VALUES (now(), '".pg_escape_string($subscription)."', $titoid, '".pg_escape_string($arquivo)."');
                     UPDATE titulo_venda SET titencaminhado_parceiro=true WHERE titoid=$titoid;
                    ";
        

        $rs = pg_query($this->conn, $sql);
        
        if(!$rs || pg_affected_rows($rs)==0) return false;
        
        return true;
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
    
    public function fetchObject(&$res){
    	return pg_fetch_object($res);
    }
}


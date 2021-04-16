<?php

/**
 * 
 * @author Willian Ouchi
 * @since 18/04/2013
 * @package modulos/Relatorio/DAO
 */
class RelCustoMedioProdutosDAO {
	
    /**
     * Link de conexão com o banco
     * @var resource
     */
    private $conn;

    /**
     * Construtor
     * @param resource $conexao
     */
    public function __construct($conexao) {
        $this->conn  = $conexao;
    }
    
    
    public function buscaRepresentantes(){
        
        try{
            
            $sql = "
                SELECT
                    representante.repoid AS representante_id,
                    representante.repnome AS representante_nome
                FROM
                    representante
                WHERE
                    representante.repexclusao IS NULL
                ORDER BY 
                    representante.repnome
            ";
            
            ob_start();
            $qrRepresentante = pg_query( $this->conn, $sql );
            ob_end_clean();
            
            if (!is_resource($qrRepresentante)){
                throw new Exception("Erro ao buscar representantes.");
            }
            
            return array( 'error' => false, 'resource' => $qrRepresentante );

        }
        catch(Exception $e){
            
            return array( 'error' => true, 'message' => $e->getMessage() );            
        }
    }
    
    
    public function pesquisaAnalitica( $filtros ){
        
        $filtro = "";
        
        if ( !empty( $filtros['dt_ini'] ) && !empty( $filtros['dt_fim'] ) ) {				
            $filtro .= " AND emvdata BETWEEN '".$filtros['dt_ini']." 00:00:00' AND '".$filtros['dt_fim']." 23:59:59' ";
        }
        
        if ( !empty( $filtros['pesquisar_por'] ) ) {				
            $filtro .= " AND emvtipo = '".$filtros['pesquisar_por']."' ";
        }
        
        if ( !empty( $filtros['representante_responsavel'] ) ) {				
            $filtro .= " AND relrrepoid = ".$filtros['representante_responsavel']." ";
        }
        
        if ( !empty( $filtros['tipo_produto'] ) ) {	
            
            if ( $filtros['tipo_produto'] == "L" ){
                
                $filtro .= " AND prdptioid IN (1, 2 ,3) ";
            }
            elseif( $filtros['tipo_produto'] == "R" ){
                
                $filtro .= " AND prdptioid IN (4, 5) ";
            }
        }

        try{
            
            $sql = "            
                SELECT
                    produto.prdoid AS produto_id,
                    produto.prdproduto AS produto_descricao,
                    representante.repnome AS representante_nome,
                    to_char(estoque_movimentacao.emvdata, 'dd/mm/YYYY') AS data,
                    estoque_movimentacao_tipo.emtdescricao AS movimentacao_tipo,
                    estoque_movimentacao.emvtipo,
                    estoque_movimentacao.emvquantidade AS quantidade,
                    (((entrada_item.entivlr_unit * entrada_item.entiqtde)-(entrada_item.entipis_contabil + entrada_item.enticofins_contabil))/entrada_item.entiqtde) AS custo_medio_unitario,
                    ((((entrada_item.entivlr_unit * entrada_item.entiqtde)-(entrada_item.entipis_contabil + entrada_item.enticofins_contabil))/entrada_item.entiqtde)* estoque_movimentacao.emvquantidade) AS total,
                    entrada.entnota AS nota,
                    entrada.entserie AS serie,
                    fornecedores.forfornecedor AS fornecedor_nome,
                    cliente_view.cliv_dsc AS cliente_nome,
                    ordem_servico.ordconnumero AS contrato_numero,
                    tipo_contrato.tpcdescricao AS contrato_tipo
                FROM
                    estoque_movimentacao
                    LEFT JOIN relacionamento_representante ON relacionamento_representante.relroid = estoque_movimentacao.emvrelroid
                    LEFT JOIN representante ON representante.repoid = relacionamento_representante.relrrepoid
                    INNER JOIN produto ON estoque_movimentacao.emvprdoid = produto.prdoid 
                    INNER JOIN estoque_movimentacao_tipo ON estoque_movimentacao.emvemtoid = estoque_movimentacao_tipo.emtoid 
                    INNER JOIN entrada ON estoque_movimentacao.emventoid = entrada.entoid
                    LEFT JOIN ordem_servico ON estoque_movimentacao.emvordoid = ordem_servico.ordoid 
                    LEFT JOIN produto_tipo ON produto.prdstipioid = produto_tipo.ptioid 
                    LEFT JOIN fornecedores ON entrada.entforoid = fornecedores.foroid
                    LEFT JOIN cliente_view ON ordem_servico.ordclioid = cliente_view.clivoid 
                    LEFT JOIN contrato ON ordem_servico.ordconnumero = contrato.connumero 
                    LEFT JOIN tipo_contrato ON contrato.conno_tipo = tipo_contrato.tpcoid
                    INNER JOIN entrada_item ON entrada.entoid = entrada_item.entientoid
                    LEFT JOIN plano_contabil ON entrada_item.entiplcoid = plcoid	
                WHERE
                    estoque_movimentacao_tipo.emtcalcula_custo_medio IS TRUE
                    AND plano_contabil.plcconta IN ('1110010010501', '1110010010502', '11100100900015')
                    $filtro
            ";
            
            ob_start();
            $qrRelatorio = pg_query($this->conn, $sql);
            ob_end_clean();
            
            if (!is_resource($qrRelatorio)){
                throw new Exception("Houve um erro ao realizar a pesquisa.");
            }

            return array( 'error' => false, 'resource' => $qrRelatorio );
        
        }
        catch(Exception $e){
            
            return array( 'error' => true, 'message' => $e->getMessage() );
            
        }
    }
    
    public function pesquisaSintetica( $filtros ){
        
        $filtro = "";
        
        if ( !empty( $filtros['dt_ini'] ) && !empty( $filtros['dt_fim'] ) ) {				
            $filtro .= " AND emvdata BETWEEN '".$filtros['dt_ini']." 00:00:00' AND '".$filtros['dt_fim']." 23:59:59' ";
        }
        
        if ( !empty( $filtros['pesquisar_por'] ) ) {				
            $filtro .= " AND emvtipo = '".$filtros['pesquisar_por']."' ";
        }
        
        if ( !empty( $filtros['representante_responsavel'] ) ) {				
            $filtro .= " AND relrrepoid = ".$filtros['representante_responsavel']." ";
        }
        
        if ( !empty( $filtros['tipo_produto'] ) ) {	
            
            if ( $filtros['tipo_produto'] == "L" ){
                
                $filtro .= " AND prdptioid IN (1, 2 ,3) ";
            }
            elseif( $filtros['tipo_produto'] == "R" ){
                
                $filtro .= " AND prdptioid IN (4, 5) ";
            }
        }
        
        try{
            
            $sql = "            
                
                SELECT
                    subquery.prdoid AS produto_id,
                    subquery.prdproduto AS produto_descricao,
                    subquery.quantidade,
                    subquery.entoid,
                    (
                        SELECT 
                            ARRAY_AGG(entioid) 
                        FROM 
                            entrada_item	
                        WHERE                             
                            entientoid = ANY(subquery.entoid)  
                            AND entiprdoid = subquery.prdoid	
                    ) as entioids
                FROM
                    (
                    SELECT 
                            produto.prdoid,
                            produto.prdproduto,
                            SUM(estoque_movimentacao.emvquantidade) as quantidade,
                            ARRAY_AGG(DISTINCT entrada.entoid) as entoid			
                    FROM 
                            estoque_movimentacao 
                            INNER JOIN produto ON estoque_movimentacao.emvprdoid = produto.prdoid 
                            LEFT JOIN relacionamento_representante ON estoque_movimentacao.emvrelroid = relacionamento_representante.relroid
                            LEFT JOIN representante ON representante.repoid = relacionamento_representante.relrrepoid
                            INNER JOIN entrada ON estoque_movimentacao.emventoid = entrada.entoid
                            INNER JOIN estoque_movimentacao_tipo ON estoque_movimentacao.emvemtoid = estoque_movimentacao_tipo.emtoid 		                            
                    WHERE
                            estoque_movimentacao_tipo.emtcalcula_custo_medio IS TRUE
                            AND entoid IN (
                                SELECT 
                                    entientoid
                                FROM 
                                    entrada_item
                                    LEFT JOIN plano_contabil ON plcoid = entiplcoid	
                                WHERE 
                                    plano_contabil.plcconta IN ('1110010010501', '1110010010502', '11100100900015')
                                GROUP BY 
                                    entientoid		
                            )
                            $filtro
                    GROUP BY  
                            produto.prdoid,
                            produto.prdproduto	
                    )as subquery
		
            ORDER BY 
		subquery.prdoid
            ";
            
            ob_start();
            $qrRelatorio = pg_query($this->conn, $sql);
            ob_end_clean();
            
            if (!is_resource($qrRelatorio)){
                throw new Exception("Houve um erro ao realizar a pesquisa.");
            }

            return array( 'error' => false, 'resource' => $qrRelatorio );
        
        }
        catch(Exception $e){
            
            return array( 'error' => true, 'message' => $e->getMessage() );
            
        }
    }
    
    
    public function calculaMediaProduto( $produto_id, $entrada_itens_ids ){
            
            $custo_medio_item = array();
            $custo_medio_total = 0;
            $media_item = 0;
            $total_itens = 0;
            $quantidade_itens = 0;
            
            try{
                $sql = "
                    SELECT
                        COALESCE(entivlr_unit, 0) AS valor_unitario,
                        COALESCE(entiqtde, 0) AS quantidade,
                        COALESCE(entipis_contabil, 0) AS pis,
                        COALESCE(enticofins_contabil, 0) AS cofins
                    FROM
                        entrada_item
                    WHERE
                        entiprdoid = $produto_id
                        AND entioid IN ( $entrada_itens_ids ) 
                ";
                
                ob_start();
                $qrEntradaItem = pg_query($this->conn, $sql);
                ob_end_clean();

                if ( !is_resource($qrEntradaItem) ){
                    throw new Exception("Houve um erro ao calcular o custo médio.");
                }
                
                while( $entrada_item = pg_fetch_assoc($qrEntradaItem) ){
                    
                    $custo_medio_item[] = ( ( ( $entrada_item['valor_unitario'] * $entrada_item['quantidade'] ) - ( $entrada_item['pis'] + $entrada_item['cofins'] ) ) /$entrada_item['quantidade'] );
                }                
                
                $quantidade_itens = count($custo_medio_item);
                
                foreach ( $custo_medio_item as $media_item){
                    $total_itens += $media_item;
                }
                
                $custo_medio_total = $total_itens / $quantidade_itens;
                
                return array( 'error' => false, 'value' => $custo_medio_total );
            }
            catch(Exception $e){

                return array( 'error' => true, 'message' => $e->getMessage() );
            }             
  
    }
    
    
}
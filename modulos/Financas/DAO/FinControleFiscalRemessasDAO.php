<?php
class FinControleFiscalRemessasDAO {

        private $conn;
        public $log = array();

        public function __construct($conn) {
            $this->conn = $conn;
        }

        public function transactionBegin() {
            pg_query($this->conn, "BEGIN;");
        }

        public function transactionCommit() {
            pg_query($this->conn, "COMMIT");
        }

        public function transactionRollback() {
            pg_query($this->conn, "ROLLBACK;");
        }


    public function tiposMovimentacao() {

        $sql = "SELECT  emtoid, emtdescricao
                FROM estoque_movimentacao_tipo 
                INNER JOIN estoque_movimentacao_tipo_pagina ON emtoid=emtpemtoid AND emtppagoid=(SELECT pagoid FROM pagina WHERE pagurl='man_remessa_recbto_estoque.php')
                WHERE  emtstatus='A' 
                ORDER BY emtdescricao";

        $rs = pg_query($this->conn, $sql);

        if (!$rs) {
            throw new Exception('Falha na pesquisa dos tipos de movimentação.');
        }

        $tipos = array();
        while ($tipo = pg_fetch_object($rs)) {
            $voTipo = new stdClass();
            $voTipo->key = $tipo->emtoid;
            $voTipo->value = $tipo->emtdescricao;

            $tipos[] = $voTipo;
        }

        return $tipos;
    }

    public function estoqueRemessaStatus() {

        $sql = "SELECT ersoid,ersdescricao
                FROM estoque_remessa_status
                WHERE ersexclusao IS NULL
                ORDER BY ersdescricao";

        $rs = pg_query($this->conn, $sql);

        if (!$rs) {
            throw new Exception('Falha na pesquisa estoque remessa status.');
        }

        $tipos = array();
        while ($tipo = pg_fetch_object($rs)) {
            $voTipo = new stdClass();
            $voTipo->key = $tipo->ersoid;
            $voTipo->value = $tipo->ersdescricao;

            $tipos[] = $voTipo;
        }

        return $tipos;
    }

      public function retornaRepresentante() {

        $sql = "SELECT repoid,repnome
                FROM representante
                WHERE repexclusao IS NULL
                ORDER BY repnome";

        $rs = pg_query($this->conn, $sql);

        if (!$rs) {
            throw new Exception('Falha na pesquisa representante.');
        }

        $tipos = array();
        while ($tipo = pg_fetch_object($rs)) {
            $voTipo = new stdClass();
            $voTipo->key = $tipo->repoid;
            $voTipo->value = $tipo->repnome;

            $tipos[] = $voTipo;
        }

        return $tipos;
    }
    
    public function retornaFornecedor() {

        $sql = "SELECT foroid,forfornecedor
                FROM fornecedores
                WHERE fordt_exclusao IS NULL
                ORDER BY forfornecedor";

        $rs = pg_query($this->conn, $sql);

        if (!$rs) {
            throw new Exception('Falha na pesquisa representante.');
        }

        $tipos = array();
        while ($tipo = pg_fetch_object($rs)) {
            $voTipo = new stdClass();
            $voTipo->key = $tipo->foroid;
            $voTipo->value = $tipo->forfornecedor;

            $tipos[] = $voTipo;
        }

        return $tipos;
    }




        public function pesquisaEstoqueRemessa($parametros){

            
            if ($parametros ['nRemessa']>0) {
                    $filtro .= " AND esroid=".$parametros ['nRemessa'];
            }elseif($parametros ['nfRemessa']>0){
                $filtro .= " AND esrpnfno_numero = ". trim($parametros ['nfRemessa']);
            }else{

                if ($parametros ['dt_ini'] != '' || ! empty ( $parametros ['dt_ini'] )) {
                    $filtro .= " AND esrdata >= '" . implode('-',array_reverse(explode('/',$parametros ['dt_ini']))) . " 00:00:00'";
                }
                if ($parametros ['dt_fim'] != '' || ! empty ( $parametros ['dt_fim'] )) {
                    $filtro .= " AND esrdata <= '" . implode('-',array_reverse(explode('/',$parametros ['dt_fim']))) . " 23:59:59'";
                }

                if ($parametros ['statusRemessa']>0) {
                    $filtro .= " AND esrersoid =" . $parametros ['statusRemessa'];
                }

                if ($parametros ['tipoMovimentacao']>0) {
                    $filtro .= " AND esremtoid  =" . $parametros ['tipoMovimentacao'];
                }

                if ($parametros ['repreRespRem']>0) {
                    $filtro .= " AND r.repoid =" . $parametros ['repreRespRem'];
                }

                if ($parametros ['repreRespDest']>0) {
                    $filtro .= " AND r2.repoid =" . $parametros ['repreRespDest'];
                }

                if ($parametros ['nSerie'] != '' || ! empty ( $parametros ['nSerie'] )) {
                    $filtro .= " AND esrinumero_serie =  '" . trim($parametros ['nSerie']) . "'";
                }
                if ($parametros ['repreFornDest']>0) {
                    $filtro .= " AND esrforoid =" . $parametros ['repreFornDest'];
                }
                
                if ($parametros ['numero_pedido']>0) {
                    $filtro .= " AND esrpnfoid =" . $parametros ['numero_pedido'];
                }
                
            }
            
            
            if($parametros['tipoRelatorio']=='S'){
            
                $sql_psq = "
                    SELECT 
                        esroid
                        ,TO_CHAR(esrdata,'DD/MM/YYYY') AS data
                        ,esrdata
                        ,prdoid
                        ,esripatrimonio
                        ,esrinumero_serie
                        ,prdproduto
                        ,COALESCE(esriqtde, 1) as quantidade
                        ,esrrelroid_emitente --remetente
                        ,esrrelroid -- destinatario
                        ,esrforoid --forncedor destinatário
                        ,esrpnfoid
                        ,esrpnfno_numero
                        ,ersdescricao
                        ,esrpnfno_numero_simbolico
                FROM estoque_remessa
                        JOIN estoque_remessa_item ON esrioid=esroid
                        JOIN produto ON prdoid = esrirefoid
                        JOIN produto_tipo ON prdptioid = ptioid
                        JOIN estoque_remessa_status ON ersoid=esrersoid
                        JOIN relacionamento_representante rr ON rr.relroid=esrrelroid_emitente
                        JOIN representante r ON r.repoid=rr.relrrepoid
                        JOIN relacionamento_representante rr2 ON rr2.relroid=esrrelroid
                        JOIN representante r2 ON r2.repoid=rr2.relrrepoid

                WHERE 1=1
                        AND  esritipo!=''

                ".$filtro." 

                UNION

                SELECT 
                        esroid
                        ,TO_CHAR(esrdata,'DD/MM/YYYY') AS data
                        ,esrdata
                        ,prdoid
                        ,esripatrimonio
                        ,esrinumero_serie
                        ,prdproduto
                        ,COALESCE(esriqtde, 1) as quantidade
                        ,esrrelroid_emitente --remetente
                        ,esrrelroid -- destinatario
                        ,esrforoid --forncedor destinatário
                        ,esrpnfoid
                        ,esrpnfno_numero
                        ,ersdescricao
                        ,esrpnfno_numero_simbolico

                FROM estoque_remessa
                        JOIN estoque_remessa_item ON esrioid=esroid
                        JOIN imobilizado ON esripatrimonio = imobpatrimonio AND esrinumero_serie = imobserial
                        JOIN produto ON prdoid = imobprdoid
                        JOIN produto_tipo ON prdptioid = ptioid
                        JOIN estoque_remessa_status ON ersoid=esrersoid
                        JOIN relacionamento_representante rr ON rr.relroid=esrrelroid_emitente
                        JOIN representante r ON r.repoid=rr.relrrepoid
                        JOIN relacionamento_representante rr2 ON rr2.relroid=esrrelroid
                        JOIN representante r2 ON r2.repoid=rr2.relrrepoid
                WHERE 1=1
                    ".$filtro." ORDER BY esrdata";

            }elseif($parametros['tipoRelatorio']=='P'){
                
                $groupby="
                    GROUP BY
			esroid
                        ,data
                        ,esrdata
                        ,prdoid
                        ,prdproduto
                        ,esrrelroid_emitente
                        ,esrrelroid
                        ,esrforoid
                        ,esrpnfoid
                        ,esrpnfno_numero
                        ,ersdescricao
                        ,esrpnfno_numero_simbolico";
                
                
                
                $sql_psq = "
                    SELECT 
                        esroid
                        ,TO_CHAR(esrdata,'DD/MM/YYYY') AS data
                        ,esrdata
                        ,prdoid
                        ,prdproduto
                        ,SUM(COALESCE(esriqtde, 1)) as quantidade
                        ,esrrelroid_emitente --remetente
                        ,esrrelroid -- destinatario
                        ,esrforoid --forncedor destinatário
                        ,esrpnfoid
                        ,esrpnfno_numero
                        ,ersdescricao
                        ,esrpnfno_numero_simbolico
                FROM estoque_remessa
                        JOIN estoque_remessa_item ON esrioid=esroid
                        JOIN produto ON prdoid = esrirefoid
                        JOIN produto_tipo ON prdptioid = ptioid
                        JOIN estoque_remessa_status ON ersoid=esrersoid
                        JOIN relacionamento_representante rr ON rr.relroid=esrrelroid_emitente
                        JOIN representante r ON r.repoid=rr.relrrepoid
                        JOIN relacionamento_representante rr2 ON rr2.relroid=esrrelroid
                        JOIN representante r2 ON r2.repoid=rr2.relrrepoid
                WHERE 1=1
                        AND  esritipo!=''

                ".$filtro.$groupby." 

                UNION

                SELECT 
                        esroid
                        ,TO_CHAR(esrdata,'DD/MM/YYYY') AS data
                        ,esrdata
                        ,prdoid
                        ,prdproduto
                        ,SUM(COALESCE(esriqtde, 1)) as quantidade
                        ,esrrelroid_emitente --remetente
                        ,esrrelroid -- destinatario
                        ,esrforoid --forncedor destinatário
                        ,esrpnfoid
                        ,esrpnfno_numero
                        ,ersdescricao
                        ,esrpnfno_numero_simbolico

                FROM estoque_remessa
                        JOIN estoque_remessa_item ON esrioid=esroid
                        JOIN imobilizado ON esripatrimonio = imobpatrimonio AND esrinumero_serie = imobserial
                        JOIN produto ON prdoid = imobprdoid
                        JOIN produto_tipo ON prdptioid = ptioid
                        JOIN estoque_remessa_status ON ersoid=esrersoid
                        JOIN relacionamento_representante rr ON rr.relroid=esrrelroid_emitente
                        JOIN representante r ON r.repoid=rr.relrrepoid
                        JOIN relacionamento_representante rr2 ON rr2.relroid=esrrelroid
                        JOIN representante r2 ON r2.repoid=rr2.relrrepoid
                WHERE 1=1
                    ".$filtro.$groupby." ORDER BY esrdata";
                
            }else{
                $groupby="
                    GROUP BY
			esroid
                        ,data
                        ,esrdata
                        ,prdoid
                        ,prdproduto
                        ,esrrelroid_emitente
                        ,esrrelroid
                        ,esrforoid
                        ,esrpnfoid
                        ,esrpnfno_numero
                        ,ersdescricao
                        ,esrpnfno_numero_simbolico";
                
                
                
                $sql_psq = "
                    SELECT 
                        esroid
                        ,TO_CHAR(esrdata,'DD/MM/YYYY') AS data
                        ,esrdata
                        ,esrrelroid_emitente --remetente
                        ,esrrelroid -- destinatario
                        ,esrforoid --forncedor destinatário
                        ,esrpnfoid
                        ,esrpnfno_numero
                        ,ersdescricao
                        ,esrpnfno_numero_simbolico
                FROM estoque_remessa
                        JOIN estoque_remessa_item ON esrioid=esroid
                        JOIN produto ON prdoid = esrirefoid
                        JOIN produto_tipo ON prdptioid = ptioid
                        JOIN estoque_remessa_status ON ersoid=esrersoid
                        JOIN relacionamento_representante rr ON rr.relroid=esrrelroid_emitente
                        JOIN representante r ON r.repoid=rr.relrrepoid
                        JOIN relacionamento_representante rr2 ON rr2.relroid=esrrelroid
                        JOIN representante r2 ON r2.repoid=rr2.relrrepoid
                WHERE 1=1
                        AND  esritipo!=''

                ".$filtro." 

                UNION

                SELECT 
                        esroid
                        ,TO_CHAR(esrdata,'DD/MM/YYYY') AS data
                        ,esrdata
                        ,esrrelroid_emitente --remetente
                        ,esrrelroid -- destinatario
                        ,esrforoid --forncedor destinatário
                        ,esrpnfoid
                        ,esrpnfno_numero
                        ,ersdescricao
                        ,esrpnfno_numero_simbolico

                FROM estoque_remessa
                        JOIN estoque_remessa_item ON esrioid=esroid
                        JOIN imobilizado ON esripatrimonio = imobpatrimonio AND esrinumero_serie = imobserial
                        JOIN produto ON prdoid = imobprdoid
                        JOIN produto_tipo ON prdptioid = ptioid
                        JOIN estoque_remessa_status ON ersoid=esrersoid
                        JOIN relacionamento_representante rr ON rr.relroid=esrrelroid_emitente
                        JOIN representante r ON r.repoid=rr.relrrepoid
                        JOIN relacionamento_representante rr2 ON rr2.relroid=esrrelroid
                        JOIN representante r2 ON r2.repoid=rr2.relrrepoid
                WHERE 1=1
                    ".$filtro." ORDER BY esrdata";
            }
   
            
            //$sql_psq.=" LIMIT 10";
            
            
//           echo '<pre>';
//            print_r($sql_psq);
//           echo '</pre>';
//           

        $rs = pg_query($this->conn, $sql_psq);

        if (!$rs) {
            throw new Exception('Falha na pesquisa .');
        }

    
        return pg_fetch_all($rs);
    
    }
   
    
    
    /**
    Método para busca representantes em massa conforme pesquisaEstoqueRemessa
     * @param array $arr lista de relroid
     * @return array Array contendo nome, cnpj,uf
     */
    public function getRepresentante(Array $arr){
        
        
        $arrReturn=array();
        
        if(sizeof($arr)>0){
        
            $SQL="
                SELECT
                        repnome
                        ,repcgc
                        ,endvuf
                        ,relroid
                FROM relacionamento_representante
                        JOIN representante ON repoid = relrrepoid
                        JOIN endereco_representante ON endvrepoid=repoid
                WHERE relroid IN(".implode(',',$arr).")
            ";
                        
            $res=pg_query($this->conn, $SQL);

            while($row=pg_fetch_object($res)){

                $arrReturn[$row->relroid]['nome']=$row->repnome;
                $arrReturn[$row->relroid]['cnpj']=$row->repcgc;
                $arrReturn[$row->relroid]['uf']=$row->endvuf;

            }
        }
        
        return $arrReturn;
        
    }
    
    
    /**
    Método para busca fornecedores em massa conforme pesquisaEstoqueRemessa
     * @param array $arr lista de foroid
     * @return array Array contendo nome, cnpj,uf
     */
    public function getFornecedor(Array $arr){
        
        
        $arrReturn=array();
        
        
        if(sizeof($arr)>0){
       
            $SQL="
                SELECT
                    foroid
                    ,forfornecedor
                    ,fordocto
                    ,forestado
            FROM fornecedores
            WHERE foroid IN(".implode(',',$arr).")
            ";

            $res=pg_query($this->conn, $SQL);

            while($row=pg_fetch_object($res)){

                $arrReturn[$row->foroid]['nome']=$row->forfornecedor;
                $arrReturn[$row->foroid]['cnpj']=$row->fordocto;
                $arrReturn[$row->foroid]['uf']=$row->forestado;

            }
        }
        return $arrReturn;
        
    } 
    

}
<?php

/**
 * Description of FinControleFiscalEquipamentoMovelDAO
 *
 * @author willianricardo
 */
class FinControleFiscalInstalacoesDAO {

    private $conn;    
    private $dataInicio;
    private $dataFim;
    private $contrato;
    private $cliente;
    private $tipoRelatorio;
    private $representante;
    private $serie;
    private $nfRetornoSimbolico;
    private $nfRemessaSimbolico;
    private $possuiNFRemessaSimbolico;
    private $possuiNFRetornoSimbolico;
    
    public function __construct($conn) {

        $this->conn = $conn;
    }

    public function getConn() {
        return $this->conn;
    }

    public function getDataInicio() {
        return $this->dataInicio;
    }

    public function getDataFim() {
        return $this->dataFim;
    }

    public function getContrato() {
        return $this->contrato;
    }

    public function getCliente() {
        return $this->cliente;
    }

    public function getTipoRelatorio() {
        return $this->tipoRelatorio;
    }

    public function getRepresentante() {
        return $this->representante;
    }

    public function getSerie() {
        return $this->serie;
    }

    public function getNfRetornoSimbolico() {
        return $this->nfRetornoSimbolico;
    }

    public function getNfRemessaSimbolico() {
        return $this->nfRemessaSimbolico;
    }

    public function getPossuiNFRemessaSimbolico() {
        return $this->possuiNFRemessaSimbolico;
    }

    public function getPossuiNFRetornoSimbolico() {
        return $this->possuiNFRetornoSimbolico;
    }

    public function setConn($conn) {
        $this->conn = $conn;
        return $this;
    }

    public function setDataInicio($dataInicio) {
        $this->dataInicio = implode('-',array_reverse(explode('/',$dataInicio)));
        return $this;
    }

    public function setDataFim($dataFim) {
        $this->dataFim = implode('-',array_reverse(explode('/',$dataFim)));
        return $this;
    }

    public function setContrato($contrato) {
        $this->contrato = $contrato;
        return $this;
    }

    public function setCliente($cliente) {
        $this->cliente = $cliente;
        return $this;
    }

    public function setTipoRelatorio($tipoRelatorio) {
        $this->tipoRelatorio = $tipoRelatorio;
        return $this;
    }

    public function setRepresentante($representante) {
        $this->representante = $representante;
        return $this;
    }

    public function setSerie($serie) {
        $this->serie = $serie;
        return $this;
    }

    public function setNfRetornoSimbolico($nfRetornoSimbolico) {
        $this->nfRetornoSimbolico = $nfRetornoSimbolico;
        return $this;
    }

    public function setNfRemessaSimbolico($nfRemessaSimbolico) {
        $this->nfRemessaSimbolico = $nfRemessaSimbolico;
        return $this;
    }

    public function setPossuiNFRemessaSimbolico($possuiNFRemessaSimbolico) {
        $this->possuiNFRemessaSimbolico = $possuiNFRemessaSimbolico;
        return $this;
    }

    public function setPossuiNFRetornoSimbolico($possuiNFRetornoSimbolico) {
        $this->possuiNFRetornoSimbolico = $possuiNFRetornoSimbolico;
        return $this;
    }

    public function getOrdemServico($tipo){
        
        $SQL = "SELECT
                    ordoid as id_os 
                    ,ordconnumero as contrato
                    ,clinome as nome_cliente
                    ,CASE WHEN clitipo = 'J'
                            THEN clino_cgc
                            ELSE clino_cpf
                    END as cliente_cnpj
                    ,repnome as representante_nome
                    ,repcgc as representante_cnpj
                    ,pedpnfoid as  numero_pedido
                    ,pnftppedido as tipo_pedido
                    ,pnfnf_numero
                    ,TO_CHAR((SELECT MAX(cmidata) FROM comissao_instalacao WHERE cmiord_serv = ordoid),'DD/MM/YYYY') as dataos
                FROM ordem_servico
                    JOIN ordem_servico_item ON ositordoid = ordoid AND ositstatus = 'C'		
                    JOIN os_tipo_item ON ositotioid = otioid
                    JOIN instalador ON itloid = orditloid     
                    JOIN clientes ON ordclioid = clioid
                    JOIN representante ON itlrepoid = repoid 
                    LEFT JOIN pedido_origem ON ordoid = pedorigem_oid
                    LEFT JOIN pedido_nota_fiscal ON pedpnfoid = pnfoid
                    LEFT JOIN pedido_origem_tipo ON potoid=pedpotoid AND potnome='".$tipo."'
                WHERE 1=1
                    AND ordstatus = 3
                    AND (SELECT MAX(cmidata) FROM comissao_instalacao WHERE cmiord_serv = ordoid) >='".$this->dataInicio." 00:00:00' and (SELECT MAX(cmidata) FROM comissao_instalacao WHERE cmiord_serv = ordoid) <='".$this->dataFim." 23:59:59'
                    ";
                    if($tipo==='OS_INSTALACAO'){
                        $SQL.=" AND ordostoid=3";
                    }else{
                        $SQL.=" AND ((ordostoid IN (1,2,9)
                                    OR
                                    (ordostoid=4) and (otioid IN (19,104,893,894)))
                                )
                       
                        ";
                    }
                    
                if($this->contrato>0){
                    $SQL.=" AND ordconnumero=".$this->contrato;
                }
                if($this->nfRetornoSimbolico>0){
                    $SQL.=" AND pnftppedido='Compra' 
                            AND pnfnf_numero=".$this->nfRetornoSimbolico;
                }
                
                if($this->nfRemessaSimbolico>0){
                    $SQL.=" AND pnftppedido='Venda' 
                            AND pnfnf_numero=".$this->nfRemessaSimbolico;
                }
                
                if($this->representante>0){
                    $SQL.=" AND itlrepoid=".$this->representante;
                }
                
                if($this->cliente>0){
                    $SQL.=" AND ordclioid=".$this->cliente;
                }
        
                if($this->possuiNFRetornoSimbolico!=''){
                    if($this->possuiNFRetornoSimbolico==1){
                        $SQL.=" AND pnftppedido='Compra' AND pnfnf_numero IS NOT NULL";
                    }else{
                        $SQL.=" AND pnftppedido='Compra' AND pnfnf_numero IS NULL";
                    }
                }
                
                if($this->possuiNFRemessaSimbolico!=''){
                    if($this->possuiNFRemessaSimbolico==1){
                        $SQL.=" AND pnftppedido='Venda' AND pnfnf_numero IS NOT NULL";
                    }else{
                        $SQL.=" AND pnftppedido='Venda' AND pnfnf_numero IS NULL";
                    }
                }
                
        
               $SQL.="
                   GROUP BY
                    ordoid
                    ,clinome
                    ,repnome
                    ,clitipo
                    ,clino_cgc
                    ,clino_cpf
                    ,repcgc
                    ,pedpnfoid
                    ,pnftppedido
                    ,pnfnf_numero
                    ,dataos
                ORDER BY
                    ordoid
                    ,ordclioid
              
                    
             ";
        
//            echo '<pre>';
//                print_r($SQL);
//            echo '</pre>';            
               
        $result = pg_query($this->getConn(), $SQL);
        
//        $rows = pg_affected_rows($result);
//        
//        $param = Array('tipo_relatorio' => 'serial');
        
        return $result;//Array($result, $rows, $param);
        
    }

    public function consultaTipoProduto(){
        
//        if($this->getAba() === "envio"){
//            $dataEnvio = " to_char(ceeenvio, 'DD/MM/YYYY') as data_envio, ";
//            $groupDate = " ceeenvio ";
//        }else{
//            $dataEnvio = " to_char(condtcodigo_postal, 'DD/MM/YYYY') as data_envio, ";
//            $groupDate = " condtcodigo_postal ";
//        }
//        
//        $sql = "SELECT 
//                    $dataEnvio
//                    connumero as contrato,
//                    equno_serie as serie,
//                    equprdoid as codigo_produto,
//                    prdproduto as descricao_produto,
//                    prdncms_codigo as codigo_ncm,
//                    clioid,
//                    clinome as nome_cliente,
//                    pnfoid as numero_pedido,
//                    pnfnf_numero as nf_remessa,
//                    count(equprdoid) as quantidade ,
//                    pnftppedido as tp_pedido,
//                    pnfbloqueada as bloqueada,
//                    SUM((select pcmcusto_medio from produto_custo_medio where pcmprdoid = prdoid ORDER BY pcmoid DESC limit 1)) as preco_unitario_1,
//                    SUM((select (entivlr_unit + entivlr_ipi) as preco_unitario from entrada_item where entiprdoid = prdoid order by entioid desc limit 1)) as preco_unitario_2,
//                    9.99 as preco_unitario_3,
//                    case when clitipo = 'J' then clino_cgc else clino_cpf end as cliente_cnpj,
//                    case when clitipo = 'J' then cliuf_com else cliuf_res end as cliente_uf
//                FROM contrato ";
//        
//                $sql .= ($this->getAba() === 'envio' ? " JOIN contrato_envio_eqpto ON contrato_envio_eqpto.ceeconoid = contrato.connumero " : "");
//                
//                $sql .= "
//                JOIN clientes ON clioid = conclioid 
//                JOIN equipamento ON equoid = conequoid
//                JOIN produto ON prdoid = equprdoid
//                LEFT JOIN pedido_origem ON pedorigem_oid = connumero 
//                LEFT JOIN pedido_origem_tipo ON potoid = pedpotoid ";
//        
//                $sql .= ($this->getAba() === "envio" ? ' AND potnome = ' . "'CONTROLE_MOVEL' " : " AND potnome = 'CONTRATO' ");
// 
//                $sql .= " LEFT JOIN pedido_nota_fiscal ON pnfoid = pedpnfoid ";
//                
//                $sql .= " WHERE 1 = 1 "; 
//                
//                $sql .= ($this->getContrato() ? ' AND connumero = ' . $this->getContrato() : '');
//                
//                if($this->getAba() === "envio"){
//                    
//                    $sql .= " AND ceeenvio between '" . $this->getDataInicio() . "' AND '" . $this->getDataFim() . "'";
//                }else{
//                    
//                    $sql .= " AND concodigo_postal IS NOT NULL ";
//                    
//                    $sql .= " AND condtcodigo_postal between '" . $this->getDataInicio() . "' AND '" . $this->getDataFim() . "'";
//                }
//   
//        $sql .= ($this->getNumeroPedido()              ? ' AND pnfoid        = ' . $this->getNumeroPedido() : '');
//        
//        $sql .= ($this->getCliente() && $this->getCliente() !== 'null' ? ' AND clioid     = ' . $this->getCliente()      : '');
//        
//        $sql .= ($this->getRemessa()                   ? ' AND pnfnf_numero  = ' . $this->getRemessa()      : '');
//        
//        $sql .= ($this->getSerie()                     ? " AND equno_serie    = '" . $this->getSerie() . "'" : '');
//        
//        $sql .= ($this->getPossuiNFRemessa() === 'nao' ? ' AND pnfnf_numero IS NULL'                        : '');
//        
//        $sql .= ($this->getPossuiNFRemessa() === 'sim' ? ' AND pnfnf_numero IS NOT NULL'                    : '');
//
//        $sql .= " GROUP BY
//                        $groupDate,
//                        connumero,
//                        equno_serie,
//                        pnfoid,
//                        equprdoid,
//                        prdproduto,
//                        prdncms_codigo,
//                        clinome,
//                        case when clitipo = 'J' then clino_cgc else clino_cpf end, 
//                        case when clitipo = 'J' then cliuf_com else cliuf_res end,
//                        pnfnf_numero,
//                        clioid
//                ORDER by $groupDate";
//        
//        $result = pg_query($this->getConn(), $sql);
//        
//        $rows = pg_affected_rows($result);
//        
//        $param = Array('tipo_relatorio' => 'produto', 'aba' => $this->getAba());
//        
//        return Array($result, $rows, $param);
        
    }
  
    public function pesquisarCliente($nome){
        
        $sql = "With cliente as (
                    SELECT clioid, clinome FROM clientes where clinome ilike '%".$nome."%'
                    )
                select json_agg(cliente) as json from cliente;";
          
        $result = pg_query($this->getConn(), $sql);
        
        $res = pg_fetch_array($result);
                
        return $res[0];
       
    }
  
    public function representante(){
        
        $sql = "With representante as (
                SELECT 
                    repoid,
                    repnome 
                FROM 
                    representante 
                WHERE 
                    repexclusao IS NULL 
                AND 
                    repoid NOT IN (SELECT 
                                        relrrep_terceirooid 
                                   FROM 
                                        relacionamento_representante 
                                   WHERE 
                                        relrrepoid<>relrrep_terceirooid)
                AND (reprevenda = 't' OR repinstalacao = 't' OR repassistencia = 't') ORDER BY repnome
                ) select json_agg(representante) as json from representante;";
        
        $result = pg_query($this->getConn(), $sql);
        
        $res = pg_fetch_array($result);

        return utf8_decode($res[0]);
       
    }

}
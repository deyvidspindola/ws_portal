<?php

/**
 * Description of FinControleFiscalEquipamentoMovelDAO
 *
 * @author willianricardo
 */
class FinControleFiscalEquipamentoMovelDAO {

    private $conn;
    private $contrato;
    private $serie;
    private $remessa;
    private $cliente;
    private $tipoRelatorio;
    private $possuiNFRemessa;
    private $dataInicio;
    private $dataFim;
    private $numeroPedido;
    private $aba;

    public function __construct($conn) {

        $this->conn = $conn;
    }

    public function getConn() {
        return $this->conn;
    }

    public function getContrato() {
        return $this->contrato;
    }

    public function getSerie() {
        return $this->serie;
    }

    public function getRemessa() {
        return $this->remessa;
    }

    public function getCliente() {
        return $this->cliente;
    }

    public function getTipoRelatorio() {
        return $this->tipoRelatorio;
    }

    public function getPossuiNFRemessa() {
        return $this->possuiNFRemessa;
    }

    public function getDataInicio() {
        return $this->dataInicio;
    }

    public function getDataFim() {
        return $this->dataFim;
    }

    public function getNumeroPedido() {
        return $this->numeroPedido;
    }
    
    public function getAba() {
        return $this->aba;
    }
    
    public function setConn($conn) {
        $this->conn = $conn;
        return $this;
    }

    public function setContrato($contrato) {
        $this->contrato = $contrato;
        return $this;
    }

    public function setSerie($serie) {
        $this->serie = $serie;
        return $this;
    }

    public function setRemessa($remessa) {
        $this->remessa = $remessa;
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

    public function setPossuiNFRemessa($possuiNFRemessa) {
        $this->possuiNFRemessa = $possuiNFRemessa;
        return $this;
    }

    public function setDataInicio($dataInicio) {
        
        $date = explode("/", $dataInicio);
        
        $data = $date[2].'-'.$date[1].'-'.$date[0];
        
        $this->dataInicio = $data;
        return $this;
    }

    public function setDataFim($dataFim) {
        
        $date = explode("/", $dataFim);
        
        $data = $date[2].'-'.$date[1].'-'.$date[0];
        
        $this->dataFim = $data;
        return $this;
    }

    public function setNumeroPedido($numeroPedido) {
        $this->numeroPedido = $numeroPedido;
        return $this;
    }
    
    public function setAba($aba) {
        $this->aba = $aba;
        return $this;
    }

    public function consultaTipoSerial(){
        
        if($this->getAba() === "envio"){
            $dataEnvio = " to_char(ceeenvio, 'DD/MM/YYYY') as data_envio, ";
        }else{
            $dataEnvio = " to_char(condtcodigo_postal, 'DD/MM/YYYY') as data_envio, ";
        }
        
        $sql = "SELECT 
                    $dataEnvio
                    connumero as contrato,
                    equno_serie as serie,
                    equprdoid as codigo_produto,
                    prdproduto as descricao_produto,
                    prdncms_codigo as codigo_ncm,
                    clioid,
                    clinome as nome_cliente,
                    pnfoid as numero_pedido,
                    pnfnf_numero as nf_remessa,
                    1 as quantidade,
                    pnftppedido as tp_pedido,
                    pnfbloqueada as bloqueada,
                    (select pcmcusto_medio from produto_custo_medio where pcmprdoid = prdoid ORDER BY pcmoid DESC limit 1) as preco_unitario_1,
                    (select (entivlr_unit + entivlr_ipi) as preco_unitario from entrada_item where entiprdoid = prdoid order by entioid desc limit 1) as preco_unitario_2,
                    9.99 as preco_unitario_3,
                    case when clitipo = 'J' then clino_cgc else clino_cpf end as cliente_cnpj,
                    case when clitipo = 'J' then cliuf_com else cliuf_res end as cliente_uf
                FROM contrato ";
        
                $sql .= ($this->getAba() === 'envio' ? " JOIN contrato_envio_eqpto ON contrato_envio_eqpto.ceeconoid = contrato.connumero " : "");
                
                $sql .= "
                JOIN clientes ON clioid = conclioid 
                JOIN equipamento ON equoid = conequoid
                JOIN produto ON prdoid = equprdoid
                JOIN pedido_origem ON pedorigem_oid = connumero 
                JOIN pedido_origem_tipo ON potoid = pedpotoid ";
        
                $sql .= ($this->getAba() === "envio" ? ' AND potnome = ' . "'CONTROLE_MOVEL' " : " AND potnome = 'CONTRATO' ");
 
                $sql .= " LEFT JOIN pedido_nota_fiscal ON pnfoid = pedpnfoid ";
                
                $sql .= " WHERE 1 = 1 "; 
                
                $sql .= ($this->getContrato() ? ' AND connumero = ' . $this->getContrato() : '');
                
                if($this->getAba() === "envio"){
                    
                    $sql .= " AND ceeenvio between '" . $this->getDataInicio() . "' AND '" . $this->getDataFim() . "'";
                }else{
                    
                    $sql .= " AND concodigo_postal IS NOT NULL ";
                    
                    $sql .= " AND condtcodigo_postal between '" . $this->getDataInicio() . "' AND '" . $this->getDataFim() . "'";
                }
   
        $sql .= ($this->getNumeroPedido()              ? ' AND pnfoid        = ' . $this->getNumeroPedido() : '');
        
        $sql .= ($this->getCliente() && $this->getCliente() !== 'null' ? ' AND clioid     = ' . $this->getCliente()      : '');
        
        $sql .= ($this->getRemessa()                   ? ' AND pnfnf_numero  = ' . $this->getRemessa()      : '');
        
        $sql .= ($this->getSerie()                     ? " AND equno_serie    = '" . $this->getSerie() . "'" : '');
        
        $sql .= ($this->getPossuiNFRemessa() === 'nao' ? ' AND pnfnf_numero IS NULL'                        : '');
        
        $sql .= ($this->getPossuiNFRemessa() === 'sim' ? ' AND pnfnf_numero IS NOT NULL'                    : '');
        
        $result = pg_query($this->getConn(), $sql);
        
        
//        echo '<pre>';
//            print_r($sql);
//        echo '</pre>';
        
        
        $rows = pg_affected_rows($result);
        
        $param = Array('tipo_relatorio' => 'serial', 'aba' => $this->getAba());
        
        return Array($result, $rows, $param);
        
    }

    public function consultaTipoProduto(){
        
        if($this->getAba() === "envio"){
            $dataEnvio = " to_char(ceeenvio, 'DD/MM/YYYY') as data_envio, ";
            $groupDate = " ceeenvio ";
        }else{
            $dataEnvio = " to_char(condtcodigo_postal, 'DD/MM/YYYY') as data_envio, ";
            $groupDate = " condtcodigo_postal ";
        }
        
        $sql = "SELECT 
                    $dataEnvio
                    connumero as contrato,
                    equno_serie as serie,
                    equprdoid as codigo_produto,
                    prdproduto as descricao_produto,
                    prdncms_codigo as codigo_ncm,
                    clioid,
                    clinome as nome_cliente,
                    pnfoid as numero_pedido,
                    pnfnf_numero as nf_remessa,
                    count(equprdoid) as quantidade ,
                    pnftppedido as tp_pedido,
                    pnfbloqueada as bloqueada,
                    SUM((select pcmcusto_medio from produto_custo_medio where pcmprdoid = prdoid ORDER BY pcmoid DESC limit 1)) as preco_unitario_1,
                    SUM((select (entivlr_unit + entivlr_ipi) as preco_unitario from entrada_item where entiprdoid = prdoid order by entioid desc limit 1)) as preco_unitario_2,
                    9.99 as preco_unitario_3,
                    case when clitipo = 'J' then clino_cgc else clino_cpf end as cliente_cnpj,
                    case when clitipo = 'J' then cliuf_com else cliuf_res end as cliente_uf
                FROM contrato ";
        
                $sql .= ($this->getAba() === 'envio' ? " JOIN contrato_envio_eqpto ON contrato_envio_eqpto.ceeconoid = contrato.connumero " : "");
                
                $sql .= "
                JOIN clientes ON clioid = conclioid 
                JOIN equipamento ON equoid = conequoid
                JOIN produto ON prdoid = equprdoid
                LEFT JOIN pedido_origem ON pedorigem_oid = connumero 
                LEFT JOIN pedido_origem_tipo ON potoid = pedpotoid ";
        
                $sql .= ($this->getAba() === "envio" ? ' AND potnome = ' . "'CONTROLE_MOVEL' " : " AND potnome = 'CONTRATO' ");
 
                $sql .= " LEFT JOIN pedido_nota_fiscal ON pnfoid = pedpnfoid ";
                
                $sql .= " WHERE 1 = 1 "; 
                
                $sql .= ($this->getContrato() ? ' AND connumero = ' . $this->getContrato() : '');
                
                if($this->getAba() === "envio"){
                    
                    $sql .= " AND ceeenvio between '" . $this->getDataInicio() . "' AND '" . $this->getDataFim() . "'";
                }else{
                    
                    $sql .= " AND concodigo_postal IS NOT NULL ";
                    
                    $sql .= " AND condtcodigo_postal between '" . $this->getDataInicio() . "' AND '" . $this->getDataFim() . "'";
                }
   
        $sql .= ($this->getNumeroPedido()              ? ' AND pnfoid        = ' . $this->getNumeroPedido() : '');
        
        $sql .= ($this->getCliente() && $this->getCliente() !== 'null' ? ' AND clioid     = ' . $this->getCliente()      : '');
        
        $sql .= ($this->getRemessa()                   ? ' AND pnfnf_numero  = ' . $this->getRemessa()      : '');
        
        $sql .= ($this->getSerie()                     ? " AND equno_serie    = '" . $this->getSerie() . "'" : '');
        
        $sql .= ($this->getPossuiNFRemessa() === 'nao' ? ' AND pnfnf_numero IS NULL'                        : '');
        
        $sql .= ($this->getPossuiNFRemessa() === 'sim' ? ' AND pnfnf_numero IS NOT NULL'                    : '');

        $sql .= " GROUP BY
                        $groupDate,
                        connumero,
                        equno_serie,
                        pnfoid,
                        equprdoid,
                        prdproduto,
                        prdncms_codigo,
                        clinome,
                        case when clitipo = 'J' then clino_cgc else clino_cpf end, 
                        case when clitipo = 'J' then cliuf_com else cliuf_res end,
                        pnfnf_numero,
                        clioid
                ORDER by $groupDate";
        
        $result = pg_query($this->getConn(), $sql);
        
        $rows = pg_affected_rows($result);
        
        $param = Array('tipo_relatorio' => 'produto', 'aba' => $this->getAba());
        
        return Array($result, $rows, $param);
        
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

}
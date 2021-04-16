<?php

/**
 * ES ? Log Integração Fox
 *
 * @file    log_conectores_integracao_fox.php
 * @author  Diego C. Ribeiro (BRQ)
 * @since   05/11/2012
 * @version 05/11/2012
 * 
 * Script responsável por consultar o status dos protocolos enviados e atualizar os protocolos
 * que ainda não foram consultados.
 */

class FinLogConectoresIntegracaoFoxDAO {
    
    /**
     * Link de conexão com o banco
     * @property resource
     */
    private $conn;    
    
    public $conector;
    public $pesq_dt_ini         = null;
    public $pesq_dt_fim         = null;    
    public $protocolo           = null;
    public $somenteProtocolo    = null;
    public $codigo              = null;
    public $status              = null;
    
    public function __construct($conn) {        
        $this->conn = $conn;
    }
    
    public function pesquisar(){
        
        $sql = "
                SELECT  lifoid, 
                        lifconector, 
                        lifdt_inicio, 
                        lifdt_fim, 
                        lif_descricao, 
                        lifprotocolo, 
                        lifreenvio, 
                        lifsituacao_protocolo, 
                        liferro_processamento
  
                FROM log_integracao_fox
                WHERE lifconector = '$this->conector' ";
        
        // Somente com protocolo
        if(isset($this->somenteProtocolo) and $this->somenteProtocolo == "Sim"){
            $sql .= " AND lifprotocolo IS NOT NULL AND lifprotocolo != ''";
        }
        
        // Pesquisa pelo Protocolo
        if(isset($this->protocolo) and !empty($this->protocolo)){
            $sql .= " AND lifprotocolo ILIKE '%$this->protocolo%'";
        }
        
		if(isset($this->pesq_dt_ini) and isset($this->pesq_dt_fim)){
			$sql .= " AND lifdt_inicio  between '$this->pesq_dt_ini 000000' and '$this->pesq_dt_fim 235959'";
		}else if(isset($this->pesq_dt_ini)){
			$sql .= " AND lifdt_inicio  >= '$this->pesq_dt_ini'";
		}else{
			$sql .= " AND lifdt_inicio  <= '$this->pesq_dt_fim'";
		}

        // Pesquisa pelo Status
        if(isset($this->status) and !empty($this->status)){
            switch ($this->status) {
                case "Processado com Sucesso":
                    $sql .= " AND lifsituacao_protocolo = 'Processado' and liferro_processamento = 'f'";
                    break;
                case "Processado com Erro" :
                    $sql .= " AND lifsituacao_protocolo = 'Processado' and liferro_processamento = 't'";
                    break;                
                case "Pendente de Processamento" :
                    $sql .= " AND (lifsituacao_protocolo != 'Processado' OR lifsituacao_protocolo IS NULL)";
                    break;
                default:
                    break;
            }
        }                
        
        $sql .= " ORDER BY lifoid ASC;";	
        
        $result = pg_query($this->conn,$sql);
        
        $arrLinhas =  array();
        if (pg_num_rows($result) > 0) {
            
            while($row = pg_fetch_assoc($result)) {
                    $arrLinhas[] = $row; 
            }
        }
        return $arrLinhas;
    }    
    
    public function detalhes(){
                
        try {
            
            if(!is_numeric($this->codigo)){
                throw new Exception('Detalhes: Código Inválido');
            }else{
                $lifoid = $this->codigo;
            }
            
            // Retorna os dados do Log do conector
            $sql = "    SELECT  lifoid, lifconector, lifdt_inicio, lifdt_fim, lif_descricao, 
                                lifprotocolo, lifreenvio, lifsituacao_protocolo, liferro_processamento 
                        FROM log_integracao_fox 
                        WHERE lifoid = $lifoid
                        ORDER BY lifoid ASC";
            $result = pg_query($this->conn,$sql);
            
            $arrDados = array();
            if(pg_num_rows($result) == 1){
                $arrDados = pg_fetch_assoc($result);
                
                if($arrDados['lifsituacao_protocolo'] == 'EmProcessamento'){
                    $arrDados['lifsituacao_protocolo'] = 'Em Processamento';
                }else if($arrDados['lifsituacao_protocolo'] == ''){
                    $arrDados['lifsituacao_protocolo'] = 'Protocolado';
                }                
            }
            
        } catch (Exception $exc) {
            echo $exc->getMessage();
            exit();
        }

        // Retorna os detalhes do conector ws1
        if($this->conector == 1){
             // Retorna os Detalhes
            $sql = "SELECT lifdoid, lifdlifoid, lifdtipo_nota, lifdstatus_processamento, 
                        lifdnumero_nota_fiscal, lifdserie_nota_fiscal, lifddetalhamento
                    FROM log_integracao_fox_detalhe
                    WHERE lifdlifoid = $lifoid 
                    ORDER BY lifdoid ASC";
            $result = pg_query($this->conn,$sql);
            
            $arrDetalhes = array();                        
            while ($linha = pg_fetch_assoc($result)){
                $arrDetalhes[] = $linha;
            }            
            $arrDados['detalhes'] = $arrDetalhes;
        }
        
        // Retorna os Pedidos enviados
        elseif($this->conector == 2){ 
            
            $sql = "SELECT
                        (   SELECT nflno_pedido FROM nota_fiscal_venda 
                            INNER JOIN  clientes ON clioid=nflclioid 
                            WHERE nfloid = CAST(lifdecodigo_enviado AS int8)  
                            GROUP BY nfloid, nflno_pedido, clioid) 
                        AS numero_pedido,
                            
                        (   SELECT clinome FROM nota_fiscal_venda 
                            INNER JOIN  clientes ON clioid=nflclioid 
                            WHERE nfloid = CAST(lifdecodigo_enviado AS int8) 
                            GROUP BY nfloid, nflno_pedido, clioid) 
                       AS nome
                    FROM log_integracao_fox_dados_enviados 
                    WHERE lifdelifoid = $lifoid
                    ORDER BY numero_pedido ASC";                       
            $result = pg_query($this->conn,$sql);
            
            $arrDetalhes = array();                        
            while ($linha = pg_fetch_assoc($result)){
                $arrDetalhes[] = $linha;
            }            
            $arrDados['detalhes'] = $arrDetalhes;
        }
        
        // Retorna os Clientes / Fornecedores enviados
        elseif($this->conector == 3){ 
            
            $sql = "SELECT lifdeoid, lifdelifoid, lifdecodigo_enviado, lifdetipo,
                        CASE WHEN lifdetipo='FO' THEN (SELECT forfornecedor FROM fornecedores WHERE foroid = CAST(lifdecodigo_enviado AS integer)) 
                            WHEN lifdetipo='CL' THEN (SELECT clinome FROM clientes WHERE clioid = CAST(lifdecodigo_enviado AS integer))
                            END as nome
                    FROM log_integracao_fox_dados_enviados
                    WHERE lifdelifoid = $lifoid
                    ORDER BY nome ASC";                  
            
            $result = pg_query($this->conn,$sql);
            
            $arrDetalhes = array();                        
            while ($linha = pg_fetch_assoc($result)){
                $arrDetalhes[] = $linha;
            }            
            $arrDados['detalhes'] = $arrDetalhes;
        }
        
        // Retorna os Clientes / Fornecedores enviados
        elseif($this->conector == 4){ 
            
            $sql = "SELECT
                            lifdecodigo_enviado, 
                            LEFT(prdproduto,120) AS descricao
                    FROM log_integracao_fox_dados_enviados
                    INNER JOIN produto ON prdoid = CAST(lifdecodigo_enviado AS integer)
                    WHERE lifdelifoid = $lifoid";                  
            
            $result = pg_query($this->conn,$sql);
            
            $arrDetalhes = array();                        
            while ($linha = pg_fetch_assoc($result)){
                $arrDetalhes[] = $linha;
            }            
            $arrDados['detalhes'] = $arrDetalhes;
        }
            
        // ERROS PROCESSAMENTO - WS2, WS3 e WS4
        if($this->conector != 1){ 
            
            $sql = "SELECT * FROM log_integracao_fox_erro_protocolo WHERE lifelifoid = $lifoid;";
            $result = pg_query($this->conn,$sql);
            
            $arrErrosProcessamento = array();                        
            while ($linha = pg_fetch_assoc($result)){
                $arrErrosProcessamento[] = $linha;
            }            
            $arrDados['erros'] = $arrErrosProcessamento;
        }                
        return $arrDados;        
    }        
}
?>
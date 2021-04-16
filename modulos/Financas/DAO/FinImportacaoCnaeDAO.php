<?php
/**
 * @autor	Paulo Sergio B Pinto
 * @versao	23/04/2020
 */

/**
 * Fornece os dados necessarios para carga de arquivbo cnae gerados pelo protheus
 */

class FinImportacaoCnaeDAO {
	
    /**
     * Link de conexão com o banco
     */
    private $conn;
    private $cd_usuario;
	
	
    /**
    * Construtor
     * @param resource $conn - Link de conexão com o banco
     */
    public function __construct($conn)
    {
	$this->conn = $conn;
	$this->cd_usuario = $_SESSION['usuario']['oid'];
    }
    
    public function getCnaeGravados($dados_arq){
        try {

            $sql = " select coalesce(cnfioid,0) as cnfioid 
                     from   cnae_fiscal_ativa 
                     where  cnfidt_importacao >= '2020-04-24 00:00:00'
                       and  cnfidt_inativacao is null 
                       limit 1";

            if(!$result = pg_query($this->conn, $sql)){
                throw new Exception('Falha ao pesquisar dados de histórico do título.');
            }

            if(pg_num_rows($result) == 1){
               $rsCnae = pg_fetch_array($result);
               $cnfioid = $rsCnae['cnfioid'];
            }
            
            return  $cnfioid;
        } catch(Exception $e) {
            return $e->getMessage();
        }
        
    }
    
    public function setInativaCnae($cnfioid){
        try {
            $sql = " UPDATE cnae_fiscal_ativa
                        SET cnfidt_inativacao=now()
                      WHERE cnfioid = ".$cnfioid ;

            if(!pg_query($this->conn, $sql)){
    		throw new Exception('Falha ao atualizar título.');
            }            
            
            return 1;
        } catch(Exception $e) {
            return $e->getMessage();
        }
        
    }
    
    public function setCabecalhoCnae($dados_arq, $cnfidescricao_motivo){
        try {
            
            $sql = " INSERT INTO cnae_fiscal_ativa
                            (cnfidt_importacao, cnfidt_inativacao, cnfiusuoid, cnfidescricao_motivo)
                        VALUES(now(), null,".$this->cd_usuario.",'".$cnfidescricao_motivo."')  RETURNING cnfioid;";
            
            $result = pg_query($this->conn, $sql);
            if ($result == false) {  
                throw new Exception('Falha ao Inserir Cabecalho:'.pg_last_error());
            } else {
                if (is_resource($result)) {
                    $obj = pg_fetch_object($result);
                    $ultimoID = $obj->cnfioid;
                }else{
                     throw new Exception('Falha ao Retornar o Ultimo ID do Cabecalho:'.pg_last_error());
                }
            }
            return $ultimoID;
            
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function setLinhaCnae($dados_arq){
        // grava cabecalho cnae
        try {
            $sql = " INSERT INTO cnae_fiscal
                        (cnacnfioid, cnaecodigo, cnaedescricao)
                        VALUES(".$dados_arq->idCnae.",'".$dados_arq->cnaecodigo."','".$dados_arq->cnaedescricao."') returning cnaoid;";
            $result = pg_query($this->conn, $sql);
            if ($result == false) {  
                throw new Exception('Falha ao Inserir Cabecalho:'.pg_last_error());
            }

            return 1;
            
        } catch(Exception $e) {
            return $e->getMessage();
        }
        
    }
    public function getRelatorioArquivoCnae($param_rel){
        $sql = "select 
                    cfa.cnfioid as id,
                    (select count(*) from cnae_fiscal where cnacnfioid = cfa.cnfioid) as n_registros,
                    cfa.cnfidt_importacao as data_importacao,
                    cfa.cnfidescricao_motivo as motivo,
                    usu.ds_login as usuario,
                    cfa.cnfidt_inativacao as data_inativacao,
                    case when cnfidt_inativacao isnull then 'Ativo' else 'Inativo' end  status
                from cnae_fiscal_ativa cfa
                inner join cnae_fiscal cf on cf.cnacnfioid = cfa.cnfioid
                inner join usuarios usu on usu.cd_usuario = cfa.cnfiusuoid
                where true  ";
        if($param_rel->dtInicial != ''){
            $sql .= " and  date(cfa.cnfidt_importacao) between '".$param_rel->dtInicial."' and '".$param_rel->dtFinal."'";
        }
        if($param_rel->cnaecodigo != ''){
            $sql .= " and  cf.cnaecodigo ilike '%".$param_rel->cnaecodigo."%' ";
        }
        if($param_rel->cnaedescricao != ''){
            $sql .= " and  cf.cnaedescricao ilike '%".$param_rel->cnaedescricao."%' ";
        }
        $sql .= " group by 1,2,3,4,5,6,7
                  order by cfa.cnfioid desc";

        if (!$result = pg_query($this->conn, $sql)) {
            throw new Exception ("Falha ao recuperar email do usuario dio processo ");
        }

        if(count($result) > 0){
            return pg_fetch_all($result);
        }

        return false;
    }
    public function getListaCnae($cnfioid){
        $sql = "select cf.cnaoid, cf.cnacnfioid, cf.cnaecodigo, cf.cnaedescricao
                from cnae_fiscal_ativa cfa 
                inner join cnae_fiscal cf on cf.cnacnfioid = cfa.cnfioid
                where true 
                and   cf.cnacnfioid = ".$cnfioid."
                order by cf.cnaoid ";

        if (!$result = pg_query($this->conn, $sql)) {
            throw new Exception ("Falha ao recuperar email do usuario dio processo ");
        }

        if(count($result) > 0){
            return pg_fetch_all($result);
        }

        return false;
    }


    /**
     * inicia transação com o BD
     */
    public function begin()	{
    	$rs = pg_query($this->conn, "BEGIN;");
    }
    
    /**
     * confirma alterações no BD
     */
    public function commit(){
    	$rs = pg_query($this->conn, "COMMIT;");
    }
    
    /**
     * desfaz alterações no BD
     */
    public function rollback(){
    	$rs = pg_query($this->conn, "ROLLBACK;");
    }
    

}

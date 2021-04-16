<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	13/03/2013 
 */

/**
 * Fornece os dados necessarios para o módulo cadastro para 
 * efetuar ações referentes a cadastro de bancos 
 */
class CadBancoDAO {
	
	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	public $conn;
	
	
	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($conn)
	{
		$this->conn = $conn;
	}
    
	/**
	 * Responsável por aplicar os filtros da tela de 
	 * pesquisa e retornar os dados dos equipamentos 
	 */
    public function pesquisar() {
        try {
            
            $where = "";
            
            $where .= (isset($_POST['cfbbanco']) && $_POST['cfbbanco'] != "") ? " AND cfbbanco = ".$_POST['cfbbanco'] : "";
            $where .= (isset($_POST['cfbnome']) && $_POST['cfbnome'] != "") ? " AND cfbnome ILIKE '%".$_POST['cfbnome']."%'" : "";
            $where .= (isset($_POST['tecoid']) && $_POST['tecoid'] != "") ? " AND tecoid = ".$_POST['tecoid'] : "";
    
            $sql = "SELECT * 
                      FROM config_banco 
           LEFT OUTER JOIN plano_contabil 
                        ON plcoid = cfbplcoid 
                 LEFT JOIN tectran
                        ON tecoid = plctecoid
                     WHERE cfbexclusao IS NULL 
                      $where
                  ORDER BY tecoid, cfbnome";

            $resultado = array('results');
            
            $cont = 0;
            
            $rs = pg_query($this->conn, $sql);
            
            $refTecRazao = "";
            
            while ($rResultados = pg_fetch_assoc($rs)) {

                if($refTecRazao != $rResultados['tecrazao']) {
                    $refTecRazao = $rResultados['tecrazao'];
                    $cont = 0;
                    
                    $resultado['results'][$rResultados['tecrazao']][$cont] = $rResultados;
                } else {
                    $resultado['results'][$rResultados['tecrazao']][$cont] = $rResultados;
                }
            
                $cont++;
                
            }
    
            $resultado['total_registros'] = 'A pesquisa retornou ' . pg_num_rows($rs) . ' registro(s).';
            
            return $resultado;
            
        }catch(Exception $e ) {
            return false;
        }
    }
    
    /**
     * Responsável por retornar dados do 
     * equipamento na tela de edição
     */
    public function editar()
    {
        try{
            $cfbbanco = $_POST['cfbbanco'];
            
            $sql = "SELECT *
                      FROM config_banco
                     WHERE cfbexclusao IS NULL
                       AND cfbbanco = $cfbbanco";
                     
            $rs = pg_query($this->conn, $sql);
    
            $arrGrupo = array();
            
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){
                    $arrBanco = $arrRs;
                }
            }
            
            return $arrBanco;
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    public function buscaEmpresas()
    {
        try {
            
            $sql = "SELECT tecoid, 
                           tecrazao
                      FROM tectran
                     WHERE tecexclusao IS NULL
                  ORDER BY tecrazao";
                      
            $rs = pg_query($this->conn, $sql);
            
            $cont = 0;
            
            $resultado = array();

            if(pg_num_rows($rs) > 0) {
                while ($rGrupos = pg_fetch_assoc($rs)) {
                    
                    $resultado[$cont]['tecoid']   = $rGrupos['tecoid'];
                    $resultado[$cont]['tecrazao'] = $rGrupos['tecrazao'];
        
                    $cont++;
                }
                
                return $resultado;
            } else {
                return false;
            }
        } catch(Exception $e) {
            return false;
        }
    }
    
    public function buscaPlanosContabeis($encode = false)
    {
        try {
            
            $where = ($_POST['tecoid'] > 0)?" AND plctecoid = ".$_POST['tecoid']:"";
            
            $sql = "SELECT plcoid, 
                           plcdescricao,
                           plctecoid
                      FROM plano_contabil
                     WHERE plcexclusao IS NULL
                      $where
                  ORDER BY plcdescricao";

            $rs = pg_query($this->conn, $sql);
            
            $cont = 0;
            
            $resultado = array();

            if(pg_num_rows($rs) > 0) {
                while ($rPlanos = pg_fetch_assoc($rs)) {
                    
                    $resultado['planos'][$cont]['plcoid']       = $rPlanos['plcoid'];
                    $resultado['planos'][$cont]['plcdescricao'] = ($encode)?utf8_encode($rPlanos['plcdescricao']):$rPlanos['plcdescricao'];
                    $resultado['planos'][$cont]['plctecoid']    = $rPlanos['plctecoid'];
        
                    $cont++;
                }

                return ($encode)?json_encode($resultado):$resultado;
            } else {
                return false;
            }
        } catch(Exception $e) {
            return false;
        }
    }

    /**
     * Retorna lista de todos os projetos cadastrados
     */
    public function salvar()
    {
        try {
            
            $cfbbanco                   = $_POST['cfbbanco'];
            $cfbtecoid                  = $_POST['cfbtecoid'];
            $cfbnome                    = utf8_decode($_POST['cfbnome']);
            $cfbconvenio                = ($_POST['cfbconvenio'])?$_POST['cfbconvenio']:0;
            $cfblimite                  = ($_POST['cfblimite'])?$_POST['cfblimite']:0;
            $cfbplcoid                  = $_POST['cfbplcoid'];
            $cfbagencia                 = $_POST['cfbagencia'];
            $cfbconta_corrente          = $_POST['cfbconta_corrente'];
            $cfbagencia_convenio        = ($_POST['cfbagencia_convenio'])?$_POST['cfbagencia_convenio']:null; 
            $cfbconta_corrente_convenio = ($_POST['cfbconta_corrente_convenio'])?$_POST['cfbconta_corrente_convenio']:null; 
            $cfbtipo                    = $_POST['cfbtipo'];
            $cfbfluxo                   = (!isset($_POST['cfbfluxo']))?'false':$_POST['cfbfluxo'];
            $status                     = $_POST['status'];            
            
            if($status == "editar") {
                $sql = "UPDATE config_banco
                           SET cfbnome                    = '$cfbnome',
                               cfbconvenio                = '$cfbconvenio', 
                               cfblimite                  = '$cfblimite',
                               cfbplcoid                  = $cfbplcoid,
                               cfbagencia                 = '$cfbagencia',
                               cfbconta_corrente          = '$cfbconta_corrente',
                               cfbagencia_convenio        = '$cfbagencia_convenio',
                               cfbconta_corrente_convenio = '$cfbconta_corrente_convenio',
                               cfbtipo                    = '$cfbtipo',
                               cfbfluxo                   = $cfbfluxo
                         WHERE cfbbanco = ".$cfbbanco;
         
                $result = pg_query($this->conn, $sql);
                
            } else {
                $sql = "INSERT INTO config_banco (
                                        cfbbanco,
                                        cfbtecoid,
                                        cfbnome,
                                        cfbconvenio,
                                        cfblimite,
                                        cfbplcoid,
                                        cfbagencia,
                                        cfbconta_corrente,
                                        cfbagencia_convenio,
                                        cfbconta_corrente_convenio,
                                        cfbtipo,
                                        cfbfluxo
                                    )
                             VALUES (
                                        $cfbbanco,
                                        $cfbtecoid,
                                        '$cfbnome',
                                        $cfbconvenio,
                                        $cfblimite,
                                        $cfbplcoid,
                                        '$cfbagencia',
                                        '$cfbconta_corrente',
                                        '$cfbagencia_convenio',
                                        '$cfbconta_corrente_convenio',
                                        '$cfbtipo',
                                        $cfbfluxo
                                    )";  
                $result = pg_query($this->conn, $sql);
                
            }

            return "1";
            
        } catch(Exception $e) {
            return "erro";
        }
    }

    
    /**
     * Verifica se já existe um teste com a mesma configuração do formulário
     */
    public function verificaIntegridade()
    {
        $cfbbanco = $_POST['cfbbanco'];

        try {
            $sql = "SELECT count(cfbbanco) as qtd 
                      FROM config_banco
                     WHERE cfbbanco = $cfbbanco
                       AND cfbexclusao IS NULL";   

            $rs = pg_query($this->conn, $sql);
    
            while ($arrRs = pg_fetch_array($rs)){ 
                $qtd  = $arrRs['qtd'];
            }
            
            return $qtd;
        } catch(Exception $e) {
            return "erro";
        }
    }

    /**
     * inicia transação com o BD
     */
    public function begin()
    {
        $rs = pg_query($this->conn, "BEGIN;");
    }
    
    /**
     * confirma alterações no BD
     */
    public function commit()
    {
        $rs = pg_query($this->conn, "COMMIT;");
    }
    
    /**
     * desfaz alterações no BD
     */
    public function rollback()
    {
        $rs = pg_query($this->conn, "ROLLBACK;");
    }
    
}
<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	11/01/2013 
 */

/**
 * Fornece os dados necessarios para o módulo do módulo cadastro para 
 * efetuar ações referentes a manutenção dos testes para equipamentos 
 */
class CadEmbarqueConfiguracoesPortalDAO {
	
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
            
            $where .= (isset($_POST['eptcfoid']) && $_POST['eptcfoid'] != "") ? " AND eptcfoid = ".$_POST['eptcfoid'] : "";
            $where .= (isset($_POST['eproid']) && $_POST['eproid'] != "") ? " AND eproid = ".$_POST['eproid'] : "";
            $where .= (isset($_POST['eqcoid']) && $_POST['eqcoid'] != "") ? " AND eqcoid = ".$_POST['eqcoid'] : "";
            $where .= (isset($_POST['eveoid']) && $_POST['eveoid'] != "") ? " AND eveoid = ".$_POST['eveoid'] : "";
            $where .= (isset($_POST['cmdcomando']) && $_POST['cmdcomando'] != "") ? " AND cmdcomando || '('||cmdmeio||') ' || cmddescricao ILIKE '%".$_POST['cmdcomando']."%'" : "";
    
            $sql = "SELECT eptcfoid,
                           eptcfdescricao,
                           eprnome,
                           eqcdescricao,
                           eveversao,
                           cmdcomando || '('||cmdmeio||') ' || cmddescricao  as cmdcomando
                      FROM equipamento_projeto_tipo_configuracao 
                 LEFT JOIN equipamento_projeto_configuracao_comando 
                        ON epcceptcfoid = eptcfoid
                 LEFT JOIN equipamento_comandos 
                        ON ecmoid = epccecmoid
                 LEFT JOIN equipamento_projeto_classe_versao 
                        ON epcvoid = ecmepcvoid
                 LEFT JOIN equipamento_projeto
                        ON epcveproid = eproid
                 LEFT JOIN equipamento_classe
                        ON epcveqcoid = eqcoid
                 LEFT JOIN equipamento_versao
                        ON epcveveoid = eveoid
                 LEFT JOIN comandos 
                        ON cmdoid = ecmcmdoid
                     WHERE eptcfdescricao IS NOT NULL
                       AND eptcfdt_exclusao IS NULL
                       AND epccdt_exclusao IS NULL
                      $where";

            $resultado = array('results');
            
            $cont = 0;
            
            $rs = pg_query($this->conn, $sql);
            
            while ($rResultados = pg_fetch_assoc($rs)) {
                $resultado['results'][$cont]['eptcfoid']       = $rResultados['eptcfoid']; 
                $resultado['results'][$cont]['eptcfdescricao'] = $rResultados['eptcfdescricao']; 
                $resultado['results'][$cont]['eprnome']        = $rResultados['eprnome']; 
                $resultado['results'][$cont]['eqcdescricao']   = $rResultados['eqcdescricao'];
                $resultado['results'][$cont]['eveversao']      = $rResultados['eveversao'];
                $resultado['results'][$cont]['cmdcomando']     = $rResultados['cmdcomando'];
                
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
            $eptcfoid = $_POST['eptcfoid'];
            
            $sql = "SELECT eptcfoid, 
                           eptcfdescricao
                      FROM equipamento_projeto_tipo_configuracao
                     WHERE eptcfdescricao IS NOT NULL
                       AND eptcfoid = $eptcfoid";
                     
            $rs = pg_query($this->conn, $sql);
    
            $arrGrupo = array();
            
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){
                    $arrGrupo = $arrRs;
                }
            }
            
            return $arrGrupo;
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    public function buscaGrupos()
    {
        try {
            
            $sql = "SELECT eptcfoid, 
                           eptcfdescricao
                      FROM equipamento_projeto_tipo_configuracao
                     WHERE eptcfdescricao IS NOT NULL
                       AND eptcfdt_exclusao IS NULL
                  ORDER BY eptcfdescricao";
                      
            $rs = pg_query($this->conn, $sql);
            
            $cont = 0;
            
            $resultado = array();

            if(pg_num_rows($rs) > 0) {
                while ($rGrupos = pg_fetch_assoc($rs)) {
                    
                    $resultado[$cont]['eptcfoid']       = $rGrupos['eptcfoid'];
                    $resultado[$cont]['eptcfdescricao'] = $rGrupos['eptcfdescricao'];
        
                    $cont++;
                }
                
                return $resultado;
            } else {
                return false;
            }
        } catch(Exception $e) {
            
        }
    }

    /**
     * Retorna lista de todos os projetos cadastrados
     */
    public function buscaEquipamentosProjeto()
    {
        try {
            
            $sql = "SELECT eproid, 
                           eprnome 
                      FROM equipamento_projeto 
                  ORDER BY eprnome";
            
            $rs = pg_query($this->conn, $sql);
            
            $cont = 0;
            
            while ($rEquipamentos = pg_fetch_assoc($rs)) {
                
                $resultado[$cont]['eproid']  = $rEquipamentos['eproid'];
                $resultado[$cont]['eprnome'] = $rEquipamentos['eprnome'];
    
                $cont++;
            }

            return $resultado;
            
        } catch(Exception $e) {
            return "erro";
        }
    }

    /**
     * Retorna lista de todas as classes cadastradas
     */
    public function buscaEquipamentosClasse()
    {
        try {
            $sql = "SELECT eqcoid, 
                           eqcdescricao 
                      FROM equipamento_classe 
                  ORDER BY eqcdescricao";
            
            $rs = pg_query($this->conn, $sql);
            
            $cont = 0;
            
            while ($rEquipamentos = pg_fetch_assoc($rs)) {
                
                $resultado[$cont]['eqcoid']       = $rEquipamentos['eqcoid'];
                $resultado[$cont]['eqcdescricao'] = $rEquipamentos['eqcdescricao'];
    
                $cont++;
            }

            return $resultado;
            
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    /**
     * Retorna a lista de todas as versões cadastradas
     * 
     * @param boolean $encode - Caso true, retorna dados em formato JSON, caso false, retorna array
     */
    public function buscaEquipamentosVersao($encode)
    {
        try {
            
            $where = (isset($_POST['eveprojeto']) && $_POST['eveprojeto'] != "" && $_POST['eveprojeto'] != 0)?" AND eveprojeto = ".$_POST['eveprojeto']:"";
            
            $sql = "SELECT eveoid, 
                           eveversao 
                      FROM equipamento_versao 
                     WHERE eveversao != ''
                       AND evedt_exclusao IS NULL
                       $where 
                  ORDER BY eveversao";

            $rs = pg_query($this->conn, $sql);
            
            $cont = 0;
            
            $resultado = array('versoes');

            if(pg_num_rows($rs) > 0) {
                while ($rEquipamentos = pg_fetch_assoc($rs)) {
                    
                    $resultado['versoes'][$cont]['eveoid']    = $rEquipamentos['eveoid'];
                    $resultado['versoes'][$cont]['eveversao'] = $rEquipamentos['eveversao'];
        
                    $cont++;
                }
                
                if($encode === true) {
                    return json_encode($resultado);
                } else {
                    return $resultado;
                }
            } else {
                return false;
            }
            
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    /**
     * Retorna lista de todos os projetos cadastrados
     */
    public function salvar()
    {
        try {

            $eptcfoid       = $_POST['eptcfoid'];
            $eptcfdescricao = ($_POST['eptcfdescricao'])?utf8_decode($_POST['eptcfdescricao']):'';

            $usuoid = $_SESSION['usuario']['oid'];
            
            if($eptcfoid != "" && $eptcfoid > 0) {
                $sql = "UPDATE equipamento_projeto_tipo_configuracao
                           SET eptcfdescricao        = '$eptcfdescricao',
                               eptcfdt_alteracao     = 'now()',
                               eptcfusuoid_alteracao = $usuoid
                         WHERE eptcfoid = ".$eptcfoid;
                         
                $result = pg_query($this->conn, $sql);
                
            } else {
                $sql = "INSERT INTO equipamento_projeto_tipo_configuracao (
                                        eptcfdescricao,
                                        eptcfdt_cadastro,
                                        eptcfusuoid_cadastro
                                    )
                             VALUES (
                                        '$eptcfdescricao',
                                        'now()',
                                        $usuoid
                                    ) RETURNING eptcfoid";  
                         
                $result = pg_query($this->conn, $sql);
                
                $eptcfoid = pg_fetch_result($result, 0, "eptcfoid");
            }

            //$epcvoid = "1";
            return $eptcfoid;
            
        } catch(Exception $e) {
            return "erro";
        }
    }

    /**
     * Lista todos os comandos cadastrados para um teste específico
     * 
     * @param boolean $encode
     * @param integer $epcvoid
     */
    public function listaComandosCadastrados()
    {
        try {
            $epcveproid = $_POST['eproid'];
            $epcveqcoid = $_POST['eqcoid'];
            $epcveveoid = $_POST['eveoid'];
            
            /*$sql = "SELECT ecmoid,  
                           cmdoid,
                           cmdcomando || '('||cmdmeio||') ' || cmddescricao  as comando 
                      FROM equipamento_comandos
                      JOIN equipamento_projeto_classe_versao
                        ON ecmepcvoid = epcvoid  
                      JOIN comandos
                        ON ecmcmdoid = cmdoid  
                       AND epcveproid = $epcveproid
                       AND epcveqcoid = $epcveqcoid
                       AND epcveveoid = $epcveveoid";*/
                       
            $sql = "SELECT cmdoid,
                           cmdcomando || '('||cmdmeio||') ' || cmddescricao  as comando 
                      FROM comandos
                  ORDER BY comando ASC";
    
            $rs = pg_query($this->conn, $sql);
                        
            $arrComandos = array('comandos');
            $aux = 0;
            
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){ 
                    $arrComandos['comandos'][$aux]['comando']   = utf8_encode($arrRs['comando']);
                    $arrComandos['comandos'][$aux]['cmdoid']    = $arrRs['cmdoid'];
                    //$arrComandos['comandos'][$aux]['ecmoid']    = $arrRs['ecmoid'];
                    
                    $aux++;
                }
            }
            
            $arrComandos['total_registros'] = utf8_encode('A pesquisa retornou ' . pg_num_rows($rs) . ' registro(s).');
            
            return json_encode($arrComandos);
            
        } catch(Exception $e) {
            return "erro";
        } 
    }
    
    /**
     * Lista todos os comandos cadastrados para um teste específico
     * 
     * @param boolean $encode
     * @param integer $epcvoid
     */
    public function listaComandosCadastradosGrupo($encode = true)
    {
        try {
            $eptcfoid = $_POST['eptcfoid'];
            
            $sql = "SELECT epccoid,  
                           cmdcomando || '('||cmdmeio||') ' || cmddescricao  as comando,
                           eprnome,
                           eqcdescricao,
                           eveversao
                      FROM equipamento_projeto_configuracao_comando
                      JOIN equipamento_comandos
                        ON epccecmoid = ecmoid
                      JOIN equipamento_projeto_classe_versao
                        ON ecmepcvoid = epcvoid  
                      JOIN equipamento_projeto
                        ON epcveproid = eproid
                      JOIN equipamento_classe
                        ON epcveqcoid = eqcoid
                      JOIN equipamento_versao
                        ON epcveveoid = eveoid  
                      JOIN comandos
                        ON ecmcmdoid = cmdoid  
                     WHERE epcceptcfoid = $eptcfoid
                       AND epccdt_exclusao IS NULL
                       AND ecmdt_exclusao IS NULL";
    
            $rs = pg_query($this->conn, $sql);
                        
            $arrComandos = array('comandos');
            $aux = 0;
            
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){
                    $arrComandos['comandos'][$aux]['comando']      = ($encode)?utf8_encode($arrRs['comando']):$arrRs['comando'];
                    $arrComandos['comandos'][$aux]['eprnome']      = ($encode)?utf8_encode($arrRs['eprnome']):$arrRs['eprnome']; 
                    $arrComandos['comandos'][$aux]['eqcdescricao'] = ($encode)?utf8_encode($arrRs['eqcdescricao']):$arrRs['eqcdescricao'];
                    $arrComandos['comandos'][$aux]['eveversao']    = ($encode)?utf8_encode($arrRs['eveversao']):$arrRs['eveversao'];
                    $arrComandos['comandos'][$aux]['epccoid']      = $arrRs['epccoid'];
                    
                    $aux++;
                }
            }
            
            $arrComandos['total_registros'] = utf8_encode('A pesquisa retornou ' . pg_num_rows($rs) . ' registro(s).');
            
            if($encode===true) 
                return json_encode($arrComandos);
            else 
                return $arrComandos;
            
        } catch(Exception $e) {
            return "erro";
        } 
    }
    
    public function salvarNovoComando()
    {
        $eptcfoid = $_POST['eptcfoid'];
        $cmdoid   = $_POST['cmdoid'];
        $eproid   = $_POST['eproid'];
        $eqcoid   = $_POST['eqcoid'];
        $eveoid   = $_POST['eveoid'];
        
        $usuoid   = $_SESSION['usuario']['oid'];
        
        try {
            $sql = "SELECT DISTINCT ecmoid, 
                                    epcvoid 
                               FROM equipamento_projeto_classe_versao 
                          LEFT JOIN equipamento_comandos 
                                 ON epcvoid = ecmepcvoid  
                                AND ecmcmdoid = $cmdoid
                                AND ecmdt_exclusao IS NULL
                              WHERE epcveproid = $eproid
                                AND epcveqcoid=$eqcoid
                                AND epcveveoid=$eveoid
                                AND epcvdt_exclusao IS NULL";
                     
            $rs = pg_query($this->conn, $sql);
    
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){ 
                    $ecmoid  = $arrRs['ecmoid'];
                    $epcvoid = $arrRs['epcvoid'];
                }
            } else {
                $sql = "INSERT INTO equipamento_projeto_classe_versao (
                                        epcveproid,
                                        epcveqcoid,
                                        epcveveoid,
                                        epcvdt_inclusao,
                                        epcvusuoid_inclusao 
                                   ) 
                            VALUES (
                                        $eproid, 
                                        $eqcoid, 
                                        $eveoid,
                                        'now()',
                                        $usuoid
                                    ) 
                          RETURNING epcvoid";
                      
                $rs = pg_query($this->conn, $sql);
           
                if(pg_num_rows($rs) > 0 ){
                    while ($arrRs = pg_fetch_array($rs)){ 
                        $epcvoid  = $arrRs['epcvoid'];
                    }
                }                
            } 
            
            if(!isset($ecmoid)){
                $sql = "INSERT INTO equipamento_comandos (
                                        ecmepcvoid, 
                                        ecmcmdoid,
                                        ecmdt_inclusao,
                                        ecmusuoid_inclusao
                                    ) 
                             VALUES (
                                        $epcvoid, 
                                        $cmdoid,
                                        'now()',
                                        $usuoid
                                    ) 
                          RETURNING ecmoid";  
                
                $rs = pg_query($this->conn, $sql);
           
                if(pg_num_rows($rs) > 0 ){
                    while ($arrRs = pg_fetch_array($rs)){ 
                        $ecmoid  = $arrRs['ecmoid'];
                    }
                }   
            }

            $sql = "INSERT INTO equipamento_projeto_configuracao_comando (
                                    epccecmoid,
                                    epcceptcfoid,
                                    epccdt_cadastro,
                                    epccusuoid_cadastro
                                )
                         VALUES (
                                    $ecmoid,
                                    $eptcfoid,
                                    'now()',
                                    $usuoid
                                )";
            $result = pg_query($this->conn, $sql);
            
            return 'ok';
        }catch(Exception $e) {
            return 'erro';
        }
    }
    
    /**
     * Verifica se já existe um teste com a mesma configuração do formulário
     */
    public function verificaIntegridadeComando()
    {
        $eproid   = $_POST['eproid'];
        $eqcoid   = $_POST['eqcoid'];
        $eveoid   = $_POST['eveoid'];
        $cmdoid   = $_POST['cmdoid'];
        $eptcfoid = $_POST['eptcfoid'];
        
        try {
            $sql = "SELECT count(epccoid) as qtd 
                      FROM equipamento_projeto_configuracao_comando
                      JOIN equipamento_comandos
                        ON ecmoid = epccecmoid
                      JOIN equipamento_projeto_classe_versao
                        ON ecmepcvoid = epcvoid 
                     WHERE epcveproid = $eproid 
                       AND epcveqcoid = $eqcoid 
                       AND epcveveoid = $eveoid
                       AND ecmcmdoid = $cmdoid
                       AND epcceptcfoid = $eptcfoid
                       AND epccdt_exclusao IS NULL
                       AND ecmdt_exclusao IS NULL";   

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
     * Exclui comando 
     * 
     * @param integer $emctoid
     */
    public function excluiComando($epccoid = false)
    {
        $epccoid = ($epccoid)?$epccoid:$_POST['epccoid'];
        $usuoid  = $_SESSION['usuario']['oid'];
        
        try {
            $sql = "UPDATE equipamento_projeto_configuracao_comando
                       SET epccdt_exclusao = 'now()',
                           epccusuoid_exclusao = $usuoid
                     WHERE epccoid = $epccoid";
                          
            $rs = pg_query($this->conn, $sql);
            
            return "ok";
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    public function excluiGrupo()
    {
        $usuoid  = $_SESSION['usuario']['oid'];
        $eptcfoid = $_POST['eptcfoid'];
        
        try{
            $sql = "UPDATE equipamento_projeto_tipo_configuracao
                       SET eptcfdt_exclusao = 'now()',
                           eptcfusuoid_exclusao = $usuoid
                     WHERE eptcfoid = $eptcfoid";

            $rs = pg_query($this->conn, $sql);
            
            return "ok";
            
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
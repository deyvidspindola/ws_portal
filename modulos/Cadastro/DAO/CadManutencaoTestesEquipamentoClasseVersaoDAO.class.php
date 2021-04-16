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
class CadManutencaoTestesEquipamentoClasseVersaoDAO {
	
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
            
            $where .= (isset($_POST['eproid']) && $_POST['eproid'] != "") ? " AND eproid = ".$_POST['eproid'] : "";
            $where .= (isset($_POST['eqcoid']) && $_POST['eqcoid'] != "") ? " AND eqcoid = ".$_POST['eqcoid'] : "";
            $where .= (isset($_POST['eveoid']) && $_POST['eveoid'] != "") ? " AND eveoid = ".$_POST['eveoid'] : "";
    
            $sql = "SELECT epcvoid, 
                           eprnome, 
                           eqcdescricao, 
                           eveversao 
                      FROM equipamento_projeto_classe_versao
                 LEFT JOIN equipamento_projeto 
                        ON epcveproid = eproid  
                 LEFT JOIN equipamento_classe
                        ON epcveqcoid = eqcoid
                 LEFT JOIN equipamento_versao 
                        ON epcveveoid = eveoid 
                     WHERE epcvdt_exclusao IS NULL
                       $where 
                  ORDER BY eprnome,
                           eqcdescricao,
                           eveversao";

            $resultado = array('equipamentos');
            
            $cont = 0;
            
            $rs = pg_query($this->conn, $sql);
            
            while ($rEquipamentos = pg_fetch_assoc($rs)) {
                
                $resultado['equipamentos'][$cont]['epcvoid']      = $rEquipamentos['epcvoid'];
                $resultado['equipamentos'][$cont]['eprnome']      = $rEquipamentos['eprnome'];
                $resultado['equipamentos'][$cont]['eqcdescricao'] = $rEquipamentos['eqcdescricao'];
                $resultado['equipamentos'][$cont]['eveversao']    = $rEquipamentos['eveversao'];
    
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
            $epcvoid = $_POST['epcvoid'];
            
            $sql = "SELECT epcveqcoid as eqcoid, 
                           epcveproid as eproid, 
                           epcveveoid as eveoid 
                      FROM equipamento_projeto_classe_versao 
                     WHERE epcvoid = $epcvoid";
                     
            $rs = pg_query($this->conn, $sql);
    
            $arrEqcv = array();
            
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){
                    $arrEqcv['epcvoid'] = $epcvoid; 
                    $arrEqcv['eproid']  = $arrRs['eproid'];
                    $arrEqcv['eqcoid']  = $arrRs['eqcoid'];
                    $arrEqcv['eveoid']  = $arrRs['eveoid'];
                }
            }
            
            return $arrEqcv;
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    /**
     * Responsável por retornar dados do
     * equipamento na tela de cópia
     */
    public function copiar()
    {
        try {
        	$epcvoid = $_POST['epcvoid'];
        
        	$sql = "SELECT epcveqcoid as eqcoid,
        	               epcveproid as eproid,
        	               epcveveoid as eveoid
        	          FROM equipamento_projeto_classe_versao
        	         WHERE epcvoid = $epcvoid";
        	 
        	$rs = pg_query($this->conn, $sql);
        
        	$arrEqcv = array();
        
        	if(pg_num_rows($rs) > 0 ){
        	    while ($arrRs = pg_fetch_array($rs)){
        	        $arrEqcv['epcvoid'] = $epcvoid;
        	        $arrEqcv['eproid']  = $arrRs['eproid'];
        	        $arrEqcv['eqcoid']  = $arrRs['eqcoid'];
        	        $arrEqcv['eveoid']  = $arrRs['eveoid'];
        	    }
        	}
        
        	return $arrEqcv;
        } catch(Exception $e) {
            return "erro";
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
     * Função que insere os dados do novo teste
     */
    public function cadastraNovoTeste()
    {
    	$usuoid = $_SESSION['usuario']['oid'];
    	
        try {
            $eproid = $_POST['eproid'];
            $eqcoid = $_POST['eqcoid'];
            $eveoid = $_POST['eveoid'];
            
            $sql = "INSERT INTO equipamento_projeto_classe_versao (epcveproid,
                                                                   epcveqcoid,
                                                                   epcveveoid,
                                                                   epcvdt_inclusao,
                                                                   epcvusuoid_inclusao ) 
                                                           VALUES ($eproid, 
                                                                   $eqcoid, 
                                                                   $eveoid,
                                                                   'now()',
                                                                   $usuoid) 
                      RETURNING epcvoid";
                      
            $rs = pg_query($this->conn, $sql);
       
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){ 
                    $epcvoid  = $arrRs['epcvoid'];
                }
            }
            //$epcvoid = "1";
            if ($epcvoid>0) {
            	return $epcvoid;
            } else {
            	return 'erro';
            }
            
        } catch(Exception $e) {
            //die($e->getMessage());
            return "erro";
        }
    }

    /**
     * Função que insere dados do novo Comando
     * 
     * @param integer $epcvoid
     * @param integer $cmdoid
     * @param integer $eptpoid
     */
    public function salvarNovoComando($epcvoid = false, $cmdoid = false, $eptpoid = false, $ecmteptpoid_antecessor = false)
    {
        $usuoid = $_SESSION['usuario']['oid'];
        
        try {
            $epcvoid                = ($epcvoid)?$epcvoid: $_POST['epcvoid'];
            $cmdoid                 = ($cmdoid)?$cmdoid:$_POST['cmdoid'];
            $eptpoid                = ($eptpoid)?$eptpoid:$_POST['eptpoid'];
            $ecmteptpoid_antecessor = ($ecmteptpoid_antecessor)?$ecmteptpoid_antecessor:($_POST['ecmteptpoid_antecessor'] && $_POST['ecmteptpoid_antecessor'] != "")?$_POST['ecmteptpoid_antecessor']:'null';
            
            $sql = "SELECT ecmoid 
                      FROM equipamento_comandos 
                     WHERE ecmepcvoid = $epcvoid 
                       AND ecmcmdoid = $cmdoid
                       AND ecmdt_exclusao IS NULL";
 
            $rs = pg_query($this->conn, $sql);
    
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){ 
                    $ecmoid  = $arrRs['ecmoid'];
                }
            } else {
                $sql = "INSERT INTO equipamento_comandos (ecmepcvoid, 
                                                          ecmcmdoid,
                                                          ecmdt_inclusao,
                                                          ecmusuoid_inclusao) 
                                                  VALUES ($epcvoid, 
                                                          $cmdoid,
                                                          'now()',
                                                          $usuoid) 
                          RETURNING ecmoid";  
                
                $rs = pg_query($this->conn, $sql);
           
                if(pg_num_rows($rs) > 0 ){
                    while ($arrRs = pg_fetch_array($rs)){ 
                        $ecmoid  = $arrRs['ecmoid'];
                    }
                }   
            }
            
            $sql = "INSERT INTO equipamento_comandos_testes(ecmtecmoid, 
                                                            ecmteptpoid,
                                                            ecmteptpoid_antecessor,
                                                            ecmtdt_inclusao,
                                                            ecmtusuoid_inclusao) 
                                                    VALUES ($ecmoid, 
                                                            $eptpoid,
                                                            $ecmteptpoid_antecessor,
                                                            'now()',
                                                            $usuoid)";

            $rs = pg_query($this->conn, $sql);
            
            return "ok";
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    /**
     * Função que insere novo alerta/pânico
     * 
     * @param integer $epcvoid
     * @param integer $pantoid
     * @param integer $eptpoid
     */
    public function salvarNovoAlertaPanico($epcvoid = false, $pantoid = false, $eptpoid = false)
    {
    	$usuoid = $_SESSION['usuario']['oid'];
    	
        try {
            
            $epcvoid = ($epcvoid)?$epcvoid:$_POST['epcvoid'];
            $pantoid = ($pantoid)?$pantoid:$_POST['pantoid'];
            $eptpoid = ($eptpoid)?$eptpoid:$_POST['eptpoid'];
                
            $sql = "SELECT epnoid 
                      FROM equipamento_panicos 
                     WHERE epnepcvoid = $epcvoid 
                       AND epnpantoid = $pantoid
                       AND epndt_exclusao IS NULL";
                       
            $rs = pg_query($this->conn, $sql);
    
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){ 
                    $epnoid  = $arrRs['epnoid'];
                }
            } else {
                $sql = "INSERT INTO equipamento_panicos (epnepcvoid, 
                                                         epnpantoid,
                                                         epndt_inclusao,
                                                         epnusuoid_inclusao) 
                                                 VALUES ($epcvoid, 
                                                         $pantoid,
                                                         'now()',
                                                         $usuoid) 
                          RETURNING epnoid";     
                
                $rs = pg_query($this->conn, $sql);
           
                if(pg_num_rows($rs) > 0 ){
                    while ($arrRs = pg_fetch_array($rs)){ 
                        $epnoid  = $arrRs['epnoid'];
                    }
                }   
            }
            
            $sql = "INSERT INTO equipamento_panicos_testes (epntepnoid, 
                                                            epnteptpoid,
                                                            epntdt_inclusao,
                                                            epntusuoid_inclusao) 
                                                    VALUES ($epnoid, 
                                                            $eptpoid,
                                                            'now()',
                                                            $usuoid)";

            $rs = pg_query($this->conn, $sql);
            
            return "ok";
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    /**
     * Retorna lista de todos os comandos
     */
    public function listaComandos($encode = true)
    {
        try {
            $sql = "SELECT cmdoid, 
                           cmdcomando || '('||cmdmeio||') ' || cmddescricao  as comando 
                      FROM comandos 
                  ORDER BY cmdcomando,
                           cmdmeio";
                           
            $rs = pg_query($this->conn, $sql);
           
            $arrComandos = array('comandos');
            $aux = 0;
           
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){ 
                    $arrComandos['comandos'][$aux]['cmdoid']  = $arrRs['cmdoid'];
                    $arrComandos['comandos'][$aux]['comando'] = ($encode === false)?$arrRs['comando']:utf8_encode($arrRs['comando']);
                    
                    $aux++;
                }
            }
            
            if($encode === false) {
                return $arrComandos;
            } else {
                return json_encode($arrComandos);
            }
            
            return json_encode($arrComandos);
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    /**
     * Lista todos os testes, exceto os que já estão vinculados a algum comando ou alerta/pânico
     */
    public function listaTestes($encode = true)
    {
        try{
            $epcvoid = $_POST['epcvoid'];
            
            $sql = "SELECT eptpoid, 
                           epttdescricao as instrucao  
                      FROM equipamento_projeto_teste_planejado 
                      JOIN equipamento_projeto_tipo_teste_planejado 
                        ON eptpepttoid = epttoid
                     WHERE eptpdt_exclusao IS NULL 
                       AND eptpoid NOT IN (
                            SELECT ecmteptpoid
                              FROM equipamento_comandos_testes
                              JOIN equipamento_comandos 
                                ON ecmtecmoid = ecmoid 
                             WHERE ecmepcvoid = $epcvoid
                               AND ecmtdt_exclusao IS NULL)
                       AND eptpoid NOT IN (
                            SELECT epnteptpoid
                              FROM equipamento_panicos_testes
                              JOIN equipamento_panicos ON epntepnoid = epnoid 
                             WHERE epnepcvoid = $epcvoid
                               AND epntdt_exclusao IS NULL)
                  ORDER BY epttdescricao";
    
            $rs = pg_query($this->conn, $sql);
            
            $arrTestes = array('testes');
            $aux = 0;
            
            
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){ 
                    $arrTestes['testes'][$aux]['eptpoid']   = $arrRs['eptpoid'];
                    $arrTestes['testes'][$aux]['instrucao'] = ($encode === false)?$arrRs['instrucao']:utf8_encode($arrRs['instrucao']);
                    
                    $aux++;
                }
            }
            
            if($encode === false) {
                return $arrTestes;
            } else {
                return json_encode($arrTestes);
            }
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    /**
     * Lista todos os testes, exceto os que já estão vinculados a algum comando ou alerta/pânico
     */
    public function listaTestesDependentes($encode = true)
    {
        try{
            $epcvoid = $_POST['epcvoid'];
            
            $sql = "SELECT eptpoid, 
                           epttdescricao as instrucao 
                      FROM equipamento_comandos a
                      JOIN equipamento_comandos_testes b 
                        ON b.ecmtecmoid = a.ecmoid
                      JOIN equipamento_projeto_teste_planejado c 
                        ON c.eptpoid = b.ecmteptpoid
                      JOIN equipamento_projeto_tipo_teste_planejado d 
                        ON d.epttoid = c.eptpepttoid
                     WHERE a.ecmepcvoid = $epcvoid
                       AND b.ecmtdt_exclusao is null
                  ORDER BY epttdescricao";
    
            $rs = pg_query($this->conn, $sql);
            
            $arrTestes = array('dependentes');
            $aux = 0;
            
            
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){ 
                    $arrTestes['dependentes'][$aux]['eptpoid']   = $arrRs['eptpoid'];
                    $arrTestes['dependentes'][$aux]['instrucao'] = ($encode === false)?$arrRs['instrucao']:utf8_encode($arrRs['instrucao']);
                    
                    $aux++;
                }
            }
            
            if($encode === false) {
                return $arrTestes;
            } else {
                return json_encode($arrTestes);
            }
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
    public function listaComandosCadastrados($encode = true, $epcvoid = false)
    {
        try {
            $epcvoid = ($epcvoid)?$epcvoid: $_POST['epcvoid'];
            
            /*$sql = "SELECT ecmtoid,  
                           cmdoid,
                           cmdcomando || '('||cmdmeio||') ' || cmddescricao  as comando,  
                           epttdescricao || '('||eptpinstrucao||')' as instrucao,
                           eptpoid,
                           epttoid  
                      FROM equipamento_comandos_testes, 
                           equipamento_comandos, 
                           equipamento_projeto_teste_planejado, 
                           equipamento_projeto_classe_versao, 
                           comandos, 
                           equipamento_projeto_tipo_teste_planejado 
                     WHERE ecmtecmoid = ecmoid 
                       AND ecmteptpoid = eptpoid 
                       AND ecmcmdoid = cmdoid  
                       AND ecmepcvoid = epcvoid  
                       AND eptpepttoid = epttoid  
                       AND epcvoid = $epcvoid
                       AND ecmtdt_exclusao IS NULL
                       AND ecmdt_exclusao IS NULL";*/
                       
           $sql = "SELECT ecmt.ecmtoid,
                          cmd.cmdoid,
                          cmd.cmdcomando || '('||cmd.cmdmeio||') ' || cmd.cmddescricao  as comando,  
                          eptt.epttdescricao || '('||eptp.eptpinstrucao||')' as instrucao,
                          eptp.eptpoid,
                          eptt.epttoid,
                          ecmt.ecmteptpoid_antecessor,
                          eptt2.epttdescricao || '('||eptp2.eptpinstrucao||')' as depende
                     FROM equipamento_comandos_testes as ecmt
                     JOIN equipamento_comandos as ecm
                       ON ecmt.ecmtecmoid = ecm.ecmoid
                     JOIN equipamento_projeto_teste_planejado as eptp
                       ON ecmt.ecmteptpoid = eptp.eptpoid 
                     JOIN equipamento_projeto_classe_versao as epvc
                       ON ecm.ecmepcvoid = epvc.epcvoid
                     JOIN comandos as cmd
                       ON ecm.ecmcmdoid = cmd.cmdoid  
                     JOIN equipamento_projeto_tipo_teste_planejado as eptt
                       ON eptp.eptpepttoid = eptt.epttoid
                LEFT JOIN equipamento_projeto_teste_planejado as eptp2
                       ON eptp2.eptpoid = ecmt.ecmteptpoid_antecessor
                LEFT JOIN equipamento_projeto_classe_versao as epvc2
                       ON ecm.ecmepcvoid = epvc2.epcvoid
                LEFT JOIN comandos as cmd2
                       ON ecm.ecmcmdoid = cmd2.cmdoid  
                LEFT JOIN equipamento_projeto_tipo_teste_planejado as eptt2
                       ON eptp2.eptpepttoid = eptt2.epttoid
                    WHERE epvc.epcvoid = $epcvoid
                      AND ecmt.ecmtdt_exclusao IS NULL
                      AND ecm.ecmdt_exclusao IS NULL";
    
            $rs = pg_query($this->conn, $sql);
                        
            $arrComandos = array('comandos');
            $aux = 0;
            
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){ 
                    $arrComandos['comandos'][$aux]['ecmtoid']    = $arrRs['ecmtoid'];
                    $arrComandos['comandos'][$aux]['comando']    = ($encode === false)?$arrRs['comando']:utf8_encode($arrRs['comando']);
                    $arrComandos['comandos'][$aux]['instrucao']  = ($encode === false)?$arrRs['instrucao']:utf8_encode($arrRs['instrucao']);
                    $arrComandos['comandos'][$aux]['cmdoid']     = $arrRs['cmdoid'];
                    $arrComandos['comandos'][$aux]['eptpoid']    = $arrRs['eptpoid'];
                    $arrComandos['comandos'][$aux]['antecessor'] = ($encode === false)?$arrRs['depende']:utf8_encode($arrRs['depende']);
                    
                    $aux++;
                }
            }
            
            $arrComandos['total_registros'] = utf8_encode('A pesquisa retornou ' . pg_num_rows($rs) . ' registro(s).');
            
            if($encode === false) {
                return $arrComandos;
            } else {
                return json_encode($arrComandos);
            }
        } catch(Exception $e) {
            return "erro";
        } 
    }
    
    /**
     * Lista todos os alertas/pânico 
     */
    public function listaAlertasPanico($encode = true)
    {
        try {
            $sql = "SELECT pantoid, 
                           pantdescricao as panico 
                      FROM panico_tipo 
                  ORDER BY pantdescricao";
                  
            $rs = pg_query($this->conn, $sql);
            
            $arrAlertas = array('alertas');
            $aux = 0;
    
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)) { 
                    $arrAlertas['alertas'][$aux]['pantoid'] = $arrRs['pantoid'];
                    $arrAlertas['alertas'][$aux]['panico']  = ($encode === false)?$arrRs['panico']:utf8_encode($arrRs['panico']);
                    
                    $aux++;
                }
            }
            
            if($encode === false) {
                return $arrAlertas;
            } else {
                return json_encode($arrAlertas);
            }
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    /**
     * Lista todos os alertas/pânico cadastrados para um teste específico
     * 
     * @param boolean $encode
     * @param integer $epcvoid
     */
    public function listaAlertasPanicoCadastrados($encode = true, $epcvoid = false)
    {
        try {
            $epcvoid = ($epcvoid)?$epcvoid: $_POST['epcvoid'];
                       
            $sql = "SELECT epntoid, 
                           pantdescricao,
                           pantoid,
                           eptpoid,
                           epttdescricao || '('||eptpinstrucao||')' as eptpinstrucao 
                      FROM equipamento_projeto_classe_versao 
                      JOIN equipamento_panicos 
                        ON epnepcvoid = epcvoid
                      JOIN panico_tipo 
                        ON epnpantoid = pantoid
                      JOIN equipamento_panicos_testes 
                        ON epntepnoid = epnoid
                      JOIN equipamento_projeto_teste_planejado 
                        ON epnteptpoid = eptpoid
    		          JOIN equipamento_projeto_tipo_teste_planejado 
    		            ON epttoid = eptpepttoid
                     WHERE epcvoid = $epcvoid
                       AND epntdt_exclusao IS NULL
                       AND epndt_exclusao IS NULL";
                       
            $rs = pg_query($this->conn, $sql);
                        
            $arrAlertas = array('alertas');
            $aux = 0;
            
            if(pg_num_rows($rs) > 0 ){
                while ($arrRs = pg_fetch_array($rs)){
                    $arrAlertas['alertas'][$aux]['epntoid']       = $arrRs['epntoid'];
                    $arrAlertas['alertas'][$aux]['pantdescricao'] = ($encode === false)?$arrRs['pantdescricao']:utf8_encode($arrRs['pantdescricao']);
                    $arrAlertas['alertas'][$aux]['eptpinstrucao'] = ($encode === false)?$arrRs['eptpinstrucao']:utf8_encode($arrRs['eptpinstrucao']);
                    $arrAlertas['alertas'][$aux]['pantoid']       = $arrRs['pantoid'];
                    $arrAlertas['alertas'][$aux]['eptpoid']       = $arrRs['eptpoid'];
                    
                    $aux++;
                }
            }
            
            $arrAlertas['total_registros'] = utf8_encode('A pesquisa retornou ' . pg_num_rows($rs) . ' registro(s).');
            
            if($encode === false) {
                return $arrAlertas;
            } else {
                return json_encode($arrAlertas);
            }
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    /**
     * Exclui comando 
     * 
     * @param integer $emctoid
     */
    public function excluiComando($ecmtoid = false)
    {
        $ecmtoid = ($ecmtoid)?$ecmtoid:$_POST['ecmtoid'];
        $epcvoid = ($_POST['epcvoid'])?$_POST['epcvoid']:null;
        $usuoid  = $_SESSION['usuario']['oid'];
        
        try {
            $sql = "UPDATE equipamento_comandos_testes
                       SET ecmtdt_exclusao = 'now()',
                           ecmtusuoid_exclusao = $usuoid
                     WHERE ecmtoid = $ecmtoid";
                          
            $rs = pg_query($this->conn, $sql);
            
            /*$sql = "UPDATE equipamento_comandos 
                       SET ecmdt_exclusao = 'now()',
                           ecmusuoid_exclusao = $usuoid
                     WHERE ecmepcvoid = ".$epcvoid;
                     
            $rs = pg_query($this->conn, $sql);*/ 
            
            return "ok";
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    public function verificaDependenciaComando()
    {
        $epcvoid = $_POST['epcvoid']; 
        $ecmtoid = $_POST['ecmtoid'];
        
        try {

            $possui = "";
            
            $sql = "SELECT b.ecmteptpoid_antecessor
                      FROM equipamento_comandos a
                      JOIN equipamento_comandos_testes b 
                        ON b.ecmtecmoid = a.ecmoid
                     WHERE a.ecmepcvoid = $epcvoid
                       and b.ecmteptpoid_antecessor = (SELECT ecmteptpoid FROM equipamento_comandos_testes WHERE ecmtoid = $ecmtoid)
                       AND b.ecmtdt_exclusao IS NULL";

            $rs = pg_query($this->conn, $sql);
            
            if(pg_num_rows($rs) > 0) {
                while ($arrRs = pg_fetch_array($rs)){ 
                    $possui  = $arrRs['ecmteptpoid_antecessor'];
                }
            }
            
            return $possui;
        } catch(Exception $e) {
            return "";
        }
    }
    
    /**
     * Exclui alerta
     * 
     * @param integer $epntoid
     */
    public function excluiAlertaPanico($epntoid = false)
    {
        $epntoid = ($epntoid)?$epntoid:$_POST['epntoid'];
        $epcvoid = ($_POST['epcvoid'])?$_POST['epcvoid']:null;
        $usuoid  = $_SESSION['usuario']['oid'];
        
        try {
            $sql = "UPDATE equipamento_panicos_testes
                       SET epntdt_exclusao = 'now()',
                           epntusuoid_exclusao = $usuoid 
                     WHERE epntoid = $epntoid";
                          
            $rs = pg_query($this->conn, $sql);
            
            /*$sql = "UPDATE equipamento_panicos 
                       SET epndt_exclusao = 'now()',
                           epnusuoid_exclusao = $usuoid
                     WHERE epnepcvoid = ".$epcvoid;
                     
            $rs = pg_query($this->conn, $sql);*/
            
            return "ok";
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    /**
     * Verifica se já existe um teste com a mesma configuração do formulário
     */
    public function verificaIntegridadeTeste()
    {
        $eproid = $_POST['eproid'];
        $eqcoid = $_POST['eqcoid'];
        $eveoid = $_POST['eveoid'];
        
        try {
            $sql = "SELECT epcvoid 
                      FROM equipamento_projeto_classe_versao 
                     WHERE epcveproid = $eproid 
                       AND epcveqcoid = $eqcoid 
                       AND epcveveoid = $eveoid
                       AND epcvdt_exclusao IS NULL";   
                       
            $rs = pg_query($this->conn, $sql);
    
            while ($arrRs = pg_fetch_array($rs)){ 
                $epcvoid  = $arrRs['epcvoid'];
            }
            
            return $epcvoid;
        } catch(Exception $e) {
            return "erro";
        }
    }
    
    public function excluiTeste()
    {
    	$usuoid  = $_SESSION['usuario']['oid'];
    	$epcvoid = $_POST['epcvoid'];
    	
    	try{
    		$sql = "UPDATE equipamento_projeto_classe_versao
    				   SET epcvdt_exclusao = 'now()',
    				       epcvusuoid_exclusao = $usuoid
    				 WHERE epcvoid = $epcvoid";

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
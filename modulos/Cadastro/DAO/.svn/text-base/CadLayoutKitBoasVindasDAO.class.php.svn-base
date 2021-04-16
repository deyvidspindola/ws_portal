<?php

/**
 * @file CadLayoutKitBoasVindasDAO.class.php
 * @author Keidi Nienkotter
 * @version 16/01/2013 10:57:02
 * @since 16/01/2013 10:57:02
 * @package SASCAR CadLayoutKitBoasVindasDAO.class.php 
 */

class CadLayoutKitBoasVindasDAO {
    
    private $conn;
    private $usuoid;
		
    public function getConfiguracoes() {
                
        $sql = "
            SELECT 
            	lconfoid, lconfdescricao 
            FROM 
            	layout_lconfiguracao 
            WHERE 
            	lconfdt_exclusao IS NULL        
            ORDER BY 
            	lconfdescricao";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['confoid']       = pg_fetch_result($rs, $i, 'lconfoid');
            $result[$i]['confdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'lconfdescricao'));
        }
        
        return $result;
        
    }
    
    public function getPropostas() {
                
        $sql = "
            SELECT 
            	tppoid, tppdescricao 
			FROM 
				tipo_proposta
			WHERE 
				tppoid_supertipo IS NULL          
            ORDER BY 
                tppdescricao";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['tppoid']       = pg_fetch_result($rs, $i, 'tppoid');
            $result[$i]['tppdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'tppdescricao'));
        }
        
        return $result;
        
    }
    
    public function getSubPropostas() {
    
        $proposta =  $_POST['tipoProposta'];

        $sql = "
            SELECT 
            	tppoid, tppdescricao 
			FROM 
				tipo_proposta
			WHERE 
				tppoid_supertipo IS NOT NULL   
			AND tppoid_supertipo = $proposta   
            ORDER BY 
                tppdescricao";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['tppoid']       = pg_fetch_result($rs, $i, 'tppoid');
            $result[$i]['tppdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'tppdescricao'));
        }
        
        return $result;
        
    }
	
    public function getContratos() {
                
        $sql = "
            SELECT 
            	tpcoid, tpcdescricao 
            FROM 
            	tipo_contrato
            WHERE 
            	tpcativo IS TRUE        
            ORDER BY 
            	tpcdescricao";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['tpcoid']       = pg_fetch_result($rs, $i, 'tpcoid');
            $result[$i]['tpcdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'tpcdescricao'));
        }
        
        return $result;
        
    }
    
    public function getClasses() {
    
    	$sql = "
        	SELECT 
    			eqcoid,eqcdescricao 
    		FROM 
    			equipamento_classe
    		ORDER BY eqcdescricao ASC
            ";
    
    	$rs = pg_query($this->conn, $sql);
    
    	$result = array();
    
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$result[$i]['eqcoid']       = pg_fetch_result($rs, $i, 'eqcoid');
    		$result[$i]['eqcdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'eqcdescricao'));
    	}
    
    	return $result;
    
    }
    
    public function getServidores() {
    
    	$sql = "
        	SELECT 
    			srvoid,srvdescricao 
    		FROM 
    			servidor_email 
    		ORDER BY srvdescricao ASC
            ";
    
    	$rs = pg_query($this->conn, $sql);
    
    	$result = array();
    
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$result[$i]['srvoid']       = pg_fetch_result($rs, $i, 'srvoid');
    		$result[$i]['srvdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'srvdescricao'));
    	}
    
    	return $result;
    
    }
	
    public function getLayouts() {
                
        $sql = "
        	SELECT 
        		lwkoid, lwkdescricao, lwkpadrao, lwkassunto_email
			FROM 
				layout_welcome_kit
			WHERE 
				lwkdt_exclusao IS NULL 
			ORDER BY 
				lwkdescricao
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['lwkoid']       = pg_fetch_result($rs, $i, 'lwkoid');
            $result[$i]['lwkdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'lwkdescricao'));
            $result[$i]['lwkpadrao'] = utf8_encode(pg_fetch_result($rs, $i, 'lwkpadrao'));
            $result[$i]['lwkassunto_email'] = utf8_encode(pg_fetch_result($rs, $i, 'lwkassunto_email'));
        }
        
        return $result;
        
    }
    
    public function getTipoLayouts() {
    
    	$sql = " SELECT  
					tcloid, tclcodigo, tcldescricao
				 FROM  
    			    tipo_config_layout
    			 WHERE 
    				tclativo IS TRUE
				 ORDER BY 
					tcldescricao ";
    
    	$rs = pg_query($this->conn, $sql);
    
    	$result = array();
    
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$result[$i]['tcloid']       = pg_fetch_result($rs, $i, 'tcloid');
    		$result[$i]['tclcodigo']    = utf8_encode(pg_fetch_result($rs, $i, 'tclcodigo'));
    		$result[$i]['tcldescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'tcldescricao'));
     	}
    
    	return $result;
    
    }
	
    public function getLayout() {
                
        $idLayout =  $_POST['idLayout'];
        
        $sql = "
        	SELECT 
        		lwkoid, lwkdescricao, lwkpadrao, lwklayout, lwkassunto_email 
			FROM 
				layout_welcome_kit
			WHERE 
				lwkdt_exclusao IS NULL 
			AND 
				lwkoid = $idLayout
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        if(pg_num_rows($rs)>0) {
            $result['lwkoid']       = pg_fetch_result($rs, $i, 'lwkoid');
            $result['lwkdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'lwkdescricao'));
            $result['lwkpadrao']    = utf8_encode(pg_fetch_result($rs, $i, 'lwkpadrao'));
            $result['lwklayout']    = utf8_encode(stripcslashes( pg_fetch_result($rs, $i, 'lwklayout')));
            $result['lwkassunto_email']    = utf8_encode(pg_fetch_result($rs, $i, 'lwkassunto_email'));
        }else{
            throw new Exception("Layout inexistente! ",1);
        }
        
        return $result;
        
    }
    
    public function getHtmlLayout() {
               
        $idLayout =  $_POST['idLayout'];

        $sql = "
            SELECT 
            	lwklayout 
			FROM 
				layout_welcome_kit
			WHERE 
				lwkdt_exclusao IS NULL 
 			AND lwkoid = $idLayout   
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = utf8_encode( stripcslashes( pg_fetch_result($rs, 0, 'lwklayout') ));
        
        return $result;
    }
    
    public function getValoresConfig() {
               
        $idConfiguracao =  $_POST['config'];

        $sql = "
            SELECT 
            	lconftppoid, lconftppoid_sub, lconftpcoid, lconflwkoid, lconeqcoid, lconsrvoid, lconanexo, lcontcloid
			FROM 
				layout_lconfiguracao 
			WHERE 
				lconfdt_exclusao IS NULL
			AND 
				lconfoid = $idConfiguracao   
            ";
        
        $rs = pg_query($this->conn, $sql);
               
        $result = array();
        
        $result['conftppoid']       = pg_fetch_result($rs, 0, 'lconftppoid');
        $result['conftppoid_sub']   = pg_fetch_result($rs, 0, 'lconftppoid_sub');
        $result['conftpcoid']       = pg_fetch_result($rs, 0, 'lconftpcoid');
        $result['conflwkoid']       = pg_fetch_result($rs, 0, 'lconflwkoid');
        $result['lconeqcoid']       = pg_fetch_result($rs, 0, 'lconeqcoid');
        $result['lconsrvoid']       = pg_fetch_result($rs, 0, 'lconsrvoid');
        $result['lconanexo']        = pg_fetch_result($rs, 0, 'lconanexo');
        $result['lcontcloid']       = pg_fetch_result($rs, 0, 'lcontcloid');
        
        return $result;
    }
    
    public function deletaConfiguracao($idConfiguracao) {
               
        $sql = "
            UPDATE layout_lconfiguracao 
            SET	
            	lconfdt_exclusao = NOW(),
            	lconfusuoid_exclusao = $this->usuoid
			WHERE 
				lconfoid = $idConfiguracao   
            ";
        
        $result = pg_query($this->conn,$sql);
	    $resDelete = pg_affected_rows($result);
	    
		if ($resDelete <= 0) {
			throw new Exception("Falha ao excluir! ",1);
		}else{
		    return true;
		}
        
    }
    
    public function gravaConfiguracao($cConfiguracao,$cProposta = null,$cSubproposta = null,$cContrato = null, $cClasse = null, $cServidor = null, $cLayout, $cTipoLayout, $anexo = null) {    	
    	// validar se já existe configuração com a combinação enviada
        try {
	    	$sqlValida = "
	        	SELECT 
	        		count(*) as config
				FROM 
					layout_lconfiguracao 
	            WHERE 
	            	lconfdt_exclusao IS NULL ";
	        
	        if($cConfiguracao > 0){
	            $sqlValida .= " AND lconfoid <> $cConfiguracao ";
	        }
	        
	        if($cProposta == null){
	            $sqlValida .= " AND lconftppoid IS NULL ";             
	        }else {
	            $sqlValida .= " AND lconftppoid = $cProposta ";            
	        }
	        
	        if($cSubproposta == null){
	            $sqlValida .= " AND lconftppoid_sub IS NULL ";              
	        }else {          
	            $sqlValida .= " AND lconftppoid_sub = $cSubproposta "; 
	        }
	        
	        if($cContrato == null){
	            $sqlValida .= " AND lconftpcoid IS NULL ";                 
	        }else {      
	            $sqlValida .= " AND lconftpcoid = $cContrato ";  
	        }
	        
	        if($cClasse == null){
	        	$sqlValida .= " AND lconeqcoid IS NULL ";
	        }else {
	        	$sqlValida .= " AND lconeqcoid = $cClasse ";
	        }
	        
	        if(!$rs = pg_query($this->conn,$sqlValida)){
	        	throw new Exception("Falha no cadastro.",1);
	        }
	        $result = pg_fetch_result($rs, 0, 'config');
	        
	        if($result > 0){
	            throw new Exception("Já existe uma configuração cadastrada com os parâmetros selecionados.",1);
	        }else {
	            
	            // gravar
	            
	            // montar descricao com as descrição dos itens
	            $descricaoConfiguracao = $this->montaDescricaoConfiguracao($cProposta,$cSubproposta,$cContrato, $cClasse);
	               
	            if($cProposta == null ){
	                $cProposta = 'null';
	            }
	            if($cSubproposta == null ){
	                $cSubproposta = 'null';
	            }
	            if($cContrato == null ){
	                $cContrato = 'null';
	            }
	            if($cClasse == null ){
	            	$cClasse = 'null';
	            }
	            if($cServidor == null ){
	            	$cServidor = 'null';
	            }
	            if($anexo == null ){
	            	$anexo = '';
	            }
	                
	            if($cConfiguracao == 0){
	                // insert                    
	                
	                $campos = "lconftppoid, lconftpcoid, lconflwkoid, lcontcloid, lconfdescricao, lconftppoid_sub, lconeqcoid, lconsrvoid, lconanexo";
	                $valores = $cProposta.",
	                			".$cContrato.",
	                			".$cLayout.",
	                			".$cTipoLayout.",		
	                			'".trim($descricaoConfiguracao)."',
	                			".$cSubproposta.",
	                			".$cClasse.",
	                			".$cServidor.",
	                			'".$anexo."'";
	                
	                $sql = "
	                	INSERT INTO layout_lconfiguracao 
	                    	($campos)
	                    VALUES
	                    	($valores)";
	                
	                $retorno = "insert";
	                
	            }else{
	                // update
	                
	                $sql = "
	                	UPDATE 
	                    	layout_lconfiguracao 
	                    SET 
	                    	lconflwkoid = $cLayout, lcontcloid = $cTipoLayout, lconsrvoid = $cServidor
	                    WHERE
	                    	lconfoid = $cConfiguracao
	                ";        
	                      
	                $retorno = "update";
	            }

	            //echo $sql;exit;
	            
	            if(!$grava = pg_query($this->conn,$sql)){
	                throw new Exception("Falha no cadastro.",1);
	            }
	            
	            return $retorno;
	        }
        }catch (Exception $e){
        	$erro = array(
                "error" => $e->getCode(),
                "msg" => utf8_encode($e->getMessage())
            );
        	return $erro;
        }
        
    }
    
    public function deletaLayout($idLayout) {

        // validar se não é o padrão
        // validar se está utilizado em alguma configuração
        $sqlValida = "SELECT 
                        	lwkpadrao, count(layout_lconfiguracao.*) AS config
                        FROM
                        	layout_welcome_kit
                        LEFT JOIN layout_lconfiguracao ON lconflwkoid = lwkoid AND lconfdt_exclusao IS NULL
                        WHERE 
                        	lwkoid = $idLayout 
                        GROUP BY lwkoid";
                
        $resultValida = pg_query($this->conn,$sqlValida);
        
        if(pg_num_rows($resultValida) > 0){
        
            if(pg_fetch_result($resultValida, 0, 'lwkpadrao') == 't'){
                throw new Exception("Este layout é definido como padrão. Se deseja excluir, favor configurar outro layout como padrão.",1);
            }
            
            if(pg_fetch_result($resultValida, 0, 'config') > 0){
                throw new Exception("Este layout é utilizado em uma configuração. Se deseja excluir, favor retirar da configuração utilizada.",1);
            }
            
                   
            $sql = "
                UPDATE layout_welcome_kit
                SET	
                	lwkdt_exclusao = NOW(),
                	lwkusuoid_exclusao = $this->usuoid
    			WHERE 
    				lwkpadrao IS FALSE
    			AND
    				lwkoid = $idLayout   
                ";
            
            $result = pg_query($this->conn,$sql);
    	    $resDelete = pg_affected_rows($result);
    	    
    		if ($resDelete <= 0) {
    			throw new Exception("Falha ao excluir! ",1);
    		}else{
    		    return true;
    		} 
        }
        
    }
    
    public function validaPadrao($idLayout = null, $nomeLayout, $htmlLayoutEdicao, $padraoLayout){
                
        // valida padrão
        $sql = "SELECT 
        			count(*) AS qtde
                FROM 
                	layout_welcome_kit
                WHERE 
                	lwkpadrao IS TRUE ";
        
        if($idLayout){
            $sql .= "AND 
            			lwkoid <> $idLayout ";
        }
        $result = pg_query($this->conn,$sql);
        $qtde = pg_fetch_result($result, 0, 'qtde');
        
        if($padraoLayout == false && $qtde == 0){
            throw new Exception("É obrigatório haver um layout cadastrado como padrão! ",1);
        }
        if($padraoLayout == true && $qtde > 0){
            throw new Exception("Há outro layout definido como padrão. Deseja tornar este layout como padrão? ",2);
        }
        
        return true;
        
        
    }
    
    public function gravaPadrao($idLayout = null, $nomeLayout, $assuntoLayout, $htmlLayoutEdicao, $padraoLayout, $definePadrao){

        try {
            if($definePadrao == 'sim'){
                // se for true o definePadrao então limpa o padrão
                $sqlLimpaPadrao = "UPDATE 
            							layout_welcome_kit
    								SET 
    									lwkpadrao = 'f'";
                $result = pg_query($this->conn,$sqlLimpaPadrao);
            
            }elseif($definePadrao == 'nao'){
                // ignora valor padrão
                $padraoLayout = false;
            }
            
            // grava registro
             if($idLayout == 0 || $idLayout == null){
                 // insert
                 $sql = "
                 		INSERT INTO layout_welcome_kit
    						(lwklayout,lwkdescricao,lwkpadrao,lwkassunto_email)
    					VALUES
                        	('".utf8_decode(addslashes($htmlLayoutEdicao))."','".utf8_decode($nomeLayout)."','".($padraoLayout?'t':'f')."','".utf8_decode($assuntoLayout)."')";
                 
                 $retorno = "insert";
             }else{
                 // update
                 $sql = "UPDATE layout_welcome_kit
                        SET 
                        	lwklayout = '".utf8_decode(addslashes($htmlLayoutEdicao))."',
                        	lwkdescricao = '".utf8_decode($nomeLayout)."',
                        	lwkassunto_email = '".utf8_decode($assuntoLayout)."',
                        	lwkpadrao = '".($padraoLayout?'t':'f')."'
                        WHERE 
                        	lwkoid = $idLayout";
                 
                 $retorno = "update";
            }
            
            
            if(!$grava = pg_query($this->conn,$sql)){
                throw new Exception("Falha no cadastro.",1);
            }
            
            return $retorno;
            
        } catch (Exception $e) {
            throw new Exception("Falha no cadastro.",1);
        }
        
        
    }
    
    private function montaDescricaoConfiguracao($cProposta = null,$cSubproposta = null,$cContrato = null, $cClasse = null){
        
        $descricaoConfiguracao = '';
        
        if($cProposta != null){
            $sqlProposta = "
            	SELECT 
            		tppdescricao
    			FROM 
    				tipo_proposta
    			WHERE 
    				tppoid = $cProposta";
            $rs = pg_query($this->conn,$sqlProposta);
            $descricao = pg_fetch_result($rs, 0, 'tppdescricao');
            
            $arrayDescricao = explode(' ', $descricao);
            $descricaoConfiguracao .= $arrayDescricao[0].' ';
        }
        
        if($cSubproposta != null){
            $sqlSubProposta = "
            	SELECT 
            		tppdescricao
    			FROM 
    				tipo_proposta
    			WHERE 
    				tppoid = $cSubproposta";
            $rs = pg_query($this->conn,$sqlSubProposta);
            $descricao = pg_fetch_result($rs, 0, 'tppdescricao');
            
            $arrayDescricao = explode(' ', $descricao);
            $descricaoConfiguracao .= $arrayDescricao[0].' ';
        }
        
        if($cContrato != null){
            $sqlContrato = "
                SELECT 
                	tpcdescricao
                FROM 
                	tipo_contrato
                WHERE 
                	tpcoid = $cContrato";
            $rs = pg_query($this->conn,$sqlContrato);
            $descricao = pg_fetch_result($rs, 0, 'tpcdescricao');
            
            $arrayDescricao = explode(' ', $descricao);
            $descricaoConfiguracao .= $arrayDescricao[0].' ';
        }
        
        if($cClasse != null){
        	$sqlClasse = "
	        	SELECT 
    				eqcdescricao 
    			FROM 
    				equipamento_classe
				WHERE 
        			eqcoid = $cClasse";
        	
        	$rs = pg_query($this->conn,$sqlClasse);
        	$descricao = pg_fetch_result($rs, 0, 'eqcdescricao');
        
        	$arrayDescricao = explode(' ', $descricao);
        	$descricaoConfiguracao .= $arrayDescricao[0];
        }
        
        return $descricaoConfiguracao;
    }
    
    public function __construct() {        
        global $conn;
        $this->conn = $conn;   
        $this->usuoid = $_SESSION['usuario']['oid'];     
    }
    
    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }
    
}


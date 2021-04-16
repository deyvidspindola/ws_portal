<?php

/**
 * @file CadClienteDAO.class.php
 * @author Keidi Nienkotter
 * @version 29/07/2013 11:00:28
 * @since 29/07/2013 11:00:28
 * @package SASCAR CadClienteDAO.class.php 
 */

class CadClienteDAO {
    
    private $conn;  
    private $cd_usuario;
    
    public function __construct() {        
        global $conn;
        $this->conn = $conn;   
        $this->cd_usuario = $_SESSION['usuario']['oid'];    

    }
	
	public function begin() {
            pg_query($this->conn, "BEGIN");
    }

    public function commit() { 
            pg_query($this->conn, "COMMIT");
    }

     public function rollback() {
            pg_query($this->conn, "ROLLBACK");
    }

    public function getClassesCliente() {
        
        $sql = "
                SELECT
                    clicloid,
                    clicldescricao
                FROM
                    cliente_classe
                WHERE
                    clicldt_exclusao IS NULL
                ORDER BY
                    clicldescricao  
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        if(pg_num_rows($rs) > 0 ){
            for($i = 0; $i < pg_num_rows($rs); $i++) {
                $result[$i]['clicloid']       = pg_fetch_result($rs, $i, 'clicloid');
                $result[$i]['clicldescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'clicldescricao'));
            }
        }
        
        return $result;
    }

    public function getDadosCliente($clioid) {
    	
    	$sql = "
        		SELECT  
					clitipo,
					cliclicloid as clicloid,
					clino_cpf,
					clinome,
					clirg,
					cliemissor_rg,
					cliuf_emiss,
					TO_CHAR(clidt_emissao_rg, 'DD/MM/YYYY') AS clidt_emissao_rg,
					TO_CHAR(clidt_nascimento, 'DD/MM/YYYY') AS clidt_nascimento,
					clinaturalidade,
					clipai,
					climae,
					clisexo,
					cliestado_civil,
					TO_CHAR(clidt_fundacao, 'DD/MM/YYYY') AS clidt_fundacao,
					clino_cgc,
					clireg_simples,
					cliinscr,
					cliinscr_municipal,
					cliuf_inscr,
                                        clicnae
										
				FROM
					clientes
				WHERE
					clioid = $clioid
            ";
    	
    	$rs = pg_query($this->conn, $sql);
    	
    	$result = array();
    	
    	if(pg_num_rows($rs) > 0 ){
    		while ($arrRs = pg_fetch_array($rs)){
    			$result = $arrRs;
    		}
    	}
    	    	
    	return $result;
    }

    public function pesquisar($params) {
    	
    	$sql = "SELECT 
    				clicldescricao, 
    				cliclicloid, 
    				clioid, 
	    			clinome, 
					TO_CHAR(clidt_cadastro,'dd/mm/yyyy') AS clidt_cadastro,     			
					CASE WHEN clitipo = 'F' 
    						THEN 'FÍSICA'
						 WHEN clitipo = 'J' 
    						THEN 'JURÍDICA'
					END AS clitipo,		
					CASE WHEN clitipo = 'F' 
    						THEN cliuf_res
						 WHEN clitipo = 'J' 
    						THEN cliuf_com
					END AS cliuf,
					CASE WHEN clitipo = 'F' 
    						THEN clicidade_res
					     WHEN clitipo = 'J' 
    						THEN clicidade_com
					END AS clicidade,
					CASE WHEN clitipo = 'F' 
    						THEN clino_cpf
						 WHEN clitipo = 'J' 
    						THEN clino_cgc
					END AS clicpfcgc,
					gernome,
    				clivisualizacao_sasgc,
                                clicnae
				FROM 
    				clientes  
				LEFT JOIN 
    				cliente_classe ON clicloid = cliclicloid AND clicldt_exclusao IS NULL 
				LEFT JOIN 
    				gerenciadora ON cligeroid = geroid
				WHERE 
    				clidt_exclusao IS NULL
    			";
    	
    	if($params['nome_busca']){
    		
    		$sql .= " AND clinome ILIKE '%".$params['nome_busca']."%'";
    		
    	}
    	

    	// filtra por tipo pessoa
    	if($params['pesq_clitipo'] == 'F'){
    		
    		$sql .= " AND clitipo = 'F'
    				";

    		// filtra por CPF
    		if($params['cpf_busca']) {
    			
    			$sql .= " AND clino_cpf = " . $params['cpf_busca'] . "
    					";
    			
    		}
    		
    	}elseif($params['pesq_clitipo'] == 'J'){
    		
    		$sql .= " AND clitipo = 'J'
    				";

    		// filtra por CNPJ
    		if($params['cpf_busca']) {
    			
    			$sql .= " AND clino_cgc = " . $params['cpf_busca'] . "
    					";
    		
    		}
    		
    	}
    	
    	// filtra por classe cliente
    	if($params['pesq_clicloid']){
    		
    		$sql .= " AND cliclicloid = " . $params['pesq_clicloid'] . "
    					";
    	}
    	
    	$rs = pg_query($this->conn, $sql);
    	
    	$result = array();
    	
    	if(pg_num_rows($rs) > 0) {
    		
    		$result = pg_fetch_all($rs);
    		
    	}
    	
        return $result;
        
    }
    
    public function getHistorico($clioid) {
        $tabela_particionada = "cliente_historico" . ($clioid % 10);
        $sql = "SELECT clihclioid,clihalteracao, 
                    CASE clihtipo WHEN 'A' THEN 'Alteração'
                    WHEN 'C' THEN 'Incluído/Excluído Anexo End.'
                    WHEN 'B' THEN 'Incluído/Excluído Obrigacao Financeira.'
                    WHEN 'D' THEN 'Alteração Endereço Cobranca'
                    WHEN 'E' THEN 'Excluído'
                    WHEN 'V' THEN 'Alterado dia de Vcto.'
                    WHEN 'F' THEN 'Alteração Endereço de Entrega.'
                    ELSE ''
                    END AS tipo, 
                    TO_CHAR(clihcadastro,'dd/MM/yyyy HH24:mi:ss') AS dt_cadastro, 
                    (SELECT ds_login FROM usuarios WHERE cd_usuario=clihusuoid)AS login 
                FROM 
                    $tabela_particionada 
                WHERE 
                    clihclioid= '$clioid'
                ORDER BY 
                    clihcadastro DESC;";

        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[] = array(
                'dt_cadastro' => pg_fetch_result($rs, $i, 'dt_cadastro'),
                'tipo' => pg_fetch_result($rs, $i, 'tipo'),
                'clihalteracao' => pg_fetch_result($rs, $i, 'clihalteracao'),
                'login' => pg_fetch_result($rs, $i, 'login')
                );
        }
        
        return $result;
        
    }
    
    public function getBeneficio($clioid) {
        $sql = "SELECT
                    embdescricao AS empresa, 
                    ebtdescricao AS beneficio,
                    clbapolice AS apolice,
                    clbitem AS item,
                    clbcartao AS cartao,
                    TO_CHAR(clbdt_final_vigencia::date, 'DD/MM/YYYY') AS validade,
                    clboid
                FROM 
                    cliente_beneficio, 
                    empresa_beneficio_tipo, 
                    empresa_beneficio
                WHERE
                    clbebtoid = ebtoid
                    AND clbdt_exclusao is null
                    AND emboid=ebtemboid 
                    AND clbclioid='$clioid'";

        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[] = array(
                'empresa' => pg_fetch_result($rs, $i, 'empresa'),
                'beneficio' => pg_fetch_result($rs, $i, 'beneficio'),
                'apolice' => pg_fetch_result($rs, $i, 'apolice'),
                'item' => pg_fetch_result($rs, $i, 'item'),
                'cartao' => pg_fetch_result($rs, $i, 'cartao'),
                'validade' => pg_fetch_result($rs, $i, 'validade'),
                'clboid' => pg_fetch_result($rs, $i, 'clboid')
                );
        }
        
        return $result;
    	
    }

    public function getSegmentacao($clioid, $tpscodigoslug) {
            $segmentacaoArray = array(0);
            if ($clioid > 0) {
            $sql = "SELECT 
                        clstsgoid 
                    FROM 
                        cliente_segmentacao
                    WHERE 
                        clstsgoid IN (
                            SELECT 
                                        tpsoid 
                            FROM 
                                tipo_segmentacao
                            WHERE 
                                tpssegmentacaooid = (
                                    SELECT 
                                        tpsoid 
                                    FROM 
                                        tipo_segmentacao 
                                    WHERE 
                                        tpscodigoslug = '$tpscodigoslug'
                                    )
                            )
                    AND 
                clsclioid = ".$clioid;
                if ($query = pg_query($this->conn, $sql)) {
                    if (pg_num_rows($query) > 0)
                        $segmentacaoArray = pg_fetch_row($query);
                }
            }

            //var_dump($segmentacaoArray);
        
            $sql = "SELECT 
                        tipo_segmentacao.* 
                    FROM
                        tipo_segmentacao 
                    WHERE   
                        tpssegmentacaooid IN (
                            SELECT 
                                    tpsoid 
                            FROM 
                                tipo_segmentacao
                            WHERE
                                tpscodigoslug = '$tpscodigoslug'
                        )
                    AND 
                        tpsdt_exclusao IS NULL
                    ORDER BY 
                        tpsdescricao ASC";
            $result = array();
            if ($query = pg_query($this->conn, $sql)) {
                if (pg_num_rows($query) > 0) {
                    while ($row=pg_fetch_object($query)) {
                        if ( ( $segmentacaoArray[0] > 0 ) && ( $segmentacaoArray[0] == $row->tpsoid ) ){
                            $result[$row->tpsoid] = array($row->tpsdescricao , 'selected');
                        } else {
                            $result[$row->tpsoid] = array($row->tpsdescricao , '');
                        }
                    }
                }
            }
            return $result;
    }

	public function getEndereco($clioid){

		$sql = "select  
					clicep_com, clino_com, clicompl_com, clicidade_com, clirua_com, clibairro_com,
					clicidade_res, clirua_res, clibairro_res, cliuf_res, cliuf_com,
					clitipo, clicep_res, clino_res, clino_cep_res, clicompl_res, clifone_res, clifone_com, clifone2_com, clifone3_com, clifone4_com,
					clifone_cel, cliemail, cliemail_nfe, clicorrespondencia, cliobservacao, clipaisoid, clicomprovante_endereco,
					endpaisoid, endlogradouro, endcidade, endcep, endno_cep, endbairro, endno_numero, endcomplemento, enduf
				from clientes, endereco 
				where cliend_cobr = endoid
					and clioid = $clioid";

		$rs = pg_query($this->conn, $sql);
		
		if(pg_num_rows($rs) > 0 ){
			for($i = 0; $i < pg_num_rows($rs); $i++) {
				
				$result[$i]['clitipo'] = pg_fetch_result($rs, $i, 'clitipo');
				
				if($result[$i]['clitipo'] == 'F'){					
					
					$cep = pg_fetch_result($rs, $i, 'clicep_res');
					$cep1 = pg_fetch_result($rs, $i, 'clino_cep_res');
					
					$result[$i]['clicep_res'] = ($cep)?str_pad($cep,8,"0",STR_PAD_LEFT):str_pad($cep1,8,"0",STR_PAD_LEFT);
					$result[$i]['clino_res'] = pg_fetch_result($rs, $i, 'clino_res');
					$result[$i]['clicompl_res'] = pg_fetch_result($rs, $i, 'clicompl_res');
					$result[$i]['clicidade_res'] = pg_fetch_result($rs, $i, 'clicidade_res');
					$result[$i]['clirua_res'] = pg_fetch_result($rs, $i, 'clirua_res');
					$result[$i]['clibairro_res'] = pg_fetch_result($rs, $i, 'clibairro_res');
					$result[$i]['cliuf_res'] = pg_fetch_result($rs, $i, 'cliuf_res');
				}else{
					$result[$i]['clicep_res'] = pg_fetch_result($rs, $i, 'clicep_com');
					$result[$i]['clino_res'] = pg_fetch_result($rs, $i, 'clino_com');
					$result[$i]['clicompl_res'] = pg_fetch_result($rs, $i, 'clicompl_com');
					$result[$i]['clicidade_res'] = pg_fetch_result($rs, $i, 'clicidade_com');
					$result[$i]['clirua_res'] = pg_fetch_result($rs, $i, 'clirua_com');
					$result[$i]['clibairro_res'] = pg_fetch_result($rs, $i, 'clibairro_com');
					$result[$i]['cliuf_res'] = pg_fetch_result($rs, $i, 'cliuf_com');
				}
				

				$cep = pg_fetch_result($rs, $i, 'endcep');
				$cep1 = pg_fetch_result($rs, $i, 'endno_cep');

                $result[$i]['clicomprovante_endereco'] = pg_fetch_result($rs, $i, 'clicomprovante_endereco');
                $result[$i]['clifone_com'] = pg_fetch_result($rs, $i, 'clifone_com');
                $result[$i]['clifone2_com'] = pg_fetch_result($rs, $i, 'clifone2_com');
                $result[$i]['clifone3_com'] = pg_fetch_result($rs, $i, 'clifone3_com');
                $result[$i]['clifone4_com'] = pg_fetch_result($rs, $i, 'clifone4_com');
                $result[$i]['clipaisoid'] = pg_fetch_result($rs, $i, 'clipaisoid');
                $result[$i]['clifone_res'] = pg_fetch_result($rs, $i, 'clifone_res');
				$result[$i]['clifone_cel'] = pg_fetch_result($rs, $i, 'clifone_cel');
				$result[$i]['cliemail'] = pg_fetch_result($rs, $i, 'cliemail');
				$result[$i]['cliemail_nfe'] = pg_fetch_result($rs, $i, 'cliemail_nfe');
				$result[$i]['clicorrespondencia'] = pg_fetch_result($rs, $i, 'clicorrespondencia');
				$result[$i]['cliobservacao'] = pg_fetch_result($rs, $i, 'cliobservacao');
                $result[$i]['endcep'] = ($cep)?str_pad($cep,8,"0",STR_PAD_LEFT):str_pad($cep1,8,"0",STR_PAD_LEFT);
                $result[$i]['endpaisoid'] = pg_fetch_result($rs, $i, 'endpaisoid');
                $result[$i]['endlogradouro'] = pg_fetch_result($rs, $i, 'endlogradouro');
				$result[$i]['endcidade'] = pg_fetch_result($rs, $i, 'endcidade');
                $result[$i]['endno_numero'] = pg_fetch_result($rs, $i, 'endno_numero');
				$result[$i]['endbairro'] = pg_fetch_result($rs, $i, 'endbairro');
                $result[$i]['endcomplemento'] = pg_fetch_result($rs, $i, 'endcomplemento');
				$result[$i]['enduf'] = pg_fetch_result($rs, $i, 'enduf');
	    	}
    	}
		
    	return $result;
	}


	public function getTipoPessoa($clioid){
	
	    $sql = "SELECT 
	                clitipo
	            FROM 
	                clientes
	            WHERE 
	                clioid = $clioid";
	
	    $rs = pg_query($this->conn, $sql);
	
	    if(pg_num_rows($rs) > 0 ){
	
	        $result['clitipo'] = pg_fetch_result($rs, 0, 'clitipo');
	
	    }
	
	    return $result;
	}

    public function getEndFavoritos($clioid){

        $sql = "select
                    endcep, endno_numero, endcomplemento, endcidade, enduf, endbairro
                from cliente_endereco, endereco
                where endoid = cendendoid
                    and cendclioid = $clioid
                    and endcep != ''
                    ";

        $rs = pg_query($this->conn, $sql);
        $result = pg_fetch_all($rs);
        
        return $result;
    }


	public function getEnderecoById($endoid){

		$sql = "select
					endcep, endno_numero, endcomplemento, endcidade, enduf, endbairro, endlogradouro
				from endereco
				where endoid = $endoid
                    and endcep != ''
                    ";

		$rs = pg_query($this->conn, $sql);
		$result = pg_fetch_object($rs);
		
    	return $result;
	}

	public function getEnderecoEntrega($clioid){
	
		$result = array();
		$sql = "SELECT 
					endoid,
					endcep, 
					endno_numero, 
					endcomplemento, 
					endcidade, 
					enduf, 
					endbairro, 
					endlogradouro 
				FROM
					cliente_endereco
				JOIN 
					clientes on clioid = cendclioid
				JOIN
					endereco on endoid = cendendoid
				WHERE
					clioid = ".$clioid;
	
		$rs = pg_query($this->conn, $sql);
		if(pg_num_rows($rs) > 0){
			$result = pg_fetch_all($rs);
		}
		
		return $result;
	}
	
	public function excluirEnderecoEntrega($params){
		$resultado = array();
		try{
			pg_query($this->conn, "BEGIN");
			if($params['endoid']){
				
				$sql = "DELETE FROM cliente_endereco where cendendoid = ".$params['endoid'].";";
				$sql .= " DELETE FROM endereco where endoid = ".$params['endoid'].";";
				

				if (!pg_query($this->conn, $sql)) {
					throw new Exception ("Erro ao excluir endereço.");
				}
				$clihalteracao = "Endereço de entrega excluido";
				$this->setHistorico($params['clioid'], $clihalteracao, 'F');
				 
			}
			pg_query($this->conn, "COMMIT");
			$mensagem = 'Registro excluído com sucesso.';
			$status = 'sucesso';
			 
		} catch (Exception $e) {
			 
			pg_query($this->conn, "ROLLBACK");
			$mensagem = $e->getMessage();
			$status = 'erro';
				
		}
		 
		$resultado['mensagem'] = $mensagem;
		$resultado['status'] = $status;
		 
		return $resultado;
	}
	
    public function getClienteContatos($clioid){
        
        $sql = "
                SELECT
                    clicoid,
                    clicclioid,
                    clicnome,
                    clicfone,
                    clicusuoid,
                    clicclioid,
                    clicfone_array,
                    clicsetor
                FROM
                    cliente_contato 
                WHERE
                    clicclioid = ".$clioid." and clicexclusao is null
                ORDER BY
                    clicnome
            ";
        $rs = pg_query($this->conn, $sql);
         
        $result = array();
         
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['clicoid'] = pg_fetch_result($rs, $i, 'clicoid');
            $result[$i]['clicclioid'] = pg_fetch_result($rs, $i, 'clicclioid');
            $result[$i]['clicnome'] = utf8_encode(pg_fetch_result($rs, $i, 'clicnome'));
            $result[$i]['clicfone'] = pg_fetch_result($rs, $i, 'clicfone');
            $result[$i]['clicusuoid'] = pg_fetch_result($rs, $i, 'clicusuoid');
            $result[$i]['clicclioid'] = pg_fetch_result($rs, $i, 'clicclioid');
            $result[$i]['clicfone_array'] = pg_fetch_result($rs, $i, 'clicfone_array');
            $result[$i]['clicsetor'] = pg_fetch_result($rs, $i, 'clicsetor');
        }
         
        return $result;
    }
    
    public function getPrazoVencimento(){
    
    	$sql = "
    			SELECT 
    				cpvoid,
    				cpvprazo_dias
    			FROM
    				cliente_prazo_vencimento
            	";
    	$rs = pg_query($this->conn, $sql);
    	 
    	$result = array();
    	 
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$result[$i]['cpvoid'] = pg_fetch_result($rs, $i, 'cpvoid');
    		$result[$i]['cpvprazo_dias'] = pg_fetch_result($rs, $i, 'cpvprazo_dias');
    	}
    	 
    	return $result;
    }
    
    public function getClienteEnderecos($clioid){
    	
    	$sql = "SELECT 
                	endoid, 
                	endcep,
                	endlogradouro,
                	endno_numero,
                	endcomplemento,
                	endbairro,
                	endcidade,
                	enduf                	
                FROM
                	endereco
                INNER JOIN 
                	cliente_endereco ON cendendoid = endoid
                WHERE 
                	cendclioid = $clioid
            "; 
    	$rs = pg_query($this->conn, $sql);
    	
    	$result = array();
    	
    	if(pg_num_rows($rs) > 0) {
    		
    		$result = pg_fetch_all($rs);
    		
    	}
    	
        return $result;
    }
    
    public function getClienteOperacoes($clioid){
    	
    	$sql = "SELECT 
                    distinct (select count(*) from operacoes_endereco_ct where oectoctoid = oct.octoid) as total,
                    octclioid,
                	octoid,
                	octoprid,
                	octresponsavel,
                	octnome,
                	octtelefone,
                	octcnpj,
                	octinscr
                FROM
                	operacoes_ct oct
                LEFT JOIN operacoes_endereco_ct on oectoctoid = octoid
                LEFT JOIN endereco ON endoid = oectendoid 
                WHERE
                	octclioid = $clioid
                	AND octdt_exclusao IS NULL
            "; 
    	$rs = pg_query($this->conn, $sql);
    	
    	$result = array();
    	
    	if(pg_num_rows($rs) > 0) {
    		
    		$result = pg_fetch_all($rs);
    		
    	}
    	
        return $result;
    }
    
    public function getClienteOperacoesById($clioid, $octoid){
    	
    	$sql = "SELECT 
                    octclioid,
                	octoid,
                	octoprid,
                	octresponsavel,
                	octnome,
                	octtelefone,
                    to_char(octcnpj,'00\".\"000\".\"000\"/\"0000\"-\"00') as octcnpj,
                	octinscr,
                    oectendoid, endlogradouro, endno_numero, endbairro, endcidade, enduf, endcep
                FROM
                	operacoes_ct oct
                LEFT JOIN operacoes_endereco_ct on oectoctoid = octoid
                LEFT JOIN endereco ON endoid = oectendoid
                WHERE
                	octclioid = $clioid
                	AND octoid = $octoid
                	AND octdt_exclusao IS NULL
            ";
    	$rs = pg_query($this->conn, $sql);
    	
    	$result = array();
    	
    	if(pg_num_rows($rs) > 0) {
    		
    		$result = pg_fetch_all($rs);
            foreach($result as $row){
                $resultado[] = array_map('utf8_encode',$row);
            }
    	}
    	
        return $resultado;
    }

    public function getEnderecoClienteOperacoesById($octoid){
        
        $sql = "SELECT 
                    octclioid,
                    octoid,
                    octoprid,
                    octresponsavel,
                    octnome,
                    octtelefone,
                    octcnpj,
                    octinscr,
                    oectendoid, endlogradouro, endno_numero, endbairro, endcidade, enduf, endcep
                FROM
                    operacoes_ct oct
                INNER JOIN operacoes_endereco_ct on oectoctoid = octoid
                INNER JOIN endereco ON endoid = oectendoid
                WHERE octoid = $octoid
                    AND octdt_exclusao IS NULL
            ";
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        $resultado = array();
        if(pg_num_rows($rs) > 0) {
            
            $result = pg_fetch_all($rs);
            foreach($result as $row){
                $resultado[] = array_map('utf8_encode',$row);
            }
        }
        
        return $resultado;
    }
    
    public function validaIdOperacao($params){
        // valida se o ID já existe
        $sqlId = "SELECT 
                      octoprid
                  FROM
                      operacoes_ct
                  WHERE
                      octdt_exclusao is null
                      AND octoprid = '" . $params['octoprid'] . "'";

        if($params['octoid']!=''){
            $sqlId .= "AND octoid != '" . $params['octoid'] . "'";
        }

        if(pg_num_rows(pg_query($this->conn, $sqlId)) > 0){
            return utf8_encode("O ID informado já é utilizado por outra operação.");
        }

    }

    public function validaCnpjOperacao($params){
        
        $octcnpj = str_replace(array('-','.','/'), '', $params['octcnpj']);

        // valida se o ID já existe
        $sqlId = "SELECT 
                      octcnpj
                  FROM
                      operacoes_ct
                  WHERE
                      octdt_exclusao is null
                      AND octcnpj = '" . $octcnpj . "'
                      AND octclioid = '" . $params['clioid'] . "'
                ";

        if($params['octoid']!=''){
            $sqlId .= "AND octoid != '" . $params['octoid'] . "'";
        }
        
        if(pg_num_rows(pg_query($this->conn, $sqlId)) > 0){
            return utf8_encode("O CNPJ informado já é utilizado por outra operação.");
        }

    }

    public function setClienteOperacao($params = array()) {
    
        $resultado = array();
        try{
            pg_query($this->conn, "BEGIN");
             
            $clioid = $params['clioid'];
            $octoid = $params['octoid'];
            $octoprid = $params['octoprid'];
            $octresponsavel = $params['octresponsavel'];
            $octnome = $params['octnome'];
            $octtelefone = $params['octtelefone'];
            $octcnpj = $params['octcnpj'];
            $octinscr = $params['octinscr'];
            //$arrOctendoid = $params['octendoid'];
            if($params['enderecosSelecionados'] != ""){
                $enderecos = explode(",",$params['enderecosSelecionados']);
            }
            
            // valida se o ID já existe
            $sqlId = "SELECT 
                          octoprid
                      FROM
                          operacoes_ct
                      WHERE
                          octdt_exclusao is null
                          AND octoprid = $octoprid";
            
            if(pg_num_rows(pg_query($this->conn, $sqlId)) > 0){
                throw new Exception ("O ID informado já é utilizado por outra operação.");
            }    
            // valida se o CNJP já existe para este cliente
            $sqlId = "SELECT 
                            octoid
                        FROM
                          operacoes_ct
                        WHERE
                          octdt_exclusao is null
                          AND octcnpj = '$octcnpj'
                          AND octclioid = $clioid";
            
            if(pg_num_rows(pg_query($this->conn, $sqlId)) > 0){
                throw new Exception ("O CNPJ informado já é utilizado por outra operação.");
            }                     
            

            // foreach($arrOctendoid as $octendoid){
            //     if($octendoid == 'N'){
            //         $enderecos[] = $this->setEnderecoEntrega($params);
            //     }
            // }            
            
            $sql = "INSERT INTO operacoes_ct
                        (octoprid, octusuoid_cadastro, octclioid, octresponsavel, octnome, octtelefone, octcnpj, octinscr)
                    VALUES
                        ($octoprid, $this->cd_usuario, $clioid, '$octresponsavel', '$octnome', '$octtelefone', '$octcnpj', '$octinscr')
                    RETURNING octoid";
            
            if($rs = pg_query($this->conn, $sql)) {
                $octoid = pg_fetch_result($rs, 0, 'octoid');
            }else{
                throw new Exception ("Erro ao inserir operação.");
            }

            if(sizeof($enderecos) <= 0){
                throw new Exception("Necessário inserir endereços");
            }

            foreach($enderecos as $octendoid){
                if($octendoid == "") continue;

                $sql = "INSERT INTO 
                            operacoes_endereco_ct
                        (oectoctoid, oectendoid)
                        VALUES ($octoid, $octendoid)";
                
                $rs = pg_query($this->conn, $sql);
            
                if(pg_affected_rows($rs) == 0) {
                    throw new Exception ("Erro ao salvar a operação.");
                }
            }

            $clihalteracao = "Cadastro de nova operação: ".$octnome.".";
            $this->setHistorico($clioid, $clihalteracao, 'C');
        
            pg_query($this->conn, "COMMIT");
            $mensagem = 'Registro incluído com sucesso.';
            $status = 'sucesso';
                         
        } catch (Exception $e) {
             
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status = 'erro';
             
        }
        
        $resultado['mensagem'] = $mensagem;
        $resultado['status'] = $status;
   
        return $resultado;
    
    }
    
    public function excluirClienteOperacao($params = array()) {

        $resultado = array();
        try{
            pg_query($this->conn, "BEGIN");
             
            $clioid = $params['clioid'];
            $octoid = $params['octoid']; 
            $octnome = $params['octnome']; 
            
            $sql = "UPDATE
                    	operacoes_ct
                    SET
                    	octdt_exclusao = NOW(),
                    	octusuoid_exclusao = $this->cd_usuario
                    WHERE 
                    	octoid = $octoid
                    	AND octclioid = $clioid";

            $rs = pg_query($this->conn, $sql);
            
            if(pg_affected_rows($rs) == 0) {
                throw new Exception ("Erro ao excluir operação.");
            }

            $sql = "DELETE FROM 
                        operacoes_endereco_ct
                    WHERE 
                        oectoctoid = $octoid";
            $rs = pg_query($this->conn, $sql);
            
            if(pg_affected_rows($rs) == 0) {
                throw new Exception ("Erro ao excluir operação.");
            }
            
            $clihalteracao = "Exclusão de operação: ".$octnome.".";
            $this->setHistorico($clioid, $clihalteracao, 'A');
        
            pg_query($this->conn, "COMMIT");
            $mensagem = 'Operação excluída com sucesso.';
            $status = 'sucesso';
                         
        } catch (Exception $e) {
             
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status = 'erro';
             
        }
        
        $resultado['mensagem'] = $mensagem;
        $resultado['status'] = $status;
   
        return $resultado;
    
    }
    
    public function editarClienteOperacao($params = array()) {
    
        $resultado = array();
        try{
            pg_query($this->conn, "BEGIN");

            $clioid = $params['clioid'];
            $octoid = $params['octoid'];
            $octoprid = $params['octoprid'];
            $octresponsavel = $params['octresponsavel'];
            $octnome = $params['octnome'];
            $octtelefone = $params['octtelefone'];
            $octcnpj = $params['octcnpj'];
            $octinscr = $params['octinscr'];
            $arrOctendoid = $params['octendoid'];
            if($params['enderecosSelecionados'] != ""){
                $enderecos = explode(",",$params['enderecosSelecionados']);
            }
            
            // valida se o ID já existe
            $sqlId = "SELECT
                          octoprid
                      FROM
                          operacoes_ct
                      WHERE
                          octdt_exclusao is null
                          AND octoid <> $octoid
                          AND octoprid = $octoprid";
            
            if(pg_num_rows(pg_query($this->conn, $sqlId)) > 0){
                throw new Exception ("O ID informado já é utilizado por outra operação.");
            }
            // valida se o CNJP já existe para este cliente
            $sqlId = "SELECT 
                            octoid
                        FROM
                          operacoes_ct
                        WHERE
                          octdt_exclusao is null
                          AND octoid <> $octoid
                          AND octcnpj = '$octcnpj'
                          AND octclioid = $clioid";
            
            if(pg_num_rows(pg_query($this->conn, $sqlId)) > 0){
                throw new Exception ("O CNPJ informado já é utilizado por outra operação.");
            }       
            
            // foreach($arrOctendoid as $octendoid){
            //     if($octendoid == 'N'){
            //         $arrOctendoid[] = $this->setEnderecoEntrega($params);
            //     }
            // }
            
            $sql = "UPDATE
                    	operacoes_ct
                    SET
                        octoprid = $octoprid,
                        octresponsavel = '$octresponsavel',
                        octnome = '$octnome',
                        octtelefone = '$octtelefone',
                        octcnpj = '$octcnpj',
                        octinscr = '$octinscr',
                        octdt_alteracao = '".date('Y-m-d')."'
                    WHERE 
                    	octoid = $octoid
                    	AND octclioid = $clioid";
            
            $rs = pg_query($this->conn, $sql);
            
            if(pg_affected_rows($rs) == 0) {
                throw new Exception ("Erro ao salvar a operação.");
            }

            // EXCLUI ENDERECOS OPERAÇÕES
            $sql = "DELETE FROM 
                    operacoes_endereco_ct
                    WHERE 
                    oectoctoid = $octoid";
            
            $rs = pg_query($this->conn, $sql);
            
            // if(pg_affected_rows($rs) == 0) {
            //     throw new Exception ("Erro ao excluir operação.");
            // }
            if(sizeof($enderecos) <= 0){
                throw new Exception("Necessário inserir endereços");
            }

            // SALVA ENDERECOS OPERAÇÕES
            foreach($enderecos as $octendoid){
                if($octendoid == "") continue;

                $sql = "INSERT INTO 
                            operacoes_endereco_ct
                        (oectoctoid, oectendoid)
                        VALUES ($octoid, $octendoid)";
                
                $rs = pg_query($this->conn, $sql);
            
                if(pg_affected_rows($rs) == 0) {
                    throw new Exception ("Erro ao salvar endereço operação.");
                }
            }
            
            $clihalteracao = "Alteração de operação: ".$octnome.".";
            $this->setHistorico($clioid, $clihalteracao, 'A');
        
            pg_query($this->conn, "COMMIT");
            $mensagem = 'Registro alterado com sucesso.';
            $status = 'sucesso';
                         
        } catch (Exception $e) {
             
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status = 'erro';
             
        }
        
        $resultado['mensagem'] = $mensagem;
        $resultado['status'] = $status;
   
        return $resultado;
    
    }
    
    public function deleteClienteContato($clioid){
        try{
            pg_query($this->conn, "COMMIT");
            
            $sql = "delete from cliente_contato where clicclioid = ".$clioid;
            pg_query($this->conn, $sql);

            pg_query($this->conn, "COMMIT");
        } catch (Exception $e) {
             
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status = 'erro';
             
        }
    }

    public function setClienteContato($params = array()){
    	$resultado = array();
    	try{
    		pg_query($this->conn, "BEGIN");
    	
    		$clioid 	= $params['clioid'];
    		$clicnome 	= $params['clicnome'];
    		$cliccpf 	= (isset($params['cliccpf'])) ? $params['cliccpf'] : 'null';
    		$clicrg	 	= (isset($params['clicrg'])) ? $params['clicrg'] : 'null';
    		$clicemail 	= (isset($params['clicemail'])) ? $params['clicemail'] : '';
    		$clicfone 	= (isset($params['clicfone'])) ? $params['clicfone'] : '';
    		$clicsetor 	= $params['clicsetor'];

			$sql = "INSERT INTO 
						cliente_contato (clicnome, clicfone, cliccpf, clicrg, clicemail, clicusuoid, clicclioid, clicsetor) 
					VALUES (
						'".$clicnome."','',".$cliccpf.",'".$clicrg."','".$clicemail."',$this->cd_usuario, $clioid, '$clicsetor'
					) RETURNING clicoid";
			
			if($rs = pg_query($this->conn, $sql)) {
				$resultado['clicoid'] = pg_fetch_result($rs, 0, 'clicoid');
			}else{
				throw new Exception ("Erro ao inserir os dados.");
			}
			
			$clihalteracao = "Cadastro de contato inserido.";
			$this->setHistorico($clioid, $clihalteracao, 'A');
    			 
    		pg_query($this->conn, "COMMIT");
    		$mensagem = 'Registro incluído com sucesso.';
    		$status = 'sucesso';
    	
    	} catch (Exception $e) {
    	
    		pg_query($this->conn, "ROLLBACK");
    		$mensagem = $e->getMessage();
    		$status = 'erro';
    	
    	}
    	 
    	$resultado['mensagem'] = $mensagem;
    	$resultado['status'] = $status;
    	
    	return $resultado;
    	
    }
    
    
    public function setPeriodoEmissaoNF($params = array()){
    	$resultado = array();
    	try{
    		pg_query($this->conn, "BEGIN");
    	
    		$clioid = $params['clioid'];
    		$clicdt_inicial = $params['clicdt_inicial'];
    		$clicdt_final = $params['clicdt_final'];
    			
    		if($clioid){
    			$sql = "UPDATE 
    			            cliente_cobranca
                        SET 
    			            clicdt_inicial = $clicdt_inicial, 
    			            clicdt_final = $clicdt_final
                        WHERE 
    			            clicclioid = $clioid
                        AND 
    			            clicexclusao IS NULL";
    			
    			if (!pg_query($this->conn, $sql)) {
    				throw new Exception ("Erro ao atualizar o Período de Emissão da Nota Fiscal.");
    			}
    			
    			$clihalteracao = "Atualizado Período de Emissão da Nota Fiscal.";
    			$this->setHistorico($clioid, $clihalteracao, 'A');
    		}
    		
    		pg_query($this->conn, "COMMIT");
    		$mensagem = 'Registro atualizado com sucesso.';
    		$status = 'sucesso';
    	
    	} catch (Exception $e) {
    	
    		pg_query($this->conn, "ROLLBACK");
    		$mensagem = $e->getMessage();
    		$status = 'erro';
    	
    	}
    	 
    	$resultado['mensagem'] = $mensagem;
    	$resultado['status'] = $status;
    	
    	return $resultado;
    	
    }
    
	public function setClienteContatoFone($params = array()){
    	$resultado = array();
    	try{
    		pg_query($this->conn, "BEGIN");
    	
    		$clicoid  			= $params['clicoid'];
    		$clicfone_array 	= $this->to_pg_array($params['clicfone_array']);
    		/*$clictpfone_array 	= $this->to_pg_array($params['clictpfone_array']);*/

    		if($clicoid){
    			$sql = "UPDATE
    						cliente_contato 
    					SET
    						clicfone_array 		= '$clicfone_array' --,clictpfone_array 	= '$clictpfone_array'
    					WHERE
    						clicoid = '$clicoid'
    					";

    			if (!pg_query($this->conn, $sql)) {
    				throw new Exception ("Erro ao atualizar contato.");
    			}
    			
    		}
    		
    		pg_query($this->conn, "COMMIT");

    		$mensagem = 'Registro incluído com sucesso.';
    		$status = 'sucesso';
    	
    	} catch (Exception $e) {
    		pg_query($this->conn, "ROLLBACK");
    		$mensagem = $e->getMessage();
    		$status = 'erro';
    	
    	}
    	 
    	$resultado['mensagem'] = $mensagem;
    	$resultado['status'] = $status;
    	
    	return $resultado;
    	
    }
    
    
    public function getDadosFaturamentoCliente($clioid){
    	 
    	$sql = "
        		SELECT
    				clioid,
    				clicontato_os_nome,
    				clicontato_os_fone,
    				cliret_iss,
    				cliret_iss_perc,
    				cliret_piscofins,
    				cliret_pis_perc,
	    			cliret_cofins_perc,
	    			cliret_csll_perc,
    				cliret_irf_perc,
    				clicagencia, 
    				clicconta,
    	            clicdt_inicial,
    	            clicdt_final
    			FROM
    				clientes
    			INNER JOIN cliente_cobranca ON cliente_cobranca.clicclioid = clientes.clioid AND cliente_cobranca.clicexclusao IS NULL
    			WHERE
    				clioid = ".$clioid."
    			ORDER BY
    				clicontato_os_nome
            ";
    	$rs = pg_query($this->conn, $sql);
    
    	$result = array();
    
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    
    		$result['clioid'] = pg_fetch_result($rs, $i, 'clioid');
    		$result['clicontato_os_nome'] = utf8_encode(pg_fetch_result($rs, $i, 'clicontato_os_nome'));
    		$result['clicontato_os_fone'] = pg_fetch_result($rs, $i, 'clicontato_os_fone');
    		$result['cliret_iss'] = pg_fetch_result($rs, $i, 'cliret_iss');
    		$result['cliret_iss_perc'] = pg_fetch_result($rs, $i, 'cliret_iss_perc');
    		$result['cliret_piscofins'] = pg_fetch_result($rs, $i, 'cliret_piscofins');
    		$result['cliret_pis_perc'] = str_replace(".",",",pg_fetch_result($rs, $i, 'cliret_pis_perc'));
    		$result['cliret_cofins_perc'] = str_replace(".",",",pg_fetch_result($rs, $i, 'cliret_cofins_perc'));
    		$result['cliret_csll_perc'] = str_replace(".",",",pg_fetch_result($rs, $i, 'cliret_csll_perc'));
    		$result['cliret_irf_perc'] = str_replace(".",",",pg_fetch_result($rs, $i, 'cliret_irf_perc'));
    		$result['clicagencia'] = pg_fetch_result($rs, $i, 'clicagencia');
    		$result['clicconta'] = pg_fetch_result($rs, $i, 'clicconta');
    		$result['clicdt_inicial'] = pg_fetch_result($rs, $i, 'clicdt_inicial');
    		$result['clicdt_final'] = pg_fetch_result($rs, $i, 'clicdt_final');
    	}
    
    	return $result;
    }
    
    public function getBandeiraCartaoCredito(){
    	$sql = "
        		SELECT
    				forcoid, forcnome
    			FROM
    				forma_cobranca
    			WHERE
    				forcoid in(24,25)
            ";
    	$rs = pg_query($this->conn, $sql);
    	
    	$result = array();
    	
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$forcnome = explode(" ",pg_fetch_result($rs, $i, 'forcnome'));
    		$result[$i]['forcoid'] = pg_fetch_result($rs, $i, 'forcoid');
    		$result[$i]['forcnome'] = $forcnome[count($forcnome)-1];
    	}
    	
    	return $result;
    }
    
    public function excluirClienteContato($params = array()){
    	$resultado = array();
    	try{
    		pg_query($this->conn, "BEGIN");
    		if($params['clicoid']){
    			$sql = "
    					UPDATE 
    						cliente_contato
			           	SET 
    						clicexclusao = 'now()',
			               	clicusuexclusao = ".$this->cd_usuario."
			         	WHERE 
			               	clicoid = ".$params['clicoid'];
    			
    			if (!pg_query($this->conn, $sql)) {
    				throw new Exception ("Erro ao inserir contato.");
    			}
    			$clihalteracao = "Contato excluído";
    			$this->setHistorico($params['clioid'], $clihalteracao, 'A');
    	
    		}
    		pg_query($this->conn, "COMMIT");
    		$mensagem = 'Registro excluído com sucesso.';
    		$status = 'sucesso';
   
    	} catch (Exception $e) {
    	    	 
    		pg_query($this->conn, "ROLLBACK");
    	    $mensagem = $e->getMessage();
			$status = 'erro';
			
		}
    	
		$resultado['mensagem'] = $mensagem;
		$resultado['status'] = $status;
   
		return $resultado;
    	 
    }
    
    public function getParticularidadesContratos($clioid){
    	
    	$sql = "
    			SELECT 
    				connumero 
    			FROM 
    				contrato 
    			WHERE 
    				condt_exclusao is null 
    				and conclioid=$clioid
    			";
    	
    	$rs = pg_query($this->conn, $sql);
    	
    	$result = array();
    	
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$result[$i]['connumero'] = pg_fetch_result($rs, $i, 'connumero');
    	}
    	return $result;
    }
    
    public function setParticularidadesPerfil($params = array()) {
    	$resultado = array();
    	try{
    		pg_query($this->conn, "BEGIN");
    
    		$clipfdepoid = $_SESSION['usuario']['depoid'];
    		$clipfusuoid = $_SESSION['usuario']['oid'];
			$clipfdescricao = addslashes($params['cliparticularidade']);
			$clipfclioid = $params['clioid'];
			$clipfclipftoid = $params['tipo'];
			$clipfoid = $params['clipfoid'];
			$con_numero = $params['con_numero'];
			
			//se nao tiver clipfoid significa que é inserção, caso contrario é uma alteração
			if($clipfoid == ""){
				
				//insere um perfil para cada contrato cadastrado
				if(count($con_numero) > 0){
					foreach ( $con_numero as $clipfconnumero) {
						
						$sql = "select cliente_perfil_i('\"$clipfdescricao\"  \"$clipfconnumero\"  \"$clipfclioid\"  \"$clipfdepoid\"  	\"$clipfclipftoid\"  \"$clipfusuoid\"') as retorno";
						
						if (!pg_query($this->conn, $sql)) {
			    			throw new Exception ("Erro ao inserir particularidades.");
			    		}
					}
				}
				//se não for selecionado contrato, insere com o conoid null, significa que é válido para todos os contratos
				else{
					
					$clipfconnumero = "NULL";		
					$sql = "select cliente_perfil_i('\"$clipfdescricao\"  \"$clipfconnumero\"  \"$clipfclioid\"  \"$clipfdepoid\"  	\"$clipfclipftoid\"  \"$clipfusuoid\"') as retorno";
					
					if (!pg_query($this->conn, $sql)) {
			    		throw new Exception ("Erro ao inserir particularidades.");
			    	}
				}
			}else{
				
				$sql = "select cliente_perfil_u('\"$clipfdescricao\"  \"$clipfclioid\"  \"$clipfdepoid\"  \"$clipfclipftoid\"  \"$clipfusuoid\"',$clipfoid) as retorno";
				if (!pg_query($this->conn, $sql)) {
		    		throw new Exception ("Erro ao inserir particularidades.");
		    	}
			}
    		
			$clihalteracao = "Particularidade cadastrada.";
			$this->setHistorico($params['clioid'], $clihalteracao, 'A');
			
			pg_query($this->conn, "COMMIT");
    		$mensagem = 'Registro incluído com sucesso.';
    		$status = 'sucesso';
    		
    	} catch (Exception $e) {
    		
    		pg_query($this->conn, "ROLLBACK");
    		$mensagem = $e->getMessage();
    		$status = 'erro';
    		
    	}
    	
    	$resultado['mensagem'] = $mensagem;
    	$resultado['status'] = $status;
    
    	return $resultado;
    }
    
    public function excluirParticularidadePerfil($params = array()){
    	$resultado = array();
    	try{
    		
    		pg_query($this->conn, "BEGIN");
    		
    		$sql = "UPDATE cliente_perfil set clipfexclusao = now() where clipfoid in (".$params['clipfoid'].")";
    	
	    	if (!pg_query($this->conn, $sql)) {
	    		throw new Exception ("Erro ao inserir particularidades.");
	    	}
	    	
	    	$clihalteracao = "Particularidade excluida.";
	    	$this->setHistorico($params['clioid'], $clihalteracao, 'A');
	    	
	    	pg_query($this->conn, "COMMIT");
	    	$mensagem = 'Registro excluído com sucesso';
	    	$status = 'sucesso';
	    	
    	} catch (Exception $e) {
    	
    		pg_query($this->conn, "ROLLBACK");
    		$mensagem = $e->getMessage();
    		$status = 'erro';
    	
    	}
    	 
    	$resultado['mensagem'] = $mensagem;
    	$resultado['status'] = $status;
    	
    	return $resultado;
    	
    }
    
    public function setCobranca($params = array()){
    	$sqlIss = "";
    	$sqlPisCofins = "";
    	// altera somente se for departamento financeiro
    	if($params['depoid'] == 70){
    	    
	    	if($params['cliret_iss'] == 1){
	    		$cliret_iss_perc = str_replace(".","",$params['cliret_iss_perc']);
	    		$cliret_iss_perc = str_replace(",",".",$cliret_iss_perc);
	    		$sqlIss = 	"cliret_iss = true, cliret_iss_perc = ".$cliret_iss_perc." ,";		
	    	}else {
	    		$sqlIss = 	"cliret_iss = false, cliret_iss_perc = null ,";		    	    
	    	}
	    	
	    	if($params['cliret_piscofins'] == 1){
	    	    if($params['cliret_pis_perc']){
    	    		$cliret_pis_perc = str_replace(".","",$params['cliret_pis_perc']);
    	    		$cliret_pis_perc = str_replace(",",".",$cliret_pis_perc);
	    	    }else{
	    	        $cliret_pis_perc = 'null';
	    	    }
	    		
	    	    if($params['cliret_cofins_perc']){
    	    		$cliret_cofins_perc = str_replace(".","",$params['cliret_cofins_perc']);
    	    		$cliret_cofins_perc = str_replace(",",".",$cliret_cofins_perc);
	    	    }else{
	    	        $cliret_cofins_perc = 'null';
	    	    }
	    		
	    	    if($params['cliret_csll_perc']){
    	    		$cliret_csll_perc = str_replace(".","",$params['cliret_csll_perc']);
    	    		$cliret_csll_perc = str_replace(",",".",$cliret_csll_perc);
	    	    }else{
	    	        $cliret_csll_perc = 'null';
	    	    }
	    		
	    	    if($params['cliret_irf_perc']){
    	    		$cliret_irf_perc = str_replace(".","",$params['cliret_irf_perc']);
    	    		$cliret_irf_perc = str_replace(",",".",$cliret_irf_perc);
	    	    }else{
	    	        $cliret_irf_perc = 'null';
	    	    }
	    		
	    		$sqlPisCofins = "
	    			cliret_piscofins = true,
					cliret_pis_perc = ".$cliret_pis_perc.",
					cliret_cofins_perc = ".$cliret_cofins_perc.",
		    		cliret_csll_perc = ".$cliret_csll_perc.",
	    			cliret_irf_perc = ".$cliret_irf_perc.", ";
			}else{

			    $sqlPisCofins = "
	    			cliret_piscofins = false,
					cliret_pis_perc = null,
					cliret_cofins_perc = null,
		    		cliret_csll_perc = null,
	    			cliret_irf_perc = null, ";
			}
    	}
    	
    	$resultado = array();
    	try{
    		pg_query($this->conn, "BEGIN");
    		
    		$sqlCliente = "
    					UPDATE 
    						clientes
						SET ";
    		if($params['clifaturamento'] != ""){
    			$sqlCliente .= "clifaturamento = '".$params['clifaturamento']."',";
    		}
    		if($params['clifat_locacao'] != ""){
    			$sqlCliente .= "clifat_locacao = '".$params['clifat_locacao']."',";
    		} 
    		
    		$clivisualizacao_sasgc = 'false';
    		if($params['clivisualizacao_sasgc'] == 't'){
    			$clivisualizacao_sasgc = 'true';
    			$sqlCliente .= "clivisualizacao_sasgc = '$clivisualizacao_sasgc',";
    		}else{
    			$sqlCliente .= "clivisualizacao_sasgc = '$clivisualizacao_sasgc',";
    		}
    					 
    		$sqlCliente .= 
    					$sqlIss."
						".$sqlPisCofins."
						clidt_alteracao = NOW(),
						cliusuoid_alteracao = $this->cd_usuario
					WHERE 
    					clioid = ".$params['clioid'];

    		if (!pg_query($this->conn, $sqlCliente)) {
    			throw new Exception ("Erro ao inserir faturamento.");
    		}
    		
    		pg_query($this->conn, "COMMIT");
    		$mensagem = 'Registro incluído com sucesso';
    		$status = 'sucesso';
    
    	} catch (Exception $e) {
    		 
    		pg_query($this->conn, "ROLLBACK");
    		$mensagem = $e->getMessage();
    		$status = 'erro';
    		 
    	}
    
    	$resultado['mensagem'] = $mensagem;
    	$resultado['status'] = $status;
    	
    	return $resultado;
    	 
    }
    
    public function getClienteFaturamento($clioid){
    	$sql = "
			SELECT
				clifoid, clifeqcoid, clifvalor_monitoramento, clifvalor_renovacao
			FROM
				cliente_faturamento
			WHERE
    			clifusuexclusao is null and
				clifclioid=".$clioid." 
			ORDER BY clifoid DESC limit 1";
    	
    	$rs = pg_query($this->conn, $sql);
    	
    	$result = array();
    	
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$result['clifeqcoid'] = pg_fetch_result($rs, $i, 'clifeqcoid');
    		$result['valor_monitoramento'] = str_replace(".", ",", pg_fetch_result($rs, $i, 'clifvalor_monitoramento'));
    		$result['valor_renovacao'] = str_replace(".", ",", pg_fetch_result($rs, $i, 'clifvalor_renovacao'));
    	}
    	return $result;
    }
    
    public function getNotaFiscalSerie(){
    	
    	$sql = "
			SELECT 
				nfsoid, nfsserie, nfsdescricao 
			FROM 
				nota_fiscal_serie 
			WHERE 
				nfsserie = 'A'";
    	
    	$rs = pg_query($this->conn, $sql);
    	
    	$result = array();
    	
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$result[$i]['nfsoid'] = pg_fetch_result($rs, $i, 'nfsoid');
    		$result[$i]['nfsserie'] = str_replace(".", ",", pg_fetch_result($rs, $i, 'nfsserie'));
    		$result[$i]['nfsdescricao'] = str_replace(".", ",", pg_fetch_result($rs, $i, 'nfsdescricao'));
    	}
    	return $result;
    }
    
    public function getHistoricoFaturamento($clioid){
    	
    	$sql = "
    			SELECT 
    	            clifeqcoid, 
                	clifoid, 
                	(select eqcdescricao from equipamento_classe where eqcoid=clifeqcoid) as eqpto, 
                    clifvalor_monitoramento as clifvalor_monitoramento,
                	clifvalor_renovacao as clifvalor_renovacao, 
                	to_char(clifcadastro,'dd/mm/yyyy') as clifcadastro, 
                    	(select ds_login from usuarios where cd_usuario=clifusucadastro) as clifusucadastro, 
                    	clifeqcoid 
                FROM cliente_faturamento 
                WHERE clifexclusao IS NULL 
                AND clifclioid=".$clioid;
    	 
    	$rs = pg_query($this->conn, $sql);
    	 
    	$result = array();
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$result[$i]['clifeqcoid'] = pg_fetch_result($rs, $i, 'clifeqcoid');
    		$result[$i]['clifoid'] = pg_fetch_result($rs, $i, 'clifoid');
    		$result[$i]['clifcadastro'] = pg_fetch_result($rs, $i, 'clifcadastro');
    		$result[$i]['eqpto'] = pg_fetch_result($rs, $i, 'eqpto');
    		$result[$i]['clifvalor_monitoramento'] = pg_fetch_result($rs, $i, 'clifvalor_monitoramento');
    		$result[$i]['clifvalor_renovacao'] = pg_fetch_result($rs, $i, 'clifvalor_renovacao');
    		$result[$i]['clifusucadastro'] = pg_fetch_result($rs, $i, 'clifusucadastro');
    	}
    	return $result;
    }
    
    public function excluirObrigacao($params){
    	$resultado = array();
    	try{
    		pg_query($this->conn, "BEGIN");
    		 
    		$sql = "SELECT
	    				clioobroid
    				FROM
    					cliente_obrigacao_financeira
    				WHERE cliooid = " . $params['cliooid_deletar'];
    		
    		if (!$rs = pg_query($this->conn, $sql)) {
    			throw new Exception ("Erro ao excluir Obrigação.");
    		}
    		 
    		$result = array();
    		 
    		if(pg_num_rows($rs) > 0) {
    			 
    			$result = pg_fetch_all($rs);
    			 
    		}
    		
    		$sql = "UPDATE
    					cliente_obrigacao_financeira
					SET
                        cliodt_termino=NOW(),
    					cliomotivo_exclusao='".$params['cliomotivo_exclusao' . $params['cliooid_deletar']]."'
    				WHERE
						cliooid=".$params['cliooid_deletar'];

    		if (!pg_query($this->conn, $sql)) {
    			throw new Exception ("Erro ao excluir Obrigação.");
    		}
    		
    		$clihalteracao = "Exclusão de Obrigacao Financeira: "  . $result[0]['clioobroid'] . ", Motivo:" . $params['cliomotivo_exclusao' . $params['cliooid_deletar']] . ".";
    		$this->setHistorico($params['clioid'], $clihalteracao, 'B');
    		
    		pg_query($this->conn, "COMMIT");
    		$mensagem = 'Registro excluído com sucesso.';
    		$status = 'sucesso';
    		 
    	} catch (Exception $e) {
    		pg_query($this->conn, "ROLLBACK");
    		$mensagem = $e->getMessage();
    		$status = 'erro';
    	}
    	 
    	$resultado['mensagem'] = $mensagem;
    	$resultado['status'] = $status;
    	 
    	return $resultado;
    }
    
    public function getObrigacao($clioid){
    	$sql = "SELECT 
    				cliooid,
    				obrobrigacao,
    				cliovl_obrigacao,
    				to_char(cliodt_inicio,'DD/MM/YYYY') AS cliodt_inicio,
    				CASE WHEN cliono_periodo_mes = 12 THEN
    					'Anual'
    				WHEN cliono_periodo_mes = 6 THEN
    					'Semestral'
    				ELSE
    					'Mensal'
    				END AS cliono_periodo_mes,
    				CASE WHEN cliodemonstracao IS TRUE THEN 
    					'Demonstração'
    				WHEN cliocortesia IS TRUE THEN
    					'Cortesia'
    				ELSE
    					'Normal'
    				END AS faturamento, 
    				to_char(cliodemonst_validade,'DD/MM/YYYY') AS cliodemonst_validade,
    				sf1.msgdescricao as software_principal,
    				sf2.msgdescricao as software_secundario,
    				nm_usuario
    			FROM
    				cliente_obrigacao_financeira
    			INNER JOIN obrigacao_financeira ON obroid = clioobroid
    			LEFT JOIN modelo_software_gerenciador AS sf1 ON cliosoftware_principal = sf1.msgoid
    			LEFT JOIN modelo_software_gerenciador AS sf2 ON cliosoftware_secundario = sf2.msgoid
    			LEFT JOIN usuarios ON cliodemonst_aprov = cd_usuario
    			WHERE
    				clioclioid = '$clioid'
    				AND cliomotivo_exclusao IS NULL";
    	
    	$rs = pg_query($this->conn, $sql);
    	
    	$result = array();
    	
    	if(pg_num_rows($rs) > 0) {
    		 
    		$result = pg_fetch_all($rs);
    		 
    	}
    	
    	return $result;
    }
    
    public function setObrigacao($params = array()){
    	$resultado = array();
    	try{
    		
    		pg_query($this->conn, "BEGIN");
    		
	    	if ($params['cliovl_obrigacao'] != "" ){
	    		$cliovl_obrigacao = str_replace(".", "", $params['cliovl_obrigacao']);
	    		$cliovl_obrigacao = str_replace(",", ".", $cliovl_obrigacao);
	    		 
	    		if($params['clioobroid'] == 50){
	    			if($params['cliosoftware_principal'] == ""){
	    				throw new Exception ("O campo Software Principal deve ser preenchido.");
	    			}
	    			if($params['cliosoftware_secundario'] == ""){
	    				throw new Exception ("O campo Software Secundário deve ser preenchido.");
	    			}
	    		}else{
	    			$params['cliosoftware_principal'] 	= 'null';
	    			$params['cliosoftware_secundario'] 	= 'null';
	    		}
	    		
    			$params['cliodemonstracao'] = 'false';
    			$params['cliocortesia'] = 'false';
	    		if($params['cliofaturamento'] == "cortesia" || $params['cliofaturamento'] == "demonstracao"){
	    			
	    			if($params['cliodemonst_aprov'] == ""){
	    				throw new Exception ("O campo Autorizado Por deve ser preenchido.");
	    			}
	    			
	    			if($params['cliodemonst_validade'] == "" && $params['cliofaturamento'] == "demonstracao"){
	    				throw new Exception ("O campo Valido Até deve ser preenchido.");
	    			}elseif($params['cliofaturamento'] == "demonstracao"){
		    			$validade = "'" . $params['cliodemonst_validade'] . "'";
	    			}else{
		    			$validade = 'null';
	    			}
	    			
	    			if($params['cliofaturamento'] == "cortesia"){
	    				$params['cliocortesia'] = 'true';
	    				$params['cliodemonstracao'] = 'false';
	    			}else{
	    				$params['cliodemonstracao'] = 'true';
	    				$params['cliocortesia'] = 'false';
	    			}
	    			
	    		}else{
	    			$params['cliodemonst_aprov'] = 'null';
	    			$validade = 'null';
	    		}
	    		
	    		$query = "INSERT INTO 
	    					cliente_obrigacao_financeira 
    					(
	    				clioclioid, 
	    				clioobroid, 
	    				cliovl_obrigacao, 
	    				cliodt_inicio, 
	    				cliono_periodo_mes,
	    				cliodemonstracao,
	    				cliodemonst_validade,
	    				cliodemonst_aprov,
	    				cliocortesia,
	    				cliosoftware_principal,
	    				cliosoftware_secundario
	    				)
							VALUES 
	    				(
	    				".$params['clioid'].", 
	    				".$params['clioobroid'].", 
	    				$cliovl_obrigacao, 
	    				'".$params['cliodt_inicio']."', 
	    				".$params['cliono_periodo_mes'].",
	    				".$params['cliodemonstracao'].",
	    				".$validade.",
	    				".$params['cliodemonst_aprov'].",
	    				".$params['cliocortesia'].",
	    				".$params['cliosoftware_principal'].",
	    				".$params['cliosoftware_secundario']."
						)";

	    		if (!pg_query($this->conn, $query)) {
	    			throw new Exception ("Erro ao inserir faturamento.");
	    		}
	    		
	    		$historico = $params['clioobroid'];
	    		
	    		$clihalteracao = "Cadastro de Obrigacao Financeira: " . $historico . ".";
	    		$this->setHistorico($params['clioid'], $clihalteracao, 'B');
	    		
	    	} else{
	    		throw new Exception ("O valor da obrigação deve ser preenchida.");
			}
			
			pg_query($this->conn, "COMMIT");
			$mensagem = 'Registro incluído com sucesso.';
			$status = 'sucesso';
	    	
    	} catch (Exception $e) {
    		 
    		pg_query($this->conn, "ROLLBACK");
    		$mensagem = $e->getMessage();
    		$status = 'erro';
    		 
    	}
    
    	$resultado['mensagem'] = $mensagem;
    	$resultado['status'] = $status;
    	 
    	return $resultado;
    }
    
    public function setFaturamento($params = array()){
    	$resultado = array();
    	try{
    		
    		pg_query($this->conn, "BEGIN");
    		
	    	if ($params['valor_monitoramento'] != "" && $params['valor_renovacao'] != ""){
	    		$valor_monitoramento = str_replace(".", "", $params['valor_monitoramento']);
	    		$valor_monitoramento = str_replace(",", ".", $valor_monitoramento);
	    		$valor_renovacao = str_replace(".", "", $params['valor_renovacao']);
	    		$valor_renovacao = str_replace(",", ".", $valor_renovacao);
	    		 
	    		$query = "insert into cliente_faturamento (clifeqcoid, clifusucadastro, clifvalor_monitoramento, clifvalor_renovacao, clifclioid)
	    		            		values (".$params['eqcoid_fat'].", $this->cd_usuario, $valor_monitoramento, $valor_renovacao,".$params['clioid'].")";
	    		 
	    		if (!pg_query($this->conn, $query)) {
	    			throw new Exception ("Erro ao inserir faturamento.");
	    		}
	    	
	    		// atualiza valor do monitoramento dos contratos do cliente com a classe informada
	    		$query = "
	    		            		UPDATE
										contrato_obrigacao_financeira
	    		            		SET
	    		            			cofvl_obrigacao=".$valor_monitoramento."
	    		            		WHERE
	    		            			cofdt_termino is null and cofobroid=1 and
	    		            			cofconoid in (
	    		            						SELECT
	    		            							connumero
	    		            						FROM
	    		            							contrato
	    		            						WHERE
	    		            							conclioid=".$params['clioid']." and
	    		            							condt_exclusao is null and coneqcoid=".$params['eqcoid_fat']."
	    		            						) ";
	    		if (!pg_query($this->conn, $query)) {
	    			throw new Exception ("Erro ao inserir faturamento.");
	    		}
	    	
	    		// atualiza valor da renovação dos contratos do cliente com a classe informada
	    		$query = "
	    		UPDATE
	    		contrato_obrigacao_financeira
	    		SET
	    		cofvl_obrigacao=$valor_renovacao
	    		WHERE
	    		cofdt_termino is null and
	    		(cofobroid=20 or cofobroid=2) and
	    		cofconoid in (
	    		SELECT
	    		connumero
	    		FROM
	    		contrato
	    		WHERE
	    		conclioid=".$params['clioid']." and
	    		condt_exclusao is null and
	    		coneqcoid=".$params['eqcoid_fat']."
	    		) ";
	    		if (!pg_query($this->conn, $query)) {
	    			throw new Exception ("Erro ao inserir faturamento.");
	    		}
	    	} else{
	    		throw new Exception ("O valor do monitoramento e renovação devem ser preenchidos.");
			}
			
			pg_query($this->conn, "COMMIT");
			$mensagem = 'Registro incluído com sucesso.';
			$status = 'sucesso';
	    	
    	} catch (Exception $e) {
    		 
    		pg_query($this->conn, "ROLLBACK");
    		$mensagem = $e->getMessage();
    		$status = 'erro';
    		 
    	}
    
    	$resultado['mensagem'] = $mensagem;
    	$resultado['status'] = $status;
    	 
    	return $resultado;
    }
    
    public function excluirFaturamento($params){
    	
    	$resultado = array();
    	try{
    		pg_query($this->conn, "BEGIN");
    	
    		$sql = "UPDATE
    					cliente_faturamento
					SET
    					clifexclusao=now(),clifusuexclusao=".$this->cd_usuario."
    				WHERE
						clifoid=".$params['clifoid'];
    	
    		if (!pg_query($this->conn, $sql)) {
    			throw new Exception ("Erro ao excluir faturamento.");
    		}
    	
    		pg_query($this->conn, "COMMIT");
    		$mensagem = 'Registro excluído com sucesso.';
    		$status = 'sucesso';
    	
    	} catch (Exception $e) {
    		pg_query($this->conn, "ROLLBACK");
    		$mensagem = $e->getMessage();
    		$status = 'erro';
    	}
    	
    	$resultado['mensagem'] = $mensagem;
    	$resultado['status'] = $status;
    	
    	return $resultado;
    }
    
    public function getParticularidadesTipo(){
    	$sql = "
    			SELECT 
    				clipftoid,
    				clipftdescricao 
    			FROM 
    				cliente_perfil_tipo 
    			WHERE 
    				clipftexclusao is null 
    			ORDER BY 
    				clipftdescricao
    			";
    	
    	$rs = pg_query($this->conn, $sql);
    	 
    	$result = array();
    	 
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$clipftdescricao = pg_fetch_result($rs, $i, 'clipftdescricao');
    		$clipftoid = pg_fetch_result($rs, $i, 'clipftoid');
    		$result[$clipftoid] = $clipftdescricao;
    	}
    	return $result;
    	
    }
    
    public function getParticularidadesPerfil($clioid){
    	$sql = "
    			SELECT 
					clipfoid,
	    			to_char(clipfcadastro,'dd/mm/YYYY') as cadastro,
	    			clipfdescricao,
    				clipftdescricao as tipo,
					CASE when clipfconnumero is null THEN 'Todos' ELSE clipfconnumero::text END as connumero 
				FROM 
    				cliente_perfil 
				JOIN 
    				cliente_perfil_tipo on clipftoid = clipfclipftoid 
    			WHERE
    				clipfclioid = ".$clioid." 
				AND 
    				clipfexclusao is null 
    			ORDER BY
    				clipfcadastro
    			";
    	
    	$rs = pg_query($this->conn, $sql);
    	 
    	$result = array();
    	 
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$clipfdescricao = pg_fetch_result($rs, $i, 'clipfdescricao');
    		$tipo = pg_fetch_result($rs, $i, 'tipo');
    		$chave = trim($tipo.$clipfdescricao);
    		$result[$chave]['clipfoid'] = pg_fetch_result($rs, $i, 'clipfoid');
    		$result[$chave]['cadastro'] = pg_fetch_result($rs, $i, 'cadastro');
    		$result[$chave]['clipfdescricao'] = $clipfdescricao;
    		$result[$chave]['tipo'] = $tipo;
    		if(pg_fetch_result($rs, $i, 'connumero') == "Todos"){
    			$result[$chave]['connumero'] = pg_fetch_result($rs, $i, 'connumero')." ";
    		}else{
    			$result[$chave]['connumero'] .= pg_fetch_result($rs, $i, 'connumero')." ";
    		}
    		$result[$chave]['clipfoids'][] = pg_fetch_result($rs, $i, 'clipfoid');
    	}
    	
    	return $result;
    }
    
    public function getGerenciadoras(){
    	$sql = "
    			SELECT 
    				geroid, 
    				gernome 
    			FROM 
    				gerenciadora 
    			WHERE 
    				gerexclusao is null 
    			ORDER BY 
    				gernome
    			";
    	$rs = pg_query($this->conn, $sql);
    	
    	$result = array();
    	
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$result[$i]['geroid'] = pg_fetch_result($rs, $i, 'geroid');
    		$result[$i]['gernome'] = pg_fetch_result($rs, $i, 'gernome');
    	}
    	 
    	return $result;
    }
    
    public function getClienteGerenciadora($clioid){
    	$sql = "
    			SELECT
    				cligeroid,
    				clirede_ip,
    				clirede_porta,
    				clitpo_integr
    			FROM
    				clientes
    			WHERE
    				clioid = ".$clioid."
    			";
    	$rs = pg_query($this->conn, $sql);
    	 
    	$result = array();
    	 
    	if(pg_num_rows($rs) > 0 ){
    		while ($arrRs = pg_fetch_array($rs)){
    			$result = $arrRs;
    		}
    	}
    	return $result;
    }
    
    public function getChat($clioid){
    	$sql = "
    			SELECT
    				clivisualizacao_sasgc
    			FROM
    				clientes
    			WHERE
    				clioid = ".$clioid."
    			";
    	$rs = pg_query($this->conn, $sql);
    	 
    	$result = array();
    	 
    	if(pg_num_rows($rs) > 0 ){
    		while ($arrRs = pg_fetch_array($rs)){
    			$result = $arrRs;
    		}
    	}
    	return $result;
    }
    
    public function getClasseEquipamento(){
    	
    	$sql = "
    			SELECT
    				eqcoid,
    				eqcdescricao
    			FROM
    				equipamento_classe
    			ORDER BY
    				eqcdescricao
    			";
    	
    	$rs = pg_query($this->conn, $sql);
    	 
    	$result = array();
    	 
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$result[$i]['eqcoid'] = pg_fetch_result($rs, $i, 'eqcoid');
    		$result[$i]['eqcdescricao'] = pg_fetch_result($rs, $i, 'eqcdescricao');
    	}
    	
    	return $result;
    }

    public function getBancos($orderby, $clause){
    	$sql = "
    			SELECT
    				bancodigo,
    				bannome, 
    				banfaturamento
    			FROM
    				banco
    			INNER JOIN 
    				forma_cobranca ON forccfbbanco = bancodigo
    			WHERE 
    				forcdebito_conta is true
    			";
    	
    	if($clause != null){
    		//$sql .= "WHERE ".$orderby." = ".$clause;
    	}
    	
    	$sql .= " ORDER BY ".$orderby;
    	
    	$rs = pg_query($this->conn, $sql);
    
    	$result = array();
    
    	for($i = 0; $i < pg_num_rows($rs); $i++) {
    		$result[$i]['bancodigo'] = pg_fetch_result($rs, $i, 'bancodigo');
    		$result[$i]['bannome'] = pg_fetch_result($rs, $i, 'bannome');
    		$result[$i]['banfaturamento'] = pg_fetch_result($rs, $i, 'banfaturamento');
    	}
    	 
    	return $result;
    }
    public function getPais() {
        $sql = "SELECT 
                    paisoid, paisnome
                FROM 
                    paises
                WHERE 
                    paisexclusao is null
                ORDER BY
                    paisoid;
                ";
        
        $rs = pg_query($this->conn, $sql);
         
        $result = array();
         
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[pg_fetch_result($rs, $i, 'paisoid')] = pg_fetch_result($rs, $i, 'paisnome');
        }
        
        return $result;        
    }

    public function getEstado($paisoid = 1) {
        $sql = "SELECT
                    estoid, estuf 
                FROM 
                    estado
                WHERE 
                    estpaisoid = '$paisoid' 
                ORDER BY
                    estnome
                ";
        
        $rs = pg_query($this->conn, $sql);
         
        $result = array();
         
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[pg_fetch_result($rs, $i, 'estoid')] = pg_fetch_result($rs, $i, 'estuf');
        }
        
        return $result;        
    }
    public function getCidade($estoid) {
        $sql = "SELECT 
                    clcnome, clcoid
                FROM 
                    correios_localidades INNER JOIN estado on (estoid = clcestoid) 
                WHERE 
                    clcuf_sg='$estoid'
                ORDER BY 
                    clcnome
                ";
        
        $rs = pg_query($this->conn, $sql);
         
        $result = array();
         
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[pg_fetch_result($rs, $i, 'clcoid')] = pg_fetch_result($rs, $i, 'clcnome');
        }
        
        return $result;        
    }
    public function getBairro($clcoid) {
        $sql = "SELECT 
                    cbanome
                FROM 
                    correios_bairros
                WHERE 
                    cbaclcoid='$clcoid'
                ";
        
        $rs = pg_query($this->conn, $sql);
         
        $result = array();
         
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[] = pg_fetch_result($rs, $i, 'cbanome');
        }
        
        return $result;        
    }
    public function getByCep($cep) {
        $sql = "SELECT 
                    clgtipo, clcestoid, clguf_sg, cbanome, clgnome, clcnome 
                FROM 
                    correios_logradouros, correios_localidades,correios_bairros 
                WHERE 
                    clgclcoid=clcoid 
                    AND clgcep='$cep' 
                    AND (clgcbaoid_ini = cbaoid or clgcbaoid_fim = cbaoid) 
                ORDER BY 
                    cbanome
                ";

        $rs = pg_query($this->conn, $sql);
         
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[] = array(
                'clgtipo' => pg_fetch_result($rs, $i, 'clgtipo'),
                'clguf_sg' => pg_fetch_result($rs, $i, 'clguf_sg'),
                'clcestoid' => pg_fetch_result($rs, $i, 'clcestoid'),
                'cbanome' => pg_fetch_result($rs, $i, 'cbanome'),
                'clgnome' => pg_fetch_result($rs, $i, 'clgnome'),
                'clcnome' => pg_fetch_result($rs, $i, 'clcnome')
                );
        }
        return $result;      
    }

    public function getByEndereco($endereco) {
        $sql = "SELECT 
                    CONCAT(clgtipo, ' ', clgnome, ' - ', clguf_sg, ' - ' ,  clgcep) as descricao,
                    clgtipo, clcestoid, clguf_sg, cbanome, clgnome, clcnome , clgcep
                FROM 
                    correios_logradouros, correios_localidades,correios_bairros 
                WHERE 
                    clgclcoid=clcoid 
                    AND ((clgtipo ||  ' ' || clgnome) ilike '%$endereco%')
                    AND (clgcbaoid_ini = cbaoid or clgcbaoid_fim = cbaoid) 
                ORDER BY 
                    cbanome";
        
        $rs = pg_query($this->conn, $sql);
         
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[] = array(
                'clgtipo' => pg_fetch_result($rs, $i, 'clgtipo'),
                'clguf_sg' => pg_fetch_result($rs, $i, 'clguf_sg'),
                'clcestoid' => pg_fetch_result($rs, $i, 'clcestoid'),
                'cbanome' => pg_fetch_result($rs, $i, 'cbanome'),
                'clgnome' => pg_fetch_result($rs, $i, 'clgnome'),
                'clcnome' => pg_fetch_result($rs, $i, 'clcnome'),
                'clgcep' => pg_fetch_result($rs, $i, 'clgcep'),
                'descricao' => pg_fetch_result($rs, $i, 'descricao')
                );
        }
        return $result;
    }
    
    public function setPrincipal($dados) {
        if($dados['indicadorid'] == "" || isset($dados['indicadorid'])){
            $dados['indicadorid'] = 'null';
        }

    	$resultado = array();
    	try{
    	    
    		pg_query($this->conn, "BEGIN");    		

    		if($dados['clioid']){
    			//  UPDATE
    			
    			if($dados['clitipo'] == 'F') {
    				$sql = "UPDATE
    							clientes
    						SET
    				            cliusuoid_alteracao = ".$this->cd_usuario.",
    							clitipo = '".$dados['clitipo']."', 
    							cliclicloid = ".($dados['clicloid']?$dados['clicloid']:'null').", 
        						clino_cpf = '".$dados['clino_cpf']."', 
        						clinome = '".$dados['clinome']."', 
        						clirg = '".$dados['clirg']."', 
        						cliemissor_rg = '".$dados['cliemissor_rg']."', 
        						cliuf_emiss = '".$dados['cliuf_emiss']."', 
        						clidt_emissao_rg = ".($dados['clidt_emissao_rg']?"'".$dados['clidt_emissao_rg']."'":'null').", 
        						clidt_nascimento = ".($dados['clidt_nascimento']?"'".$dados['clidt_nascimento']."'":'null').", 
        						clinaturalidade = '".$dados['clinaturalidade']."', 
        						clipai = '".$dados['clipai']."', 
        						climae = '".$dados['climae']."', 
        						clisexo = '".$dados['clisexo']."', 
        						cliestado_civil = '".$dados['cliestado_civil']."',
                                cliclioid_indicacao = ".$dados['indicadorid']."
    				        WHERE
    				            clioid = ".$dados['clioid'];
    			}else{
    				$sql = "UPDATE
    							clientes
						    SET
    				            cliusuoid_alteracao = ".$this->cd_usuario.",
    							clitipo = '".$dados['clitipo']."', 
        				        cliclicloid = ".($dados['clicloid']?$dados['clicloid']:'null').", 
        				        clino_cgc = '".$dados['clino_cgc']."', 
        				        clinome = '".$dados['clinomePJ']."', 
        				        clireg_simples = '".$dados['clireg_simples']."', 
        				        cliinscr = '".$dados['cliinscr']."', 
        				        cliuf_inscr = '".$dados['cliuf_inscr']."', 
        				        clicnae = '".$dados['clicnae']."', 
        				        cliinscr_municipal = '".$dados['cliinscr_municipal']."', 
        				        clidt_fundacao = ".($dados['clidt_fundacao']?"'".$dados['clidt_fundacao']."'":'null').",
                                cliclioid_indicacao = ".$dados['indicadorid']."
    				        WHERE
    				            clioid = ".$dados['clioid'];
    			}

    			$rs = pg_query($this->conn, $sql);
    			if(pg_affected_rows($rs) > 0) {
    			    $resultado['clioid'] = $dados['clioid']; 
    			}else{
    			    throw new Exception ("Erro ao atualizar os dados.");    			    
    			}
    			$clihalteracao = "Cadastro de usuário atualizado.";
    			$this->setHistorico($resultado['clioid'], $clihalteracao, 'A');
    			$mensagem = 'Registro alterado com sucesso.';
    			
    		}else{
    			// INSERT
    			
    			if($dados['clitipo'] == 'F') {
    				$sql = "INSERT INTO 
	    						clientes
	    						(cliusuoid, clitipo, cliclicloid, clino_cpf, clinome, clirg, cliemissor_rg, cliuf_emiss, 
                                 clidt_emissao_rg, clidt_nascimento, clinaturalidade, clipai, climae, clisexo, cliestado_civil, cliclioid_indicacao)
	    					VALUES
	    						(".$this->cd_usuario.",
    				             '".$dados['clitipo']."', 
	    						 ".($dados['clicloid']?$dados['clicloid']:'null').",
	    						 '".$dados['clino_cpf']."', 
	    						 '".$dados['clinome']."', 
	    						 '".$dados['clirg']."', 
	    						 '".$dados['cliemissor_rg']."', 
	    						 '".$dados['cliuf_emiss']."', 
	    						 ".($dados['clidt_emissao_rg']?"'".$dados['clidt_emissao_rg']."'":'null').", 
	    						 ".($dados['clidt_nascimento']?"'".$dados['clidt_nascimento']."'":'null').", 
	    						 '".$dados['clinaturalidade']."', 
	    						 '".$dados['clipai']."', 
	    						 '".$dados['climae']."', 
	    						 '".$dados['clisexo']."', 
	    						 '".$dados['cliestado_civil']."',
                                 ".$dados['indicadorid']." )
	    					RETURNING clioid";
    			}else{
    			    
    				$sql = "INSERT INTO 
	    						clientes
	    						(cliusuoid, clitipo, cliclicloid, clino_cgc, clinome, clireg_simples, cliinscr, cliuf_inscr, cliinscr_municipal, clidt_fundacao, cliclioid_indicacao, clicnae)
	    					VALUES
	    						(".$this->cd_usuario.",
    				             '".$dados['clitipo']."', 
	    						 ".($dados['clicloid']?$dados['clicloid']:'null').",
	    						 '".$dados['clino_cgc']."', 
	    						 '".$dados['clinomePJ']."',
	    						 '".$dados['clireg_simples']."', 
	    						 '".$dados['cliinscr']."',
	    						 '".$dados['cliuf_inscr']."', 
	    						 '".$dados['cliinscr_municipal']."', 
	    						 ".($dados['clidt_fundacao']?"'".$dados['clidt_fundacao']."'":'null').",
                                 ".$dados['indicadorid'].",
                                 '".$dados['clicnae']."'
                                  )
	    					RETURNING clioid";
    				
    			}
    			
    			if($rs = pg_query($this->conn, $sql)) {
    			    $resultado['clioid'] = pg_fetch_result($rs, 0, 'clioid'); 
    			}else{
    			    throw new Exception ("Erro ao inserir os dados.");    			    
    			}

    			$clihalteracao = "Usuário cadastrado.";
    			$this->setHistorico($resultado['clioid'], $clihalteracao, 'C');
    			$mensagem = 'Registro incluído com sucesso.';
    			
    		}
    		
    		pg_query($this->conn, "COMMIT");
    		$status = 'sucesso';
    		
    	} catch (Exception $e) {
    		pg_query($this->conn, "ROLLBACK");
    		$mensagem = $e->getMessage();
    		$status = 'erro';
    	}
    	$resultado['mensagem'] = $mensagem;
    	$resultado['status'] = $status;
    	
    	return $resultado;
    	
    }


    public function setHistorico($clioid, $clihalteracao, $tipo) {
        $resultado = array();
        try{
            pg_query($this->conn, "BEGIN");

            if ($clioid == '') {
                throw new Exception ("ID do cliente não pode ser nulo.");
            }
            $sql = "SELECT 
                        cliente_historico_i ('$clioid', '$this->cd_usuario','$clihalteracao','$tipo','','');";
            
            if (!pg_query($this->conn, $sql)) {
                        throw new Exception ("Erro ao inserir histórico.");
            }
        
            pg_query($this->conn, "COMMIT");
            $mensagem = 'Histórico incluído com sucesso.';
            $status = 'sucesso';
        } catch (Exception $e) {
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status = 'erro';
        }
        $resultado['mensagem'] = $mensagem;
        $resultado['status'] = $status;

        return $resultado;
    }


    public function setSegmentacao($clioid, $clstsgoid= array()) {

        $resultado = array();
        try{
            pg_query($this->conn, "BEGIN");

                    if ($clioid == '') {
                            throw new Exception ("ID do cliente não pode ser nulo.");
                        }
                    $limparSegmentacao = "DELETE FROM 
                                                cliente_segmentacao 
                                          WHERE 
                                                clsclioid = ".$clioid;

                    if (!pg_query($this->conn, $limparSegmentacao)) {
                        throw new Exception ("Erro ao limpar segmentação.");
                    }
                    
                    $sql_add = array();

                    foreach ($clstsgoid as $chave) {
                        if ($chave != 0) {
                            $sql_add[] = "(".$clioid.", ".$chave.")";
                        }
                    }
                        
                    if(!empty($sql_add)) {
                        $criarSegmentacao = "INSERT INTO cliente_segmentacao 
                                                    (clsclioid, clstsgoid)
                                            VALUES  ".implode(',', $sql_add);

                        if (!pg_query($this->conn, $criarSegmentacao)) {
                            throw new Exception ("Erro ao criar segmentação.");
                        }
						
						$historico = 'Adicionada segmentação';
						$this->setHistorico($clioid, $historico, 'A');
                    }

                    pg_query($this->conn, "COMMIT");
                    $mensagem = 'Registro alterado com sucesso.';
                    $status = 'sucesso';
        } catch (Exception $e) {
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status = 'erro';
        }
        $resultado['mensagem'] = $mensagem;
        $resultado['status'] = $status;

        return $resultado;
    }
    
    public function setEndereco($params) {

        $resultado = array();
        try{
            pg_query($this->conn, "BEGIN");

                    if ($params['clioid'] == '') {
                        throw new Exception ("ID do cliente não pode ser nulo.");
                    }

                        //Busca os dados já cadastrados do cliente
                        $sql="SELECT clitipo, 
                                        CASE WHEN clitipo='F' THEN ( COALESCE(clirua_res,'')||', '||COALESCE(clino_res,0)||', '||COALESCE(clicompl_res,'')||', '||COALESCE(clibairro_res,'')||', '||COALESCE(clicep_res,'')||', '||COALESCE(clicidade_res,'')||', '||COALESCE(cliuf_res,''))
                                             WHEN clitipo='J' THEN ( COALESCE(clirua_com,'')||', '||COALESCE(clino_com,0)||', '||COALESCE(clicompl_com,'')||', '||COALESCE(clibairro_com,'')||', '||COALESCE(clicep_com,'')||', '||COALESCE(clicidade_com,'')||', '||COALESCE(cliuf_com,''))
                                             ELSE ''
                                             END AS cliente_end_ant,
                                        clicorrespondencia, cliobservacao, cliend_cobr
                                        FROM clientes
                                        WHERE clioid=" . $params['clioid'];
                        
                        if (!$res_cli_cad = pg_query($this->conn, $sql)) {
                            throw new Exception ("Erro ao consultar dados cadastrados.");
                        }

                        if (pg_num_rows($res_cli_cad)>0) {
                            // $cliendoid              = pg_fetch_result($res_cli_cad,0,'cliendoid');
                            $cliend_cobr              = pg_fetch_result($res_cli_cad,0,'cliend_cobr');
                            $clitipo                = pg_fetch_result($res_cli_cad,0,'clitipo');
                            $cliend_ant             = pg_fetch_result($res_cli_cad,0,'cliente_end_ant');
                            $clicorrespondencia_ant = pg_fetch_result($res_cli_cad,0,'clicorrespondencia');
                            $cliobservacao_ant      = pg_fetch_result($res_cli_cad,0,'cliobservacao');
                        } else {
                            throw new Exception ("Não há dados cadastrados para este ID de cliente.");
                        }

                        if ($clitipo == 'F') {
                            $tipo = 'res';
                        } else {
                            $tipo = 'com';
                        }

                        $parametros = " \"".$params['endno_numero']."\" \"".$params['endcomplemento']."\" \"".$params['endbairro']."\" \"".$params['enduf']."\" \"".$params['endcep']."\" \"".$params['endcidade']."\" \"".$params['endlogradouro']."\" \"NULL\" \"".$params['cliemail']."\" ";
                        $parametros = str_replace("\\'", "\\'\\'", str_replace("\\\"", "\\\\\"", $parametros));

                        if ($cliend_cobr == '') {

                            $sql = "SELECT endereco_cobranca_i('$parametros') AS retorno";


                            if(!$cli_endereco_cobranca = pg_query($this->conn, $sql)){
                                throw new Exception ("Houve um erro ao cadastrar endereço de cobrança.");
                            }
                            $cliend_cobr = pg_fetch_result($cli_endereco_cobranca,0,"retorno");

                            //CEP antigo endereco cobranca 
                            $sqlCobranca = "UPDATE 
                                                endereco
                                            SET 
                                                endno_cep = ".$params['endcep']."
                                            WHERE
                                                endoid = $cliend_cobr";
                            if(!$cepCobrancaAntigo = pg_query($this->conn, $sqlCobranca)){
                                throw new Exception ("Houve um erro ao atualizar o CEP de cobrança antigo.");
                            }

                            $end_cobranca = $params['endlogradouro'].", ".$params['endno_numero'].", ".$params['endcomplemento'].", ".$params['endbairro'].", ".$params['endcep'].", ".$params['endcidade'].", ".$params['enduf'];

                            if(strtoupper($endFavoritoAntigo) != strtoupper($endFavoritoNovo)){
                                $clihalteracao="Inserção Endereço Cobrança $end_cobranca";
                                $this->setHistorico($params['clioid'], $clihalteracao, 'D');
                            }
                        } else {

                            $endAntigo = $this->getEnderecoById($cliend_cobr);
                            $endCobrancaAntigo = $endAntigo->endlogradouro.", ".$endAntigo->endcep.", ".$endAntigo->endcomplemento.", ".$endAntigo->endbairro.", ".$endAntigo->endcidade.", ".$endAntigo->enduf;
                            $endCobrancaNovo = $params['endlogradouro'].", ".$params['endcep'].", ".$params['endcomplemento'].", ".$params['endbairro'].", ".$params['endcidade'].", ".$params['enduf'];

                            $sql = "SELECT endereco_cobranca_u('$parametros',$cliend_cobr) AS retorno";

                            // var_dump($sql)
                            if (!$cli_endereco_cobranca = pg_query($this->conn, $sql)) {
                                throw new Exception ("Erro ao atualizar dados do Endereço de Cobrança.");
                            }

                            $cliend_cobr = pg_fetch_result($cli_endereco_cobranca,0,"retorno");
                            $sqlCobranca = "UPDATE 
                                                endereco
                                            SET 
                                                endno_cep = ".$params['endcep']."
                                            WHERE
                                                endoid = $cliend_cobr";
                            
                            if(!$cepCobrancaAntigo = pg_query($this->conn, $sqlCobranca)){
                                throw new Exception ("Houve um erro ao atualizar o CEP de cobrança antigo.");
                            }

                            if(strtoupper($endCobrancaAntigo) != strtoupper($endCobrancaNovo)){
                                    $clihalteracao="Alterado Endereço de Cobrança de $endCobrancaAntigo para $endCobrancaNovo. ";
                                    $this->setHistorico($params['clioid'], $clihalteracao, 'D');
                            }
                        }

                       
                        $this->setEnderecoEntrega($params);
                        

                        $sql = "UPDATE 
                                    clientes
                                SET 
                                    cliend_cobr='".$cliend_cobr."',
                                    cliemail='".$params['cliemail']."',
                                    cliemail_nfe='".$params['cliemail_nfe']."',
                                    clicep_".$tipo." = '".$params['clicep_res']."',
                                    clino_cep_".$tipo." = '".$params['clicep_res']."',
                                    clipaisoid = '1',
                                    cliuf_".$tipo."='".$params['cliuf_res']."',
                                    clicidade_".$tipo."=formata_str('".$params['clicidade_res']."'),
                                    clibairro_".$tipo."=formata_str('".$params['clibairro_res']."'),
                                    clirua_".$tipo."=formata_str('".$params['clirua_res']."'),
                                    clino_".$tipo."='".$params['clino_res']."',
                                    clicompl_".$tipo."=formata_str('".$params['clicompl_res']."'),
                                    clifone_res='".$params['clifone_res']."',
                                    clifone_cel='".$params['clifone_cel']."',
                                    clifone_com='".$params['clifone_com']."',
                                    clifone2_com='".$params['clifone2_com']."',
                                    clifone3_com='".$params['clifone3_com']."',
                                    clifone4_com='".$params['clifone4_com']."',
                                    clicorrespondencia='".$params['clino_correspondente']."',
                                    cliobservacao='".$params['clino_observacoes']."',
                                    clidt_alteracao=now(), 
                                    cliusuoid_alteracao=".$this->cd_usuario." where clioid=".$params['clioid']."";
                        if (!pg_query($this->conn, $sql)) {
                            throw new Exception ("Erro ao atualizar dados do Endereço Principal.");
                        }

                        $end_cli = $params['clirua_res'].", ".$params['clino_res'].", ".$params['clicompl_res'].", ".$params['clibairro_res'].", ".$params['clicep_res'].", ".$params['clicidade_res'].", ".$params['cliuf_res'];
                        
                        if(strtoupper($cliend_ant) != strtoupper($end_cli)){
                            $clihalteracao="Alterado Endereço Principal de $cliend_ant para $end_cli. ";
                            $this->setHistorico($params['clioid'], $clihalteracao, 'A');
                        }
                        
                        if ($params['clino_correspondente'] != $clicorrespondencia_ant){
                            $clihalteracao="Alterado correspondências A/C $clicorrespondencia_ant para ". $params['clino_correspondente'] . ".";
                            $this->setHistorico($params['clioid'], $clihalteracao, 'A');
                        }
                        if ($params['clino_observacoes'] != $cliobservacao_ant){
                            $clihalteracao="Alterada observação $cliobservacao_ant para ". $params['clino_observacoes'] .".";
                            $this->setHistorico($params['clioid'], $clihalteracao, 'A');
                        }


                    pg_query($this->conn, "COMMIT");
                    $mensagem = 'Registro alterado com sucesso.';
                    $status = 'sucesso';
        } catch (Exception $e) {
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status = 'erro';
        }
        $resultado['mensagem'] = $mensagem;
        $resultado['status'] = $status;

        return $resultado;
    }
    

    public function setEnderecoEntrega($params){
    	if ($params['endoid'] != ''){
    		
    		$endAntigo = $this->getEnderecoById($params['endoid']);
    		$endFavoritoAntigo = $endAntigo->endlogradouro.", ".$endAntigo->endcep.", ".$endAntigo->endcomplemento.", ".$endAntigo->endbairro.", ".$endAntigo->endcidade.", ".$endAntigo->enduf;
    		$endFavoritoNovo = $params['entrega_logradouro'].", ".$params['entrega_no_cep'].", ".$params['entrega_complemento'].", ".$params['entrega_bairro'].", ".$params['entrega_cidade'].", ".$params['entrega_uf'];
    	
    		$sql = "UPDATE
						endereco
					SET
						endno_numero        =   '".$params['entrega_numero']."',
						endcomplemento      =   '".$params['entrega_complemento']."',
						endbairro           =   '".$params['entrega_bairro']."',
						enduf               =   '".$params['entrega_uf']."',
						endcep           =   '".$params['entrega_no_cep']."',
						endcidade           =   '".$params['entrega_cidade']."',
						endlogradouro       =   '".$params['entrega_logradouro']."',
						endpaisoid          =   '1'
					WHERE
						endoid = ".$params['endoid']."
					";
    		if (!pg_query($this->conn, $sql)) {
    			throw new Exception ("Erro ao atualizar dados do Endereço de Entrega.");
    		}
    		if(strtoupper($endFavoritoAntigo) != strtoupper($endFavoritoNovo)){
    			$clihalteracao="Alterado Endereço de Entrega de $endFavoritoAntigo para $endFavoritoNovo. ";
    			$this->setHistorico($params['clioid'], $clihalteracao, 'F');
    		}
            return $params['endoid'];
    	} else if ($params['entrega_no_cep'] != ''){
    		
    		if ($this->validaEndereco($params)) {
    			throw new Exception ("Erro ao atualizar dados do Endereço de Entrega na tabela endereco.");
    		}
    		
    		$sql = "INSERT INTO endereco
					(
						endno_numero,
						endcomplemento,
						endbairro,
						enduf,
						endcep,
						endcidade,
						endlogradouro,
						endpaisoid
					)
					VALUES
					(
						'".$params['entrega_numero']."',
						'".$params['entrega_complemento']."',
						'".$params['entrega_bairro']."',
						'".$params['entrega_uf']."',
						'".$params['entrega_no_cep']."',
						'".$params['entrega_cidade']."',
						'".$params['entrega_logradouro']."',
						'1'
					)
					RETURNING endoid
					";


    		if(!$cli_endereco_entrega = pg_query($this->conn, $sql)){
    			throw new Exception ("Erro ao atualizar dados do Endereço de Entrega na tabela endereco.");
    		}
    		$endereco_entrega_novo = pg_fetch_result($cli_endereco_entrega,0,"endoid");
    	
    		$sql = "INSERT INTO
						cliente_endereco
					(
						cendclioid,
						cendendoid
					)
					VALUES
					(
						".$params['clioid'].",
						".$endereco_entrega_novo."
					)
					";
    		if(!pg_query($this->conn, $sql)){
    			throw new Exception ("Erro ao atualizar dados do Endereço de Entrega na tabela cliente_endereco.");
    		}
    		$clihalteracao="Incluído Endereço de Entrega";
    		$this->setHistorico($params['clioid'], $clihalteracao, 'F');

            return $endereco_entrega_novo;
    	}
    }

    /**
     * verifica existencia de endereco conforme parametros
     */
	public function validaEndereco($params){
		
		$sql = "SELECT 
					endoid
				FROM
					endereco
				JOIN 
					cliente_endereco on endoid = cendendoid 
				JOIN 
					clientes on clioid = cendclioid
				WHERE 
					endno_numero ='".$params['entrega_numero']."' AND
					endcomplemento='".$params['entrega_complemento']."' AND
					endbairro='".$params['entrega_bairro']."' AND
					enduf='".$params['entrega_uf']."' AND
					endcep='".$params['entrega_no_cep']."' AND
					endcidade='".$params['entrega_cidade']."' AND
					endlogradouro='".$params['entrega_logradouro']."' AND
					endpaisoid=1 AND
					clioid = ".$params['clioid'];

		$rs = pg_query($this->conn, $sql);
	    
	    $result = pg_fetch_all($rs);
	    return $result;
	}
	
public function getAnexo($clioid) {
    
    $sql = "SELECT
                clicomprovante_endereco
            FROM
                clientes
            WHERE
                clioid = $clioid
        ";
    
    $rs = pg_query($this->conn, $sql);
    
    $result = pg_fetch_object($rs);
    return $result;
}

public function downloadAnexo ($caminho, $arquivo) {
    try{
        if ($caminho == '') {
            throw new Exception ("Caminho Vazio.");
        }
        if ($arquivo == '') {
            throw new Exception ("Arquivo Inválido.");
        }

        $caminhoArquivo = $_SERVER['DOCUMENT_ROOT'].'/'.$caminho.'/'.$arquivo;
        if (!file_exists($caminhoArquivo)) {
            throw new Exception ("Arquivo não encontrado ou o identificador é inválido!.");
        }

        $f= explode(".", $arquivo);
        $ext = $f[count($f) - 1];   

        $file_extension=strtolower($ext);
        
        switch ($file_extension) {
            case "txt": $ctype="text/plain"; break;
            case "pdf": $ctype="application/pdf"; break;
            case "exe": $ctype="application/octet-stream"; break;
            case "zip": $ctype="application/zip"; break;
            case "doc": $ctype="application/msword"; break;
            case "xls": $ctype="application/vnd.ms-excel"; break;
            case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
            case "gif": $ctype="image/gif"; break;
            case "png": $ctype="image/png"; break;
            case "jpe": case "jpeg":
            case "jpg": $ctype="image/jpg"; break;
            default: $ctype="application/force-download";
        }
    } catch(Exception $e){
            echo $e->getMessage();
    }

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-Type: $ctype");
    header("Content-Disposition: attachment; filename=$arquivo;");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($caminhoArquivo));
    set_time_limit(0);
    readfile($caminhoArquivo) or die("Arquivo não encontrado!");
}

    public function setAnexo($clioid , $arquivo){
        
        $resultado = array();
   
        try{
            
            if (empty($clioid)) {
                throw new Exception ("ID do cliente não pode ser nulo.");
            }
                       
            pg_query($this->conn, "BEGIN");
            
            $sql = " UPDATE clientes
                        SET clicomprovante_endereco           = '".$arquivo."'
                        WHERE clioid = ".$clioid;
            
            if (!pg_query($this->conn, $sql)) {
                throw new Exception ("Erro ao atualizar anexo.");
            }
            if ($arquivo != '')
            {
                $clihalteracao = "Novo Anexo de Comprovante de Endereço.";
                $mensagem = 'Comprovante de Endereço atualizado com sucesso.';
            } else {
                $clihalteracao = "Excluido Anexo de Comprovante de Endereço.";
                $mensagem = 'Comprovante de Endereço excluído com sucesso.';
            }
            
            $this->setHistorico($clioid, $clihalteracao, 'A');
            
            pg_query($this->conn, "COMMIT");
            $status = 'sucesso';
            
        }catch(Exception $e){
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status = 'erro';
        }
        
        $resultado['mensagem'] = utf8_encode($mensagem);
        $resultado['status'] = $status;

        return $resultado;
    }

public function uploadAnexoClioid($caminho, $arquivo, $permitido = array(), $clioid){
    try{
            if (empty($clioid)) {
                throw new Exception ("ID do cliente não pode ser nulo.");
            }
            require_once("includes/classes/Upload.php");
            $resultado = array();
            $arquivoNome = '';
            $uploadArquivo = new upload($_FILES[$arquivo], 'pt_BR');
                if ($uploadArquivo->uploaded) {
                    $uploadArquivo->image_resize = false;
                    $uploadArquivo->file_max_size = 1024*1000;
                    $uploadArquivo->allowed = $permitido;
                    $uploadArquivo->process($_SERVER['DOCUMENT_ROOT'].'/'.$caminho.'/');


                    if ($uploadArquivo->processed) {
                        $resultado = $this->setAnexo($clioid, $uploadArquivo->file_dst_name);
                        if ($resultado['status'] = 'sucesso')
                        {
                            $resultado = array('status' => 'sucesso', 'mensagem' => $uploadArquivo->file_dst_name, 'clioid' => $clioid);    
                        }
                        

                    } else {
                        if ($uploadArquivo->error == 'Arquivo muito grande.') {
                            $resultado = array('status' => 'erro', 'mensagem' => utf8_encode('A imagem não deve ultrapassar 1 MB'), 'clioid' => '');
                        } else {
                            $resultado = array('status' => 'erro', 'mensagem' => $uploadArquivo->error, 'clioid' => '');
                        }
                    }
                }
        } catch(Exception $e){
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status = 'erro';
            $resultado['mensagem'] = utf8_encode($mensagem);
            $resultado['status'] = $status;
        }
        


        return $resultado;
    }

    
    public function setGerenciadora($params, $clioid , $cd_usuario){
    	
    	$resultado = array();
   
    	try{
    		
    		if (empty($clioid)) {
    			throw new Exception ("ID do cliente não pode ser nulo.");
    		}
    		
    		if (empty($params['cligeroid'])) {
    			$params['cligeroid'] = 'NULL';
    		}
    		
    		pg_query($this->conn, "BEGIN");
    		
    		$sql = " UPDATE clientes
						SET cligeroid           = ".$params['cligeroid'].", 
						    clirede_ip          = '".$params['clirede_ip']."', 
						    clirede_porta       = '".$params['clirede_porta']."', 
						    clitpo_integr       = '".$params['clitpo_integr']."',
						    clidt_alteracao     = NOW(),
						    cliusuoid_alteracao = '$cd_usuario'
					  WHERE clioid = ".$clioid;
    		
    		if (!pg_query($this->conn, $sql)) {
    			throw new Exception ("Erro ao atualizar gerenciadora.");
    		}
    		
    		$clihalteracao = "Cadastro de gerenciadora atualizado.";
    		$this->setHistorico($params['clioid'], $clihalteracao, 'A');
    		
    		pg_query($this->conn, "COMMIT");
    		$mensagem = 'Registro alterado com sucesso.';
    		$status = 'sucesso';
    		
    	}catch(Exception $e){
    		pg_query($this->conn, "ROLLBACK");
    		$mensagem = $e->getMessage();
            $status = 'erro';
    	}
    	
    	$resultado['mensagem'] = $mensagem;
    	$resultado['status'] = $status;

    	return $resultado;
    }


    public function excluirBeneficio($clboid) {

        $resultado = array();
        try{
            pg_query($this->conn, "BEGIN");

                    if ($clboid == '') {
                            throw new Exception ("ID do beneficio não pode ser nulo.");
                        }
                    $sql = "UPDATE 
                                cliente_beneficio 
                            SET 
                                clbdt_exclusao = NOW(), 
                                clbusuoid_exclusao='".$_SESSION['usuario']['oid']."'
                            WHERE clboid='$clboid'";

                    if (!pg_query($this->conn, $sql)) {
                        throw new Exception ("Erro ao excluir beneficio.");
                    }
                    
                    pg_query($this->conn, "COMMIT");
                    $mensagem = 'Beneficio excluido com sucesso.';
                    $status = 'sucesso';

        } catch (Exception $e) {
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status = 'erro';
        }

        $resultado['mensagem'] = $mensagem;
        $resultado['status'] = $status;

        return $resultado;

    }

    public function validaCNPJ($cnpj, $clioid = null) {


        $sql = "SELECT 
                    count(*) as result
                FROM 
                    clientes 
                WHERE 
                    clino_cgc = '$cnpj'
                    AND clidt_exclusao IS NULL
            ";
        
        if($clioid)
            $sql .= " AND clioid <> $clioid";

        $rs = pg_query($this->conn, $sql);
         
        $result = pg_fetch_result($rs, 0, 'result');
         
        return $result;

    }

    public function validaCPF($cpf, $clioid = null) {


        $sql = "SELECT 
                    count(*) as result
                FROM 
                    clientes 
                WHERE 
                    clino_cpf = '$cpf'
                    AND clidt_exclusao IS NULL
            ";
            
        if($clioid)
            $sql .= " AND clioid <> $clioid";
         
        $rs = pg_query($this->conn, $sql);
         
        $result = pg_fetch_result($rs, 0, 'result');
         
        return $result;

    }

    public function getObrigacaoCliente(){
    	$sql = "select * from obrigacao_financeira where obroid in (33,50) order by obroid";
    	
    	$rs = pg_query($this->conn, $sql);
    	 
    	$result = array();
    	 
    	if(pg_num_rows($rs) > 0) {
    	
    		$result = pg_fetch_all($rs);
    	
    	}
    	 
    	return $result;
    }
    
    public function getSoftwareCliente(){
    	$sql = "SELECT msgoid, msgdescricao FROM modelo_software_gerenciador WHERE msgexclusao IS NULL ORDER BY msgdescricao";
    	
    	$rs = pg_query($this->conn, $sql);
    	 
    	$result = array();
    	 
    	if(pg_num_rows($rs) > 0) {
    	
    		$result = pg_fetch_all($rs);
    	
    	}
    	 
    	return $result;
    }

    function to_pg_array($set) {
    	settype($set, 'array'); // can be called with a scalar or array
    	$result = array();
    	foreach ($set as $t) {
    		if (is_array($t)) {
    			$result[] = to_pg_array($t);
    		} else {
    			$t = str_replace('"', '\\"', $t); // escape double quote
    			if (! is_numeric($t)) // quote only non-numeric values
    				$t = '"' . $t . '"';
    			$result[] = $t;
    		}
    	}
    	return '{' . implode(",", $result) . '}'; // format
    }
	
    public function getAutorizacaoPor(){
    	$sql = "select cd_usuario,nm_usuario from usuarios where usutipo=13 or usurefoid=2 or cd_usuario=893 or cd_usuario=604 and dt_exclusao is null order by nm_usuario";
    	
    	$rs = pg_query($this->conn, $sql);
    	 
    	$result = array();
    	 
    	if(pg_num_rows($rs) > 0) {
    	
    		$result = pg_fetch_all($rs);
    	
    	}
    	 
    	return $result;
    }

    /**
    * Recupera as profissoes cadastradas para seguradora
    *
    * @return array
    *
    */
    public function recuperarProfissoesSeguradora() {
        $retorno =  array();

        $sql = "
                SELECT 
                    pspsoid,
                    pspsprofdesc 
                FROM 
                    produto_seguro_profissoes_seguradora 
                ORDER BY 
                    pspsprofdesc";

         $rs = pg_query($this->conn, $sql);

         while($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
         }

         return $retorno;
    }

     /**
    * Recupera as particularidades de cliente SIGGO
    *
    * @return array
    *
    */
    public function recuperarParticularidadeSiggo($clioid) {
        $retorno =  array();

        $sql = "
                SELECT 
                    clippessoa_politicamente_exposta1,
                    clippessoa_politicamente_exposta2,
                    clippspsoid,
                    cliptipo_segurado
                FROM 
                    cliente_particularidade
                WHERE
                    clipclioid = ".intval($clioid)."
                ";

         $rs = pg_query($this->conn, $sql);

         while($registro = pg_fetch_object($rs)) {
            $retorno[] = $registro;
         }

         return $retorno;
    }

    /**
    * Verifica se devera realziar um INSERT ou UPDATE
    * na tabela [cliente_particularidade]
    *
    * @return boolean
    *
    */
    public function isAtualizaParticularidadeSiggo($clioid) {

        $sql = "
            SELECT EXISTS
                (
                    SELECT 
                        clipoid
                    FROM 
                        cliente_particularidade
                    WHERE 
                        clipclioid = ".intval($clioid)."
                ) AS atualiza";

         $rs = pg_query($this->conn, $sql);
         $registro = pg_fetch_object($rs);
         $retorno = ($registro->atualiza == 't') ? true : false;

         return $retorno;

    }

    /**
    * Insere um novo registro na tabela [cliente_particularidade]
    * @param array $dados
    *
    * @return boolean
    *
    */
    public function atualizarParticularidadeSiggo($dados) {

        $sql = "
                UPDATE
                    cliente_particularidade
                SET
                    clippessoa_politicamente_exposta1 =  ".$dados['clippessoa_politicamente_exposta1'].",
                    clippessoa_politicamente_exposta2 = ".$dados['clippessoa_politicamente_exposta2'].",
                    clippspsoid = ".$dados['clippspsoid'].",
                    cliptipo_segurado =  ".$dados['cliptipo_segurado']."
                WHERE
                    clipclioid = ".$dados['clioid']."                ";

         $rs = pg_query($this->conn, $sql);

         if(pg_affected_rows($rs) > 0){
            return true;
         }

         return false;
    }

    /**
    * Atualiza um registro na tabela [cliente_particularidade]
    * @param array $dados
    *
    * @return boolean
    *
    */
    public function inserirParticularidadeSiggo($dados) {

        $sql = "
                INSERT INTO 
                    cliente_particularidade
                (
                    clipclioid,
                    clippessoa_politicamente_exposta1,
                    clippessoa_politicamente_exposta2,
                    clippspsoid,
                    cliptipo_segurado
                )
                VALUES 
               (
                ".$dados['clioid'].",
                ".$dados['clippessoa_politicamente_exposta1'].",
                ".$dados['clippessoa_politicamente_exposta2'].",
                ".$dados['clippspsoid'].",
                ".$dados['cliptipo_segurado']."
                )";

         $rs = pg_query($this->conn, $sql);

         if(pg_affected_rows($rs) > 0){
            return true;
         }

         return false;
    }

    public function verificaClienteCT($clioid) {
        $sql = "SELECT EXISTS
                (
                    SELECT 1
                    FROM 
                        clientes
                    INNER JOIN 
                        contrato ON conclioid = clioid
                    WHERE 
                        coneqcoid in (38,168)
                        AND (clidt_exclusao IS NULL OR clidt_exclusao > now())
                        AND clioid =". $clioid ."
                ) AS ct";
                            
        $rs = pg_query($this->conn, $sql);

        $retorno = pg_fetch_object($rs);

        $resp = ($retorno->ct == 't') ? true : false;
        
        return $resp;
    }

    public function getDadosClienteCT($clioid) {
        
        $sql = "
                SELECT 
                    clinome, 
                    cliemail, 
                    usullogin 
                FROM 
                    clientes
                INNER JOIN 
                    cliente_localizacao ON clocclioid = clioid
                INNER JOIN 
                    usuario_localizacao ON usuloid = clocusuloid
                WHERE 
                    clioid =".$clioid." 
            ";
        
        $rs = pg_query($this->conn, $sql);

        $result = pg_fetch_array($rs, 0, PGSQL_ASSOC);

        return $result;
    }

    /**
     * Grava as chamadas aos WS da CargoTracck
     * @param string $method
     * @param string $url
     * @param string $dados
     * @param string $resposta
     * @throws Exception
     */
    public function gravaLOG($method = null, $url = null, $dados = null, $resposta = null){
            
        if($method != null){
            $method = addslashes($method);
        }
        if($url != null){
            $url = addslashes($url);
        }
        if($dados != null){
            $dados = addslashes($dados);
        }
        if($resposta != null){
            $resposta = addslashes($resposta);
        }

        // Adiciona LOG
        pg_query($this->conn, "BEGIN");
        $sql = "INSERT INTO log_ws_cargotracck
                    (lwctmethod,lwcturl,lwctdados,lwctresposta)
                VALUES
                    ('$method','$url','$dados','$resposta')";

        if (!pg_query($this->conn, $sql)) {
            throw new Exception ("Erro ao adicionar LOG.");
        }
        
        pg_query($this->conn, "COMMIT");
        pg_query($this->conn, "END");

    }
}


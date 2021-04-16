<?php


/**
 * Classe referente as ações do 'Contrato Usuário'
 *
 * @file    ti_contrato_usuario.class.php
 * @author  BRQ
 * @since   08/08/2012
 * @version 08/08/2012
 * 
 * Deverá permitir que os usuários configurem os tipos de contrato que um determinado usuário terá acesso.
 * Tais como: Aprovar, Consultar, Excluir e liberar os contratos.
 */

#############################################################################################################
#   Histórico
#       06/09/2012 - Diego C. Ribeiro (BRQ)
#           Criação do arquivo 'ti_contrato_usuario.class.php' - DUM 79720
#############################################################################################################

class ContratoUsuario {
	
	private $conn;
	public $mensagem;
	
	public function __construct($conn){
		$this->conn = $conn;
	}
	
	/**
         * Combo Tipos de Contrato
	 * Pesquisa todos os tipos de contratos existentes no sistema e que estejam ativos (flag "tpcativo").       
         * Quando é passado o usuário, são excluídos da pesquisa os contratos em que ele esta cadastrado e que não foram excluídos
         * 
	 * @return array Array de Contratos existentes
	 */
	public function pesquisarTiposContrato(){		     
            
		if(isset($_GET['cod_usuario'])){
                    $cod_usuario = (int)$_GET['cod_usuario'];
		}elseif($_POST['cod_usuario']){
                    $cod_usuario = (int)$_POST['cod_usuario'];
                }
		
		try {			
                    $query = "	SELECT tpcoid, tpcdescricao
                                            FROM tipo_contrato
                                            WHERE tpcativo = TRUE";

                    // Quando é passado o usuário, são excluídos da pesquisa os contratos em que ele 
                    // esta cadastrado (aprovados e não aprovados) e que não foram excluídos
                    if(isset($cod_usuario) and is_numeric($cod_usuario)){
                        $query .= " AND tpcoid NOT IN 
                                    (   	select tcutpcoid from tipo_contrato_usuario AS tcu
                                            LEFT JOIN tipo_contrato AS t ON tcu.tcutpcoid = t.tpcoid
                                            WHERE tcuusuoid = $cod_usuario 
                                            AND( tcudt_aprovacao IS  NULL OR (tcudt_aprovacao IS NOT NULL AND tcudt_exclusao IS NULL))
                                    ) ";
                    }			
                    $query .= " ORDER BY tpcdescricao;";    
                    
                    $result = pg_query($this->conn, $query);

                    $arrTiposContrato = array();			
                    while ($row = pg_fetch_assoc($result)) {
                            $arrTiposContrato[$row['tpcoid']] = $row['tpcdescricao'];
                    }
                    return $arrTiposContrato;
			
		} catch (Exception $e) {
			$this->mensagem = $e->getMessage();
		}				
	}
	
	/**
	 * Pesquisa os usuários por parte do nome e pelo tipo de contrato informado
	 */
	public function pesquisarUsuariosTipoContrato($usuario,$tipo_contrato){
		
		try {
			
			// Para realizar a pesquisa, pelo menos um dos campos deverá ser selecionado
			if(empty($usuario) and (empty($tipo_contrato) or $tipo_contrato == "Selecione")){
				 throw new Exception("Favor preencher pelo menos um dos campos para executar a pesquisa.");			 
			}
			
			// Faz a pesquisa somente na tabela de usuários
			if(!empty($usuario) and (empty($tipo_contrato) or $tipo_contrato == "-1")){
				
				$query = "	SELECT cd_usuario AS    cod_usuario, 
									nm_usuario AS usuario, 
									ds_login AS login,
									usuemail AS email 
							FROM usuarios
							WHERE nm_usuario ILIKE '$usuario%'
							ORDER BY nm_usuario;";
			
			// Pesquisa os usuários cadastrados na tabela tipo_contrato_usuario	
			}else{
				
				if(!is_numeric($tipo_contrato)){
					throw new Exception("Houve um erro no código informado do tipo do contrato");
				}
				
				$query = "	SELECT cd_usuario AS    cod_usuario, 
									nm_usuario AS usuario, 
									ds_login AS login,
									usuemail AS email 
							FROM tipo_contrato_usuario AS tcu
							LEFT JOIN usuarios AS u ON tcu.tcuusuoid = u.cd_usuario
							LEFT JOIN tipo_contrato AS t ON tcu.tcutpcoid = t.tpcoid
							WHERE tcu.tcutpcoid = $tipo_contrato
                                                        AND tcudt_exclusao IS NULL";
													
				if(!empty($usuario)){
					$query .= " OR u.nm_usuario ILIKE '$usuario%'";
				}
				
				$query .= " GROUP BY cd_usuario, nm_usuario, ds_login, usuemail					
							ORDER BY nm_usuario";
			}							
			
			$result = pg_query($this->conn, $query);

			$arrUsuarioContrato = array();
			while ($row = pg_fetch_assoc($result)) {
				$arrUsuarioContrato[] = $row;
			}
			return $arrUsuarioContrato;
		
		} catch (Exception $e) {
			$this->mensagem = $e->getMessage();
		}		
	}	

	/**
	 * Pesquisa as aprovações Pendentes
	 */
	public function pesquisarAprovacoesPendentes(){
		
		try {
			$query = "  SELECT  tcu.tcuoid          AS cod_tipo_contrato_usuario,
                                            u.nm_usuario 	AS nome_usuario,
                                            t.tpcdescricao 	AS tipo_contrato, 
                                            tcu.tcudt_cadastro 	AS data_cadastro, 
                                            u2.nm_usuario 	AS nome_usuario_cadastro,
                                            tcu.tcutpcoid	AS id_tipo_contrato
                                        
                                    FROM tipo_contrato_usuario      AS tcu
                                        INNER JOIN usuarios         AS u ON tcu.tcuusuoid = u.cd_usuario
                                        INNER JOIN usuarios         AS u2 ON tcu.tcuusuoid_cadastro = u2.cd_usuario
                                        INNER JOIN tipo_contrato    AS t ON tcu.tcutpcoid = t.tpcoid	
                                    WHERE tcudt_aprovacao IS NULL
                                        AND tcudt_exclusao IS NULL";
			$result = pg_query($this->conn, $query);                       
			
                        $arrAprovacoesPendentes = array();
                        if(pg_num_rows($result) > 0){                            
                            while ($row = pg_fetch_assoc($result)) {

                                    $data = date_create($row['data_cadastro']);
                                    $data = date_format($data, 'd/m/Y');
                                    $row['data_cadastro'] = $data;
                                    $arrAprovacoesPendentes[] = $row;
                            }
                            
                        }else{
                            $this->mensagem = "Não há aprovações pendentes para aprovar.";
                        }
                        return $arrAprovacoesPendentes;
			
		} catch (Exception $e) {
			$this->mensagem = $e->getMessage();
		}
	}
	
	/**
	 * Pesquisa os Dados dos tipos de contrato cadastrados para o usuário
	 * @param boolean $somenteAprovados - Pesquisar somente os contratos aprovados ou para aprovar
	 */
	public function pesquisarContratosCadastradosUsuario(){
		
		try {
			
			if(isset($_POST['cod_usuario'])){
				$cod_usuario = (int)$_POST['cod_usuario'];
			}else{
				throw new Exception("Houve um erro com o código do usuário.");
			}
			
			$query = "	SELECT 	tcu.tcuoid                                      AS cod_tipo_contrato_usuario,
								t.tpcdescricao 			AS contrato_usuario,
								tcu.tcudt_cadastro 		AS data_cadastro, 
								u.nm_usuario			AS nome_usuario,
								tcu.tcudt_aprovacao		AS data_aprovacao,
								u2.nm_usuario			AS nome_usuario_cadastro,
								u3.nm_usuario 			AS nome_usuario_aprovacao
						FROM tipo_contrato_usuario AS tcu
						INNER JOIN usuarios AS u ON tcu.tcuusuoid = u.cd_usuario
						INNER JOIN usuarios AS u2 ON tcu.tcuusuoid_cadastro = u2.cd_usuario
						INNER JOIN tipo_contrato AS t ON tcu.tcutpcoid = t.tpcoid
						LEFT JOIN usuarios AS u3 ON tcu.tcuusuoid_aprovacao = u3.cd_usuario 
						WHERE tcu.tcuusuoid = $cod_usuario 
 
                                                    AND tcudt_exclusao IS NULL";	

			$result = pg_query($this->conn, $query);
				
			$arrContratosCadastrados = array();
			while ($row = pg_fetch_assoc($result)) {
				
				$data1 = date_create($row['data_cadastro']);
				$data1 = date_format($data1, 'd/m/Y');
				$row['data_cadastro'] = $data1;
				
				$data2 = date_create($row['data_aprovacao']);
				$data2 = date_format($data2, 'd/m/Y');
				$row['data_aprovacao'] = $data2;
				
				$arrContratosCadastrados[] = $row;
			}
			return $arrContratosCadastrados;
			
		} catch (Exception $e) {
			$this->mensagem = $e->getMessage();
		}			
	}

	/**
         * Pesquisa os dados do usuário
         * @return array Array com o Nome e Login do usuário
         * @throws Exception
         */
        public function pesquisarDadosUsuario(){
				
		try {
			
			if(isset($_POST['cod_usuario'])){
				$cod_usuario = (int)$_POST['cod_usuario'];
			}else{
				throw new Exception("Houve um erro com o código do usuário.");
			}
			
			$query = "SELECT nm_usuario, ds_login FROM usuarios WHERE cd_usuario = $cod_usuario";
			$result = pg_query($this->conn, $query);
			
			$arrUsuario = array();
			if(pg_num_rows($result) == 1){			
				$arrUsuario = pg_fetch_assoc($result);
			}
			return $arrUsuario;
			
		} catch (Exception $e) {
			$this->mensagem = $e->getMessage();
		}		
	}
	
	/**
         * Cadastra um tipo de contrato para o usuário
         * @throws Exception
         */
        public function cadastrarTipoContratoUsuario(){
		
            try {

                if(isset($_POST['cod_usuario'])){
                    $cod_usuario = (int)$_POST['cod_usuario'];
                }else{
                    throw new Exception("Houve um erro com o código do usuário.");
                }

                $tcuusuoid_cadastro = $_SESSION['usuario']['oid'];

                if(isset($_POST['tipo_contrato']) and is_numeric($_POST['tipo_contrato'])){
                        $tipo_contrato = $_POST['tipo_contrato'];
                }else{
                        throw new Exception("O valor informado para o tipo de contrato é inválido.");
                }
                
                $query = "SELECT * 
                            FROM tipo_contrato_usuario
                            WHERE tcuusuoid = $cod_usuario
                                    AND tcutpcoid = $tipo_contrato
                                    AND tcudt_aprovacao IS NULL
                                    AND tcudt_exclusao IS NULL;";
                $result = pg_query($this->conn, $query);                
                
                if(pg_num_rows($result) > 0){			
                    throw new Exception("Tipo de contrato já cadastrado, aguardando aprovação.");
                }

                $query = "INSERT INTO tipo_contrato_usuario (tcuusuoid, tcutpcoid, tcudt_cadastro, tcuusuoid_cadastro)
                                        VALUES ($cod_usuario, $tipo_contrato, NOW(), $tcuusuoid_cadastro);";
                                $result = pg_query($this->conn, $query);    
                
                if($result){
                    $this->mensagem = "Tipo de contrato cadastrado com sucesso, aguardando aprovação.";
                }else{
                    $this->mensagem = "Não foi possível cadastrar o Tipo de contrato.";
                }

            } catch (Exception $e) {
                    $this->mensagem = $e->getMessage();
            }				
	}
        
        /**
         * Exclui o tipo de contrato selecionado
         * @param type $cod_tipo_contrato_excluir
         * @return boolean
         */
        public function excluirTipoContratoUsuario($cod_tipo_contrato_excluir){
            
            try {
                
                $tcuusuoid_exclusao = $_SESSION['usuario']['oid'];
                
                $query = "  UPDATE tipo_contrato_usuario 
                            SET tcudt_exclusao = NOW(), 
                                tcuusuoid_exclusao = $tcuusuoid_exclusao 
                            WHERE tcuoid = $cod_tipo_contrato_excluir;";
                $result = pg_query($this->conn, $query);    
                
                if($result){
                    $this->mensagem = "O contrato foi excluído com sucesso!";
                    return true;
                }else{
                    return false;
                }
                
            } catch (Exception $e) {
                $this->mensagem = $e->getMessage();
            }                     
        }
        
        /**
         * 
         * @param type $arrAprovarContratoUsuario
         * @return boolean
         * @throws Exception
         */
        public function aprovarContratoUsuario($arrAprovarContratoUsuario){ 
             try {
                             	
                $tcuusuoid_aprovacao = $_SESSION['usuario']['oid'];

                if(is_array($arrAprovarContratoUsuario) and count($arrAprovarContratoUsuario)>0){
                    foreach ($arrAprovarContratoUsuario as $value) {
                    	
							
                    		$arrDados = explode(",",$value);
                    		$value = $arrDados[0];
                    		 
                    		if(is_numeric($value)){
                    			$query = "  UPDATE tipo_contrato_usuario
                    			SET tcudt_aprovacao = NOW(), tcuusuoid_aprovacao = $tcuusuoid_aprovacao
                    			WHERE tcuoid = $value
                    			AND tcudt_aprovacao IS NULL;";
                    		
                    			$result = pg_query($this->conn, $query);
                    		}else{
                    			throw new Exception("Código do tipo do contrato inválido.");
                    		}	
                    	}
                    	                                      
                    $this->mensagem = "Os contratos selecionados foram aprovados com sucesso";
                    return true;
                }                
            } catch (Exception $e) {
                $this->mensagem = $e->getMessage();
            }              
        }
}
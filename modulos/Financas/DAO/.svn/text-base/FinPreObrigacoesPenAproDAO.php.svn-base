<?php

/**
 * APROVAÇÃO DE TAG SASWEB – PRÉ OBRIGAÇÃO FINANCEIRA
 *
 * @file FinPreObrigacoesPenAproDAO.php
 * @author Ernando de Castro <ernando.castro.ext@sascar.com.br>
 * @version 1.0
 * @since 06/08/2015 08:46
 */

class FinPreObrigacoesPenAproDAO {

	/**
	 * Conexão com o banco de dados
	 * @var resource
	 */
	private $conn;

	/**
	 * Mensagem de erro para o processamentos dos dados
	 * @const String
	 */
	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";


	public function __construct($conn) {
		//Seta a conexão na classe
		$this->conn = $conn;
	}
	

	// Insere a Funcionalidade no Pacote, se este já estiver criado: 
	public function inserirFuncionalidadePacote($obroid) {
	
		// Insere a Funcionalidade no Pacote
		$sql = "INSERT INTO 
				obrigacao_financeira_item 
            	(ofiservico,ofiobroid)
  			(SELECT
	          $obroid,
	          p.obroid
   			FROM 
   				obrigacao_financeira p
   			WHERE 
   				p.obrtipo_obrigacao='P'
    		AND 
    		p.obrtag_pacote = 
    			(SELECT 
    				f.obrtag_pacote
        		FROM 
        			obrigacao_financeira f
        		WHERE 
        			f.obrdt_exclusao 
        			IS NULL
          		AND 
          			f.obroid=$obroid 
          		AND 
          			f.obroid 
          		NOT IN(SELECT 
          				ofiservico 
          			   FROM 
          			   	obrigacao_financeira_item 
          			   WHERE 
          			   	f.obroid = ofiservico 
          			   AND 
          			   	ofiobroid=p.obroid 
          			   AND 
          			   	ofiexclusao IS NULL))
     		AND obrdt_exclusao IS NULL);";
		
		$resultado = pg_query($this->conn, $sql);
	
		if ($resultado) {
			return true;
		} else {
			throw new ErrorException('Falha a Insere a Funcionalidade no Pacote, se este já estiver criado.');
		}
	}

	// Busca dados da pesquisa de acordo com processo em execução
	public function buscarDadosPesquisa($parametros) {
		
       $filtros = '0=0';
       
       if ($parametros->status !="") {
       	 $filtros.= " AND rastrastsoid = $parametros->status";
       }
           
       
       if ($parametros->dtInicio !="" && $parametros->dtFim !="") {       	
      		
            $dtInicio = implode("-",array_reverse(explode("/",$parametros->dtInicio)));            
            $dtFim = implode("-",array_reverse(explode("/",$parametros->dtFim)));
          
        	$filtros.= " AND rastdt_cadastro between '$dtInicio 00:00:00' AND '$dtFim 23:59:59'";
       
       }
       
       if ($parametros->tag !="") {
          $filtros.= " AND (rasttag_funcionalidade = '$parametros->tag' OR rasttag_pacote = '$parametros->tag')";
       }
      
       if ($parametros->descricao !="") {
       	  $filtros.= " AND rastdescricao ilike '%$parametros->descricao%'";
       }
       
       if ($parametros->tipo !="") {
         $filtros.= " AND rasttipo = '$parametros->tipo'";
       }
       
	
		$sql = "SELECT rastoid,
			       rastdescricao,
			       to_char(rastdt_aprovacao,'dd/mm/yyyy hh:mm') AS data_aprovacao,
  			   (SELECT nm_usuario
   				  FROM usuarios
   				WHERE 
           					rastusuoid_aprovacao=cd_usuario)AS aprovador,
               				rasttag_pacote,
              			 rasttag_funcionalidade,
              		 CASE
                   WHEN rasttipo= 'F' THEN 'Funcionalidade'
                   ELSE 'Pacote'
               END AS tipo,
      	 	rastsdescricao,
  			(SELECT 
  				obrobrigacao
   			FROM 
   				obrigacao_financeira
   			WHERE 
   				obrdt_exclusao IS NULL
     		AND 
     			obrrastoid=rastoid limit 1) AS obrigacao_financeira
			FROM 
				rastreamento_tag, rastreamento_tag_status
			WHERE 
				rastrastsoid=rastsoid
 			 AND $filtros;";
		
		$resultado = pg_query($this->conn, $sql);
		
		if ($resultado) {
			if (pg_num_rows($resultado) > 0) {
				return pg_fetch_all($resultado);
			} else {
				throw new Exception('Nenhum Registro Encontrado!');
			}
		} else {
			throw new ErrorException('Não foi encontrado nenhum registros!');
		}
	
		
	}

    /**
     * Atualiza tag do Pacote na obrigação
     * @param unknown $obroid
     * @throws Exception
     * @return boolean
     */
    public function atualizaTagPacoteObrigacao($obroid) {

        $sql = "UPDATE 
        			obrigacao_financeira
				SET 
					obrtag_pacote=rasttag_pacote
				FROM 
					rastreamento_tag
				WHERE 
					obrrastoid=rastoid
  				AND 
  					obroid=$obroid;";

        if (!$res = pg_query($this->conn, $sql)) {
            throw new Exception("Falha ao Atualiza tag do Pacote na obrigação.");
        }

        return true;
    }

    /**
     * UPDATE que altera o status da TAG para Aprovada.
     * @param unknown $rastoid
     * @throws Exception
     */
    public function processarPrevisao($rastoid,$obroid,$cd_usuario) {
    
    		$sql = "UPDATE 
    					rastreamento_tag 
    				SET 
    					rastrastsoid=3, 
    					rastobroid=$obroid,
    					rastdt_aprovacao=now(), 
    					rastusuoid_aprovacao=$cd_usuario
    				WHERE 
    					rastoid=$rastoid;";
    
    	//echo "Update -> <br/><br/>". $sql;
    
    	if (!$res = pg_query($this->conn, $sql)) {
    		throw new Exception("Falha ao alterar o status da TAG para Aprovada.");
    	}
    	
    	if (pg_affected_rows($res) == 0){
    		throw new Exception("Nenhum registro alterado.");
    	}		
    
    }

    public function insereHistorico($obroid, $parametros, $descHist) {
        if($obroid == null || empty($obroid)) {
            $sqlCodObr = "SELECT
                            rastobroid
                          FROM
                            rastreamento_tag
                          WHERE
                            rastoid = $parametros->rastoid;";

            $resCodObr = pg_query($this->conn, $sqlCodObr);

            if(pg_num_rows($resCodObr) > 0) {
                $obroid = pg_fetch_result($resCodObr, 0, 'rastobroid');
            }
        }

        $vlrPadrao = 'NULL';
        $obrDescricao = '';
        $usuoid = $_SESSION['usuario']['oid'];

        if(!empty($obroid)) {
            $sqlObr = "SELECT
                        COALESCE(obrvl_obrigacao, 0) AS valor_padrao,
                        obrvl_minimo AS valor_minimo,
                        obrvl_maximo AS valor_maximo,
                        obrobrigacao AS obrDescricao
                    FROM
                        obrigacao_financeira
                    WHERE
                        obroid = $obroid
                    LIMIT 1";

            $resObr = pg_query($this->conn, $sqlObr);

            

            if(pg_num_rows($resObr) > 0) {
                $vlrPadrao = pg_fetch_result($resObr, 0, 'valor_padrao');
                $vlrMinimo = pg_fetch_result($resObr, 0, 'valor_minimo');
                $vlrMaximo = pg_fetch_result($resObr, 0, 'valor_maximo');
                $obrDescricao = pg_fetch_result($resObr, 0, 'obrDescricao');
            }
        } else {
                $obroid = "NULL";
        }

        if(empty($vlrMinimo)) {
            $vlrMinimo = 'NULL';
        }
        if(empty($vlrMaximo)) {
            $vlrMaximo = 'NULL'; 
        }

        $sqlRast = "SELECT
                        rastrastsoid
                    FROM
                        rastreamento_tag
                    WHERE
                        rastoid = $parametros->rastoid";

        $resRast = pg_query($this->conn, $sqlRast);

        $rastrastsoid = 'NULL';

        if(pg_num_rows($resRast) > 0) {
            $rastrastsoid = pg_fetch_result($resRast, 0, 'rastrastsoid');
        }

        $sqlHist = "INSERT INTO rastreamento_tag_historico
                (
                    rasthrastoid,
                    rasthobroid,
                    rasthdt_cadastro,
                    rasthrastsoid,
                    rasthusuoid,
                    rasthacao,
                    rasthvalor_padrao,
                    rasthvalor_maximo,
                    rasthvalor_minimo,
                    rasthdt_cancelamento,
                    rasthdesc_obrigacao,
                    rasthobrigacao_unica_cliente
                ) VALUES (
                    $parametros->rastoid,
                    $obroid,
                    now(),
                    $rastrastsoid,
                    $usuoid,
                    '$descHist',
                    $vlrPadrao,
                    $vlrMaximo,
                    $vlrMinimo,
                    null,
                    '$obrDescricao',
                    '$parametros->obrigacao_unica'
                )";

        $resHist = pg_query($this->conn, $sqlHist);

        if(!$resHist) {
            throw new ErrorException("Não foi possível registrar o histórico!");
        }
    }
    
    /**
     * INSERT para criação da Obrigação Financeira de PACOTE
     * @param unknown $dados lista de objet
     * @throws ErrorException
     * @return boolean
     */
    public function inserirObrigacaoFinanceiraPacote($parametros) {
    
    	$valor = str_replace("." , "" , $parametros->valor );
    	$valor = str_replace("," , "." , $valor );
    	
    	$proRata = $parametros->proRata == "" ? 'NULL':$parametros->proRata;
    	
    	// INSERT para criação da Obrigação Financeira de PACOTE
    	$sql = "INSERT INTO 
    				obrigacao_financeira 
    			(
    				obrrastoid,
    				obrorigem,
    				obrtag_pacote,
    				obrtipo_obrigacao,
    				obroftoid,
    				obrobrigacao, 
    				obraliseroid,
    				obrofgoid,
    				obrvl_obrigacao, 
    				obrprorata,
                    obrcliente)
				VALUES ($parametros->rastoid,
				        'G',     
				        '$parametros->tagPacote',
				        '$parametros->classificacao',
				         $parametros->tipo,
				        '$parametros->obrigFinanceira',
				         $parametros->codServico,
				         $parametros->grupoFaturamento,
				         $valor, 
				         $proRata,
				    	 '$parametros->obrigacao_unica') RETURNING obroid;";
    	
    	    
		$dbResult = pg_query($this->conn, $sql);
		
				
		if (!$dbResult) {
			throw new ErrorException('Erro ao inserir obrigação financeira.');
		}
		
		if (pg_num_rows($dbResult) > 0) {
			return pg_fetch_all($dbResult);
		} 
    }
    /**
     * Insere as Funcionalidades no Pacote, se já existirem funcionalidades criadas	
     * @param unknown $obroid
     * @throws ErrorException
     * @return boolean
     */
    public function inserirFuncionalidadesPacote($obroid) {
        // Insere as Funcionalidades no Pacote, se já existirem funcionalidades criadas	
    	$sql = "INSERT INTO 
    				obrigacao_financeira_item 
            	(ofiobroid ,ofiservico)
			  		(SELECT 
			          $obroid,   
			          f.obroid  
			   FROM 
			   		obrigacao_financeira f
			   WHERE 
			   		f.obrtipo_obrigacao='V'
			   AND f.obrtag_pacote =
		       (SELECT 
		       		p.obrtag_pacote
		        FROM 
		        	obrigacao_financeira p
		        WHERE 
		        	p.obrdt_exclusao IS NULL
		        AND 
		            p.obroid=$obroid)
     		AND 
     			obrdt_exclusao IS NULL
     		AND 
     			f.obroid 
     		NOT IN(SELECT 
     					ofiservico 
     			   FROM 
     					obrigacao_financeira_item 
     			  WHERE 
     					f.obroid = ofiservico 
     				AND 
     					ofiobroid=$obroid 
     				AND 
     					ofiexclusao IS NULL)
			);";
    
    	$resultado = pg_query($this->conn, $sql);
    
    	if ($resultado) {
    		return true;
	    } else {
	    	throw new ErrorException('Falha ao Insere as Funcionalidades no Pacote, se já existirem funcionalidades criadas.');
	    }
    }
    /**
     * UPDATE que altera o status da TAG para Aprovada.
     * @param unknown $rastoid
     * @throws Exception
     */
    public function processarAlteraStatusTagAprovada($rastoid,$obroid,$cd_usuario) {
    
    	$sql = "UPDATE 
    				rastreamento_tag 
    			SET 
    				rastrastsoid = 3,
    				rastobroid = $obroid, 
    				rastdt_aprovacao = now(), 
    				rastusuoid_aprovacao = $cd_usuario 
    			WHERE 
    				rastoid = $rastoid;";
    
    	//echo "Update -> <br/><br/>". $sql;
    
    	if (!$res = pg_query($this->conn, $sql)) {
    		throw new Exception("Falha ao alterar o status da TAG para Aprovada.");
    	}
    	if (pg_affected_rows($res) == 0){
    		throw new Exception("Nenhum registro alterado.");
       }
    
    }
    
   /**
    *  busca a lista de tipo com oftexclusao IS NULL
    * @throws Exception
    * @throws ErrorException
    * @return object
    */    
   public function recuperarParametrosTipo() {
    
        $sql = "SELECT 
        			oftoid,
      				oftdescricao
				FROM 
        			obrigacao_financeira_tipo
				WHERE 
        			oftexclusao IS NULL
				ORDER BY oftdescricao;";
    
    	$resultado = pg_query($this->conn, $sql);
    
    	if ($resultado) {
    		if (pg_num_rows($resultado) > 0) {
    			return pg_fetch_all($resultado);
    		} else {
    			throw new Exception('Não foram encontrados tipo.');
    		}
    	} else {
    		throw new ErrorException('Falha ao recuperar tipo.');
    	}
    	
    }
    /**
     * Busca a lista status com rastsdt_exclusao IS NULL
     * @param unknown $status
     * @throws Exception
     * @throws ErrorException
     * @return object
     */
    public function recuperarParametrosStatus(){
    	
    	
    	$sql = "SELECT 
			       rastsoid,
			       rastsdescricao
				FROM 
       				rastreamento_tag_status
				WHERE 
				     rastsdt_exclusao IS NULL
				ORDER BY rastsdescricao;";
    	
    	$resultado = pg_query($this->conn, $sql);
    	
    	if ($resultado) {
    		if (pg_num_rows($resultado) > 0) {    
    						    			
    			return pg_fetch_all($resultado);
    			
    		} else {
    			throw new Exception('Não foram encontrados status.');
    		}
    	} else {
    		throw new ErrorException('Falha ao recuperar status.');
    	}
    	
    }

    public function alterarStatus($rastoid, $status) {

        $sql = "UPDATE
                    rastreamento_tag
                SET
                    rastrastsoid = $status
                WHERE
                    rastoid = $rastoid";

        $res = pg_query($this->conn, $sql);

        if(!$res) {
            throw new ErrorException("Não foi possível alterar o status.");
        }

        return true;
    }
   
    /**
     * SQL apresenta dados da Funcionalidade
     * @param unknown $rastoid
     * @throws Exception
     * @throws ErrorException
     * @return multitype:
     */
    
    public function recuperarDadosFuncionalidade($rastoid){
    	 
    	 
    	$sql = "SELECT
			       rastdescricao AS descricao,		
			       rasttag_funcionalidade AS tag, 
			       rasttag_pacote,
                   rastobrigacao_unica_cliente,
                   rastrastsoid AS status
				FROM  
				    rastreamento_tag
				WHERE 
				    rastoid=$rastoid;";

    	$resultado = pg_query($this->conn, $sql);
    	 
    	if ($resultado) {
    		if (pg_num_rows($resultado) > 0) {
    				
    			return pg_fetch_all($resultado);
    			 
    		} else {
    			throw new Exception('Não foram encontrados status.');
    		}
    	} else {
    		throw new ErrorException('Falha ao recuperar status.');
    	}
    	 
    }
    
    /**
     * SQL para apresentar dados do Pacote
     * @param unknown $rastoid
     * @throws Exception
     * @throws ErrorException
     * @return multitype:
     */
    
    public function recuperarDadosPacote($rastoid){
    
    
    	$sql = "SELECT
			       rastdescricao AS descricao,		
			       rasttag_pacote AS tag,
                   rastobrigacao_unica_cliente,
                   rastrastsoid AS status
				FROM  
				       rastreamento_tag
				WHERE 
				       rastoid=$rastoid;";
    
    	$resultado = pg_query($this->conn, $sql);
    
    	if ($resultado) {
    		if (pg_num_rows($resultado) > 0) {
    
    			return pg_fetch_all($resultado);
    
    		} else {
    			throw new Exception('Não foram encontrados status.');
    		}
    	} else {
    		throw new ErrorException('Falha ao recuperar status.');
    	}
    
    }
    /**
     * Código do Serviço
     * @throws Exception
     * @throws ErrorException
     * @return multitype:
     */
    public function codigoServico(){
    	
    	$sql = "SELECT 
	    			aliseroid,
	       			alisercodigoservico||' - '||aliseratividade AS descricao
				FROM 
    				aliquota_servico
				ORDER BY 
    				descricao;";
    	 
    	$resultado = pg_query($this->conn, $sql);
    	 
    	if ($resultado) {
    		if (pg_num_rows($resultado) > 0) {
    				
    			return pg_fetch_all($resultado);
    			 
    		} else {
    			throw new Exception('Não foram encontrados Código do Serviço.');
    		}
    	} else {
    		throw new ErrorException('Falha ao recuperar Código do Serviço.');
    	}
    	
    }
    /**
     * Grupo de Faturamento
     * @throws Exception
     * @throws ErrorException
     * @return multitype:
     */
    public function grupoFaturamento() {
    	
      		 $sql = "SELECT 
	      		 		ofgoid,
	       				ofgdescricao
					FROM 
      		 			obrigacao_financeira_grupo
					WHERE 
      		 			ofgexclusao IS NULL
					ORDER BY 
      		 			ofgdescricao;";
    	 
    	$resultado = pg_query($this->conn, $sql);
    	 
    	if ($resultado) {
    		if (pg_num_rows($resultado) > 0) {
    				
    			return pg_fetch_all($resultado);
    			 
    		} else {
    			throw new Exception('Não foram encontrados Grupo de Faturamento.');
    		}
    	} else {
    		throw new ErrorException('Falha ao recuperar Grupo de Faturamento.');
    	}
    }
    
    /**
     *  Pró-Rata
     * @throws Exception
     * @throws ErrorException
     * @return multitype:
     */
    public function proRata() {
    	 
    	$sql = "SELECT 
    				obroid, 
    				obrobrigacao 
    			FROM 
    				obrigacao_financeira 
    			WHERE
    				obrofgoid=22 
    			AND 
    				obrdt_exclusao 
    			IS NULL ORDER BY 
    				obrobrigacao;";
    
    	$resultado = pg_query($this->conn, $sql);
    
    	if ($resultado) {
    		if (pg_num_rows($resultado) > 0) {
    
    			return pg_fetch_all($resultado);
    
    		} else {
    			throw new Exception('Não foram encontrados Pró-Rata.');
    		}
    	} else {
    		throw new ErrorException('Falha ao recuperar Pró-Rata.');
    	}
    }
    /**
     * Validar Tag
     * @param unknown $obrrastoid
     * @throws Exception
     * @throws ErrorException
     */
    public function validarTag($parametros) {
    
    	$sql = "SELECT 
    				obrrastoid
    			FROM 
    				obrigacao_financeira 
    			WHERE 
    				obrdt_exclusao IS NULL 
    			AND 
    				obrrastoid=$parametros->rastoid;";
    
    	$resultado = pg_query($this->conn, $sql);
    
    		if (pg_num_rows($resultado) > 0) {     			
    			throw new Exception('Tag já cadastrada.');
    			return false;
    		}
    		return true;
    	
    }
    
	
	// INSERT para criação da Obrigação Financeira de FUNCIONALIDADE
	public function inserirCriacaoObrigacao($parametros) {
		
		$valor = str_replace("." , "" , $parametros->valor ); 
		$valor = str_replace("," , "." , $valor );
		$proRata = $parametros->proRata == "" ? 'NULL':$parametros->proRata;
		
		$sql_prorata = "INSERT INTO 
							obrigacao_financeira 
							(obrrastoid,
							obrtag_pacote,
							obrtag_funcionalidade,
							obrtipo_obrigacao,
							obroftoid,
							obrobrigacao, 
							obraliseroid,
							obrofgoid,
							obrvl_obrigacao,
							obrprorata,
							obrorigem,
                            obrcliente)
						VALUES 
						($parametros->rastoid,
						'$parametros->tagPacote',
				        '$parametros->rasttag_pacote',
				        '$parametros->classificacao',
				         $parametros->tipo,
				        '$parametros->obrigFinanceira',
				         $parametros->codServico,
				         $parametros->grupoFaturamento,
				         $valor,
						 $proRata,
						 'G',
                         '$parametros->obrigacao_unica') RETURNING obroid;";
		
	    $dbResult = pg_query($this->conn, $sql_prorata);
		
		if (!$dbResult) {
			throw new Exception("Falha ao INSERT para criação da Obrigação Financeira de FUNCIONALIDADE.");
		}
		
		if (pg_num_rows($dbResult) > 0) {	
			return pg_fetch_all($dbResult);
		} 
		
	}


	/**
	 * Visualizar  detalhe da Funcionalidade
	 * @param unknown $parametros
	 * @throws Exception
	 * @return multitype:
	 */
	public function consultarDetalheFuncionalidade($rastoid) {
		
		$sql = "SELECT 'Funcionalidade' AS tipo,
					  (SELECT a.rastdescricao
					   FROM rastreamento_tag a
					   WHERE a.rasttag_pacote=b.rasttag_pacote
					     AND a.rasttipo='P' limit 1)AS pacote,
					     
					       rastdescricao AS descricao,
					       to_char(rastdt_aprovacao,'dd/mm/yyyy hh:mm') AS data_aprovacao,
					
					  (SELECT nm_usuario
					   FROM usuarios
					   WHERE rastusuoid_aprovacao=cd_usuario) AS aprovador,
					       rasttag_funcionalidade AS tag,
					       rastsdescricao AS status,
					
					  (SELECT obrobrigacao
					   FROM obrigacao_financeira
					   WHERE obrdt_exclusao IS NULL
					     AND obrrastoid=rastoid limit 1) AS obrigacao_financeira,
                        rastobrigacao_unica_cliente
					FROM rastreamento_tag b,
					     rastreamento_tag_status
					WHERE rastrastsoid=rastsoid
					  AND rastoid=$rastoid;";		

		if (!$resultado = pg_query($this->conn, $sql)) {
			throw new Exception("Falha ao Visualizar detalhe da Funcionalidade");
		}
	
		if (pg_num_rows($resultado) > 0) {
				return pg_fetch_all($resultado);
		} else {
			throw new Exception("Nenhum registro encontrado.");
		}
	
	}

    public function consultaHistorico($rastoid) {
        $sql = "SELECT
                    rasthdt_cadastro AS data_cadastro,
                    nm_usuario AS usuario,
                    rasthacao AS acao,
                    rasthvalor_padrao AS valor_padrao,
                    rasthvalor_minimo AS valor_minimo,
                    rasthvalor_maximo AS valor_maximo,
                    rasthdesc_obrigacao AS descricao,
                    rasthdt_cancelamento AS data_cancelamento,
                    rasthobrigacao_unica_cliente AS obr_unica,
                    rastsdescricao AS status
                FROM
                    rastreamento_tag_historico
                    INNER JOIN usuarios ON rasthusuoid = cd_usuario
                    INNER JOIN rastreamento_tag_status on rasthrastsoid = rastsoid
                WHERE
                    rasthrastoid = $rastoid
                ORDER BY
                    rasthdt_cadastro DESC";

        $res = pg_query($this->conn, $sql);

        if(pg_num_rows($res) > 0) {
            return pg_fetch_all($res);
        }

        return null;
    }
	/**
	 * Visualizar  detalhe do Pacote
	 * @param unknown $rastoid
	 * @throws Exception
	 * @return multitype:
	 */
	public function consultarDetalhePacote($rastoid) {
	
		$sql = "SELECT
			      'Pacote' AS tipo,
			       rastdescricao AS descricao,		
			       to_char(rastdt_aprovacao,'dd/mm/yyyy hh:mm') AS data_aprovacao,
				  (SELECT 
				  		nm_usuario
				   FROM 
				   		usuarios
				   WHERE 
				   		rastusuoid_aprovacao=cd_usuario) AS aprovador,
				        rasttag_pacote AS tag,
				        rastsdescricao AS status,
				  (SELECT 
				  		obrobrigacao
				   FROM 
				   		obrigacao_financeira
				   WHERE 
				   		obrdt_exclusao IS NULL
				     AND 
				     	obrrastoid=rastoid limit 1) AS obrigacao_financeira,
                    rastobrigacao_unica_cliente
				FROM 
					 rastreamento_tag,
				     rastreamento_tag_status
				WHERE 
					rastrastsoid=rastsoid
				AND 
					rastoid=$rastoid;";
	
		
		if (!$resultado = pg_query($this->conn, $sql)) {
			throw new Exception("Falha ao Visualizar  detalhe do Pacote.");
		}
	
		if (pg_num_rows($resultado) > 0) {
			return pg_fetch_all($resultado);
		} else {
			throw new Exception("Nenhum registro encontrado.");
		}
	
	}
	
	
	/**
	 * Funcionalidades do Pacote
	 * @param unknown $rastoid
	 * @throws Exception
	 * @return multitype:
	 */
	public function consultarDetalheFuncionalidadesPacote($rastoid) {
	
		$sql = "SELECT
       				rastdescricao AS descricao,		
       				to_char(rastdt_aprovacao,'dd/mm/yyyy hh:mm') AS data_aprovacao,
				  (SELECT 
				  		nm_usuario
				   FROM 
				   		usuarios
				   WHERE
					    rastusuoid_aprovacao=cd_usuario) AS aprovador,
					    rasttag_funcionalidade AS tag,
					    rastsdescricao AS status,
				  (SELECT 
				  		obrobrigacao
				   FROM 
				   		obrigacao_financeira
				   WHERE 
				   		obrdt_exclusao IS NULL
				     AND 
				     	obrrastoid=rastoid limit 1) AS obrigacao_financeira
				FROM 
					 rastreamento_tag,
				     rastreamento_tag_status
				WHERE 
					 rastrastsoid=rastsoid
				  AND 
				  	 rasttipo='F'
				  AND 
				  	rasttag_pacote=(
				  	SELECT 
				  		a.rasttag_pacote 
				  	FROM 
				  		rastreamento_tag as a 
				  	 WHERE 
				  		a.rastoid=$rastoid);";
	
			
		if (!$resultado = pg_query($this->conn, $sql)) {
			throw new Exception("Falha ao consultar Funcionalidades do Pacote.");
		}
	
		if (pg_num_rows($resultado) > 0) {
			return pg_fetch_all($resultado);
		} else {
			throw new Exception("Nenhum registro encontrado.");
		}
	
	}
	
	/**
	 * @see Vincula a obrigação com um produto
	 *
	 * @param int $obroid
	 * @param int $usuoid
	 *
	 * @throws Exception
	 *
	 * @return int id do produto
	 */
	public function	inserirProdutoParaObrigacao($obroid, $obrobrigacao, $usuoid){
		$obrobrigacao = utf8_decode($obrobrigacao);
		$sqli = "INSERT INTO produto (prdproduto, prdusuoid, prdtp_cadastro, prdgrmoid)
		VALUES ('$obrobrigacao', $usuoid, 'S', 13)
		RETURNING prdoid;";
		if (!$resultado = pg_query($this->conn, $sqli)) {
			throw new Exception("Falha ao inserir novo produto.");
		}
	
		return $prdoid = pg_fetch_result($resultado, 0, 'prdoid');
	}
	
	/**
	 * @see Vincular Produto a obrigação financeira
	 *
	 * @param int $prdoid
	 * @param int $obroid
	 *
	 * @throws Exception
	 * @return boolean
	 */
	public function vincularProdutoParaObrigacao($prdoid, $obroid){
		$sqlu = "UPDATE obrigacao_financeira SET obrprdoid = $prdoid WHERE obroid = $obroid;";

		if (!$resultado = pg_query($this->conn, $sqlu)) {
			throw new Exception("Falha ao vincular o produto para obrigação financeira.");
		}
		return true;
	}

	/**
	 * Abre a transação
	 */
	public function begin(){
		pg_query($this->conn, 'BEGIN');
	}

	/**
	 * Finaliza um transação
	 */
	public function commit(){
		pg_query($this->conn, 'COMMIT');
	}

	/**
	 * Aborta uma transação
	 */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK');
	}


}
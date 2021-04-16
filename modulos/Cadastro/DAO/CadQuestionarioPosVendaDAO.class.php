<?php

/**
 * @file CadQuestionarioPosVendaDAO.class.php
 * @author Paulo Henrique da Silva Junior
 * @version 25/06/2013
 * @since 25/06/2013
 * @package SASCAR CadQuestionarioPosVendaDAO.class.php
 */

/*
 * Acesso a dados para o módulo Tipos de Segmento de Mercado
*/
class CadQuestionarioPosVendaDAO {
	
	/**
	 * Conexão com o banco de dados
	 * @var resource
	 */
	private $conn;	
	
	/**
	 * Construtor, recebe a conexão com o banco
	 * @param resource $connection
	 * @throws Exception
	 */
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

    public function getTipoItem() {
    	return array(
    				'I' 	=> array('Apenas Informativo', 'padrao', 'sempeso'),
	                'A' 	=> array('Avaliação', 'padrao', 'compeso' ),
	                'O'	 	=> array('Observação', 'padrao', 'sempeso'),
                    'V' 	=> array('Nota', 'padrao', 'compeso'),
                    'OC'	=> array('Observação Curta', 'padrao', 'sempeso'),
					'AM'	=> array('Avaliação Múltipla', 'radio', 'compeso'),
					'VE'	=> array('Verificação', 'checkbox', 'compeso'),
					'E'		=> array('Escolha', 'select', 'compeso')
                    );
    }


    public function getOcorrencia () {
    	$sql = "
            SELECT 
            	rhtoid, rhtdescricao
            FROM 
            	representante_historico_tipo 
            WHERE 
            	rhtdt_exclusao IS NULL 
            ORDER BY
            	rhtabreviacao
            ";

        $rs = pg_query($this->conn, $sql);
        
        $result = array(
        	);
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[pg_fetch_result($rs, $i, 'rhtoid')] = pg_fetch_result($rs, $i, 'rhtdescricao');
        }
        return $result;    	
    }


    public function getQuestionarioItem ($pqioid = null) {
    	$sql = "SELECT 
    				pqioid, pqipsqoid, pqiitem_topico, pqitipo_item, 
		    		pqipeso, pqiavalia_representante, pqirhtoid, 
       				pqidescricao_ocorrencia, pqiitem_ordem, pqinome_imagem
  				FROM 
  					posvenda_questionario_item
  				WHERE
  					pqioid = '$pqioid'
  					AND pqidt_exclusao IS NULL
				;";    	

        $result = pg_query($this->conn, $sql);
        
        return pg_fetch_object($result);
    }


    public function getTipoPesquisa ($pstoid) {
    	$sql = "SELECT 
    				psttitulo, pstdescricao, pstvinculo_servico
  				FROM 
  					posvenda_tipo
  				WHERE
  					pstoid = '$pstoid'
  					AND pstdt_exclusao IS NULL
  				LIMIT 1
				;";    	
		$result = pg_query($this->conn, $sql);
        
        return pg_fetch_object($result);  
    }


    public function getItemOpc ($pqioid = null) {
    	$sql = "SELECT 
    				pqiooid, pqio_topico, pqiopqioid
  				FROM
  					posvenda_questionario_item_opc
  				WHERE
  					pqiopqioid = '$pqioid'
  					AND pqiodt_exclusao IS NULL
  				ORDER BY
  					pqiooid ASC
				;";    	
		$rs = pg_query($this->conn, $sql);

        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[pg_fetch_result($rs, $i, 'pqiooid')] = pg_fetch_result($rs, $i, 'pqio_topico');
        }
        return $result;    
    }


	public function getPesoTotal($questaoid) {
		  $query =	"SELECT 
	        				psvpeso AS peso 
	        			FROM 
	        				posvenda 
	        			WHERE 
	        				psvoid = '".$questaoid."'";
		    $rs = pg_query($this->conn, $query);
		    return pg_fetch_result($rs,0,"peso");	        
	}


	public function excluirImagensAntigas() {
		$arquivosBanco = array();
		$arquivosPasta = array();
		$arquivosExcluir = array();
		$query =	"SELECT 
	        			pqinome_imagem
	        		FROM 
	        			posvenda_questionario_item
	        		WHERE 
	        			pqinome_imagem is not null
	        		";
 		$rs = pg_query($this->conn, $query);
        $excluido = false;

        $arquivosBanco = pg_fetch_all_columns($rs, 0);
		foreach (new DirectoryIterator(_SITEDIR_.'/arq_questionario') as $fileInfo) {
		    if($fileInfo->isDot()) continue;
		    $arquivosPasta[] = $fileInfo->getFilename();
		}

		$arquivosExcluir = array_diff($arquivosPasta, $arquivosBanco);
		if (count($arquivosExcluir) > 0)
		{
			foreach ($arquivosExcluir as $arq) {
	        	$arquivo = _SITEDIR_.'/arq_questionario/'.$arq;
	        	if (is_file($arquivo))
	        	{
	        		$excluido = unlink($arquivo);	
	        	}
	        }	
		}
        return $excluido;
	}


    public function inserirDados($params) {
        

    	$resultado = array();

	    if($params['pqipeso'] == '')
	    {
	    	$params['pqipeso'] = 0;
	    }
	        
	    if($params['pqitipo_item'] == "O" || $params['pqitipo_item'] == "I" || $params['pqitipo_item'] == "OC")
	    {
	        $params['pqiavalia_representante'] = 'FALSE';
	        $params['pqipeso'] = 0;
	    }

		try{
	        pg_query($this->conn, "BEGIN");
	        
	        if($params['pqiitem_topico'] == '')
	        {
	        	throw new Exception ("Informe o campo Descrição.");
	        }
	        if($params['pqiitem_ordem'] == '')
	        {
	        	throw new Exception ("Informe o campo Ordem.");
	        }

	        if(!$params['pqirhtoid'] && $params['pqitipo_item'] == "A" && $params['pqiavalia_representante'] == true){
				throw new Exception ("Informe o campo Tipo Ocorrência.");
			}

			if ($params['pqirhtoid'] == '31' && $params['pqitipo_item'] == 'A')
			{
				$query =	"SELECT 
	        				pqirhtoid, pqitipo_item 
	        			FROM 
	        				posvenda_questionario_item, posvenda_questionario, posvenda 
	        			WHERE 
	        				psqpsvoid = psvoid 
	        				AND pqipsqoid = psqoid 
	        				AND psvoid = '".$params['questaoid']."' 
	        				AND psvdt_exclusao is null 
	        				AND psqdt_exclusao is null 
	        				AND pqidt_exclusao is null 
	        				AND pqirhtoid = '31'
	        				AND pqitipo_item = 'A'";
				if($params['pqioid'] != '') {
					$query .= " AND pqioid <> '".$params['pqioid']."'";
				}	        				
	        	$query .= ";";
	        	$rs = pg_query($this->conn, $query);
		    	$validaReenvio = pg_num_rows($rs);
		    	if ($validaReenvio > 0)
		    	{
		    		throw new Exception ("Só pode haver um tipo de item Avaliação com Tipo Ocorrência Reenvio de Email.");
		    	}
			}
		

	        $query =	"SELECT 
	        				sum(pqipeso) AS peso 
	        			FROM 
	        				posvenda_questionario_item, posvenda_questionario, posvenda 
	        			WHERE 
	        				psqpsvoid = psvoid 
	        				AND pqipsqoid = psqoid 
	        				AND psvoid = '".$params['questaoid']."' 
	        				AND psvdt_exclusao is null 
	        				AND psqdt_exclusao is null 
	        				AND pqidt_exclusao is null ";
			if($params['pqioid'] != '') {
				$query .= " AND pqioid <> '".$params['pqioid']."'";
			}	        				
	        $query .= ";";
		    $rs = pg_query($this->conn, $query);
		    $soma = pg_fetch_result($rs,0,"peso");
		    $soma += $params['pqipeso'];

		    $pesoTotal = $this->getPesoTotal($params['questaoid']);

		    if(($soma) > $pesoTotal)
		    {
		    	throw new Exception ("A soma dos pesos das questões é de [" . $soma . "], ultrapassaram o limite de [".$pesoTotal."]");
		    }

	        $query =	"SELECT 
	        				pqiitem_ordem 
	        			FROM 
	        				posvenda_questionario_item, posvenda_questionario, posvenda 
	        			WHERE 
	        				psqpsvoid = psvoid 
	        				AND pqipsqoid = '".$params['questionarioid']."'  
	        				AND pqiitem_ordem  = '".$params['pqiitem_ordem']."' 
	        				AND psvdt_exclusao is null 
	        				AND psqdt_exclusao is null 
	        				AND pqidt_exclusao is null ";
			if($params['pqioid'] != '') {
				$query .= " AND pqioid <> '".$params['pqioid']."'";
			}
			$query .= ";";

    		
		    $rs = pg_query($this->conn, $query);
		    $ordem = pg_num_rows($rs);
		    if(($ordem) > 0)
		    {
		    	throw new Exception ("Ordem já existente");
		    }


		    if ($params['pqioid'] != '')
		    {

		    	$sql = "UPDATE
		    				posvenda_questionario_item
		    			SET
		    				pqiitem_topico = '" . utf8_decode($params['pqiitem_topico']) . "',
		    				pqitipo_item = '" . $params['pqitipo_item'] . "',
		    				pqipeso = '" . $params['pqipeso'] . "', 
		    				pqiavalia_representante = ". $params['pqiavalia_representante'] . ",";

				if ($params['pqirhtoid'] != '')
				{
					$sql.= "pqirhtoid = ".$params['pqirhtoid']." ,";
				}
				if ($params['arquivo'] != '')
				{
					$sql.= "pqinome_imagem = '".$params['arquivo']."' ,";
				}
				$sql .= "	pqidescricao_ocorrencia = '".utf8_decode($params['pqidescricao_ocorrencia'])."',
	            		 	pqiitem_ordem = '".$params['pqiitem_ordem']."'
	            		WHERE pqioid = '".$params['pqioid']."'
	            		 ";
				if(!$sql_val = pg_query($this->conn, $sql)){
		        	throw new Exception ("Houve um erro ao atualizar o registro.");
		        }
		    } else {

				//caso nao tenha sido informado o tipo de ocorrencia, o insert sera sem este campo
				$sql = "INSERT INTO 
							posvenda_questionario_item
								(
									pqipsqoid, 
									pqidt_cadastro, 
									pqiitem_topico, 
									pqitipo_item, 
									pqipeso, 
									pqiavalia_representante, 
						";
				if ($params['pqirhtoid'] != '')
				{
					$sql.= "pqirhtoid, ";
				}
				if ($params['arquivo'] != '')
				{
					$sql.= "pqinome_imagem, ";
				}

				$sql.="pqidescricao_ocorrencia,
									pqiitem_ordem
								)
	            		VALUES (
	            					'".$params['questionarioid']."', 
	            					NOW(), 
	            					'" . utf8_decode($params['pqiitem_topico']) . "', 
	            					'" . $params['pqitipo_item'] . "', 
	            					'" . $params['pqipeso'] . "', 
	            					". $params['pqiavalia_representante'] . ",";
				if ($params['pqirhtoid'] !=	'')
				{
					$sql.= $params['pqirhtoid'].",";
				}
				if ($params['arquivo'] != '')
				{
					$sql.= "'".$params['arquivo']."',";
				}
	            $sql .= "'".utf8_decode($params['pqidescricao_ocorrencia'])."',
	            					'".$params['pqiitem_ordem']."'
	            				) returning pqioid;"; 

	            if(!$sql_val = pg_query($this->conn, $sql)){
		        	throw new Exception ("Houve um erro ao cadastrar o registro.");
		        }
		        $pqioid = pg_fetch_result($sql_val,0,"pqioid");

		        if ($params['salvaQuestionario'] == 'radio'){
		        	$sql = '';
		        	for ($i = 1; $i <= $params['numcampos']; $i++) {
		        		$sql .="INSERT INTO
		        						posvenda_questionario_item_opc
		        							(
		        								pqiopqioid,
		        								pqio_topico,
		        								pqiodt_cadastro
		        							)
									VALUES (
												'".$pqioid."',
												'".$i."',
												NOW()
										);
		        						";
		        	}
		        	if(!$sqlOpc = pg_query($this->conn, $sql)){
		        		throw new Exception ("Houve um erro ao cadastrar as opções.");
		        	}
		        } else if ($params['salvaQuestionario'] == 'checkbox' || $params['salvaQuestionario'] == 'select'){
		        	$sql = '';
		        	if ($params['numcampos'] == '')
		        	{
		        		throw new Exception ("Pelo menos um campo deve ser adicionado.");
		        	} else {
		        		foreach ($params['numcampos'] as $chave => $valor) {
		        			$sql .="INSERT INTO
		        						posvenda_questionario_item_opc
		        							(
		        								pqiopqioid,
		        								pqio_topico,
		        								pqiodt_cadastro
		        							)
									VALUES (
												'".$pqioid."',
												'".utf8_decode($valor)."',
												NOW()
										);
		        						";
		        		}
		        		if(!$sqlOpc = pg_query($this->conn, $sql)){
			        		throw new Exception ("Houve um erro ao cadastrar as opções.");
			        	}
		        	}
		        }
	    	}
		    
		    $mensagem = "A soma dos pesos atual é [" . $soma . "] ";
			$comboStatus = '';
		    if($soma == $pesoTotal) {
	            $comboStatus .= '<option value="P" >Pendente</option>';
			    $comboStatus .= '<option value="I" >Inativo</option>';
			    $comboStatus .= '<option value="A" >Ativo</option>';
			} else {
			    $comboStatus .= '<option value="P" >Pendente</option>';
			}
			$resultado['comboStatus'] = $comboStatus;
			$status = 'SUCESSO';

			pg_query($this->conn, "END");
		}
		catch (Exception $e) {
	        pg_query($this->conn, "ROLLBACK");
	        $mensagem = $e->getMessage();
	        $status = 'ERRO';
	    }

	    $resultado['mensagem'] = utf8_encode($mensagem);
	    $resultado['status'] = utf8_encode($status);

	    return $resultado;

        
    }
	

	
}
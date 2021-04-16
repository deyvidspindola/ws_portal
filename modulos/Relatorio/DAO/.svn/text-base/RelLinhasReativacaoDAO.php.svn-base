<?php
/**
 * CAMADA DE PERSISTÊNCIA DO MÓDULO RELATÓRIO DE LINHAS PARA REATIVAÇÃO
 **/
class RelLinhasReativacaoDAO
{
	//Objeto de conexao com o banco de dados
	private $conn;
	//usuario logado
	private $usuoid;
	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	public function __construct($conn) {
		//Seta a conexao na classe
		$this->conn = $conn;
		$this->usuoid = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

		//Se nao tiver nada na sessao, a execucao vem do CRON
		if(empty($this->usuoid)) {
			$this->usuoid = 2750;
		}
	}


    /**
	* Recupera os os status da linha 
	* @author   Thomas de Lima
	* @return array/stdClass com os status possíveis da linha
	*/
	public function recuperarStatusLinha() {

		$sql = "SELECT csloid, cslstatus
				FROM celular_status_linha
				WHERE cslexclusao IS NULL 
				AND csloid IN (2,22,25,26)
				ORDER BY cslstatus";

		$rs = $this->executarQuery($sql);
		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return (object)$retorno;
	}

	/**
	* Recupera os status do contrato
	* @author   Thomas de Lima
	* @return array/stdClass com os status possíveis do contrato
	*/
	public function recuperarStatusContrato() {

		$retorno = array();

		$sql = "SELECT csioid, csidescricao FROM contrato_situacao WHERE csiexclusao IS NULL ORDER BY csidescricao";
		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return (object)$retorno;
	}

	/**
	* Recupera os dados Cliente
	* Request: AJAX
	*/
	public function recuperarCliente($parametros) {

		$retorno = array();
		$subQuery = '';
		$join = '';

		if(!empty($parametros->tipo_pessoa)){
			$subQuery = "
				,clicformacobranca
				,clicagencia
        		,clicconta
        		,cliccartao
				,forccfbbanco
				,clidia_vcto
				";

			$join = "
					LETF JOIN
						cliente_cobranca ON (clicclioid = clioid AND clicexclusao IS NULL)
					LEFT JOIN
				  		forma_cobranca ON (forcoid = clicformacobranca AND forcexclusao IS NULL)
					";
		}

		$sql = "
			SELECT
		        clioid,
		        clinome,
		        (CASE WHEN clitipo = 'J' THEN
		        	clino_cgc
		        ELSE
		        	clino_cpf
		        END) as cpf_cnpj
				".$subQuery."
			FROM
				clientes
			".$join."
			WHERE
				clidt_exclusao IS NULL
 			AND";

		if(empty($parametros->tipo_pessoa)) {
			$sql .= " clinome ILIKE '". $parametros->texto . "%'";
		} else {

			if($parametros->tipo_pessoa == 'J'){
				$sql .= " clino_cgc::VARCHAR ILIKE '". floatval($parametros->texto) . "%'";
			} else {
				$sql .= " clino_cpf::VARCHAR ILIKE '". floatval($parametros->texto) . "%'";
			}
		}

		$sql .= "
 			ORDER BY
 				clinome
			LIMIT 10
			";

		//echo "<pre>";var_dump($sql);echo "</pre>";exit();

		if(!$rs = pg_query($this->conn,$sql)){
			return $retorno;
		}

	 	if ($rs = pg_query($this->conn, $sql)) {

            $i = 0;
            while ($tupla = pg_fetch_object($rs)) {
                $retorno[$i]['id'] = $tupla->clioid;
                if(empty($parametros->tipo_pessoa)) {
                	$retorno[$i]['label'] 	= utf8_encode($tupla->clinome);
                	$retorno[$i]['value'] 	= utf8_encode($tupla->clinome);
            	} else {
            		$retorno[$i]['label'] 	= utf8_encode($tupla->clinome);
                	$retorno[$i]['value'] 	= $tupla->cpf_cnpj;
                	$retorno[$i]['nome'] 	= utf8_encode($tupla->clinome);
                	$retorno[$i]['forma'] 	= $tupla->clicformacobranca;
                	$retorno[$i]['banco'] 	= $tupla->forccfbbanco;
                	$retorno[$i]['agencia'] = $tupla->clicagencia;
                	$retorno[$i]['conta'] 	= $tupla->clicconta;
                	$retorno[$i]['cartao'] 	= $tupla->cliccartao;
                	$retorno[$i]['dia_vcto']= $tupla->clidia_vcto;
            	}

                $i++;
            }

        }

		return $retorno;
	}



	/**
	 * Metodo para realizar a pesquisa de varios registros
	 * @author   Andre Zilz
	 * @param stdClass $parametros Filtros da pesquisa
	 * @param array $paginacao | LIMIT e OFFSET
	 * @param string $ordenacao | campos do ORDER BY
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarLinhasReativacao(stdClass $parametros, $paginacao = null, $ordenacao = null) 
	{

		$retorno = array();
		$sql = '';

		if(!isset($paginacao)) {
			$select = "COUNT(linreaoid) AS total_registros";
			$orderBy = "";

		} else {

			$select = "
				TO_CHAR(linreadt_cadastro,'dd/mm/yyyy hh24:mi')AS data_cadastro,
				linreaoid, 
				linreaclioid, 
				linreausuoid, 
				linrealinnumero as linha, 
				arano_ddd as ddd, 
				clinome as cliente, 
				u.nm_usuario as usuario,
				lincsloid, 
				cslstatus as statusLinha,
				equoid,
				equno_serie,
				connumero as contrato,
				csidescricao as statusContrato, 
				veiplaca as placa,
				tpcdescricao as tipoContrato
			";

			$orderBy = " ORDER BY ";
			$orderBy .= (!empty($ordenacao)) ? $ordenacao :  "linreadt_cadastro";
		}

		$sql = "
			SELECT
			".$select."
			FROM linha_reativacao lr 
			INNER JOIN linha l ON linrealinnumero = linnumero AND linreaaraoid = linaraoid
			INNER JOIN area a ON linreaaraoid = araoid 
			INNER JOIN clientes c ON linreaclioid = clioid
			INNER JOIN usuarios u ON linreausuoid = cd_usuario
			INNER JOIN celular_status_linha csl ON lincsloid = csloid
			LEFT JOIN equipamento e ON linrealinnumero = equno_fone AND linreaaraoid = equaraoid
			LEFT JOIN contrato ON equoid = conequoid
			LEFT JOIN contrato_situacao ON csioid = concsioid
			LEFT JOIN veiculo ON conveioid = veioid
			LEFT JOIN tipo_contrato ON tpcoid = conno_tipo

			WHERE lincsloid <> 1 
		    ";

		//Montando a cláusula WHERER
		$sql .=  $this->montarFiltros($parametros);

	    $sql .= $orderBy;

    	if (isset($paginacao->limite) && isset($paginacao->offset)) {
            $sql.= "
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "
            ";
        }

		$rs = $this->executarQuery($sql);

		while($row = pg_fetch_object($rs)){
			$retorno[] = $row;
		}

		return $retorno;
	}


	/**
	 * Metodo para realizar a pesquisa de varios registros para planilha CSV
	 * @author   Andre Zilz
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarLinhasReativacaoCsv(stdClass $parametros){

		$retorno = array();

		$sql = "SELECT linreaoid, 
					linreaclioid, 
					TO_CHAR(linreadt_cadastro,'dd/mm/yyyy hh24:mi')AS data_cadastro,
					linreausuoid, 
					linrealinnumero as linha, 
					arano_ddd as ddd, 
					clinome as cliente, 
					u.nm_usuario as usuario,
					lincsloid, 
					cslstatus as statusLinha,
					equoid,
					equno_serie,
					connumero as contrato,
					csidescricao as statusContrato, 
					veiplaca as placa,
					tpcdescricao as tipoContrato
					
				FROM linha_reativacao lr 
				INNER JOIN linha l ON linrealinnumero = linnumero AND linreaaraoid = linaraoid
				INNER JOIN area a ON linreaaraoid = araoid 
				INNER JOIN clientes c ON linreaclioid = clioid
				INNER JOIN usuarios u ON linreausuoid = cd_usuario
				INNER JOIN celular_status_linha csl ON lincsloid = csloid
				LEFT JOIN equipamento e ON linrealinnumero = equno_fone AND linreaaraoid = equaraoid
				LEFT JOIN contrato ON equoid = conequoid
				LEFT JOIN contrato_situacao ON csioid = concsioid
				LEFT JOIN veiculo ON conveioid = veioid
				LEFT JOIN tipo_contrato ON tpcoid = conno_tipo

				WHERE lincsloid <> 1 ";

		//Montando a cláusula WHERER
		$sql .=  $this->montarFiltros($parametros);
		$sql .= " ORDER BY linreadt_cadastro";

		$rs = $this->executarQuery($sql);

		while($row = pg_fetch_object($rs)){
			$retorno[] = $row;
		}

		return $retorno;
	}

	/**
	 * Montar a string com as condicionais da quey de pesquisa
	 * @param  stdClass $parametros
	 * @return string
	 */
	private function montarFiltros($parametros) {

		//Periodo
	    if (!empty($parametros->data_inicial) && !empty($parametros->data_final)) {
	        $sql .= " AND linreadt_cadastro BETWEEN '" . $parametros->data_inicial . " 00:00:00' AND '" . $parametros->data_final . " 23:59:59'";
	    }
	    //Status
	    if (!empty($parametros->linnumero)) {
	        $sql .= " AND linrealinnumero ='" . $parametros->linnumero . "'";
	    }
	    //Status da linha
	    if (!empty($parametros->csloid)) {
	        $sql .= " AND csloid ='" . $parametros->csloid . "'";
	    }
	    //Status do contrato
	    if (!empty($parametros->csioid)) {
	        $sql .= " AND csioid =" . intval($parametros->csioid);
	    }
	    //Cliente
	    if (!empty($parametros->clioid_pesq)) {
	        $sql .= " AND linreaclioid = " . intval($parametros->clioid_pesq);
	    }
	    //Contrato
	    if (!empty($parametros->connumero)) {
	        $sql .= " AND connumero =" . intval($parametros->connumero);
	    }
		//Placa
	    if (!empty($parametros->placa)) {
		     $sql .= " AND veiplaca ILIKE  '%".$parametros->placa."%'";
		}

		return $sql;
	}


	/**
	 * submete uma query
	 * @param  string $query
	 * @return recordset
	 */
    private function executarQuery($query) {

	    if(!$rs = pg_query($this->conn, $query)) {
	        throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	    }
	    return $rs;
    }

	/**
	 * Abre a transação
	 */
	public function begin(){
		pg_query($this->conn, 'BEGIN');
	}

	/**
	 * Finaliza um transacao
	 */
	public function commit(){
		pg_query($this->conn, 'COMMIT');
	}

	/**
	 * Aborta uma transacao
	 */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK');
	}


}
<?php
/**
 * CAMADA DE PERSISTÊNCIA DO MÓDULO RELATÓRIO DE LINHAS PARA REATIVAÇÃO
 **/
class RelEnvioResetEquipamentoDAO
{
	//Objeto de conexao com o banco de dados
	private $conn;
	//usuario logado
	private $usuoid;
	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	/**
	* Método construtor
	**/
	public function __construct($conn)
	{
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
	public function recuperarEquipamentosProjeto()
	{

		$sql = "SELECT eproid, eprnome
				FROM equipamento_projeto
				ORDER BY eprnome ASC";

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
	public function recuperarCliente($parametros)
	{

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
	 * @author Thomas Lima
	 * @param stdClass $parametros Filtros da pesquisa
	 * @param array $paginacao | LIMIT e OFFSET
	 * @param string $ordenacao | campos do ORDER BY
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarResultado(stdClass $parametros, $paginacao = null, $ordenacao = null) 
	{
		$retorno = array();
		$sql = '';

		if(!isset($paginacao)) {
			$select = "COUNT(enroid) AS total_registros";
			$orderBy = "";

		} else {

			$select = " veiplaca, 
						clinome, 
						eprnome, 
						TO_CHAR(enrdt_envio,'dd/mm/yyyy hh24:mi')AS data_envio_comando,
						CASE enrstatus_posicao
				           WHEN 'P' THEN 'Voltou posicionar'
				           WHEN 'N' THEN 'N�o voltou posicionar'
				           ELSE 'Aguardando resultado reset'
				        END AS status_posicao
						";
			$orderBy = " ORDER BY ";
			$orderBy .= (!empty($ordenacao)) ? $ordenacao :  "enrdt_envio";
		}

		$sql = "SELECT {$select}
				FROM   envio_reset 
				JOIN   veiculo ON enrveioid = veioid 
				JOIN   contrato ON conveioid = veioid 
				JOIN   clientes ON conclioid = clioid 
				JOIN   equipamento ON conequoid = equoid 
				JOIN   equipamento_versao ON eveoid = equeveoid 
				JOIN   equipamento_projeto ON eveprojeto = eproid 
				WHERE  1=1";

		//Montando a cl�usula WHERER
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
	 * Montar a string com as condicionais da quey de pesquisa
	 * @param  stdClass $parametros
	 * @return string
	 */
	private function montarFiltros($parametros)
	{
		//Periodo
	    if (!empty($parametros->data_inicial) && !empty($parametros->data_final)) {
	        $sql .= " AND enrdt_envio BETWEEN '" . $parametros->data_inicial . " 00:00:00' AND '" . $parametros->data_final . " 23:59:59'";
	    }
	    //Cliente
	    if (!empty($parametros->clioid_pesq)) {
	        $sql .= " AND clioid = " . intval($parametros->clioid_pesq);
	    }
	    //Equipamento
	    if (!empty($parametros->eproid)) {
	        $sql .= " AND eveprojeto ='" . $parametros->eproid . "'";
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
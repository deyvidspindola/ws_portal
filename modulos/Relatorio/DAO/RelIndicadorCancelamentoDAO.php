<?php

/**
 * Classe RelIndicadorCancelamentoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Relatorio
 * @author   GABRIEL PEREIRA <gabriel.pereira@meta.com.br>
 * 
 */
class RelIndicadorCancelamentoDAO {

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

/**
 * Método para realizar a pesquisa de varios registros
 * @param stdClass $parametros Filtros da pesquisa
 * @return array
 * @throws ErrorException
 */
public function pesquisarContratos(stdClass $parametros){

	$retorno = array();

	$sql = "SELECT
				csioid,
				csidescricao AS status,
				COUNT(connumero) AS qtd
			FROM 
				contrato
		--INNER JOIN
		--	equipamento_classe ON (eqcoid = coneqcoid)
			INNER JOIN
				contrato_situacao ON (concsioid = csioid)
			WHERE
				conclioid = " . intval($parametros->cliente_id) . "
			AND
				condt_cadastro BETWEEN '" . $parametros->data_de . " 00:00:00' AND '" . $parametros->data_ate . " 23:59:59'
			AND
				condt_exclusao IS NULL
			GROUP BY
				csioid
				ORDER BY
				csidescricao ASC";


		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$total = 0;
		$dados = array();
		while($row = pg_fetch_object($rs)){
			$total += $row->qtd;
			$dados[] = $row;
		}


		$totalItens = count($dados);
		$totalPercentual = 0;

		$i = 1;
		foreach ($dados as $key => $value) {

			if ($i == $totalItens) {

				$porcentagem = 100 - $totalPercentual;
				
				if (is_float($porcentagem)) {
					$value->porcentagem = floatval(substr($porcentagem, 0, strrpos($porcentagem,'.') +2));
				} else {
					$value->porcentagem = floatval($porcentagem);
				}

				
			} else {
				$porcentagem = ($value->qtd / $total) * 100;

 				if (is_float($porcentagem)) {
					$value->porcentagem = floatval(substr($porcentagem, 0, strrpos($porcentagem,'.') +2));
				} else {
					$value->porcentagem = floatval($porcentagem);
				}


				

				$totalPercentual += $value->porcentagem;
			}	

			$value->destacado = false;

			if (preg_match("/Rescisão/", $value->status)) {
				$value->destacado = true;
			}
			$value->porcentagem = str_replace('.', ',', $value->porcentagem);
			$retorno[] = $value;
			$i++;
		}

		return $retorno;
	}



	public function pesquisarClasseTermo(stdClass $parametros) {
		$retorno = array();

		$sql = "SELECT
					eqcoid,
					eqcdescricao AS classe,
					COUNT(connumero) AS qtd
				FROM 
					contrato
				INNER JOIN
					equipamento_classe ON (eqcoid = coneqcoid)
				WHERE
					conclioid = " . intval($parametros->cliente_id) . "
				AND
					condt_cadastro BETWEEN '" . $parametros->data_de . " 00:00:00' AND '" . $parametros->data_ate . " 23:59:59'
				AND
					condt_exclusao IS NULL
				GROUP BY
					eqcoid
				ORDER BY
					eqcdescricao ASC";


		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		$total = 0;
		$dados = array();
		while($row = pg_fetch_object($rs)){
			$total += $row->qtd;
			$dados[] = $row;
		}


		$totalItens = count($dados);
		$totalPercentual = 0;

		$i = 1;
		foreach ($dados as $key => $value) {

			if ($i == $totalItens) {
				$porcentagem = 100 - $totalPercentual;
				
				if (is_float($porcentagem)) {
					$value->porcentagem = floatval(substr($porcentagem, 0, strrpos($porcentagem,'.') +2));
				} else {
					$value->porcentagem = floatval($porcentagem);
				}

			} else {
				$porcentagem = ($value->qtd / $total) * 100;

				if (is_float($porcentagem)) {
					$value->porcentagem = floatval(substr($porcentagem, 0, strrpos($porcentagem,'.') +2));
				} else {
					$value->porcentagem = floatval($porcentagem);
				}

				$totalPercentual += $value->porcentagem;
			}	

			$value->porcentagem = str_replace('.', ',', $value->porcentagem);
			$retorno[] = $value;
			$i++;
		}

		return $retorno;
	}



	public function pesquisarSugestoesReclamacoes(stdClass $parametros) {

		$retorno = array();

		$sql = "SELECT
					trsoid,
					trsdescricao,
					csugstatus,

					CASE 	WHEN csugstatus = 'P' THEN 'Pendente'
					WHEN csugstatus = 'C' THEN 'Concluído'
					WHEN csugstatus = 'E' THEN 'Em Andamento'
					WHEN csugstatus = 'A' THEN 'Aguardando Conclusão'
					WHEN csugstatus = 'L' THEN 'Aguardando Laudo'
					END AS status,

					COUNT(csugoid) AS qtd
				FROM
					cliente_sugestao 
				INNER JOIN
					tipo_recl_sugestao ON (trsoid = csugtrsoid)
				WHERE 
					csugclioid = " . intval($parametros->cliente_id) . "
				AND
					csugcadastro BETWEEN '" . $parametros->data_de . " 00:00:00' AND '" . $parametros->data_ate . " 23:59:59'
				AND
					csugexclusao IS NULL
				GROUP BY
					trsoid,
					csugstatus
				ORDER BY
					trsdescricao,
					csugstatus";

		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}


		$total = 0;
		$dados = array();
		while($row = pg_fetch_object($rs)){
			$total += $row->qtd;
			$dados[] = $row;
		}


		$totalItens = count($dados);
		$totalPercentual = 0;

		$i = 1;
		foreach ($dados as $key => $value) {

			if ($i == $totalItens) {
				$porcentagem = 100 - $totalPercentual;

				if (is_float($porcentagem)) {
					$value->porcentagem = floatval(substr($porcentagem, 0, strrpos($porcentagem,'.') +2));
				} else {
					$value->porcentagem = floatval($porcentagem);
				}

			} else {
				$porcentagem = ($value->qtd / $total) * 100;

				if (is_float($porcentagem)) {
					$value->porcentagem = floatval(substr($porcentagem, 0, strrpos($porcentagem,'.') +2));
				} else {
					$value->porcentagem = floatval($porcentagem);
				}
				
				$totalPercentual += $value->porcentagem;
			}	

			$value->porcentagem = str_replace('.', ',', $value->porcentagem);
			
			$retorno['grafico'][$value->trsoid] = $value;
			$retorno['grafico'][$value->trsoid]->total += $value->qtd;
			$retorno['tabela'][] = $value;


			$i++;
		}


		return $retorno;
	}


public function buscarMotivos(stdClass $parametros) {


	$retorno = array();

	$sql = "SELECT
				mtroid,
				mtrdescricao,
				COUNT(csugoid) AS qtd
			FROM
				cliente_sugestao 
			INNER JOIN
				tipo_recl_sugestao ON (trsoid = csugtrsoid)
			INNER JOIN
				motivo_reclamacao ON (mtroid  = csumtroid)
			WHERE 
				csugclioid = " . intval($parametros->cliente_id) . "
			AND
				csugstatus = '" . $parametros->status . "'
			AND
				csugtrsoid = " . intval($parametros->tipo) . "
			AND
				csugcadastro BETWEEN '" . $parametros->data_de . " 00:00:00' AND '" . $parametros->data_ate . " 23:59:59'
			AND
				csugexclusao IS NULL
			GROUP BY
				mtroid
			ORDER BY
				qtd DESC";

	if (!$rs = pg_query($this->conn, $sql)){
		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	}


	while ($row = pg_fetch_object($rs)) {
		$row->mtrdescricao = utf8_encode($row->mtrdescricao);
		$retorno[] = $row;
	}

	return $retorno;
}


/**
     * Buscar cliente por nome sendo ele PJ || PF
     * 
     * @param stdClass $parametros parametros para busca.
     * 
     * @return array $retorno
     */
public function buscarClienteNome($parametros) {

	$retorno = array();


	if (trim($parametros->nome) === '') {
		echo json_encode($retorno);
		exit;
	}

	$sql = "SELECT
	clioid,
	clinome
	FROM
	clientes
	WHERE
	clidt_exclusao IS NULL 
	AND
	clinome ILIKE '" . pg_escape_string($parametros->nome) . "%'

	ORDER BY
	clinome
	LIMIT 10";

	if ($rs = pg_query($this->conn, $sql)) {
		if (pg_num_rows($rs) > 0) {
			$i = 0;
			while ($objeto = pg_fetch_object($rs)) {
				$retorno[$i]['id'] = $objeto->clioid;
				$retorno[$i]['label'] = utf8_encode($objeto->clinome);
				$retorno[$i]['value'] = utf8_encode($objeto->clinome);
				$i++;
			}
		}
	}

	return $retorno;
}


public function buscarClienteNomeId($id) {
	$sql = "SELECT
				clinome
			FROM
				clientes
			WHERE
				clioid = " . intval($id) . "";

	if (!$rs = pg_query($this->conn, $sql)){
		throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
	}

	$clinome = pg_fetch_object($rs);


	return $clinome->clinome;

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
?>

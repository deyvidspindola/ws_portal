<?php

/**
 * Classe CadChamadasSeguradoraDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   Vinicius Senna <teste_desenv@sascar.com.br>
 * 
 */
class CadChamadasSeguradoraDAO {

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
	 * Busca tipos de combustiveis 
	 * @return [type] [description]
	 */
	public function buscaCombustivel() {

		$retorno = array();

		$sql = "SELECT
					psccombid,
					psccombdesc
				FROM 
					produto_seguro_combustivel
				ORDER BY psccombdesc ASC";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Busca uso do veiculo
	 * @return [type] [description]
	 */
	public function buscaUsoVeiculo() {

		$retorno = array();

		$sql = "SELECT
					psuvutilid,
					psuvutildesc
				FROM
					produto_seguro_utilizacao_veiculo
				ORDER BY psuvutildesc ASC";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Busca profissoes da seguradora
	 * @return [type] [description]
	 */
	public function buscaProfissoesSeguradora() {

		$retorno = array();

		$sql = "SELECT
					pspsprofid,
					pspsprofdesc
				FROM
					produto_seguro_profissoes_seguradora
				ORDER BY pspsprofdesc ASC";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;

	}

	/**
	 * Busca forma de pagamento
	 * @return [type] [description]
	 */
	public function buscaFormaPagamento() {

		$retorno = array();

		$sql = "SELECT
					--psfcseguradoracod,
					psfcsascarcod,
					psfcsascardesc
				FROM
					produto_seguro_forma_cobranca 
				ORDER BY psfcsascardesc ASC";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Busca Ids revenda para popular a combo
	 * @return [type] [description]
	 */
	public function buscaIdRevenda() {

		$retorno = array();

		$sql = "SELECT DISTINCT
					--pscoid,
					--psccorroid,
					psccodseg
				FROM
					produto_seguro_corretor ";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;

	}

	/**
	 * Busca id do corretor
	 * @param  [type] $psccodseg [description]
	 * @return [type]            [description]
	 */
	public function buscaIdCorretor($psccodseg) {

		$sql = "SELECT
					psccorroid,
					psccodseg
				FROM
					produto_seguro_corretor 
				WHERE
					psccodseg = '" . intval($psccodseg) ."' LIMIT 1";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if(pg_num_rows($rs) > 0) {
			$registro = pg_fetch_object($rs);
			return $registro->psccorroid;
		}

		return false;
	}

	/**
	 * Busca dados do cliente a partir de um determinado contrato
	 * @param  [type] $connumero [description]
	 * @return [type]            [description]
	 */
	public function buscaDadosClienteContrato($connumero) {

		$connumero = (int) $connumero;
		$retorno = array();

		$sql = "SELECT
					cli.clinome AS nome_cliente,
					psp.pspenvclisexo AS sexo,
					psp.pspenvcliestadocivil AS estado_civil,
					psp.pspenvcliprofissao AS profissa,
					to_char(cli.clidt_nascimento, 'DD/MM/YYYY') AS dt_nasc,
					psp.pspenvclippenome1 AS pep1,
					psp.pspenvclippenome2 AS pep2,
					psp.pspenvcliresddd AS ddd_res,
					psp.pspenvclirestelefone AS fone_res,
					psp.pspenvclicelddd AS ddd_cel,
					psp.pspenvcliceltelefone AS num_cel,
					psp.pspenvcliemail AS email,
					psp.pspenvcliendereco AS endereco,
					psp.pspenvcliendnumero AS endereco_num,
					psp.pspenvcliendcomplemento AS complemento,
					psp.pspenvcliendcidade AS cidade,
					psu.psuufdescricao AS uf,
					psp.pspenvveiplaca AS placa,
					psp.pspenvveichassi AS chassi,
					psp.pspenvveiutilizacao AS uti_vei,
					psp.pspenvveiutilizacao AS tipo_seguro,
					psfc.psfcsascarcod AS forma_pag,
					con.coneqcoid AS classe_produto_prop,
					--prp.prpcorroid AS id_corretor_intranet
					psc.pscoid AS id_corretor_intranet
				FROM
					contrato AS con
				INNER JOIN
					clientes AS cli
				ON cli.clioid = con.conclioid
				INNER JOIN
					proposta AS prp
				ON prp.prptermo = con.connumero
				INNER JOIN 
					produto_seguro_proposta  AS psp
				ON psp.pspconnumero = con.connumero
				INNER JOIN
					produto_seguro_forma_cobranca AS psfc
				ON CAST (psfc.psfcseguradoracod AS integer) = psp.pspenvformapagamento
				INNER JOIN
					produto_seguro_uf AS psu
				ON CAST (psu.psuufcodigo AS integer) = psp.pspenvclienduf
				LEFT JOIN
					produto_seguro_corretor AS psc
				ON psc.psccorroid = prp.prpcorroid
				WHERE
					con.connumero = ". $connumero ."
				ORDER BY psp.pspoid LIMIT 1 ";

		if (!$rs = pg_query($this->conn, $sql)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			foreach ($registro as $key => $value) {
				$registro->$key = utf8_encode($registro->$key);
			}
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Busca corretores siggo seguro
	 * @return [type] [description]
	 */
	public function buscaCorretoresSeguro() {

		$retorno = array();

		$query = "	SELECT 
						DISTINCT
						cor.corroid,
						cor.corrnome,
						psc.pscoid
					FROM 
						produto_seguro_corretor AS psc
					INNER JOIN
						corretor AS cor
					ON cor.corroid = psc.psccorroid
					WHERE
						psc.pscativo = TRUE
					AND
						psc.pscdt_exclusao IS NULL 
					ORDER BY cor.corrnome ASC";

		if (!$rs = pg_query($this->conn, $query)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		while($registro = pg_fetch_object($rs)) {
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Recupera numero da apolice gerada na seguradora.
	 * @param  [type] $connumero numero do contrato
	 * @return [type]            [description]
	 */
	public function recuperaApoliceGerada($connumero) {

		$connumero = (int) $connumero;

		$query = "SELECT 
						psaretapolicecd 
					FROM 
						produto_seguro_apolice 
					WHERE psaconnumero  = " . $connumero . " ORDER BY psaoid DESC LIMIT 1";

		if (!$rs = pg_query($this->conn, $query)) {
			throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
		}

		if(pg_num_rows($rs) > 0) {
			$registro = pg_fetch_object($rs);
			return $registro->psaretapolicecd;
		}

		return false;
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

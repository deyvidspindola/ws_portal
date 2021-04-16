<?php

/**
 * Classe CadDadosEquipamentoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   Leandro Alves Ivanaga <leandro.ivanaga@meta.com.br> <leandro.ivanaga.ext@sascar.com.br>
 *
 */
class CadDadosEquipamentoDAO {

	/** Conexão com o banco de dados */
	private $conn;

	/** Usuario logado */
	private $usarioLogado;

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	public function __construct($conn) {

		//Seta a conexao na classe
        $this->conn = $conn;
        $this->usarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO
        if(empty($this->usarioLogado)) {
            $this->usarioLogado = 2750;
        }
	}

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisar(stdClass $parametros){

		$retorno = array();

		$sql = "SELECT 
                    v.veioid, v.veiplaca, v.veichassi, c.connumero, cli.clinome, equesn, equno_serie, ev.eveversao, ep.eprnome
				FROM 
					veiculo AS v
				JOIN
					contrato AS c ON c.conveioid = v.veioid
				JOIN
					equipamento AS e ON e.equoid = c.conequoid
				JOIN
					equipamento_versao AS ev ON ev.eveoid = e.equeveoid
				JOIN
					equipamento_projeto AS ep ON ep.eproid = ev.eveprojeto
				JOIN
					clientes AS cli ON cli.clioid = c.conclioid
				WHERE 
					1 = 1
                ";
				// AND (ep.eprnome ilike 'MTC700' or ep.eprnome ilike 'LMU4230')
                // ep.eprnome ilike 'MTC700'

        if ( isset($parametros->veioid) && !empty($parametros->veioid) ) {
            $sql .= " AND
                        veioid = " . pg_escape_string( $parametros->veioid );
        }
        if ( isset($parametros->placa) && !empty($parametros->placa) ) {
            $sql .= " AND
                        veiplaca ILIKE '%" . pg_escape_string( $parametros->placa ) . "%'";
        }
        if ( isset($parametros->chassi) && !empty($parametros->chassi) ) {
            $sql .= " AND
                        veichassi = '" . pg_escape_string( $parametros->chassi ) . "'";
        }
        if ( isset($parametros->clioid) && !empty($parametros->clioid) ) {
            $sql .= " AND
                        clioid = " . pg_escape_string( $parametros->clioid );
        }
        // if ( isset($parametros->cliente) && !empty($parametros->cliente) ) {
        //     $sql .= " AND
        //                 clinome ILIKE '%" . pg_escape_string( $parametros->cliente ) . "%'";
        // }
        if ( isset($parametros->contrato) && !empty($parametros->contrato) ) {
            $sql .= " AND
                        connumero = " . pg_escape_string( $parametros->contrato );
        }

        $sql .= " ORDER BY eprnome ASC, veioid DESC";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Método para realizar a pesquisa do consolidado do cliente
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarConsolidado(stdClass $parametros){

		$retorno = array();

		$sql = "SELECT 
                    cli.clioid, cli.clinome, COUNT(v.veioid) AS total
				FROM 
					veiculo AS v
				JOIN
					contrato AS c ON c.conveioid = v.veioid
				JOIN
					equipamento AS e ON e.equoid = c.conequoid
				JOIN
					equipamento_versao AS ev ON ev.eveoid = e.equeveoid
				JOIN
					equipamento_projeto AS ep ON ep.eproid = ev.eveprojeto
				JOIN
					clientes AS cli ON cli.clioid = c.conclioid
				WHERE 
					1 = 1
                ";

        if ( isset($parametros->cliente) && !empty($parametros->cliente) ) {
            $sql .= " AND
                        clinome ILIKE '%" . pg_escape_string( $parametros->cliente ) . "%'";
        }
         if ( isset($parametros->veioid) && !empty($parametros->veioid) ) {
            $sql .= " AND
                        veioid = " . pg_escape_string( $parametros->veioid );
        }
        if ( isset($parametros->placa) && !empty($parametros->placa) ) {
            $sql .= " AND
                        veiplaca ILIKE '%" . pg_escape_string( $parametros->placa ) . "%'";
        }
        if ( isset($parametros->chassi) && !empty($parametros->chassi) ) {
            $sql .= " AND
                        veichassi = '" . pg_escape_string( $parametros->chassi ) . "'";
        }
        if ( isset($parametros->clioid) && !empty($parametros->clioid) ) {
            $sql .= " AND
                        clioid = " . pg_escape_string( $parametros->clioid );
        }
        if ( isset($parametros->contrato) && !empty($parametros->contrato) ) {
            $sql .= " AND
                        connumero = " . pg_escape_string( $parametros->contrato );
        }

        $sql .= " GROUP BY cli.clioid
	        	ORDER BY cli.clinome ASC";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/** 
     * Submete uma query a execucao do SGBD
     * @param  [string] $query
     * @return [bool]
     */
	private function executarQuery($query) {

        if(!$rs = pg_query($this->conn, $query)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $rs;
    }
}
?>

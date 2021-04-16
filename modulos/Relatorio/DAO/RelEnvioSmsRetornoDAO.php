<?php

/**
 * Classe RelEnvioSmsRetornoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Relatorio
 * @author   MARCELLO BORRMANN <marcello.b.ext@sascar.com.br>
 *
 */
class RelEnvioSmsRetornoDAO {

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

		$sql = "
				SELECT DISTINCT
					hsedata, 
					TO_CHAR(hsedata, 'DD/MM/YYYY') AS dt_envio,
					hsecodigo_retorno,
					hsetelefone,
					ordoid,
					(SELECT TO_CHAR(osadata, 'DD/MM/YYYY') FROM ordem_servico_agenda WHERE hseordoid = osaordoid ORDER BY osaoid DESC LIMIT 1) AS dt_agenda,
					clinome, 
					veiplaca,
					ostdescricao
				FROM
					historico_sms_envio
					INNER JOIN ordem_servico ON hseordoid = ordoid
					INNER JOIN ordem_servico_agenda ON hseordoid = osaordoid
					INNER JOIN contrato ON ordconnumero = connumero
					INNER JOIN clientes ON conclioid = clioid
					INNER JOIN veiculo ON conveioid = veioid
					LEFT JOIN os_tipo ON ostoid  = ordostoid
				WHERE
					TRIM(hsecodigo_retorno) <> '' 
					AND
					hsestatus = 'S'"; 
		
		// Período Envio SMS
		if(
			$parametros->dt_ref_busca == 1 && 
			(isset($parametros->dt_ini_busca) && trim($parametros->dt_ini_busca) != '') && 
			(isset($parametros->dt_fim_busca) && trim($parametros->dt_fim_busca) != '')
		){
			$sql .= "
					AND 
					hsedata BETWEEN '".$parametros->dt_ini_busca." 00:00:00' AND '".$parametros->dt_fim_busca." 23:59:59' ";
		}
		// Período Agendamento O.S.
		elseif(
			(isset($parametros->dt_ini_busca) && trim($parametros->dt_ini_busca) != '') &&
			(isset($parametros->dt_fim_busca) && trim($parametros->dt_fim_busca) != '')
		){
			$sql .= "
					AND 
					osadata BETWEEN '".$parametros->dt_ini_busca." 00:00:00' AND '".$parametros->dt_fim_busca." 23:59:59' ";
		}
		// Cód. Cancelamento
		if(isset($parametros->hsecodigo_retorno_busca) && trim($parametros->hsecodigo_retorno_busca) != ''){
			$sql .= "
					AND 
					hsecodigo_retorno ILIKE '".$parametros->hsecodigo_retorno_busca."' ";
		}
		// Nº O.S.
		if(isset($parametros->ordoid_busca) && !empty($parametros->ordoid_busca)){
			$sql .= "
					AND 
					ordoid = ".$parametros->ordoid_busca." ";
		}
		// Cliente
		if(isset($parametros->clinome_busca) && trim($parametros->clinome_busca) != ''){
			$sql .= "
					AND 
					clinome ILIKE '%".$parametros->clinome_busca."%' ";
		}
		//Cód. DDD + Nº Celular
		if((isset($parametros->endno_ddd_busca) && !empty($parametros->endno_ddd_busca)) || (isset($parametros->endno_cel_busca) && !empty($parametros->endno_cel_busca))){
			
			if (!isset($parametros->endno_ddd_busca) || empty($parametros->endno_ddd_busca)){
				$parametros->endno_ddd_busca = '%';
			}
			if (!isset($parametros->endno_cel_busca) || empty($parametros->endno_cel_busca)){
				$parametros->endno_cel_busca = '%';				
			}
			
			$sql .= "
					AND 
					hsetelefone ILIKE '".$parametros->endno_ddd_busca."".$parametros->endno_cel_busca."' ";
		}
		// Placa
		if(isset($parametros->veiplaca_busca) && trim($parametros->veiplaca_busca) != ''){
			$sql .= "
					AND 
					veiplaca ILIKE '".$parametros->veiplaca_busca."' ";
		}
		
		$sql .= "
				ORDER BY 
					hsedata, 
					clinome, 
					ordoid; ";
		
		//echo $sql."</br>";
		//exit;
		$rs = pg_query($this->conn,$sql);
		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		
		return $retorno;
	}

	/** Abre a transação */
	public function begin(){
		pg_query($this->conn, 'BEGIN');
	}

	/** Finaliza um transação */
	public function commit(){
		pg_query($this->conn, 'COMMIT');
	}

	/** Aborta uma transação */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK');
	}

	/** Submete uma query a execucao do SGBD */
	private function executarQuery($query) {

        if(!$rs = pg_query($query)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $rs;
    }
}
?>
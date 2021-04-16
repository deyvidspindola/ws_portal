<?php

/**
 * Classe responsável pela persistência de dados.
 *
 * @author Marcello Borrmann <marcello.b.ext@sascar.com.br>
 * @since 13/05/2016
 * @category Class
 * @package CancelamentoOsTecladoApagadoDAO
 */

class CancelamentoOsTecladoApagadoDAO { 
	
    private $conn;
	
    /**
     * Método para buscar dados de OSs 
     * que possuem apenas o serviço de 
     * assistência para teclado com defeito 
     * alegado "teclado apagado".
     *
     * @param $parametro1,$parametro2
     * @return object
     * @throws ErrorException
     */
    public function buscarDadosOS($parametro1,$parametro2){
    	
    	$sql = "SELECT
					ordoid, 
					orddt_ordem,
    				TO_CHAR(orddt_ordem,'DD/MM/YYYY') AS dt_ordem,
					ositoid,
					osaoid,
					CASE
						WHEN osadata IS NOT NULL THEN osadata
						ELSE NOW()
					END AS data_ultimo_agendamento, 
					clioid,
					cliemail,
					clinome,
					condt_exclusao,
					conveioid,
    				veiplaca,
					(SELECT array_to_string(array(SELECT osecemail FROM ordem_servico_email_contato WHERE osecordoid = ordoid), ';')) AS email_os
				FROM 
					ordem_servico 
					JOIN ordem_servico_item ON ositordoid = ordoid 
					JOIN ordem_servico_defeito ON osdfoid = ositosdfoid_alegado 
					LEFT JOIN ordem_servico_agenda ON osaordoid = ordoid 
    					AND osaoid = (SELECT osa.osaoid FROM ordem_servico_agenda osa WHERE osa.osaordoid = ordoid ORDER BY osa.osadata DESC, osa.osahora ASC LIMIT 1)
    					AND osaexclusao IS NULL
    					AND osausuoid_excl IS NULL   
					JOIN clientes ON clioid = ordclioid 
					LEFT JOIN cliente_classe ON clicloid = cliclicloid 
					JOIN contrato ON ordconnumero = connumero 
    				LEFT JOIN veiculo ON conveioid = veioid
				WHERE 
    				TRUE
					AND ositexclusao IS NULL
					AND ositstatus <> 'X'    
					AND ((SELECT COUNT(1) FROM ordem_servico_item osi WHERE osi.ositordoid = ordoid) <= 1 )
					AND coalesce(ordstatus, 0) <> 9  
					AND ordstatus IN (".$parametro1.") 
					AND osdfdescricao ILIKE '%teclado%apagado%' 
					AND (clicloid IS NULL OR clicloid = ".$parametro2."); 
				";
		
		//echo $sql."</br>";
		//exit;
		if (!$rs = pg_query($this->conn,$sql)) {
			throw new ErrorException("Erro ao buscar dados de OS.");
		}
		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		
		return $retorno;
    	
    }
	
    /**
     * Método para buscar dados dos domínios 
	 * da intranet referente as condições de 
     * busca das OSs.
     *
     * @param null
     * @return object
     * @throws ErrorException
     */
    public function buscarDadosDominio(){
		
		$sql = "
				SELECT 
					CASE WHEN valregoid = 60 THEN valvalor ELSE '' END AS ossoid,
					CASE WHEN valregoid = 61 THEN valvalor ELSE '' END AS clicloid
				FROM 
					dominio
					JOIN registro ON domoid = regdomoid
					JOIN valor ON valregoid = regoid
				WHERE
					domnome ILIKE 'Parametrizacoes Cancelamento de O.S. Teclado Apagado'
					AND valtpvoid IN (1,8);
				";
		
		//echo $sql."</br>";
		//exit;
		if (!$rs = pg_query($this->conn,$sql)) {
			throw new ErrorException("Erro ao buscar dados de domínio.");
		}
		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		
		return $retorno;
    	
    }
	
    /**
     * Método para buscar dados das OSs para as quais 
	 * não existam mensagens "recebidas e lidas" ou 
	 * "enviadas" no período entre abertura da OS e a
	 * data de agendamento.
     *
     * @param $veioid,$orddt_ordem,$data_ultimo_agendamento
     * @return int
     * @throws ErrorException
     */
    public function buscarDadosMSG($veioid,$orddt_ordem,$data_ultimo_agendamento, $clioid ){
		
		if($veioid == ''){
			
			$retorno = 0;
		} 
		else {
    	
	    	$sql = "
					SELECT 
						COUNT(1)
					FROM 
						mensagem_teclado_cli" . ( $clioid % 10 ) . "
					WHERE
						mentveioid = ".$veioid."
						AND mentdata BETWEEN '".$orddt_ordem."' AND '".$data_ultimo_agendamento."'
						AND (mentorigem = 'VS' OR ((mentorigem = 'LV' OR mentorigem = 'SV') AND mentdt_leitura IS NOT NULL)); 
					";
			
			//echo $sql."</br>";
			//exit;
			if (!$rs = pg_query($this->conn_gerenciadoras2,$sql)) {
				throw new ErrorException("Erro ao buscar mensagem de gerenciadora.");
			}
			
			$retorno = pg_fetch_result($rs,0,0);
		}
		
		return $retorno;
	}
	
    /**
     * Método para cancelar as OSs.
     *
     * @param $ordoid
     * @return boolean
     * @throws ErrorException
     */
    public function cancelarOS($ordoid){	

		$sql = "
				UPDATE
					ordem_servico
				SET
					ordstatus = 9, 
					ordaoamoid = 18 
				WHERE 
					ordoid = " . intval($ordoid) . ";
				";

		//echo $sql."</br>";
		//exit;
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao cancelar OS->" . intval($ordoid) . ".");
		}
		return true;
	}
	
    /**
     * Método para inserir histórico nas OSs.
     *
     * @param $ordoid
     * @return boolean
     * @throws ErrorException
     */
    public function inserirHistoricoOS($ordoid){	

		$sql = "
				INSERT INTO ordem_situacao (
					orsusuoid,
					orssituacao,
					orsdt_situacao,
					orsordoid)
				VALUES(
					2750, 
					'O.S. Cancelada pelo seviço de cancelamento de O.S. de assistência para teclado apagado',
					NOW(),
					" . intval($ordoid) . ");
				";
		
		//echo $sql."</br>";
		//exit;
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao inserir histórico de cancelamento na OS->" . intval($ordoid) . ".");
		}
		return true;
	}
	
    /**
     * Método para cancelar agendamento nas OSs.
     *
     * @param $osaoid
     * @return boolean
     * @throws ErrorException
     */
    public function cancelarAgendamentoOS($osaoid){	

		$sql = "
				UPDATE
					ordem_servico_agenda
				SET
					osaexclusao = NOW(), 
					osausuoid_excl = 2750
				WHERE 
					osaoid = " . intval($osaoid) . ";
				";
		
		//echo $sql."</br>";
		//exit;
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao cancelar agendamento ->" . intval($osaoid) . " da OS.");
		}
		return true;
	}
	
    /**
     * Método para cancelar os itens das OSs.
     *
     * @param $ositoid
     * @return boolean
     * @throws ErrorException
     */
    public function cancelarItemOS($ositoid){	

		$sql = "
				UPDATE
					ordem_servico_item
				SET
					ositstatus = 'X'
				WHERE 
					ositoid = " . intval($ositoid) . ";
				";
		
		//echo $sql."</br>";
		//exit;
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao cancelar item ->" . intval($ositoid) . " da OS.");
		}
		return true;
	}
	
    /**
     * Método para buscar dados do e-mail 
	 * que deverá ser enviado ao cliente
     *
     * @param $ositoid
     * @return boolean
     * @throws ErrorException
     */
    public function buscarDadosEmail(){	
		
		$sql = "
				SELECT 
					seeremetente,
					seecabecalho,
					seecorpo
				FROM 
					servico_envio_email
				WHERE
					seecabecalho ILIKE 'Cancelamento de Ordem de Servi%o Teclado Apagado';
				";
		
		//echo $sql."</br>";
		//exit;
		if (!$rs = pg_query($this->conn,$sql)){
			throw new ErrorException("Erro ao buscar dados de e-mail.");
		}
		 		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		
		return $retorno;
	}   

	
    public function __construct() {
        global $conn;
        $this->conn = $conn;
		
        global $dbstring_gerenciadoras2;
		$this->conn_gerenciadoras2 = pg_connect ($dbstring_gerenciadoras2);
    }

    public function __get($var) {
        return $this->$var;
    }

    /**
     * Abre a transação
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transação
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transação
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }

}

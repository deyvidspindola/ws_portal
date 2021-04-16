<?php

/**
 * Classe para persistência de dados deste modulo
 */




/**
 * @class Cancelamento de Pré-Vendas
 * @author Vanessa Rabelo <vanessa.rabelo@meta.com.br>
 * @since 16/09/2013
 * Camada de regras de negócio.
 */
 class CancelamentoAutomaticoPreReservaDAO  {

    private $conn;    
    

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    public function __get($var) {
    	return $this->$var;
    }
    

    
    /**
     * Recupera os dias parametrizados para cancelamento de requisição SEM APROVAÇÃO
     *
     * @return int
     */
	public function buscarHorasCancelamentoAgendamento(){	
			
		
		$solicitacoes = array();
			
		$sql = "SELECT 		
					ragoid,
					ragordoid,
					ragdt_cadastro,
  					EXTRACT(epoch FROM (NOW() - ragdt_cadastro))/3600 as horas
				FROM 		
					reserva_agendamento
				WHERE 		
					ragrasoid = '1'";
		
		$rs = pg_query($this->conn, $sql);
		
		for ($i = 0; $i < pg_num_rows($rs); $i++) {
			$solicitacoes[$i]['ragoid'] = pg_fetch_result($rs, $i, 'ragoid');
			$solicitacoes[$i]['ragordoid'] = pg_fetch_result($rs, $i, 'ragordoid');
			$solicitacoes[$i]['ragdt_cadastro'] = pg_fetch_result($rs, $i, 'ragdt_cadastro');
			$solicitacoes[$i]['horas'] = pg_fetch_result($rs, $i, 'horas');
		}
		
		return $solicitacoes;
		
	}
				
    
	/**
	 * Atualiza status das Pré-Reservas  para Cancelado.
	 *
	 * @param int $reqmoid
	 */
	public function atualizarCancelamentoAgendamento($ragoid){
	
		$ragoid		= (int)$ragoid;
		
        echo '<pre> Update de status';
		//Cancela a requisição
		echo $sql = "
                    UPDATE 	
                        reserva_agendamento
                    SET		
                        ragrasoid = '2'
				WHERE 	
					ragoid = ". $ragoid ."
				";
        
        echo '</pre>';
	
		return pg_affected_rows(pg_query($this->conn, $sql));		
	}

	
	/**
	 * Inserir um novo histórico na tabela ordem_situacao
	 * @param stdClass $dados Dados a serem gravados (ragordoid)
	 * @return \stdClass
	 * @throws Exception
	 * @throws ExceptionDAO
	 */
	public function gravarHistoricoCancelamento ($ragordoid){
	
		//Verifica se os atributos foram atribuidos
		if (!isset($ragordoid) || empty($ragordoid)){
			throw new Exception('Ordem de Serviço não informado');
		}
		
		 echo '<pre>Inserção de histórico:';
		//Prepara o SQL
		echo $sql = "
               INSERT INTO
                   ordem_situacao
                               (
                                  orsordoid,
                                  orsusuoid,
                                  orssituacao,					
                                  orsdt_situacao,					
								  orsstatus_bkp,
								  orsdt_agenda,
								  orshr_agenda,
								  orsstatus		
                                )
                   VALUES
                               (
                                   " . $ragordoid . ",
                                   2750,
                                   'As reservas desta ordem de serviço foram automaticamente canceladas devido ao não agendamento no período de 1 hora após as reservas serem efetuadas.',	
                                   NOW(),
                                   NULL,
                                   NULL,
                                   NULL,
                                   '110'					
                                  )";
		
      		$query   = pg_query($this->conn, $sql);
            echo '</pre>';
       		$retorno = (!$query) ? false : true;
		
		return $retorno;
  	}
	
	 public function atualizarCancelamentoItensEstoque(){
		
		//Cancela a Reserva e liberar os itens reservados quando a data do Agendamento já passou e os materiais/imobilizados não foram utilizados.
        $sql = "UPDATE reserva_agendamento 
                    SET ragrasoid = 2 
                FROM reserva_agendamento_item,
                     reserva_agendamento_status
                WHERE ragrasoid = rasoid
                    AND rasoid IN(1,3)
                    AND rairagoid = ragoid
                    AND ragosaoid IN (SELECT osaoid FROM ordem_servico_agenda WHERE osaordoid=ragordoid  AND osadata<now()::date)";

        pg_query($this->conn, $sql);

        //Cancela a Reserva e liberar os itens reservados quando a data do Agendamento já passou e os materiais/imobilizados não foram utilizados.
        $sql = "UPDATE reserva_agendamento_item 
                    SET raidt_exclusao = now() 
                FROM reserva_agendamento 
                WHERE raidt_exclusao IS NULL 
                    AND rairagoid = ragoid 
                    AND ragrasoid = 2 
                    AND ragosaoid IN (SELECT osaoid FROM ordem_servico_agenda WHERE osaordoid=ragordoid  AND osadata<now()::date)";

        pg_query($this->conn, $sql);

        //Cancela a Reserva e liberar os itens reservados quando a data do Agendamento já passou e os materiais/imobilizados não foram utilizados.
        $sql = "UPDATE reserva_agendamento SET 
                    ragrasoid = 2,
                    ragjustificativa_cancelamento ='CANCELAMENTO DE RESERVA AUTOMÁTICA(CRON),AGENDAMENTO EXPIRADO SEM UTILIZAÇÃO DA RESERVA.'
                FROM reserva_agendamento_item,
                     reserva_agendamento_status
                WHERE ragrasoid = rasoid
                    AND rasoid IN(1,3)
                    AND rairagoid = ragoid
                    AND ragosaoid IS NULL
                    AND ragordoid IN (SELECT osaordoid 
                              FROM ordem_servico_agenda 
                              WHERE ragosaoid IS NULL 
                              AND osaordoid=ragordoid 
                              AND osadata < now()::date 
                              AND raidt_cadastro::date = osacadastro::date)";

        pg_query($this->conn, $sql);

        //Cancela a Reserva e liberar os itens reservados quando a data do Agendamento já passou e os materiais/imobilizados não foram utilizados.
        $sql = "UPDATE reserva_agendamento_item 
                    SET raidt_exclusao = now() 
                FROM reserva_agendamento 
                WHERE raidt_exclusao IS NULL 
                    AND rairagoid = ragoid 
                    AND ragrasoid = 2 
                    AND ragosaoid IS NULL
                    AND ragordoid IN (SELECT osaordoid 
                            FROM ordem_servico_agenda 
                            WHERE osaordoid=ragordoid 
                            AND osadata<now()::date 
                            AND ragosaoid IS NULL 
                            AND raidt_cadastro::date = osacadastro::date)";

        pg_query($this->conn, $sql);

        //Atualiza status da Reserva para INstalado
		$sql = "UPDATE 
				   reserva_agendamento 
			    SET 
				   ragrasoid = 4 
			    WHERE 
				   ragrasoid IN (1,3) 
				   AND ragordoid IN (SELECT ordoid FROM ordem_servico WHERE ragordoid = ordoid AND ordstatus=3 )
				   AND ragdt_cancelamento IS NULL;";

        pg_query($this->conn, $sql);

				
		//Cancela itens Reservados já atendidos
		$sql = "UPDATE 
				   reserva_agendamento_item
			    SET 
				   raidt_exclusao = now()
                FROM
       			   reserva_agendamento 
			    WHERE 
				   rairagoid=ragoid 
				   AND ragrasoid = 4 
				   AND raidt_exclusao IS NULL";

        pg_query($this->conn, $sql);


        // Gera Histórico na O.S com Reserva Cancelada
        $sql = "INSERT INTO ordem_situacao (orsstatus,orsusuoid,orssituacao,orsordoid) 
                (SELECT 110,2750,'Reserva Cancelada em decorrência do Cancelamento da Remessa:'||raiesroid,ragordoid FROM reserva_agendamento,reserva_agendamento_item, estoque_remessa WHERE rairagoid=ragoid AND raiesroid = esroid AND esrersoid=4 AND ragrasoid IN (1,3));";

	 	pg_query($this->conn, $sql);

			
        // Cancela a Reserva de Estoque quando Reservado em Transito e a Remessa foi Cancelada
		$sql = "UPDATE 
					   reserva_agendamento 
				    SET 
					   ragrasoid = 2,
	                                   ragdt_cancelamento = now()
				    WHERE 
					   ragrasoid IN (1,3) 
					   AND ragoid IN (SELECT rairagoid FROM reserva_agendamento_item, estoque_remessa
	 				WHERE rairagoid=ragoid AND raiesroid = esroid AND esrersoid=4)
					   AND ragdt_cancelamento IS NULL;";

        pg_query($this->conn, $sql);

        // Cancela itens da Reserva quando atrelada a uma Remessa Cancelada
        $sql = "UPDATE 
					   reserva_agendamento_item 
				    SET 
                       raidt_exclusao = now(),
                       raijustificativa = 'Reserva Cancelada em decorrência do Cancelamento da Remessa:'||raiesroid			    
                    WHERE 
					   raidt_exclusao IS NULL  
					   AND raiesroid IN (SELECT esroid FROM reserva_agendamento, estoque_remessa
	 				WHERE rairagoid=ragoid AND raiesroid = esroid AND esrersoid=4 AND ragrasoid = 2)";

        return pg_affected_rows(pg_query($this->conn, $sql));
	}

 }


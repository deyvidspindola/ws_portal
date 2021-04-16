<?php
/**
 * Classe DAO do controle mensageria
 * @since 03/03/2016
 */
class MensageriaSmartAgendaDAO {


     const MENSAGEM_ERRO_PROCESSAMENTO                  = "Houve um erro no processamento dos dados.";
    const MENSAGEM_ERRO_ATUALIZAR_MENSAGEM             = "Houve um erro ao atualizar o status da mensagem.";
    const MENSAGEM_ERRO_ATUALIZAR_TENTATIVAS           = "Erro ao atualizar a quantidade de tentativas de processamento da mensagem.";
    const MENSAGEM_ERRO_BUSCAR_CONTEXTO                = "Ocorreu um erro ao recuperar o contexto da mensagem.";
    const MENSAGEM_ERRO_BUSCAR_PROPRIEDADES            = "Erro ao recuperar propriedades da mensagem.";
    const MENSAGEM_ERRO_BUSCAR_INSTALADOR              = "Erro ao recuperar informações do instalador";
    const MENSAGEM_ERRO_BUSCAR_REPRESENTANTE           = "Erro ao recuperar informações do representante";
    const MENSAGEM_ERRO_BUSCAR_CLIENTE                 = "Erro ao recuperar informações do cliente";
    const MENSAGEM_ERRO_BUSCAR_USUARIO                 = "Ocorreu um erro ao recuperar informações do usuário";
    const MENSAGEM_ERRO_HISTORICO_OS                   = "Ocorreu um erro ao salvar o histórico da OS.";
    const MENSAGEM_ERRO_ATUALIZAR_AGENDA               = "Ocorreu um erro ao atualizar o agendamento.";
    const MENSAGEM_ERRO_BUSCAR_AGENDAMENTO             = "Ocorreu um erro ao recuperar os dados do agendamento.";
    const MENSAGEM_ERRO_ATUALIZAR_ITEM_OS              = "Ocorreu um erro ao recuperar os dados do item da OS.";
    const MENSAGEM_ERRO_GRAVAR_CHECKLIST               = "Ocorreu um erro ao gravar o checklist.";
    const MENSAGEM_ERRO_MOTIVO_NOSHOW                  = "Ocorreu um erro ao recuperar dados do motivo No Show.";
    const MENSAGEM_ERRO_ATIVIDADE_STATUS               = "Ocorreu um erro ao buscar o status atividade/agenda.";
    const MENSAGEM_ERRO_STATUS_HISTORICO_OS            = "Ocorreu um erro ao buscar o status do histórico da OS.";
    const MENSAGEM_ERRO_GRAVAR_CHECKLIST_ITEM          = "Ocorreu um erro ao gravar item do checklist.";
    const MENSAGEM_ERRO_CHECKLIST_ITEM                 = "Ocorreu um erro ao recuperar dados do item da checklist";
    const MENSAGEM_ERRO_LAYOUT_EMAIL                   = "Ocorreu um erro ao recuperar layout do e-mail.";
    const MENSAGEM_AGENDAMENTO_NAO_ENCONTRADO          = "Agendamento não encontrado.";
    const MENSAGEM_ERRO_BUSCAR_ORDEM_SERVICO           = "Ocorreu um erro ao recuperar os dados da ordem de serviço.";
    const MENSAGEM_ORDEM_SERVICO_NAO_ENCONTRADO        = "Ordem serviço não encontrado.";
    const MENSAGEM_ERRO_ATUALIZAR_ORDEM_SERVICO        = "Ocorreu um erro ao atualizar a ordem de servico.";
    const MENSAGEM_ERRO_ATUALIZAR_ORDEM_SERVICO_AGENDA = "Ocorreu um erro ao atualizar o agendamento da OS.";

    const STATUS_FILA_AGUARDANDO  = 1;
    const STATUS_FILA_PROCESSANDO = 2;
    const STATUS_FILA_PROCESSADO  = 3;
    const STATUS_FILA_ABORTADO    = 4;
    const STATUS_FILA_ERRO        = 5;

	private $conn;

    public function __construct($conn) {
    	$this->conn = $conn;
    }

    public function buscaLogsErro() {

        $sql = '';
    	$retorno = new stdClass();

    	try {
    		$sql = "SELECT
    					smoid,
    					smsmcoid,
    					smsmsoid,
    					smid_ofsc,
    					smprioridade,
    					smdt_cadastro,
    					smnumero_tentativas
					FROM
						smartagenda_mensageria
					WHERE
						(smnumero_tentativas >= 5 OR smsmsoid = ".self::STATUS_FILA_PROCESSANDO.")
					AND
						smdt_cadastro < NOW() - INTERVAL '24 hours'";




			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception("Erro ao realizar a busca dos logs de erro.");
			}

			$retorno->resultado = $rs;

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function gravaMensageriaBacklog($smbsmcoid,$smbsmsoid,$smbid_ofsc,$smbprioridade,$smbdt_fila) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		$sql = "INSERT INTO
    					smartagenda_mensageria_backlog(
    						smbsmcoid,
    						smbsmsoid,
    						smbid_ofsc,
    						smbprioridade,
    						smbdt_fila
						)
					VALUES (
						':smbsmcoid',
						':smbsmsoid',
						':smbid_ofsc',
						':smbprioridade',
						':smbdt_fila'
					)RETURNING smboid;";

			$sql = str_replace(":smbsmcoid", pg_escape_string($smbsmcoid), $sql);
			$sql = str_replace(":smbsmsoid", pg_escape_string($smbsmsoid), $sql);
			$sql = str_replace(":smbid_ofsc", pg_escape_string($smbid_ofsc), $sql);
			$sql = str_replace(":smbprioridade", pg_escape_string($smbprioridade), $sql);
			$sql = str_replace(":smbdt_fila", pg_escape_string($smbdt_fila), $sql);



			$rs = pg_query($this->conn, $sql);

			if(!$rs || pg_affected_rows($rs) == 0) {
				throw new Exception("Erro ao cadastrar backlog da mensageria");
			}

			$id = pg_fetch_object($rs,0);
			$retorno->insert_id = $id->smboid;

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

    	return $retorno;
    }

   	public function buscaPropriedadesMensageria($smoid) {

        $sql = '';
   		$retorno = new stdClass();

    	try {

    		$sql = "SELECT
    					smpsmoid,
						smpchave,
						smpvalor
					FROM
						smartagenda_mensageria_propriedades
					WHERE
						smpsmoid = ':smoid'";

			$sql = str_replace(":smoid", pg_escape_string($smoid), $sql);



			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_PROPRIEDADES);
			}

			$retorno->resultado = $rs;

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

    	return $retorno;
   	}

    public function gravaMensageriaPropriedadesBacklog($smpbsmboid,$smpbchave,$smpbvalor) {

        $sql = '';
    	$retorno = new stdClass();

    	try {
    		$sql = "INSERT INTO
    					smartagenda_mensageria_propriedades_backlog (
    						smpbsmboid,
    						smpbchave,
    						smpbvalor
						)
					VALUES(
						':smpbsmboid',
						':smpbchave',
						':smpbvalor'
					);";

			$sql = str_replace(":smpbsmboid", pg_escape_string($smpbsmboid), $sql);
			$sql = str_replace(":smpbchave", pg_escape_string($smpbchave), $sql);
			$sql = str_replace(":smpbvalor", pg_escape_string($smpbvalor), $sql);



			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception("Erro ao cadastrar propriedades do backlog");
			}

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

    	return $retorno;
    }

    public function excluiRegistrosBacklog($data) {

        $sql = '';
    	$retorno = new stdClass();

    	try {
    		$sql = "DELETE FROM smartagenda_mensageria_backlog WHERE smbdt_fila::DATE <= ':data'";
    		$sql = str_replace(":data", pg_escape_string($data), $sql);



			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception("Erro ao excluir backlog de mensageria.");
			}

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

    	return $retorno;
    }

    public function quantidadeDias() {

        $sql = '';
    	$retorno = new stdClass();

    	try {

	    	$sql = "SELECT
		                pcsidescricao AS qtd
				   	FROM
				   		parametros_configuracoes_sistemas_itens
			     	INNER JOIN
			     		parametros_configuracoes_sistemas ON pcsoid = pcsipcsoid
				  	WHERE pcsipcsoid = 'SMART_AGENDA'
					AND pcsioid = 'LIMPEZA_LOG'
				    AND pcsidt_exclusao IS NULL
					AND pcsdt_exclusao IS NULL LIMIT 1";



			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception("Erro ao realizar a busca da parametrizacao de registros.");
			} else if(pg_num_rows($rs) == 0) {
				throw new Exception("A consulta da parametrizacao nao retornou registros.");
			}

			$retorno->resultado = pg_fetch_object($rs);

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function excluiFilaObsoleta() {

        $sql = '';
    	$retorno = new stdClass();

    	try {

	    	$sql = "DELETE FROM
                        smartagenda_mensageria
                    WHERE
                        smsmsoid  IN (
                            ".self::STATUS_FILA_PROCESSANDO.",
                            ".self::STATUS_FILA_ERRO."
                            )
                    AND
                        smnumero_tentativas >= 5
                    AND
                        smdt_cadastro < NOW() - INTERVAL '24 hours'";



			if(!pg_query($this->conn, $sql)) {
				throw new Exception("Erro ao realizar limpeza do log.");
			}

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function buscaDadosFila($filtro,$order = null) {

        $sql = '';
    	$retorno = new stdClass();
        $dados = array();
        $iDsFila = array();

    	try {

    		if(!$where = $this->where($filtro)) {
				throw new Exception("Erro ao recuperar dados da fila.");
			}

    		$sql = "SELECT
    					smoid,
    					smsmcoid,
    					smsmsoid,
    					smid_ofsc,
    					smprioridade,
    					smdt_cadastro,
    					smnumero_tentativas
					FROM
						smartagenda_mensageria
						";

			$sql .= $where;

			if(!is_null($order)) {
				$sql .= " ORDER BY " . $order;
			}

            $sql .= " LIMIT (SELECT
                                pcsidescricao::INT
                            FROM
                                parametros_configuracoes_sistemas_itens
                            WHERE
                                pcsipcsoid = 'SMART_AGENDA'
                            AND
                                pcsioid = 'LIMITE_REGISTROS_FILA_MENSAGERIA'
                            )";



			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception("Erro ao recuperar dados da fila.");
			}

            while($tupla = pg_fetch_object($rs)){
                $dados[] = $tupla;
                $iDsFila[] = intval($tupla->smoid);
            }

			$retorno->resultado = $dados;
            $retorno->ids_fila = $iDsFila;

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function buscaContexto($filtro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$where = $this->where($filtro)) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_CONTEXTO);
			}

    		$sql = "SELECT
    					smcoid,
    					smcdescricao,
    					smcusuoid_cadastro,
    					smcdt_cadastro,
    					smcusuoid_exclusao,
    					smcdt_exclusao,
                        smcativo
					FROM
						smartagenda_mensageria_contexto
						";

			$sql .= $where;



			$rs = pg_query($this->conn, $sql);

			if(!$rs || pg_num_rows($rs) == 0) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_CONTEXTO);
			}

			$retorno->resultado = pg_fetch_object($rs);

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function atualizaMensagem($dados,$filtro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$dadosUpdate = $this->update($dados)) {
    			throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_MENSAGEM);
    		}

    		if(!$where = $this->where($filtro)) {
				throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_MENSAGEM);
			}

    		$sql = "UPDATE
						smartagenda_mensageria
					SET " . $dadosUpdate . " " . $where;



			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_MENSAGEM);
			}

			$retorno->resultado = $rs;

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function atualizaTentativasMensagem($dadosRegistro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_TENTATIVAS);
			}

    		$sql = "UPDATE
						smartagenda_mensageria
					SET  smnumero_tentativas = smnumero_tentativas + 1 " . $where;

            $sql .= " RETURNING smnumero_tentativas";



			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_TENTATIVAS);
			}

           $tupla = pg_fetch_object($rs);
           $tentativas = isset($tupla->smnumero_tentativas) ? $tupla->smnumero_tentativas : 0;
		   $retorno->resultado = $tentativas;

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function gravaHistoricoOrdemServico($dados) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$dadosInsert = $this->insert($dados)) {
    			throw new Exception(self::MENSAGEM_ERRO_HISTORICO_OS);
    		}

	    	$sql = "INSERT INTO
	    				ordem_situacao
	    				(:colunas)
						VALUES
						(:valores)";

			$sql = str_replace(':colunas', $dadosInsert['columns'], $sql);
			$sql = str_replace(':valores', $dadosInsert['values'], $sql);


            //echo"<pre>";var_dump($sql);echo"</pre>";exit();

			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_HISTORICO_OS);
			}

			$retorno->resultado = $rs;

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function infoRepresentante($dadosRegistro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

	    	if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_REPRESENTANTE);
			}

	    	$sql = "SELECT
	    				repoid,
	    				repnome,
	    				repcgc,
	    				repe_mail,
	    				endvddd,
	    				endvfone
	    			FROM
	    				representante
	    			INNER JOIN
	    				endereco_representante
	    			ON endvrepoid = repoid ";

			$sql .= $where;


			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_REPRESENTANTE);
			}

			$retorno->resultado = pg_fetch_assoc($rs);

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function infoInstalador($dadosRegistro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

	    	if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_INSTALADOR);
			}

			$sql = "SELECT
	    				itloid,
	    				itlnome,
	    				itlrepoid,
	    				itlemail,
	    				itlfone,
	    				itlfone_sms
	    			FROM
	    				instalador
	    			";

			$sql .= $where;


			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_INSTALADOR);
			}

			$retorno->resultado = pg_fetch_assoc($rs);

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function buscaDadosCliente($dadosRegistro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_CLIENTE);
			}

	    	$sql = "SELECT
	    				clioid,
	    				clinome,
	    				clino_cpf,
	    				clifone_res,
	    				clifone_cel,
	    				cliemail
	    			FROM
	    				clientes";

			$sql .= $where;


			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_CLIENTE);
			}

			$retorno->resultado = pg_fetch_assoc($rs);

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function buscaDadosContatoCliente($dadosRegistro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_CLIENTE);
			}

	    	$sql = "SELECT
	    				osecnome,
	    				osecemail,
	    				oscccelular
          			FROM
          				ordem_servico_email_contato
             			LEFT JOIN ordem_servico_celular_contato ON osccordoid = osecordoid";

			$sql .= $where;


			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_CLIENTE);
			}

			$retorno->resultado = pg_fetch_assoc($rs);

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function buscarDadosUsuario($dadosRegistro) {

        $sql = '';
		$retorno = new stdClass();

    	try {

    		if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_USUARIO);
			}

	    	$sql = "SELECT
	    				cd_usuario
	    			FROM
	    				usuarios";

			$sql .= $where;


			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_USUARIO);
			}

			$usuario = pg_fetch_assoc($rs);
			//$retorno->resultado = pg_fetch_assoc($rs);
			return $usuario['cd_usuario'];
		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function buscaMotivoHistoricoCorretora($dadosRegistro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_STATUS_HISTORICO_OS);
			}

	    	$sql = "SELECT
	    				*
	    			FROM
	    				motivo_hist_corretora";

			$sql .= $where;
            $sql .= ' LIMIT 1';

			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_STATUS_HISTORICO_OS);
			}

			$res = pg_fetch_assoc($rs);
			return $res['mhcoid'];

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function atualizaAgenda($dados,$filtro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$dadosUpdate = $this->update($dados)) {
    			throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_AGENDA);
    		}

    		if(!$where = $this->where($filtro)) {
				throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_AGENDA);
			}

    		$sql = "UPDATE
						ordem_servico_agenda
					SET " . $dadosUpdate . " " . $where;



			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_AGENDA);
			}

			$retorno->resultado = $rs;

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function buscaDadosAgendamento($dadosRegistro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_AGENDAMENTO);
			}

	    	$sql = "SELECT
	    				*
	    			FROM
	    				ordem_servico_agenda ";

			$sql .= $where;


			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_AGENDAMENTO);
			} elseif (pg_num_rows($rs) == 0) {
				throw new Exception(self::MENSAGEM_AGENDAMENTO_NAO_ENCONTRADO);
			}

			$retorno->resultado = pg_fetch_assoc($rs);
		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function buscaDadosOrdemServico($dadosRegistro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_ORDEM_SERVICO);
			}

	   	$sql = "SELECT
	    				*
	    			FROM
	    				ordem_servico ";

			 $sql .= $where;


			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_ORDEM_SERVICO);
			} elseif (pg_num_rows($rs) == 0) {
				throw new Exception(self::MENSAGEM_ORDEM_SERVICO_NAO_ENCONTRADO);
			}

			$retorno->resultado = pg_fetch_assoc($rs);
		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function buscaDadosAgendaOS($dadosRegistro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_AGENDAMENTO);
			}

	    	$sql = "SELECT
	    				osaordoid,
	    				osaplaca,
	    				osahora,
	    				osadata,
	    				ostdescricao,
					osaid_atividade
	    			FROM
	    				ordem_servico_agenda
	    				INNER JOIN ordem_servico ON osaordoid = ordoid
	    				LEFT JOIN os_tipo ON ordostoid = ostoid ";

			$sql .= $where;


			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_AGENDAMENTO);
			}

			$retorno->resultado = pg_fetch_assoc($rs);
		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function buscaLayout($dadosRegistro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_LAYOUT_EMAIL);
			}

	    	$sql = "SELECT
	    				seeoid,
	    				seetipo
	    			FROM
	    				servico_envio_email
	    				INNER JOIN servico_envio_email_titulo ON seeseetoid = seetoid
	    				INNER JOIN servico_envio_email_funcionalidade ON seetseefoid = seefoid ";

			$sql .= $where;


			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_LAYOUT_EMAIL);
			}

			$retorno->resultado = array();

			while($linha = pg_fetch_assoc($rs)) {
				if($linha['seetipo'] == 'S') {
					$retorno->resultado['SMS'] = $linha['seeoid'];
				} elseif ($linha['seetipo'] == 'E') {
					$retorno->resultado['EMAIL'] = $linha['seeoid'];
				}
			}
		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function atualizaItemOS($dados,$filtro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$dadosUpdate = $this->update($dados)) {
    			throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_ITEM_OS);
    		}

    		if(!$where = $this->where($filtro)) {
				throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_ITEM_OS);
			}

    		$sql = "UPDATE
						ordem_servico_item
					SET " . $dadosUpdate . " " . $where;



			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_ITEM_OS);
			}

			$retorno->resultado = $rs;

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function gravaChecklist($dados) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$dadosInsert = $this->insert($dados)) {
    			throw new Exception(self::MENSAGEM_ERRO_GRAVAR_CHECKLIST);
    		}

	    	$sql = "INSERT INTO
	    				checklist_ordem_servico
	    				(:colunas)
						VALUES
						(:valores) RETURNING cosoid;";

			$sql = str_replace(':colunas', $dadosInsert['columns'], $sql);
			$sql = str_replace(':valores', $dadosInsert['values'], $sql);

			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_GRAVAR_CHECKLIST);
			}

			$retorno->resultado = pg_fetch_assoc($rs);

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function gravaChecklistItem($dados) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$dadosInsert = $this->insert($dados)) {
    			throw new Exception(self::MENSAGEM_ERRO_GRAVAR_CHECKLIST_ITEM);
    		}

	    	$sql = "INSERT INTO
	    				checklist_ordem_servico_item
	    				(:colunas)
						VALUES
						(:valores);";

			$sql = str_replace(':colunas', $dadosInsert['columns'], $sql);
			$sql = str_replace(':valores', $dadosInsert['values'], $sql);

			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_GRAVAR_CHECKLIST_ITEM);
			}

			$retorno->resultado = $rs;

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}
		return $retorno;
    }

    public function buscaChecklistItem($dadosRegistro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_CHECKLIST_ITEM);
			}

	    	$sql = "SELECT
	    				*
	    			FROM
	    				checklist_item ";

			$sql .= $where;


			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_CHECKLIST_ITEM);
			}

			$retorno->resultado = pg_fetch_assoc($rs);
		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function buscaMotivoNoShow($dadosRegistro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_MOTIVO_NOSHOW);
			}

	    	$sql = "SELECT
	    				*
	    			FROM
	    				os_motivo_noshow ";

			$sql .= $where;
			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_MOTIVO_NOSHOW);
			}

            $retorno->resultado = pg_fetch_assoc($rs);

		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function buscaAtividadeStatusAgenda($dadosRegistro) {

        $sql = '';
    	$retorno = new stdClass();

    	try {

    		if(!$where = $this->where($dadosRegistro)) {
				throw new Exception(self::MENSAGEM_ERRO_ATIVIDADE_STATUS);
			}

	    	$sql = "SELECT
	    				*
	    			FROM
	    				atividade_status_agenda ";

			$sql .= $where;


			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception(self::MENSAGEM_ERRO_ATIVIDADE_STATUS);
			}

			$retorno->resultado = pg_fetch_assoc($rs);
		} catch(Exception $e) {
			$retorno->erro = $e->getMessage() . " QUERY: " . $sql;
		}

		return $retorno;
    }

    public function atualizaOrdemServico($dados,$filtro) {

        $sql = '';
        $retorno = new stdClass();

        try {

            if(!$dadosUpdate = $this->update($dados)) {
                throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_ORDEM_SERVICO);
            }

            if(!$where = $this->where($filtro)) {
                throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_ORDEM_SERVICO);
            }

            $sql = "UPDATE
                        ordem_servico
                    SET " . $dadosUpdate . " " . $where;



            $rs = pg_query($this->conn, $sql);

            if(!$rs) {
                throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_ORDEM_SERVICO);
            }

            $retorno->resultado = $rs;

        } catch(Exception $e) {
            $retorno->erro = $e->getMessage() . " QUERY: " . $sql;
        }

        return $retorno;
    }

    public function atualizaOrdemServicoAgenda($dados,$filtro) {

        $sql = '';
        $retorno = new stdClass();

        try {

            if(!$dadosUpdate = $this->update($dados)) {
                throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_ORDEM_SERVICO_AGENDA);
            }

            if(!$where = $this->where($filtro)) {
                throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_ORDEM_SERVICO_AGENDA);
            }

            $sql = "UPDATE
                        ordem_servico_agenda
                    SET " . $dadosUpdate . " " . $where;



            $rs = pg_query($this->conn, $sql);

            if(!$rs) {
                throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_ORDEM_SERVICO_AGENDA);
            }

            $retorno->resultado = $rs;

        } catch(Exception $e) {
            $retorno->erro = $e->getMessage() . " QUERY: " . $sql;
        }

        return $retorno;
    }

    public function excluirLocalInstalacao($ordoid) {

        $sql = '';
        $retorno = new stdClass();

        try{

            $sql = "DELETE FROM ordem_servico_inst  WHERE osiordoid = ".intval($ordoid)."";



            $rs = pg_query($this->conn, $sql);

            if(!$rs) {
                throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            $retorno->resultado = $rs;

        } catch(Exception $e) {
            $retorno->erro = $e->getMessage() . " QUERY: " . $sql;
        }

        return $retorno;
    }

    private function insert($dados) {

    	if(is_null($dados) || !is_array($dados) || count($dados) == 0) {
    		return false;
    	}

    	$valores = '';
    	$colunas = implode(", ", array_keys($dados));

    	foreach ($dados as $key => $value) {

    		$valor = pg_escape_string($value);

    		if(strlen($valores) > 0) {
				$valores .= ' , ';
			}

			if($valor != 'NULL') {
				$valor = "'" . $valor . "'";
			}

    		$valores .= " " . $valor . " ";
    	}

    	return array('columns' => $colunas, 'values' => $valores);
    }

    private function orderBy($arrOrder) {

    	$order = '';

    	if(!isset($arrOrder['columns']) || !isset($arrOrder['order'])) {
    		return false;
		}

    	foreach ($arrOrder['columns'] as $column) {

    		if(strlen($order) > 0) {
    			$order .= ' , ';
    		}

    		$order .= " " . $column . " ";
    	}

    	return " ORDER BY " . $order . $arrOrder['order'] ;
    }

    private function where($filtro) {

    	$where = '';

    	foreach ($filtro as $key => $value) {

    		if(!isset($value['condition']) || !isset($value['value'])) {
    			return false;
    		}

            $valor = is_string($value['value']) ? pg_escape_string($value['value']) : $value['value'];

			if(strlen($where) > 0) {
				$where .= " AND ";
			}

			if($valor != 'NULL' && $valor != 'TRUE') {

                 if($value['condition'] == 'IN'){
                    $valor = "(" . implode(',', $valor) . ")";
                } else {
				$valor = "'" . $valor . "'";
			}

			}


			$where .= " " .$key . " ". $value['condition'] ." " . $valor . " " ;
		}

		if(strlen($where) == 0) {
			return false;
		}

		return " WHERE ". $where;
    }

    private function update($dadosArray){
    	$dados = '';
        $strSeparador = '';

		foreach ($dadosArray as $key => $value) {
			$valor = pg_escape_string($value);

			if($value != 'NOW()' && $value != 'NULL') {
				$valor = "'" . $valor . "'";
			}

            $dados .= $strSeparador . $key." = ".$valor." ";
            $strSeparador = ', ';
        }

        if(strlen($dados) == 0) {
        	return false;
        }

        return $dados;
    }

    /**
	 * Abre uma transação
	 */
	public function begin(){
		pg_query($this->conn, 'BEGIN;');
	}

	/**
	 * Finaliza uma transação
	 */
	public function commit(){
		pg_query($this->conn, 'COMMIT;');
	}

	/**
	 * Aborta uma transação
	 */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK;');
	}

    public function isAgendamentoAtivo($osaoid) {

        $sql = '';
        $retorno = new stdClass();

        try {

            $sql = "SELECT EXISTS(
                            SELECT
                                1
                            FROM
                                ordem_servico_agenda
                            WHERE osaoid  = ".intval($osaoid)."
                            AND osaexclusao IS NULL
                            ) AS existe";

            $rs = pg_query($this->conn, $sql);

            if(!$rs) {
                throw new Exception(self::MENSAGEM_ERRO_BUSCAR_AGENDAMENTO);
}

            $tupla = pg_fetch_object($rs);

            $retorno->existe = isset($tupla->existe) ? $tupla->existe : 'f';
            $retorno->existe = ($retorno->existe == 't') ? TRUE : FALSE;


        } catch(Exception $e) {
            $retorno->erro = $e->getMessage() . " QUERY: " . $sql;
        }

        return $retorno;
    }

    public function liberarFila() {

        $sql = '';
        $retorno = new stdClass();

        try{

            $sql = "UPDATE smartagenda_mensageria
                    SET smsmsoid = ".self::STATUS_FILA_AGUARDANDO."
                    WHERE smoid IN (
                        SELECT smoid FROM smartagenda_mensageria
                        WHERE smsmsoid = ".self::STATUS_FILA_PROCESSANDO."
                        AND smdt_cadastro < (NOW()  - INTERVAL '30 minutes')
                    )";

            $rs = pg_query($this->conn, $sql);

            if(!$rs) {
                throw new Exception(self::MENSAGEM_ERRO_ATUALIZAR_MENSAGEM);
}

        }  catch(Exception $e) {
            $retorno->erro = $e->getMessage() . " QUERY: " . $sql;
        }

        return $retorno;
    }

    public function getRelacionamentoRepresentante($repoid) {

        $sql = '';
        $retorno = new stdClass();

        try{

            $sql = "SELECT relroid
                    FROM relacionamento_representante
                    WHERE relrrepoid = ".intval($repoid)."
                    LIMIT 1 ";

            $rs = pg_query($this->conn, $sql);

            if( !$rs || pg_num_rows($rs) == 0 ) {
                throw new Exception(self::MENSAGEM_ERRO_BUSCAR_REPRESENTANTE);
            }

            $tupla = pg_fetch_object($rs);
            $retorno->relroid = $tupla->relroid;

        }  catch(Exception $e) {
            $retorno->erro = $e->getMessage() . " QUERY: " . $sql;
        }

        return $retorno;
    }

    public function gravarHistoricoAnexo($dados) {

        $sql = '';
        $retorno = new stdClass();

        try {

            foreach ($dados as $key => $value) {

                 $sql = "INSERT INTO
                        historico_ordem_servico_anexo(
                            hosaordoid,
                            hosatpaoid,
                            hosaarquivo,
                            hosaobservacao,
                            hosausuoid_cadastro
                        )
                    VALUES (
                        ". intval($value['ordem_servico']) .",
                        ". $value['tipo_anexo'] .",
                        '". $value['nome_arquivo'] ."',
                        '". $value['obs_arquivo'] ."',
                        ".  $value['id_usuario'] ."
                    );";

                $rs = pg_query($this->conn, $sql);

                if(!$rs || pg_affected_rows($rs) == 0) {
                    throw new Exception("Erro ao gravar dados do anexo.");
                }
            }

        } catch(Exception $e) {
            $retorno->erro = $e->getMessage() . " QUERY: " . $sql;
        }

        return $retorno;
    }

     public function buscarItemDefeito($idItemOS, $idDefeito) {

        $sql = '';
        $retorno = new stdClass();

        try {

            $sql = "SELECT
                        osdfoid
                    FROM
                        ordem_servico_defeito
                    WHERE
                        osdfotioid  = ".intval($idItemOS)."
                    AND
                        osdfotdoid = ".intval($idDefeito);

            $rs = pg_query($this->conn, $sql);

            if(!$rs) {
                throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            $retorno->resultado = pg_fetch_assoc($rs);

        } catch(Exception $e) {
            $retorno->erro = $e->getMessage() . " QUERY: " . $sql;
        }

        return $retorno;
    }

    public function setCancelarReservaPrestadorAgendado( $numeroOrdemServico){

        $statusCancelado   = 2;
        $statusReservado   = 3;

        $sql = '';
        $retorno = new stdClass();

        try {

            $sql = "UPDATE
                    reserva_agendamento
                SET
                    ragrasoid = ". $statusCancelado .",
                    ragjustificativa_cancelamento = 'Ordem de Servico concluida antes da data agendada',
                    ragdt_cancelamento = NOW()
                WHERE TRUE
                AND ragrepoid != (  SELECT pcsidescricao::INT
                                    FROM parametros_configuracoes_sistemas_itens
                                    WHERE pcsipcsoid = 'SMART_AGENDA'
                                    AND pcsioid = 'REPOID_SOLICITACAO_FALSA'
                                  )
                AND ragordoid = ". intval($numeroOrdemServico) ."
                AND ragrasoid IN (". $statusReservado .")
                AND ragdt_cancelamento IS NULL
                RETURNING ragoid;";

              $rs = pg_query($this->conn, $sql);

            if(pg_num_rows($rs) > 0 ){

                $result = pg_fetch_all($rs);

                foreach ($result as $ragoid){

                    $sql_itens = "
                                UPDATE
                                    reserva_agendamento_item
                                 SET
                                    raidt_exclusao = NOW()
                                WHERE
                                    rairagoid = ".$ragoid['ragoid']."
                                AND
                                    raidt_exclusao IS NULL";

                     $rs = pg_query($this->conn, $sql_itens);
                }

            }

        } catch(Exception $e) {

            $retorno->erro = $e->getMessage() . " QUERY: " . $sql;
        }

        return $retorno;
    }

}
?>

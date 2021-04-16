<?php

// grava log de erro
ini_set ( "log_errors", 1 );
ini_set ( 'error_log', '/tmp/smart_agenda_email_sms_' . date ( 'd-m-Y' ) . '.txt' );

require_once _MODULEDIR_ . 'SmartAgenda/Action/SmartAgenda.php';
require_once _MODULEDIR_ . 'SmartAgenda/Action/Agenda.php';
require_once _MODULEDIR_ . 'SmartAgenda/Action/OrdemServico.php';
require_once _MODULEDIR_ . 'SmartAgenda/Action/Contrato.php';
require_once _MODULEDIR_ . 'SmartAgenda/DAO/ComunicacaoEmailsSMSDAO.php';
require_once _MODULEDIR_ . 'Cadastro/Action/SendLayoutEmails.php';
require_once _MODULEDIR_ . 'Principal/Action/ServicoEnvioEmail.php';
require_once _SITEDIR_ . 'lib/funcoes.php';
require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';
require_once _SITEDIR_ . 'lib/funcoes.php';


class ComunicacaoEmailsSMS {


	private $smartAgenda;
	private $ServicoEmail;
	private $SendLayoutEmails;
	private $agenda;
	private $ordemServico;
    private $contrato;
	private $dao;
	private $contexto;
	private $cd_usuario;
	private $id;
	private $emailDestino;
	private $celularDestino;
	private $nomeContato;
	private $tipoAgendamento;
	private $dadosEndereco;

	public function __construct() {
		Global $conn;
		$this->smartAgenda = new SmartAgenda ();
		$this->ServicoEmail = new ServicoEnvioEmail ();
		$this->SendLayoutEmails = new SendLayoutEmails ();
		$this->agenda = new Agenda ();
		$this->ordemServico = new OrdemServico();
        $this->contrato = new Contrato();
		$this->dao = new ComunicacaoEmailsSMSDAO ( $conn );
        $this->cd_usuario       = $this->dao->getUsuarioLogado();
	}

    public function setTipoAgendamento ($tipoAgendamento){

        $this->tipoAgendamento = $tipoAgendamento;
    }

	public function __set($nome, $valor) {
		$this->$nome = $valor;
	}
	public function __get($nome) {
		return $this->$nome;
	}

	/*
	 * Metedo de envio de email e SMS
	 */
	public function EnviaEmailSms() {

		// recebe os valor do nome para validação
		$contato = $this->nomeContato;
		//verifica se o contexto está vazio , se estivar retorna false
		if($this->id == "" || !isset($this->id) || empty($this->id)){
			return false;

		}

		//pega o tipo de id e vai buscar os dados do agendamento
		$agendamento = $this->agenda->getDadosAgendamento( $this->id, 'AGENDAMENTO');

        $campos = array('ordconnumero');
        $filtros = "WHERE ordoid = " . intval($agendamento['osaordoid']);
        $connumero = $this->ordemServico->recuperarDadosOrdemServico($campos, $filtros);
        $isClienteSiggo = false;

        if($connumero){
        	$isClienteSiggo = $this->contrato->isContratoSiggo($connumero[0]['ordconnumero']);
        }

        //verifica se o contexto está vazio , se estivar retorna false
        if($this->contexto == "" || !isset($this->contexto) || empty($this->contexto)){
        	$this->ordemServico->salvaHistorico ( $agendamento ['osaordoid'], $this->cd_usuario, "PROBLEMA ENVIO EMAIL/SMS CONTEXTO NÃO DEFINIDO", $agendamento ['osadata'], $agendamento['osahora'], $idTipoAgendamento);
            return false;

        }

		//vai buscar o id do tipo de agendamento passando o motivo agendamento
        if($this->tipoAgendamento == 'U') {

            if($this->contexto == 'AGENDAMENTO_CANCELADO') {
                 $statusMotivo = 'Agendamento Cancelado';
            } else {
                $statusMotivo = 'Agendamento Unitário';
            }

        } else {
            $statusMotivo = '';
        }


		$idTipoAgendamento = $this->ordemServico->retornoHistoricoCorretora($statusMotivo);

		//Verifica se o nome do contato está vazio caso estiver retorna false
		if($contato == "" || !isset($contato) || empty($contato)){
			$contato == 'Cliente';
		}

		// recupera o titulo e o tipo de envio se é email , sms, ou ambos  e o tipo agendamento para preencher o historico do sms passando como parametro o contexto
		$contexto = $this->ListaDadosContexto( $this->contexto , $isClienteSiggo );


		// caso o contexto passado não exista ele retorna falso
		if($contexto ['titulo'] == '' || !isset($contexto ['titulo']) || empty($contexto ['titulo'])){
			$this->ordemServico->salvaHistorico ( $agendamento ['osaordoid'], $this->cd_usuario, "PROBLEMA ENVIO EMAIL/SMS CONTEXTO SEM TITULO DEFINIDO", $agendamento ['osadata'], $agendamento['osahora'], $idTipoAgendamento);
			return false;

		}


		//retorna  o codigo do titulo e da funcionalidade de acordo com o nome do titulo passado
		$dadosLayout = $this->SendLayoutEmails->getTituloFuncionalidade ( $contexto ['titulo'] );

		if($dadosLayout == null || empty($dadosLayout) || !isset($dadosLayout) || count($dadosLayout) == 0 ){
			// chama o metodo salvaHistorico na classe ordem de serviço passando os parametros
			$this->ordemServico->salvaHistorico ( $agendamento ['osaordoid'], $this->cd_usuario, "Layout de Email/SMS não localizado", $agendamento ['osadata'], $agendamento['osahora'], $idTipoAgendamento);
			return false;

		}

		//id da funcionalidade
		$dadosEmail ['seeseefoid'] = $dadosLayout [0] ['funcionalidade_id'];
		//id do titulo
		$dadosEmail ['seeseetoid'] = $dadosLayout [0] ['titulo_id'];

		$codigoLayout = array();

		if($contexto ['tipo'] == 'Email'){

			$dadosEmail ['seetipo'] = 'E';
			//Busca layout para o envio de email passando os $dadosEmail retornado do metodo getTituloFuncionalidade
			$codigoLayout[] = $this->SendLayoutEmails->buscaLayoutEmail ( $dadosEmail );

		}elseif ($contexto ['tipo'] == 'SMS') {

			$dadosEmail ['seetipo'] = 'S';
			//Busca layout para o envio de email passando os $dadosEmail retornado do metodo getTituloFuncionalidade
			$codigoLayout[] = $this->SendLayoutEmails->buscaLayoutEmail ( $dadosEmail );

		}elseif ($contexto ['tipo'] == 'Ambos') {

			$tipo = array('E','S');

			foreach ($tipo as $chave => $valor) {

				$dadosEmail ['seetipo'] = $valor;
				//Busca layout para o envio de email passando os $dadosEmail retornado do metodo getTituloFuncionalidade
				$codigoLayout[] = $this->SendLayoutEmails->buscaLayoutEmail ( $dadosEmail );
			}

		}


		if(count($codigoLayout) == 0){
			// chama o metodo salvaHistorico na classe ordem de serviço passando os parametros
			$this->ordemServico->salvaHistorico ( $agendamento ['osaordoid'], $this->cd_usuario, "LAYOUT NÃO CADASTRADO COM OS DADOS INFORMADOS", $agendamento ['osadata'], $agendamento['osahora'], $idTipoAgendamento);
			return false;

		}

		$layouts = array();

		foreach ($codigoLayout as $chave => $valor) {
			//usca o layout de acordo com o ID do codigo do layout
			$layouts[] = $this->SendLayoutEmails->getLayoutEmailPorId ( $valor ['seeoid'] );
		}


		if(count($layouts) == 0 ){
			// chama o metodo salvaHistorico na classe ordem de serviço passando os parametros
			$this->ordemServico->salvaHistorico ( $agendamento ['osaordoid'], $this->cd_usuario, "LAYOUT NÃO CADASTRADO", $agendamento ['osadata'], $agendamento['osahora'], $idTipoAgendamento);
			return false;

		}

		foreach ($layouts as $chave => $layout) {

			//retorna o servidor
			$servidor = $layout ['seesrvoid'];

			//metodo para substituir as tag ['NOME'] Placa [PLACA] ETC pelos valores reais
			$corpo_envio = $this->substituiTAG ( $layout ['seecorpo'], $this->contexto, $agendamento );


			//Verifica o tipo de context se é sms e email ou somente email ou sms
			if($layout['seetipo'] == 'E') {


				//verifica se o email está vazio
				if ($this->emailDestino == "" || ! isset ( $this->emailDestino ) || empty ( $this->emailDestino )) {
					$this->ordemServico->salvaHistorico ( $agendamento ['osaordoid'], $this->cd_usuario, "PROBLEMA ENVIO EMAIL/SMS E-MAIL DE DESTINO NAO DEFINIDO", $agendamento ['osadata'], $agendamento['osahora'], $idTipoAgendamento);
					return false;

				}

				//verifica se é um email valido
				$isEmailValido = $this->ValidaEmail( $this->emailDestino );

				if (! $isEmailValido) {
					$this->ordemServico->salvaHistorico ( $agendamento ['osaordoid'], $this->cd_usuario, "PROBLEMA ENVIO EMAIL/SMS E-MAIL DE DESTINO INVALIDO", $agendamento ['osadata'], $agendamento['osahora'], $idTipoAgendamento);
					return false;
				}

				//chama o metodo de enviar email
				$envio = $this->enviarEmail( $this->emailDestino, $layout ['seecabecalho'], $corpo_envio, $layout['seesrvoid']);

				//pega os dados do remetente
				$dadosRemetente = $this->ordemServico->dadosRemetente($servidor);

				// Envio ocorreu com sucesso
				// Salva o texto do envio no historio da OS
				if ($envio) {

					$msg = $this->montarHistorico(true, "E-mail", "email", $dadosRemetente['srvremetente_email'], $this->emailDestino, $corpo_envio, $layout ['seecabecalho'], "");
					// chama o metodo salvaHistorico na classe ordem de serviço passando os parametros
					$retornoOrdemServico = $this->ordemServico->salvaHistorico ( $agendamento ['osaordoid'], $this->cd_usuario, $msg, $agendamento ['osadata'], $agendamento['osahora'], $idTipoAgendamento);

					if(!$retornoOrdemServico){
						return false;

					}
				} else {

					$msg = $this->montarHistorico(false, "E-mail", "email", $dadosRemetente['srvremetente_email'], $this->emailDestino, $corpo_envio, $layout ['seecabecalho'], "");
					// chama o metodo salvaHistorico na classe ordem de serviço passando os parametros
					$retornoOrdemServico = $this->ordemServico->salvaHistorico ( $agendamento ['osaordoid'], $this->cd_usuario, $msg, $agendamento ['osadata'], $agendamento['osahora'], $idTipoAgendamento);
					if(!$retornoOrdemServico){
						return false;

					}
				}
			}
			//Verifica o tipo de context se é sms e email ou somente email ou sms
			if ($layout['seetipo'] == 'S') {

				//verifica se celular está vazio caso estiver retorna false
				if ($this->celularDestino == "" || ! isset ( $this->celularDestino ) || empty ( $this->celularDestino )) {
					$this->ordemServico->salvaHistorico ( $agendamento ['osaordoid'], $this->cd_usuario, "PROBLEMA ENVIO EMAIL/SMS CELULAR DE DESTINO NAO DEFINIDO", $agendamento ['osadata'], $agendamento['osahora'], $idTipoAgendamento);
					return false;

				}

				//valida o numero do celular
				$celular = $this->ValidaCelular ( $this->celularDestino );
				//verifica se o celular é valido
				if (!$celular) {
					$this->ordemServico->salvaHistorico ( $agendamento ['osaordoid'], $this->cd_usuario, "PROBLEMA ENVIO EMAIL/SMS CELULAR DE DESTINO INVALIDO", $agendamento ['osadata'], $agendamento['osahora'], $idTipoAgendamento);
					return false;

				}

				//chama o metodo de enviar sms passando a msg e o numero de celular e retorna ok caso enviado
				$returnSMS = $this->enviarMensagensSms ( $corpo_envio, $this->celularDestino );

                //Dados historico SMS
                $dadosHistoricoSMS = array();
                $dadosHistoricoSMS['hseconnumero']       = $connumero[0]['ordconnumero'];
                $dadosHistoricoSMS['hseordoid']          = $agendamento['osaordoid'];
                $dadosHistoricoSMS['hseusuoid_cadastro'] = $this->cd_usuario;
                $dadosHistoricoSMS['hsetipo']            = $contexto['TipoAgendamento'];
                $dadosHistoricoSMS['hseseeoid']          = $layout ['seeoid'];
                $dadosHistoricoSMS['hsetelefone']        = $this->celularDestino;

				//se enviado retorna ok e salva
				if ($returnSMS == "OK") {

					$msg = $this->montarHistorico(true, "SMS", "sms","","", $corpo_envio, "", $this->celularDestino);
                    $dadosHistoricoSMS['hsestatus'] = 'S';
                    $dadosHistoricoSMS['hsetexto'] = $msg;

					// chama o metodo salvaHistorico na classe ordem de serviço passando os parametros
					$retornoOrdemServico = $this->ordemServico->salvaHistorico ( $agendamento ['osaordoid'], $this->cd_usuario, $msg, $agendamento ['osadata'], $agendamento['osahora'], $idTipoAgendamento);
					if(!$retornoOrdemServico){
						return false;

					}
					//chama o metodo salvaHistorico na classe ordem de serviço passando os parametros sms
					$retornoHistSMS = $this->salvarHistoricoSMS($dadosHistoricoSMS);

				} else {
					$msg = $this->montarHistorico(false, "SMS", "sms","","", $corpo_envio, "", $this->celularDestino);
                    $dadosHistoricoSMS['hsestatus'] = 'I';
                    $dadosHistoricoSMS['hsetexto'] = $msg;

					// chama o metodo salvaHistorico na classe ordem de serviço passando os parametros
					$retornoOrdemServico = $this->ordemServico->salvaHistorico ( $agendamento ['osaordoid'], $this->cd_usuario, $msg, $agendamento ['osadata'], $agendamento['osahora'], $idTipoAgendamento);
					if(!$retornoOrdemServico){
						return false;

					}
					//chama o metodo salvaHistorico na classe ordem de serviço passando os parametros sms
					$retornoHistSMS = $this->salvarHistoricoSMS($dadosHistoricoSMS);
				}
			}

		}

		return true;

	}

	/*
	Envia os e-mails e SMSs vindos da mensageria
	*/
	public function enviaEmailSMSMensageria($tags, $idLayout, $email = NULL, $fone = NULL, $ordoid = NULL) {
		// Se não tiver nenhum id de layout, pula fora
		if(empty($idLayout)) {
			return false;
		}

		// Busca o layout de e-mail/SMS
		$layout = $this->SendLayoutEmails->getLayoutEmailPorId($idLayout);

		// Se não houver layout para aquele id, pula fora
		if($layout == null || empty($layout) || !isset($layout) || count($layout) == 0 ){
			return false;
		}

		// Faz a substituição das tags específicas do mensageria no corpo da mensagem
		foreach($tags as $key => $item) {
			if(!is_numeric($key)) {
				$layout['seecorpo'] = str_replace('[' . $key . ']', $item, $layout['seecorpo']);
			}
		}

		//Se foi passado um endereço de e-mail, envia o e-mail
		$email = trim($email);

		if(!empty($email)) {
			return $this->enviarEmail($email, $layout['seecabecalho'], $layout['seecorpo'], $layout['seesrvoid']);
		}

		$fone = trim($fone);

        $envioSMS = $this->enviarMensagensSms($layout['seecorpo'], $fone);

        if(!empty($ordoid)){

            $campos = array('ordconnumero');
            $filtros = "WHERE ordoid = " . intval($ordoid);
            $dadosOS = $this->ordemServico->recuperarDadosOrdemServico($campos, $filtros);
        }

        $dadosHistoricoSMS = array();
        $dadosHistoricoSMS['hseconnumero']       = $dadosOS[0]['ordconnumero'];
        $dadosHistoricoSMS['hseordoid']          = $ordoid;
        $dadosHistoricoSMS['hseusuoid_cadastro'] = $this->cd_usuario;
        $dadosHistoricoSMS['hsetipo']            = 'D1';
        $dadosHistoricoSMS['hsetexto']           = $layout['seecorpo'];
        $dadosHistoricoSMS['hseseeoid']          = $idLayout;
        $dadosHistoricoSMS['hsetelefone']        = $fone;

        if($envioSMS) {
            $dadosHistoricoSMS['hsestatus'] = 'S';
			$this->salvarHistoricoSMS($dadosHistoricoSMS);
			return true;
		} else {
            $dadosHistoricoSMS['hsestatus'] = 'I';
			$this->salvarHistoricoSMS($dadosHistoricoSMS);
			return false;
		}

	}


	//Metodo que chama a dao para armazenar o historico do sms
	public function salvarHistoricoSMS($dados) {

        $retorno = false;

        if( !isset( $dados['hseusuoid_cadastro'])
            || !isset($dados['hsetipo'])
            || !isset($dados['hseseeoid'])
            || !isset($dados['hsestatus'])) {

            return $retorno;
        }

        $dados['hseconnumero']       = isset($dados['hseconnumero']) ? floatval($dados['hseconnumero']) : 'NULL';
        $dados['hseordoid']          = isset($dados['hseordoid'])    ? floatval($dados['hseordoid']) : 'NULL';
        $dados['hsetexto']           = isset($dados['hsetexto'])     ? $dados['hsetexto'] : 'NULL';
        $dados['hsetelefone']        = isset($dados['hsetelefone'])  ? preg_replace ( '/\D/', '', $dados['hsetelefone'] ) : 'NULL';
        $dados['hseusuoid_cadastro'] = intval($dados['hseusuoid_cadastro']);
        $dados['hseseeoid']          = intval($dados['hseseeoid']);

		$retorno = $this->dao->salvarHistoricoSMS($dados);

        return $retorno;
	}

	/**
	 * Enviar um e-mail informativo
	 *
	 * @param unknown $destinatarioEmail
	 * @param unknown $titulo
	 * @param unknown $texto
	 * @return boolean
	 */
	public function enviarEmail($destinatario, $titulo, $texto, $servidorEmail = NULL) {

		$destinatario = trim ( $destinatario );

		$mail = new PHPMailer ();

		$mail->ClearAllRecipients ();

		$mail->IsSMTP ();

		$mail->From = "sascar@sascar.com.br";
		$mail->FromName = "Sascar";

		if(isset($servidorEmail)) {
			// Recupera informações do servidor de e-mail
			$rs = $this->dao->recuperaInformacoesServidorEmail($servidorEmail);

			if(!is_null($rs)) {
				$mail->From = $rs['srvremetente_email'];
				$mail->FromName = $rs['srvremetente_nome'];
			}
		}

		$mail->Subject = $titulo;

		$mail->MsgHTML ( $texto );

		if ( _AMBIENTE_ != 'PRODUCAO') {
			$destinatario = "teste_desenv@sascar.com.br";
		}

		$mail->AddAddress ( $destinatario );

		return $mail->Send ();
	}

	/**
	 * Envia SMS para todos os telefones cadastrados para determinada OS
	 *
	 * @param object $template
	 *        	Dados do template
	 * @param array $telefones
	 *        	Telefones cadastrados para determinada OS
	 */
	private function enviarMensagensSms($corpoSms, $telefones) {
		$retornoSms = enviaSms( $telefones, $corpoSms );
		return $retornoSms;
	}

	//Metodo que retorna os tipo de contexto permitidos
	public function ListaDadosContexto($valor, $isSiggo) {

        if($isSiggo == TRUE) {
            $tipoCliente  ='Siggo';
        } else {
            $tipoCliente  ='Sascar';
        }


		$contexto = array ();

		switch ($valor) {
			case 'AGENDAMENTO' :
				$contexto = array (
						'titulo' => $tipoCliente . " Agendamento",
						'tipo' => "Ambos" ,
						'TipoAgendamento' => 'AG'
				);
				break;
			case 'AGENDAMENTO_CANCELADO' :
				$contexto = array (
						'titulo' => $tipoCliente . " Agendamento cancelado",
						'tipo' => "Ambos" ,
						'TipoAgendamento' => 'CA'
				);
				break;
			case 'REAGENDAMENTO' :
				$contexto = array (
						'titulo' => $tipoCliente. " Reagendamento",
						'tipo' => "Ambos" ,
						'TipoAgendamento' => 'RG'
				);
				break;
			case 'DADOS_TECNICOS' :
				$contexto = array (
						'titulo' => $tipoCliente . " Dados dos técnicos",
						'tipo' => "Email"
				);
				break;
			default :
				$contexto = array (
						'titulo' => "",
						'tipo' => ""
				);
				break;
		}

		return $contexto;
	}

	//Metodo para  altera as tags do texto vindo do banco
	public function substituiTAG($texto, $contexto, $agendamento) {

		$texto = str_replace ( "[ORDEM_SERVICO]", $agendamento ['osaordoid'], $texto );
		$texto = str_replace ( "[PLACA]", $agendamento ['osaplaca'], $texto );
		$texto = str_replace ( "[DATA]", date ( 'd/m/Y', strtotime ( $agendamento ['osadata'] ) ), $texto );

        $dadosPrestador = $this->dao->recuperarDadosPrestador( $agendamento['osarepoid'] );
        
        $dadosEndereco = $this->dadosEndereco;
        
        if($dadosEndereco != NULL){
            
            $logradouro = $dadosEndereco['address'];
            $logradouro .= ' ' .$dadosEndereco['XA_ADDRESS_2'];
            
            $logradouro .= ' - ' . $dadosEndereco['XA_NEIGHBORHOOD_NAME'];
            
            $logradouro .= ' - ' . $dadosEndereco['city'];
            $logradouro .= '/' . $dadosEndereco['state'];
            
            if(!empty($dadosEndereco['zip']) && isset($dadosEndereco['zip'])){
                $logradouro .= ' CEP: ' . $dadosEndereco['zip'];
            }else {
                $logradouro .= ' CEP: Não Informado ';
            }
            
        }else{
            $logradouro = '';
        }
        

        $telefonePrestador = '(' . $dadosPrestador->endvddd . ') ' . $dadosPrestador->endvfone;

        $texto = str_replace('[REPRESENTANTE]', $dadosPrestador->repnome, $texto);
        $texto = str_replace('[TELEFONE_REPRESENTANTE]', $telefonePrestador, $texto);
        $texto = str_replace('[LOGRADOURO]', $logradouro , $texto);

        if($agendamento ['osatipo_atendimento'] == 'F') {

            $texto = str_replace ( "[TIME_SLOT]", substr($agendamento ['osahora'], 0, 5), $texto );

        } else {

            $texto = str_replace ( "[TIME_SLOT]", substr($agendamento ['osahora'], 0, 5) . " as " . substr($agendamento ['osahora_final'], 0, 5), $texto );

        }


		//Verifica se é o contexto sobre instalador
		if ($contexto == "DADOS_TECNICOS") {
			$instalador = $this->dao->dadosInstalador( $agendamento['osarepoid'] );

			foreach ( $instalador as $row ) {

				$tecnico .= "Nome: ".$row ['itlnome']." CPF:".$row ['itlno_cpf'] . "</br>";

			}

			$texto = str_replace ( "[CPF]", "", $texto );
			$texto = str_replace ( "Nome:", "", $texto );
			$texto = str_replace ( "CPF:", "", $texto );
			$texto = str_replace ( "[NOME]", $tecnico, $texto );
		}

		return $texto;
	}

	/*
	 * Metodo para validar email
	 */
	public function ValidaEmail($email) {

		if (! preg_match ( "/^(([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z-]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}){0,1}$/", $email )) {
			return false;
		} else {
			return true;
		}
	}

	// Tira as mascaras do telefone e faz a validação com 8 ou 9 digitos
	public function ValidaCelular($telefone) {
		// remove tudo que não for digito
		$onlyNumbers = preg_replace ( '/\D/', '', $telefone );

		if (! preg_match ( "/^(\(0?\d{2}\)\s?|0?\d{2}[\s.-]?)\d{4,5}[\s.-]?\d{4}$/", $onlyNumbers )) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Função para retornar a data com o formato correto
	 */
	private function formataData() {
		// EXEMPLO -> Segunda-feira, 12 de agosto de 2013 13:20

		// Concatena a data da semana
		$data = $this->getDiaSemana ();

		// Concatena o dia
		$data .= Date ( "d" );

		// Concatena o mês
		$data .= ' de ' . $this->getMes () . ' de ';

		// Concatena o ano
		$data .= Date ( "Y" );

		return $data;
	}

	// Retorna o dia da semana em extenso
	private function getDiaSemana() {
		$dia_semana = Date ( "w" );
		$dia_semana ++;

		switch ($dia_semana) {
			case 1 :
				$dia = "Domingo, ";
				break;
			case 2 :
				$dia = "Segunda-feira, ";
				break;
			case 3 :
				$dia = "Terça-feira, ";
				break;
			case 4 :
				$dia = "Quarta-feira, ";
				break;
			case 5 :
				$dia = "Quinta-feira, ";
				break;
			case 6 :
				$dia = "Sexta-feira, ";
				break;
			case 7 :
				$dia = "Sábado, ";
				break;
		}

		return $dia;
	}

	// Retorna o mes em extenso
	private function getMes() {
		// Mês sem o zero inicial
		switch (Date ( "n" )) {
			case 1 :
				$mes = "Janeiro";
				break;
			case 2 :
				$mes = "Fevereiro";
				break;
			case 3 :
				$mes = "Março";
				break;
			case 4 :
				$mes = "Abril";
				break;
			case 5 :
				$mes = "Maio";
				break;
			case 6 :
				$mes = "Junho";
				break;
			case 7 :
				$mes = "Julho";
				break;
			case 8 :
				$mes = "Agosto";
				break;
			case 9 :
				$mes = "Setembro";
				break;
			case 10 :
				$mes = "Outubro";
				break;
			case 11 :
				$mes = "Novembro";
				break;
			case 12 :
				$mes = "Dezembro";
				break;
		}

		return $mes;
	}

	/**
	 * Monta a observação para gravar histórico
	 *
	 * @param string $email
	 * @param array $insucesso
	 * @param string $motivo
	 * @return string $obs
	 */
	public function montarHistorico($sucesso, $emailSms, $tipo, $dadosRemetente, $email, $corpo_envio, $assunto, $celular) {
		if ($sucesso) {
			$msg = $emailSms. " enviado para o cliente: \n";
		} else {
			$msg = "Falha no envio de " . $emailSms . " ao cliente: \n";
		}

		if ($tipo == "email") {

			$msg .= "    De: " . $dadosRemetente . "\n";
			$msg .= "    Para: " . $email . "\n";
			$msg .= "    Assunto: " . $assunto . "\n";
			$msg .= "    Mensagem: " . $corpo_envio . "\n";
			$msg .= "    Enviado em: " . $this->formataData () . "\n";
		} else {

			$msg .= "    Enviado em: " . $this->formataData () . "\n";
			$msg .= "    Enviado para: " . $celular . "\n";
			$msg .= "    Mensagem: " . $corpo_envio . "\n";
		}

		$msg = nl2br ( $msg );


		return $msg;
	}
}

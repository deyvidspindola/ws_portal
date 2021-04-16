<?php

/**
 * Classe para persistência de dados deste modulo
 */
require _MODULEDIR_ . 'Principal/DAO/PrnOrdermServicoDAO.php';
require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';


// Casses para busca de layout e utilização serviço de envio email
include_once _MODULEDIR_ . 'Cadastro/Action/SendLayoutEmails.php';
include_once _MODULEDIR_ . 'Principal/Action/ServicoEnvioEmail.php';


/**
 * @class PrnOrdermServico
 * @author Ricardo Marangoni da mota <ricardo.mota@meta.com.br>
 * @since 21/09/2012
 * Camada de regras de negócio.
 */
class PrnOrdermServico {

    private $dao;    

    public function index() {
        
    }

    public function fechamentoOS($ordoid) {

        $emails = array();

        $email_cliente = $this->dao->getEmailCliente($ordoid);        

        if (!empty($email_cliente)) {
            array_push($emails, $email_cliente);
        }

        $regra_seguranca = false;
        $email_gerenciadoras = $this->dao->getEmailGerenciadora($ordoid);

        if (!empty($email_gerenciadoras)) {
            foreach ($email_gerenciadoras as $email_gerenciadora) {
                array_push($emails, $email_gerenciadora);
            }       

            // quando possui gerenciadora verifica Embarque Regras de Segurança
            $regra_seguranca =  $this->validaRegrasSeguranca($ordoid);
            
        }

        $emails_os = $this->dao->getEmailsOs($ordoid);

        if (!empty($emails_os)) {

            foreach ($emails_os as $email_os) {
                array_push($emails, $email_os);
            }
        }
        
        $status = $this->dao->getStatus($ordoid);
        $motivo_os = $this->dao->getMotivoOs($ordoid);
		$classe_contrato = $this->dao->getClasseContrato($ordoid);

        // ASM-4772 - Baixa de Equipamento X E-mail Cliente
        // Não deve enviar e-mail para o cliente quando a OS for:
        // Retirada e Motivo Rescisão ou
        // Retirada e Motivo Rescisão por Inadimplência
        // Ou o contrato for 1069 - Mercedes-benz vans connect
        if (!empty($emails) && ($status != 9) && ($classe_contrato != 1069) && (strpos($motivo_os, 'RETIRADA-RESCISAO') === false)) {
            $this->sendEmailFechamentoOS($ordoid, $emails, $regra_seguranca);
        }
        
        if ($status != 9) {
        	$this->iniciarProcessoClienteIndicador($ordoid);
        }
    }

    public function sendEmailFechamentoOS($ordoid, $emails, $regra_seguranca = false) {
                
        $placa = $this->dao->getPlacaVeiculoByOs($ordoid);
        $nome_instalador = $this->dao->getNomeInstaladorByOs($ordoid);
        $lista_email = $emails;
        //array com os destinatarios do email
        /*if ($_SESSION['servidor_teste'] == 1) {
            $lista_email = array("ricardo.mota@meta.com.br");
        } else {
            $lista_email = $emails;
        }*/

        $mail = new PHPMailer();        

        $mail->IsSMTP();
        $mail->From = "sistema@sascar.com.br";
        $mail->FromName = "Intranet SASCAR - E-mail automático";
        
        if($regra_seguranca){

            // Utilizar cliemail_nfe2 apenas quando for enviar e-mail referente a conclusão de OS com regras Telemetria
            $email_cliente_nf2 = $this->dao->getEmailClienteNf2($ordoid);
            if (!empty($email_cliente_nf2)) {
                array_push($lista_email, $email_cliente_nf2);
            }

        	$mail->Subject = "Configurar Telemetria Veículo Placa $placa - Ordem de Serviço concluída Nº $ordoid";
        	
        	$mail->MsgHTML("
        			A Ordem de Serviço $ordoid referente ao veículo placa $placa foi
        			concluída na data " . date('d/m/Y à\s H:i:s') . " pelo instalador
        			$nome_instalador
	        		
	        	<br><br><b>Atenção: Devido à troca de equipamento (no caso de serviços de assistência técnica) 
	        		ou instalação de equipamento (no caso de serviços de Instalação ou Reinstalação), 
	        		será necessário o embarque das regras de segurança e das configurações de telemetria 
	        		(se houver) para este veículo.</b>

                    <br><br><b style=\"color: red;\">Atenção: Necessário configurar telemetria do veículo.</b>

                    <br><br><br><br>
                    SASCAR TECNOLOGIA E SEGURANÇA AUTOMOTIVA SA
                    <br>41. 3299.6000
                    <br>0800 41 6004
                    <br>Paixao Pela Inovação
        			");
        	
        }else{
        	
	        $mail->Subject = "Ordem de Serviço concluída Nº $ordoid";
	
	        $mail->MsgHTML("
		            A Ordem de Serviço $ordoid referente ao veículo placa $placa foi 
		            concluída na data " . date('d/m/Y à\s H:i:s') . " pelo instalador 
		            $nome_instalador
	        ");
	        
        }
        
        // remover possíveis duplicados
        $lista_email = array_unique($lista_email);
        $lista_email_envios = array();
        //adiciona os destinatarios
        foreach ($lista_email as $destinatario) {
            if($this->validaEmail($destinatario)) {                  
                $mail->ClearAllRecipients();
                $mail->AddAddress($destinatario);
                $mail->Send();
                array_push($lista_email_envios, $destinatario);
            }
        }

        // ASM-4689
        // REGISTRA ENVIO E-MAIL FECHAMENTO OS
        $msg = "E-mail fechamento OS enviado: \n";
        $msg .= "    Enviado em: " . $this->formataData() . "\n";
        $msg .= "    Enviado para: " . implode(',', $lista_email_envios) . "\n";
        $msg .= "    Assunto: " . $mail->Subject . "\n";
        $msg .= $mail->Body;
                        
        $msg = nl2br($msg);
        $status = $this->dao->getStatus($ordoid);
        $historicoOS = $this->dao->registraHistoricoOS($ordoid, $msg, $status);

    }

    private function validaEmail($email) {
        if (substr_count($email, "@") == 0) {
            // Verifica se o e-mail possui @
            return false;
        }
        
        $parseEmail = explode("@", $email);
        
        if (strlen($parseEmail[0]) < 1) {
            //Verifica se o email tem mais de 1 caracter antes do @
            return false;
        }
        
        if (!checkdnsrr($parseEmail[1], "MX")) {
            // Verificar se o domínio existe 
            return false;
        }
        
        return true;
    }

    public function PrnOrdermServico() {
        $this->dao = new PrnOrdermServicoDAO();
    }
    
    /**
     * @author Leandro Ivanaga
     * Função para envio de e-mail.
     * Deve enviar email os clientes que tiverem em algum momente a situação como inadimplente e agora esta como autorizada ou cancelada
     * STI 80439.
     */
    public function enviarEmail($ordoid){
    	
    	$status = $this->dao->getStatus($ordoid);
    	
    	$envio = true;
    	
    	switch ($status){
    		case '4':
    			$titulo = "O.S. - Autorizacao apos inadimplencia";
    			break;
    		case '9':
    			$titulo = "O.S. - Cancelamento apos inadimplencia";
    			break;
    		default:
    			$envio = false;
    	}
    	
    	//Verificar se a ordem em algum momento esteve com Status Aguardando Autorização Cobrança
    	$texto = "Status da ordem de serviço alterado de Aguardando Autorização Cobrança para Aguardando Autorização.";
    	$historico	= $this->dao->verificaHistorico($ordoid, $texto);
    	
    	if ($envio == true && $historico > 0){
    		
	    	$this->SendLayoutEmails = new SendLayoutEmails();
			$this->ServicoEmail = new ServicoEnvioEmail();
									
			$dadosLayout = $this->SendLayoutEmails->getTituloFuncionalidade($titulo);
				
			$dadosEmail['seeseefoid'] = $dadosLayout[0]['funcionalidade_id'];
			$dadosEmail['seeseetoid'] = $dadosLayout[0]['titulo_id'];
			
			$codigoLayout = $this->SendLayoutEmails->buscaLayoutEmail($dadosEmail);
			$layout = $this->SendLayoutEmails->getLayoutEmailPorId($codigoLayout['seeoid']);
			$servidor = $layout['seesrvoid'];
									
			$corpo_envio = $layout['seecorpo'];
						
			$dadosCliente	= $this->dao->dadosCliente($ordoid);
			
			$clioid = $dadosCliente['clioid'];
			$dadosOS		= $this->dao->dadosOS($clioid, $ordoid);
			
			$corpo_envio = str_replace('[DIA]',		date('d'),		$corpo_envio);
			$corpo_envio = str_replace('[MES]',		$this->getMes(),		$corpo_envio);
			$corpo_envio = str_replace('[ANO]',		date('Y'),		$corpo_envio);
			$corpo_envio = str_replace('[CONTRATANTE]', $dadosCliente['clinome'],	$corpo_envio);
			$corpo_envio = str_replace('[NOME_CONTRATANTE]',$dadosCliente['clinome'],$corpo_envio);
			$corpo_envio = str_replace('[OS]',		$ordoid,	$corpo_envio);
			$corpo_envio = str_replace('[PLACA]',	$dadosOS['veiplaca'],		$corpo_envio);
			$corpo_envio = str_replace('[CHASSI]',	$dadosOS['veichassi'],		$corpo_envio);
			$corpo_envio = str_replace('[CONTRATO]', $dadosOS['ordconnumero'],		$corpo_envio);
			
			// Faturas
			$corpo_envio = str_replace('[FATURA]',	$dadosFaturas[0]['titoid'],		$corpo_envio);
			$corpo_envio = str_replace('[VALOR_FATURA]',$dadosFaturas[0]['titvl_titulo'],	$corpo_envio);

			// REALIZA ENVIO DO EMAIL.
			$envio = $this->ServicoEmail->enviarEmail(
									$dadosCliente['cliemail'], 
									$layout['seecabecalho'], 
									$corpo_envio, 
									'',
									'',
									'',
									$servidor,
									'teste_desenv@sascar.com.br'
					);
			
			// Envio ocorreu com sucesso
			// Salva o texto do envio no historio da OS
			if ($envio['erro'] == null){
				$dadosRemetente = $this->dao->dadosRemetente($servidor);
				
				$msg = "E-mail enviado para o cliente: \n";
				$msg .= "    De: " . $dadosRemetente['srvremetente_email'] . "\n";
				$msg .= "    Enviado em: " . $this->formataData() . "\n";
				$msg .= "    Enviado para: " . $dadosCliente['cliemail'] . "\n";
				$msg .= "    Assunto: " . $layout['seecabecalho'] . "\n";
				$msg .= $corpo_envio;
								
				//$msg = pg_escape_string($msg);
	
				$msg = nl2br($msg);
				$historicoOS = $this->dao->registraHistoricoOS($ordoid, $msg, $status);
			}
    	}
    }
    /**
     * Função para retornar a data com o formato correto
     */
    private function formataData(){
    	//EXEMPLO -> Segunda-feira, 12 de agosto de 2013 13:20
    
    	// Concatena a data da semana
    	$data = $this->getDiaSemana();
    
    	// Concatena o dia
    	$data .= Date("d");
    
    	// Concatena o mês
    	$data .= ' de ' . $this->getMes() . ' de ';
    
    	// Concatena o ano
    	$data .= Date("Y");
    
    	return $data;
    }
    
    // Retorna o dia da semana em extenso
    private function getDiaSemana(){
    
    	$dia_semana = Date("w");
    	$dia_semana++;
    
    	switch ($dia_semana){
    		case 1:
    			$dia = "Domingo, ";
    			break;
    		case 2:
    			$dia = "Segunda-feira, ";
    			break;
    		case 3:
    			$dia = "Terça-feira, ";
    			break;
    		case 4:
    			$dia = "Quarta-feira, ";
    			break;
    		case 5:
    			$dia = "Quinta-feira, ";
    			break;
    		case 6:
    			$dia = "Sexta-feira, ";
    			break;
    		case 7:
    			$dia = "Sábado, ";
    			break;
    	}
    
    	return $dia;
    }
    
    // Retorna o mes em extenso
    private function getMes(){  
        // Mês sem o zero inicial
        switch (Date("n")){
            case 1:
                $mes = "Janeiro";
                break;
            case 2:
                $mes = "Fevereiro";
                break;
            case 3:
                $mes = "Março";
                break;
            case 4:
                $mes = "Abril";
                break;
            case 5:
                $mes = "Maio";
                break;
            case 6:
                $mes = "Junho";
                break;
            case 7:
                $mes = "Julho";
                break;
            case 8:
                $mes = "Agosto";
                break;
            case 9:
                $mes = "Setembro";
                break;
            case 10:
                $mes = "Outubro";
                break;
            case 11:
                $mes = "Novembro";
                break;
            case 12:
                $mes = "Dezembro";
                break;
        }
        
        return $mes;
    }
    

    /**
     * Inicia o processo de geração de crédito futuro ao cliente indicador
     *
     * @param int $ordoid
     * @throws Exception
     *
     */
    private function iniciarProcessoClienteIndicador($ordoid) {
    
    	$ordoid = (int)$ordoid;
    	$dadosHistorico  = new stdClass();
    
    	//DUM 81231
    	try {
    		$creditoIndicador = $this->dao->verificarElegivelAmigoIndicador($ordoid);
    
    		$dadosOs = $this->dao->buscarDadosOrdemServico($ordoid);
    		$clienteIndicador = $this->dao->atualizarClienteIndicador(null, $dadosOs->ordconnumero);
    
    		if ($creditoIndicador) {
    
    			$clienteIndicador = $this->dao->atualizarClienteIndicador($ordoid, $dadosOs->ordconnumero);
    			$this->dao->iniciarTransacao();
    
    			$campanha = $this->dao->buscarInformacaoesCampanha($clienteIndicador->cfcicfcpoid);
    			$dadosClienteIndicador = $this->dao->buscarDadosIndicador($clienteIndicador->cfciclioid);
    
    			$dadosHistorico->connumero          = $dadosOs->ordconnumero;
    			$dadosHistorico->campanha           = $clienteIndicador->cfcicfcpoid;
    			$dadosHistorico->cliente            = $clienteIndicador->cfciclioid;
    			$dadosHistorico->usuario            = 2750;
    			$dadosHistorico->cfmcoid            = $campanha->cfmcoid;
    			$dadosHistorico->cfcpobroid         = $campanha->cfcpobroid;
    			$dadosHistorico->cfcptipo_desconto  = $campanha->cfcptipo_desconto;
    			$dadosHistorico->cfcpdesconto       = $campanha->cfcpdesconto;
    			$dadosHistorico->cfcpqtde_parcelas  = $campanha->cfcpqtde_parcelas;
    			$dadosHistorico->cfcpaplicacao      = $campanha->cfcpaplicacao;
    			$dadosHistorico->cfcpobservacao     = $campanha->cfcpobservacao;
    			$dadosHistorico->cfcpaplicar_sobre  = $campanha->cfcpaplicar_sobre;
    			$dadosHistorico->cfmcdescricao      = $campanha->cfmcdescricao;
    			$dadosHistorico->obrobrigacao       = $campanha->obrobrigacao ;
    			$dadosHistorico->cfostatus          = 1;
    			$dadosHistorico->cfoforma_inclusao  = 2; //automática
    			$dadosHistorico->cfooid             = $this->dao->gerarCreditoFuturoClienteIndicador($dadosHistorico);
    			$dadosHistorico->nome_instalador    = '';
    			$dadosHistorico->cpf_instalador     = '';
    			$dadosHistorico->nome_indicador     = $dadosClienteIndicador->clinome;
    			$dadosHistorico->cfhsaldo_parcelas	= $campanha->cfcpqtde_parcelas . ' x ' . (($campanha->cfcptipo_desconto==1) ? $campanha->cfcpdesconto.' %' : 'R$ '.$campanha->cfcpdesconto);
    
    			if($dadosClienteIndicador->clitipo == 'J') {
    				$dadosHistorico->doc_indicador  =  $this->formatarCnpjCpf('##.###.###/####-##', $dadosClienteIndicador->cpf_cnpj);
    			}
    			else {
    				$dadosHistorico->doc_indicador  =  $this->formatarCnpjCpf('###.###.###-##', $dadosClienteIndicador->cpf_cnpj);
    			}
    
    
    			if (!empty($dadosOs->orditloid)) {
    				$dadosInstalador = $this->dao->buscarDadosInstalador($dadosOs->orditloid);
    				$dadosHistorico->nome_instalador = $dadosInstalador->itlnome;
    				$dadosHistorico->cpf_instalador = $this->formatarCnpjCpf('###.###.###-##', $dadosInstalador->itlno_cpf);
    			}
    
    
    			//Grava os históricos
    			$obs =  $this->montarHistoricoCliente($dadosHistorico);
    			$this->dao->inserirHistoricoCliente($dadosHistorico, $obs);
    			$this->dao->inserirHistoricoContrato($dadosHistorico);
    			$this->dao->inserirHistoricoCreditoFuturo($dadosHistorico);
    		}
    
    		$this->dao->comitarTransacao();
    
    	} catch(Exception $e) {
    
    		$this->dao->abortarTransacao();
    		throw new Exception ($e->getMessage());
    	}
    
    }
    
    /**
     * Monta a observacao do Historico de cliente
     *
     * @param stdClass $dadosHistorico
     * @return string
     *
     */
    private function montarHistoricoCliente(stdClass $dadosHistorico) {
    
    	$aplicacao = ($dadosHistorico->cfcpaplicacao == 'I') ? 'Integral' : 'Parcelas';
    	$sobre     = ($dadosHistorico->cfcpaplicar_sobre == 1) ? 'Monitoramento' : 'Locação';
    	$valorFormatado =  number_format($dadosHistorico->cfcpdesconto, 2, ',', '.');
    	$tipoDesconto = ($dadosHistorico->cfcptipo_desconto == '2') ? 'R$ '. $valorFormatado : $valorFormatado.'%';
    
    	$obs = "Inclusão - encerramento de O.S : Crédito Futuro Cód. Identif.: ". $dadosHistorico->cfooid;
    	$obs .= " - ". $dadosHistorico->cfmcdescricao;
    	$obs .= " - Desc.: ". $tipoDesconto;
    	$obs .= " - Aplicação: ". $aplicacao;
    	$obs .= " - Sobre: ".$sobre;
    	$obs .= " Obrig. Financ. Descto: ". $dadosHistorico->obrobrigacao ;
    	$obs .= " Status: Aprovado";
    	$obs .= " Contrato Indicado: " . $dadosHistorico->connumero;
    
    	return $obs;
    
    }
    
    /**
     * Valida necessidade de ser emitido alerta para Embarque de Regras de Segurança para as Gerenciadoras
     *
     * @param string $ordoid
     * @return boolean
     */
    private function validaRegrasSeguranca($ordoid){
    	
    	$ordoid = (int)$ordoid;
    	
    	$info = $this->dao->buscarDadosOsContrato($ordoid);
    	
    	if(is_object($info)){
    		
	    	if($info->grupo == 2){
	    		// grupo classe == carga
	    		
	    		$servico = explode(',', $info->servico); // monta array para verificação
				if(in_array(4, $servico)){
					// assistencia 4
					
					if(!empty($info->equesn) && (!empty($info->troca) && $info->troca != 'f') ){
						// possui equipamento com linha e efetuou uma troca						
						return true;
						
					}elseif(!empty($info->consobroid) && in_array($info->consobroid, array(133, 1697, 288))){			
						// possui alguma obrigação financeira de telemetria no contrato: 
                        // 133 - LOCAÇÃO TELEMETRIA
                        // 1697 LOCAÇÃO TELEMETRIA CAN
                        // 288 - LOCACAO TELEMETRIA SASTM FULL
						
						$serv = $this->dao->buscarDadosServicosOS($ordoid);
						// possui serviço:
						// i.	Item: ACESSORIOS;
						// ii.	Tipo: ASSISTÊNCIA;
						// iii.	Motivo: TELEMETRIA (1) ;
						if(!empty($serv) && $serv->valido == 1){							
							return true;
						}else{
							return false;
						}
						
					}else{		
						return false;						
					}					
					
				}elseif(in_array(1, $servico) || in_array(2, $servico)){
					// instalacao 1 / reinstalacao 2 
					return true;
					
				}else{
					// serviço diferente de instalacao/reinstalacao/assistencia
					return false;
				}				
	    	
	    	}else{
	    		// diferente de carga
	    		return false;    		
	    	}
	    	
    	}else{
	    	// sem dados
    		return false;   
    	}
    }
    
    /**
     * Formata um CNPJ ou CPF
     *
     * @param string $mascara
     * @param string $string
     * @return string
     */
    public function formatarCnpjCpf($mascara,$codigo) {
    
    	$codigo = str_replace(" ","",$codigo);
    
    	for($i=strlen($codigo);$i>0;$i--) {
    
    		$mascara[strrpos($mascara,"#")] = $codigo[$i-1];
    	}
    
    	$mascara = str_replace("#", "0", $mascara);
    
    	return $mascara;
    }
}
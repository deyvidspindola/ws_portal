<?php

set_time_limit(0);
/**
 * Carrega Classes
 */
require _MODULEDIR_ . 'Cron/DAO/ordemServicoDAO.php';
include _MODULEDIR_ . 'Cadastro/Action/SendLayoutEmails.php';

include_once _MODULEDIR_ . 'Principal/Action/ServicoEnvioEmail.php';

/**
 * @class Action da rotina do cron para ordem de serviço
 * @author Rafael Barbeta da Silva <rafaelbarbetasilva@brq.com>
 * @since 01/07/2013
 */
class ordemServico {

	private $dao;

	public function __construct($conn){
		$this->dao	 = new ordemServicoDAO($conn);
	}

	/**
	 * Chama Model para registrar Histórico
	 **/
	public function registraHistoricoOS($ordoid, $msg){	
		$this->dao->registraHistoricoOS($ordoid, $msg);
	}

	
	/**
	* Busca Ordem de serviços que estão aguardando autorização de cobrança
	*  
 	* @return array $retorno
	*/
    public function getOsCobranca(){

		$getOsCobranca = $this->dao->getOsCobranca();
		
		return $getOsCobranca;
    }

	/**
	* Busca Ordem de serviços que estão com a data de agendamento do dia seguinte
	*  
 	* @return array $retorno
	*/
    public function getOsAgendamento(){

		$getOsAgendamento = $this->dao->getOsAgendamento();
		
		return $getOsAgendamento;
    }
	
	/**
	 * Busca o título do email na Paramentros_siggo
	 */
	public function getTituloParamSiggo($nome){
		
		$tituloParamSiggo = $this->dao->getTituloParamSiggo($nome);

		return $tituloParamSiggo[0]['parsvalor'];
	}

	/**
	* Confere inadimplência e atualiza status ou cancela Ordem de Serviço
	*  
 	* @return Array ('Erro OS', 'Nº OS')
	*/
	public function atualizarOrdemServico($arrayOS){
		
		$retorno['erro'] = false;
		$retorno['OS']   = false;
		$alteraOrdem	 = true;

		// STI 80439
		foreach($arrayOS as $val)
		{
			//$retorno['OS'][$val['ordoid']] = false;
			$retorno['OS'][$val['ordoid']] = array(
						'clioid' => $val['ordclioid']
					);

			// Confere inadimplência.
			// Caso o retorne maior que 1 então tem titulos nao pagos
			$titulos = $this->dao->conferePagamento($val['ordclioid']);

			// Confere validade OS
			// Caso retorne maior que 1 então a OS tem mais de 30 dias a partir da data de criação
			$ordemVencida = $this->dao->validadeOrdemServico($val['ordoid']);

			// Atualiza Status Aguardando Autorização
			if($titulos == 0){
				$msg = 'OS alterado automaticamente para Aguardando Autorização';
				$msg_historico = 'Status da ordem de serviço alterado de Aguardando Autorização Cobrança para Aguardando Autorização.';
				$retorno['OS'][$val['ordoid']]['status'] = 1;
				$alteraOrdem = $this->dao->atualizarOrdemServico($val['ordoid'], '1', $msg);
				$salvaHistorico = $this->dao->registraHistoricoOS($val['ordoid'], $msg_historico);
			}
			
			// Cancela Ordem Serviço 
			// Cliente possui titulos não pagos.
			// E a ordem possui mais de 30 dias de validade após criação.
			if(($titulos > 0) && ($ordemVencida > 0)){
				$msg_historico = 'O.S. cancelada por motivo de inadimplência que ultrapassou 30 dias.';
				$retorno['OS'][$val['ordoid']]['status'] = 9;
				$alteraOrdem = $this->dao->atualizarOrdemServico($val['ordoid'], '9', $msg_historico);
				$salvaHistorico = $this->dao->registraHistoricoOS($val['ordoid'], $msg_historico);
			}
			
			if(!$alteraOrdem){				
				$retorno['erro'] = $val['ordoid'];
				return $retorno;
			}
			
			// Se não tem alteração de status para o cliente remove do retorno
			if (empty($retorno['OS'][$val['ordoid']]['status'])) {
				unset ($retorno['OS'][$val['ordoid']] );
			}
		}

		return $retorno;
	}
	
	/**
	 * Registra histórico da OS conforme alteração
	 * @param array $ordem array(Nº Da Ordem => Status )
	 * @return bool
	 *
	 */
	public function registraHistorico($ordem){
	
		foreach($ordem as $ordoid => $val){
			$novoStatus = $val['status'];
						
			if($novoStatus == '1'){
				$msg = 'Status da ordem de serviço alterado de Aguardando Autorização Cobrança para Aguardando Autorização.';
				$historicoOS = $this->dao->registraHistoricoOS($ordoid, $msg);
			}
			
			if($novoStatus == '9'){
				$msg = 'O.S. cancelada por motivo de inadimplência que passou 30 dias.';
				$historicoOS = $this->dao->registraHistoricoOS($ordoid, $msg);
			}

			if(!$historicoOS){  
				return false;  
			}
		}
		return true;
	}
	
	/**
	 * Monta e envia e-mail informando cancelamento da OS
	 * @param array com OS's para carregar dados do cliente
	 *
	 */
	public function emailAviso($dados){

		$countsEnvios = 0;
		
		$this->SendLayoutEmails = new SendLayoutEmails();
		$this->ServicoEmail = new ServicoEnvioEmail();
				
		$titulo = "O.S. - Cancelamento apos inadimplencia acima de 30 dias";
			
		$dadosLayout = $this->SendLayoutEmails->getTituloFuncionalidade($titulo);
			
		$dadosEmail['seeseefoid'] = $dadosLayout[0]['funcionalidade_id'];
		$dadosEmail['seeseetoid'] = $dadosLayout[0]['titulo_id'];
	
		$codigoLayout = $this->SendLayoutEmails->buscaLayoutEmail($dadosEmail);

		$layout = $this->SendLayoutEmails->getLayoutEmailPorId($codigoLayout['seeoid']);
		
		$servidor = $layout['seesrvoid'];
				

		// Enviar o email para o cliente
		foreach($dados as $ordoid => $val){

			// Só será enviado email para os clientes que tiveram a ordem cancelada
			// Status alterado altomaticamente para cancelado
			if ($val['status'] == 9) {
								
				$clioid = $val['clioid'];
								
				$corpo_envio = $layout['seecorpo'];
				
				$dadosCliente	= $this->dao->dadosCliente($clioid);
				$dadosOS		= $this->dao->dadosOS($clioid, $ordoid);
				$dadosFaturas	= $this->dao->dadosFatura($clioid);
				// Monta tabela com as faturas
				if (!empty($dadosFaturas) && is_array($dadosFaturas)){
					$tableFaturas = "<table><tr><td align=\"center\" width=\"100px\"> Código </td><td align=\"center\" width=\"100px\"> Valor R$ </td><td align=\"center\" width=\"100px\"> Vencimento </td></tr>";
					$linha = "";
					foreach ($dadosFaturas AS $fatura){
						$linha = "<tr><td align=\"center\">";

						$linha .= $fatura['titoid'];
						$linha .= "</td><td align=\"center\">";
						$linha .= $this->converteDinheiro($fatura['titvl_titulo']);
						$linha .= "</td><td align=\"center\">";
						$linha .= $fatura['vencimento'];
						$linha .= "</td><td align=\"center\">";
						$linha .= "</td></tr>";
						
						$tableFaturas .= $linha;
					}
					$tableFaturas .= "</table>";
				} 
					
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
				$corpo_envio = str_replace('[FATURA]', $tableFaturas,		$corpo_envio);
							
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
					
					$countsEnvios++;
					
					$dadosRemetente = $this->dao->dadosRemetente($servidor);
										
					$msg = "E-mail enviado para o cliente: \n";
					$msg .= "    De: " . $dadosRemetente['srvremetente_email'] . "\n";
					$msg .= "    Enviado em: " . $this->formataData() . "\n";
					$msg .= "    Enviado para: " . $dadosCliente['cliemail'] . "\n";
					$msg .= "    Assunto: " . $layout['seecabecalho'] . "\n";
					$msg .= $corpo_envio;
									
					$msg = nl2br($msg);
					
					$historicoOS = $this->dao->registraHistoricoOS($ordoid, $msg);
				}
			}
		}
		return $countsEnvios;
	}

	// Busca se existe histórico de envio de e-mail para a OS
	public function getEmailOsAgenda($ordoid){

		// Busca {Envio do lembrete de agendamento enviado com sucesso}
		// para conferir se existe Histórico de envio de e-mail
		$getEmailOSAgenda = $this->dao->getEmailOSAgenda($ordoid);
		
		$retorno = ($getEmailOSAgenda > 0) ? true : false;		
		return $retorno;
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
	
	private function converteDinheiro($valor) {
		$valor = number_format($valor, 2, ',', '.');
		return $valor;
	}
}
<?php
header("Content-Type: text/html; charset=ISO-8859-1",true);

//include_once("lib/phpMailer/class.phpmailer.php");
include_once("modulos/Principal/Action/ServicoEnvioEmail.php");
require_once("lib/funcoes.php");
require_once("includes/php/auxiliares.php");
include_once("lib/nusoap.php");
require("modulos/Relatorio/DAO/RelInsucessoDiscadorDAO.php");


class RelInsucessoDiscador
{
	private $dao;
	public $ligddt_ligacao_ini;
	public $ligddt_ligacao_fin;
	public $clinome;
	public $comp_insucessos;
	public $qtde_insucessos;
	public $ligdcampanha;
	public $tipo_envio;
	public $ligdoid;
	public $cd_usuario;
	public $mensagem;
	public $countInsucessos;
	public $insucessos;
	public $contagem;
	public $where;
	public $where2;
	public $msg;
	public $countAjax;
	public $retornoListagem;
	public $tipo_proposta;
	public $sub_tipo_proposta;
	public $tipo_contrato;
	
	public function RelInsucessoDiscador() {
		
		global $conn;
		
		$this->dao = new RelInsucessoDiscadorDAO($conn);
		
		$this->countAjax 			= (isset($_POST["countAjax"])) 				? trim($_POST["countAjax"]) 			: 0;
		$this->ligddt_ligacao_ini 	= (isset($_POST["ligddt_ligacao_ini"])) 	? trim($_POST["ligddt_ligacao_ini"]) 	: "";
		$this->ligddt_ligacao_fin 	= (isset($_POST["ligddt_ligacao_fin"])) 	? trim($_POST["ligddt_ligacao_fin"]) 	: "";
		$this->clinome 				= (isset($_POST["clinome"])) 				? trim($_POST["clinome"]) 				: "";
		$this->comp_insucessos 		= (isset($_POST["comp_insucessos"])) 		? trim($_POST["comp_insucessos"]) 		: "";
		$this->qtde_insucessos 		= (isset($_POST["qtde_insucessos"])) 		? ($_POST["qtde_insucessos"]) 			: "";
		$this->ligdcampanha 		= (isset($_POST["ligdcampanha"])) 			? trim($_POST["ligdcampanha"]) 			: "";
		$this->tipo_envio 			= (isset($_POST["tipo_envio"])) 			? trim($_POST["tipo_envio"]) 			: "";
		$this->ligdoid 				= (isset($_POST["ligdoid"])) 				? $_POST["ligdoid"] 					: "";
		$this->cd_usuario 			= $_SESSION['usuario']['oid'];
		$this->ura 					= (isset($_POST["ura"])) 					? (int) $_POST["ura"] 					: "";
		$this->tipo_proposta        = (isset($_POST["tipo_proposta"])) 		    ? ($_POST["tipo_proposta"]) 			: "";
		$this->sub_tipo_proposta    = (isset($_POST["sub_tipo_proposta"])) 		? ($_POST["sub_tipo_proposta"]) 		: "";
		$this->tipo_contrato        = (isset($_POST["tipo_contrato"])) 		    ? ($_POST["tipo_contrato"]) 			: "";
		
		$nomeEmpresa = "SASCAR";
		
		if (!empty($this->tipo_proposta)){
		
			//recupera os dados de configuração da empresa (por tipo de proposta) para o envio de sms
			$dados_empresa = $this->dao->getDadosEmpresaProposta($this->tipo_proposta);
			
			if(!is_object($dados_empresa)){
				//se não existe dados por tipo de proposta, então busca os dados da empresa padrao
				$dados_empresa = $this->dao->getDadosEmpresaPadrao();
			}
				
			if(!is_object($dados_empresa)){
				throw new exception($dados_empresa);
			}
			
			$nomeEmpresa = $dados_empresa->nome_remetente;

		}

		//monta a mensagem padrão de acordo o nome da empresa encontrado
		$mensagem_txt = "Prezado Cliente, não estamos conseguindo contato com o senhor.\nFavor entrar em contato com a $nomeEmpresa.\nObrigado.";
		
		$this->mensagem  = (isset($_POST["mensagem"])) ? $_POST["mensagem"] : $mensagem_txt;

		$this->insucessos = array();
		
		$this->where = "";
		$this->where2 = "";
		if (!empty($this->ligddt_ligacao_ini) && !empty($this->ligddt_ligacao_fin)){
			if (!validaVar($this->ligddt_ligacao_ini, "data")){
				throw new exception("A data inicial do Período é inválida!");
			}
			if (!validaVar($this->ligddt_ligacao_fin, "data")){
				throw new exception("A data final do Período é inválida!");
			}
			$txt = " AND ligddt_ligacao BETWEEN '".$this->ligddt_ligacao_ini." 00:00:00' AND '".$this->ligddt_ligacao_fin." 23:59:59'";
			$this->where .= $txt;
			$this->where2 .= $txt;
		}
		if (!empty($this->clinome)){
			$txt = " AND clinome ILIKE '%".$this->clinome."%'";
			$this->where .= $txt;
			$this->where2 .= $txt;
		}
		/*if ($this->comp_insucessos && ($this->qtde_insucessos || $this->qtde_insucessos=="0")){
			$txt = " AND (SELECT COUNT(ldc.ligdoid) FROM ligacoes_discador as ldc INNER JOIN ligacoes_discador_status ON ligdldsoid = ldsoid WHERE ldc.ligdconnumero = ld.ligdconnumero AND ldsdt_exclusao IS NULL AND ldsinsucesso = TRUE ".$this->where.")".$this->comp_insucessos.$this->qtde_insucessos;
			$this->where .= $txt;
			$this->where2 .= $txt;
		}*/
		if (!empty($this->ligdcampanha)){
			$txt = " AND ligdcampanha ILIKE '%".$this->ligdcampanha."%'";
			$this->where .= $txt;
			$this->where2 .= $txt;
		}
		if (!empty($this->tipo_envio)){
			if ($this->tipo_envio == "sem_envio"){
				$txt = " AND ligdconnumero NOT IN (SELECT ligdconnumero FROM ligacoes_discador WHERE ligdtipo_envioemail IS NOT NULL OR ligdtipo_enviosms IS NOT NULL AND ligddt_ligacao BETWEEN '".$this->ligddt_ligacao_ini." 00:00:00' AND '".$this->ligddt_ligacao_fin." 23:59:59') ";
				$this->where .= $txt;
			}
			else if($this->tipo_envio == "SMS"){
				$txt = " AND ligdtipo_enviosms = '".$this->tipo_envio."'";
				$this->where .= $txt;
			}
			else
			{
				$txt = " AND ligdtipo_envioemail = '".$this->tipo_envio."'";
				$this->where .= $txt;
			}
		}
		if ($this->ura == 1){
			$txt = " AND ligdenvio_atendimento = 't'";
			$this->where .= $txt;
			$this->where2 .= $txt;
		}
		
		if (!empty($this->sub_tipo_proposta)){
			$txt = " AND prptppoid = $this->sub_tipo_proposta ";
			$this->where .= $txt;
			$this->where2 .= $txt;
			
		}elseif (!empty($this->tipo_proposta)){
			$txt = " AND ( prptppoid =  $this->tipo_proposta OR t2.tppoid_supertipo = $this->tipo_proposta ) ";
			$this->where .= $txt;
			$this->where2 .= $txt;
		}
		
		if (!empty($this->tipo_contrato)){
			$txt = " AND conno_tipo = $this->tipo_contrato";
			$this->where .= $txt;
			$this->where2 .= $txt;
		}
		
	}
	
	public function index() {
		
	}	
	
	//Acoes -----------------------
	
	public function enviar_email() {
		try{
			
			$this->msg = "";
			$envio = "";
			foreach ($this->ligdoid as $id){
				// Varre todos os checkbox marcados
				$rsCliente = $this->dao->atribCliente($id);	
				$placa = $rsCliente["placa"];
				
				//pega email válido, os emails do desenvolvimento são inválidos
				if($_SESSION['servidor_teste'] == 1){
					//recupera email de testes da tabela parametros_configuracoes_sistemas_itens
					$emailTeste = $this->dao->getEmailTeste();
					$rsCliente["cliemail"] = $emailTeste->pcsidescricao;
				}

				if ($this->validaEmail($rsCliente["cliemail"]) === false){
					// Valida o endereço de e-mail
					$this->msg .= "".$rsCliente["clinome"]." - E-mail inválido.<br />&nbsp;&nbsp;&nbsp;";
				}
				else {
					$msg_envio = "Placa: ".$placa."\n\n".utf8_decode($this->mensagem);
					
					// Envia e-mail ao cliente
					$envio = $this->enviaEmail($rsCliente["cliemail"], $msg_envio);

					if ($envio === false){
						// Se retornou algum erro adiciona o erro a mensagem
						$this->msg .= "".$rsCliente["clinome"]." - Erro ao enviar e-mail.<br />&nbsp;&nbsp;&nbsp;";
					}
					else{
						// Recupera dados para gravar no histórico
						$rsInsucesso = $this->dao->buscaDadosInsucesso($rsCliente['ligdconnumero']);

						// Senão grava a ação no histórico						
						$this->gravaHistorico("E-MAIL", $id, $rsCliente["ligdtipo"], $rsCliente["ligdconnumero"], $rsCliente["ligdordoid"], $rsInsucesso);
					}
				}
			}
			if (!empty($this->msg)){
				throw new Exception($this->msg);
			}
			else{
				$this->msg = "E-mail(s) enviado(s) com sucesso";
			}
			
			$retorno = array(
					"erro"				=> 0,
					"msg"				=> utf8_encode($this->msg)
			);
			
			echo json_encode($retorno);
			exit;
			
		}
		catch (Exception $e){
			
			$retorno = array(
					"erro"				=> 1,
					"msg"				=> utf8_encode($e->getMessage())
			);
				
			echo json_encode($retorno);
			exit;
		}
	}
	
	public function enviar_sms() {
		try{
			
			$this->msg = "";
			$envio = "";
			$xml = "";
			foreach ($this->ligdoid as $id){
				// Varre todos os checkbox marcados
				$rsTelefone = $this->dao->atribTelefone($id);
				
				// Remove caracteres do telefone
				$caracteres = array("(", ")", "-", " ");
				$telefone = str_replace($caracteres, "", $rsTelefone["telefone_celular"]);
				if (!$this->validaTelefone($telefone)){
					// Se o telefone é inválido adiciona o nome do cliente a mensagem de erro
					$this->msg .= "<p>".$id." - Número incorreto.</p>";
				}else{
					$msg_envio = "Placa: ".$rsTelefone["placa"]."\n\n".$this->mensagem;
					
					// Se o telefone é válido envia a mensagem, o envio é unitário para poder tratar o retornor de cada mensangem
					$envio = $this->enviaSMS($telefone, $msg_envio);
					if ($envio == "OK"){
						// Se foi enviado com sucesso grava a ação no histórico						
						$rsInsucesso =$this->dao->buscaDadosInsucesso($rsTelefone['ligdconnumero']);

						$this->gravaHistorico("SMS", $id, $rsTelefone["ligdtipo"], $rsTelefone["ligdconnumero"], $rsTelefone["ligdordoid"], $rsInsucesso);
					}
					else{
						// Senão informa que o houve erro no envio
						$this->msg .= "<p>".$id." - Mensagem não enviada.</p>";
					}
				}
			}
			if (empty($this->msg)){
				$this->msg = "SMS(s) enviado(s) com sucesso";
			}
			
			$retorno = array(
					"erro"				=> 0,
					"msg"				=> utf8_encode($this->msg)
			);
				
			echo json_encode($retorno);
			exit;
			
		}
		catch (Exception $e){
			
			$retorno = array(
					"erro"				=> 1,
					"msg"				=> utf8_encode($e->getMessage())
			);
				
			echo json_encode($retorno);
			exit;
		}
	}
	
	/**
	 * Recupera todos os tipos de propostas cadastradas
	 * @author Márcio Sampaio Ferreira <marcioferreira@brq.com>
	 * 05/06/2013
	 * @return array;
	 */
	public function getTipoProposta(){
		
			return $this->dao->getTipoProposta();
	}
	
	/**
	 * Recupera todos os tipos de contratos cadastrados
	 * @author Márcio Sampaio Ferreira <marcioferreira@brq.com>
	 * 05/06/2013
	 * @return array;
	 */
	public function getTipoContrato(){
	
		return $this->dao->getTipoContrato();
	
	}
	
	/**
	 * Recupera todos os sub tipos de propostas cadastradas
	 * @author Márcio Sampaio Ferreira <marcioferreira@brq.com>
	 * 05/06/2013
	 * 
	 * @param  idTipoProsposta int
	 * @param  ajax boolean
	 * @return array se $ajax for true
	 * @return jason se $ajax for false
	 * 
	 */
	public function getSubTipoProposta(){
		
		$tipoProposta = (isset($_POST["idTipoProsposta"])) 	? trim($_POST["idTipoProsposta"]) 	: "";
		$ajax         = (isset($_POST["ajax"])) ? trim($_POST["ajax"]) 	: "";
		
		$retorno = $this->dao->getSubTipoProposta($tipoProposta);
		
		if($ajax){
			echo json_encode($retorno);
			exit;
			
		}else{
			return $retorno;
		}
	}
	
	
	public function pesquisar() {
		try{
			
			$count = (!$this->countAjax) ? 0 : $this->countAjax;
			
			$this->geraListagem($count);

			$resultado = array();
			
			$id = 0;
			
			foreach ($this->insucessos AS $insucesso) {	
				
				$escapar = false;

				if ($this->comp_insucessos && ($this->qtde_insucessos || $this->qtde_insucessos=="0")){
					if (trim($this->comp_insucessos) == "=") {
						$escapar = true;
						if ($this->contagem[$insucesso["ligdconnumero"]] == $this->qtde_insucessos) {
							$escapar = false;
						}
					}
					elseif (trim($this->comp_insucessos) == ">=") {
						$escapar = true;
						if ($this->contagem[$insucesso["ligdconnumero"]] >= $this->qtde_insucessos) {
							$escapar = false;
						}
					}
					elseif (trim($this->comp_insucessos) == "<=") {
						$escapar = true;
						if ($this->contagem[$insucesso["ligdconnumero"]] <= $this->qtde_insucessos) {
							$escapar = false;
						}
					}
					elseif (trim($this->comp_insucessos) == ">") {
						$escapar = true;
						if ($this->contagem[$insucesso["ligdconnumero"]] > $this->qtde_insucessos) {
							$escapar = false;
						}
					}
					elseif (trim($this->comp_insucessos) == "<") {
						$escapar = true;
						if ($this->contagem[$insucesso["ligdconnumero"]] < $this->qtde_insucessos) {
							$escapar = false;
						}
					}
				}
				
				if (!$escapar) {
				
					$resultado[$id]["ligdoid"]				= ($insucesso["ligdoid"]);
					$resultado[$id]["ligdtipo"]				= ($insucesso["ligdtipo"]);
					$resultado[$id]["clinome"]				= ($insucesso["clinome"]);
					$resultado[$id]["ligddt_ligacao"]		= ($insucesso["ligddt_ligacao"]);
					$resultado[$id]["ligdconnumero"]		= ($insucesso["ligdconnumero"]);
					$resultado[$id]["ldsdescricao"]			= ($insucesso["ldsdescricao"]);
					$resultado[$id]["ligdcampanha"]			= ($insucesso["ligdcampanha"]);
					$resultado[$id]["ligddt_envioemail"]	= ($insucesso["ligddt_envioemail"]);
					$resultado[$id]["ligddt_enviosms"]		= ($insucesso["ligddt_enviosms"]);
					$resultado[$id]["ligdtipo_enviosms"]	= ($insucesso["ligdtipo_enviosms"]);
					$resultado[$id]["ligdtipo_envioemail"]	= ($insucesso["ligdtipo_envioemail"]);
					$resultado[$id]["contador"]				= ($this->contagem[$insucesso["ligdconnumero"]]);
					$resultado[$id]["tipo_envio"]			= ($this->tipo_envio);
					$resultado[$id]["tipo_proposta"]	    = ($insucesso["tipo_proposta"]);
					
					$id++;
				}
			}
			
			$this->countInsucessos = $id;
			
			if ($this->countInsucessos == 0){
				throw new exception("Nenhum resultado encontrado.");
			}
			
			$retorno = array(
					"erro"				=> 0,
					"msg"				=> "",
					"countInsucessos"	=> $this->countInsucessos,
					"resultado"			=> $resultado
			);
			
			//echo json_encode($retorno);
	        //exit;
	        $this->retornoListagem = $retorno;
		}
		catch (Exception $e){
			
			$retorno = array(
					"erro"				=> 1,
					"msg"				=> utf8_encode($e->getMessage()),
					"countInsucessos"	=> $this->countInsucessos,
					"resultado"			=> array()
			);
				
			//echo json_encode($retorno);
			//exit;
			$this->retornoListagem = $retorno;
			
		}
	}
	
	// ----------------------------

	/**
	 * Gera listagem
	 */
	private function geraListagem($count) {			
		$this->insucessos = $this->dao->gerarListagem($this->where, $count);
				
		if (!is_array($this->contagem)) {
			$this->contagem = array();
			$this->contagem = $this->dao->gerarContagem($this->where2);
			$this->countInsucessos = $this->dao->retornaNumeroRegistros($this->where);
		}
	}
	
	/**
	 * Remove acentuação de um string
	 * @param unknown_type $str
	 * @return unknown
	 */
	private function removeAcentos($str){
		
		$busca     = array("à","á","ã","â","ä","è","é","ê","ë","ì","í","î","ï","ò","ó","õ","ô","ö","ù","ú","û","ü","ç", "'", '"','º','ª','°', '&','´','`');
		$substitui = array("a","a","a","a","a","e","e","e","e","i","i","i","i","o","o","o","o","o","u","u","u","u","c", "" , "" ,'' ,'' ,'', '','','');
		$str       = str_replace($busca,$substitui,$str);
		$busca     = array("À","Á","Ã","Â","Ä","È","É","Ê","Ë","Ì","Í","Î","Ï","Ò","Ó","Õ","Ô","Ö","Ù","Ú","Û","Ü","Ç","‡","“", "<", ">" );
		$substitui = array("A","A","A","A","A","E","E","E","E","I","I","I","I","O","O","O","O","O","U","U","U","U","C", ""  ,"" , "" , "");
		$str       = str_replace($busca,$substitui,$str);
		
		return $str;
	}
	
	/**
	 * Formata número de telefone
	 * @param unknown_type $numero
	 * @return string
	 */
	private function formataTelefone($numero){
		$numero = "(".substr($numero, 0, 2).")".substr($numero, 2, 4)."-".substr($numero, 6, 4);
		return $numero;
	}
	
	/**
	 * Válida um endereço de e-mail
	 * @param unknown_type $email
	 * @return boolean
	 */
	private function validaEmail($email){
		if (substr_count($email , "@") == 0){
			// Verifica se o e-mail possui @
			return false;
		}
		$parseEmail = explode("@", $email);
		if (strlen($parseEmail[0]) < 3){
			//Verifica se o email tem mais de 3 caracteres
			return false;
		}
		if (!checkdnsrr($parseEmail[1], "MX")){
			// Verificar se o domínio existe
			return false;
		}
		return true;
	}
	
	/**
	 * Envia um e-mail
	 * @param unknown_type $email
	 * @param unknown_type $mensagem
	 * @return boolean
	 */
	private function enviaEmail($email, $mensagem){
		
		try{
			//instânica classe de configurações de servidores para envio de email de acordo a proposta
			$servicoEnvioEmail = new ServicoEnvioEmail();
	
			//recupera os dados de configuração do servidor para o envio de e-mails
			$servidor_email = $this->dao->getDadosEmpresaProposta($this->tipo_proposta);
			
			if(!is_object($servidor_email)){
				//se não existe dados por tipo de proposta, então busca os dados no servidor padrão
				$servidor_email = $this->dao->getDadosEmpresaPadrao();
			}
			
			if(!is_object($servidor_email)){
				throw new exception($servidor_email);
			}
			
			//recupera email de testes no BD
			$emailTeste = $this->dao->getEmailTeste();
			
			//envia o email	
			$envio_email = $servicoEnvioEmail->enviarEmail(	$email, //$email_destinatario = null
															"Comunicado ".$servidor_email->nome_remetente, //$assunto_email = null,
															nl2br($mensagem), //$corpo_email = null,
															$arquivo_anexo = null,
															$email_copia = null,
															$servidor_email->copia_oculta,//$email_copia_oculta = null,
															$servidor_email->servidor,//$servidor_email = null,
															$emailTeste->pcsidescricao//$email_desenvolvedor = null 
														    );
			
			if(!empty($envio_email['erro'])){
				throw new exception($envio_email['msg']);
			}
		
			return true;
		
		}catch(Exception $e){
			
			return false;
		}
		
	}
	
	/**
	 * Valida número de telefone
	 * @param unknown_type $telefone
	 * @return boolean
	 */
	private function validaTelefone($telefone){
		// Verifica se o telefone contém apenas números, e se o tamanho da string é 10 ou 11
		if (!ctype_digit($telefone) || strlen($telefone) < 10 || strlen($telefone) > 11){
			return false;
		}
		return true;
	}
	
	/**
	 * Envia um SMS
	 * @param unknown_type $telefone
	 * @param unknown_type $mensagem
	 * @return unknown
	 */
	private function enviaSMS($telefone, $mensagem){

		try {
			
			// Trata o conteúdo da mensagem para o webservice aceitar a mensagem
			$mensagem = $this->removeAcentos(utf8_decode($mensagem));
			$mensagem = nl2br($mensagem);
			$mensagem = strip_tags($mensagem);
			
			//Limpa o cache
			ini_set("soap.wsdl_cache_enabled", "0");
			
			//se for servidor de testes recupera o celular de teste do BB
			if($_SESSION['servidor_teste'] == 1){
				
				//pesquisa celular de testes no BD
				$cel_teste = $this->dao->getCelularTesteSms();
				$telefone  = trim($cel_teste->pcsidescricao);
					
				if($telefone == ''){
					throw new exception("Informe o celular de teste para enviar o SMS.");
				}
			}
			
			//se for servidor local da máquina, instancia o cliente com proxy
			if(strstr($_SERVER['HTTP_HOST'], $_SERVER['SERVER_ADDR']) ){
				
				$client = new SoapClient('https://webservices.twwwireless.com.br/reluzcap/wsreluzcap.asmx?WSDL',
						array(  'trace'         => 1,
								'exceptions'    => 1,
								'soap_version'  => SOAP_1_1,
								'proxy_host' => "10.2.57.200",
								'proxy_port' => 3128 ));
			
			//produção e outros ambientes
			}else{
				
				$client = new SoapClient('https://webservices.twwwireless.com.br/reluzcap/wsreluzcap.asmx?WSDL',
						array(  'trace'         => 1,
								'exceptions'    => 1,
								'soap_version'  => SOAP_1_1));
				
			}
			
			$save_result = $client->EnviaSMS(array('NumUsu'=>'sascar2','Senha'=>'car666','SeuNum'=>'511023','Celular'=>'55'.$telefone,'Mensagem'=>$mensagem));
			$xmlres = $save_result->EnviaSMSResult;
			
			return $xmlres;
			
		} catch (SoapFault $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Grava informações no histórico do termo ou na os
	 * Atualiza informação das ligações
	 * @param unknown_type $tipoHistorico
	 * @param unknown_type $ligacaoID
	 * @param unknown_type $ligacaoTipo
	 * @param unknown_type $clienteContato
	 * @param unknown_type $contratoNumero
	 * @param unknown_type $osID
	 */
	//private function gravaHistorico($tipoHistorico, $ligacaoID, $ligacaoTipo, $clienteContato, $contratoNumero, $osID){
	private function gravaHistorico($tipoHistorico, $ligacaoID, $ligacaoTipo, $contratoNumero, $osID, $rsInsucesso){
		global $conn;

		$mensagemHistorico = "Último Contato: ".$rsInsucesso['ultimo_contato']."\nQuantidade de Insucessos: ".$rsInsucesso['qtd_insucessos']."\nData de Envio: ".date('d/m/Y')."\nTipo de Envio: ".$tipoHistorico;

		if ($tipoHistorico == "E-MAIL"){
			$updateCampos = " ligdtipo_envioemail = '".$tipoHistorico."',  ligddt_envioemail = NOW() ";
		}
		elseif ($tipoHistorico == "SMS"){
			$updateCampos = " ligdtipo_enviosms = '".$tipoHistorico."',  ligddt_enviosms = NOW() ";
		}
		
		$this->dao->insereHistorico($ligacaoTipo,$contratoNumero,$this->cd_usuario,$mensagemHistorico,$osID);

		$this->dao->atualizaLigacoesDiscador($updateCampos, $ligacaoID);
	}	
	
}
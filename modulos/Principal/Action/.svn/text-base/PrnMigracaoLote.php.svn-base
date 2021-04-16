<?php

/*
 * require para persistência de dados - classe DAO 
 */
require_once(_MODULEDIR_ . 'Principal/DAO/PrnMigracaoLoteDAO.php');

require_once(_SITEDIR_ . 'lib/phpMailer/class.phpmailer.php');
require_once('includes/php/contrato_funcoes.php');

/**
 * @PrnMigracaoLote.php
 * 
 * Classe para Migração de Contratos em Lote.
 * 
 * Permite Inclusão e Planilha e Migração em Lote para Ex, gerando email com processamento.
 * 
 * 
 * @author	Alex Sandro Médice
 * @email alex.medice@meta.com.br
 * @since 24/10/2012
 * @package Principal
 * 
 */
class PrnMigracaoLote {
	
	/**
	 * Separador de colunas CSV
	 * 
	 * @var string
	 */
	const SEPARADOR_CSV = ';';

    /**
     * Atributo para acesso a persistência de dados
     * @property DAO
     */
    private $dao;
    private $conn;
    private $conusuoid;
    
	/**
	 * Separador de emails para o campo destinatários
	 * 
	 * @var array
	 */
    private $separadores_email = array(',',';');

    /*
     * Construtor
     *
     */
    public function __construct() {

        global $conn;
        
        $this->conusuoid = $_SESSION['usuario']['oid'];

        $this->conn = $conn;

        $this->dao = new PrnMigracaoLoteDAO($conn, $this->conusuoid);
    }
    
    public function index($request) {
    	$retorno 				= $request;
    	$retorno['acao'] 		= 'pesquisar';
    	
    	return $retorno;
    }
    
    public function pesquisar($request) {
    	$retorno = $request;
    	$retorno['msg'] = '';
    	$retorno['contratos'] = array();
    	$chassis=array();

    	$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
    	$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
    	$ativar = ($request['is_ativar']=='s');
    	if(isset($_REQUEST['is_ativar'])){
    		$ativar = ($_REQUEST['is_ativar']=='s');
    	}
    	try {
	    	if (isset($_FILES['arquivo'])) {
	    		
		    	$btn_processar = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : array();
		    	if(!$_FILES['arquivo']['error']){
		    		$csv = trim(file_get_contents($_FILES['arquivo']['tmp_name']));
		    	}
		    	
		    	if ($_FILES['arquivo']['error']) {
		    		$retorno['msg'] = 'Erro ao processar o arquivo, entre em contato com o administrador.';
		    	}
		    	else if(empty($csv)) {
		    		$retorno['msg'] = 'Não foram encontrados dados com base nos registros informados no arquivo.';
		    	}
		    	else if ($this->isArquivoCsvInvalido($btn_processar, $ativar)) {
		    		$retorno['msg'] = 'Atenção formato inválido, selecione apenas arquivos no formato TXT.';
		    	}
		    	else {			    	
			    	$chassis = $this->listaDeChassi($btn_processar['tmp_name']);			    	
			    	if (!count($chassis)) {
			    		$retorno['msg'] = 'Não foram encontrados dados com base nos registros informados no arquivo.';
			    	}
		    	}
	    	}
	    	else {
	    		$chassis = $this->getSessionChassis();
	    	}

	    	if (count($chassis)) {
		    	$retorno['contratos'] = $this->buscarContratos($chassis, $order, $sort, $ativar);	
		    	if (count($retorno['contratos']) > 0) {
		    		$retorno['acao'] = 'migrar';
		    		$retorno['msg'] = 'Arquivo processado com sucesso.';
		    	}
		    	elseif ($retorno['msg'] == '') {
		    		$retorno['msg'] = 'Não foram encontrados dados com base nos registros informados no arquivo.';
		    	}
	    	}
	    	
    	} catch (Exception $e) {
    		$retorno['msg'] = 'Erro ao processar o arquivo, entre em contato com o administrador.';
    	}
    	    	
    	return $retorno;
    }

    public function migrar($request) {
    	$retorno = $request;
    	$contratosMigrados = array();
    	$migrar_contratos = isset($_POST['migrar_contratos']) ? $_POST['migrar_contratos'] : array();
    	$retorno['is_ativar'] =  isset($_POST['is_ativar']) ? $_POST['is_ativar'] : '';
    	$retorno['is_gera_os'] = isset($_POST['is_gera_os']) ? $_POST['is_gera_os'] : '';
    	$retorno['is_email_processamento'] = isset($_POST['is_email_processamento']) ? $_POST['is_email_processamento'] : '';
    	$retorno['destinatarios'] = isset($_POST['destinatarios']) ? $_POST['destinatarios'] : '';
    	$retorno['msg'] = '';
    	
    	if ($retorno['is_ativar'] == '') {
    		$retorno['msg'] = 'Informe a Ação';
    	}
    	if ($retorno['is_gera_os'] == '' && $retorno['is_ativar']!='s') {
    		$retorno['msg'] = 'Informe o Gera O.S';
    	}
    	if ($retorno['is_email_processamento'] == 1 and $retorno['destinatarios'] == '') {
    		$retorno['msg'] = 'Informe o Destinatário';
    	}
    	if (!count($retorno['migrar_contratos'])) {
    		$retorno['msg'] = 'Informe o Contrato';
    	}
    	
    	if($retorno['is_ativar']=='s'){
    		$retorno['is_gera_os']='n';
    	}    	
    	
    	if ($retorno['msg'] == '') {
	    	try {
	    		pg_query($this->conn, "BEGIN;");
	    		$usuario_cod =  isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : 0;
	    		    		
	    		// inicia a migração somente dos contratos selecionados via checkbox
		    	foreach ($retorno['migrar_contratos'] as $i => $connumero) {
	    			$contrato = $this->dao->contrato($connumero);
		    		
	    			//caso vá reativar, é preciso cancelar os existentes, até mesmo os de retirada.
		    		if($retorno['is_ativar']=='s'){
		    			$this->dao->cancelarOS($connumero, $usuario_cod, 'Cancelado devido a migração de Ex-Bradesco para Bradesco, status da OS cancelada.');
		    			//migra de um tipo de contrato para seu correspondente.
		    			$connumero_novo = $this->ativarContrato($contrato);
		    		}
		    		else {
			    		//migra de um tipo de contrato para seu correspondente.
			    		$connumero_novo = $this->migrarContrato($contrato);
		    		}
		    		
		    		//gera os, caso seja, ativação, não deve gerar os. neste caso, $retorno['is_gera_os'] foi definido para 'n'. 
		    		if ($retorno['is_gera_os'] == 's') {
		    			$this->dao->gerarOS($connumero_novo, $contrato->coneqcoid);
		    		}
		    		
		    		$contratosMigrados[] = $connumero_novo;
		    	}
		    	
		    	if (!count($contratosMigrados)) {
		    		$retorno['msg'] = 'Nenhum contrato migrado.';
		    	}

		    	pg_query($this->conn, "COMMIT;");
		    	
		    	if ($retorno['is_email_processamento'] == 1) {
	    			$destinatarios = $this->getEmailsSeparados($retorno['destinatarios']);	    	
	    			$this->enviarEmailProcessamento($contratosMigrados, $destinatarios, ($retorno['is_ativar']=='s'));
	    		}
	    		
	
		    	$retorno = $this->limparDadosMigrados();
		    	
	    		$retorno['msg'] = 'Migração realizada com sucesso.';
	    		
	    	} catch (Exception $e) {
	        	pg_query($this->conn, "ROLLBACK;");
	        
	    		$retorno['msg'] = $e->getMessage();
	    	}
    	}
    	
    	$retorno['contratos'] = array();
    	try {
	    	// chama os contratos selecionados pelos números de chassi novamente
    		$chassis = $this->getSessionChassis();
	    	if (count($chassis)) {
    			$retorno['contratos'] = $this->buscarContratos($chassis,'','', ($retorno['is_ativar']=='s') );
	    	}
    	} catch (Exception $e) {
    		$retorno['msg'] = $e->getMessage();
    	}
    	
    	return $retorno;
    }
    
    /**
     * Migra contrato
     * 
     * @param array $contrato Dados do contrato a ser migrado
     * @return int $connumero_novo Número do novo contrato gerado
     */
    private function migrarContrato($contrato) {
    	
    	$isExecutarTransacao = false;
    	
    	$connumero_novo = migrar_contrato($this->conusuoid, $contrato->tpcoidcorrespondente_ex, $contrato->contrato, 0, $contrato->tipo_contrato, 'f', '', $this->conn, $isExecutarTransacao);
    	
    	if (!$connumero_novo) {
    		throw new Exception('A Migração foi cancelada porque não foi possível migrar o contrato: '.$contrato->contrato);
    	}
    	
    	return $connumero_novo;
    }
    
    /**
     * Ativar contrato
     * @param int $contrato
     * @return int
     */
    private function ativarContrato($contrato){
    	$this->dao->ativarContrato($contrato);
    	return $contrato->contrato;
    }
    
    /**
     * Pega a lista de emails para enviar os dados de processamento
     * 
     * @param string $destinatarios
     * @return array:
     */
    private function getEmailsSeparados($destinatarios) {
    	
    	$emails = array();
    	$emailsTmp = explode(',', $destinatarios);
    	foreach ($emailsTmp as $email) {	
    		$emails= array_merge($emails, explode(';', $email));
    	}
    	
    	$emails = array_unique($emails);
    	
    	foreach ($emails as $i=>&$email) {
    		$email = trim($email);
    		
    		if (empty($email)) {
    			unset($emails[$i]);
    			continue;
    		}
    		
    		$this->validarEmail($email);
    	}
    	
    	return $emails;
    }
    
    /**
     * Validação de email
     * 
     * @param string $email
     * @return boolean
     */
    private function validarEmail($email) {
    	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    		throw new Exception('O Destinatário '.$email.' não é válido.');
    	}
    	
    	return true;
    }
        
    /**
     * Envia email para os indicados no campo Destinatário(s), fazendo uma busca das informações, de acordo com os contratos novos gerados
     * 
     * @param array $contratosMigrados Números de contratos migrados
     * @param array $destinatarios
     * @return boolean
     */
    private function enviarEmailProcessamento($contratosMigrados, $destinatarios, $ativando=false) {
    	
    	$contratos = $this->dao->contratosMigrados($contratosMigrados);
    	
    	$htmlEmail = $this->htmlEmailProcessamento($contratos, $ativando);
    	
    	$mail = new PHPMailer();
    	$mail->isSmtp();
    	$mail->IsHTML(true);
    	
    	$mail->From = "sascar@sascar.com.br";
    	$mail->FromName = "Sascar";
    	$mail->Subject = "SASCAR - Migração em Lote.";
    	$mail->MsgHTML($htmlEmail);
    	
    	foreach ($destinatarios as $destinatario) {
    		$mail->AddAddress($destinatario);
    	}
    	
    	if(!$mail->Send()){
    		throw new Exception('Não foi possível enviar email para os indicados no campo Destinatário(s): '.$mail->ErrorInfo.'.');
    	}
    	
    	return true;	
    }
    
    /**
     * Gera o HTML para envio de email para os indicados no campo Destinatário(s)
     * 
     * @param array $contratos
     * @return string
     */
    private function htmlEmailProcessamento($contratos, $ativando=false) {
    	ob_start(0);
    	?>
    	
			<style type="text/css">
				h2 {
					font-size: 18px;
					margin-bottom: 10px;
					padding: 0;
				}
				
				img {
					float: left;
				}
				
				.container {
					width: 680px;
					border-spacing: 0;
					border-left: 1px solid #000000;
					border-right: 1px solid #000000;
					border-bottom: 1px solid #000000;
				}
				
				.container td {
					padding: 2px;
					padding-left: 5px;
					font-size: 14px;
					border-top: 1px solid #000000;
				}
				
				.content {
					padding-left: 5px;
					padding-right: 5px;
				}
				
				.content td {
					border: none;
					margin: 0;
				}
				
				#contentList {
					padding-bottom: 25px;
				}
				
				.list {
					border-spacing: 0;
					border-right: 1px solid #000000;
					border-bottom: 1px solid #000000;
				}
				.list td {
					padding: 2px;
					border-top: 1px solid #000000;
					border-left: 1px solid #000000;
				}
				.list .header {
					background: #cccccc;
					font-weight: bold;
				}
				.list .tdc {
					background: #eeeeee;
				}
				.list .tde {
					background: #ffffff;
				}
			</style>
			<table width="680" class="container" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>EMAIL</td>
			 	</tr>
				<tr>
					<td id="contentList">
						<table width="100%" class="content" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<h2>Migração em Lote</h2>
								</td>
						 	</tr>
							<tr>
								<td>
									Data Processamento: <?php echo date('d/m/Y'); ?>
								</td>
						 	</tr>
							<tr>
								<td>
									Usuário: <?php echo (isset($_SESSION['usuario']['nome'])) ? $_SESSION['usuario']['nome'] : $this->conusuoid; ?><br /><br />
								</td>
						 	</tr>
							<tr>
								<td>
									<table width="100%" class="list" border="0" cellspacing="0" cellpadding="0">
										<tr class="header">
										<?php if(!$ativando): ?>
											<td align="center" width="20%">Contrato Original</td>
										<?php endif ?>
											<td align="center" width="20%">Data <?php echo ($ativando ? "Reativação" : "Cancelamento"); ?></td>
											<td align="center" width="15%">Contrato <?php echo ( !$ativando ? "Novo":"");?></td>
											<td align="center" width="15%">Placa</td>
											<td align="center" width="15%">Tipo Contrato</td>
											<td align="center" width="15%">O.S</td>
										</tr>
										<?php foreach ($contratos as $i=>$contrato): ?>
										<tr class="<?php echo ((($i % 2) == 0)) ? 'tdc' : 'tde'; ?>">
										<?php if(!$ativando): ?>
											<td align="center"><?php echo $contrato->contrato_original; ?>&nbsp;</td>
											<td align="center"><?php echo (($contrato->data_cancelamento != '')) ? date('d/m/Y', strtotime($contrato->data_cancelamento)) : ''; ?>&nbsp;</td>
										<?php else: ?>
											<td align="center"><?php echo date('d/m/Y'); ?>&nbsp;</td>
										<?php endif; ?>
											<td align="center"><?php echo $contrato->novo_contrato; ?>&nbsp;</td>
											<td align="center"><?php echo $contrato->placa; ?>&nbsp;</td>
											<td align="center"><?php echo $contrato->tipo_contrato; ?>&nbsp;</td>
											<td align="center"><?php echo $contrato->os; ?>&nbsp;</td>
										</tr>
										<?php endforeach; ?>
									</table>
								</td>
						 	</tr>
						 </table>
					</td>
			 	</tr>
				<tr>
					<td>&nbsp;</td>
			 	</tr>
			 </table>
			 
		<?php
		$htmlEmail = ob_get_contents();
		ob_end_clean();
		
		return $htmlEmail;
    }

    /**
     * Valida o arquivo CSV com os chassis dos veículos a serem migrados
     * 
     * @param $_FILES $arquivo
     * @return string
     */
    private function isArquivoCsvInvalido($arquivo, $ativar=false) {
		$formatos = array('.txt');
		if(!$ativar){
			$formatos[]='.csv';
		}    	

    	if (!isset($arquivo['error']) or $arquivo['error'] != 0) {
    		return true;
    	}
    	
    	if (!in_array(substr($arquivo['name'], -4, 4), $formatos)) {
    		return true;
    	}
    	
    	$csv = file_get_contents($arquivo['tmp_name']);
    	if (!$csv) {
    		return true;
    	}
    	
    	return false;
    }
    
    /**
     * Cria lista de chassi baseado no arquivo CSV
     * 
     * @param string $pathCsv Path absoluto do arquivo CSV
     * @return array
     */
    private function listaDeChassi($pathCsv) {
    	$chassis = array();
    	
    	$handle = fopen($pathCsv, "r");
    	while (($linha = fgetcsv($handle, null, self::SEPARADOR_CSV)) !== FALSE) {
			$chassi = trim(current($linha));
			
    		if (!empty($chassi)) {
    			$chassis[] = $chassi;
    		}
    	}
    	
    	$this->setSessionChassis($chassis);
    	
    	return $chassis;
    }
    
    private function buscarContratos($chassis, $order='', $sort='', $ativando=false) {
    	return $this->dao->contratosPorChassi($chassis, $order, $sort, $ativando);
    }
    
    private function setSessionChassis($chassis) {
    	$_SESSION['chassis'] = json_encode($chassis);
    }
    
    private function getSessionChassis() {
    	if (isset($_SESSION['chassis'])) {
    		return json_decode($_SESSION['chassis']);
    	}
    	
    	return array();
    }
    
    /**
     * Limpa os dados armazenados em sessão para uma nova migração
     * 
     * @return array:
     */
    private function limparDadosMigrados() {
    	$this->setSessionChassis(array());

    	$retorno['is_ativar'] = '';
    	$retorno['is_gera_os'] = '';
    	$retorno['is_email_processamento'] = '';
    	$retorno['migrar_contratos'] = array();
    	
    	return array();
    }
}
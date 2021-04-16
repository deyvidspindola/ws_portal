<?php
/**
 * Classe de persistência de dados 
 */
require (_MODULEDIR_.'Cadastro/DAO/CadImportacaoCorreiosDAO.php');

/**
 * CadImportacaoCorreios.php
 * 
 * Classe Action para a importação dos Correios
 * 
 * @author Marcelo Fuchs
 * @package Principal
 * @since 18/04/2013 14:00
 * 
 */
class CadImportacaoCorreios {

	const EM_PROCESSAMENTO      = 'P';
	const IMPORTADO_COM_SUCESSO = 'S';
	const FALHA_IMPORTACAO      = 'F';
	
	private $dao;   
	private $id_usuario;
    
	private $msgSucesso = ""; 
    private $msgAlerta = ""; 
    private $msgErro = "";  
	
    private $pathCorreios = "/var/www/arquivos_correios/";
    private $files = array( "DNE_GU_BAIRROS.TXT",			"DNE_GU_LOCALIDADES.TXT",
							"DNE_GU_AC_LOGRADOUROS.TXT",	"DNE_GU_AL_LOGRADOUROS.TXT",
							"DNE_GU_AM_LOGRADOUROS.TXT",	"DNE_GU_AP_LOGRADOUROS.TXT",
							"DNE_GU_BA_LOGRADOUROS.TXT",	"DNE_GU_CE_LOGRADOUROS.TXT",
							"DNE_GU_DF_LOGRADOUROS.TXT",	"DNE_GU_ES_LOGRADOUROS.TXT",
							"DNE_GU_GO_LOGRADOUROS.TXT",	"DNE_GU_MA_LOGRADOUROS.TXT",
							"DNE_GU_MG_LOGRADOUROS.TXT",	"DNE_GU_MS_LOGRADOUROS.TXT",
							"DNE_GU_MT_LOGRADOUROS.TXT",	"DNE_GU_PA_LOGRADOUROS.TXT",
							"DNE_GU_PB_LOGRADOUROS.TXT",	"DNE_GU_PE_LOGRADOUROS.TXT",
							"DNE_GU_PI_LOGRADOUROS.TXT",	"DNE_GU_PR_LOGRADOUROS.TXT",
							"DNE_GU_RJ_LOGRADOUROS.TXT",	"DNE_GU_RN_LOGRADOUROS.TXT",
							"DNE_GU_RO_LOGRADOUROS.TXT",	"DNE_GU_RR_LOGRADOUROS.TXT",
							"DNE_GU_RS_LOGRADOUROS.TXT",	"DNE_GU_SC_LOGRADOUROS.TXT",
							"DNE_GU_SE_LOGRADOUROS.TXT",	"DNE_GU_SP_LOGRADOUROS.TXT",
							"DNE_GU_TO_LOGRADOUROS.TXT");

	/** 
	 * Construtor
	 */
	public function __construct() {
		global $conn;		
		$this->dao = new CadImportacaoCorreiosDAO($conn);		
		$this->id_usuario = $_SESSION['usuario']['oid'];
		return true;
	}
	
	/**
	 * Importa arquivo ZIP e descompacta em pasta especifica
	 */
	protected function _importarArquivo()
	{
		
		/**
		 * VALIDANDO INFORMACOES DO ARQUIVO
		 */
		if(!is_uploaded_file($_FILES['arquivo_zip']['tmp_name'])){
			throw new Exception('Falha no upload do arquivo.');			
		}
		
		$filename = strtolower($_FILES['arquivo_zip']['name']);
		if (!preg_match('/\.zip$/', strtolower($filename))) 
		{
			throw new Exception('O arquivo deve estar no formato ZIP.');			
		}
	
		$filezip =  $this->pathCorreios.$filename;
		if(file_exists($filezip)) unlink($filezip);
		
		if(!move_uploaded_file($_FILES['arquivo_zip']['tmp_name'], $filezip))
		{
			throw new Exception('Falha no upload do arquivo.');	
		}
		
		$rs = zip_open($filezip);
		if(!is_resource($rs)) {
			throw new Exception('Falha ao ler o arquivo "'.$filename.'".');
		}
		
		$enviados = array();
		$menor1K  = array();
		$conta=0;
		while($zip_file = zip_read($rs)){
			$zip_name = strtoupper(zip_entry_name($zip_file));
			$zip_filesize = zip_entry_filesize($zip_file);
			if(in_array($zip_name, $this->files)){
				$enviados[]=$zip_name;		
					
				if($zip_filesize < 1024 )
					$menor1K[]=$zip_name;
			}
		}	
		zip_close($rs);
		
		/*
		$naoEnviados = array_diff($this->files, $enviados);
		if(count($naoEnviados) > 0){
			throw new Exception('Os seguintes arquivos não foram enviados: '.implode(", ", $naoEnviados).'. <br />Adicione os arquivos faltantes no arquivo ZIP e envie novamente.');
		}
		*/
		if(count($menor1K) > 0){
			throw new Exception('Os seguintes arquivos estão vazios: '.implode(", ", $menor1K).'. <br />Adicione os arquivos faltantes no arquivo ZIP e envie novamente.');
		}
		
		/**
		 * INFORMAÇÕES VÁLIDAS - DESCOMPACTAR E GRAVAR LOG
		 */		
		$rs = zip_open($filezip);
		if(!is_resource($rs)) {
			throw new Exception('Falha ao ler o arquivo "'.$filename.'".');
		}	

		try {
			while($zip_file = zip_read($rs)){
				$zip_name = zip_entry_name($zip_file);
				$zip_filesize = zip_entry_filesize($zip_file);
				if(in_array($zip_name, $this->files)){
					$destinotxt = $this->pathCorreios.$zip_name;
					$rsdestino = fopen($destinotxt, 'w+');
					while ($content = zip_entry_read($zip_file)){
						fwrite($rsdestino, $content);
					}
					fclose($rsdestino);
				}
			}
			zip_close($rs);
		} catch (Exception $e) {
			throw new Exception('Falha ao descompactar o arquivo "'.$filename.'".');
		}
		
		if(file_exists($filezip)) unlink($filezip);
		$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::EM_PROCESSAMENTO );				
		return true;
	}
	
	/**
	 * Método principal
	 */
	public function index() {
		require_once _MODULEDIR_ . 'Cadastro/View/cad_importacao_correios/importar.php';
		return true;
	}
	
	/**
	 * Método de imprtação. Depende da selecao do arquivo no metodo index()
	 */
	public function importar(){	
		ob_start();
		try {
			$this->_importarArquivo();
			$this->msgSucesso="Importação realizada com sucesso.";	
		} catch (Exception $e) {
			$this->msgErro = $e->getMessage();
		}
		ob_end_clean();		
		return $this->index();
	}	
	
	/**
	 * USADO PELO CRON - Método responsável pela importação
	 */
	public function importa() {
		set_time_limit(0);
		
		$retorno = array(
			'status'   => true,
			'dados'    => array(),
			'mensagem' => null
		);
		
		echo "<b>".date('H:i:s')."</b><br />";
		
		try {
			$dir_caminho = substr(_SITEDIR_, 0, stripos(_SITEDIR_, 'sistemaweb'));
			
			if(stristr($dir_caminho, 'html')) {
				$dir_caminho = substr($dir_caminho, 0, stripos($dir_caminho, 'html'));
			}
			
			$dir_caminho.= "docs_temporario/correios/";
			$dir_objeto  = dir($dir_caminho);
			
			while($arq_nome = $dir_objeto->read()) {
				if(filetype($dir_caminho.$arq_nome) == 'file') {
					$arq_objeto = @fopen($dir_caminho.$arq_nome, 'r');
					
					if($arq_objeto) {
						$arq_status = true;
						
						$valores = array();
						
						while($arq_status) {
							$arq_linha = fgets($arq_objeto, 6400);
							
							if($arq_linha !== false) {
								if(!in_array(substr($arq_linha, 0, 1), array('C', '#'))) {
									$registro = array(
										'tipo'       => trim(substr($arq_linha, 0, 1)),
										'estado'     => trim(substr($arq_linha, 1, 2)),
										'localidade' => array(
											'chave' => intval(substr($arq_linha, 9, 8)),
											'nome'  => trim(substr($arq_linha, 17, 72))
										),
										'bairro'     => array(
											'inicial' => array(
												'chave' => intval(substr($arq_linha, 94, 8)),
												'nome'  => trim(substr($arq_linha, 102, 72))
											),
											'final'   => array(
												'chave' => intval(substr($arq_linha, 179, 8)),
												'nome'  => trim(substr($arq_linha, 187, 72))
											)
										),
										'logradouro' => array(
											'tipo'        => trim(substr($arq_linha, 259, 26)),
											'preposicao'  => trim(substr($arq_linha, 285, 3)),
											'titulo'      => trim(substr($arq_linha, 288, 72)),
											'nome'        => trim(substr($arq_linha, 374, 72)),
											'abreviatura' => trim(substr($arq_linha, 446, 36))
										),
										'cep'        => trim(substr($arq_linha, 518, 8)),
										'status'     => trim(substr($arq_linha, 527, 1))
									);
									
									$registro["localidade"]        = $this->dao->getLocalidade($registro["localidade"]["chave"]);
									$registro["bairro"]["inicial"] = $this->dao->getBairro($registro["bairro"]["inicial"]["chave"]);
									
									if($registro["localidade"] && $registro["bairro"]["inicial"]) {
										$registro["bairro"]["final"] = $this->dao->getBairro($registro["bairro"]["final"]["chave"]);
										
										if(!$registro["bairro"]["final"]) {
											$registro["bairro"]["final"] = array(
												'chave' => null,
												'nome'  => null
											);
										}
										
										$valores[] = "
											(
											)
										";
										
										if(count($valores) == 500) {
											$valores  = array();
											
											echo date('H:i:s')."<br />";
										}
										
										/* echo "<pre>";
										echo print_r($registro, 1);
										echo "</pre>"; */
									}
								}
							} else {
								$arq_status = false;
							}
						}
						
						fclose($arq_objeto);
					}
				}
			}
		} catch(Exception $e) {
			$retorno["status"]   = false;
			$retorno["dados"]    = array();
			$retorno["mensagem"] = $e->getMessage();
		}
		
		echo "<b>".date('H:i:s')."</b><br />";
		
		echo json_encode($retorno);
		
		return true;
	}
	
	/**
	 * Gerar tabela de arquivos HTML
	 */
	function gerarTabelaArquivos() {
		$html = "";
		$contador = 0;
		$par = 0;
		foreach ($this->files as $arquivo) {
			if ($contador == 0) {
				if ($par == 0) {
					$html .= "<tr class='par'>";
					$par = 1;
				} else {
					$html .= "<tr>";
					$par = 0;
				}
			}
			
			if (file_exists($this->pathCorreios . $arquivo)) {
				$html .= "<td><img src='images/apr_bom.gif' alt='Existente' /> ".$arquivo."</td>";
			} else {
				$html .= "<td><img src='images/apr_ruim.gif' alt='Inexistente' /> ".$arquivo."</td>";
			}
			
			if ($contador == 3) {
				$html .= "</tr>";
				$contador = 0;
			} else {
				$contador++;
			}
		}
		return $html;
	}
	
	
	
	/**
	 * STI - 85377 - Automatizar processo de atualização de base do correio
	 *
	 */
	public function getArquivoDownload(){
	
		if(empty($this->id_usuario)){
			//usuário AUTOMATICO para processos onde não existe autenticação
			$this->id_usuario = 2750;
		}
		
		//RECUPERA O NOME DO USUÁRIO
		$ret_param = $this->dao->getParametrosDownload(84);
		//$username = '11323728';
		$username = $ret_param['valor'];
		
		if(empty($username)){
			$msg = 'NOME do usuário para acesso ao site não encontrado.';;
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			return array('success' => false, 'message' => $msg);
		}
		
		//RECUPERA A PASSWORD
		$ret_param = $this->dao->getParametrosDownload(86);
		//$password = '03112879';
		$password = $ret_param['valor'];
		
		
		if(empty($password)){
			$msg = 'SENHA do usuário para acesso ao site não encontrado.';;
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			return array('success' => false, 'message' => $msg);
		}
		
		
		//Inicia o cURL
		$ch = curl_init();
		 
		//RECUPERA A URL DE LOGIN
		$ret_param = $this->dao->getParametrosDownload(88);
		//http://www.corporativo.correios.com.br/edne/login.cfm
		$url_login = $ret_param['valor'];
		
		if(empty($url_login)){
			$msg = 'URL DE LOGIN para acesso ao site não encontrado.';;
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			return array('success' => false, 'message' => $msg);
		}
		
		
		// Define a URL original (do formulário de login)
		curl_setopt($ch, CURLOPT_URL, $url_login);
		
		curl_setopt($ch, CURLOPT_HEADER, 1);
		 
		// Habilita o protocolo POST
		curl_setopt ($ch, CURLOPT_POST, 1);
		
		
		//RECUPERA CAMPOS DE USUARIO E SENHA PARA POST NO SITE
		$ret_param = $this->dao->getParametrosDownload(90);
		$campos_login_post = $ret_param['valor'];
		
		if(empty($campos_login_post)){
			$msg = 'CAMPOS DE LOGIN E SENHA de post para acesso ao site não encontrado.';;
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			return array('success' => false, 'message' => $msg);
		}

		
		// Define os parâmetros que serão enviados (usuário e senha por exemplo)
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $campos_login_post);
		 
		// Imita o comportamento patrão dos navegadores: manipular cookies
		curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		 
		// Define o tipo de transferência (Padrão: 1)
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		 
		curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7');
		 
		// Executa a requisição
		$store = curl_exec ($ch); // contendo o HTML da página resultado (depois do submit do login)
		 
		// Pega o código de resposta HTTP
		$result = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		 
		if($result == 302){
			
			//RECUPERA URL APÓS LOGIN
			$ret_param = $this->dao->getParametrosDownload(92);
			//http://www.corporativo.correios.com.br/edne/default.cfm?s=true
			$url_apos_login = $ret_param['valor'];
			
			if(empty($url_apos_login)){
				$msg = 'URL APOS LOGIN para acesso ao site não encontrado.';;
				$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
				return array('success' => false, 'message' => $msg);
			}
			
			// Define uma nova URL para ser chamada (após o login)
			curl_setopt($ch, CURLOPT_URL, $url_apos_login);
					 
			// Executa a segunda requisição
			$content = curl_exec ($ch); // contendo o HTML da página chamada na segunda requisição.
			// Pega o código de resposta HTTP
			$result = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			 
			if($result == 200){
				
				//RECUPERA URL DO ARQUIVO GERAL .ZIP
				$ret_param = $this->dao->getParametrosDownload(94);
				//http://www.corporativo.correios.com.br/edne/download/
				$url_arquivo_geral = $ret_param['valor'];
					
				if(empty($url_arquivo_geral)){
					$msg = 'URL DO ARQUIVO GERAL não encontrado.';;
					$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
					return array('success' => false, 'message' => $msg);
				}
				
				
				//RECUPERA O NOME DO ARQUIVO GERAL QUE SERÁ BAIXADO DOS SITES DOS CORREIOS
				$ret_param = $this->dao->getParametrosDownload(82);
				
				$filename = $ret_param['valor'];
				//$filename = 'dne_gu.zip';
				
				if(empty($filename)){
					$msg = 'NOME DO ARQUIVO GERAL não encontrado.';;
					$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
					return array('success' => false, 'message' => $msg);
				}
				
				
				$url = $url_arquivo_geral.$filename;
				//$url      = 'http://www.corporativo.correios.com.br/edne/download/DNE_GU.zip';
			
				$path     = '/var/www/arquivos_correios/';
				 
				if(file_exists($path.$filename)){
					unlink($path.$filename);
				}
				 
				// Limpando pasta
				if(is_dir($path)){
					if($handle = opendir($path)){
						while(($file = readdir($handle)) !== false){
							if($file != '.' && $file != '..'){
								unlink($path.$file);
							}
						}
					}
				}
				 
				// Realizando o download do arquivo
				$fp = fopen($path.$filename, 'w');
				 
				$curlZip = curl_init($url);
				curl_setopt($curlZip, CURLOPT_FILE, $fp);
				 
				$data = curl_exec($curlZip);
				 
				curl_close($curlZip);
				 
				if(fclose($fp)){
					
					//Faz a descompactação do arquivo baixado 
					$this->descompactarArquivo($filename);
				}
				 
			}else{
				// Encerra o cURL
				curl_close ($ch);
				
				$msg = 'O site dos correios não responde. => '.$result;
				$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
				return array('success' => false, 'message' => $msg);
			}
	
		}else{
			// Encerra o cURL
			curl_close ($ch);
			
			$msg = 'Falha ao tentar comunicação com o site dos correios. => '.$result;
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			return array('success' => false, 'message' => $msg);
		}
		 
		// Encerra o cURL
		curl_close ($ch);
		
		$msg = 'Download arquivo .zip base dos correios realizado com sucesso.';
		$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::IMPORTADO_COM_SUCESSO,$msg);
		return array('success' => false, 'message' => $msg);
	   
	}
	

	/**
	 * STI 85377
	 * 
	 */
	public function descompactarArquivo($nome_arquivo){
		
		
		if(!file_exists($this->pathCorreios.$nome_arquivo)){
			$msg = 'Falha no upload, arquivo não encontrado.';
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			throw new Exception($msg);
		}
		
		$filezip = strtolower($this->pathCorreios.$nome_arquivo);
		
		if (!preg_match('/\.zip$/', strtolower($filezip))){
			$msg = 'O arquivo deve estar no formato ZIP => '.$filezip;
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			throw new Exception($msg);
		}
		
		$rs = zip_open($filezip);
		
		if(!is_resource($rs)) {
			$msg = 'Falha ao ler o arquivo = '. $filezip;
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			throw new Exception($msg);
		}	
			
		
		$enviados = array();
		$menor1K  = array();
		$conta=0;
		while($zip_file = zip_read($rs)){
			$zip_name = strtoupper(zip_entry_name($zip_file));
			$zip_filesize = zip_entry_filesize($zip_file);
				$enviados[]=$zip_name;
					
				if($zip_filesize < 1024 )
					$menor1K[]=$zip_name;
		}
		
		zip_close($rs);
		
		if(count($menor1K) > 0){
			$msg = 'Os seguintes arquivos estão vazios: '.implode(", ", $menor1K).'. <br />Adicione os arquivos faltantes no arquivo ZIP e envie novamente.';
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			throw new Exception($msg);
		}
		
		/**
		 * INFORMAÇÕES VÁLIDAS - DESCOMPACTAR E GRAVAR LOG
		 */
		$rs = zip_open($filezip);
		if(!is_resource($rs)) {
			
			$msg = 'Falha ao ler o arquivo "'.$filezip.'".';
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			throw new Exception($msg);
		}
		
		try {
			while($zip_file = zip_read($rs)){
				$zip_name = zip_entry_name($zip_file);
				$zip_filesize = zip_entry_filesize($zip_file);
					$destinotxt = $this->pathCorreios.$zip_name;
					$rsdestino = fopen($destinotxt, 'w+');
					while ($content = zip_entry_read($zip_file)){
						fwrite($rsdestino, $content);
					}
					fclose($rsdestino);
			}
			zip_close($rs);
		} catch (Exception $e) {
			$msg = 'Falha ao descompactar o arquivo "'.$filezip.'".';
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			throw new Exception($msg);
		}
		
		
		
		//RECUPERA CAMPOS DE USUARIO E SENHA PARA POST NO SITE
		$ret_param = $this->dao->getParametrosDownload(96);
		$prefixo_arquivo = $ret_param['valor'];
		
		if(empty($prefixo_arquivo)){
			$msg = 'PREFIXO DO ARQUIVO para descompactar não encontrado.';;
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			return array('success' => false, 'message' => $msg);
		}
		
		
		$arquivos = dir($this->pathCorreios);
		
		//busca o arquivo que contém o prefixo parametrizado no bd
		while (($arq = $arquivos->read()) != false){
			
			if(strstr($arq, $prefixo_arquivo)){
				$arquivoDescom = $this->pathCorreios.$arq;
			}
		}
		
		$arquivos->close();
		
		//Descompactar segundo arquivo arquivo
		$rs = zip_open($arquivoDescom);
		
		if(!is_resource($rs)) {
			$msg = 'Falha ao ler o arquivo "'.$arquivoDescom.'".';
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			throw new Exception($msg);
		}
		
		try {
			while ( $zip_file = zip_read ( $rs ) ) {
				$zip_name = zip_entry_name ( $zip_file );
				$zip_filesize = zip_entry_filesize ( $zip_file );
				
					$destinotxt = $this->pathCorreios.$zip_name;
					$rsdestino = fopen ( $destinotxt, 'w+' );
					
					while ( $content = zip_entry_read ( $zip_file ) ) {
						fwrite ( $rsdestino, $content );
					}
					
					fclose ( $rsdestino );
			}
			zip_close($rs);
			
		} catch (Exception $e) {
			$msg = 'Falha ao descompactar o arquivo "'.$filename.'".';
			$this->dao->gravarHistorico($this->id_usuario, CadImportacaoCorreios::FALHA_IMPORTACAO,$msg);
			throw new Exception($msg);
		}
		
		
		//APAGA ARQUIVO .zip
		//if(file_exists($arquivoDescom)) unlink($arquivoDescom);
		
		//Mantém só os arquivos que serão baixados
		$arquivos = "";
		$arquivos = dir($this->pathCorreios);
		
		while ( ($arq = $arquivos->read ()) != false ) {
			
			if (!strstr ( $arq, $prefixo_arquivo )) {
				$arquivo_DEL = $this->pathCorreios . $arq;
				
				if (file_exists ( $arquivo_DEL ))
					unlink ( $arquivo_DEL );
			}
		}
		
		$arquivos->close();
		
		return true;
		
	}
	
	
		
}
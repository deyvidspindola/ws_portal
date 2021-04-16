<?php


namespace infra\Helper;

/**
 *
 * @tutorial : Classe responsÃ¡vel por gerar logs, possibilitando tambÃ©m gerar logs com arquivos e diretÃ³rios dinÃ¢micos.
 * inicializado no lib/config.php
 * include_once("lib/gerar_logs/Logger.php");
 *
 * @package sistemaWeb/lib/gerar_logs
 *
 * @example
 * Logger::logInfo(
 * 		"Mensagem do Log",
 *  	__FILE__, 
 *  	__LINE__,
 * 		array(
 * 			'classe' 			=> __CLASS__, 		// Nome Classe
 * 			'metodo' 			=> __FUNCTION__,	// Nome MÃ©todo
 * 			'dirDinamico'		=> "/var/www/html/desenvolvimento/sistemaWeb/processar_cobr_registrada_log/",
 * 			'nomeArqDinamico'	=> 'login_portal', 	//Nome do Arquivo sem a extensÃ£o do arquivo: prefixoArq + nomeArqDinamico + self::PATH 
 * 			'prefixoNomeArq'	=> 'financas', 		//Prefixo do nome do arquivo.
 * 			'usuario'			=> "1017",
 * 		);
 *);
 * @author alexandre.reczcki
 *
 * @version 1.0
 */
class Logger
{
	
	/** Diagnosticos do protocolo para os niveis de uso definidos pela RFC 5424s */
    const DEBUG 	= 100;
    const INFO 		= 200;    
    const NOTICE 	= 250;
    const WARNING 	= 300;
    const ERROR 	= 400;
    const CRITICAL 	= 500;
    const ALERT 	= 550;
    const EMERGENCY = 600;
	
    /** DiretÃ³rio Default do Log */
    const PATH = _DIRETORIO_LOG_;
    
    /** ExtensÃ£o Default do Log */
    const EXTENSAO = ".txt";
    
    /**
     * @tutorial: Diagnosticos do protocolo para os niveis de uso definidos pela RFC 5424    
     * EnumeraÃ§Ã£o dos numeros para listagem e nome atravÃ©s da funÃ§Ã£o getLevelName(Logger::levels)
     *  
     * @var array $levels Logger::levels
     */
    private static $levels = array(
		self::DEBUG     => 'DEBUG',
    	self::INFO      => 'INFO',
    	self::NOTICE    => 'NOTICE',
    	self::WARNING   => 'WARNING',
    	self::ERROR     => 'ERROR',
    	self::CRITICAL  => 'CRITICAL',
    	self::ALERT     => 'ALERT',
    	self::EMERGENCY => 'EMERGENCY',
    );
    
    /**
     * 
     * @tutorial : ValidaÃ§Ãµes e logica para entender as parametrizaÃ§Ãµes do atributo $context, para gerar o log.
     * 
     * @param string $message
     * @param int $level Logger::INFO, Logger::NOTICE, Logger::ERROR
     * @param array $context
     * 
     * @param array $context
     * 	array(
     * 		'classe' 			=> __CLASS__, 		// Nome Classe
     * 		'metodo' 			=> __FUNCTION__,	// Nome MÃ©todo
     * 		'dirDinamico'		=> "/var/www/html/desenvolvimento/sistemaWeb/processar_cobr_registrada_log/",
     * 		'nomeArqDinamico'	=> 'login_portal', 	//Nome do Arquivo sem a extensÃ£o do arquivo: prefixoNomeArq + nomeArqDinamico + self::PATH 
     * 		'prefixoNomeArq'	=> 'financas', 		//Prefixo do nome do arquivo.
     * 		'usuario'			=> "1017",
     * );
     */
    private static function gerarLog($level, $message, $arquivo, $linha, $context = array()) {
    	/** validar diretorio configuravel na constante PATH */
    	if (! file_exists(self::PATH)) {
    		return false;
    	}
    	
    	$context = (object) $context;
    	/** Valida diretÃ³rio dinamico */
    	if(isset($context->dirDinamico)){
    		if (! file_exists($context->dirDinamico)) {
    			return false;
    		}	
    	}
    	
    	$levelString 	= self::getLevelName($level);
    	$navegador 		= self::getBrowser();
    	$data_hora 		= date("Y-m-d H:i:s");
    	$ip 			= $_SERVER['REMOTE_ADDR'];
    
    	/** Valida chaves do parametro $context */
    	$usuario 	= isset($context->usuario) ? $context->usuario : NULL;
    
    	/** Define o diretorio do arquivo de acordo com a passagem do parametro: dirDinamico */
    	$diretorio 	= isset($context->dirDinamico) ? $context->dirDinamico : self::PATH;
    	
    	/** Define o nome do Arquivo de Acordo com a passagem de parametro:  nomeArqDinamico, prefixoNomeArq */
    	$nomeArquivo 	= isset($context->nomeArqDinamico) ? $context->nomeArqDinamico :  date("dmY");
    	$nomeArquivo	= (isset($context->prefixoNomeArq) ? ($context->prefixoNomeArq . "_" . $nomeArquivo) : ($nomeArquivo));
    	$nomeArquivo 	= self::removerCaractesEspeciais($nomeArquivo);
    	$nomeArquivo	.= "_log" . self::EXTENSAO;
    
    	/** Monta o texto de log para gravar no arquivo */
    	$msg = "[LOG: $levelString]|[DATA: {$data_hora}]|[USUARIO: {$usuario}]|[NAVEGADOR: {$navegador}]|[CLASSE: $context->classe]|[METODO: $context->metodo]|[LINHA: $linha]|[IP: $ip]|[ARQUIVO: $arquivo]|[MENSAGEM: {$message}]";
    
    	return self::salvarlog($diretorio, $nomeArquivo, $msg);
    }
        
 	/**
     * @tutorial: Registrar os LOG em um arquivo TXT, conforme configuraÃ§Ãµes
     * dos parametros descritos abaixo:
 	 * 
 	 * @param string $diretorio Caminho do diretÃ³rio.
 	 * @param string $arquivo Nome do Arquivo.
 	 * @param string $msg 
 	 */
    private static function salvarlog($diretorio, $nomeArquivo, $msg)
    {
    	$file = fopen(self::getFile($diretorio, $nomeArquivo), "a+");
    	if ($file){
    		fwrite($file,  utf8_encode($msg) . "\r\n");
	    	fclose($file);
	    	return true;
    	}
    	return false;
    }

    /**
     * @tutorial : Obtem o diretÃ³rio aonde serÃ¡ armazenado o log.
     * Contatena o diretÃ³rio com o Arquivo.
     * 
     * @param string $diretorio Caminho do diretÃ³rio.
 	 * @param string $arquivo Nome do Arquivo.
     * 
     * @return string Diretorio aonde serÃ¡ armazenado o arquivo
     */
    private static function getFile($diretorio, $arquivo = '')
    {
    	return $diretorio . $arquivo;
    }
    
    /**
     * @tutorial : Verifica o browser que foi feita a requisiÃ§Ã£o,
     * definido na variavel global do apache: $_SERVER['HTTP_USER_AGENT']
     * 
     * @return string Browser que foi feita a requisiÃ§Ã£o
     */
    public static function getBrowser()
    {
        $navegador = $_SERVER['HTTP_USER_AGENT']; //Pega o navegador

        switch ($navegador) {
            case preg_match("/MSIE 8.0/i", $navegador) > 0:
                return "Internet Explorer 8";
                break;

            case preg_match("/MSIE 7.0/i", $navegador) > 0:
                return "Internet Explorer 7";
                break;
            
            case preg_match("/MSIE 6.0/i", $navegador) > 0:
                return "Internet Explorer 6";
                break;
            
            case preg_match("/Firefox/i", $navegador) > 0:
                return "Mozilla Firefox";
                break;
            
            case preg_match("/Opera/i", $navegador) > 0:
                return "Opera";
                break;
            
            case preg_match("/Chrome/i", $navegador) > 0:
                return "Google Chrome";
                break;
                
            case preg_match("/Safari/i", $navegador) > 0:
                return "Safari";
                break;
            
            default:
                return "Desconhecido";
                break;
        }

    }
    
    /**
     * @tutorial : Array com as enumeraÃ§Ãµes dos self::$levels
     * 
     * @example
     * Logger::getLevels()
     * 
     * @return array
     */
    public static function getLevels(){
    	return array_flip(static::$levels);
    }
    
    /**
     * @tutorial : Obtem o nome do level de acordo com o parametro $level
     * verifica se o parametro existe na chave do atributo $levels.
     * 
     * @param int $level Logger::INFO
     * 
     * @throws InvalidArgumentException Tipo de log inexistente.
     * @return string Retorna o Nome do Lavel (INFO, ERROR, WARNING etc...)
     */
    private static function getLevelName($level){
    	if (!isset(static::$levels[$level])) {
    		throw new InvalidArgumentException('Level "'.$level.'" is not defined, use one of: '.implode(', ', array_keys(static::$levels)));
    	}    
    	return static::$levels[$level];
    }
    
    private static function removerCaractesEspeciais($string) {
    	return preg_replace('{\W}', '', preg_replace('{ +}', 
    			'', strtr(utf8_decode(html_entity_decode($string)),
    			utf8_decode('Ã€Ã�ÃƒÃ‚Ã‰ÃŠÃ�Ã“Ã•Ã”ÃšÃœÃ‡Ã‘Ã Ã¡Ã£Ã¢Ã©ÃªÃ­Ã³ÃµÃ´ÃºÃ¼Ã§Ã±'),'AAAAEEIOOOUUCNaaaaeeiooouucn')));
    }
    
    /**
     * @tutorial : ExeceÃ§Ãµes tratadas que nÃ£o sÃ£o ocorrencia de erro. Usar para apontar mal uso de uma API ou Ferramenta, ou apontar uma falha de tratamento de um parametro.
     *
     * @example Logger::logWarning("Warning Message", __FILE__, __LINE__);
     *
     * @param string $message
     * @param __FILE__ $file
     * @param __LINE__ $line
     * @param array $context array('usuario' => "1017",  'prefixoNomeArq'=>'ws_portal', 'classe' => __CLASS__, 'metodo' => __FUNCTION__);
     * 
     */
    public static function logWarning($message, $file, $line, $context = array()) { return self::gerarLog(static::WARNING, $message, $file, $line, $context); }
    
    /**
     * @tutorial : Registar Logs de Eventos de processamento Exemplos: Registros do UsuÃ¡rio, logs de navegaÃ§Ã£o do usuÃ¡rio.
     *
     * @example Logger::logInfo("Info Message", __FILE__, __LINE__);
     *
     * @param string $message
     * @param __FILE__ $file
     * @param __LINE__ $line
     * @param array $context array('usuario' => "1017",  'prefixoNomeArq'=>'ws_portal', 'classe' => __CLASS__, 'metodo' => __FUNCTION__);
     */
    public static function logInfo($message, $file, $line, $context = array()) { return self::gerarLog(static::INFO, $message, $file, $line, $context); }
    
    /**
     * @tutorial : Registrar eventos incomuns
     *
     * @example Logger::logNotice("Notice Message", __FILE__, __LINE__);
     *
     * @param string $message
     * @param __FILE__ $file
     * @param __LINE__ $line
     * @param array $context array('usuario' => "1017",  'prefixoNomeArq'=>'ws_portal', 'classe' => __CLASS__, 'metodo' => __FUNCTION__);
     */
    public static function logNotice($message, $file, $line, $context = array()) { return self::gerarLog(static::NOTICE, $message, $file, $line, $context); }
    
    /**
     * @tutorial : Registrar erros em runtime
     *
     * @example Logger::logError("Error Message", __FILE__, __LINE__);
     *
     * @param string $message
     * @param __FILE__ $file
     * @param __LINE__ $line
     * @param array $context array('usuario' => "1017",  'prefixoNomeArq'=>'ws_portal', 'classe' => __CLASS__, 'metodo' => __FUNCTION__);
     */
    public static function logError($message, $file, $line, $context = array()) { return self::gerarLog(static::ERROR, $message, $file, $line, $context); }
    
    /**
     * @tutorial : CondiÃ§Ãµes Criticas, nÃ£o conseguiu encontrar diretÃ³rio, ou acessar pÃ¡gina.
     *
     * @example Logger::logCritical("Critical Message", __FILE__, __LINE__);
     *
     * @param string $message
     * @param __FILE__ $file
     * @param __LINE__ $line
     * @param array $context array('usuario' => "1017",  'prefixoNomeArq'=>'ws_portal', 'classe' => __CLASS__, 'metodo' => __FUNCTION__);
     */
    public static function logCritical($message, $file, $line, $context = array()) { return self::gerarLog(static::CRITICAL, $message, $file, $line, $context); }
    
    /**
     * @tutorial : Apontar algum serviÃ§o que estÃ¡ fora, como falha na conexÃ£o com o banco de dados.
     *
     * @example Logger::logAlert("Alert Message", __FILE__, __LINE__);
     *
     * @param string $message
     * @param __FILE__ $file
     * @param __LINE__ $line
     * @param array $context array('usuario' => "1017",  'prefixoNomeArq'=>'ws_portal', 'classe' => __CLASS__, 'metodo' => __FUNCTION__);
     */
    public static function logAlert($message, $file, $line, $context = array()) { return self::gerarLog(static::ALERT, $message, $file, $line, $context); }
    
    /**
     * @tutorial : Log de Debug
     *
     * @example Logger::logDebug("Debug Message", __FILE__, __LINE__);
     *
     * @param string $message
     * @param __FILE__ $file
     * @param __LINE__ $line
     * @param array $context array('usuario' => "1017",  'prefixoNomeArq'=>'ws_portal', 'classe' => __CLASS__, 'metodo' => __FUNCTION__);
     */
    public static function logDebug($message, $file, $line, $context = array()) { return self::gerarLog(static::DEBUG, $message, $file, $line, $context); }
    
    /**
     * @tutorial : Um processamento ou Cron que falhou e parou a excecuÃ§Ã£o
     *
     * @example Logger::logEmergency("Emergency Message", __FILE__, __LINE__);
     *
     * @param string $message
     * @param __FILE__ $file
     * @param __LINE__ $line
     * @param array $context array('usuario' => "1017",  'prefixoNomeArq'=>'ws_portal', 'classe' => __CLASS__, 'metodo' => __FUNCTION__);
     */
    public static function logEmergency($message, $file, $line, $context = array()) { return self::gerarLog(static::EMERGENCY, $message, $file, $line, $context); }
    
}
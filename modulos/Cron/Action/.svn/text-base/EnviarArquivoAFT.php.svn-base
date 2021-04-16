<?php

use module\Parametro\ParametroCobrancaRegistrada;

class EnviarArquivoAFT {
    private $connFTP;
    private $servidor;
    private $port = 2121;
    private $usuario;
    private $senha;
    private $outbox;
    private $inbox;

    public function __construct() {
    	global $conn;
    	
    	$this->dao = new CronEnviarArquivoAFTDAO($conn);
        $this->finCobrancaReg = new FinCobrancaRegistrada;

        $this->outbox = ParametroCobrancaRegistrada::getCaixaPostalAftOutbox();
        $this->inbox = ParametroCobrancaRegistrada::getCaixaPostalAftInbox();
    }

    public function enviarArquivoAFT() {
        $this->carregaConexao();

        $parametros = new StdClass;
        $parametros->ddl_forma_cobranca_arquivo = '84';
        $parametros->txt_qtde_titulo = '99999';
        $parametros->txt_qtde_titulos = '99999';

        try{
        	
        	$caminhoArquivoAFT = $this->finCobrancaReg->geraCSVArquivo($parametros, 'AFT', 2750);
        	
        	if(!$caminhoArquivoAFT){
        		//echo 'Não há arquivos gerados para enviar para a pasta OUTBOX do AFT'. PHP_EOL . PHP_EOL;
        	}else{
        	
	        	$pastaLocal = $caminhoArquivoAFT['failed']->file_path;
	        	$arquivoLocal = $caminhoArquivoAFT['failed']->file_name;
	        	
	        	$envio = @ftp_put($this->connFTP, $this->outbox . $arquivoLocal, $pastaLocal . $arquivoLocal, FTP_BINARY);
	        	
	        	if (!$envio) {
	        		throw new Exception('Falha ao mover arquivo para o AFT.');
	        	}
	        	
	        	ftp_close($this->connFTP);
	        	
	        	//echo 'Arquivo enviado para o AFT com sucesso. '. PHP_EOL . PHP_EOL;
        	}
        	
        }catch (Exception $e){
        	echo  $e->getMessage();
        }

    }

    public function lerRetornoAFT() {
    	
        $this->carregaConexao();

        $arquivos = $this->buscaArquivosInbox();
        
        if(count($arquivos) > 0){
        	
        	$arquivosNaoProcessados = array();
        	
        	require_once _MODULEDIR_ . '/Financas/Action/FinRetornoCobrancaRegistrada.php';
        	
        	foreach ($arquivos as $arquivoCaminhoRemoto) {
        		
        		$arquivoCaminhoLocal = $this->salvarLocalmenteArquivoProcessado($arquivoCaminhoRemoto);
        		$action = new FinRetornoCobrancaRegistrada;
        		$processado = $action->upload('dadosRetornoCobranca', basename($arquivoCaminhoLocal), 84,'AFT');
        		
        		if (!empty($processado['msg'])) {
        			$arquivosNaoProcessados[] = $arquivo;
        		} else {
        			$this->moveArquivoProcessado($arquivoCaminhoRemoto);
        		}
        	}
        	
        	ftp_close($this->connFTP);
        	
        	return array_diff($arquivos, $arquivosNaoProcessados);

         }else{
         	//echo 'Não há arquivos na pasta INBOX do AFT para processamento.'. PHP_EOL . PHP_EOL;
         }
    }

    private function buscaArquivosInbox() {
        $arquivos = array();

        if (is_array($this->inbox)) {
            foreach ($this->inbox as $inboxFolder) {
                $arrayArquivos = ftp_nlist($this->connFTP, $inboxFolder);
                if ($arrayArquivos) {
                    $arquivos = array_merge($arquivos,$arrayArquivos);
                }
            }
        } else {
            $arquivos = ftp_nlist($this->connFTP, $this->inbox);
        }

        if (count($arquivos) == 0) {
            return false;
        }

        return array_filter((array) $arquivos, function ($arquivo) {
            return strcasecmp(substr($arquivo, -4), '.ret') === 0 
                || strcasecmp(substr($arquivo, -4), '.txt') === 0;
        });
    }

    private function salvarLocalmenteArquivoProcessado($arquivo) {
        $nomeArquivo = basename($arquivo);
        $arquivoCaminhoLocal = _SITEDIR_ . "processar_cobr_registrada/{$nomeArquivo}";
        $download = @ftp_get($this->connFTP, $arquivoCaminhoLocal, $arquivo, FTP_BINARY);

        if (!$download) {
        	throw new Exception("Erro ao fazer download do arquivo '$arquivo' para processamento.". PHP_EOL);
        }

        return $arquivoCaminhoLocal;
    }

    private function moveArquivoProcessado($arquivo) {
        $path = '';
        $pathArquivoProcessado = '';

        if (is_array($this->inbox)) {
            foreach ($this->inbox as $inboxFolder) {
                if (strpos($arquivo,$inboxFolder) !== false) {
                    $path = $inboxFolder . 'Processados/';
                    $pathArquivoProcessado = str_replace($inboxFolder, $path, $arquivo);
                    break;
                }
            }
        } else {
            $path = $this->inbox . 'Processados/';
            $pathArquivoProcessado = str_replace($this->inbox, $path, $arquivo);
        }
    	
    	if(!ftp_chdir($this->connFTP,$path)){
    		ftp_mkdir( $this->connFTP, $path);
    	}
       
    	$mode = "777";
    	$mode = octdec( str_pad($mode,4,'0',STR_PAD_LEFT) );
    	///ftp_chmod($ftp_stream, $mode, $file); 
        
    	// tenta usar o chmod em $file para 644
    	if (ftp_chmod($this->connFTP, $mode, $path) !== false) {
    		echo "Foram mudadas as permissões da pasta $path para 644\n";
    	} else {
    		echo "não foi possível usar o chmod da pasta $path\n";
    	}
        
    	//permite mover o arquivo
    	if (ftp_chmod($this->connFTP, $mode, $pathArquivoProcessado) !== false) {
    		echo "Foram mudadas as permissões do arquivoa $pathArquivoProcessado para 644\n";
    	} else {
    		echo "não foi possível usar o chmod do arquivo $pathArquivoProcessado\n";
    	}
    	    	
    	$movido = ftp_rename($this->connFTP, $arquivo, $pathArquivoProcessado);

        print_r($movido); 
        
        if (!$movido) {
            throw new Exception("Erro ao mover de '{$arquivo}' para '{$pathArquivoProcessado}'");
        }else{
        	echo $arquivo. ' -> Arquivo movido com sucesso.'. PHP_EOL;
        }
    }

    private function carregaConexao() {
        if (is_resource($this->connFTP)) {
            return;
        }

        $this->carregaParametrizacao();
        $this->connFTP = ftp_connect($this->servidor, $this->port);

        if (!$this->connFTP) {
            throw new Exception('Erro ao conectar com o servidor FTP');
        }

        $login = ftp_login($this->connFTP, $this->usuario, $this->senha);

        if (!$login) {
            throw new Exception('Erro ao autenticar com o servidor FTP');
        }

        ftp_pasv($this->connFTP, true);
    }

    private function carregaParametrizacao() {
        $sufixo = _AMBIENTE_ == 'PRODUCAO' ? 'PRODUCAO' : 'TESTE';
        $this->senha = $this->dao->buscarParametrosAFT(array('pcsipcsoid' => 'COBRANCA_REGISTRADA', 'pcsioid' => "SENHA_AFT_{$sufixo}"));
        $this->usuario = $this->dao->buscarParametrosAFT(array('pcsipcsoid' => 'COBRANCA_REGISTRADA', 'pcsioid' => "USUARIO_AFT_{$sufixo}"));
        $this->servidor = $this->dao->buscarParametrosAFT(array('pcsipcsoid' => 'COBRANCA_REGISTRADA', 'pcsioid' => "IP_AFT_{$sufixo}"));
        $this->senha = $this->senha['pcsidescricao'];
        $this->usuario = $this->usuario['pcsidescricao'];
        $this->servidor = $this->servidor['pcsidescricao'];
    }

    public function verificaArquivosNoServidor(){
        $this->carregaConexao();
        $outbox = ftp_nlist($this->connFTP, $this->outbox);
        $inbox = array();
        $processado = array();

        if (is_array($this->inbox)) {
            foreach ($this->inbox as $inboxFolder) {
                $arrayArquivos = ftp_nlist($this->connFTP, $inboxFolder);
                if ($arrayArquivos) {
                    $inbox = array_merge($inbox,$arrayArquivos);
                }
                $arrayArquivos = ftp_nlist($this->connFTP, $inboxFolder . '/Processados/');
                if ($arrayArquivos) {
                    $processado = array_merge($processado, $arrayArquivos);
                }
            }
        } else {
            $inbox = ftp_nlist($this->connFTP, $this->inbox);
            $processado = ftp_nlist($this->connFTP, $this->inbox . '/Processados/');
        }

        ftp_close($this->connFTP);
        return compact('outbox', 'inbox', 'processado');
    }
}

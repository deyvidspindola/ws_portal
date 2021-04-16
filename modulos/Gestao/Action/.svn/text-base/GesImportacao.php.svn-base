<?php

/**
 * Importação.
 *
 * @package Gestão
 * @author  João Paulo Tavares da Silva <joao.silva@meta.com.br>
 */

class GesImportacao{

	private $dao;

	private $view;

    private $layout;

    const MENSAGEM_ALERTA_CAMPO_OBRIGATORIO = "Existem campos obrigatórios não preenchidos.";

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    const MENSAGEM_ALERTA_ARQUIVO_INVALIDO = "Apenas arquivos com extensão .csv são permitidos para a importação.";

    const MENSAGEM_ALERTA_IMPORTACAO_NAO_REALIZADA = "Importação não realizada, o arquivo contém dados inválidos ou não preenchidos.";

    const MENSAGEM_ALERTA_VALORES_INVALIDOS = "Importação não realizada, existem valores no arquivo que devem estar previamente cadastrados.";

    const MENSAGEM_SUCESSO_ARQUIVO_IMPORTADO = "Arquivo importado com sucesso.";

    const MENSAGEM_ALERTA_REGISTROS_AMBIGUOS = "Importação não realizada, o arquivo não deve conter registros com a mesma meta, indicador e data.";

    const MENSAGEM_ALERTA_CODIGO_META_SEM_VALOR_PREVISTO = "Importação não realizada, o registro ([CODIGO_DA_META]) não possui um valor previsto.";

    const MENSAGEM_ALERTA_DATA_FORA_PERIODO = "Importação não realizada, a data do registro ([CODIGO_DA_META]) não está entre o mês anterior e o mês atual.";

	public function __construct(GesImportacaoDAO $dao, $layout){

		$this->dao = $dao;

        $this->layout = $layout;

		 /*
         * Cria o objeto View.
         */
        $this->view = new stdClass();

        $this->param = new stdClass();

        $this->view->status = true;

        // Dados
        $this->view->dados = null;

        $this->view->permissao = false;

        $this->view->erro = false;

		$this->view->caminho = _MODULEDIR_ . 'Gestao/View/ges_importacao/';

		$this->tratarParametros();
	}

	public function index(){

		$this->verificarPermissoes();
		$this->validarParametros();
		if($this->view->status && isset($this->param->acao)){

			$acao = $this->param->acao;
			if(method_exists($this, $acao)){

				$this->$acao();
			}else{
				$this->view->mensagem->erro = self::MENSAGEM_ERRO_PROCESSAMENTO;
			}
		}

		include $this->view->caminho . 'index.php';
	}

	/**
	 * Método responsavel pela importação de Indicadores Previstos.
	 *
	 * @return void
	 */
	private function importarIndicadoresPrevistos(){

		if($this->validarArquivo()){
		   $arquivoMemoria  = $this->param->arquivo['tmp_name'];
    	   $linhas = explode("\n", file_get_contents($arquivoMemoria));
    	   $numeroLinha = 0;
    	   $importado = true;
    	   $this->dao->begin();
    	   foreach($linhas as $linha){
                $numeroLinha++;

	    	   	 // Pula linhas vazias e a primeira linha (header)
                if ( ( strlen(trim($linha)) == 0 || (!preg_match('/[^;\r\n]/', $linha)) ) || ($numeroLinha == 1)) {
	                continue;
	            }
	            $colunas = explode(';', $linha);

	            $dados = new stdClass();
	            $dados->codMeta       = $this->tratarDados($colunas[0], 'string');
	            $dados->codIndicador  = $this->tratarDados($colunas[1], 'string');
	            $dados->data          = $this->tratarDados($colunas[2], 'data');
	            $dados->valorPrevisto = $this->tratarDados($colunas[3], 'float');
    	   		$dados->usuario        = $_SESSION['usuario']['oid'];

    	   		if(!$this->verificarDadosObrigatorios($dados)){
    	   			$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_IMPORTACAO_NAO_REALIZADA;
    	   			$importado = false;
    	   			break;
    	   		}

                $dados->valorRealizado = 0;

	   			$indicador = $this->dao->buscarIndicador($dados);
	   			if(!is_object($indicador)){
	   				$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_VALORES_INVALIDOS;
	   				$importado = false;
	   				break;
	   			}
                $dados->idIndicador = $indicador->gmioid;

   				$meta = $this->dao->buscarMeta($dados);
            	if(!is_object($meta)){
            		$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_VALORES_INVALIDOS;
	   				$importado = false;
	   				break;
            	}
                $dados->idMeta = $meta->gmeoid;

	   			try{
	   				$indicadorMeta = $this->dao->buscarIndicadorMeta($dados);
	   				if(is_object($indicadorMeta)){
	            		$this->dao->atualizarIndicadorMeta($dados);
	   				}else{
	   					$this->dao->gravarIndicadorMeta($dados);
	   				}
            	}catch(ErrorException $e){
            		$this->view->mensagem->erro = $e->getMessage();
	            	$importado = false;
	            	break;
            	}
    	   }
    	   if($importado){
    	   		$this->view->mensagem->sucesso = self::MENSAGEM_SUCESSO_ARQUIVO_IMPORTADO;
    	   		$this->dao->commit();
    	   }else{
    	   		$this->dao->rollback();
    	   }
		}else{
			$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_ARQUIVO_INVALIDO;
		}
	}

	/**
	 * Método responsavel pela importação de Indicadores Realizados.
	 *
	 * @return void
	 */
	private function importarIndicadoresRealizados(){

		if($this->validarArquivo()){
		   $arquivoMemoria  = $this->param->arquivo['tmp_name'];
    	   $linhas = explode("\n", file_get_contents($arquivoMemoria));
    	   $numeroLinha = 0;
    	   $importado = true;
    	   $this->dao->begin();
    	   $registros = array();
    	   foreach($linhas as $linha){
                $numeroLinha++;

	    	   	 // Pula linhas vazias e a primeira linha (header)
	            if ( ( strlen(trim($linha)) == 0 || (!preg_match('/[^;\r\n]/', $linha)) ) || ($numeroLinha == 1)) {
	                continue;
	            }
	            $colunas = explode(';', $linha);
	            $dados = new stdClass();
	            $dados->codMeta        = $this->tratarDados($colunas[0], 'string');
	            $dados->codIndicador   = $this->tratarDados($colunas[1], 'string');
	            $dados->data           = $this->tratarDados($colunas[2], 'data');
	            $dados->valorRealizado = $this->tratarDados($colunas[3], 'float');
	            $dados->usuario        = $_SESSION['usuario']['oid'];

            	if(!$this->verificarDadosObrigatorios($dados)){
					$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_IMPORTACAO_NAO_REALIZADA;
	   				$importado = false;
	   				break;
            	}

                $dados->valorPrevisto = 0;

            	if(!$this->view->permissao && !$this->verificarPeriodoData($dados)){
            		$this->view->mensagem->alerta = str_replace("([CODIGO_DA_META])", $dados->codMeta, self::MENSAGEM_ALERTA_DATA_FORA_PERIODO);;
	   				$importado = false;
	   				break;
            	}

            	if($this->verificarRegistrosAmbiguos($registros, $dados)){
	            	$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_REGISTROS_AMBIGUOS;
    	   			$importado = false;
    	   			break;
	            }

            	$indicador = $this->dao->buscarIndicador($dados);
            	if(!is_object($indicador)){
            		$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_VALORES_INVALIDOS;
	   				$importado = false;
	   				break;
            	}
                $dados->idIndicador = $indicador->gmioid;

                $meta = $this->dao->buscarMeta($dados);
            	if(!is_object($meta)){
            		$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_VALORES_INVALIDOS;
	   				$importado = false;
	   				break;
            	}
                $dados->idMeta = $meta->gmeoid;

            	/*$indicadorMeta = $this->dao->buscarIndicadorMeta($dados);
            	if(!is_object($indicadorMeta)){
            		$this->view->mensagem->alerta = str_replace("([CODIGO_DA_META])", $dados->codMeta, self::MENSAGEM_ALERTA_CODIGO_META_SEM_VALOR_PREVISTO);
	   				$importado = false;
	   				break;
            	}*/

            	try{
                    $indicadorMeta = $this->dao->buscarIndicadorMeta($dados);
                    if(is_object($indicadorMeta)){
                        $this->dao->atualizarIndicadorMeta($dados);
                    }else{
                        $this->dao->gravarIndicadorMeta($dados);
                    }
            	}catch(ErrorException $e){
            		$this->view->mensagem->erro = $e->getMessage();
	            	$importado = false;
	            	break;
            	}

                array_push($registros, $dados);
	        }
	        if($importado){
    	   		$this->view->mensagem->sucesso = self::MENSAGEM_SUCESSO_ARQUIVO_IMPORTADO;
    	   		$this->dao->commit();
    	   	}else{
    	   		$this->dao->rollback();
    	    }
		}else{
			$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_ARQUIVO_INVALIDO;
		}
	}

	/**
	 * Método responsavel pela importação de Ações.
	 *
	 * @return void
	 */
	private function importarAcao(){

		if($this->validarArquivo()){
			$arquivoMemoria  = $this->param->arquivo['tmp_name'];
    	    $linhas = explode("\n", file_get_contents($arquivoMemoria));
    	    $numeroLinha = 0;
    	    $importado = true;
    	    $this->dao->begin();
    	    foreach($linhas as $linha){
                $numeroLinha++;

	    	   	 // Pula linhas vazias e a primeira linha (header)
	            if ( ( strlen(trim($linha)) == 0 || (!preg_match('/[^;\r\n]/', $linha)) ) || ($numeroLinha == 1)) {
	                continue;
	            }
	            $colunas = explode(';', $linha);

	            $dados = new stdClass();
	            $dados->idPlanoDeAcao  = $this->tratarDados($colunas[0], 'int');
	            $dados->nomeAcao	   = $this->tratarDados($colunas[1], 'string');
	            $dados->dataInicio     = $this->tratarDados($colunas[2], 'data');
	            $dados->dataFim		   = $this->tratarDados($colunas[3], 'data');
	            $dados->usuario        = $_SESSION['usuario']['oid'];

	            if(!$this->verificarDadosObrigatorios($dados)){
	            	$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_IMPORTACAO_NAO_REALIZADA;
	   				$importado = false;
	   				break;
	            }

	            try{
	            	$planoDeAcao = $this->dao->buscarPlanoDeAcao($dados);
	            	$dados->funoid = $planoDeAcao->gplfunoid_responsavel;
	            	$this->dao->gravarAcao($dados);
	            }catch(ErrorException $e){
	            	$this->view->mensagem->erro = $e->getMessage();
	            	$importado = false;
	            	break;
	            }
	        }
	        if($importado){
    	   		$this->view->mensagem->sucesso = self::MENSAGEM_SUCESSO_ARQUIVO_IMPORTADO;
    	   		$this->dao->commit();
    	   	}else{
    	   		$this->dao->rollback();
    	    }
		}else{
			$this->view->mensagem->alerta = self::MENSAGEM_ALERTA_ARQUIVO_INVALIDO;
		}
	}

	/**
	 * Verifica se a data espeficicada pelo usuário no arquivo
	 * esta entre o mês atual ou mês anterior.
	 * @param stdClass $dados
	 * @return boolean
	 */
	private function verificarPeriodoData(stdClass $dados){

		$data = explode("-",$dados->data);
		$mesArquivo = $data[1];
		$anoArquivo = $data[0];
		$mesAtual    = date('m');
		$mesAnterior = $mesAtual - 1;
		$anoAtual    = date('Y');
		$anoAnterior  = $anoAtual - 1;

        if( ($anoArquivo == $anoAtual) && ($mesArquivo == $mesAtual || $mesArquivo == $mesAnterior) ) {
            return true;
        } else if( ($mesArquivo == 12 && $mesAtual == 1) && ($anoArquivo == $anoAnterior) ) {
            return true;
        }

		return false;
	}

	/**
	 * Verifica se existem registros ambiguos no arquivo.
	 *
	 * @param array $registros
	 * @param stdClass $dados
	 * @return boolean
	 */
	private function verificarRegistrosAmbiguos($registros, stdClass $dados){

		foreach($registros as $registro){
			if( ($registro->codIndicador == $dados->codIndicador) && ($registro->codMeta == $dados->codMeta) && ($registro->data == $dados->data) ) {
				return true;
            }
		}
		return false;
	}

	/**
	 * Verifica se a extenção do arquivo é .csv
	 * @return boolean
	 */
	private function validarArquivo(){
		if(!stripos($this->param->arquivo['name'], ".csv")){
			return false;
		}
		return true;
	}

	/**
	 * Verifica se exitem dados não preenchidos dentro de uma linha do arquivo.
	 *
	 * @param stdClass $dados
	 * @return boolean
	 */
	private function verificarDadosObrigatorios(stdClass $dados){
		foreach($dados as $dado){
			if(empty($dado)){
				return false;
			}
		}
		return true;
	}

	/**
     * Verifica se o usuário tem permissao de super usuario. Se tiver será apresentado
     * a tela inteira, senão será apresentado apenas o quadro "Importar Indicadores Realizados".
     *
     * @return void
     */
	private function verificarPermissoes(){
		try{

			$permissoes = $this->dao->buscarPermissoesUsuario($_SESSION['usuario']['funoid']);

			if($permissoes->super_usuario == 1){
				$this->view->permissao = true;
			}
		}catch(ErrorException $e){
			$this->view->erro = true;
			$this->view->mensagem->erro = $e->getMessage();
		}
	}

    /**
     * Faz o tratamento dos dados.
     *
     * @param string $dado
     * @param string $tipo
     * @return string || int || float
     */
    private function tratarDados($dado, $tipo) {

        $dado = trim($dado);

        if(empty($dado)) {
            return $dado;
        }

        if($tipo == 'int') {

            if(is_numeric($dado)) {
                $dado = intval($dado);
            }
            else {
                return '';
            }
        }else if($tipo == 'data'){
        	$data = explode("/",$dado);

        	$d = $data[0];
        	$m = $data[1];
        	$y = $data[2];

        	if(!is_null($d) && !is_null($m) && !is_null($y)){

	        	if(checkdate($m, $d, $y)){
	        		return $y . '-' . $m . '-' . $d;
	        	}
	        	return '';
        	}else{
        		return '';
        	}
        }else if ($tipo == 'float') {

            $dado = str_replace(",",".", $dado);

             if(is_numeric($dado)) {
                 $dado = floatval($dado);
            }
            else {
                return '';
            }
        }
        return $dado;
    }

    /**
     * Método que instância os dados do $_POST e $_GET.
     *
     * @return Void
     */
    private function tratarParametros() {
        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $this->param->$key = isset($_POST[$key]) ? $value : '';
            }
        }

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {
                if (!isset($this->param->$key)) {
                    $this->param->$key = isset($_GET[$key]) ? $value : '';
                }
            }
        }

        if (count($_FILES) > 0) {
           foreach ($_FILES as $key => $value) {
               if (!isset($retorno->$key)) {
                     $this->param->$key = isset($_FILES[$key]) ? $value : '';
               }
           }
        }
        if(!empty($this->param->arquivo_ind_prev['name'])){

        	$this->param->arquivo = $this->param->arquivo_ind_prev;

        }else if(!empty($this->param->arquivo_ind_real['name'])){

        	$this->param->arquivo = $this->param->arquivo_ind_real;

        }else if(!empty($this->param->arquivo_acao['name'])){
			$this->param->arquivo = $this->param->arquivo_acao;
        }
    }

    /**
     * Método que verifica campos obrigatórios.
     *
     * @return Void
     */
    private function validarParametros(){

        $camposDestacados = array();

        if($this->param->acao == 'importarAcao'){
        	if(empty($this->param->arquivo_acao['name'])) {
	            $camposDestacados[] = array(
	                'campo'    => 'arquivo_acao',
	            );
           	    $this->view->status = false;
        	}

        } else if($this->param->acao == 'importarIndicadoresPrevistos'){
        	if(empty($this->param->arquivo_ind_prev['name'])) {

	            $camposDestacados[] = array(
	                'campo'    => 'ind_prev',
	            );
           	    $this->view->status = false;
        	}

        }else if($this->param->acao == 'importarIndicadoresRealizados'){
        	if(empty($this->param->arquivo_ind_real['name'])) {
	            $camposDestacados[] = array(
	                'campo'    => 'ind_real',
	            );
           	    $this->view->status = false;
        	}
        }

        $this->view->destaque = $camposDestacados;

        if (!$this->view->status) {
            $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_CAMPO_OBRIGATORIO;
        }
	}
}
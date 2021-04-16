<?php
set_time_limit(600);
/**
 * Classe FinManutencaoFaturamentoUnificado.
 * Camada de regra de negócio.
 *
 * @package  Financas
 * @author   André Luiz Zilz <andre.zilz@meta.com.br>
 *
 */
class FinManutencaoFaturamentoUnificado {

    /*
     * Constantes de Mensagens
     */
    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_ALERTA_SEM_RESUMO        = "Não há resumo de Faturamento gerado.";
    const MENSAGEM_ALERTA_MES_VIGENTE       = "Alterações são permitidas apenas no mês vigente.";
    const MENSAGEM_ALERTA_ARQUIVO_INVALIDO      = "Formato de arquivo inválido.";
    const MENSAGEM_ERRO_PROCESSAMENTO       = "Foram encontrados erros durante o processamento. Analise o log para mais informações.";
    const MENSAGEM_ERRO_RESUMO_MES_VIGENTE  = "Alterações são permitidas apenas no resumo do mês vigente.";
    const MENSAGEM_ERRO_PROCESSAMENTO_BANCO = "Houve um erro no processamento dos dados.";
    const MENSAGEM_SUCESSO_PROCESSO         = "Arquivo processado com sucesso.";
    const MOTIVO_DADO_INVALIDO              = 'Linha com informações faltantes ou dados inválidos.';
    const MENSAGEM_ERRO_GERAR_ARQUIVO       = 'Não foi possível gravar arquivo no diretório:';


    /**
     * Contém dados a serem utilizados na View.
     *
     * @var stdClass
     */
    private $view;

    /**
     * Instancia da camada de persistência de banco de dados
     *
     * @var FinManutencaoFaturamentoUnificadoDAO
     */
    private $dao;

    /**
     * Armazena os erros do processamento do arquivo
     *
     * @var type
     */
    private $errosArquivo;


    /**
     * Método construtor.
     *
     * @param CadExemploDAO $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {

        //Verifica o se a variável é um objeto e a instancia na atributo local
        if (is_object($dao)) {
            $this->dao = $dao;
        }

        //Cria objeto da view
        $this->view = new stdClass();

        //Mensagem
        $this->view->mensagemErro = '';
        $this->view->mensagemAlerta = '';
        $this->view->mensagemSucesso = '';

        //Dados para view
        $this->view->dados = null;

        //Filtros/parametros utlizados na view
        $this->view->parametros = null;

        //Flag que define bloqueio dos campos
        $this->view->status = false;

        //Tipo de exception
        $this->view->exception = '';

        //Erros do Proecessamento do Arquivo
        $this->errosArquivo = array();

    }

    /**
     * Método padrão da classe.
     *
     * Reponsável também por realizar a pesquisa invocando o método privado
     *
     * @return void
     */
    public function index() {

        //Inclir a view padrão
        require_once _MODULEDIR_ . "Financas/View/fin_manutencao_faturamento_unificado/index.php";
    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     *
     * @return stdClass Parametros tradados
     *
     * @retrun stdClass
     */
    private function tratarParametros() {
        $retorno = new stdClass();

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $retorno->$key = isset($_POST[$key]) ? $value : '';
            }
        }

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {

                //Verifica se atributo já existe e não sobrescreve.
                if (!isset($retorno->$key)) {
                     $retorno->$key = isset($_GET[$key]) ? $value : '';
                }
            }
        }

        if (count($_FILES) > 0) {
           foreach ($_FILES as $key => $value) {

               //Verifica se atributo já existe e não sobrescreve.
               if (!isset($retorno->$key)) {
                    $retorno->$key = isset($_FILES[$key]) ? $value : '';
               }
           }
        }
        return $retorno;
    }

    /**
     * Popula os arrays
     *
     * @return void
     */
    private function inicializarParametros() {

        //Verifica se os parametro existem, senão iniciliza todos
		$this->view->parametros->data_referencia = isset($this->view->parametros->data_referencia) ? $this->view->parametros->data_referencia : "" ;
        $this->view->parametros->arquivo = isset($this->view->parametros->arquivo) ? $this->view->parametros->arquivo : "" ;
        $this->view->parametros->modo = isset($this->view->parametros->modo) ? $this->view->parametros->modo : "" ;

    }

    /**
     * Zera os parâmetros do formulário
     */
    private function limparParametros() {

        $this->view->parametros->data_referencia = null;
        $this->view->parametros->arquivo = null;
        $this->view->parametros->modo = null;
    }

    /**
     * Inicia o processo
     */
    public function processarArquivo() {

        $errosArquivo = array();
        $motivo = '';
        $dados_ressalva = array();

        try{

            $this->dao->begin();

            $this->view->parametros = $this->tratarParametros();

            $this->inicializarParametros();

            $this->validarCamposObrigatorios($this->view->parametros);

            //Validar Tipo CSV
            if(!stripos($this->view->parametros->arquivo['name'], ".csv")) {
                $this->view->exception = 'alerta';
                throw new Exception(self::MENSAGEM_ALERTA_ARQUIVO_INVALIDO);
            }

            /**
             * Verificar se já possui registro no banco com a data vigente
             */
            $data = $this->view->parametros->data_referencia;
            $data = substr($data, 3, 7);
        
            if(!$this->dao->veririfcarDataReferencia($data)) {

                $this->view->exception = 'alerta';
                throw new Exception(self::MENSAGEM_ALERTA_SEM_RESUMO);
            }

            $dadosArquivo = $this->importarArquivo($this->view->parametros->arquivo);
            $total = count($dadosArquivo);
            $comErro = 0;
           
                 $i = 0;
                 foreach($dadosArquivo as $dados) {
                $erro_linha = '';
      
                // Verifica se tem a quantidade correta de campos na linha
                $qtde_colunas = count(explode(';', $dados->dados_linha));
                if($qtde_colunas < 8 || $qtde_colunas > 9) {
                    while($qtde_colunas < 8) {
                        $erro_linha .= ";";
                        $qtde_colunas++;
                    }

                    $erro_linha .= html_entity_decode("Quantidade inv&aacute;lida de colunas. ");
             
                    $dados_ressalva[$i] = str_replace(array("\r\n", "\n\r", "\n", "\r"), '', $dados->dados_linha . ';' . $erro_linha);

                    $comErro++;

                    $i++;

                    continue;
                }

                if($dados->operacao == 'I') {
                    $this->dao->preparaQueryInsert();
                     /*
                      * Verifica validade dos OIDs e guarda os erros
                      */
                    $erro_linha = $this->dao->validarIDs($dados);

                    if(strlen($erro_linha) > 0) {

                        $motivo = self::MOTIVO_DADO_INVALIDO;

                        //Remove a linha com erro dos dados que irão para o banco
                        $dados_ressalva[$i] = str_replace(array("\r\n", "\n\r", "\n", "\r"), '', $dados->dados_linha . ';' . $erro_linha);

                        $comErro++;

                        unset($dadosArquivo[$i]);
                    } else {
                     	if(!$this->dao->inserirDados($dados)) {
                             $motivo = 'Erro técnico ao incluir item.';
                         }
                     }

                     /**
                      * Armazena o erro
                      */
                     if(!empty($motivo)) {
                     	
                        $erros = new stdClass();
                        $erros->dados_linha = $dados->dados_linha;
                        $erros->numero_linha = $dados->numero_linha;
                        $erros->motivo = $motivo;
                   
                        $errosArquivo[] = $erros;
						$motivo = '';
                        unset($erros);
                     }
                } else if($dados->operacao == 'R') {
                    /*
                    * Verifica validade dos OIDs e guarda os erros
                    */
                    $erro_linha = $this->dao->validarIDs($dados);

                    if(strlen($erro_linha) > 0) {

                      $motivo = self::MOTIVO_DADO_INVALIDO;

                      //Remove a linha com erro dos dados que irão para o banco
                        $dados_ressalva[$i] = str_replace(array("\r\n", "\n\r", "\n", "\r"), '', $dados->dados_linha . ';' . $erro_linha);

                        $comErro++;

                      unset($dadosArquivo[$i]);
                    } else {
                    $resultado = $this->dao->excluirDados($dados, $data);

                    if($resultado == 'ERRO') {
                       $motivo = 'Erro técnico ao deletar item.';
                    }
                    else if ($resultado == 'ZERO') {
                        $motivo = 'Item não existe no faturamento.';
                    }

                    /**
                      * Armazena o erro
                      */
                     if(!empty($motivo)) {
                        $erros = new stdClass();
                        $erros->dados_linha = $dados->dados_linha;
                        $erros->numero_linha = $dados->numero_linha;
                        $erros->motivo = $motivo;
						$motivo = '';
                        $errosArquivo[] = $erros;

                        unset($erros);
                     }
                   }
                } else {
                    $erro_linha = $this->dao->validarIDs($dados);

                    if(strlen($erro_linha) > 0) {

                        $motivo = self::MOTIVO_DADO_INVALIDO;

                        //Remove a linha com erro dos dados que irão para o banco
                        $dados_ressalva[$i] = str_replace(array("\r\n", "\n\r", "\n", "\r"), '', $dados->dados_linha . ';' . $erro_linha);

                        $comErro++;

                        unset($dadosArquivo[$i]);
                }
                }

                   $i++;
                }
                $this->errosArquivo = array_merge($this->errosArquivo, $errosArquivo);

            // Gera o arquivo de ressalvas
            // Define o Byte Order Mark(BOM) do CSV para o Excel mostrar caracters especiais corretamente (tudo em UTF-8)
            $BOM = "\xEF\xBB\xBF";
            $arquivo_ressalva = '';
            if(count($dados_ressalva) > 0) {
                $arquivo_ressalva = "contrato;clioid;clinome;obroid;obrdescricao;valor;tipo;operacao;observacao\n";

                foreach($dados_ressalva as $linha) {
                    $arquivo_ressalva .= $linha . "\n";
            }

                $arquivo = new stdClass();
                $arquivo->file_path = "/var/www/docs_temporario/";
                $arquivo->file_name = "ressalvas_" . $banco . date('_d_m_Y_His') . ".csv";

                $file = fopen($arquivo->file_path . $arquivo->file_name, 'a');

                fwrite($file, $BOM . utf8_encode($arquivo_ressalva));

                $this->view->dados['ressalvas'] = $arquivo;
            }

            $this->dao->commit();

            if(count($dados_ressalva) <= 0) {
                $this->view->mensagemSucesso = "Total de itens processados com sucesso: $total.";
            } else {

                $arquivoGerado = $this->gerarLogErros($this->errosArquivo);

                $this->view->mensagemErro = "Processamento concluído com ressalvas. Total de itens que precisam ser reprocessados: $comErro de $total.";
            }

            $this->limparParametros();

            $this->index();

        }catch (Exception $e) {

            $this->dao->rollback();

            if($this->view->exception == 'erro') {
                $this->view->mensagemErro = $e->getMessage();
            }
            else if ($this->view->exception == 'alerta') {
                $this->view->mensagemAlerta = $e->getMessage();
            }

            $this->view->exception = '';
            $this->limparParametros();

            $this->index();
        }
    }


    /**
     * Valida os campos obrigatórios prevendo falha do JS
     *
     * @param stdClass $dados
     * @throws Exception
     */
    private function validarCamposObrigatorios(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        if (!isset($dados->data_referencia) || trim($dados->data_referencia) == '') {
            $camposDestaques['data_referencia'] = 1;
            $error = true;
        }
        if (!isset($dados->arquivo['name']) || trim($dados->arquivo['name']) == '') {
            $camposDestaques['arquivo'] = 1;
            $error = true;
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            $this->view->exception = 'alerta';
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
     * Extrai os dados do arquivo
     *
     * @param array $arquivo
     * @return array
     * @throws Exception
     */
     protected function importarArquivo(array $arquivo) {

         $numeroLinha = 1;
         $dadosArquivo = array();

         if(!isset($arquivo)) {
            throw new Exception("Erro irreparável ao processar planilha! Verifique o formato ou a consistência da planilha.");
         }

        $arquivoMemoria  = $arquivo['tmp_name'];
        $arquivo = pg_escape_string(htmlspecialchars(file_get_contents($arquivoMemoria)));
        $linhas = explode("\n", file_get_contents($arquivoMemoria));


        foreach ($linhas as $linha) {

            // Pula linhas vazias e a primeira linha (header)
            if ((strlen(trim($linha)) == 0) || ($numeroLinha == 1)) {
                $numeroLinha++;
                continue;
            }

            // Remove a observação do arquivo de ressalva
            if(count(explode(';', $linha)) == 9) {
                $linha = substr($linha, 0, strrpos($linha, ';'));
            }

            // Remove colunas vazias do CSV (por exemplo, ";;;;")
            $linha = rtrim(preg_replace('~;{2,}~', ';', $linha), ';');

            $colunas = explode(';', $linha);

            $prefconnumero      = $this->tratarDados($colunas[0], 'int');
            $prefclioid         = $this->tratarDados($colunas[1], 'int');
            $clinome            = $this->tratarDados($colunas[2], 'string');
            $prefobroid         = $this->tratarDados($colunas[3], 'int');
            $obrdescricao       = $colunas[4];
            $prefvalor          = $this->tratarDados($colunas[5], 'float');            
            $preftipo_obrigacao = $this->tratarDados($colunas[6], 'tipo');
            $operacao           = $this->tratarDados($colunas[7], 'operacao');
            $observacao         = $colunas[8];

            // Verificação para campos vazios
            if(empty($prefconnumero)) {
            	$erros = new stdClass();
            	
            	$erros->dados_linha     = $linha;
            	$erros->numero_linha    = $numeroLinha;
            	$erros->motivo          = " Contrato não informado.";
            	
            	$this->errosArquivo[] = $erros;
            	
            	unset($erros);
            } elseif(empty($prefclioid)) {
            	$erros = new stdClass();
            	 
            	$erros->dados_linha     = $linha;
            	$erros->numero_linha    = $numeroLinha;
            	$erros->motivo          = " Cliente não informado.";
            	 
            	$this->errosArquivo[] = $erros;
            	 
            	unset($erros);
            } else if(empty($prefobroid)) {
            	$erros = new stdClass();
            	
            	$erros->dados_linha     = $linha;
            	$erros->numero_linha    = $numeroLinha;
            	$erros->motivo          = "Obrigação financeira não informada.";
            	
            	$this->errosArquivo[] = $erros;
            	
            	unset($erros);
            }else if(empty($prefvalor)) {

            	  $erros = new stdClass();
            	   
            	  $erros->dados_linha     = $linha;
            	  $erros->numero_linha    = $numeroLinha;
            	  $erros->motivo          = "Valor não informado.";
            	   
            	  $this->errosArquivo[] = $erros;
            	   
            	  unset($erros);
            } else if(empty($preftipo_obrigacao)) {
            	  
            	$erros = new stdClass();
            	
            	$erros->dados_linha     = $linha;
            	$erros->numero_linha    = $numeroLinha;
            	$erros->motivo          = "Tipo da obrigação financeira informada inválido.";
            	
            	$this->errosArquivo[] = $erros;
            	
            	unset($erros);
            }
            
            /*
             * Dados validos
             */
            $dados = new stdClass();

            $dados->prefconnumero = (int)$prefconnumero;
            $dados->prefclioid = (int)$prefclioid;
            $dados->clinome = $clinome;
            $dados->prefobroid = (int)$prefobroid;
            $dados->obrdescricao = $obrdescricao;
            $dados->prefvalor = (float)$prefvalor;
            $dados->preftipo_obrigacao = $preftipo_obrigacao;
            $dados->operacao = $operacao;
            $dados->numero_linha = $numeroLinha;
            $dados->dados_linha = $linha;
            $dados->prefdt_referencia = $this->view->parametros->data_referencia;

            $dadosArquivo[] = $dados;

            unset($dados);
            unset($colunas);

            $numeroLinha++;
        }
  
        return $dadosArquivo;
    }

    /**
     * Faz o tratamento dos dados antes de enviar ao banco
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
        }
        else if ($tipo == 'float') {

            $dado = str_replace(",",".", $dado);

             if(is_numeric($dado)) {
                 $dado = floatval($dado);
            }
            else {
                return '';
            }
        }
        else if ($tipo == 'tipo') {
            //Somente Monitoramento ou Locação
            if ($dado != 'M') {

                if ($dado != 'L') {
                    return '';
                }

            }
        }
        else if($tipo == 'operacao') {
            if($dado != 'I' && $dado != 'R') {
                return '';
            }
        }

        return $dado;

    }


    /**
     * Cria um arquivo CSV com o Log de erros
     *
     * @param type $dadosErro
     * @return String
     * @throws Exception
     */
    private function gerarLogErros($dadosErro) {

        require_once "lib/Components/CsvWriter.php";
        $arquivo = "erros_insercao_itens_faturamento_".date('Ymmhis').".csv";
		$diretorio = '/var/www/docs_temporario/';

        try {
            if (is_dir($diretorio) && is_writable($diretorio)) {

                // Gera CSV
                $csvWriter = new CsvWriter( $diretorio.$arquivo, ';', '', true);

                //Cabeçalho
                $csvWriter->addLine(array(
                    'contrato',
                    //'cod_cli',
                    'cliente',
                    'codobroid',
                    'Taxa',
                    'Valor Taxa',
                    //'Tipo',
                    'Linha',
                    'Falha'
                ));

                //Dados
                foreach($dadosErro as $linha) {

                    $linha->dados_linha = str_replace("\n", '', $linha->dados_linha);
                    $linha->dados_linha = str_replace("\r", '', $linha->dados_linha);

                    $dadosLinha = explode(";", $linha->dados_linha);
                    $dadosLinha[6] = $linha->numero_linha;
                    $dadosLinha[7] = $linha->motivo;

                    $csvWriter->addLine($dadosLinha);
                }

                //Verifica se o arquivo foi gerado
                $arquivoGerado = file_exists( $diretorio.$arquivo);
                if ($arquivoGerado === false) {
                    $this->view->exception = 'erro';
                    throw new Exception(self::MENSAGEM_ERRO_GERAR_ARQUIVO . " " . $diretorio);
                }

                return $diretorio.$arquivo;
            }
            else {

                 $this->view->exception = 'erro';
                 throw new Exception(self::MENSAGEM_ERRO_GERAR_ARQUIVO . " " . $diretorio);
            }

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }

    }
}


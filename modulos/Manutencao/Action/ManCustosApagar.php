<?php

/**
 * Classe ManCustosApagar.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   CÁSSIO VINÍCIUS LEGUIZAMON BUENO <cassio.bueno.ext@sascar.com.br>
 *
 */
class ManCustosApagar  {

    /** Objeto DAO da classe */
    private $dao;

	/** propriedade para dados a serem utilizados na View. */
    private $view;

	/** Usuario logado */
	private $usuarioLogado;

    /** criado para setar metodos magicos */
    private $dados;

    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Registro excluído com sucesso.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_ALERTA_CAMPOS_TAMANHO      = "A Descrição deve ter no mínimo três dígitos.";
    const MENSAGEM_ALERTA_DUPLICIDADE         = "Já existe um registro com a mesma descrição.";


    /**
     * Método construtor.
     * @param $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {                

        $this->dao                          = (is_object($dao)) ? $this->dao = $dao : NULL;
        $this->view                         = new stdClass();
        $this->view->mensagemErro           = '';
        $this->view->mensagemAlerta         = '';
        $this->view->mensagemSucesso        = '';
        $this->view->dados                  = null;
        $this->view->parametros             = null;
        $this->view->status                 = false;
        $this->usuarioLogado                = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';        

        //Se nao tiver nada na sessao assume usuario AUTOMATICO (para CRON e WebService)
        $this->usuarioLogado                = (empty($this->usuarioLogado)) ? 2750 : intval($this->usuarioLogado);
    }

    /**
     * Reponsável também por realizar a pesquisa invocando o método privado
     * @return void
     */
    public function index( stdClass $dados = null ) {        

        /*try {

            $this->view->parametros = $this->tratarParametros();            

            //Inicializa os dados
            $this->inicializarParametros();

            $this->view->dados = $this->pesquisar($this->view->parametros);

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }*/
        
        header ( 'Location: custos_apagar.php' );   
        //Incluir a view padrão
        //require_once _MODULEDIR_ . "Manutencao/View/man_custos_apagar/index.php";
    }

    /**
     * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros
     *
     * @return stdClass Parametros tradados
     * @return stdClass
     */
    private function tratarParametros() {

	   $retorno = new stdClass();

       if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {

                //Verifica se atributo ja existe e nao sobrescreve.
                if (!isset($retorno->$key)) {
                     $retorno->$key = isset($_GET[$key]) ? trim($value) : '';
                }
            }
        }

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {

                if(is_array($value)) {

                    //Tratamento de POST com Arrays
                    foreach ($value as $chave => $valor) {
                        $value[$chave] = trim($valor);
                    }
                    $retorno->$key = isset($_POST[$key]) ? $_POST[$key] : array();

                } else {
                    $retorno->$key = isset($_POST[$key]) ? trim($value) : '';
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
     * Popula e trata os parametros bidirecionais entre view e action
     * @return void
     */
    private function inicializarParametros() {

        /*
        //Verifica se os parametro existem, senão iniciliza todos
        $this->view->parametros->tipvdescricao = isset($this->view->parametros->tipvdescricao) && !empty($this->view->parametros->tipvdescricao) ? trim($this->view->parametros->tipvdescricao) : "";
		$this->view->parametros->tipvcategoria = isset($this->view->parametros->tipvcategoria) && !empty($this->view->parametros->tipvcategoria) ? trim($this->view->parametros->tipvcategoria) : ""; 
        $this->view->parametros->tipvcarreta = isset($this->view->parametros->tipvcarreta) && !empty($this->view->parametros->tipvcarreta) ? trim($this->view->parametros->tipvcarreta) : "";         

        */
        $this->view->parametros->apgvl_apagar               = ((isset($this->view->parametros->apgvl_apagar)) ? trim($this->formataDinheiro($this->view->parametros->apgvl_apagar)) : "0,00");
        $this->view->parametros->apgvl_desconto             = ((isset($this->view->parametros->apgvl_desconto)) ? trim($this->formataDinheiro($this->view->parametros->apgvl_desconto)) : "0,00");
        $this->view->parametros->apgvl_juros                = ((isset($this->view->parametros->apgvl_juros)) ? trim($this->formataDinheiro($this->view->parametros->apgvl_juros)) : "0,00");
        $this->view->parametros->apgvl_multa                = ((isset($this->view->parametros->apgvl_multa)) ? trim($this->formataDinheiro($this->view->parametros->apgvl_multa)) : "0,00");
        $this->view->parametros->apgvl_tarifa_bancaria      = ((isset($this->view->parametros->apgvl_tarifa_bancaria)) ? trim($this->formataDinheiro($this->view->parametros->apgvl_tarifa_bancaria)) : "0,00");
        $this->view->parametros->apgvl_ir                   = ((isset($this->view->parametros->apgvl_ir)) ? trim($this->formataDinheiro($this->view->parametros->apgvl_ir)) : "0,00");
        $this->view->parametros->apgvl_pis                  = ((isset($this->view->parametros->apgvl_pis)) ? trim($this->formataDinheiro($this->view->parametros->apgvl_pis)) : "0,00");
        $this->view->parametros->apgvl_cofins               = ((isset($this->view->parametros->apgvl_cofins)) ? trim($this->formataDinheiro($this->view->parametros->apgvl_cofins)) : "0,00");
        $this->view->parametros->apgvl_csll                 = ((isset($this->view->parametros->apgvl_csll)) ? trim($this->formataDinheiro($this->view->parametros->apgvl_csll)) : "0,00");
        $this->view->parametros->apgvl_inss                 = ((isset($this->view->parametros->apgvl_inss)) ? trim($this->formataDinheiro($this->view->parametros->apgvl_inss)) : "0,00");
        $this->view->parametros->apgvl_iss                  = ((isset($this->view->parametros->apgvl_iss)) ? trim($this->formataDinheiro($this->view->parametros->apgvl_iss)) : "0,00");
        $this->view->parametros->apgcsrf                    = ((isset($this->view->parametros->apgcsrf)) ? trim($this->formataDinheiro($this->view->parametros->apgcsrf)) : "0,00");    


    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     * @param stdClass $filtros Filtros da pesquisa
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        /*$filtros = $this->tratarParametros();

        $resultadoPesquisa = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {

            $this->view->mensagemAlerta = self::MENSAGEM_NENHUM_REGISTRO;
        }

        $this->view->filtros = $filtros;
        $this->view->status = TRUE;

        return $resultadoPesquisa; 
        */
    }

    /**
     * Responsável por formatar a mascara do centro de custo     
     * @return void
     */

    public function formataCentroCusto($numero){
        if(strlen($numero) < 11){
            $buf = str_repeat("0", 11 - strlen($numero)) . $numero;
        }else{
            $buf = $numero;
        }
        $buf2 = substr($buf, 0, 1).".".substr($buf, 1, 1).".".substr($buf, 2, 2).".".substr($buf, 4, 2).".".substr($buf, 6, 2).".".substr($buf, 8, 3);

        if(strlen($numero) == 12) { 
            $buf2 .= "-" . substr($buf, 11, 1);
        }
        return $buf2;
    }

    /**
     * Responsável por formatar a mascara do plano contabil - exibido dentro da combo     
     * @return void
     */
    public function formataPlanoContabil($numero){
        if(strlen($numero) < 13){
            $buf = str_repeat("0", 13 - strlen($numero)) . $numero;
        }else{
            $buf = $numero;
        }
        $buf2 = substr($buf, 0, 1) . "." . substr($buf, 1, 1) . "." . substr($buf, 2, 2) . "." . substr($buf, 4, 2) . "." . substr($buf, 6, 3) . "." . substr($buf, 9, 4);
        if(strlen($numero) == 14){
            $buf2 .= "-" . substr($buf, 13, 1);
        }
        return $buf2;
    }


    /**
     * Responsável pela formatação da mascara de dinheiro
     * @return void
     */
    public function formataDinheiro($numero = null){

        $retorno = $numero; //number_format($numero, 2);
        return $retorno;
    }

    /**
     * Responsável por receber exibir o formulário de cadastro ou invocar
     * o metodo para salvar os dados
     * @param stdClass $parametros
     * @return void
     */
    public function cadastrar($parametros = null) {        

        //identifica se o registro foi gravado
        $registroGravado = FALSE;
        
        try{

            if (is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();                
            } else {

                $this->view->parametros = $parametros;
                $this->view->parametros->cntno_centro = $this->formataCentroCusto($parametros->cntno_centro);                
            }
            
            if($this->view->parametros->apgtecoid == ""){
                $this->view->parametros->apgtecoid  = isset($_POST['apgtecoid']) ? $_POST['apgtecoid'] : 0;
            }

            $this->view->planoContabil = $this->retornaContasContabeis('6',$this->view->parametros->apgtecoid);

            /* utilizado para alterar o formato de exibição do plcconta 1.1.01.01.001.0001 */
            foreach ($this->view->planoContabil as $keyPlanoContabil => $valuePlanoContabil) {
                $this->view->planoContabil[$keyPlanoContabil]->plcconta = $this->formataPlanoContabil($this->view->planoContabil[$keyPlanoContabil]->plcconta);    
            }

            //var_dump($parametros);

            if(substr($this->view->parametros->apglinha_digitavel, 0, 1) == 8){
                $this->view->parametros->apglinha_digitavel_conc1 = substr($this->view->parametros->apglinha_digitavel, 0, 12);
                $this->view->parametros->apglinha_digitavel_conc2 = substr($this->view->parametros->apglinha_digitavel, 12, 12);
                $this->view->parametros->apglinha_digitavel_conc3 = substr($this->view->parametros->apglinha_digitavel, 24, 12);
                $this->view->parametros->apglinha_digitavel_conc4 = substr($this->view->parametros->apglinha_digitavel, 36, 12);                
            }else{
                $this->view->parametros->apglinha_digitavel1 = substr($this->view->parametros->apglinha_digitavel, 0, 5);
                $this->view->parametros->apglinha_digitavel2 = substr($this->view->parametros->apglinha_digitavel, 5, 5);
                $this->view->parametros->apglinha_digitavel3 = substr($this->view->parametros->apglinha_digitavel, 10, 5);
                $this->view->parametros->apglinha_digitavel4 = substr($this->view->parametros->apglinha_digitavel, 15, 6);
                $this->view->parametros->apglinha_digitavel5 = substr($this->view->parametros->apglinha_digitavel, 21, 5);
                $this->view->parametros->apglinha_digitavel6 = substr($this->view->parametros->apglinha_digitavel, 26, 6);
                $this->view->parametros->apglinha_digitavel7 = substr($this->view->parametros->apglinha_digitavel, 32, 1);
                $this->view->parametros->apglinha_digitavel8 = substr($this->view->parametros->apglinha_digitavel, 33, 14);
            }
           
            //Incializa os parametros
            $this->inicializarParametros();            

            //Verificar se foi submetido o formulário e grava o registro em banco de dados
            if (isset($_POST) && !empty($_POST)) {

                $registroGravado = $this->salvar($this->view->parametros);                
            }             

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }

        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado){            
            //$this->index();
            //var_dump($this->view->parametros->apgoid);

            $this->view->parametros = $this->dao->pesquisarPorID($this->view->parametros->apgoid);

            // Todas as empresas
            $this->view->empresas = $this->retornaEmpresas();
            $this->view->tiposDocumentos = $this->retornaTiposDocumentos();
            $this->view->tiposContasPagar = $this->retornaTiposContasPagar();            
            $this->view->parametros->cntno_centro = $this->formataCentroCusto($this->view->parametros->cntno_centro);                                
            $this->view->planoContabil = $this->retornaContasContabeis('6', $this->view->parametros->apgtecoid);
                      
            if(substr($this->view->parametros->apglinha_digitavel, 0, 1) == 8){
                $this->view->parametros->apglinha_digitavel_conc1 = substr($this->view->parametros->apglinha_digitavel, 0, 12);
                $this->view->parametros->apglinha_digitavel_conc2 = substr($this->view->parametros->apglinha_digitavel, 12, 12);
                $this->view->parametros->apglinha_digitavel_conc3 = substr($this->view->parametros->apglinha_digitavel, 24, 12);
                $this->view->parametros->apglinha_digitavel_conc4 = substr($this->view->parametros->apglinha_digitavel, 36, 12);                
            }else{
                $this->view->parametros->apglinha_digitavel1 = substr($this->view->parametros->apglinha_digitavel, 0, 5);
                $this->view->parametros->apglinha_digitavel2 = substr($this->view->parametros->apglinha_digitavel, 5, 5);
                $this->view->parametros->apglinha_digitavel3 = substr($this->view->parametros->apglinha_digitavel, 10, 5);
                $this->view->parametros->apglinha_digitavel4 = substr($this->view->parametros->apglinha_digitavel, 15, 6);
                $this->view->parametros->apglinha_digitavel5 = substr($this->view->parametros->apglinha_digitavel, 21, 5);
                $this->view->parametros->apglinha_digitavel6 = substr($this->view->parametros->apglinha_digitavel, 26, 6);
                $this->view->parametros->apglinha_digitavel7 = substr($this->view->parametros->apglinha_digitavel, 32, 1);
                $this->view->parametros->apglinha_digitavel8 = substr($this->view->parametros->apglinha_digitavel, 33, 14);
            }

            /* utilizado para alterar o formato de exibição do plcconta 1.1.01.01.001.0001 */
            foreach ($this->view->planoContabil as $keyPlanoContabil => $valuePlanoContabil) {
                $this->view->planoContabil[$keyPlanoContabil]->plcconta = $this->formataPlanoContabil($this->view->planoContabil[$keyPlanoContabil]->plcconta);    
            }
            
            require_once _MODULEDIR_ . "Manutencao/View/man_custos_apagar/cadastrar.php";
        } else {                            
            require_once _MODULEDIR_ . "Manutencao/View/man_custos_apagar/cadastrar.php";
        }
    }

    /**
     * Responsável por receber exibir o formulário de edição ou invocar
     * o metodo para salvar os dados
     * @return void
     */
    public function editar() {        

        try {
            
            //Verifica se o id foi passado por parametro
                //Busca os parametros do POST/GET
                $parametros = $this->tratarParametros();                        
                $parametros->apgoid = $parametros->apgoid;


            // Todas as empresas
            $this->view->empresas = $this->retornaEmpresas();
            $this->view->tiposDocumentos = $this->retornaTiposDocumentos();
            $this->view->tiposContasPagar = $this->retornaTiposContasPagar();
            
            //Verifica se foi informado o id do cadastro
            if (isset($parametros->apgoid) && intval($parametros->apgoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->apgoid = (int) $parametros->apgoid;

                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->apgoid);

                //Chama o metodo para edição passando os dados do registro por parametro.              
                $this->cadastrar($dados);
            } else {                
                $this->index();
            }

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        }
    }

    /**
     * Grava os dados na base de dados.
     *
     * @param stdClass $dados Dados a serem gravados
     * @return void
     */
    private function salvar(stdClass $dados) {        


        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        if ((int)$dados->apgoid > 0) {
            
                //Efetua a gravação do registro
                $gravacao = $this->dao->atualizar($dados);                

                //Seta a mensagem de atualização
                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        }

        //Comita a transação
        $this->dao->commit();

        //unset($_GET);
        //unset($_POST);

        return $gravacao;
    }

    /**
     * Validar os campos obrigatórios do cadastro.
     *
     * @param stdClass $dados Dados a serem validados
     * @throws Exception
     * @return void
     */
    private function validarCamposCadastro(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();
        $camposDestaquesTamanho = array();
        
        //Verifica os campos obrigatórios
        if (!isset($dados->apgvl_apagar) || trim($dados->apgvl_apagar) == '') {
            $camposDestaques[] = array(
                'campo' => 'apgvl_apagar'
            );
        }
        if (!isset($dados->apgforma_recebimento) || trim($dados->apgforma_recebimento) == '') {
            $camposDestaques[] = array(
                'campo' => 'apgforma_recebimento'
            );
        }
        if (!isset($dados->apgtipo_docto) || trim($dados->apgtipo_docto) == '') {
            $camposDestaques[] = array(
                'campo' => 'apgtipo_docto'
            );
        }
        if (!isset($dados->apgdt_vencimento) || trim($dados->apgdt_vencimento) == '') {
            $camposDestaques[] = array(
                'campo' => 'apgdt_vencimento'
            );
        }
        if (!isset($dados->apgcodigo_barras) || trim($dados->apgcodigo_barras) == '') {
            $camposDestaques[] = array(
                'campo' => 'apgcodigo_barras'
            );
        }
        if (!isset($dados->apgdt_dtpagamento) || trim($dados->apgdt_dtpagamento) == '') {
            $camposDestaques[] = array(
                'campo' => 'apgdt_dtpagamento'
            );
        }                  

        if (!empty($camposDestaques)) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        //verifica tamanho dos campos
        if (!isset($dados->apgobs) || strlen(trim($dados->apgobs)) < 3) {
            $camposDestaquesTamanho[] = array(
                'campo' => 'apgobs'
            );
        }

        if (!empty($camposDestaquesTamanho)) {
            $this->view->dados = $camposDestaquesTamanho;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_TAMANHO);
        }
    }

    /**
     * Executa a exclusão de registro.
     * @return void
     */
    public function excluir() {
        
        $retorno = "OK";

        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($parametros->apgoid) || trim($parametros->apgoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->apgoid = (int) $parametros->apgoid;

            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros);

            if (!$confirmacao) {
                $retorno = "ERRO";
            }else{
                //Comita a transação
                $this->dao->commit();
            }

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $retorno = "ERRO";
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $retorno = "ERRO";
        }

        echo $retorno;        

        exit;
    }


    
    public function VerificaDigitosLinhaDigitavel( $linhaDig, $tipoOper ){
        
        if($tipoOper == 1){

            $digitoVerif1 = substr($linhaDig, 9, 1);
            $digitoVerif2 = substr($linhaDig, 20, 1);
            $digitoVerif3 = substr($linhaDig, 31, 1);

            $blocoDigitoVerif1 = substr($linhaDig, 0, 9);
            $blocoDigitoVerif2 = substr($linhaDig, 10, 10);
            $blocoDigitoVerif3 = substr($linhaDig, 21, 10);        

            $obj1   = $this->modulo_10( $blocoDigitoVerif1 );
            $obj2   = $this->modulo_10( $blocoDigitoVerif2 );
            $obj3   = $this->modulo_10( $blocoDigitoVerif3 );

            if ($digitoVerif1 == $obj1 && $digitoVerif2 == $obj2 && $digitoVerif3 == $obj3 ) {
                return "digitosVerificadoresOK";
            }else{
                $arr = array ( 'sucesso' => "digitosVerificadoresErrados", );
                return $arr;
            }
        }else if($tipoOper == 2){

            /* logica para o digitos verificadores concessionarias */
            $digitoVerif1 = substr($linhaDig, 11, 1);
            $digitoVerif2 = substr($linhaDig, 23, 1);
            $digitoVerif3 = substr($linhaDig, 35, 1);
            $digitoVerif4 = substr($linhaDig, 47, 1);
            
            $blocoDigitoVerif1 = substr($linhaDig, 0, 11);
            $blocoDigitoVerif2 = substr($linhaDig, 12, 11);
            $blocoDigitoVerif3 = substr($linhaDig, 24, 11);
            $blocoDigitoVerif4 = substr($linhaDig, 36, 11);            

            $obj1   = $this->modulo_10( $blocoDigitoVerif1 );
            $obj2   = $this->modulo_10( $blocoDigitoVerif2 );
            $obj3   = $this->modulo_10( $blocoDigitoVerif3 );
            $obj4   = $this->modulo_10( $blocoDigitoVerif4 );

            if ($digitoVerif1 == $obj1 && $digitoVerif2 == $obj2 && $digitoVerif3 == $obj3 && $digitoVerif4 == $obj4 ) {
                return "digitosVerificadoresOK";
            }else{
                $arr = array ( 'sucesso' => "digitosVerificadoresErrados", );
                return $arr;
            }
        }        
    }


    public function verificaCodBarrasToValorDataVencimento(){            
      
        if($_POST["apgcodigo_barras"] == ""){
            $retorno = "codigoBarrasVazio"; 
        }
        
        if($_POST["tipoOper"] == 1){ 

            if($_POST["apgcodigo_barras"] != "" ){                

                $arrDados           = $this->codigoBarrasTituloCobranca($_POST["apgcodigo_barras"]);
                $valor_titulo       = str_pad(str_replace(".","", str_replace(",", "", $_POST['apgvl_pago'])), 10, '0', STR_PAD_LEFT);
                $data_vencimento    = $this->gerarFatorVencimento($arrDados->fatorVencimento);                
                
                $str = "";
                $str =  $arrDados->codBanco.
                        $arrDados->codMoeda.
                        $arrDados->fatorVencimento.
                        $arrDados->valorTitulo.
                        $arrDados->campoLivre;

                $intDigVer = $this->modulo_11($str, $base = 9, $r = 0);

                if($intDigVer == $arrDados->dvGeral ){
                    $strCodigoBarras = $_POST["apgcodigo_barras"];
                    $retorno = "digitosVerificadoresOK";
                }else{
                    $strCodigoBarras = $_POST["apgcodigo_barras"];
                    $retorno = "digitosVerificadoresErrados";
                }                
                

            }elseif($_POST["linhaDigitavel"] != ""){
                $arrDados           = $this->linhaDigitavelTituloCobranca($_POST["linhaDigitavel"]);                 
                $valor_titulo       = str_pad(str_replace(".","", str_replace(",", "", $_POST['apgvl_pago'])), 10, '0', STR_PAD_LEFT);
                $data_vencimento    = $this->gerarFatorVencimento($arrDados->fatorVencimento);                

                $retorno = $this->VerificaDigitosLinhaDigitavel($_POST["linhaDigitavel"], $_POST["tipoOper"]);                                
                
                if($retorno == "digitosVerificadoresOK" ){

                    $strCodigoBarras =  $arrDados->codBanco .
                                        $arrDados->codMoeda .
                                        $arrDados->dvGeral .
                                        $arrDados->fatorVencimento .
                                        $arrDados->valorTitulo .
                                        $arrDados->campoLivre1 .
                                        $arrDados->campoLivre2 .
                                        $arrDados->campoLivre3;                    
                }
            }

        }elseif($_POST["tipoOper"] == 2){

            //codigoBarrasConcessionarias

            if($_POST["apgcodigo_barras"] != "" ){

                $arrDados           = $this->codigoBarrasConcessionarias($_POST["apgcodigo_barras"]);                
                $valor_titulo       = str_pad(str_replace(".","", str_replace(",", "", $_POST['apgvl_pago'])), 11, '0', STR_PAD_LEFT);
                $data_vencimento    = $this->gerarFatorVencimento($arrDados->fatorVencimento);
                $ignoraVerifDig     = 0;

                $arrDados->valorTitulo = $arrDados->valorDocumento;

                $str = "";
                $str =  $arrDados->identProduto .
                        $arrDados->identSegmento .
                        $arrDados->identValor .
                        $arrDados->valorDocumento .                        
                        $arrDados->empresaOrgao .
                        $arrDados->campoLivre;                        

                if($arrDados->identValor == 6 || $arrDados->identValor == 7){
                    $intDigVer = $this->modulo_10($str);

                }else if($arrDados->identValor == 8 || $arrDados->identValor == 9){
                    $intDigVer = $this->modulo_11($str, $base = 9, $r = 0);                    
                }

                if($arrDados->identSegmento <= 4 ){
                    if($intDigVer == $arrDados->dvGeral){
                        $strCodigoBarras = $_POST["apgcodigo_barras"];
                        $retorno = "digitosVerificadoresOK";

                    }else{
                        if( $arrDados->identProduto == 8 && $arrDados->identSegmento > 4 ){
                            $strCodigoBarras = $_POST["apgcodigo_barras"];
                            $retorno = "digitosVerificadoresOK";
                            $ignoraVerifDig = 1;

                        }else{
                            $strCodigoBarras = $_POST["apgcodigo_barras"];
                            $retorno = "digitosVerificadoresErrados";                        
                        }
                    }
                }else{
                    $strCodigoBarras = $_POST["apgcodigo_barras"];
                    $retorno = "digitosVerificadoresOK";
                    $ignoraVerifDig = 1;
                }                   

            }

            if($_POST["linhaDigitavel"] != ""){

                $arrDados           = $this->linhaDigitavelConcessionarias($_POST["linhaDigitavel"]);                 
                $valor_titulo       = str_pad(str_replace(".","", str_replace(",", "", $_POST['apgvl_pago'])), 11, '0', STR_PAD_LEFT);
                $data_vencimento    = $this->gerarFatorVencimento($arrDados->fatorVencimento);
                $arrDados->valorTitulo = $arrDados->valorDocumento1.$arrDados->valorDocumento2;                

                if($arrDados->identProduto == 8 && $arrDados->identSegmento > 4){
                    $retorno = "digitosVerificadoresOK";
                    $ignoraVerifDig = 1;
                    
                }else{
                    $retorno = $this->VerificaDigitosLinhaDigitavel($_POST["linhaDigitavel"], $_POST["tipoOper"]);                    
                }
                
                if($retorno == "digitosVerificadoresOK" ){
                    
                    $strCodigoBarras =  $arrDados->identProduto .
                                        $arrDados->identSegmento .
                                        $arrDados->identValor .
                                        $arrDados->dvGeral .
                                        $arrDados->valorDocumento1 .
                                        $arrDados->valorDocumento2 .
                                        $arrDados->empresaOrgao .
                                        $arrDados->campoLivre1 .
                                        $arrDados->campoLivre2 .
                                        $arrDados->campoLivre3;
                }
            }

        }

        if($retorno == "digitosVerificadoresOK"){

            if(($valor_titulo != $arrDados->valorTitulo) && ($data_vencimento != $_POST['apgdt_vencimento']) && $_POST["tipoOper"] == 1 && $arrDados->valorTitulo != "0000000000" && $arrDados->fatorVencimento != "0000" ) {
                $arr = array (  'sucesso'           => "nOK", 
                                'valor'             => number_format($valor_titulo / 100, 2, ",", "."), 
                                'valorCodBarras'    => number_format($arrDados->valorTitulo / 100, 2, ",", "."), 
                                'data_vencimento'   => $data_vencimento, 
                                'apgdt_vencimento'  => $_POST['apgdt_vencimento'],
                                'novocodigobarras'  => $strCodigoBarras
                            );   

            }elseif (($data_vencimento != $_POST['apgdt_vencimento']) && $_POST["tipoOper"] == 1 && $arrDados->valorTitulo != "0000000000" && $arrDados->fatorVencimento != "0000" ){               
                $arr = array (  'sucesso'           => "datas diferentes", 
                                'valor'             => number_format($valor_titulo / 100, 2, ",", "."), 
                                'valorCodBarras'    => number_format($arrDados->valorTitulo / 100, 2, ",", "."), 
                                'data_vencimento'   => $data_vencimento, 
                                'apgdt_vencimento'  => $_POST['apgdt_vencimento'],
                                'novocodigobarras'  => $strCodigoBarras
                            );

            }elseif($valor_titulo != $arrDados->valorTitulo && $arrDados->valorTitulo != "0000000000" && $arrDados->fatorVencimento != "0000" && $ignoraVerifDig == 0 ) {
                $arr = array (  'sucesso'           => "valores diferentes", 
                                'valor'             => number_format($valor_titulo / 100, 2, ",", "."), 
                                'valorCodBarras'    => number_format($arrDados->valorTitulo / 100, 2, ",", "."),
                                'data_vencimento'   => $data_vencimento, 
                                'apgdt_vencimento'  => $_POST['apgdt_vencimento'],
                                'novocodigobarras'  => $strCodigoBarras
                            );
                                        
            }else{
                $arr = array (  'sucesso'           => "OK", 
                                'valor'             => number_format($valor_titulo / 100, 2, ",", "."), 
                                'valorCodBarras'    => number_format($arrDados->valorTitulo / 100, 2, ",", "."), 
                                'data_vencimento'   => $data_vencimento, 
                                'apgdt_vencimento'  => $_POST['apgdt_vencimento'],
                                'novocodigobarras'  => $strCodigoBarras
                            );
            }

        }else{
            $arr = array (      'sucesso'           => "digitosVerificadoresErrados", 
                                'valor'             => number_format($valor_titulo / 100, 2, ",", "."), 
                                'valorCodBarras'    => number_format($arrDados->valorTitulo / 100, 2, ",", "."), 
                                'data_vencimento'   => $data_vencimento, 
                                'apgdt_vencimento'  => $_POST['apgdt_vencimento'],
                                'novocodigobarras'  => $strCodigoBarras
                            );
            
        }

        echo json_encode($arr);        
    }


    /**
     * Retorna todas empresas para o select de pesquisa
     * @return void
     */    
    private function retornaEmpresas(){           
        try {
            $retorno = $this->dao->retornaTodasEmpresas();
            return $retorno;            
        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();
        }        
    }

    /**
     * Retorna todos os tipos de documentos
     * @return void
     */      
    private function retornaTiposDocumentos() {
        try {
            $retorno = $this->dao->retornaTodosTiposDocumentos();
            return $retorno;
        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();
        }     
    }

    /**
     * Retorna todos os tipos de contas a pagar
     * @return void
     */     
    private function retornaTiposContasPagar() {
        try {
            $retorno = $this->dao->retornaTodosTiposContasPagar();
            return $retorno;
        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();
        }     
    }

    /**
     * Retorna todos os contas contabeis
     * @return void
     */     
    private function retornaContasContabeis($plctipo = null, $plctecoid = null) {    
        try {            
            $retorno = $this->dao->retornaTodasContasContabeis($plctipo, $plctecoid);                    
            return $retorno;
        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();
        }     
    }

    /**
     * Captura informações da Linha Digitavel Padrão Titulos de Cobranca     
     * @param  [type] $linhaDigitavel [description]     
     * @return [type]                 [description]     
     */    
    private function linhaDigitavelTituloCobranca($linhaDigitavel){
        
        //341|9|11012|1|3456788005|8|7123457000|1|6|1667|0000012345
        $dados = new stdClass;
        $dados->codBanco        = substr($linhaDigitavel, 0, 3);
        $dados->codMoeda        = substr($linhaDigitavel, 3, 1);
        $dados->campoLivre1     = substr($linhaDigitavel, 4, 5);
        $dados->dvCampo1        = substr($linhaDigitavel, 9, 1);
        $dados->campoLivre2     = substr($linhaDigitavel, 10, 10);
        $dados->dvCampo2        = substr($linhaDigitavel, 20, 1);
        $dados->campoLivre3     = substr($linhaDigitavel, 21, 10);
        $dados->dvCampo3        = substr($linhaDigitavel, 31, 1);
        $dados->dvGeral         = substr($linhaDigitavel, 32, 1);
        $dados->fatorVencimento = substr($linhaDigitavel, 33, 4);
        $dados->valorTitulo     = substr($linhaDigitavel, 37, 10);

        return $dados;
    }

    /**     
     * Captura informações da Linha Digitável Padrão Concessionárias     
     * @param  [type] $linhaDigitavel [description]     
     * @return [type]                 [description]     
     */

    private function linhaDigitavelConcessionarias($linhaDigitavel){        
        
        //8|4|6|1|0000000|5|3627|0006|000|1|20001020000|0|00457986595|9
        $dados = new stdClass;
        $dados->identProduto    = substr($linhaDigitavel, 0, 1);
        $dados->identSegmento   = substr($linhaDigitavel, 1, 1); //1 = Prefeituras (IPTU); 2 = Saneamento; 3 = Energia Elétrica e Gás; 4 = Telecomunicações;
        $dados->identValor      = substr($linhaDigitavel, 2, 1); //6 = Reais; 7 = Moeda Variável;
        $dados->dvGeral         = substr($linhaDigitavel, 3, 1);
        $dados->valorDocumento1 = substr($linhaDigitavel, 4, 7);
        $dados->dvCampo1        = substr($linhaDigitavel, 11, 1);
        $dados->valorDocumento2 = substr($linhaDigitavel, 12, 4);
        $dados->empresaOrgao    = substr($linhaDigitavel, 16, 4);
        $dados->campoLivre1     = substr($linhaDigitavel, 20, 3);
        $dados->dvCampo2        = substr($linhaDigitavel, 23, 1);
        $dados->campoLivre2     = substr($linhaDigitavel, 24, 11);
        $dados->dvCampo3        = substr($linhaDigitavel, 35, 1);
        $dados->campoLivre3     = substr($linhaDigitavel, 36, 11);
        $dados->dvCampo4        = substr($linhaDigitavel, 47, 1);

        return $dados;
    }

    /**
     * Captura informações do Codigo de Barras Padrão Concessionárias
     * @param  [type] $codigoBarras [description]
     * @return [type]               [description]
     */
    private function codigoBarrasConcessionarias($codigoBarras){

        //8|4|6|1|00000003627|0006|0002000102000000457986595

        $dados = new stdClass;
        $dados->identProduto    = substr($codigoBarras, 0, 1);
        $dados->identSegmento   = substr($codigoBarras, 1, 1); //1 = Prefeituras (IPTU); 2 = Saneamento; 3 = Energia Elétrica e Gás; 4 = Telecomunicações; 
        $dados->identValor      = substr($codigoBarras, 2, 1); //6 = Reais; 7 = Moeda Variável;
        $dados->dvGeral         = substr($codigoBarras, 3, 1); 
        $dados->valorDocumento  = substr($codigoBarras, 4, 11); 
        $dados->empresaOrgao    = substr($codigoBarras, 15, 4); 
        $dados->campoLivre      = substr($codigoBarras, 19); 

        return $dados;
    }

    /**
     * Captura informações do Codigo de Barras Padrão Titulos de Cobranca
     * @param  [type] $codigoBarras [description]
     * @return [type]               [description]
     */
    private function codigoBarrasTituloCobranca($codigoBarras){

        //341|9|6|1667|0000012345|1101234567880057123457000

        $dados = new stdClass;
        $dados->codBanco        = substr($codigoBarras, 0, 3);
        $dados->codMoeda        = substr($codigoBarras, 3, 1); 
        $dados->dvGeral         = substr($codigoBarras, 4, 1); 
        $dados->fatorVencimento = substr($codigoBarras, 5, 4); 
        $dados->valorTitulo     = substr($codigoBarras, 9, 10); 
        $dados->campoLivre      = substr($codigoBarras, 19); 

        return $dados;
    }

    /**
     * Gera a data de vencimento para verificar se a codigo de barras corresponde ao valor do campo data
     */
    private function gerarFatorVencimento($novoFator)   {
    
        $fator = 1000; //corresponde o dia 03/07/2000 (soma 1 ao fator por dia decorrido até a data de pagamento)
        $dataFator = "";
        $date = "2000-07-03";
        $novoFator = $novoFator - $fator;

        /** Para somar +x dias faça: */
        $dataFator = date("d/m/Y", strtotime("+$novoFator day", strtotime($date)));

        return $dataFator;
    }


    public function gerarCodigoDeBarras() {
                
        $stringCodigoBarras = "";
        $valor_titulo       = str_pad(str_replace(".","", str_replace(",", "", $_POST['apgvl_pago'])), 10, '0', STR_PAD_LEFT);
        $data_vencimento    = $this->gerarFatorVencimento($arrDados->fatorVencimento);        

        if($_POST['tipoOperacao'] == "2"){ // por exemplo concessionarias

            $string = "";
            $string .= $_POST['apglinha_digitavel_conc1'];
            $string .= $_POST['apglinha_digitavel_conc2'];
            $string .= $_POST['apglinha_digitavel_conc3'];
            $string .= $_POST['apglinha_digitavel_conc4'];

            if($string != ""){
                $obj = $this->linhaDigitavelConcessionarias ( $string );               
                $stringCodigoBarras = "";

                $stringCodigoBarras = $obj->identProduto;
                $stringCodigoBarras .= $obj->identSegmento;
                $stringCodigoBarras .= $obj->identValor;                
                $stringCodigoBarras .= $obj->dvGeral;                
                $stringCodigoBarras .= $obj->valorDocumento1;                
                $stringCodigoBarras .= $obj->valorDocumento2;
                $stringCodigoBarras .= $obj->empresaOrgao;
                $stringCodigoBarras .= $obj->campoLivre1; 
                $stringCodigoBarras .= $obj->campoLivre2; 
                $stringCodigoBarras .= $obj->campoLivre3;
                $sucesso = "OK";
            }
            
        }else if($_POST['tipoOperacao'] == "1"){ // por exemplo GNRE

            $string = "";
            $string .= $_POST['apglinha_digitavel1'];
            $string .= $_POST['apglinha_digitavel2'];
            $string .= $_POST['apglinha_digitavel3'];
            $string .= $_POST['apglinha_digitavel4'];
            $string .= $_POST['apglinha_digitavel5'];
            $string .= $_POST['apglinha_digitavel6'];
            $string .= $_POST['apglinha_digitavel7'];
            $string .= $_POST['apglinha_digitavel8'];
            
            if($string != ""){
                $obj = $this->linhaDigitavelTituloCobranca ( $string );
                
                $stringCodigoBarras = $obj->codBanco;
                $stringCodigoBarras .= $obj->codMoeda;
                $stringCodigoBarras .= $obj->campoLivre1;                
                $stringCodigoBarras .= $obj->campoLivre2;                
                $stringCodigoBarras .= $obj->campoLivre3;                
                $stringCodigoBarras .= $obj->dvGeral;
                $stringCodigoBarras .= $obj->fatorVencimento;
                $stringCodigoBarras .= $obj->valorTitulo;                
                $sucesso = "OK";
            }
        }

        $arr = array (  'sucesso'               => $sucesso, 
                        'stringCodigoBarras'    => $stringCodigoBarras,
                        'valor'                 => $valor_titulo, 
                        'valorCodBarras'        => number_format($arrDados->valorTitulo / 100, 2, ",", "."), 
                        'data_vencimento'       => $data_vencimento, 
                        'apgdt_vencimento'      => $_POST['apgdt_vencimento']
                    );
                
        echo json_encode($arr);
        
    }

    private function modulo_10($num) {

        $numtotal10 = 0;
        $fator = 2;

        // Separacao dos numeros
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo (falor 10)
            $temp = $numeros[$i] * $fator; 
            $temp0=0;
            foreach (preg_split('//', $temp, -1, PREG_SPLIT_NO_EMPTY) as $k => $v) {
                $temp0 += $v;
            }
            $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
            // monta sequencia para soma dos digitos no (modulo 10)
            $numtotal10 += $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            } else {
                $fator = 2; // intercala fator de multiplicacao (modulo 10)
            }
        }
        
        // várias linhas removidas, vide função original
        // Calculo do modulo 10
        $resto = $numtotal10 % 10;
        $digito = 10 - $resto;
    
        if ($resto == 0) {
            $digito = 0;
        }
        
        return $digito;
    
    }


    private function modulo_11($num, $base = 9, $r = 0) {

        $soma = 0;
        $fator = 2;
    
        /* Separacao dos numeros */
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num, $i - 1, 1);
            // Efetua multiplicacao do numero pelo falor
            $parcial[$i] = $numeros[$i] * $fator;
            // Soma dos digitos
            $soma += $parcial[$i];
            if ($fator == $base) {
                // restaura fator de multiplicacao para 2
                $fator = 1;
            }
            $fator++;
        }
    
        /* Calculo do modulo 11 */
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = 0;
            }
            $retorno = $digito;
        } elseif ($r == 1) {
            $resto = $soma % 11;
            $retorno = $resto;
        }

        if($retorno == 0 || $retorno == 1 || $retorno == 10 || $retorno == 11 ){
            $retorno = 1;
        }

        return $retorno;
    }


    public function buscaFornecedorGPS(){
        $resultado = array();

        $identificadorGPS = $_POST['identificadorGPS'];        
        $resultado = $this->dao->retornaNomeFornecedorGPS($identificadorGPS);

        //header('Content-Type: application/json');
        //echo json_encode($resultado);
    }


    /**
     * Buscar fornecedores (autocomplete)
     * @return json
     */
    public function buscarFornecedor() {        
        
        $resultado = array();
        $parametros = $this->tratarParametros();

        if (strlen($parametros->term) > 2) {
            $resultado = $this->dao->getNomeFornecedor(utf8_decode($parametros->term));
        }        

        header('Content-Type: application/json');
        echo json_encode($resultado);
    }


}


<?php

require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';

/**
 * Classe CadItensEssenciais.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   LUIZ FERNANDO PONTARA <fernandopontara@brq.com>
 *
 */
class CadItensEssenciais {

    /** Objeto DAO da classe */
    private $dao;

	/** propriedade para dados a serem utilizados na View. */
    private $view;

	/** Usuario logado */
	private $usuarioLogado;

    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Registro excluído com sucesso.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_ALERTA_REGISTRO_CADASTRADO = "Esse registro já está cadastrado.";
    const MENSAGEM_SUCESSO_IMPORTAR           = "Registro(s) importado(s) com sucesso.";
    const MENSAGEM_ALERTA_FORMATO_ARQUIVO     = "O arquivo deve ser no formato CSV.";
    const MENSAGEM_ALERTA_TAMANHO_ARQUIVO     = "O arquivo não deve ser maior que 1MB.";
    const MENSAGEM_ERRO_IMPORTAR              = "Erro na importação de dados.";

    /**
     * Método construtor.
     * @param $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {


        $this->dao                   = (is_object($dao)) ? $this->dao = $dao : NULL;
        $this->view                  = new stdClass();
        $this->view->mensagemErro    = '';
        $this->view->mensagemAlerta  = '';
        $this->view->mensagemSucesso = '';
        $this->view->dados           = null;
        $this->view->parametros      = null;
        $this->view->paginacao       = null;
        $this->view->status          = false;
        $this->usuarioLogado         = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO (para CRON e WebService)
        $this->usuarioLogado         = (empty($this->usuarioLogado)) ? 2750 : intval($this->usuarioLogado);
    }

    /**
     * Reponsável também por realizar a pesquisa invocando o método privado
     * @return void
     */
    public function index() {

        try {

            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {

                $this->view->dados = $this->pesquisar($this->view->parametros);
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        //Incluir a view padrão
        require_once _MODULEDIR_ . "Cadastro/View/cad_itens_essenciais/index.php";
    }

    /**
     * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros
     *k
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


        // para acao pesquisar
        $this->view->tipoOrdemServico       = $this->dao->getTipoOrdemServico();
        $this->view->getClasseEquipamento   = $this->dao->getClasseEquipamento();
        $this->view->getMateriais           = $this->dao->getMateriais();
        $this->view->getMarcaVeiculo        = $this->dao->getMarcaVeiculo();

        //Verifica se os parametro existem, senão iniciliza
        $this->view->parametros->iesoid = isset($this->view->parametros->iesoid) ? $this->view->parametros->iesoid : "" ;
        $this->view->parametros->iesostoid = isset($this->view->parametros->iesostoid) ? $this->view->parametros->iesostoid : "" ;
        $this->view->parametros->ieseqcoid = isset($this->view->parametros->ieseqcoid) ? $this->view->parametros->ieseqcoid : "" ;
        $this->view->parametros->ieseproid = isset($this->view->parametros->ieseproid) ? $this->view->parametros->ieseproid : "" ;
        $this->view->parametros->ieseveoid = isset($this->view->parametros->ieseveoid) ? $this->view->parametros->ieseveoid : "" ;
        $this->view->parametros->iespprdoid = isset($this->view->parametros->iespprdoid) ? $this->view->parametros->iespprdoid : "" ;
        $this->view->parametros->iesotioid = isset($this->view->parametros->iesotioid) ? $this->view->parametros->iesotioid : "" ;
        $this->view->parametros->iesmcaoid = isset($this->view->parametros->iesmcaoid) ? $this->view->parametros->iesmcaoid : "" ;
        $this->view->parametros->iesmlooid = isset($this->view->parametros->iesmlooid) ? $this->view->parametros->iesmlooid : "" ;

        //popula combo Equipamento
        if (is_numeric($this->view->parametros->ieseqcoid)) {
            $this->view->getEquipamento = $this->dao->getEquipamento($this->view->parametros->ieseqcoid);
        }

        //popula combo Versão
        if (is_numeric($this->view->parametros->ieseproid)) {
            $this->view->getVersao = $this->dao->getVersao($this->view->parametros->ieseproid);
        }

        //popula combo Motivo da Ordem de Serviço
        if (is_numeric($this->view->parametros->iesostoid)) {

            $this->view->getMotivoOrdemServico = $this->dao->getMotivoOrdemServico( $this->view->parametros->iesoid,$this->view->parametros->iesostoid,$this->view->parametros->iesotitipo);
        }

        //popula combo Modelo do Veículo
        if (is_numeric($this->view->parametros->iesmcaoid)) {
            $this->view->getModeloVeiculo = $this->dao->getModeloVeiculo($this->view->parametros->iesmcaoid);
        }

    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     * @param stdClass $filtros Filtros da pesquisa
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        try {

            $paginacao = new PaginacaoComponente();

            $this->inicializarParametros();

            $this->view->parametros = $this->tratarParametros();

            $totalRegistros = $this->dao->pesquisar($filtros);

            $this->view->totalResultados = count($totalRegistros);

            // Valida se houve resultado na pesquisa
            if ($this->view->totalResultados == 0) {
                throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
            }

            // Desabilita combo de classificacao
            $paginacao->desabilitarComboClassificacao();
            $this->view->paginacao = $paginacao->gerarPaginacao($this->view->totalResultados);

            $resultadoPesquisa = $this->dao->pesquisar($filtros, $paginacao->buscarPaginacao());

            //Validar os campos
            $this->validarCampos($this->view->parametros);

            $this->view->status = TRUE;

            return $resultadoPesquisa;

        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
        }
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
            }

            //Incializa os parametros
            $this->inicializarParametros();

            //faz o tratamento dos parametros para receber os itens e quantidade
            $itens = array();
            $aux = 0;
            foreach ($this->view->parametros as $key => $value) {

                $item_id = explode("_", $key);

                if($item_id[0] == "item"){

                    if($value <= 0){
                        $value = 1;
                    }

                    $itens[$item_id[1]] = $value;

                    unset($this->view->parametros->$key);
                }

                $aux ++;
            }

            $this->view->parametros->iespprdoid = $itens;

            //verifica acao
            if($this->view->parametros->acao == "erro_importar"){
                //não entra no if para salvar
                unset($_POST);

                $this->view->arquivo = "/var/www/docs_temporario/erros_importacao_itens_essenciais.txt";
            }

            //Verificar se foi submetido o formulário e grava o registro em banco de dados
            if (isset($_POST) && !empty($_POST)) {

                //Validar os campos
                $this->validarCampos($this->view->parametros);

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
            unset($_POST);
            $this->index();
        } else {

            if(isset($this->view->parametros->iesoid) && intval($this->view->parametros->iesoid) > 0) {
                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($this->view->parametros->iesoid);
                $this->view->parametros->iespiesoid = $dados->iespiesoid;
                $this->view->parametros->materiaisCadastrados = $dados->materiaisCadastrados;
            }

            require_once _MODULEDIR_ . "Cadastro/View/cad_itens_essenciais/cadastrar.php";
        }
    }

    /**
     * Responsável por receber exibir o formulário de edição ou invocar
     * o metodo para salvar os dados
     * @return void
     */
    public function editar() {

        try {
            //Parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id do cadastro
            if (isset($parametros->iesoid) && intval($parametros->iesoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->iesoid = (int) $parametros->iesoid;

                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->iesoid);

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
    private function salvar(stdClass $dados, $import_arquivo = false) {

        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        if ($dados->iesoid > 0) {
            //Efetua a gravação do registro
            $gravacao = $this->dao->atualizar($dados);

            //Seta a mensagem de atualização
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        } else {

            //verifica se registro já está cadastrado
            if (!$import_arquivo){
                $idExistente = $this->dao->pesquisarExistente($this->view->parametros);
                if( isset($idExistente->iesoid) ){
                    throw new Exception(self::MENSAGEM_ALERTA_REGISTRO_CADASTRADO);
                }
            }

            //Efetua a inserção do registro
            $gravacao = $this->dao->inserir($dados);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        }

        //Comita a transação
        $this->dao->commit();

        return $gravacao;
    }

    /**
     * Validar os campos obrigatórios do cadastro.
     *
     * @param stdClass $dados Dados a serem validados
     * @throws Exception
     * @return void
     */
    private function validarCampos(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        /**
         * Verifica os campos obrigatórios
         */
        if (!isset($dados->iesotitipo) || trim($dados->iesotitipo) == '') {
            $camposDestaques[] = array(
                'campo' => 'iesotitipo'
            );
        }

        if (!isset($dados->iesostoid) || trim($dados->iesostoid) == '') {
            $camposDestaques[] = array(
                'campo' => 'iesostoid'
            );
        }

        if (!isset($dados->iesotioid) || trim($dados->iesotioid) == '') {
            $camposDestaques[] = array(
                'campo' => 'iesotioid'
            );
        }

        if (!empty($camposDestaques)) {
            $this->view->validacao = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
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
            if (!isset($parametros->iesoid) || trim($parametros->iesoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->iesoid = (int) $parametros->iesoid;

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

    /**
     * Importa arquivo CSV
     */
    public function importar() {

        try {

            $this->view->parametros = $this->tratarParametros();

            //valida se o formato do arquivo e CSV
            if(!$this->validaCsv($this->view->parametros->arquivo["type"])){
                throw new Exception(self::MENSAGEM_ALERTA_FORMATO_ARQUIVO);
            }

            //valida o arquivo foi selecionado
            if ($this->view->parametros->arquivo["error"] == 4) {
                throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
            }

            //valida tamanho do arquivo
            if($this->view->parametros->arquivo["error"] == 2) {
                throw new Exception(self::MENSAGEM_ALERTA_TAMANHO_ARQUIVO);
            }

            $uploaddir = '/var/www/docs_temporario/';
            $uploadfile = $uploaddir . basename($this->view->parametros->arquivo["name"]);
            if(!move_uploaded_file($this->view->parametros->arquivo["tmp_name"], $uploadfile)) {
                throw new ErrorException(self::MENSAGEM_ERRO_IMPORTAR);
            }

            // manipula arquivo
            $handle = fopen ($uploadfile,"r");
            $conteudo = array();
            $linha = 1;
            while (($data = fgetcsv($handle, filesize($uploadfile), "\n")) !== FALSE) {

                //gera array separado por linhas e colunas
                $conteudo[$linha] = explode(";", $data[0]);

                $linha++;
            }

            //faz o tratamento das informações do CSV

            $this->dadosImportacao($conteudo);

            //fecha CSV
            fclose ($handle);

            //remove arquivo do servidor
            unlink($uploadfile);

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
            $this->index();
        }
    }

    /**
     * Manipula informacoes vindas do arquivo CSV e grava no banco de dados
     * Descrição das Colunas:
     * [n][0] = Tipo de ordem de serviço
     * [n][1] = Motivo da ordem de serviço
     * [n][2] = Classe do equipamento
     * [n][3] = Equipamento
     * [n][4] = Versão do equipamento
     * [n][5] = Modelo do veículo
     * [n][6] = Marca do veículo
     * [n][7] = Exclusão
     * [n][8] = Quantidade
     * [n][col >= 9] = Materiais / Acessórios
     * @param array [$conteudo] [Conteudo do CSV já formatado em array por linhas e colunas]
     * @return
     */
    private function dadosImportacao($conteudo){

        try{

            $this->dao->begin();

            //remove descrição das colunas (1° linha)
            unset($conteudo[1]);

            //insere dados da importacao na tabela temporária
            if(!$this->dao->insereImportacao($conteudo)){
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //busca IDS dos registros importados
            if(!$conteudoIds = $this->dao->getIdsImportacao()){
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //faz a validação dos dados importados
            if(!$erroLog = $this->validaConteudo($conteudoIds)){
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //se existir erros
            if(is_array($erroLog)){

                //percorre array de erros e gera string para salvar no arquivo
                foreach ($erroLog as $value) {
                    $textoLog .= $value . PHP_EOL;
                }

                //gera arquivo de retorno - Log de erros
                $this->arquivoRetorno($textoLog);

                //encerra importacao
                throw new ErrorException(self::MENSAGEM_ERRO_IMPORTAR);
            }

            $this->realizarImportacao($conteudoIds);

            $this->dao->commit();

            unset($_POST);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_IMPORTAR;
            $this->index();

        } catch (Exception $e) {
            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();

            $parametros = (object) array( "acao" => "erro_importar");
            $this->cadastrar($parametros);
        }
    }

    /**
     * Realiza a importação dos dados
     * @return void
     */
    public function realizarImportacao($conteudoIds){

        try{
            //percorre registros
            foreach ($conteudoIds as $valor) {

                //array para inserir no banco
                $dados = new stdClass();

                $dados->iesostoid = ( (trim($valor['ostoid']) == '#' ) ? '' : $valor['ostoid'] );
                $dados->ieseqcoid = ( (trim($valor['eqcoid']) == '#' ) ? '' : $valor['eqcoid'] );
                $dados->ieseproid = ( (trim($valor['eproid']) == '#' ) ? '' : $valor['eproid'] );
                $dados->ieseveoid = ( (trim($valor['eveoid']) == '#' ) ? '' : $valor['eveoid'] );
                $dados->iesotioid = ( (trim($valor['otioid']) == '#' ) ? '' : $valor['otioid'] );
                $dados->iesmcaoid = ( (trim($valor['mcaoid']) == '#' ) ? '' : $valor['mcaoid'] );
                $dados->iesmlooid = ( (trim($valor['mlooid']) == '#' ) ? '' : $valor['mlooid'] );

                //itens
                $dados->iespprdoid = array();

                //somente dados dos materiais / acessorios
                $materiais = array_slice($valor, 12);
                foreach ($materiais as $item) {
                    if($item != "#"){
                        $dados->iespprdoid[$item] = $valor['quantidade'];
                    }
                }

                //verifica se registro já existe da base de dados
                $idExistente = $this->dao->pesquisarExistente($dados);
                if( isset($idExistente->iesoid) ){
                    $dados->iesoid = $idExistente->iesoid;
                }

                //caso seja exclusao
                if(strtoupper($valor['exclusao']) === "S"){
                    //verifica se encontrou registro no banco
                    if( isset($dados->iesoid) ){
                        //exclui registro
                        if(!$this->dao->excluir($dados)){
                            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
                        }

                    }
                }else{

                    if(!$this->salvar($dados, true)){
                        throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
                    }
                }

                unset($dados);
            }

        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();
        }

    }


    /**
     * Valida todo o conteúdo da importação
     * @param  [array] $conteudoIds
     * @return [array] [Conteudo validado]
     */
    public function validaConteudo($conteudoIds){

        //cria array de erros
        $logErros = array();

        //faz a validação de cada linha
        foreach ($conteudoIds as $valor) {

            /* Validações por campo/Coluna */

            //Item de ordem de serviço
            if(trim(strtoupper ($valor['otitipo'])) != trim("A") && trim(strtoupper($valor['otitipo'])) != trim("E") ){
                $logErros[] = $this->descricaoErros(1, $valor['num_linha'], "Item ordem de serviço");
            }elseif (trim($valor['otitipo']) == "#") {
                $logErros[] = $this->descricaoErros(3, $valor['num_linha'], "Item ordem de serviço");
            }
            //Tipo de ordem de serviço
            if(trim($valor['ostoid']) == "#"){
                $logErros[] = $this->descricaoErros(3, $valor['num_linha'], "Tipo de ordem de serviço");
            }elseif (trim($valor['ostoid']) == "") {
                $logErros[] = $this->descricaoErros(1, $valor['num_linha'], "Tipo de ordem de serviço");
            }

            //Motivo da ordem de serviço
            if (trim($valor['otioid']) == "#") {
                $logErros[] = $this->descricaoErros(3, $valor['num_linha'], "Motivo da ordem de serviço");
            }elseif (trim($valor['otioid']) == "") {
                $logErros[] = $this->descricaoErros(1, $valor['num_linha'], "Motivo da ordem de serviço");
            }

            //Classe do equipamento
            if (trim($valor['eqcoid']) == "") {
                $logErros[] = $this->descricaoErros(1, $valor['num_linha'], "Classe do equipamento");
            }

            //Equipamento
            if (trim($valor['eproid']) == "") {
                $logErros[] = $this->descricaoErros(1, $valor['num_linha'], "Equipamento");
            }

            //Versão do equipamento
            if (trim($valor['eveoid']) == "") {
                $logErros[] = $this->descricaoErros(1, $valor['num_linha'], "Versão do equipamento");
            }

            //Modelo do veículo
            if (trim($valor['mlooid']) == "") {
                $logErros[] = $this->descricaoErros(1, $valor['num_linha'], "Modelo do veículo");
            //Se o modelo do veículo é selecionado, o campo marca do veiculo torna-se obrigatório
            }elseif (trim($valor['mlooid']) != "#" && trim($valor['mcaoid']) == "#") {
                $logErros[] = $this->descricaoErros(3, $valor['num_linha'], "Marca do veículo");
            }

            //Marca do veículo
            if (trim($valor['mcaoid']) == "") {
                $logErros[] = $this->descricaoErros(1, $valor['num_linha'], "Marca do veículo");
            }

            //Quantidade
            if(!is_numeric($valor['quantidade']) || $valor['quantidade'] > 99){
                $logErros[] = $this->descricaoErros(4, $valor['num_linha'], "Quantidade");
            }

            //Materiais / Acessórios
            $materiais = array_slice($valor, 12);
            $possuiMaterial = false;
            foreach ($materiais as $chave => $item) {
                //verifica se encontrou descricao
                if (trim($item) == "") {
                    $logErros[] = $this->descricaoErros(1, $valor['num_linha'], "Materiais / Acessórios $chave");
                }

                //existe material / acessório informado
                if (is_numeric($item)) {
                    $possuiMaterial = true;
                }

            }

            //valida se há material / acessório idêntico na mesma linha
            $arrMateriaisDuplicados = array_unique(array_diff_assoc($materiais, array_unique($materiais)));
            $materiaisDuplicados = FALSE;
            foreach ($arrMateriaisDuplicados as $chave => $item) {
                if(is_numeric($item)){
                    $materiaisDuplicados = TRUE;
                }
            }
            if($materiaisDuplicados){
                $logErros[] = $this->descricaoErros(4, $valor['num_linha'], "Materiais / Acessórios");
            }

            //caso possua material, validar obrigatoriedade da coluna quantidade
            if($possuiMaterial && trim($valor['quantidade']) == ""){
                $logErros[] = $this->descricaoErros(3, $valor['num_linha'], "Quantidade");
            }
            //caso possua quantidade, validar obrigatoriedade da coluna Materiais / Acessórios
            if((int)$valor['quantidade'] > 0 && $possuiMaterial == false){
                $logErros[] = $this->descricaoErros(3, $valor['num_linha'], "Materiais / Acessórios");
            }

            //Quantidade não pode ser zero caso possua Material / Acessório
            if((int)$valor['quantidade'] == 0 && $possuiMaterial == true){
                $logErros[] = $this->descricaoErros(4, $valor['num_linha'], "Quantidade");
            }


            /* valida relacionamento dos dados */

            $relacErros = $this->dao->validaRelacionamento($valor);
            //relacionamento Tipo Ordem Servico / Motivo de Ordem Serviço
            if($relacErros[1] == 1){
                $logErros[] = $this->descricaoErros(2, $valor['num_linha'], "Tipo de ordem de serviço", "Motivo da ordem de serviço");
            }
            //relacionamento Versão do Equipamento / Equipamento
            if($relacErros[2] == 1){
                $logErros[] = $this->descricaoErros(2, $valor['num_linha'], "Equipamento", "Versão do equipamento");
            }
            //relacionamento Marca Veículo / Modelo Veículo
            if($relacErros[3] == 1){
                $logErros[] = $this->descricaoErros(2, $valor['num_linha'], "Marca do veículo", "Modelo do veículo");
            }


            /* Outras Validações */

            //valida se exite linhas duplicados do arquivo
            if($valor['duplicados'] > 1){
                $logErros[] = $this->descricaoErros(6, $valor['num_linha']);
            }

        }

        if(count($logErros) > 0){
            return $logErros;
        }else{
            return true;
        }

    }


    /**
     * Retorna erros da importação
     * @param  [int]    $codigo [codigo do erro gerado]
     * @param  [int]    $linha  [linha do erro]
     * @param  [string] $coluna [descrição da coluna]
     * @return [string]         [Descrição do erro]
     */
    private function descricaoErros($codigo, $linha, $coluna1 = "", $coluna2 = ""){

        switch ($codigo) {
            case '1':
                    $mensagem = "linha" . $linha . ": coluna " . $coluna1 . ": Descrição não localizada no sistema";
                break;
            case '2':
                    $mensagem = "linha" . $linha . ": coluna " . $coluna1 . ": Não possui relacionamento com " . $coluna2;
                break;
            case '3':
                    $mensagem = "linha" . $linha . ": coluna " . $coluna1 . ": Preencha o campo";
                break;
            case '4':
                    $mensagem = "linha" . $linha . ": coluna " . $coluna1 . ": Valor inválido";
                break;
            case '5':
                    $mensagem = "linha" . $linha . ": Valores duplicados";
                break;
            case '6':
                    $mensagem = "linha" . $linha . ": Linha duplicada";
                break;
            default:
                    $mensagem = "linha" . $linha . ": Descrição de erro não encontrado";
                break;
        }

        return $mensagem;
    }


    /**
     * Gera arquivo de retorno da importação
     *
     * @param string [informações a serem gravadas no arquivo]
     * @return void
     */
    private function arquivoRetorno($texto){

        //diretorio do arquivo a ser gravado
        $insertdir = '/var/www/docs_temporario/';

        //gera e escreve o arquivo
        $fp = fopen($insertdir."erros_importacao_itens_essenciais.txt", "w+");

        $escreve = fwrite($fp, $texto);

        // Fecha o arquivo
        fclose($fp);
    }

    /**
     * Busca Motivo Ordem Serviço
     * @return JSON
     */
    public function getMotivoOrdemServico(){

        $this->view->parametros = $this->tratarParametros();

        $retorno = array();

        if(isset($this->view->parametros->iesostoid) && isset($this->view->parametros->iesotitipo)){
            $retorno = $this->dao->getMotivoOrdemServico(0,$this->view->parametros->iesostoid,$this->view->parametros->iesotitipo);
        }

        echo json_encode($retorno);
        exit;
    }

    /**
     * Busca Equipamento
     * @return JSON
     */
    public function getEquipamento(){

        $this->view->parametros = $this->tratarParametros();

        $retorno = array();

        if(isset($this->view->parametros->ieseqcoid)){
            $retorno = $this->dao->getEquipamento($this->view->parametros->ieseqcoid);
        }

        echo json_encode($retorno);
        exit;
    }

    /**
     * Busca Versão
     * @return JSON
     */
    public function getVersao(){

        $this->view->parametros = $this->tratarParametros();

        $retorno = array();

        if(isset($this->view->parametros->ieseproid)){
            $retorno = $this->dao->getVersao($this->view->parametros->ieseproid);
        }

        echo json_encode($retorno);
        exit;
    }

    /**
     * Busca Modelo do Veiculo
     * @return JSON
     */
    public function getModeloVeiculo(){

        $this->view->parametros = $this->tratarParametros();

        $retorno = array();

        if(isset($this->view->parametros->iesmcaoid)){
            $retorno = $this->dao->getModeloVeiculo($this->view->parametros->iesmcaoid);
        }

        echo json_encode($retorno);
        exit;
    }

    /**
     * Valida se arquivo é CSV
     * @return bool
     */
    private function validaCsv($formato){

        //verificando tipo do arquivo
        $csv_mimetypes = array(
            'text/csv',
            'text/plain',
            'application/csv',
            'text/comma-separated-values',
            'application/excel',
            'application/vnd.ms-excel',
            'application/vnd.msexcel',
            'text/anytext',
            'application/octet-stream',
            'application/txt'
        );

        if(in_array($formato, $csv_mimetypes)) {
            return true;
        }else{
            return false;
        }
    }


}
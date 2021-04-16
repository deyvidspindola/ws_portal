<?php

header ('Content-type: text/html; charset=ISO-8859-1');
require_once _SITEDIR_ . "lib/Components/CsvWriter.php";

/**
 * Classe prnBackoffice.
 * Camada de regra de negócio.
 *
 * @package  Principal
 * @author   Vanessa Rabelo <vanessa.rabelo@meta.com.br>
 *
 */
class prnBackoffice {

    /**
     * Objeto DAO da classe.
     *
     * @var prnBackofficeDAO
     */
    private $dao;

    /**
     * Mensagem de alerta para campos obrigatórios não preenchidos
     * @const String
     */

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";

    /**
     * Mensagem de sucesso para inserção do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_INCLUIR = "Registro incluído com sucesso.";

    /**
     * Mensagem de sucesso para alteração do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_ATUALIZAR = "Registro alterado com sucesso.";

    /**
     * Mensagem de sucesso para exclusão do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_EXCLUIR = "Registro excluído com sucesso.";

    /**
     * Mensagem para nenhum registro encontrado
     * @const String
     */
    const MENSAGEM_NENHUM_REGISTRO = "Nenhum registro encontrado.";

    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    const MENSAGEM_SUCESSO_ARQUIVO = "Arquivo gerado com sucesso.";
    /**
     * Tipo de relatório Sintetico
     * @const String
     */
    const TIPO_RELATORIO_SINTETICO = "S";

    /**
     * Tipo de relatório Analitico
     * @const String
     */
    const TIPO_RELATORIO_ANALITICO = "A";

    /**
     * Contém dados a serem utilizados na View.
     *
     * @var stdClass
     */
    private $view;
    private $portalCliente = FALSE;

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

        //Status de uma transação
        $this->view->status = false;


    }

    /**
     * Método padrão da classe.
     *
     * Reponsável também por realizar a pesquisa invocando o método privado
     *
     * @return void
     */
    public function index() {
        try {
            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            $this->cd_usuario = $_SESSION['usuario']['oid'];

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && ( $this->view->parametros->acao == 'pesquisar' || $this->view->parametros->acao == 'gerar_csv') ) {

                $this->view->dados = $this->pesquisar($this->view->parametros);

                if ($this->view->parametros->acao == 'gerar_csv'){

                    if ($this->view->parametros->selecao_por == self::TIPO_RELATORIO_ANALITICO){
                        if ($this->gerarCSVAnalitico()){
                            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ARQUIVO;
                        }
                        unset($this->view->dados);
                    }
                    elseif ($this->view->parametros->selecao_por == self::TIPO_RELATORIO_SINTETICO){
                        if ($this->gerarCSVSintetico()){
                            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ARQUIVO;
                        }
                        unset($this->view->dados);
                    }
                }
            }


            //Inicializa os dados
            $this->inicializarParametros();

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->parametros->buscarTipoContrato = $this->dao->buscarTipoContrato();
            $this->view->parametros->buscarUF 			= $this->dao->buscarUF();

            $this->view->mensagemAlerta = $e->getMessage();

        }

        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Principal/View/prn_backoffice/index.php";
    }



    private function gerarCSVAnalitico(){

        //Diretório do Arquivo
        $caminho = '/var/www/docs_temporario/';
        //Nome do arquivo
        $nome_arquivo = 'backofficeanalitico_'.date("Ymd").'.csv';


        if (count($this->view->dados) == 0){
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        if ( file_exists($caminho) ){

            $csvWriter = new CsvWriter( $caminho.$nome_arquivo, ';', '', true);

            $csvWriter->addLine( "Resultado da Pesquisa");


            $cabecalho = array(
                'Nº',
                'Data/Hora',
                'Cliente',
                'Placa',
                'Tipo Contrato',
                'Motivo',
                'Status',
                'Tempo Concl.',
                'UF',
                'Cidade',
                'Atendente'
            );

            $csvWriter->addLine($cabecalho);

            foreach($this->view->dados as $linha){
                $linhaCSV = array(

                    $linha->bacoid,
                    $linha->bacdt_solicitacao,
                    $linha->clinome,
                    $linha->bacplaca,
                    $linha->tpcdescricao,
                    $linha->bmsdescricao,
                    $linha->status,
                    $linha->data,
                    $linha->clcuf_sg,
                    $linha->clcnome,
                    $linha->nm_usuario
                );
                
                $csvWriter->addLine($linhaCSV);
            }

            $this->view->arquivo = $nome_arquivo;
        } else {
            throw new Exception('Erro ao gerar o arquivo.');
        }

        return true;

    }


    private function gerarCSVSintetico(){

        $arrTempo = array(
                          '9_maior1mes'=>'Maior que 1 mês',
                          '8_menor1mes'=>'Até 1 Mês',
                          '7_menor5dias'=>'Até 5 dias',
                          '6_menor3dias'=>'Até 3 dias',
                          '5_menor2dias'=>'Até 2 dias',
                          '4_menor1dia'=>'Até 1 dia',
                          '3_menor12horas'=>'Até 12 horas',
                          '2_menor6horas'=>'Até 6 horas',
                          '1_menor2horas'=>'Até 2 horas'
                      );
        ksort($arrTempo);

        //Diretório do Arquivo
        $caminho = '/var/www/docs_temporario/';
        //Nome do arquivo
        $nome_arquivo = 'backofficesintetico_'.date("Ymd").'.csv';

        if (count($this->view->dados) == 0){
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        if ( file_exists($caminho) ){

            $csvWriter = new CsvWriter( $caminho.$nome_arquivo, ';', '', true);

            $csvWriter->addLine( "Resultado da Pesquisa");

            // RESULTADO POR MOTIVO
            $cabecalho = array(
                'Motivo',
                'Quantidade'
            );

            $csvWriter->addLine($cabecalho);
            if (count($this->view->dados['motivo']) > 0){
                $total=0;
                foreach($this->view->dados['motivo'] as $linha){
                    $total+=$linha->solicitacoes;
                    $linhaCSV = array(
                        $linha->motivo,
                        $linha->solicitacoes
                    );
                    $csvWriter->addLine($linhaCSV);
                }
                $csvWriter->addLine( array('Total', $total) );
            }
            $csvWriter->addLine(array(''));

            // RESULTADO POR TEMPO
            $cabecalho = array(
                    'Tempo',
                    'Qtde. em Andamento',
                    'Qtde. Concluído',
                    'Qtde. Pendente',
                    'Subtotal'
                );

            $csvWriter->addLine($cabecalho);
            if (count($this->view->dados['tempo']) > 0){
                $total=0;
                $total_concluido=0;
                $total_andamento=0;
                $total_pendente=0;

                foreach ($arrTempo as $chave => $tempo) {
                    $linha = (isset($this->view->dados['tempo'][$chave]) ? $this->view->dados['tempo'][$chave] : array());
                    $Subtotal_linha = intval($linha['P'])+intval($linha['A'])+intval($linha['C']);
                    $total_concluido+=intval($linha['C']);
                    $total_andamento+=intval($linha['A']);
                    $total_pendente+=intval($linha['P']);
                    $total+=$Subtotal_linha;

                    $linhaCSV = array(
                        $tempo,
                        (isset($linha['A']) ? $linha['A']  : 0 ),
                        (isset($linha['C']) ? $linha['C']  : 0 ),
                        (isset($linha['P']) ? $linha['P']  : 0 ),
                        ($Subtotal_linha ? $Subtotal_linha  : 0 )
                    );

                    $csvWriter->addLine($linhaCSV);
                }
                $csvWriter->addLine( array('Total', $total_andamento, $total_concluido, $total_pendente, $total) );
            }
            $csvWriter->addLine(array(''));

            // RESULTADO POR ATENDENTE
            $cabecalho = array(
                    'Atendente',
                    'Qtde. em Andamento',
                    'Qtde. Concluído',
                    'Qtde. Pendente',
                    'Subtotal'
                );

            $csvWriter->addLine($cabecalho);
            if (count($this->view->dados['atendente']) > 0){
                $total=0;
                $total_concluido=0;
                $total_andamento=0;
                $total_pendente=0;
                foreach($this->view->dados['atendente'] as $linha){
                    $Subtotal_linha = $linha->concluido+$linha->andamento+$linha->pendente;
                    $total_concluido+=$linha->concluido;
                    $total_andamento+=$linha->andamento;
                    $total_pendente+=$linha->pendente;
                    $total+=$Subtotal_linha;

                    $linhaCSV = array(
                        $linha->nm_usuario,
                        $linha->andamento,
                        $linha->concluido,
                        $linha->pendente,
                        $Subtotal_linha
                    );
                    $csvWriter->addLine($linhaCSV);
                }
                $csvWriter->addLine( array('Total', $total_andamento, $total_concluido, $total_pendente, $total) );
            }
            $csvWriter->addLine(array(''));

            $this->view->arquivo = $nome_arquivo;
        } else {
            throw new Exception('Erro ao gerar o arquivo.');
        }

        return true;

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
        return $retorno;
    }

    /**
     * Popula os arrays para os combos de estados e cidades
     *
     * @return void
     */
    private function inicializarParametros() {

        //Verifica se os parametro existem, senão iniciliza todos
    $this->view->parametros->bacoid = isset($this->view->parametros->bacoid) ? $this->view->parametros->bacoid : "" ;     $this->view->parametros->bacclioid = isset($this->view->parametros->bacclioid) ? $this->view->parametros->bacclioid : "" ;    $this->view->parametros->bacplaca = isset($this->view->parametros->bacplaca) ? $this->view->parametros->bacplaca : "" ;     $this->view->parametros->bacusuoid_atendente = isset($this->view->parametros->bacusuoid_atendente) ? $this->view->parametros->bacusuoid_atendente : "" ;    $this->view->parametros->bactpcoid = isset($this->view->parametros->bactpcoid) ? $this->view->parametros->bactpcoid : "" ;    $this->view->parametros->bacfone = isset($this->view->parametros->bacfone) ? $this->view->parametros->bacfone : "" ;    $this->view->parametros->baccpf_cnpj = isset($this->view->parametros->baccpf_cnpj) ? $this->view->parametros->baccpf_cnpj : "" ;    $this->view->parametros->bacbmsoid = isset($this->view->parametros->bacbmsoid) ? $this->view->parametros->bacbmsoid : "" ;    $this->view->parametros->bacdetalhamento_solicitacao = isset($this->view->parametros->bacdetalhamento_solicitacao) ? $this->view->parametros->bacdetalhamento_solicitacao : "" ;    $this->view->parametros->bacstatus = isset($this->view->parametros->bacstatus) ? $this->view->parametros->bacstatus : "" ;


    $this->cd_usuario = $_SESSION['usuario']['oid'];
	    $this->view->parametros->dt_evento_de = isset($this->view->parametros->dt_evento_de) ? trim($this->view->parametros->dt_evento_de) : date('d/m/Y');
	    $this->view->parametros->dt_evento_ate = isset($this->view->parametros->dt_evento_ate) ? trim($this->view->parametros->dt_evento_ate) : date('d/m/Y');
    $this->view->parametros->status = isset($this->view->parametros->status) ? trim($this->view->parametros->status) : '';
    $this->view->parametros->selecao_por = isset($this->view->parametros->selecao_por) ? trim($this->view->parametros->selecao_por) : '';
    $this->view->parametros->cliente = isset($this->view->parametros->cliente) ? trim($this->view->parametros->cliente) : '';
    $this->view->parametros->placa = isset($this->view->parametros->placa) ? trim($this->view->parametros->placa) : '';
	    $this->view->parametros->uf = isset($this->view->parametros->uf) ? trim($this->view->parametros->uf) : '';
	    $this->view->parametros->cidade = isset($this->view->parametros->cidade) ? trim($this->view->parametros->cidade) : '';

    $this->view->parametros->data_confirmar = isset($this->view->parametros->data_confirmar) ? trim($this->view->parametros->data_confirmar) : '';
    $this->view->parametros->cliente_nm = isset($this->view->parametros->cliente_nm) ? trim($this->view->parametros->cliente_nm) : '';
    $this->view->parametros->idplaca = isset($this->view->parametros->idplaca) ? trim($this->view->parametros->idplaca) : '';

        $this->view->parametros->bacstatus = isset($this->view->parametros->bacstatus) ? $this->view->parametros->bacstatus : "";

    $this->view->parametros->buscarTipoContrato      = $this->dao->buscarTipoContrato();
    $this->view->parametros->buscarAtendente         = $this->dao->buscarAtendente();
    $this->view->parametros->buscarMotivo            = $this->dao->buscarMotivo();
    $this->view->parametros->buscarAtendenteLogado   = $this->dao->buscarAtendenteLogado();
	    $this->view->parametros->buscarUF 				 = $this->dao->buscarUF();

    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     *
     * @param stdClass $filtros Filtros da pesquisa
     *
     * @return array
     */
    private function pesquisar(stdClass $parametros) {

      if ($this->validarPesquisa($parametros)) {
        if($parametros->selecao_por==self::TIPO_RELATORIO_SINTETICO) {
          $resultadoPesquisa['motivo'] = $this->dao->pesquisarSinteticoPorMotivo($parametros);
          $resultadoPesquisa['tempo'] = $this->dao->pesquisarSinteticoPorTempo($parametros);
          $resultadoPesquisa['atendente'] = $this->dao->pesquisarSinteticoPorAtendente($parametros);
        }
        else {
          $resultadoPesquisa = $this->dao->pesquisar($parametros);
        }
      }

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->status = TRUE;

        return $resultadoPesquisa;
    }

    public function buscarDinamicamente(){

        $filtro     = isset($_GET['filtro']) ? $_GET['filtro'] : '';
        $parametro  = isset($_GET['term']) ? utf8_decode(trim($_GET['term'])) : '';
        $clioid     = isset($_GET['clioid']) ? trim($_GET['clioid']) : '';
        $telefone   = isset($_GET['telefone']) ? trim($_GET['telefone']) : '';

        try{

            $retorno  = array();

            switch ($filtro) {
                case 'nome':
                	$parametro = strtoupper($parametro);
                	$parametro = $this->removerAcentos($parametro);
                	
                	$retorno = $this->dao->retornarPesquisaDinamicaNome($filtro, strtoupper($parametro));

                    if (is_array($retorno) && count($retorno) > 0){
                        foreach($retorno as $key => $objeto){
                            $retorno[$key]['cpf_cgc'] = ($objeto['clitipo'] == 'F') ? str_pad($objeto['cpf_cgc'], 11, '0', STR_PAD_LEFT) : str_pad($objeto['cpf_cgc'], 14, '0', STR_PAD_LEFT);
                            $retorno[$key]['cpf_cgc'] = $this->formataCNPJCPF($retorno[$key]['cpf_cgc']);
                            $retorno[$key]['telefone'] = preg_replace('/\D/', '', $retorno[$key]['telefone']);
                            $retorno[$key]['telefone'] = $this->formataTelefone($retorno[$key]['telefone']);
                       
                        }
                    }

                    break;

                case 'placa':
                    $clioid = intval($clioid);
                    //busca placas vinculadas ao cliente
                    $retorno = $this->dao->buscarPlacasCliente($clioid, $_GET['acao']);

                    break;
            }

            echo json_encode($retorno);

        }catch(Exception $e){
            echo json_encode(array('error'=>true));
        }
        exit();

    }


    /**
     * Responsável por receber exibir o formulário de cadastro ou invocar
     * o metodo para salvar os dados
     *
     * @param stdClass $parametros Dados do cadastro, para edição (opcional)
     *
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

            //Verificar se foi submetido o formulário e grava o registro em banco de dados
            if (isset($_POST) && !empty($_POST) && $_POST['acao'] != 'incluir_historico') {
                $registroGravado = $this->salvar($this->view->parametros);
            }else if ($this->portalCliente == TRUE){
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
        if($this->portalCliente) {
            return $registroGravado;
        }
        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado){
            $this->index();
        } else {



            //@TODO: Montar dinamicamente o caminho apenas da view Index
            require_once _MODULEDIR_ . "Principal/View/prn_backoffice/cadastrar.php";
        }
    }

    /**
     * Responsável por receber exibir o formulário de edição ou invocar
     * o metodo para salvar os dados
     *
     * @return void
     */
    public function editar() {
        try {
            //Parametros
            $parametros = $this->tratarParametros();


            //Verifica se foi informado o id do cadastro
            if (isset($parametros->bacoid) && intval($parametros->bacoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->bacoid = (int) $parametros->bacoid;

                //Pesquisa o registro para edição
                $parametros = $this->dao->pesquisarPorID($parametros->bacoid);
                
                //busca placas vinculadas ao cliente
                $parametros->placas = $this->dao->buscarPlacasCliente($parametros->clioid, $_GET['acao']);

                $parametros->baccpf_cnpj = ($parametros->clitipo == 'F') ? str_pad($parametros->baccpf_cnpj, 11, '0', STR_PAD_LEFT) : str_pad($parametros->baccpf_cnpj, 14, '0', STR_PAD_LEFT);
                $parametros->baccpf_cnpj = $this->formataCNPJCPF($parametros->baccpf_cnpj);
               
                $parametros->bacfone  = preg_replace('/\D/', '', $parametros->bacfone );
                $parametros->bacfone = $this->formataTelefone($parametros->bacfone);


                $parametros->dadosHistorico = $this->dao->pesquisarHistico($parametros->bacoid);

                //Chama o metodo para edição passando os dados do registro por parametro.
                $this->cadastrar($parametros);
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
     *
     * @return void
     */
    private function salvar(stdClass $parametros) {

        //Validar os campos
       $this->validarCamposCadastro($parametros);

       $parametros->baccpf_cnpj = preg_replace('/[^0-9]/', '',$parametros->baccpf_cnpj);
       $parametros->bacfone = preg_replace('/\D/', '',$parametros->bacfone);
		      
        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        if ($parametros->bacoid > 0) {


            //Efetua a gravação do registro
            $gravacao = $this->dao->atualizar($parametros);

            //Seta a mensagem de atualização
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        } else {

            //Efetua a inserção do registroec
            $gravacao = $this->dao->inserir($parametros);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        }

        // Se retornou 'TRUE' da gravacao da solicitacao
        if ($gravacao === true){
          $gravacao = $this->inserirHistoricoContrato($parametros);
        }

        if ($parametros->acao == "portal_cliente"){
            $detalhesMotivo = $this->dao->buscarMotivo($parametros->bacbmsoid);
            $motivo = str_replace('Motivo:', '', $parametros->motivo_portal_cliente);
            
            $obsHistorico = "Solicitação Backoffice
            Data da Solicitação: {$parametros->data_confirmar}
            Status da Solicitação: {$status}
            Motivo: {$detalhesMotivo[0]->bmsdescricao}
            Detalhamento da Solicitação: {$parametros->bacdetalhamento_solicitacao}
            ";

            $this->gravarHistorico(
                $_REQUEST['id'],
                4873,
                //$parametros->bacdetalhamento_solicitacao, 
                $obsHistorico,
                null,
                null,
                300    // Agendamento via Portal - Impossibilidade de agendamento
              );
          }

        //Comita a transação
        $this->dao->commit();

        return $gravacao;
    }

  /**
     * Grava os dados no histórico do termo.
     *
     * @param stdClass $parametros Dados a serem gravados
     *
     * @return void
     */
    public function inserirHistoricoContrato(stdClass $parametros) {

      // Buscar detalhes do veiculo (numero de contrato)
      $detalhesVeiculo = $this->dao->retornarPesquisaDinamicaPlaca($parametros->bacplaca, $parametros->clioid, false);
      // Status da solicitação
      switch ($parametros->bacstatus) {
        case 'P':
          $status = "Pendente";
          break;
        case 'A':
          $status = "Em Andamento";
          break;
        case 'C':
          $status = "Concluido";
          break;
      }
      
      if ($parametros->acao == "cadastrar"){
        // Buscar tipo do motivo
        $detalhesMotivo = $this->dao->buscarMotivo($parametros->bacbmsoid);

        $obsHistorico = "Solicitação Backoffice
        Data da Solicitação: {$parametros->data_confirmar}
        Status da Solicitação: {$status}
        Motivo: {$detalhesMotivo[0]->bmsdescricao}
        Detalhe: {$parametros->bacdetalhamento_solicitacao}
        ";
      } else if($parametros->acao == "portal_cliente"){
        $detalhesMotivo = $this->dao->buscarMotivo($parametros->bacbmsoid);
        $motivo = str_replace('Motivo:', '', $parametros->motivo_portal_cliente);
        //$data = date("d/m/Y H:i:s",strtotime($parametros->data_confirmar));
        $obsHistorico = "Solicitação Backoffice
        Data da Solicitação: {$parametros->data_confirmar}
        Status da Solicitação: {$status}
        Motivo: {$detalhesMotivo[0]->bmsdescricao}
        Detalhamento da Solicitação: {$parametros->bacdetalhamento_solicitacao}
        ";
      }else{
        $obsHistorico = "Solicitação Backoffice
        Data da Solicitação: {$parametros->data_confirmar}
        Status da Solicitação: {$status}
        Detalhamento da Tratativa: {$parametros->bachtratativa}
        ";
        }
      
      //Inicia a transação
      $this->dao->begin();

      //Gravação
      $gravacao = null;

      $gravacao = $this->dao->inserirHistoricoContrato($detalhesVeiculo[0]['connumero'], $obsHistorico);

      //Comita a transação
      $this->dao->commit();

      return $gravacao;
    }

    /**
     * Método validarPesquisa()
     * Válida campos de pesquisa conforme a regra da demanda.
     *
     * @param array $parametros =>  Parâmetros para pesquisa.
     *
     * @return boolean
     */
    private function validarPesquisa(stdClass $parametros) {

        $camposDestaques = array();

        $obrigatoriosPreenchidos = true;
        $periodoAnalise = true;

        //válido se foi informado a data de inicio de inclusao
        if (trim($parametros->dt_evento_de) == '') {
            $obrigatoriosPreenchidos = false;
            $camposDestaques[] = array(
                            'campo' => 'dt_evento_de'
            );
        }

        //válido se foi informado a data de final de inclusao
        if (trim($parametros->dt_evento_ate) == '') {
            $obrigatoriosPreenchidos = false;
            $camposDestaques[] = array(
                            'campo' => 'dt_evento_ate'
            );
        }

        $this->view->dados = $camposDestaques;

        if ($obrigatoriosPreenchidos == false) {
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        if ($periodoAnalise == false) {
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        return true;
    }





    /**
     * Validar os campos obrigatórios do cadastro.
     *
     * @param stdClass $dados Dados a serem validados
     *
     * @throws Exception
     *
     * @return void
     */
    private function validarCamposCadastro(stdClass $parametros) {



                //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        if (trim($parametros->bacstatus) == '') {
            $error = true;
            $camposDestaques[] = array(
                'campo' => 'status'
            );
        }

        if (trim($parametros->clioid) == '') {
            $error = true;
            $camposDestaques[] = array(
                'campo' => 'clioid'
            );
        }


        if (trim($parametros->bacplaca) == '') {
            $error = true;
            $camposDestaques[] = array(
                'campo' => 'idplaca'
            );
        }


        if (trim($parametros->tpcoid) == '') {
            $error = true;
            $camposDestaques[] = array(
                'campo' => 'tpcoid'
            );
        }


        if (trim($parametros->bacfone) == '') {
            $error = true;
            $camposDestaques[] = array(
                'campo' => 'clifone'
            );
        }

        if (trim($parametros->baccpf_cnpj) == '') {
            $error = true;
            $camposDestaques[] = array(
                'campo' => 'cpf_cgc'
            );
        }

        if (trim($parametros->bacbmsoid) == '') {
            $error = true;
            $camposDestaques[] = array(
                'campo' => 'tipo_motivo'
            );
        }

        if (trim($parametros->bacdetalhamento_solicitacao) == '') {
            $error = true;
            $camposDestaques[] = array(
                'campo' => 'bacdetalhamento_solicitacao'
            );
        }


        


        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    public function executarQuery($query){
        global $conn;
        if(!$rs = pg_query($conn, $query)) {

            $msgErro = self::MENSAGEM_ERRO_PROCESSAMENTO;

            if( _AMBIENTE_ == 'LOCALHOST' || _AMBIENTE_ == 'DESENVOLVIMENTO' ) {
                $msgErro = "Erro ao processar a query: " . $query;
            }
            throw new ErrorException($msgErro);
        }
        return $rs;
    }

	//Metodo para gravar o historico de email e sms
	public function gravarHistorico($ordem,$usuario,$msg,$dataAgenda = null,$horaAgenda = null,$status = null){
        $dataAgenda = is_null($dataAgenda) ? "NULL" : "'{$dataAgenda}'";
        $horaAgenda = is_null($horaAgenda) ? "NULL" : "'{$horaAgenda}'";
        $status = is_null($status) ? "NULL" : "{$status}";

			$sql = "INSERT INTO
					ordem_situacao (orsordoid, orsusuoid, orssituacao, orsdt_agenda, orshr_agenda, orsstatus)
				VALUES ($ordem, $usuario, '$msg', $dataAgenda, $horaAgenda, $status)";

			$rs = $this->executarQuery($sql);
			$retorno = (!$rs) ? false : true;

		return $retorno;
	}

     public function incluir_historico() {
        try{
            //Retorna os parametros
            $parametros = $this->tratarParametros();

             
             //Realiza o CAST do parametro
             $parametros->bacoid = (int) $parametros->bacoid;
             $bachtratativa = isset($_POST['bachtratativa']) ? $_POST['bachtratativa']   : '';

             //Validar os campos
            $this->validarCamposCadastroHistorico($bachtratativa, $parametros);
            
             //Gravação
            $gravacao = null;

            $gravacao = $this->dao->inserirHistorico($parametros->bacoid,$bachtratativa);
            
            if ($gravacao === true){
              $this->inserirHistoricoContrato($parametros);
            }
            
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;

            unset($_POST['bachtratativa']);

            //Comita a transação
            $this->dao->commit();
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();

            $this->index();

            exit();

        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }

        $this->editar($parametros->bacoid);


    }


    /**
     * Validar os campos obrigatórios do cadastro.
     *
     * @param stdClass $dados Dados a serem validados
     *
     * @throws Exception
     *
     * @return void
     */
    private function validarCamposCadastroHistorico($bachtratativa, stdClass $parametros) {
        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        if (trim($bachtratativa) == '') {
            $error = true;
            $camposDestaques[] = array(
                'campo' => 'bachtratativa'
            );
        }
        
        if (trim($parametros->bacplaca) == '') {
            $error = true;
            $camposDestaques[] = array(
                'campo' => 'idplaca'
            );
        }
        
        if (trim($parametros->bacstatus) == '') {
            $error = true;
            $camposDestaques[] = array(
                'campo' => 'status'
            );
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }





    /**
     * Executa a exclusão de registro.
     *
     * @return void
     */
    public function excluir() {
        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($parametros->bacoid) || trim($parametros->bacoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->bacoid = (int) $parametros->bacoid;

            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->bacoid);

            //Comita a transação
            $this->dao->commit();

            if ($confirmacao) {

                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_EXCLUIR;
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

        $this->index();
    }



    private function formataCNPJCPF($numero){
        if(strlen($numero) <= 11){
            $mascara = str_repeat("0",11-strlen($numero)).$numero;
            $mascara = substr($mascara,0,3).".".substr($mascara,3,3).".".substr($mascara,6,3)."-".substr($mascara,9,2);
        }else{
            $mascara = str_repeat("0",14-strlen($numero)).$numero;
            $mascara = substr($mascara,0,2).".".substr($mascara,2,3).".".substr($mascara,5,3)."/".substr($mascara,8,4)."-".substr($mascara,12,2);
}
        return $mascara;
    }

    private function formataTelefone($numero){  

        $numero = preg_replace('/\D/', '', $numero);
        
        if(strlen($numero) > 10){  
            $mascara = "(" . substr($numero, 0, 2) . ") " . substr($numero, 2, 5) . "-" . substr($numero, 7, 4);
         }else{
            $mascara = "(" . substr($numero, 0, 2) . ") " . substr($numero, 2, 4) . "-" . substr($numero, 6, 4);
       
        }  
        return $mascara;
       
    }

    /**
     * Retorna as cidades de acordo com o estado
     */
    public function buscarCidade() {

    	ob_start();
    	try {

    		$estoid 	= $_POST['uf'];
    		$cidades	= $this->dao->buscarCidade($estoid);

    		$retorno		= array(
    				'erro'		=> false,
    				'codigo'	=> 0,
    				'retorno'	=> $cidades
    		);

    		echo  json_encode($retorno) ;
    		ob_flush();
    		exit;
		}
    	catch (Exception $e) {

    		ob_end_clean();
    		$retorno		= array(
    				'erro'		=> true,
    				'codigo'	=> $e->getCode(),
    				'retorno'	=> 	$e->getMessage()
    		);
    		echo json_encode($retorno);
    		exit;
    	}
    }

    /**
     * @access formulario_cadastro.php e prn_backoffice.js
     */
    public function buscarPorPlaca(){
    	try{
    		$filtro = trim(isset($_GET['term']) ? $_GET['term'] : '');
    		$retorno = $this->dao->buscarPorPlaca($filtro);
    			
    		echo json_encode($retorno);
    			
    	}catch(Exception $e){
    		echo json_encode(array('error'=>true));
    	}
    	exit();
    	 
    }
        
    //FUNÇÃO PARA REMOVER ACENTOS
    private function removerAcentos($str)
    {
    	$from = 'ÀÁÃÂÉÊÍÓÕÔÚÜÇàáãâéêíóõôúüç';
    	$to   = 'AAAAEEIOOOUUCaaaaeeiooouuc';
    
    	return strtr($str, $from, $to);
    }

    public function setUsuarioLogado($codigoUsuario) {
        $_SESSION['usuario']['oid'] = $codigoUsuario;
        $this->cd_usuario = $codigoUsuario;
    }

    public function getUsuarioLogado() {
        return $this->cd_usuario;
    }

    public function setPortalCliente($value) {
        $this->portalCliente = $value;
    }

}

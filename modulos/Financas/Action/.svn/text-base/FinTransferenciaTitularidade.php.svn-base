<?php
//include _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';
include_once 'lib/phpMailer/class.phpmailer.php';
require_once (_MODULEDIR_ . 'Financas/DAO/FinTransferenciaTitularidadeDAO.php');
require _MODULEDIR_ .'Financas/View/FinTransferenciaTitularidadeView.class.php';
require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';
require_once _SITEDIR_."lib/Components/CsvWriter.php";
require _SITEDIR_ . 'modulos/core/infra/autoload.php';

use module\GestorCredito\GestorCreditoService as GestorCredito;
require_once _MODULEDIR_ . 'eSitef/Action/IntegracaoSoftExpress.class.php';

class FinTransferenciaTitularidade {
    
    //const VIEW_DIR = 'Principal/View/prn_transferencia_titularidade/';
    const VIEW_DIR = 'Financas/View/fin_transferencia_titularidade/';
    private $msgErro = "";
    private $dao;
    private $cd_usuario;
    private $formaPagamentoAtualTeste;
    
    const filtroscsv = '';
    
    /**
     * Mensagem de alerta para campos obrigatórios não preenchidos
     * @const String
     */
    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Ao menos  um dos campos da pesquisa devem ser preenchidos.";
    const MENSAGEM_ALERTA_CAMPOS_DATAS = "Data inicio e data fim devem ser preenchidas.";
    const MENSAGEM_ALERTA_CAMPOS_DATAS_DIAS = "A data não pode ser maior que 31 dias";
    const MENSAGEM_ALERTA_CAMPOS_DATAS_MAIOR = "Data de inicio não pode ser maior que a data de fim";
    /**
     * Mensagem para nenhum registro encontrado
     * @const String
     */
    const MENSAGEM_NENHUM_REGISTRO = "Nenhum registro encontrado.";
    
    /**
     * Contém dados a serem utilizados na View.
     *
     * @var stdClass
     */
    private $view;
    private $TitularidadeView;
    
    function __construct() {
        global $conn;
        $this->dao = new FinTransferenciaTitularidadeDAO ( $conn );
        $this->TitularidadeView      = new FinTransferenciaTitularidadeView();
        $this->cd_usuario = $_SESSION['usuario']['oid'];
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
        
        $this->view->paginacao = null;
        
        $this->view->ordenacao = null;
        
        $rs = pg_query ($conn,"SELECT eqcoid,formata_str(eqcdescricao) AS descricao FROM equipamento_classe ORDER BY descricao;");
        $n = pg_num_rows ($rs);
        $_SESSION['snequipamento_classe']=$n;
        for ($i = 0; $i < $n; $i++) {
            $arr = pg_fetch_array ($rs,$i);
            $_SESSION["seqcoid"][$arr["eqcoid"]] = $arr["descricao"];
            $_SESSION["sequipamento_classe"][$i]["eqcoid"]=$arr["eqcoid"];
            $_SESSION["sequipamento_classe"][$i]["eqcdescricao"]=$arr["descricao"];
        }
        
    }
    
    private function view($viewName){
        include _MODULEDIR_ . self::VIEW_DIR . $viewName;
    }
    
    //@@abre a pagina de nova transferencia para preencher
    public function cadastrar(){
        //@
        $statusLista = $this->dao->pesquisastatuscontrato();
        // print_r($statusLista);
        include (_MODULEDIR_ . 'Financas/View/fin_transferencia_titularidade/formulario_nova_transferencia' . (isset ( $_POST ['ajax'] ) ? '.ajax' : '') . '.php');
    }
    
    //@@Metodo de pesquisa de clientes de auto complete do jquery
    public function pesquisaClientes() {
        $cliente = $_POST ['cliente'];
        
        if (! empty ( $cliente ) && isset ( $cliente )) {
            $result = $this->dao->pesquisaCliente ( $cliente );
            
            while ( $rsRow = pg_fetch_array ( $result ) ) {
                
                if (isset ( $rsRow ['retornocpf'] ) || ! empty ( $rsRow ['retornocpf'] ) || $rsRow ['retornocpf'] != '' || $rsRow ['retornocpf'] != null) {
                    $resultadoCPFCNPJ = $this->mask ( $rsRow ['retornocpf'], '###.###.###-##' );
                    echo utf8_decode ('<li id="resultado" onclick="set_item(\'' . $rsRow ['label'] . '\',\'' . $rsRow ['id'] . '\', \'' . $resultadoCPFCNPJ . '\')">' . $rsRow ['label'] . '  ' . 'CPF:' . utf8_decode ( $resultadoCPFCNPJ ) . "</li>");
                } else {
                    
                    $resultadoCPFCNPJ = $this->mask ( $rsRow ['retornocnpj'], '##.###.###/####-##' );
                    echo utf8_decode ('<li id="resultado" onclick="set_item(\'' . $rsRow ['label'] . '\',\'' . $rsRow ['id'] . '\', \'' . $resultadoCPFCNPJ . '\')">' . $rsRow ['label'] . '  ' . 'CNPJ:'  .   $resultadoCPFCNPJ  . "</li>");
                }
            }
        }
    }
    
    
    //@@Metodo de pesquisa de clientes de auto complete do jquery
    public function pesquisaClientesNovo() {
        $cliente = $_POST ['cliente'];
        
        if (! empty ( $cliente ) && isset ( $cliente )) {
            $result = $this->dao->pesquisaCliente ( $cliente );
            
            while ( $rsRow = pg_fetch_array ( $result ) ) {
                if (isset ( $rsRow ['retornocpf'] ) || ! empty ( $rsRow ['retornocpf'] ) || $rsRow ['retornocpf'] != '' || $rsRow ['retornocpf'] != null) {
                    $resultadoCPFCNPJ =  $this->mask ( $rsRow ['retornocpf'], '###.###.###-##' );
                    echo utf8_decode ('<li id="resultado" onclick="set_itemnovo(\'' . $rsRow ['label'] . '\',\'' . $rsRow ['id'] . '\', \'' . $resultadoCPFCNPJ . '\')">' . $rsRow ['label'] . '  ' .  'CPF:'. utf8_decode ( $resultadoCPFCNPJ ) . "</li>");
                } else {
                    $resultadoCPFCNPJ =  $this->mask ( $rsRow ['retornocnpj'], '##.###.###/####-##' );
                    echo utf8_decode ('<li id="resultado" onclick="set_itemnovo(\'' . $rsRow ['label'] . '\',\'' . $rsRow ['id'] . '\', \'' . $resultadoCPFCNPJ . '\')">' . $rsRow ['label'] . '  ' .  'CNPJ:'. $resultadoCPFCNPJ  . "</li>");
                }
            }
        }
    }
    
    //@Abre a pagina de novo titular com a consulta efetuada
    public function novo(){
        $parametros = $_POST;
        
        if ($parametros['chk_oid']) {
            
            $parametros['contratoIdArray'] = $parametros['chk_oid'];
            
            try {
                //pesquisa o cliente com os dados passados como parametros
                $transferencia = $this->dao->pesquisaTransferencia($parametros);
                
                $valortotalLocacao = 0;
                $valortotalAcessorios = 0;
                $valortotalMonitoramento = 0;
                $valortotalTotal = 0;
                
                for($j = 0; $j < count($transferencia); $j++) {
                    $contrato = $transferencia[$j]['connumero'];
                    
                    $prazoFidelidade = $transferencia[$j]['conprazo_contrato'];
                    $dataInicioVigencia = $transferencia[$j]['inicio_vigencia'];
                    $upgradeDown = $this->dao->upgradeDown ( $contrato );
                    $paralizacao = $this->dao->paralizacao ( $contrato , $dataInicioVigencia, $prazoFidelidade);
                    $fidelizacao = $this->dao->fidelizacao ( $contrato );

                    if($upgradeDown != null) {
                        if ($upgradeDown[1] != 2) {
                            $transferencia[$j]['inicio_vigencia'] = $upgradeDown[0];
                        } else {
                            $transferencia[$j]['inicio_vigencia'] = $upgradeDown[0];
                        }
                    }

                    if($paralizacao != null) {
                        $transferencia[$j]['conprazo_contrato'] = $transferencia[$j]['conprazo_contrato'] + $paralizacao[0];
                        $transferencia[$j]['conprazo_contrato'] = $transferencia[$j]['conprazo_contrato'] + $paralizacao[0];
                    }

                    if($fidelizacao != null) {
                        $transferencia[$j]['inicio_vigencia'] = $fidelizacao[0];
                        $transferencia[$j]['conprazo_contrato'] = $fidelizacao[1];
                    }

                    /*if($paralizacao != null) {
                        //$prazoFidelidade = $paralizacao[1];
                    }*/

                    $dataAtual = date('d-m-Y');
                    
                    $dataTerminoVigencia= date('m-d-Y', strtotime("+".$transferencia[$j]['conprazo_contrato']." months",   strtotime($transferencia[$j]['inicio_vigencia'])));
                    
                    if ($dataInicioVigencia == $dataAtual){
                        $mesesTerminoVigencia = $prazoFidelidade;
                    }else{
                        $resultadoSubtracao = (strtotime($dataAtual) - strtotime($dataTerminoVigencia))/86400;
                        
                        $diasRestantes = (int)ceil( $resultadoSubtracao / (60 * 60 * 24));
                        
                        $mesesRestantes = (int)ceil($diasRestantes / 30.416666666666668);
                        
                        if ($mesesRestantes > 0){
                            $mesesTerminoVigencia = 0;
                        }else {
                            $mesesTerminoVigencia = $mesesRestantes;
                        }
                    }
                    
                    /**
                     * Locação
                     */
                    $valorLocacao = $this->dao->pesquisaLocacao($contrato);
                    
                    for($l = 0; $l < count($valorLocacao); $l++) {
                        if($valorLocacao[$l]['valor_locacao'] != 0) {
                            $valortotalLocacao += $valorLocacao[$l]['valor_locacao'];
                        }
                        echo "<pre>";
                        //var_dump("valor - > ". $valortotal);
                        echo "</pre>";
                    }
                    
                    $valortotalTotal = $valortotalTotal + $valortotalLocacao;
                    
                    if($valortotalLocacao == 0) {
                        $valortotalLocacao = "0.00";
                    }
                    $transferencia[$j]['locacao']=$valortotalLocacao;
                    
                    $valortotalLocacao = 0;
                    
                    /**
                     * Acessórios
                     */
                    $valorAcessorios = $this->dao->pesquisaAcessorios($contrato);
                    
                    for($a = 0; $a < count($valorAcessorios); $a++) {
                        if($valorAcessorios[$a]['valor_acessorios'] != 0) {
                            $valortotalAcessorios += $valorAcessorios[$a]['valor_acessorios'];
                        }
                        echo "<pre>";
                        //var_dump("valor - > ". $valortotal);
                        echo "</pre>";
                    }
                    
                    if($valortotalAcessorios == 0) {
                        $valortotalAcessorios = "0.00";
                    }
                    $transferencia[$j]['acessorios']=$valortotalAcessorios;
                    
                    $valortotalAcessorios = 0;
                    
                    /**
                     * Monitoramento
                     */
                    $valorMonitoramento = $this->dao->pesquisaMonitoramento($contrato);
                    
                    for($m = 0; $m < count($valorMonitoramento); $m++) {
                        if($valorMonitoramento[$m]['valor_monitoramento'] != 0) {
                            $valortotalMonitoramento += $valorMonitoramento[$m]['valor_monitoramento'];
                        }
                        
                    }
                    
                    $valortotalTotal = $valortotalTotal + $valortotalMonitoramento;
                    
                    if($valortotalMonitoramento == 0) {
                        $valortotalMonitoramento = "0.00";
                    }
                    $transferencia[$j]['monitoramento']=$valortotalMonitoramento;
                    
                    $valortotalMonitoramento = 0;
                    
                    /**
                     * Total
                     */
                    
                    if($valortotalTotal == 0) {
                        $valortotalTotal = "0.00";
                    }
                    $transferencia[$j]['total']=$valortotalTotal;
                    
                    $valortotalTotal = 0;
                    
					$transferencia[$j]['locacao'] = $valortotalLocacao = "0.00";
                }                
            } catch (Exception $e) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => $e->getMessage(),
                    "redirect" => ""
                ));
                return;
            }
        }
        
        $cliente = $_POST ['cliente'];
        
        if (! empty ( $cliente ) && isset ( $cliente )) {
            $result = $this->dao->pesquisaCliente ( $cliente );
            
            $rsRow = pg_fetch_array ( $result );
            if (isset ( $rsRow ['retornocpf'] ) || ! empty ( $rsRow ['retornocpf'] ) || $rsRow ['retornocpf'] != '' || $rsRow ['retornocpf'] != null) {
                $resultadoCPFCNPJ = $this->mask ( $rsRow ['retornocpf'], '###.###.###-##' );
            } else {
                $resultadoCPFCNPJ = $this->mask ( $rsRow ['retornocnpj'], '##.###.###/####-##' );
            }
        }
        
        
        include (_MODULEDIR_ . 'Financas/View/fin_transferencia_titularidade/dados_contatos' . (isset ( $_POST ['ajax'] ) ? '.ajax' : '') . '.php');
        
    }
    
    
    /**
     * @@retorna os dados dos  Titulos Pendentes dos Contratos Selecionados
     */
    public function dadosContato() {
        
        
    }
    
    
    
    
    /**
     * @@pesquisa e retorna as informações do cliente passando os parametros de nome, placa ou contrato na tela de nova proposta
     */
    public function pesquisa() {
        
        // $this->_testaFaturamentoUnificado();
        if (isset ( $_POST )) {
            
            $parametros = $_POST;
            
            if(isset($parametros['statusId']) ){
                $parametros['concsioidArray'][] = $parametros['statusId'];
            }
            
            
            try {
                //pesquisa o cliente com os dados passados como parametros
                $transferencia = $this->dao->pesquisaTransferencia ( $parametros );
                
                
                if(!$transferencia){
                    
                    include (_MODULEDIR_ . 'Financas/View/fin_transferencia_titularidade/contratos_transferencias' . (isset ( $_POST ['ajax'] ) ? '.ajax' : '') . '.php');
                    return;
                }
                
                $valortotalLocacao = 0;
                $valortotalAcessorios = 0;
                $valortotalMonitoramento = 0;
                $valortotalTotal = 0;
                
                for($j = 0; $j < count($transferencia); $j++) {
                    
                    $contrato = $transferencia[$j]['connumero'];
                    
                    $prazoFidelidade = $transferencia[$j]['conprazo_contrato'];
                    $dataInicioVigencia = $transferencia[$j]['inicio_vigencia'];
                    $upgradeDown = $this->dao->upgradeDown ( $contrato );
                    $paralizacao = $this->dao->paralizacao ( $contrato , $dataInicioVigencia, $prazoFidelidade);
                    $fidelizacao = $this->dao->fidelizacao ( $contrato );

                    if($upgradeDown != null) {
                        if ($upgradeDown[1] != 2) {
                            $transferencia[$j]['inicio_vigencia'] = $upgradeDown[0];
                        } else {
                            $transferencia[$j]['inicio_vigencia'] = $upgradeDown[0];
                        }
                    }

                    if($paralizacao != null) {
                        $transferencia[$j]['conprazo_contrato'] = $transferencia[$j]['conprazo_contrato'] + $paralizacao[0];
                        $prazoFidelidade = $transferencia[$j]['conprazo_contrato'] + $paralizacao[0];
                    }

                    if($fidelizacao != null) {
                        $transferencia[$j]['inicio_vigencia'] = $fidelizacao[0];
                        $prazoFidelidade = $fidelizacao[1];
                    }

                    /*if($paralizacao != null) {
                        //$prazoFidelidade = $paralizacao[1];
                    }*/

                    $dataAtual = date('d-m-Y');
                    
                    $dataTerminoVigencia= date('m-d-Y', strtotime("+".$transferencia[$j]['conprazo_contrato']." months",   strtotime($transferencia[$j]['inicio_vigencia'])));
                    
                    if ($dataInicioVigencia == $dataAtual){
                        $mesesTerminoVigencia = $prazoFidelidade;
                    }else{
                        $resultadoSubtracao = (strtotime($dataAtual) - strtotime($dataTerminoVigencia))/86400;
                        
                        $diasRestantes = (int)ceil( $resultadoSubtracao / (60 * 60 * 24));
                        
                        $mesesRestantes = (int)ceil($diasRestantes / 30.416666666666668);
                        
                        if ($mesesRestantes > 0){
                            $mesesTerminoVigencia = 0;
                        }else {
                            $mesesTerminoVigencia = $mesesRestantes;
                        }
                    }
                    
                    /**
                     * Locação
                     */
                    $valorLocacao = $this->dao->pesquisaLocacao($contrato);
                    
                    for($l = 0; $l < count($valorLocacao); $l++) {
                        if($valorLocacao[$l]['valor_locacao'] != 0) {
                            $valortotalLocacao += $valorLocacao[$l]['valor_locacao'];
                        }
                        
                    }
                    
                    
                    $valortotalTotal = $valortotalTotal + $valortotalLocacao;
                    
                    if($valortotalLocacao == 0) {
                        $valortotalLocacao = "0.00";
                    }
                    $transferencia[$j]['locacao']=$valortotalLocacao;
                    $transferencia[$j]['valor_locacao']=$valortotalLocacao;
                    
                    $valortotalLocacao = 0;
                    
                    /**
                     * Acessórios
                     */
                    $valorAcessorios = $this->dao->pesquisaAcessorios($contrato);
                    
                    for($a = 0; $a < count($valorAcessorios); $a++) {
                        if($valorAcessorios[$a]['valor_acessorios'] != 0) {
                            $valortotalAcessorios += $valorAcessorios[$a]['valor_acessorios'];
                        }
                        
                    }
                    
                    if($valortotalAcessorios == 0) {
                        $valortotalAcessorios = "0.00";
                    }
                    $transferencia[$j]['acessorios']=$valortotalAcessorios;
                    
                    $valortotalAcessorios = 0;
                    
                    /**
                     * Monitoramento
                     */
                    $valorMonitoramento = $this->dao->pesquisaMonitoramento($contrato);
                    
                    for($m = 0; $m < count($valorMonitoramento); $m++) {
                        if($valorMonitoramento[$m]['valor_monitoramento'] != 0) {
                            $valortotalMonitoramento += $valorMonitoramento[$m]['valor_monitoramento'];
                        }
                    }
                    
                    $valortotalTotal = $valortotalTotal + $valortotalMonitoramento;
                    
                    if($valortotalMonitoramento == 0) {
                        $valortotalMonitoramento = "0.00";
                    }
                    $transferencia[$j]['monitoramento']=$valortotalMonitoramento;
                    
                    $valortotalMonitoramento = 0;
                    
                    /**
                     * Total
                     */
                    if($valortotalTotal == 0) {
                        $valortotalTotal = "0.00";
                    }
                    $transferencia[$j]['total']=$valortotalTotal;
                    
                    $valortotalTotal = 0;
                    ;
                }
                
            } catch ( Exception $e ) {
                echo json_encode ( array (
                    "status" => "error",
                    "message" => $e->getMessage (),
                    "redirect" => ""
                ) );
                return;
            }
        }
        //@
        
        include (_MODULEDIR_ . 'Financas/View/fin_transferencia_titularidade/contratos_transferencias' . (isset ( $_POST ['ajax'] ) ? '.ajax' : '') . '.php');
    }
    
    
    //@Abre a pagina de pesquisa e retorna a consulta
    public function index() {
        try {
            //trata os parametros do post ou get
            $this->view->parametros = $this->tratarParametros();
            
            //array com o
            /*$dados = array(
             'titularantigo' => $_POST['nomeCliente'],
             'novocliente' => $_POST['novoTitular'],
             'dt_ini' => $_POST['dt_ini'],
             'dt_fim' => $_POST['dt_fim'],
             'statusSolicitacaoTransDivida' => $_POST['statusSolicitacaoTransDivida'],
             'statusSolicitacaoSerasa' => $_POST['statusSolicitacaoSerasa'],
             'numeroSolicitacao' => $_POST['numeroSolicitacao'],
             'numeroContrato' => $_POST['numeroContrato'],
             'dt_ini_conclusao' => $_POST['dt_ini_conclusao'],
             'dt_fim_conclusao' => $_POST['dt_fim_conclusao'],
             'usuarios_conclusao' => $_POST['usuarios_conclusao']
             );*/
            
            //Inicializa os dados
            //  $this->inicializarParametros();
            
            //Verificar se a ação pesquisar e executa pesquisa
            if ($this->view->parametros->acao == 'buscaPropostaTranferenciaTitularidade') {
                
                //faz a verificação de campos vazios
                if($this->view->parametros->dt_ini == '' && $this->view->parametros->dt_fim == ''){
                    
                    if($this->view->parametros->dt_ini_conclusao == '' && $this->view->parametros->dt_fim_conclusao == ''){
                        
                        if($this->view->parametros->nomeCliente != '' || $this->view->parametros->novoTitular || $this->view->parametros->numeroSolicitacao
                            || $this->view->parametros->numeroContrato != '' || $this->view->parametros->statusSolicitacaoTransDivida != ''
                            || $this->view->parametros->statusSolicitacaoSerasa != '' || $this->view->parametros->usuarios_conclusao != '' ){
                                
                                //chama o metodo passando os valores para pesquisa
                                $this->view->dados = $this->buscaPropostaTranferenciaTitularidade($this->view->parametros);
                        }
                        else{
                            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
                        }
                    }
                    //verifica o campo das datas senão estão vazios
                    else if(($this->view->parametros->dt_ini_conclusao != '' &&  $this->view->parametros->dt_fim_conclusao == '')
                        || ($this->view->parametros->dt_ini_conclusao == '' &&  $this->view->parametros->dt_fim_conclusao != '' )){
                            
                            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_DATAS);
                    }else{
                        
                        list($dia_i, $mes_i, $ano_i) = explode("/", $this->view->parametros->dt_ini_conclusao); //Data inicial
                        list($dia_f, $mes_f, $ano_f) = explode("/", $this->view->parametros->dt_fim_conclusao); //Data final
                        
                        $mk_i = mktime(0, 0, 0, $mes_i, $dia_i, $ano_i); // obtem tempo unix no formato timestamp
                        $mk_f = mktime(0, 0, 0, $mes_f, $dia_f, $ano_f); // obtem tempo unix no formato timestamp
                        
                        $diferenca = $mk_f - $mk_i; //Acha a diferença entre as datas
                        
                        if($diferenca < 0){
                            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_DATAS_MAIOR);
                        }else{
                            
                        }
                        date_default_timezone_set('America/Sao_Paulo');
                        
                        $inicio = $this->view->parametros->dt_ini_conclusao;
                        $fim = $this->view->parametros->dt_fim_conclusao;
                        
                        $dataInicio = explode("/",$inicio);
                        $dataFim = explode("/",$fim);
                        
                        $data_inicial = $dataInicio[2]."-".$dataInicio[1]."-".$dataInicio[0];
                        $data_final = $dataFim[2]."-".$dataFim[1]."-".$dataFim[0];
                        
                        $dif = strtotime($data_inicial) - strtotime($data_final);
                        
                        $dias = floor($dif/(60*60*24) -1);
                        
                        $dias = explode("-",$dias);
                        
                        if($dias[1] <= 31) {
                            $this->view->dados = $this->buscaPropostaTranferenciaTitularidade($this->view->parametros);
                        }else{
                            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_DATAS_DIAS);
                        }
                    }
                }else if(($this->view->parametros->dt_ini != '' &&  $this->view->parametros->dt_fim == '') || ($this->view->parametros->dt_ini == '' &&  $this->view->parametros->dt_fim != '' )){
                    throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_DATAS);
                }else{
                    date_default_timezone_set('America/Sao_Paulo');
                    
                    $inicio = $this->view->parametros->dt_ini;
                    $fim = $this->view->parametros->dt_fim;
                    
                    $dataInicio = explode("/",$inicio);
                    $dataFim = explode("/",$fim);
                    
                    $data_inicial = $dataInicio[2]."-".$dataInicio[1]."-".$dataInicio[0];
                    $data_final = $dataFim[2]."-".$dataFim[1]."-".$dataFim[0];
                    
                    $dif = strtotime($data_inicial) - strtotime($data_final);
                    
                    $dias = floor($dif/(60*60*24) -1);
                    
                    $dias = explode("-",$dias);
                    
                    if($dias[1] <= 31) {
                        $this->view->dados = $this->buscaPropostaTranferenciaTitularidade($this->view->parametros);
                    }else{
                        throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_DATAS_DIAS);
                    }
                }
            }
        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
        }
        
        //@
        include (_MODULEDIR_ . 'Financas/View/fin_transferencia_titularidade/index.php');
    }
    
    
    
    //@Abre a pagina de historico e retorna o historico de operações no CNPJ
    public function historico(){
        /*$this->acao = 'historico';
         $this->view('historico.php');*/
        
        if (isset ( $_POST )) {
            
            $parametros = $_POST;
            
            
            if(isset($parametros['statusId']) ){
                $parametros['concsioidArray'][] = $parametros['statusId'];
            }
            
            try {
                //pesquisa o cliente com os dados passados como parametros
                $transferencia = $this->dao->pesquisaTransferencia( $parametros );
            } catch ( Exception $e ) {
                echo json_encode ( array (
                    "status" => "error",
                    "message" => $e->getMessage (),
                    "redirect" => ""
                ) );
                return;
            }
        }
        //@
        include (_MODULEDIR_ . 'Financas/View/fin_transferencia_titularidade/historico_lista' . (isset ( $_POST ['ajax'] ) ? '.ajax' : '') . '.php');
    }
    
    //Metodo que atualiza as proposta cadastrada e que já forão aprovadas pelo serasa e pela transferencia titularidade
    public function editar(){
        
        //recebe o id da proposta
        $id = $_GET['idProposta'];
        
        //retorna os contratos pesquisados pelo ID
        $retornoContratos = $this->dao->retornaTransferenciasPorContrato($id);
        
        
        
        
        //lista a proposta da transferencia por ID
        $retorno = $this->dao->listSolicitacaoTransferenciaPorID($id);
        
        //Verifica se já existe id da proposta na tabela proposta_transferencia_cliente e retorna os dados
        $listaClientesNovo = $this->dao->consultaNovoClientePropostaId($id);
        
        //caso não exista cliente cadastrado na tabela proposta_transferencia_cliente é verificado se existe o cliente na tabela clientes
        $listaClienteExistente = $this->consultaClienteExistente($retorno['ptrano_documento']);
        if($listaClienteExistente['clioid'] != '' || $listaClienteExistente['clioid'] != null || !empty($listaClienteExistente['clioid'])){
            $formaPagamentoAntiga = $this->dao->consultaFormaPagamentoExistente($listaClienteExistente['clioid']);
            $formaPagamentoAntigaCadastrada = $this->dao->consultaFormaPagamentoExistenteCadastrada($listaClienteExistente['clioid']);
        }
        
        
        //retorna as formas de pagamento
        $listaFormaPagamentoIdProposta = $this->dao->listaFormaPagamentoIdProposta($id);
        
        foreach ( $retornoContratos as $row => $key ) {
            $contratos[] = $key['connumero'];
        }
        $totalContratos = count($contratos);
        
        $valorTaxasContratos = 0;
        
        //busca os valores das taxa de transferencia da tabela dominios
        $taxaTrans = $this->dao->taxaTransferencia();
        
        foreach ($taxaTrans as $row){
            
            $qtdaContrato = explode(",",$row->valvalor);
            
            //verifica a quantidade de contratos
            if($totalContratos >= $qtdaContrato[0] && $totalContratos <= $qtdaContrato[1]) {
                
                //retorna o valor dos contratos passando o id da tabela registro para a tabela valor
                $valorTaxasContratos = $this->dao->taxaTransferenciaPorID($row->valregoid);
            }
            /*
             * caso a quantidade de contrato for entre 1 e 9 a taxa será cobrado por veiculo
             * senão vai ser cobrada uma taxa unica
             */
            if($qtdaContrato <= 9 && $qtdaContrato >= 1) {
                $texto = 'por veículo';
            }else{
                $texto = 'taxa única';
            }
            
        }
        
        $contratos = implode ( ",", $contratos );
        
        //consulta se existe contratos com pagamento pendente , caso exista não abre o formulario de cadastro
        $dados = $this->dao->titulosPendentesContratos ( $contratos );
        //pesquisaTransferencia
        //$resultadoEditar = $this->dao->
        include (_MODULEDIR_ . 'Financas/View/fin_transferencia_titularidade/editar' . (isset ( $_POST ['ajax'] ) ? '.ajax' : '') . '.php');
    }
    
    /**
     * Metodo que retorna o resultado da pesquisa do index, passado os parametros selecionados pelo usuario, conforme os valores retornado , esse metodo cria a paginação
     */
    private function buscaPropostaTranferenciaTitularidade(stdClass $filtros){
        // $this->_testaFaturamentoUnificado();
        if (isset ( $_POST )) {
            $parametros = $_POST;
            
            if(isset($parametros['statusId']) ){
                $parametros['concsioidArray'][] = $parametros['statusId'];
            }
            
            try {
                //pesquisa o cliente com os dados passados como parametros
                $transferencia = $this->dao->pesquisaTransferencia ( $parametros );
                
            } catch ( Exception $e ) {
                echo json_encode ( array (
                    "status" => "error",
                    "message" => $e->getMessage (),
                    "redirect" => ""
                ) );
                return;
            }
        }
        $this->filtroscsv = $filtros;
        
        $paginacao = new PaginacaoComponente();
        
        //busca o resultado na DAO conforme os filtros passados
        $resultPesquisa = $this->dao->BuscaPropostaTranferencia($filtros);
        
        
        if (count($resultPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }else{
            $campos = array(
                'ptraoid' => 'Proposta',
                'clinome' => 'Cliente',
                'ptradt_cadastro' => 'Data de Cadastro',
                'ptrasfoid_analise_credito' => 'Status Serasa',
                'ptrasfoid_analise_divida' => 'Status Transferencia Divida',
            );
            
            
            if ($paginacao->setarCampos($campos)) {
                //$this->view->ordenacao = $paginacao->gerarOrdenacao('ptraoid');
                $this->view->paginacao = $paginacao->gerarPaginacao(count($resultPesquisa));
                
            }
            
            $resultPesquisa = $this->dao->BuscaPropostaTranferencia($filtros, $paginacao->buscarPaginacao());
        }
        
        return $resultPesquisa;
        
        //include (_MODULEDIR_ . 'Financas/View/fin_transferencia_titularidade/resultado_pesquisa' . (isset ( $_POST ['ajax'] ) ? '.ajax' : '') . '.php');
    }
    
    public function geraExcelDetalhesNota() {
        
        // $this->_testaFaturamentoUnificado();
        if (isset ( $_POST )) {
            $parametros = $_POST;
            
            if(isset($parametros['statusId']) ){
                $parametros['concsioidArray'][] = $parametros['statusId'];
            }
            
            try {
                //pesquisa o cliente com os dados passados como parametros
                $transferencia = $this->dao->pesquisaTransferencia ( $parametros );

                /*
                $contrato = $transferencia[$j]['connumero'];

                $prazoFidelidade = $transferencia[$j]['conprazo_contrato'];
                $dataInicioVigencia = $transferencia[$j]['inicio_vigencia'];
                $upgradeDown = $this->dao->upgradeDown ( $contrato );
                $paralizacao = $this->dao->paralizacao ( $contrato , $dataInicioVigencia, $prazoFidelidade);
                $fidelizacao = $this->dao->fidelizacao ( $contrato );

                if($upgradeDown != null) {
                    if ($upgradeDown[1] != 2) {
                        $transferencia[$j]['inicio_vigencia'] = $upgradeDown[0];
                    } else {
                        $transferencia[$j]['inicio_vigencia'] = $upgradeDown[0];
                    }
                }

                if($paralizacao != null) {
                    $transferencia[$j]['conprazo_contrato'] = $transferencia[$j]['conprazo_contrato'] + $paralizacao[0];
                    $prazoFidelidade = $transferencia[$j]['conprazo_contrato'] + $paralizacao[0];
                }

                if($fidelizacao != null) {
                    $transferencia[$j]['inicio_vigencia'] = $fidelizacao[0];
                    $prazoFidelidade = $fidelizacao[1];
                }*/

                /*if($paralizacao != null) {
                    //$prazoFidelidade = $paralizacao[1];
                }*/
                
            } catch ( Exception $e ) {
                echo json_encode ( array (
                    "status" => "error",
                    "message" => $e->getMessage (),
                    "redirect" => ""
                ) );
                return;
            }
        }
        
        $colunas = array(
            'Cliente:',
            'Contrato:',
            'Inicio de Vigência:',
            'Meses Restantes:',
            'Placa:',
            'Tipo do Contrato:',
            'Classe do Contrato:',
            'Locação:',
            'Acessórios:',
            'Monitoramento:',
            'Valor Total:',
            'Status:'
        );
        
        /*$this->load->library('excel');
         inicializa_planilha($this->excel,'Relatório Financeiro');
         */
        $countLinhas = 4;
        echo "<pre>";
        //var_dump($transferencia);
        echo "</pre>";
        foreach ($transferencia as $key => $value) {
            $linha = array(
                $value['clinome'], // Contrato
                $value['connumero'], // Início Vigência
                $value['inicio_vigencia'], // Prazo Fidelidade
                $value['conprazo_contrato'], // Classe
                $value['veiplaca'], // Valor de Tratamento de Informações
                $value['tipo_contrato'], // Valor da Locação
                $value['classe_contrato'], // Valor dos Acessórios
                $value['locacao'], // Data do último reajuste de IGPM/INPC
                $value['acessorios'], // Data do último reajuste de IGPM/INPC
                $value['monitoramento'], // Data do último reajuste de IGPM/INPC
                $value['total'], // Data do último reajuste de IGPM/INPC
                $value['csidescricao'] // Status
            );
            
            //$this->excel->getActiveSheet()->fromArray($linha, null, 'B'.$countLinhas);
            $countLinhas = $countLinhas + 1;
        }
        
        /*cria_cabecalho_planilha($this->excel,'     Relatório de Transferência',$colunas);
         */
        //$filename='relatorio_transferencia'.date('dmYHis').'.xls'; //save our workbook as this file name
        //$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //$objWriter->save(str_replace(__FILE__,excel_path().$filename,__FILE__));
        
        $nome = str_replace(" ", "_", $value['clinome']);
        $filename['file_name'] = 'relatorio_transferenciasdasda_'.$nome.'_'.date('d-m-Y').'.xls';
        /*file_put_contents($filename['file_path'] . $filename['file_name'], $linha);
         $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
         $objWriter->save(str_replace(__FILE__,excel_path().$filename['file_name'] ,__FILE__));
         
         
         echo json_encode(array(
         "codigo"	=> 0,
         "msg"		=> 'Planilha CSV gerada com sucesso.',
         "arquivo"	=> $filename['file_path'] . $filename['file_name'],
         ));
         
         */
        /*$nome_arquivo='/var/fatura_voz.csv';
        
        $fp=fopen($nome_arquivo,"w");
        fwrite($fp,$filename['file_name']);
        fclose($fp);*/
        print ("&nbsp&nbsp&nbsp&nbsp".$filename['file_name']);
        return $filename;
    }
    
    
    
    
    
    //verifica a forma de pagamento do cliente já existente na tabela clientes
    public function retormaFormaPagamentoClienteExistente($id){
        $result = $this->dao->consultaFormadePagamentoAtualIDCliente($id);
        return $result;
    }
    
    /**
     * Verifica se já existe o cliente cadastrado na tabela clientes para preencher os campos do editar
     */
    public function consultaClienteExistente($documento){
        $retornoClientes = $this->dao->consultaClienteCadastrado($documento);
        return $retornoClientes;
    }
    
    
    
    
    
    
    
    public function mask($val, $mask) {
        $maskared = '';
        $k = 0;
        
        $str = str_replace(" ","",$val);
        $val = $str;
        for($i = 0; $i <= strlen ( $mask ) - 1; $i ++) {
            if ($mask [$i] == '#') {
                
                if (isset ( $val [$k] ))
                    $maskared .= $val [$k ++];
            } else {
                if (isset ( $mask [$i] ))
                    $maskared .= $mask [$i];
            }
        }
        return $maskared;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * STI 84969
     *
     * @param int $contrato
     * @throws Exception
     */
    public function verificarParalisacaoFaturamento($contrato){
        
        try {
            
            if(empty($contrato)){
                $cod_erro = 3;
                throw new Exception('O contrato deve ser informado para verificar se há paralisação do faturamento.');
            }
            
            //verificar os contratos que possuem paralisaçao
            $retornoParalisacao = $this->dao->verificarParalisacaoFaturamento($contrato);
            
            if(is_array($retornoParalisacao)){
                
                for($i = 0; $i < count($retornoParalisacao); $i++){
                    
                    $virgula = ', ';
                    
                    if(count($retornoParalisacao) == 1){
                        $virgula = '';
                    }else if($i == (count($retornoParalisacao)-1)){
                        $virgula = '';
                    }
                    
                    $contratos_paralisados .= $retornoParalisacao[$i]['contrato'].$virgula;
                    
                }
                
                $erro['tipo_erro'] = 2;
                $erro['msg']   = utf8_encode("Não é possível realizar a transferência de titularidade, o(s) veículo(s) encontra(m)-se em paralisação do faturamento. Contrato(s): " . $contratos_paralisados);
                
                return $erro;
                
            }
            
            return ;
            
        } catch (Exception $e) {
            
            $erro['tipo_erro'] = $cod_erro;
            $erro['msg']       = utf8_encode($e->getMessage());
            
            return $erro;
        }
        
    }
    
    
    
    
    
    
    //@@@Metodo que cadastra a solicitação de transferencia e faz a verificação do serasa e transferencia titularidade
    public function cadSolicitacaoTransferencia() {
        //recebe os contratos selecionados
        $tipoContratos = $_POST ['chk_oid'];
        
        //retorna a quantidade de contratos
        $totalContratos = count($tipoContratos);
        
        $valorTaxasContratos = 0;
        
        for($i = 0; $i < count($tipoContratos);$i++) {
            $explodeContratos = explode("-",$tipoContratos[$i]);
            if(is_numeric($explodeContratos[1])) {
                $contratos[] = $explodeContratos[1];
            }
        }
        //busca os valores das taxa de transferencia da tabela dominios
        $taxaTrans = $this->dao->taxaTransferencia();
        
        foreach ($taxaTrans as $row){
            $qtdaContrato = explode(",",$row->valvalor);
            
            //verifica a quantidade de contratos
            if($totalContratos >= $qtdaContrato[0] && $totalContratos <= $qtdaContrato[1]) {
                //retorna o valor dos contratos passando o id da tabela registro para a tabela valor
                $valorTaxasContratos = $this->dao->taxaTransferenciaPorID($row->valregoid);
            }
            /*
             * caso a quantidade de contrato for entre 1 e 9 a taxa será cobrado por veiculo
             * senão vai ser cobrada uma taxa unica
             */
            if($qtdaContrato <= 9 && $qtdaContrato >= 1) {
                $texto = 'por veículo';
            }else{
                $texto = 'taxa única';
            }
        }
        $valorTotalCompra = 0;
        /*
         * Lista todos contratos e pesquisa os valores de acessorios, locação e monitoramento
         */
        for($i = 0; $i < count ( $contratos); $i ++) {
            //retorna o valor de locação acessorio
            $valor = $this->dao->ValorLocacaoAcessorios($contratos[$i]);
            //retorna o valor de locação equipamento
            $valor2 = $this->dao->ValorLocacaoEquipamento($contratos[$i]);
            //retorna o valor de monitoramento
            $valor3 = $this->dao->ValorMonitoramento($contratos[$i]);
            
            if($valor != 0) {
                $valorTotalCompra += $valor;
            }
            
            if($valor2 != 0) {
                $valorTotalCompra += $valor2;
            }
            
            if($valor3 != 0) {
                $valorTotalCompra += $valor3;
            }
        }
        
        $valorTotalCompra =  number_format($valorTotalCompra, 2);
        $valorTotalCompra = number_format($valorTotalCompra, 2, ',', '.');
        
        $cnpjcpf = $_POST ['cnpjcpf'];
        
        $cnpjcpf = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $cnpjcpf);
        $telefone1 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST['contato1']);
        $telefone2 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST['contato2']);
        
        
        $cd_usuario = $_SESSION ['usuario'] ['oid'];
        
        
        //verifica se existe titulos pendentes $ptrasfoid_status = 1 existe titulos pendentes e $ptrasfoid_status = 2 não existe titulos pendentes
        if (isset ( $_POST ['chk_all_titulos_pendentes'] ) || ! empty ( $_POST ['chk_all_titulos_pendentes'] ) || $_POST ['chk_all_titulos_pendentes'] != null) {
            $ptrasfoid_status = 1;
        } else {
            $ptrasfoid_status = 2;
        }
        
        //tipo de pessoa fisica ou jurifica
        $tipoPessoa = $_POST['prptipo_pessoa'];
        
        //criando o array com os dados passando para o serasa
        $dadosAnalise = array (
            'formaPagamento' => 1,
            'tipoPessoa' => $tipoPessoa,
            'tipoProposta' => 1,
            'tipoContrato' => 0,
            'qtdEquipamentos' => 1,
            'valorTotalCompra' => $valorTotalCompra
        );
        
        //retorna a pesquisa feita no serasa
        $sretornoSerasa = GestorCredito::analisaCredito($cnpjcpf, $dadosAnalise,'P');
        
        //$sretornoSerasa = 2;
        //$ptrasfoid_status = 2;
        //$sretornoSerasa->dados ['prppsfoidgestor'] = 2;
        
        /*
         * se o retorno do serasa for igual a 3 cliente foi reprovada e transferencia titularidade
         * é finaliza, senão o status vai ser em andamento
         */
        if($sretornoSerasa->dados ['prppsfoidgestor'] == 3){
            $statusconclusaoproposta = 'F';
        }else{
            $statusconclusaoproposta = 'A';
        }
        
        //caso o gestor de credito retornar vazio vai dar a mensagem que ocorreu problema no processo do serasa
        if(!isset($sretornoSerasa->dados ['prppsfoidgestor'])||
            empty($sretornoSerasa->dados ['prppsfoidgestor']) ||
            $sretornoSerasa->dados ['prppsfoidgestor'] == ''){
                
                $this->msgErro = "Erro ao processar problemas no retorno do serasa, entre em contato com o suporte.";
                echo json_encode ( array (
                    "status" => "error",
                    "message" => $this->msgErro,
                    "redirect" => ""
                ) );
                return;
                
        }
        
        //caso o titulos pendentes estiver vazio vai dar a mensagem que ocorreu problema
        if(!isset($ptrasfoid_status) || empty($ptrasfoid_status) || $ptrasfoid_status == ''){
            $this->msgErro = "Erro ao processar problemas no status de divida, entre em contato com o suporte.";
            echo json_encode ( array (
                "status" => "error",
                "message" => $this->msgErro,
                "redirect" => ""
            ) );
            return;
        }
        
        //recebo os valores retornados pelo serasa
        $retornoSerasa .= $sretornoSerasa->dados['prpresultado_aciap']." ".
            $sretornoSerasa->dados['CHAVE']." ".
            $sretornoSerasa->dados['MSGE_TIPO']." ".
            $sretornoSerasa->dados['MSGE_DESC']." ".
            $sretornoSerasa->dados['LIMITE']." ".
            $sretornoSerasa->dados['POLITICA']." ".
            $sretornoSerasa->dados['RELATORIO']."".
            $sretornoSerasa->dados['ERRO']." ".
            $sretornoSerasa->dados['DADOSPOLITICA']." ".
            $sretornoSerasa->dados['TIPO_DEC']['prpobservacao_financeiro'];
            
            
            
            /*
             * cria o array com os dados do cliente e o retorno do serasa
             */
            $dados = array (
                'tel_tit_anterior' =>$_POST ['telefone1_titular'],
                'tel2_tit_anterior' =>$_POST ['telefone2_titular'],
                'ptraemail_tit_anterior' =>$_POST ['email_titular'],
                'ptraresp_tit_anterior' => $_POST['responsavel_titular'],
                'tipo_pessoa' => $tipoPessoa,
                'cpfcnpj' => $cnpjcpf,
                'nome' => $_POST ['nomerazaosocial'],
                'contato' => $_POST ['contato'],
                'contato1' => $telefone1,
                'contato2' => $telefone2,
                'email' => $_POST ['email'],
                'ptramotivo_trans' => $_POST ['motivo_Transferencia'],
                'idusuario' => $this->cd_usuario,
                'ptrasfoid_analise' => $sretornoSerasa->dados ['prppsfoidgestor'],
                //'ptrasfoid_analise' => 2,
                'ptrasfoid_status' => $ptrasfoid_status,
                //'ptrasfoid_status' => 2,
                'contratos' => $contratos,
                'ptraresultado_serasa' =>utf8_encode(trim($retornoSerasa)),
                'statusconclusaoproposta'=>$statusconclusaoproposta
            );
            
            
            //envia os dados para cadastrar a proposta e retorna true ou false;
            $resultado = $this->dao->cadastraSolicitacaoTransferencia ( $dados );
            
            //verifica se o retorno do cadastro foi true ou false se foi falso retorna msg de erro
            if($resultado == 0){
                $this->msgErro = "Erro ao processar, entre em contato com o suporte.";
                echo json_encode ( array (
                    "status" => "error",
                    "message" => $this->msgErro,
                    "redirect" => ""
                ) );
                return;
                
            }else{
                
                if ($sretornoSerasa->dados ['prppsfoidgestor'] != 2 && $ptrasfoid_status != 2 && ($sretornoSerasa->dados ['prppsfoidgestor'] != 3 || $ptrasfoid_status != 3 )) {
                    
                    echo json_encode ( array (
                        "status" => "msgsucesso",
                        "proposta" => $resultado,
                        "message" => utf8_encode("Proposta nº {$resultado} gravada com sucesso, proposta enviada para aprovação de transferência de dívida e aprovação de crédito manual."),
                        "redirect" => ""
                            ) );
                    return;
                    
                } else if ($sretornoSerasa->dados ['prppsfoidgestor'] == 2 && $ptrasfoid_status != 2 && $ptrasfoid_status != 3) {
                    
                    
                    echo json_encode ( array (
                        "status" => "msgsucesso",
                        "proposta" => $resultado,
                        "message" => utf8_encode("Proposta nº {$resultado} gravada com sucesso, proposta enviada para aprovação de transferência de dívida."),
                        "redirect" => ""
                            ) );
                    return;
                    
                } else if ($sretornoSerasa->dados ['prppsfoidgestor'] != 2 && $sretornoSerasa->dados ['prppsfoidgestor'] != 3 &&  $ptrasfoid_status == 2) {
                    
                    echo json_encode ( array (
                        "status" => "msgsucesso",
                        "proposta" => $resultado,
                        "message" => utf8_encode("Proposta nº {$resultado} gravada com sucesso, proposta enviada para aprovação de crédito manual."),
                        "redirect" => ""
                            ) );
                    return;
                }else if($sretornoSerasa->dados ['prppsfoidgestor'] == 3 ||  $ptrasfoid_status == 3){
                    
                    $titulo = "Transferência Titularidade Contratos SASCAR";
                    
                    $assunto= "Prezado cliente!
                        
								Informamos que a sua solicitação de transferência de titularidade não foi
							    concluída devido crédito negado ou não autorizada a transferência das faturas
							    ao novo proprietário.<br />
                        
								Em caso de dúvidas, estamos à disposição através do e-mail <br />
							    transferencia.titularidade@sascar.com.br";
                    
                    $email = $this->enviaEmail($_POST ['email'],$_POST ['email_titular'],$titulo,$assunto);
                    
                    if($email) {
                        echo json_encode ( array (
                            "status" => "msgsucesso",
                            "proposta" => $resultado,
                            "message" => utf8_encode("Proposta foi reprovada,foi enviado e-mail para o  cliente."),
                            "redirect" => ""
                        ) );
                        return;
                    }else{
                        echo json_encode ( array (
                            "status" => "error",
                            "message" => 'Proposta foi reprovada! Porem falha ao enviar e-mail!',
                            "redirect" => ""
                        ) );
                        return;
                    }
                    
                }else if($sretornoSerasa->dados ['prppsfoidgestor'] == 2 && $ptrasfoid_status == 2){
                    
                    $titulo = "Documentos para Transferência de Titularidade Contratos SASCAR";
                    if($tipoPessoa == 'F') {
                        $assunto = "Prezado Cliente!
                        
				 Para realizarmos a transferência de titularidade será cobrado do novo titular uma taxa no valor de R$ $valorTaxasContratos ($texto) mais os valores de locação
				do equipamento e acessórios, caso haja, ambos clientes não pode ter nenhuma pendência financeira externa ou interna com a Sascar.<br />
				
				É necessário o envio da documentação abaixo do NOVO TITULAR:<br />
				
				- Cópia de RG e CPF ou CNH;<br />
				- Cópia do comprovante de residência atualizado;<br />
				- Cópia do documento do veículo.<br />
				- Telefones e e-mail para contato<br />
				
				O processo de transferência de titularidade só será iniciado se for enviado toda a documentação corretamente, caso contrário o antigo proprietário permanecerá com a responsabilidade dos
				pagamentos e o contrato em seu nome.<br />
				
				No aguardo do retorno.<br />
				
				Att,";
                        
                    }else{
                        $assunto= "Prezado Cliente!
                        
				 Para realizarmos a transferência de titularidade será cobrado do novo titular uma taxa no valor de R$ $valorTaxasContratos ($texto) mais os valores de locação
				do equipamento e acessórios, caso haja, ambos clientes não pode ter nenhuma pendência financeira externa ou interna com a Sascar.<br />
				
				É necessário o envio da documentação abaixo do NOVO TITULAR:<br />
				
				
				- Cópia do contrato social;<br />
				- Cópia do documento do veículo.;<br />
				- Telefones e e-mail para contato;<br />
				
				O processo de transferência de titularidade só será iniciado se for enviado toda a documentação corretamente, caso contrário o antigo proprietário permanecerá com a responsabilidade dos
				pagamentos e o contrato em seu nome.<br />
				
				No aguardo do retorno.<br />
				
				Att,";
                    }
                    
                    $email = $this->enviaEmail($_POST ['email'],$_POST ['email_titular'],$titulo,$assunto);
                    
                    if($email) {
                        echo json_encode ( array (
                            "status" => "msgsucesso",
                            "proposta" => $resultado,
                            "message" => utf8_encode("Proposta nº $resultado gravada com sucesso, foi enviado e-mail para o novo cliente com a listagem da documentação necessária para conclusão da transferência."),
                            "redirect" => ""
                        ) );
                        return;
                    }else{
                        echo json_encode ( array (
                            "status" => "error",
                            "message" => 'Cadastro efetuado com susseco! Porem falha ao enviar e-mail!',
                            "redirect" => ""
                        ) );
                        return;
                    }
                    
                }
                
            }
            
            
            //@@@@
            include (_MODULEDIR_ . 'Financas/View/fin_transferencia_titularidade/novo_cliente' . (isset ( $_POST ['ajax'] ) ? '.ajax' : '') . '.php');
    }
    
    
    
    
    
    
    
    //metodo que vai buscar na dao e retorna todos os estados
    public function retornaEstados() {
        $resultEstados = $this->dao->Estados ();
        
        return $resultEstados;
    }
    
    //metodo que vai buscar na dao e retorna todos os paises
    public function retornaPaises() {
        $resultPaises = $this->dao->Paises ();
        
        return $resultPaises;
    }
    
    //metodo para cadastrar pessoas autorizada
    public function cadPessoaAutorizada() {
        $cpf = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prtcpf_aut']);
        $telefone1 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prtfone_res_aut']);
        $telefone2 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prtfone_com_aut']);
        $telefone3 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prtfone_cel_aut']);
        $dados = array (
            'ptraoid' => $_POST ['ptraoid'],
            'prtnome_aut' => $_POST ['prtnome_aut'],
            'prtcpf_aut' => $cpf,
            'prtrg_aut' => $_POST ['prtrg_aut'],
            'prtfone_res_aut' => $telefone1,
            'prtfone_com_aut' => $telefone2,
            'prtfone_cel_aut' =>$telefone3 ,
            'prtid_nextel_aut' => $_POST ['prtid_nextel_aut']
        );
        
        $return = $this->dao->insertPessoaAutorizada ( $dados );
        
        if(!$return) {
            echo json_encode(array("tipo_msg" => "e", "msg" => "Problemas para inserir pessoas autorizadas."));
            exit;
        }
        $result = $this->listaPessoa($_POST ['ptraoid']);
        if($result != null){
            //chama o componente passando o retorno do banco e monta grid na tela
            $this->TitularidadeView->getComponenteListaPessoaAutorizada($result);
            exit;
        } else{
            echo json_encode(array("tipo_msg" => "i", "msg" => "Nenhum resultado encontrado."));
            exit;
        }
    }
    
    //lista pessoas autorizadas passando o id da proposta
    public function listaPessoa($id) {
        $resultado = $this->dao->listaPessoaDaoIdProposta ( $id );
        
        return $resultado;
    }
    
    //lsita pessoas autorizadas passando o id
    public function listaPessoaID($id){
        $result = $this->dao->listaPessoaDaoId($id);
        return $result;
    }
    
    //edita pessoas autorizadas
    public function EdicaoPessoaAutorizada(){
        $idPessoaAutorizada = $_POST['id'];
        
        $resultEdicao = $this->listaPessoaID($idPessoaAutorizada);
        
        
        echo json_encode(array("tipo_msg" => "i",
            "ptpaoid" =>$resultEdicao['ptpaoid'],
            "ptpaptraoid" =>$resultEdicao['ptpaptraoid'],
            "nome" =>$resultEdicao['ptpanome'],
            "cpf" => $resultEdicao['ptpacpf'],
            "rg" => $resultEdicao['ptparg'],
            "foneresidencial"=>$resultEdicao['ptpafone_residencial'],
            "fonecelular" => $resultEdicao['ptpafone_celular'],
            "fonecomercial" =>$resultEdicao['ptpafone_comercial'],
            "nextel" =>$resultEdicao['ptpaidnextel']
        ));
        exit;
    }
    
    //chama o metodo para atualizar pessoas autorizada
    public function atualizaPessoaAutorizada(){
        
        
        $cpf = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST['ptpacpf']);
        $telefone1 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST['ptpafone_res_pessoa_auto']);
        $telefone2 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST['ptpafone_com_pessoa_auto']);
        $telefone3 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST['ptpafone_cel_pessoa_auto']);
        
        
        
        $dados = Array(
            'ptpaoid' =>$_POST['ptpaoid'],
            'ptpanome' => $_POST['ptpanome'],
            'ptpacpf' =>$cpf,
            'ptparg' => $_POST['ptparg'],
            'ptpafone_residencial' => $telefone1,
            'ptpafone_comercial' => $telefone2,
            'ptpafone_celular' => $telefone3,
            'ptpaidnextel' => $_POST['ptpaid_nextel_pessoa_auto']
            );
        
        $result = $this->dao->updatecontatoPessoasAutorizadas($dados);
        
        if(!$result) {
            
            echo json_encode(array("tipo_msg" => "i", "msg" => "Problemas para atualizar registro de contato de pessoas autorizadas."));
            exit;
        }else{
            
            $resultPessoasAutor = $this->listaPessoa($_POST['ptpaptraoid']);
            if($result != null){
                $this->TitularidadeView->getComponenteListaPessoaAutorizada($resultPessoasAutor);
                exit;
            } else{
                echo json_encode(array("tipo_msg" => "i", "msg" => "Nenhum resultado encontrado."));
                exit;
            }
        }
        
    }
    
    //metodo para cadastrar contato de emergencia
    public function cadContatoEmergencia(){
        
        $telefone1 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prcfone_res_cont_emerg']);
        $telefone2 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prcfone_com_cont_emerg']);
        $telefone3 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prcfone_cel_cont_emerg']);
        
        $dados = array(
            'ptceptraoid' =>$_POST['ptraoid'],
            'ptcenome' => $_POST['prcnome_cont_emerg'],
            'ptcefone_residencial' => $telefone1,
            'ptcefone_comercial' => $telefone2,
            'ptcefone_celular' => $telefone3,
            'ptceidnextel' => $_POST['prcid_nextel_cont_emerg']
            
        );
        
        $return = $this->dao->insertContatoEmergencia($dados);
        
        if(!$return) {
            echo json_encode(array("tipo_msg" => "e", "msg" => "Problemas para inserir contato emergencia."));
            exit;
        }
        
        $result = $this->listaContatoEmergencia($_POST ['ptraoid']);
        if($result != null){
            //chama o componente passando o retorno do banco e monta grid na tela
            $this->TitularidadeView->getComponenteListaContatosEmergencia($result);
            exit;
        } else{
            echo json_encode(array("tipo_msg" => "i", "msg" => "Nenhum resultado encontrado."));
            exit;
        }
    }
    
    //lista o contato emergencia passando o id da proposta
    public function listaContatoEmergencia($id){
        $result = $this->dao->listaContatoEmergencia($id);
        return $result;
    }
    
    //metodo para editar o contato de emergencia
    public function EdicaoEmergencia(){
        $idInstalacao = $_POST['id'];
        
        $resultEdicao = $this->listContatoEmergenciaID($idInstalacao);
        
        echo json_encode(array("tipo_msg" => "i",
            "ptceoid" =>$resultEdicao['ptceoid'],
            "ptceptraoid" =>$resultEdicao['ptceptraoid'],
            "nome" =>$resultEdicao['ptcenome'],
            "foneresidencial"=>$resultEdicao['ptcefone_residencial'],
            "fonecelular" => $resultEdicao['ptcefone_celular'],
            "fonecomercial" =>$resultEdicao['ptcefone_comercial'],
            "nextel" =>$resultEdicao['ptceidnextel']
        ));
        exit;
    }
    
    //lista o contato emergencia passando i id
    public function listContatoEmergenciaID($id){
        $result = $this->dao->listaContatoEmergenciaID($id);
        return $result;
    }
    
    
    //atualiza o contato de emergencia
    public function atualizaContatoEmergencia(){
        
        
        
        $telefone1 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST['prcfone_res_cont_emerg']);
        $telefone2 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST['prcfone_com_cont_emerg']);
        $telefone3 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST['prcfone_cel_cont_emerg']);
        
        $dados = Array(
            'ptceoid' => $_POST['ptceoid'],
            'prcnome_cont_emerg' => $_POST['prcnome_cont_emerg'],
            'prcfone_res_cont_emerg' => $telefone1,
            'prcfone_com_cont_emerg' => $telefone2,
            'prcfone_cel_cont_emerg' => $telefone3,
            'prcid_nextel_cont_emerg' => $_POST['prcid_nextel_cont_emerg']
            );
        
        
        $result = $this->dao->updatecontatoEmergencia($dados);
        
        if(!$result) {
            
            echo json_encode(array("tipo_msg" => "i", "msg" => "Problemas para atualizar registro de contato instalação emergencia."));
            exit;
        }else{
            
            $resultContatoEmerg = $this->listaContatoEmergencia($_POST['id_prop_contEmerg']);
            
            if($result != null){
                $this->TitularidadeView->getComponenteListaContatosEmergencia($resultContatoEmerg);
                exit;
            } else{
                echo json_encode(array("tipo_msg" => "i", "msg" => "Nenhum resultado encontrado."));
                exit;
            }
        }
        
    }
    
    //cadastra o contato instalação
    public function cadContatoInstalacao(){
        
        $telefone1 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prcfone_res_cont_inst']);
        $telefone2 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prcfone_com_cont_inst']);
        $telefone3 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prcfone_cel_cont_inst']);
        
        $dados = array(
            'ptciptraoid' =>$_POST['ptraoid'],
            'ptcinome' => $_POST['prcnome_cont_inst'],
            'ptcifone_residencial' => $telefone1,
            'ptcifone_comercial' => $telefone2,
            'ptcifone_celular' => $telefone3,
            'ptcidnextel' => $_POST['prcid_nextel_cont_inst']
            
        );
        
        
        $return = $this->dao->insertContatoInstalacao($dados);
        
        
        if(!$return) {
            echo json_encode(array("tipo_msg" => "e", "msg" => "Problemas para inserir contato emergencia."));
            exit;
        }
        
        $result = $this->listaContatoInstalacao($_POST ['ptraoid']);
        if($result != null){
            $this->TitularidadeView->getComponenteListaContatoInstalacao($result);
            exit;
        } else{
            echo json_encode(array("tipo_msg" => "i", "msg" => "Problemas para cadastrar instalação assistencia."));
            exit;
        }
    }
    
    //lista o contato de instalacao passando o id da proposta
    public function listaContatoInstalacao($id){
        $result = $this->dao->listaContatoInstalacaoIDProposta($id);
        return $result;
    }
    
    //lista o contrato instalacao passando o id
    public function listContatoInstalacaoID($id){
        $result = $this->dao->ContatoInstalacaoID($id);
        return $result;
    }
    
    //metodo que edita o contato de instalacao
    public function EdicaoContatoInstalacao(){
        $idInstalacao = $_POST['id'];
        
        $resultEdicao = $this->listContatoInstalacaoID($idInstalacao);
        
        echo json_encode(array("tipo_msg" => "i",
            "ptcioid" =>$resultEdicao['ptcioid'],
            "ptciptraoid" =>$resultEdicao['ptciptraoid'],
            "nome" =>$resultEdicao['ptcinome'],
            "foneresidencial"=>$resultEdicao['ptcifone_residencial'],
            "fonecelular" => $resultEdicao['ptcifone_celular'],
            "fonecomercial" =>$resultEdicao['ptcifone_comercial'],
            "nextel" =>$resultEdicao['ptcidnextel']
        ));
        exit;
    }
    
    
    
    //metodo para atualizar o contato de instalacao
    public function atualizaContatoInstalacao(){
        $telefone1 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST['prcfone_res_cont_inst']);
        $telefone2 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST['prcfone_com_cont_inst']);
        $telefone3 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST['prcfone_cel_cont_inst']);
        
        $dados = Array(
            'ptraoid' => $_POST['ptraoid'],
            'prcnome_cont_inst' => $_POST['prcnome_cont_inst'],
            'prcfone_res_cont_inst' => $telefone1,
            'prcfone_com_cont_inst' => $telefone2,
            'prcfone_cel_cont_inst' => $telefone3,
            'prcid_nextel_cont_inst' => $_POST['prcid_nextel_cont_inst']
            );
        
        $result = $this->dao->updatecontatoInstalacao($dados);
        
        if(!$result) {
            
            echo json_encode(array("tipo_msg" => "i", "msg" => "Problemas para atualizar registro de contato instalação assistência."));
            exit;
        }else{
            
            $resultContatoInstalacao = $this->listaContatoInstalacao($_POST['id_prop_InstalAssis']);
            if($result != null){
                $this->TitularidadeView->getComponenteListaContatoInstalacao($resultContatoInstalacao);
                exit;
            } else{
                echo json_encode(array("tipo_msg" => "i", "msg" => "Nenhum resultado encontrado."));
                exit;
            }
        }
        
    }
    
    //metodo que ira anexar a arquivo e salvar as informações no banco
    public function anexarArquivos(){
        
        //$temp_name = str_replace(" ", "_", $_FILES['arquivo']['tmp_name']);
        $timestamp = time(); // Salva o timestamp atual numa variável
        $dataHora =  date('dmYHis', $timestamp); // Exibe DD/MM/YYYY HH:MM:SS em função de um timestamp
        $ptraoid = $_POST['ptraoid'];
        $descricao = $_POST['descricao'];
        
        $arquivo = str_replace(" ", "", $_FILES['arquivo']['name']);
        
        $arquivoNome = $_POST['ptraoid'].'-'.$dataHora."-".$arquivo;
        
        $file_path = $PATH =  _SITEDIR_ ."faturamento/transferencia_titularidade/".$arquivoNome;
        
        $result = move_uploaded_file($_FILES['arquivo']['tmp_name'], $file_path);
        
        $dados = array(
            'ptaptraoid' => $ptraoid,
            'ptanm_arquivo' => $arquivoNome,
            'ptadescricao' => $descricao,
            'ptratipo_anexo' => 'false',
            'ptrausuoid' =>$this->cd_usuario
        );
        
        if ($result) {
            
            $resultadoAnexos = $this->dao->inserirAnexosProposta($dados);
            
            if(!$resultadoAnexos) {
                unlink($file_path);
                echo json_encode(array("tipo_msg" => "i", "msg" => "Problemas para anexar arquivos."));
                exit;
            }else{
                $resultAnexo = $this->listaAnexosProposta($ptraoid);
                
                if($resultAnexo != null){
                    $this->TitularidadeView->getComponenteListaAnexosProposta($resultAnexo);
                    exit;
                } else{
                    echo json_encode(array("tipo_msg" => "i", "msg" => "Nenhum resultado encontrado."));
                    exit;
                }
                
            }
        }else{
            echo json_encode(array("tipo_msg" => "i", "msg" => "Problemas para fazer upload do arquivo."));
            exit;
        }
        
    }
    
    //metodo que ira anexar a carta e salvar as informações no banco
    public function anexarCarta(){
        
        
        //$temp_name = str_replace(" ", "_", $_FILES['arquivo']['tmp_name']);
        $timestamp = time(); // Salva o timestamp atual numa variável
        $dataHora =  date('dmYHis', $timestamp); // Exibe DD/MM/YYYY HH:MM:SS em função de um timestamp
        $ptraoid = $_POST['ptraoid'];
        $descricao = $_POST['descricao'];
        
        $arquivo = str_replace(" ", "", $_FILES['arquivo']['name']);
        
        $arquivoNome = $_POST['ptraoid'].'-'.$dataHora."-".$arquivo;
        
        $qtdaAnexoCarta = $this->listaAnexosCarta($ptraoid);
        
        if(!empty($qtdaAnexoCarta) || $qtdaAnexoCarta != null){
            $qtdaAnexoCarta = $this->listaAnexosCarta($ptraoid);
            $resultado = $this->TitularidadeView->getComponenteListaCarta($qtdaAnexoCarta);
            echo json_encode(array("tipo_msg" => "i", "msg" => utf8_encode("É permitido o anexo de apenas uma carta."),"html"=>$resultado));
            exit;
        }else{
            
            $file_path = $PATH =  _SITEDIR_ ."faturamento/transferencia_titularidade/".$arquivoNome;
            $result = move_uploaded_file($_FILES['arquivo']['tmp_name'], $file_path);
            
            $dados = array(
                'ptaptraoid' => $ptraoid,
                'ptanm_arquivo' => $arquivoNome,
                'ptadescricao' => $descricao,
                'ptratipo_anexo' => 'true',
                'ptrausuoid' =>$this->cd_usuario
            );
            
            if ($result) {
                
                $resultadoAnexos = $this->dao->inserirAnexosProposta($dados);
                
                if(!$resultadoAnexos) {
                    unlink($file_path);
                    echo json_encode(array("tipo_msg" => "i", "msg" => "Problemas para anexar carta."));
                    exit;
                }else{
                    $qtdaAnexoCarta = $this->listaAnexosCarta($ptraoid);
                    
                    if($qtdaAnexoCarta != null){
                        $resultadoAnexo = $this->TitularidadeView->getComponenteListaCarta($qtdaAnexoCarta);
                        echo json_encode(array("tipo_msg" => "retorno", "msg" => "","html"=>$resultadoAnexo));
                        exit;
                    } else{
                        echo json_encode(array("tipo_msg" => "i", "msg" => "Nenhum resultado encontrado.","html"=>""));
                        exit;
                    }
                    
                }
            }else{
                echo json_encode(array("tipo_msg" => "i", "msg" => "Problemas para fazer upload do arquivo.","html"=>""));
                exit;
            }
        }
        
        
    }
    
    //lista todos os anexos da proposta passando o id
    public function listaAnexosProposta($id){
        $result = $this->dao->listAnexosPropostaId($id);
        return $result;
    }
    
    //retorna a carta anexada passando o id
    public function listaAnexosCarta($id){
        $result = $this->dao->listaAnexoCartaId($id);
        return $result;
    }
    
    
    //metodo chamando para enviar email
    public function enviaEmail($email,$email2,$titulo,$assunto){
        
        
        # Enviar o e-mail
        $mail = new PHPMailer();
        $mail->ClearAllRecipients();
        $mail->IsSMTP();
        $mail->From = 'sascar@sascar.com.br';
        $mail ->FromName = "Sascar";
        //$mail->SMTPDebug = true;
        $mail->AddAddress($email);
        //$mail->AddAddress('teste_desenv@sascar.com.br');
        $mail->AddAddress($email2);
        $mail->Subject = $titulo;
        $mail->MsgHTML($assunto);
        
        if (! $mail->Send ()) {
            return false;
            die();
        }
        
        return true;
    }
    
    
    
    
    
    
    
    
    
    
    //@@@metodo que reprova a transferencia do serasa feito manualmente
    public  function reprovaSerasaManual(){
        
        $dados = array(
            'idProposta' =>$_POST['idProposta'],
            'motivo' => $_POST['motivo'],
            'idUsuario' => $this->cd_usuario
        );
        //chama o metodo da dao e atualiza o status do banco para reprovar a transferencia dde analise de creidito id da proposta o motivo e usuario que reprovou
        $retornoReprovaAnaliseCredito = $this->dao->atualizaSolicitacaoTransferenciaCreditoSerasa($dados);
        
        if($retornoReprovaAnaliseCredito == true) {
            
            //retorna todas as solicitações com as informações e envia email para o cliente informando que foi reprovado
            $retorno = $this->dao->listSolicitacaoTransferenciaPorID($_POST['idProposta']);
            
            $email = $this->enviaEmail($retorno['ptraemail_tit_anterior'],$retorno['ptracontato_email'],$titulo,$assunto);
            
            $titulo = "Transferência Titularidade Contratos SASCAR";
            
            $assunto= "Prezado cliente.!
                
								Informamos que a sua solicitação de transferência de titularidade não foi
							    concluída devido crédito negado ou não autorizada a transferência das faturas
							    ao novo proprietário.<br />
                
								Em caso de dúvidas, estamos à disposição através do e-mail <br />
							    transferencia.titularidade@sascar.com.br";
            
            if($email) {
                echo json_encode ( array (
                    "status" => "msgsucesso",
                    "proposta" => $retornoReprovaAnaliseCredito,
                    "message" => utf8_encode("Análise de crédito reprovada"),
                    'statusanalise' => $retorno ['ptrasfoid_analise_credito'],
                    "redirect" => ""
                ) );
                return;
            }else{
                echo json_encode ( array (
                    "status" => "msgsucesso",
                    "proposta" => $retornoReprovaAnaliseCredito,
                    "message" => utf8_encode("Proposta foi reprovada! Porem falha ao enviar e-mail!"),
                    'statusanalise' => $retorno ['ptrasfoid_analise_credito'],
                    "redirect" => ""
                ) );
                return;
            }
        }else{
            echo json_encode ( array (
                "status" => "msgerro",
                "proposta" => $retornoReprovaAnaliseCredito,
                "message" => utf8_encode("Problemas para reprovar analise de crédito  tente novamente ou entre em contato com o suporte"),
                'statusanalise' => '',
                "redirect" => ""
            ) );
        }
    }
    
    //@@@metodo que vai reprovar a transferencia de titularidade
    public function reprovaTransferenciaTitularidade(){
        
        $dados = array(
            'idProposta' =>$_POST['idProposta'],
            'motivo' => $_POST['motivo'],
            'idUsuario' => $this->cd_usuario
        );
        
        //chama o metodo da dao e atualiza o status do banco para reprovar a transferencia passando id da proposta o motivo e usuario que reprovou
        $retornoReprovaTransDivida = $this->dao->atualizaSolicitacaoTransferenciaTitularidade($dados);
        
        if($retornoReprovaTransDivida == true) {
            
            //retorna todas as solicitações com as informações e envia email para o cliente informando que foi reprovado
            $retorno = $this->dao->listSolicitacaoTransferenciaPorID($_POST['idProposta']);
            
            $titulo = "Transferência Titularidade Contratos SASCAR";
            
            $assunto= "Prezado cliente.!
                
								Informamos que a sua solicitação de transferência de titularidade não foi
							    concluída devido crédito negado ou não autorizada a transferência das faturas
							    ao novo proprietário.<br />
                
								Em caso de dúvidas, estamos à disposição através do e-mail <br />
							    transferencia.titularidade@sascar.com.br";
            
            $email = $this->enviaEmail($retorno['ptraemail_tit_anterior'],$retorno['ptracontato_email'],$titulo,$assunto);
            
            if($email) {
                echo json_encode ( array (
                    "status" => "msgsucesso",
                    "proposta" =>  $retornoReprovaTransDivida,
                    "message" => utf8_encode("Análise de crédito reprovada"),
                    'statustitularidade' => $retorno ['ptrasfoid_analise_divida'],
                    "redirect" => ""
                ) );
                return;
            }else{
                echo json_encode ( array (
                    "status" => "msgsucesso",
                    "proposta" => $retornoReprovaAnaliseCredito,
                    "message" => utf8_encode("Proposta foi reprovada! Porem falha ao enviar e-mail!"),
                    'statustitularidade' => $retorno ['ptrasfoid_analise_divida'],
                    "redirect" => ""
                ) );
                return;
            }
            
            
        }else{
            echo json_encode ( array (
                "status" => "msgerro",
                "proposta" => $retornoReprovaTransDivida,
                "message" => utf8_encode("Problemas reprova transferencia titularidade tente novamente ou entre em contato com o suporte"),
                'statustitularidade' => '',
                "redirect" => ""
            ) );
        }
    }
    
    //@@@@metodo para aprova o serasa manualmente
    public function aprovaSerasaManual(){
        
        $dados = array(
            'idProposta' =>$_POST['idProposta'],
            'idUsuario' => $this->cd_usuario
        );
        
        //muda o status da tabela para aprovado passando o id da proposta e o usuario que aprovou
        $retornoSerasaManual = $this->dao->aprovaTransferenciaCreditoSerasa($dados);
        
        //caso retorna true
        if($retornoSerasaManual == true) {
            
            //retorna todas as solicitações da transferencia
            $retorno = $this->dao->listSolicitacaoTransferenciaPorID($_POST['idProposta']);
            
            
            //se analise de credito e divida ambos estiverem aprovado vai verificar o valor da taxa
            if($retorno ['ptrasfoid_analise_credito'] == 2 && $retorno ['ptrasfoid_analise_divida'] == 2) {
                
                //recebo o numero total de contratos
                $totalContratos = $this->dao->listaNumContratosPorProposta($_POST['idProposta']);
                
                $valorTaxasContratos = 0;
                //tras todos os valores de taxas por numero de contratos
                $taxaTrans = $this->dao->taxaTransferencia();
                
                foreach ($taxaTrans as $row){
                    
                    $qtdaContrato = explode(",",$row->valvalor);
                    
                    //verifica se a qtda de contratos e maior que a quantidades de contratos retornado da tabela de dominio
                    if($totalContratos >= $qtdaContrato[0] && $totalContratos <= $qtdaContrato[1]) {
                        
                        //recebe o valor da taxa passando o id do registro
                        $valorTaxasContratos = $this->dao->taxaTransferenciaPorID($row->valregoid);
                    }
                    //se for maior que nova a cobrança será por taxa unica senão será por veiculo
                    if($qtdaContrato <= 9 && $qtdaContrato >= 1) {
                        $texto = 'por veículo';
                    }else{
                        $texto = 'taxa única';
                    }
                    
                }
                
                $titulo = "Documentos para Transferência de Titularidade Contratos SASCAR";
                
                if(strlen($retorno['ptrano_documento']) <= 11) {
                    $assunto = "Prezado Cliente!
                    
								Para realizarmos a transferência de titularidade será cobrado do novo titular uma taxa no valor de R$ $valorTaxasContratos ($texto) mais os valores de locação
								do equipamento e acessórios, caso haja, ambos clientes não pode ter nenhuma pendência financeira externa ou interna com a Sascar.<br />
								
								É necessário o envio da documentação abaixo do NOVO TITULAR:<br />
								
								- Cópia de RG e CPF ou CNH;<br />
								- Cópia do comprovante de residência atualizado;<br />
								- Cópia do documento do veículo.<br />
								- Telefones e e-mail para contato<br />
								
								O processo de transferência de titularidade só será iniciado se for enviado toda a documentação corretamente, caso contrário o antigo proprietário permanecerá com a responsabilidade dos
								pagamentos e o contrato em seu nome.<br />
								
								No aguardo do retorno.<br />
								
								Att,";
                }else {
                    
                    $assunto= "Prezado Cliente!
                    
								 Para realizarmos a transferência de titularidade será cobrado do novo titular uma taxa no valor de R$ $valorTaxasContratos ($texto) mais os valores de locação
								do equipamento e acessórios, caso haja, ambos clientes não pode ter nenhuma pendência financeira externa ou interna com a Sascar.<br />
								
								É necessário o envio da documentação abaixo do NOVO TITULAR:<br />
								
								
								- Cópia do contrato social;<br />
								- Cópia do documento do veículo.;<br />
								- Telefones e e-mail para contato;<br />
								
								O processo de transferência de titularidade só será iniciado se for enviado toda a documentação corretamente, caso contrário o antigo proprietário permanecerá com a responsabilidade dos
								pagamentos e o contrato em seu nome.<br />
								
								No aguardo do retorno.<br />
								
								Att,";
                }
                
                
                //chamando o metodo para enviar email
                $email = $this->enviaEmail($retorno['ptraemail_tit_anterior'],$retorno['ptracontato_email'],$titulo,$assunto);
                
                if($email) {
                    echo json_encode ( array (
                        "status" => "msgsucesso",
                        "proposta" =>  $retornoSerasaManual,
                        "message" => utf8_encode("Análise de crédito aprovado foi enviado e-mail para o novo cliente com a listagem da documentação necessária para conclusão da transferência."),
                        'statusanalise' => $retorno ['ptrasfoid_analise_credito'],
                        'statustitularidade' => $retorno ['ptrasfoid_analise_divida'],
                        "redirect" => ""
                    ) );
                    return;
                }else{
                    echo json_encode ( array (
                        "status" => "msgsucesso",
                        "proposta" => $retornoSerasaManual,
                        "message" => utf8_encode("Análise de crédito aprovado! Porem falha ao enviar e-mail!"),
                        'statusanalise' => $retorno ['ptrasfoid_analise_credito'],
                        'statustitularidade' => $retorno ['ptrasfoid_analise_divida'],
                        "redirect" => ""
                    ) );
                    return;
                }
                
                
                
            }else{
                
                echo json_encode ( array (
                    "status" => "msgsucesso",
                    "proposta" => $retornoSerasaManual,
                    "message" => utf8_encode("Análise de crédito aprovado"),
                    'statusanalise' => $retorno ['ptrasfoid_analise_credito'],
                    'statustitularidade' => $retorno ['ptrasfoid_analise_divida'],
                    "redirect" => ""
                ) );
            }
            
            
        }else{
            echo json_encode ( array (
                "status" => "msgerro",
                "proposta" => $retornoSerasaManual,
                "message" => utf8_encode("Problemas Aprovação Serasa Manual tente novamente ou entre em contato com o suporte"),
                'statusanalise' => '',
                "redirect" => ""
            ) );
        }
        
    }
    
    //@@@metodo que vai aprovar transferencia de titularidade
    public function aprovaTransferenciaTitularidade(){
        $dados = array(
            'idProposta' =>$_POST['idProposta'],
            'idUsuario' => $this->cd_usuario
        );
        
        //metodo que vai na dao para atualizar o banco para aprovado passando o id da proposta e o usuario que est
        $returnAprovadaTitularidade = $this->dao->aprovaTransferenciaTitularidade($dados);
        
        //se retornar true vai uma verificação
        if($returnAprovadaTitularidade == true) {
            
            //retorna a lista de solicitação de transferencia
            $retorno = $this->dao->listSolicitacaoTransferenciaPorID($_POST['idProposta']);
            
            //se analise de credito e analise de divida estiver aprovado ambos
            if($retorno ['ptrasfoid_analise_credito'] == 2 && $retorno ['ptrasfoid_analise_divida'] == 2) {
                
                //retorna o numero total de contratos da proposta
                $totalContratos = $this->dao->listaNumContratosPorProposta($_POST['idProposta']);
                
                $valorTaxasContratos = 0;
                
                //retorna os valores da taxa
                $taxaTrans = $this->dao->taxaTransferencia();
                
                
                foreach ($taxaTrans as $row){
                    
                    //recebe a quantidade de contratos por taxa
                    $qtdaContrato = explode(",",$row->valvalor);
                    
                    //verifica se total de contratos é maior que a qtdacontrato retorna da tabela dominio
                    if($totalContratos >= $qtdaContrato[0] && $totalContratos <= $qtdaContrato[1]) {
                        
                        //passa o id do registro
                        $valorTaxasContratos = $this->dao->taxaTransferenciaPorID($row->valregoid);
                    }
                    
                    //se for maior que nova a cobrança será por taxa unica senão será por veiculo
                    if($qtdaContrato <= 9 && $qtdaContrato >= 1) {
                        $texto = 'por veículo';
                    }else{
                        $texto = 'taxa única';
                    }
                    
                }
                
                $titulo = "Documentos para Transferência de Titularidade Contratos SASCAR";
                
                if(strlen($retorno['ptrano_documento']) <= 11) {
                    $assunto = "Prezado Cliente!
                    
					Para realizarmos a transferência de titularidade será cobrado do novo titular uma taxa no valor de R$ $valorTaxasContratos ($texto) mais os valores de locação
					do equipamento e acessórios, caso haja, ambos clientes não pode ter nenhuma pendência financeira externa ou interna com a Sascar.<br />
					
					É necessário o envio da documentação abaixo do NOVO TITULAR:<br />
					
					- Cópia de RG e CPF ou CNH;<br />
					- Cópia do comprovante de residência atualizado;<br />
					- Cópia do documento do veículo.<br />
					- Telefones e e-mail para contato<br />
					
					O processo de transferência de titularidade só será iniciado se for enviado toda a documentação corretamente, caso contrário o antigo proprietário permanecerá com a responsabilidade dos
					pagamentos e o contrato em seu nome.<br />
					
					No aguardo do retorno.<br />
					
					Att,";
                }else {
                    
                    $assunto= "Prezado Cliente!
                    
				Para realizarmos a transferência de titularidade será cobrado do novo titular uma taxa no valor de R$ $valorTaxasContratos ($texto) mais os valores de locação
				do equipamento e acessórios, caso haja, ambos clientes não pode ter nenhuma pendência financeira externa ou interna com a Sascar.<br />
				
				É necessário o envio da documentação abaixo do NOVO TITULAR:<br />
				
				
				- Cópia do contrato social;<br />
				- Cópia do documento do veículo.;<br />
				- Telefones e e-mail para contato;<br />
				
				O processo de transferência de titularidade só será iniciado se for enviado toda a documentação corretamente, caso contrário o antigo proprietário permanecerá com a responsabilidade dos
				pagamentos e o contrato em seu nome.<br />
				
				No aguardo do retorno.<br />
				
				Att,";
                }
                
                
                //chama o metodo de enviar o email e envia
                $email = $this->enviaEmail($retorno['ptraemail_tit_anterior'],$retorno['ptracontato_email'],$titulo,$assunto);
                
                if($email) {
                    echo json_encode ( array (
                        "status" => "msgsucesso",
                        "proposta" => $returnAprovadaTitularidade,
                        "message" => utf8_encode("Transferência de dívida aprovado foi enviado e-mail para o novo cliente com a listagem da documentação necessária para conclusão da transferência."),
                        'statustitularidade' => $retorno ['ptrasfoid_analise_divida'],
                        'statusanalise' => $retorno ['ptrasfoid_analise_credito'],
                        "redirect" => ""
                    ) );
                    return;
                }else{
                    echo json_encode ( array (
                        "status" => "msgsucesso",
                        "proposta" => $returnAprovadaTitularidade,
                        "message" => utf8_encode("Transferência de dívida aprovado! Porem falha ao enviar e-mail!"),
                        'statustitularidade' => $retorno ['ptrasfoid_analise_divida'],
                        'statusanalise' => $retorno ['ptrasfoid_analise_credito'],
                        "redirect" => ""
                    ) );
                    return;
                }
                
                
                
            }else {
                
                echo json_encode ( array (
                    "status" => "msgsucesso",
                    "proposta" => $returnAprovadaTitularidade,
                    "message" => utf8_encode("Transferência de dívida aprovado"),
                    'statustitularidade' => $retorno ['ptrasfoid_analise_divida'],
                    'statusanalise' => $retorno ['ptrasfoid_analise_credito'],
                    "redirect" => ""
                ) );
            }
            
            
        }else{
            echo json_encode ( array (
                "status" => "msgerro",
                "proposta" => $returnAprovadaTitularidade,
                "message" => utf8_encode("Problemas Aprovação de transferência de divida tente novamente ou entre em contato com o suporte"),
                'statustitularidade' => '',
                "redirect" => ""
            ) );
        }
        
    }
    
    
    
    
    
    
    //@@metodo que lista usuario que concluiu proposta
    public function listUsuarioConcluirProposta(){
        $result = $this->dao->retornaUsuarioConcluirProposta();
        return $result;
    }
    
    
    
    
    //metodo que vai excluir o arquivo
    public function excluirArquivo(){
        
        $arquivoNome = $_POST['arquivo'];
        $id = $_POST['id'];
        $idpropAnexo = $_POST['idpropAnexo'];
        
        $result = $this->dao->excluirArquivo($id);
        
        if(!$result) {
            
            echo json_encode(array("tipo_msg" => "i", "msg" => "Problemas para deletar arquivo."));
            exit;
        }else{
            
            $file_path = $PATH =  _SITEDIR_ ."faturamento/transferencia_titularidade/".$arquivoNome;
            
            if(unlink($file_path)){
                
                $resultAnexo = $this->listaAnexosProposta($idpropAnexo);
                
                $this->TitularidadeView->getComponenteListaAnexosProposta($resultAnexo);
                
                
            }else {
                echo json_encode(array("tipo_msg" => "i", "msg" => "Acesso negado - Não pode excluir o arquivo do servidor!"));
                exit;
            }
            
        }
        
    }
    
    //metodo que vai excluir a carta
    public function excluirCarta(){
        $arquivoNome = $_POST['arquivo'];
        
        $id = $_POST['id'];
        
        $result = $this->dao->excluirCarta($id);
        
        if(!$result) {
            
            echo json_encode(array("tipo_msg" => "i", "msg" => "Problemas para deletar carta."));
            exit;
        }else{
            
            $file_path = $PATH =  _SITEDIR_ ."faturamento/transferencia_titularidade/".$arquivoNome;
            
            if(unlink($file_path)){
                echo json_encode(array("tipo_msg" => "i", "msg" => "O Arquivo $arquivoNome foi excluido com sucesso"));
                exit;
            }else {
                echo json_encode(array("tipo_msg" => "i", "msg" => "Acesso negado - Não pode excluir o arquivo do servidor!"));
                exit;
            }
            
        }
        
    }
    
    //metodo para ecluir o contato instalação
    public function excluircontatoInstalacao(){
        $id = $_POST['id'];
        $ptciptraoid  = $_POST['idInstalAssis'];
        
        $result = $this->dao->excluircontatoInstalacao($id);
        
        if(!$result) {
            
            echo json_encode(array("tipo_msg" => "i", "msg" => "registro de contato instalação assistência."));
            exit;
        }else{
            $result = $this->listaContatoInstalacao($ptciptraoid);
            if($result != null){
                $this->TitularidadeView->getComponenteListaContatoInstalacao($result);
                exit;
            } else{
                echo json_encode(array("tipo_msg" => "i", "msg" => "Nenhum resultado encontrado."));
                exit;
            }
        }
        
        
    }
    
    //metodo que vai excluir contato emergencia
    public function excluircontatoEmergencia(){
        $id = $_POST['id'];
        $ptceptraoid  = $_POST['idContEmerg'];
        
        $result = $this->dao->excluircontatoEmergencia($id);
        
        if(!$result) {
            
            echo json_encode(array("tipo_msg" => "i", "msg" => "registro de contato emergencia."));
            exit;
        }else{
            $result = $this->listaContatoEmergencia($ptceptraoid);
            if($result != null){
                $this->TitularidadeView->getComponenteListaContatosEmergencia($result);
                exit;
            } else{
                echo json_encode(array("tipo_msg" => "i", "msg" => "Nenhum resultado encontrado."));
                exit;
            }
        }
        
        
    }
    
    //metodo que vai excluir os contatos de pessoa autorizada
    public function excluircontatoPessoaAutorizada(){
        $id = $_POST['id'];
        $ptpaptraoid   = $_POST['idContPessoaAut'];
        
        $result = $this->dao->excluircontatoPessoaAutorizada($id);
        
        if(!$result) {
            
            echo json_encode(array("tipo_msg" => "i", "msg" => "registro de pessoa autorizada."));
            exit;
        }else{
            $result = $this->listaPessoa($ptpaptraoid);
            if($result != null){
                $this->TitularidadeView->getComponenteListaPessoaAutorizada($result);
                exit;
            } else{
                echo json_encode(array("tipo_msg" => "i", "msg" => "Nenhum resultado encontrado."));
                exit;
            }
        }
        
        
    }
    
    //retorna os tipo de pagamento
    public function tipoPagamento() {
        $resultPagamento = $this->dao->formasPagamento ();
        
        return $resultPagamento;
    }
    
    //metodo para retornar todas as data de vencimento
    public function dataVencimento(){
        $resultadoVencimento = $this->dao->diaVencimentoBoleto();
        
        return $resultadoVencimento;
    }
    
    //metodo para retorna o id da forma de pagamento
    public function formaPagamentoID(){
        
        $id = $_POST['id'];
        
        $resultado = $this->dao->getFormaPagamentoCreditoDebito($id);
        
        if(count($resultado) > 0 && !empty($resultado) || $resultado != null ){
            
            if($resultado['debito'] == 't') {
                $numeroBanco = $this->getNomeBanco($id);
                echo json_encode(array("codigo" => 1, "msg" => "Debito", "codigobanco" =>$numeroBanco['id_banco'], "nomebanco" => $numeroBanco['banco'] ));
                exit;
                
            }else{
                echo json_encode(array("codigo" => 2, "msg" => "Credito", "codigobanco" =>"", "nomebanco" =>""));
                exit;
            }
        }else{
            echo json_encode(array("codigo" => 3, "msg" => "Outros", "codigobanco" =>"", "nomebanco" =>""));
            exit;
        }
    }
    
    //metodo para retorna o nome do banco passando o id do banco
    public function getNomeBanco($id){
        $resultBanco = $this->dao->getNomeBancoID($id);
        return $resultBanco;
    }
    
    
    
    
    
    //@@@salva a proposta sem a validação dos campos , aonde pode ser editada enquanto o processo não estiver finalizado ou concluido
    public function salvaPropostaTransferencia(){
        
        
        
        $prtno_documento = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prtno_documento_editar']);
        $cep = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prpend_cep']);
        $prcfone_cont = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prcfone_cont']);
        $prcfone_cont2 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prcfone_cont2']);
        $prcfone_cont3 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prcfone_cont3']);
        $cepCob = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prpendcob_cep']);
        $nCartao = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['nCartao']);
        
        $cidadeCobr = '';
        $bairroCobr = '';
        $cidadend = '';
        $bairroEnd = '';
        
        if(!empty($_POST['prpend_pais']) && $_POST['prpend_pais'] != '') {
            $paisEnd = $_POST['prpend_pais'];
        }else{
            $paisEnd = "null";
        }
        
        if($_POST['prpend_est'] != ''  && !empty($_POST['prpend_est'])) {
            $estadoEnd = $_POST['prpend_est'];
        }else{
            $estadoEnd = "null";
        }
        
        if($_POST['prpend_num'] != '' && !empty($_POST['prpend_num'])) {
            $endNumero = $_POST['prpend_num'];
        }else{
            $endNumero = 0;
        }
        
        if($_POST['prpendcob_pais'] != '' && !empty($_POST['prpendcob_pais'])) {
            $paisEndCobr = $_POST['prpendcob_pais'];
        }else{
            $paisEndCobr = "null";
        }
        
        if($_POST['prpendcob_est'] != ''  && !empty($_POST['prpendcob_est'])) {
            $estadoEndCobr = $_POST['prpendcob_est'];
        }else{
            $estadoEndCobr = "null";
        }
        
        if($_POST['prpendcob_num'] != ''  && !empty($_POST['prpendcob_num'])) {
            $endNumeroCobr = $_POST['prpendcob_num'];
        }else{
            $endNumeroCobr = 0;
        }
        
        if($_POST['prpemi_dt'] != ''  && !empty($_POST['prpemi_dt'])) {
            $dataEmissao = explode("/",$_POST['prpemi_dt']);
            $dataEmissao = $dataEmissao[2]."-".$dataEmissao[1]."-".$dataEmissao[0];
            $dataEmissaoRg  = "'$dataEmissao'";
            
        }else {
            $dataEmissaoRg = "null";
        }
        
        if($_POST['prpnas_dt'] != '' && !empty($_POST['prpnas_dt'])) {
            $dataNas = explode("/",$_POST['prpnas_dt']);
            $dataNasc = $dataNas[2]."-".$dataNas[1]."-".$dataNas[0];
            $dataNascFisica  = "'$dataNasc'";
            
        }else{
            $dataNascFisica = "null";
        }
        
        if($_POST['prpfund_dt'] != ''  && !empty($_POST['prpfund_dt'])) {
            $dataFundacaoJuridica = $_POST['prpfund_dt'];
            //$dataFundacao = explode("/",$_POST['prpfund_dt']);
            //$dataFundacaoJur = $dataFundacao[2]."-".$dataFundacao[1]."-".$dataFundacao[0];
            $dataFundacaoJuridica  = "' $dataFundacaoJuridica'";
            
            
        }else{
            $dataFundacaoJuridica = "null";
        }
        
        if($_POST['tipo_pagamento'] != ''  && !empty($_POST['tipo_pagamento'])) {
            $tipoPagamento = $_POST['tipo_pagamento'];
            
        }else{
            $tipoPagamento = "null";
        }
        
        if($_POST['data_vencimento'] != ''  && !empty($_POST['data_vencimento']) ) {
            $dataVencimento = $_POST['data_vencimento'];
        }else{
            $dataVencimento = "null";
        }
        
        if($_POST['idBanco'] != '' && !empty($_POST['idBanco'])) {
            $idBanco = $_POST['idBanco'];
        }else{
            $idBanco = "null";
        }
        
        if($_POST['prpend_cid'] == '' && empty($_POST['prpend_cid'])){
            $cidadend = $_POST['prpend_cidade'];
        }else{
            $cidadend = $_POST['prpend_cid'];
        }
        
        if($_POST['prpend_bairro'] == '' && empty($_POST['prpend_bairro'])){
            $bairroEnd = $_POST['prpend_combobairro'];
        }else{
            $bairroEnd = $_POST['prpend_bairro'];
        }
        
        
        if($_POST['prpendcob_cid'] == '' && empty($_POST['prpendcob_cid'])){
            $cidadeCobr = $_POST['prpendCob_cidade'];
        }else{
            $cidadeCobr = $_POST['prpendcob_cid'];
        }
        
        if($_POST['prpendcob_bairro'] == '' && empty($_POST['prpendcob_bairro'])){
            $bairroCobr = $_POST['prpend_combobairrocobr'];
        }else{
            $bairroCobr = $_POST['prpendcob_bairro'];
        }
        $dados = array(
            'ptcptraoid' => $_POST['ptraoid'],
            'ptcnumdocumento' =>$prtno_documento,
            'ptcnome' =>$_POST['prtcontratante'],
            'ptcrg' => $_POST['prtrg'],
            'ptcorgaoemissor' => $_POST['prtrgorgaoemissor'],
            'ptcdataemissao' => $dataEmissaoRg,
            'ptcdatanasc' => $dataNascFisica,
            'ptcnomepai' => $_POST['prtfiliacaopai'],
            'ptcnomemae' => $_POST['prtfiliacaoMae'],
            'ptcsexo' => $_POST['prtsexo'],
            'ptcivil' => $_POST['prtestado_civil'],
            'ptcoptantesimples' => $_POST['prtoptante_simples']	,
            'ptcdatafundacao' =>$dataFundacaoJuridica,
            'ptcestadoinscricaoest' =>$_POST['prtie_estado'],
            'ptcinscricaoest' => $_POST['prtie_num'],
            'ptctipopessoa' =>$_POST['prtipopessoa'],
            'ptendpaisoid' => $paisEnd,
            'ptendestoid' => $estadoEnd,
            'ptendcep' => $cep,
            'ptendcidade' => $cidadend,
            'ptendbairro' => $bairroEnd,
            'ptendlogradouro' => $_POST['prpend_log'],
            'ptendnumero' => $endNumero,
            'ptendcomplemento' =>$_POST['prpend_compl'],
            'ptendfone' => $prcfone_cont,
            'ptendfone2' => $prcfone_cont2,
            'ptendfone3' => $prcfone_cont3,
            'ptendemail' => $_POST['prpend_email'],
            'ptendemailnf' => $_POST['prpend_emailnf'],
            'ptendcobpaisoid' => $paisEndCobr,
            'ptendcobestoid' => $estadoEndCobr,
            'ptendcobcep' => $cepCob,
            'ptendcobcidade' => $cidadeCobr ,
            'ptendcobbairro' => $bairroCobr,
            'ptendcoblogradouro' => $_POST['prpendcob_log'],
            'ptendcobnumero' => $endNumeroCobr,
            'prpendcob_compl' => $_POST['prpendcob_compl'],
            'ptfpforcoid' => $tipoPagamento,
            'ptfpcdvoid' => $dataVencimento,
            'ptfpbancodigo' => $idBanco,
            'ptfpagencia' => $_POST['nAgencia'],
            'ptfpnumconta' => $_POST['nConta'],
            'ptfpnumcartaocredito' =>$nCartao,
            'ptfpvalidadeCartaoCredito' => $_POST['dataCartao']
            
        );
        
        //insere na tabela propostaTransferencia
        $result = $this->dao->insertPropostaTransferencia($dados);
        
        if($result){
            echo json_encode(array("tipo_msg" => "i", "msg" => "Dados da Proposta atualizados."));
            exit;
        }else{
            echo json_encode(array("tipo_msg" => "e", "msg" => "Erro ao atualizar dados da proposta"));
            exit;
        }
        
    }
    
    /**
     * @@@Trata os parametros do POST/GET. Preenche um objeto com os parametros do POST e/ou GET.
     * @return stdClass Parametros tradados
     *
     * @retrun stdClass
     */
    private function tratarParametros($parametros = null) {
        if(is_null($parametros)) {
            $retorno = new stdClass();
        } else {
            $retorno = $parametros;
        }
        
        
        if (count($_POST) > 0) {
            unset($_GET);
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
    
    
    
    
    
    
    
    public function retornaEstadoEndereco(){
        
        $retornoEstado = $this->dao->listaEstadoEnderecoIdPais($_POST['id']);
        
        
    }
    
    
    
    
    
    
    
    //@@@gera o arquivo cvs da pesquisa
    public function gerarCSV() {

        // $this->_testaFaturamentoUnificado();
        if (isset ( $_POST )) {
            $parametros = $_POST;

            if(isset($parametros['statusId']) ){
                $parametros['concsioidArray'][] = $parametros['statusId'];
            }

            try {
                //pesquisa o cliente com os dados passados como parametros
                $resultado = $this->dao->pesquisaTransferencia ( $parametros );

                $valortotalLocacao = 0;
                $valortotalAcessorios = 0;
                $valortotalMonitoramento = 0;
                $valortotalTotal = 0;

                for($j = 0; $j < count($resultado); $j++) {

                    /*$contrato = $resultado[$j]['connumero'];

                    $prazoFidelidade = $resultado[$j]['conprazo_contrato'];
                    $dataInicioVigencia = $resultado[$j]['inicio_vigencia'];
                    $upgradeDown = $this->dao->upgradeDown ( $contrato );
                    $paralizacao = $this->dao->paralizacao ( $contrato , $dataInicioVigencia, $prazoFidelidade);
                    $fidelizacao = $this->dao->fidelizacao ( $contrato );

                    if($upgradeDown != null) {
                        if ($upgradeDown[1] != 2) {
                            $resultado[$j]['inicio_vigencia'] = $upgradeDown[0];
                        } else {
                            $resultado[$j]['inicio_vigencia'] = $upgradeDown[0];
                        }
                    }

                    if($paralizacao != null) {
                        $resultado[$j]['conprazo_contrato'] = $resultado[$j]['conprazo_contrato'] + $paralizacao[0];
                        $prazoFidelidade = $resultado[$j]['conprazo_contrato'] + $paralizacao[0];
                    }

                    if($fidelizacao != null) {
                        $resultado[$j]['inicio_vigencia'] = $fidelizacao[0];
                        $prazoFidelidade = $fidelizacao[1];
                    }

                    /*if($paralizacao != null) {
                        //$prazoFidelidade = $paralizacao[1];
                    }*/

                    $dataAtual = date('d-m-Y');

                    $dataTerminoVigencia= date('m-d-Y', strtotime("+".$resultado[$j]['conprazo_contrato']." months",   strtotime($resultado[$j]['inicio_vigencia'])));

                    if ($dataInicioVigencia == $dataAtual){
                        $mesesTerminoVigencia = $prazoFidelidade;
                    }else{
                        $resultadoSubtracao = (strtotime($dataAtual) - strtotime($dataTerminoVigencia))/86400;

                        $diasRestantes = (int)ceil( $resultadoSubtracao / (60 * 60 * 24));

                        $mesesRestantes = (int)ceil($diasRestantes / 30.416666666666668);

                        if ($mesesRestantes > 0){
                            $mesesTerminoVigencia = 0;
                        }else {
                            $mesesTerminoVigencia = $mesesRestantes;
                        }
                    }

                    /**
                     * Locação
                     */
                    $valorLocacao = $this->dao->pesquisaLocacao($contrato);

                    for($l = 0; $l < count($valorLocacao); $l++) {
                        if($valorLocacao[$l]['valor_locacao'] != 0) {
                            $valortotalLocacao += $valorLocacao[$l]['valor_locacao'];
                        }

                    }


                    $valortotalTotal = $valortotalTotal + $valortotalLocacao;

                    if($valortotalLocacao == 0) {
                        $valortotalLocacao = "0.00";
                    }
                    $resultado[$j]['locacao']=$valortotalLocacao;
                    $resultado[$j]['valor_locacao']=$valortotalLocacao;

                    $valortotalLocacao = 0;

                    /**
                     * Acessórios
                     */
                    $valorAcessorios = $this->dao->pesquisaAcessorios($contrato);

                    for($a = 0; $a < count($valorAcessorios); $a++) {
                        if($valorAcessorios[$a]['valor_acessorios'] != 0) {
                            $valortotalAcessorios += $valorAcessorios[$a]['valor_acessorios'];
                        }

                    }

                    if($valortotalAcessorios == 0) {
                        $valortotalAcessorios = "0.00";
                    }
                    $resultado[$j]['acessorios']=$valortotalAcessorios;

                    $valortotalAcessorios = 0;

                    /**
                     * Monitoramento
                     */
                    $valorMonitoramento = $this->dao->pesquisaMonitoramento($contrato);

                    for($m = 0; $m < count($valorMonitoramento); $m++) {
                        if($valorMonitoramento[$m]['valor_monitoramento'] != 0) {
                            $valortotalMonitoramento += $valorMonitoramento[$m]['valor_monitoramento'];
                        }
                    }

                    $valortotalTotal = $valortotalTotal + $valortotalMonitoramento;

                    if($valortotalMonitoramento == 0) {
                        $valortotalMonitoramento = "0.00";
                    }
                    $resultado[$j]['monitoramento']=$valortotalMonitoramento;

                    $valortotalMonitoramento = 0;

                    /**
                     * Total
                     */
                    if($valortotalTotal == 0) {
                        $valortotalTotal = "0.00";
                    }
                    $resultado[$j]['total']=$valortotalTotal;

                    $valortotalTotal = 0;
                    ;
                }


                $content .= "Cliente:;Contrato:;Inicio de Vigência:;Meses Restantes:;Placa:;Tipo do Contrato:;Classe do Contrato:;Locação:;Acessórios:;Monitoramento:;Valor Total:;Status:\n";

                foreach ($resultado as $row  => $value) {
                    $content .= $value['clinome'].";";
                    $content .= $value['connumero'].";";
                    $content .= $value['inicio_vigencia'].";";
                    $content .= $mesesTerminoVigencia."/".$value['conprazo_contrato'].";";
                    $content .= $value['veiplaca'].";";
                    $content .= $value['tipo_contrato'].";";
                    $content .= $value['classe_contrato'].";";
                    $content .= $value['locacao'].";";
                    $content .= $value['acessorios'].";";
                    $content .= $value['monitoramento'].";";
                    $content .= $value['total'].";";
                    $content .= $value['csidescricao']."\n";
                }

                $arquivo['file_path'] = "/var/www/docs_temporario/";
                $arquivo['file_name'] = "transferencia_titularidade_" . date('d-m-Y') . ".csv";
                file_put_contents($arquivo['file_path'] . $arquivo['file_name'], $content);

                echo json_encode(array(
                    "codigo"	=> 0,
                    "msg"		=> 'Planilha CSV gerada com sucesso.',
                    "arquivo"	=> $arquivo['file_path'] . $arquivo['file_name'],
                ));
                echo "<br>&nbsp&nbsp&nbsp&nbspPlanilha CSV gerada com sucesso.";
                exit;
            } catch (Exception $e) {


                echo json_encode(array(
                    "codigo"	=> 1,
                    "msg"		=> 'Falha ao gerar planilha CSV.',
                ));
                echo "<br>&nbsp&nbsp&nbsp&nbspFalha ao gerar planilha CSV.";
                exit;
            }
        }
    }
    
    
    
    
    /*
     * Busca o id do estado passando a sigla
     */
    public function buscaEstadoID($uf){
        $idEstado = $this->dao->buscaIdEstado($uf);
        return $idEstado;
    }
    
    /*
     * Metodo que  para preencher o endereço recebendo o post do CEP
     */
    public function retornaEnderecosCEP(){
        
        $cep = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['cep']);
        $buscaEndereco = $this->dao->buscaEnderecoCEP($cep);
        
        if($buscaEndereco != '' || $buscaEndereco != null || $buscaEndereco > 0) {
            $uf = $buscaEndereco['clguf_sg'];
            $clcoid = $buscaEndereco['clgclcoid'];
            $cep = $buscaEndereco['clgcep'];
            $logradouro = $buscaEndereco['clgnome'];
            $cidade = $buscaEndereco['clcnome'];
            
            
            $buscabairro = $this->dao->buscaBairroCep($uf,$clcoid,$cep);
            
            $bairro = $buscabairro['cbanome'];
            
            echo json_encode(array(
                "sucesso"	=> "i",
                "estado"	=> utf8_encode($uf),
                "cidade"	=> utf8_encode($cidade),
                "bairro"	=> utf8_encode($bairro),
                "Logradouro"	=> utf8_encode($logradouro),
            ));
            exit;
        }else {
            echo json_encode(array(
                "sucesso"	=> "e",
                "msg"		=> 'Nenhum resultado encontrado para este CEP',
            ));
            exit;
        }
        
        
    }
    
    //Lista todas as cidades passando a sigla do estado
    public function listCidadesSiglaEstadoEndereco(){
        
        $sigla = $_POST['sigla'];
        $listaCidades = $this->dao->buscaCidadesSiglaEstado($sigla);
        
        if($listaCidades != null){
            $this->TitularidadeView->getComponenteListaCidades($listaCidades);
            exit;
        } else if($listaCidades == '' ||$listaCidades == null || $listaCidades <= 0){
            echo json_encode(array("tipo_msg" => "i", "msg" =>utf8_encode("Não existe cidades relacionados a esse estado.")));
            exit;
        }else{
            echo json_encode(array("tipo_msg" => "e", "msg" => "Problemas para listar cidades."));
            exit;
        }
        
    }
    
    //lista os bairros referente a cidade e estado passado no formulario de endereço
    public function listaBairros(){
        //getComponenteListaBairros
        $cidade = $_POST['cidade'];
        $sigla = $_POST['siglaEstado'];
        $buscaIdLocalidade = $this->dao->buscaIdLocalidade($sigla,$cidade);
        
        $buscaBairros = $this->dao->buscaBairrosIdLocalidade($buscaIdLocalidade['clcoid']);
        
        if($buscaBairros != null){
            $this->TitularidadeView->getComponenteListaBairros($buscaBairros);
            exit;
        } else if($buscaBairros == '' ||$buscaBairros == null || $buscaBairros <= 0){
            echo json_encode(array("tipo_msg" => "i", "msg" =>utf8_encode("Não existe bairros relacionados a essa cidade.")));
            exit;
        }else{
            echo json_encode(array("tipo_msg" => "i", "msg" => "Problemas para listar bairros."));
            exit;
        }
    }
    
    //Lista todas as cidades passando a sigla do estado para o endereco cobrança
    public function listCidadesSiglaEstadoEnderecoCobranca(){
        
        $sigla = $_POST['sigla'];
        $listaCidades = $this->dao->buscaCidadesSiglaEstado($sigla);
        
        if($listaCidades != null){
            $this->TitularidadeView->getComponenteListaCidadesEnderecoCobranca($listaCidades);
            exit;
        } else if($listaCidades == '' ||$listaCidades == null || $listaCidades <= 0){
            echo json_encode(array("tipo_msg" => "i", "msg" =>utf8_encode("Não existe cidades relacionados a esse estado.")));
            exit;
        }else{
            echo json_encode(array("tipo_msg" => "e", "msg" => "Problemas para listar cidades."));
            exit;
        }
        
    }
    
    //lista os bairros referente a cidade e estado passado no formulario de endereço cobrança
    public function listaBairrosEnderecoCobranca(){
        //getComponenteListaBairros
        $cidade = $_POST['cidade'];
        $sigla = $_POST['siglaEstado'];
        $buscaIdLocalidade = $this->dao->buscaIdLocalidade($sigla,$cidade);
        
        $buscaBairros = $this->dao->buscaBairrosIdLocalidade($buscaIdLocalidade['clcoid']);
        
        if($buscaBairros != null){
            $this->TitularidadeView->getComponenteListaBairrosEnderecoCobranca($buscaBairros);
            exit;
        } else if($buscaBairros == '' ||$buscaBairros == null || $buscaBairros <= 0){
            echo json_encode(array("tipo_msg" => "i", "msg" =>utf8_encode("Não existe bairros relacionados a essa cidade.")));
            exit;
        }else{
            echo json_encode(array("tipo_msg" => "i", "msg" => "Problemas para listar bairros."));
            exit;
        }
    }
    
    
    
    
    
    
    
    
    
    
    //@@@@cancela a solicitação de transferencia passando o id da proposta
    public function cancelarSolicitacao(){
        
        $idproposta = $_POST['id'];
        
        $returnCancelaSolicitacao = $this->dao->cancelaSolicitacaoPropostaTransferencia($idproposta);
        
        if($returnCancelaSolicitacao == true) {
            
            echo json_encode ( array (
                "status" => "msgsucesso",
                "message" => utf8_encode("Proposta de transferência cancelada com sucesso."),
                "redirect" => ""
            ) );
            return;
            
        }else{
            echo json_encode ( array (
                "status" => "msgerro",
                "message" => utf8_encode("Problemas para cancelar proposta transferência"),
                "redirect" => ""
            ) );
        }
    }
    
    
    
    
    /**
     * @@@@Gera um novo Contrato/Termo com um novo numero para o Cliente que está sendo transferido
     *
     * @retrun stdClass
     */
    public function gerarTermo(){
        
        $idProposta = $_POST['ptraoid'];
        
        //Função para validar Inscrição Estadual de Qualquer Estado
        //print_r($this->validaIE('PR', 3549488500));
        
        unset($camposVazio);
        
        $camposVazio = array ("status" => "error",) ;
        if($_POST['prtipopessoa'] == 'F') {
            
            if($_POST['prtcontratante'] == '' || empty($_POST['prtcontratante'])){
                $camposVazio[] = "prtcontratante";
            }
            if($_POST['prtrg'] == '' || empty($_POST['prtrg'])){
                $camposVazio[] = "prtrg";
            }
            if($_POST['prtrgorgaoemissor'] == '' || empty($_POST['prtrgorgaoemissor'])){
                $camposVazio[] = "prtrgorgaoemissor";
            }
            if($_POST['prpemi_dt'] == '' || empty($_POST['prpemi_dt'])) {
                $camposVazio[] = "prpemi_dt";
            }
            if($_POST['prpnas_dt'] == '' || empty($_POST['prpnas_dt'])) {
                $camposVazio[] = "prpnas_dt";
            }
            if($_POST['prtfiliacaopai'] == '' || empty($_POST['prtfiliacaopai'])) {
                $camposVazio[] = "prtfiliacaopai";
            }
            if($_POST['prtfiliacaoMae'] == '' || empty($_POST['prtfiliacaoMae'])) {
                $camposVazio[] = "prtfiliacaoMae";
            }
            if($_POST['prtsexo'] == '' || empty($_POST['prtsexo'])) {
                $camposVazio[] = "prtsexo";
            }
            if($_POST['prtestado_civil'] == '' || empty($_POST['prtestado_civil'])) {
                $camposVazio[] = "prtestado_civil";
            }
            
            
        }else {
            if($_POST['prtoptante_simples'] == '' || empty($_POST['prtoptante_simples'])){
                $camposVazio[] = "prtoptante_simples";
            }
            if($_POST['prtcontratante'] == '' || empty($_POST['prtcontratante'])){
                $camposVazio[] = "prtcontratante";
            }
            if($_POST['prpfund_dt'] == '' || empty($_POST['prpfund_dt'])){
                $camposVazio[] = "prpfund_dt";
            }
            if($_POST['prtie_estado'] == '' || empty($_POST['prtie_estado'])) {
                $camposVazio[] = "prtie_estado";
            }
            if($_POST['prtie_num'] == '' || empty($_POST['prtie_num'])) {
                $camposVazio[] = "prtie_num";
            }
            
        }
        
        if($_POST['taxa'] == '' || empty($_POST['taxa'])) {
            $camposVazio[] = "taxa";
        }
        
        if($_POST['prpend_cep'] == '' || empty($_POST['prpend_cep'])){
            $camposVazio[] = "prpend_cep";
        }
        
        if($_POST['prpend_pais'] == '' || empty($_POST['prpend_pais'])){
            $camposVazio[] = "prpend_pais";
        }
        if($_POST['prpend_est'] == '' || empty($_POST['prpend_est'])){
            $camposVazio[] = "prpend_est";
        }
        
        if(($_POST['prpend_cid'] == '' || empty($_POST['prpend_cid'])) && ($_POST['prpend_cidade'] == '' || empty($_POST['prpend_cidade'])) ){
            $camposVazio[] = "prpend_cid";
        }
        
        if(($_POST['prpend_bairro'] == '' || empty($_POST['prpend_bairro'])) && ($_POST['prpend_combobairro'] == '' || empty($_POST['prpend_combobairro'])) ){
            $camposVazio[] = "prpend_bairro";
        }
        
        if($_POST['prpend_log'] == '' || empty($_POST['prpend_log'])){
            $camposVazio[] = "prpend_log";
        }
        
        if($_POST['prpend_num'] == '' || empty($_POST['prpend_num'])){
            $camposVazio[] = "prpend_num";
        }
        
        if($_POST['prcfone_cont'] == '' || empty($_POST['prcfone_cont'])){
            $camposVazio[] = "prcfone_cont";
        }
        
        if($_POST['prcfone_cont2'] == '' || empty($_POST['prcfone_cont2'])){
            $camposVazio[] = "prcfone_cont2";
        }
        
        if($_POST['prcfone_cont3'] == '' || empty($_POST['prcfone_cont3'])){
            $camposVazio[] = "prcfone_cont3";
        }
        
        if($_POST['prpend_email'] == '' || empty($_POST['prpend_email'])){
            $camposVazio[] = "prpend_email";
        }
        
        if($_POST['prpend_emailnf'] == '' || empty($_POST['prpend_emailnf'])){
            $camposVazio[] = "prpend_emailnf";
        }
        
        if($_POST['prpendcob_cep'] == '' || empty($_POST['prpendcob_cep'])){
            $camposVazio[] = "prpendcob_cep";
        }
        
        if($_POST['prpendcob_pais'] == '' || empty($_POST['prpendcob_pais'])){
            $camposVazio[] = "prpendcob_cep";
        }
        
        if($_POST['prpendcob_est'] == '' || empty($_POST['prpendcob_est'])){
            $camposVazio[] = "prpendcob_est";
        }
        
        if(($_POST['prpendcob_cid'] == '' || empty($_POST['prpendcob_cid'])) && ($_POST['prpendCob_cidade'] == '' || empty($_POST['prpendCob_cidade'])) ){
            $camposVazio[] = "prpendcob_cid";
        }
        
        if(($_POST['prpendcob_bairro'] == '' || empty($_POST['prpendcob_bairro'])) && ($_POST['prpend_combobairrocobr'] == '' || empty($_POST['prpend_combobairrocobr'])) ){
            $camposVazio[] = "prpend_bairro";
        }
        
        if($_POST['prpendcob_log'] == '' || empty($_POST['prpendcob_log'])){
            $camposVazio[] = "prpendcob_log";
        }
        
        if($_POST['prpendcob_num'] == '' || empty($_POST['prpendcob_num'])){
            $camposVazio[] = "prpendcob_num";
        }
        
        if($_POST['tipo_pagamento'] == '' || empty($_POST['tipo_pagamento'])){
            $camposVazio[] = "tipo_pagamento";
        }
        
        if($_POST['data_vencimento'] == '' || empty($_POST['data_vencimento'])){
            $camposVazio[] = "data_vencimento";
        }
        
        if($_POST['tipoPagamentoAtual'] == 'Debito') {
            if($_POST['idBanco'] == '' || empty($_POST['idBanco'])){
                $camposVazio[] = "idBanco";
            }
            if($_POST['nAgencia'] == '' || empty($_POST['nAgencia'])){
                $camposVazio[] = "nAgencia";
            }
            if($_POST['nConta'] == '' || empty($_POST['nConta'])){
                $camposVazio[] = "nConta";
            }
        }else if($_POST['tipoPagamentoAtual'] == 'Credito'){
            if($_POST['nCartao'] == '' || empty($_POST['nCartao'])){
                $camposVazio[] = "nCartao";
            }
            if($_POST['dataCartao'] == '' || empty($_POST['dataCartao'])){
                $camposVazio[] = "dataCartao";
            }
        }
        
        if(count($camposVazio) > 1){
            echo json_encode ($camposVazio);
            return;
        }
        
        if($_POST['prtie_estado'] !='' || !empty($_POST['prtie_estado'])) {
            $estadoIE = $this->dao->buscaSiglaEstado($_POST['prtie_estado']);
        }
        
        if($_POST['prtie_num'] != '' || !empty($_POST['prtie_num'])){
            if(!$this->validaIE($estadoIE['estuf'],$_POST['prtie_num'])) {
                echo json_encode ( array (
                    "status" => "erroie",
                    "message" =>  utf8_encode("Inscrição Estadual inválida"),
                    "redirect" => ""
                ) );
                return;
            }
        }
        
        $listPessoasAutorizadas = $this->dao->listaPessoaDaoIdPropostaCount($idProposta);
        $listContatoEmergencia =  $this->dao->listaContatoEmergenciaCount($idProposta);
        $listaContatoInstalacao = $this->dao->listaContatoInstalacaoIDPropostaCount($idProposta);
        $listaAnexos = $this->dao->listAnexosPropostaIdCount($idProposta);
        $listaAnexosCarta = $this->dao->listaAnexoCartaIdCount($idProposta);
        
        if($listPessoasAutorizadas == 0 ) {
            echo json_encode ( array (
                "status" => "errorCombos",
                "message" => utf8_encode("É obrigatório possuir pessoas autorizadas, contato de emergência e contatos para instalação/assistência cadastrados para prosseguir com a transferência."),
                "redirect" => ""
            ) );
            return;
        }else if($listContatoEmergencia == 0) {
            echo json_encode ( array (
                "status" => "errorCombos",
                "message" => utf8_encode("É obrigatório possuir pessoas autorizadas, contato de emergência e contatos para instalação/assistência cadastrados para prosseguir com a transferência."),
                "redirect" => ""
            ) );
            return;
        }else if($listaContatoInstalacao == 0) {
            echo json_encode ( array (
                "status" => "errorCombos",
                "message" => utf8_encode("É obrigatório possuir pessoas autorizadas, contato de emergência e contatos para instalação/assistência cadastrados para prosseguir com a transferência."),
                "redirect" => ""
            ) );
            return;
        }else if($listaAnexos == 0) {
            echo json_encode ( array (
                "status" => "errorCombos",
                "message" => utf8_encode("Favor preencher ao menos um anexo"),
                "redirect" => ""
            ) );
            return;
        }else if($listaAnexosCarta == 0) {
            echo json_encode ( array (
                "status" => "errorCombos",
                "message" => utf8_encode("A carta de transferência deve ser anexada."),
                "redirect" => ""
            ) );
            return;
        }
        
        $prtno_documento = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prtno_documento_editar']);
        $cep = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prpend_cep']);
        $prcfone_cont = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prcfone_cont']);
        $prcfone_cont2 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prcfone_cont2']);
        $prcfone_cont3 = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prcfone_cont3']);
        $cepCob = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['prpendcob_cep']);
        $nCartao = str_replace(array(".", ",", "-", "/", "(", ")", " "), "", $_POST ['nCartao']);
        
        $cidadeCobr = '';
        $bairroCobr = '';
        $cidadend = '';
        $bairroEnd = '';
        
        if($_POST['ptcorgaoemissor'] != ''  && !empty($_POST['ptcorgaoemissor']) ) {
            $orgaoemissor = $_POST['ptcorgaoemissor'];
        }else{
            $orgaoemissor = "null";
        }
        
        if(!empty($_POST['prpend_pais']) && $_POST['prpend_pais'] != '') {
            $paisEnd = $_POST['prpend_pais'];
        }else{
            $paisEnd = "null";
        }
        
        if($_POST['prpend_est'] != ''  && !empty($_POST['prpend_est'])) {
            $estadoEnd = $_POST['prpend_est'];
        }else{
            $estadoEnd = "null";
        }
        
        if($_POST['prpend_num'] != '' && !empty($_POST['prpend_num'])) {
            $endNumero = $_POST['prpend_num'];
        }else{
            $endNumero = 0;
        }
        
        if($_POST['prpendcob_pais'] != '' && !empty($_POST['prpendcob_pais'])) {
            $paisEndCobr = $_POST['prpendcob_pais'];
        }else{
            $paisEndCobr = "null";
        }
        
        if($_POST['prpendcob_est'] != ''  && !empty($_POST['prpendcob_est'])) {
            $estadoEndCobr = $_POST['prpendcob_est'];
        }else{
            $estadoEndCobr = "null";
        }
        
        if($_POST['prpendcob_num'] != ''  && !empty($_POST['prpendcob_num'])) {
            $endNumeroCobr = $_POST['prpendcob_num'];
        }else{
            $endNumeroCobr = 0;
        }
        
        if($_POST['prpemi_dt'] != ''  && !empty($_POST['prpemi_dt'])) {
            $dataEmissao = explode("/",$_POST['prpemi_dt']);
            $dataEmissao = $dataEmissao[2]."-".$dataEmissao[1]."-".$dataEmissao[0];
            $dataEmissaoRg  = "'$dataEmissao'";
            
        }else {
            $dataEmissaoRg = "null";
        }
        
        if($_POST['prpnas_dt'] != '' && !empty($_POST['prpnas_dt'])) {
            $dataNas = explode("/",$_POST['prpnas_dt']);
            $dataNasc = $dataNas[2]."-".$dataNas[1]."-".$dataNas[0];
            $dataNascFisica  = "'$dataNasc'";
            
        }else{
            $dataNascFisica = "null";
        }
        
        if($_POST['prpfund_dt'] != ''  && !empty($_POST['prpfund_dt'])) {
            $dataFundacaoJuridica = $_POST['prpfund_dt'];
            //$dataFundacao = explode("/",$_POST['prpfund_dt']);
            //$dataFundacaoJur = $dataFundacao[2]."-".$dataFundacao[1]."-".$dataFundacao[0];
            $dataFundacaoJuridica  = "' $dataFundacaoJuridica'";
            
            
        }else{
            $dataFundacaoJuridica = "null";
        }
        
        if($_POST['tipo_pagamento'] != ''  && !empty($_POST['tipo_pagamento'])) {
            $tipoPagamento = $_POST['tipo_pagamento'];
            
        }else{
            $tipoPagamento = "null";
        }
        
        if($_POST['data_vencimento'] != ''  && !empty($_POST['data_vencimento']) ) {
            $dataVencimento = $_POST['data_vencimento'];
        }else{
            $dataVencimento = "null";
        }
        
        if($_POST['idBanco'] != '' && !empty($_POST['idBanco'])) {
            $idBanco = $_POST['idBanco'];
        }else{
            $idBanco = "null";
        }
        
        if($_POST['prpend_cid'] == '' && empty($_POST['prpend_cid'])){
            $cidadend = $_POST['prpend_cidade'];
        }else{
            $cidadend = $_POST['prpend_cid'];
        }
        
        if($_POST['prpend_bairro'] == '' && empty($_POST['prpend_bairro'])){
            $bairroEnd = $_POST['prpend_combobairro'];
        }else{
            $bairroEnd = $_POST['prpend_bairro'];
        }
        
        
        if($_POST['prpendcob_cid'] == '' && empty($_POST['prpendcob_cid'])){
            $cidadeCobr = $_POST['prpendCob_cidade'];
        }else{
            $cidadeCobr = $_POST['prpendcob_cid'];
        }
        
        if($_POST['prpendcob_bairro'] == '' && empty($_POST['prpendcob_bairro'])){
            $bairroCobr = $_POST['prpend_combobairrocobr'];
        }else{
            $bairroCobr = $_POST['prpendcob_bairro'];
        }
        
        $cd_usuario = $_SESSION["usuario"]["oid"];
        $dados = array(
            'ptcptraoid' => $_POST['ptraoid'],
            'ptcnumdocumento' =>$prtno_documento,
            'ptcnome' =>$_POST['prtcontratante'],
            'ptcrg' => $_POST['prtrg'],
            'ptcorgaoemissor' => $_POST['prtrgorgaoemissor'],
            'ptcdataemissao' => $dataEmissaoRg,
            'ptcdatanasc' => $dataNascFisica,
            'ptcnomepai' => $_POST['prtfiliacaopai'],
            'ptcnomemae' => $_POST['prtfiliacaoMae'],
            'ptcsexo' => $_POST['prtsexo'],
            'ptcivil' => $_POST['prtestado_civil'],
            'ptcoptantesimples' => $_POST['prtoptante_simples']	,
            'ptcdatafundacao' =>$dataFundacaoJuridica,
            'ptcestadoinscricaoest' =>$_POST['prtie_estado'],
            'ptcinscricaoest' => $_POST['prtie_num'],
            'ptctipopessoa' =>$_POST['prtipopessoa'],
            'ptendpaisoid' => $paisEnd,
            'ptendestoid' => $estadoEnd,
            'ptendcep' => $cep,
            'ptendcidade' => $cidadend,
            'ptendbairro' => $bairroEnd,
            'ptendlogradouro' => $_POST['prpend_log'],
            'ptendnumero' => $endNumero,
            'ptendcomplemento' =>$_POST['prpend_compl'],
            'ptendfone' => $prcfone_cont,
            'ptendfone2' => $prcfone_cont2,
            'ptendfone3' => $prcfone_cont3,
            'ptendemail' => $_POST['prpend_email'],
            'ptendemailnf' => $_POST['prpend_emailnf'],
            'ptendcobpaisoid' => $paisEndCobr,
            'ptendcobestoid' => $estadoEndCobr,
            'ptendcobcep' => $cepCob,
            'ptendcobcidade' => $cidadeCobr ,
            'ptendcobbairro' => $bairroCobr,
            'ptendcoblogradouro' => $_POST['prpendcob_log'],
            'ptendcobnumero' => $endNumeroCobr,
            'prpendcob_compl' => $_POST['prpendcob_compl'],
            'ptfpforcoid' => $tipoPagamento,
            'ptfpcdvoid' => $dataVencimento,
            'ptfpbancodigo' => $idBanco,
            'ptfpagencia' => $_POST['nAgencia'],
            'ptfpnumconta' => $_POST['nConta'],
            'ptfpnumcartaocredito' =>$nCartao,
            'ptfpvalidadeCartaoCredito' => $_POST['dataCartao'],
            'usuoid' => $cd_usuario
            
        );
        
        
        //se o cliente já existir atualiza senão salva e retorno o id do cliente
        $returnCliente = $this->dao->SaveAtualizaCliente($dados);
        
        //recebe o id do cliente
        $idCliente = $returnCliente;
        
        //busca todo contratos da proposta passando o idproposta
        $retornoContratos = $this->dao->listContratoIdProposta($idProposta);
        
        //Retorna as Notas Atrasadas
        $NotasAtrasadas = $this->notaAtrasadaAction($idProposta);
        
        $count = 0;
        
        //$titulosEmAberos = $this->titulosEmAberto($idProposta);
        
        foreach ($retornoContratos as $key) {
            
            
            $ncontrato = $key['pttcoconoid'];
            
            
            //retorna o valor do monitoramento passando o numero do contrato
            $valormonitoramento = $this->dao->retornaValorMonitoramentoSerieA($ncontrato);
            
            //retorna a quantidade de parcelas faltantes do contrato
            $qtdaParcelas = $this->dao->retornaQtdaParcelasContrato($ncontrato);
            
            if( $qtdaParcelas == '' || $qtdaParcelas == null || $qtdaParcelas <= 0){
                echo json_encode ( array (
                    "status" => "errorContrato",
                    "message" => utf8_encode("contrato $ncontrato não possuí contrato pagamento"),
                    "redirect" => ""
                ) );
                return;
                exit();
            }
            
            $obrigacaofinanceira =$this->dao->retornaObrigacaoFinanceiraFaturada($ncontrato);
            
            
            $idObg = $obrigacaofinanceira['obroid'];
            
            // retorna quantidade de parcelas faturadas
            $parcelasfaturadas = $this->dao->retornaParcelasFaturadasObrigacaoContratoID($ncontrato,$idObg);
            
            
            
            
            
            /* variaveis
             prcContratoAntigo //parcelamento contrato antigo
             valorLocacaoAntigo //valor de locacao contrato antigo
             quantidadeFaturada //quantas parcelas foram faturadas no contrato antigo
             prcFaltante // quantas parcelas faltam
             novoPrc // novo parcelamento
             
             conta
             
             
             prcFaltante = prcContratoAntigo - quantidadeFaturada
             
             totalFaltante = valorLocacaoAntigo * prcFaltante
             valorAtualLocacao =  totalFaltante / novoPrc*/
            
            //retorna o valor de parcelas que ainda falta para finalizar
            $totalParcelas = $qtdaParcelas['cpvparcela'] -  $parcelasfaturadas['count'] ;
            
            //retorna a quantidade de parcelas para o novo contrato
            $condicoesPagamento = $this->dao->retornaCondicoesPagamentoNovoContrato($totalParcelas);
            
            // se a condição de pagamento vir vazia , e setado com o id que seria pagamento a vista senão pega o id de condição de pagamento
            if($condicoesPagamento['cpvoid'] == '' || empty($condicoesPagamento['cpvoid']) || $condicoesPagamento['cpvoid'] == null) {
                $idCondicaoPag = 13;
            }else {
                $idCondicaoPag = $condicoesPagamento['cpvoid'];
            }
            
            if($qtdaParcelas['cpvparcela'] <=  $parcelasfaturadas['count']) {
                $valorParcelaNovoContrato = 0.01;
            }else{
                
                //[cpvparcela] => 36
                //[obroid] => 1179
                //[count] => 7 = parcelas faturadas
                // parcelas faltantes 29
                // [valor] => 26.38
                
                //pego o retorno de locação paga atualmente
                $valorLocacao = $this->dao->retornaValorPagoLocacao($ncontrato);
                
                
                
                //Obtido o valor da locação o sistema deverá fazer a seguinte conta do valor das parcelas do novo contrato
                //$valorParcelaNovoContrato = (($qtdaParcelas['cpvparcela']  * $valorLocacao['valor'])/ $condicoesPagamento['cpvparcela']);
                $valorParcelaNovoContrato = (($totalParcelas  * $valorLocacao['valor'])/ $condicoesPagamento['cpvparcela']);
                
                //33.917142857143
                //27.322142857143
                
                
            }
            
            //retorna Código da Obrigacao Financeira do Servico
            $retornoObgFinServico = $this->dao->retornaObrigacaoFinanceiroContrato($ncontrato);
            
            $dataVencimento =  $this->retornaDataVencimento();
            $dataRef = explode("-",$dataVencimento);
            $ano = $dataRef[0];
            $mes = $dataRef[1];
            $dia = $dataRef[2];
            $dataReferencia = $ano."-".$mes."-"."01";
            
            
            $data[$ncontrato] = array(
                'contrato' => $key['pttcoconoid'],
                'idCliente' => $idCliente,
                'usuario' => $cd_usuario,
                'formapagamento' => $tipoPagamento,
                'valormonitoramento' => $valormonitoramento['valor'],
                'condicoespagamento' => $idCondicaoPag,
                'obrfinanceiraserv' => $retornoObgFinServico['eqcobroid'],
                'valorparcelanovocontrato' => $valorParcelaNovoContrato,
                'taxa' => $_POST['taxa'],
                'dataReferencia' => $dataReferencia,
                'dataVencimento' =>$this->retornaDataVencimento(),
            );
            
            
            
            //filtra os registros do contrato que está sendo transferido de obrigacoes e acessorios.
            $retornaRegistrosContratosTransferidos = $this->dao->retornaContratosTransferidosObrigacoesAcessorios($ncontrato);
            
            
            if(count($retornaRegistrosContratosTransferidos) > 0 && !empty($retornaRegistrosContratosTransferidos) || $retornaRegistrosContratosTransferidos != null) {
                
                foreach ($retornaRegistrosContratosTransferidos as $key) {
                    
                    $parcelasEquipamentoNovoValor = 0;
                    
                    if(!empty($key['constadoid']) || $key['constadoid'] != ''){
                        
                        //obter as referencia do campo tadoid para atualizar o tadclioid
                        $referenciaTermoAditivo = $this->dao->retornaReferenciaContratoTransferido($key['constadoid']);
                        
                        ////Obter a condição de pagamento do contrato que está sendo transferido
                        $condicaoPagamentoAntigo = $this->dao->retornaCondicaoPagamentoAntigoContratoTransferido($key['constadoid']);
                        
                        //obter a quantidade de parcelas restantes para a locação do acessório
                        $retornoParcelasFaltanteLocacao = $this->dao->retornaParcelasFaltantesLocacaoAcessorio($ncontrato,$key['consobroid']);
                        
                        //calculo do valor de parcelas restantes para o acessório
                        $totalParcelaRestanteAcessorios = ($condicaoPagamentoAntigo['cpvparcela'] - $retornoParcelasFaltanteLocacao['parcelasfaltantes']);
                        
                        //calcula o valor total faltante
                        $valorTotalFaltante  = ($totalParcelaRestanteAcessorios * $key['consvalor'] );
                        
                        //obter a nova quantidade de parcelas que o equipamento se encaixará
                        $qtdaParcelasEquipamento = $this->dao->retornaCondicoesPagamentoNovoContrato($totalParcelaRestanteAcessorios);
                        
                        //ober o novo valor das parcelas
                        $parcelasEquipamentoNovoValor = ($valorTotalFaltante / $qtdaParcelasEquipamento['cpvparcela']);
                        
                        //atualiza o cadastro do contrato de serviço e id do novo cliente
                        //$upadateContratoServidoUsuario = $this->dao->updateContratoServico($key['consoid'],$retornoContrato,$parcelasEquipamentoNovoValor);
                        
                        //se tiver termo aditivo atualiza o novo cliente
                        //$updateTermoAditivo = $this->dao->updateTermoAditivo($idCliente,$referenciaTermoAditivo['tadclioid']);
                        
                        $data[$ncontrato]['obr'][] = array('consoid' => $key['consoid'],
                            'constadoid' => $key['constadoid'],
                            'novovalor' =>$parcelasEquipamentoNovoValor,
                            'idCliente' => $idCliente,
                            'referencia' => $referenciaTermoAditivo['tadclioid']);
                        
                    }else{
                        
                        
                        // retorna quantidade de parcelas faturadas
                        $parcelasfaturadas = $this->dao->retornaParcelasFaturadasObrigacaoContratoID($ncontrato,$key['consobroid']);
                        
                        //retorna o valor de parcelas que ainda falta para finalizar
                        $totalParcelasObrAcessorios = $qtdaParcelas['cpvparcela'] -  $parcelasfaturadas['count'] ;
                        
                        //retorna a quantidade de parcelas para o novo contrato
                        $condicoesPagamentoAsc = $this->dao->retornaCondicoesPagamentoNovoContrato($totalParcelasObrAcessorios);
                        
                        
                        if($qtdaParcelas['cpvparcela'] <=  $parcelasfaturadas['count']) {
                            $parcelasEquipamentoNovoValor = 0.01;
                        }else{
                            //Obtido o valor da locação o sistema deverá fazer a seguinte conta do valor das parcelas do novo contrato
                            $parcelasEquipamentoNovoValor  = (($totalParcelasObrAcessorios * $key['consvalor'] ) /  $condicoesPagamentoAsc['cpvparcela']);
                        }
                        
                        
                        
                        $data[$ncontrato]['obr'][] = array(
                            'consoid' => $key['consoid'],
                            'constadoid' => $key['constadoid'],
                            'novovalor' =>$parcelasEquipamentoNovoValor,
                            'idCliente' => '',
                            'referencia' =>''
                        );
                        
                        //atualiza o cadastro do contrato de serviço e id do novo cliente
                        //$upadateContratoServidoUsuario = $this->dao->updateContratoServico($key['consoid'],$retornoContrato,$valorTotalFaltanteNovoValor);
                    }
                }
                
                
                
                
            }
            $valorContrato = 0;
            //--------------------------------Regra 6.12 --------------------------------------//
            if($NotasAtrasadas != '' || $NotasAtrasadas != null || $NotasAtrasadas > 0) {
                
                foreach ($NotasAtrasadas as $key) {
                    $valorContrato = $key['valor_contrato'];
                }
                
                if($count == 0) {
                    
                    foreach ($NotasAtrasadas as $key) {
                        $valorTotalNotasAtrasadas = $key['nflvl_total'];
                    }
                    $dataVencimento =  $this->retornaDataVencimento();
                    
                    $dataRef = explode("-",$dataVencimento);
                    $ano = $dataRef[0];
                    $mes = $dataRef[1];
                    $dia = $dataRef[2];
                    $dataReferencia = $ano."-".$mes."-"."01";
                    
                    $data[$ncontrato]['notaAtrasadas'][] = array(
                        'nfldt_referencia'  => $dataReferencia,
                        'nfldt_vencimento' => $this->retornaDataVencimento(),
                        'nflusuoid' => $cd_usuario,
                        'nflclioid' =>$idCliente,
                        'nflvl_total'=>$valorTotalNotasAtrasadas
                    );
                    
                    
                    
                }
            }
            
            //verificar se tem notas para vencer passando o contrato
            $NotaAVencer = $this->dao->retornaNotasNaoVencidas($ncontrato);
            
            if( $NotaAVencer != '' || $NotaAVencer != null || $NotaAVencer > 0){
                
                foreach ($NotaAVencer as $row) {
                    $nflvl_total = str_replace(".", "", $row['nflvl_total']);
                    $nflvl_contrato = str_replace(".", "", $row['valor_contrato']);
                    
                    
                    if($nflvl_total == $nflvl_contrato) {
                        
                        $data[$ncontrato]['titulo'][] = array(
                            'titoid' => $row['titoid'],
                            'titusuoid_alteracao' => $cd_usuario,
                            'titvl_titulo' =>'',
                            'baixatotalnota' => true
                        );
                        
                    }else{
                        
                        $valor = ($row['nflvl_total'] - $row['valor_contrato']);
                        
                        $data[$ncontrato]['titulo'][] = array(
                            'titoid' => $row['titoid'],
                            'titusuoid_alteracao' => $cd_usuario,
                            'titvl_titulo' =>$valor,
                            'baixatotalnota' => false
                        );
                        
                        //se a data de referencia for vazia pega o primeiro dia do mês
                        if($row['titdt_referencia'] == '' || $row['titdt_referencia'] == null || empty($row['titdt_referencia'])) {
                            $titdt_referencia = date("Y-m");
                            $dataAtualReferencia = $titdt_referencia."-01";
                        }else{
                            $dataAtualReferencia = $row['titdt_referencia'];
                        }
                        
                        $data[$ncontrato]['tituloInsere'][] = array(
                            'titoid' => $row['titoid'],
                            'titnfloid' => $row['titnfloid'] ,
                            'titdt_referencia' =>$dataAtualReferencia,
                            'titdt_vencimento'=>$row['titdt_vencimento'],
                            'titno_parcela'=>$row['titno_parcela'],
                            'titclioid'=>$row['titclioid'],
                            'titusuoid_alteracao' => $cd_usuario,
                            'titvl_titulo' => $row['valor_contrato'],
                            'titvl_contrato' => $valorContrato
                        );
                    }
                }
            }
            
            
            
            
            
            
            if($count == 0){
                $dataVencimento =  $this->retornaDataVencimento();
                $dataRef = explode("-",$dataVencimento);
                $ano = $dataRef[0];
                $mes = $dataRef[1];
                $dia = $dataRef[2];
                $dataReferencia = $ano."-".$mes."-"."01";
                $data[$ncontrato]['taxaTransferencia'][] = array(
                    'nfldt_referencia'  => $dataReferencia,
                    'nfldt_vencimento' => $this->retornaDataVencimento(),
                    'nflusuoid' => $cd_usuario,
                    'nflclioid' =>$idCliente,
                    'nflvl_total'=>$_POST['taxa']
                );
            }
            
            
            
            
            $count= $count + 1;
            
        }
        
        
        
        
        
        //@@@@@gera o novo contrato para o cliente
        $retornoContrato = $this->dao->geranovoContrato($data,$idProposta);
        
        if($retornoContrato != '' && isset($retornoContrato) && $retornoContrato != null) {
            
            
            echo json_encode ( array (
                "status" => "sucess",
                "message" => utf8_encode("Termo gerado com sucesso"),
                "cliente" => $idCliente,
                "redirect" => ""
            ) );
            return;
        }else{
            echo json_encode ( array (
                "status" => "errorGeracao",
                "message" => utf8_encode("Probramas para gerar termo"),
                "redirect" => ""
            ) );
            return;
        }
        
        
    }
    
    
    //@retorna todas as notas que estão em atraso
    public function notaAtrasadaAction($idProposta){
        
        //busca todo contratos da proposta passando o idproposta
        $retornoContratos = $this->dao->listContratoIdProposta($idProposta);
        
        
        foreach ($retornoContratos as $key) {
            $contratos .= $key['pttcoconoid'].",";
        }
        
        $ncontratos = strlen($contratos);
        
        $contratos = substr($contratos,0, $ncontratos-1);
        
        //retorna notas em atraso passando todos os contratos da transferencia
        $notaAtrasadas = $this->dao->retornaNotasEmAtraso($contratos);
        
        
        return $notaAtrasadas;
    }
    
    //@busca os titulos que estão em aberto
    public function titulosEmAberto($idProposta){
        
        
        //busca todo contratos da proposta passando o idproposta
        $retornoContratos = $this->dao->listContratoIdProposta($idProposta);
        
        foreach ($retornoContratos as $key) {
            $contratos .= $key['pttcoconoid'].",";
        }
        
        $ncontratos = strlen($contratos);
        
        $contratos = substr($contratos,0, $ncontratos-1);
        
        
        //busca os titulos em aberto passando o numero dos contratos
        $titulosAbertos = $this->dao->retornaTitulosAbertos($contratos);
        
        
        
        return $titulosAbertos;
    }
    
    //retorna a datas de vencimento para quinze dias a partir da data atual, caso caia final de semana a data vencimento vai ser no proximo dia util
    public function retornaDataVencimento(){
        
        $data =  date('d/m/Y', strtotime("+15 days"));
        $dia =  substr($data,0,2);
        $mes =  substr($data,3,2);
        $ano =  substr($data,6,9);
        
        $dataVenc = date("w", mktime(0,0,0,$mes,22,$ano) );
        
        switch($dataVenc){
            case"0": $dataVenc = date('Y-m-d', strtotime("+16 days"));	   break;
            case"1": $dataVenc = date('Y-m-d', strtotime("+15 days")); break;
            case"2": $dataVenc = date('Y-m-d', strtotime("+15 days"));   break;
            case"3": $dataVenc = date('Y-m-d', strtotime("+15 days"));  break;
            case"4": $dataVenc = date('Y-m-d', strtotime("+15 days"));  break;
            case"5": $dataVenc = date('Y-m-d', strtotime("+15 days"));   break;
            case"6": $dataVenc = date('Y-m-d', strtotime("+17 days"));		break;
        }
        
        return $dataVenc;
    }
    
    //função para validar a IE passando o estado e numero da inscrição
    function validaIE($uf, $ie) {
        
        $uf = strtoupper($uf);
        $uf = trim($uf);
        $ie = strtoupper(preg_replace("[()-./,:]", "", $ie));
        $ie = trim($ie);
        
        
        
        if ($ie == 'ISENTO' or $ie == 'ISENTA') {
            return true;
        } else {
            
            switch ($uf) {
                case "AC":
                    if (strlen($ie) != 13) {
                        return 0;
                    } else {
                        if (substr($ie, 0, 2) != '01') {
                            return 0;
                        } else {
                            $b = 4;
                            $soma = 0;
                            for ($i = 0; $i <= 10; $i++) {
                                $soma += $ie[$i] * $b;
                                $b--;
                                if ($b == 1) {
                                    $b = 9;
                                }
                            }
                            $dig = 11 - ($soma % 11);
                            if ($dig >= 10) {
                                $dig = 0;
                            }
                            if (!($dig == $ie[11])) {
                                return 0;
                            } else {
                                $b = 5;
                                $soma = 0;
                                for ($i = 0; $i <= 11; $i++) {
                                    $soma += $ie[$i] * $b;
                                    $b--;
                                    if ($b == 1) {
                                        $b = 9;
                                    }
                                }
                                $dig = 11 - ($soma % 11);
                                if ($dig >= 10) {
                                    $dig = 0;
                                }
                                
                                return ($dig == $ie[12]);
                            }
                        }
                    }
                    break;
                    
                case "AL":
                    if (strlen($ie) != 9) {
                        return 0;
                    } else {
                        if (substr($ie, 0, 2) != '24') {
                            return 0;
                        } else {
                            $b = 9;
                            $soma = 0;
                            for ($i = 0; $i <= 7; $i++) {
                                $soma += $ie[$i] * $b;
                                $b--;
                            }
                            $soma *= 10;
                            $dig = $soma - (( (int) ($soma / 11) ) * 11);
                            if ($dig == 10) {
                                $dig = 0;
                            }
                            
                            return ($dig == $ie[8]);
                        }
                    }
                    break;
                    
                case "AM":
                    if (strlen($ie) != 9) {
                        return 0;
                    } else {
                        $b = 9;
                        $soma = 0;
                        for ($i = 0; $i <= 7; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                        }
                        if ($soma <= 11) {
                            $dig = 11 - $soma;
                        } else {
                            $r = $soma % 11;
                            if ($r <= 1) {
                                $dig = 0;
                            } else {
                                $dig = 11 - $r;
                            }
                        }
                        return ($dig == $ie[8]);
                    }
                    break;
                    
                case "AP":
                    if (strlen($ie) != 9) {
                        return 0;
                    } else {
                        if (substr($ie, 0, 2) != '03') {
                            return 0;
                        } else {
                            $i = substr($ie, 0, -1);
                            if (($i >= 3000001) && ($i <= 3017000)) {
                                $p = 5;
                                $d = 0;
                            } elseif (($i >= 3017001) && ($i <= 3019022)) {
                                $p = 9;
                                $d = 1;
                            } elseif ($i >= 3019023) {
                                $p = 0;
                                $d = 0;
                            }
                            
                            $b = 9;
                            $soma = $p;
                            for ($i = 0; $i <= 7; $i++) {
                                $soma += $ie[$i] * $b;
                                $b--;
                            }
                            $dig = 11 - ($soma % 11);
                            if ($dig == 10) {
                                $dig = 0;
                            } elseif ($dig == 11) {
                                $dig = $d;
                            }
                            
                            return ($dig == $ie[8]);
                        }
                    }
                    
                    break;
                    
                case "BA":
                    if (strlen($ie) != 8) {
                        return 0;
                    } else {
                        $arr1 = array('0', '1', '2', '3', '4', '5', '8');
                        $arr2 = array('6', '7', '9');
                        
                        $i = substr($ie, 0, 1);
                        
                        if (in_array($i, $arr1)) {
                            $modulo = 10;
                        } elseif (in_array($i, $arr2)) {
                            $modulo = 11;
                        }
                        
                        $b = 7;
                        $soma = 0;
                        for ($i = 0; $i <= 5; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                        }
                        
                        $i = $soma % $modulo;
                        if ($modulo == 10) {
                            if ($i == 0) {
                                $dig = 0;
                            } else {
                                $dig = $modulo - $i;
                            }
                        } else {
                            if ($i <= 1) {
                                $dig = 0;
                            } else {
                                $dig = $modulo - $i;
                            }
                        }
                        
                        if (!($dig == $ie[7])) {
                            return 0;
                        } else {
                            $b = 8;
                            $soma = 0;
                            for ($i = 0; $i <= 5; $i++) {
                                $soma += $ie[$i] * $b;
                                $b--;
                            }
                            $soma += $ie[7] * 2;
                            $i = $soma % $modulo;
                            if ($modulo == 10) {
                                if ($i == 0) {
                                    $dig = 0;
                                } else {
                                    $dig = $modulo - $i;
                                }
                            } else {
                                if ($i <= 1) {
                                    $dig = 0;
                                } else {
                                    $dig = $modulo - $i;
                                }
                            }
                            
                            return ($dig == $ie[6]);
                        }
                    }
                    break;
                    
                case "CE":
                    if (strlen($ie) != 9) {
                        return 0;
                    } else {
                        $b = 9;
                        $soma = 0;
                        for ($i = 0; $i <= 7; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                        }
                        $dig = 11 - ($soma % 11);
                        
                        if ($dig >= 10) {
                            $dig = 0;
                        }
                        
                        return ($dig == $ie[8]);
                    }
                    break;
                    
                case "DF":
                    if (strlen($ie) != 13) {
                        return 0;
                    } else {
                        if (substr($ie, 0, 2) != '07') {
                            return 0;
                        } else {
                            $b = 4;
                            $soma = 0;
                            for ($i = 0; $i <= 10; $i++) {
                                $soma += $ie[$i] * $b;
                                $b--;
                                if ($b == 1) {
                                    $b = 9;
                                }
                            }
                            $dig = 11 - ($soma % 11);
                            if ($dig >= 10) {
                                $dig = 0;
                            }
                            
                            if (!($dig == $ie[11])) {
                                return 0;
                            } else {
                                $b = 5;
                                $soma = 0;
                                for ($i = 0; $i <= 11; $i++) {
                                    $soma += $ie[$i] * $b;
                                    $b--;
                                    if ($b == 1) {
                                        $b = 9;
                                    }
                                }
                                $dig = 11 - ($soma % 11);
                                if ($dig >= 10) {
                                    $dig = 0;
                                }
                                
                                return ($dig == $ie[12]);
                            }
                        }
                    }
                    break;
                    
                case "ES":
                    if (strlen($ie) != 9) {
                        return 0;
                    } else {
                        $b = 9;
                        $soma = 0;
                        for ($i = 0; $i <= 7; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                        }
                        $i = $soma % 11;
                        if ($i < 2) {
                            $dig = 0;
                        } else {
                            $dig = 11 - $i;
                        }
                        
                        return ($dig == $ie[8]);
                    }
                    break;
                    
                case "GO":
                    if (strlen($ie) != 9) {
                        return 0;
                    } else {
                        $s = substr($ie, 0, 2);
                        
                        if (!( ($s == 10) || ($s == 11) || ($s == 15) )) {
                            return 0;
                        } else {
                            $n = substr($ie, 0, 7);
                            
                            if ($n == 11094402) {
                                if ($ie[8] != 0) {
                                    if ($ie[8] != 1) {
                                        return 0;
                                    } else {
                                        return 1;
                                    }
                                } else {
                                    return 1;
                                }
                            } else {
                                $b = 9;
                                $soma = 0;
                                for ($i = 0; $i <= 7; $i++) {
                                    $soma += $ie[$i] * $b;
                                    $b--;
                                }
                                $i = $soma % 11;
                                if ($i == 0) {
                                    $dig = 0;
                                } else {
                                    if ($i == 1) {
                                        if (($n >= 10103105) && ($n <= 10119997)) {
                                            $dig = 1;
                                        } else {
                                            $dig = 0;
                                        }
                                    } else {
                                        $dig = 11 - $i;
                                    }
                                }
                                
                                return ($dig == $ie[8]);
                            }
                        }
                    }
                    break;
                    
                case "MA":
                    if (strlen($ie) != 9) {
                        return 0;
                    } else {
                        if (substr($ie, 0, 2) != 12) {
                            return 0;
                        } else {
                            $b = 9;
                            $soma = 0;
                            for ($i = 0; $i <= 7; $i++) {
                                $soma += $ie[$i] * $b;
                                $b--;
                            }
                            $i = $soma % 11;
                            if ($i <= 1) {
                                $dig = 0;
                            } else {
                                $dig = 11 - $i;
                            }
                            
                            return ($dig == $ie[8]);
                        }
                    }
                    break;
                    
                case "MT":
                    if (strlen($ie) != 11) {
                        return 0;
                    } else {
                        $b = 3;
                        $soma = 0;
                        for ($i = 0; $i <= 9; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                            if ($b == 1) {
                                $b = 9;
                            }
                        }
                        $i = $soma % 11;
                        if ($i <= 1) {
                            $dig = 0;
                        } else {
                            $dig = 11 - $i;
                        }
                        
                        return ($dig == $ie[10]);
                    }
                    break;
                    
                case "MS":
                    if (strlen($ie) != 9) {
                        return 0;
                    } else {
                        if (substr($ie, 0, 2) != 28) {
                            return 0;
                        } else {
                            $b = 9;
                            $soma = 0;
                            for ($i = 0; $i <= 7; $i++) {
                                $soma += $ie[$i] * $b;
                                $b--;
                            }
                            $i = $soma % 11;
                            if ($i == 0) {
                                $dig = 0;
                            } else {
                                $dig = 11 - $i;
                            }
                            
                            if ($dig > 9) {
                                $dig = 0;
                            }
                            
                            return ($dig == $ie[8]);
                        }
                    }
                    break;
                    
                case "MG":
                    if (strlen($ie) != 13) {
                        return 0;
                    } else {
                        $ie2 = substr($ie, 0, 3) . '0' . substr($ie, 3);
                        
                        $b = 1;
                        $soma = "";
                        for ($i = 0; $i <= 11; $i++) {
                            $soma .= $ie2[$i] * $b;
                            $b++;
                            if ($b == 3) {
                                $b = 1;
                            }
                        }
                        $s = 0;
                        for ($i = 0; $i < strlen($soma); $i++) {
                            $s += $soma[$i];
                        }
                        $i = substr($ie2, 9, 2);
                        $dig = $i - $s;
                        if ($dig != $ie[11]) {
                            return 0;
                        } else {
                            $b = 3;
                            $soma = 0;
                            for ($i = 0; $i <= 11; $i++) {
                                $soma += $ie[$i] * $b;
                                $b--;
                                if ($b == 1) {
                                    $b = 11;
                                }
                            }
                            $i = $soma % 11;
                            if ($i < 2) {
                                $dig = 0;
                            } else {
                                $dig = 11 - $i;
                            }
                            
                            return ($dig == $ie[12]);
                        }
                    }
                    break;
                    
                case "PA":
                    if (strlen($ie) != 9) {
                        return 0;
                    } else {
                        if (substr($ie, 0, 2) != 15) {
                            return 0;
                        } else {
                            $b = 9;
                            $soma = 0;
                            for ($i = 0; $i <= 7; $i++) {
                                $soma += $ie[$i] * $b;
                                $b--;
                            }
                            $i = $soma % 11;
                            if ($i <= 1) {
                                $dig = 0;
                            } else {
                                $dig = 11 - $i;
                            }
                            
                            return ($dig == $ie[8]);
                        }
                    }
                    break;
                    
                case "PB":
                    if (strlen($ie) != 9) {
                        return 0;
                    } else {
                        $b = 9;
                        $soma = 0;
                        for ($i = 0; $i <= 7; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                        }
                        $i = $soma % 11;
                        if ($i <= 1) {
                            $dig = 0;
                        } else {
                            $dig = 11 - $i;
                        }
                        
                        if ($dig > 9) {
                            $dig = 0;
                        }
                        
                        return ($dig == $ie[8]);
                    }
                    break;
                    
                case "PR":
                    if (strlen($ie) != 10) {
                        return 0;
                    } else {
                        $b = 3;
                        $soma = 0;
                        for ($i = 0; $i <= 7; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                            if ($b == 1) {
                                $b = 7;
                            }
                        }
                        $i = $soma % 11;
                        if ($i <= 1) {
                            $dig = 0;
                        } else {
                            $dig = 11 - $i;
                        }
                        
                        if (!($dig == $ie[8])) {
                            return 0;
                        } else {
                            $b = 4;
                            $soma = 0;
                            for ($i = 0; $i <= 8; $i++) {
                                $soma += $ie[$i] * $b;
                                $b--;
                                if ($b == 1) {
                                    $b = 7;
                                }
                            }
                            $i = $soma % 11;
                            if ($i <= 1) {
                                $dig = 0;
                            } else {
                                $dig = 11 - $i;
                            }
                            
                            return ($dig == $ie[9]);
                        }
                    }
                    break;
                    
                case "PE":
                    if (strlen($ie) == 9) {
                        $b = 8;
                        $soma = 0;
                        for ($i = 0; $i <= 6; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                        }
                        $i = $soma % 11;
                        if ($i <= 1) {
                            $dig = 0;
                        } else {
                            $dig = 11 - $i;
                        }
                        
                        if (!($dig == $ie[7])) {
                            return 0;
                        } else {
                            $b = 9;
                            $soma = 0;
                            for ($i = 0; $i <= 7; $i++) {
                                $soma += $ie[$i] * $b;
                                $b--;
                            }
                            $i = $soma % 11;
                            if ($i <= 1) {
                                $dig = 0;
                            } else {
                                $dig = 11 - $i;
                            }
                            
                            return ($dig == $ie[8]);
                        }
                    } elseif (strlen($ie) == 14) {
                        $b = 5;
                        $soma = 0;
                        for ($i = 0; $i <= 12; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                            if ($b == 0) {
                                $b = 9;
                            }
                        }
                        $dig = 11 - ($soma % 11);
                        if ($dig > 9) {
                            $dig = $dig - 10;
                        }
                        
                        return ($dig == $ie[13]);
                    } else {
                        return 0;
                    }
                    break;
                    
                case "PI":
                    if (strlen($ie) != 9) {
                        return 0;
                    } else {
                        $b = 9;
                        $soma = 0;
                        for ($i = 0; $i <= 7; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                        }
                        $i = $soma % 11;
                        if ($i <= 1) {
                            $dig = 0;
                        } else {
                            $dig = 11 - $i;
                        }
                        if ($dig >= 10) {
                            $dig = 0;
                        }
                        
                        return ($dig == $ie[8]);
                    }
                    break;
                    
                case "RJ":
                    if (strlen($ie) != 8) {
                        return 0;
                    } else {
                        $b = 2;
                        $soma = 0;
                        for ($i = 0; $i <= 6; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                            if ($b == 1) {
                                $b = 7;
                            }
                        }
                        $i = $soma % 11;
                        if ($i <= 1) {
                            $dig = 0;
                        } else {
                            $dig = 11 - $i;
                        }
                        
                        return ($dig == $ie[7]);
                    }
                    break;
                    
                case "RN":
                    if (!( (strlen($ie) == 9) || (strlen($ie) == 10) )) {
                        return 0;
                    } else {
                        $b = strlen($ie);
                        if ($b == 9) {
                            $s = 7;
                        } else {
                            $s = 8;
                        }
                        $soma = 0;
                        for ($i = 0; $i <= $s; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                        }
                        $soma *= 10;
                        $dig = $soma % 11;
                        if ($dig == 10) {
                            $dig = 0;
                        }
                        
                        $s += 1;
                        return ($dig == $ie[$s]);
                    }
                    break;
                    
                case "RS":
                    if (strlen($ie) != 10) {
                        return 0;
                    } else {
                        $b = 2;
                        $soma = 0;
                        for ($i = 0; $i <= 8; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                            if ($b == 1) {
                                $b = 9;
                            }
                        }
                        $dig = 11 - ($soma % 11);
                        
                        if ($dig >= 10) {
                            $dig = 0;
                        }
                        
                        return ($dig == $ie[9]);
                    }
                    break;
                    
                case "RO":
                    if (strlen($ie) == 9) {
                        $b = 6;
                        $soma = 0;
                        for ($i = 3; $i <= 7; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                        }
                        $dig = 11 - ($soma % 11);
                        if ($dig >= 10) {
                            $dig = $dig - 10;
                        }
                        
                        return ($dig == $ie[8]);
                    } elseif (strlen($ie) == 14) {
                        $b = 6;
                        $soma = 0;
                        for ($i = 0; $i <= 12; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                            if ($b == 1) {
                                $b = 9;
                            }
                        }
                        $dig = 11 - ( $soma % 11);
                        if ($dig > 9) {
                            $dig = $dig - 10;
                        }
                        
                        return ($dig == $ie[13]);
                    } else {
                        return 0;
                    }
                    break;
                    
                case "RR":
                    if (strlen($ie) != 9) {
                        return 0;
                    } else {
                        if (substr($ie, 0, 2) != 24) {
                            return 0;
                        } else {
                            $b = 1;
                            $soma = 0;
                            for ($i = 0; $i <= 7; $i++) {
                                $soma += $ie[$i] * $b;
                                $b++;
                            }
                            $dig = $soma % 9;
                            
                            return ($dig == $ie[8]);
                        }
                    }
                    break;
                    
                case "SC":
                    if (trim(strlen($ie)) != 9) {
                        
                        return 0;
                    } else {
                        $b = 9;
                        $soma = 0;
                        for ($i = 0; $i <= 7; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                        }
                        $dig = 11 - ($soma % 11);
                        if ($dig <= 1) {
                            $dig = 0;
                        }
                        
                        return ($dig == $ie[8]);
                    }
                    break;
                    
                case "SP":
                    
                    if (strtoupper(substr($ie, 0, 1)) == 'P') {
                        if (strlen($ie) != 13) {
                            return 0;
                        } else {
                            $b = 1;
                            $soma = 0;
                            for ($i = 1; $i <= 8; $i++) {
                                $soma += $ie[$i] * $b;
                                $b++;
                                if ($b == 2) {
                                    $b = 3;
                                }
                                if ($b == 9) {
                                    $b = 10;
                                }
                            }
                            $dig = $soma % 11;
                            return ($dig == $ie[9]);
                        }
                    } else {
                        
                        if (strlen($ie) != 12) {
                            return 0;
                        } else {
                            $b = 1;
                            $soma = 0;
                            for ($i = 0; $i <= 7; $i++) {
                                $soma += $ie[$i] * $b;
                                $b++;
                                if ($b == 2) {
                                    $b = 3;
                                }
                                if ($b == 9) {
                                    $b = 10;
                                }
                            }
                            $dig = $soma % 11;
                            if ($dig > 9) {
                                $dig = 0;
                            }
                            
                            if ($dig != $ie[8]) {
                                return 0;
                            } else {
                                $b = 3;
                                $soma = 0;
                                for ($i = 0; $i <= 10; $i++) {
                                    $soma += $ie[$i] * $b;
                                    $b--;
                                    if ($b == 1) {
                                        $b = 10;
                                    }
                                }
                                $dig = $soma % 11;
                                
                                
                                return ($dig == $ie[11]);
                            }
                        }
                    }
                    break;
                    
                case "SE":
                    if (strlen($ie) != 9) {
                        return 0;
                    } else {
                        $b = 9;
                        $soma = 0;
                        for ($i = 0; $i <= 7; $i++) {
                            $soma += $ie[$i] * $b;
                            $b--;
                        }
                        $dig = 11 - ($soma % 11);
                        if ($dig > 9) {
                            $dig = 0;
                        }
                        
                        return ($dig == $ie[8]);
                    }
                    break;
                    
                case "TO":
                    if (strlen($ie) != 11) {
                        return 0;
                    } else {
                        $s = substr($ie, 2, 2);
                        if (!( ($s == '01') || ($s == '02') || ($s == '03') || ($s == '99') )) {
                            return 0;
                        } else {
                            $b = 9;
                            $soma = 0;
                            for ($i = 0; $i <= 9; $i++) {
                                if (!(($i == 2) || ($i == 3))) {
                                    $soma += $ie[$i] * $b;
                                    $b--;
                                }
                            }
                            $i = $soma % 11;
                            if ($i < 2) {
                                $dig = 0;
                            } else {
                                $dig = 11 - $i;
                            }
                            
                            return ($dig == $ie[10]);
                        }
                    }
                    break;
            }
        }
    }
    
    
    public function transferirEmMassaJson(){
        
        ob_start();
        
        header('Content-Type: application/json');
        
        try{
            $dataTransferenciasJson = file_get_contents('php://input');
            
            $dataTransferenciasObj = json_decode($dataTransferenciasJson);
            
            $dataTransferenciasArray = $this->mountTransferenciaDataArray($dataTransferenciasObj);
            
            $result = $this->transferirEmMassa($dataTransferenciasArray);
            
            $returnArray = array();
            
            $pathMessage = _MODULEDIR_ . 'Financas/View/fin_transferencia_titularidade/transferenciaMessage.php';
            
            $returnArray['success']                     = $result['success'];
            $returnArray['message']                     = utf8_encode($result['message']);
            $returnArray['message_view']                = utf8_encode( $this->getFileInclude($pathMessage, $result) );
            $returnArray['contratos_transferidos_view'] = '';
            $returnArray['contratos_errors_view']       = '';
            
            
            if( !empty($result['contratos_transferidos']) ){
                $contratosTransferidos = $result['contratos_transferidos'];
                $path = _MODULEDIR_ . 'Financas/View/fin_transferencia_titularidade/contratosTransferidos.php';
                $returnArray['contratos_transferidos_view'] = utf8_encode( $this->getFileInclude($path, $contratosTransferidos) );
            }
            
            if( !empty($result['contratos_errors']) ){
                $contratosErrors = $result['contratos_errors'];
                $path = _MODULEDIR_ . 'Financas/View/fin_transferencia_titularidade/contratos_erros.php';
                $returnArray['contratos_errors_view'] = utf8_encode( $this->getFileInclude($path, $contratosErrors) );
            }
            
            
            
            
        } catch (Exception $e) {
            
            $returnArray['success']                        = 0;
            $returnArray['message']                        = utf8_encode($e->getMessage());
            $returnArray['message_view']                   = '';
            $returnArray['contratos_transferidos_view']    = '';
            $returnArray['contratos_errors_view']          = '';
            
        }
        
        ob_end_clean();
        ob_flush();
        
        $jsonReturn = json_encode($returnArray);
        
        die($jsonReturn);
        
    }
    
    private function getFileInclude($path, $data){
        
        ob_start();
        include $path;
        $view = ob_get_clean();
        return $view;
    }
    
    
    public function convertRealTODecimal($valor){
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
        return $valor;
    }
    
    /*
     * @param mixed[] $dataTransferencias object type stdClass contendo dados dos contratos a serem transferidos.
     */
    private function mountTransferenciaDataArray(stdClass  $dataTransferenciasObj){
        
        $novoTitularObj  = $dataTransferenciasObj->novoTitular;
        $contratosObj    = $dataTransferenciasObj->contratos;
        
        $refidelizar     = $novoTitularObj->refidelizar;
        $prazoMeses      = $novoTitularObj->prazoIsencao;
        $dataCobrancaTaxa = $dataTransferenciasObj->dataInicioVigencia;
        $dataCobrancaTaxaa = $novoTitularObj->dataInicioVigencia;



        $dataTransferenciasArray = array();
        
        if(!$novoTitularObj->cpfCnpj){
            throw new Exception("ERRO: Novo Titular (cpfCnpj) não informado.");
        }

        if($refidelizar == "sim" && $prazoMeses == "-"){
            throw new Exception("ERRO: Meses de Vigência não informado.");
        }

        $dataTransferenciasArrayTeste = (array) $dataTransferenciasObj;
        if(empty($dataTransferenciasArrayTeste)){
            throw new Exception('ERRO: Nenhum Contrato selecionado para transferência.');
        }
        
        unset($dataTransferenciasArrayTeste);
        
        $novoTitularObj->cpfCnpj =  preg_replace('/[^0-9]/', '', $novoTitularObj->cpfCnpj);
        
        $valorTaxaInstalacao = $novoTitularObj->valorTaxaInstalacao;
        $valorTaxaInstalacao = '0,00';
        
        $novoTitularArray = $this->dao->consulaClienteID($novoTitularObj->id);

        $clitipoNumber = '';
        
        if($novoTitularArray['clitipo'] === 'J')
            $clitipoNumber = 2;
        else if($novoTitularArray['clitipo'] === 'F')
            $clitipoNumber = 1;
        
        foreach($dataTransferenciasObj->contratos as $key => $contratoObj){
           
            $contratoNumero      = $contratoObj->contrato;
            $valorAcessorios     = $contratoObj->valorAcessorios;
            $valorMonitoramento  = $contratoObj->valorMonitoramento;
            $valorLocacao        = $this->convertRealTODecimal( $contratoObj->valorLocacao);
            $dataInicioVigencia  = $contratoObj->dataInicioVigencia;
            
            
            $dataCad = date('Y-m-d H:m:s'); //$contratoResult['dataCad'];
            $indice_vet = strtotime($dataCad);
            
            if(!$contratoNumero){
                throw new Exception("ERRO: um item não possui número do contrato.");
                
            }
            
            if(empty($contratoNumero)){
                throw new Exception('ERRO: Contrato: número não informado.');
            }
            
            $dataResult = $this->dao->pesquisaContrato($contratoNumero);
            
            if(empty($dataResult)){
                throw new Exception('ERRO: Contrato: número "'.$contratoNumero .'" não existe.');
            }
            
            $contratoResult = $dataResult[0];
            
            
            $dataTransferenciasArray[$key] = $contratoResult[0];
            
            $dataTransferenciasArray[$key]['refidelizar'] = $refidelizar;
            $dataTransferenciasArray[$key]['prazoMeses'] = $prazoMeses;

            $dataTransferenciasArray[$key]['acao'] = 'confirmar';
            $dataTransferenciasArray[$key]['acaoTaxaInstalacao'] = '';
            $dataTransferenciasArray[$key]['origem_chamada'] = 'PC';
            $dataTransferenciasArray[$key]['documento_pesquisado'] = '';
            $dataTransferenciasArray[$key]['entrada'] = 'I';
            
            
            $dataTransferenciasArray[$key]['indice_vet'] = $indice_vet;
                       
            $dataInicioVigenciaArray = explode('/', $dataInicioVigencia);
            
            $dataInicioVigenciaTimestamp = $dataInicioVigenciaArray[2] . '-' . $dataInicioVigenciaArray[1] . '-' . $dataInicioVigenciaArray[0];
            
            $dataTransferenciasArray[$key]['data_inicio_vigencia_timestamp'] = $dataInicioVigenciaTimestamp;
            $asd = $dataTransferenciasArray[$key]['data_inicio_vigencia_timestamp'];
            
            $dataTransferenciasArray[$key]['locacao_valor'] = $valorLocacao;
            //$dataTransferenciasArray[$key]['prpoid']   =  $contratoResult['prpoid'];
            
            $dataTransferenciasArray[$key]['prpoid_todos'] = '';
            
            $dataTransferenciasArray[$key]['migrar_contrato'] = '';
            $dataTransferenciasArray[$key]['prospostaCliente'] =  '';
            $dataTransferenciasArray[$key]['pcoroid'] = $contratoResult['pcoroid'];
            $dataTransferenciasArray[$key]['prptpcseguradora'] = 'f';
            $dataTransferenciasArray[$key]['prptpcassociacao'] = $contratoResult['prptpcassociacao'];
            $dataTransferenciasArray[$key]['prpautorizacao_alcada'] = $contratoResult['prpautorizacao_alcada'];
            $dataTransferenciasArray[$key]['prpautorizacao_tecnica'] = $contratoResult['prpautorizacao_tecnica'];
            $dataTransferenciasArray[$key]['prpstatus_aprovacao_taxa'] =  $contratoResult['prpstatus_aprovacao_taxa'];
            $dataTransferenciasArray[$key]['materialExclusao'] =$contratoResult['materialExclusao'];
            $dataTransferenciasArray[$key]['tipoAutorizacaoDesconto'] = $contratoResult['tipoAutorizacaoDesconto'];
            $dataTransferenciasArray[$key]['tpcfamilia'] = $contratoResult['tpcfamilia'];
            $dataTransferenciasArray[$key]['prpclifunoid'] = $contratoResult['prpclifunoid'];
            $dataTransferenciasArray[$key]['obrvl_minimo'] = $contratoResult['obrvl_minimo'];
            $dataTransferenciasArray[$key]['obrvl_maximo'] = $contratoResult['obrvl_maximo'];
            $dataTransferenciasArray[$key]['pagador'] = $contratoResult['pagador'];
            $dataTransferenciasArray[$key]['hiddenAcp245'] = 'false';
            $dataTransferenciasArray[$key]['dataCad'] = $dataCad;
            $dataTransferenciasArray[$key]['prptipo_proposta'] = "T";
            $dataTransferenciasArray[$key]['id_proposta'] = 8;
            $dataTransferenciasArray[$key]['validar_familia_sascar'] = 'f';
            
            $dataTransferenciasArray[$key]['prptpcoid']  = 0; //$contratoResult['tpcoid']
            
            $dataTransferenciasArray[$key]['prpgera_os_instalacao'] = $contratoResult['prpgera_os_instalacao'];
            $dataTransferenciasArray[$key]['prpnum_veiculos'] = 1;
            
            
            $dataTransferenciasArray[$key]['prppropensao_churn'] = $contratoResult['prpoid'];
            $dataTransferenciasArray[$key]['prppropensao_compra'] = $contratoResult['prpoid'];
            $dataTransferenciasArray[$key]['prptermo_original'] = $contratoResult['connumero'];
            
            $dataTransferenciasArray[$key]['prpmsuboid'] = 1;
            
            $dataTransferenciasArray[$key]['cfp_cliente'] = $contratoResult['clioid'];
            
            $dataTransferenciasArray[$key]['cfp_clitipo'] = $novoTitularArray['clitipo'];
            
            $dataTransferenciasArray[$key]['cfp_nome'] = $contratoResult['clinome'];
            $dataTransferenciasArray[$key]['cfp_doc'] = $contratoResult['cfp_doc'];
            
            $dataTransferenciasArray[$key]['prpclioid'] =  $novoTitularArray['clioid']; 
            
            $dataTransferenciasArray[$key]['clioid'] = $novoTitularArray['clioid'];
            $dataTransferenciasArray[$key]['prptipo_pessoa'] = $clitipoNumber; 
                        
            $dataTransferenciasArray[$key]['prpno_cpf_cgc_fis'] = $novoTitularArray['clino_documento'];
            
            
            $dataTransferenciasArray[$key]['prpcpf_funcionario'] = $contratoResult['prpcpf_funcionario'];
            
                        
            $dataTransferenciasArray[$key]['prplocatario_fis']       = $novoTitularArray['clinome'];
            $dataTransferenciasArray[$key]['prpclicloid_fis']        = $novoTitularArray['cliclicloid'];
            
            $dataTransferenciasArray[$key]['prpno_rg']               = (!$novoTitularArray['clirg']) ? '' : $novoTitularArray['clirg'];
            $dataTransferenciasArray[$key]['prpemissor_rg']          = $novoTitularArray['cliemissor_rg'];
            $dataTransferenciasArray[$key]['prpdt_emissao_rg']       = $contratoResult['prpdt_emissao_rg'];
            
            $dataTransferenciasArray[$key]['prpdt_nascimento_fis']   = $novoTitularArray['clidt_nascimento'];
            
            $dataTransferenciasArray[$key]['prppai']                 = $contratoResult['prppai'];
            $dataTransferenciasArray[$key]['prpmae']                 = $contratoResult['prpmae'];
            $dataTransferenciasArray[$key]['prpsexo']                = $contratoResult['prpsexo'];
            $dataTransferenciasArray[$key]['prpestado_civil']        = $contratoResult['prpestado_civil'];
            
            $dataTransferenciasArray[$key]['prpno_cpf_cgc_jur']      = $novoTitularArray['clino_documento'];
            
            $dataTransferenciasArray[$key]['prpoptante_simples']     =  in_array($novoTitularArray['clireg_simples'], array('N', 'S') )  ?  $novoTitularArray['clireg_simples']: 'N';
           
            $dataTransferenciasArray[$key]['prplocatario_jur']       = $novoTitularArray['clinome'];
            
            $dataTransferenciasArray[$key]['prpclicloid_jur']        = $novoTitularArray['cliclicloid'];;
            $dataTransferenciasArray[$key]['prpinscricao_uf']        = $contratoResult['prpinscricao_uf'];
            $dataTransferenciasArray[$key]['prpinscricao']           = $contratoResult['prpinscricao'];
            $dataTransferenciasArray[$key]['prpinscricao_mun']       = $contratoResult['prpinscricao_mun'];
            $dataTransferenciasArray[$key]['prpclicnae']             = $contratoResult['prpclicnae'];
            //print_r($contratoResult);die;
            $dataTransferenciasArray[$key]['prpdt_nascimento_jur']   = $novoTitularArray['clidt_nascimento'];
            
            //Dados contato 1
            $dataTransferenciasArray[$key]['prpno_cep1']             = $novoTitularArray['endno_cep'];
            $dataTransferenciasArray[$key]['prppaisoid']             = $novoTitularArray['clipaisoid'];
            $dataTransferenciasArray[$key]['hd3_endereco']           = $novoTitularArray['endbairro'];
            $dataTransferenciasArray[$key]['prpuf1']                 = $novoTitularArray['enduf'];
            $dataTransferenciasArray[$key]['prpcidade1']             = $novoTitularArray['endcidade'];
            $dataTransferenciasArray[$key]['hd5_endereco']           = $novoTitularArray['endbairro'];
            $dataTransferenciasArray[$key]['prpbairro1']             = $novoTitularArray['endbairro'];
            $dataTransferenciasArray[$key]['prpendereco1']           = $novoTitularArray['endlogradouro'];
            $dataTransferenciasArray[$key]['prpno_endereco1']        = $novoTitularArray['endno_numero'];
            $dataTransferenciasArray[$key]['prpcompl1']              = $novoTitularArray['endcomplemento'];
            
            $dataTransferenciasArray[$key]['prptom_municipio']       = $contratoResult['prptom_municipio'];
            $dataTransferenciasArray[$key]['prpfone1']               = $novoTitularArray['fone1'];
            $dataTransferenciasArray[$key]['prpfone2']               = $novoTitularArray['fone2'];
            $dataTransferenciasArray[$key]['prpfone3']               = $novoTitularArray['fone3'];
            
            $dataTransferenciasArray[$key]['prpemail']               = $novoTitularArray['cliemail'];
            $dataTransferenciasArray[$key]['prpemail_nfe']           = $novoTitularArray['cliemail_nfe'];
            $dataTransferenciasArray[$key]['prpemail_nfe1']          = $novoTitularArray['cliemail_nfe'];
            $dataTransferenciasArray[$key]['prpemail_nfe2']          = $novoTitularArray['cliemail_nfe'];
            
            $dataTransferenciasArray[$key]['copiar_dados_cobranca']  = 't';

            //Dados contato
            $dataTransferenciasArray[$key]['prpccep']                = $novoTitularArray['endno_cep'];
            $dataTransferenciasArray[$key]['prpcpaisoid']            = $novoTitularArray['clipaisoid'];
            $dataTransferenciasArray[$key]['hd3_cobranca']           = $novoTitularArray['endbairro'];
            $dataTransferenciasArray[$key]['prpcuf']                 = $novoTitularArray['enduf'];
            $dataTransferenciasArray[$key]['prpccidade']             = $novoTitularArray['endcidade'];
            $dataTransferenciasArray[$key]['hd5_cobranca']           = $novoTitularArray['endbairro'];
            $dataTransferenciasArray[$key]['prpcbairro']             = $novoTitularArray['endbairro'];
            $dataTransferenciasArray[$key]['prpcrua']                = $novoTitularArray['endlogradouro'];
            $dataTransferenciasArray[$key]['prpcnumero']             = $novoTitularArray['endno_numero'];
            $dataTransferenciasArray[$key]['prpccomplemento']        = $novoTitularArray['endcomplemento'];
            $dataTransferenciasArray[$key]['prpccom_municipio']      = $novoTitularArray['endcidade'];
            
            $dataTransferenciasArray[$key]['copiar_dados']           = 't';
            
            $dataTransferenciasArray[$key]['prptipo_pessoa_prop']    = $novoTitularArray['clitipo'];
            $dataTransferenciasArray[$key]['prpproprietario']        = $contratoResult['prpproprietario'];
            
            $dataTransferenciasArray[$key]['prpno_cpf_cgc_prop_fis'] = $novoTitularArray['clino_documento'];
            $dataTransferenciasArray[$key]['prpno_cpf_cgc_prop_jur'] = $novoTitularArray['clino_documento'];
            
            $dataTransferenciasArray[$key]['prpno_cep2']             = $novoTitularArray['endno_cep'];
            $dataTransferenciasArray[$key]['hd3_proprietario']       = $novoTitularArray['endbairro'];
            $dataTransferenciasArray[$key]['prpuf2']                 = $novoTitularArray['enduf'];
            $dataTransferenciasArray[$key]['prpcidade2']             = $novoTitularArray['endcidade'];
            $dataTransferenciasArray[$key]['hd5_proprietario']       = $novoTitularArray['endbairro'];
            $dataTransferenciasArray[$key]['prpbairro2']             = $novoTitularArray['endbairro'];
            $dataTransferenciasArray[$key]['prpendereco2']           = $novoTitularArray['endlogradouro'];
            $dataTransferenciasArray[$key]['prpno_endereco2']        = $novoTitularArray['endno_numero'];
            $dataTransferenciasArray[$key]['prpcompl2']              = $novoTitularArray['endcomplemento'];
            $dataTransferenciasArray[$key]['prpccom_municipio2']     = $novoTitularArray['endcidade'];
            $dataTransferenciasArray[$key]['prpfone4']               = $novoTitularArray['fone1'];
            $dataTransferenciasArray[$key]['prpfone5']               = $novoTitularArray['fone2'];
            
            
            $dataTransferenciasArray[$key]['_termo_veiculo']         = $contratoResult['prptermo'];
            $dataTransferenciasArray[$key]['prpchassi']              = $contratoResult['veichassi'];
            $dataTransferenciasArray[$key]['prpplaca']               = $contratoResult['veiplaca'];
            $dataTransferenciasArray[$key]['prpmcaoid']              = (!$contratoResult['prpmcaoid']) ? 'null' : $contratoResult['prpmcaoid'];
            $dataTransferenciasArray[$key]['prpno_serie_veiculo']    = $contratoResult['prpno_serie_veiculo'];
            $dataTransferenciasArray[$key]['tipo_veiculo']           = $contratoResult['tipo_veiculo'];
            $dataTransferenciasArray[$key]['prpmlooid']              = $contratoResult['mlooid'];
            $dataTransferenciasArray[$key]['prpno_motor_veiculo']    = $contratoResult['veino_motor'];
            $dataTransferenciasArray[$key]['prpno_ano']              = $contratoResult['veino_ano'];
            $dataTransferenciasArray[$key]['prpcor']                 = $contratoResult['veicor'];
            
            $dataTransferenciasArray[$key]['prprenavam']             = $contratoResult['veino_renavan'];
            
            
            $dataTransferenciasArray[$key]['prpseguradora']          = $contratoResult['prpseguradora'];
            $dataTransferenciasArray[$key]['prpnumero_proposta']     = $contratoResult['prpnumero_proposta'];
            $dataTransferenciasArray[$key]['prpapolice']             = $contratoResult['prpapolice'];
            $dataTransferenciasArray[$key]['prpgeroid']              = $contratoResult['prpgeroid'];
            $dataTransferenciasArray[$key]['prputilizacao']          = $contratoResult['prputilizacao'];
            $dataTransferenciasArray[$key]['prpdt_inicio_seguro']    = $contratoResult['prpdt_inicio_seguro'];
            $dataTransferenciasArray[$key]['prpdt_fim_seguro']       = $contratoResult['prpdt_fim_seguro'];
            $dataTransferenciasArray[$key]['prpcod_cia']             = $contratoResult['prpcod_cia'];
            $dataTransferenciasArray[$key]['prpcod_unid_emis']       = $contratoResult['prpcod_unid_emis'];
            $dataTransferenciasArray[$key]['prpcod_ramo']            = $contratoResult['prpcod_ramo'];
            $dataTransferenciasArray[$key]['prpnum_item']            = $contratoResult['prpnum_item'];
            $dataTransferenciasArray[$key]['prpnum_adiantamento']    = $contratoResult['prpnum_adiantamento'];
            $dataTransferenciasArray[$key]['prpno_endosso']          = $contratoResult['prpno_endosso'];
            $dataTransferenciasArray[$key]['pembnome']               = $contratoResult['pembnome'];
            $dataTransferenciasArray[$key]['pembcomprimento']        = $contratoResult['pembcomprimento'];
            $dataTransferenciasArray[$key]['pembpotencia']           = $contratoResult['pembpotencia'];
            $dataTransferenciasArray[$key]['pembregistro']           = $contratoResult['pembregistro'];
            $dataTransferenciasArray[$key]['pembcasco']              = $contratoResult['pembcasco'];
            $dataTransferenciasArray[$key]['pembtransmissao']        = $contratoResult['pembtransmissao'];
            $dataTransferenciasArray[$key]['pembhelices']            = $contratoResult['pembhelices'];
            $dataTransferenciasArray[$key]['tmp_num_veiculos']       = $contratoResult['tmp_num_veiculos'];
            $dataTransferenciasArray[$key]['posicao_veiculo']        = $contratoResult['posicao_veiculo'];
            //$dataTransferenciasArray[$key]['quantidade_veiculos']    = $contratoResult['quantidade_veiculos'];
            $dataTransferenciasArray[$key]['prpdias_demonstracao']   = $contratoResult['prpdias_demonstracao'];
            $dataTransferenciasArray[$key]['execcontas']             = $contratoResult['execcontas'];
            $dataTransferenciasArray[$key]['prpregcoid']             = $contratoResult['prpregcoid'];
            $dataTransferenciasArray[$key]['prprczoid']              = $contratoResult['prprczoid'];
            $dataTransferenciasArray[$key]['telemkt']                = $contratoResult['telemkt'];
            $dataTransferenciasArray[$key]['propconta']              = $contratoResult['propconta'];
            $dataTransferenciasArray[$key]['prpcorroid']             = $contratoResult['prpcorroid'];
            $dataTransferenciasArray[$key]['prpcorretor']            = $contratoResult['prpcorretor'];
            $dataTransferenciasArray[$key]['prpcorroid_funcao_selecionar'] = $contratoResult['prpcorroid_funcao_selecionar'];
            $dataTransferenciasArray[$key]['prpcorroid_funcao_limpar'] = $contratoResult['prpcorroid_funcao_limpar'];
            $dataTransferenciasArray[$key]['prggeroid']              = $contratoResult['prggeroid'];
            $dataTransferenciasArray[$key]['prpcorroid_c']           = $contratoResult['prpcorroid_c'];
            $dataTransferenciasArray[$key]['prpcorretor_c']          = $contratoResult['prpcorretor_c'];
            $dataTransferenciasArray[$key]['prpcorroid_c_funcao_selecionar'] = $contratoResult['prpcorroid_c_funcao_selecionar'];
            $dataTransferenciasArray[$key]['prpcorroid_c_funcao_limpar']     = $contratoResult['prpcorroid_c_funcao_limpar'];
            $dataTransferenciasArray[$key]['prpemail_corretor']      = $contratoResult['prpemail_corretor'];
            $dataTransferenciasArray[$key]['prpfone_rescorretor']    = $contratoResult['prpfone_rescorretor'];
            $dataTransferenciasArray[$key]['prpfone_comcorretor']    = $contratoResult['prpfone_comcorretor'];
            $dataTransferenciasArray[$key]['prpfone_celcorretor']    = $contratoResult['prpfone_celcorretor'];
            $dataTransferenciasArray[$key]['prpgerente_neg']         = $contratoResult['prpgerente_neg'];
            $dataTransferenciasArray[$key]['prpdt_solicitacao']      = $contratoResult['prpdt_solicitacao'];
            $dataTransferenciasArray[$key]['prphr_solicitacao']      = $contratoResult['prphr_solicitacao'];
            $dataTransferenciasArray[$key]['prpmotivo']              = $contratoResult['prpmotivo'];
            $dataTransferenciasArray[$key]['prpcorretor_recebe_comissao']   = $contratoResult['prpcorretor_recebe_comissao'];
            $dataTransferenciasArray[$key]['tipo_associacao']        =  $contratoResult['tipo_associacao'];
            
            $dataTransferenciasArray[$key]['forcdebito_conta_anterior'] = $contratoResult['prpforcoid'];
            $dataTransferenciasArray[$key]['forcdebito_conta_selecionado_/'] = 'f';
            
            
            $dataTransferenciasArray[$key]['forccartao_credito_conta_selecionado'] = 'f';
            $dataTransferenciasArray[$key]['forma_atual_pagamento']                = $novoTitularArray['forcoid'];
            $dataTransferenciasArray[$key]['forma_atual_pagamento_cliente']        = $novoTitularArray['forcoid'];
            $dataTransferenciasArray[$key]['forma_pagamento_atual_debito_banco']   = $novoTitularArray['bancodigo'];
            $dataTransferenciasArray[$key]['forma_pagamento_atual_debito_agencia'] = $novoTitularArray['clicagencia'];
            $dataTransferenciasArray[$key]['forma_pagamento_atual_debito_conta']   = $novoTitularArray['forcdebito_conta'];
            $dataTransferenciasArray[$key]['forma_pagamento_cartao_credito']       = $novoTitularArray['forccobranca_cartao_credito'];
            $dataTransferenciasArray[$key]['forma_pagamento_atual_cartao_numero']  = $novoTitularArray['cliccartao'];
            $dataTransferenciasArray[$key]['cartaoCreditoAlterado']                = 'f';
            
            $dataTransferenciasArray[$key]['prpforcoid']                 = $novoTitularArray['forcoid'];
            
            $dataTransferenciasArray[$key]['prpdia_vcto_boleto']         = $novoTitularArray['clidia_vcto'];
            $dataTransferenciasArray[$key]['dataVencimentoAtualCliente'] = $novoTitularArray['clidia_vcto'];
            $dataTransferenciasArray[$key]['prpcartao']                  = $novoTitularArray['cliccartao'];
            $dataTransferenciasArray[$key]['nome_portador']              = $novoTitularArray['nome_portador'];
            
            $dataTransferenciasArray[$key]['prpcartao_validade']         = $novoTitularArray['cliccartao_validade'];
            $dataTransferenciasArray[$key]['prpbancodigo_cheque']        = $novoTitularArray['bancodigo'];
            $dataTransferenciasArray[$key]['prpcheque']                  = $contratoResult['prpcheque'];
            
            $dataTransferenciasArray[$key]['prpbancodigo_hidden']        = $novoTitularArray['bancodigo'];
            $dataTransferenciasArray[$key]['prpbancodigo']               = $novoTitularArray['bancodigo'];
            
            $dataTransferenciasArray[$key]['prpdebito_agencia']          = $novoTitularArray['clicagencia'];
            $dataTransferenciasArray[$key]['prpdebito_cc']               = $novoTitularArray['clicconta'];
            $dataTransferenciasArray[$key]['prpcampcoid']                = $contratoResult['prpcampcoid'];
            $dataTransferenciasArray[$key]['prpeqcoid']                  = $contratoResult['prpeqcoid'];
            $dataTransferenciasArray[$key]['prpobroid']                  = $contratoResult['prpobroid'];
            $dataTransferenciasArray[$key]['prpvl_tabela']               = $contratoResult['prpvl_tabela'];
            $dataTransferenciasArray[$key]['prpvl_minimo']               = $contratoResult['prpvl_minimo'];
            $dataTransferenciasArray[$key]['eqcprazo_inst']              = $contratoResult['eqcprazo_inst'];
            $dataTransferenciasArray[$key]['prpcpvoid']                  = $contratoResult['prpcpvoid'];
            $dataTransferenciasArray[$key]['hidden_prpvl_servico']       = $contratoResult['hidden_prpvl_servico'];
            $dataTransferenciasArray[$key]['prpvl_servico']              = $contratoResult['prpvl_servico'];
            
            $dataTransferenciasArray[$key]['prppercentual_desconto_locacao'] = 0;
            
            $dataTransferenciasArray[$key]['hidden_prpvl_monitoramento'] = '';
            $dataTransferenciasArray[$key]['prpvl_monitoramento']        = $valorMonitoramento;
            
            $dataTransferenciasArray[$key]['ppagvl_negociado_adesao']    = $contratoResult['ppagvl_negociado_adesao'];
            $dataTransferenciasArray[$key]['valorTaxaInstalacao']        = $contratoResult['valorTaxaInstalacao'];
            $dataTransferenciasArray[$key]['prpprazo_contrato']          = $contratoResult['prpprazo_contrato'];
            $dataTransferenciasArray[$key]['prpagmulta_rescissoria']     = $contratoResult['prpagmulta_rescissoria'];
            $dataTransferenciasArray[$key]['prpfamilia_produto']         = $contratoResult['prpfamilia_produto'];
            $dataTransferenciasArray[$key]['ppagvl_deslocamento']        = $contratoResult['ppagvl_deslocamento'];
            $dataTransferenciasArray[$key]['prosobroid']                 = $contratoResult['prosobroid'];
            $dataTransferenciasArray[$key]['prossituacao']               = $contratoResult['prossituacao'];
            $dataTransferenciasArray[$key]['prosvalor']                  = $contratoResult['prosvalor'];
            $dataTransferenciasArray[$key]['prosqtde']                   = $contratoResult['prosqtde'];
            $dataTransferenciasArray[$key]['proscontrato']               = $contratoResult['proscontrato'];
            $dataTransferenciasArray[$key]['prosno_cep']                 = $contratoResult['prosno_cep'];
            $dataTransferenciasArray[$key]['prospaisoid']                = $contratoResult['prospaisoid'];
            $dataTransferenciasArray[$key]['hd3_correios_servico']       = $contratoResult['hd3_correios_servico'];
            $dataTransferenciasArray[$key]['prosuf']                     = $contratoResult['prosuf'];
            $dataTransferenciasArray[$key]['proscidade']                 = $contratoResult['proscidade'];
            $dataTransferenciasArray[$key]['hd5_correios_servico']       = $contratoResult['hd5_correios_servico'];
            $dataTransferenciasArray[$key]['prosbairro']                 = $contratoResult['prosbairro'];
            $dataTransferenciasArray[$key]['prosendereco']               = $contratoResult['prosendereco'];
            $dataTransferenciasArray[$key]['prosno_endereco']            = $contratoResult['prosno_endereco'];
            $dataTransferenciasArray[$key]['proscompl']                  = $contratoResult['proscompl'];
            $dataTransferenciasArray[$key]['prosmun_ibge']               = $contratoResult['prosmun_ibge'];
            $dataTransferenciasArray[$key]['posicao_servico']            = $contratoResult['posicao_servico'];
            $dataTransferenciasArray[$key]['prosvalor_minimo']           = $contratoResult['prosvalor_minimo'];
            $dataTransferenciasArray[$key]['prosendoid_gerenciador']     = $contratoResult['prosendoid_gerenciador'];
            $dataTransferenciasArray[$key]['nao_instalar']               = $contratoResult['nao_instalar'];
            $dataTransferenciasArray[$key]['instalar']                   = $contratoResult['instalar'];
            
            $dataTransferenciasArray[$key]['ck_prosinstalar']['0']       = '';
            $dataTransferenciasArray[$key]['ck_prosinstalar']['1']       = '';
            $dataTransferenciasArray[$key]['ck_prosinstalar']['2']       = '';
            $dataTransferenciasArray[$key]['ck_prosinstalar']['3']       = '';
            $dataTransferenciasArray[$key]['ck_prosinstalar']['4']       = '';
            $dataTransferenciasArray[$key]['ck_prosinstalar']['5']       = '';
            $dataTransferenciasArray[$key]['ck_prosinstalar']['6']       = '';
            $dataTransferenciasArray[$key]['ck_prosinstalar']['7']       = '';
            $dataTransferenciasArray[$key]['ck_prosinstalar']['8']       = '';
            $dataTransferenciasArray[$key]['ck_prosinstalar']['9']       = '';
            
            //$dataTransferenciasArray[$key]['qtde_servicos']              = $contratoResult['qtde_servicos'];
            $dataTransferenciasArray[$key]['observacaoInstalacao']       = $contratoResult['observacaoInstalacao'];
            $dataTransferenciasArray[$key]['servobroid']                 = $contratoResult['servobroid'];
            $dataTransferenciasArray[$key]['servvalor']                  = $contratoResult['servvalor'];
            $dataTransferenciasArray[$key]['servcontrato']               = $contratoResult['servcontrato'];
            $dataTransferenciasArray[$key]['id_Acp245']                  = $contratoResult['id_Acp245'];
            $dataTransferenciasArray[$key]['priveiculo']                 = $contratoResult['priveiculo'];
            $dataTransferenciasArray[$key]['priddd']                     = $contratoResult['priddd'];
            
            $dataTransferenciasArray[$key]['prioploid']                  = 7;
            
            $dataTransferenciasArray[$key]['taxa_instalacao_pagamento_hidden']     = '';
            $dataTransferenciasArray[$key]['taxa_instalacao_parcelamento_hidden']  = '';
            $dataTransferenciasArray[$key]['taxa_instalacao_hidden']      = $contratoResult['taxa_instalacao_hidden'];
            $dataTransferenciasArray[$key]['taxa_instalacao_pagamento']   = $contratoResult['taxa_instalacao_pagamento'];
            $dataTransferenciasArray[$key]['taxa_instalacao_num_cartao']  = $contratoResult['taxa_instalacao_num_cartao'];
            $dataTransferenciasArray[$key]['taxa_instalacao_nome_portador'] = $contratoResult['taxa_instalacao_nome_portador'];
            $dataTransferenciasArray[$key]['taxa_instalacao_validade_cartao'] = $contratoResult['taxa_instalacao_validade_cartao'];
            $dataTransferenciasArray[$key]['taxa_instalacao_codigo_seguranca'] = $contratoResult['taxa_instalacao_codigo_seguranca'];
            $dataTransferenciasArray[$key]['taxa_instalacao_parcelamento'] = $contratoResult['taxa_instalacao_parcelamento'];
            $dataTransferenciasArray[$key]['taxa_instalacao_qntd_veiculos'] = $contratoResult['taxa_instalacao_qntd_veiculos'];
            
            $dataTransferenciasArray[$key]['taxa_instalacao_valor_maximo'] = '0,00';
            $dataTransferenciasArray[$key]['taxa_instalacao_valor_minimo'] = '0,00';
            
            $dataTransferenciasArray[$key]['taxa_instalacao_valor_unitario'] = '';
            $dataTransferenciasArray[$key]['taxa_instalacao_valor']      = '0,00';
            
            $dataTransferenciasArray[$key]['taxa_instalacao_parcela']    = $contratoResult['taxa_instalacao_parcela'];
            $dataTransferenciasArray[$key]['prcnome_aut']                = $contratoResult['prcnome_aut'];
            $dataTransferenciasArray[$key]['prccpf_aut']                 = $contratoResult['prccpf_aut'];
            $dataTransferenciasArray[$key]['prcrg_aut']                  = $contratoResult['prcrg_aut'];
            $dataTransferenciasArray[$key]['prcfone_res_aut']            = $contratoResult['prcfone_res_aut'];
            $dataTransferenciasArray[$key]['prcfone_com_aut']            = $contratoResult['prcfone_com_aut'];
            $dataTransferenciasArray[$key]['prcfone_cel_aut']            = $contratoResult['prcfone_cel_aut'];
            $dataTransferenciasArray[$key]['prcid_nextel_aut']           = $contratoResult['prcid_nextel_aut'];
            $dataTransferenciasArray[$key]['prcoid_aut']                 = $contratoResult['prcoid_aut'];
            $dataTransferenciasArray[$key]['replicar_aut']               = $contratoResult['replicar_aut'];
            $dataTransferenciasArray[$key]['tmp_pessoa_aut']             = 1;
            $dataTransferenciasArray[$key]['prcnome_eme']                = $contratoResult['prcnome_eme'];
            $dataTransferenciasArray[$key]['prcfone_res_eme']            = $contratoResult['prcfone_res_eme'];
            $dataTransferenciasArray[$key]['prcfone_com_eme']            = $contratoResult['prcfone_com_eme'];
            $dataTransferenciasArray[$key]['prcfone_cel_eme']            = $contratoResult['prcfone_cel_eme'];
            $dataTransferenciasArray[$key]['prcid_nextel_eme']           = $contratoResult['prcid_nextel_eme'];
            $dataTransferenciasArray[$key]['prcoid_eme']                 = $contratoResult['prcoid_eme'];
            $dataTransferenciasArray[$key]['replicar_eme']               = $contratoResult['replicar_eme'];
            $dataTransferenciasArray[$key]['tmp_pessoa_eme']             = $contratoResult['tmp_pessoa_eme'];
            $dataTransferenciasArray[$key]['prcnome_ins']                = $contratoResult['prcnome_ins'];
            $dataTransferenciasArray[$key]['prcfone_res_ins']            = $contratoResult['prcfone_res_ins'];
            $dataTransferenciasArray[$key]['prcfone_com_ins']            = $contratoResult['prcfone_com_ins'];
            $dataTransferenciasArray[$key]['prcfone_cel_ins']            = $contratoResult['prcfone_cel_ins'];
            $dataTransferenciasArray[$key]['prcid_nextel_ins']           = $contratoResult['prcid_nextel_ins'];
            $dataTransferenciasArray[$key]['prcobs_ins']                 = $contratoResult['prcobs_ins'];
            $dataTransferenciasArray[$key]['prcoid_ins']                 = $contratoResult['prcoid_ins'];
            $dataTransferenciasArray[$key]['replicar_ins']               = $contratoResult['replicar_ins'];
            $dataTransferenciasArray[$key]['tmp_pessoa_ins']             = 1;
            $dataTransferenciasArray[$key]['prpvl_adesao']               = '0,00';
            $dataTransferenciasArray[$key]['campoAutorizacao']           = $contratoResult['campoAutorizacao'];
            $dataTransferenciasArray[$key]['valorAutorizacao']           = $contratoResult['valorAutorizacao'];
            $dataTransferenciasArray[$key]['prpstatus']                  = $contratoResult['prpstatus'];
            $dataTransferenciasArray[$key]['prppsfoid']                  = 2;
            $dataTransferenciasArray[$key]['prpobservacao_financeiro']   = $contratoResult['prpobservacao_financeiro'];
            $dataTransferenciasArray[$key]['prpresultado_aciap']         = $contratoResult['prpresultado_aciap'];
            $dataTransferenciasArray[$key]['prpusuoid_aprovacao_fin']    = 2750;
            $dataTransferenciasArray[$key]['prphobs']                    = $contratoResult['prphobs'];
            
            
            //extras para adicionar veículo
            $dataTransferenciasArray[$key]['veiebs']                = $contratoResult['veiebs'];
            $dataTransferenciasArray[$key]['veimodeoid']            = $contratoResult['veimodeoid'];
            $dataTransferenciasArray[$key]['veiacessorios_pneu']    = $contratoResult['veiacessorios_pneu'];
            $dataTransferenciasArray[$key]['veieixcoid']            = $contratoResult['veieixcoid'];
            $dataTransferenciasArray[$key]['veipneus_germinados']   = $contratoResult['veipneus_germinados'];
            $dataTransferenciasArray[$key]['veidimpoid']            = (!$contratoResult['veidimpoid']) ? 0 : $contratoResult['veidimpoid'];
            $dataTransferenciasArray[$key]['veicomprimento']        = $contratoResult['veicomprimento'];
            $dataTransferenciasArray[$key]['veicapacidade']         = $contratoResult['veicapacidade'];
            $dataTransferenciasArray[$key]['tipcarreta']            = $contratoResult['prptipcoid'];
            $dataTransferenciasArray[$key]['veitccoid']             = $contratoResult['veitccoid'];
            
            //para o template de resposta
            $dataTransferenciasArray[$key]['novo_clinome'] = $novoTitularArray['clinome'];
            $dataTransferenciasArray[$key]['novo_clino_documento'] = $novoTitularArray['clino_documento'];
            $dataTransferenciasArray[$key]['novo_clitipo'] = $novoTitularArray['clitipo'];
        }
        
        return $dataTransferenciasArray;
    }
    
    /*
     * @param mixed[] $dataTransferencias array contendo dados dos contratos a serem transferidos.
     */
    private function transferirEmMassa(array $dataTransferencias){
        
        require  _SITEDIR_.'TransferenciaTitularidade.php';
        
        $transferenciaTitularidade = new TransferenciaTitularidade();
        
        ob_start();
        
        $result = $transferenciaTitularidade->trasferirEmMassa($dataTransferencias);
        
        ob_end_clean();
        ob_flush();
        
        return $result;
        
    }
    
}


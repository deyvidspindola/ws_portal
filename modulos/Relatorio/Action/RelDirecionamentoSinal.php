<?php

require_once _MODULEDIR_ . 'Principal/Action/PrnLogAlteracaoSinalGerenciadora.php';

class RelDirecionamentoSinal{

    const MENSAGEM_ALERTA_CAMPO_OBRIGATORIO = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_INFO_CAMPO_OBRIGATORIO = "Campos com * são obrigatorios.";
    const MENSAGEM_ALERTA_SEM_REGISTRO = "Nenhum registro encontrado.";
    const DIRETORIO_PRODUTOS_SOLICITADOS = '/var/www/docs_temporario/';

	public function __construct(RelDirecionamentoSinalDAO $dao){

		$this->dao = $dao;

		  /*
         * Cria o objeto View.
         */
        $this->view = new stdClass();


        // Dados
        $this->view->dados    = new stdClass();
        $this->view->mensagem = new stdClass();

		$this->param = new stdClass();

        foreach($_POST as $key => $value){
            $this->param->{$key} = $value;
        }

		$this->view->status = true;

		//campos incorretos
		$this->view->campos = array();

		$this->view->mensagem->info    = self::MENSAGEM_INFO_CAMPO_OBRIGATORIO;

 		$this->view->caminho = _MODULEDIR_ . 'Relatorio/View/rel_direcionamento_sinal/';

        $this->view->gerenciadoras = $dao->listarGerenciadoras();

        $this->actionLog = new PrnLogAlteracaoSinalGerenciadora();


        if(!empty($this->param->veiculo) && empty($this->param->cliente_termo)){
            $data = $this->dao->localizarClienteByPlacaVeiculo($this->param->veiculo);
            if(!empty($data)){
                $this->param->cliente = $data[0]->clioid;
                $this->param->cliente_termo = $data[0]->clinome;
            }
        }

	}


	public function index(){
		include $this->view->caminho . 'index.php';
	}

    public function filtrarDados($data_inicial = null, $data_final = null, $veiculo = null, $veiculo_id = null, $gerenciadora = null, $cliente = null){

        /* Data Inicial */
        if(!is_null($data_inicial) && !empty($data_inicial)){
            $data_inicial = implode('-', array_reverse(explode('/', $data_inicial)));
        }else{
            $data_inicial = date('Y-m-d', strtotime(date('Y-m-d H:i:s') . ' -90 days'));
        }

        /* Data Final */
        if(!is_null($data_final) && !empty($data_final)){
            $data_final = implode('-', array_reverse(explode('/', $data_final)));
        }else{
            $data_final = date('Y-m-d', strtotime($data_inicial . ' +90 days'));
        }

        if(empty($cliente) && empty($veiculo))
            throw new Exception('Você precisa informar um veículo ou um cliente.');

        if(
            !empty($cliente)
            && empty($veiculo)
            && empty($veiculo_id)
            && (floor((strtotime($data_final) - strtotime($data_inicial)) / (60*60*24)) > 30)
        ){
            throw new Exception('Para buscas por cliente o período deve ser inferior a 30 dias.');
        }

        if(floor((strtotime($data_final) - strtotime($data_inicial)) / (60*60*24)) > 90)
            throw new Exception('O período deve ser inferior a 90 dias.');


        return $this->dao->getLogDirecionamentoSinal($data_inicial, $data_final, $veiculo, $veiculo_id, $gerenciadora, $cliente);

    }

    public function validarPesquisa(){

        try {

            $data_inicial = isset($_POST['periodo_data_inicial']) ? $_POST['periodo_data_inicial'] : null;
            $data_final = isset($_POST['periodo_data_final']) ? $_POST['periodo_data_final'] : null;
            $veiculo = isset($_POST['veiculo']) ? $_POST['veiculo'] : null;
            $veiculo_id = isset($_POST['veiculo_id']) ? $_POST['veiculo_id'] : null;
            $gerenciadora = isset($_POST['gerenciadora']) ? $_POST['gerenciadora'] : null;
            $cliente = isset($_POST['cliente']) ? $_POST['cliente'] : null;

            $this->view->data = $this->filtrarDados($data_inicial, $data_final, $veiculo, $veiculo_id, $gerenciadora, $cliente);

        }catch(Exception $e){
            $this->view->exception = $e->getMessage();
        }

        include $this->view->caminho . 'index.php';

    }

    public function ajaxLocalizarClienteByPlacaVeiculo(){

        $data = array();

        if(isset($_POST['placa_veiculo']))
            $data = $this->dao->localizarClienteByPlacaVeiculo($_POST['placa_veiculo']);

        header('Content-Type: application/json');
        echo json_encode($data);
        
    }

    public function ajaxLocalizarClienteByVeiculoId(){

        $data = array();

        if(isset($_POST['veiculo_id']))
            $data = $this->dao->localizarClienteByVeiculoId($_POST['veiculo_id']);

        header('Content-Type: application/json');
        echo json_encode($data);
        
    }

    public function ajaxCliente(){

        $data = array();

        if(isset($_POST['cliente_termo']))
            $data = $this->dao->localizarCliente($_POST['cliente_termo']);

        header('Content-Type: application/json');
        echo json_encode($data);

    }

    public function exportarParaArquivo($formato, $data_inicial = null, $data_final = null, $veiculo = null, $veiculo_id = null, $gerenciadora = null, $cliente = null){

        if(!in_array($formato, array('pdf', 'excel')))
            throw new Exception('Formato do arquivo inválido');

        $data = $this->filtrarDados($data_inicial, $data_final, $veiculo, $veiculo_id, $gerenciadora, $cliente);

        /* Data Inicial */
        if(is_null($data_inicial) || empty($data_inicial)){
            $data_inicial = date('Y-m-d', strtotime(date('Y-m-d H:i:s') . ' -90 days'));
        }else{
            $data_inicial = implode('-', array_reverse(explode('/', $data_inicial)));
        }

        /* Data Final */
        if(is_null($data_final) || empty($data_final)){
            $data_final = date('Y-m-d', strtotime($data_inicial . ' +90 days'));
        }else{
            $data_final = implode('-', array_reverse(explode('/', $data_final)));
        }

        if(!is_null($cliente) && !empty($cliente))
            $nomeCliente = $this->dao->getNomeCliente($cliente);
            $nomeCliente = $nomeCliente ? $nomeCliente : null;

        if(!is_null($gerenciadora) && !empty($gerenciadora))
            $nomeGerenciadora = $this->dao->getNomeGerenciadora($gerenciadora);
            $nomeGerenciadora = $nomeGerenciadora ? $nomeGerenciadora : null;

        $cabecalho = array();

        $cabecalho['data_inicial'] = date('d/m/Y', strtotime($data_inicial));
        $cabecalho['data_final'] = date('d/m/Y', strtotime($data_final));
        $cabecalho['veiculo'] = !is_null($veiculo) && !empty($veiculo) ? $veiculo : 'Todos';
        $cabecalho['veiculo_id'] = !is_null($veiculo_id) && !empty($veiculo_id) ? $veiculo_id : 'Todos';
        $cabecalho['cliente'] = !is_null($nomeCliente) ? $nomeCliente : 'Todos';
        $cabecalho['gerenciadora_risco'] = !is_null($nomeGerenciadora) ? $nomeGerenciadora : 'Todos';

        /* Formatar dados */
        foreach($data as &$row){
            $row['data_solicitacao'] = is_null($row['data_solicitacao']) ? '-' : date('d/m/Y H:i', strtotime($row['data_solicitacao']));
            $row['data_execucao'] = is_null($row['data_execucao']) ? '-' : date('d/m/Y H:i', strtotime($row['data_execucao']));
        }

        if($formato === 'excel'){
            $this->actionLog->exportarCsvLogDirecionamentoSinal($cabecalho, $data);
        }elseif($formato === 'pdf'){
            $this->actionLog->exportarPdfLogDirecionamentoSinal($cabecalho, $data);
        }else{
            throw new Exception('Não foi possível gerar o arquivo.');            
        }            

    }

    public function exportarExcel(){

        try {

            $data_inicial = isset($_POST['periodo_data_inicial']) ? $_POST['periodo_data_inicial'] : null;
            $data_final = isset($_POST['periodo_data_final']) ? $_POST['periodo_data_final'] : null;
            $veiculo = isset($_POST['veiculo']) ? $_POST['veiculo'] : null;
            $veiculo_id = isset($_POST['veiculo_id']) ? $_POST['veiculo_id'] : null;
            $gerenciadora = isset($_POST['gerenciadora']) ? $_POST['gerenciadora'] : null;
            $cliente = isset($_POST['cliente']) ? $_POST['cliente'] : null;

            $this->exportarParaArquivo('excel', $data_inicial, $data_final, $veiculo, $veiculo_id, $gerenciadora, $cliente);

        }catch(Exception $e){
            $this->view->exception = $e->getMessage();
        }

        include $this->view->caminho . 'index.php';

    }

    public function exportarPdf(){

        try {

            $data_inicial = isset($_POST['periodo_data_inicial']) ? $_POST['periodo_data_inicial'] : null;
            $data_final = isset($_POST['periodo_data_final']) ? $_POST['periodo_data_final'] : null;
            $veiculo = isset($_POST['veiculo']) ? $_POST['veiculo'] : null;
            $veiculo_id = isset($_POST['veiculo_id']) ? $_POST['veiculo_id'] : null;
            $gerenciadora = isset($_POST['gerenciadora']) ? $_POST['gerenciadora'] : null;
            $cliente = isset($_POST['cliente']) ? $_POST['cliente'] : null;

            $this->exportarParaArquivo('pdf', $data_inicial, $data_final, $veiculo, $veiculo_id, $gerenciadora, $cliente);

        }catch(Exception $e){
            $this->view->exception = $e->getMessage();
        }

        include $this->view->caminho . 'index.php';

    }

}
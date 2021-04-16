<?php

class RelCalculoDeslocamentoTecnico{

    const MENSAGEM_ALERTA_CAMPO_OBRIGATORIO = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_INFO_CAMPO_OBRIGATORIO = "Campos com (*) são obrigatórios.";
    const MENSAGEM_ALERTA_SEM_REGISTRO = "Nenhum registro encontrado.";

	public function __construct(RelCalculoDeslocamentoTecnicoDAO $dao){

		$this->dao = $dao;

        $this->view = new stdClass();
        $this->view->dados    = new stdClass();
        $this->view->mensagem = new stdClass();

		$this->param = new stdClass();
		$this->tratarParametros();
		$this->view->status = true;
		$this->view->campos = array();
		$this->view->mensagem->info    = self::MENSAGEM_INFO_CAMPO_OBRIGATORIO;
 		$this->view->caminho = _MODULEDIR_ . 'Relatorio/View/rel_calculo_deslocamento_tecnico/';
	}

	public function index(){

        $repoid_sessao = null;
        $itloid_sessao = null;
        $bloquearComboTecnico = FALSE;
        $bloquearComboRepresentante = FALSE;
        $this->view->dados->bloquearPesquisa = false;

        if( isset($_SESSION['usuario']['depoid']) ) {
            if($_SESSION['usuario']['depoid'] == '9'){

                //Se usuário logado for Tecnico, não pode aparecer outros tecnicos
                if (stripos($_SESSION['usuario']['login'], 'TEC.') !== false) {
                    $itloid_sessao = str_replace('TEC.', "", $_SESSION['usuario']['login']);
                    $this->param->itloid = $itloid_sessao;

                }

                //Se usuário logado for Representante, não pode aparecer outros representantes
                if (stripos($_SESSION['usuario']['login'], 'PRS.') !== false) {
                    $repoid_sessao = str_replace('PRS.', "", $_SESSION['usuario']['login']);
                    $this->param->repoid = $repoid_sessao;

                } else {

                    if( $_SESSION['usuario']['cargo'] == '663' &&  (stripos($_SESSION['usuario']['login'], 'TEC.') === false) ) {
                         $bloquearComboTecnico = true;
                         $bloquearComboRepresentante = true;
                         $this->view->dados->bloquearPesquisa = true;
                    } else {
                        $repoid_sessao = $_SESSION['usuario']['refoid'];
                        $this->param->repoid = $repoid_sessao;
                    }
                }

            }
        }

        if( !$bloquearComboRepresentante ) {
            $this->view->dados->representates  = $this->dao->buscarRepresentates($repoid_sessao);
        } else{
            $this->view->dados->representates  = array();
        }

		$this->view->dados->tecnicos       = array();
        $this->view->dados->mensagens_cron = $this->retornaMensagensCron();

        if (!empty($this->param->repoid) && !$bloquearComboTecnico ) {
            $this->view->dados->tecnicos = $this->dao->buscarTecnicos($this->param, $itloid_sessao);
        }

        if (isset($this->param->acao) && $this->param->acao != 'index') {

            $this->validarParametros();

            if ($this->view->status) {

                $this->view->dados->pesquisa = $this->pesquisar($this->param);

                if (!$this->view->dados->pesquisa) {
                    $this->view->status           = false;
                    $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_SEM_REGISTRO;
                } else {

                    //tratamanto dos dados
                    foreach ($this->view->dados->pesquisa as $data => $representantes){
                        foreach ($representantes as $id_representante => $representante){
                            foreach ($representante as $id_tecnico => $atendimentos){
                                foreach ($atendimentos as $chave => $atendimento){

                                    $atendimento->logradouro = wordwrap($atendimento->logradouro, 50, "<br>", true);

                                    $atendimento->cep = preg_replace('/\D/', '', $atendimento->cep);

                                    $atendimento->cep = $this->mask(str_pad($atendimento->cep, 8, "0", STR_PAD_LEFT), '#####-###');

                                    $this->view->dados->pesquisa[$data][$id_representante][$id_tecnico][$chave] = $atendimento;
                                }
                            }
                        }
                    }
                }
            }
        }

		include $this->view->caminho . 'index.php';
	}

	private function pesquisar($params){

        $retorno = $this->dao->pesquisar($params);
    	return $retorno;
	}

    private function retornaMensagensCron(){

        /*
        * Informação Importante:
        * Essa sequência foi definada no Cron (CalculoDeslocamentoTecnico.php)
        * Favor nunca alterar esta ordem sem alterar tmb nas constantes do Cron
         */
        $retorno = array();
        $retorno[0] = 'Sem deslocamento.';
        $retorno[1] = 'Deslocamento calculado com sucesso';
        $retorno[2] = 'Erro no endereço do ponto atual.';
        $retorno[3] = 'Erro no endereço do ponto anterior.';
        $retorno[4] = 'Rota não encontrada.';

        return $retorno;
    }

	private function validarParametros(){

        $camposDestacados = array();

        //Valida se as datas foram informadas
        if (empty($this->param->dt_inicio) || empty($this->param->dt_fim)) {

            if (empty($this->param->dt_inicio)){
                $camposDestacados[] = array(
                    'campo'    => 'dt_inicio',
                    'mensagem' => ''
                );
            }
            if (empty($this->param->dt_fim)){
                $camposDestacados[] = array(
                    'campo'    => 'dt_fim',
                    'mensagem' => ''
                );
            }

            $this->view->status   = false;
        }

        //Valida se a data inicial é menor que a data final
        if (!empty($this->param->dt_inicio) && !empty($this->param->dt_fim)) {

            $dataInicial = implode('-', array_reverse(explode('/', substr($this->param->dt_inicio, 0, 10)))).substr($this->param->dt_inicio, 10);
            $dataFinal = implode('-', array_reverse(explode('/', substr($this->param->dt_fim, 0, 10)))).substr($this->param->dt_fim, 10);

            if($dataInicial > $dataFinal) {
                 $camposDestacados[] = array(
                    'campo'    => 'dt_inicio',
                    'mensagem' => 'A data inicial não pode ser maior que a data final.'
                );
                $camposDestacados[] = array(
                    'campo'    => 'dt_fim',
                    'mensagem' => 'A data inicial não pode ser maior que a data final.'
                );

                $this->view->status = false;
            }
        }

        $this->view->destaque = $camposDestacados;

        if (!$this->view->status) {

            if($camposDestacados[0]['mensagem'] == '') {
                $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_CAMPO_OBRIGATORIO;
            } else {
                $this->view->mensagem->alerta = $camposDestacados[0]['mensagem'];
            }
        }
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
    }

    /**
     * Método que retorna as Cidades por Ajax.
     *
     * @return Void
     */
    public function buscarTecnicos() {
        $dados = new stdClass();
        $dados->erro     = false;
        $dados->retorno  = null;
        $itloid_sessao   = null;

        //Se usuário logado for Tecnico, não pode aparecer outros tecnicos
        if (stripos($_SESSION['usuario']['login'], 'TEC.') !== false) {
            $itloid_sessao = str_replace('TEC.', "", $_SESSION['usuario']['login']);
            $this->param->itloid = $itloid_sessao;
        }

        try {
            $retorno = $this->dao->buscarTecnicos($this->param, $itloid_sessao);

            if (is_array($retorno)){
                foreach ($retorno as $chave => $valor) {
                    $retorno[$chave]->itloid  = $valor->itloid;
                    $retorno[$chave]->itlnome = utf8_encode($valor->itlnome);
                }
            }

            $dados->retorno = $retorno;

        } catch (ErrorException $e) {
            $dados->erro    = true;
            $dados->retorno = utf8_encode($e->getMessage());
        } catch (Exception $e) {
            $dados->erro    = true;
            $dados->retorno = utf8_encode($e->getMessage());
        }

        echo json_encode($dados);
    }

    /**
     * Método que aplica qualquer máscara
     *
     * @return mask
     */
    public function mask($val, $mask) {

        $maskared = '';
        $k = 0;

        for($i = 0; $i<=strlen($mask)-1; $i++) {

            if($mask[$i] == '#') {
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }

        return $maskared;
    }

}
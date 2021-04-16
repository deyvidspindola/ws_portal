<?php
include "lib/Components/PHPExcel/PHPExcel.php";

/**
 * Produtos Reservados.
 *
 * @package Relatório
 * @author  Kleber Goto Kihara <kleber.kihara@meta.com.br>
 */
class RelProdutosReservados {


    private $dao;
    private $param;
    private $view;
    private $usuarioLogado;
    private $isDeptoTecnico;

    const DIRETORIO_PRODUTOS_RESERVADOS     = '/var/www/docs_temporario/';
    const MENSAGEM_ALERTA_CAMPO_OBRIGATORIO = 'Existem campos obrigatórios não preenchidos.';
    const MENSAGEM_ALERTA_SEM_REGISTRO      = 'Nenhum registro encontrado.';
    const MENSAGEM_ERRO_PROCESSAMENTO       = 'Houve um erro no processamento dos dados.';
    const MENSAGEM_INFO_CAMPO_OBRIGATORIO   = 'Campos com * são obrigatórios.';

    /**
     * Método construtor.
     *
     * @param RelReportComercialDao $dao Objeto DAO.
     *
     * @return Void
     * @todo Parar a execução e apresentar o erro padrão (caso não receba $dao).
     */
    public function __construct($dao = null) {
        /*
         * Cria o objeto Dao.
         */
        if (is_object($dao)) {
            $this->dao = $dao;
        }

        /*
         * Cria o objeto Parâmetros.
         */
        $this->param = new stdClass();

        $this->tratarParametros();

        /*
         * Cria o objeto View.
         */
        $this->view = new stdClass();

        // Caminho do diretório
        $this->view->caminho = _MODULEDIR_ . 'Relatorio/View/rel_produtos_reservados/';

        // Campos incorretos
        $this->view->campos = array();

        // Dados
        $this->view->dados = null;

        // Mensagens
        $this->view->mensagem =  new stdClass();
        $this->view->mensagem->alerta  = '';
        $this->view->mensagem->erro    = '';
        $this->view->mensagem->info    = self::MENSAGEM_INFO_CAMPO_OBRIGATORIO;
        $this->view->mensagem->sucesso = '';

        // Status
        $this->view->status = true;

        $this->usuarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        $this->isDeptoTecnico = false;

    }

    /**
     * Método padrão da classe.
     *
     * @return Void
     */
    public function index() {

        try {
            $this->view->dados                = new stdClass();
            $this->view->dados->cidade        = array();
            $this->view->dados->classe        = $this->dao->buscarEquipamentoClasse();
            $this->view->dados->estado        = $this->dao->buscarEstado();
            $this->view->dados->representante = $this->dao->buscarRepresentante();
            $this->view->dados->status        = $this->dao->buscarReservaAgendamentoStatus();
            $this->view->dados->produtos      = $this->dao->buscarProdutos();
            $this->view->dados->tipo          = $this->dao->buscarOsTipo();

            $idPrestador = $this->recuperarCodigoPrestadorServico();

            if(!empty($idPrestador)){
                $this->param->repoid = $idPrestador;
                $this->isDeptoTecnico = true;
            }

            if (isset($this->param->ufuf)) {
                $this->view->dados->cidade = $this->dao->buscarCidade($this->param);
            }

            if (isset($this->param->acao) && $this->param->acao != 'index') {
                $this->validarParametros();

                if ($this->view->status) {
                    switch ($this->param->acao) {
                        case 'exportar' :
                            $this->view->dados->arquivo = $this->pesquisar(true);

                            if (!$this->view->dados->arquivo) {
                                $this->view->status           = false;
                                $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_SEM_REGISTRO;
                            }
                            break;
                        case 'pesquisar' :
                            $this->view->dados->pesquisa = $this->pesquisar(false);

                            if (!$this->view->dados->pesquisa) {
                                $this->view->status           = false;
                                $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_SEM_REGISTRO;
                            }
                            break;
                    }
                }
            }

        } catch (ErrorException $e) {
            $this->view->status         = false;
            $this->view->mensagem->erro = $e->getMessage();
        } catch (Exception $e) {
            $this->view->status           = false;
            $this->view->mensagem->alerta = $e->getMessage();
        }

        require_once $this->view->caminho.'index.php';
    }

    /**
     * Método que retorna as Cidades por Ajax.
     *
     * @return Void
     */
    public function buscarCidade() {

        $dados                  = new stdClass();
        $dados->status          = true;
        $dados->html            = null;
        $dados->mensagem        = new stdClass();
        $dados->mensagem->tipo  = null;
        $dados->mensagem->texto = null;
        $this->view->dados      = new stdClass();

        try {
            $this->view->dados->cidade = $this->dao->buscarCidade($this->param);

            ob_start();

            require_once $this->view->caminho.'ajax_cidades.php';

            $dados->html = utf8_encode(ob_get_clean());
        } catch (ErrorException $e) {
            $dados->status          = false;
            $dados->mensagem->tipo  = 'erro';
            $dados->mensagem->texto = utf8_encode($e->getMessage());
        } catch (Exception $e) {
            $dados->status          = false;
            $dados->mensagem->tipo  = 'alerta';
            $dados->mensagem->texto = utf8_encode($e->getMessage());
        }

        echo json_encode($dados);
    }

    /**
     * Método que executa a pesquisa.
     *
     * @param Boolean $exportar Determina se o resultado será exportado.
     *
     * @return Mixed
     */
    private function pesquisar($exportar = false) {
        $retorno = $this->dao->buscarProdutoReservado($this->param);

        if ($exportar) {
            $arquivo = 'rel_produto_reservado_'.date('Y-m-d').'_'.date('H-i-s').'.xlsx';

            $phpExcelReader = PHPExcel_IOFactory::createReader("Excel2007");
            $phpExcel       = $phpExcelReader->load($this->view->caminho.'modelo.xlsx');

            foreach ($retorno as $indice => $registro) {
                $linha = $indice + 8;

                $phpExcel->getActiveSheet()->getStyle('A'.$linha)->getAlignment()->setHorizontal('right');
                $phpExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($registro->ordem_servico));
                $phpExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($registro->tipo_os));
                $phpExcel->getActiveSheet()->getStyle('C'.$linha)->getAlignment()->setHorizontal('center');
                $phpExcel->getActiveSheet()->setCellValue('C'.$linha, utf8_encode($registro->dt_agenda ? date('d/m/Y', strtotime($registro->dt_agenda)) : ''));
                // $phpExcel->getActiveSheet()->getStyle('D'.$linha)->getAlignment()->setHorizontal('center');
                // $phpExcel->getActiveSheet()->setCellValue('D'.$linha, utf8_encode($registro->hr_agenda));
                $phpExcel->getActiveSheet()->setCellValue('D'.$linha, utf8_encode($registro->uf));
                $phpExcel->getActiveSheet()->setCellValue('E'.$linha, utf8_encode($registro->cidade));
                $phpExcel->getActiveSheet()->setCellValue('F'.$linha, utf8_encode($registro->representante));
                $phpExcel->getActiveSheet()->setCellValue('G'.$linha, utf8_encode($registro->instalador));
                $phpExcel->getActiveSheet()->getStyle('H'.$linha)->getAlignment()->setHorizontal('center');
                $phpExcel->getActiveSheet()->setCellValue('H'.$linha, utf8_encode(date('d/m/Y H:i:s', strtotime($registro->dt_reserva))));
                $phpExcel->getActiveSheet()->setCellValue('I'.$linha, utf8_encode($registro->status));
                $phpExcel->getActiveSheet()->setCellValue('J'.$linha, utf8_encode($registro->produto));
                $phpExcel->getActiveSheet()->getStyle('K'.$linha)->getAlignment()->setHorizontal('right');
                $phpExcel->getActiveSheet()->setCellValue('K'.$linha, utf8_encode($registro->versao));
                $phpExcel->getActiveSheet()->setCellValue('L'.$linha, utf8_encode($registro->serial));
                $phpExcel->getActiveSheet()->setCellValue('M'.$linha, utf8_encode($registro->classe));
                $phpExcel->getActiveSheet()->setCellValue('N'.$linha, utf8_encode($registro->transito));
                $phpExcel->getActiveSheet()->getStyle('O'.$linha)->getAlignment()->setHorizontal('right');
                $phpExcel->getActiveSheet()->setCellValue('O'.$linha, utf8_encode($registro->remessa));
                $phpExcel->getActiveSheet()->setCellValue('P'.$linha, utf8_encode($registro->dt_remessa));
                $phpExcel->getActiveSheet()->setCellValue('Q'.$linha, utf8_encode($registro->usuario));
            }

            $phpExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            //$phpExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
            $phpExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);

            $phpExcelWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
            $phpExcelWriter->setPreCalculateFormulas(false);
            $phpExcelWriter->save(self::DIRETORIO_PRODUTOS_RESERVADOS . $arquivo);

            return $arquivo;
        } else {
            return $retorno;
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
     * Método que valida os dados do $_POST e $_GET.
     *
     * @return Void
     * @todo Validar data.
     */
    private function validarParametros() {
        if (empty($this->param->reldt_tipo)) {
            $this->view->campos[] = array(
                'campo'    => 'reldt_tipo',
                'mensagem' => null
            );
            $this->view->status   = false;
        }

        if (empty($this->param->reldt_inicial)) {
            $this->view->campos[] = array(
                'campo'    => 'reldt_inicial',
                'mensagem' => null
            );
            $this->view->status   = false;
        }

        if (empty($this->param->reldt_final)) {
            $this->view->campos[] = array(
                'campo'    => 'reldt_final',
                'mensagem' => null
            );
            $this->view->status   = false;
        }

        if (!$this->view->status) {
            $this->view->mensagem->alerta = self::MENSAGEM_ALERTA_CAMPO_OBRIGATORIO;
        }
    }

     /**
    * Recupera dados para popular a combo instalador
    * @return JSON | array
    *
    */
    public function popularComboInstaladorAjax() {

        $this->tratarParametros();
        $dados = array();

        $dados = $this->dao->recuperarDadosInstalador($this->param->repoid);

        echo json_encode($dados);
        exit;
    }

    /**
     * recupera o ID do prestador de servico caso a relacao existe para o usuario
     * @return [integer]
     */
    private function recuperarCodigoPrestadorServico() {

        $repoid = 0;

        $isDeptoTecnico = $this->dao->verificarDepartamentoTecnico($this->usuarioLogado);

        if($isDeptoTecnico) {

            $repoid = $this->dao->recuperarCodigoPrestadorServico($this->usuarioLogado);
        }

        return $repoid;

    }

}
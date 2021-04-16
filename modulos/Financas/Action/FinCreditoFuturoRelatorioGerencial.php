<?php
include  _SITEDIR_ . "lib/Components/PHPExcel/PHPExcel.php";

/**
 * Classe FinCreditoFuturoRelatorioGerencial.
 * Camada de regra de negócio.
 *
 * @package  Financas
 * @author   José Fernando <jose.carlos@meta.com.br>
 *
 */
class FinCreditoFuturoRelatorioGerencial {

    /**
     * Objeto DAO da classe.
     *
     * @var CadExemploDAO
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
    /**
     * Contém dados a serem utilizados na View.
     *
     * @var stdClass
     */
    private $view;

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

        $this->view->graficoSinteticoMensal = null;
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

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
                $this->view->dados = $this->pesquisar($this->view->parametros);
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        //Inclir a view padrão
        require_once _MODULEDIR_ . "Relatorio/View/rel_descontos_conceder/index.php";
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
        $this->view->parametros->tipo_relatorio = isset($this->view->parametros->tipo_relatorio) ? trim($this->view->parametros->tipo_relatorio) : 'A';
        $this->view->parametros->motivo_credito = isset($this->view->parametros->motivo_credito) ? trim($this->view->parametros->motivo_credito) : '-1';
        $this->view->parametros->tipo_campanha_promocional = isset($this->view->parametros->tipo_campanha_promocional) ? trim($this->view->parametros->tipo_campanha_promocional) : '';
        $this->view->parametros->periodo_inclusao_ini = isset($this->view->parametros->periodo_inclusao_ini) ? trim($this->view->parametros->periodo_inclusao_ini) : '';
        $this->view->parametros->periodo_inclusao_fim = isset($this->view->parametros->periodo_inclusao_fim) ? trim($this->view->parametros->periodo_inclusao_fim) : '';
        $this->view->parametros->forma_inclusao = isset($this->view->parametros->forma_inclusao) ? trim($this->view->parametros->forma_inclusao) : '-1';
        $this->view->parametros->cliente_id = isset($this->view->parametros->cliente_id) ? trim($this->view->parametros->cliente_id) : '';
        $this->view->parametros->nome_cliente = isset($this->view->parametros->nome_cliente) ? trim($this->view->parametros->nome_cliente) : '';
        $this->view->parametros->tipo_pessoa = isset($this->view->parametros->tipo_pessoa) ? trim($this->view->parametros->tipo_pessoa) : '';
        $this->view->parametros->cliente_doc_J = isset($this->view->parametros->cliente_doc_J) ? trim($this->view->parametros->cliente_doc_J) : '';
        $this->view->parametros->cliente_doc_F = isset($this->view->parametros->cliente_doc_F) ? trim($this->view->parametros->cliente_doc_F) : '';
        $this->view->parametros->numero_nf = isset($this->view->parametros->numero_nf) ? trim($this->view->parametros->numero_nf) : '';
        $this->view->parametros->serie_nf = isset($this->view->parametros->serie_nf) ? trim($this->view->parametros->serie_nf) : 'Todos';
        $this->view->parametros->tipo_resultado = isset($this->view->parametros->tipo_resultado) ? trim($this->view->parametros->tipo_resultado) : '';

		$this->view->parametros->listarMotivoCredito = $this->dao->listarMotivosCreditos();
        $this->view->parametros->listarTipoCampanha  = $this->dao->listarTiposCampanhas();
        $this->view->parametros->listarSerieNota  = $this->dao->listarSeriesNotas();
        $this->view->parametros->usuariosAprovadoresCc = $this->dao->buscarUsuariosAprovadoresCc();
        $this->view->parametros->emailUsuarioLogado = $this->dao->buscarEmailUsuarioLogado();

    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     *
     * @param stdClass $filtros Filtros da pesquisa
     *
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        $resultadoPesquisa = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->status = TRUE;

        return $resultadoPesquisa;
    }


    /**
     * Action da tela de relatório concedidos
     * @return void
     */
    public function relatorioCreditosConcedidos() {

        $this->view->parametros = $this->tratarParametros();
        $this->inicializarParametros();
        $this->view->parametros->aba_ativa = "credito_concedidos";

        try {

            $this->limparDados();

            if ( isset($this->view->parametros->sub_acao) && $this->view->parametros->sub_acao == 'pesquisarConcedidos' ) {

                $this->view->dados = $this->pesquisarConcedidos($this->view->parametros);

                $this->armazenarDados($this->view->parametros,$this->view->dados);

                if ($this->view->parametros->tipo_relatorio == 'S' && $this->view->parametros->tipo_resultado == 'm') {
                    $this->view->graficoSinteticoMensal = $this->gerarGraficoSinteticoMensal();
                }


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


        //require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/relatorio_creditos_concedidos/layout_relatorio_descontos_concedidos.php";
        require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/relatorio_creditos_concedidos/index.php";

    }

    /**
     * Método que gera xls do relatorio tipo análitico - Creditos Concedidos
     * @param bool $isAjax
     * @return string
     */
    public function gerarXlsAnalitico($isAjax = true) {
         $this->view->parametros = $this->tratarParametros();
         $this->inicializarParametros();

         try {

            // Arquivo modelo para gerar o XLS
            $arquivoModelo = _MODULEDIR_.'Financas/View/fin_credito_futuro_relatorio_gerencial/templates/modelo_analitico.xlsx';

            // Instância PHPExcel
            $reader = PHPExcel_IOFactory::createReader("Excel2007");

            // Carrega o modelo
            $PHPExcel = $reader->load($arquivoModelo);

            // Processa o relatório
            $dados = $this->pegarDados();
            $pesquisa = $dados['parametros'];
            $relatorio = $dados['resultado'];

            $motivoCredito = array();
            $tipoCamapanhaPromocional = array();

            foreach ($pesquisa->listarMotivoCredito as $motivo) {
                $motivoCredito[$motivo->cfmcoid] = $motivo->cfmcdescricao;
            }

            foreach ($pesquisa->listarTipoCampanha as $tipo) {
                $tipoCamapanhaPromocional[$tipo->cftpoid] = $tipo->cftpdescricao;
            }

            //echo $motivoCredito[$pesquisa->motivo_credito]; exit;

            if (count($relatorio['descontos'])) {

                $PHPExcel->getActiveSheet()->setCellValue('E2', $pesquisa->periodo_inclusao_ini . ' a ' . $pesquisa->periodo_inclusao_fim);
                //$PHPExcel->getActiveSheet()->setCellValue('F3', utf8_encode($row->motivo_credito));
                $PHPExcel->getActiveSheet()->setCellValue('E4', utf8_encode($motivoCredito[$pesquisa->motivo_credito]));
                $PHPExcel->getActiveSheet()->setCellValue('E5', utf8_encode($tipoCamapanhaPromocional[$pesquisa->tipo_campanha_promocional]));

                $linha = 9;
                foreach ($relatorio['descontos'] as $row) {

                    //$PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getNumberFormat()->setFormatCode('0');
                    $PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($row->data_emissao_nota));

                    $PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($row->numero_nota . '/' . $row->serie_nota));

                    //$PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode('0');
                    $PHPExcel->getActiveSheet()->setCellValue('C'.$linha, utf8_encode($row->cliente));

                    $PHPExcel->getActiveSheet()->setCellValue('D'.$linha, utf8_encode($row->cliente_doc));

                    //verificar coluna E (UF)
                    //$PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode('0');
                    $PHPExcel->getActiveSheet()->setCellValue('E'.$linha, utf8_encode(($row->credito_futuro_id)));

                    $PHPExcel->getActiveSheet()->setCellValue('F'.$linha, utf8_encode($row->motivo_credito));

                    //$PHPExcel->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode('0');
                    $PHPExcel->getActiveSheet()->setCellValue('G'.$linha, utf8_encode($row->forma_inclusao));

                    //$PHPExcel->getActiveSheet()->getStyle('H'.$linha)->getNumberFormat()->setFormatCode('#.##0');
                    $PHPExcel->getActiveSheet()->setCellValue('H'.$linha, utf8_encode($row->protocolo));

                    $PHPExcel->getActiveSheet()->setCellValue('I'.$linha, utf8_encode($row->campanha_promocional));

                    $PHPExcel->getActiveSheet()->setCellValue('J'.$linha, utf8_encode($row->valor_itens));

                    $PHPExcel->getActiveSheet()->setCellValue('K'.$linha, utf8_encode($row->valor_desconto));

                    $PHPExcel->getActiveSheet()->setCellValue('L'.$linha, utf8_encode($row->valor_nota));

                    $PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $PHPExcel->getActiveSheet()->getStyle('D'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $PHPExcel->getActiveSheet()->getStyle('F'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $PHPExcel->getActiveSheet()->getStyle('G'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $PHPExcel->getActiveSheet()->getStyle('H'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $PHPExcel->getActiveSheet()->getStyle('I'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                    $PHPExcel->getActiveSheet()->getStyle('J'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $PHPExcel->getActiveSheet()->getStyle('K'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $PHPExcel->getActiveSheet()->getStyle('L'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


                    $linha++;
                }



                $PHPExcel->getActiveSheet()->setCellValue('I'.$linha, 'Total');

                $PHPExcel->getActiveSheet()->setCellValue('J'.$linha, number_format($relatorio['total_valor_itens'],2,',','.'));

                $PHPExcel->getActiveSheet()->setCellValue('K'.$linha, number_format($relatorio['total_valor_desconto'],2,',','.'));

                $PHPExcel->getActiveSheet()->setCellValue('L'.$linha, number_format($relatorio['total_valor_notas'],2,',','.'));

                $formatacaoBorda = array(
                 'borders' => array(
                       'outline' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => '000000'),
                        ),
                   ),
                 'font' => array( 'name' => 'Calibri',
                      'size' => 9,
                      'bold' => true,
                      'color' => array( 'argb' => '000000')
                  ),
                 'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array('rgb' => 'FF9A00')
                  )
                 );

                $PHPExcel->getActiveSheet()->getStyle('I'.$linha)->applyFromArray($formatacaoBorda);
                $PHPExcel->getActiveSheet()->getStyle('J'.$linha)->applyFromArray($formatacaoBorda);
                $PHPExcel->getActiveSheet()->getStyle('K'.$linha)->applyFromArray($formatacaoBorda);
                $PHPExcel->getActiveSheet()->getStyle('L'.$linha)->applyFromArray($formatacaoBorda);

                $PHPExcel->getActiveSheet()->getStyle('I'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('J'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('K'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('L'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $PHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(false);
                $PHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            }
            else {
                $PHPExcel->getActiveSheet()->setCellValue('A8', utf8_encode("Nenhum resultado encontrado."));
            }

            $writer = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
            $writer->setPreCalculateFormulas(false);

            $file = "Relatorio_Gerencial_de_Descontos_Concedidos-" . date('d-m-Y') . ".xls";
            $dir = '/var/www/docs_temporario/';

            if(!file_exists($dir) || !is_writable($dir)) {
                throw new Exception('Houve um erro ao gerar o arquivo.');
            }

            //echo "aqui";
            $writer->save($dir.$file);

            unset($dados);
            unset($pesquisa);
            unset($relatorio);
            unset($motivoCredito);
            unset($tipoCamapanhaPromocional);

            if (file_exists($dir.$file)) {

                if ($isAjax) {
                    echo $dir.$file;
                    exit;
                } else {
                     return $dir.$file;
                }

            }

            if ($isAjax) {
                echo 0;
                exit;
            } else {
                return false;
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

    }

    /**
     * Método que gera xls do relatorio tipo sintético mensal - Creditos Concedidos
     * @param bool $isAjax
     * @return string
     */
    public function gerarXlsSinteticoMensal($isAjax = true) {

        $this->view->parametros = $this->tratarParametros();
        $this->inicializarParametros();

        try {

            // Arquivo modelo para gerar o XLS
            $arquivoModelo = _MODULEDIR_.'Financas/View/fin_credito_futuro_relatorio_gerencial/templates/modelo_sintetico_mensal.xlsx';

            // Instância PHPExcel
            $reader = PHPExcel_IOFactory::createReader("Excel2007");

            // Carrega o modelo
            $PHPExcel = $reader->load($arquivoModelo);

            // Processa o relatório
            $dados = $this->pegarDados();
            $pesquisa = $dados['parametros'];
            $relatorio = $dados['resultado'];


            $motivoCredito = array();
            $tipoCamapanhaPromocional = array();

            foreach ($pesquisa->listarMotivoCredito as $motivo) {
                $motivoCredito[$motivo->cfmcoid] = $motivo->cfmcdescricao;
            }


            //echo $motivoCredito[$pesquisa->motivo_credito]; exit;

            if (count($relatorio['descontos'])) {

                $formatacaoBorda = array(
                   'borders' => array(
                     'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '000000'),
                        ),
                     ),
                   'font' => array( 'name' => 'Calibri',
                      'size' => 9,
                      'bold' => true,
                      'color' => array( 'argb' => '000000')
                      ),
                   'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => 'FF9A00')
                    )
                   );

                $PHPExcel->getActiveSheet()->setCellValue('D2', $pesquisa->periodo_inclusao_ini . ' a ' . $pesquisa->periodo_inclusao_fim);
                //$PHPExcel->getActiveSheet()->setCellValue('F3', utf8_encode($row->motivo_credito));
                $PHPExcel->getActiveSheet()->setCellValue('D4', utf8_encode($motivoCredito[$pesquisa->motivo_credito]));
                $PHPExcel->getActiveSheet()->setCellValue('D5', utf8_encode($tipoCamapanhaPromocional[$pesquisa->tipo_campanha_promocional]));

                $linha = 8;

                foreach ($relatorio['descontos'] as $key => $row) {

                        //$PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getNumberFormat()->setFormatCode('0');
                   $PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($row->data_emissao_nota));

                        //$PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode('0');
                    $PHPExcel->getActiveSheet()->setCellValue('B'.$linha, number_format($row->valor_itens, 2, ',', '.'));

                    $PHPExcel->getActiveSheet()->setCellValue('C'.$linha, number_format($row->valor_desconto, 2, ',', '.'));

                        //verificar coluna E (UF)
                        //$PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode('0');
                    $PHPExcel->getActiveSheet()->setCellValue('D'.$linha, number_format($row->valor_nota, 2, ',', '.'));

                    $PHPExcel->getActiveSheet()->setCellValue('E'.$linha, $row->percentual . '%');

                    $PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $PHPExcel->getActiveSheet()->getStyle('D'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


                    $linha++;
                }

                $PHPExcel->getActiveSheet()->setCellValue('B'.$linha, number_format($relatorio['valor_itens_total'],2,',','.'));

                $PHPExcel->getActiveSheet()->setCellValue('C'.$linha, number_format($relatorio['valor_descontos_total'],2,',','.'));

                $PHPExcel->getActiveSheet()->setCellValue('D'.$linha, number_format($relatorio['valor_nota_total'],2,',','.'));

                $PHPExcel->getActiveSheet()->setCellValue('E'.$linha, $relatorio['percentual_descontos_total'] . '%');



                $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($formatacaoBorda);
                $PHPExcel->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($formatacaoBorda);
                $PHPExcel->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($formatacaoBorda);
                $PHPExcel->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($formatacaoBorda);


                $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('D'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


                $PHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(false);
                $PHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
                $PHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            }
            else {
                $PHPExcel->getActiveSheet()->setCellValue('A8', utf8_encode("Nenhum resultado encontrado."));
            }

            $writer = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
            $writer->setPreCalculateFormulas(false);

            $file = "Relatorio_Gerencial_de_Descontos_Concedidos-Sintetico-" . date('d-m-Y') . ".xls";
            $dir = '/var/www/docs_temporario/';

            if(!file_exists($dir) || !is_writable($dir)) {
                throw new Exception('Houve um erro ao gerar o arquivo.');
            }

            //echo "aqui";
            $writer->save($dir.$file);

            unset($dados);
            unset($pesquisa);
            unset($relatorio);
            unset($motivoCredito);
            unset($tipoCamapanhaPromocional);

            if (file_exists($dir.$file)) {

                if ($isAjax) {
                    echo $dir.$file;
                    exit;
                } else {
                   return $dir.$file;
               }

           }

           if ($isAjax) {
            echo 0;
            exit;
        } else {
            return false;
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
}

     /**
     * Método que gera xls do relatorio tipo sintético diario - Creditos Concedidos
     * @param bool $isAjax
     * @return string
     */
    public function gerarXlsSinteticoDiario($isAjax = true) {
        $this->view->parametros = $this->tratarParametros();
         $this->inicializarParametros();

         try {

            // Arquivo modelo para gerar o XLS
            $arquivoModelo = _MODULEDIR_.'Financas/View/fin_credito_futuro_relatorio_gerencial/templates/modelo_sintetico_diario.xlsx';

            // Instância PHPExcel
            $reader = PHPExcel_IOFactory::createReader("Excel2007");

            // Carrega o modelo
            $PHPExcel = $reader->load($arquivoModelo);

            // Processa o relatório
            $dados = $this->pegarDados();
            $pesquisa = $dados['parametros'];
            $relatorio = $dados['resultado'];

            $motivoCredito = array();
            $tipoCamapanhaPromocional = array();

            foreach ($pesquisa->listarMotivoCredito as $motivo) {
                $motivoCredito[$motivo->cfmcoid] = $motivo->cfmcdescricao;
            }


            //echo $motivoCredito[$pesquisa->motivo_credito]; exit;

            if (count($relatorio['descontos'])) {

                $formatacaoBorda = array(
                 'borders' => array(
                       'outline' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => '000000'),
                        ),
                   ),
                 'font' => array( 'name' => 'Calibri',
                      'size' => 9,
                      'bold' => true,
                      'color' => array( 'argb' => '000000')
                  ),
                 'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array('rgb' => 'FF9A00')
                  )
                 );

                $PHPExcel->getActiveSheet()->setCellValue('D2', $pesquisa->periodo_inclusao_ini . ' a ' . $pesquisa->periodo_inclusao_fim);
                //$PHPExcel->getActiveSheet()->setCellValue('F3', utf8_encode($row->motivo_credito));
                $PHPExcel->getActiveSheet()->setCellValue('D4', utf8_encode($motivoCredito[$pesquisa->motivo_credito]));
                $PHPExcel->getActiveSheet()->setCellValue('D5', utf8_encode($tipoCamapanhaPromocional[$pesquisa->tipo_campanha_promocional]));

                $linha = 8;
                foreach ($relatorio['descontos'] as $grupos) {

                    foreach ($grupos['itens'] as $key => $row) {

                        //$PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getNumberFormat()->setFormatCode('0');
                        $PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($row->data_emissao_nota));

                        $PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($row->motivo_credito));

                        //$PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode('0');
                        $PHPExcel->getActiveSheet()->setCellValue('C'.$linha, number_format($row->valor_itens, 2, ',', '.'));

                        $PHPExcel->getActiveSheet()->setCellValue('D'.$linha, number_format($row->valor_desconto, 2, ',', '.'));

                        //verificar coluna E (UF)
                        //$PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode('0');
                        $PHPExcel->getActiveSheet()->setCellValue('E'.$linha, number_format($row->valor_nota, 2, ',', '.'));

                        $PHPExcel->getActiveSheet()->setCellValue('F'.$linha, $row->percentual . '%');

                        $PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $PHPExcel->getActiveSheet()->getStyle('D'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $PHPExcel->getActiveSheet()->getStyle('F'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                        $linha++;
                    }

                    $PHPExcel->getActiveSheet()->setCellValue('B'.$linha, 'Total');

                    $PHPExcel->getActiveSheet()->setCellValue('C'.$linha, number_format($grupos['vl_itens'],2,',','.'));

                    $PHPExcel->getActiveSheet()->setCellValue('D'.$linha, number_format($grupos['vl_desc'],2,',','.'));

                    $PHPExcel->getActiveSheet()->setCellValue('E'.$linha, number_format($grupos['vl_nf'],2,',','.'));

                    $PHPExcel->getActiveSheet()->setCellValue('F'.$linha, $grupos['vl_percentual'] . '%');

                    $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($formatacaoBorda);
                    $PHPExcel->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($formatacaoBorda);
                    $PHPExcel->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($formatacaoBorda);
                    $PHPExcel->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($formatacaoBorda);
                    $PHPExcel->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($formatacaoBorda);

                    $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('D'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('F'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $linha++;

                }

                $linha = $linha + 1;

                $PHPExcel->getActiveSheet()->setCellValue('B'.$linha, 'Total Geral');

                $PHPExcel->getActiveSheet()->setCellValue('C'.$linha, number_format($relatorio['valor_itens_total'],2,',','.'));

                $PHPExcel->getActiveSheet()->setCellValue('D'.$linha, number_format($relatorio['valor_descontos_total'],2,',','.'));

                $PHPExcel->getActiveSheet()->setCellValue('E'.$linha, number_format($relatorio['valor_nota_total'],2,',','.'));

                $PHPExcel->getActiveSheet()->setCellValue('F'.$linha, $relatorio['percentual_descontos_total'] . '%');

                $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->applyFromArray($formatacaoBorda);
                $PHPExcel->getActiveSheet()->getStyle('C'.$linha)->applyFromArray($formatacaoBorda);
                $PHPExcel->getActiveSheet()->getStyle('D'.$linha)->applyFromArray($formatacaoBorda);
                $PHPExcel->getActiveSheet()->getStyle('E'.$linha)->applyFromArray($formatacaoBorda);
                $PHPExcel->getActiveSheet()->getStyle('F'.$linha)->applyFromArray($formatacaoBorda);

                $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('D'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('F'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $PHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(false);
                $PHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
                $PHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            }
            else {
                $PHPExcel->getActiveSheet()->setCellValue('A8', utf8_encode("Nenhum resultado encontrado."));
            }

            $writer = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
            $writer->setPreCalculateFormulas(false);

            $file = "Relatorio_Gerencial_de_Descontos_Concedidos-Sintetico-" . date('d-m-Y') . ".xls";
            $dir = '/var/www/docs_temporario/';

            if(!file_exists($dir) || !is_writable($dir)) {
                throw new Exception('Houve um erro ao gerar o arquivo.');
            }

            //echo "aqui";
            $writer->save($dir.$file);

            unset($dados);
            unset($pesquisa);
            unset($relatorio);
            unset($motivoCredito);
            unset($tipoCamapanhaPromocional);

            if (file_exists($dir.$file)) {

                if ($isAjax) {
                    echo $dir.$file;
                    exit;
                } else {
                     return $dir.$file;
                }

            }

            if ($isAjax) {
                echo 0;
                exit;
            } else {
                return false;
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
    }

     /**
     * Método que gera grafico do relatorio tipo sintético mensal - Creditos Concedidos
     * @return string
     */
    public function gerarGraficoSinteticoMensal() {

        $sitedir = strpos($_SERVER['HTTP_HOST'], '10.20.12.') === false ? '' : _SITEDIR_.'/lib/php5-jpgraph/';

        require_once $sitedir.'jpgraph.php';
        require_once $sitedir.'jpgraph_bar.php';

        $dados = $this->pegarDados();
        $pesquisa = $dados['parametros'];
        $relatorio = $dados['resultado'];

        // Create the graph. These two calls are always required
        $graph = new Graph(1097,400);
        $graph->SetScale("textlin");

        $months = $gDateLocale->GetShortMonth();

        $mes_total_itens = array();
        $mes_total_descontos = array();
        $mes_total_nota = array();
        $months = array();

        foreach ($relatorio['descontos'] as $key => $item) {
            $mes_total_itens[]     = $item->valor_itens;
            $mes_total_descontos[] = $item->valor_desconto;
            $mes_total_nota[]      = $item->valor_nota;
            $months[]              = $key;
        }


        $setSize = count($months) == 1 ? true : false;

        $graph->SetShadow('',0,0,false);
        $graph->SetBackgroundGradient('white', 'white', 2, BGRAD_FRAME);
        $graph->img->SetMargin(40,30,20,40);
        $graph->xaxis->SetTickLabels($months);

// Create the bar plots
        $b1plot = new BarPlot($mes_total_itens);
        $b1plot->SetFillColor("#00aa00");
        $b1plot->SetLegend("Vlr.Itens");
        $b1plot->SetShadow("silver", 3, 3, true);

        if ($setSize) {
            $b1plot->SetWidth(100);
        }


        $b2plot = new BarPlot($mes_total_descontos);
        $b2plot->SetFillColor("red");
        $b2plot->SetLegend("Vlr.Descto.");
        $b2plot->SetShadow("silver", 3, 3, true);

        if ($setSize) {
            $b2plot->SetWidth(100);
        }

        $b3plot = new BarPlot($mes_total_nota);
        $b3plot->SetFillColor("blue");
        $b3plot->SetLegend("Vlr.NF");
        $b3plot->SetShadow("silver", 3, 3, true);

        if ($setSize) {
            $b3plot->SetWidth(100);
        }

// Create the grouped bar plot
        $gbplot = new GroupBarPlot(array($b1plot,$b2plot,$b3plot));
        $labels = array ('legenda 1', 'legenda 2', 'legenda 3');

        $gbplot->SetFillColor('#E234A9');
// ...and add it to the graPH
        $graph->Add($gbplot);

//$graph->title->Set("Sintético");
        $graph->xaxis->title->Set("Período");
        $graph->yaxis->title->Set("Valor");

        $graph->title->SetFont(FF_FONT1,FS_BOLD);
        $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
        $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

         /*
         * PERMISSÕES
         */
        chmod(_SITEDIR_.'images/grafico', 0777);
        $file = "images/grafico/grafico_concedidos_sinteticos_" . md5(date('H:i:s')) . ".jpg";

        // Display the graph
        //unlink(_SITEDIR_ . $file);
        $graph->Stroke(_SITEDIR_ . $file);

        return $file;


    }

    /**
     * Método que realiza busca dos creditos concedidos conforme parametros
     * @param stdClass $parametros
     * @return array
     */
    private function pesquisarConcedidos(stdClass $parametros) {

        if ($this->validarCamposCreditosConcedidos($parametros)) {

            $this->view->status = false;

            if ($parametros->tipo_relatorio == 'A') {

                //se tipo análitico
                $dados = $this->dao->pesquisarConcedidosAnalitico($parametros);

                if ( count($dados['descontos']) > 0 ) {
                    $this->view->status = true;
                }

            } else if ($parametros->tipo_relatorio == 'S') {

                //senao se tipo sintetico
                $dados = $this->dao->pesquisarConcedidosSintetico($parametros);

                if ( count($dados['descontos']) > 0 ) {
                    $this->view->status = true;
                }

            }


            if ($this->view->status == false) {
                throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
            }


            return $dados;

        }

    }

    /**
     * Método reseta dados de formulário de envio de email
     * @param stdClass $parametros
     * @return array
     */
    public function resetFormularioEnviarEmail() {
        $dados = $this->pegarDados();
        $this->inicializarParametros();


        $retorno['email'] = $this->view->parametros->emailUsuarioLogado;

        if (isset($_POST['tipo_pesquisa']) && $_POST['tipo_pesquisa'] == 'S') {
            $retorno['conteudo'] = utf8_encode("Prezado(s),
Segue anexo, o Relatório Gerencial de Descontos Concedidos - Sintético referente o período de " . $dados['parametros']->periodo_inclusao_ini . " a " . $dados['parametros']->periodo_inclusao_fim . ".

Att.
" . $_SESSION['usuario']['nome_completo'] . "");
        } else {
            $retorno['conteudo'] = utf8_encode("Prezado(s),
Segue anexo, o Relatório Gerencial de Descontos Concedidos referente o período de " . $dados['parametros']->periodo_inclusao_ini . " a " . $dados['parametros']->periodo_inclusao_fim . ".

Att.
" . $_SESSION['usuario']['nome_completo'] . "");
        }


    echo json_encode($retorno);

    }

    /**
     * Método que realiza envio de e-mail dos creditos concedidos
     * @return boolean
     */
    public function enviarEmail() {

        $this->view->parametros = $this->tratarParametros();
        $this->inicializarParametros();

        try {

            if ($this->view->parametros->tipo == "A") {

                $arquivoXls = $this->gerarXlsAnalitico(false);

            } else if ($this->view->parametros->tipo == "S") {

                if ($this->view->parametros->tipo_pesquisa == "d") {
                    $arquivoXls = $this->gerarXlsSinteticoDiario(false);
                } else {
                    $arquivoXls = $this->gerarXlsSinteticoMensal(false);
                }

            }

            $mail = new PHPMailer();

            $mail->IsSMTP();
            $mail->From = "sascar@sascar.com.br";
            $mail->FromName = "SASCAR";
            $mail->Subject = utf8_decode($this->view->parametros->email_assunto);
            $mail->MsgHTML(utf8_decode(nl2br($this->view->parametros->email_corpo)));
            $mail->AddAttachment($arquivoXls);
            $mail->ClearAllRecipients();

            if($_SESSION['servidor_teste'] == 1) {
                $mail->AddAddress(_EMAIL_TESTE_);
            } else {

                $emails = explode(';', $this->view->parametros->email_para);

                foreach ($emails as $key => $email) {
                    $mail->AddAddress(trim($email));
                }

            }

            if (isset($this->view->parametros->email_cc) && trim($this->view->parametros->email_cc) != '') {

                $emailsCC = explode(';', $this->view->parametros->email_cc);

                foreach ($emailsCC as $key => $emailCC) {
                    $mail->AddCC(trim($emailCC));
                }
            }

            if ($mail->Send()){
                $mail->ClearAllRecipients();
                $mail->ClearAttachments();
                ob_clean();
                echo "1";
                exit;
            }

        } catch (ErrorException $e) {
            ob_clean();
            echo "0";
            exit;

        } catch (Exception $e) {
            ob_clean();
            echo "0";
            exit;
        }

        ob_clean();
        echo "0";
        exit;
    }


    /**
     * Método listarCampanhasVigentes()
     *
     * Action da tela de "Campanha(s) Promocional(ais) Vigente(s)"
     *
     * @return void
     */
    public function listarCampanhasVigentes() {

        $this->view->parametros->aba_ativa = "campanhas_vigentes";
        try {

            $this->view->dados = $this->dao->buscarCampanhasVigentes();

            if (count($this->view->dados) == 0) {
                throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
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

        require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/listar_campanhas_vigentes.php";
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
            $this->index();
        } else {

            //@TODO: Montar dinamicamente o caminho apenas da view Index
            require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_gerencial/cadastrar.php";
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
            if (isset($parametros->cfcpoid) && intval($parametros->cfcpoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->cfcpoid = (int) $parametros->cfcpoid;

                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->cfcpoid);

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
     *
     * @return void
     */
    private function salvar(stdClass $dados) {

        //Validar os campos
        $this->validarCamposCadastro($dados);

        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        if ($dados->cfcpoid > 0) {
            //Efetua a gravação do registro
            $gravacao = $this->dao->atualizar($dados);

            //Seta a mensagem de atualização
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        } else {
            //Efetua a inserção do registro
            $gravacao = $this->dao->inserir($dados);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        }

        //Comita a transação
        $this->dao->commit();

        return $gravacao;
    }



    private function validarCamposCreditosConcedidos($dados) {

        $camposDestaques = array();

        $valido = true;

        if (!isset($dados->periodo_inclusao_ini) || trim($dados->periodo_inclusao_ini) == '') {

            $camposDestaques[] = array(
                'campo' => 'periodo_inclusao_ini'
            );

            $valido = false;
        }

        if (!isset($dados->periodo_inclusao_fim) || trim($dados->periodo_inclusao_fim) == '') {

            $camposDestaques[] = array(
                'campo' => 'periodo_inclusao_fim'
            );

            $valido = false;
        }

        $this->view->dados = $camposDestaques;

        if (!$valido) {
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        return $valido;

    }


    private function armazenarDados($parametros, $resultadoPesquisa) {
        $_SESSION['credito_futuro_relatorios']['pesquisa'] = array('parametros' => $parametros, 'resultado' => $resultadoPesquisa);
    }

    private function limparDados() {
        unset($_SESSION['credito_futuro_relatorios']['pesquisa']);
    }

    private function pegarDados() {
        return $_SESSION['credito_futuro_relatorios']['pesquisa'];
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
    private function validarCamposCadastro(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        /**
         * Verifica os campos obrigatórios
         */
        /** Ex.:
        if (!isset($dados->excnome) || trim($dados->excnome) == '') {
            $camposDestaques[] = array(
                'campo' => 'excnome'
            );
            $error = true;
        }
		*/

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
            if (!isset($parametros->cfcpoid) || trim($parametros->cfcpoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->cfcpoid = (int) $parametros->cfcpoid;

            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->cfcpoid);

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


}


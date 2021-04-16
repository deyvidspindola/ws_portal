<?php

require_once _SITEDIR_ . "lib/Components/PHPExcel/PHPExcel.php";

require_once "lib/Components/CsvWriter.php";

/**
 * FinConsultaNotaFiscalVivo.php
 * - Relatório Consulta Notas Fiscais Vivo
 *
 * @package Finanças
 * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
 * @since   22/10/2013
 *
 */
class FinConsultaNotaFiscalVivo {

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
        $this->view->xls = false;

        //Filtros/parametros utlizados na view
        $this->view->parametros = null;

        //Status de uma transação
        $this->view->status = false;

        //Tipo de relatorio (N ou P)
        $this->view->relatorio = null;

    }

    /**
     * Método index()
     *
     * @return void()
     */
    public function index() {

        try {

            if ( ( ( isset($_POST['acao']) && trim($_POST['acao']) == 'pesquisar') ) || isset($_GET['nfloid']) ) {


                if (!isset($_GET['nfloid'])){

                    $this->limparSessaoPesquisa();
                    $this->view->parametros = $this->tratarParametrosPesquisa();

                } else {

                    $this->view->parametros = (object) $_SESSION['pesquisa'];

                    $this->view->parametros->nfloid = intval($_GET['nfloid']);


                }

                // Efetua pesquisa para uso no relatório visual
                $this->view->dados = $this->pesquisar($this->view->parametros);

                //utiliza pesquisa para uso no CSV
				$this->view->csv = $this->gerarCsv($this->view->dados, $this->view->relatorio);
            }

            //Inicializa os dados
            $this->inicializarParametros();


        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->view->parametros->buscarEventoStatus = $this->dao->buscarEventoStatus();

            $this->view->parametros->buscarSerieNotaFiscal = $this->dao->buscarSerieNotaFiscal();

            $this->view->mensagemAlerta = $e->getMessage();
        }

        require_once _MODULEDIR_ . "Financas/View/fin_consulta_nota_fiscal_vivo/index.php";
    }

    /**
     * Método pesquisar() - Realiza pesquisa por filtro
     *
     * @param array $parametros =>  Parâmetros para pesquisa.
     *
     * @return object  $resultadoPesquisa(
     */
    private function pesquisar(stdClass $parametros) {


        if ($this->validarPesquisa($parametros)) {

            if ($parametros->selecao_por == 'N') {

                if ($parametros->nfloid) {
                    $resultadoPesquisa = $this->dao->consultarNotaFiscalVivoPlaca($parametros);
                    $this->view->relatorio = 'P';
                    $this->view->parametros->selecao_por = 'P';
                } else {
                    $resultadoPesquisa = $this->dao->consultarNotaFiscalVivo($parametros);
                    $this->view->relatorio = 'N';
                }



            } else {

                $resultadoPesquisa = $this->dao->consultarNotaFiscalVivoPlaca($parametros);

                $this->view->relatorio = 'P';

            }
            if (count($resultadoPesquisa) == 0) {

                throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
            }

            $this->view->status = TRUE;

            return $resultadoPesquisa;

        }
    }

    /**
     * Método gerarcSV()
     * Gera o arquivo csv conforme pesquisa
     *
     * @param array $consulta   => Dados do para gerar CSV.
     * @param string $relatorio => Tipo de relatório
     *
     * @return string  nome do arquivo
     */
    private function gerarCsv(array $consulta, $tipoRelatorio) {

        //echo "<pre>";print_r($consulta); echo "</pre>";

        //Diretório do Arquivo
        $caminho = '/var/www/docs_temporario/';

        //Nome do arquivo
        $nomeArquivo = 'ConsultaNotaFiscalVivo'.date("dmYHis").'.csv';

        //Flag para identifica se o arquivo foi gerado
        $arquivo = false;

        //Verifica se o caminho existe
        if ( file_exists($caminho) ){

			$somaValorNf = 0;
			$somaPago = 0;
			$somaValorItemNf = 0;
			$somaValorVivo = 0;

			// Gera CSV
            $csvWriter = new CsvWriter( $caminho.$nomeArquivo, ';', '', true);

            //Gera o cabeçalho
            if ($tipoRelatorio == 'N') {
                $csvWriter->addLine( "Resultado da Pesquisa por Nota Fiscal" );
                $cabecalho = array(
                    "NF/Série",
                    "Cod. Cliente",
                    "Cliente",
                    "Vencimento",
                    "Mês / Ano Referência",
                    "Ciclo",
                    "Retorno VIVO",
                    "Conta",
                    "Valor NF",
                    "Pago",
                    "Status Sascar",
                    "Status VIVO"
                );
            } else{
                $csvWriter->addLine( "Resultado da Pesquisa por Placa" );
                $cabecalho = array(
                    "NF/Série",
                    "Cod. Cliente",
                    "Cliente",
                    "Vencimento",
                    "Placa",
                    "Contrato",
                    "Mês / Ano Referência",
                    "Ciclo",
                    "Retorno VIVO",
                    "Valor Item NF",
                    "Valor VIVO",
                    "Status Sascar",
                    "Status VIVO",
                    "Obrigação Financeira"
                );
            }
            //Adiciona o Cabeçalho
            $csvWriter->addLine( $cabecalho );

            //Adiciona os dados ao corpo do CSV
            if (!empty($consulta)) {

				foreach ($consulta AS $relatorio) {

                    //Trata os dados
                    $relatorio->nf_serie                  = ( !empty($relatorio->nf_serie) )                ? $relatorio->nf_serie : ' ';
                    $relatorio->codigo_cliente            = ( !empty($relatorio->codigo_cliente) )          ? $relatorio->codigo_cliente : ' ';
					$relatorio->cliente    			      = ( !empty($relatorio->cliente) )                 ? $relatorio->cliente : ' ';
					$relatorio->vencimento                = ( !empty($relatorio->vencimento) )              ? $relatorio->vencimento : ' ';
					if ($tipoRelatorio == 'P') {
    					$relatorio->placa                 = ( !empty($relatorio->placa) )                   ? $relatorio->placa : ' ';
    					$relatorio->contrato              = ( !empty($relatorio->contrato) )                ? $relatorio->contrato : ' ';
					}
					$relatorio->data_referencia           = ( !empty($relatorio->data_referencia) )         ? $relatorio->data_referencia : ' ';
					$relatorio->ciclo                     = ( !empty($relatorio->ciclo) )                   ? $relatorio->ciclo : ' ';
					$relatorio->retorno_vivo              = ( !empty($relatorio->retorno_vivo) )            ? $relatorio->retorno_vivo : ' ';
					if ($tipoRelatorio == 'N') {
					    $relatorio->conta          	      = ( !empty($relatorio->conta) )                   ? $relatorio->conta : ' ';
    					$relatorio->valor_nf              = ( !empty($relatorio->valor_nf) )                ? $relatorio->valor_nf : '0';
    					$relatorio->pago                  = ( !empty($relatorio->pago) )                    ? $relatorio->pago : '0';
				    } else {
				        $relatorio->valor_item_nf         = ( !empty($relatorio->valor_item_nf) )           ? $relatorio->valor_item_nf : '0';
				        $relatorio->valor_vivo            = ( !empty($relatorio->valor_vivo) )              ? $relatorio->valor_vivo : '0';
				    }
				    $relatorio->status_sascar             = ( !empty($relatorio->status_sascar) )           ? $relatorio->status_sascar : ' ';
					$relatorio->status_vivo               = ( !empty($relatorio->status_vivo) )             ? $relatorio->status_vivo : ' ';
					if ($tipoRelatorio == 'P') {
					    $relatorio->obrigacao_financeira  = ( !empty($relatorio->obrigacao_financeira) )    ? $relatorio->obrigacao_financeira : ' ';
				    }

					// Corpo do CSV
				    if ($tipoRelatorio == 'N') {
                        $csvWriter->addLine(
                            array(
                                $relatorio->nf_serie,
    							$relatorio->codigo_cliente,
    							$relatorio->cliente,
    							$relatorio->vencimento,
    							$relatorio->data_referencia,
    							$relatorio->ciclo,
    							$relatorio->retorno_vivo,
    							$relatorio->conta,
    							number_format($relatorio->valor_nf,2,",","."),
    							number_format($relatorio->pago,2,",","."),
    							$relatorio->status_sascar,
    							$relatorio->status_vivo
                            )
                        );
                        $somaValorNf += $relatorio->valor_nf;
                        $somaPago += $relatorio->pago;
				    } else {
				        $csvWriter->addLine(
			                array(
		                        $relatorio->nf_serie,
		                        $relatorio->codigo_cliente,
		                        $relatorio->cliente,
		                        $relatorio->vencimento,
			                    $relatorio->placa,
			                    $relatorio->contrato,
		                        $relatorio->data_referencia,
		                        $relatorio->ciclo,
		                        $relatorio->retorno_vivo,
		                        number_format($relatorio->valor_item_nf,2,",","."),
		                        number_format($relatorio->valor_vivo,2,",","."),
		                        $relatorio->status_sascar,
		                        $relatorio->status_vivo,
			                    $relatorio->obrigacao_financeira
			                )
				        );
				        $somaValorItemNf += $relatorio->valor_item_nf;
				        $somaValorVivo += $relatorio->valor_vivo;
				    }

                } //Foreach

            }

			// Totais
            if ($tipoRelatorio == 'N') {
    			$csvWriter->addLine(
    				array(
    					'Total',
    					'',
    					'',
    					'',
    					'',
    					'',
    					'',
    					'',
    					number_format($somaValorNf,2,",","."),
    					number_format($somaPago,2,",",".")
    				)
    			);
            } else {
                $csvWriter->addLine(
                        array(
                                'Total',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                number_format($somaValorItemNf,2,",","."),
                                number_format($somaValorVivo,2,",",".")
                        )
                );
            }

			$totalRegistros = count($consulta);
			$totalRegistros = ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';

			// Totais
			$csvWriter->addLine(
				array(
					$totalRegistros
				)
			);

        } //IF File_exists

        //Verifica se o arquivo foi gerado
        $arquivo = file_exists( $caminho.$nomeArquivo);

        //Lança uma exceção em caso de erro na geração do arquivo
        if ( $arquivo === false ){
            throw new Exception();
        }

        //Se o arquivo foi gerado carrega a view para download do CSV
        if ( $arquivo === true ){
            return $caminho.$nomeArquivo;
        }

        return false;

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
     * Método inicializarParametros()
     * Inicializar parâmetros.
     *
     * @return boolean
     */
    private function inicializarParametros() {
    	$this->view->parametros = new StdClass;

    	$this->view->parametros->dt_evento_de = isset($this->view->parametros->dt_evento_de) ? trim($this->view->parametros->dt_evento_de) : '';
        $this->view->parametros->dt_evento_ate = isset($this->view->parametros->dt_evento_ate) ? trim($this->view->parametros->dt_evento_ate) : '';
        $this->view->parametros->status_sascar = isset($this->view->parametros->status_sascar) ? trim($this->view->parametros->status_sascar) : '';
        $this->view->parametros->status_vivo = isset($this->view->parametros->status_vivo) ? $this->view->parametros->status_vivo : '';
        $this->view->parametros->selecao_por = isset($this->view->parametros->selecao_por) ? trim($this->view->parametros->selecao_por) : '';
        $this->view->parametros->nota_fiscal = isset($this->view->parametros->nota_fiscal) ? trim($this->view->parametros->nota_fiscal) : '';
        $this->view->parametros->serie = isset($this->view->parametros->serie) ? trim($this->view->parametros->serie) : '';
        $this->view->parametros->cliente = isset($this->view->parametros->cliente) ? trim($this->view->parametros->cliente) : '';
        $this->view->parametros->cpfcnpj = isset($this->view->parametros->cpfcnpj) ? trim($this->view->parametros->cpfcnpj) : '';
        $this->view->parametros->placa = isset($this->view->parametros->placa) ? trim($this->view->parametros->placa) : '';
        $this->view->parametros->tipo_periodo = isset($this->view->parametros->tipo_periodo) ? trim($this->view->parametros->tipo_periodo) : '';

        $this->view->parametros->buscarEventoStatus = $this->dao->buscarEventoStatus();
        $this->view->parametros->buscarSerieNotaFiscal = $this->dao->buscarSerieNotaFiscal();

    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     *
     * @return stdClass Parametros tradados
     *
     * @retrun stdClass
     */
    public function tratarParametrosPesquisa() {

        $temp = array();

        if (isset($_POST['acao']) && $_POST['acao'] = 'pesquisar') {

            foreach ($_POST as $key => $value) {
                if (isset($_POST[$key])) {
                    if (is_array($_POST[$key])){
                        $temp[$key] = $_POST[$key];
                    } else {
                        $temp[$key] = trim($_POST[$key]);
                    }
                } elseif (isset($_SESSION['pesquisa'][$key])) {
                    if (is_array($_SESSION['pesquisa'][$key])){
                        $temp[$key] = $_SESSION['pesquisa'][$key];
                    } else {
                        $temp[$key] = trim($_SESSION['pesquisa'][$key]);
                    }
                }
                $_SESSION['pesquisa'][$key] = $temp[$key];
            }

        }

        $_SESSION['pesquisa']['usarSessao'] = TRUE;

        return (object) $_SESSION['pesquisa'];
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
                if (!is_array($_POST[$key])) {
                    $retorno->$key = isset($_POST[$key]) ? htmlentities(strip_tags($value)) : '';
                } else {
                    $retorno->$key = isset($_POST[$key]) ? $value : '';
                }
            }
            //Limpa o POST
            //unset($_POST);
        }

        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {

                //Verifica se atributo já existe e não sobrescreve.
                if (!isset($retorno->$key)) {
                    if (!is_array($_GET[$key])) {
                        $retorno->$key = isset($_GET[$key]) ? htmlentities(strip_tags($value)) : '';
                    } else {
                        $retorno->$key = isset($_GET[$key]) ? $value : '';
                    }
                }
            }
            //Limpa o GET
            //unset($_GET);
        }


        return $retorno;
    }

    /**
     * Método responsável por limpar sessão de pesquisa
     *
     * @return void
     */
    public function limparSessaoPesquisa() {

        if (isset($_SESSION['pesquisa']) && is_array($_SESSION['pesquisa'])) {
            foreach ($_SESSION['pesquisa'] as $key => $value) {
                $_SESSION['pesquisa'][$key] = '';
            }
        }
    }

}

?>

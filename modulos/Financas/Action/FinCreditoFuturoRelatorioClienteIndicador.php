<?php

require_once _SITEDIR_ . "lib/Components/PHPExcel/PHPExcel.php";

/**
 * FinCreditoFuturoRelatorioClienteIndicador.php
 * - Relatório de Clientes Indicadores - Crédito Futuro
 *
 * @package Finanças
 * @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
 * @since   21/10/2013
 *
 */
class FinCreditoFuturoRelatorioClienteIndicador {

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
    }

    /**
     * Método index()
     *
     * @return void()
     */
    public function index() {
        
        try {

            if ((isset($_POST['acao']) && trim($_POST['acao']) == 'pesquisar')) {
                
                $this->limparSessaoPesquisa();

                $this->view->parametros = $this->tratarParametrosPesquisa();
                
                //echo "<pre>";print_r($this->view->parametros); echo "</pre>";
                
                //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                $this->view->dados = $this->pesquisar($this->view->parametros);

                //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                $this->view->xls = $this->gerarXls($this->view->dados, $this->view->parametros);
            
            } 

            //Inicializa os dados
            $this->inicializarParametros();

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            $this->view->parametros->usuarioInclusaoRelatorioClienteIndicador = $this->dao->buscarUsuarioInclusaoRelatorioClienteIndicador();
            
            $this->view->mensagemAlerta = $e->getMessage();
        }

        require_once _MODULEDIR_ . "Financas/View/fin_credito_futuro_relatorio_cliente_indicador/index.php";
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
            
            $resultadoPesquisa = $this->dao->buscarClientesIndicadores($parametros);
            
            if (count($resultadoPesquisa) == 0) {
                
                throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
            }

            $this->view->status = TRUE;

            return $resultadoPesquisa;

        }
    }

    /**
     * Método gerarXls()
     * Gera o arquivo xls conforme pesquisa
     *
     * @param array    $relatorio  => Dados do para gerar XLS. 
     * @param stdClass $parametros => Parâmetros para gerar cabeçalho.
     * 
     * @return string  nome do arquivo
     */
    private function gerarXls(array $relatorio, stdClass $parametros) {

        //echo "<pre>";print_r($parametros); echo "</pre>";
        
        $cfcieqptoInstalado = ($parametros->cfcieqpto_instalado == 't') ? 'Sim' : (($parametros->cfcieqpto_instalado == 'f') ? 'Não' : 'Todos');
        $cfciformaInclusao = (trim($parametros->cfciforma_inclusao) == 'M') ? 'Manual' : ((trim($parametros->cfciforma_inclusao) == 'A') ? 'Automática' : 'Todos');
        $cfciusuoidInclusao = ( $parametros->cfciusuoid_inclusao !== "") ? $this->dao->buscarUsuarioInclusaoRelatorioClienteIndicadorPorId($parametros->cfciusuoid_inclusao) : 'Todos';
        
        // Arquivo modelo para gerar o XLS
        $arquivoModelo = _MODULEDIR_.'Financas/View/fin_credito_futuro_relatorio_cliente_indicador/template_relatorio_credito_futuro_cliente_indicador.xlsx';
    
        // Instância PHPExcel
        $reader = PHPExcel_IOFactory::createReader("Excel2007");
            
        // Carrega o modelo
        $PHPExcel = $reader->load($arquivoModelo);
        
        // Carrega Dados dos filtros no cabeçalho do XLS
        $PHPExcel->getActiveSheet()->setCellValue('F2', utf8_encode($parametros->cfcidt_inclusao_de." a ".$parametros->cfcidt_inclusao_ate));
        $PHPExcel->getActiveSheet()->setCellValue('F3', utf8_encode($parametros->nome.$parametros->razao_social));    
        $PHPExcel->getActiveSheet()->setCellValue('F4', utf8_encode($parametros->nome_indicado.$parametros->razao_social_indicado));
        $PHPExcel->getActiveSheet()->setCellValue('F5', utf8_encode($cfciusuoidInclusao));
        $PHPExcel->getActiveSheet()->setCellValue('F6', utf8_encode($parametros->cfcitermo));
        $PHPExcel->getActiveSheet()->setCellValue('F7', utf8_encode($cfcieqptoInstalado));        
        $PHPExcel->getActiveSheet()->setCellValue('F8', utf8_encode($cfciformaInclusao));        

        //echo "<pre>";print_r($relatorio); echo "</pre>";
        if (count($relatorio)) {
        
            $linha = 12;
            
            foreach ($relatorio as $row) {

                //$PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getNumberFormat()->setFormatCode('0');
                $PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($row->dt_inclusao));

                $PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($row->cliente_nome));

                //$PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getNumberFormat()->setFormatCode('0');
                $PHPExcel->getActiveSheet()->setCellValue('C'.$linha, utf8_encode($row->doc));

                $PHPExcel->getActiveSheet()->setCellValue('D'.$linha, utf8_encode($row->contrato));

                //$PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getNumberFormat()->setFormatCode('0');
                $PHPExcel->getActiveSheet()->setCellValue('E'.$linha, utf8_encode($row->cliente_nome_indicado));

                $PHPExcel->getActiveSheet()->setCellValue('F'.$linha, utf8_encode($row->nome_campanha));

                //$PHPExcel->getActiveSheet()->getStyle('G'.$linha)->getNumberFormat()->setFormatCode('0');
                $PHPExcel->getActiveSheet()->setCellValue('G'.$linha, utf8_encode($row->dt_inicio_vigencia));

                //$PHPExcel->getActiveSheet()->getStyle('H'.$linha)->getNumberFormat()->setFormatCode('#.##0');
                $PHPExcel->getActiveSheet()->setCellValue('H'.$linha, utf8_encode($row->dt_fim_vigencia));

                $PHPExcel->getActiveSheet()->setCellValue('I'.$linha, utf8_encode($row->cfcieqpto_instalado_descricao));

                $PHPExcel->getActiveSheet()->setCellValue('J'.$linha, utf8_encode($row->cfciforma_inclusao_descricao)); 

                $PHPExcel->getActiveSheet()->setCellValue('K'.$linha, utf8_encode($row->usuario_inclusao_nome)); 
                
                $PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $PHPExcel->getActiveSheet()->getStyle('D'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $PHPExcel->getActiveSheet()->getStyle('F'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $PHPExcel->getActiveSheet()->getStyle('G'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $PHPExcel->getActiveSheet()->getStyle('H'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $PHPExcel->getActiveSheet()->getStyle('I'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPExcel->getActiveSheet()->getStyle('J'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $PHPExcel->getActiveSheet()->getStyle('K'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $linha++;
            }
        
            $PHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);                
            $PHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true); 
        }
        else {
            $PHPExcel->getActiveSheet()->setCellValue('A12', utf8_encode("Nenhum resultado encontrado."));
        }

        $writer = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $writer->setPreCalculateFormulas(false);
        
        //Relatorio Registros de Credito Futuro ddmmaaaa hhmmss.xls
        $file = "relatorio_cliente_indicador_" . date('dmY') . "_" . date('Hi') . ".xlsx";
        $dir = '/var/www/docs_temporario/';

        if (!file_exists($dir) || !is_writable($dir)) {
            throw new Exception('Houve um erro ao gerar o arquivo.');
        }
        
        $writer->save($dir.$file);
        

        if (file_exists($dir.$file)) {
            return $dir.$file;
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
        if (trim($parametros->cfcidt_inclusao_de) == '') {
            $obrigatoriosPreenchidos = false;
            $camposDestaques[] = array(
                'campo' => 'cfcidt_inclusao_de'
            );
        }

        //válido se foi informado a data de final de inclusao
        if (trim($parametros->cfcidt_inclusao_ate) == '') {
            $obrigatoriosPreenchidos = false;
            $camposDestaques[] = array(
                'campo' => 'cfcidt_inclusao_ate'
            );
        }

        $this->view->dados = $camposDestaques;

        if ($obrigatoriosPreenchidos == false) {
            throw new Exception("O período de inclusão é uma informação obrigatória.");
        }

        if ($periodoAnalise == false) {
            throw new Exception("Deve ser informado data inicial e final.");
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

        $this->view->parametros->cfcidt_inclusao_de = isset($this->view->parametros->cfcidt_inclusao_de) ? trim($this->view->parametros->cfcidt_inclusao_de) : '';
        $this->view->parametros->cfcidt_inclusao_ate = isset($this->view->parametros->cfcidt_inclusao_ate) ? trim($this->view->parametros->cfcidt_inclusao_ate) : '';
        $this->view->parametros->cliente_nome = isset($this->view->parametros->cliente_nome) ? trim($this->view->parametros->cliente_nome) : '';
        $this->view->parametros->tipo_pessoa = isset($this->view->parametros->tipo_pessoa) ? trim($this->view->parametros->tipo_pessoa) : '';
        $this->view->parametros->cpf = isset($this->view->parametros->cpf) ? trim($this->view->parametros->cpf) : '';
        $this->view->parametros->cnpj = isset($this->view->parametros->cnpj) ? trim($this->view->parametros->cnpj) : '';
        $this->view->parametros->contrato = isset($this->view->parametros->contrato) ? trim($this->view->parametros->contrato) : '';
        $this->view->parametros->cfcistatus = isset($this->view->parametros->cfcistatus) ? trim($this->view->parametros->cfcistatus) : '';
        $this->view->parametros->cfciusuoid_inclusao = isset($this->view->parametros->cfciusuoid_inclusao) ? trim($this->view->parametros->cfciusuoid_inclusao) : '';
        $this->view->parametros->usuarioInclusaoRelatorioClienteIndicador = $this->dao->buscarUsuarioInclusaoRelatorioClienteIndicador();
        
        $this->view->parametros->cliente_id = isset($this->view->parametros->cliente_id) ? $this->view->parametros->cliente_id : '';
        $this->view->parametros->cliente_id_indicado = isset($this->view->parametros->cliente_id_indicado) ? $this->view->parametros->cliente_id_indicado : '';
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
                    $temp[$key] = trim($_POST[$key]);
                } elseif (isset($_SESSION['pesquisa'][$key])) {
                    $temp[$key] = trim($_SESSION['pesquisa'][$key]);
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
    
    /**
     * Buscar cliente por nome sendo ele PJ || PF
     *
     * @return array $retorno
     */
    public function buscarClienteNome() {
    
        $parametros = $this->tratarParametros();
        
        $parametros->tipo = trim($parametros->filtro) != '' ? trim($parametros->filtro) : '';
        $parametros->nome = trim($parametros->term) != '' ? trim($parametros->term) : '';
    
        $retorno = $this->dao->buscarClienteNome($parametros);
    
        echo json_encode($retorno);
        exit;
    }
    
    /**
     * Buscar cliente por documento (CPF/CNPJ)
     *
     * @return array $retorno
     */
    public function buscarClienteDoc() {
    
        $parametros = $this->tratarParametros();
    
        $parametros->tipo = trim($parametros->filtro);
        $parametros->documento = preg_replace("/[^0-9]/", "", $parametros->term);
    
        $retorno = $this->dao->buscarClienteDoc($parametros);
    
        echo json_encode($retorno);
        exit;
    }

}

?>

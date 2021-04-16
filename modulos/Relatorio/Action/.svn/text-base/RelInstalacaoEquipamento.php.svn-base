<?php

/**
 * Classe RelInstalacaoEquipamento.
 * Camada de regra de negócio.
 *
 * @package  Relatorio
 * @author   Ricardo Bonfim <ricardo.bonfim@meta.com.br>
 *
 */
class RelInstalacaoEquipamento {

    /**
     * Objeto DAO da classe.
     *
     * @var RelInstalacaoEquipamentoDAO
     */
    private $dao;

    /**
     * Objeto com os dados do arquivo de saída do relatório
     *
     * @var object
     */
    private $arquivo;

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

            //Verificar se a ação gerarRelatorio e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'gerarRelatorio' ) {
                $this->validarCamposPesquisa($this->view->parametros);
                $nomeArquivo = $this->gerarRelatorioInstalacaoEquipamento($this->view->parametros);
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        //Incluir a view padrão
        require_once _MODULEDIR_ . "Relatorio/View/rel_instalacao_equipamento/index.php";
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

        $this->view->parametros->tiposServico = $this->dao->buscarTiposServico();
        $this->view->parametros->tiposInstalacao = $this->dao->buscarTiposInstalacao();

    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     *
     * @param stdClass $filtros Filtros da pesquisa
     *
     * @return array
     */
    private function gerarRelatorioInstalacaoEquipamento(stdClass $filtros) {

        $resultadoPesquisa = $this->dao->buscarDadosInstalacaoEquipamento($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $nomeArquivo = "/var/www/docs_temporario/instalacaoequipamentos_" . date("Ymd_Gis") . ".csv";
        $this->abrirArquivo($nomeArquivo);

        $this->gravarCabecalhoArquivo();

        $this->gravarRelatorioArquivo($resultadoPesquisa);

        $this->fecharArquivo();

        $this->view->status = TRUE;

        return $nomeArquivo;
    }

    private function gravarCabecalhoArquivo() {
        // Relatório gerado deverá estar no formato CSV com a sequência de colunas abaixo: (na mesma linha)
        $cabecalho = array(
            "nota",
            "dt_entrada",
            "serie_nota",
            "dt_emissao",
            "cliente", 
            "instalador",
            "representante", 
            "cnpj",
            "nr_serie",
            "nr_patrimonio",
            "nr_os",
            "nr_contrato",
            "cd_produto",
            "ds_produto",
            "visitas_improdutivas",
            "motivo",
            "pagamento",
            "dt_conclusao_os",
            "tipo_contrato",
            "classe",
            "valor_total_nota",          
            "qtd_deslocamento",
            "valor_deslocamento",
            "valor_total_deslocamento",
            "valor_desconto",
            "valor_pedagio",
            "valor_comissao",
            "valor_total_servico",
            "tipo_os");

        $this->gravarDados($cabecalho);
    }

    private function gravarRelatorioArquivo($dadosPesquisa) {

        for($i=0; $i < count($dadosPesquisa); $i++) {
            $this->gravarDados($dadosPesquisa[$i]);

            // Salva no arquivo a cada 1000 registros
            if($i%1000 === 0) {
                $this->salvarDadosBuffer();
            }
        }
    }

    /**
     * Abre o arquivo
     */
    private function abrirArquivo($nomeArquivo) {
        $this->arquivo = fopen($nomeArquivo, "w" );
    }

    private function fecharArquivo() {
        fclose( $this->arquivo );
    }

    /**
     * Recebe um array e grava no arquivo no formato CSV separado por ';'
     *
     * @param array $dados 
     */
    private function gravarDados($dados) {
        $dadosCsv = implode($dados, ';');
        $dadosCsv .= "\n";
        fwrite($this->arquivo, $dadosCsv);
    }

    /**
     * Grava os dados que estão no buffer de saída no arquivo, liberando a memória
     */
    private function salvarDadosBuffer() {
        fflush($this->arquivo);
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
    private function validarCamposPesquisa(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        if (!isset($dados->dataInicial) || trim($dados->dataInicial) == '') {
            $camposDestaques[] = array(
                'campo' => 'dataInicial'
            );
            $error = true;
        }

        if (!isset($dados->dataFinal) || trim($dados->dataFinal) == '') {
            $camposDestaques[] = array(
                'campo' => 'dataFinal'
            );
            $error = true;
        }

        if (!isset($dados->tipoPesquisa) || trim($dados->tipoPesquisa) == '') {
            $camposDestaques[] = array(
                'campo' => 'tipoPesquisa'
            );
            $error = true;
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

}
<?php

/**
 * Classe CadDadosEquipamento.
 * Camada de regra de negócio.
 *
 * @package  Relatorio
 * @author   Denilson Andre de Sousa  <denilson.sousa@sascar.com.br>
 *
 */

require_once _SITEDIR_."lib/Components/CsvWriter.php";

class RelAnaliseConsumoSatelital {

    /** Objeto DAO da classe */
    private $dao;

	/** propriedade para dados a serem utilizados na View. */
    private $view;


    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";
    const MENSAGEM_ERRO_EXPORTAR              = "Houve um erro ao exportar arquivo.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ALERTA_INFORME_FILTRO      = "Informe algum filtro para realizar a busca.";
    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_ALERTA_CAMPOS_TAMANHO      = "A Descrição deve ter no mínimo três dígitos.";
    const MENSAGEM_ALERTA_DUPLICIDADE         = "Já existe um registro com a mesma descrição.";

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
        $this->view->consolidadoCliente     = null;
        $this->view->dados           = null;
        $this->view->parametros      = null;
        $this->view->status          = false;

    }

    /**
     * Reponsável também por realizar a pesquisa invocando o método privado
     * @return void
     */
    public function index() {
        try {
            $this->view->parametros = $this->tratarParametros();

            if (isset($this->view->parametros) && 
                (isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar')
            ) {

                // VERIFICAÇÃO SE INFORMOU ALGUM FILTRO
                if ($this->verificarFiltro() == false) {
                    $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_INFORME_FILTRO;
                } else if ($this->view->parametros->tipopesquisa == 'sintetico') {
                    // VERIFICAR SE DEVE GERAR RESULTADO CONSOLIDADO DO CLIENTE
                    // QUANDO TEM PESQUISA DE CLIENTE MAS AINDA NÃO TEM O CÓDIGO DO CLIENTE NA TELA
                    $this->view->consolidadoCliente = $this->pesquisarConsolidado($this->view->parametros);
                } else {
                    $this->view->dados = $this->pesquisar($this->view->parametros);
                }

            } 
        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
        }

        //Incluir a view padrão
        require_once _MODULEDIR_ . "Relatorio/View/rel_analise_consumo_satelital/index.php";
    }

    /**
     * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros
     *
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
     * Verificar se foi passado filtros para busca
     *
     * @return stdClass
     */
    private function verificarFiltro() {
        $campos = array('antena', 'contrato', 'cliente', 'mes', 'ano');
        
        foreach ($campos as $campo) {
            if (isset($this->view->parametros->$campo) && !empty($this->view->parametros->$campo)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     * @param stdClass $filtros Filtros da pesquisa
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        $filtros = $this->tratarParametros();

        $resultadoPesquisa = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            $this->view->mensagemAlerta = self::MENSAGEM_NENHUM_REGISTRO;
        }

        $retorno = array();
        $retorno['OUTROS'] = array();

        foreach ($resultadoPesquisa as $key => $equip) {
            $retorno['OUTROS'][] = $equip;
        }

        $this->view->filtros = $filtros;
        $this->view->status = TRUE;

        return $retorno;
    }

    /**
     * Responsável por tratar e retornar o resultado consolidado.
     * @param stdClass $filtros Filtros da pesquisa
     * @return array
     */
    private function pesquisarConsolidado(stdClass $filtros) {

        $filtros = $this->tratarParametros();

        $resultadoConsolidado = $this->dao->pesquisarConsolidado($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoConsolidado) == 0) {
            $this->view->mensagemAlerta = self::MENSAGEM_NENHUM_REGISTRO;
        }

        $this->view->filtros = $filtros;
        $this->view->status = TRUE;

        return $resultadoConsolidado;
    }

    /**
     * Método usado para gerar o arquivo csv
     * @param  stdClass $filtros [description]
     * @return array            
     */
    public function exportarCSV() {

        $arquivo = 'dados_analise_consumo_satelital_'.time().'.csv';
        $diretorio = '/var/www/docs_temporario/';

        $filtros = $this->tratarParametros();

        try {
            if (is_dir($diretorio) && is_writable($diretorio)) {

                $dados = $this->pesquisar($filtros);

                $csvWriter = new CsvWriter($diretorio.$arquivo, ';', '', true);

                if(count($dados['OUTROS']) > 0) {
                    // Colunas
                    $csvWriter->addLine(array(
                        utf8_decode('acsasatno_serie'),
                        utf8_decode('acsconsumo_operadora'),
                        utf8_decode('acsdata_apuracao'),
                        utf8_decode('acsveiplaca'),
                        utf8_decode('acsveioid'),
                        utf8_decode('acsequno_serie'),
                        utf8_decode('acsequesn'),
                        utf8_decode('acseveversao'),
                        utf8_decode('acslinpotoid'),
                        utf8_decode('acslinnumero'),
                        utf8_decode('acscslstatus'),
                        utf8_decode('acslincid'),
                        utf8_decode('acslscdescricao'),
                        utf8_decode('acsasatstatus_fornecedor'),
                        utf8_decode('acscsidescricao'),
                        utf8_decode('acsconnumero'),
                        utf8_decode('acstpcdescricao'),
                        utf8_decode('acseqcdescricao'),
                        utf8_decode('acscpagvl_servico'),
                        utf8_decode('acscpagmonitoramento'),
                        utf8_decode('acsconsvalor_total'),
                        utf8_decode('acstimeonsat'),
                        utf8_decode('acstimeoffsat'),
                        utf8_decode('acsconclioid'),
                        utf8_decode('acsclinome'),
                        utf8_decode('acsoploperadora'),
                        utf8_decode('acsgerenciadoras')
                    ));
                    
                    foreach($dados['OUTROS'] as $resultado) {
                        
                        $linha = array(); 
                        $linha[0] = $resultado->acsasatno_serie;
                        $linha[1] = $resultado->acsconsumo_operadora;
                        $linha[2] = $resultado->acsdata_apuracao;
                        $linha[3] = $resultado->acsveiplaca;
                        $linha[4] = $resultado->acsveioid;
                        $linha[5] = $resultado->acsequno_serie;
                        $linha[6] = $resultado->acsequesn;
                        $linha[7] = $resultado->acseveversao;
                        $linha[8] = $resultado->acslinpotoid;
                        $linha[9] = $resultado->acslinnumero;
                        $linha[10] = $resultado->acscslstatus;
                        $linha[11] = $resultado->acslincid;
                        $linha[12] = $resultado->acslscdescricao;
                        $linha[13] = $resultado->acsasatstatus_fornecedor;
                        $linha[14] = $resultado->acscsidescricao;
                        $linha[15] = $resultado->acsconnumero;
                        $linha[16] = $resultado->acstpcdescricao;
                        $linha[17] = $resultado->acseqcdescricao;
                        $linha[18] = $resultado->acscpagvl_servico;
                        $linha[19] = $resultado->acscpagmonitoramento;
                        $linha[20] = $resultado->acsconsvalor_total;
                        $linha[21] = $resultado->acstimeonsat;
                        $linha[22] = $resultado->acstimeoffsat;
                        $linha[23] = $resultado->acsconclioid;
                        $linha[24] = $resultado->acsclinome;
                        $linha[25] = $resultado->acsoploperadora;
                        $linha[26] = $resultado->acsgerenciadoras;


                        $csvWriter->addLine($linha);
                    }                    
                    // return array('caminho' => $diretorio . $arquivo , 'arquivo' => $arquivo);
                    
                }else{
                    throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
                }

                //Verifica se o arquivo foi gerado
                $arquivoGerado = file_exists($diretorio.$arquivo);

                if ($arquivoGerado === false) {
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
                }

                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $arquivo);
                readfile($diretorio.$arquivo);
                die;

                // throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
            } else {
                throw new Exception(self::MENSAGEM_ERRO_EXPORTAR);                
            } 
        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
        }

        //Incluir a view padrão
        require_once _MODULEDIR_ . "Relatorio/View/rel_analise_consumo_satelital/index.php";
    }

    

    /**
     * Convert XML em array
     * @param $xml
     * @return $array
     * */
    private function toArray($xml) {

        $array = json_decode(json_encode($xml), TRUE);

        foreach ( array_slice($array, 0) as $key => $value ) {
            if ( empty($value) ) $array[$key] = NULL;
            elseif ( is_array($value) ) $array[$key] = $this->toArray($value);
        }

        return $array;
    }

}

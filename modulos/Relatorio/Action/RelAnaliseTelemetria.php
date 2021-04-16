<?php

/**
 * Classe RelAnaliseTelemetria.
 * Camada de regra de negócio.
 *
 * @package  Relatorio
 * @author   Leandro Alves Ivanaga <leandro.ivanaga@meta.com.br> <leandro.ivanaga.ext@sascar.com.br>
 *
 */


// die($dbstring_bdcentral);

require_once _SITEDIR_."lib/Components/CsvWriter.php";

class RelAnaliseTelemetria {



    /** Objeto DAO da classe */
    private $dao;

    /** propriedade para dados a serem utilizados na View. */
    private $view;

    /** Usuario logado */
    private $usuarioLogado;

    /** Config de dados de equipamento */
    private $configDadosEquipamento;
    private $configDadosEquipamentoCommands;

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
        $this->usuarioLogado         = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';     
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
        require_once _MODULEDIR_ . "Relatorio/View/rel_analise_telemetria/index.php";
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
        $campos = array('placa', 'periodo_de', 'periodo_ate');

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

        $retorno = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        // if (count($resultadoPesquisa) == 0) {
        //     $this->view->mensagemAlerta = self::MENSAGEM_NENHUM_REGISTRO;
        // }

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

        $arquivo = 'dados_equipamento_'.time().'.csv';
        $diretorio = '/var/www/docs_temporario/';

        $filtros = $this->tratarParametros();

        try {
            if (is_dir($diretorio) && is_writable($diretorio)) {

                $dados = $this->pesquisar($filtros);
                $dadosEquipamento = json_decode($filtros->equipamentos);

                $csvWriter = new CsvWriter($diretorio.$arquivo, ';', '', true);

                if(count($dados['COMMAND_MTC']) > 0) {

                    $csvWriter->addLine(array(''));
                    $csvWriter->addLine(array('Grupo Equipamento MTC'));

                    // Colunas
                    $csvWriter->addLine(array(
                        utf8_decode('Placa'),
                        utf8_decode('Cliente'),
                        utf8_decode('Eq Esn'),
                        utf8_decode('Eq Versão'),
                        utf8_decode('Eq Projeto'),
                        utf8_decode('Firmware Versão'),
                        utf8_decode('Firmware Date'),
                        utf8_decode('Lua Versão'),
                        utf8_decode('Lua Date'),
                        utf8_decode('Data Chegada')
                    ));

                    foreach($dados['COMMAND_MTC'] as $resultado) {
                        $linha = array(); 
                        $linha[0] = $resultado->veiplaca;
                        $linha[1] = $resultado->clinome;
                        $linha[2] = $resultado->equesn;
                        $linha[3] = $resultado->eveversao;
                        $linha[4] = $resultado->eprnome;

                        $esn = $resultado->equesn;
                        if (property_exists($dadosEquipamento, $esn)) {
                            $linha[5] = $dadosEquipamento->$esn->versao_firmware;
                            $linha[6] = $dadosEquipamento->$esn->firmware_date;
                            $linha[7] = $dadosEquipamento->$esn->lua_versao_script;
                            $linha[8] = $dadosEquipamento->$esn->data_script_lua;
                            $linha[9] = $dadosEquipamento->$esn->data_chegada;
                        }

                        $csvWriter->addLine($linha);
                    }                    
                    // return array('caminho' => $diretorio . $arquivo , 'arquivo' => $arquivo);
                } 

                if(count($dados['COMMAND_LMU']) > 0) {

                    $csvWriter->addLine(array(''));
                    $csvWriter->addLine(array('Grupo Equipamento LMU'));

                    // Colunas
                    $csvWriter->addLine(array(
                        utf8_decode('Placa'),
                        utf8_decode('Cliente'),
                        utf8_decode('Eq Esn'),
                        utf8_decode('Eq Versão'),
                        utf8_decode('Eq Projeto'),
                        utf8_decode('Versão Perfil'),
                        utf8_decode('Versão Teclado'),
                        utf8_decode('Hosted App'),
                        utf8_decode('Blackbox'),
                        utf8_decode('Peg Enables'),
                        utf8_decode('Inbound Url00'),
                        utf8_decode('Inbound Port00'),
                        utf8_decode('Inbound Url01'),
                        utf8_decode('Inbound Port01'),
                        utf8_decode('Data Chegada'),

                        utf8_decode('Ini Speed Deaccel'),
                        utf8_decode('Breack Deaccel')
                    ));

                    foreach($dados['COMMAND_LMU'] as $resultado) {
                        $linha = array();
                        $linha[0] = $resultado->veiplaca;
                        $linha[1] = $resultado->clinome;
                        $linha[2] = $resultado->equesn;
                        $linha[3] = $resultado->eveversao;
                        $linha[4] = $resultado->eprnome;

                        $esn = $resultado->equesn;
                        if (property_exists($dadosEquipamento, $esn)) {
                            $linha[5] = $dadosEquipamento->$esn->versao_perfil;
                            $linha[6] = $dadosEquipamento->$esn->versao_teclado;
                            $linha[7] = $dadosEquipamento->$esn->hosted_app;
                            $linha[8] = $dadosEquipamento->$esn->telemetria_segundo_a_segundo;
                            $linha[9] = $dadosEquipamento->$esn->peg_enables;
                            $linha[10] = $dadosEquipamento->$esn->inbound_url00;
                            $linha[11] = $dadosEquipamento->$esn->inbound_port00;
                            $linha[12] = $dadosEquipamento->$esn->inbound_url01;
                            $linha[13] = $dadosEquipamento->$esn->inbound_port01;
                            $linha[14] = $dadosEquipamento->$esn->data_chegada;
                            $linha[15] = $dadosEquipamento->$esn->ini_speed_deaccel;
                            $linha[16] = $dadosEquipamento->$esn->breack_deaccel;
                        }

                        $csvWriter->addLine($linha);
                    }                    
                    // return array('caminho' => $diretorio . $arquivo , 'arquivo' => $arquivo);
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
        require_once _MODULEDIR_ . "Cadastro/View/cad_dados_equipamento/index.php";
    }

    /**
     * Responsável por realizar o download do xml de dados do equipamento
     * @return xml file
     */
    public function download() {
        $filtros = $this->tratarParametros();

        $nameFile = 'equip_' . $filtros->projeto .  '_esn_' . $filtros->esn . '.xml';

        $key = $this->configDadosEquipamento[$filtros->projeto];
        $defaultCommand = $this->configDadosEquipamentoCommands[$key];

        $xmlBuffer = $this->sendCommandConsultarResumoConfiguracao($filtros->esn, $defaultCommand);
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . $nameFile);
        header("Content-Type: text/xml");
        header("Content-Transfer-Encoding: binary");
        echo $xmlBuffer['valor'];
    }

    /**
     * Responsável por realizar o download do xml de dados do equipamento
     * @return xml file
     */
    public function showXml() {
        $filtros = $this->tratarParametros();

        $nameFile = 'equip_' . $filtros->projeto .  '_esn_' . $filtros->esn . '.xml';

        $key = $this->configDadosEquipamento[$filtros->projeto];
        $defaultCommand = $this->configDadosEquipamentoCommands[$key];

        $xmlBuffer = $this->sendCommandConsultarResumoConfiguracao($filtros->esn, $defaultCommand);
        header("Content-Type: text/xml");
        echo $xmlBuffer['valor'];
    }

    /**
     * Responsável por buscar e retornar dados do equipamento.
     * @return array
     */
    public function getDadosEquipamento() {

        $filtros = $this->tratarParametros();

        try {
            $retorno = array();

            if (array_key_exists($filtros->projeto, $this->configDadosEquipamento)) {
                $key = $this->configDadosEquipamento[$filtros->projeto];
                $defaultCommand = $this->configDadosEquipamentoCommands[$key];

                $xmlBuffer = $this->sendCommandConsultarResumoConfiguracao($filtros->esn, $defaultCommand);
                if (isset($xmlBuffer['valor']) && $xmlBuffer['valor'] > 0) {
                    $xmlBuffer['sucesso'] = false;
                    echo json_encode($xmlBuffer);
                    die;
                }

                $xmlFormatado = "";
                for($i=0;$i<strlen($xmlBuffer['valor']);$i++){
                    if((preg_match('/[\w\d_"<>\/?.:=\s]/',$xmlBuffer['valor'][$i]))){
                        $xmlFormatado .= $xmlBuffer['valor'][$i];
                    }
                }
                $xmlFormatado = new SimpleXMLElement($xmlFormatado);
                $retorno = $this->toArray($xmlFormatado);
                
                $retorno['equipamentoConfig'] = $key;
                $retorno['sucesso'] = true;
                echo json_encode($retorno);
                die;
            }

        } catch (Exception $e) {
            $retorno = array(
                'sucesso' => false,
                'message' => $e->getMessage()
            );
        }

        echo json_encode($retorno);
        die;
    }

    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     * @param string $esn Código ESN
     * @param string $defautCommand Comando padrão a ser executado, definido na constante
     * @return array
     */
    private function sendCommandConsultarResumoConfiguracao($esn, $defaultCommand) {
        
        $finalizador="\r\n";
        /*
         * Comando para solicitar ao SERVIDOR as configurações 
         * DEVOLVE UM XML COMO RESPOSTA
        */
        $acao = 'X';
        $sendCommand = str_replace('__ESN__', $esn, $defaultCommand);
        $sendCommand = str_replace('__FINALIZADOR__', $finalizador, $sendCommand);
        $sendCommand = str_replace('__ACAO__', $acao, $sendCommand);

        $xmlBuffer = $this->comandoSocketTesteEquipamento($sendCommand);
        $xmlBuffer = array('socket' => 2, 'valor' => $xmlBuffer['codigo'], 'descricao' => $xmlBuffer['descricao']);
      
        return $xmlBuffer;
    }            

    // função para retorno de grande volume de caracteres pelo socket
    private function comandoSocketTesteEquipamento($executeComando, $max_len = 150000, $nonblock = false){

        $arrRetorno[0]   = "Comando foi executado";
        $arrRetorno[1]   = "Comando foi armazenado";
        $arrRetorno[2]   = "Equipamento esta offline";
        $arrRetorno[3]   = "Comando na fila";
        $arrRetorno[4]   = "Comando nao e suportado";
        $arrRetorno[5]   = "Comando nao executado por timeout";
        $arrRetorno[6]   = "Comando foi recusado";
        $arrRetorno[7]   = "Comando foi enviado para operacoes offline";
        $arrRetorno[8]   = "Comando via SMS foi enviado";
        $arrRetorno[9]   = "Equipamento on-line";
        $arrRetorno[10]  = "Comando nao armazenado devido a outro comando estar na fila";
        $arrRetorno[11]  = "Texto como erro apos [11 TEXTO_COM_O_ERRO]";
        $arrRetorno[12]  = "Existe um comando enfileirado pela gerenciadora";
        $arrRetorno[13]  = "";
        $arrRetorno[14]  = "Comando enviado mas, recusado pelo equipamento(NAK)";
        $arrRetorno[15]  = "ERRO - ESN nao encontrado.";
        $arrRetorno[255] = "Erro nos parametros";

        $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));

        // socket_connect($socket, _SERVIDOR_COMANDOS_, 8500);    
        socket_connect($socket, $this->servidorComandos, 8500);    

        $sent = socket_write($socket, $executeComando, strlen($executeComando));

        /**
         * GERAÇÃO DO LOG DE COMANDOS
         */
        $logPath = "/tmp/ws_portal_comandos_" . date('Ymd') . ".log";
        $fp = fopen($logPath,"a");
        $row = "DATA: " . date('d/m/Y H:i:s') . "\n". $executeComando;
        $row.="*********************************************************\n\n";
        
        fputs ($fp,$row);
        fclose($fp);
        chmod($logPath, 0755);
        sleep(2);


        if($nonblock === true){
            socket_set_nonblock ($socket);
        }

        $buffer = $this->leSocketComprimentoDados($socket, $max_len);
        socket_close($socket);

        $arrRet = array('codigo' => $buffer, 'descricao' => $arrRetorno[$buffer]);
        return  $arrRet;
    }

    // função utilizada na comandoSocketTesteEquipamento() acima
    private function leSocketComprimentoDados ($socket, $len)
    {
        $offset = 0;
        $socketData = '';

        while ($offset < $len) {
            if (($data = @socket_read ($socket, $len-$offset)) === false) {
                $this->error();
                return false;
            }

            $dataLen = strlen ($data);
            $offset += $dataLen;
            $socketData .= $data;

            if ($dataLen == 0) {
                break;
            }
        }

        return $socketData;
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

<?php

/**
 * logEBS - Conector para o webservice da EBS/FOX
 *
 *
 * @file    ws1_integracao_ebs.php
 * @author  André Timermann - BRQ
 * @since   10/07/2012
 * @version 10/07/2012
 * 
 * Classe responsável por gerenciar os logs da conexão ao Webservice da EBS
 * Exemplo de uso:
 * 
 *   $log = new logEBS();
 *   Deve ser passado o conector logEBS::WS1, logEBS::WS2, logEBS::WS3, logEBS::WS4
 *    $log->conector = logEBS::WS1;
 *
 *    //------- NOVO DETALHAMENTO ----
 *    $det = new logEBSDetalhamento();
 *    $det->tipoNota = logEBSDetalhamento::NOTA_SAIDA;
 *    $det->statusProcessamento = logEBSDetalhamento::SUCESSO;
 *    $det->numeroNotaFiscal = 1234;
 *    $det->seriaNotaFiscal = 5678;
 *    $det->detalhamento = "Nota Fiscal recebida com sucesso";
 *
 *    $log->addDetalhamento($det);
 *    //-------
 *    $det = new logEBSDetalhamento();
 *    $det->tipoNota = logEBSDetalhamento::NOTA_ENTRADA;
 *    $det->statusProcessamento = logEBSDetalhamento::ERRO;
 *    $det->numeroNotaFiscal = 6;
 *    $det->seriaNotaFiscal = 3;
 *    $det->detalhamento = "Erro de validação: Deve ser string";
 *
 *    $log->addDetalhamento($det);
 *
 * */
#############################################################################################################
#   Histórico
#       10/07/2012 - André Timermann (BRQ)
#           Criação do arquivo - DUM 79924
#       05/11/2012 - Diego C. Ribeiro (BR)
#           Alterada a forma como salva o Log, tabela, nome de campo, etc. - STI 80292
#############################################################################################################


require_once "logEBSDetalhamento.class.php";
require_once "credencial.php";
require_once "funcoesEBS.php";

class logEBS {
    // Tipo de Conectores

    const WS1 = 1;
    const WS2 = 2;
    const WS3 = 3;
    const WS4 = 4;
    const WS5 = 5;

    /*
      Conector
     */

    public $conector;

    /*
      Hora do inicio da execução
     */
    public $inicioExecucao;

    /*
      Se ocorreu algum erro
     */
    public $erro = False;

    /*
      Detalhamentos
     */
    protected $detalhamento = array();

    /*
      Descricao
     */
    public $descricao = false;

    /**
     * Número do protocolo recebido
     */
    public $protocolo = false;

    /**
     * Status do protocolo.
     */
    public $erro_protocolo = null;

    /*
     * Armazena o código do erro se houver.
     */
    public $codigo_erro = false;

    /**
     * Indica se o reenvio dos pedidos de venda é necessário
     */
    public $reenvio = null;

    /**
     * Armazena o código dos pedidos que foram enviados
     */
    public $dados_enviados = null;
    public $tipoRecebimento = null;
    public $idLog = null;
    
    /**
     * Armazena o array de erros por protocolo
     * @var array 
     */
    public $arrProtocolosErros = array();   
	
	/**
	 * Define a exibição de erros na tela
	 */
	public $exibirErros = true;
	
	/**
	 * Mensagens de Erros no caso de atualização pela Intranet
	 */
	public $mensagemErro = null;

    /*
     * Construtor
     */

    function __construct() {
        $this->inicioExecucao = date("Y-m-d H:i:s");
    }

    /*
     * Adiciona um detalhamento do LOG
     */
    public function addDetalhamento(logEBSDetalhamento $log) {
        $this->detalhamento[] = $log;
    }

    /**
     * Recupera a data/hora final da execução do conector
     * @return string (datetime)
     */
    public function fimExecucao() {
        return date("Y-m-d H:i:s");
    }

    /**
     * Salva o log dos conectores na Base de dados
     * @param $tipoNota - Utilizado somente pelo conetor WS1(entrada, saida, cancelamento_entrada ou cancelamento_saida)
     */
    public function save($tipoNota = null) {
        if ($this->conector == 1) {
            $this->conector_1($tipoNota);
        } elseif ($this->conector == 2) {
            $this->conector_2();
        } elseif ($this->conector == 3 or $this->conector == 4) {
            $this->conector_3_4();
        }
    }

    /**
     * Salva os LOGs referentes a:
     * - Recebimento de Notas Fiscais de Saída
     * - Recebimento de cancelamento de Notas Fiscais de Saida
     * - Recebimento de Notas Fiscais de Entrada
     * - Recebimento de cancelamento de Notas Fiscais de Entrada
     * 
     * @param strint $tipoNota (entrada, saida, cancelamento_entrada ou cancelamento_saida)
     * 
     * @global type $conn
     * @return bool 
     */
    public function conector_1($tipoNota) {

        global $conn;

        ///////////////////////////////////////////////////////////
        // Cria log (Principal, resumo)
        ///////////////////////////////////////////////////////////
        $query = sprintf("INSERT INTO log_integracao_fox 
                                    (lifconector, 
                                    lifdt_inicio, 
                                    lifdt_fim , 
                                    lif_descricao, 
                                    lifprotocolo)
                VALUES (%d, '%s', '%s', '%s', '%s') RETURNING lifoid;", 
                
                $this->conector, 
                $this->inicioExecucao, 
                $this->inicioExecucao, 
                "", 
                $this->protocolo);        

        $result = pg_query($conn, $query);
        $id = pg_fetch_result($result, 0, "lifoid");
        $this->idLog = $id;



        ///////////////////////////////////////////////////////////
        // Cria Detalhamento do log
        ///////////////////////////////////////////////////////////

        $statusProcessamentoConector = False;

        /*
          ----------------
          DOCUMENTAÇÃO:
          ----------------
          Será criado um Contados de notas atráves de um array:

          O array $notas terá os 4 elementos cujo indice é o tipo do array (entrada, saida, cancelamento_entrada e cancelamento)

          Cada elemento é um array com a lista das notas enviadas e cujo valor é 0 ou 1 dependendo se teve problema ou não

          Por exemplo:
          Temos uma conexão que recebeu:
          - 4 notas de entrada  (2 com erro)
          - 2 notas de saida    (1 com erro)
          - 1 cancelamento de nota de entrada (0 com erro)
          - 0 cancelamento de nota de saida

          o array $notas ficaria:

          $notas = array(logEBSDetalhamento::NOTA_ENTRADA => array(10 => 0, 11 => 0, 12 => 1, 13 =>1),
          logEBSDetalhamento::NOTA_SAIDA => array(14 => 1, 15 => 0),
          logEBSDetalhamento::CANCELAMENTO_NOTA_ENTRADA => array(16 => 1),
          logEBSDetalhamento::CANCELAMENTO_NOTA_SAIDA => array()
          )

          4 notas com numero 10, 11, 12, 13 (12 e 13 com erro) do tipo NOTA_ENTRADA
          2 notas com numero 14 e 15 (15 com erro) do tipo NOTA_SAIDA
          1 nota com numero 16 (sem nenhum erro) do tipo CANCELAMENTO_NOTA_ENTRADA
          nenhuma nota do tipo CANCELAMENTO_NOTA_SAIDA


         */

        $notas = array(
            logEBSDetalhamento::NOTA_ENTRADA => array(),
            logEBSDetalhamento::NOTA_SAIDA => array(),
            logEBSDetalhamento::CANCELAMENTO_NOTA_ENTRADA => array(),
            logEBSDetalhamento::CANCELAMENTO_NOTA_SAIDA => array()
        );

        // Monta o array explicado acima
        foreach ($this->detalhamento AS $det) {

            $statusProcessamentoConector = $det->statusProcessamento;

            // Usado para o contador
            if (!isset($notas[$det->tipoNota][$det->numeroNotaFiscal])) {
                $notas[$det->tipoNota][$det->numeroNotaFiscal] = $det->statusProcessamento;
            } else {
                $notas[$det->tipoNota][$det->numeroNotaFiscal] = $det->statusProcessamento;
            }


            // Detalhamento da Nota
            $query = sprintf("INSERT INTO log_integracao_fox_detalhe (lifdlifoid, lifdtipo_nota, lifdstatus_processamento , lifdnumero_nota_fiscal, lifdserie_nota_fiscal, lifddetalhamento)
                    VALUES (%d, %d, %d, %d, %d,'%s');", $id, $det->tipoNota, $det->statusProcessamento, $det->numeroNotaFiscal, $det->seriaNotaFiscal, str_replace("'", '"', $det->detalhamento));

            $result = pg_query($conn, $query);
        }

        ///////////////////////////////////////////////////////////
        // Atualiza dados na Tabela de log principal
        ///////////////////////////////////////////////////////////
        // Faz contagem das notas atráves do array explicado acima
        $totalNotasErro = null;
        if ($tipoNota == "entrada") {

            $totalNotasSucesso = $this->contarArray($notas[logEBSDetalhamento::NOTA_ENTRADA], 1);
            $totalNotasErro .= $this->contarArray($notas[logEBSDetalhamento::NOTA_ENTRADA], 0);

            $descricaoConector = sprintf("Notas Fiscais de entrada recebidas com sucesso: %d,\n", $totalNotasSucesso);
            $descricaoConector .= sprintf("Notas Fiscais de entrada recebidas com erros: %d,\n", $totalNotasErro);
        } elseif ($tipoNota == "saida") {

            $totalNotasSucesso = $this->contarArray($notas[logEBSDetalhamento::NOTA_SAIDA], 1);
            $totalNotasErro .= $this->contarArray($notas[logEBSDetalhamento::NOTA_SAIDA], 0);

            $descricaoConector = sprintf("Notas Fiscais de saída recebidas com sucesso: %d, \n", $totalNotasSucesso);
            $descricaoConector .= sprintf("Notas Fiscais de saida recebidas com erros: %d\n", $totalNotasErro);
        } elseif ($tipoNota == "cancelamento_entrada") {

            $totalNotasSucesso = $this->contarArray($notas[logEBSDetalhamento::CANCELAMENTO_NOTA_ENTRADA], 1);
            $totalNotasErro .= $this->contarArray($notas[logEBSDetalhamento::CANCELAMENTO_NOTA_ENTRADA], 0);

            $descricaoConector = sprintf("Cancelamento de Notas Fiscais de entrada recebidas com sucesso: %d,\n", $totalNotasSucesso);
            $descricaoConector .= sprintf("Cancelamento de Notas Fiscais de entrada recebidas com erros: %d\n", $totalNotasErro);
        } elseif ($tipoNota == "cancelamento_saida") {

            $totalNotasSucesso = $this->contarArray($notas[logEBSDetalhamento::CANCELAMENTO_NOTA_SAIDA], 1);
            $totalNotasErro .= $this->contarArray($notas[logEBSDetalhamento::CANCELAMENTO_NOTA_SAIDA], 0);

            $descricaoConector = sprintf("Cancelamento de Notas Fiscais de saída recebidas com sucesso: %d, \n", $totalNotasSucesso);
            $descricaoConector .= sprintf("Cancelamento de Notas Fiscais de saida recebidas com erros: %d\n", $totalNotasErro);
        }

        if ($totalNotasErro > 0) {
            $booErro = 'true';
        } else {
            $booErro = 'false';
        }

        try {
            $query = "	UPDATE 
                                log_integracao_fox 
                        SET 
                                lifdt_fim = '" . date("Y-m-d H:i:s") . "', 
                                lif_descricao = '$descricaoConector',
                                lifsituacao_protocolo = 'Processado',
                                liferro_processamento =  '$booErro'
                        WHERE 
                                lifoid = $id";

            $result = pg_query($conn, $query);
            
            
        } catch (Exception $e) {
            echo $e->getMessage();
            exit();
        }
        return true;
    }

    /**
     * Conta a quantidade de valores iguais a variavel $valor_comparacao
     * @param type $array
     * @param type int
     * @return int
     */
    public function contarArray($array, $valor_comparacao) {
        $contador = 0;
        foreach ($array as $a) {
            if ($a == $valor_comparacao) {
                $contador++;
            }
        }
        return $contador;
    }

    /**
     * Salva os Logs referentes ao envio dos Produtos do Conector 2
     * @global type $conn 
     */
    public function conector_2() {

        global $conn;
        // print '<pre>'; print_r($arrItens); print '</pre>';exit();
        ///////////////////////////////////////////////////////////
        // Cria log (Principal, resumo)
        ///////////////////////////////////////////////////////////
        try {

            pg_query($conn, "BEGIN");

            // Para cada envio ao WS gera um Protocolo (referente a todos os pedidos enviados)
            $query = sprintf("INSERT INTO log_integracao_fox 
                                    (lifconector, 
                                    lifdt_inicio, 
                                    lifdt_fim, 
                                    lif_descricao,
                                    lifprotocolo)                            
                            VALUES (%d, '%s', '%s', '%s','%s')
                            RETURNING lifoid", 
                    
                            $this->conector, 
                            $this->inicioExecucao, 
                            $this->fimExecucao(), 
                            $this->descricao, 
                            $this->protocolo);
            
            $result = pg_query($conn, $query);
            $lifoid = pg_fetch_result($result, 0, 'lifoid');
            $valueTemp = explode(',', $this->dados_enviados);

            // Para cada Pedido que foi enviado, é inserido um registro na tabela log_integracao_fox_dados_enviados
            foreach ($valueTemp as $value) {
                $query = "  INSERT INTO log_integracao_fox_dados_enviados
                            (lifdelifoid, 
                            lifdecodigo_enviado, 
                            lifdetipo)
                        VALUES ($lifoid, '$value', 'PE');";
                pg_query($conn, $query);
            }

            pg_query($conn, "COMMIT");
        } catch (Exception $e) {
            echo "Exceção LOG Conector_2 ", $e->getMessage(), "\n";
            exit();
        }
    }

    /**
     * Salva os Logs referentes ao Envio dos conectores 3 e 4
     * @return boolean 
     */
    public function conector_3_4() {

        global $conn;

        ///////////////////////////////////////////////////////////
        // Cria log (Principal, resumo)
        ///////////////////////////////////////////////////////////
        try {
            $query = sprintf("INSERT INTO log_integracao_fox 
                            (lifconector, 
                            lifdt_inicio, 
                            lifdt_fim , 
                            lif_descricao,
                            lifprotocolo)
                    VALUES (%d, '%s', '%s', '%s','%s')
                    RETURNING lifoid", 
                    
                    $this->conector, 
                    $this->inicioExecucao, 
                    $this->fimExecucao(), 
                    $this->descricao, 
                    $this->protocolo);

            $result = pg_query($conn, $query);
            $lifoid = pg_fetch_result($result, 0, 'lifoid');

            // Para cada envio, é inserido um registro na tabela log_integracao_fox_dados_enviados
            if (isset($this->dados_enviados) and is_array($this->dados_enviados) and count($this->dados_enviados) > 0) {
                foreach ($this->dados_enviados as $codigo => $tipo) {
                    $query = "  INSERT INTO log_integracao_fox_dados_enviados
                                    (lifdelifoid, 
                                    lifdecodigo_enviado, 
                                    lifdetipo)
                                VALUES ($lifoid, '$codigo', '$tipo');";
                    pg_query($conn, $query);
                }
            }
        } catch (Exception $e) {
            echo "Exceção LOG Conector_3_4: ", $e->getMessage(), "\n";
            exit();
        }
    }

    /**
     * Consulta os dados do Protocolo
     * @global type $conn
     * @param type $protocolo
     * @return boolean 
     */
    public function consultarDadosProtocolo($protocolo = false, $id = false) {

        global $conn;

        $query = "SELECT 
                        lifoid,
                        lifconector,
                        lifdt_inicio,
                        lifdt_fim,
                        lif_descricao,
                        lifprotocolo,
                        lifreenvio,
                        lifsituacao_protocolo,
                        liferro_processamento,
                        (SELECT array_to_string(array(SELECT lifdecodigo_enviado from log_integracao_fox_dados_enviados WHERE lifdelifoid = lifoid), ',') ) as lifdecodigo_enviado
                FROM log_integracao_fox 
                WHERE lifconector = '$this->conector' ";

        if ($protocolo !== false) {
            $query .= " AND lifprotocolo = '$protocolo';";
        } elseif ($id !== false) {
            $query .= " AND lifoid = '$id';";
        } else {
            return false;
        }

        try {
            $result = pg_query($conn, $query);
            if (pg_num_rows($result) > 0) {
                $arrDados = array();
                $arrDados = pg_fetch_assoc($result);
                return $arrDados;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo "consultarDadosProtocolo: " . $e->getMessage() . "\n";
            exit();
        }
    }

    /**
     * Função utilizada para atualizar o Status do Protocolo enviado
     * 
     * @global type $conn
     * @param string $status - Houve erro no Protocolo? True, False
     * @param string $protocolo
     * @param array $arrErros  - Array de erros recebidos do canal de integração
     */
    // public function atualizarStatusProtocolo($status = null, $protocolo, $arrErros = null, $situacaoProtocolo){
    public function atualizarStatusProtocolo($protocolo, $situacaoProtocolo, $status = null, $arrObjetoErros = null) {

        global $conn;

        pg_query($conn, "BEGIN");

        // Atualiza o status do protocolo com TRUE or FALSE
        try {

            $query = " UPDATE log_integracao_fox 
                       SET 
                            lifsituacao_protocolo = '$situacaoProtocolo'";

            if ($status != null) {
                $query .= ", liferro_processamento = '$status'";
            }

            $query .= " WHERE lifprotocolo = '$protocolo'";
            $result = pg_query($conn, $query);
        } catch (Exception $e) {
            echo "atualizaStatusProtocolo: " . $e->getMessage() . "\n";
            pg_query($conn, "ROLLBACK");
            return false;
        }        

        // Insere os erros do protocolo se houverem       
        if (!empty($arrObjetoErros)) {            

            try {
                $query = "	SELECT lifoid
			       			FROM log_integracao_fox
			       			WHERE lifprotocolo = '$protocolo'";   
                $result = pg_query($conn, $query);
                $lifoid = pg_fetch_result($result, 0, "lifoid");
                
            } catch (Exception $e) {
                echo "atualizaStatusProtocolo(), Inserir Log de erro  " . $e->getMessage() . "\n";
                pg_query($conn, "ROLLBACK");
                return false;
            }

            try {
                // Verifica se o retorno é apenas um objeto de Erro
                if (is_object($arrObjetoErros->Erro)) {

                    try {

                        if (isset($arrObjetoErros->Erro->Codigo)) {
                            $query = sprintf("INSERT INTO log_integracao_fox_erro_protocolo
                                         (lifelifoid, lifecodigo_erro, lifedocumento, lifemensagem)
                                         VALUES (%d,'%s','%s','%s')", $lifoid, $arrObjetoErros->Erro->Codigo, $arrObjetoErros->Erro->DocumentoOrigem, utf8_decode(addslashes($arrObjetoErros->Erro->Mensagem)));
                            $result = pg_query($conn, $query);
                        }
                    } catch (Exception $e) {
                        echo "atualizaStatusProtocolo(), inserir objeto Erro " . $e->getMessage() . "\n";
                        pg_query($conn, "ROLLBACK");
                        return false;
                    }

                // Verifica se o retorno é um array de objetos de Erro                
                } elseif (is_array($arrObjetoErros->Erro)) {

                    // Percorre o array de objetos
                    foreach ($arrObjetoErros->Erro as $Erro) {

                        // Salva os Erros gerado por cada protocolo
                        try {

                            if (isset($Erro->Codigo)) {
                                $query = sprintf("INSERT INTO log_integracao_fox_erro_protocolo
                                                 (lifelifoid, lifecodigo_erro, lifedocumento, lifemensagem)
                                                 VALUES (%d,'%s','%s','%s')", $lifoid, $Erro->Codigo, $Erro->DocumentoOrigem, utf8_decode(addslashes($Erro->Mensagem)));
                                $result = pg_query($conn, $query);
                            }
                        } catch (Exception $e) {
                            echo "atualizaStatusProtocolo(), inserir array de erros: " . $e->getMessage() . "\n";
                            pg_query($conn, "ROLLBACK");
                            return false;
                        }
                    }
                } else {
                    throw new Exception("atualizaStatusProtocolo(), Houve um erro ao indentificar o tipo de retorno dos Erros.");
                }
            } catch (Exception $exc) {
                echo $exc->getMessage();
                pg_query($conn, "ROLLBACK");
                return false;
            }
        }                    
        pg_query($conn, "COMMIT");
        return true;
    }

    /**
     * Atualiza campo 'lifreenvio' como TRUE para que esse protocolo não seja enviado novamente.
     * @global type $conn
     * @param type $arrProtocolosReenviar 
     */
    public function atualizarProtocoloReenvio($arrProtocolosReenviar) {

        global $conn;

        foreach ($arrProtocolosReenviar as $arrRow) {

            try {
                $query = " UPDATE log_integracao_fox 
                            SET lifreenvio = 'true' 
                            WHERE lifconector = '$this->conector'
                                AND lifprotocolo = '" . $arrRow['lifprotocolo'] . "'";
                pg_query($conn, $query);
            } catch (Exception $e) {
                echo "atualizarProtocoloReenvio: " . $e->getMessage() . "\n";
                exit();
            }
        }
    }

    /**
     *
     * Função utilizada para consultar o Status dos protocolos na Base da SASCAR,
     * se o campo lifsituacao_protocolo  estiver vazio, indica que devemos consultar no
     * canal de integração o seu status.
     * @global type $conn
     * @return array de protocolos a consultar
     */
    public function consultarStatusProtocolo() {

        global $conn;
        
        if(empty($this->conector)){
            $conector = "1','2','3','4";
        }else{
            $conector = $this->conector;
        }

        $query = " 	SELECT * FROM log_integracao_fox 
						WHERE lifconector IN ('$conector') AND 
						(lifsituacao_protocolo != 'Processado' OR  lifsituacao_protocolo IS NULL)
						AND lifprotocolo IS NOT NULL AND TRIM(lifprotocolo) != ''
						ORDER BY lifoid DESC"; 
        
        try {
            $result = pg_query($conn, $query);
            if (pg_num_rows($result) > 0) {
                $arrDados = array();
                while ($row = pg_fetch_assoc($result)) {
                    $arrDados[] = $row['lifprotocolo'];
                }
                return $arrDados;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo "consultaProtocolo: " . $e->getMessage() . "\n";
            exit();
        }
    }

    /**
     * Consulta os detalhes do conector ws1, filtra por um determinado protocolo ou id do log
     * @global type $conn
     * @param string $protocolo
     * @param int $id
     * @return array
     */
    public function consultarDetalhesProtocolo($protocolo = false, $id = false) {

        global $conn;

        $query = "SELECT lifdnumero_nota_fiscal, lifdserie_nota_fiscal, lifddetalhamento
                FROM log_integracao_fox AS log
                INNER JOIN log_integracao_fox_detalhe AS det ON log.lifoid = det.lifdlifoid
                WHERE lifconector = '1' ";

        if ($protocolo !== false) {
            $query .= " AND lifprotocolo = '$protocolo';";
        } elseif ($id !== false) {
            $query .= " AND lifoid = '$id';";
        }

        try {
            $result = pg_query($conn, $query);
            $arrDados = array();
            if (pg_num_rows($result) > 0) {                
                while ($row = pg_fetch_assoc($result)) {
                    $arrDados[] = $row;
                }
                
            } 
            return $arrDados;
        } catch (Exception $e) {
            echo "consultarDetalhesProtocolo: " . $e->getMessage() . "\n";
            exit();
        }
    }
    
    /**
     * Consulta o status dos protocolos de processamento pendentes na intranet,
     * depois consulta os protocolos encontrados no Sistema FOX e atualiza-os
     * intranet
     * @global type $client
     * @return boolean
     */
    public function consultarAtualizarProtocolosEnviados() {
    
    	// Retorna um array de protocolos a consultar no canal de integração
    	$arrProtocosConsultar = $this->consultarStatusProtocolo();
    	$arrProtocolosErros = null;        
		$this->mensagemErro = null;
        
    	// Consulta os Protocolos, atualiza o status se foi processado ou obteve erros
    	if ($arrProtocosConsultar !== false and !empty($arrProtocosConsultar)) {    
            
            foreach ($arrProtocosConsultar as $key => $protocolo) {

                if ($protocolo == '') {
                        unset($arrProtocosConsultar[$key]);
                        continue;
                }
                try {
                        global $client;
                        $req = req();
                        $req->protocolo = $protocolo;
                        $ret = $client->ConsultarProtocolo($req);    				    			

                        // Consulta o Status do protocolo
                        $protocoloFox = $ret->ConsultarProtocoloResult;

                        // print '<pre>'; print_r($protocoloFox); print '</pre>'; exit();
                        // Se a situação do Protocolo não for Processado, atualiza com a situação recebida
                        if ($protocoloFox->SituacaoProcessamento != 'Processado') {
                                $this->atualizarStatusProtocolo($protocolo, $protocoloFox->SituacaoProcessamento);
                                continue;

                        // Protocolo igual a 'Processado'
                        } else {

                                /**
                                 * Se o resultado for 'Sucesso', o conector deverá atualizar o campo
                                 * "liferro_processamento" igual a false, e deverá avançar para o próximo
                                 * protocolo.
                                 */
                                if ($protocoloFox->ResultadoProcessamento == 'Sucesso') {

                                        $this->atualizarStatusProtocolo( $protocolo, $protocoloFox->SituacaoProcessamento, 'false');
                                        continue;

                                        /**
                                         * Caso o resultado seja 'Erro':
                                         *   - gravar no campo 'liferro_processamento' o valor TRUE,
                                         *   - gravar no campo 'lifcodigo_erro' o erro retornado pelo canal de integração
                                         */
                                } else { // Status ERRO
                                        if (isset($protocoloFox->Erros) and !empty($protocoloFox->Erros)) {
                                                // Atualiza o Status do Procolo e insere os erros gerados se houverem
                                                $this->atualizarStatusProtocolo($protocolo, $protocoloFox->SituacaoProcessamento, 'true', $protocoloFox->Erros);
                                                $arrProtocolosErros[$protocolo] = $protocoloFox->Erros;
                                        }
                                }
                        }
                } catch (SoapFault $fault) {
				/*
					if($this->exibirErros == true){
                        echo '<br>SoapFault3\n';
                        echo '<br>Code: ' . $fault->faultcode . '\n';
                        echo '<br>String: ' . $fault->faultstring . '\n';
					}else {
						$this->mensagemErro .= "<br>Protocolo $protocolo com erro no processamento: $fault->faultstring";						
					}*/
                }
            }
    	}
       
        $this->arrProtocolosErros = utf8Decode_recursivo($arrProtocolosErros);        
        
    	if(isset($arrProtocosConsultar) and is_array($arrProtocosConsultar) and count($arrProtocosConsultar)>0){
            return true;
        }else{
            return false;
        }
    }
}
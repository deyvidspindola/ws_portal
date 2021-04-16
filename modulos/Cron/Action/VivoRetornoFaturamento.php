<?php

/**
 * Classe para persistência de dados deste modulo
 *
 *  @package RetornoFaturamento
 */

require_once _MODULEDIR_ . 'Cron/DAO/VivoRetornoFaturamentoDAO.php';

/**
 * Classe responsável pelo retorno para  a Intranet de informações de faturamento atualizadas pela Vivo
 *
 *  @package RetornoFaturamento
 *  @author  Angelo Frizzo Junior <angelo.frizzo@meta.com.br>
 *  @since   04/10/2013
 */

class RetornoFaturamento {

    /**
    * Objeto DAO.
    *
    * @var stdClass
    */
    private $dao;

    /**
     * Mensagens de erro
     */
    const M1 =  'Arquivo não possui a estrutura correta.';
    const M2 =	'Subscription Id [subscription] não encontrado.';
    const M3 =	'Mês/Ano Parcela [parcela] para o Subscription Id [subscription] não foi localizada.';
    const M4 =	'Não localizado item de nota fiscal para o lote [registro_lote] e nome do arquivo [nome_arquivo_envio], informados.';

    /**
    * Busca os arquivos abaixo no diretório
    * caso existam todos os arquivos nesse diretório deve iniciar a atualização.
    * Todos os arquivos devem ser do tipo NE ou RE e iniciados por RSEG.
    *
    *  @return string
    */
    public function importarArquivosServidor() {

        $caminho = '/vivo/entrada';

        $caminhoDestino = "/var/www/ARQUIVO_MORTO_ITENS_FATURADOS_PARCEIRO";

        if (is_dir($caminho)) {

            $diretorio = dir($caminho);

            $existeArquivos = scandir($caminho);

            //verifica se existem arquivos na pasta
            if (count($existeArquivos) > 2) {

                //lendo os arquivos do diretório
                while ($arquivo = $diretorio->read()) {

                    //recupera a extensão do arquivo em letras maiusculas
                    $extensao = strtoupper(end(explode(".", $arquivo)));

                    //verificar o tipo de arquivo
                    if ((($extensao == 'NE')||($extensao == 'RE')) && (strripos($arquivo, "RSEG.011") !== false)) {

                        echo "<br><br>ARQUIVO LOCALIZADO:".$arquivo;echo"<br>";

                        //RN 004 - Validar Layout
                        $layout = $this->validarLayout($caminho."/".$arquivo);

                        if ($layout) {

                            $ponteiro = fopen($caminho."/".$arquivo, "r");

                            $terminou = 0;

                            $evento = array();

                            $titulo = array();

                            while ($terminou == 0) {

                                echo "<br><br>LINHA ATUAL: ".$linha = fgets($ponteiro, 4096)."<br>";

                                if ((trim($linha) != "") && ($terminou == 0)) {

                                    $codregistro = (int)trim(substr($linha, 0, 1));

                                    //RN 011 - verifica condições para processar linha (Não deve ser Rodapé e deve ser Detalhe / Linha)
                                    if ($codregistro != 9) {

                                        if ($codregistro == 1) {

                                            $subscriptionId = trim(substr($linha, 4, 21));

                                            // RN 007 - verifica se Subscription Id (campo Assinante) informado está cadastrado na base
                                            $subscriptionIdValido = $this->dao->verificarSubscriptionId($subscriptionId);

                                            if ($subscriptionIdValido) {

                                                // RN 008 - verifica Parcela cadastrada
                                                $dataReferenciaParcela = trim(substr($linha, 75, 6));

                                                $arrayTitulo = $this->dao->buscarParcelaCadastrada($subscriptionId, $dataReferenciaParcela);

                                                $numeroParcela = $arrayTitulo['titno_parcela'];

                                                $titoid = $arrayTitulo['titoid'];

                                                $nfloid = $arrayTitulo['nfloid'];

                                                $arquivoRemessa = trim(substr($linha, 81, 35));

                                                $lote = intval( trim( substr($linha, 116, 7) ) );

                                                $nfioid = $this->dao->buscarIdItemNotaFiscal($lote, $arquivoRemessa);

                                                if ($nfioid == 0) {
                                                    //gera Log de processamento
                                                    $msgLog = str_replace("[registro_lote]", $lote, self::M4);

                                                    $msgLog = str_replace("[nome_arquivo_envio]", $arquivoRemessa, $msgLog);

                                                    $this->gravarLog($arquivo, $msgLog);

                                                } else if (trim($numeroParcela) != "") {

                                                    // abre a transação
                                                    $this->dao->begin();

                                                    // atribui variavéis utilizadas mais de uma vez abaixo
                                                    $status 		= trim(substr($linha, 25, 1));
                                                    $dataVencimento = trim(substr($linha, 57, 8));
                                                    $ciclo 			= ($status == 'F') ? (int)trim(substr($linha, 73, 2)) : "NULL";

                                                    // grava evento na tabela veiculo parceiro
                                                    $evento = array(
                                                        'subscription_id' => $subscriptionId,
                                                        'status' => $status,
                                                        'codigo_motivo' => trim(substr($linha, 26, 3)),
                                                        'data_evento' => $this->preparaDataBanco(trim(substr($linha, 29, 8))),
                                                        'valor_liquido' => $this->preparaValorBanco(trim(substr($linha, 37, 10))),
                                                        'valor_bruto' => $this->preparaValorBanco(trim(substr($linha, 47, 10))),
                                                        'data_vencimento' => $this->preparaDataBanco($dataVencimento),
                                                        'data_emissao' => $this->preparaDataBanco(trim(substr($linha, 65, 8))),
                                                        'ciclo_faturamento' => $ciclo,
                                                        'numero_parcela' => $numeroParcela,
                                                        'arquivo_remessa' => $arquivoRemessa,
                                                        'arquivo_retorno' => $arquivo,
                                                        'lote' => $lote,
                                                        'nfioid' => $nfioid
                                                    );

                                                    if ($this->dao->gravarEvento($evento) ) {

                                                        if (($status == "Y") || ($status == "F")) {

                                                            $this->dao->atualizarDataVencimentoTitulo($titoid, $this->preparaDataBanco($dataVencimento));

                                                            $this->dao->atualizarDataVencimentoNotaFiscal($nfloid, $this->preparaDataBanco($dataVencimento));

                                                            if ($status == "F") {
                                                            	
                                                            	$this->dao->atualizarCicloVeiculoParceiro($subscriptionId, $ciclo);
                                                            
                                                            }
                                                        }

                                                    } else {

                                                        throw new Exception('Erro na gravação dos dados na tabela veiculo_parceiro_evento');

                                                    }

                                                    //Comita a transação
                                                    $this->dao->commit();

                                                } else {

                                                    //gera Log de processamento
                                                    $msgLog = str_replace("[subscription]", $subscriptionId, self::M3);

                                                    $msgLog = str_replace("[parcela]", $dataReferenciaParcela, $msgLog);

                                                    $this->gravarLog($arquivo, $msgLog);

                                                }

                                            } else {

                                                //gera Log de processamento
                                                $msgLog = str_replace("[subscription]", $subscriptionId, self::M2);

                                                $this->gravarLog($arquivo, $msgLog);

                                            }

                                        }

                                    } else {


                                        //move o arquivo para a pasta arquivo morto
                                        fflush($ponteiro);

                                        fclose($ponteiro);

                                        if (is_resource($ponteiro)) {

                                            fclose($ponteiro);

                                        }

                                        sleep(1);

                                        rename($caminho."/".$arquivo, $caminhoDestino."/".$arquivo);

                                        echo "<br>Rodapé do Arquivo Identificado. Movido para: ".$caminhoDestino."/".$arquivo."<br/>";

                                        // finaliza processamento do arquivo
                                        $terminou = 1;

                                    }
                                }

                            }

                        } else {

                            //gera Log de processamento
                            $this->gravarLog($arquivo, self::M1);

                            //move o arquivo para a pasta arquivo morto
                            rename($caminho."/".$arquivo, $caminhoDestino."/".$arquivo);

                            echo "<br>Erro Validação Layout. Movido para: ".$caminhoDestino."/".$arquivo."<br/>";

                        }

                    } else {

                        if ((trim($arquivo) != '.')&&(trim($arquivo) != '..')) {

                            //move o arquivo para a pasta arquivo morto
                            rename($caminho."/".$arquivo, $caminhoDestino."/".$arquivo);

                            echo "<br>Nomenclatura Arquivo Inválida. Movido para: ".$caminhoDestino."/".$arquivo."<br/>";

                        }

                    }

                }

            } else {

                throw new Exception('Diretório está vazio.');

            }

        } else {

            throw new Exception('Diretório não existe.');

        }

        return true;

    }

    /**
     * Validar Layout Arquivo conforme RN004
     *
     * @param str $caminho => Caminho do arquivo.
     *
     * @return boolean
     */
    public function validarLayout($caminho) {

        $ponteiro = fopen($caminho, "r");

        $contadorGeral = 0;

        $contadorDetalhes = 0;

        while (!feof($ponteiro)) {

            $linha = fgets($ponteiro, 4096);

            $codRegistro = (int)trim(substr($linha, 0, 1));

            if (trim($linha) != "") {

                // RN004 - item 1
                if (($contadorGeral == 0) && ($codRegistro != 0)) {

                    fclose($ponteiro);

                    return false;

                }

                if ($contadorGeral > 0) {

                    //RN004 - item 2
                    if ($codRegistro == 1) {

                        $contadorDetalhes++;

                    } else {

                        if ($codRegistro == 9) {

                            $qtdeRegistros = (int)trim(substr($linha, 1, 7));

                            if ($qtdeRegistros != $contadorDetalhes) {

                                fclose($ponteiro);

                                return false;

                            }

                        } else {

                            fclose($ponteiro);

                            return false;

                        }

                    }

                }

            }

            $contadorGeral++;
        }

        fclose($ponteiro);

        return true;

    }

    /**
     * Grava log de sucesso e erro
     *
     * @param int $arquivo  => Nome do arquivo TXT.
     * @param int $mensagem => Descrição do motivo de histórico
     *
     * @return void
     */
    public function gravarLog($arquivo, $mensagem) {

        $this->dao->gravarLog($arquivo, $mensagem);

    }

    /**
     * Prepara data para gravar no Bancod e dados (YYYYMMAA para YYYY-MM-AA)
     *
     * @param str $data => Data a ser preparada para o BD.
     *
     * @return string
     */
    private function preparaDataBanco ($data) {

        if ($data) {
            $ano = substr($data, 0, 4);

            $mes = str_pad(substr($data, 4, 2), "0", STR_PAD_LEFT);

            $dia = str_pad(substr($data, 6, 2), "0", STR_PAD_LEFT);

            return $ano."-".$mes."-".$dia;

        } else {

            return null;

        }
    }
    
    /**
     * Prepara valor para gravar no Banco de dados (1234567 para 12.34567)
     *
     * @param str $valor => Valor a ser preparado para o BD.
     *
     * @return real
     */
    private function preparaValorBanco ($valor) {
    
    	if ($valor) {
    		$valor = $valor/100000;
    
    		return $valor;
    
    	} else {
    
    		return null;
    
    	}
    }


    /**
     * Metodo Construtor
     */
    public function __construct() {

        $this->dao = new RetornoFaturamentoDAO();

    }

}
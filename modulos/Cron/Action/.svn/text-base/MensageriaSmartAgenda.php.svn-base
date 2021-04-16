<?php
/**
 * Classe que realiza manutencao da mensageria do OFSC
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/mensageria_smart_agenda_'.date('d-m-Y').'.txt');

require_once _MODULEDIR_ . 'Cron/DAO/MensageriaSmartAgendaDAO.php';
require_once _MODULEDIR_ . 'SmartAgenda/Action/EstoqueAgenda.php';
require_once _MODULEDIR_ . 'SmartAgenda/Action/ComunicacaoEmailsSMS.php';
require_once _MODULEDIR_ . 'SmartAgenda/Action/Outbound.php';
require_once _MODULEDIR_ . 'SmartAgenda/Action/Activity.php';
require_once _MODULEDIR_ . 'SmartAgenda/Action/Contrato.php';
require_once _MODULEDIR_ . "SmartAgenda/Action/Agenda.php";
require_once _MODULEDIR_ . 'SmartAgenda/Action/OrdemServico.php';
require_once _MODULEDIR_ . 'SmartAgenda/Action/ControleConsumo.php';

class MensageriaSmartAgenda {

    const NO_SHOW_CLIENTE = '12';
    const TIPO_ARQUIVO_ASSINATURA = 1;

    private $dao;
    private $nomeRotina;
    private $nomeArquivoLog;
    private $diretorioLog;
    private $erro;
    private $statusMensageria;
    private $regrasContexto;
    private $idUsuario;
    private $outBound;
    private $diretorioImgAssinatura;
    private $agendaClass;
    private $ordemServicoClass;
    private $statusOFSC = array();
    private $conn;


    public function __construct($conn, $nomeRotina = '', $regrasContexto = null) {

        $this->conn                   = $conn;
        $this->dao                    = new MensageriaSmartAgendaDAO($conn);
        $this->outBound               = new Outbound();
        $this->agendaClass            = new Agenda($conn);
        $this->ordemServicoClass      = new OrdemServico($conn);
        $this->nomeRotina             = $nomeRotina;
        $this->nomeArquivoLog         = 'log_erro_cron_smartagenda_'.date('Ymd').'.txt';
        $this->diretorioLog           = '/var/www/docs_temporario/';
        $this->diretorioImgAssinatura = '/var/www/arquivos_intranet/ordem_servico/assinatura/';
        $this->erro                   = '';
        $this->statusMensageria       = array(
                                            'AGUARDANDO'  => 1,
                                            'PROCESSANDO' => 2,
                                            'PROCESSADO'  => 3,
                                            'ABORTADO'    => 4,
                                            'ERRO'        => 5
                                            );
        $this->regrasContexto         = $regrasContexto;
        $this->idUsuario              = $this->dao->buscarDadosUsuario(
                                            array(
                                                "ds_login"    => array("value" => 'SMART.AGENDA', "condition" => "     = "),
                                                "dt_exclusao" => array("value" => 'NULL', "condition"         => "IS")
                                            ) );

    }

    private function getDiretorioImagemAssinatura() {
        return $this->diretorioImgAssinatura;
    }

    private function getNomeArquivoLog() {
        return $this->nomeArquivoLog;
    }

    private function getDiretorioLog() {
        return $this->diretorioLog;
    }

    private function getErro() {
        return $this->erro;
    }

    private function setErro($strErro) {
        $this->erro = $strErro;
    }

    private function getNomeRotina() {
        return $this->nomeRotina;
    }

    private function getUsuarioSmartAgenda() {

        if( !isset($this->idUsuario) || empty($this->idUsuario) ){
            $this->idUsuario = 2750;
        }

        return $this->idUsuario;
    }

    private function getDataRemocao($quantidadeDias) {
        $time = '-' . (int) $quantidadeDias . ' day';
        return date('Y-m-d', strtotime($time, strtotime('now')));
    }

    private function validaQuantidadeDias($dias) {

        if(!is_numeric($dias) || (int) $dias < 0) {
            return false;
        }

        return (int) $dias;
    }

    private function getStatusMensageria($status) {
        return isset($this->statusMensageria[$status]) ? $this->statusMensageria[$status] : null;
    }

    private function getRegrasContexto($contexto) {
        return isset($this->regrasContexto[$contexto]) ? $this->regrasContexto[$contexto] : null;
    }

    private function empilhaMensagemProcessada($smoid,$smid_ofsc) {
        $dadosEnvio = array(
            'message_id'    => $smid_ofsc,
            'external_id'   => $smoid,
            'status'        => 'delivered',
            'description'   => 'Mensagem processada com sucesso'
        );

        $this->statusOFSC[] = $dadosEnvio;
    }

    private function getMensagensProcessadas() {
        return $this->statusOFSC;
    }

    public function limparFila() {

        try {

            $this->dao->begin();

            // Busca processos com erro na fila
            $mensagens = $this->dao->buscaLogsErro();

            if(isset($mensagens->erro)) {
                $this->gravaLogErro($mensagens);
                throw new Exception($mensagens->erro);
            }

            if($this->gravaBacklog($mensagens) === false) {
                throw new Exception("Erro ao gravar os registros de backlog.");
            }

            if($this->excluiFilaBacklog() === false) {
                throw new Exception("Erro ao excluir registros da fila/backlog");
            }

            $this->dao->commit();

        } catch(Exception $e) {
            $this->dao->rollback();
            echo $e->getMessage() . "\n";
        }
    }

    private function gravaBacklog($mensagens) {

        $retorno = true;

        try {

            // Percorre cada mensagem de erro e grava no backlog
            while ($mensagem = pg_fetch_object($mensagens->resultado)) {

                $rsBacklog = $this->dao->gravaMensageriaBacklog(
                    $mensagem->smsmcoid,
                    $mensagem->smsmsoid,
                    $mensagem->smid_ofsc,
                    $mensagem->smprioridade,
                    $mensagem->smdt_cadastro
                );

                if(isset($rsBacklog->erro)) {
                    $this->gravaLogErro($rsBacklog);
                    throw new Exception($rsBacklog->erro);
                }

                // Busca propriedades da mensagem
                $propriedades = $this->dao->buscaPropriedadesMensageria($mensagem->smoid);

                if(isset($propriedades->erro)) {
                    $this->gravaLogErro($propriedades);
                    throw new Exception($propriedades->erro);
                }

                // Grava propriedades da mensageria no backlog
                while($propriedade = pg_fetch_object($propriedades->resultado)) {

                    $rsPropriedade = $this->dao->gravaMensageriaPropriedadesBacklog(
                        $rsBacklog->insert_id,
                        $propriedade->smpchave,
                        $propriedade->smpvalor
                    );

                    if(isset($rsPropriedade->erro)) {
                        $this->gravaLogErro($rsPropriedade);
                        throw new Exception($rsPropriedade->erro);
                    }
                }
            }

        } catch(Exception $e) {
            $retorno = false;
            echo $e->getMessage() . "\n";
        }

        return $retorno;
    }

    private function excluiFilaBacklog() {

        $retorno = true;

        try {
            // Pega quantidade de dias parametrizados
            $dias = $this->dao->quantidadeDias();

            if(isset($dias->erro)) {
                $this->gravaLogErro($dias);
                throw new Exception($dias->erro);
            }

            // Verifica se a quantidade de dias é válida
            $qtdDias = $this->validaQuantidadeDias($dias->resultado->qtd);

            if($qtdDias === false) {
                $msgErro = "Quantidade de dias invalida: " . (string) $dias->resultado->qtd;
                $this->setErro($msgErro);
                $this->gravaLogErro($dias->sql);
                throw new Exception($msgErro);
            }

            // Exclui processos obsoletos na fila
            $rsExclusaoFila = $this->dao->excluiFilaObsoleta();

            if(isset($rsExclusaoFila->erro)) {
                $this->gravaLogErro($rsExclusaoFila);
                throw new Exception($rsExclusaoFila->erro);
            }

            // Exclui registros antigos de backlog
            $rsExclusaoBacklog = $this->dao->excluiRegistrosBacklog($this->getDataRemocao($qtdDias));

            if(isset($rsExclusaoBacklog->erro)) {
                $this->gravaLogErro($rsExclusaoBacklog);
                throw new Exception($rsExclusaoBacklog->erro);
            }

        } catch(Exception $e) {
            $retorno = false;
            echo $e->getMessage() . "\n";
        }

        return $retorno;
    }

    public function executarFila() {

        try {

            $this->dao->begin();
                $dadosFila = $this->dao->liberarFila();
            $this->dao->commit();

            if(isset($dadosFila->erro)) {
                $this->gravaLogErro($dadosFila);
                throw new Exception($dadosFila->erro);
            }

            // Pega mensagens na fila com o status "aguardando"
            $dadosFila = $this->dao->buscaDadosFila(
                array("smsmsoid" => array("value" => $this->getStatusMensageria('AGUARDANDO'), "condition" => "="),
                      "smnumero_tentativas" => array("value" => "5", "condition" => "<")),
                " smprioridade DESC, smoid DESC "
            );

            if(isset($dadosFila->erro)) {
                $this->gravaLogErro($dadosFila);
                throw new Exception($dadosFila->erro);
            }

            if(!empty($dadosFila->resultado)) {

                $this->dao->begin();

                    // Altera o status da mensagem para "processando" para cada mensagem
                    $dadosUpdate = array('smsmsoid' => $this->getStatusMensageria('PROCESSANDO'));
                    $dadosRegistro = array('smoid' => array( "value" => $dadosFila->ids_fila, "condition" => "IN"));
                    $resUpdate = $this->dao->atualizaMensagem($dadosUpdate,$dadosRegistro);

                    if(isset($resUpdate->erro)) {
                        $this->gravaLogErro($resUpdate);
                        throw new Exception($resUpdate->erro);
                    }

                $this->dao->commit();

                foreach ($dadosFila->resultado as $chave => $msg) {

                    $resMensagens = $this->processaMensagem($msg);

                    if(!empty($msg->smid_ofsc)){
                        $this->empilhaMensagemProcessada($msg->smoid,$msg->smid_ofsc);
                    }
                }

            }

        } catch (Exception $e) {
            echo "\n".$e->getMessage()."\n";
            $dadosLog = new stdClass();
            $dadosLog->erro = $e->getMessage();
            $dadosLog->sql = 'desconhecida';
            $this->gravaLogErro($dadosLog);
        }

        $this->enviaStatusMensagemLoteOracle($this->getMensagensProcessadas());
    }

    private function processaMensagem($msg) {

        $dadosRegistro = array('smoid' => array( "value" => $msg->smoid, "condition" => "="));

        try{

            $this->dao->begin();

                // Busca contexto da mensagem
                $resContexto = $this->dao->buscaContexto(
                    array("smcoid" => array("value" => $msg->smsmcoid, "condition" => "="))
                );

                if(isset($resContexto->erro)) {
                    $this->gravaLogErro($resContexto);
                    throw new Exception($resContexto->erro);
                }

                // Caso o contexto esteja ativo
                if(isset($resContexto->resultado->smcativo) && $resContexto->resultado->smcativo == 't') {
                    // Processa o contexto
                    $resProcessamento = $this->processaContexto($resContexto->resultado->smcdescricao,$msg->smoid,$msg->smid_ofsc);

                    if(isset($resProcessamento->erro)) {
                        $this->gravaLogErro($resProcessamento);
                        throw new Exception($resProcessamento->erro);
                    }
                }

                // Altera status para processado.
                $dadosUpdate =  array('smsmsoid' => $this->getStatusMensageria('PROCESSADO'));

                $resStatusProcessado = $this->dao->atualizaMensagem($dadosUpdate,$dadosRegistro);

                if(isset($resStatusProcessado->erro)) {
                    $this->gravaLogErro($resStatusProcessado);
                    throw new Exception($resStatusProcessado->erro);
                }

            $this->dao->commit();

        } catch (Exception $e) {

            $this->dao->rollback();

            $this->dao->begin();

                // Altera o status da mensagem para "AGUARDANDO" para cada mensagem
                $dadosUpdate = array('smsmsoid' => $this->getStatusMensageria('AGUARDANDO'));
                $resUpdate = $this->dao->atualizaMensagem($dadosUpdate,$dadosRegistro);

            $this->dao->commit();
            echo "\n".$e->getMessage();

            $dadosLog = new stdClass();
            $dadosLog->erro = $e->getMessage();
            $dadosLog->sql = 'desconhecida';
            $this->gravaLogErro($dadosLog);
        }

        try {

                $this->dao->begin();

                    // Atualiza tentativas de processamento da mensagem
                    $resTentativa = $this->dao->atualizaTentativasMensagem($dadosRegistro);

                    if(isset($resTentativa->erro)) {
                        $this->gravaLogErro($resTentativa);
                    } else {

                        if($resTentativa->resultado >= 5) {
                            $dadosUpdate =  array('smsmsoid' => $this->getStatusMensageria('ERRO'));
                            $resStatusErro = $this->dao->atualizaMensagem($dadosUpdate,$dadosRegistro);

                            if(isset($resStatusErro->erro)) {
                                $this->gravaLogErro($resStatusErro);
                            }
                        }

                    }

                $this->dao->commit();

        } catch (Exception $e) {
             echo "\n".$e->getMessage();

            $dadosLog = new stdClass();
            $dadosLog->erro = $e->getMessage();
            $dadosLog->sql = 'desconhecida';
            $this->gravaLogErro($dadosLog);
        }

    }

    private function mapeiaItensAssistencia( $arrayPropriedades ) {

        $arrRetorno = array();
        $arrCorrespondentes = array(
            'XA_DEFECT_FOUND',
            'XA_DEFECT_CAUSE',
            'XA_TECHNICAL_OCCURRENCE',
            'XA_TECHNICAL_SOLUTION',
            'XA_AFFECTED_COMPONENT',
            'XA_TECHNICAL_NOTES'
        );

        for ($i=1; $i < 21 ; $i++) {

            foreach ($arrayPropriedades as $chave => $valor) {

                $indiceItem = "XA_SERVICE_REASON_".$i;
                $idItemOS = isset($arrayPropriedades[$indiceItem]) ? $arrayPropriedades[$indiceItem] : NULL;

                foreach ($arrCorrespondentes as $item) {

                    $itemAssistencia = $item . '_' . (string)$i;

                    if($chave == $itemAssistencia) {

                        if( $item == 'XA_DEFECT_FOUND' && strlen($valor) > 0 ) {
                            $retorno = $this->dao->buscarItemDefeito($idItemOS, $valor);

                            if( isset($retorno->resultado['osdfoid']) ){
                                $osdfoid = $retorno->resultado['osdfoid'];
                            } else{
                                $osdfoid = '';
                            }
                            $arrRetorno[$i][$item] = $osdfoid;
                        } else{
                            $arrRetorno[$i][$item] = $valor;
                        }
                    }
                }
            }
        }

        return $arrRetorno;
    }

    private function retornaCampoLaudo($prop) {

        $camposLaudo = array(
            'XA_TECHNICAL_NOTES' =>'ositobs',
            'XA_DEFECT_FOUND' =>'ositosdfoid_analisado',
            'XA_DEFECT_CAUSE' =>'ositotcoid',
            'XA_TECHNICAL_OCCURRENCE' =>'ositotooid',
            'XA_AFFECTED_COMPONENT' =>'ositotaoid',
            'XA_TECHNICAL_SOLUTION' =>'ositotsoid'
        );

        return isset($camposLaudo[$prop]) ? $camposLaudo[$prop] : NULL;
    }

    private function retornaStatusOracle($contexto) {

        $camposStatus = array(
            'activity_started' => 'started',
            'activity_completed'=> 'complete',
            'activity_suspended'=> 'suspended',
            'activity_notdone'=> 'notdone',
            'activity_cancelled'=> 'cancelled'
        );

        return isset($camposStatus[$contexto]) ? $camposStatus[$contexto] : NULL;
    }

    private function historicoOrdem($textoHist,
                                    $contexto,
                                    $dadosCliente=NULL,
                                    $dadosInst=NULL,
                                    $dadosRep=NULL,
                                    $propriedades=NULL,
                                    $dadosAgenda=NULL) {

        if(isset($dadosInst['itlnome']) && strpos($textoHist, '[TECNICO]') !== false) {
            $textoHist = str_replace('[TECNICO]', (string) $dadosInst['itlnome'], $textoHist);
        } else {
            $textoHist = str_replace('[TECNICO]', '[Não informado]', $textoHist);
        }

        if(strpos($textoHist, '[REPRESENTANTE]') !== false) {
            if(isset($dadosRep['repnome'])) {
                $textoHist = str_replace('[REPRESENTANTE]', (string) $dadosRep['repnome'], $textoHist);
            } else {
                $textoHist = str_replace('[REPRESENTANTE]', '[Não informado]', $textoHist);
            }

            if(isset($propriedades['resource_from'])) {
                $textoHist = str_replace('[REPRESENTANTE]', (string) $propriedades['resource_from'], $textoHist);
            } else {
                $textoHist = str_replace('[REPRESENTANTE]', '[Não informado]', $textoHist);
            }
        }
        if(isset($propriedades['date'])) {
            $propriedades['date'] = date('d/m/Y',strtotime($propriedades['date']));
        }

        switch ($contexto) {
            case 'activity_started':
                $textoHist = str_replace('[DATA]', (string) $propriedades['date'], $textoHist);
                $textoHist = str_replace('[HORA]', (string) $propriedades['start_time'], $textoHist);
            case 'activity_moved':
                $textoHist = str_replace('[HORA_INI_OFSC]', (string) $propriedades['activity_start_time'], $textoHist);
                $textoHist = str_replace('[HORA_FIM_OFSC]', (string) $propriedades['activity_end_time'], $textoHist);
                $textoHist = ($dadosAgenda['osaemergencial'] == 't' ? "Atendimento Emergencial - O horário estimado previsto para o técnico executar o serviço não foi determinado." : $textoHist);
                break;
            case 'activity_completed':
                $textoHist = str_replace('[DATA]', (string) $propriedades['date'], $textoHist);
                $textoHist = str_replace('[HORA]', (string) $propriedades['start_time'], $textoHist);
                break;
            case 'activity_cancelled':
                $textoHist = str_replace('[DATA]', (string) $propriedades['date'], $textoHist);
                $textoHist = str_replace('[HORA INICIO]', date("H:i", strtotime($dadosAgenda['osahora'])), $textoHist);
                $textoHist = str_replace('[HORA FIM]', date("H:i", strtotime($dadosAgenda['osahora'] . " +2 hours")), $textoHist);
                $textoHist = str_replace('[MOTIVO]', (string) $propriedades['XA_CANCELATION_REASON_LABEL'], $textoHist);
                $textoHist = str_replace('[OBSERVACAO]', (string) $propriedades['XA_CANCELATION_NOTES'], $textoHist);
                break;
            case 'activity_suspended':
                $textoHist = str_replace('[DATA]', (string) $propriedades['date'], $textoHist);
                $textoHist = str_replace('[HORA]', (string) date('H:i',strtotime($propriedades['message_time'])), $textoHist);
                $textoHist = str_replace('[OS]', (string) $dadosAgenda['osaordoid'], $textoHist);
                $textoHist = str_replace('[OBSERVACAO]', (string) $propriedades['XA_SUSPENSION_NOTES'], $textoHist);
                $textoHist = str_replace('[MOTIVO]', (string) $propriedades['XA_SUSPENSION_REASON_LABEL'], $textoHist);
                break;
            case 'activity_notdone':
                $textoHist = str_replace('[DATA]', (string) $propriedades['date'], $textoHist);
                $textoHist = str_replace('[HORA INICIO]', date("H:i", strtotime($dadosAgenda['osahora'])), $textoHist);
                $textoHist = str_replace('[HORA FIM]', date("H:i", strtotime($dadosAgenda['osahora'] . " +2 hours")), $textoHist);
                $textoHist = str_replace('[MOTIVO]', (string) $propriedades['XA_NOTDONE_REASON_LABEL'], $textoHist);
                $textoHist = str_replace('[OBSERVACAO]', (string) $propriedades['XA_NOTDONE_NOTES'], $textoHist);
                break;
            case 'customer_notification_activity_cancelled':
            case 'customer_notification_change':
                $textoHist = str_replace('[DATA]', (string) date('d/m/Y',strtotime($dadosAgenda['osadata'])) , $textoHist);
                $textoHist = str_replace('[HORA INICIO]', date("H:i", strtotime($dadosAgenda['osahora'])), $textoHist);
                $textoHist = str_replace('[HORA FIM]', date("H:i", strtotime($dadosAgenda['osahora'] . " +2 hours")), $textoHist);
                break;
            case 'customer_notification_day_before':
                $textoHist = str_replace('[EMAIL CLIENTE]', (string) $dadosCliente['osecemail'], $textoHist);
                $textoHist = str_replace('[CELULAR CLIENTE]', (string) $dadosCliente['oscccelular'], $textoHist);
                break;
            case 'customer_notification_same_day_reminder':
                $textoHist = str_replace('[EMAIL CLIENTE]', (string) $dadosCliente['osecemail'], $textoHist);
                $textoHist = str_replace('[CELULAR CLIENTE]', (string) $dadosCliente['oscccelular'], $textoHist);
                break;
            default:
                break;
        }

        return $textoHist;
    }

    private function gravaChecklist($ordoid,$arrayPropriedades) {

        $retorno                = new stdClass();
        $cosordoid              = (int) $ordoid;
        $rsInitial              = NULL;
        $rsFinal                = NULL;
        $existeChecklistInicial = false;
        $existeChecklistFinal   = false;
        $keysChecklistPrincipal = array('BOX_CONDITIONS','BATTERY','TECH_NOTES','CUST_NOTES');
        $arrayCondicoes         = array('S' => 'F', 'N' => 'N', 'ND' => 'I');

        try {

            if(isset($arrayPropriedades['XA_INITIAL_CHECK_BOX_CONDITIONS']) &&
                trim($arrayPropriedades['XA_INITIAL_CHECK_BOX_CONDITIONS']) != '') {
                // Dados checklist inicial
                $dadosInsertInitial = array(
                    'cosciboid'             => $arrayPropriedades['XA_INITIAL_CHECK_BOX_CONDITIONS'],
                    'cosbateria_volts'      => $arrayPropriedades['XA_INITIAL_CHECK_BATTERY'],
                    'cosobs_tecnico'        => $arrayPropriedades['XA_INITIAL_CHECK_TECH_NOTES'],
                    'cosobs_cliente'        => $arrayPropriedades['XA_INITIAL_CHECK_CUST_NOTES'],
                    'cosformulario'         => 'I',
                    'cosusuoid_cadastro'    => $this->getUsuarioSmartAgenda(),
                    'cosordoid'             => $cosordoid
                );

                // Insere dados principais da checklist inicial
                $rsInitial = $this->dao->gravaChecklist($dadosInsertInitial);

                if(isset($rsInitial->erro)) {
                    throw new Exception($rsInitial->erro);
                }
                $existeChecklistInicial = true;
            }

            if(isset($arrayPropriedades['XA_FINAL_CHECK_BOX_CONDITIONS']) &&
                trim($arrayPropriedades['XA_FINAL_CHECK_BOX_CONDITIONS']) != '') {
                // Dados checklist final
                $dadosInsertFinal = array(
                    'cosciboid'             => $arrayPropriedades['XA_FINAL_CHECK_BOX_CONDITIONS'],
                    'cosbateria_volts'      => $arrayPropriedades['XA_FINAL_CHECK_BATTERY'],
                    'cosobs_tecnico'        => $arrayPropriedades['XA_FINAL_CHECK_TECH_NOTES'],
                    'cosobs_cliente'        => $arrayPropriedades['XA_FINAL_CHECK_CUST_NOTES'],
                    'cosformulario'         => 'F',
                    'cosusuoid_cadastro'    => $this->getUsuarioSmartAgenda(),
                    'cosordoid'             => $cosordoid
                );

                // Insere dados principais da checklist final
                $rsFinal = $this->dao->gravaChecklist($dadosInsertFinal);

                if(isset($rsFinal->erro)) {
                    throw new Exception($rsFinal->erro);
                }
                $existeChecklistFinal = true;
            }

            if( $existeChecklistFinal || $existeChecklistInicial ) {

                // Percorre as propriedades para preencher o checklist
                foreach ($arrayPropriedades as $key => $value) {

                    $checklistInicial   =  (strpos($key, 'XA_INITIAL_CHECK_') !== false);
                    $checklistFinal     =  (strpos($key, 'XA_FINAL_CHECK_') !== false);

                   if( $checklistInicial && !$existeChecklistInicial ) {
                        continue;
                    } else if( $checklistFinal && !$existeChecklistFinal ) {
                        continue;
                    }

                    $chave = str_replace('XA_INITIAL_CHECK_', '', $key);
                    $chave = str_replace('XA_FINAL_CHECK_', '', $chave);

                    // Caso não seja chave principal e seja do checklist principal ou final
                    if( ($checklistInicial  || $checklistFinal) && !in_array($chave, $keysChecklistPrincipal) ) {

                        // Busca dados do item de acordo com a descrição
                        $dadosRegistro = array(
                            'clichave_ofsc' => array("value" => $chave, "condition" => "="),
                            'clidt_exclusao'  =>  array("value" => "NULL", "condition" => "IS")
                        );

                        $rsItem = $this->dao->buscaChecklistItem($dadosRegistro);

                        if(isset($rsItem->erro)) {
                            throw new Exception($rsItem->erro);
                        }

                        $dadosInsertItem = array();

                        // Insere itens do checklist inicial
                        if( $checklistInicial ) {
                            $dadosInsertItem['cosicosoid'] = $rsInitial->resultado['cosoid'];
                        } else if( $checklistFinal ) {
                            // referencia do checklist final
                            $dadosInsertItem['cosicosoid'] = $rsFinal->resultado['cosoid'];
                        }

                        $dadosInsertItem['cosiclioid'] = $rsItem->resultado['clioid'];
                        $dadosInsertItem['cosicondicao'] = isset($arrayCondicoes[$value]) ? $arrayCondicoes[$value] : 'I';

                        // Grava item da checklist
                        $rsChecklistItem = $this->dao->gravaChecklistItem($dadosInsertItem);

                        if(isset($rsChecklistItem->erro)) {
                             throw new Exception($rsChecklistItem->erro);
                        }
                    }
                }
            }


        } catch(Exception $e) {
            $retorno->erro = $e->getMessage();
            echo "\n".$e->getMessage();
        }

        return $retorno;
    }

    private function processaContexto($contexto,$smoid,$smid_ofsc) {


        $retorno = new stdClass();
        $arrayPropriedades = array();
        $dadosContaoCliente = NULL;
        $dadosRepInst = NULL;
        $dadosAgendamento = NULL;
        $dadosAgendaOS = NULL;
        $dadosLayout = NULL;

        echo "\n<br/><b>Contexto atual: $contexto\n </b><br/>";

        try {
            // Pega regras do contexto presente no array de parametrização
            $regras = $this->getRegrasContexto($contexto);

            // Se não houver regras do contexto no array, não processa
            if(is_null($regras)) {
                echo "\nNao Possui regras do contexto, ira finalizar o processamento.\n<br />";
                return;
            }

            // Busca as propriedades da mensagem
            $rsPropriedades = $this->dao->buscaPropriedadesMensageria($smoid);

            if(isset($rsPropriedades->erro)) {
                throw new Exception($rsPropriedades->erro);
            }

            // Associa propriedades no array
            while($propriedade = pg_fetch_object($rsPropriedades->resultado)) {
                $arrayPropriedades[$propriedade->smpchave] = $propriedade->smpvalor;
            }

            echo "\n<br><b>Propriedades da do contexto:</b>\n<br/>";
            echo "<pre>";var_dump($arrayPropriedades);echo "</pre>";

            // Id da OS
            $ordoid  = isset($arrayPropriedades['XA_WO_NUMBER']) ? $arrayPropriedades['XA_WO_NUMBER'] : NULL;

            $statusOS = true;

            //Se ordoid dif de null pega o status da OS
            if(!is_null($ordoid)) {
                $dadosRegistro = array(
                    'ordoid' => array( "value" => $ordoid, "condition" => "=")
                );

                $rsAgendamento = $this->dao->buscaDadosOrdemServico($dadosRegistro);

                if(isset($rsAgendamento->erro)) {
                    throw new Exception($rsAgendamento->erro);
                }

                if( $rsAgendamento->resultado && (isset($regras['is_processo_fim_do_dia']) && $regras['is_processo_fim_do_dia'] === true) ) {
                    $statusOS = ($rsAgendamento->resultado['ordstatus'] == '3' || $rsAgendamento->resultado['ordstatus'] == '24' ? false : true);
                }

            }

            //se o status da OS for diferente de 3 (Concluído) e diferente de 24 (Aguardando Conclusão Técnico)
            if ($statusOS) {

                // Id do ordem_servico_agenda
                $osaoid = isset($arrayPropriedades['appt_number']) ? $arrayPropriedades['appt_number'] : NULL;

                // Busca dados do agendamento
                if(!is_null($osaoid)) {
                    $dadosRegistro = array(
                        'osaoid' => array( "value" => $osaoid, "condition" => "=")
                    );

                    $dadosAgendamento = $this->dao->buscaDadosAgendamento($dadosRegistro);

                    $cloneDadosAgendamento = clone($dadosAgendamento);

                    if(isset($dadosAgendamento->erro)) {
                        throw new Exception($dadosAgendamento->erro);
                    }

                    $ordoid = is_null($ordoid) ? $dadosAgendamento->resultado['osaordoid'] : $ordoid;
                }

                // Busca dados do cliente
                if(isset($dadosAgendamento->resultado)) {

                    // Recuperda dados de contato do cliente para envio de e-mail/SMS
                    $arrayDadosContato = array(
                        'osecordoid ' => array("value" => (int) $ordoid, "condition" => "=")
                    );

                    $dadosContatoCliente = $this->dao->buscaDadosContatoCliente($arrayDadosContato);

                    if(isset($dadosContatoCliente->erro)) {
                        throw new Exception($dadosContatoCliente->erro);
                    }
                }

                // Busca dados dos representantes/instalador atual e novo (caso necessário)
                if(isset($arrayPropriedades['external_id']) || isset($arrayPropriedades['destination_resource'])) {
                    $dadosRepInst = $this->dadosRepresentanteInstalador($arrayPropriedades);

                    if(isset($dadosRepInst->erro)) {
                        throw new Exception($dadosRepInst->erro);
                    }
                }

                if( isset($regras['set_instalador']) && $regras['set_instalador'] === true ) {

                    $dadosUpdate = array();

                    if( isset($arrayPropriedades['destination_resource_external_id']) ) {
                        $tipoDestino = preg_replace('/\d/', '', $arrayPropriedades['destination_resource_external_id']);
                        $itloid      = preg_replace("/[^0-9]/","",$arrayPropriedades['destination_resource_external_id']);
                    } else {
                        $tipoDestino = preg_replace('/\d/', '', $arrayPropriedades['external_id']);
                        $itloid      = preg_replace("/[^0-9]/","",$arrayPropriedades['external_id']);
                    }

                    if($tipoDestino == 'TC' && $contexto != 'activity_cancelled') {
                        $dadosInstalador     = $this->dao->infoInstalador(array('itloid' => array('value' => $itloid,'condition' => ' = ')));
                        $dadosRelacionamento = $this->dao->getRelacionamentoRepresentante( $dadosInstalador->resultado['itlrepoid'] );
                        $dadosUpdate         = array('orditloid' => $itloid, 'ordrelroid' => $dadosRelacionamento->relroid);

                    } else if($contexto == 'activity_cancelled') {
                        $dadosUpdate = array('orditloid' => 'NULL');
                    }

                    if( ! empty($dadosUpdate) ) {

                        $dadosRegistro = array('ordoid' => array( "value" => $ordoid, "condition" => "="));
                        $resUpdate     = $this->dao->atualizaOrdemServico($dadosUpdate,$dadosRegistro);

                        if(isset($resUpdate->erro)) {
                            throw new Exception($resUpdate->erro);
                        }
                    }

                }

                if(isset($regras['del_agendamento']) && $regras['del_agendamento'] !== false) {

                    //Verifica se o agendamento ja esta cancelado
                    $rsAgendamentoAtivo = $this->dao->isAgendamentoAtivo($arrayPropriedades['appt_number']);

                    if(isset($rsAgendamentoAtivo->erro)) {
                        throw new Exception($rsAgendamentoAtivo->erro);
                    }

                    if($rsAgendamentoAtivo->existe){

                        $motivoExclusao = isset($arrayPropriedades['XA_CANCELATION_REASON_LABEL']) ? $arrayPropriedades['XA_CANCELATION_REASON_LABEL'] : '';

                        // Atualiza ordem_servico_agenda
                        $dadosUpdate = array('osaexclusao' => 'NOW()',
                                                'osausuoid_excl' => $this->getUsuarioSmartAgenda(),
                                                'osamotivo_excl' => $motivoExclusao );
                        $dadosRegistro = array('osaoid' => array( "value" => $arrayPropriedades['appt_number'], "condition" => "="),
                                                'osaexclusao' => array( "value" => 'NULL', "condition" => "IS"));

                        $resUpdate = $this->dao->atualizaOrdemServicoAgenda($dadosUpdate,$dadosRegistro);

                         if(isset($resUpdate->erro)) {
                            throw new Exception($resUpdate->erro);
                        }
                    } else {
                        $regras['set_historico_os'] = false;
                    }
                }

                // Atualiza dados do agendamento
                if(isset($regras['set_agendamento']) && $regras['set_agendamento'] !== false) {

                    $dadosUpdate = array();

                    $dadosUpdate['instalador']          = isset($dadosRepInst->dadosInstalador)            ? $dadosRepInst->dadosInstalador            : NULL;
                    $dadosUpdate['instalador_novo']     = isset($dadosRepInst->dadosInstaladorNovo)        ? $dadosRepInst->dadosInstaladorNovo        : NULL;
                    $dadosUpdate['representante_novo']  = isset($dadosRepInst->dadosRepresentanteNovo)     ? $dadosRepInst->dadosRepresentanteNovo     : NULL;
                    $dadosUpdate['activity_start_time'] = isset($arrayPropriedades['activity_start_time']) ? $arrayPropriedades['activity_start_time'] : NULL;
                    $dadosUpdate['activity_end_time']   = isset($arrayPropriedades['activity_end_time'])   ? $arrayPropriedades['activity_end_time']   : NULL;
                    $dadosUpdate['activity_coordx']     = isset($arrayPropriedades['activity_coordx'])     ? $arrayPropriedades['activity_coordx']     : NULL;
                    $dadosUpdate['activity_coordy']     = isset($arrayPropriedades['activity_coordy'])     ? $arrayPropriedades['activity_coordy']     : NULL;

                    $dadosAgendamento = $this->atualizaDadosAgendamento(
                        $osaoid,
                        $regras['set_agendamento'],
                        $arrayPropriedades,
                        $dadosUpdate,
                        $this->retornaStatusOracle($contexto)
                    );

                    if(isset($dadosAgendamento->erro)) {
                        throw new Exception($dadosAgendamento->erro);
                    }
                }

                // Grava dados do checklist
                if(isset($regras['set_checklist']) && $regras['set_checklist'] == true) {

                    $rsChecklist = $this->gravaChecklist($ordoid,$arrayPropriedades);

                    if(isset($rsChecklist->erro)) {
                        throw new Exception($rsChecklist->erro);
                    }
                }

                // Salva histórico na ordem de serviço
                if(isset($regras['set_historico_os']) && $regras['set_historico_os'] !== false) {

                    echo "<br/>\nSalva historico da OS: ".$ordoid."\n<br/>";

                    $motivoCorretora = $this->dao->buscaMotivoHistoricoCorretora(
                        array(
                            'mhcdescricao' => array('value' => $regras['set_historico_os']['status'],'condition' => '='),
                            'mhcexclusao' => array('value' => 'NULL','condition' => 'IS')
                        )
                    );
                    $dadosRegistro = array(
                        'osaoid' => array( "value" => $osaoid, "condition" => "=")
                    );


                    $dadosAgenda = $this->dao->buscaDadosAgendamento($dadosRegistro);

                    if(isset($dadosAgenda->erro)) {
                        throw new Exception($dadosAgenda->erro);
                    }

                    $dadosInstalador    = isset($dadosRepInst->dadosInstalador) ? $dadosRepInst->dadosInstalador : NULL;
                    $dadosRepresentante = isset($dadosRepInst->dadosRepresentante) ? $dadosRepInst->dadosRepresentante : NULL;
                    $rsAgendamento      = isset($dadosAgenda->resultado) ? $dadosAgenda->resultado : NULL;
                    $motivoCorretora    = isset($motivoCorretora) ? $motivoCorretora : 'NULL';


                    $dadosHistorico = $this->historicoOrdem(
                        $regras['set_historico_os']['texto'],
                        $contexto,
                        $dadosContatoCliente->resultado,
                        $dadosInstalador,
                        $dadosRepresentante,
                        $arrayPropriedades,
                        $rsAgendamento
                    );

                    $dadosInsert = array(
                        'orsordoid'     => $ordoid,
                        'orsusuoid'     => $this->getUsuarioSmartAgenda(),
                        'orssituacao'   => $dadosHistorico,
                        'orsstatus'     => $motivoCorretora
                    );

                    $rsHistoricoOrdem = $this->dao->gravaHistoricoOrdemServico($dadosInsert);

                    if(isset($rsHistoricoOrdem->erro)) {
                        throw new Exception($rsHistoricoOrdem->erro);
                    }
                }


                // Envia notificação cliente/técnico/representante
                if( (isset($regras['send_email']) && $regras['send_email'] !== false) ||
                    (isset($regras['send_sms']) && $regras['send_sms'] !== false) ) {

                    echo "\nEnvia notificacao e-mail/sms\n";

                    $dadosCliente = new stdClass();

                    $dadosRegistro = array(
                        'osaoid' => array( "value" => $osaoid, "condition" => "=")
                    );

                    $dadosAgendaOS = $this->dao->buscaDadosAgendaOS($dadosRegistro);

                    if(isset($dadosAgendaOS->erro)) {
                        throw new Exception($dadosAgendaOS->erro);
                    }

                    $dadosAgendaOS = isset($dadosAgendaOS->resultado) ? $dadosAgendaOS->resultado : NULL;

                    if($contexto == 'activity_notdone') {

                        if( isset($arrayPropriedades['XA_NOTDONE_REASON']) ) {
                            $dadosCliente = ( $arrayPropriedades['XA_NOTDONE_REASON'] == self::NO_SHOW_CLIENTE ) ? $dadosContatoCliente : NULL;
                        }
                    }

                   //STI-86654
                    if ($this->trataEnvioFimDoDia($contexto, $arrayPropriedades['user_login'], $cloneDadosAgendamento)){

                        $rsNotificacao = $this->enviaNotificacao(
                                $regras['send_email'],
                                $regras['send_sms'],
                                $dadosRepInst,
                                $dadosCliente,
                                $dadosAgendaOS,
                                $arrayPropriedades,
                                $regras
                        );

                        if(isset($rsNotificacao) && $rsNotificacao == false) {
                            echo "Erro ao enviar notificacao <br/> \n";
                        }

                    }

                }

                // Faz o cancelamento da solicitação da reserva de estoque
                if(isset($regras['set_estoque']) && $regras['set_estoque'] == true) {

                    echo "<br>\nCancela Reserva de Estoque, solicitacao e quota\n";

                    $cancelaReserva     = true;
                    $liberaCota         = true;
                    $cancelaSolicitacao = true;
                    $cancelaReservaCD   = true;


                    if($contexto == 'activity_moved'){

                        $cancelaReserva = false;
                        $liberaCota = false;
                        $cancelaSolicitacao = false;

                        // Verificação se faz cancelamento de reserva ou não
                        if(isset($dadosRepInst->dadosInstalador)) {
                            // Compara com o instalador/representante destino
                            if((isset($dadosRepInst->dadosInstaladorNovo)
                                && $dadosRepInst->dadosInstaladorNovo['itlrepoid'] != $dadosRepInst->dadosInstalador['itlrepoid']) ||
                               (isset($dadosRepInst->dadosRepresentanteNovo)
                                && $dadosRepInst->dadosRepresentanteNovo['repoid'] != $dadosRepInst->dadosInstalador['itlrepoid'])) {
                                $cancelaReserva = true;
                                $cancelaSolicitacao = true;
                                $liberaCota = true;
                            } else if(!isset($dadosRepInst->dadosInstaladorNovo) && !isset($dadosRepInst->dadosRepresentanteNovo)) {
                                $cancelaReserva = true;
                                $cancelaSolicitacao = true;
                                $liberaCota = true;
                            }
                        } else if(isset($dadosRepInst->dadosRepresentante)) {
                            // Compara com o instalador/representante destino
                            if((isset($dadosRepInst->dadosInstaladorNovo)
                                && $dadosRepInst->dadosInstaladorNovo['itlrepoid'] != $dadosRepInst->dadosRepresentante['repoid']) ||
                               (isset($dadosRepInst->dadosRepresentanteNovo)
                                && $dadosRepInst->dadosRepresentanteNovo['repoid'] != $dadosRepInst->dadosRepresentante['repoid'])) {
                                $cancelaReserva = true;
                                $cancelaSolicitacao = true;
                                $liberaCota = true;
                            } else if(!isset($dadosRepInst->dadosInstaladorNovo) && !isset($dadosRepInst->dadosRepresentanteNovo)) {
                                $cancelaReserva = true;
                                $cancelaSolicitacao = true;
                                $liberaCota = true;
                            }
                        }
                    } else  if( $contexto == 'cancel_activity_by_wo' && $rsAgendamento->resultado['ordstatus'] == '3' ) {
                        $cancelaSolicitacao = false;
                        $cancelaReservaCD = false;
                    }

                    if( $cancelaReserva ) {

                        if( $contexto == 'activity_completed'  || $contexto == 'activity_notdone'){
                            $cancelaReservaCD   = false;
                            $cancelaSolicitacao = false;
                        }

                        if($cancelaReservaCD) {
                            $rsReservaEstoque = $this->liberaReservaEstoque( $ordoid, $osaoid, $cancelaReserva );

                            if(isset($rsReservaEstoque['status']) && $rsReservaEstoque['status'] == 'erro') {
                                throw new Exception($rsReservaEstoque['msg']);
                            }
                        } else {
                            $rsReservaEstoque = $this->dao->setCancelarReservaPrestadorAgendado( $ordoid );

                            if(isset($rsReservaEstoque->erro)) {
                                throw new Exception($dadosAgendaOS->erro);
                            }
                        }
                    }

                    if( $cancelaSolicitacao ) {
                        $rsSolicitacao = $this->cancelarSolicitacao( $ordoid, $osaoid );
                    }

                    if( $liberaCota ){
                        $controleConsumo    = new ControleConsumo($this->conn);
                        $controleConsumo->setIdOrdemServico(array($ordoid));
                        $controleConsumo->setIdAgendamento($osaoid);
                        $controleConsumo->removerAgenda();

                    }
                }

                // Grava laudo técnico
                if(isset($regras['set_laudo_tecnico']) && $regras['set_laudo_tecnico'] == true) {

                    echo "<br>\nGrava Lauda Tecnico\n";

                    $resLaudoTecnico = $this->gravaLaudoTecnico( $ordoid, $arrayPropriedades);

                    if(isset($resLaudoTecnico->erro)) {
                        throw new Exception($resLaudoTecnico->erro);
                    }
                }

                // Permissão para concluir atividade
                if(isset($regras['get_permissao_conclusao']) && $regras['get_permissao_conclusao'] ==  true) {

                    echo "\nPermissao para concluir atividade no OFSC\n";

                    $dadosRegistro = array(
                        'ordoid' => array( "value" => $ordoid, "condition" => "=")
                    );

                    $rsAgendamento = $this->dao->buscaDadosOrdemServico($dadosRegistro);

                    if(isset($rsAgendamento->erro)) {
                        throw new Exception($rsAgendamento->erro);
                    }

                    if($rsAgendamento->resultado) {

                        $permissao = $rsAgendamento->resultado['ordstatus'] == '3' ? 1 : 0;

                        if($permissao === 0) {
                            //Nota: mensagem nao pode conter caracteres especiais
                            $msg = "Deve-se concluir a ordem de servico para prosseguir";
                        } else {
                            $msg = "Autorizado concluir a atividade";
                        }

                        $propriedades = array(
                            'XA_REQUEST_PERMISSION_CLOSE' => $permissao,
                            'XA_REQUEST_RESPONSE'         => $msg
                        );

                        if( ! isset($arrayPropriedades['aid']) ) {
                            $idAtividade = $dadosAgendamento->resultado['osaid_atividade'];
                        } else {
                            $idAtividade = $arrayPropriedades['aid'];
                        }

                         $retornoActivity = $this->atualizarAtividadeOFSC($idAtividade, $propriedades);

                        if(isset($retornoActivity->erro)) {
                            throw new Exception($retornoActivity->erro);
                        }
                    }
                }

                // Grava imagem da assinatura digital enviada
                if(isset($regras['set_imagem']) && $regras['set_imagem'] == true) {
                    echo "<br>\nGravar Imagens\n";
                    $listaImagens = $this->gravaImagens($arrayPropriedades);

                    if( !empty($listaImagens) ){
                        $resAnexo = $this->dao->gravarHistoricoAnexo( $listaImagens );

                        if(isset($resAnexo->erro)) {
                            throw new Exception($resAnexo->erro);
                        }
                    }
                }

                // Cancela atividade no OFSC
                if(isset($regras['del_atividade_ofsc']) && $regras['del_atividade_ofsc'] == true) {
                    echo "\Cancelar atividade no OFSC\n";
                    $this->cancelaAtividade($dadosAgendamento->resultado);
                }


                if(isset($regras['del_local_instalacao']) && $regras['del_local_instalacao'] == true){
                    echo "\nDeletar local instalacao\n";

                    $resExcluir = $this->dao->excluirLocalInstalacao($ordoid);

                    if(isset($resExcluir->erro)) {
                        throw new Exception($resExcluir->erro);
                    }

                }

            }

        } catch (Exception $e) {
            $retorno->erro = $e->getMessage();
            echo "\n".$e->getMessage();
        }

        echo "<br/>\n------------\n<br/>";

        return $retorno;
    }

    private function trataEnvioFimDoDia($contexto, $user_login, $dadosAgendamento) {

        if ($contexto == 'customer_notification_activity_cancelled'
            && strcasecmp($user_login,'message_engine') == 0
            &&  $dadosAgendamento->resultado['osaasaoid'] == 2 ) {
            return false;
        }

        return true;
    }

    private function gravaLaudoTecnico( $ordoid, $arrayPropriedades ) {


        $retorno = new stdClass();
        $itensMapeados = $this->mapeiaItensAssistencia( $arrayPropriedades );
        $dadosUpdate = NULL;

        try {
            foreach ($itensMapeados as $key => $arrProp) {

                // Caso XA_DEFECT_FOUND_N não tenha valor, significa que não será feito a atualização na tabela [ordem_servico_item]
                if(isset($arrProp['XA_DEFECT_FOUND']) && strlen($arrProp['XA_DEFECT_FOUND']) > 0) {

                   $indiceItem = "XA_SERVICE_REASON_".$key;
                    $ositotioid = isset($arrayPropriedades[$indiceItem]) ? $arrayPropriedades[$indiceItem] : NULL;

                    if(!is_null($ositotioid)) {
                        $dadosRegistro = array(
                                        'ositordoid' => array("value" => $ordoid, "condition" => "="),
                                        'ositotioid' => array( "value" => $ositotioid, "condition" => "="),
                                         'ositexclusao' => array( "value" => 'NULL', "condition" => "IS")
                                         );
                        $dadosUpdate = array();

                        foreach ($arrProp as $chave => $valor) {
                            $dadosUpdate[$this->retornaCampoLaudo($chave)] = $valor;
                        }

                        //Realiza o update
                        $res = $this->dao->atualizaItemOS($dadosUpdate,$dadosRegistro);

                        if(isset($res->erro)) {
                            throw new Exception($res->erro);
                        }
                    }

                }
            }
        } catch (Exception $e) {
            $retorno->erro = $e->getMessage();
        }

        return $retorno;
    }

    private function dadosRepresentanteInstalador($arrayPropriedades) {

        $retorno = new stdClass();
        $retorno->dadosInstalador = NULL;
        $retorno->dadosRepresentante = NULL;
        $retorno->dadosRepresentanteNovo = NULL;
        $retorno->instaladorNovo = NULL;
        $tipo = preg_replace('/\d/', '', $arrayPropriedades['external_id']);
        $external_id = preg_replace("/[^0-9]/","",$arrayPropriedades['external_id']);

        try {

            // Instalador
            if(($tipo === 'TC' || $tipo === 'TEC.') && $arrayPropriedades['external_id']) {
                // busca dados do instalador
                $instalador = $this->dao->infoInstalador(
                    array(
                        'itloid' => array('value' => $external_id,'condition' => '=')
                    )
                );

                if(isset($instalador->erro)) {
                    throw new Exception($instalador->erro);
                }

                // Busca dados do representante
                $representante = $this->dao->infoRepresentante(
                    array(
                        'repoid' => array('value' => $instalador->resultado['itlrepoid'],'condition' => '=')
                    )
                );

                if(isset($representante->erro)) {
                    throw new Exception($representante->erro);
                }
            }

            // Representante
            if($tipo === 'PS' && $arrayPropriedades['external_id']) {
                // busca dados do representante
                $representante = $this->dao->infoRepresentante(
                    array(
                        'repoid' => array('value' => $external_id,'condition' => '=')
                    )
                );

                if(isset($representante->erro)) {
                    throw new Exception($representante->erro);
                }
            }

            // Bucket ou instalador para o qual a atividade será movida
            if(isset($arrayPropriedades['destination_resource_external_id'])) {
                // Caso o external_id venha com PSXXXX, é prestador
                // Caso o external_id venha com TCXXXX, é instalador
                $tipoDestino = preg_replace('/\d/', '', $arrayPropriedades['destination_resource_external_id']);
                $external_id_destination = preg_replace("/[^0-9]/","",$arrayPropriedades['destination_resource_external_id']);
                $buscaInstaladorDestino = false;
                $buscaRepresentanteDestino = false;
                $instaladorDestino = null;
                $representanteDestino = null;

                if($tipoDestino == 'PS') {
                    $representanteDestino = $external_id_destination;
                    $buscaRepresentanteDestino = true;
                } else if ($tipoDestino == 'TC' || $tipoDestino == 'TEC.') {
                    $instaladorDestino = $external_id_destination;
                    $buscaInstaladorDestino = true;
                    $buscaRepresentanteDestino = true;
                }

                if($buscaInstaladorDestino == true) {
                    // busca dados do instalador
                    $instaladorNovo = $this->dao->infoInstalador(
                        array(
                            'itloid' => array('value' => $instaladorDestino,'condition' => '=')
                        )
                    );

                    if(isset($instaladorNovo->erro)) {
                        throw new Exception($instaladorNovo->erro);
                    }

                    $representanteDestino = $instaladorNovo->resultado['itlrepoid'];
                }

                if($buscaRepresentanteDestino == true) {
                    // busca dados do representante
                    $representanteNovo = $this->dao->infoRepresentante(
                        array(
                            'repoid' => array('value' => $representanteDestino,'condition' => '=')
                        )
                    );

                    if(isset($representanteNovo->erro)) {
                        throw new Exception($representanteNovo->erro);
                    }
                }

            }

            $retorno->dadosInstalador = isset($instalador->resultado) ? $instalador->resultado : NULL;
            $retorno->dadosRepresentante = isset($representante->resultado) ? $representante->resultado : NULL;
            $retorno->dadosRepresentanteNovo = isset($representanteNovo->resultado) ? $representanteNovo->resultado : NULL;
            $retorno->dadosInstaladorNovo = isset($instaladorNovo->resultado) ? $instaladorNovo->resultado : NULL;

        } catch (Exception $e) {
            $retorno->erro = $e->getMessage();
            echo "\n".$e->getMessage();
        }

        return $retorno;
    }

    public function atualizaDadosAgendamento($osaoid, $regras, $arrayPropriedades, $dados, $statusOracle) {


        echo "<br/>\nAtualiza dados do agendamento numero: ".intval($osaoid)."\n<br/>";

        $retorno = new stdClass();
        $dadosUpdate = array();
        $dadosRegistro = array('osaoid' => array( "value" => $osaoid, "condition" => "="));

        try {

            // representante novo
            if(in_array('prestador', $regras) && isset($dados['representante_novo']['repoid'])) {
                $dadosUpdate['osarepoid'] = $dados['representante_novo']['repoid'];
            }

            // instalador novo
            if( in_array('instalador', $regras) ) {

                if( isset($dados['instalador_novo']['itloid']) ){
                    $dadosUpdate['osaitloid'] = $dados['instalador_novo']['itloid'];
                } else if( isset($dados['instalador']['itloid']) ){
                    $dadosUpdate['osaitloid'] = $dados['instalador']['itloid'];
                }

            }

            if (in_array('start_end_time_activity', $regras) && isset($dados['activity_start_time']) && !empty($dados['activity_start_time'])) {
                $dadosUpdate['osahora_ini_ofsc'] = $dados['activity_start_time'];
            }

            if (in_array('start_end_time_activity', $regras) && isset($dados['activity_end_time']) && !empty($dados['activity_end_time'])) {
                $dadosUpdate['osahora_fim_ofsc'] = $dados['activity_end_time'];
            }

            if( in_array('coordenadas', $regras) && isset($dados['activity_coordx']) && isset($dados['activity_coordy']) ) {
                $dadosUpdate['osacoordx'] = $dados['activity_coordx'];
                $dadosUpdate['osacoordy'] = $dados['activity_coordy'];
            }

            // status da atividade no agendamento
            if(in_array('status_atividade', $regras)) {
                $dadosRegistroStatus = array(
                    'asastatus_oracle' => array( "value" => $statusOracle, "condition" => "=")
                );

                $status = $this->dao->buscaAtividadeStatusAgenda($dadosRegistroStatus);

                if(isset($status->erro)) {
                    throw new Exception($status->erro);
                }

                $dadosUpdate['osaasaoid'] = $status->resultado['asaoid'];
            }

            // Caso tenha que atualizar o motivo do agendamento
            if( in_array('motivo', $regras) ) {

                if( isset($arrayPropriedades['XA_CANCELATION_REASON_LABEL']) ){
                    $dadosUpdate['osamotivo_ofsc'] = $arrayPropriedades['XA_CANCELATION_REASON_LABEL'];
                } else if ( isset($arrayPropriedades['XA_NOTDONE_REASON_LABEL'] ) ) {
                     $dadosUpdate['osamotivo_ofsc'] = $arrayPropriedades['XA_NOTDONE_REASON_LABEL'];
                }
            }

            // Caso tenha que atualizar o motivo de no-show
            if( in_array('motivo_noshow', $regras)
                && isset($arrayPropriedades['XA_NOTDONE_REASON_LABEL'])
                && isset($arrayPropriedades['XA_NOTDONE_REASON']) ) {

                $dadosWhere = array(
                    'omnid_ofsc' => array( "value" => $arrayPropriedades['XA_NOTDONE_REASON'], "condition" => "=")
                );

                $dadosMotivo = $this->dao->buscaMotivoNoShow($dadosWhere);

                if( isset($dadosMotivo->resultado['omnoid']) && !is_null($dadosMotivo->resultado['omnoid']) ) {
                    $dadosUpdate['osamotivo_ofsc'] = $arrayPropriedades['XA_NOTDONE_REASON_LABEL'];
                    $dadosUpdate['osaomnoid']      = $dadosMotivo->resultado['omnoid'];
                }
            }

            // realiza atualização da [ordem_servico_agenda]
            $resAtualizaAgenda = $this->dao->atualizaAgenda($dadosUpdate,$dadosRegistro);

            if(isset($resAtualizaAgenda->erro)) {
                throw new Exception($resAtualizaAgenda->erro);
            }

        } catch (Exception $e) {
            $retorno->erro = $e->getMessage();
        }

        return $retorno;
    }

    public function enviaNotificacao($destEmail,
                                    $destSms,
                                    $dadosRepInst,
                                    $dadosCliente,
                                    $dadosAgendaOS,
                                    $arrayPropriedades,
                                    $regras) {


        $retorno  = false;
        $objEnvio = new ComunicacaoEmailsSMS();
        $ordoid   = isset($dadosAgendaOS['osaordoid']) ? $dadosAgendaOS['osaordoid'] : NULL;
        $tags     = $this->substituirTags( $dadosRepInst, $dadosCliente, $dadosAgendaOS, $arrayPropriedades);

        // Envia notifiação para o representante atual
        if(isset($dadosRepInst->dadosRepresentante) && isset($regras['layout_tec_prestador']['seetdescricao']) ) {

            $dadosRegistro = array(
                'seetdescricao' => array("value" => $regras['layout_tec_prestador']['seetdescricao'], "condition" => "="),
                'seefdescricao' => array("value" => $regras['layout_tec_prestador']['seefdescricao'], "condition" => "="),
                'seedt_exclusao' => array("value" => 'NULL', "condition" => "IS")
            );

            $dadosLayout = $this->dao->buscaLayout($dadosRegistro);

            if(isset($dadosLayout->erro)) {
                throw new Exception($dadosLayout->erro);
            }

            //Envia e-mail para o representante
            if($destEmail != false && in_array('R', $destEmail)) {
                $email = $dadosRepInst->dadosRepresentante['repe_mail'];
                echo "\nE-mail: ".  $email;
                if($objEnvio->enviaEmailSMSMensageria($tags, $dadosLayout->resultado['EMAIL'], $email, NULL, $ordoid)) {
                    $retorno = true;
                }
            }
            // Envia SMS para o representante
            if($destSms != false && in_array('R', $destSms)) {
                $fone = preg_replace( '/[^0-9]/', '', $dadosRepInst->dadosRepresentante['endvddd'].$dadosRepInst->dadosRepresentante['endvfone']);
                echo "\n SMS: ". $fone;
                if($objEnvio->enviaEmailSMSMensageria($tags, $dadosLayout->resultado['SMS'], NULL, $fone, $ordoid)) {
                    $retorno = true;
                }
            }
        }

        // Envia notificação para o instalador atual
        if(isset($dadosRepInst->dadosInstalador) && isset($regras['layout_tec_prestador']['seetdescricao']) ) {

            $dadosRegistro = array(
                'seetdescricao' => array("value" => $regras['layout_tec_prestador']['seetdescricao'], "condition" => "="),
                'seefdescricao' => array("value" => $regras['layout_tec_prestador']['seefdescricao'], "condition" => "="),
                'seedt_exclusao' => array("value" => 'NULL', "condition" => "IS")
            );

            $dadosLayout = $this->dao->buscaLayout($dadosRegistro);

            if(isset($dadosLayout->erro)) {
                throw new Exception($dadosLayout->erro);
            }

            // Envia e-mail para tecnico
            if($destEmail != false && in_array('T', $destEmail)) {
                $email = $dadosRepInst->dadosInstalador['itlemail'];
                echo "\nE-mail: ".  $email;
                if($objEnvio->enviaEmailSMSMensageria($tags, $dadosLayout->resultado['EMAIL'], $email, NULL, $ordoid)) {
                    $retorno = true;
                }
            }

            // Envia SMS para o técnico
            if($destSms != false && in_array('T', $destSms)) {
                $fone = preg_replace( '/[^0-9]/', '',$dadosRepInst->dadosInstalador['itlfone_sms']);
                echo "\n SMS: ". $fone;
                if($objEnvio->enviaEmailSMSMensageria($tags, $dadosLayout->resultado['SMS'], NULL, $fone, $ordoid)) {
                    $retorno = true;
                }
            }
        }

        // Envia e-mail para cliente
        if(isset($dadosCliente->resultado)) {

            $dadosOS    = $this->ordemServicoClass->recuperarDadosOrdemServico(array('ordconnumero'), "WHERE ordoid      = " . $dadosAgendaOS['osaordoid']);
            $contrato   = new Contrato();
            $nomeLayout = $contrato->isContratoSiggo(  $dadosOS[0]['ordconnumero'] ) ? 'layout_siggo' : 'layout_sascar';

            $dadosRegistro = array(
                'seetdescricao' => array("value" => $regras[$nomeLayout]['seetdescricao'], "condition" => "="),
                'seefdescricao' => array("value" => $regras[$nomeLayout]['seefdescricao'], "condition" => "="),
                'seedt_exclusao' => array("value" => 'NULL', "condition" => "IS")
            );

            $dadosLayout = $this->dao->buscaLayout($dadosRegistro);

            if(isset($dadosLayout->erro)) {
                throw new Exception($dadosLayout->erro);
            }

            // Envia e-mail para cliente
            if($destEmail != false && in_array('C', $destEmail)) {
                $email = $tags["EMAIL_CLIENTE"];
                echo "\nE-mail: ".  $email;
                if($objEnvio->enviaEmailSMSMensageria($tags, $dadosLayout->resultado['EMAIL'], $email, NULL, $ordoid)) {
                    $retorno = true;
                }
            }

            // Envia SMS para o técnico
            if($destSms != false && in_array('C', $destSms)) {
                $fone = preg_replace( '/[^0-9]/', '',$tags["CELULAR_CLIENTE"]);
                echo "\n SMS: ". $fone;
                if($objEnvio->enviaEmailSMSMensageria($tags, $dadosLayout->resultado['SMS'], NULL, $fone, $ordoid)) {
                    $retorno = true;
                }
            }
        }

        return $retorno;
    }

    private function substituirTags($dadosRepInst, $dadosCliente, $dadosAgendaOS, $arrayPropriedades){

        $tags = array();
        $tags["DATA_SISTEMA"]     = date("d/m/Y");
        $tags["HORA_SISTEMA"]     = date("H:i");
        $tags["TECNICO_DESTINO"]  = isset($dadosRepInst->dadosInstalador['itlnome']) ? $dadosRepInst->dadosInstalador['itlnome'] : NULL;
        $tags["REPRESENTANTE"]    = isset($dadosRepInst->dadosRepresentante['repnome']) ? $dadosRepInst->dadosRepresentante['repnome'] : NULL;
        $tags["OS"]               = isset($dadosAgendaOS['osaordoid']) ? $dadosAgendaOS['osaordoid'] : NULL;
        $tags["TIPO_OS"]          = isset($dadosAgendaOS['ostdescricao']) ? $dadosAgendaOS['ostdescricao'] : NULL;
        $tags["TECNICO"]          = isset($dadosRepInst->dadosInstalador['itlnome']) ? $dadosRepInst->dadosInstalador['itlnome'] : NULL;
        $tags["HORA_INICIO"]      = date("H:i", strtotime($dadosAgendaOS['osahora']));
        $tags["HORA_FIM"]         = date("H:i", strtotime($dadosAgendaOS['osahora'] . " +2 hours"));
        $tags["PLACA"]            = isset($dadosAgendaOS['osaplaca']) ? $dadosAgendaOS['osaplaca'] : NULL;
        $tags["DATA_AGENDAMENTO"] = date("d/m/Y", strtotime($dadosAgendaOS['osadata']));
        $tags["HORA_AGENDAMENTO"] = date("H:i", strtotime($dadosAgendaOS['osahora']));
        $tags["TIME_SLOT"]        = $tags["HORA_INICIO"];
        $tags["MOTIVO"]           = NULL;
        $tags["OBSERVACAO"]       = NULL;

        if( !is_null($dadosCliente) ) {
            $tags["EMAIL_CLIENTE"]    = isset($dadosCliente->resultado['osecemail']) ? $dadosCliente->resultado['osecemail'] : NULL;
            $tags["CELULAR_CLIENTE"]  = isset($dadosCliente->resultado['oscccelular']) ? $dadosCliente->resultado['oscccelular'] : NULL;
        }

        if(isset($dadosAgendaOS['osatipo_atendimento']) && $dadosAgendaOS['osatipo_atendimento'] == 'M') {
            $tags["TIME_SLOT"] = $tags["HORA_INICIO"] . ' às ' . $tags["HORA_FIM"];
        }

        //Motivo
        if( isset($arrayPropriedades["XA_CANCELATION_REASON_LABEL"]) ) {

             $tags["MOTIVO"] = $arrayPropriedades["XA_CANCELATION_REASON_LABEL"];

        } else if ( isset($arrayPropriedades["XA_SUSPENSION_REASON_LABEL"]) ) {

            $tags["MOTIVO"] = $arrayPropriedades["XA_SUSPENSION_REASON_LABEL"];

        } else if ( isset($arrayPropriedades["XA_NOTDONE_REASON_LABEL"]) ) {

            $tags["MOTIVO"] = $arrayPropriedades["XA_NOTDONE_REASON_LABEL"];

        }

        //OBSERVACAO
        if( isset($arrayPropriedades["XA_CANCELATION_NOTES"]) ) {

             $tags["OBSERVACAO"] = $arrayPropriedades["XA_CANCELATION_NOTES"];

        } else if ( isset($arrayPropriedades["XA_SUSPENSION_NOTES"]) ) {

            $tags["OBSERVACAO"] = $arrayPropriedades["XA_SUSPENSION_NOTES"];

        } else if ( isset($arrayPropriedades["XA_NOTDONE_NOTES"]) ) {

            $tags["OBSERVACAO"] = $arrayPropriedades["XA_NOTDONE_NOTES"];

        }

        return $tags;
    }

    public function cancelaAtividade($dadosAgendamento) {

        $retorno = new stdClass();
        $dataAgendada = strtotime($dadosAgendamento['osadata']);
        $dataAtual = strtotime(date('Y-m-d'));
        $isDataPassada = ($dataAgendada < $dataAtual) ? true : false;

        echo "\nCancelando atividade. Dados do agendamento: ";
        echo '<pre>'; var_dump($dadosAgendamento); echo '</pre>';

        if(isset($dadosAgendamento['osaid_atividade']) && trim($dadosAgendamento['osaid_atividade']) != '') {

            $objActivity = new Activity();
            $objActivity->setActivityId( $dadosAgendamento['osaid_atividade'] );
            $statusAtividade = $objActivity->ConsultarAtividade();

            if( isset($statusAtividade->error_msg)) {
                $retorno->erro = $statusAtividade->error_msg;
            }

            echo "\nStatus da atividade: ";
            echo '<pre>'; var_dump($statusAtividade); echo '</pre>';


            //caso não esteja cancelado ou estiver com status pendente
            if( (isset($statusAtividade->status)) && ($statusAtividade->status == 'pending') && !$isDataPassada ) {

                $res = $objActivity->cancelarAtividade();
                $retorno = $res;

                if( isset($res->error_msg)) {
                    $retorno->erro = $res->error_msg;
                }
            }

        } else {
            echo "\nAgendamento nao possui id da atividade\n";
        }

        echo '<pre>Retorno cancelamento:'; var_dump($retorno); echo '</pre>';

        return $retorno;
    }

    public function liberaReservaEstoque( $ordoid, $osaoid, $isCancelar ) {

        $resultado = false;
        $estoqueAgenda = new EstoqueAgenda($this->conn );
        $estoqueAgenda->setNumeroOrdemServico($ordoid);
        $estoqueAgenda->setAgendamentoID($osaoid);

        if($isCancelar) {
            $resultado = $estoqueAgenda->setCancelarReserva();
        } else {
            $resultado = $estoqueAgenda->setProdutoInstalado();
        }

        return $resultado;
    }

    public function cancelarSolicitacao($ordoid,$osaoid) {

        $estoqueAgenda = new EstoqueAgenda($this->conn );
        $estoqueAgenda->setNumeroOrdemServico($ordoid);
        $estoqueAgenda->setAgendamentoID($osaoid);

        return $estoqueAgenda->setCancelarSolicitacaoProduto();
    }

    public function gravaImagens($arrayPropriedades) {

        $listaImagens = array();

        //$property_type = array('XA_CUST_SIGNATURE', 'XA_CUST_SIGNATURE_MISUSE_FORM');
        $property_type = array('XA_CUST_SIGNATURE');

        $os = $arrayPropriedades['XA_WO_NUMBER'];
        $aid = $arrayPropriedades['aid'];
        $diretorio = $this->getDiretorioImagemAssinatura();
        $objActivity = new Activity();
        $objActivity->setActivityId($aid);

        for($i = 0; $i < sizeof($property_type); $i++) {

            $objActivity->setPropertyLabel( $property_type[$i] );
            $file = $objActivity->getFile();

            if(isset($file->error_msg)) {
                echo "<br/>\n Erro ao recuperar imagem do OFSC: " . $file->error_msg . "\n";
                continue;
            }

            $fullpath = $diretorio.$os.'_'.$aid. date('His') .'.jpg';
            $fp = fopen($fullpath,'x+');
            fwrite($fp, $file);
            fclose($fp);

            $listaImagens[$i]['ordem_servico']   = $os;
            $listaImagens[$i]['tipo_anexo']   = self::TIPO_ARQUIVO_ASSINATURA;
            $listaImagens[$i]['nome_arquivo'] = $os.'_'.$aid. date('His') .'.jpg';
            $listaImagens[$i]['obs_arquivo'] = 'Assinatura digital do cliente';
            $listaImagens[$i]['id_usuario']   = $this->idUsuario;

        }

        return $listaImagens;

    }

   public function atualizarAtividadeOFSC($idAtividade, $propriedades) {

        echo "\nEnvia update da atividade para o OFSC\n";

        $retorno = new stdClass();
        $activityClass  = new Activity();

        try
        {

            $activityClass->setActivityId( $idAtividade );

            foreach ($propriedades as $chave => $valor) {
                $activityClass->setPropriedades($chave, $valor);
            }

            $resposta = $activityClass->atualizarAtividade();

            if( isset($resposta->error_msg)) {
                $retorno->erro = $resposta->error_msg;

            }

        } catch (Exception $ex) {
            $retorno->erro = $e->getMessage();
        }

        return $retorno;
    }

    public function enviaStatusMensagemLoteOracle($mensagens) {

        $resposta = NULL;

        try {

            $resposta = $this->outBound->retornoLote($mensagens);

            if(is_array($resposta) && isset($resposta['error_msg'])) {
                throw new Exception($resposta['error_msg']);
            }

        } catch (Exception $e) {
            echo "\n" . $e->getMessage() . "\n";
        }

        echo "<pre>";var_dump($resposta);echo "</pre>";
    }

    private function gravaLogErro($info) {
        $arquivo = $this->getDiretorioLog().$this->getNomeArquivoLog();

        $fp = fopen($arquivo, "a+");
        $conteudo = date("H:i:s") . "\n";
        $conteudo .= "CRON: " . $this->getNomeRotina() . "\n";
        $conteudo .= "ERRO: ";

        if(strlen($this->getErro()) > 0) {
            $conteudo .=  $this->getErro() . "\n";
        }

        if(isset($info->erro) && strlen($info->erro) > 0) {
            $conteudo .= (string) $info->erro ."\n";
        }

        if(isset($info->sql)) {
            $conteudo .= "QUERY: " . $info->sql . "\n";
        }

        $conteudo .= "_____________________________________________\n";

        fwrite($fp, $conteudo);

        fclose($fp);
    }

    /**
     * Error handler
     * @param  [integer]    $errno   [contém o nível de erro que aconteceu]
     * @param  [string]     $errstr  [contém a mensagem de erro]
     * @param  [string]     $errfile [contém o nome do arquivo no qual o erro ocorreu]
     * @param  [integer]    $errline [contém o número da linha na qual o erro ocorreu]
     * @return [type]
     */
    public function errorHandler($errno, $errstr, $errfile, $errline) {

        switch ($errno) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_COMPILE_WARNING:
            case E_USER_ERROR:
            case E_WARNING:
            case E_USER_WARNING:
            case E_PARSE:
                $erro = "[$errno] $errstr<br />\n";
                $erro .= "Error on line $errline in file $errfile \n";
                $this->setErro($erro);
                break;

            case E_USER_NOTICE:
                $erro = "[$errno] $errstr<br />\n";
                $erro .= "Error on line $errline in file $errfile \n";
                echo $erro;
            break;

            default:
                $erro = "[$errno] $errstr<br />\n";
                $erro .= "Error on line $errline in file $errfile \n";
                echo $erro;
                break;
        }

        /* Don't execute PHP internal error handler */
        return true;
    }

}

?>

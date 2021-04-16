<?php

require_once _MODULEDIR_ . 'Cron/Action/CronAction.php';
require_once _MODULEDIR_ . 'Cron/DAO/VerificaRegraTrcDAO.php';
require_once _SITEDIR_ . 'lib/funcoes.php';

class VerificaRegraTrc extends CronAction {

    /**
     * Instancia objeto DAO com a conexão do BD
     */
    public function __construct() {
        global $conn;

        $this->dao = new VerificaRegraTrcDAO($conn);
    }

    /**
     * Função principal da classe
     * Busca ordens de serviço válidas e envia email e sms conforme regra TRC (verificar ES)
     */
    public function validarRegraTrc() {

        $ordensServicoValidas = $this->buscarOrdensServicoValidas();

        foreach ($ordensServicoValidas as $ordemServico) {
            $emails = array();
            $telefones = array();

            $emails = $this->dao->buscarEmails($ordemServico);
            $telefones = $this->dao->buscarTelefones($ordemServico);

            $templateEmail = $this->buscarTemplate($ordemServico, "EMAIL");
            $templateSms = $this->buscarTemplate($ordemServico, "SMS");

            $mensagemDepuracao =  "OS: " . $ordemServico->ordoid . "\n";
            $mensagemDepuracao .= "Tipo do contrato: " . $ordemServico->tipo_contrato . "\n";
            $mensagemDepuracao .= "Dias após conclusão: " . $ordemServico->diasaposconclusao . "\n";
            if(count($emails) > 0) {
                if(!isset($templateEmail->seecorpo)) {
                    $mensagemDepuracao .= "Não foi possível notificar o cliente via Email pois não foi encontrado template válido.\n";
                } else {
                    $this->enviarEmails($templateEmail, $emails);
                    $mensagemDepuracao .= "Email enviado com sucesso para o(s) email(s): " . implode(", ", $emails) . ".\n";
                }
            } else {
                $mensagemDepuracao .= "Não foi possível notificar o cliente via Email pois não foi encontrado email válido.\n";
            }

            if(count($telefones) > 0) {
                if(!isset($templateSms->seecorpo)) {
                    $mensagemDepuracao .= "SMS não foi enviado para o(s) número(s) (" . implode(", ", $telefones) . ") pois nenhum template foi encontrado.\n";

                    $templateSms = new stdClass();
                    $templateSms->seecorpo = "SMS não foi enviado para o(s) número(s) (" . implode(", ", $telefones) . ") pois nenhum template foi encontrado.";
                    
                    $statusEnvio = 'I';
                    
                } else {
                    $mensagemEnvioSms = $this->formatarSms($templateSms->seecorpo, $ordemServico->ordconnumero);
                    $this->enviarMensagensSms($mensagemEnvioSms, $telefones);

                    $templateSms->seecorpo = "SMS enviado para: " . implode(", ", $telefones) . " --> " . $mensagemEnvioSms;

                    $statusEnvio = 'S';
                    $mensagemDepuracao .= "SMS enviado para: " . implode(", ", $telefones) . " --> " . $mensagemEnvioSms . "\n";

                }
            } else {
                $mensagemDepuracao .= "Não foi possível notificar o cliente via SMS pois não foi encontrado telefone válido.\n";

                $templateSms = new stdClass();
                $templateSms->seecorpo = "Não foi possível notificar o cliente via SMS pois não foi encontrado telefone válido.";

                $statusEnvio = 'I';
            }
            if($this->dao->salvarHistoricoSmsEnvio($ordemServico, $templateSms, $statusEnvio)){
                $mensagemDepuracao .= "Histórico de envio de SMS gravado com sucesso\n\n";
            } else {
                $mensagemDepuracao .= "Não foi possível gravar o histórico do envio de SMS\n\n";
            }

            echo $mensagemDepuracao;
        }
    }

    /**
     * Busca Ordens de serviço válidas conforme regra TRC
     *
     * @return array
     */
    private function buscarOrdensServicoValidas() {
        return $this->dao->buscarOrdensServicoValidas();
    }

    /**
     * Busca template adequado a determinada OS
     *
     * @param object $ordemServico Dados da ordem de serviço
     * @param string $tipoTemplate Tipo do template a buscar: 'SMS' ou 'EMAIL'
     * 
     * @return object
     */
    private function buscarTemplate($ordemServico, $tipoTemplate) {
        $tipoContrato = $this->dao->buscarTipoContrato($ordemServico->ordconnumero);

        $diasAposConclusao = $ordemServico->diasaposconclusao;
        $idTituloLayout = $this->buscarIdTituloLayout($tipoContrato->tpcdescricao, $diasAposConclusao);
        
        $idFuncionalidadeLayout = $this->buscarIdFuncionalidadeLayout("Ordem de Serviço");

        $template = $this->dao->buscarDadosTemplate($idTituloLayout, $idFuncionalidadeLayout, $tipoTemplate);

        return $template;
    }

    /**
     * Formata o título do layout conforme parametros recebidos
     *
     * @param string $descricao Descrição do tipo do contrato
     * @param int $dias Quantidade de dias após conclusão da OS
     * 
     * @return string
     */
    private function formataTituloLayout($descricao, $dias) {
        if (strtoupper($descricao) == "VIVO") {
            $tituloLayout = "Regra TRC Vivo " . $dias;
        } else if (strtoupper($descricao) == "SIGGO") {
            $tituloLayout = "Regra TRC Siggo " . $dias;
        } else {
            $tituloLayout = "Regra TRC Sascar " . $dias;
        }
        return $tituloLayout;
    }

    /**
     * Busca id do título do layout conforme parâmetros recebidos
     *
     * @param string $descricao Descrição do tipo do contrato
     * @param int $dias Quantidade de dias após conclusão da OS
     * 
     * @return object
     */
    private function buscarIdTituloLayout($descricao, $dias) {
        $tituloLayout = $this->formataTituloLayout($descricao, $dias);

        return $this->dao->buscarIdTituloLayout($tituloLayout);
    }

    /**
     * Busca id da funcionalidade do layout conforme parâmetros recebidos
     *
     * @param string $funcionalidade Descrição da funcionalidade do contrato
     * 
     * @return object
     */
    private function buscarIdFuncionalidadeLayout($funcionalidade) {
        return $this->dao->buscarIdFuncionalidadeLayout($funcionalidade);
    }

    /**
     * Envia email para todos os emails cadastrados para determinada OS
     *
     * @param object $template Dados do template
     * @param array $emails Emails cadastrados para determinada OS
     */
    private function enviarEmails($templateEmail, $emails) {
        foreach ($emails as $email) {
            enviaEmail($email, $templateEmail->seecabecalho, $templateEmail->seecorpo, null, $templateEmail->seeremetente);
        }
    }

    /**
     * Envia SMS para todos os telefones cadastrados para determinada OS
     *
     * @param object $template Dados do template
     * @param array $telefones Telefones cadastrados para determinada OS
     */
    private function enviarMensagensSms($corpoSms, $telefones) {
        foreach ($telefones as $telefone) {
            enviaSms($telefone, $corpoSms);
        }
    }

    function formatarSms ($template, $idContrato) {
        $tipoContrato = $this->dao->buscarTipoContrato($idContrato);
        $descricao = $tipoContrato->tpcdescricao;

        if (strtoupper($descricao) == "VIVO") {
            $msgSms = "Vivo informa: " . preg_replace("/\[contrato\]/", $idContrato, $template);
        } else if (strtoupper($descricao) == "SIGGO") {
            $msgSms = "Siggo informa: " . preg_replace("/\[contrato\]/", $idContrato, $template);
        } else {
            $msgSms = "Sascar informa: " . preg_replace("/\[contrato\]/", $idContrato, $template);
        }

        return $msgSms;
    }

}
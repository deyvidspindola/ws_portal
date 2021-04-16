<?php

require_once _CRONDIR_ . 'lib/validaCronProcess.php';
require_once _MODULEDIR_ . 'Cron/Action/CronAction.php';
include _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';

/**
 *
 */
class VerificarIntervencaoCorretor {

    /**
     * Objeto DAO da classe.
     *
     * @var CadExemploDAO
     */
    private $dao;
    private $os;

    /**
     * Método construtor.
     *
     * @param CadExemploDAO $dao Objeto DAO da classe
     */
    public function executar($dao) {


        $this->dao = $dao;
        $this->notificarOsRetirada();
    }

    public function notificarOsRetirada() {


        $this->os = $this->dao->buscarOS();

        if (count($this->os)) {
            foreach ($this->os as $os) {
                $emailsNotificados = $this->notificarPorEmail($os);
                if ($emailsNotificados !== false) {
                    $this->registrarHistoricoEnvioEmail($emailsNotificados, $os->ordoid);
                    echo date('Y-m-d H:i:s') . ' - OS: ' . $os->ordoid . ' / PLACA: ' . $os->placa . " - Notificação realizada com sucesso.\n";
                } else if ($emailsNotificados === false) {
                    echo date('Y-m-d H:i:s') . ' - OS: ' . $os->ordoid . ' / PLACA: ' . $os->placa . " - Falha ao enviar e-mail.\n";
                } else {
                    echo date('Y-m-d H:i:s') . ' - OS: ' . $os->ordoid . ' / PLACA: ' . $os->placa . " - E-mail do corretor não informado.\n";
                }
            }
        }
    }

    public function notificarPorEmail(stdClass $os) {

        $mail = new PHPMailer();

        $mail->IsSMTP();
        $mail->From = "intervencaoretirada@sascar.com.br";
        $mail->FromName = "SASCAR";
        $mail->Subject = "Intervenção Corretor - O.S de Retirada Pendente (" . $os->placa . ")";
        $mail->MsgHTML($this->montarConteudoEmailNotificacao($os));
        $mail->ClearAllRecipients();

        if ($_SESSION['servidor_teste']) {
            $mail->AddAddress('teste_desenv@sascar.com.br');
        } else {
            $mail->AddAddress($os->email_corretor);
        }

        if ($mail->Send()) {

            $emails_noticados[] = $os->email_corretor;
        }

        if ($os->tipo_contrato_id == '41' /* && !empty($os->email_sucursal) */) {

            $os->email_sucursal = $this->dao->buscarEmailSucursal($os->proposta);

            if (!empty($os->email_sucursal)) {
                if ($_SESSION['servidor_teste']) {
                    $mail->AddAddress('teste_desenv@sascar.com.br');
                } else {
                    $mail->AddAddress($os->email_sucursal);
                }

                if ($mail->Send()) {

                    $emails_noticados[] = $os->email_sucursal;
                }
            }

            $seguradoraBradesco = $this->dao->buscarEmailSeguradoraBradesco();
            
            if ($seguradoraBradesco->tpcenvia_email_intervencao == 't') {

                if (!empty($seguradoraBradesco->tpccopia_email_intervencao)){

                    if ($_SESSION['servidor_teste']) {
                        $mail->AddAddress('teste_desenv@sascar.com.br');
                    } else {
                        $mail->AddAddress($seguradoraBradesco->tpccopia_email_intervencao);
                    }

                    if ($mail->Send()) {
                        $emails_noticados[] = $seguradoraBradesco->tpccopia_email_intervencao;
                    }
                }
            }
        }

        if (count($emails_noticados) > 0) {
            return $emails_noticados;
        } else {
            return false;
        }

    }

    public function registrarHistoricoEnvioEmail($emailsNotificados, $ordoid) {

        $parametros = new stdClass();

        //Ação para inserir no histórico da OS: Intervenção Corretor (código 66);
        $parametros->orsstatus = 66;

        //Texto para inserir no histórico da OS: Intervenção Corretor Enviada aos destinatários: [e-mail_ destinatários];
        $parametros->orssituacao = 'Intervenção Corretor Enviada aos destinatários:' . implode(', ', $emailsNotificados);

        //usuario da automático
        $parametros->orsusuoid = 2750;

        $this->dao->registrarHistoricoOs($parametros, $ordoid);
    }

    public function montarConteudoEmailNotificacao(stdClass $os) {

        $conteudo = "<p><img src='" . _PROTOCOLO_ . "http://www.sascar.com.br/images/logo.png' border='0'></p>
				<p>Prezado(a) Corretor(a)</p>
				<p>Fomos sinalizados via sistema que o veículo abaixo descrito necessita de retirada do equipamento de rastreamento.</p>
				<p>Informamos que nas tentativas de contato para agendamento, não conseguimos a disponibilização do veiculo. Sendo assim, solicitamos a vossa intervenção afim de que o mesmo retorne a ligação a SASCAR para agilizarmos a retirada o mais breve possível.</p>
				<p>Telefone para atendimento:<br/>
                   0300 788 6004 (todas as localidades) <br/>
                   Atendimento de segunda a sexta-feira das 08:00 às 20:00 horas <br/>
                   sábados das 09:00 às 15:00 horas.</p>
				<p>Obs.: Favor enviar e-mail de retorno nos dando um posicionamento.</p>

				<table border='1' style='width:600px'>
				<tr><td colspan='2' ><b>Dados do Veículo / Segurado</b></td></tr>
				<tr>
                    <td style='width:100px;'>Segurado:</td>
                    <td>&nbsp;" . $os->segurado . "</td>
				</tr>
				<tr>
                    <td style='width:100px;'>Veículo:</td>
                    <td>&nbsp; " . $os->veiculo . "</td>
				</tr>
				<tr>
                    <td style='width:100px;'>Placa:</td>
                    <td>&nbsp;" . $os->placa . "</td>
				</tr>
				<tr>
                    <td style='width:100px;'>Chassi:</td>
                    <td>&nbsp;" . $os->chassi . "</td>
				</tr>
				<tr>
                    <td style='width:100px;'>Proposta:</td>
                    <td>&nbsp;" . $os->proposta . "</td>
				</tr>
				<tr>
                    <td style='width:100px;'>Sucursal:</td>
                    <td>&nbsp;" . $os->sucursal . "</td>
				</tr>
				<tr>
                    <td style='width:100px;'>Apólice:</td>
                    <td>&nbsp;" . $os->apolice . "</td>
				</tr>
				<tr>
                    <td style='width:100px;'>Item:</td>
                    <td>&nbsp;" . $os->item_apolice . "</td>
				</tr>
				<tr>
                    <td style='width:100px;'>Motivo:</td>
                    <td>&nbsp;" . $os->motivo . "</td>
				</tr>
				</table>
				<p>Desde já agradecemos.</p>
				<br><br>";

        return $conteudo;
    }

}


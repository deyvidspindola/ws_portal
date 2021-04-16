<?php

/**
 * Classe padrão para envio de emails
 */
require _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';
require _SITEDIR_ . 'lib/Atom/Config/Sistema.php';

/**
 * @author ricardo.marangoni
 */
class CrnSugestaoReclamacao {

    private $dao;

    public function __construct($conn) {
        $this->dao = new CrnSugestaoReclamacaoDAO($conn);
    }

    /**
     * 
     */
    public function buscarOcorrenciasOuvidoria() {

        return $this->dao->buscarOcorrenciasOuvidoria();
    }

    public function validarDadosEnvio($ocorrencia) {
        if ( $ocorrencia['csugnome_contato'] 
                && filter_var($ocorrencia['csugemail_contato'], FILTER_VALIDATE_EMAIL) ) {
            return true;
        }
        return false;
    }

    public function enviarEmail($ocorrencia) {

        //array com os destinatarios do email
        if ($_SESSION['servidor_teste'] == 1) {
            $lista_email = array(_EMAIL_TESTE_, "wagner.pereira@sascar.com.br");
        } else {
            $lista_email = array($ocorrencia['csugemail_contato']);
        }

        $mail = new PHPMailer();
        $mail->ClearAllRecipients();

        $mail->IsSMTP();
        $mail->From = "sistema@sascar.com.br";
        $mail->FromName = "Intranet SASCAR - E-mail automático";
        $mail->Subject = $ocorrencia['seecabecalho'];

        $mensagem = "
            A/C<br />
            Sr(a) [CLIENTE]<br />
            " . $ocorrencia['seecorpo'] . "         
            Ocorrência de N. [N.O] registrada no dia [DIA] do mês [MES] de [ANO].<br /><br />            
            
            Mensagem original:<br /><br />
            [MENSAGEM ORIGINAL]<br />
        ";

        $mail->MsgHTML($this->substituirVariaveis($mensagem, $ocorrencia));

        //adiciona os destinatarios
        foreach ($lista_email as $destinatarios) {
            $mail->AddAddress($destinatarios);
        }

        return $mail->Send();
    }

    private function substituirVariaveis($string, $ocorrencia) {

        $patterns = array();
        $patterns[0] = '[\[CLIENTE\]]';
        $patterns[1] = '[\[N\.O\]]';
        $patterns[2] = '[\[DIA\]]';
        $patterns[3] = '[\[MES\]]';
        $patterns[4] = '[\[ANO\]]';
        $patterns[5] = '[\[MENSAGEM ORIGINAL\]]';
        
        $replacements = array();
        $replacements[5] = $ocorrencia['csugnome_contato'];
        $replacements[4] = $ocorrencia['csugoid'];
        $replacements[3] = date('d');
        $replacements[2] = date('m');
        $replacements[1] = date('Y');
        
        $mensagemOriginal = preg_replace($patterns, $replacements, $ocorrencia['csugdescricao']);
        
        $replacements[0] = $mensagemOriginal;
        
        return preg_replace($patterns, $replacements, $string);        
        
    }
    
    public function gravarLogEnvio($ocorrencia, $status) {
        
        $obs = "";
        
        switch ($status) {
            case 'S':
                $obs = "ENVIO DE E-MAIL AUTOMATICO ". $ocorrencia['seeobjetivo'] .": Enviado com sucesso.";
                break;
            case 'P':
                $obs = "ENVIO DE E-MAIL AUTOMATICO ". $ocorrencia['seeobjetivo'] .": Envio pendente";
                break;
            case 'E':
                $obs = "ENVIO DE E-MAIL AUTOMATICO ". $ocorrencia['seeobjetivo'] .": Houve erro no envio";
                break;            
        }
        
        $this->dao->gravarLogEnvio($ocorrencia['csugoid'], $obs);
        
    }
    
    public function atualizarDataEnvio($csugoid) {
       $this->dao->atualizarDataEnvio($csugoid);
    }

}
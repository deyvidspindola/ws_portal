<?php

/**
 * Classe de Envio de Email do processamento
 *
 * @file    enviarEmail.php
 * @author  BRQ
 * @since   07/08/2012
 * @version 07/08/2012
 * 
 * Exemplo de uso:
 * 
 * $conector = 2;
 * $dataHora = date("m/d/y H:i:s"); 
 * $resumo = "Foram enviados 50 clientes";
 * 
 * $email = new enviarEmail(2); // Conector 2
 * $email->cabecalhoResumo($dataHora, $resumo); // DataHora e o Resumo de envio/recebimento do conector
 * $arrCampos = array(15,  85662139, 'João Maria');
 * $email->addLinhaDetalhes($arrCampos); 
 * $email->enviar();
 * 
 */
#############################################################################################################
#   Histórico
#       10/07/2012 - Diego C. Ribeiro(BRQ)
#           Criação do arquivo - DUM 79924
#############################################################################################################

require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';
require_once 'funcoesEBS.php';

/**
 * Envio de Email do Processamento
 * 
 *  - O e-mail deverá ser enviado para os seguintes endereços:
 *      osmar.s@sascar.com.br
 *      felipe.beni@sascar.com.br
 * 
 * Após registrar o LOG, deverá ser enviado um e-mail para os endereços abaixo, 
 * mostrando o resultado da execução dos conectores. 
 * Neste email deverá constar o mesmo resultado que será armazenado no LOG:
 * 
 * Para todos os conectores:
 *  - Data/Hora da execução
 *  - Nome do conector e processo executado 
 *      (Ex; Conector WS1 ? Recebimento de Notas Fiscais de Saída)
 *  - Resumo do processamento
 *      Ex: 12 Notas Fiscais recebidas com sucesso na Intranet
 *          03 Notas Fiscais recebidas com erros que não puderam ser recebidas na Intranet
 * 
 * Para o Conector WS1:
 *  - Enviar o detalhamento do LOG mostrando quais notas foram recebidas 
 *      (entrada e saída), quais apresentaram erros e quais foram os erros 
 *      encontrados, conforme apresentado no protótipo 1.
 * 
 * Para o Conector WS2:
 * Neste e-mail, deverá ser apresentado o Numero do Pedido, o CNPJ e o NOME do cliente.
 * 
 * Para conectores WS3, e WS4:
 *  - Deverão ser enviados quais produtos e clientes/fornecedores foram enviados para o FOX 
 *  - Apresentar código e Nome
 * 
 */
class enviarEmail {

    /**
     * @var int Número do Conector (1,2,3 ou 4)
     */
    protected $conector = null;

    /**
     * @var string Código HTML com o cabeçalho do Resumo de Envio 
     */
    protected $cabecalhoResumo = null;

    /**
     * @var string Código HTML com o corpo do email
     */
    protected $corpoMensagem = null;

    /**
     * @var string Data e hora da execução do script
     */
    protected $dataHora = null;

    /**
     * @var string Descrição do conector na tabela (HTML) de resumo, também
     * é utilizada como assunto do email que será enviado
     */
    protected $msgConector = null;

    /**
     * Conteúdo dos vários emails a enviar
     * @var type 
     */
    protected $arrEmail = false;
        
    /**
     *
     * @var String que contém o email a ser enviado
     */
    public $msgEmail= false;

    /**
     * Método Construtor, seta o número do conector
     * 
     * @param int $conector
     * @param datetime $dataHora
     * @param string $resumo 
     */
    public function __construct($conector = false, $emailErro = false, $emailMultiplo = false) {

        $this->conector = $conector;

        switch ($this->conector) {
            case 1:
                $this->msgConector = "Conector WS1";
                break;
            case 2: $this->msgConector = "Conector WS2 Envio de Pedidos de Venda";
                break;
            case 3: $this->msgConector = "Conector WS3 Envio de Clientes e Fornecedores";
                break;
            case 4: $this->msgConector = "Conector WS 4 Envio de Produtos";
                break;
            default:
                break;
        }

        // Limita a estrutura a um único cabeçalhoResumo e cabeçalhoDetalhes
        if ($emailMultiplo === false) {

            if ($emailErro === false) {
                // Cria o cabeçalho da Tabela de Detalhes do Envio
                $this->cabecalhoDetalhes();
            } else {
                // Cria o cabeçalho da Tabela de Detalhes do Envio dos Erros
                $this->cabelhoDetalhesEmailErro();
            }

            // Possui várias estruturas de tabelas, para vários 'envios' aglutinados
            // Todo o processo para gerar o cabeçalho e os detalhes será de forma manual
        } else {
            $emailMultiplo = true;
        }
    }

    public function __get($name) {
        return $this->$name;
    }

    public function __set($name, $value) {
        if ($name == 'arrEmail') {
            $this->arrEmail[] = $value;
            
        }else if($name == 'msgConector'){
            $this->msgConector = $value;
        }
    }

    public function cabelhoDetalhesEmailErro() {

        $this->corpoMensagem .= "<table class=\"bordaum\">
                                    <tr>
                                        <td colspan='3' class='detalhes'>Detalhes</td>
                                    </tr>
                                    <tr>
                                        <th>Pedido</th>
                                        <th>CPF/CNPJ</th>
                                    </tr>";
    }

    /**
     * Seta na variável $this->corpoMensagem  o cabeçalho da 'tabela de detalhes'
     * de acordo com o conector selecionado
     */
    public function cabecalhoDetalhes() {

        switch ($this->conector) {
            case 1: $this->corpoMensagem = "<table class=\"bordaum\">
                                        <tr>
                                            <td colspan='3' class='detalhes'>Detalhes</td>
                                        </tr>
                                        <tr>
                                            <th>Nota Fiscal</th>
                                            <th>Serie</th>
                                            <th>Status do Processamento</th>
                                        </tr>";
                break;

            case 2: $this->corpoMensagem = "<table class=\"bordaum\">
                                        <tr>
                                            <td colspan='3' class='detalhes'>Detalhes</td>
                                        </tr>
                                        <tr>
                                            <th width='100' align='center'>Nº do Pedido</th>
                                            <th width='150'>CPF/CNPJ</th>
                                            <th>Nome do Cliente</th>
                                        </tr>";
                break;

            case 3: $this->corpoMensagem = "<table class=\"bordaum\">
                                         <tr>
                                            <td colspan='3' class='detalhes'>Detalhes</td>
                                        </tr>
                                        <tr>
                                            <th>Cliente/Fornecedor</th>
                                            <th>Tipo do Cadastro</th>
                                            <th>CpfCnpj</th>
                                        </tr>";
                break;

            case 4: $this->corpoMensagem = "<table class=\"bordaum\">
                                        <tr>
                                            <td colspan='2' class='detalhes'>Detalhes</td>
                                        </tr>
                                        <tr>
                                            <th>Código Produto</th>
                                            <th>Descrição</th>
                                        </tr>";
                break;
        }
    }

    /**
     * Seta o cabeçalho da Tabela de resumo do Processamento de cada Conector
     * @param type $dataHora
     * @param type $resumo 
     */
    public function cabecalhoResumo($dataHora, $resumo, $tipoNota = "") {

        $this->cabecalhoResumo = "
                            <style type=\"text/css\">
                                .bordaum {background:#FFFFFF; width:700px; border:1px solid #000000}
                                .bordaum th {background:#A2B5CD; font-size:12px; font-family:Arial}
                                .bordaum td {background:#FFFFFF border:3px solid #000000; font-size:12px; font-family:Arial}
                                .detalhes {background:#C6E2FF; text-align:center; font-size:12px; font-family:Arial}
                            </style>
                
                            <table class=\"bordaum\">
                              <tr>
                                <th>Conector</th>
                                <th>Data/hora execução</th>";

        switch ($tipoNota) {
            case 'entrada': $this->cabecalhoResumo .= "<th>Recebimento de Notas Entrada</th>";
                break;
            case 'saida': $this->cabecalhoResumo .= "<th>Recebimento de Notas Saída</th>";
                break;
            case 'cancelamento_entrada': $this->cabecalhoResumo .= "<th>Recebimento de Cancelamento de Notas de Entrada </th>";
                break;
            case 'cancelamento_saida': $this->cabecalhoResumo .= "<th>Recebimento de Cancelamento de Notas de Saída</th>";
                break;
            default: $this->cabecalhoResumo .= "<th>Resumo Processamento</th>";
                break;
        }

        $this->cabecalhoResumo .= "
                                
                              </tr>
                              <tr>
                                <td>$this->msgConector</td>
                                <td>" . data_e_hora_to($dataHora, false) . "</td>
                                <td>$resumo</td>
                              </tr>
                            </table>";
    }

    /**
     * O protocolo do conector WS2 ? Pedido de Venda de numero XXXX, 
     *  gerado no dia XX/XX/XXXX às HH:MM:SS teve 
     *  erro ao ser processado pelo canal de Integração EBS.  
     */
    public function cabecalhoResumoEmailErro($protocolo, $dataHora, $resumo) {

        $this->cabecalhoResumo .= "
                            <style type=\"text/css\">
                                .bordaum {background:#FFFFFF; width:700px; border:1px solid #000000}
                                .bordaum th {background:#A2B5CD; font-size:12px; font-family:Arial}
                                .bordaum td {background:#FFFFFF border:3px solid #000000; font-size:12px; font-family:Arial}
                                .detalhes {background:#C6E2FF; text-align:center; font-size:12px; font-family:Arial}
                            </style>
                            <br>
                            <table class=\"bordaum\">
                              <tr>
                                <th>Protocolo</th>
                                <th>Data/hora execução</th>
                                <th>Erros</th>
                              </tr>
                              <tr>
                                <td>$protocolo</td>
                                <td>$dataHora</td>
                                <td>$resumo</td>
                              </tr>
                            </table><br>";
    }

    /*
     * Adiciona uma linha com os dados de Envio Detalhado de cada conector
     */

    public function addLinhaDetalhes($arrCampos) {

        if ($this->conector == 1 and count($arrCampos) != 3) {
            return "Atenção! O conector WS1 exige 3 campos para a tabela de detalhes, verifique.";
        } elseif ($this->conector == 2 and count($arrCampos) != 3) {
            return "Atenção! O conector WS2 exige 3 campos para a tabela de detalhes, verifique.";
        } elseif ($this->conector == 3 and count($arrCampos) != 3) {
            return "Atenção! O conector WS3 exige 3 campos para a tabela de detalhes, verifique.";
        } elseif ($this->conector == 4 and count($arrCampos) != 2) {
            return "Atenção! O conector WS4 exige 3 campos para a tabela de detalhes, verifique.";
        }

        $this->corpoMensagem .= "\n<tr>";
        foreach ($arrCampos as $value) {
            $this->corpoMensagem .= "<td>$value</td>";
        }
        $this->corpoMensagem .= "</tr>";
    }

    public function addLinhaDetalhesErro($arrCampos) {

        $this->corpoMensagem .= "<tr>";
        foreach ($arrCampos as $value) {
            $this->corpoMensagem .= "<td>$value</td>";
        }
        $this->corpoMensagem .= "</tr>";
    }

    public function fechaTabela() {
        $this->corpoMensagem .= "</table><br>";
    }

    /**
     * Envia os dados do envio do email
     * 
     */
    public function enviar() {
        
        $mensagem = "";
        
        // Envia o email de acordo com o conteúdo da variavel msgEmail
        if(isset($this->msgEmail) and $this->msgEmail != ''){
            $mensagem =  $this->msgEmail; 
                     
        // Array de mensagens para enviar num único email
        }else if ($this->arrEmail != false) {
            if (is_array($this->arrEmail) and count($this->arrEmail) > 0) {
                foreach ($this->arrEmail as $value) {
                    $mensagem .= '<br>' . $value;
                }
            } else {
                $mensagem = "";
            }
        
        // Se o cabeçalho e corpo da mensagem foram inicializadas    
        }else if(isset($this->cabecalhoResumo) and isset($this->corpoMensagem)) {
            $mensagem = $this->cabecalhoResumo . $this->corpoMensagem;
            
        // Não há mensagem para enviar
        }else{
            echo 'Não há mensagens para enviar';
        }
     
        if ($mensagem != "") {
            
            echo '<p>Corpo do email a enviar para simples conferência</p>';
            echo $mensagem;

            // Em homologação, comentar envio de e-mail
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->From = "sascar@sascar.com.br";
            $mail->FromName = "Sascar";
            $mail->Subject = $this->msgConector;
            $mail->MsgHTML($mensagem);
            $mail->ClearAllRecipients();

            /**/
            // Faz o envio
            if ($mail->Send()) {
                return "/n Email - OK /n";
            } else {
                return "/n Email - Erro /n";
            }
            /**/

            // Limpa os destinatários e os anexos
            $mail->ClearAllRecipients();
            $mail->ClearAttachments();
        }
    }

}
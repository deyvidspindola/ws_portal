<?php

require_once _MODULEDIR_ . "Principal/DAO/AtivacaoEquipamentoIvecoClienteDAO.php";
require_once _MODULEDIR_ . "Principal/Operadoras/Operadora.php";

/** 
 *
 * @author Ricardo Rojo Bonfim
 */
class AtivacaoEquipamentoIvecoCliente {

    private $soapClient;
    private $dao;

    private $equipamento;

    public function __construct() {
        $this->dao = new AtivacaoEquipamentoIvecoClienteDAO();
        $this->soapClient = new SoapClient(
            'http://172.16.2.52:8010/WebserviceAcp/AcpWSService?WSDL',
            array(  'trace' => 1,
                'exceptions' => 1,
                'soap_version' => SOAP_1_1)
            );

    }

    public function ativar( $idEquipamento ) {

        try {

            $this->getEquipamento($idEquipamento);

            $chave = $this->gerarChave();

            $statusEnvio = $this->enviarSmsEquipamento($chave);

            $mensagemHistorico = '';
            if ( isset($_SESSION) && isset($_SESSION['usuario']) ) {
                $mensagemHistorico = "Comando de ativação enviado para o equipamento por: " . $_SESSION['usuario']['nome_completo'] . ".";
            } else {
                $mensagemHistorico = "Comando de ativação enviado para o equipamento.";
            }
            $this->salvarHistorico( $mensagemHistorico );

        } catch (SoapFault $e) {
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }

        return $statusEnvio;
    }

    public function desativar( $idEquipamento ) {

        try {

            $this->getEquipamento($idEquipamento);

            $chave = '020A11150000000D80208A' . trim($this->equipamento->iccid);

            $statusEnvio = $this->enviarSmsEquipamento($chave);
            if ( isset($_SESSION) && isset($_SESSION['usuario']) ) {
                $mensagemHistorico = "";
                $mensagemHistorico .= "Comando de desativação enviado por: " . $_SESSION['usuario']['nome_completo'] . " para o equipamento: \n\n";
                $mensagemHistorico .= "Número de série do Equipamento: " . $this->equipamento->numero_serie . "\n";
                $mensagemHistorico .= "Telefone: (" . substr($this->equipamento->telefone, 0, 2) . ") " . substr($this->equipamento->telefone, 2);
            } else {
                $mensagemHistorico = "Comando de desativação enviado para o equipamento.";
            }
            $this->salvarHistorico( $mensagemHistorico );

        } catch (SoapFault $e) {
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }

        return $statusEnvio;
    }


    private function getEquipamento($idEquipamento) {

            $this->equipamento = $this->dao->getEquipamento( $idEquipamento );

            if ( !is_object($this->equipamento) ) {
                throw new Exception( 'Equipamento invalido.' );
            }

            return true;
    }

    private function gerarChave() {
        $parametros = $this->inicializarParametrosGeracaoChave();

        $result = $this->soapClient->gerarChave($parametros);

        return $result->return;
    }

    private function enviarSmsEquipamento( $chave ) {
        $parametros = $this->inicializarParametrosEnvioSms($chave);

        $result = $this->soapClient->enviarSms8bit($parametros);

        if ( $result->return !== 'SMS ENVIADO' ) {
            throw new Exception('Falha ao enviar SMS: ' . $statusEnvio);
        }

        return $result->return;
    }

    private function inicializarParametrosGeracaoChave() {

        $gerarChave = new stdClass();
        $contrato = new stdClass();

        $operadora = Operadora::buscarOperadora($this->equipamento->operadora);

        $contrato->chaveDinamica = '5A4D3555EFE03D2746696B71880FB40B60127BC8072990560DFB0162D10E9F71';
        $contrato->codigoErro = '';
        $contrato->conOid = '';
        $contrato->femOid = '';
        $contrato->iccid = '';
        $contrato->msisdn = '';
        $contrato->tentativaSmsDia = '';
        $contrato->usuOidCadastro = '';

        $gerarChave->operadora = new SoapVar($operadora, SOAP_ENC_OBJECT);
        $gerarChave->contrato = new SoapVar($contrato, SOAP_ENC_OBJECT);

        return new SoapVar( $gerarChave, SOAP_ENC_OBJECT );
    }

    private function inicializarParametrosEnvioSms( $chave ) {

        $enviarSms8bit = new stdClass();

        $enviarSms8bit->numeroOrigem = '';
        $enviarSms8bit->numeroDestino = $this->equipamento->telefone;
        if ( $_SESSION['servidor_teste'] ) {
            $enviarSms8bit->numeroDestino = '4188895568'; // Numero para testes
        }
        $enviarSms8bit->binarioSMS = $chave;

        return new SoapVar( $enviarSms8bit, SOAP_ENC_OBJECT);
    }

    private function salvarHistorico( $obs ) {

        try {

            $idUsuario = ( ( $_SESSION['usuario'] && $_SESSION['usuario']['oid'] ) ? $_SESSION['usuario']['oid'] : 4873);

            $parametros = array(
                    'connumero' => (int) $this->equipamento->connumero,
                    'usuoid' => (int) $idUsuario,
                    'obs' => $obs,
                );

            $this->dao->salvarHistorico($parametros);

        } catch (Exception $e) {
            throw $e;
        }

        return true;
    }

}

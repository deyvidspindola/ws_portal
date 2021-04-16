<?php

/**
 * Classe CadRepresentante.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   LUIZ FERNANDO PONTARA <fernandopontara@brq.com>
 *
 */

include "services/ADUsers/lib/config_ad.php";

//Classe PHP Mailer
require_once 'lib/phpMailer/class.phpmailer.php';

//Carrega as classes Middleware para comunicação com o OFSC
require_once _MODULEDIR_ ."SmartAgenda/Action/User.php";
require_once _MODULEDIR_ ."SmartAgenda/Action/Resource.php";

class CadRepresentante {

    /** Objeto DAO da classe */
    private $dao;

    /** propriedade para dados a serem utilizados na View. */
    private $view;

    /** Usuario logado */
    private $usuarioLogado;

    private $wsdl;

    private $adHash;

    private $urlPass; // URL para redefinição de senha

    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";
    const MENSAGEM_ERRO_DELETAR_OFSC  = 'Erro ao inativar o representante no OFSC.';
    const MENSAGEM_ERRO_CADASTRO_OFSC = 'Erro ao Criar/Alterar o representante no OFSC.';
    const MENSAGEM_SUCESSO_INCLUIR    = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR  = "Registro alterado com sucesso.";


    /**
     * Método construtor.
     * @param $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {

        global $adKeySmartAgenda;
        global $endpointAD;
        global $urlRedSenha;

        $this->dao                   = (is_object($dao)) ? $this->dao = $dao : NULL;
        $this->view                  = new stdClass();
        $this->view->mensagemErro    = '';
        $this->view->mensagemAlerta  = '';
        $this->view->mensagemSucesso = '';
        $this->view->dados           = null;
        $this->view->parametros      = null;
        $this->view->status          = false;
        $this->usuarioLogado         = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO (para CRON e WebService)
        $this->usuarioLogado         = (empty($this->usuarioLogado)) ? 2750 : intval($this->usuarioLogado);

        //Hash do AD
        $this->adHash                = $adKeySmartAgenda;

        //Define WSDL
        $this->wsdl                  = $endpointAD;

        //String do campo AD tabela usuarios
        $this->adString              = "2.ADDB.1.S2";

        // URL para redefinição de senha
        $this->urlPass = $urlRedSenha;
    }

    /**
     * Monta username para instalador ou representante
     * @param  [type] $tipoUsuario ['R' => representante, 'I' => instalador]
     * @param  [type] $oid         [itloid ou  repoid]
     * @return [type]              [description]
     */
    public function montaUsername($tipoUsuario,$oid) {

        $userName = '';
        $oid = (int) $oid;

        if($tipoUsuario == 'R') {
            $userName = 'PRS.' . $oid;
        } else if($tipoUsuario == 'I') {
            $userName = 'TEC.' . $oid;
        }

        return $userName;
    }

    /**
     * Cria Login via AD
     * @param  [int]    repoid    [ ID do representante    ]
     * @param  [string] repnome   [ Nome do Representante  ]
     * @param  [string] repe_mail [ Email do Representante ]
     */
    public function criaUsuario($repoid, $repnome, $repe_mail){

        try{
            $this->dao->begin();

            //verifica se já existe email cadastrado na base
            //if(!$this->dao->validaEmail($repe_mail)){
            //    throw new Exception("Email já cadastrado.");
            //}

            // Monta usuário do representante
            $usuarioRepresentante = $this->montaUsername('R',$repoid);

            //dados essenciais para cadastro na tabela de usuarios
            $dadosRepresentante = array(
                'repoid'            => $repoid,
                'ds_login'          => $usuarioRepresentante,
                'repnome'           => addslashes(trim($repnome)),
                'repe_mail'         => $repe_mail,
                'usudepoid'         => 9,  // REPRESENTANTE COMERCIAL
                'usucargooid'       => 664, // REPRESENTANTE TECNICO
                'usuloginseqad'     => $this->adString,
                'usuacesso_externo' => 'true'
            );

            // Verifica se existe usuario cadastrado no BD
            $dadosUsuario = $this->dao->verificaUsuarioExistente($usuarioRepresentante);

            // caso não tenha usuário inserido na base
            if($dadosUsuario == false) {

                //realiza cadastro na tabela de usuarios
                if(!$this->dao->insereUsuario($dadosRepresentante)){
                    throw new Exception("Erro ao inserir usuário.");
                }

            } else {
                throw new Exception("Usuário já existe na base de dados.");
            }

             // Verifica se existe usuário no active directory
            $dadosPesquisaUsuario = array(
                'adHash'        => $this->adHash,
                'userName'      => $usuarioRepresentante,
                'fAll'          => false
            );

            $responsePesquisa = $this->acessaWebService("consultarUsuario", $dadosPesquisaUsuario);

            // Se a pesquisa não retornar usuário, cria usuário no AD
            if(!isset($responsePesquisa->usuarios)) {
                //parametros para WS criarUsuario
                $paramCriarUsuario = array(
                    'adHash'       => $this->adHash,
                    'userName'     => $usuarioRepresentante,
                    'userEmail'    => $dadosRepresentante['repe_mail'],
                    'userProfile'  => "smart",
                    'completeName' => utf8_encode($repnome)
                );

                //chama webservice para criar usuario no AD
                $responseWS = $this->acessaWebService("criarUsuario", $paramCriarUsuario);

                if(is_array($responseWS) && isset($responseWS['status']) && $responseWS['status'] == 'erro') {
                    throw new Exception(utf8_decode($responseWS['descricao']));
                }
            }

            //parametros para WS gerarTokenPreAuth
            $paramGerarToken = array(
                'userName'     => $usuarioRepresentante,
                'userEmail'    => $dadosRepresentante['repe_mail'],
                'adHash'       => $this->adHash
            );

            //chama webservice para gerar Token de pré cadastro
            $token = $this->acessaWebService("gerarTokenPreAuth", $paramGerarToken);

            if(is_array($token) && isset($token['status']) && $token['status'] == 'erro') {
                throw new Exception(utf8_decode($token['descricao']));
            }

            //parametros para enviar email ao Representante
            $paramEmail = array(
                'nome'  => $repnome,
                'email' => $dadosRepresentante['repe_mail'],
                'token' => $token->token,
                'login' => $usuarioRepresentante
            );

            //envia email ao usuario (Representante)
            if(!$this->enviaEmail($paramEmail,'R')){
                throw new Exception("Erro ao enviar o e-mail ao Representante.");
            }

            //conclui integracao do usuario com AD
            if(!$this->dao->concluiIntegracao($repoid)){
                throw new Exception("Erro ao concluir integração com AD.");
            }

            $this->dao->commit();
            $retorno['status'] = "ok";

        } catch (Exception $e) {
            $this->dao->rollback();
            $retorno['status'] = "erro";
            $retorno['descricao'] = $e->getMessage();
        }

        return $retorno;

    }

    /**
     * Edita Login AD
     * @param  [int]    repoid    [ ID do representante    ]
     * @param  [string] repnome   [ Nome do Representante  ]
     * @param  [string] repe_mail [ Email do Representante ]
     */
    public function editaUsuario($repoid, $repnome, $repe_mail){

        try{

            //Verifica se já foi feita integraçao deste Representante
            if(!$this->dao->integracaoConcluida($repoid)) {

                $this->dao->begin();

                // Monta usuário do representante
                $usuarioRepresentante = $this->montaUsername('R',$repoid);

                //verifica se existe usuario cadastrado para este representante
                if($usuRepresentante = $this->dao->usuarioRepresentante($repoid)){

                    //dados essenciais para cadastro na tabela de usuaruios
                    $dadosRepresentante = array(
                        'usurefoid'     => $repoid,
                        'nm_usuario'    => $repnome,
                        'usuemail'      => $repe_mail,
                        'ds_login'      => $usuarioRepresentante,
                        'cd_usuario'    => $usuRepresentante[0]['cd_usuario']
                    );

                    //atualiza dados do usuario do representante
                    if(!$this->dao->editaUsuario($dadosRepresentante)) {
                        throw new Exception("Erro ao editar o usuário do representante.");
                    }

                }else{

                    //dados essenciais para cadastro na tabela de usuarios
                    $dadosRepresentante = array(
                        'repoid'            => $repoid,
                        'ds_login'          => $usuarioRepresentante,
                        'repnome'           => $repnome,
                        'repe_mail'         => $repe_mail,
                        'usudepoid'         => 9,  // REPRESENTANTE COMERCIAL
                        'usucargooid'       => 664, // REPRESENTANTE TECNICO
                        'usuloginseqad'     => $this->adString,
                        'usuacesso_externo' => 'true'
                    );


                    //realiza cadastro na tabela de usuarios
                    if(!$this->dao->insereUsuario($dadosRepresentante)) {
                        throw new Exception("Erro ao inserir usuário.");
                    }

                }

                // Verifica se o usuário já existe no AD
                $paramBusca = array(
                    'adHash'       => $this->adHash,
                    'userName'     => $usuarioRepresentante,
                    'fAll'         => false
                );

                $responseUsuario = $this->acessaWebService("consultarUsuario", $paramBusca);

                if(!isset($responseUsuario->usuarios)) {

                    //paramentros para WS criarUsuario
                    $paramCriarUsuario = array(
                        'adHash'       => $this->adHash,
                        'userName'     => $usuarioRepresentante,
                        'userEmail'    => $repe_mail,
                        'userProfile'  => "smart",
                        'completeName' => utf8_encode($repnome)
                    );

                    //chama webservice para criar usuario no AD
                    $responseWS = $this->acessaWebService("criarUsuario", $paramCriarUsuario);

                    if(is_array($responseWS) && isset($responseWS['status']) && $responseWS['status'] == 'erro') {
                        throw new Exception(utf8_decode($responseWS['descricao']));
                    }
                }

                //paramentros para WS gerarTokenPreAuth
                $paramGerarToken = array(
                    'userName'     => $usuarioRepresentante,
                    'userEmail'    => $repe_mail,
                    'adHash'       => $this->adHash
                );

                //chama webservice para gerar Token de pré cadastro
                $token = $this->acessaWebService("gerarTokenPreAuth", $paramGerarToken);

                if(is_array($token) && isset($token['status']) && $token['status'] == 'erro') {
                    throw new Exception(utf8_decode($token['descricao']));
                }

                //parametros para enviar email ao Representante
                $paramEmail = array(
                    'nome'  => $repnome,
                    'email' => $repe_mail,
                    'token' => $token->token,
                    'login' => $usuarioRepresentante
                );

                //envia email ao usuario (Representante)
                if(!$this->enviaEmail($paramEmail,'R')){
                    throw new Exception("Erro ao enviar o e-mail ao Representante.");
                }

                //conclui integracao do usuario com AD
                if(!$this->dao->concluiIntegracao($repoid)){
                    throw new Exception("Erro ao concluir integração com AD.");
                }

                // Finaliza a transação relativa ao representante
                $this->dao->commit();

                $dadosInstaladores = $this->criaUsuarioInstaladorRepresentante($repoid);

            }

            $retorno['status'] = "ok";
        } catch (Exception $e) {
            $this->dao->rollback();
            $retorno['status'] = "erro";
            $retorno['descricao'] = $e->getMessage();
        }

        return $retorno;

    }

    /**
     * Pega lista de instaladores do representante para processar cadastro
     * @param  [type] $repoid [id do representante]
     * @return [type]         [description]
     */
    public function criaUsuarioInstaladorRepresentante($repoid) {
        //verifica se Representante tem instalador
        $instaladores = $this->dao->getInstaladores($repoid);

        //caso exista instaladores para o representante
        if($instaladores){
            //percorre todos os instaladores do Representante
            foreach ($instaladores as $row) {
                $this->processaCadastroInstalador($row,$repoid);
            }
        }
    }

    /**
     * Realiza cadastro do instalador AD e DB
     * @param  [type] $row    [dados do instalador]
     * @param  [type] $repoid [id do representante]
     * @return [type]         [description]
     */
    public function processaCadastroInstalador($row,$repoid) {

        try{
            $this->dao->begin();

            // Monta o username do instalador
            $userName = $this->montaUsername('I',$row['itloid']);

            // Verifica se existe usuario cadastrado no BD
            $dadosUsuario = $this->dao->usuarioInstalador($userName);

            $dadosInstalador = array(
                'repoid'            => $repoid,
                'itloid'            => $row['itloid'],
                'ds_login'          => $userName,
                'itlnome'           => $row['itlnome'],
                'itlemail'          => $row['itlemail'],
                'usudepoid'         => 9,   // INSTALADOR
                'usucargooid'       => 663, // INSTALADOR TERCEIRO
                'usuloginseqad'     => $this->adString,
                'usuacesso_externo' => 'true'
            );

            if($dadosUsuario == false) {

                //realiza cadastro na tabela de usuarios
                if(!$this->dao->insereUsuarioInstalador($dadosInstalador)){
                    throw new Exception("Erro ao inserir usuário do Instalador: ". $row['itlnome']);
                }

            } else {
                throw new Exception("Usuário já existe na base de dados: " . $row['itlnome']);
            }

            // Parametros para pesquisa do usuário
            $paramBusca = array(
                'adHash'       => $this->adHash,
                'userName'     => $userName,
                'fAll'         => false
            );

            // Verifica se o usuário já existe no AD
            $responseUsuario = $this->acessaWebService("consultarUsuario", $paramBusca);

            if(!isset($responseUsuario->usuarios)) {

                // Parametros para WS criarUsuario
                $paramCriarUsuario = array(
                    'adHash'       => $this->adHash,
                    'userName'     => $userName,
                    'userEmail'    => utf8_encode($row['itlemail']),
                    'userProfile'  => "smart",
                    'completeName' => utf8_encode($row['itlnome'])
                );

                // Chama webservice para criar usuario no AD
                $responseWS = $this->acessaWebService("criarUsuario", $paramCriarUsuario);

                if(is_array($responseWS) && isset($responseWS['status']) && $responseWS['status'] == 'erro') {
                    throw new Exception($responseWS['descricao']);
                }

                // Parametros para WS gerarTokenPreAuth
                $paramGerarToken = array(
                    'userName'     => $userName,
                    'userEmail'    => $row['itlemail'],
                    'adHash'       => $this->adHash
                );

                // chama webservice para gerar Token de pré cadastro
                $token = $this->acessaWebService("gerarTokenPreAuth", $paramGerarToken);

                if(is_array($token) && isset($token['status']) && $token['status'] == 'erro') {
                    throw new Exception($token['descricao']);
                }

                // Parametros para envio do e-mail
                $paramEmail = array(
                    'nome'  => $row['itlnome'],
                    'email' => $row['itlemail'],
                    'token' => $token->token,
                    'login' => $userName
                );

                // Envia email ao usuario (Instalador)
                if(!$this->enviaEmail($paramEmail,'I')) {
                    throw new Exception("Erro ao enviar o e-mail ao Instalador: ".$row['itlnome']);
                }

                // Seta itladfull = true
                if(!$this->dao->concluiIntegracaoInstalador($row['itloid'])) {
                    throw new Exception("Erro ao concluir integração com AD.");
                }
            }

            $this->dao->commit();
        } catch (Exception $e) {
            $this->dao->rollback();
        }
    }

    /**
     * Excluir Representante e seus respectivos instaladores
     * @param  [int] $repoid [iD do Representante]
     * @return [type]         [description]
     */
    public function excluirRepresentante($repoid){

        try{

            $this->dao->begin();

            //Busca dados do Representante
            if(!$representante = $this->dao->getRepresentante($repoid)){
                throw new Exception("Representante não encontrado.");
            }

            //exclui representante
            if(!$this->dao->excluirRepresentante($repoid)){
                throw new Exception("Erro ao excluir Representante.");
            }

            //exclui usuarios (tabela) do Representante e seus instaladores
            if(!$this->dao->excluirUsuario($repoid)){
                throw new Exception("Erro ao excluir os usuários.");
            }

            $paramBusca = array(
                'adHash'       => $this->adHash,
                'userName'     => $this->montaUsername('R',$repoid),
                'fAll'         => false
            );

            $responseUsuario = $this->acessaWebService("consultarUsuario", $paramBusca);

            if(isset($responseUsuario->usuarios)) {

                //parametros para WS deletarUsuario (AD)
                $paramExcluirRepresentante = array(
                    'adHash'    => $this->adHash,
                    'userName'  => $this->montaUsername('R',$repoid)
                );

                //chama webservice para excluir usuario do Representante no AD
                $this->acessaWebService("deletarUsuario", $paramExcluirRepresentante);
            }

            //verifica se Representante tem instalador
            $instaladores = $this->dao->getInstaladores($repoid);

            //caso exista instaladores para o representante
            if($instaladores){

                //exclui todos os instaladores do respectivo Representante
                if(!$this->dao->excluirInstaladoresRepresenante($repoid)){
                    throw new Exception("Erro ao excluir instaladores do representante.");
                }

                //percorre todos os instaladores do Representante
                foreach ($instaladores as $row) {

                    $paramBusca = array(
                        'adHash'       => $this->adHash,
                        'userName'     => $this->montaUsername('I',$row['itloid']),
                        'fAll'         => false
                    );

                    $responseUsuario = $this->acessaWebService("consultarUsuario", $paramBusca);

                    if(isset($responseUsuario->usuarios)) {
                        //parametros para WS excluirUsuario (AD)
                        $paramExcluirInstalador = array(
                            'adHash'    => $this->adHash,
                            'userName'  => $this->montaUsername('I',$row['itloid'])
                        );

                        //chama webservice para excluir usuario do Instalador no AD
                        $this->acessaWebService("deletarUsuario", $paramExcluirInstalador);

                        unset($paramExcluirInstalador);
                    }
                    unset($paramBusca);
                }

            }

            $this->dao->commit();
            $retorno['status'] = "ok";

        } catch (Exception $e) {
            $this->dao->rollback();
            $retorno['status'] = "erro";
            $retorno['descricao'] = $e->getMessage();
        }

        return $retorno;

    }

    /**
     * Conecta ao SOAP Web Service
     * @param  [string] $metodo     [Método relacionado ao WSDL]
     * @param  [array]  $parametros [Parametros para o respectivo método]
     * @return [array]
     */
    private function acessaWebService($metodo, $parametros){

        try {

            // Instancia serviço
            $paramsClient = array(
                    'features'      => SOAP_SINGLE_ELEMENT_ARRAYS,
                    'trace'         => true,
                    'exceptions'    => 1,
                    'soap_version'  => SOAP_1_1,
                    'style'         => SOAP_DOCUMENT,
                    'use'           => SOAP_LITERAL
            );

            // Instancia Web Service
            $ws = new SoapClient($this->wsdl, $paramsClient);

            // chama método do Web Service
            $soapResponse = $ws->$metodo($parametros);

            if($soapResponse->codigo == '0'){
                return $soapResponse;
            }else{
                throw new Exception();
            }

        }catch (SoapFault $e){

            $retorno['status'] = 'erro';
            $retorno['descricao'] = $e->getMessage();

            return $retorno;

        } catch (Exception $e) {

            $retorno['status'] = 'erro';
            $retorno['codigo'] = $soapResponse->codigo;
            $retorno['descricao'] = $soapResponse->descricao;

            return $retorno;
        }

    }

    /**
     * Metodo para enviar e-mail
     * @param  [string] $dados [Token gerado]
     * @return [bool]
     */
    private function enviaEmail($dados, $tipo){

        $assunto = "Portal de Servicos: Criação/Alteração de Senha";
        $tipoDestinatario = 'Prezado';

        if($tipo == 'R') {
            $tipoDestinatario .= ' Prestador ';
        } else if($tipo == 'I') {
            $tipoDestinatario .= ' Instalador ';
        }

        $corpo_email = $tipoDestinatario .  $dados['nome'] . ",
            <br>
            <br>
                Seu login gerado é: " . $dados['login'] . "
            <br>
            <br>
            Para completar a criação/alteração de sua senha  de acesso, copie e cole o endereço abaixo
            na barra  de seu navegador e siga as instruções no formulário que será exibido.
            <br>
            <br>
            <a href='".$this->urlPass."'>".$this->urlPass."</a>
            <br>
            <br>
            Copie e cole no local adequado o token abaixo:
            <br>
            <br>
                <strong>" . $dados['token'] . "</strong>
            <br>
            <br>
            Você tem 72 Horas a contar do recebimento deste e-mail para efetivar a alteração,
            após este prazo o token expira.
        ";

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->From = "sascar@sascar.com.br";
        $mail->FromName = "sistema@sascar.com.br";
        $mail->Subject = $assunto;
        $mail->MsgHTML($corpo_email);
        $mail->ClearAllRecipients();
        $mail->AddAddress($dados['email']);

        $isEnviado = $mail->Send();

        if(!$isEnviado) {
            return false;
        }else{
            return true;
        }

    }

    public function salvaUsuarioOFSC($repoid, $repnome) {

        try{

            $ds_login = $this->montaUsername('R',$repoid);
            $resource_id = 'PS'.$repoid;
            $usuRepresentante = $this->dao->existeUsuarioRepresentante($ds_login);
            $repnome = ucwords(strtolower($repnome));

            if (!$usuRepresentante) throw new Exception("Representante sem login de usuário");

            // Pega informações do representante
            $infoRepresentante = $this->dao->getRepresentante( $repoid );
            $repEmail = isset($infoRepresentante[0]['repe_mail']) ? $infoRepresentante[0]['repe_mail'] : '';
            $dadosRecursoOFSC = $this->recuperaDadosRecursoOFSC( $resource_id );

            if(_AMBIENTE_ == 'PRODUCAO') {

                if( isset($dadosRecursoOFSC->resourceId) && isset($dadosRecursoOFSC->parentResourceId) ){
                    $parentResourceId = $dadosRecursoOFSC->parentResourceId;
                } else {
                    $parentResourceId = 'CO1';
                }

            } else {
                $parentResourceId = 'TESTES_SASCAR';
            }

            // Seta propriedades do resource
            $resource = new Resource();
            $resource->setIdRecurso( $resource_id );
            $resource->setPropriedades('status', 'active');
            $resource->setPropriedades('parentResourceId', $parentResourceId);
            $resource->setPropriedades('resourceType', 'BK');
            $resource->setPropriedades('name', $repnome);
            $resource->setPropriedades('resourceId', 'PS'.$repoid);
            $resource->setPropriedades('language', 'pt');
            $resource->setPropriedades('timeZone', 'BRST');
            $resource->setPropriedades('timeFormat', '24-hour');
            $resource->setPropriedades('dateFormat', 'dd/mm/yy');

            if(trim($repEmail) != '') {
                $resource->setPropriedades('email', $repEmail);
            }

            // Seta dados do endereço do representante, caso existir
            if($endRep = $this->dao->enderecoRepresentante($repoid)) {
                //XR_ADDRESS == Endereço
                if(trim($endRep->endvrua) != '') {
                    $endereco = $endRep->endvrua;
                    if(trim($endRep->endvnumero) != '') {
                        $endereco .= ', ' . $endRep->endvnumero;
                    }
                    $resource->setPropriedades('XR_ADDRESS', $endereco);
                }
                //phone == Telefone
                if(trim($endRep->endvfone) != '' && trim($endRep->endvddd) != '') {
                    $fone = $endRep->endvddd.' '.$endRep->endvfone;
                    $resource->setPropriedades('phone', $fone);
                }
                //XR_ADDRESS_2 == Complemento
                if(trim($endRep->endvcomplemento) != '') {
                    $resource->setPropriedades('XR_ADDRESS_2', $endRep->endvcomplemento);
                }
                //XR_NEIGHBORHOOD_NAME == Bairro
                if(trim($endRep->endvbairro) != '') {
                    $resource->setPropriedades('XR_NEIGHBORHOOD_NAME', $endRep->endvbairro);
                }
                //XR_CITY == Cidade
                if(trim($endRep->endvcidade) != '') {
                    $resource->setPropriedades('XR_CITY', $endRep->endvcidade);
                }
                //XR_ZIPCODE == CEP
                if(trim($endRep->endvcep) != '') {
                    $resource->setPropriedades('XR_ZIPCODE', $endRep->endvcep);
                }
                //XR_STATE ==  estado
                if(trim($endRep->endvuf) != '') {
                    $resource->setPropriedades('XR_STATE', $endRep->endvuf);
                }
            }

            if( isset($dadosRecursoOFSC->resourceId) ) {
                $dadosRecursoOFSC = $resource->updateResource();
            } else{
                $dadosRecursoOFSC = $resource->createResource();
            }

            if(  isset($dadosRecursoOFSC->error_msg)  ) {
                throw new Exception($dadosRecursoOFSC->error_msg);
            }

            $user = new User();

            $user->setLogin($ds_login);
            $dadosUser = $user->getUser();

            $user->setPropriedades('status', 'active');
            $user->setPropriedades('name', $repnome);
            $user->setPropriedades('language', 'pt');
            $user->setPropriedades('timeZone', 'BRST');
            $user->setPropriedades('timeFormat', '24-hour');
            $user->setPropriedades('dateFormat', 'dd/mm/yy');
            $user->setPropriedades('password', 'Sascar@'.date('Y') );
            $user->setPropriedades('XU_EXTERNAL_ID', $usuRepresentante->cd_usuario);
            $user->setPropriedades('resources', array($resource_id) );

            if( isset( $dadosUser->login ) ){
				$user->setPropriedades('userType', utf8_decode($dadosUser->userType));
                $retornoUser = $user->updateUser();
            } else{
                $user_type = $user->parametrizacaoSincronizacao('ad_prestadores');
				$user->setPropriedades('userType', utf8_decode($user_type));
                $retornoUser = $user->createUser();
            }

            if(  isset($retornoUser->error_msg)  ) {
                throw new Exception($retornoUser->error_msg);

            } else {
                $this->dao->concluiIntegracaoOracle($repoid);
            }

        }catch(Exception $e){
            if( _AMBIENTE_ == 'LOCALHOST' || _AMBIENTE_ == 'DESENVOLVIMENTO' ){
                throw new Exception( $e->getMessage() );
            }
            throw new Exception(self::MENSAGEM_ERRO_CADASTRO_OFSC);
        }
    }

    public function removerRecursoOFSC($repoid) {

        try{

            $this->removerPrestadorOFSC($repoid);

            $dadosInstaladores = $this->dao->getInstaladoresOFSC($repoid);

            if ( $dadosInstaladores !== false) {
                foreach ($dadosInstaladores as $chave => $valor) {
                   $this->removerInstaladorOFSC( $valor['itloid'] );
                }
            }

            $this->dao->cancelaIntegracaoOracle($repoid);

        } catch(Exception $e){
            if( _AMBIENTE_ == 'LOCALHOST' || _AMBIENTE_ == 'DESENVOLVIMENTO' ){
                throw new Exception( $e->getMessage() );
            }
            throw new exception(self::MENSAGEM_ERRO_DELETAR_OFSC);
        }

    }

    private function removerPrestadorOFSC($repoid){

        $ds_login = $this->montaUsername('R',$repoid);

        $user = new User();
        $user->setLogin( $ds_login );
        $dadosUser = $user->getUser();

        if( isset($dadosUser->login) && isset($dadosUser->status) ) {

            if( $dadosUser->status == 'active' ){

                $user->setPropriedades('status', 'inactive');
                $retornoUser = $user->updateUser();

                if( isset($retornoUser->error_msg) ) {
                    throw new exception($retornoUser->error_msg);
                }
            }
        }

        $resource = new Resource();
        $resource->setIdRecurso( 'PS'.$repoid );
        $dadosRecurso = $resource->getResource();

        if( isset($dadosRecurso->status) ){

            if( $dadosRecurso->status == 'active' ){
                $resource->setPropriedades('status', 'inactive');
                $retornoResource = $resource->updateResource();

                if( isset($retornoResource->error_msg) ) {
                    throw new exception($retornoResource->error_msg);
                }
            }
        }

    }

    private function removerInstaladorOFSC($itloid){

        $ds_login = $this->montaUsername('I', $itloid);

        $user = new User();
        $user->setLogin( $ds_login );
        $dadosUser = $user->getUser();

        if( isset($dadosUser->login) && isset($dadosUser->status) ) {

            if( $dadosUser->status == 'active' ){

                $user->setPropriedades('status', 'inactive');
                $retornoUser = $user->updateUser();

                if( isset($retornoUser->error_msg) ) {
                    throw new exception($retornoUser->error_msg);
                }
            }
        }

        $resource = new Resource();
        $resource->setIdRecurso( 'TC'.$itloid );
        $dadosRecurso = $resource->getResource();

        if( isset($dadosRecurso->status) ){

            if( $dadosRecurso->status == 'active' ){
                $resource->setPropriedades('status', 'inactive');
                $retornoResource = $resource->updateResource();

                if( isset($retornoResource->error_msg) ) {
                    throw new exception($retornoResource->error_msg);
                }
            }
        }
     }

    public function recuperaDadosRecursoOFSC($IdRecurso) {

        $resource = new Resource();
        $resource->setIdRecurso( $IdRecurso );
        $dadosUsuario = $resource->getResource();

        return $dadosUsuario;

    }

}


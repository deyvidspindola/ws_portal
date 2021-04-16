<?php

/**
 * Classe CadInstalador.
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

class CadInstalador {

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
    const MENSAGEM_SUCESSO_INCLUIR    = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR  = "Registro alterado com sucesso.";
    const MENSAGEM_ERRO_DELETAR_OFSC  = 'Erro ao inativar o instalador no OFSC.';
    const MENSAGEM_ERRO_CADASTRO_OFSC = 'Erro ao Criar/Alterar o isntalador no OFSC.';


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
     * Monta username para instalador
     * @param  [type] $oid         [itloid]
     * @return [type]              [description]
     */
    public function montaUsername($oid) {
        return 'TEC.' . intval($oid);
    }

    /**
     * Monta username para representante
     * @param  [type] $oid         [repoid]
     * @return [type]              [description]
     */
    public function montaUsernameRepresentante($oid) {
        return 'PRS.'.intval($oid);
    }

    /**
     * Cria Login via AD
     * @param  [int]    itloid    [ ID do Instalador    ]
     * @param  [string] itlnome   [ Nome do Instalador  ]
     * @param  [string] itlemail  [ Email do Instalador ]
     */
    public function criaUsuario($itloid, $itlnome, $itlemail, $repoid){

        try{
            $this->dao->begin();

            // Monta o username do instalador
            $userName = $this->montaUsername($itloid);

            //dados essenciais para cadastro na tabela de usuarios
            $dadosInstalador = array(
                'repoid'            => $repoid,
                'itloid'            => $itloid,
                'ds_login'          => $userName,
                'itlnome'           => addslashes(trim($itlnome)),
                'itlemail'          => $itlemail,
                'usudepoid'         => 9,   // INSTALADOR
                'usucargooid'       => 663, // INSTALADOR TERCEIRO
                'usuloginseqad'     => $this->adString,
                'usuacesso_externo' => 'true'
            );

            // Verifica se existe usuario caedastrado no BD
            $dadosUsuario = $this->dao->usuarioInstalador($userName);

            if($dadosUsuario == false) {
                //realiza cadastro na tabela de usuarios
                if(!$this->dao->insereUsuario($dadosInstalador)){
                    throw new Exception("Erro ao inserir usuário.");
                }
            } else {
                throw new Exception("Usuário já existe na base de dados.");
            }

            //parametros para WS criarUsuario
            $paramCriarUsuario = array(
                'adHash'       => $this->adHash,
                'userName'     => $userName,
                'userEmail'    => $dadosInstalador['itlemail'],
                'userProfile'  => "smart",
                'completeName' => utf8_encode($dadosInstalador['itlnome'])
            );

            //chama webservice para criar usuario no AD
            $responseWS = $this->acessaWebService("criarUsuario", $paramCriarUsuario);

            if(is_array($responseWS) && isset($responseWS['status']) && $responseWS['status'] == 'erro') {
                throw new Exception($responseWS['descricao']);
            }

            //parametros para WS gerarTokenPreAuth
            $paramGerarToken = array(
                'userName'     => $userName,
                'userEmail'    => $dadosInstalador['itlemail'],
                'adHash'       => $this->adHash
            );

            //chama webservice para gerar Token de pré cadastro
            $token = $this->acessaWebService("gerarTokenPreAuth", $paramGerarToken);

            if(is_array($token) && isset($token['status']) && $token['status'] == 'erro') {
                throw new Exception($token['descricao']);
            }

            //parametros para enviar email ao Instalador
            $paramEmail = array(
                'nome'  => $itlnome,
                'email' => $dadosInstalador['itlemail'],
                'token' => $token->token,
                'login' => $userName
            );

            //envia email ao usuario (Instalador)
            if(!$this->enviaEmail($paramEmail)){
                throw new Exception("Erro ao enviar o e-mail ao Instalador.");
            }

            //conclui integracao do usuario com AD
            if(!$this->dao->concluiIntegracao($itloid)){
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
     * Editan login AD
     * @param  [type] $itloid    [id do instalador]
     * @param  [type] $itlnome   [nome do instalador]
     * @param  [type] $itlemail  [email do instalador]
     * @return [type]            [description]
     */
    public function editaUsuario($itloid, $itlnome, $itlemail, $repoid) {

        try{

            $this->dao->begin();

            // Monta o username do instalador
            $userName = $this->montaUsername($itloid);

            // Verifica se tem usuário cadastrado para o instalador
            if($usuInstalador = $this->dao->usuarioInstalador($userName)){

                //dados essenciais para cadastro na tabela de usuaruios
                $dadosInstalador = array(
                    'usurefoid'     => $repoid,
                    'nm_usuario'    => $itlnome,
                    'usuemail'      => $itlemail,
                    'ds_login'      => $userName,
                    'cd_usuario'    => $usuInstalador[0]['cd_usuario']
                );

                //atualiza dados do usuario do representante
                if(!$this->dao->editaUsuario($dadosInstalador)){
                    throw new Exception("Erro ao editar o usuário.");
                }

            } else {

                //dados essenciais para cadastro na tabela de usuarios
                $dadosInstalador = array(
                    'repoid'            => $repoid,
                    'ds_login'          => $userName,
                    'itlnome'           => $itlnome,
                    'itlemail'          => $itlemail,
                    'usudepoid'         => 9,   // Departamento instalação
                    'usucargooid'       => 663, // Instalador terceiro
                    'usuloginseqad'     => $this->adString,
                    'usuacesso_externo' => 'true'
                );


                //realiza cadastro na tabela de usuarios
                if(!$this->dao->insereUsuario($dadosInstalador)){
                    throw new Exception("Erro ao inserir usuário.");
                }

            }


            if(!$this->dao->integracaoConcluida($itloid)){


                // Verifica se o usuário já existe no AD
                $paramBusca = array(
                    'adHash'       => $this->adHash,
                    'userName'     => $userName,
                    'fAll'         => false
                );

                $responseUsuario = $this->acessaWebService("consultarUsuario", $paramBusca);

                if(!isset($responseUsuario->usuarios)) {

                    //paramentros para WS criarUsuario
                    $paramCriarUsuario = array(
                        'adHash'       => $this->adHash,
                        'userName'     => $userName,
                        'userEmail'    => $itlemail,
                        'userProfile'  => "smart",
                        'completeName' => utf8_encode($itlnome)
                    );

                    //chama webservice para criar usuario no AD
                    $responseWS = $this->acessaWebService("criarUsuario", $paramCriarUsuario);

                    if(is_array($responseWS) && isset($responseWS['status']) && $responseWS['status'] == 'erro') {
                        throw new Exception($responseWS['descricao']);
                    }
                }

                //paramentros para WS gerarTokenPreAuth
                $paramGerarToken = array(
                    'userName'     => $userName,
                    'userEmail'    => $itlemail,
                    'adHash'       => $this->adHash
                );

                //chama webservice para gerar Token de pré cadastro
                $token = $this->acessaWebService("gerarTokenPreAuth", $paramGerarToken);

                if(is_array($token) && isset($token['status']) && $token['status'] == 'erro') {
                    throw new Exception($token['descricao']);
                }

                //parametros para enviar email ao Instalador
                $paramEmail = array(
                    'nome'  => $itlnome,
                    'email' => $itlemail,
                    'token' => $token->token,
                    'login' => $userName
                );

                //envia email ao usuario (Instalador)
                if(!$this->enviaEmail($paramEmail)){
                    throw new Exception("Erro ao enviar o e-mail ao Instalador.");
                }

                //conclui integracao do usuario com AD
                if(!$this->dao->concluiIntegracao($itloid)){
                    throw new Exception("Erro ao concluir integração com AD.");
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
     * Excluir Instalador
     * @param  [int] $itloid [iD do Instalador]
     * @return [bool]
     */
    public function excluirInstalador($itloid){

        try{
            $this->dao->begin();

            // Monta o username do instalador
            $userName = $this->montaUsername($itloid);

            //Busca dados do Instalador
            if(!$instalador = $this->dao->getInstalador($itloid)){
                throw new Exception("Instalador não encontrado.");
            }

            //exclui instalador
            if(!$this->dao->excluirInstalador($itloid)){
                throw new Exception("Erro ao excluir Instalador.");
            }

            //exclui usuario (tabela) do Instalador
            if(!$this->dao->excluirUsuario($userName)){
                throw new Exception("Erro ao excluir os usuários.");
            }

             // Verifica se o usuário já existe no AD
            $paramBusca = array(
                'adHash'       => $this->adHash,
                'userName'     => $userName,
                'fAll'         => false
            );

            $responseUsuario = $this->acessaWebService("consultarUsuario", $paramBusca);

            if(isset($responseUsuario->usuarios)) {

                //parametros para WS excluirUsuario (AD)
                $paramExcluirInstalador = array(
                    'adHash'    => $this->adHash,
                    'userName'  => $userName
                );

                //chama webservice para excluir usuario do Instalador no AD
                $retornoWS = $this->acessaWebService("deletarUsuario", $paramExcluirInstalador);

                if(is_array($retornoWS) && isset($retornoWS['status']) && $retornoWS['status'] == 'erro') {
                    throw new Exception($retornoWS['descricao']);
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
    private function enviaEmail($dados){

        $assunto = "Portal de Servicos: Criação/Alteração de Senha";

        $corpo_email = "
            Prezado Instalador " . $dados['nome'] . ",
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

        if(!$isEnviado ) {
            return false;
        }else{
            return true;
        }

    }

    public function isRepresentanteOFSC($repoid) {

        $retorno = false;

        try {
            $ds_login = $this->montaUsernameRepresentante($repoid);
            $retorno = $this->dao->existeUsuarioRepresentante($ds_login);

            if( $retorno ) {
                $retorno = $this->dao->isRepresentanteOFSC($repoid);
            }

            if( $retorno ){

                $resource = new Resource();
                $resource->setIdRecurso( 'PS'.$repoid );
                $dadosRecursoOFSC = $resource->getResource();

                if( isset( $dadosRecursoOFSC->error_msg ) ){
                    throw new Exception( $dadosRecursoOFSC->error_msg );
                }

                if( isset( $dadosRecursoOFSC->resourceId ) ) {
                    $retorno = true;
                }
            }

        } catch (Exception $e) {
            $retorno = false;
        }

        return $retorno;
    }

    public function salvaUsuarioOFSC($itloid, $itlnome, $itlrepoid) {

        try{

            $ds_login = $this->montaUsername($itloid);
            $resource_id = 'TC'.$itloid;

            $ds_login_representante = 'PRS.'.$itlrepoid;
            $parent_id = 'PS'.$itlrepoid;

            $itlnome = ucwords(strtolower($itlnome));

            $dadosRecursoOFSC = $this->recuperaDadosRecursoOFSC( $resource_id );

            $resource = new Resource();
            $resource->setIdRecurso( $resource_id );
            $resource->setPropriedades('status', 'active');
            $resource->setPropriedades('parentResourceId', $parent_id);
            $resource->setPropriedades('resourceType', 'MW'); // TECNICO
            $resource->setPropriedades('name', $itlnome);
            $resource->setPropriedades('resourceId', 'TC'.$itloid);
            $resource->setPropriedades('language', 'pt');
            $resource->setPropriedades('timeZone', 'BRST');
            $resource->setPropriedades('timeFormat', '24-hour');
            $resource->setPropriedades('dateFormat', 'dd/mm/yy');

            if($endItl = $this->dao->getInstalador($itloid)) {
                //XR_ADDRESS == Endereço
                if(trim($endItl->itlendereco) != '') {
                    $resource->setPropriedades('XR_ADDRESS', $endItl->itlendereco);
                }
                //phone == Telefone
                if(trim($endItl->itlfone) != '') {
                     $resource->setPropriedades('phone', preg_replace('/[\D]/', '', $endItl->itlfone) );
                }
                //Celular SMS
                if(trim($endItl->itlfone) != '') {
                    $resource->setPropriedades('XR_PHONE', preg_replace('/[\D]/', '', $endItl->itlfone_sms) );
                }
                //XR_ADDRESS_2 == Complemento
                if(trim($endItl->itlcomplemento) != '') {
                    $resource->setPropriedades('XR_ADDRESS_2', $endItl->itlcomplemento);
                }
                //XR_NEIGHBORHOOD_NAME == Bairro
                if(trim($endItl->itlbairro) != '') {
                    $resource->setPropriedades('XR_NEIGHBORHOOD_NAME', $endItl->itlbairro);
                }
                //XR_CITY == Cidade
                if(trim($endItl->itlcidade) != '') {
                    $resource->setPropriedades('XR_CITY', $endItl->itlcidade);
                }
                //XR_ZIPCODE == CEP
                if(trim($endItl->itlcep) != '') {
                    $resource->setPropriedades('XR_ZIPCODE', $endItl->itlcep);
                }
                //XR_STATE ==  estado
                if(trim($endItl->itlestado) != '') {
                    $resource->setPropriedades('XR_STATE', $endItl->itlestado);
                }
                //email
                if(trim($endItl->itlemail) != '') {
                    $resource->setPropriedades('email', $endItl->itlemail);
                }
                //XR_CPF == CPF
                if(trim($endItl->itlno_cpf) != '') {
                    $resource->setPropriedades('XR_CPF', $endItl->itlno_cpf);
                }
                if(trim($endItl->repnome) != '') {
                    $repnome = ucwords(strtolower($endItl->repnome));
                    $resource->setPropriedades('XR_SERVICE_PROVIDER_NAME', $repnome);
                }
            }

            if( isset($dadosRecursoOFSC->resourceId) ) {
                $dadosRecursoOFSC = $resource->updateResource();
            } else{
                $dadosRecursoOFSC = $resource->createResource();
            }

            if(  isset($dadosRecursoOFSC->error_msg)  ) {
                throw new Exception( $dadosRecursoOFSC->error_msg );
            }

            $dadosUsuario = $this->dao->usuarioInstalador( $ds_login );

            $user = new User();

            $user->setLogin( $ds_login );
            $dadosUser = $user->getUser();

            $user->setPropriedades('status', 'active');
            $user->setPropriedades('name', $itlnome);
            $user->setPropriedades('language', 'pt');
            $user->setPropriedades('timeZone', 'BRST');
            $user->setPropriedades('timeFormat', '24-hour');
            $user->setPropriedades('dateFormat', 'dd/mm/yy');
            $user->setPropriedades('password', 'Sascar@'.date('Y'));
            $user->setPropriedades('mainResourceId', $resource_id);
            $user->setPropriedades('resources', array($resource_id) );


            if($dadosUsuario != false) {
                $user->setPropriedades('XU_EXTERNAL_ID', $dadosUsuario[0]['cd_usuario']);
            }

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
                $this->dao->concluiIntegracaoOracle( $itloid );
            }

        } catch(Exception $e){
            if( _AMBIENTE_ == 'LOCALHOST' || _AMBIENTE_ == 'DESENVOLVIMENTO' ){
                echo"<pre>";var_dump( $e->getMessage() );echo"</pre>";exit();
           }

            throw new Exception(self::MENSAGEM_ERRO_CADASTRO_OFSC);
        }
    }

    public function removeUsuarioOFSC($itloid) {

        try{

            $ds_login = $this->montaUsername($itloid);

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

            $this->dao->cancelaIntegracaoOracle($itloid);

        }catch(Exception $e){
            if( _AMBIENTE_ == 'LOCALHOST' || _AMBIENTE_ == 'DESENVOLVIMENTO' ){
                throw new Exception( $e->getMessage() );
            }
            throw new exception(self::MENSAGEM_ERRO_DELETAR_OFSC);
        }
    }

    public function recuperaDadosRecursoOFSC($IdRecurso) {

        $resource = new Resource();
        $resource->setIdRecurso( $IdRecurso );
        $dadosUsuario = $resource->getResource();

        return $dadosUsuario;

    }

    /**
     * Verifica se um determinado instalador está sincronizado.
     * @param  [type] $itloid [description]
     * @return [type]         [description]
     */
    public function instaladorSincronizado($itloid) {
        $itloid = (int) $itloid;
        return $this->dao->instaladorSincronizado($itloid);
    }

}


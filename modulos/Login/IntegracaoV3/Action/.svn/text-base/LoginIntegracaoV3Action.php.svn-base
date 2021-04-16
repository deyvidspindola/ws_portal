<?php

require_once _MODULEDIR_ . 'Login/IntegracaoV3/DAO/LoginIntegracaoV3DAO.php';
require_once _MODULEDIR_ . 'Login/IntegracaoV3/VO/IntegradoraVO.php';
require_once _MODULEDIR_ . 'Login/IntegracaoV3/VO/GerenciadoraVO.php';
require_once _MODULEDIR_ . 'Login/IntegracaoV3/VO/RetornoVO.php';
require_once _MODULEDIR_ . 'Login/Commun/VO/AutorAcaoVO.php';
require_once _SITEDIR_ . '/lib/gerar_logs/Logger.php';

/**
 *
 * @author alexandre.reczcki
 *
 * Criar Usuário e Senha WS Sasintegra V3
 *
 * @package Login/IntegracaoV3/Action
 */
class LoginIntegracaoV3Action{

	/** @var LoginIntegracaoV3DAO @see Persistencia Func Integracao */
	private $loginIntegracaoV3DAO;

	/** @var int @see Id da gerenciadora inserido no construtor apenas */
	private $geroid;
	
	/** @var array @see Array de configuração do log */
	private $arrayLog = array();
	
	/** @var boolean @see Modo debug, caso marcado como true printa na tela */
	private $imprimirNaTela = false;
	
	/** @var  AutorAcaoVO */
	private $autorAcaoVO;
	
	/**
	 * Mensagens de ERRO
	 */
	const MENSAGEM_ERRO_ID_INVALIDO = "FAVOR INFORMAR UM NUMERO VALIDO DE ID DA GERENCIADORA";
	const MENSAGEM_ERRO_GERENCIADORA_NAO_ENCONTRADA = "GERENCIADORA NÃO ENCONTRADA";
	const MENSAGEM_ERRO_lOGIN_INTEGRADORA_NAO_ENCONTRADA = "LOGIN INTEGRADORA NÃƒO ENCONTRADA";
	const MENSAGEM_ERRO_GERAR_LOGIN_SASINTEGRA = "ERRO AO GERAR LOGIN GERENCIADORA NA INTEGRAÇÃO SASINTEGRA";
	const MENSAGEM_ERRO_AO_CONCEDER_PERMISSAO_ACESSO_METODOS = "ERRO AO CONCEDER PERMISSÃO DE ACESSO MÉTODOS";
	const MENSAGEM_ERRO_ESTRUTURA_POSICAO = "ERRO AO CRIAR ESTRUTURA POSIÇÃO";
	const MENSAGEM_ERRO_ESTRUTURA_PACOTES = "ERRO AO CRIAR ESTRUTURA DE PACOTES";
	const MENSAGEM_ERRO_GERENCIADORA_LOGIN = "FAVOR VERFIQUE O CADASTRO, O LOGIN DA GERENCIADORA ESTÁ NULO OU VAZIO [MESMO LOGIN DO SASGC]";
	const MENSAGEM_ERRO_GERENCIADORA_NOME = "FAVOR VERFIQUE O CADASTRO, O NOME DA GERENCIADORA ESTÁ NULO OU VAZIO";
	const MENSAGEM_ERRO_GERENCIADORA_JA_POSSUI_ACESSO = "GERENCIADORA JÁ POSSUI ACESSO A INTEGRAÇÃO";
	const MENSAGEM_ERRO_GERENCIADORA_NAO_POSSUI_ACESSO = "GERENCIADORA NÃO POSSUI ACESSO A INTEGRAÇÃO, NÃO HÁ SENHA PARA RESETAR";
	const MENSAGEM_ERRO_AO_RESETAR_SENHA_SASINTEGRA = "ERRO AO RESETAR SENHA NO SASINTEGRA";
	const USUARIO_SEM_ACESSO = "USUARIO NÃO POSSUI ACESSO PARA PROSSEGUIR COM A AÇÃO";
	const FALHA_AO_GERAR_HISTORICO = "OCORREU UMA FALHA AO GERAR O HISTÓRICO DESSA AÇÃO";
	
	/**
	 * Mensagens de SUCESSO
	 */
	const MENSAGEM_SUCESSO = "INTEGRAÇÃO REALIZADA COM SUCESSO!!!";
	const MENSAGEM_SUCESSO_RESETE_SENHA = "SENHA DA INTEGRAÇÃO RESETADA COM SUCESSO!!!";
	
	const SERVICO_DOWNLOAD = "download.php?arquivo=";
	const CAMINHO_LOG = _DIRETORIO_LOG_;
	
	const GERACAO_USUARIO_V3 = "CRIADO USUÁRIO SASINTEGRAV3";
	const RESET_SENHA_USUARIO_V3 = "RESET DE SENHA SASINTEGRAV3";
	
	private $departamentosAutorizadosServico = array(543,606);
	
	/**
	 * Configurao da geração de log e
	 * setar o id da gerenciadora na qual será realizada
	 * a geração da integração no SasintegraV3, necessário
	 * para a utilização do método gerarAcessoIntegradora().
	 * 
	 * @param string $geroid $geroid Id da gerenciadora inserido no construtor apenas 
	 * @param boolean $imprimirNaTela
	 */
	public function __construct($geroid = "", AutorAcaoVO $autorAcaoVO = null, $imprimirNaTela = false){
		$this->geroid = $geroid;
		$this->autorAcaoVO = $autorAcaoVO;
		if(! empty($this->geroid) && intval($this->geroid)){
		    $this->arrayLog = array('nomeArqDinamico' => "criar_integradora_v3_geroid_$geroid", 
		        'usuario'       =>  $this->autorAcaoVO->id);
		}else{
		    $this->arrayLog = array('nomeArqDinamico' => "criar_integradora_v3");
		}
		
		if($imprimirNaTela){
			$this->imprimirNaTela = $imprimirNaTela;
		}
		$this->loginIntegracaoV3DAO = new LoginIntegracaoV3DAO($this->arrayLog, $imprimirNaTela);
	}
		
	/**
	 * Metodo que realizara todo o processo necessário 
	 * de inserir uma gerenciadora para utilizar a integração do SasintegraV3
	 * 
	 * 1º CADASTRAR INTEGRADORA
	 * 2º CADASTRAR AS PERMISSOES DA INTEGRADORA
	 * 3º CRIAR ESTRUTURA DE TABELAS DE POSIÇÃO PARA A INTEGRADORA NOVA
	 * 4º INSERIR TABELA CONTROLE DE PACOTE | BANCO: SASINTEGRAV3_POSICAO
	 * 5° FAZER LOGIN COMO NOVO USUARIO ATRAVÉS DO SOAPUI, PARA COLOCAR O USUÁRIO NO CACHE E ATUALIZAR O CACHE
	 * 6º FAZER LOGIN COMO NOVO USUARIO ATRAVÉS DO SOAPUI, PARA COLOCAR O USUÁRIO NO CACHE E ATUALIZAR O CACHE.
	 * 
	 * @example 
	 * $loginIntegracaoV3Action = new LoginIntegracaoV3Action($_GET['GEROID']);
	 * $retorno = new RetornoVO();
	 * $retorno = $loginIntegracaoV3Action->gerarAcessoIntegradora();
	 * 
	 * @return RetornoVO
	 */
	public function gerarAcessoIntegradora(){
		try {
			
		    if(! $this->validarAcessoGerenciarIntegracao()){
		        throw new Exception(self::USUARIO_SEM_ACESSO);
		    }
		    
 			$this->loginIntegracaoV3DAO->begin(LoginIntegracaoV3DAO::obterConexaoSasIntegraV3());
 			$this->loginIntegracaoV3DAO->begin(LoginIntegracaoV3DAO::obterConexaoSasIntegraV3Posicao());
 			
			Logger::logInfo("\n [INICIANDO - GERAR ACESSO INTEGRADORA] \n GERENCIADORA :-> $this->geroid \n", __FILE__, __LINE__, $this->arrayLog);
			
			$vo = new IntegradoraVO();
			$vo = $this->buscarGerenciadora($this->geroid);
			$this->printarObjetoIntegradoraVO($vo);
			
			$this->inserirIntegradoraInformacoesGerenciadora($vo);
			$this->concederPermissoesIntegradoraMetodos($vo->intid);

			$this->criarEstruturaPosicao($vo->intid);
			$this->criarControleDePacotes($vo->intid);
			
			$this->loginIntegracaoV3DAO->commit(LoginIntegracaoV3DAO::obterConexaoSasIntegraV3());
			$this->loginIntegracaoV3DAO->commit(LoginIntegracaoV3DAO::obterConexaoSasIntegraV3Posicao());
			
			$this->inserirHistoricoAcao($this->geroid, $this->autorAcaoVO->id, self::GERACAO_USUARIO_V3);
			
			$this->recarregarCacheIntegradora();
			Logger::logInfo("[FINALIZADA COM SUCESSO] - GERENCIADORA :-> $this->geroid", __FILE__, __LINE__, $this->arrayLog);
			
 			return new RetornoVO(true, self::MENSAGEM_SUCESSO, $this->buscarLoginIntegradora($this->geroid));
 			
		} catch (Exception $e) {
			Logger::logError("\n [FINALIZADA COM ERRO] \n GERENCIADORA :->" . $this->geroid . " - \n EXCEPTION: " . $e->getMessage() . "\n", __FILE__, __LINE__, $this->arrayLog);
			
 			$this->loginIntegracaoV3DAO->rollback(LoginIntegracaoV3DAO::obterConexaoSasIntegraV3());
 			$this->loginIntegracaoV3DAO->rollback(LoginIntegracaoV3DAO::obterConexaoSasIntegraV3Posicao());
 			
 			return new RetornoVO(false, $e->getMessage(), null);
		}
	}

	/**
	 * 1 - CADASTRAR INTEGRADORA
	 *
	 * @param int $geroid
	 * @throws Exception
	 *
	 * @return integradoraVO
	 */
	protected function inserirIntegradoraInformacoesGerenciadora(integradoraVO $vo){

		if (! isset($vo->intnome) || $vo->intnome == ""){
			throw new Exception(self::MENSAGEM_ERRO_GERENCIADORA_NOME);
		}
		
		if (! isset($vo->intlogin) || $vo->intlogin == ""){
			throw new Exception(self::MENSAGEM_ERRO_GERENCIADORA_LOGIN);
		}

		if ($this->loginIntegracaoV3DAO->validarSePossuiAcesso($vo->intid)){
			throw new Exception(self::MENSAGEM_ERRO_GERENCIADORA_JA_POSSUI_ACESSO);
		}
		
		if(! $this->loginIntegracaoV3DAO->inserirIntegradora($vo->intid, $vo->intnome, $vo->intlogin)){
			throw new Exception(self::MENSAGEM_ERRO_GERAR_LOGIN_SASINTEGRA);
		}
		
		return TRUE;
	}
	
	/**
	 * Resetar senha usuário SasintegraV3
	 * 
	 * @example 
	 * $loginIntegracaoV3Action = new LoginIntegracaoV3Action($_GET['GEROID']);
	 * $retorno = new RetornoVO();
	 * $retorno = $loginIntegracaoV3Action->resetarSenhaIntegradora();
	 * 
	 * @param string $intsenha
	 * @throws Exception
	 * @return RetornoVO
	 */
	public function resetarSenhaIntegradora($intsenha = 'sascar'){
		try {
		    
		    if(! $this->validarAcessoGerenciarIntegracao()){
		        throw new Exception(self::USUARIO_SEM_ACESSO);
		    }
		    
			$this->loginIntegracaoV3DAO->begin(LoginIntegracaoV3DAO::obterConexaoSasIntegraV3());
			
			Logger::logInfo("\n [INICIANDO - RESET DE SENHA] \n GERENCIADORA :-> $this->geroid \n", __FILE__, __LINE__, $this->arrayLog);
			
			$vo = new IntegradoraVO();
			$vo = $this->buscarGerenciadora($this->geroid);
			$this->printarObjetoIntegradoraVO($vo);
			
			if ($this->loginIntegracaoV3DAO->validarSePossuiAcesso($vo->intid) == FALSE){
				throw new Exception(self::MENSAGEM_ERRO_GERENCIADORA_NAO_POSSUI_ACESSO);
			}
			
			if($this->loginIntegracaoV3DAO->resetarSenha($vo->intid, md5($intsenha)) == FALSE){
				throw new Exception(self::MENSAGEM_ERRO_AO_RESETAR_SENHA_SASINTEGRA);
			}
			
			$this->loginIntegracaoV3DAO->commit(LoginIntegracaoV3DAO::obterConexaoSasIntegraV3());
			
			$this->inserirHistoricoAcao($this->geroid, $this->autorAcaoVO->id, self::RESET_SENHA_USUARIO_V3);
			
			$this->recarregarCacheIntegradora();
			
			Logger::logInfo("\n [FINALIZADO COM SUCESSO - RESET DE SENHA] \n GERENCIADORA :-> $this->geroid \n", __FILE__, __LINE__, $this->arrayLog);
			
			return new RetornoVO(TRUE, self::MENSAGEM_SUCESSO_RESETE_SENHA, $this->buscarLoginIntegradora($this->geroid));
			
		} catch (Exception $e) {
			Logger::logError("\n [FINALIZADA COM ERRO] \n GERENCIADORA :->" . $this->geroid . " - \n EXCEPTION: " . $e->getMessage() . "\n", __FILE__, __LINE__, $this->arrayLog);
				
			$this->loginIntegracaoV3DAO->rollback(LoginIntegracaoV3DAO::obterConexaoSasIntegraV3());
			
			return new RetornoVO(FALSE, $e->getMessage(), null);
		}
			
	}
		
	/**
	 * 2º CADASTRAR AS PERMISSOES DA INTEGRADORA
	 *
	 * @param int $intid
	 * @throws Exception
	 * @return boolean
	 */
	protected function concederPermissoesIntegradoraMetodos($intid){

		if(! isset($intid) || ! intval($intid)){
			throw new Exception(self::MENSAGEM_ERRO_ID_INVALIDO);
		}

		if (! $this->loginIntegracaoV3DAO->concederPermissoesIntegradoraMetodos($intid)){
			throw new Exception(self::MENSAGEM_ERRO_AO_CONCEDER_PERMISSAO_ACESSO_METODOS);
		}
		return true;
	}

	/**
	 * 3º CRIAR ESTRUTURA DE TABELAS DE POSIÇÃO PARA A INTEGRADORA NOVA
	 *
	 * @param int $intid
	 * @throws Exception
	 * @return boolean
	 */
	protected function criarEstruturaPosicao($intid){

		if(! isset($intid) || ! intval($intid)){
			throw new Exception(self::MENSAGEM_ERRO_ID_INVALIDO);
		}

		if (! $this->loginIntegracaoV3DAO->criarEstruturaPosicao($intid)){
			throw new Exception(self::MENSAGEM_ERRO_ESTRUTURA_POSICAO);
		}
		return true;
	}

	/**
	 * 4º INSERIR TABELA CONTROLE DE PACOTE | BANCO: SASINTEGRAV3_POSICAO
	 *
	 * @param int $intid
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	protected function criarControleDePacotes($intid){

		if(! isset($intid) || ! intval($intid)){
			throw new Exception(self::MENSAGEM_ERRO_ID_INVALIDO);
		}

		if (! $this->loginIntegracaoV3DAO->criarControleDePacotes($intid)){
			throw new Exception(self::MENSAGEM_ERRO_ESTRUTURA_PACOTES);
		}
		return TRUE;
	}
	
	/**
	 *
	 * 5° FAZER LOGIN COMO NOVO USUARIO ATRAVÉS DO SOAPUI, PARA COLOCAR O
	 * USUÁRIO NO CACHE E ATUALIZAR O CACHE. -- OBS: CASO NÃO REALIZADO, SÓ IRÁ
	 * COMEÇAR CHEGAR POSIÇÃO APÓS AUTENTICAÇÃO DA INTEGRADORA.
	 * -- Reiniciar o cache acessando http://sasintegra.sascar.com.br/SasIntegra/RecarregarCache
	 *
	 */
	private function recarregarCacheIntegradora(){
	    $url = _WS_SASINTEGRA_V3_ . "/RecarregarCache";
	    $data = file_get_contents($url);
		Logger::logInfo("\n Recarregar Cache Integradora: \n $url \n" . " Data: $data", __FILE__, __LINE__, $this->arrayLog);
	}
	
	/**
	 *
	 * 6º FAZER LOGIN COMO NOVO USUARIO ATRAVÉS DO SOAPUI, PARA COLOCAR O USUÁRIO NO CACHE E ATUALIZAR O CACHE.
	 * -- OBS: CASO NÃO REALIZADO, S? IR? COMEÇAR CHEGAR POSIÇÃO APÓS AUTENTICAÇÃO DA INTEGRADORA.
	 * Reiniciar o cache acessando http://sasintegra.sascar.com.br/SasIntegra/RecarregarCache
	 *
	 * http://sasintegra.sascar.com.br/SasIntegra/SasIntegraWSService?wsdl
	 */
	private function testarIntegracao($intid){
		if(! isset($intid) || ! intval($intid)){
			throw new Exception(self::MENSAGEM_ERRO_ID_INVALIDO);
		}
	}
	
	/**
	 * Busca o cadatro da gerenciadora na intranet
	 * 
	 * @param int $geroid
	 * @throws Exception
	 * @return IntegradoraVo
	 */
	public function buscarGerenciadora($geroid){
		if (! isset($geroid) || ! intval($geroid)){
			throw new Exception(self::MENSAGEM_ERRO_ID_INVALIDO);
		}

		$vo = new integradoraVO();
		$vo = $this->loginIntegracaoV3DAO->buscarGerenciadora($geroid);

		if (! isset($vo)){
			throw new Exception(self::MENSAGEM_ERRO_GERENCIADORA_NAO_ENCONTRADA);
		}

		return $vo;
	}
	
	/**
	 * Buscar o login da integradora
	 *
	 * @param int $geroid
	 * @throws Exception
	 * @return IntegradoraVo
	 */
	public function buscarLoginIntegradora($intid){
	    if (! isset($intid) || ! intval($intid)){
	        throw new Exception(self::MENSAGEM_ERRO_ID_INVALIDO);
	    }
	    
	    $vo = new integradoraVO();
	    $vo = $this->loginIntegracaoV3DAO->buscarLoginIntegradora($intid);
	    
	    if (! isset($vo)){
	        throw new Exception(self::MENSAGEM_ERRO_lOGIN_INTEGRADORA_NAO_ENCONTRADA);
	    }
	    
	    return $vo;
	}	
	
	/**
	 * 
	 * @param string $gernome_busca
	 * @param string $busca_gersoftware
	 * @param string $buscaTipoCliente
	 * @param string $buscaComIntegracao
	 * 
	 * $busca_gersoftware = array
	 * array('tipoSoftware' => '',  'softwareNome' => 'Todos'),
	 * array('tipoSoftware' => '1', 'softwareNome' => 'CLIENTE'),
	 * array('tipoSoftware' => '6', 'softwareNome' => 'FROTAS'),
	 * array('tipoSoftware' => '3', 'softwareNome' => 'PA'),
	 * array('tipoSoftware' => '4', 'softwareNome' => 'SUPORTE SASCAR'),
	 * array('tipoSoftware' => '2', 'softwareNome' => 'TEGMA'),
	 * array('tipoSoftware' => '5', 'softwareNome' => 'TELEMETRIA')
	 * );
	 * 
	 * $buscaTipoCliente = array(
	 * array('buscaIntegracao' => "TODOS", 'descricao' => "Todos"),
	 * array('buscaIntegracao' => "NAO", 'descricao' => "Não"),
	 * array('buscaIntegracao' => "SIM", 'descricao' => "Sim")
	 * );
	 * 
	 * 
	 * @return GerenciadoraVO:null GerenciadoraVO
	 */
	public function	buscarGerenciadoraLista($gernome_busca, $busca_gersoftware, $buscaTipoCliente, $buscaComIntegracao = 'TODOS'){
		$gerenciadoraVoList = array();
				
		$resultado_pesquisa = null;
		$resultado_pesquisa = $this->loginIntegracaoV3DAO->buscarGerenciadoraLista($gernome_busca, $busca_gersoftware, $buscaTipoCliente);

		while($row = pg_fetch_object($resultado_pesquisa)){
			$vo = new GerenciadoraVO();
			$vo->geroid 	= $row->geroid;
			$vo->gernome 	= $row->gernome;
			$vo->gercnpj 	= $row->gercnpj;
			$vo->gercidade 	= $row->gercidade;
			$vo->geruf 		= $row->geruf;
			$vo->gerfone 	= $row->gerfone;
			$vo->gerfone2 	= $row->gerfone2;
			$vo->gerfone3 	= $row->gerfone3;
			$vo->gerfone0800 	= $row->gerfone0800;
			$vo->gersoftware 	= $row->gersoftware;
			$vo->gertipo 		= $row->gertipo;
			$vo->geranexo 		= $row->geranexo;
			$vo->geremail_direcionamento 	= $row->geremail_direcionamento;
			$vo->geremail_alt_placa 		= $row->geremail_alt_placa;
			$vo->gertdescricao 				= $row->gertdescricao;
			$vo->geracessochat 				= $row->geracessochat;
			$vo->possuiIntegracao = $this->loginIntegracaoV3DAO->validarSePossuiAcesso($vo->geroid);
			$vo->arquivoLog = NULL;
			
			if($vo->possuiIntegracao){
			    $arquivo = self::CAMINHO_LOG . "criar_integradora_v3_geroid_".$vo->geroid. "_log.txt";
			    if (file_exists($arquivo)) {
			        $vo->arquivoLog = self::SERVICO_DOWNLOAD . $arquivo;
			    }
			}
			
			if($buscaComIntegracao != "TODOS"){
				
				if($buscaComIntegracao == "SIM" && $vo->possuiIntegracao == TRUE){
					$gerenciadoraVoList[] = $vo;
				}
				
				if($buscaComIntegracao == "NAO" &&  $vo->possuiIntegracao == FALSE){
					$gerenciadoraVoList[] = $vo;
				}
				
			}else{
				$gerenciadoraVoList[] = $vo;				
			}
			
		}
		return $gerenciadoraVoList;
	}
	
	private function inserirHistoricoAcao($geroid, $cd_usuario, $descricaoAcao){
	    if (! $this->loginIntegracaoV3DAO->inserirHistoricoAcao($geroid, $cd_usuario, $descricaoAcao)){
	        throw new Exception(self::FALHA_AO_GERAR_HISTORICO);
	    }
	    Logger::logInfo("\n Gerar Histórico Gerenciadora: \n $geroid \n", __FILE__, __LINE__, $this->arrayLog);
	}
	
	/**
	 * Printar e logar o Objeto de retorno.
	 * @param IntegradoraVO $vo
	 * 
	 * @return void
	 */
	private function printarObjetoIntegradoraVO(IntegradoraVO $vo){
		Logger::logInfo("\n Objeto: \n" 
				. " Id da gerenciadora: " .  $vo->intid 
				. " | " . "Nome da gerencidora:  " 
				. $vo->intnome  .  " "  
				.  "\n", __FILE__, __LINE__, $this->arrayLog);
		
		if($this->imprimirNaTela){
			echo"#######################################################################";
			echo"<pre/>";
			print_r($vo);
			echo"<br/>";
			echo"#######################################################################";
		}
	}
    
	/**
	 * Validar se o departamento pode ou não executar a ação.
	 * @return boolean
	 */
	private function validarAcessoGerenciarIntegracao(){
	    if (in_array($this->autorAcaoVO->departamento, $this->departamentosAutorizadosServico)){
	        return true;
	    }
	    return false;
	}
	
}

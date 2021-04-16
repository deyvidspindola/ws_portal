<?php

require_once _MODULEDIR_ . 'Login/IntegracaoV3/VO/IntegradoraVO.php';

/**
 *
 * @author alexandre.reczcki
 *
 * $dbstring_sasintegraV3
 * $dbstring_sasintegraV3_posicao
 * psql -h 172.16.19.182 sasintegraV3 suporte
 * psql -h 172.16.19.182 sasintegraV3_posicao suporte
 *
 * @package Login/IntegracaoV3/DAO
 *
 */
class LoginIntegracaoV3DAO{
	
	private $arrayLog = array();
	
	private $conexaoV3 			= NULL;
	private $conexaoV3Posicao 	= NULL;
	private $conn 				= NULL;
	
	private $imprimirNaTela 	= false;
	
	/**
	 * Contrutor recebe definições de configuração do serviço de log
	 * 
	 * @param array $arrayLog
	 */
	public function __construct($arrayLog = array(), $imprimirNaTela = false){
		
		if($imprimirNaTela){
			$this->imprimirNaTela = $imprimirNaTela;
		}
		
		if(count($arrayLog)>0){
			$this->arrayLog = $arrayLog;
		}
	}
	
	/**
	 * Mensagens de ERRO
	 */
	const MENSAGEM_ERRO_NAO_CONEXAO = "NÃO FOI POSSIVEL CONECTAR COM O BANCO DE DADOS";
	
	/**
	 * 1 - CADASTRAR INTEGRADORA | BANCO: SASINTEGRAV3 -- OBS: a senha padrao deve ser: sascar
	 * insert into integradora(intid,intnome,intlogin,intsenha,inttipo,intdt_cadastro, intqtd_max_dias_posicoes, intqtd_max_dias_posicoes_hist)
	 * values ('886','VETTA GERENCIADORA DE RISCOS LTDA','vetta',md5('sascar'),'1',now(),'2','31');
	 * 
	 * @param int $intid
	 * @param string $intnome
	 * @param int $intlogin
	 * @param string $intsenha
	 * @param number $inttipo
	 * @param number $intqtd_max_dias_posicoes
	 * @param number $intqtd_max_dias_posicoes_hist
	 * @throws Exception
	 * @return boolean
	 */
	public function inserirIntegradora(
			$intid,
			$intnome,
			$intlogin,//USUÁ�RIO DEVE SER O MESMO SASGC: "Login SASGC" --USUARIO DEVE SER LETRA MAIUSCULO
			$intsenha = 'sascar',//SENHA MINUSCULA
			$inttipo = 1,
			$intqtd_max_dias_posicoes = 2,
			$intqtd_max_dias_posicoes_hist = 31){
	
		try {
			$sqlInsert = "insert into integradora
							(
								intid,
								intnome,
								intlogin,
								intsenha,
								inttipo,
								intdt_cadastro,
								intqtd_max_dias_posicoes,
								intqtd_max_dias_posicoes_hist
							)
							values (
								$intid,
								'$intnome',
								'$intlogin',
								md5('$intsenha'),
								'$inttipo',
								now(),
								'$intqtd_max_dias_posicoes',
								'$intqtd_max_dias_posicoes_hist'
							);
			";
			$this->printarQueryDebug($sqlInsert);
			$this->executarQuery(self::obterConexaoSasIntegraV3(), $sqlInsert);
			
			return true;
	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	/**
	 * 2 - CADASTRAR AS PERMISSOES DA INTEGRADORA | BANCO: SASINTEGRAV3 
	 * PRIMEIRO CAMPO = ID DA PERMISSAO, SEGUNDO CAMPO = INTEGRADORAID (GEROID)
	 * 
	 * @param int $intid
	 * @throws Exception
	 * @return boolean
	 */
	public function concederPermissoesIntegradoraMetodos($intid){
		try {
			$sqlInsert = "
				insert into permissao_metodo values (1, $intid);
				insert into permissao_metodo values (2, $intid);
				insert into permissao_metodo values (3, $intid);
				insert into permissao_metodo values (4, $intid);
				insert into permissao_metodo values (5, $intid);
				insert into permissao_metodo values (6, $intid);
				insert into permissao_metodo values (7, $intid);
				insert into permissao_metodo values (8, $intid);
				insert into permissao_metodo values (9, $intid);
				insert into permissao_metodo values (10, $intid);
				insert into permissao_metodo values (11, $intid);
				insert into permissao_metodo values (13, $intid);
				insert into permissao_metodo values (14, $intid);
				insert into permissao_metodo values (15, $intid);
				insert into permissao_metodo values (16, $intid);
				insert into permissao_metodo values (17, $intid);
				insert into permissao_metodo values (18, $intid);
				insert into permissao_metodo values (19, $intid);
				insert into permissao_metodo values (20, $intid);
				insert into permissao_metodo values (21, $intid);
				insert into permissao_metodo values (22, $intid);
				insert into permissao_metodo values (25, $intid);
				insert into permissao_metodo values (27, $intid);
				insert into permissao_metodo values (28, $intid);
				insert into permissao_metodo values (30, $intid);
				insert into permissao_metodo values (33, $intid);
				insert into permissao_metodo values (35, $intid);
			";
			$this->printarQueryDebug($sqlInsert);
			
			$this->executarQuery(self::obterConexaoSasIntegraV3(), $sqlInsert);
				
			return true;
			
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
		}
	}
		
	/**
	 * Validar se a gerenciadora já possui acesso no SASINTEGRA
	 * 
	 * @param int $intid
	 * @return boolean
	 */
	public function validarSePossuiAcesso($intid){
		$sqlSelect = "select * from integradora where intid = $intid;";
		$this->printarQueryDebug($sqlSelect);
		
		$rs = $this->executarQuery(self::obterConexaoSasIntegraV3(), $sqlSelect);
		
		if(pg_num_rows($rs) > 0){
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * 3 - CRIAR ESTRUTURA DE TABELAS DE POSIÇÃO PARA A INTEGRADORA NOVA | BANCO: SASINTEGRAV3_POSICAO
	 * select criar_estrutura_posicao($intid);
	 * 
	 * @param int $intid
	 * @return boolean
	 */
	public function criarEstruturaPosicao($intid){
		try {

			$sqlFunction = "select criar_estrutura_posicao($intid);";
			$this->printarQueryDebug($sqlFunction);
			
			$this->executarQuery(self::obterConexaoSasIntegraV3Posicao(), $sqlFunction);

			return true;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
		
	/**
	4 - INSERIR TABELA CONTROLE DE PACOTE | BANCO: SASINTEGRAV3_POSICAO
		insert into controle_pacote values (886, 0);
	*/
	
	/**
	 * 4 - INSERIR TABELA CONTROLE DE PACOTE | BANCO: SASINTEGRAV3_POSICAO
	 * insert into controle_pacote values (886, 0);
	 * 
	 * @param int $intid
	 * @return boolean
	 */
	public function criarControleDePacotes($intid){
		try {
			$sqlInsert = "insert into controle_pacote values ($intid, 0);";
			$this->printarQueryDebug($sqlInsert);
			
			$this->executarQuery(self::obterConexaoSasIntegraV3Posicao(), $sqlInsert);
			return true;

		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	/**
	 * Busca o cadatro da gerenciadora na intranet bd:sascar
	 * 
	 * @param int $geroid
	 * @return IntegradoraVO|NULL
	 */
	public function buscarGerenciadora($geroid){
		try {
			$sqlSelect = "select * from gerenciadora where geroid = $geroid and gerexclusao is null;";
			$this->printarQueryDebug($sqlSelect);
			
			$rs = $this->executarQuery(self::obterConexaoIntranet(), $sqlSelect);

			if(pg_num_rows($rs) > 0){
				$vo = new IntegradoraVO();
				
				$vo->intid 		= (pg_fetch_result($rs, 0, 'geroid'));
				$vo->intnome 	= (pg_fetch_result($rs, 0, 'gernome'));
				$vo->intlogin 	= (pg_fetch_result($rs, 0, 'gersasgc_usuario'));
					
				return $vo;
			}

			return NULL;

		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	public function buscarGerenciadoraLista($gernome_busca, $busca_gersoftware, $busca_tipo){
		try {
			$resultado_pesquisa = null;
			
			$query = "	SELECT 
							geroid, gernome, gercnpj, gercidade,
			                geruf, gerfone, gerfone2, gerfone3,
			                gerfone0800, gersoftware, gertipo, geranexo,
			                geremail_direcionamento, geremail_alt_placa,
			                gertdescricao, geracessochat,
			                (SELECT count(gercuoid)
			                    FROM gerenciadora_customizacao
			                    WHERE gercugeroid=geroid
							) as tem_integracao
		                FROM gerenciadora
				           LEFT JOIN gerenciadora_tipo on gertoid=gergertoid
		                WHERE gerexclusao IS NULL ";
			
			if($gernome_busca){
				$query .= " AND gernome ILIKE '%$gernome_busca%' ";
			}
			
			if($busca_gersoftware == "S"){
				$query .= " AND (gersoftware_principal = 2 OR gersoftware_secundario = 2)";
			}
			
			if($busca_gersoftware == "I"){
				$query .= " AND (gersoftware_principal = 3 OR gersoftware_secundario = 3)";
			}
			
			if($busca_gersoftware == "SS"){
				$query .= " AND gersoftware = false";
			}
			
			if($busca_gersoftware == "SI"){
				$query .= " AND gersoftware = true";
			}
			
			if($busca_tipo > 0){
				$query .= " AND gertoid = $busca_tipo ";
			}
			
			$query .= " ORDER BY gernome;";
			
			$resultado_pesquisa = pg_query(self::obterConexaoIntranet(), $query);
			
			return $resultado_pesquisa;
			
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	public function resetarSenha($intid, $intsenha){
		try {
			$sqlUpdate = "update integradora set intsenha = '$intsenha' where intid = $intid;";
			$this->printarQueryDebug($sqlUpdate);
			$this->executarQuery(self::obterConexaoSasIntegraV3(), $sqlUpdate);
			return true;
		} catch (Exception $e) {
			return false;
		}		
	}

	public function buscarLoginIntegradora($intid){
	    try {
	        $sqlSelect = "select intid, intnome, intlogin from integradora where intid = $intid;";
	        $this->printarQueryDebug($sqlSelect);
	        
	        $rs = $this->executarQuery(self::obterConexaoSasIntegraV3(), $sqlSelect);
	        
	        if(pg_num_rows($rs) > 0){
	            $vo = new IntegradoraVO();
	            
	            $vo->intid 		= (pg_fetch_result($rs, 0, 'intid'));
	            $vo->intnome 	= (pg_fetch_result($rs, 0, 'intnome'));
	            $vo->intlogin 	= (pg_fetch_result($rs, 0, 'intlogin'));
	            
	            return $vo;
	        }
	        
	        return NULL;
	        
	    } catch (Exception $e) {
	        return $e->getMessage();
	    }
	}	

	public function inserirHistoricoAcao($geroid, $cd_usuario, $descricaoAcao){
	    try {
            $sqlInsert = "insert into historico_gerenciadora (hgrobs,hgrusuoid,hgrgeroid,hgrcadastro) values ('$descricaoAcao',$cd_usuario,$geroid,current_timestamp(0))";
            $this->printarQueryDebug($sqlInsert);
            $this->executarQuery(self::obterConexaoIntranet(), $sqlInsert);
            
            return true;
	    } catch (Exception $e) {
	        return false;
	    }	
	}
	
	/**
	 * Conecta bd sascar
	 * valida se o atributo já contem a conexão aberta
	 * 
	 * @return string $conn
	 */
	public function obterConexaoIntranet(){
		if($this->conn == NULL){
			global $conn;
			return $this->conn = $conn;
		}
		return $this->conn;
	}
	
	/**
	 * Valida se o atributo já contem a conexão aberta
	 * 
	 * @return string $conexaoV3
	 * @throws Exception
	 */
	public function obterConexaoSasIntegraV3(){
		if($this->conexaoV3 == NULL){
			global $dbstring_sasintegraV3;
			if (! $this->conexaoV3 = pg_connect ($dbstring_sasintegraV3)) {
				throw new Exception(self::MENSAGEM_ERRO_NAO_CONEXAO);
			}
		}
		return $this->conexaoV3;
	}
	
	/**
	 * Valida se o atributo já contem a conexão aberta
	 * 
	 * @return string $conexaoV3Posicao
	 * @throws Exception
	 */
	public function obterConexaoSasIntegraV3Posicao(){
		if($this->conexaoV3Posicao == NULL){
			global $dbstring_sasintegraV3_posicao;
			if (! $this->conexaoV3Posicao = pg_connect ($dbstring_sasintegraV3_posicao)) {
				throw new Exception(self::MENSAGEM_ERRO_NAO_CONEXAO);
			}
		}
		return $this->conexaoV3Posicao;
	}
	
	/**
	 * Abre a transação
	 * @param string $conexao
	 */
	public function begin($conexao){
		pg_query($conexao, 'BEGIN');
	}

	/** 
	 * Finaliza um transação 
	 * @param String $conexao
	 */
	public function commit($conexao){
		pg_query($conexao, 'COMMIT');
	}
	
	/**
	 * Aborta uma transação
	 * @param String $conexao
	 */
	public function rollback($conexao){
		pg_query($conexao, 'ROLLBACK');
	}
	
	/**
	 * Submete uma query a execucao BD Postgres
	 * 
	 * @param string $conexao
	 * @param string $query
	 * @throws Exception
	 * @return resource
	 */
	private function executarQuery($conexao, $query) {
		if(!$rs = pg_query($conexao, $query)) {
			throw new Exception("ERRO AO EXECUTAR QUERY");
		}
		return $rs;
	}
	
	/**
	 * Printar e logar a string com a query que foi executada.
	 * 
	 * @param string $query
	 */
	private function printarQueryDebug($query, $imprimirNaTela=false){
		Logger::logInfo("\n \n Query: \n \n \n $query \n\n", __FILE__, __LINE__, $this->arrayLog);
		if($this->imprimirNaTela){
			echo"#######################################################################";
			echo"<pre/>";
			print_r($query);
			echo"<br/>";
			echo"#######################################################################";
		}
	}
}

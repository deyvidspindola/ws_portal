<?php

require_once _SITEDIR_ . '/lib/gerar_logs/Logger.php';
/**
 * Classe CadEspelhamento4grDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   Rafael Araújo <rafael.araujo.ext@sascar.com.br>
 *
 */
class CadEspelhamento4grDAO {

	/** Conexão com o banco de dados */
	private $conn;

	/** Usuario logado */
	private $usarioLogado;

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";
	const MENSAGEM_ERRO_NAO_CONEXAO = "NÃO FOI POSSIVEL CONECTAR COM O BANCO DE DADOS";

	public function __construct($conn) {

		//Seta a conexao na classe
        $this->conn = $conn;
        $this->usarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

		$this->arrayLog = array('nomeArqDinamico' => "cad_espelhamento_4gr", 
		        'usuario'       => $this->usarioLogado);

        //Se nao tiver nada na sessao assume usuario AUTOMATICO
        if(empty($this->usarioLogado)) {
            $this->usarioLogado = 2750;
        }
	}

	public function listarGerenciadoras(){

        $sql = 'SELECT
                    geroid,
                    formata_str(gernome) as descricao
                FROM
                    gerenciadora WHERE gerexclusao is null 
                ORDER BY
					gernome';

        $rs = pg_query($this->conn, $sql);

        $listaItens = array();
        while($row = pg_fetch_object($rs)) {

            $listaItens[] = $row;

            $i++;
        }

        return $listaItens;

	}
	
	public function listarClientes($search){

		$sql = "SELECT clioid, clino_cpf, clino_cgc, clitipo, clinome as nome 
				FROM clientes WHERE  clinome ILIKE '%$search%' ORDER BY clinome LIMIT 20";

        $rs = pg_query($this->conn, $sql);

		$listaItens = array();
		$i = 0;
        while($row = pg_fetch_object($rs)) {

			$listaItens[$i]['clioid'] = $row->clioid;
			$listaItens[$i]['nome'] = $row->nome;
			if ( $row->clitipo == 'F'){
				$listaItens[$i]['doc'] = $row->clino_cpf;
			}
			else {
				$listaItens[$i]['doc'] = $row->clino_cgc;
			}
			$listaItens[$i]['tipo'] = $row->clitipo;
            $i++;
        }

        return $listaItens;

	}

	public function cadastrar4GR($fields){

		$error = array();
		$success = array();
		#Verifica se o usuário já existe no banco
		if ( !self::verificaUsuario($fields->user_4gr) ){
			
			$id = self::obterId4GR();

			### Insere a nova 4GR na tabela Integradora
			$sql = "INSERT INTO integradora (intid, intnome, intlogin, intsenha, inttipo, intdt_cadastro, intqtd_max_dias_posicoes, intqtd_max_dias_posicoes_hist)" .
				"VALUES ('" . $id . "','" . $fields->name_4gr . "','" . $fields->user_4gr . "'," . "md5 ('" . $fields->password_4gr . "'),'" . $fields->tipo . "',now(),'2','31');";

			$rs = pg_query(self::obterConexaoSasIntegraV3(), $sql);

			if ($rs){
				
				//$st_tipo;
				if ($fields->tipo == 4){
					$st_tipo = "4°GR";
				}
				else {
					$st_tipo = "4°GR FULL";
				}
				Logger::logInfo("\n[CADASTRO 4°GR] - NOME:[$fields->name_4gr] - ID:[$id] - LOGIN:[$fields->user_4gr] - TIPO:[$st_tipo]\n", __FILE__, __LINE__, $this->arrayLog);

				### Insere as permissões nos métodos liberados para 4°GR e 4°GR Full
				// Atualizado PRDSEG-4181 -> $permissoes = array(1, 2, 4, 7, 9, 10, 13, 14, 15, 16, 18, 19, 20, 27, 33, 35, 40, 42); 
				$permissoes = self::getAllMetodos();
				if (sizeof($permissoes) == 0) { 
					return $error = array("error" => utf8_encode("Nenhum metódo encontrado.")); 
				} 

				for ($i = 0; $i < sizeof($permissoes); $i++){

					$sql = "INSERT INTO permissao_metodo (perm_met_id, perm_integradoraid) values ($permissoes[$i],$id);";
					$rs = pg_query(self::obterConexaoSasIntegraV3(), $sql);

					if (!$rs){
						return $error = array("error" => utf8_encode("Erro ao cadastrar os métodos para a 4°GR"));
					}

					Logger::logInfo("\n[METODO LIBERADO] - ID:[$permissoes[$i]]\n", __FILE__, __LINE__, $this->arrayLog);
				}

				### Insere os clientes somente para a 4°GR
				if ($fields->tipo == 4){
					for ($i=0; $i < sizeof($fields->clientes); $i++) {
						$id_cli = $fields->clientes[$i];
						$sql = "INSERT INTO integradora_visualizadora (intvintid,intvintid_permitido,intvclioid_permitido)" .
							"VALUES ($id, $fields->id_gr, $id_cli);";
						$rs = pg_query(self::obterConexaoSasIntegraV3(), $sql);

						if (!$rs){
							return $error = array("error" => "Erro ao cadastrar os clientes para a 4°GR");
						}

						Logger::logInfo("\n[CLIENTE VINCULADO] - ID:[$id_cli]\n", __FILE__, __LINE__, $this->arrayLog);
					}
				}
				else {
					$sql = "INSERT INTO integradora_visualizadora (intvintid,intvintid_permitido,intvclioid_permitido)" .
							"VALUES ($id, $fields->id_gr, 0);";
					$rs = pg_query(self::obterConexaoSasIntegraV3(), $sql);

					if (!$rs){
						return $error = array("error" => utf8_encode("Erro ao cadastrar os clientes para a 4°GR"));
					}

					Logger::logInfo("\n[CLIENTE VINCULADO] - ID:[0]\n", __FILE__, __LINE__, $this->arrayLog);
				}

				### Insere controle de pacote de posições
				$sql = "INSERT INTO controle_pacote values ($id,1,now());";
				$rs = pg_query(self::obterConexaoSasIntegraV3Posicao(), $sql);

				if (!$rs){
					return $error = array("error" => utf8_encode("Erro ao cadastrar o controle de pacote de posição para a 4°GR"));
				}

				Logger::logInfo("\n[CONTROLE DE PACOTE] - SETADO POSICAO 1\n", __FILE__, __LINE__, $this->arrayLog);
			}
			else {
				return $error = array("error" => utf8_encode("Erro ao cadastrar a 4°GR na tabela integradora"));
			}
		}
		else {
			return $error = array("error" => utf8_encode("Usuário já cadastrado no sistema"));
		}

		$success["success"] = utf8_encode("4°GR cadastrada com sucesso");
		$success["result"] = array("id_4gr" => $id);

		return $success;

	}

	public function consultar4GR($fields){

		$error = array();
		#Verifica se o usuário já existe no banco

		$sql = "SELECT intid, intnome, intlogin, inttipo FROM integradora WHERE ";

		if ($fields->name_4gr && $fields->user_4gr){
			$sql .= "(intnome LIKE '%" . $fields->name_4gr . "%' OR intlogin LIKE '%" . $fields->user_4gr . "%') AND inttipo = " . $fields->tipo . ";";
		}
		else if ($fields->user_4gr){
			$sql .= "intlogin LIKE '%" . $fields->user_4gr . "%' AND inttipo = " . $fields->tipo . ";";
		}
		else if ($fields->name_4gr){
			$sql .= "intnome LIKE '%" . $fields->name_4gr . "%' AND inttipo = " . $fields->tipo . ";";
		}
	
		$rs = self::executaQuery(self::obterConexaoSasIntegraV3(), $sql);

		for ($i = 0; $i < sizeof($rs['result']); $i++){
			if ($rs['result'][$i]['inttipo'] == 5 ){
				$rs['result'][$i]['tipo'] = utf8_encode('4°GR - FULL');
			}
			else if ($rs['result'][$i]['inttipo'] == 4){
				$rs['result'][$i]['tipo'] = utf8_encode('4°GR');
			}
		}

		if (!$rs['result']) {
			$rs['error'] = utf8_encode("Nenhuma 4°GR encontrada");
		}

		return $rs;
	}

	public function pegar4GR($fields){

		$sql = 'SELECT int.intid, int.intnome, int.intlogin, int.inttipo, intv.intvclioid_permitido, intv.intvintid_permitido
				FROM integradora AS int
				INNER JOIN integradora_visualizadora AS intv ON int.intid = intv.intvintid
				WHERE int.intid = ' . $fields->intid . ';';

		$rs = self::executaQuery(self::obterConexaoSasIntegraV3(), $sql);

		$clientes = array();
		for ($i = 0; $i< sizeof($rs['result']); $i++){
			$cliente = $rs['result'][$i]['intvclioid_permitido'];

			$sql = 'SELECT clioid, clino_cpf, clino_cgc, clitipo, clinome FROM clientes
					WHERE clioid = ' . $cliente . ';';
			$res = self::executaQuery($this->conn, $sql);

			$clientes[$i]['clitipo'] = $res['result'][0]['clitipo'];
			$clientes[$i]['clinome'] = $res['result'][0]['clinome'];
			$clientes[$i]['clioid'] = $res['result'][0]['clioid'];

			if ($res['result'][0]['clitipo'] == 'J'){
				$clientes[$i]['doc'] = $res['result'][0]['clino_cgc'];
			}
			else {
				$clientes[$i]['doc'] = $res['result'][0]['clino_cpf'];
			}
		}

		$rs['result'] = array(
			'intid' => $rs['result'][0]['intid'],
			'intlogin' => $rs['result'][0]['intlogin'],
			'intnome' => $rs['result'][0]['intnome'],
			'inttipo' => $rs['result'][0]['inttipo'],
			'gr_id' => $rs['result'][0]['intvintid_permitido']
		);

		$rs['clientes'] = $clientes;

		if(!$clientes){
			return array("error" => utf8_encode("Esta 4°GR não possui nenhum cliente vinculado, verificar com o administrador do sistema"));
		}
		
		return $rs;
	}

	public function salvar4GR($fields){

		if ($fields->password_4gr){
			$sql = "UPDATE integradora SET intsenha = md5 ('" . $fields->password_4gr . "') WHERE intid =" . $fields->id_4gr . ";";

			$rs = self::executaQuery(self::obterConexaoSasIntegraV3(), $sql);

			Logger::logInfo("\n[4°GR ALTERACAO DE SENHA - ID:[$fields->id_4gr]\n", __FILE__, __LINE__, $this->arrayLog);

		}

		if ($fields->clientes){
			$sql = 'DELETE FROM integradora_visualizadora WHERE intvintid = ' . $fields->id_4gr . ';';

			$rs = pg_query(self::obterConexaoSasIntegraV3(), $sql);

			if ($fields->tipo == 4){
				for ($i=0; $i < sizeof($fields->clientes); $i++) {
					$id_cli = $fields->clientes[$i];
					$sql = "INSERT INTO integradora_visualizadora (intvintid,intvintid_permitido,intvclioid_permitido)" .
						"VALUES ($fields->id_4gr, $fields->id_gr, $id_cli);";
					$rs = pg_query(self::obterConexaoSasIntegraV3(), $sql);

					if (!$rs){
						return $error = array("error" => utf8_encode("Erro ao cadastrar os clientes para a 4°GR"));
					}

					Logger::logInfo("\n[4°GR ALTERACAO DE CLIENTES - ID:[$fields->id_4gr] - ID_CLIENTE:[$id_cli]\n", __FILE__, __LINE__, $this->arrayLog);
				}
			}
		}

		$sql = 'SELECT int.intid, int.intnome, int.intlogin, int.inttipo, intv.intvclioid_permitido, intv.intvintid_permitido
				FROM integradora AS int
				INNER JOIN integradora_visualizadora AS intv ON int.intid = intv.intvintid
				WHERE int.intid = ' . $fields->id_4gr . ';';

		$rs = self::executaQuery(self::obterConexaoSasIntegraV3(), $sql);

		$clientes = array();
		for ($i = 0; $i< sizeof($rs['result']); $i++){
			$cliente = $rs['result'][$i]['intvclioid_permitido'];

			$sql = 'SELECT clioid, clino_cpf, clino_cgc, clitipo, clinome FROM clientes
					WHERE clioid = ' . $cliente . ';';
			$res = self::executaQuery($this->conn, $sql);

			$clientes[$i]['clitipo'] = $res['result'][0]['clitipo'];
			$clientes[$i]['clinome'] = $res['result'][0]['clinome'];
			$clientes[$i]['clioid'] = $res['result'][0]['clioid'];

			if ($res['result'][0]['clitipo'] == 'J'){
				$clientes[$i]['doc'] = $res['result'][0]['clino_cgc'];
			}
			else {
				$clientes[$i]['doc'] = $res['result'][0]['clino_cpf'];
			}
		}

		$rs['result'] = array(
			'intid' => $rs['result'][0]['intid'],
			'intlogin' => $rs['result'][0]['intlogin'],
			'intnome' => $rs['result'][0]['intnome'],
			'inttipo' => $rs['result'][0]['inttipo'],
			'gr_id' => $rs['result'][0]['intvintid_permitido']
		);

		$rs['clientes'] = $clientes;
		$rs['success'] = utf8_encode('Alterações salvas com sucesso');

		return $rs;
	}

	public function remover4GR($fields){

		$sql = "SELECT * FROM controle_pacote WHERE cont_pac_intid = $fields->id_4gr";
		$rs = self::executaQuery(self::obterConexaoSasIntegraV3Posicao(), $sql);

		$posicao =  $rs['result'][0]['cont_pac_ultidpacote'];

		Logger::logInfo("\n[REMOVENDO CONTROLE DE PACOTE] - SETADO POSICAO: $posicao\n", __FILE__, __LINE__, $this->arrayLog);
		
		$sql = "DELETE FROM controle_pacote WHERE cont_pac_intid = $fields->id_4gr";
		$rs = pg_query(self::obterConexaoSasIntegraV3Posicao(), $sql);

		//////////////////////////////////////////////////
		$sql = "SELECT * FROM  integradora_visualizadora WHERE intvintid = $fields->id_4gr";
		$rs = self::executaQuery(self::obterConexaoSasIntegraV3(), $sql);

		for ($i = 0; $i< sizeof($rs['result']); $i++){
			$id_cliente =  $rs['result'][$i]['intvclioid_permitido'];
			Logger::logInfo("\n[REMOVENDO CLIENTE] - ID:[$id_cliente]\n", __FILE__, __LINE__, $this->arrayLog);
		}
		$sql = "DELETE FROM integradora_visualizadora WHERE intvintid = $fields->id_4gr";
		$rs = pg_query(self::obterConexaoSasIntegraV3(), $sql);

		//////////////////////////////////////////////////
		$sql = "SELECT * FROM  permissao_metodo WHERE perm_integradoraid = $fields->id_4gr";
		$rs = self::executaQuery(self::obterConexaoSasIntegraV3(), $sql);

		for ($i = 0; $i< sizeof($rs['result']); $i++){
			$id_metodo =  $rs['result'][$i]['perm_met_id'];
			Logger::logInfo("\n[REMOVENDO METODO] - ID:[$id_metodo]\n", __FILE__, __LINE__, $this->arrayLog);
		}

		$sql = "DELETE FROM permissao_metodo where perm_integradoraid = $fields->id_4gr";
		$rs = pg_query(self::obterConexaoSasIntegraV3(), $sql);
		
		//////////////////////////////////////////////////

		Logger::logInfo("\n[REMOVENDO 4°GR] - ID:[$fields->id_4gr]\n", __FILE__, __LINE__, $this->arrayLog);

		$sql = "DELETE FROM integradora WHERE intid = $fields->id_4gr";
		$rs = pg_query(self::obterConexaoSasIntegraV3(), $sql);

		return $success = array("success" => utf8_encode("4°GR removida com sucesso"));
	}
	
	public function obterConexaoSasIntegraV3(){
		if($this->dbstring_sasintegraV3 == NULL){
			global $dbstring_sasintegraV3;
			if (! $this->dbstring_sasintegraV3 = pg_connect ($dbstring_sasintegraV3)) {
				throw new Exception(self::MENSAGEM_ERRO_NAO_CONEXAO);
			}
		}
		return $this->dbstring_sasintegraV3;
	}

	public function obterConexaoSasIntegraV3Posicao(){
		if($this->dbstring_sasintegraV3_posicao == NULL){
			global $dbstring_sasintegraV3_posicao;
			if (! $this->dbstring_sasintegraV3_posicao = pg_connect ($dbstring_sasintegraV3_posicao)) {
				throw new Exception(self::MENSAGEM_ERRO_NAO_CONEXAO);
			}
		}
		return $this->dbstring_sasintegraV3_posicao;
	}

	public function verificaUsuario($userName){
		$sql = "SELECT intid FROM integradora WHERE intlogin = " . "'" . $userName . "';";

		$rs = self::executaQuery(self::obterConexaoSasIntegraV3(), $sql);
		if ($rs['result']){
			return true;
		}
		else {
			return false;
		}
	}

	public function obterId4GR(){
		$sql = "SELECT intid FROM integradora WHERE intid >= 50000 ORDER BY intid DESC LIMIT 1";

		$rs = self::executaQuery(self::obterConexaoSasIntegraV3(), $sql);
		
		$var = intval($rs['result'][0]['intid']);
		$id = $var + 1;
		
		return $id;
	}

	public function executaQuery($conn, $sql){
		
		$rs = pg_query($conn, $sql);

		$qtde_fields = pg_num_fields($rs);
		$listaItens = array();
		$list = array();
		$i = 0;
		//$row = pg_fetch_object($rs);

		while($row = pg_fetch_object($rs)) {

			for($j = 0; $j < $qtde_fields; $j++){

				$fieldname = pg_field_name($rs, $j);
				$listaItens[$i]["$fieldname"] = utf8_encode($row->$fieldname);
			}
			$i++;
		}

		$list = array('result' => $listaItens);
		return $list;
	}

	public function getAllMetodos () 
	{ 
		$sql = 'SELECT mtdid FROM metodo WHERE mtdid > 0 ORDER BY mtdid';
		$rs = pg_query(self::obterConexaoSasIntegraV3(), $sql);
		$metodos = array(); 
		while($row = pg_fetch_object($rs)) {
			$metodos[] = $row->mtdid;
		}
		return $metodos; 
	} 
	
}
?>
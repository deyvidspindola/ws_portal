<?php 
/**
 * Gravar histórico da liberação de acesso ao usuário
 * @author Allan Helfstein
 * @since 13/11/2017
 */
class ManPermissaoFuncaoCargoDAO {

	private $conn;

	private $idHistorico;
	private $idFuncao;
	private $idCargo;
	private $idUsuario;
	private $tipo;

	const FUNCAOPERMISSAOCARGOHISTORICO = "funcao_permissao_cargo_historico AS fpch";

	public function __construct($conn) 
	{
		$this->conn 	  = $conn;

		$this->idFuncao  = null;
		$this->idCargo	  = null;
		$this->idUsuario = null;
	}

	public function getFuncao()
	{
		return $this->idFuncao;
	}

	public function setFuncao($funcao = null)
	{
		$this->idFuncao = $funcao;
	}
	public function getCargo()
	{
		return $this->idCargo;
	}

	public function setCargo($cargo = null)
	{
		$this->idCargo = $cargo;
	}

	public function getUsuario()
	{
		return $this->idUsuario;
	}

	public function setUsuario($usuario = null)
	{
		$this->idUsuario = $usuario;
	}


	public function getTipo()
	{
		return $this->tipo;
	}

	public function setTipo($tipo = null)
	{
		$this->tipo = $tipo;
	}

	public function gravaHistorico()
	{
		$query =  "INSERT INTO "
				 . self::FUNCAOPERMISSAOCARGOHISTORICO 
				 . " (fpchcadastro,fpchprhoid,fpchfuncoid,fpchusuoid) "
				 . " VALUES "
			 	 . " (NOW(),$this->idCargo,$this->idFuncao,$this->idUsuario)";
	 	$execute   = pg_query($this->conn, $query);
 	 	 
	}

	public function getHistorico()
	{
		$query = "SELECT 
		fpch.fpchprhoid, 
		TO_CHAR(fpch.fpchcadastro, 'dd/mm/yyyy HH24:MI') AS fpchcadastro, 
		string_agg(fun.funcnome,', ') as funcao, 
						usu.cd_usuario,
						usu.nm_usuario,
						usu.ds_login,
						fpch.fpchtipo 
				  FROM "
	 		   . self::FUNCAOPERMISSAOCARGOHISTORICO 
	 		   . " LEFT JOIN perfil_rh AS prh ON prh.prhoid = fpch.fpchprhoid "
	 		   . " LEFT JOIN funcao AS fun ON fun.funcoid = fpch.fpchfuncoid "
	 		   . " LEFT JOIN usuarios AS usu ON usu.cd_usuario = fpch.fpchusuoid  "
	 		   . " WHERE 1=1 "
	 		   . " AND fpch.fpchprhoid = $this->idCargo"
	 		   . " GROUP BY fpch.fpchtipo, fpch.fpchprhoid, TO_CHAR(fpchcadastro, 'dd/mm/yyyy HH24:MI'), usu.cd_usuario, usu.nm_usuario, usu.ds_login"
			   . " ORDER BY TO_CHAR(fpchcadastro, 'dd/mm/yyyy HH24:MI') DESC; ";
			   //print $query;
	 	$execute = pg_query($this->conn, $query);
	 	return pg_fetch_all($execute);
 	 	 
	}	
}


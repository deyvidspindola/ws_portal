<?php 
/**
 * Gravar histórico da liberação de acesso ao usuário
 * @author Allan Helfstein
 * @since 13/11/2017
 */
class ManPermissaoPaginaCargoDAO {

	private $conn;

	private $idHistorico;
	private $idPagina;
	private $idCargo;
	private $idUsuario;
	private $tipo;

	const PAGINAPERMISSAOCARGOHISTORICO = "pagina_permissao_cargo_historico AS ppch";

	public function __construct($conn) 
	{
		$this->conn 	  = $conn;

		$this->idPagina  = null;
		$this->idCargo	  = null;
		$this->idUsuario = null;
	}

	public function getPagina()
	{
		return $this->idPagina;
	}

	public function setPagina($pagina = null)
	{
		$this->idPagina = $pagina;
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
				 . self::PAGINAPERMISSAOCARGOHISTORICO 
				 . " (ppchcadastro,ppchprhoid,ppchpagoid,ppchusuoid) "
				 . " VALUES "
			 	 . " (NOW(),$this->idCargo,$this->idPagina,$this->idUsuario)";
	 	$execute   = pg_query($this->conn, $query);
 	 	 
	}

	public function getHistorico()
	{
		$query = "SELECT ppch.ppchprhoid, TO_CHAR(ppch.ppchcadastro, 'dd/mm/yyyy HH24:MI') AS ppchcadastro, string_agg(pag.pagtitulo,', ') as paginas, 
						usu.cd_usuario,
						usu.nm_usuario,
						usu.ds_login,
						ppch.ppchtipo 
				  FROM "
	 		   . self::PAGINAPERMISSAOCARGOHISTORICO 
	 		   . " LEFT JOIN perfil_rh AS prh ON prh.prhoid = ppch.ppchprhoid "
	 		   . " LEFT JOIN pagina AS pag ON pag.pagoid = ppch.ppchpagoid "
	 		   . " LEFT JOIN usuarios AS usu ON usu.cd_usuario = ppch.ppchusuoid  "
	 		   . " WHERE 1=1 "
	 		   . " AND ppch.ppchprhoid = $this->idCargo"
	 		   . " GROUP BY ppch.ppchtipo, ppch.ppchprhoid, TO_CHAR(ppchcadastro, 'dd/mm/yyyy HH24:MI'), usu.cd_usuario, usu.nm_usuario, usu.ds_login"
			   . " ORDER BY TO_CHAR(ppchcadastro, 'dd/mm/yyyy HH24:MI') DESC; ";
			   //print $query;
	 	$execute = pg_query($this->conn, $query);
	 	return pg_fetch_all($execute);
 	 	 
	}	
}


<?php 
/**
 * Gravar histórico da liberação de acesso ao usuário
 * @author Allan Helfstein
 * @since 04/12/2017
 */
class ManUsuarioDAO {

	private $conn;

	public function __construct($conn) 
	{
		$this->conn 	  = $conn;
	}

	
	public function getUsuariosPorCargo($get_prhoid)
	{
		
	    $sqlUsuarioPorCargo = " 
	        SELECT 
	            usu.cd_usuario,
	            usu.nm_usuario,
	            prh.prhperfil
	         FROM 
	                perfil_rh as prh 
	        LEFT JOIN usuarios as usu ON usu.usucargooid = prh.prhoid
	        WHERE  
	             prh.prhoid = {$get_prhoid}
	          -- AND
	              -- usuario_exclusao IS NULL
	        ";
	    $dados = pg_query($this->conn, $sqlUsuarioPorCargo);
	    // $count = pg_num_rows($dados);
	        
	    // if($count < 1)
	    // {
	    //     $resultado['erro'] = "Não foi possível encontrar usuários com este cargo.";
	    // }

	    // $resultado['quantidade'] = $count;
	    // $resultado['resultado'] = pg_fetch_all($dados);

	    return pg_fetch_all($dados);
	    // return $resultado;
	}

}

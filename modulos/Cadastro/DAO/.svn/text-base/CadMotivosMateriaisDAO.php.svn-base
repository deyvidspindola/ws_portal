<?php 

class CadMotivosMateriaisDAO {
	private $conn;
	
	public function CadMotivosMateriaisDAO()
	{	
		global $conn;
		$this->conn = $conn;
	}
	
        
        /**
	 * Alimentar a combo de Tipos
	 * 
	 * @author Willian Ouchi
	 * @return array
	 * 
	 */	
	public function getTipos() {
		$retorno = array();
		
		$sql = "
                    SELECT 
                        ostoid,
                        ostdescricao 
                    FROM 
                        os_tipo 
                    WHERE 
                        ostdt_exclusao IS NULL 
                    ORDER BY 
                        ostdescricao
                ";
		
		if (!$res = pg_query($this->conn,$sql)) {
			throw new Exception("Falha ao pesquisar tipos",1);
		}
		
		while ($linha = pg_fetch_assoc($res)) {
			$retorno[$linha['ostoid']] = $linha['ostdescricao'];
		}
		
		return $retorno;
	}
        
        
	/**
	 * Alimentar a combo de motivos para instalação e não apresentar tipo de Isntalção de Equipamento
	 * 
	 * @author Vanessa Rabelo
	 * @return array
	 * 
	 */	
	public function getMotivos($tipo = null) {
		$retorno = array();
		$where = "";
                
                if (!empty($tipo)){
                    $where .= " AND otiostoid = $tipo";
                }
                
		$sql = "
                    SELECT 
                            otioid, otidescricao
                    FROM 
                            os_tipo_item
                    WHERE
                        otidt_exclusao IS NULL    
                        $where
                    ORDER BY
                            otidescricao
                ";

		if (!$res = pg_query($this->conn,$sql)) {
			throw new Exception("Falha ao pesquisar motivos",1);
		}
		
		while ($linha = pg_fetch_assoc($res)) {
			$retorno[$linha['otioid']] = utf8_encode($linha['otidescricao']);
		}
		
		return $retorno;
	}
	
	/**
	 * Alimentar a combo de produto
	 *
	 * @author Leandro Ivanaga
	 * @return array
	 *
	 */
	public function getProdutos() {
		$retorno = array();
		
		/* 
		STI 80781
		Na combobox Produto, deve aparecer apenas referente aos grupos:
			ID: 7  - Tipo: MATERIAL DE INSTALAÇÃO
			ID: 34 - Tipo: IMOBILIZADO PARA LOCAÇÃO
			ID: 41 - Tipo: MATERIAL MANUTENÇAO DE EQUIPAMENTOS
			ID: 42 - Tipo: IMOBILIZADO EM DEMONSTRAÇÃO
			ID: 44 - Tipo: MATERIAL DE REVENDA
		*/
		$gruposPermitidos = array("7,34,41,42,44");
		
		// Tranforma o array de grupos permitidos em string, para utilização na consulta SQL
		$gruposPermitidos = implode(',', $gruposPermitidos );
		
		try{
			$sql = "
				SELECT 
					prod.prdoid, prod.prdproduto, prod.prdgrmoid, grpmat.grmdescricao
				FROM 
					produto prod
				INNER JOIN 
					grupo_material grpmat ON prod.prdgrmoid = grpmat.grmoid
				WHERE 
					prod.prddt_exclusao IS NULL 
					AND prod.prdstatus='A' 
					AND prod.prdtecnologia='t'
					AND grpmat.grmoid in ($gruposPermitidos)
				ORDER BY 
					prod.prdproduto ASC
			";
		
			$res = pg_query($this->conn,$sql);
			if (!$res) {
				throw new Exception("Falha ao pesquisar os produtos.",1);
			}
		
			if (pg_num_rows($res) == 0) {
				throw new Exception("Não foi encontrado nenhum produto.",1);
			}
			
			while ($linha = pg_fetch_assoc($res)) {
				$retorno[] = array(
							"prdoid"  		=> $linha['prdoid'],
							"prdproduto" 	=> utf8_encode($linha['prdproduto'])
						);
			}

		}catch (Exception $e){
			$retorno = array(
							"error" => $e->getCode(),
							"msg" 	=> utf8_encode($e->getMessage())
					);
			
		}
		return $retorno;
	}
        

	/**
	 * Alimentar a combo de materiais
	 *
	 * @author Vanessa Rabelo
	 * @return array
	 *
	 */
	public function getMateriais() {
		$retorno = array();
	
		$sql = "SELECT
					prdoid, prdproduto
				FROM
					produto
				WHERE 
					prddt_exclusao is null
				ORDER BY
					prdproduto";
	
		if (!$res = pg_query($this->conn,$sql)) {
			throw new Exception("Falha ao pesquisar materiais",1);
		}
	
		while ($linha = pg_fetch_assoc($res)) {
			$retorno[$linha['prdoid']] = $linha['prdproduto'];
		}
	
		return $retorno;
	}

	/**
	 * Pequisa de materiais materiais
	 *
	 * @author Leandro Ivanaga 17/09/2019 ASM-4722
	 * @return array
	 *
	 */
	public function pesquisarMateriais($filtros = array()) {
		$retorno = array();

		$where = "";
		if(!empty($filtros['material'])) {
			$where = "AND prdproduto ilike '%".$filtros['material']."%'";
		}

		$sql = "SELECT
					prdoid, prdproduto
				FROM
					produto
				WHERE
					prddt_exclusao IS NULL
					$where
				ORDER BY
					prdproduto";

		if (!$res = pg_query($this->conn,$sql)) {
			throw new Exception("Erro ao pesquisar materiais",1);
		}

		$i=0;
		while ($linha = pg_fetch_assoc($res)) {
			$retorno[$i]['prdoid'] = $linha['prdoid'];
			$retorno[$i]['prdproduto'] = utf8_encode($linha['prdproduto']);
			$i++;
		}

		return $retorno;
	}

	

	/**
	 * Retorna os materiais referentes ao projeto de Motivos
	 * 
	 * @param unknown_type $motivo
	 * @return array
	 */
	public function getMateriaisMotivo($motivo) {
		$retorno = array();
		
		$motivo = (!empty($motivo)) ? $motivo : 0;
		
		$sql = "SELECT 
					mproid, mprprdoid, prdproduto,
					CASE WHEN mpressencial THEN 'Sim' ELSE '' END AS mpressencial
				FROM  
					motivo_produto 
					INNER JOIN produto ON prdoid=mprprdoid
				WHERE
					mprotioid=$motivo";
		
		
		
		if (!$res = pg_query($this->conn,$sql)) { 	
			throw new Exception("Falha ao pesquisar materiais",1);
		}
		
	
		$i=0;
		while ($linha = pg_fetch_assoc($res)) {
			$retorno[$i]['mproid'] = $linha['mproid'];
			$retorno[$i]['mprprdoid'] = $linha['mprprdoid'];
			$retorno[$i]['prdproduto'] = utf8_encode($linha['prdproduto']);
			$retorno[$i]['essencial'] = utf8_encode($linha['mpressencial']);
			$i++;
		}		
		
		return $retorno;
	}	
	
	/**
	 * Retorna os materiais referentes ao projeto de Motivos e Produto 
	 *
	 * @param  $motivo, $produto
	 * @return array
	 */
	public function getMateriaisMotivoProduto($motivo, $produto) {
		$retorno = array();
					
		$motivo = (!empty($motivo)) ? $motivo : 0;
		$produto = (!empty($produto)) ? $produto : 0;
		
		$sql = "SELECT 
					mpm.mpmoid AS materialoid, mat.prdproduto AS materialdescricao,
					CASE WHEN mpm.mpmessencial THEN 'Sim' ELSE '' END AS materialessencial
				FROM motivo_produto_material mpm
				INNER JOIN 
					os_tipo_item oi ON oi.otioid = mpm.mpmotioid
				INNER JOIN 
					produto p ON p.prdoid = mpm.mpmprdoid 
				INNER JOIN 
					produto mat ON mat.prdoid = mpm.mpmmatoid
				WHERE 
					mpm.mpmotioid = $motivo 
					AND mpm.mpmprdoid = $produto
				";
				
		if (!$res = pg_query($this->conn,$sql)) {
			throw new Exception("Falha ao pesquisar materiais.",1);
		}
	
		$i=0;
		while ($linha = pg_fetch_assoc($res)) {
			$retorno[$i]['mproid'] = $linha['materialoid'];
			$retorno[$i]['prdproduto'] = utf8_encode($linha['materialdescricao']);
			$retorno[$i]['essencial'] = utf8_encode($linha['materialessencial']);
			$i++;
		}
	
		return $retorno;
	}
	
	public function setMaterial($motivo,$material,$essencial) {
		$sqlInsert = "	INSERT INTO motivo_produto
							(mprprdoid,mprotioid,mpressencial)
						VALUES
							($material,$motivo,$essencial)";
		
		if (!$resInsert = pg_query($this->conn,$sqlInsert)) {
			throw new Exception("INSERT INTO motivo_produto(mprprdoid,mprotioid) VALUES ($material,$motivo)",1);
		}
	}
	/**
	 * Função: Salvar o material relacionado ao motivo e produto
	 */
	public function setMaterialMotivoProduto($motivo,$produto,$material,$essencial) {
		$sqlInsert = "	
				INSERT INTO 
					motivo_produto_material
						(mpmotioid, mpmprdoid, mpmmatoid, mpmessencial) 
				VALUES 
					($motivo, $produto, $material, $essencial);
				";
	
		if (!$resInsert = pg_query($this->conn,$sqlInsert)) {
			throw new Exception("	
				INSERT INTO motivo_produto_material	(mpmotioid, mpmprdoid, mpmmatoid) VALUES ($motivo, $produto, $material);",1);
		}
	}
	
	public function delMaterial($motivo,$relacao) {
		$sqlDelete = "	DELETE FROM 
							motivo_produto
						WHERE
							mproid = $relacao";
	
		if (!$resDelete = pg_query($this->conn,$sqlDelete)) {
			throw new Exception("Falha ao excluir material ",1);
		}
	}
	
	public function delMaterialMotivoProduto($motivo,$produto,$relacao) {
		$sqlDelete = "	
						DELETE FROM
							motivo_produto_material
						WHERE
							mpmoid = $relacao";
	
		if (!$resDelete = pg_query($this->conn,$sqlDelete)) {
			throw new Exception("Falha ao excluir material ",1);
		}
	}
	
}



?>
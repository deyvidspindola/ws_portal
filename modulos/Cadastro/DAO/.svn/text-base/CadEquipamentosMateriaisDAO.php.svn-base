<?php

class CadEquipamentosMateriaisDAO {
	private $conn;

	public function CadEquipamentosMateriaisDAO($conn)
	{
		$this->conn = $conn;
	}

	/**
	 * Alimentar a combo de equipamentos
	 *
	 * @author Rafael Dias
	 * @return array
	 *
	 */
	public function getEquipamentos() {
		$retorno = array();

		$sql = "SELECT
					eproid, eprnome
				FROM
					equipamento_projeto
				ORDER BY
					eprnome";

		if (!$res = pg_query($this->conn,$sql)) {
			throw new Exception("Falha ao pesquisar equipamentos.",1);
		}

		while ($linha = pg_fetch_assoc($res)) {
			$retorno[$linha['eproid']] = $linha['eprnome'];
		}

		return $retorno;
	}

	/**
	 * Alimentar a combo de classes
	 *
	 * @author Rafael Dias
	 * @return array
	 *
	 */
	public function getClasses() {
		$retorno = array();

		$sql = "SELECT
					eqcoid, eqcdescricao
				FROM
					equipamento_classe
				WHERE
					eqcinativo IS NULL
				ORDER BY
					eqcdescricao";

		if (!$res = pg_query($this->conn,$sql)) {
			throw new Exception("Falha ao pesquisar classes.",1);
		}

		while ($linha = pg_fetch_assoc($res)) {
			$retorno[$linha['eqcoid']] = $linha['eqcdescricao'];
		}

		return $retorno;
	}

	/**
	 * Alimentar a combo de materiais
	 *
	 * @author Rafael Dias
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
					prddt_exclusao IS NULL
				ORDER BY
					prdproduto";

		if (!$res = pg_query($this->conn,$sql)) {
			throw new Exception("Erro ao pesquisar materiais",1);
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
	 * Retorna os materiais referentes ao projeto de equipamento
	 *
	 * @param unknown_type $filtros
	 * @return array
	 */
	public function getMateriaisEquipamento($filtros) {
		$retorno = array();

		$equipamento = (!empty($filtros['equipamento'])) ? $filtros['equipamento'] : 0;
        $classe = (!empty($filtros['classe'])) ? $filtros['classe'] : 0;

        if($classe ==0 && $equipamento !=0){
            $where = " eppeproid=$equipamento ";
        }
        if($classe !=0 && $equipamento ==0){
            $where = " eqcoid=$classe ";
        }
        if($classe !=0 && $equipamento !=0){
            $where = " eppeproid =$equipamento and eqcoid=$classe ";
        }
        if($classe ==0 && $equipamento ==0){
            $where = " eppeproid =$equipamento and eqcoid=$classe ";
        }

		$sql = "SELECT
					eppoid, eppprdoid, prdproduto, eqcdescricao
				FROM
					equipamento_projeto_produto
					INNER JOIN produto ON prdoid=eppprdoid
					LEFT JOIN equipamento_classe ON eqcoid=eppeqcoid
				WHERE
					$where";

		if (!$res = pg_query($this->conn,$sql)) {
			throw new Exception("Falha ao pesquisar materiais.",1);
		}

		$i=0;
		while ($linha = pg_fetch_assoc($res)) {
			$retorno[$i]['eppoid'] = $linha['eppoid'];
			$retorno[$i]['eppprdoid'] = $linha['eppprdoid'];
			$retorno[$i]['prdproduto'] = utf8_encode($linha['prdproduto']);
			$descricao = $linha['eqcdescricao'];
			$descricao = (strlen($descricao)>0) ? $descricao : 'Todas';
			$retorno[$i]['eqcdescricao'] = utf8_encode($descricao);
			$i++;
		}

		return $retorno;
	}

	public function setMaterial($equipamento, $material, $classe) {
		if($classe == NULL || strtolower($classe) == 'null') {
			throw new Exception("Falha ao cadastrar material sem Classe",1);
			return false;
		}
		$sqlInsert = "	INSERT INTO equipamento_projeto_produto
							(eppprdoid,eppeproid,eppeqcoid)
						VALUES
							($material, $equipamento, $classe)";

		if (!$resInsert = pg_query($this->conn, $sqlInsert)) {
			throw new Exception("Falha ao cadastrar material",1);
		}
	}

	public function delMaterial($equipamento,$relacao) {
		$sqlDel = "	DELETE FROM
						equipamento_projeto_produto
					WHERE
						eppeproid=$equipamento
						AND eppoid=$relacao";

		if (!$resDel = pg_query($this->conn,$sqlDel)) {
			throw new Exception("Falha ao excluir material.",1);
		}
	}

}

?>
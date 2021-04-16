<?php
/**
 * Relatório de posição de estoque
 * @author Bruno Luiz Kumagai Aldana
 * @since 18/05/2015
 */
class RelPosicaoEstoqueDAO {

	/**
	 * Link de conexão
	 * @var resource
	 */
	private $conn;

	/**
	 * Construtor
	 * @param resource $conn
	 */
	public function __construct($conn) {
		$this->conn = $conn;
	}

	/**
	 * Consulta data posição 
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getDataPosicaoEstoque($filtros = '') {
		$retorno  =  array();
		if (!empty($filtros['data_posicao'])) {
			$campos = " petoid, petdt_posicao ";
			$filtro = " WHERE petdt_posicao::date = '".$filtros['data_posicao']."' ";
		}else{
			$campos = "DISTINCT(petdt_posicao) ";
		}
		$sql = "SELECT 
				$campos
				FROM 
					posicao_estoque_trimestral 
				$filtro
				ORDER BY 
					petdt_posicao DESC ";
	
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao retornar data posição estoque ");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $row->petoid;
			$retorno[$i]['data'] = date('d/m/Y', strtotime( $row->petdt_posicao));
			$i++;
		}
	
		return $retorno;
	}
	
	/**
	 * Consulta lista de representantes
	 * @param String repstatus
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getRepresentanteList($repstatus = '') {
	
		$retorno  =  array();
	
		
		if (!empty($repstatus)) {
			$filtro = " WHERE repstatus = '".trim($repstatus)."' ";
		}
		
		$sql = "SELECT 
		        	repoid,repnome 
				FROM 
					representante $filtro 
				ORDER BY 
					repnome";
		  
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao consultar representantes ");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) { 
			$retorno[$i]['id'] = $row->repoid;
			$retorno[$i]['nome'] = utf8_encode($row->repnome);
			$i++;
		}
		 
		return $retorno;
	}
	
	/**
	 * Consulta estados brasileiros 
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getUfList() {
		$retorno  =  array();
		
		$sql = "SELECT 
					ufuf 
				FROM 
					uf 
				ORDER BY 
					ufuf";
		 
		
		if (!$rs = pg_query($this->conn, $sql)){
		throw new ErrorException("Erro ao retornar estados ");
		}
		
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
		$retorno[$i]['id'] = $row->ufoid;
		$retorno[$i]['uf'] = $row->ufuf; 
		$i++;
		}
 
	    return $retorno; 
	}
	/**
	 * Consulta cidades pelo estado
	 * @param String uf
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getCidadesList($uf = '') {
	
		$retorno  =  array();
	  
		if (!empty($uf)) {
			$filtro .= " and ciduf = '".trim($uf)."' ";  
		}
		
		$sql = "SELECT 
					ciddescricao,cidoid 
				FROM 
					cidade 
				WHERE 
					cidexclusao is null $filtro 
				ORDER BY 
					ciddescricao";
		$sql .= "  ";
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao consultar cidades ");
		}
	
		$i = 0;
		while ($row = pg_fetch_object($rs)) {
			$retorno[$i]['id'] = $row->cidoid; 
			$retorno[$i]['label'] = utf8_encode($row->ciddescricao);
			$i++;
		}
	 
		return $retorno; 
	}
 
	/**
	 * Consulta Relatório data posição estoque trimestral
	 * @param Array filtros
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getRelatorioPosicaoEstoque($filtros) {
		$filtro = '';
		// Validações data posição obrigatória
		if (!isset($filtros['data_posicao_estoque'])) {
		 	throw new Exception("Período não informado");
	 	}
	 	$filtros['data_posicao_estoque'] = implode("-",array_reverse(explode("/",$filtros['data_posicao_estoque']))); 
		$filtro .= " pet.petdt_posicao::date = '".$filtros['data_posicao_estoque']."' "; 
		
		// Status representante
		if (isset($filtros['repstatus']) && !empty($filtros['repstatus'])) {
			$filtro .= " AND repstatus = '".$filtros['repstatus']."' ";
		}
		
		// id do representante
		if (isset($filtros['repoid']) && !empty($filtros['repoid'])) {
			$filtro .= " AND rep.repoid = '".$filtros['repoid']."' ";
		}
		
		// tipo item
		if (isset($filtros['tipo_item']) && !empty($filtros['tipo_item'])) {
			$filtro .= " AND pet.pettp_item = '".$filtros['tipo_item']."' ";
		}
		// uf 
		if (isset($filtros['uf']) && !empty($filtros['uf'])) {
			$filtro .= " AND erep.endvuf = '".$filtros['uf']."' ";
		}
		
		// cidade
		if (isset($filtros['cidade']) && !empty($filtros['cidade'])) {
			$filtro .= " AND erep.endvcidade = '".$filtros['cidade']."' ";
		}
		 
		$retorno  =  array();
	
		$sql = " SELECT pet.petoid, pet.petpcmoid, pet.petdt_posicao as data_posicao_estoque, 
						pet.petrepoid as id_representante, rep.repnome,
						CASE WHEN pet.pettp_item='I' THEN 'Imobilizado' 
                             ELSE 'Material de Instalação'
                        END
                        as tipo_item,
						CASE WHEN rep.repstatus='A' THEN 'Ativo'
                             WHEN rep.repstatus='I' THEN 'Inativo'
                             ELSE 'Ativo - Aguardando Distrato'
                        END
                        as repstatus, 
						erep.endvcidade, 
						erep.endvuf, 
						pet.petprdoid as id_produto, 
						pro.prdproduto as nome_produto, 
						pet.petqtd_disponivel - pet.petqtd_reserva  as qtd_disponivel, 
						pet.petqtd_reserva as qtd_reserva,
						pet.petqtd_instalador as qtd_instalador,  
						pet.petqtd_retirada as qtd_retirada, 
						pet.petqtd_retornado as qtd_retornado, 
						pet.petqtd_recall as qtd_recall, 
						pet.petqtd_recall_disponivel as qtd_recall_disponivel, 
						pet.petqtd_transito - pet.petqtd_reserva_transito as qtd_transito,
						pet.petqtd_reserva_transito as qtd_reserv_transi,
						pet.petqtd_manutencao_fornecedor as qtd_fornecedor,
						pet.petqtd_conferencia_IF as qtd_conferencia_if, 
						pet.petqtd_manutencao_interna as qtd_manutencao_interna,
						pet.petqtd_aguardando_manutencao as qtd_aguardando_manutencao,  
						pcm.pcmcusto_medio as custo_medio_produto
						, SUM(
						       coalesce(pet.petqtd_disponivel::integer,0)        + coalesce(pet.petqtd_instalador::integer,0)         +  
						       coalesce(pet.petqtd_retirada::integer,0)          + coalesce(pet.petqtd_retornado::integer,0)          + coalesce(pet.petqtd_recall::integer,0)                  + 
						       coalesce(pet.petqtd_recall_disponivel::integer,0) + coalesce(pet.petqtd_transito::integer,0)           + coalesce(pet.petqtd_manutencao_fornecedor::integer,0)   +
						       coalesce(pet.petqtd_conferencia_IF::integer,0)    + coalesce(pet.petqtd_manutencao_interna::integer,0) + coalesce(pet.petqtd_aguardando_manutencao::integer,0) 
						     ) as total
						, SUM(
						      (
						       coalesce(pet.petqtd_disponivel::integer,0)        + coalesce(pet.petqtd_instalador::integer,0)         +  
						       coalesce(pet.petqtd_retirada::integer,0)          + coalesce(pet.petqtd_retornado::integer,0)          + coalesce(pet.petqtd_recall::integer,0)                  + 
						       coalesce(pet.petqtd_recall_disponivel::integer,0) + coalesce(pet.petqtd_transito::integer,0)           + coalesce(pet.petqtd_manutencao_fornecedor::integer,0)   +
						       coalesce(pet.petqtd_conferencia_IF::integer,0)    + coalesce(pet.petqtd_manutencao_interna::integer,0) + coalesce(pet.petqtd_aguardando_manutencao::integer,0) 
						      ) * pcm.pcmcusto_medio) as vlr_total
						      
						FROM posicao_estoque_trimestral pet
						JOIN produto pro                           on pet.petprdoid   = pro.prdoid 
					    LEFT JOIN produto_custo_medio pcm          on pcm.pcmoid      = pet.petpcmoid  AND pcm.pcmdt_exclusao IS NULL AND pcm.pcmusuoid_exclusao IS NULL
						JOIN representante rep                     on rep.repoid      = pet.petrepoid
						LEFT JOIN endereco_representante erep      on erep.endvrepoid = rep.repoid
						
						WHERE 
						
						$filtro
						
						GROUP BY pet.petoid, rep.repnome, rep.repstatus, erep.endvcidade, erep.endvuf, pro.prdproduto, custo_medio_produto 
						ORDER BY tipo_item, rep.repnome, nome_produto ";
	
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("Erro ao retornar data posição estoque ");
		}

		$i = 0;
		while ($row = pg_fetch_object($rs)) {
	 
			$retorno[$i]['data_posicao_estoque'] = date('d/m/Y', strtotime( $row->data_posicao_estoque));
			$retorno[$i]['tipo_item']                     = $row->tipo_item;
			$retorno[$i]['repoid']                        = $row->id_representante;
			$retorno[$i]['repnome']                       = $row->repnome;
			$retorno[$i]['cidade']                        = $row->endvcidade;
			$retorno[$i]['uf']                            = $row->endvuf;
			$retorno[$i]['idprd']                         = $row->id_produto;
			$retorno[$i]['prdproduto']                    = $row->nome_produto;
			$retorno[$i]['qtd_disponivel']                = $row->qtd_disponivel;
			$retorno[$i]['qtd_reserva']                   = $row->qtd_reserva;
			$retorno[$i]['qtd_instalador']                = $row->qtd_instalador;
			$retorno[$i]['qtd_retirada']                  = $row->qtd_retirada;
			$retorno[$i]['qtd_retornado']                 = $row->qtd_retornado;
			$retorno[$i]['qtd_recall']                    = $row->qtd_recall;
			$retorno[$i]['qtd_recall_disponivel']         = $row->qtd_recall_disponivel;
			$retorno[$i]['qtd_manutencao_fornecedor']     = $row->qtd_fornecedor;
			$retorno[$i]['qtd_transito']                  = $row->qtd_transito;
			$retorno[$i]['qtd_reserv_transi']             = $row->qtd_reserv_transi;
			$retorno[$i]['qtd_conferencia_if']            = $row->qtd_conferencia_if;
			$retorno[$i]['qtd_manutencao_interna']        = $row->qtd_manutencao_interna;
			$retorno[$i]['qtd_aguardando_manutencao']     = $row->qtd_aguardando_manutencao;
			$retorno[$i]['total']                         = $row->total;
			$retorno[$i]['custo_medio_produto']           = $row->custo_medio_produto;
			$retorno[$i]['vlr_total']                     = $row->vlr_total;
			$retorno[$i]['repstatus']                     = $row->repstatus;
			$retorno[$i]['count']                         = $row->count;
			$i++;
		}
	
		return $retorno;
	}
}
<?php

/**
 * @file PrnBemConsumoDAO.class.php
 *
 * Modulo DAO para Bem de Consumo
 *
 * @author Rafael Barbeta da Silva
 * @version 28/08/2013
 * @package SASCAR PrnBemConsumoDAO.class.php
 */


class PrnBemConsumoDAO {
    
    private $conn;

    public function __construct() {
    	global $conn;
    	$this->conn = $conn;
    	$this->cd_usuario = $_SESSION['usuario']['oid'];
    }
	
	public function begin(){
        pg_query($this->conn, "BEGIN;");
    }

    public function commit(){
        pg_query($this->conn, "COMMIT;");
    }
    
	public function rollback(){
        $rs = pg_query($this->conn, "ROLLBACK;");
    }

	
	public function getFornecedores($text){
	
		$retorno = false;
		
		$sql = "SELECT foroid, forfornecedor FROM fornecedores 
				WHERE forfornecedor ILIKE '%$text%'
					AND fordt_exclusao is null
				ORDER BY forfornecedor";

        $query  = pg_query($this->conn, $sql);
		$result = pg_fetch_all($query);
		
		if(pg_num_rows($query) > 0){
			// Trata Utf8 no nome do fornecedor
			foreach($result as $key => $val){
				$retorno[$key] = array(
									'foroid' => $val['foroid'], 
									'forfornecedor' => utf8_encode($val['forfornecedor']));
			}
		}

        return $retorno;
	}
	
	public function confereNotaFornecedor($dados){
	
		$sql = "SELECT 
		            entiprdoid, prdproduto, entoid 
		        FROM 
		            entrada, entrada_item, produto  
				WHERE 
		            entoid = entientoid
					AND entiprdoid = prdoid
					AND entnota = '".$dados['nfloid']."' 
					AND entforoid = ".$dados['foroid']."
					AND prdproduto ILIKE '".$dados['prdproduto']."'";


		if($rs = pg_query($this->conn, $sql)){
			if(pg_num_rows($rs) > 0) {
				$result = pg_fetch_all($rs);
				return $result[0];
			}
		}
	}
	
	public function getEquipamentos($entoid, $equprdoid){
		
		$sql = "SELECT 
		            equoid 
		        FROM 
		            equipamento 
		        WHERE 
		            equentoid = ". intval($entoid) ."
	            AND 
	            	equprdoid = ". intval($equprdoid) ."            	
            	";
	
		if($rs = pg_query($this->conn, $sql)){
			if(pg_num_rows($rs) > 0) {
				$result = pg_fetch_all($rs);
				return $result;
			}
		}
	}
	
	public function getSerialConsumo($entoid, $equprdoid){
		
		$sql = "SELECT 
		            cseroid 
		        FROM 
		            consumo_serial 
		        WHERE 
		            cserentoid = ". intval($entoid) . "
		            AND cserprdoid = ". intval($equprdoid) . "
	            ";	

		if($rs = pg_query($this->conn, $sql)){
			if(pg_num_rows($rs) > 0) {
				$result = pg_fetch_all($rs);
				return $result;
			}
		}
	}
	
	public function getEquipamentosLayout($prdproduto,$entoid){
		
		$sql = "SELECT 
		            equoid 
		        FROM 
		            produto, equipamento
				WHERE 
				    prdoid=equprdoid
					AND prdproduto ILIKE '$prdproduto'
					AND equentoid = '$entoid'
					AND equno_serie IS NULL";
	
		if($rs = pg_query($this->conn, $sql)){
			if(pg_num_rows($rs) > 0) {
				$result = pg_fetch_all($rs);
				return $result[0]['equoid'];
			}
		}
	}

	public function getlinhas($CCID){
		
		$sql = "SELECT 
		            linaraoid, linnumero, lincid 
		        FROM
		            linha 
				WHERE lincid = '$CCID'";

	
		if($rs = pg_query($this->conn, $sql)){
			if(pg_num_rows($rs) > 0) {
				$result = pg_fetch_all($rs);
				return $result[0];
			}
		}
	}
	
	public function getDDD($araoid){
		
		$sql = "SELECT 
		            arano_ddd 
		        FROM 
		            area 
		        WHERE 
		            araoid = '$araoid'";

	
		if($rs = pg_query($this->conn, $sql)){
			if(pg_num_rows($rs) > 0) {
				$result = pg_fetch_all($rs);
				return $result[0]['arano_ddd'];
			}
		}
	}
	
	public function confereSerial($serial){
		
		$sql = "SELECT 
		            equoid 
		        FROM 
		            equipamento 
		        WHERE
		            equno_serie = $serial";
		
	
		if($rs = pg_query($this->conn, $sql)){
			if(pg_num_rows($rs) > 0) {
				$result = pg_fetch_all($rs);
				return $result[0]['equoid'];
			}
		}
	}

	public function setRadioFrequencia($RF, $cseroid){

		$qtdeCaracteres = str_split(trim($RF));
	    if(sizeof($qtdeCaracteres) != 3){
	        throw new Exception('O RF "' . trim($RF) . '" informado deve conter 3 digitos.');
	    }
	    
		// Confere se existe registro
		$sql = "select aectrf from atributo_equipamento_ct where aectcseroid = $cseroid";
		$query = pg_query($this->conn, $sql);
		$rows  = pg_num_rows($query);

		// Insert / Update - RF
		if($rows == 0){
			$sql = "INSERT INTO atributo_equipamento_ct (aectrf, aectdt_cadastro, aectcseroid) 
					VALUES ($RF, now(), $cseroid)";
		}else{
			$sql = "UPDATE atributo_equipamento_ct SET aectrf = $RF where aectcseroid = $cseroid";
		}

		if(!@pg_query($this->conn, $sql)){
			throw new Exception("Erro ao vincular a rádio frequência.");
		}

	}
	
	public function setEquipamento($numeroLinha, $equoid, $serial, $IMEI, $cseroid, $ddd,$ccid,$versao){

		// Verifica se o CCID já está associado a um celular
		$resultado = $this->verificaCCIDAssociadoCelular($ccid);
			
		if(sizeof($resultado)>0 && $resultado!= false){
			throw new Exception('O CCID '.$ccid.' informado já está associado a um celular.');
		}
			
		$resultado = $this->verificaLinhaExiste($ccid);
			
		if(sizeof($resultado)>0){
			$resultado = $this->verificaExisteCelularImei($IMEI,$ccid,$resultado);
		
			if($resultado == "sucesso"){
				$resultado = $this->verificaEquipamentoImei($IMEI);
		
				if($resultado > 0 || $resultado == "sucesso"){
					throw new Exception('Já existe um equipamento cadastrado com ESN ' . $IMEI . '.');
				}
			}
			
			if($this->verificaEquipamentoTemLinha($equoid) == 'nao_tem'){
				
				$sql = "UPDATE 
				            consumo_serial 
				        SET 
				            cserserial = '$serial' 
				        WHERE
				            cseroid = $cseroid";
				
				if(!pg_query($this->conn, $sql)){
					throw new Exception('Erro ao vincular o serial de equipamento bem de consumo.');
				}

				$sql = "UPDATE 
				            equipamento 
				        SET 
							equno_serie = '$serial', 
							equesn		= '".trim($IMEI)."', 
							equaraoid 	= '".$numeroLinha['linaraoid']."', 
							equno_ddd 	= '".$ddd."', 
							equeveoid 	= '".$versao."',
							equno_fone	= '".$numeroLinha['linnumero']."'
						WHERE equoid 	= $equoid";
				if(!pg_query($this->conn, $sql)){
					throw new Exception("Erro ao vincular o serial de equipamento.");
				}
				
			}else{
				echo $equoid;
				throw new Exception('Equipamento já possui linha cadastrada');
			}
		}
	}
	
	/**
	 * Busca os produtos
	 * @param array $params
	 */
	public function getProdutos($params = null) {
	
		$sql = "
	    	SELECT
	    		prdoid,
    	        prdproduto
	    	FROM
	    		produto
	    	WHERE
    			prdproduto IS NOT NULL
				AND prdptioid = 6 
	    	";
	
		if ($params['produto_busca'] != "") {
			$sql .= " AND prdproduto ILIKE '%". utf8_decode($params['produto_busca']) ."%'";
		}
	
		$sql .= " ORDER BY prdproduto ASC"; 
	
		$rs = pg_query($this->conn, $sql);
	
		$array = "";
		if(pg_num_rows($rs) > 0) {
			$result = pg_fetch_all($rs);
	
			$x = 0;
			foreach ($result as $resultado){
				$array[$x]['prdoid'] 		= $resultado['prdoid'];
				$array[$x]['prdproduto'] 	= utf8_encode($resultado['prdproduto']);
				$x++;
			}
		}
	
		return $array;
	}
	
	/**
	 * Busca os clientes
	 * @param array $params
	 */
	public function getClientes($params = null) {
	
		$sql = "
	    	SELECT
	    		clioid,
    	        clinome
	    	FROM
	    		clientes
	    	WHERE
    			clinome IS NOT NULL
	    	";
	
		if ($params['cliente_busca'] != "") {
			$sql .= " AND clinome ILIKE '%". utf8_decode($params['cliente_busca']) ."%'";
		}
	
		$sql .= " ORDER BY clinome ASC";
	
		$rs = pg_query($this->conn, $sql);
	
		$array = "";
		if(pg_num_rows($rs) > 0) {
			$result = pg_fetch_all($rs);
	
			$x = 0;
			foreach ($result as $resultado){
				$array[$x]['clioid'] 	= $resultado['clioid'];
				$array[$x]['clinome'] 	= utf8_encode($resultado['clinome']);
				$x++;
			}
		}
	
		return $array;
	}
	
	/**
	 * Busca Operações
	 * @param array $params
	 */
	public function getOperacoes($clioid) {
	
		$sql = "
	    	SELECT
	    		octoid,
    	        octnome,
				octoprid
	    	FROM
	    		operacoes_ct
	    	WHERE
    			octnome IS NOT NULL
				AND octclioid = ".$clioid."
	    	ORDER BY octnome ASC";
	
		$rs = pg_query($this->conn, $sql);
	
		$array = "";
		if(pg_num_rows($rs) > 0) {
			$result = pg_fetch_all($rs);
	
			$x = 0;
			foreach ($result as $resultado){
				$array[$x]['octoid'] 	= $resultado['octoid'];
				$array[$x]['octnome'] 	= utf8_encode($resultado['octnome']);
				$array[$x]['octoprid'] 	= $resultado['octoprid'];
				$x++;
			}
		}
	
		return $array;
	}
	
	/**
	 * Lista representantes
	 * @param array $params
	 */
	public function getRepresentantes($params = null){
		
		$sql = "
	    	SELECT
	    		repoid,
    	        repnome
	    	FROM
	    		representante
	    	WHERE
    			repnome IS NOT NULL
	    	";
		 
		$sql .= " ORDER BY repnome ASC";
		 
		$rs = pg_query($this->conn, $sql);
		 
		$array = "";
		if(pg_num_rows($rs) > 0) {
			$result = pg_fetch_all($rs);
			 
			$x = 0;
			foreach ($result as $resultado){
				$array[$x]['repoid'] 	= $resultado['repoid'];
				$array[$x]['repnome'] 	= $resultado['repnome'];
				$x++;
			}
		}
		 
		return $array;
	}
	
	/**
	 * Lista representantes estoque
	 * @param array $params
	 */
	public function getRepresentanteEstoque($params = null){
		
		$sql = "
	    	SELECT
				repnome,
    			relroid
			FROM
				representante,
    			relacionamento_representante
			WHERE
				relrrep_terceirooid = repoid
	    	";
		 
		if ($params['repoid'] != "") {
			$sql .= " AND relrrepoid = ". $params['repoid'];
		}
		$sql .= " ORDER BY repnome";
		 
		$rs = pg_query($this->conn, $sql);
		 
		$array = "";
		if(pg_num_rows($rs) > 0) {
			$result = pg_fetch_all($rs);
			 
			$x = 0;
			foreach ($result as $resultado){
				$array[$x]['relroid'] 	= $resultado['relroid'];
				$array[$x]['repnome'] 	= utf8_encode($resultado['repnome']);
				$x++;
			}
		}
		 
		return $array;
	}
	
	/**
	 * Lista status
	 * @param array $params
	 */
	public function getStatus($params = null){
		
		$sql = "
	    	SELECT
	    		eqsoid,
    	        eqsdescricao
	    	FROM
				equipamento_status
	    	WHERE
    			eqsdescricao IS NOT NULL
	    	";
		 
		$sql .= " ORDER BY eqsdescricao ASC";
		 
		$rs = pg_query($this->conn, $sql);
		 
		$array = "";
		if(pg_num_rows($rs) > 0) {
			$result = pg_fetch_all($rs);
			 
			$x = 0;
			foreach ($result as $resultado){
				$array[$x]['eqsoid'] 		= $resultado['eqsoid'];
				$array[$x]['eqsdescricao'] 	= $resultado['eqsdescricao'];
				$x++;
			}
		}
		 
		return $array;
	}
	
	
	/**
	 * Pesquisa
	 * @param array $params
	 */
	public function pesquisa($params = null){
		//equipamento com consumo_serial pelo numero serial (CAST)
		//contrato_item_ct = equipamento
		$sql = "SELECT 
					prdoid, equoid,
					prdproduto,
					equoid,
					equno_serie,
					eqsdescricao as status,
					repnome as representante_etoque,
					clinome as cliente ,
					cectoid as cod_venda,
					connumero as contrato
				FROM
					consumo_serial 
				INNER JOIN
					produto ON cserprdoid = prdoid
				LEFT JOIN 
					equipamento ON cserserial::BIGINT = equno_serie
				LEFT JOIN
					equipamento_status ON equeqsoid = eqsoid
				LEFT JOIN
					contrato_equipamento_ct ON cectequoid = equoid
				LEFT JOIN
					contrato_item_ct ON cectconitctoid = conitctoid
				LEFT JOIN
					contrato ON connumero = conitctconnumero
				LEFT JOIN
					relacionamento_representante ON cserrelroid = relroid
				LEFT JOIN
					representante ON relrrep_terceirooid = repoid
				LEFT JOIN
					clientes ON conclioid = clioid
				LEFT JOIN
					entrada ON cserentoid = entoid
				WHERE
					prddt_exclusao IS NULL
				";
	
		if(isset($params['data_inicial']) && trim($params['data_inicial'])!="" && isset($params['data_final']) && trim($params['data_final'])!=""){
			$sql .= " AND entcadastro BETWEEN '" . $this->formataData(true,$params['data_inicial'],'ini') . "' AND '" . $this->formataData(true,$params['data_final'],'fim') . "'";
		}
		if(isset($params['prdoid']) && trim($params['prdoid'])!=""){
			$sql .= " AND prdoid = " . $params['prdoid'];
		}
		if(isset($params['nota_fiscal']) && trim($params['nota_fiscal'])!=""){
			$sql .= " AND entnota ilike '" . $params['nota_fiscal'] . "'";
		}
		if(isset($params['serie']) && trim($params['serie'])!=""){
			$sql .= " AND entserie ilike '" . $params['serie'] . "'";
		}
		if(isset($params['eqsoid']) && trim($params['eqsoid'])!=""){
			$sql .= " AND eqsoid = " . $params['eqsoid'];
		}
		if(isset($params['foroid']) && trim($params['foroid'])!=""){
			$sql .= " AND cserforoid = " . $params['foroid'];
		}
		if(isset($params['numero_serie']) && trim($params['numero_serie'])!=""){
			$sql .= " AND equno_serie = '" . $params['numero_serie'] . "'";
		}
		if(isset($params['sem_serial']) &&  trim($params['sem_serial']) == 'sim'){
			$sql .= " AND equno_serie IS NULL";
		}
		if(isset($params['repoid']) &&  trim($params['repoid']) != ""){
			$sql .= " AND relrrepoid = " . $params['repoid'];
		}
		if(isset($params['relroid']) &&  trim($params['relroid']) != ""){
			$sql .= " AND relroid = " . $params['relroid'];
		}
		if(isset($params['cod_venda']) &&  trim($params['cod_venda']) != ""){
			$sql .= " AND cectoid ilike '" . $params['cod_venda'] . "'";
		}
		if(isset($params['clioid']) &&  trim($params['clioid']) != ""){
			$sql .= " AND conclioid = " . $params['clioid'];
		}
		if(isset($params['operacao']) &&  trim($params['operacao']) != ""){
			$sql .= " AND conclioid = " . $params['operacao'];
		}
		 
		$sql .= " ORDER BY prdproduto LIMIT 2000";
		
		$rs = pg_query($this->conn, $sql);
		 
		if(pg_num_rows($rs) > 0) {
			$result = pg_fetch_all($rs);
		}
		 
		return $result;
	}
	
	/**
	 * Dados da compra
	 * @param int $serial
	 */
	public function dadosDaCompra($serial){
		
		$sql = "SELECT distinct
					prdoid, equoid,
					prdproduto,
					equno_serie,
					eqsdescricao as status,
					rep_estoque.repnome as representante_etoque,
					rep.repnome as representante,
					clinome as cliente ,
					cectoid as codVenda,
					connumero as contrato,
    				forfornecedor as fornecedor,
    				to_char(entdt_entrada, 'DD/MM/YYYY') as entrada,
    				to_char(entdt_emissao, 'DD/MM/YYYY') as emissao,
    				entnota as nota_fiscal,
    				entserie as serie,
    				enttotal as valor_total
				FROM
					consumo_serial 
				INNER JOIN
					produto ON cserprdoid = prdoid
				LEFT JOIN 
					equipamento ON cserserial::BIGINT = equno_serie
				LEFT JOIN
					equipamento_status ON equeqsoid = eqsoid
				LEFT JOIN
					contrato_equipamento_ct ON cectequoid = equoid
				LEFT JOIN
					contrato_item_ct ON cectconitctoid = conitctoid
				LEFT JOIN
					contrato ON connumero = conitctconnumero
				LEFT JOIN
					relacionamento_representante ON equrelroid = relroid
				LEFT JOIN
					representante ON relrrep_terceirooid = repoid
				LEFT JOIN
					clientes ON conclioid = clioid
				LEFT JOIN
					entrada ON equentoid = entoid
				INNER JOIN
					fornecedores ON cserforoid = foroid
				INNER JOIN
					representante AS rep_estoque ON relrrep_terceirooid = rep_estoque.repoid
    			INNER JOIN
					representante AS rep ON relrrepoid = rep.repoid
				WHERE
					prddt_exclusao IS NULL
    				AND equno_serie = " . $serial;
		
		$rs = pg_query($this->conn, $sql);
		 
		if(pg_num_rows($rs) > 0) {
			$result = pg_fetch_all($rs);
		}
		 
		return $result[0];
	}
	

	/**
	 * Se encontra linha com o chip informado return true
	 * @param int $ccid
	 * @throws exception
	 * @return boolean
	 */
	public function verificaLinhaExiste($ccid){
	
		$sql = "SELECT
					linoid,
					linnumero,
					linoploid,
					linaraoid,
					linhabilitacao,
					lincsloid,
					arano_ddd
				FROM
					linha
				INNER JOIN
					area ON araoid = linaraoid
				WHERE
					lincid = '$ccid'
					AND linlintoid > 0
					AND linexclusao IS NULL";
		
		if(!$rs = pg_query($this->conn,$sql)){
			throw new exception('Erro: Não foi possivel executar consulta para imei de equipamentos');
		}
	
		if(pg_num_rows($rs) > 0) {
			return pg_fetch_array($rs);
		}else{
			return false;
		}
	
	}
	
	/**
	* Verifica se CID está associado a um celular
	 * @param text $ccid
	 * @throws exception
	* @return boolean
	*/
	public function verificaCCIDAssociadoCelular($ccid){
	
		$sql = "SELECT
					linoid,
					linnumero
				FROM
					linha
				WHERE
					lincid = '$ccid'
					AND lincsloid=1
					AND linoid IN (SELECT cellinoid FROM celular WHERE cellinoid=linoid AND celdt_exclusao IS NULL)";

		if(!$rs = pg_query($this->conn,$sql)){
			throw new exception('Erro: Não foi possivel executar consulta que verifica se CID está associado');
		}

		if(pg_num_rows($rs) > 0) {
			return pg_fetch_array($rs);
		}else{
			return false;
		}
	}

	/**
	 * Query para verificar se ja existe equipamento com o imei informado
	 * @param text $equesn
	 * @throws exception
	 * @return boolean
	 */
	public function verificaEquipamentoImei($equesn){
	
		$sql = "SELECT
					equpatrimonio
				FROM
					equipamento
				WHERE
					equesn = '$equesn'
					AND equdt_exclusao IS NULL";
		
		if(!$rs = pg_query($this->conn,$sql)){
			throw new exception('Erro: Não foi possivel executar consulta que verifica se IMEI está associado a um equipamento');
			return "erro";
		}

		if(pg_num_rows($rs) > 0) {
			$equpatrimonio =  pg_fetch_result($rs, 0, 'equpatrimonio');
			return $equpatrimonio;
		}else{
			return "erro";
		}
	}
	
	/**
	 * Verifica se existe celular com o ESN (imei) informado
	 * @param text 	$imei
	 * @param text 	$ccid
	 * @param array $resLinha
 	 * @throws exception
 	 */
 	public function verificaExisteCelularImei($imei,$ccid,$resLinha){
	
 		$sql = "SELECT
		 			celoid
				FROM
					celular
				WHERE
					celdt_exclusao IS NULL
					AND celesn = '$imei'";

		if( !$rs = pg_query($sql) ){
			throw new exception ('Erro: Não foi possivel verificar celular');
			return "erro";
		}
	
		if (pg_num_rows($rs) == 0) {
		
			// Cadastrando novo celular
			$celesn           = $imei;
			$cellinha         = $resLinha['linnumero'];
			$celusuoid        = $this->cd_usuario;
			$celstatus        = 5;
			$celmcloid        = 23;
			$celoploid        = $resLinha['linoploid'];
			$celaraoid        = $resLinha['linaraoid'];
			$celobs           = "Celular Cadastrado via Importação Bem de Consumo";
			$celforoid        = 40;
			$celhabilitacao   = $resLinha['linhabilitacao'];
			$celcsloid        = $resLinha['lincsloid'];
			$cellinoid        = $resLinha['linoid'];
			$celcvooid        = 24;
			$celversao        = 4;
			$celdt_fabricacao = date("Y-m-d");
		
			$sql_cel = "INSERT INTO celular (celesn,
											cellinha,
											celusuoid,
											celstatus,
											celmcloid,
											celoploid,
											celaraoid,
											celobs,
											celforoid,
											celdt_remessa,
											celhabilitacao,
											celcsloid,
											cellinoid,
											celcvooid,
											celversao,
											celdt_fabricacao,
											celgarantia)
										VALUES
											('$celesn',
											$cellinha,
											$celusuoid,
											$celstatus,
											$celmcloid,
											$celoploid,
											$celaraoid,
											'$celobs',
											$celforoid,
											now(),
											'$celhabilitacao',
											$celcsloid,
											$cellinoid,
											$celcvooid,
											$celversao,
											'$celdt_fabricacao',
											'$celdt_fabricacao'::date + interval '13 month');";
		
			if(!pg_query($this->conn,$sql_cel)){
				throw new Exception('Erro: Não foi possivel cadastrar celular');
				return "erro";
			}
			
			$sql = "UPDATE linha SET linlscoid = 7 WHERE lincid ='".$ccid."'";
			
			if(!pg_query($this->conn,$sql)){
					throw new Exception('Erro: Não foi possivel atualizar linha');
					return "erro";
			}
				
			$sql = "INSERT INTO celular_historico
			(
					celhusuoid,
					celhcelsoid,
					celhlscoid,
					celhcsloid,
					celhobs,
					celharaoid,
					celhfone,
					celhlinoid,
					celhmotivo
			)
			VALUES
			(
					$celusuoid,
					$celstatus,
					7,
					$celcsloid,
					'$celobs',
					$celaraoid,
					$cellinha,
					$cellinoid,
					'Alteração via importação de Bem de Consumo.'
			);";
		
			if(!pg_query($this->conn, $sql)){
				throw new Exception('Erro: Não foi possivel cadastrar celular');
				return "erro";
			}
								
		}else{
			$celoid =  pg_fetch_result($rs, 0, 'celoid');
				
			// Cadastrando novo celular
			$celesn           = $imei;
			$cellinha         = $resLinha['linnumero'];
			$celusuoid        = $this->cd_usuario;
			$celstatus        = 5;
			$celmcloid        = 23;
			$celoploid        = $resLinha['linoploid'];
			$celaraoid        = $resLinha['linaraoid'];
			$celobs           = "Celular Cadastrado via Importação de Serial de Consumo Serial";
			$celforoid        = 40;
			$celhabilitacao   = $resLinha['linhabilitacao'];
			$celcsloid        = $resLinha['lincsloid'];
			$cellinoid        = $resLinha['linoid'];
			$celcvooid        = 24;
			$celversao        = 4;
			$celdt_fabricacao = date("Y-m-d");
						
			$sql_cel = "UPDATE
							celular
						SET
							celesn 				= '$celesn',
							cellinha 			= $cellinha,
							celusuoid 			= $celusuoid,
							celstatus			= $celstatus,
							celmcloid			= $celmcloid,
							celoploid			= $celoploid,
							celaraoid			= $celaraoid,
							celobs				= '$celobs',
							celforoid			= $celforoid,
							celdt_remessa		= NOW(),
							celhabilitacao		= '$celhabilitacao',
							celcsloid			= $celcsloid,
							cellinoid			= $cellinoid,
							celcvooid			= $celcvooid,
							celversao			= $celversao,
							celdt_fabricacao	= '$celdt_fabricacao',
							celgarantia			= '$celdt_fabricacao'::date + interval '13 month'
						WHERE
							celoid				= $celoid";
											
			if(!pg_query($this->conn,$sql_cel)){
				throw new Exception('Erro: Não foi possivel atualizar celular');
				return "erro";
			}

			$sql = "UPDATE linha SET linlscoid = 7 WHERE lincid ='".$ccid."'";
				
			if(!pg_query($this->conn,$sql)){
				throw new Exception('Erro: Não foi possivel atualizar linha');
				return "erro";
			}

			$sql = "INSERT INTO celular_historico
							(
									celhusuoid,
									celhcelsoid,
									celhlscoid,
									celhcsloid,
									celhobs,
									celharaoid,
									celhfone,
									celhlinoid,
									celhmotivo
							)VALUES(
									$celusuoid,
									$celstatus,
									7,
									$celcsloid,
									'$celobs',
									$celaraoid,
									$cellinha,
									$cellinoid,
									'Alteração via importação Bem de Consumo.'
							);";
										
			if(!pg_query($this->conn, $sql)){
					throw new Exception('Erro: Não foi possivel cadastrar celular');
					return "erro";
			}
		}
		
		return "sucesso";
	}
	
	/**
	 * Lista versão do equipamento
	 */
	public function getVersao(){
		$sql = "SELECT 
					* 
				FROM 
					equipamento_versao 
				WHERE 
					evedt_exclusao IS NULL
				ORDER BY
					eveversao";
		
		if($rs = pg_query($this->conn, $sql)){
			if(pg_num_rows($rs) > 0) {
				$result = pg_fetch_all($rs);
				return $result;
			}
		}
		
	}
	
	/**
	 * verifica se o equipamento está sem linha
	 * @param int $serial
	 * @return boolean
	 */
	public function verificaEquipamentoTemLinha($equoid){
		$sql = "SELECT 
					equoid,
                    equpatrimonio 
				FROM 
					equipamento 
				WHERE 
					equoid = '$equoid'
					AND equesn IS NULL 
					AND equno_fone IS NULL";

		if($rs = pg_query($this->conn, $sql)){
			
			if(pg_num_rows($rs) > 0) {
				return "nao_tem";
			}else{
				return "tem";
			}
			
		}
	}
	
	
	/**
	 * Formata data
	 * @param Boolean $paraBanco
	 * @param Date $data
	 */
	private function formataData($paraBanco = true,$data,$posicao = null){
		 
		if($paraBanco == true){
			//dd/mm/aaaa
			//"2012-03-28 09:13:19-03"
			$array = explode("/",$data);
			$formatado = $array[2] . "-" . $array[1] . "-" . $array[0];
		}
		if($posicao == 'ini'){
		    $formatado .= ' 00:00:00';
		}
		if($posicao == 'fim'){
		    $formatado .= ' 23:59:59';
		}
		 
		return $formatado;
	}


	/** 
	* Verifica a quantidade de equipamentos ainda sem serial associado
	*
	* @param int $entoid
	* @param int $equprdoid
	* @return int
	*/
	public function recuperarSaldoEquipamento($entoid, $equprdoid) {

		$total = 0;

		$sql = "SELECT 
					(SUM(total)/2)::INTEGER AS resto
				FROM (
					    (
					    	SELECT 
					    		COUNT(1) AS total 
				    		FROM 
				    			equipamento 
			    			WHERE 
		    					equentoid = ". intval($entoid) ."
	    					AND 
	    						equprdoid = ". intval($equprdoid) ." 
							AND 
								equno_serie IS NULL
						) 
					    UNION ALL
					    (
				    		SELECT 
				    			COUNT(1) AS total 
			    			FROM 
			    				consumo_serial 
		    				WHERE 
		    					cserentoid = ". intval($entoid) ." 
	    					AND 
	    						cserprdoid = ". intval($equprdoid) ." 
    						AND 
    							(cserserial IS NULL OR TRIM(cserserial) ='')
						) 
				) AS FOO";

		if($rs = pg_query($this->conn, $sql)) {			
			
			$retorno = pg_fetch_object($rs);
			$total = isset($retorno->resto) ? $retorno->resto : 0;
		}

		return $total;
	}

}

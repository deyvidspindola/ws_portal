<?php

/**
 * @file PrnImobilizadoDAO.class.php
 *
 * Modulo DAO para Imobilizado
 *
 * @author Rafael Mitsuo Moriya
 * @version 20/09/2013
 * @package SASCAR PrnImobilizadoDAO.class.php
 */


class PrnImobilizadoDAO {
    
    private $conn;

    public function __construct() {
    	global $conn;
    	$this->conn = $conn;
    	$this->cd_usuario = $_SESSION['usuario']['oid'];
    }
	
	public function begin(){
        pg_query($this->conn, "BEGIN;");
    }
	
	public function end(){
        pg_query($this->conn, "END;");
    }

    public function commit(){
        pg_query($this->conn, "COMMIT;");
    }
    
	public function rollback(){
        $rs = pg_query($this->conn, "ROLLBACK;");
    }

	/**
	 * Lista fornecedores
	 * @param text $text
	 * @return Ambigous <boolean, multitype:NULL unknown >
	 */
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
	
	/**
	 * Confere se o fornecedor tem nota de entrada
	 * @param Array $dados
	 * @return Array
	 */
	public function confereNotaFornecedor($dados, $serie = null){
	
		$sql = "SELECT 
		            entiprdoid, prdproduto, entoid ,entdt_entrada, entdt_emissao
		        FROM 
		            entrada, entrada_item, produto  
				WHERE 
		            entoid = entientoid
					AND entiprdoid = prdoid
					AND entnota = '".$dados['nota_fiscal']."' 
					AND entforoid = ".$dados['busca_foroid']."
					AND entexclusao IS NULL
					AND prdproduto ILIKE '".$dados['prdproduto']."'";
		if ( isset($serie) && $serie != null ) {
                $sql .= " AND entserie = '$serie' ";
        }
        $query  = pg_query($this->conn, $sql);
		$result = pg_fetch_all($query);
		
		return $result[0];
	}
	
	/**
	 * Pega equipamentos
	 * @param int $entoid
	 * @param int $equprdoid
	 * @return array
	 */
	public function getEquipamentos($entoid, $equprdoid){
		
		$sql = "SELECT 
		            equoid 
		        FROM 
		            equipamento 
		        WHERE 
		            equnf_entrada = $entoid
		            AND equprdoid = $equprdoid";

		$query  = pg_query($this->conn, $sql);
		$result = pg_fetch_all($query);
		
		return $result;
	}
	
	/**
	 * Pega o serial dos imobilizados
	 * @param int $entoid
	 * @param int $equprdoid
	 * @param array $array
	 * @return array
	 */
	public function getSerialImobilizado($entoid, $equprdoid, $array){
		
		$sql = "SELECT 
		            imoboid,
		            imobpatrimonio
		        FROM 
		            imobilizado
		        WHERE 
		            imobentoid = $entoid
		            AND imobprdoid = $equprdoid";
		
		$patrimonios = "";
		for($x = 0; $x < count($array) ; $x++){
			$patrimonios .= $array[$x][0];
			if($x < count($array)-1){
				$patrimonios .= ",";	
			}
		}
		
		$sql .= " AND imobpatrimonio in ($patrimonios)";
		
		$rs = pg_query($this->conn, $sql);
		
		$array = "";
		if(pg_num_rows($rs) > 0) {
			$result = pg_fetch_all($rs);
		
			foreach ($result as $resultado){
				$array[$resultado['imobpatrimonio']]['imoboid'] = $resultado['imoboid'];
			}
		}
		
		return $array;
	}
	
	/**
	 * Pega o serial dos imobilizados que não existem
	 * @param int $entoid
	 * @param int $equprdoid
	 * @param array $array
	 * @return array
	 */
	public function getSerialImobilizadoInexistentes($entoid, $equprdoid, $array){
		
		$retorno = "";
		for($x = 0; $x < count($array) ; $x++){
			$patrimonio = $array[$x][0];
			
			$sql = "SELECT 
			           1
			        FROM 
			            imobilizado
			        WHERE 
			            imobentoid = $entoid
			            AND imobprdoid = $equprdoid
						AND imobpatrimonio = $patrimonio";
			
			$rs = pg_query($this->conn, $sql);
			
			if(pg_num_rows($rs) == 0) {
				$retorno .= "," . $patrimonio;
			}
		}
		
		return $retorno;
	}
	
	/**
	 * Pega equipamentos pelo patrimonio e tipo de produto
	 * @param text $prdproduto
	 * @param text $patrimonio
	 */
	public function getEquipamentosLayout($prdproduto,$patrimonio){
		
		$sql = "SELECT 
		            equoid 
		        FROM 
		            produto, equipamento
		        INNER JOIN
		        	imobilizado ON imobpatrimonio = equpatrimonio
				WHERE 
				    prdoid=equprdoid
					AND prdproduto ILIKE '$prdproduto'
					AND imobpatrimonio = '$patrimonio'";

		if(@$rs = pg_query($this->conn, $sql)){
			if(pg_num_rows($rs) > 0) {
				$result = pg_fetch_all($rs);
				return $result[0]['equoid'];
			}
		}
	}

	/**
	 * Pega linhas
	 * @param text $CCID
	 * @return array
	 */
	public function getlinhas($CCID){
		
		$sql = "SELECT 
		            linaraoid, linnumero, lincid, lincsloid 
		        FROM
		            linha 
				WHERE lincid = '$CCID'";

		if(@$rs = pg_query($this->conn, $sql)){
			if(pg_num_rows($rs) > 0) {
				$result = pg_fetch_all($rs);
				return $result[0];
			}
		}
	}
	
	/**
	 * Pega DDD
	 * @param int $araoid
	 */
	public function getDDD($araoid){
		
		$sql = "SELECT 
		            arano_ddd 
		        FROM 
		            area 
		        WHERE 
		            araoid = '$araoid'";

		if(@$rs = pg_query($this->conn, $sql)){
			if(pg_num_rows($rs) > 0) {
				$result = pg_fetch_all($rs);
				return $result[0]['arano_ddd'];
			}
		}
	}
	
	/**
	 * Confere serial
	 * @param text $serial
	 */
	public function confereSerial($serial){
		
		$sql = "SELECT 
		            equoid 
		        FROM 
		            equipamento 
		        WHERE
		            equno_serie = $serial";
		
		if(@$rs = pg_query($this->conn, $sql)){
			if(pg_num_rows($rs) > 0) {
				$result = pg_fetch_all($rs);
				return $result[0]['equoid'];
			}
		}
		
		
	}
	
	/**
	 * Grava dados no equipamento
	 * @param int $numeroLinha
	 * @param int $equoid
	 * @param int $serial
	 * @param int $imei
	 * @param int $imoboid
	 * @param int $ddd
	 * @param int $rf
	 * @param int $importar_versao
	 * @param int  $ccid
	 * @param int  $nota_entrada
	 * @throws Exception
	 * @return string
	 */
	public function setEquipamento($numeroLinha, $equoid, $serial, $imei, $imoboid, $ddd, $rf , $importar_versao, $ccid, $nota_entrada){
		
		$arrayrf = @str_split(trim($rf));
		
		if(sizeof($arrayrf) != 3){
			throw new Exception('O RF "' . trim($rf) . '" informado deve conter 3 digitos.');
		}
		
		// Verifica se o CCID já está associado a um celular
		$resultado = $this->verificaCCIDAssociadoCelular($ccid);
		
		if(sizeof($resultado)>0 && $resultado!= false){
			throw new Exception('O CCID '.$ccid.' informado já está associado a um celular.');
		}
		
		$resultado = $this->verificaLinhaExiste($ccid);
		
		if(sizeof($resultado)>0){
			$resultado = $this->verificaExisteCelularImei($imei,$ccid,$resultado);
			
			if($resultado == "sucesso"){
				
				$resultado = $this->verificaEquipamentoImei($imei);

				if($resultado > 0 || $resultado == "sucesso"){
					throw new Exception('Já existe um equipamento cadastrado com IMEI ' . $imei . '. Patrimonio '. $resultado);
				}
			}
			
			if($this->verificaEquipamentoTemLinha($equoid) == 'nao_tem'){
				
				$sql = "UPDATE
							equipamento
						SET
							equno_serie = '$serial',
							equesn 		= '".trim($imei)."',
							equaraoid 	= '".$numeroLinha['linaraoid']."',
							equno_ddd 	= '".$ddd."',
							equeveoid 	= '".$importar_versao."',
							equentrada 	= '".$nota_entrada['entdt_entrada']."',
							equemissao 	= '".$nota_entrada['entdt_emissao']."',
							equno_fone	= '".$numeroLinha['linnumero']."'
						WHERE 
							equoid = $equoid";
				
				if(!pg_query($this->conn, $sql)){
					throw new Exception('Erro ao vincular o serial de equipamento.');
				}
				
				$sql = "UPDATE 
				            imobilizado 
				        SET 
				            imobserial 	= '$serial',
				            imobimsoid	= 3 
				        WHERE
				            imoboid = $imoboid";
				
				if(!pg_query($this->conn, $sql)){
					throw new Exception('Erro ao vincular o serial de equipamento bem de consumo.');
				}
				
				$sql = "SELECT
							*
						FROM
							atributo_equipamento_ct
						WHERE
				            aectimoboid = $imoboid";
				
				if(!$rs = pg_query($this->conn, $sql)){
					throw new Exception('Erro ao verificar atributo do equipamento.');
				}
				
				/**
				 * Verifica se já existe rf para o imobilizado
				 * Caso tenha atualiza a ultima cadastrada com data de exclusão e insere uma nova RF
				 * Caso não tenha insere uma nova RF
				 */
				if(pg_num_rows($rs) > 0) {
					
					$result = pg_fetch_all($rs);
					
					foreach ($result as $resultado){
						$sql = "UPDATE 
						            atributo_equipamento_ct
						        SET 
						            aectdt_exclusao = NOW()
						        WHERE
						            aectimoboid = $imoboid";
						
						if(!pg_query($this->conn, $sql)){
							throw new Exception('Erro desabilitar o rf de equipamento.');
						}else{
							$sql = "INSERT INTO 
										atributo_equipamento_ct 
										(aectimoboid,aectrf) 
									VALUES 
										($imoboid,$rf)";
							if(!pg_query($this->conn, $sql)){
								throw new Exception('Erro ao vincular o rf de equipamento.');
							}
						}
					}
				}else{
					$sql = "INSERT INTO 
								atributo_equipamento_ct 
								(aectimoboid,aectrf) 
							VALUES 
								($imoboid,$rf)";
					
					if(!pg_query($this->conn, $sql)){
						throw new Exception('Erro ao vincular o rf de equipamento.');
					}
				}
			}else{
				throw new Exception('Equipamento já possui linha cadastrada');
			}
		}else{
			throw new Exception('CCID ' . $ccid . ' não existe');
		}
		
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
					lincid ilike '$ccid'  
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
					lincid ilike '$ccid'
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
					equesn ilike '$equesn'
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
					AND celesn ilike '$imei'";

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
			$celobs           = "Celular Cadastrado via Importação de Serial de Imobilizado";
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
													'Alteração via importação de Serial.'
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
			$celobs           = "Celular Cadastrado via Importação de Serial de Imobilizado";
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
									'Alteração via importação de Serial.'
								);";
			
			if(!pg_query($this->conn, $sql)){
				throw new Exception('Erro: Não foi possivel cadastrar celular');
				return "erro";
			}
		}
		
		return "sucesso";
	}
	
	/**
	 * Busca os produtos
	 * @param array $params
	 */
	public function getProdutos($params = null) {
	
		$sql = "
	    	SELECT
    			distinct
	    		prdoid,
    	        prdproduto
	    	FROM
	    		produto
    		INNER JOIN
    			imobilizado ON imobprdoid = prdoid
	    	WHERE
    			prdproduto IS NOT NULL
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
		
		if(@$rs = pg_query($this->conn, $sql)){
			if(pg_num_rows($rs) > 0) {
				return "nao_tem";
			}else{
				return "tem";
			}
		}
	}

	/** 
	* Verifica a quantidade de equipamentos ainda sem serial associado
	*
	* @param int $entoid 	- ID Tabela Entrada
	* @param int $equprdoid - ID Tabela Produto
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
		    					equnf_entrada = ". intval($entoid) ."
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
				                imobilizado 
				            WHERE 
				                imobentoid =  ". intval($entoid) ." 
	    					AND 
	    						imobprdoid = ". intval($equprdoid) ."
	    					AND
	    						imobpatrimonio IS NOT NULL
    						AND
                				(imobserial IS NULL OR TRIM(imobserial) ='')
						) 
				) AS FOO";
				
		if($rs = pg_query($this->conn, $sql)) {			
			
			$retorno = pg_fetch_object($rs);
			$total = isset($retorno->resto) ? $retorno->resto : 0;
		}

		return $total;
	}


}

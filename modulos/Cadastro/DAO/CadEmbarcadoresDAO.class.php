<?php
 
/**
 * @file CadEmbarcadoresDAO.class.php
 * @author Diego de Campos Noguês
 * @version 17/06/2013
 * @since 17/06/2013
 * @package SASCAR CadEmbarcadoresDAO.class.php
 */
/**
 * Acesso a dados para o módulo Embarcadores
 */
class CadEmbarcadoresDAO {
	
	/**
	 * Conexão com o banco de dados
	 * @var resource
	 */
	private $conn;	
	
	/**
	 * Construtor, recebe a conexão com o banco
	 * @param resource $connection
	 * @throws Exception
	 */
	public function __construct() {        
        global $conn;
        $this->conn = $conn;   
        $this->usuoid = $_SESSION['usuario']['oid'];    

    }

    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }

    public function getGerRisco ($gerenciadora) {
	
    	$sql = "
            SELECT 
            	geroid, gernome 
            FROM 
            	gerenciadora 
            WHERE 
            	gerexclusao IS NULL
            	and gernome ilike '%$gerenciadora%'
            ORDER BY gernome";

        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[pg_fetch_result($rs, $i, 'geroid')] = utf8_encode(htmlentities(pg_fetch_result($rs, $i, 'gernome')));
        }
        return $result;    	
    }

	   public function getGerRiscoRel($where = '') {
    	$sql = "
            SELECT 
            	geroid, gernome 
            FROM 
            	gerenciadora 
            WHERE 
            	gerexclusao IS NULL
            	".$where."
            ORDER BY gernome";

        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[pg_fetch_result($rs, $i, 'geroid')] = htmlentities(pg_fetch_result($rs, $i, 'gernome'));
        }
        return $result;    	
    }
	
    public function getSegmentos ($selected = null) {
    	$sql = "
            SELECT 
            	segoid, segdescricao 
            FROM 
            	segmento 
            ORDER BY segdescricao";

        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[pg_fetch_result($rs, $i, 'segoid')] = pg_fetch_result($rs, $i, 'segdescricao');
        }
        return $result;    	
    }

    public function getGerenciadorasPorEmbarcador ($idEmbarcador, $returnArray = false, $label = false) {
    	$sql = "
            SELECT 
            	embggeroid, 
            	gernome
            FROM 
            	embarcador_gerenciadora 
            INNER JOIN
            	gerenciadora 
            		ON geroid = embggeroid
            WHERE 
            	embgemboid = {$idEmbarcador}
			order by gernome";

        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            if($label):
            	$result[pg_fetch_result($rs, $i, 'embggeroid')] = htmlentities(pg_fetch_result($rs, $i, 'gernome'));
            else:
            	$result[] = pg_fetch_result($rs, $i, 'embggeroid');
            endif;
        }

        if($returnArray === false &&
        						$label === false)
        	$result = implode(',', $result);

        return $result;    	
    }

    public function getTransportadorasPorEmbarcador ($idEmbarcador, $returnArray = false, $label = false) {

    	$sql = "SELECT 
					embtclioid, clinome
				FROM embarcador_transportadora 
					INNER JOIN clientes ON clioid = embtclioid
					INNER JOIN contrato ON conclioid = clioid
					INNER JOIN tipo_contrato ON conno_tipo = tpcoid
				WHERE embtemboid = {$idEmbarcador} 
					AND clientes.clitipo = 'J'
					AND clidt_exclusao is null
					AND condt_exclusao is null 
					AND concsioid = 1
					AND tpcdescricao not ilike 'EX-%'
				GROUP BY embtclioid, clinome";

        $rs = pg_query($this->conn, $sql);
        
        $result = array();
		
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            if($label):
            	$result[pg_fetch_result($rs, $i, 'embtclioid')] = htmlentities(pg_fetch_result($rs, $i, 'clinome'));
            else:
            	$result[] = pg_fetch_result($rs, $i, 'embtclioid');
            endif;
        }

        if($returnArray === false &&
        						$label === false)
        	$result = implode(',', $result);

        return $result;    	
    }

	public function getTransportadoraCliente($cliente=''){

		$sql = "SELECT 
					clioid, clinome
				FROM clientes
					INNER JOIN contrato ON conclioid = clioid
					INNER JOIN tipo_contrato ON conno_tipo = tpcoid
				WHERE clinome ilike '%$cliente%'
					AND clientes.clitipo = 'J'
					AND clidt_exclusao is null
					AND condt_exclusao is null 
					AND concsioid = 1
					AND tpcdescricao not ilike 'EX-%'
				GROUP BY clioid, clinome
				ORDER BY clinome";

		$rs = pg_query($this->conn, $sql);

        $result = array();

        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[pg_fetch_result($rs, $i, 'clioid')] = htmlentities( pg_fetch_result($rs, $i, 'clinome'));
        }

		return $result;
	}

    public function getTransportadoras ($where = '') {
    	$sql = "
            SELECT 
            	traoid, tranome 
            FROM 
            	transportadora 
            WHERE 
            	traexclusao IS NULL
            	".$where." 
            ORDER BY tranome";

        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[pg_fetch_result($rs, $i, 'traoid')] = htmlentities( pg_fetch_result($rs, $i, 'tranome'));
        }

        return $result;
    }

    public function getEstados ($selected = null, $uf = false) {
    	$sql = "
            SELECT 
            	estuf, estnome 
            FROM 
            	estado 
            WHERE 
            	estpaisoid = 1 
            AND
            	estnome IS NOT NULL
            ORDER BY estuf";

        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
        	$label = (!$uf)?'estnome':'estuf';
            $result[pg_fetch_result($rs, $i, 'estuf')] = pg_fetch_result($rs, $i, 'estuf');
        }
        return $result;    	
    }

    public function getEmbarcador ($id) {
    	$sql = "
            SELECT 
            	*,
            	to_char(embdt_alteracao, 'DD/MM/YYYY HH24:MI') as embdt_alteracao
            FROM 
            	embarcador 
            WHERE 
            	emboid = {$id}";

        $rs = pg_query($this->conn, $sql);
        
        $result = pg_fetch_all($rs);
        $result = $result[0];

        // formata CNPJ
        $result['embcnpj'] = $this->formatarCNPJ($result['embcnpj']);

        return $result;    	
    }    

    public function pesquisa($params = array()) {

    	$sql = "
    		SELECT 
			    emboid, 
			    embnome,
			    segdescricao,
			    embcnpj, 
			    embrua, 
			    embnumero, 
			    embcomplemento, 
			    embbairro, 
       			embuf, 
       			embcidade, 
       			embcep, 
       			embcontato, 
       			embtelefone1, 
       			embtelefone2, 
       			embtelefone3, 
       			embemail, 
       			embsegoid,
			CASE WHEN 
			    embfrota = true 
			THEN 'S' 
			ELSE 'N' END AS frota_propria 
			FROM 
				embarcador 
			INNER JOIN
				segmento ON segoid = embsegoid";

		// filtro por nome
		if($params['embnome'] != '')
			$sql .= " AND to_ascii(embnome) ILIKE to_ascii('%".$params['embnome']."%')";

		// filtro por cnpj
		if($params['embcnpj'] != '') {
			$sql .= " AND embcnpj = '".$this->formatarCNPJ($params['embcnpj'], false)."'";
        }

		// filtro por segmento
		if($params['embsegoid'] != '')
			$sql .= " AND embsegoid = '".$params['embsegoid']."'";

		// filtro por estado/uf
		if($params['embuf'] != '')
			$sql .= " AND embuf = '".$params['embuf']."'";

		// filtro por cidade
		if($params['embcidade'] != '')
			$sql .= " AND to_ascii(embcidade) ILIKE to_ascii('%".$params['embcidade']."%')";

		// filtro por frota
		if($params['embfrota'] != '')
			$sql .= " AND embfrota = TRUE";

		$sql .= " ORDER BY embnome ";

		$rs = pg_query($this->conn, $sql);

		$result = pg_fetch_all($rs);
	
		if(!$result)
			$result = array();

        return $result;  

    }

    public function excluirDados($emboid)
	{
		$resultado = array();
		try{
	        pg_query($this->conn, "BEGIN");
	        
	        if(!$emboid){
	        	throw new Exception ("Erro ao Excluir.");
	        }

            $query  =   "SELECT 
                            count(*)
                        FROM
                            visita_posvenda
                        WHERE
                            vpvemboid = $emboid
                        ";
            if(!$sql = pg_query($this->conn, $query)){
                throw new Exception ("Erro ao consultar vínculo com visita de pós venda.");
            }   
            $ocorrencias_visita = pg_num_rows($sql);

            if ($ocorrencias_visita > 0)
            {
                throw new Exception ("Não é possível excluir embarcador com visita de pós venda cadastrada.");
            }


	        // exclui gerenciadoras relacionadas
	        $query = "  DELETE FROM
							embarcador_gerenciadora
						WHERE
							embgemboid = '$emboid'
					";

			if(!$sql = pg_query($this->conn, $query)){
	        	throw new Exception ("Houve um erro ao excluir o registro.");
	        }	

	        // exclui transportadoras relacionadas
	        $query = "  DELETE FROM
							embarcador_transportadora
						WHERE
							embtemboid = '$emboid'
					";

			if(!$sql = pg_query($this->conn, $query)){
	        	throw new Exception ("Houve um erro ao excluir o registro.");
	        }	

            //deleta do banco embarcador
            $query = "  DELETE FROM
                            embarcador
                        WHERE
                            emboid = '$emboid'
                    ";

            if(!$sql = pg_query($this->conn, $query)){
                throw new Exception ("Houve um erro ao excluir o registro.");
            }

			$mensagem = "Registro excluído com sucesso.";
			$acao = "index";
			pg_query($this->conn, "END");
		}
		catch (Exception $e) {
	        pg_query($this->conn, "ROLLBACK");
	        $mensagem = $e->getMessage();
	    }

	    $resultado['mensagem'] = $mensagem;
	    $resultado['acao'] = $acao;
	    return $resultado;

	}

	public function atualizaDados($params) {    	

    	// salva campos em outras variavies
    	$gerRiscoSelAdd = ($params['gerRiscoSelAdd'] != '')?explode(',', $params['gerRiscoSelAdd']):null;
    	$gerRiscoSelRem = ($params['gerRiscoSelRem'] != '')?explode(',', $params['gerRiscoSelRem']):null;
    	$transpSelAdd   = ($params['transpSelAdd']   != '')?explode(',', $params['transpSelAdd']):null;
    	$transpSelRem   = ($params['transpSelRem']   != '')?explode(',', $params['transpSelRem']):null;

    	// remove campos não utilizados no update
    	$params = $this->removeCamposPost($params);

    	// atualiza usuoid e dt_criacao
    	$params['embusuoid_alteracao'] = $this->usuoid;
    	$params['embdt_alteracao']     = 'NOW()';

        // se campo frota é desmarcado, seta como false
        $params['embfrota']     = ($params['embfrota'] == '')?'0':'1';

    	// remove formatação cep
    	$params['embcep'] = str_replace('-', '', $params['embcep']);

        // remove formatação cnpj
        $params['embcnpj']     = $this->formatarCNPJ($params['embcnpj'], false);

    	$fields = implode(',', array_keys($params));
    	$values = strtoupper(str_replace("''", 'null', "'".implode("','", array_map('trim', array_values($params)))."'"));

    	$valNome = $this->validaEmbarcadorNome($params['embnome'], $params['emboid']);
    	$valCNPJ = $this->validaEmbarcadorCNPJ($params['embcnpj'], $params['emboid']);

    	try{    		
    		// valida nome antes de iniciar a transaction    		
    		if(!$valNome) {
    			throw new Exception ("Registro já cadastrado");
    		}

    		// valida cnpj antes de iniciar a transaction    		
    		if(!$valCNPJ) {
    			throw new Exception ("Registro já cadastrado");
    		}

    		// valida nome antes de iniciar a transaction    		
    		if($params['embnome'] == '') {
    			throw new Exception ("Preencha os campos obrigatórios");
    		}

    		// valida segmento antes de iniciar a transaction    		
    		if($params['embsegoid'] == '') {
    			throw new Exception ("Preencha os campos obrigatórios");
    		}

    		pg_query($this->conn, "BEGIN");
	        
    		$query = "UPDATE embarcador SET ";
    			foreach($params as $key => $value):
    				if($value == '')
    					$value = 'null';
    				else
    					$value = "'".$value."'";

    					$query .= " {$key} = {$value},";
    			endforeach;

    		$query = trim($query, ',');
    		$query .= " WHERE emboid = {$params['emboid']}";

			if(!$sql = pg_query($this->conn, $query)){
	        	throw new Exception ("Houve um erro ao atualizar o registro.");
	        }	

	        $idEmbarcador = $params['emboid'];	        

	        // faz exclusões se necessário
	        if(count($gerRiscoSelRem) > 0):
	        	foreach ($gerRiscoSelRem as $q) {
	        		$q = "DELETE FROM 
							embarcador_gerenciadora 
						  WHERE 
							embggeroid = {$q}";

	        		if(!pg_query($this->conn, $q)){
	        			throw new Exception ("Houve um erro ao excluir o registro.");
	        		}
	        	}	        	
	        endif;

	        if(count($transpSelRem) > 0):	        
	        	foreach ($transpSelRem as $q) {
	        		$q = "DELETE FROM
							embarcador_transportadora 
						  WHERE 
							embtclioid = {$q}";
	        		if(!pg_query($this->conn, $q)){
	        			throw new Exception ("Houve um erro ao excluir o registro.");
	        		}
	        	}
	        endif;
	        // fim exclusoes

	        // faz inclusões se necessário
	        if(count($gerRiscoSelAdd) > 0):
	        	// ignora gerenciadoras que já existem no cadastro
	        	$gerRiscoSelArr = $this->getGerenciadorasPorEmbarcador($idEmbarcador, true);
	        	foreach($gerRiscoSelAdd as $key => $value):
	        		if(in_array($value, $gerRiscoSelArr))
	        			unset($gerRiscoSelAdd[$key]);
	        	endforeach;

	        	foreach ($gerRiscoSelAdd as $q) {	
	        		$q = "INSERT INTO
	        				embarcador_gerenciadora
	        				(embgemboid,embggeroid,embgdt_cadastro,embgusuoid_inclusao)
							VALUES
							({$idEmbarcador},{$q},'NOW()',{$this->usuoid})";

	        		if(!pg_query($this->conn, $q)){
	        			throw new Exception ("Houve um erro ao inserir o registro.");
	        		}
	        	}	        	
	        endif;

	        if(count($transpSelAdd) > 0):
	        	// ignora transportadoras que já existem no cadastro
	        	$transpSelArr = $this->getTransportadorasPorEmbarcador($idEmbarcador, true);
	        	foreach($transpSelAdd as $key => $value):
	        		if(in_array($value, $transpSelArr))
	        			unset($transpSelAdd[$key]);
	        	endforeach;

	        	foreach ($transpSelAdd as $q) {
	        		$q = "INSERT INTO
	        				embarcador_transportadora
	        				(embtemboid,embtclioid,embtdt_cadastro,embtusuoid_inclusao)
							VALUES
							({$idEmbarcador},{$q},'NOW()',{$this->usuoid})";

					if(!pg_query($this->conn, $q)){
	        			throw new Exception ("Houve um erro ao inserir o registro.");
	        		}
	        	}	        	
	        endif;
	        // fim inclusões

			$mensagem = "Registro atualizado com sucesso.";
			$acao = "index";
			pg_query($this->conn, "END");    
		}
		catch (Exception $e) {
	        pg_query($this->conn, "ROLLBACK");
	        $mensagem = $e->getMessage();
	    }

	    $resultado['mensagem'] = $mensagem;
	    $resultado['acao']     = $acao;
	    return $resultado;
        
    }

    public function inserirDados($params) {    

    	// salva campos em outras variavies
    	$gerRiscoSelAdd = ($params['gerRiscoSelAdd'] != '')?explode(',', $params['gerRiscoSelAdd']):null;
    	$gerRiscoSelRem = ($params['gerRiscoSelRem'] != '')?explode(',', $params['gerRiscoSelRem']):null;
    	$transpSelAdd   = ($params['transpSelAdd']   != '')?explode(',', $params['transpSelAdd']):null;
    	$transpSelRem   = ($params['transpSelRem']   != '')?explode(',', $params['transpSelRem']):null;

    	// remove campos não utilizados no primeiro insert
    	$params = $this->removeCamposPost($params);

    	// adiciona usuoid e dt_criacao
    	$params['embusuoid_inclusao']      = $this->usuoid;
    	$params['embdt_cadastro'] = 'NOW()';

    	$params['embusuoid_alteracao'] = $this->usuoid;
    	$params['embdt_alteracao']     = 'NOW()';

        // se campo frota é desmarcado, seta como false
        $params['embfrota']     = ($params['embfrota'] == '')?'0':'1';

    	// remove formatação cep
    	$params['embcep'] = str_replace('-', '', $params['embcep']);

        // remove formatação cnpj
        $params['embcnpj']     = $this->formatarCNPJ($params['embcnpj'], false);

    	// remove campo de id
    	unset($params['emboid']);

    	$fields = implode(',', array_keys($params));
    	$values = strtoupper(strtr(str_replace("''", 'null', "'".implode("','", array_map('trim', array_values($params)))."'"), "áéíóúâêôãõàèìòùçUüïö","ÁÉÍÓÚÂÊÔÃÕÀÈÌÒÙÇÜÏÖ"));

    	$valNome = $this->validaEmbarcadorNome($params['embnome'], $params['emboid']);
    	$valCNPJ = $this->validaEmbarcadorCNPJ($params['embcnpj'], $params['emboid']);

    	try{
    		
    		// valida nome antes de iniciar a transaction    		
    		if(!$valNome) {
    			throw new Exception ("Registro já cadastrado");
    		}

    		// valida cnpj antes de iniciar a transaction    		
    		if(!$valCNPJ) {
    			throw new Exception ("Registro já cadastrado");
    		}

    		// valida nome antes de iniciar a transaction    		
    		if($params['embnome'] == '') {
    			throw new Exception ("Preencha os campos obrigatórios");
    		}

    		// valida segmento antes de iniciar a transaction    		
    		if($params['embsegoid'] == '') {
    			throw new Exception ("Preencha os campos obrigatórios");
    		}

	        pg_query($this->conn, "BEGIN");
    		$query = "INSERT INTO embarcador
									($fields) 
								VALUES 
									($values)
								RETURNING 
									emboid";

			if(!$sql = pg_query($this->conn, $query)){
	        	throw new Exception ("Houve um erro ao cadastrar o registro.");
	        }	

	        $idEmbarcador = pg_fetch_row($sql, 0); 
	        $idEmbarcador = $idEmbarcador[0]; 	        

	        // faz exclusões se necessário
	        if(count($gerRiscoSelRem) > 0):
	        	foreach ($gerRiscoSelRem as $q) {
	        		$q = "DELETE FROM 
							embarcador_gerenciadora 
							WHERE 
								embggeroid = {$q}";

	        		if(!pg_query($this->conn, $q)){
	        			throw new Exception ("Houve um erro ao excluir o registro.");
	        		}
	        	}	        	
	        endif;

	        if(count($transpSelRem) > 0):	        
	        	foreach ($transpSelRem as $q) {
	        		$q = "DELETE FROM 
							embarcador_transportadora 
							WHERE 
								embtclioid = {$q}";
	        		if(!pg_query($this->conn, $q)){
	        			throw new Exception ("Houve um erro ao excluir o registro.");
	        		}
	        	}
	        endif;
	        // fim exclusoes

	        // faz inclusões se necessário
	        if(count($gerRiscoSelAdd) > 0):
	        	foreach ($gerRiscoSelAdd as $q) {
	        		$q = "INSERT INTO
	        				embarcador_gerenciadora
	        				(embgemboid,embggeroid,embgdt_cadastro,embgusuoid_inclusao)
							VALUES
							({$idEmbarcador},{$q},'NOW()',{$this->usuoid})";

	        		if(!pg_query($this->conn, $q)){
	        			throw new Exception ("Houve um erro ao inserir o registro.");
	        		}
	        	}	        	
	        endif;

	        if(count($transpSelAdd) > 0):
	        	foreach ($transpSelAdd as $q) {
	        		$q = "INSERT INTO
	        				embarcador_transportadora
	        				(embtemboid,embtclioid,embtdt_cadastro,embtusuoid_inclusao)
							VALUES
							({$idEmbarcador},{$q},'NOW()',{$this->usuoid})";

	        		if(!pg_query($this->conn, $q)){
	        			throw new Exception ("Houve um erro ao inserir o registro.");
	        		}
	        	}	        	
	        endif;
	        // fim inclusões

			$mensagem = "Registro cadastrado com sucesso.";
			$acao = "index";
			pg_query($this->conn, "END");    
		}
		catch (Exception $e) {
	        pg_query($this->conn, "ROLLBACK");
	        $mensagem = $e->getMessage();
	    }

	    $resultado['mensagem'] = $mensagem;
	    $resultado['acao']     = $acao;
	    return $resultado;
        
    }

    public function validaEmbarcadorNome($nome, $idCadastro = null) {

    	$retorno = false;

    	$sql = "SELECT emboid FROM embarcador WHERE embnome ILIKE upper('{$nome}')";  

    	$rs = pg_query($this->conn, $sql);

    	$count = pg_num_rows($rs);


    	// se não encontrar pelo nome retorna true e libera o cadastro
    	if($count == 0)
    		$retorno = true;

    	// se tem id é 'update', não pode barrar atualização do mesmo registro
		if($idCadastro != null && $retorno == false):
			$idBanco = pg_fetch_result($rs, 0, 'emboid');

			if($idCadastro == $idBanco)
				$retorno = true;

		endif;    

    	return $retorno;
    }

    public function validaEmbarcadorCNPJ($cnpj, $idCadastro = null) {

    	$retorno = false;

    	$sql = "SELECT emboid FROM embarcador WHERE embcnpj = '{$cnpj}'";    

    	$rs = pg_query($this->conn, $sql);

    	$count = pg_num_rows($rs);

    	// se não encontrar pelo pelo cnpj retorna true e libera o cadastro
    	if($count == 0)
    		$retorno = true;

    	// se tem id é 'update', não pode barrar atualização do mesmo registro
		if($idCadastro != null && $retorno == false):
			$idBanco = pg_fetch_result($rs, 0, 'emboid');

			if($idCadastro == $idBanco)
				$retorno = true;

		endif;     

    	return $retorno;
    }

    public function	removeCamposPost($params) {
    	// campos que devem permanecer
    	$arrCampos = array(
    			'emboid',
    			'embnome',
				'embcnpj',
				'embrua',
				'embnumero',
				'embcomplemento',
				'embbairro',
				'embcidade',
				'embuf',
				'embcep',
				'embcontato',
				'embtelefone1',
				'embtelefone2',
				'embtelefone3',
				'embemail',
				'embfrota',
				'embsegoid',
				'embobservacao'
    		);

    	return array_intersect_key($params, array_flip($arrCampos));    	
    }

    function formatarCNPJ($campo, $formatado = true){
        //retira caracteres especiais
        $codigoLimpo = preg_replace("/\D/",'',$campo);  

        //verifica se o codigoLimpo é válido
        if (!is_numeric($codigoLimpo)){
            return false; 
        }   
     
        if ($formatado){ 
            // seleciona a máscara para cnpj
            $mascara = '##.###.###/####-##'; 
     
            $indice = -1;
            for ($i=0; $i < strlen($mascara); $i++) {
                if ($mascara[$i]=='#') $mascara[$i] = $codigoLimpo[++$indice];
            }
            //retorna o campo formatado
            $retorno = $mascara;
     
        }else{
            //se não quer formatado, retorna o campo limpo
            $retorno = $codigoLimpo;
        }
     
        return $retorno;
     
    }
	
}
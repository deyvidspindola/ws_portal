<?php
 
/**
 * @file CadCadConfiguracaoEquipamentoDAO.class.php
 * @author Leandro Alves Ivanaga - leandroivanaga@brq.com
 * @version 07/08/2013
 * @since 07/08/2013
 * @package SASCAR CadCadConfiguracaoEquipamentoDAO.class.php
 */
/**
 * Acesso a dados para o módulo de Cadastro de Configuração de Equipamento
 */
class CadConfiguracaoEquipamentoDAO {
	
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

    /**
     * Busca os equipamentos referentes a CargoTracck que estejam ativos e não excluidos 
     */
    public function getEquipamentos($params) {
    	
        //ptioid 1: Imobilizado
        //ptioid 6: Consumo com Baixa Por Serial

    	$sql = "
	    	SELECT
	    		prdoid, 
    	        prdproduto,
            	(CASE ptioid WHEN  1
                    THEN 'I'
                WHEN 6
                    THEN 'C'                
                END) AS tipoproduto, ptidescricao, prdtp_cadastro,
                imotdescricao
	    	FROM
	    		produto
            INNER JOIN
            	produto_tipo ON prdptioid = ptioid            
            LEFT JOIN 
                imobilizado_tipo ON imotoid = prdimotoid
	    	WHERE
	    		prdcargotracck IS TRUE
            AND
                prddt_exclusao is null 
    		AND 
                prdstatus='A' 
    		
	    	";
    	
    	if ($params['equipamento_busca'] != "") {
    		$sql .= " AND prdproduto ILIKE '%". $params['equipamento_busca'] ."%'";
    	}

        if ($params['tipo_equip'] == 'DCT') {
            $sql .= " AND prdptioid = 6";
        }

        if ($params['tipo_equip'] == 'RTN') {
            $sql .= " AND (prdptioid = 1 AND prdimotoid = 3) ";
        }

        if($params['prdoid'] != ""){
            $sql .= " AND prdoid = ".$params['prdoid'];
        }
    	
    	$sql .= " ORDER BY prdproduto ASC";
        
    	$rs = pg_query($this->conn, $sql);
    	
    	$result = pg_fetch_all($rs);
    	
    	return $result;
    }

    /**
     * Buscar cliente por nome sendo ele PJ || PF
     *
     * @param stdClass $parametros
     * @return array $retorno
     */
    public function buscarClienteNome($parametros) {

        $retorno = array();    
    
        $sql = "SELECT    
                    clioid,
                    clinome,
                    (CASE WHEN clitipo = 'J' THEN
                        clino_cgc
                    ELSE
                        clino_cpf
                    END) AS doc,
                    clitipo AS tipo    
               FROM
                    clientes
               WHERE
                    clidt_exclusao IS NULL ";
    
        if (trim($parametros['tipo']) != '') {
            $sql  .= " AND clitipo = '" . pg_escape_string($parametros['tipo']) . "' ";
        }
         
    
        $sql .= " AND
                        clinome ILIKE '" . pg_escape_string($parametros['nome']) . "%'
    
               ORDER BY
                        clinome
               LIMIT 50";

        $rs = pg_query($this->conn, $sql);

        while($obj = pg_fetch_object($rs)) {
            $retorno[] = $obj;
        }    
    
        return $retorno;
    }
    /**
     * Buscar cliente que possuem Configurações 
     *
     * @param stdClass $parametros
     * @return array $retorno
     */
    public function buscarClienteTipoEquipamento($param) {
    
    	$retorno = array();
    	    
    	$sql = "
				SELECT DISTINCT
                    clioid,
                    clinome,
                    (CASE WHEN clitipo = 'J' THEN
                        clino_cgc
                    ELSE
                        clino_cpf
                    END) AS doc
               FROM
                    clientes
				INNER JOIN 
					configuracao_equipamento_cliente ON ceqcclioid = clioid
				INNER JOIN 
					configuracao_equipamento ON ceqcceqpoid = ceqpoid
				INNER JOIN 
					produto ON ceqpprdoid = prdoid
				WHERE
                    clidt_exclusao IS NULL";
        if($param['tipo_equip'] == 'DCT'){
        	$sql .= " AND prdptioid = 6";
        }
        if($param['tipo_equip'] == 'RTN'){
        	$sql .= " AND prdptioid = 1  AND prdimotoid = 3";
    	}
    	if($param['ceqpprdoid']){
    		$sql .= " AND prdoid = ".$param['ceqpprdoid'];
    	} else if($param['equipamento_selected']){
    		$sql .= " AND prdoid = ".$param['equipamento_selected'];
    	}
                    
		$sql .= " ORDER BY clinome";
    
    	$rs = pg_query($this->conn, $sql);
    
    	while($obj = pg_fetch_object($rs)) {
    		$retorno[] = $obj;
    	}
    
    	return $retorno;
    }

    public function getObrigacaoFinanceira($obroid = null) {
        
        $result = array();
        $sql = "
            SELECT 
                obroid,
                obrobrigacao
            FROM
                obrigacao_financeira
            WHERE 
                obrdt_exclusao IS NULL ";

        if($obroid != null){
            $sql .=" AND obroid =".$obroid;
        }else{
			// busca somente se estiver na tabela de preços vigente
        	$sql .=" AND obroid IN (SELECT tpiobroid 
								FROM tabela_preco
								INNER JOIN tabela_preco_item on tpitproid = tproid
								WHERE tprstatus = 'A'
								AND tpiexclusao IS NULL) ";
        }
        
        $sql .= " ORDER BY obrobrigacao ";
                
        $rs = pg_query($this->conn, $sql);
        
        if(pg_num_rows($rs) > 0) {
            $result = pg_fetch_all($rs);
        }    
        
        return $result;     
    } 


    public function pesquisa($params = array()) {

        $retorno = array();

        $sql = "SELECT DISTINCT
                    ceqpoid, 
                    veqpdescricao, 
                    iposeqpdescricao, 
                    emeeqpdescricao, 
                    ciceqpdescricao, 
                    prdproduto,
                    ceqpdisp_comercial
                FROM 
                    configuracao_equipamento
                INNER JOIN 
                    produto p ON p.prdoid = ceqpprdoid
                LEFT JOIN 
                    configuracao_equipamento_cliente ON ceqcceqpoid = ceqpoid
                LEFT JOIN 
                    validade_equipamento ve ON ve.veqpoid = ceqpveqpoid
                LEFT JOIN 
                    intervalo_posicionamento_equipamento ip ON ip.iposeqpoid = ceqpiposeqpoid
                LEFT JOIN 
                    emergencia_equipamento ee ON ee.emeeqpoid = ceqpemeeqpoid
                LEFT JOIN 
                    ciclo_equipamento ce ON ce.ciceqpoid = ceqpciceqpoid
                WHERE 
                    ceqpdt_exclusao IS NULL";

        // FILTRO PELOS PARAMETROS INFORMADOS
        // equipamento

        if($params['ceqpoid'] != "") {
        	
            $sql .= " AND ceqpoid = ". intval($params['ceqpoid'])."";
            
        }else{
        	
	        if($params['tipo_equip'] == "DCT") {
	        	$sql .= " AND prdptioid = 6";
	        }
	        if($params['tipo_equip'] == "RTN") {
	            $sql .= " AND (prdptioid = 1 AND prdimotoid = 3)";
	        }
	        
	        if (intval($params['ceqpprdoid']) > 0) {
	            $sql .= " AND ceqpprdoid = ". intval($params['ceqpprdoid'])."";
	        }
	        // validade
	        if ($params['ceqpveqpoid'] != "") {
	            $sql .= " AND ceqpveqpoid = ". intval($params['ceqpveqpoid'])."";
	        }
	        // intervalo
	        if ($params['ceqpiposeqpoid'] != "") {
	            $sql .= " AND ceqpiposeqpoid = ". intval($params['ceqpiposeqpoid'])."";
	        }
	        // emergencia
	        if ($params['ceqpemeeqpoid'] != "") {
	            $sql .= " AND ceqpemeeqpoid = ". intval($params['ceqpemeeqpoid'])."";
	        }
	        // ciclo
	        if ($params['ceqpciceqpoid'] != "") {
	            $sql .= " AND ceqpciceqpoid = ". intval($params['ceqpciceqpoid'])."";
	        }
	        // restricao de venda
	        if ($params['venda_restrita'] == "S") {
	            $sql .= " AND ceqcoid IS NOT NULL";
	        }
	        // clientes
	        if (is_array($params['cliente'])) {
	            $clientes = implode(",", $params['cliente']);
	            $sql .= " AND ceqcclioid IN ($clientes)";
	        }

            if($params['ceqpdisp_comercial'] != ""){
                $ceqpdisp_comercial = ($params['ceqpdisp_comercial'] == 'S') ? 'true' : 'false';
                $sql .= " AND ceqpdisp_comercial = $ceqpdisp_comercial" ;
            }
	        
        }

        $sql .= " ORDER BY prdproduto ASC"; //echo $sql; exit;

        $rs = pg_query($this->conn, $sql);

        if(pg_num_rows($rs) > 0) {
            $retorno = pg_fetch_all($rs);
        }

        return $retorno;  
    }

    public function getConfiguracaoEquipamento ($id) {

        //ptioid 1: Imobilizado
        //ptioid 6: Consumo com Baixa Por Serial

        $sql = "SELECT 
                    ceqpoid, ceqpprdoid, ceqpveqpoid, 
                    ceqpiposeqpoid, ceqpemeeqpoid, ceqpciceqpoid, 
                    prdproduto AS equipamento_busca, 
                    (CASE ptioid WHEN  1
                    THEN 'I'
                    WHEN 6
                    THEN 'C'                
                    END) AS tipoproduto,
                    ceqpobroid, ceqpobroid_taxa, ceqpincidencia_taxa,
                    ptidescricao, 
                    prdtp_cadastro,
                    imotdescricao, ceqpdisp_comercial
                FROM 
                    configuracao_equipamento
                INNER JOIN 
                    produto p ON p.prdoid = ceqpprdoid
                INNER JOIN
                    produto_tipo ON prdptioid = ptioid
                LEFT JOIN 
                    imobilizado_tipo ON imotoid = prdimotoid
                WHERE 
                    ceqpoid = ".intval($id);

        $rs = pg_query($this->conn, $sql);

        $result = pg_fetch_all($rs);
        $result = $result[0];       

        return $result;     
    }

    public function getConfiguracaoEquipamentoTaxa($ceqpoid){
        $sql = "SELECT 
                    CASE WHEN ceqptxincidencia_taxa = 'C' THEN
                    'Cobrar a Cada Contrato'
                    ELSE
                    'Cobrar a Cada Equipamento'
                    END AS incidencia,
                    obroid,
                    obrobrigacao,
                    ceqptxoid
                FROM 
                    configuracao_equipamento_taxa
                INNER JOIN
                    obrigacao_financeira ON obroid = ceqptxobroid
                WHERE 
                    ceqptxceqpoid = '$ceqpoid'
                AND ceqptxdt_exclusao IS NULL";

        $rs = pg_query($this->conn, $sql);

        if(pg_num_rows($rs) > 0) {
            $result = pg_fetch_all($rs);
        }

        return $result;
    }

    /**
    * Recupera informações da relacao Cliente > configuração
    * @param int $ceqpoid
    * @return array
    */
    public function buscarConfiguracaoEquipamentoCliente($ceqpoid){

        $retorno = array();

        $sql = "SELECT
                    clioid,
                    clinome,
                    (CASE WHEN clitipo = 'J' THEN
                    clino_cgc
                    ELSE
                    clino_cpf
                    END) AS doc,
                    clitipo AS tipo 
                FROM 
                    configuracao_equipamento_cliente 
                INNER JOIN 
                    clientes ON (clioid = ceqcclioid)
                WHERE
                    ceqcceqpoid = ".intval($ceqpoid);

        $rs = pg_query($this->conn, $sql);

        while($tuplas = pg_fetch_object($rs)) {
            $retorno[] = $tuplas;
        }       

        return $retorno;
    }

    public function excluirDados($ceqpoid) {
        $resultado = array();
        try{
            pg_query($this->conn, "BEGIN");
            
            if(!$ceqpoid){
                throw new Exception ("Erro ao Excluir.");
            }           

            $query = "  UPDATE
                            configuracao_equipamento
                        SET
                            ceqpdt_exclusao = NOW(),
                            ceqpusuoid_exclusao = '$this->usuoid'
                        WHERE
                            ceqpoid = '$ceqpoid'
                    ";

            if(!$sql = pg_query($this->conn, $query)){
                throw new Exception ("Houve um erro ao excluir o registro.");
            }

            $mensagem = 'Registro excluído com sucesso.';
            $status   = 'sucesso';
            $acao     = 'index';
            pg_query($this->conn, "END");
        }
        catch (Exception $e) {
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status   = 'alerta';
        }

        $resultado['mensagem'] = $mensagem;
        $resultado['status']   = $status;
        $resultado['acao']     = $acao;
        return $resultado;
    }

    public function atualizaDados($params) {     
        
        $status = 'erro';
        $listaClientes = $params['listaClientes'];

        try{
            // Pesquisa se existe outra configuração além da que esta sendo alterada com os mesmo parametros informados
            $configuracaoExistente = $this->configuracaoExiste($params);

            // Verifica se os campos obrigatorios foram informados
            $valConfiguracao = $this->validaConfiguracao($params);

            // valida se existe configuração com esta mesma configuração
            if(count($configuracaoExistente) > 0) {
                throw new Exception ("Configuração já cadastrada para este equipamento.");
            }

            // Verifica se campos obrigatorios foram todos informados
            if ($valConfiguracao === false){
                $status = 'alerta';
                throw new Exception ("Preencha os campos obrigatórios");
            }

            // remove campos não utilizados no update
            $params = $this->removeCamposPost($params);

            // atualiza usuoid e dt_alteracao
            $params['ceqpusuoid_alteracao'] = $this->usuoid;
            $params['ceqpdt_alteracao']     = 'NOW()';

            if($params['tipoproduto'] != 'C' && $params['tipoproduto'] != 'I'){
                $params['ceqpobroid_taxa'] = '';
            }
            if($params['ceqpobroid_taxa'] == ''){
                $params['ceqpincidencia_taxa'] = '';
            }

            ksort($params); // ordena o array pelas chaves
            array_pop($params); // remove a chave tipo de produto

            $params['ceqpdisp_comercial'] = ($params['ceqpdisp_comercial'] == 'S') ? 'true' : 'false';

            $fields = implode(',', array_keys($params));
            $values = strtoupper(str_replace("''", 'null', "'".implode("','", array_map('trim', array_values($params)))."'"));

            pg_query($this->conn, "BEGIN");

            $query = "UPDATE configuracao_equipamento SET ";
            foreach($params as $key => $value){
                if($value == '')
                    $value = 'null';
                else
                    $value = "'".$value."'";

                    $query .= " {$key} = {$value},";
            }

            $query = trim($query, ',');
            $query .= " WHERE ceqpoid = {$params['ceqpoid']}";

            if(!$sql = pg_query($this->conn, $query)){
                $status = 'erro';
                throw new Exception ("Houve um erro ao atualizar o registro.");
            }    

            $this->cadastraTaxas($params['ceqpoid']);

            $this->atualizarRelacaoClienteConfiguracao($params['ceqpoid'], $listaClientes);             

            $mensagem = 'Registro atualizado com sucesso.';
            $status   = 'sucesso';
            $acao     = 'index';
            pg_query($this->conn, "END");    
        } catch (Exception $e) {
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status   = 'alerta';
        }

        $resultado['mensagem'] = $mensagem;
        $resultado['status']   = $status;
        $resultado['acao']     = $acao;
        return $resultado;

    }

    /**
     * Função para verificar se já existe outra configuração exatamente igual cadatrada
     */
    public function configuracaoExiste($params = array()) {

        $sql = "SELECT 
                ceqpoid, ceqpprdoid, ceqpveqpoid, ceqpiposeqpoid, ceqpemeeqpoid, ceqpciceqpoid
                FROM 
                configuracao_equipamento
                WHERE
                ceqpdt_exclusao IS NULL";

        // FILTRO DE ACORDO COM OS PARAMETROS
        // equipamento
        if ($params['ceqpprdoid'] != "") {
            $sql .= " AND ceqpprdoid = {$params['ceqpprdoid']}";
        } else {
            $sql .= " AND ceqpprdoid IS NULL ";
        }

        // validade
        if ($params['ceqpveqpoid'] != "") {
            $sql .= " AND ceqpveqpoid = {$params['ceqpveqpoid']}";
        } else {
            $sql .= " AND ceqpveqpoid IS NULL ";
        }

        // intervalo
        if ($params['ceqpiposeqpoid'] != "") {
            $sql .= " AND ceqpiposeqpoid = {$params['ceqpiposeqpoid']}";
        } else {
            $sql .= " AND ceqpiposeqpoid IS NULL ";
        }

        // emergencia
        if ($params['ceqpemeeqpoid'] != "") {
            $sql .= " AND ceqpemeeqpoid = {$params['ceqpemeeqpoid']}";
        } else {
            $sql .= " AND ceqpemeeqpoid IS NULL ";
        }

        // ciclo
        if ($params['ceqpciceqpoid'] != "") {
            $sql .= " AND ceqpciceqpoid = {$params['ceqpciceqpoid']}";
        } else {
            $sql .= " AND ceqpciceqpoid IS NULL ";
        }

        // CONDIÇÃO UTILIZADA PARA VERIFICAR SE EXISTE REGISTRO COM A MESMA CONFIGURAÇÃO
        // E DIFERENTE DO REGISTRO QUE ESTA SENDO ALTERADO
        if ($params['acao'] == "atualizar" && $params['ceqpoid']) {
            $sql .= " AND ceqpoid != {$params['ceqpoid']}";
        }

        $rs = pg_query($this->conn, $sql);

        $result = pg_fetch_all($rs);

        if(!$result)
            $result = array();

        return $result;
    }

    /**
    * Função para validar se os campos obrigatorios foram informados
    */
    public function validaConfiguracao($params) {

        // Considera inicialmente que foi informado corretamente, caso algum campo obrigatorio não informado retorna false
        $retorno = true;

        // Equipamento
        if (($params['ceqpprdoid'] == "" || empty($params['ceqpprdoid']))
        	&& ($params['equipamento_selected'] == "" || empty($params['equipamento_selected']))) {
            $retorno = false;
        }

        if($params['tipo_equip'] == 'DCT'){ 
            // Validade
            if ($params['ceqpveqpoid'] == "" || empty($params['ceqpveqpoid'])) {
                $retorno = false;
            }
        }    
        
        return $retorno;
    }

    public function removeCamposPost($params) {
        // campos que devem permanecer (Obrigatorio)
        $arrCampos = array(
            'ceqpprdoid',
            'ceqpveqpoid',
            'ceqpiposeqpoid',
            'ceqpemeeqpoid',
            'ceqpciceqpoid',
            'ceqpobroid',
            'ceqpobroid_taxa',
            'ceqpincidencia_taxa',
            'tipoproduto',
        	'ceqpobroid_valor_faturamento',
            'ceqpdisp_comercial'
        );

        // campos que devem permanecer somente se forem informados - em caso de edição possui esta chave
        if ($params['ceqpoid'] != "") {
            array_push($arrCampos, 'ceqpoid');
        }

        return array_intersect_key($params, array_flip($arrCampos));        
    }

    public function cadastraTaxas($ceqpoid){

        try {
            if(sizeof($_SESSION['conf_equ_taxa'])>0){
                while ($incidencia_taxa = current($_SESSION['conf_equ_taxa'])) {
                    $obroid =  key($_SESSION['conf_equ_taxa']);

                    $sql = "INSERT INTO 
                        configuracao_equipamento_taxa
                        (ceqptxobroid,ceqptxceqpoid,ceqptxincidencia_taxa)
                        VALUES
                        ('$obroid','$ceqpoid','$incidencia_taxa')";

                    if(!$sql = pg_query($this->conn, $sql)){
                        throw new Exception ("Houve um erro ao cadastrar o registro.");
                    }

                    next($_SESSION['conf_equ_taxa']);
                }
            }

            $mensagem = 'Registro cadastrado com sucesso.';
            $status   = 'sucesso';
            pg_query($this->conn, "COMMIT");
            pg_query($this->conn, "END");

        } catch (Exception $e) {
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status   = 'alerta';
        }

        $resultado['mensagem'] = $mensagem;
        $resultado['status']   = $status;
        return $resultado;
    }

    /**
    * Remove relacao de cliente com configuracao de equipamento 
    * e relaciona novamente com os novos dados
    * @param int $ceqcceqpoid | string $clientes
    * @return void
    */
    public function atualizarRelacaoClienteConfiguracao($ceqcceqpoid, $clientes) {       

        try{

            if(empty($ceqcceqpoid)) {
                throw new Exception('');
            }

            $sql = "DELETE FROM
                    configuracao_equipamento_cliente
                    WHERE
                    ceqcceqpoid = ".intval($ceqcceqpoid)."";

            if($rs = pg_query($this->conn, $sql)){    

            if(!empty($clientes)) {
                $this->adicionarRelacaoClienteConfiguracao($ceqcceqpoid, $clientes);  
            }                               

            } else {
                throw new Exception('');
            }               

        } catch(Exception $e){
            throw new Exception ("Não foi possível alterar os clientes vinculados para esta configuração.");
        }
    }

    /**
    * Adicona relacao de cliente com configuracao de equipamento
    * @param int $ceqcceqpoid | string $clientes
    * @return void
    */
    private function adicionarRelacaoClienteConfiguracao($ceqcceqpoid, $clientes) {

        $clientes = explode(',', $clientes);

        try{

            if(empty($ceqcceqpoid) || empty($clientes)) {
                throw new Exception('???');
            }

            $prepare = "PREPARE 
                        insert_configuracao_equipamento_cliente
                        AS INSERT INTO configuracao_equipamento_cliente (ceqcclioid, ceqcceqpoid) VALUES ($1,$2);";

            $rs = pg_query($this->conn, $prepare);

            foreach($clientes as $clioid) {

                $execute = "EXECUTE insert_configuracao_equipamento_cliente 
                            (".intval($clioid).",".intval($ceqcceqpoid).")";

                $rs = pg_query($this->conn, $execute);
            }            

        } catch(Exception $e){
            throw new Exception ("Não foi possível vincular os clientes para esta configuração.");
        }
    }

    public function existeTaxa($params){
        if(isset($params['ceqpoid']) && $params['ceqpoid']!=''){
            $sql = "SELECT 
            			1 
            		FROM 
            			configuracao_equipamento_taxa 
            		WHERE 
            			ceqptxceqpoid = '" . $params['ceqpoid'] . "' 
            			AND ceqptxobroid = '" . $params['ceqpobroid_taxa'] . "' 
            			AND ceqptxdt_exclusao IS NULL";

            $rs = pg_query($this->conn, $sql);

            if(pg_num_rows($rs) > 0) {
                return true;
            }

            return false;

        }else{
            return false;
        }
    }


    public function inserirDados($params) {  

        $status = 'erro';
        $listaClientes = $params['listaClientes'];
        $tipo_equip = $params['tipo_equip'];

        // remove campo de id
        unset($params['ceqpoid']);

        // Pesquisa se existe configuração com os mesmo parametros informados
        $configuracaoExistente = $this->configuracaoExiste($params);

        // if($params['tipo_equip'] == 'RTN' && $params['ceqpobroid_taxa'] == ''){
        //     $params['ceqpobroid_taxa'] = 339;
        //     $params['ceqpincidencia_taxa'] = 'C';
        // }

        // remove campos não utilizados no primeiro insert, permanece somente os campos informados
        $params = $this->removeCamposPost($params);

        // adiciona usuoid e dt_criacao
        $params['ceqpusuoid_cadastro']      = $this->usuoid;
        $params['ceqpdt_cadastro'] = 'NOW()';

        $params['ceqpusuoid_alteracao'] = $this->usuoid;
        $params['ceqpdt_alteracao']     = 'NOW()';

        if($params['ceqpobroid_taxa'] == ''){
            $params['ceqpincidencia_taxa'] = '';
        }

        ksort($params); // ordena o array pelas chaves
        array_pop($params); // remove a chave tipo de produto

        $params['ceqpdisp_comercial'] = ($params['ceqpdisp_comercial'] == 'S') ? 'true' : 'false';

        $fields = implode(',', array_keys($params));
        $values = strtoupper(str_replace("''", 'null', "'".implode("','", array_map('trim', array_values($params)))."'"));

        $params['tipo_equip'] = $tipo_equip;
        // Verifica se os campos obrigatorios foram informados
        $valConfiguracao = $this->validaConfiguracao($params);

        try{

            // valida se existe configuração com esta mesma configuração        
            if(count($configuracaoExistente) > 0) {
                $status = 'alerta';
                throw new Exception ("Configuração já cadastrada para este equipamento.");
            }

            // Verifica se campos obrigatorios foram todos informados
            if ($valConfiguracao === false){
                throw new Exception ("Preencha os campos obrigatórios");
            }

            pg_query($this->conn, "BEGIN");

            $query = "INSERT INTO configuracao_equipamento
            ($fields) VALUES ($values) RETURNING ceqpoid";

            if(!$sql = pg_query($this->conn, $query)){
                $status = 'erro';
                throw new Exception ("Houve um erro ao cadastrar o registro.");
            }    

            $ceqpoid = pg_fetch_result($sql, 0, 'ceqpoid');
            $this->cadastraTaxas($ceqpoid);

            if(!empty($listaClientes)) {
                $this->adicionarRelacaoClienteConfiguracao($ceqpoid, $listaClientes);
            }           

            $mensagem = 'Registro cadastrado com sucesso.';
            $status   = 'sucesso';
            $acao     = 'index';

            if($params['ceqpdisp_comercial'] == 'true'){
                $resultado['informativos']['mensagem']= 'Esta configuração está elegível para uso na plataforma de vendas e CRM.';
                $resultado['informativos']['status']   = 'info';
            }else{
                $resultado['informativos']['mensagem']= 'Esta configuração ainda não estará disponível na plataforma de vendas e CRM.';
                $resultado['informativos']['status']   = 'info';
            }
            pg_query($this->conn, "END");    
        } catch (Exception $e) {
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();            
        }

        $resultado['mensagem'] = $mensagem;
        $resultado['status']   = $status;
        $resultado['acao']     = $acao;
        return $resultado;
    }
    
    public function excluirTaxa($ceqptxoid){
    
    	try {
    			
    		$sql = "UPDATE
    		configuracao_equipamento_taxa
    		SET
    		ceqptxdt_exclusao = now()
    		WHERE
    		ceqptxoid = $ceqptxoid";
    			
    		if(!$query = pg_query($this->conn, $sql)){
    		throw new Exception ("Houve um erro ao excluir o registro.");
	        }
    
			$mensagem = 'Registro excluido com sucesso.';
            $status   = 'sucesso';
    		$acao     = 'index';
    		pg_query($this->conn, "END");
    					
    	} catch (Exception $e) {
    				
    	pg_query($this->conn, "ROLLBACK");
    		$mensagem = $e->getMessage();
    				$status   = 'alerta';
    
    	}
    
    		$resultado['mensagem'] = $mensagem;
    		$resultado['status']   = $status;
    		$resultado['acao']     = $acao;
    		return $resultado;
    }
    
}
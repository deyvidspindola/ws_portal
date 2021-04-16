<?php
/**
 * @file RelDataVencimentoDAO.php
 * @author cassio.bueno
 * @version 19/05/2017 16:50:00
 * @since 19/05/2017 16:50:00
 * @package SASCAR RelDataVencimentoDAO.php 
 */

//grava log de erro
ini_set("log_errors", 1);
/*ini_set('error_log','/tmp/boletagem_massiva_'.date('d-m-Y').'.txt');*/

class RelDataVencimentoDAO{
	
	/**
	 * Link de conexão com o banco
	 *
	 * @property resource
	 */
	private $conn;
	
	public $usuarioID;
	
	/**
	 * Construtor
	 *
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($conn) {
	
		$this->conn = $conn;
		
		if(empty($this->usuarioID)){
			$this->usuarioID = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid']: NULL;
		}
	}
	
	
	
	/**
	 * Retorna as campanhas geradas de acordo o filtro informado na tela montando a paginação
	 * 
	 * @param object $dados
	 */
	public function getCampanhas($pesquisa, $paginacao = null, $ordenacao = null){				

		if(!isset($paginacao)) {
			$select = "COUNT(titulo.titoid) AS total_registros";	
			$orderBy = "";
		
		}else{
		
			$select =  " 
					titulo.titoid, 
                    TO_CHAR(titulo.titemissao, 'DD/MM/YYYY') AS data_emissao,
                    titulo.titvl_titulo, 
                    TO_CHAR(titulo_historico_vencimento.thvdtanterior, 'DD/MM/YYYY') AS data_vencimento, 
                    TO_CHAR(titulo_historico_vencimento.thvdtposterior, 'DD/MM/YYYY') AS data_vencimento_alterada,
                    titulo_motivo_alteracao_vencimento.tmavdescricao, 
                    usuarios.nm_usuario, 
                    TO_CHAR(titulo_historico_vencimento.thvcadastro, 'DD/MM/YYYY') AS data, 
                    TO_CHAR(titulo_historico_vencimento.thvcadastro, 'HH24:MI:SS') AS hora 
			";
			
			$orderBy = " ORDER BY ";
			$orderBy .= (!empty($ordenacao)) ? $ordenacao :  "titoid, thvcadastro";		
			
		}
		
		$sql =" SELECT 
		               ".$select."   
				FROM 
                    titulo_historico_vencimento
                INNER JOIN titulo 
                    ON titulo.titoid = titulo_historico_vencimento.thvtitoid
                INNER JOIN titulo_motivo_alteracao_vencimento 
                    ON titulo_motivo_alteracao_vencimento.tmavoid = titulo_historico_vencimento.thvtmavoid
                INNER JOIN usuarios 
                    ON usuarios.cd_usuario = titulo_historico_vencimento.thvusuoid
                INNER JOIN clientes 
                    ON clientes.clioid = titulo.titclioid    
                WHERE 
					(titulo_historico_vencimento.thvcadastro 
						BETWEEN '".implode('-', array_reverse(explode('/', $pesquisa->data_ini)))." 00:00:01' 
						AND '".implode('-', array_reverse(explode('/', $pesquisa->data_fim)))." 23:59:59'
					)					
				";

		if($pesquisa->usuario_nome != 'NULL'){
			$sql .=" AND clientes.clinome ILIKE '%$pesquisa->usuario_nome%'";
		}

			
		if($pesquisa->filter_cpf_cnpj_gerador  != 'NULL'){
			//Filtro por CPF ou CNPJ        

            // limpa . / - da string para buscar no banco de dados
            $cpf_cgc = preg_replace( '#[^0-9]#', '', $pesquisa->filter_cpf_cnpj_gerador );
            $sql .= " AND ( clientes.clino_cpf = " .$cpf_cgc. "  OR clientes.clino_cgc = ".$cpf_cgc." ) ";
		}

		$sql .= $orderBy;
		
		if (isset($paginacao->limite) && isset($paginacao->offset)) {
			$sql.= "
                LIMIT
                    " . intval($paginacao->limite) . "
                OFFSET
                    " . intval($paginacao->offset) . "
            ";
		}
		
		if($_GET['cassio'] == "true"){
			echo '<pre>';
			var_dump($sql);
			echo '</pre>';
		}

		if(!$result = pg_query($this->conn, $sql)){
			throw new Exception('Falha ao pesquisar campanhas.');
		}
		
		if(pg_num_rows($result) > 0){
			return pg_fetch_all($result);
		}
		
		return false;		
		
	}
	
	/**
	 * inicia transação com o BD
	 */
	public function begin()	{
		$rs = pg_query($this->conn, "BEGIN;");
	}
	
	/**
	 * confirma alterações no BD
	 */
	public function commit(){
		$rs = pg_query($this->conn, "COMMIT;");
	}
	
	/**
	 * desfaz alterações no BD
	 */
	public function rollback(){
		$rs = pg_query($this->conn, "ROLLBACK;");
	}
	
	
}



?>
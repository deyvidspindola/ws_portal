<?php 
/**
 * @file ProdutoComSeguroDAO.clas..php
 * @author marcioferreira
 * @version 13/11/2013 15:00:28
 * @since 13/11/2013 15:00:28
 * @package SASCAR ProdutoComSeguroDAO.class.php 
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/log_produto_seguro_'.date('d-m-Y').'.txt');

class ProdutoComSeguroDAO{

	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	private $connProduto;


	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($connSiggo){

		$this->connProduto = $connSiggo;

	}
	
	
	/**
	 * Retorna mensagem (de/para) de retorno da operadora
	 *
	 * @param inteiro $idMensagem
	 * @throws Exception
	 * @return object
	 */
	public function getMensagem($cod_msg){

		try{
			
			if(!is_int($cod_msg)){
				throw new Exception('O código da mensagem deve ser informado para recuperar mensagem');
			}

			$sql = " SELECT psmoid                    AS msg_id,
			                psmretcodigo              AS msg_cod,
							psmretdescricaosascar     AS msg_sascar,
							psmretdescricaoseguradora AS msg_seguradora
					   FROM produto_seguro_mensagens
					  WHERE psmretcodigo = '".trim($cod_msg)."'
						AND psmdt_exclusao 
			                IS NULL  ";

			if (!$result = pg_query($this->connProduto, $sql)) {
				throw new Exception("Falha ao recuperar mensagem");
			}

			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}

		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
	
	/**
	 * Insere mensagem de retorno do Ws caso não exista na tabela
	 * 
	 * @param int $cod_msg
	 * @param string $desc_mensagem
	 * @throws Exception
	 * @return object
	 */
	public function setMensagem($cod_msg, $desc_mensagem){

		try{

			if(!is_int($cod_msg)){
				throw new Exception('O código da mensagem deve ser informado para inserir');
			}
			
			if($desc_mensagem == NULL){
				throw new Exception('A descrição da mensagem deve ser informada');
			}

			$sql = " INSERT INTO produto_seguro_mensagens ( psmretcodigo, 
					                                        psmretdescricaosascar,
					                                        psmretdescricaoseguradora,
					                                        psmdt_cadastro,
					                                        psmusuoid_inclusao )
			      								   VALUES ( ".trim($cod_msg).",
															'".trim($desc_mensagem)."',
			                                                '".trim($desc_mensagem)."',			 
			                                                 NOW(),
			                                                 2750  ) 
			                                                		
									   			  RETURNING psmoid  ";
			
			if(!$result = pg_query($this->connProduto, $sql)){
				throw new Exception('Falha ao inserir mensagem de retorno do WS');
			}
			
			return pg_fetch_result($result, 0, "psmoid");

		}catch (Exception $e){
			echo $e->getMessage();
		}
	}

	
	/**
	 * Recupera o id da seguradora com benefício == SEGURO
	 *
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function getSeguradora(){
	
		try {
	
			$sql = " SELECT emboid
					   FROM empresa_beneficio
			     INNER JOIN empresa_beneficio_tipo ON ebtemboid = emboid
				      WHERE ebtdescricao = 'SEGURO'
					    AND embdt_exclusao IS NULL
					    AND ebtdt_exclusao IS NULL";
	
			if (!$result = pg_query($this->connProduto, $sql)) {
				throw new Exception("Falha ao recuperar seguradora");
			}
	
			if (pg_num_rows($result) > 0) {
				return pg_fetch_all($result);
			}
	
			return false;
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	
	/**
	 * Pesquisa a classe do produto com benefício == SEGURO
	 *
	 * @param int $codClasse
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function getClasseProduto($codClasse, $seguradora){

		try {

			$sql = " SELECT emboid
					   FROM empresa_beneficio
			     INNER JOIN empresa_beneficio_tipo ON ebtemboid = emboid
				 INNER JOIN equipamento_classe_beneficio ON eqcbebtoid = ebtoid
				      WHERE ebtdescricao = 'SEGURO'
				        AND emboid = $seguradora
						AND eqcbeqcoid = $codClasse 
						AND embdt_exclusao IS NULL
						AND embdt_exclusao IS NULL
						AND eqcbdt_exclusao IS NULL ";

			if (!$result = pg_query($this->connProduto, $sql)) {
				throw new Exception("Falha ao pesquisar seguradora pela classe do produto");
			}

			if (pg_num_rows($result) > 0) {
				return pg_fetch_all($result);
			}

			return false;

		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	
	/**
	 * Recupera o id(serial) da linha da tabela produto_seguro_cotacao do número da cotação retornada
	 * do WS
	 *
	 * @param int $num_cotacao
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getCodigoCotacao($num_cotacao,$codigoCorretor){
	
		try {
				
			if(empty($num_cotacao)){
				throw new Exception('O número da cotação deve ser informado para buscar o código');
			}
				
			$sql = " SELECT pscoid AS cod_cotacao
					   FROM produto_seguro_cotacao
					  WHERE pscretcotacao = $num_cotacao
				   ORDER BY pscoid DESC
					  LIMIT 1 ";

			$result = pg_query($this->connProduto, $sql);

			if (!$result) {
				return false;
			}
			else{
				return $result;
			}

			
			


			//return false;
						
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * Recupera o id(serial) da linha da tabela produto_seguro_cotacao do número da cotação retornada
	 * do WS
	 *
	 * @param int $num_cotacao
	 * @throws Exception
	 * @return object|boolean
	 */
	public function getCodigoCotacaoCorretor($num_cotacao,$codigoCorretor){
	
		try {
				
			$sql = " SELECT pscoid AS cod_cotacao
					   FROM produto_seguro_cotacao
					  WHERE pscretcotacao = $num_cotacao AND pscenvrevenda = '{$codigoCorretor}'
				   ORDER BY pscoid DESC
					  LIMIT 1 ";
				
			$result = pg_query($this->connProduto, $sql);

			if (!$result) {
				return false;
			}
			else{
				return $result;
			}



			//return false;
						
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}


	/**
	 * Recupera o idRevenda do corretor
	 *
	 * @param int $idCorretor
	 * @throws Exception
	 * @return object|boolean
	 */
	public function buscaIdRevendaCorretor($idCorretor){

		try {
				

			$sql = "SELECT psccodseg FROM produto_seguro_corretor WHERE pscoid = ".$idCorretor;
			
			$result = pg_query($this->connProduto, $sql);
			if ($result) {
				return $result;
			}


			return false;			
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	/**
	 * Recupera layout para montar o XML de envio de dados para o WS da seguradora informada
	 * 
	 * @param int $seguradora
	 * @param string $nome_servico (nome da função que será chamada no WS)
	 * @param string $tipo_servico  ENTRADDA/SAÍDA
	 * @throws Exception
	 * @return multitype:
	 */
	public function getLayoutXmlEntrada($seguradora, $nome_servico, $tipo_servico){

		try{

			if(empty($seguradora)){
				throw new Exception('A Seguradora deve ser informada para recuperação do layout XML de entrada de dados para gerar proposta');
			}
				
			if(empty($nome_servico)){
				throw new Exception('O nome do serviço para recuperar o XML deve ser informado');
			}
				
			if(empty($tipo_servico)){
				throw new Exception('O tipo serviço para recuperar o XML deve ser informado');
			}
	
			 $sql = " SELECT pssnome AS servico, 
				             LOWER(pssitag_nome) AS tag, 
				             pssitag_sequencia AS sequencia, 
				             pssitipo_servico AS tipo
				        FROM produto_seguro_servicos
				  INNER JOIN produto_seguro_servicos_itens ON pssoid = pssipssoid
				       WHERE pssemboid = $seguradora
				         AND pssnome = '$nome_servico'
				         AND pssitipo_servico = '$tipo_servico'
				         AND pssdt_exclusao IS NULL
				         AND pssidt_exclusao IS NULL
				    ORDER BY pssitag_sequencia";
			 
			 if (!$result = pg_query($this->connProduto, $sql)) {
			 	throw new Exception("Falha ao recupera layout XML de entrada de dados");
			 }

			 if (pg_num_rows($result) > 0) {
			 	return pg_fetch_all($result);
			 }

		}catch (Exception $e){
			echo $e->getMessage();
		}

	}
	
	
	/**
	 * Recupera o corpo do email, o e-mail será montado e enviado 
	 * O tipo de corpo do e-mail será retornado de acordo o parâmetro do campos: se.seedescricao, se.seecabecalho, se.seedescrica
	 * que devem possuir cadastro no bd
	 *
	 * @param string $seetdescricao
	 * @author Márcio Sampaio Ferreira
	 * @return array
	 */
	public function getDadosCorpoEmail($paramLayout){
			
		try{
				
			if(empty($paramLayout)){
				throw new Exception("A descricao para as busca nao pode ser vazia");
			}
	
			//recupera dados do e-mail(assunto, corpo, etc ...)
			$sql= " SELECT sf.seefdescricao         AS funcionalidade
						 , se.seecorpo              AS corpo_email
						 , se.seecabecalho          AS assunto_email
						 , seepadrao
						 , srvlocalizador           AS servidor
					  FROM servico_envio_email se
					  JOIN servico_envio_email_funcionalidade sf ON sf.seefoid = se.seeseefoid
					  JOIN servico_envio_email_titulo st ON st.seetoid = se.seeseetoid
					  JOIN servidor_email ON srvoid = seesrvoid
					 WHERE sf.seefdescricao = 'Produto com Seguro'
					   AND st.seetdescricao = '".trim($paramLayout)."'
					   AND se.seepadrao = 't'
					   AND se.seedt_exclusao IS NULL
					   AND sf.seefdt_exclusao IS NULL ";
				
			if (!$result = pg_query($this->connProduto, $sql)) {
				throw new Exception("Erro ao recuperar dados para envio de e-mails.");
			}
	
			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}
				
			return false;
	
		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
	
	
	/**
	 * Recupera o email de testes
	 *
	 * @author Márcio Sampaio Ferreira <marcioferreira@brq.com>
	 * 14/06/2013
	 *
	 * @return Object
	 */
	public function getEmailTeste(){
	
		try{
	
			$sql = "  SELECT pcsidescricao, pcsioid
		  				FROM
							parametros_configuracoes_sistemas,
							parametros_configuracoes_sistemas_itens
		 				WHERE
							pcsoid = pcsipcsoid
					    AND pcsdt_exclusao is null
						AND pcsidt_exclusao is null
						AND pcsipcsoid = 'PARAMETROSAMBIENTETESTE'
						AND pcsioid = 'EMAIL'
					  LIMIT 1 ";
	
			if (!$result = pg_query($this->connProduto, $sql)) {
				throw new Exception ("Falha ao recuperar email de teste ");
			}
	
			if(count($result) > 0){
				return pg_fetch_object($result);
			}
	
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * inicia transação com o BD
	 */
	public function begin()	{
		$rs = pg_query($this->connProduto, "BEGIN;");
	}
	
	/**
	 * confirma alterações no BD
	 */
	public function commit(){
		$rs = pg_query($this->connProduto, "COMMIT;");
	}
	
	/**
	 * desfaz alterações no BD
	 */
	public function rollback(){
		$rs = pg_query($this->connProduto, "ROLLBACK;");
	}
	

}
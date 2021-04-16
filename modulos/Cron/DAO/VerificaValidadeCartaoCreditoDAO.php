<?php
/**
 * Classe responsável em recuperar dados referentes a cartões de créditos que irão vencer ou vencendo 
 * 
 * @file VerificaValidadeCartaoCreditoDAO.php
 * @author marcioferreira
 * @version 15/05/2013 16:25:19
 * @since 15/05/2013 16:25:19
 * @package SASCAR VerificaValidadeCartaoCreditoDAO.php 
 */

class VerificaValidadeCartaoCreditoDAO{
	
	private $conn;
	
	// Construtor
	public function __construct($conn) {
	
		$this->conn = $conn;
	}
	
	/**
	 * Método para buscar os cartões que irão vencer ou vencendo de acordo a quantidade de dias passados no parâmetro
	 * 
	 * @author Márcio Sampaio Ferreira
	 * @param int $dias
	 * @return object
	 */
	public function getCartoesVencendo($dias){

		try{
			
			if($dias == 'hoje'){
				$dias = 0;
			}
						
			$sql=" SELECT to_char(ccc.cccdt_validade, 'dd-mm-YYYY') AS data_validade,
						  ccc.cccdt_validade - CURRENT_DATE AS dias_vencer,
						  ccc.cccclioid AS id_cliente,
						  UPPER(cl.clinome) AS nome_cliente,
						  cl.cliemail AS email_cliente
					FROM cliente_cobranca_credito ccc
					INNER JOIN clientes cl ON cl.clioid = ccc.cccclioid
					WHERE cccdt_validade IS NOT NULL
					AND (ccc.cccdt_validade - current_date) = $dias
					AND ccc.cccativo IS TRUE
					AND (SELECT COUNT(connumero)
						 FROM contrato
						 WHERE concsioid = 1
						 AND conveioid IS NOT NULL
						 AND condt_exclusao IS NULL
						 AND conclioid = cl.clioid) >= 1 ";

			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Erro ao buscar datas de vencimento de cartoes de credito.');
			}
			
			if (pg_num_rows($result) > 0) {
				return pg_fetch_all($result);
			}
			
		}catch(Exception $e){
			echo $e->getMessage();
			exit;
		}
	}
	
	
	/**
	 * Método para buscar o corpo do email dos cartões que estão vencendo, o e-mail será montado e enviado para o cliente
	 * O tipo de corpo do e-mail será retornado de acordo o parâmetro do campos: se.seedescricao, se.seecabecalho, se.seedescrica
	 * que devem possuir cadastro no bd
	 * 
	 * @author Márcio Sampaio Ferreira
	 * @return array
	 */
	public function getDadosCorpoEmailCartoesVencendo(){
			
		try{
			
			//recupera dados do e-mail(assunto, corpo, etc ...)
			$sql =" SELECT sf.seefdescricao AS funcionalidade,
					       se.seecorpo AS corpo_email,
					       se.seecabecalho AS assunto_email
					FROM servico_envio_email se
					INNER JOIN servico_envio_email_funcionalidade sf ON sf.seefoid = se.seeseefoid
					INNER JOIN servico_envio_email_titulo st ON st.seetoid = se.seeseetoid
					WHERE sf.seefdescricao = 'Cartão de Crédito'
					AND st.seetdescricao = 'Aviso de vencimento'
					AND se.seedescricao = 'Cartões a vencer'
					AND se.seecabecalho = 'Aviso de vencimento - Cartão de Crédito'
					AND se.seedt_exclusao IS NULL
					AND sf.seefdt_exclusao IS NULL
					LIMIT 1 ";
			
			if (!$result = pg_query($this->conn, $sql)) {
				throw new Exception("Erro ao recuperar conteúdo para envio de e-mails.");
			}
			
			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}
			
		}catch (Exception $e){
			echo $e->getMessage();
			exit;
		}
	}
	
	
	/**
	 * Método para buscar o corpo do email dos cartões vencidos, o e-mail será montado e enviado para o cliente
	 * O tipo de corpo do e-mail será retornado de acordo o parâmetro do campos: se.seedescricao, se.seecabecalho, se.seedescrica
	 * que devem possuir cadastro no bd
	 * 
	 * @author Márcio Sampaio Ferreira
	 * @return array
	 */
	public function getDadosCorpoEmailCartoesVencidos(){
			
		try{
				
			//recupera dados do e-mail(assunto, corpo, etc ...)
			$sql =" SELECT sf.seefdescricao AS funcionalidade,
					       se.seecorpo AS corpo_email,
					       se.seecabecalho AS assunto_email
					FROM servico_envio_email se
					INNER JOIN servico_envio_email_funcionalidade sf ON sf.seefoid = se.seeseefoid
					INNER JOIN servico_envio_email_titulo st ON st.seetoid = se.seeseetoid
					WHERE sf.seefdescricao = 'Cartão de Crédito'
					AND st.seetdescricao = 'Aviso de vencimento'
					AND se.seedescricao = 'Cartões vencendo'
					AND se.seecabecalho = 'Aviso de vencimento - Cartão de crédito vencido'
					AND se.seedt_exclusao IS NULL
					AND sf.seefdt_exclusao IS NULL
					LIMIT 1 ";
				
			if (!$result = pg_query($this->conn, $sql)) {
				throw new Exception("Erro ao recuperar conteúdo para envio de e-mails de cartoes vencidos.");
			}
				
			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}
				
		}catch (Exception $e){
			echo $e->getMessage();
			exit;
		}
	}
	
	
	/**
	 * Método para buscar os cartões que foram incluídos no mês de vencimento do cartão
	 * Verifica se a data de vigência do contrato é a data corrente e que não
	 * seja o último dia da data de validade do cartão
	 *
	 * @author Márcio Sampaio Ferreira
	 * @return array
	 */
	public function getCartoesIncluidosMesVecimento(){
			
		try{
			
			$sql=" SELECT to_char(ccc.cccdt_validade, 'dd-mm-YYYY') AS data_validade, 
					       ccc.cccdt_validade - CURRENT_DATE AS dias_vencer, 
					       ccc.cccclioid AS id_cliente, 
					       UPPER(cl.clinome) AS nome_cliente,
					       cl.cliemail AS email_cliente,
					       to_char(ccc.cccdt_inclusao, 'dd-mm-YYYY') as data_inclusao,
					       ccc.cccdt_validade - ccc.cccdt_inclusao::timestamp::date as tempo_inclusao_cartao
					FROM cliente_cobranca_credito ccc
					INNER JOIN clientes cl ON cl.clioid = ccc.cccclioid
					INNER JOIN contrato ct ON ct.conclioid = cl.clioid
					WHERE cccdt_validade IS NOT NULL
					AND (ccc.cccdt_validade - ccc.cccdt_inclusao::timestamp::date) <= 30
					AND ccc.cccativo IS TRUE
					AND (ct.condt_ini_vigencia::timestamp::date = NOW()::timestamp::date)
					AND ct.concsioid = 1
					AND ct.conveioid IS NOT NULL
					AND ct.condt_exclusao IS NULL
					AND ct.condt_ini_vigencia IS NOT NULL
					AND (ccc.cccdt_validade - CURRENT_DATE) > 0 ";

			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Erro ao buscar cartoes incluidos no mes de vencimento.');
			}
			
			if (pg_num_rows($result) > 0) {
				return pg_fetch_all($result);
			}
			
		}catch(Exception $e){
			echo $e->getMessage();
			exit;
		}
	}
	
}
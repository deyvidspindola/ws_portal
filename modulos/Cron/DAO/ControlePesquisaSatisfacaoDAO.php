<?php
/**
 * @file ControlePesquisaSatisfacaoDAO.php
 * @author marcioferreira
 * @version 01/07/2013 10:16:35
 * @since 01/07/2013 10:16:35
 * @package SASCAR ControlePesquisaSatisfacaoDAO.php 
 */

class ControlePesquisaSatisfacaoDAO{
	
	
	private $conn;
	
	// Construtor
	public function __construct($conn) {
	
		$this->conn = $conn;
	}
	
	/**
	 * Recupera os questionários Ativos dos tipos:
	 * Pesquisa Pós Venda, Instalação e Manutenção.
	 * 
	 * @param int $param ##valores possíveis : 8,9,10
	 * @throws Exception
	 * @return object:
	 */
	public function getDadosQuestionario($param = NULL){

		try{
			
			if(!empty($param)){
				
				$sql =" SELECT psvoid, psvtipo
						  FROM posvenda
						 WHERE psvstatus = 'A'
						   AND psvtipo = $param
						 LIMIT 1 ";
					
				if(!$result = pg_query($this->conn, $sql)){
					throw new Exception('Erro ao pesquisar questionarios.');
				}

				if (pg_num_rows($result) > 0) {
					return pg_fetch_object($result);
				}
			}
						
			return false;
			
		}catch(Exception $e){
			echo $e->getMessage();
		}
		
	}
	
	/**
	 * Controla o envio de e-mail retornando o id do controle
	 *  
	 * @param int $clioid           --Código do Cliente ou da Gerenciadora vinculado a visita;
	 * @param int $cod_questionario --Código do Questionário selecionado;
	 * @param int $tipo_pesquisa    --Tipo de Pesquisa = Pós Venda, Instalação e Manutenção ##valores possíveis : 8,9,10
	 * @param int $id_visita        --Id da vista selecionada;
	 *
	 * @throws Exception
	 * @return boolean
	 */
	public function setControleEnvioEmail($clioid, $gerenciadora, $ordoid, $cod_questionario, $tipo_pesquisa, $id_visita, $email_cliente){
			
		try{
			
			if(!empty($clioid)){
				$coluna = 'pcqclioid';
				$valorColuna = $clioid;
			}
				
			if(!empty($gerenciadora)){
				$coluna = 'pcqgeroid';
				$valorColuna = $gerenciadora;
			}
			
			$sql = " INSERT INTO 
			         posvenda_controle_questionario( 
							 $coluna,
							 pcqordoid,
							 pcqpsvoid,
							 pcqpstoid,
							 pcqvpvoid,
							 pcqendereco_email,
							 pcqdt_status,
							 pcqstatus )
		              VALUES(
		                     $valorColuna,
		              		 $ordoid,	
							 $cod_questionario,
			                 $tipo_pesquisa,
			                 $id_visita,
		    				 '$email_cliente',
		    				 NOW(),
			                 '0') RETURNING pcqoid ";
			//error_log("SQL de novo questionario: " . $sql , 1, "denilson.sousa@sascar.com.br");		
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Erro ao inserir dados de controle de envio de e-mail');
				error_log("Erro ao criar questionario: " . $sql , 1, "denilson.sousa@sascar.com.br");
			}
			
			$id_controle = pg_fetch_result($result, 0, "pcqoid");
			
			return $id_controle;
			
		}catch(Exception $e){
			echo $e->getMessage();
			error_log("Erro ao criar questionario: " . $sql . " ----- " . $e->getMessage() , 1, "denilson.sousa@sascar.com.br");
			exit;
		}
	}

		
	/**
	 * Efetua a atualização da tabela após o processo de envio de email
	 * 
	 * @param int $id_controle_questionario
	 * @param int $status
	 * @param string $obs_envio
	 * @throws Exception
	 * @return boolean
	 */
	public function atualizarControleEnvioEmail($id_controle_questionario, $status, $obs_envio){
		
		try{

			$sql =" UPDATE posvenda_controle_questionario
					   SET pcqstatus = $status ,
						   pcqobservacao = '$obs_envio',
						   pcqdt_envio =  ";

			if($status === 1 ){
				$sql .= " NOW() "; //--Data de Envio (data e hora atual caso e-mail enviado com sucesso);
			}else{
				$sql .= " NULL ";
			}
				
			$sql .=" WHERE pcqoid = $id_controle_questionario ";
			
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Erro ao inserir dados de controle de envio de e-mail');
			}
				
			return true;

		}catch(Exception $e){
			echo $e->getMessage();
			exit;
		}
		
	}
	
	/**
	 * Recupera todas as visitas para Cliente ou Gerenciadora que 
	 * foram cadastradas no dia e não estejam excluídas.
	 * 
	 * @throws Exception
	 * @return array:
	 */
	public function pesquisarPosVendaAreaTecnica(){
		
		try{
			$sql = "SELECT vpvoid    AS id_visita,
					       vpvclioid AS clioid,
					       vpvgeroid AS gerenciadora,
					       vpvemail  AS cliemail
					  FROM visita_posvenda
					 WHERE vpvpara IN ('C','G')
					   AND vpvemail is not null
					   AND TO_DATE(TO_CHAR(vpvcadastro,'dd/mm/yyyy'),'dd/mm/yyyy') = TO_DATE(TO_CHAR(NOW(),'dd/mm/yyyy'),'dd/mm/yyyy') 
					   AND vpvexclusao IS NULL
					   AND EXISTS (SELECT 1
					                 FROM visita_posvenda_atendimento
					                WHERE vpavpvoid = vpvoid)
				  ORDER BY 1 ";
			
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Erro ao pesquisar clientes pos venda e area tecnica.');
			}
				
			if (pg_num_rows($result) > 0) {
				return pg_fetch_all($result);
			}
			
			return false;
			
		}catch(Exception $e){

			echo $e->getMessage();
		}
	}
	
	
	/**
	 * Recupera todas as Ordens de Serviços 
	 * concluídas no dia e que possuam pelo menos um serviço de Instalação concluída.
	 * 
	 * @throws Exception
	 * @return multitype:
	 */
	public function pesquisarOrdemServicoInstalacao(){
		
		try{
			
			$sql = "SELECT ordem_servico.ordoid    AS ordem, 
					       ordem_servico.ordclioid AS clioid, 
					       clientes.cliemail       AS cliemail, 
					       CASE 
					          WHEN tipo_proposta.tppoid_supertipo = 12 THEN 'SIGGO'
					          WHEN contrato.conno_tipo IN (844,858) THEN 'VIVO'
					          ELSE 'SASCAR'
					       END AS tipo_layout,
					       TO_CHAR(max(ordem_situacao.orsdt_situacao),'dd/mm/yyyy hh24:mi') AS dt_conclusao
					  FROM ordem_servico 
					  JOIN contrato ON contrato.connumero = ordem_servico.ordconnumero
					  JOIN proposta ON prptermo = contrato.connumero
					  JOIN tipo_proposta ON tppoid = prptppoid
					  JOIN clientes ON clioid = ordclioid
					  JOIN ordem_situacao ON orsordoid = ordoid
					 WHERE ordstatus = '3'
					   AND ordem_situacao.orsstatus = 43
					   AND ordem_situacao.orssituacao ilike 'O.S. Concl%' 
					   AND ordem_situacao.orsdt_situacao >= CURRENT_DATE 
					   AND conno_tipo NOT IN (1069) -- MERCEDES-BENZ VANS CONNECT 
					   AND EXISTS (SELECT 1 
									 FROM ordem_servico_item
									 JOIN os_tipo_item ON otioid = ositotioid
									WHERE ordem_servico_item.ositordoid = ordoid
									  AND os_tipo_item.otiostoid = 1 -- (INSTALACAO)
									  AND os_tipo_item.otidt_exclusao IS NULL
									  AND ordem_servico_item.ositstatus = 'C')
				  GROUP BY ordem_servico.ordoid, clientes.cliemail, tipo_layout ";
			
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Erro ao pesquisar clientes ordem de servico instalacao.');
			}
		
			if (pg_num_rows($result) > 0) {
				return pg_fetch_all($result);
			}
			
			return false;
				
		}catch(Exception $e){
		
			echo $e->getMessage();
		}
		
	}
	
	
	/**
	 * Recupera todas as Ordens de Serviços de manutenção
	 * foram cadastradas no dia e não estejam excluídas.
	 *
	 * 2019-03-18	Removido o MAX(ordem_servico.ordoid) e adicionado no ordem_servico.ordoid no group by, desta forma vai enviar questionario para todas as ordens de manutencao finalizadas no dia.
	 *
	 * @throws Exception
	 * @return array:
	 */
	public function pesquisarOrdemServicoManutencao(){
	
		try{
			
			 $sql =" SELECT ordem_servico.ordoid AS ordem
							, ordem_servico.ordclioid AS clioid
							, clientes.cliemail       AS cliemail
							, CASE WHEN tipo_proposta.tppoid_supertipo = 12 THEN 'SIGGO' ELSE 'SASCAR' END tipo_proposta
							, CASE WHEN contrato.conno_tipo IN (844, 858) THEN 'VIVO' ELSE 'SASCAR' END tipo_contrato
							, TO_CHAR(max(ordem_situacao.orsdt_situacao),'dd/mm/yyyy hh24:mi') AS dt_conclusao
					   FROM ordem_servico
					   JOIN contrato ON contrato.connumero = ordem_servico.ordconnumero
					   JOIN proposta ON prptermo = contrato.connumero
					   JOIN tipo_proposta ON tppoid = prptppoid
					   JOIN clientes ON clioid = ordclioid
					   JOIN ordem_situacao ON orsordoid = ordoid
					  WHERE ordstatus = '3'
					    AND ordem_situacao.orsstatus = 43
					    AND ordem_situacao.orssituacao ilike 'O.S. Concl%' 
					    AND ordem_situacao.orsdt_situacao >= CURRENT_DATE 
						AND conno_tipo NOT IN (1069) -- MERCEDES-BENZ VANS CONNECT 
		 		        AND EXISTS (SELECT 1
					                  FROM ordem_servico_item
					                  JOIN os_tipo_item ON otioid = ositotioid
					                 WHERE ordem_servico_item.ositordoid = ordoid
					                   AND os_tipo_item.otiostoid IN (2, 3, 4, 9) -- (REINSTALAÇÃO,RETIRADA,ASSISTÊNCIA,REINSTALAÇÃO NÃO COBRAR)
					                   AND os_tipo_item.otidt_exclusao IS NULL
					                   AND ordem_servico_item.ositstatus = 'C')
				       GROUP BY ordem_servico.ordclioid, clientes.cliemail, orditloid, tipo_proposta, tipo_contrato, ordem_servico.ordoid
				       ORDER BY ordem_servico.ordclioid ";
				
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Erro ao pesquisar clientes ordem de servico manutencao.');
			}
	
			if (pg_num_rows($result) > 0) {
				return pg_fetch_all($result);
			}
				
			return false;
				
		}catch(Exception $e){
	
			echo $e->getMessage();
		}
	}
	
	/**
	 * Seleciona as pesquisas do tipo Instalação, que foram respondidos no dia, que possuam uma pergunta do tipo Avaliação,
	 * Tipo de ocorrência Reenvio de email e que a resposta da pergunta for SIM. Reenviar a pesquisa para o e-mail informado
	 * na resposta.
	 * 
	 * @throws Exception
	 * @return multitype:|boolean
	 */
	public function selecionarReenvioPesquisas(){

		try{
		
			$sql =" SELECT pcqclioid AS clioid, 
					       pcqpsvoid AS id_questio, 
					       pcqordoid AS ordem, 
					       pcqpstoid AS tipo_pesquisa, 
					       SUBSTRING(pqridesc_resposta FROM 3 FOR 150) AS cliemail,
					       '1' AS reenvio,
					       CASE WHEN proposta.prptppoid = 12 THEN 'SIGGO'
					            WHEN contrato.conno_tipo IN (844,858) THEN 'VIVO'
					            ELSE 'SASCAR'
					       END AS tipo_layout
					  FROM posvenda_controle_questionario
					  JOIN ordem_servico on ordem_servico.ordoid = posvenda_controle_questionario.pcqordoid 
					  JOIN contrato on contrato.connumero = ordem_servico.ordconnumero
					  JOIN proposta on prptermo = contrato.connumero
					  JOIN posvenda_tipo ON pstoid = pcqpstoid
					  JOIN posvenda_quest_resposta ON pqrpcqoid = pcqoid
					  JOIN posvenda_quest_resposta_item ON pqripqroid = pqroid
					  JOIN posvenda_questionario_item ON pqioid = pqripqioid  
					  JOIN representante_historico_tipo ON rhtoid = pqirhtoid
					 WHERE pqitipo_item = 'A'
					   AND rhtdescricao = 'Reenvio de E-mail'
					   AND SUBSTRING(pqridesc_resposta FROM 1 FOR 1) = 'N'
					   AND pstoid IN (8, 10)
					   AND pcqstatus = '2'
					   AND TO_DATE(TO_CHAR(pcqdt_status,'dd/mm/yyyy'),'dd/mm/yyyy') = TO_DATE(TO_CHAR(NOW(),'dd/mm/yyyy'),'dd/mm/yyyy')  ";
		
			if(!$result = pg_query($this->conn, $sql)){
				throw new Exception('Erro ao recuperar dados para reenvio de pesquisas.');
			}
		
			if (pg_num_rows($result) > 0) {
				return pg_fetch_all($result);
			}
		
			return false;
		
		}catch(Exception $e){
		
			echo $e->getMessage();
		}
		
	}
	
	
	/**
	 * Filtra todas as pesquisas que estão sem resposta (status = 0 ou 1) a 30 dias (a partir da data de envio 
	 * do e-mail) e atualiza para  Status = 3 (Concluída automaticamente (Expirada)). 
	 * Atualiza também a data do status para a data atual.
	 * 
	 * Expiracao Alterado para 30 dias da data do envio por solicitacao do Maicon Mota 18-03-2019 
	 *
	 * @throws Exception
	 * @return boolean
	 */
	public function atualizarPesquisas(){
		
		try{
		
			$sql_pesquisas = " UPDATE posvenda_controle_questionario
								  SET pcqstatus = 3, pcqdt_status = NOW()
								WHERE pcqstatus IN ('0','1')
								  AND (pcqdt_cadastro::timestamp::date - CURRENT_DATE) <= -30  ";
		
			if(!$result = pg_query($this->conn, $sql_pesquisas)){
				throw new Exception('Erro ao atualizar pesquisas.');
			}
		
			return true;
		
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Método para buscar o corpo do email, o e-mail será montado e enviado para o cliente
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
					 WHERE sf.seefdescricao = 'Pesquisa de Satisfacao'
					   AND st.seetdescricao = 'Pesquisa de Satisfacao - ".trim($paramLayout)."'
					   AND se.seepadrao = 't'
					   AND se.seedt_exclusao IS NULL
					   AND sf.seefdt_exclusao IS NULL ";
			
			if (!$result = pg_query($this->conn, $sql)) {
				throw new Exception("Erro ao recuperar dados para envio de e-mails.");
			}
	
			if (pg_num_rows($result) > 0) {
				return pg_fetch_object($result);
			}
			
			return false;
	
		}catch (Exception $e){
			echo $e->getMessage();
			exit;
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
	
			$sql = "SELECT pcsidescricao, pcsioid
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
	
			if (!$result = pg_query($this->conn, $sql)) {
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

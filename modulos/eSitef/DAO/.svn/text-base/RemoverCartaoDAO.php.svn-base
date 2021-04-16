<?php
/**
 * @file RemoverCartaoDAO.php
 * @author marcioferreira
 * @version 27/05/2013 17:27:12
 * @since 27/05/2013 17:27:12
 * @package SASCAR RemoverCartaoDAO.php 
 */

class RemoverCartaoDAO{
	
	private $conn;

	// Construtor
	public function __construct($conn) {
	
		$this->conn = $conn;
	}
	
	/**
	 * Recupera dados do cartão que será removido pelo id do cliente,
	 * retornando o hash do cartão (ccchash) e o id da transação (ctcoid),
	 * 
	 */
	public function getDadosCartao($clioid){
		
		try{
			$sql =" SELECT ccchash, ctcoid, cccclioid				
					  FROM cliente_cobranca_credito 
					 INNER JOIN controle_transacao_cartao ON ctcclioid = cccclioid
					 WHERE cccativo IS TRUE
					   AND ctcsucesso IS TRUE
					   AND ctctipotransacao = 'C' 
			 	       AND cccclioid = $clioid ";
	
	       $sql .=" ORDER BY ctcoid DESC  LIMIT 1 ";
			
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao recuperar hash do cartão.</b>');
			}
			
			return pg_fetch_object($rs);
			
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Recupera o id da transação corrente do cliente iniciada no beginRemoveStoreCard 
	 * 
	 * @param int $clioid
	 * @throws Exception
	 * @return object
	 */
	public function getIdTransacaoRemocao($clioid){
		
		try{
			
			if(empty($clioid)){
				throw new Exception('Id do cliente não pode ser vazio.');
			}
			
			$sql =" SELECT ctcoid				
				      FROM cliente_cobranca_credito 
				INNER JOIN controle_transacao_cartao ON ctcclioid = cccclioid
				     WHERE cccclioid = $clioid
				       AND cccativo IS TRUE
				       AND ctctipotransacao = 'C' 
				       AND ctccancelamento_cartao IS TRUE 
				       AND ctcdt_inclusao::DATE = NOW()::DATE 
					 ORDER BY ctcoid DESC  
                     LIMIT 1 ";
			
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao recuperar id da transação de remoção.</b>');
			}
				
			return pg_fetch_object($rs);
			
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Insere dados do POST enviados pela SE logo após invocar o método beginRemoveStoreCard
	 * os das são recebidos no arquivo recuperaTransacao.php
	 * 
	 * @param object $dadosPost
	 * @throws Exception
	 * @return boolean
	 */
	public function inserirDadosPostRemocaoCartao($dadosPost, $statusTransacao){
		
		try {
			
		 $sql = "INSERT INTO controle_transacao_cartao
							 ( 
							   ctcdt_inclusao,
							   ctctipotransacao,
							   ctcsucesso,
							   ctccancelamento_cartao,
							   ctcclioid,
							   ctcstatus,
							   ctcnita,
							   ctcnsua,
							   ctcnsu, 
							   ctcautorizadora,
							   ctcmotivo
							   )
							   
						VALUES ( 
								NOW(),
								'P',
								'$statusTransacao',
								't',
								$dadosPost->cliente,
								'$dadosPost->status',
								'$dadosPost->nita',
								'$dadosPost->nsua',
								$dadosPost->nsu, 
								$dadosPost->autorizadora,
								'Dados retornados da SE' 
								) ";
		 
			if (!pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao inserir dados do POST para remoção do cartao de crédito.</b>');
			}

			return true;					
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Recupera informações recebidas do POST para enviar para o método doRemoveStoreCard 
	 * confirmando a remoção do cartão na SE 
	 * 
	 * @param string $status //valores possíveis 'NOV','CON','EXP'
	 * @param int $ctcnsu  //id da transação enviada no momento de armazanar o cartão, na remoção, o nome ctcnsu
	 * @param int $clioid
	 * @throws Exception
	 * @return object
	 */
	
	public function getDadosConfirmacaoRemocaoCartao($clioid, $ctcnsu, $status = null){
		
		try{
				
			if(empty($clioid)){
				throw new Exception('Necessário Id do cliente para buscar dados.');
			}
				
			$sql =" SELECT ctcoid          AS id_transacao,
						   ccchash         AS hash ,	
						   ctcclioid       AS cliente, 
					       ctcnita         AS nita,
					       ctcnsua         AS nsua,
						   ctcnsu          AS nsu, 
					       ctcstatus       AS status, 
					       ctcautorizadora AS autorizadora
				      FROM cliente_cobranca_credito
				INNER JOIN controle_transacao_cartao ON ctcclioid = cccclioid
				     WHERE ctcsucesso IS TRUE
				       AND ctctipotransacao = 'P'
				       AND ctccancelamento_cartao IS TRUE
				       AND ctcnita IS NOT NULL
				       AND ccchash IS NOT NULL
				       AND cccativo IS TRUE
				       AND ctcdt_inclusao::DATE = NOW()::DATE
				       AND ctcclioid = $clioid 
					   AND ctcnsu    = $ctcnsu ";
			
		   if(!empty($status)){
		   	 $sql .= " AND ctcstatus IN ('$status')";
		   }
			
			 $sql.=" ORDER BY ctcoid DESC
				     LIMIT 1 ";
			
			if (!$rs = pg_query($this->conn, $sql)) {
			   throw new Exception('ERRO: <b>Falha ao recuperar dados para enviar confirmação de remoção do cartão de crédito.</b>');
		    }
		
			return pg_fetch_object($rs);
		
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Para toda transação de inclusão ou remoção de cartão de crédito,
	 * inserir um registro na tabela controle_transacao_cartao,
	 * onde o campo ctctipotransacao (tipo da transação realizada)  será igual a "C";
	 *
	 * @param integer $clioid - Id do Cliente
	 * 
	 */
	public function incluirTransacaoCartao($clioid){
			
		try {
			
			if(empty($clioid)){
				throw new Exception('Necessário informar o id do cliente para gravar log de transação.');
			}
	
			$sql = "INSERT INTO controle_transacao_cartao
								(ctcclioid,
								ctcdt_inclusao,
								ctctipotransacao,
								ctcsucesso,
								ctccancelamento_cartao,
								ctcmotivo )
								VALUES ($clioid,
										NOW(),
										'C',
										'f',
										't',
										'Remoção de Cartão' )
			 
							RETURNING ctcoid ";
			
			if (!$result = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao inserir controle de transação para remoção do cartão de crédito.</b>');
			}

			$id_transacao = pg_fetch_result($result, 0, "ctcoid");

			return $id_transacao;

		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Atualiza informações das transações durante o processo de remoção de um cartão de crédito
	 * 
	 * @param int $transacaoID
	 * @param string $statusTransacao
	 * @param object $dadosTransacao
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	public function atualizarTransacaoCartao($transacaoID, $statusTransacao, $dadosTransacao, $motivo = null){

		try {

			if(empty($transacaoID)){
				throw new Exception('O Id da transação deve ser informado.');
			}
			
			if(!is_object($dadosTransacao)){
				throw new Exception('Dados da transação inválidos.');
			}
			
			if(empty($dadosTransacao->cliente)){
				throw new Exception('O Id do cliente deve ser informado.');
			}
			
			$sql = "UPDATE controle_transacao_cartao
					   SET ctcdt_inclusao = NOW(),
						   ctcsucesso               = '$statusTransacao' 
			               , ctccancelamento_cartao = 't'";
			
			if(!empty($dadosTransacao->status)){
				$sql .=", ctcstatus       = '$dadosTransacao->status' ";
			}
			
			if(!empty($dadosTransacao->nita)){
				$sql .=", ctcnita         = '$dadosTransacao->nita'";
			}			

			if(!empty($dadosTransacao->nsua)){
				$sql .=", ctcnsua         = '$dadosTransacao->nsua' ";
			}
				
			if(!empty($dadosTransacao->nsu)){
				$sql .= ", ctcnsu         =  $dadosTransacao->nsu ";
			}

			if(!empty($dadosTransacao->autorizadora)){
				$sql .= " , ctcautorizadora = $dadosTransacao->autorizadora ";
			}
			
			if(!empty($motivo)){
				$sql .= " , ctcmotivo = '$motivo' ";
			}
				
			$sql .= " WHERE ctcoid = $transacaoID
						AND ctcclioid = $dadosTransacao->cliente ";

			if (!pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao atualizar o controle da transação de remoção do cartao de crédito.</b>');
			}

			return true;
			
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
}

<?php
/**
 * Classe para recuperar e persistir dados do cliente
 * 
 * @file PrnClienteDAO.php
 * @author marcioferreira
 * @version 06/03/2013 16:56:43
 * @since 06/03/2013 16:56:43
 * @package SASCAR PrnClienteDAO.php 
 */


class PrnClienteDAO{

	private $conn;

	/**
	 * Construtor
	 *
	 * @autor Márcio Sampaio Ferreira
	 * @email marcioferreira@brq.com
	 */
	public function __construct($conn) {

		$this->conn = $conn;
	}
	
	/**
	 * Atualiza dados do cliente
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function atualizarCliente($id_cliente, $campos) {

		try{
			$sql =" UPDATE clientes
					SET
					$campos , clidt_alteracao = now()
					WHERE clioid = " . $id_cliente ;
	
			if (!pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao atualizar dados do cliente.</b>');
			}
				
			return true;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Insere historico do cliente
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function inserirHistoricoCliente($dadosHistorico, $dadosConfirma) {
		
		try{
			$sql =" SELECT cliente_historico_i(
						{$dadosConfirma->id_cliente},
						{$dadosConfirma->id_usuario},
						'{$dadosHistorico->texto_alteracao}',
						'{$dadosHistorico->tipo}',
						'{$dadosConfirma->protocolo}',
						{$dadosHistorico->id_atendimento} 
					); ";
	
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao atualizar histórico do cliente.</b>');
			}
			
			return true;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/**
	 * Busca o email do cliente
	 * Se o campo cliemail estiver vazio
	 * Pega o campo cliemail_nfe
	 * Se ambos estiverem vazios retorna false
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function getEmailCliente($id_cliente) {

		try{
			$sql = "SELECT
					CASE WHEN cliemail IS NOT NULL THEN cliemail
					     WHEN cliemail_nfe IS NOT NULL THEN	cliemail_nfe
					     ELSE
					     ''
					     END as email_cliente
					FROM clientes
					WHERE clioid = $id_cliente ";
	
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao recuperar e-mail do cliente.</b>');
			}
	
			return pg_fetch_object($rs);
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Pesquisa os clientes de acordo com os parametros informados
	 * @return	Array com os itens filtrados pela pesquisa
	 * */
	public function getClientes($clinome,$clitipo, $clioid, $clino_documento ) {

		try{
			$clino_documento = preg_replace('/[^\d]/', '', $clino_documento);
	
			if (!empty($clinome)) {
				$where = " AND clinome ILIKE '%$clinome%' ";
			}
	
			if (!empty($clino_documento)) {
				if ($clitipo == "F") {
					$where .= " AND clino_cpf = $clino_documento ";
				} elseif ($clitipo == "J") {
					$where .= " AND clino_cgc = $clino_documento ";
				}
			}
	
			if (!empty($clitipo)) {
				$where .= " AND clitipo = '$clitipo' ";
			}
	
			if(!empty($clioid) && is_numeric($clioid)){
				$where .= " AND clioid = '$clioid' ";
			}
	
			$sql = " SELECT c.clioid,
							c.clinome,
							c.clitipo,
							c.clino_cpf,
					        c.clino_cgc,
							CASE WHEN c.clitipo = 'F' THEN c.clino_cpf
							ELSE c.clino_cgc
							END AS clino_documento,
							fc.forcnome,
							COALESCE(ccc.cccsufixo,'') AS cccresufixo,
							bannome,
							clicagencia,
							clicconta,
							forcdebito_conta
						FROM clientes c
						JOIN cliente_cobranca cc ON c.clioid = cc.clicclioid
						JOIN forma_cobranca fc ON cc.clicformacobranca = fc.forcoid
						LEFT JOIN cliente_cobranca_credito ccc ON c.clioid = ccc.cccclioid
						LEFT JOIN banco ON bancodigo = forccfbbanco
						WHERE c.clidt_exclusao IS NULL 
						AND clicexclusao IS NULL
						AND ccc.cccativo = TRUE
							$where
	
						UNION SELECT c.clioid,
								     c.clinome,
								     c.clitipo,
								     c.clino_cpf,
					    			 c.clino_cgc,
									 CASE WHEN c.clitipo = 'F' THEN c.clino_cpf
									 ELSE c.clino_cgc
									 END AS clino_documento,
									 fc.forcnome,
							         '' AS cccresufixo,
									 bannome,
							         clicagencia,
							         clicconta,
							         forcdebito_conta
							   FROM clientes c
							   JOIN cliente_cobranca cc ON c.clioid = cc.clicclioid
							   JOIN forma_cobranca fc ON cc.clicformacobranca = fc.forcoid
							   LEFT JOIN banco ON bancodigo = forccfbbanco
							   WHERE c.clidt_exclusao IS NULL
							   AND clicexclusao IS NULL
							   AND fc.forccobranca_cartao_credito IS FALSE
							   $where
							   ORDER BY clinome ASC ";
			
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao recuperar dados de clientes.</b>');
			}
	
			$resultado = array();
			$resultado['clientes'] = array();
	
			$cont = 0;
			while ($rcliente = pg_fetch_assoc($rs)) {
	
				$resultado['clientes'][$cont]['clioid']          = utf8_encode($rcliente['clioid']);
				$resultado['clientes'][$cont]['clinome']         = utf8_encode($rcliente['clinome']);
				$resultado['clientes'][$cont]['clitipo']         = empty($rcliente['clitipo'])         ? '' : $rcliente['clitipo'];
			    $resultado['clientes'][$cont]['clino_documento'] = empty($rcliente['clino_documento']) ? '' : $rcliente['clino_documento'];
				$resultado['clientes'][$cont]['clitipo']         = empty($rcliente['clitipo']) 	       ? '' : $rcliente['clitipo'];
				$resultado['clientes'][$cont]['forcnome']        = empty($rcliente['forcnome'])        ? '' : utf8_encode($rcliente['forcnome']);
				$resultado['clientes'][$cont]['cccresufixo']     = empty($rcliente['cccresufixo']) 	   ? '' : utf8_encode($rcliente['cccresufixo']);
				$resultado['clientes'][$cont]['bannome']     	 = empty($rcliente['bannome']) 	   	   ? '' : utf8_encode($rcliente['bannome']);
				$resultado['clientes'][$cont]['clicagencia']     = empty($rcliente['clicagencia']) 	   ? '' : utf8_encode($rcliente['clicagencia']);
				$resultado['clientes'][$cont]['clicconta']     	 = empty($rcliente['clicconta']) 	   ? '' : utf8_encode($rcliente['clicconta']);
	
				$cont++;
			}
	
			$resultado['total_registros'] = utf8_encode('A pesquisa retornou ' . pg_num_rows($rs) . ' registro(s).');
	
			return $resultado;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Retorna dados do cliente ao clicar sobre no nome do mesmo na lista de pesquisa
	 * 
	 */
	public function getDadosCliente($clioid) {
		
		try{

			if(empty($clioid) && !is_numeric($clioid)){
				throw new Exception('ERRO: <b>Falha ao recuperar dados do cliente informado.</b>');
			}

			$sql = " SELECT
	                    clioid,
	                    clinome,
	                    clitipo,
					    clino_cpf,
					    clino_cgc,
	                    CASE WHEN clitipo = 'F' THEN
	                        clino_cpf
	                    ELSE
	                        clino_cgc
	                    END AS clino_documento,
	                    forcoid,
						cdvoid,
	                    clidia_vcto,
	                    bancodigo,
	                    clicagencia,
	                    clicconta,
	                    cliemail,
						endemail,
	                    cliemail_nfe,
	                    endddd,
	                    endfone,
	                    endno_cep,
	                    endcep,
	                    CASE
	                        WHEN endpaisoid is not null THEN endpaisoid
	                        ELSE 1
	                    END AS endpaisoid,
		
			    		CASE
	                        WHEN endestoid is not null THEN endestoid
	                        ELSE (SELECT estoid FROM estado WHERE estuf = enduf)
	                    END AS endestoid,
		   			    enduf,
	                    endcidade,
	                    endbairro,
	                    endlogradouro,
	                    endno_numero,
	                    endcomplemento,
	                    forcdebito_conta,
						clicdias_prazo, 
						clicdias_uteis,
						clictipo,
						clicdia_mes,
						clicdia_semana,
						clictitular_conta,
						clifaturamento,
						clifat_locacao
	                FROM
	                    clientes
	                    LEFT JOIN endereco ON endoid = cliend_cobr
	                    LEFT JOIN cliente_cobranca ON clicclioid = clioid
	                    LEFT JOIN forma_cobranca ON forcoid  = clicformacobranca
	                    LEFT JOIN banco ON bancodigo = forccfbbanco
					    LEFT JOIN cliente_dia_vcto ON cdvdia = clidia_vcto
	                WHERE
	                    clicexclusao IS NULL AND
	                    clioid = " . $clioid . "
	                ORDER BY
	                    clicoid DESC
	                LIMIT 1  ";
		
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao recuperar dados do cliente informado.</b>');
			}
		
			$resultado = array();
		
			$rcliente = pg_fetch_assoc($rs);
		
			$resultado['clioid'] = utf8_encode($rcliente['clioid']);
			$resultado['clinome'] = utf8_encode($rcliente['clinome']);
			$resultado['clitipo'] = $rcliente['clitipo'];
			if($resultado['clitipo'] == 'F'){
				$resultado['clino_documento'] = $this->formata_cgc_cpf($rcliente['clino_cpf']);
			
			}else if($resultado['clitipo'] == 'J'){
				$resultado['clino_documento'] = $this->formata_cgc_cpf($rcliente['clino_cgc']);
			}
			$resultado['forcoid'] = $rcliente['forcoid'];
			$resultado['cdvoid'] = $rcliente['cdvoid'];
			$resultado['clidia_vcto'] = $rcliente['clidia_vcto'];
			$resultado['bancodigo'] = ($rcliente['forcdebito_conta'] == 'f') ? '' : $rcliente['bancodigo'];
			$resultado['clicagencia'] = $rcliente['clicagencia'];
			$resultado['clicconta'] = $rcliente['clicconta'];
			$resultado['cliemail'] = utf8_encode($rcliente['cliemail']);
			$resultado['endemail'] = utf8_encode($rcliente['endemail']);
			$resultado['cliemail_nfe'] = utf8_encode($rcliente['cliemail_nfe']);
			$resultado['endddd'] = $rcliente['endddd'];
			$resultado['endfone'] = $rcliente['endfone'];
			$resultado['endno_cep'] = $rcliente['endno_cep'];			
			$resultado['endcep'] = $rcliente['endno_cep'];
			$resultado['endpaisoid'] = $rcliente['endpaisoid'];
			$resultado['endestoid'] = $rcliente['endestoid'];
			$resultado['enduf'] = $rcliente['enduf'];
			$resultado['endcidade'] = utf8_encode($rcliente['endcidade']);
			$resultado['endbairro'] = utf8_encode($rcliente['endbairro']);
			$resultado['endlogradouro'] = utf8_encode($rcliente['endlogradouro']);
			$resultado['endno_numero'] = $rcliente['endno_numero'];
			$resultado['endcomplemento'] = utf8_encode($rcliente['endcomplemento']);
			$resultado['forcdebito_conta'] = $rcliente['forcdebito_conta'];
			$resultado['clicdias_uteis'] = $rcliente['clicdias_uteis'];
			$resultado['clicdias_prazo'] = $rcliente['clicdias_prazo'];
			$resultado['clictipo'] = $rcliente['clictipo'];
			$resultado['clictitular_conta'] = $rcliente['clictitular_conta'];
			$resultado['clicdia_mes'] = $rcliente['clicdia_mes'];
			$resultado['clicdia_semana'] = $rcliente['clicdia_semana'];
			$resultado['clifaturamento'] = $rcliente['clifaturamento'];
			$resultado['clifat_locacao'] = $rcliente['clifat_locacao'];
			
			
			return $resultado;
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Helper para formatar cnpj ou cpf
	 * 
	 * */
	public function formata_cgc_cpf($numero){
		if(strlen($numero)<=11){
			$buf=@str_repeat("0",11-strlen($numero)).$numero;
			$buf=substr($buf,0,3).".".substr($buf,3,3).".".substr($buf,6,3)."-".substr($buf,9,2);
		}else{
			$buf=@str_repeat("0",14-strlen($numero)).$numero;
			$buf=substr($buf,0,2).".".substr($buf,2,3).".".substr($buf,5,3)."/".substr($buf,8,4)."-".substr($buf,12,2);
		}
		return $buf;
	}
	

	/**
	 * Verifica se existe algum contrato ativo do cliente informado
	 *
	 * @autor Renato Teixeira Bueno
	 * @email renato.bueno@meta.com.br
	 */
	public function contratoAtivoCliente($id_cliente) {
	
		try{
			$sql = "SELECT connumero
					FROM contrato
					WHERE concsioid = 1
					AND	condt_exclusao IS NULL
					AND	conclioid = $id_cliente ";
				
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao recuperar contratos ativos do cliente informado.</b>');
			}
		
			return (pg_num_rows($rs) > 0) ? true : false;
			
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
}

//fim arquivos
?>
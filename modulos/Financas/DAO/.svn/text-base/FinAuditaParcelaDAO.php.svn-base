<?php

/**
 * Classe FinAuditaParcelaDAO.
 * Camada de modelagem de dados.
 *
 * @package  Financas
 * @author   Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
 *
 */
class FinAuditaParcelaDAO {

	/** Conexão com o banco de dados */
	private $conn;

	/** Usuario logado */
	private $usarioLogado;

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	public function __construct($conn) {

		//Seta a conexao na classe
        $this->conn = $conn;
        $this->usarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO
        if(empty($this->usarioLogado)) {
            $this->usarioLogado = 2750;
        }
	}

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisar(stdClass $parametros){

		$retorno = array();

		$sql = "SELECT 
					consoid,
					nfino_numero, 
					nfioid,
					nficonoid,
					nfldt_cancelamento,
					conssituacao,
					consiexclusao,
					consinstalacao,
					trim(nflserie) AS nflserie,
					nfitipo,
					nfiparcela,
					cpvparcela,
					CASE
						WHEN nfidt_referencia < consinstalacao THEN
							'<span class=\"error\">A data de referência da nota ('|| nfidt_referencia ||') é menor do que a data de instalação do serviço ('|| consinstalacao ||')</span>'
						ELSE
							nfidt_referencia::text
					END AS nfidt_referencia,
					obrobrigacao,
					eqcdescricao
				FROM 
					nota_fiscal
				INNER JOIN
		            nota_fiscal_item ON nfino_numero = nflno_numero AND nflserie = nfiserie
	            INNER JOIN
	            	obrigacao_financeira ON obroid = nfiobroid
		        LEFT JOIN
		            contrato_servico ON consoid = nficonsoid
		        INNER JOIN
		            contrato ON connumero = nficonoid
	            LEFT JOIN
	            	equipamento_classe ON eqcoid = coneqcoid
		        INNER JOIN 
		            contrato_pagamento ON cpagconoid = connumero
		        INNER JOIN
		            cond_pgto_venda ON cpvoid = cpagcpvoid		        
				WHERE 
					1 = 1";

        if ( isset($parametros->nfino_numero) && trim($parametros->nfino_numero) != '' ) {

            $sql .= " AND
                        nfino_numero = " . intval( $parametros->nfino_numero ) . "";
            
        }

        if ( isset($parametros->nficonoid) && trim($parametros->nficonoid) != '' ) {

            $sql .= " AND
                        nficonoid = " . intval( $parametros->nficonoid ) . "";
            
        }

        $sql .= " ORDER BY nficonoid, nfiparcela";

        /*echo '<pre>';
        echo $sql;
        echo '</pre>';*/
        
		$rs = pg_query($this->conn, $sql);

		while($registro = pg_fetch_object($rs)){

			$conssituacao = '';
			$consiexclusao = '';
			$consinstalacao = '';
			
			if(!is_null($registro->consoid)) {
				$conssituacao = is_null($registro->conssituacao) ? '<span class="error">Situação do serviço não pode ser nula.</span>' : ($registro->conssituacao != 'L' ? '<span class="error">' .$registro->conssituacao . '</span>' : $registro->conssituacao);
				$consiexclusao = !is_null($registro->consiexclusao) ? '<span class="error">' . date('d/m/Y H:i:s', strtotime($registro->consiexclusao)) . '</span>' : '';
				$consinstalacao = is_null($registro->consinstalacao) ? '<span class="error">Data de instalação do serviço não pode ser nula.</span>' : date('d/m/Y H:i:s', strtotime($registro->consinstalacao));
			}

			$retorno[] = array(
					'nfioid' => $registro->nfioid,
					'nfino_numero' => $registro->nfino_numero,  
					'nficonoid' => $registro->nficonoid, 
					'obrobrigacao' => $registro->obrobrigacao, 
					'nfldt_cancelamento' => !is_null($registro->nfldt_cancelamento) ? '<span class="error">' . date('d/m/Y H:i:s', strtotime($registro->nfldt_cancelamento)) . '</span>' : '',
					'conssituacao' => $conssituacao,
					'consiexclusao' => $consiexclusao,
					'consinstalacao' => $consinstalacao,
					'nflserie' => $registro->nflserie != 'A' ? '<span class="error">' .$registro->nflserie . '</span>' : $registro->nflserie,
					'nfitipo'  => $registro->nfitipo != 'L' && $registro->nfitipo != 'C' ? '<span class="error">' .$registro->nfitipo . '</span>' : $registro->nfitipo,
					'nfiparcela' => $registro->nfiparcela,
					'cpvparcela' => $registro->cpvparcela,
					'eqcdescricao' => $registro->eqcdescricao,
					'nfidt_referencia' => date('d/m/Y', strtotime($registro->nfidt_referencia))
				);


		}

		return $retorno;
	}

	/** Abre a transação */
	public function begin(){
		pg_query($this->conn, 'BEGIN');
	}

	/** Finaliza um transação */
	public function commit(){
		pg_query($this->conn, 'COMMIT');
	}

	/** Aborta uma transação */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK');
	}

	/** Submete uma query a execucao do SGBD */
	private function executarQuery($query) {

        if(!$rs = pg_query($query)) {
            throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $rs;
    }
}
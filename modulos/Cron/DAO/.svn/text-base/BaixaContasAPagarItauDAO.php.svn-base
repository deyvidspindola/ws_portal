<?php

/**
 * Classe responsável pela persistência de dados.
 *
 * @author Marcello Borrmann <marcello.b.ext@sascar.com.br>
 * @since 13/05/2016
 * @category Class
 * @package BaixaContasAPagarItauDAO
 */

class BaixaContasAPagarItauDAO { 
	
    private $conn;
	
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function __get($var) {
        return $this->$var;
    }

    /**
     * Abre a transação
     */
    public function begin() {
        pg_query($this->conn, 'BEGIN');
    }

    /**
     * Finaliza um transação
     */
    public function commit() {
        pg_query($this->conn, 'COMMIT');
    }

    /**
     * Aborta uma transação
     */
    public function rollback() {
        pg_query($this->conn, 'ROLLBACK');
    }
	
    /**
     * Método para buscar dados parametrizados  
	 * (parametros_configuracoes_sistemas).
     *
     * @param codigoParametro, codigoItemParametro
     * @return object
     */
    public function buscaParametros($parametros){
		
		$sql = "
			SELECT 
				pcsidescricao
			FROM 
				parametros_configuracoes_sistemas_itens 
			WHERE 
				pcsipcsoid = '".$parametros->codigoParametro."' 
				AND pcsioid = '".$parametros->codigoItemParametro."';  
		";
		
		//echo $sql."</br>";
		//exit;
		if (!$rs = pg_query($this->conn,$sql)){
			throw new ErrorException("DAO->Erro ao buscar dados parametrizados.");
		}
		
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		
		return $retorno;
    	
    }

    /**
     * Método para buscar código do banco pagador
     * (apagar_banco).
     *
     * @param apgoid, agencia, contaCorrente
     * @return object
     */
    public function buscaBancoPagador($dados){
    	$retorno = array();
    
    	$sql = "SELECT 
    				abbancodigo 
    			FROM 
    				apagar_banco 
    			WHERE 
    				abtecoid = (SELECT apgtecoid FROM apagar WHERE apgoid = ".$dados->apgoid.") 
    				AND abagencia = '".$dados->agencia."'
    				AND abconta_corrente ILIKE '%".$dados->contaCorrente."';
	 	";
    	
    	//echo $sql."</br>";
    	//exit;
		if (!$rs = pg_query($this->conn,$sql)){
			throw new ErrorException("DAO->Erro ao buscar banco pagador.");
		}
    
    	while($registro = pg_fetch_object($rs)){
    		$retorno[] = $registro;
    	}
    
    	return $retorno;
    }
	
	/**
	 * Método para buscar número da remessa
	 * (apagar).
	 *
	 * @param apgoid
	 * @return object
	 */
	public function buscaNoRemessa($dados){
    	$retorno = array();

    	$sql = "
			SELECT 
				apgno_remessa 
			FROM 
				apagar 
			WHERE 
				apgoid  = ".$dados->apgoid.";
    	";
   
    	//echo $sql."</br>";
    	//exit;
    	if (!$rs = pg_query($this->conn,$sql)){
    		throw new ErrorException("DAO->Erro ao buscar número da remessa.");
		}
    	
    	while($registro = pg_fetch_object($rs)){
    	    	$retorno[] = $registro;
    	}
    	
    	return $retorno;
    }

    /**
     * Método para verificar se a remessa já foi processada
     * (apagar_controle_arquivo).
     *
     * @param numeroRemessa
     * @return bool
     */
    /* 
    public function validaRemessaProcessada($noRemessa){
    
    	$sql = "
			SELECT
    			1
    		FROM
    			apagar_controle_arquivo
    		WHERE
    			apcedt_retorno IS NOT NULL
    			AND apceapgno_remessa = ".$noRemessa.";
	 	";
    	
    	//echo $sql."</br>";
    	//exit;
		if (!$rs = pg_query($this->conn,$sql)){
			throw new ErrorException("DAO->Erro ao verificar se a remessa já foi processada.");
		}
    
		return (pg_num_rows($rs) > 0) ? 't' : 'f'; 
    }
     */

    /**
     * Método para buscar código da forma de pagamento
     * (apagar).
     *
     * @param apgoid
     * @return object
     */
    public function buscaFormaPagamento($dados){
    	$retorno = array();
    
    	$sql = "
			SELECT 
				apgforcoid 
			FROM 
				apagar 
			WHERE 
				apgoid = ".$dados->apgoid.";
	 	";
    	
    	//echo $sql."</br>";
    	//exit;
		if (!$rs = pg_query($this->conn,$sql)){
			throw new ErrorException("DAO->Erro ao buscar forma de pagamento.");
		}
    
    	while($registro = pg_fetch_object($rs)){
    		$retorno[] = $registro;
    	}
    
    	return $retorno;
    	
    }

    /**
     * Método para efetuar a baixa do título
     * (apagar_pgto_u).
     *
     * @param numeroTitulo, dtEfetivaPgto, formaPgto, bancoPagador, valorRealPago
     * @return bool
     */
    public function efetuaBaixaTitulo($dados){
    	
    	$sql = "
			SELECT
				apagar_pgto_u(
					'".$dados->numeroTitulo."',
					'\"".$dados->dtEfetivaPgto."\"
					\"".$dados->formaPgto."\" 
					\"".$dados->bancoPagador."\" 
					\"0\" 
					\"0\" 
					\"0\" 
					\"".$dados->valorRealPago."\" 
					\"2750\"', 
					'');
		";
    	
    	//echo $sql."</br>";
    	//exit;
    	if (!$rs = pg_query($this->conn, $sql)){
    		//throw new ErrorException("DAO->Erro ao realizar a baixa do título ".$dados->numeroTitulo.".");
            return false;
    	}else{
            return true;
        }
    }

    /**
     * Método para buscar código do status
     * (apagar_status).
     *
     * @param codigoBanco, tipo, codStatus
     * @return object
     */
    public function buscaCodigoStatus($dados){
    	$retorno = array();

    	$sql = "
    		SELECT 
	    		apgsoid
	    	FROM 
	    		apagar_status
	    	WHERE 
	    		apgsbancoid = ".$dados->codigoBanco." 
	    		AND apgstipo = '".$dados->tipo."' 
	    		AND apgscodigo IN (".$dados->codStatus.");
    	";
   
    	//echo $sql."</br>";
    	//exit;
    	if (!$rs = pg_query($this->conn,$sql)){
    		throw new ErrorException("DAO->Erro ao buscar código do status.");
		}
    	
    	while($registro = pg_fetch_object($rs)){
    	    	$retorno[] = $registro;
    	}
    	
    	return $retorno;
    }

    /**
     * Método para atualizar o status do título
     * (apagar).
     *
     * @param apgsoid, baixaAutomatica, apgnosso_numero, apgoid
     * @return bool
     */
    public function atualizaStatusTitulo($dados){
    	
		$sql = "
			UPDATE
				apagar
			SET
				apgapgsoid = ".$dados->apgsoid.", 
				apgbaixa_automatica = '".$dados->baixaAutomatica."', 
				apgnosso_numero = ".$dados->apgnosso_numero."
			WHERE 
				apgoid IN (".$dados->apgoid.");
		";
		
		//echo $sql."</br>";
		//exit;
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("DAO->Erro ao atualizar o status do título ".$dados->apgoid.".");
		}
    	
    	return (pg_affected_rows($rs) > 0) ? 't' : 'f';
    }

    /**
     * Método para buscar código da ocorrência
     * (apagar_ocorrencia).
     *
     * @param codigoOcorrencia
     * @return object
     */
    public function buscaCodigoOcorrencia($codOcorrencia){
    	$retorno = array();

    	$sql = "
			SELECT 
				apgooid, apgodescricao
			FROM 
				apagar_ocorrencia 
			WHERE 
				apgocodigo  = '".$codOcorrencia."';
    	";
   
    	//echo $sql."</br>";
    	//exit;
    	if (!$rs = pg_query($this->conn,$sql)){
    		throw new ErrorException("DAO->Erro ao buscar código da ocorrência.");
		}
    	
    	while($registro = pg_fetch_object($rs)){
    	    	$retorno[] = $registro;
    	}
    	
    	return $retorno;
    }
	
    /**
     * Método para inserir histórico de contas a pagar
     * (apagar_historico).
     *
     * @param 
     * @return boolean
     */
    public function inserirHistoricoAPagar($dados){ 
	
		$sql = "
			INSERT INTO 
				apagar_historico(
					apghapgoid, 
					apghapgno_remessa, 
					apghapgsoid, 
					apghapgooid, 
					apghusuoid_cadastro)
				SELECT
	    			apgoid, 
	    			apgno_remessa,
				    ".$dados->apgsoid.", 
				    ".$dados->apgooid.",
				    2750
	    		FROM 
					apagar 
				WHERE
				    apgoid = ".$dados->apgoid.";
		";
		
		//echo $sql."</br>";
		//exit;
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("DAO->Erro ao inserir histórico de contas a pagar.");
		}
    	
    	return (pg_affected_rows($rs) > 0) ? 't' : 'f';
	}

	/**
     * Método para buscar qtde total de títulos e 
     * qtde de títulos processados 
     * (apagar).
     *
     * @param noRemessa, listaStatus
     * @return object
     */
	public function buscaQuantidades($noRemessa,$listaStatus){
		$retorno = array();

		$sql = "
			SELECT
				COUNT(apg.apgoid) AS qtd_total,
				(
					SELECT
						COUNT(apgoid)
					FROM apagar
					WHERE
						apgno_remessa = apg.apgno_remessa
						AND apgapgsoid IN (".$listaStatus.")
				) AS qtd_processada
			FROM
				apagar apg
			WHERE
				apg.apgno_remessa = ".$noRemessa."
			GROUP BY
				apg.apgno_remessa;
			";
   
    	//echo $sql."</br>";
    	//exit;
    	if (!$rs = pg_query($this->conn,$sql)){
    		throw new ErrorException("DAO->Erro ao buscar quantidades de títulos.");
		}
    	
    	while($registro = pg_fetch_object($rs)){
    	    $retorno[] = $registro;
    	}
    	
    	return $retorno;
    }
	
	/**
	 * Método para setar a data de retorno da remessa
	 * (apagar_controle_arquivo).
	 *
	 * @param
	 * @return boolean
	 */
	public function setaDtRetornoRemessa($noRemessa){
	
		$sql = "
			UPDATE
				apagar_controle_arquivo
			SET
				apcedt_retorno = NOW()
			WHERE
				apceapgno_remessa = '$noRemessa';
		";
	
		//echo $sql."</br>";
		//exit;
		if (!$rs = pg_query($this->conn, $sql)){
			throw new ErrorException("DAO->Erro ao setar data de retorno da remessa".$noRemessa.".");
		}
    	
    	return (pg_affected_rows($rs) > 0) ? 't' : 'f';
	}

    public function verificaMovimentacaoBancaria($apgoid){

        $sql = "SELECT
                    apgmbcooid
                FROM
                    apagar
                WHERE
                    apgoid = $apgoid
                AND
                    apgmbcooid IS NOT NULL
            ";

        if (!$rs = pg_query($this->conn, $sql)){
            throw new ErrorException("DAO->Erro ao verificar movimentacao bancaria do titulo ".$apgoid.".");
        }
        
        return (pg_num_rows($rs) > 0) ? 't' : 'f';

    } 

	public function isRemessaVinculadaTituloAPagar($apgoid){
		$sql = "SELECT
					apgno_remessa
				FROM
					apagar
				WHERE
					apgoid = $apgoid";

		if (!$rs = pg_query($this->conn,$sql)){
			throw new ErrorException("DAO->Erro ao verificar se remessa é vinculada a títulos a pagar.");
		}

		$registro = pg_fetch_array($rs);

		if(isset($registro["apgno_remessa"])){
			return True;
		}else{
			return False;
		}
	}

}

<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Jorge A. D. kautzmann
 * @version 29/08/2013
 * @since 29/08/2013
 * @package Core
 * @subpackage Classe de Acesso a Dados de Cliente
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\GestorCredito;

use infra\ComumDAO;

class GestorCreditoDAO extends ComumDAO{
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Retorna dados de gestor_credito_parametrizacao.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 28/10/2013
	 * @param int $prptpcoid
	 * @param int $prptppoid
	 * @param string $tipo_pessoa
	 * @param int $prptppoid_sub
	 * @return array / false
	 */
	public function gestorCreditoParametrizacaoGetDados($prptpcoid, $prptppoid, $tipo_pessoa, $prptppoid_sub){
		$sqlString = "
			SELECT
            	gcpindica_gestor,
                gcpconlimite
            FROM
            	gestor_credito_parametrizacao
            WHERE
            	gcpdt_exclusao IS NULL
            AND
            	gcptipocontrato = $prptpcoid
            AND
            	gcptppoid = $prptppoid
            AND
            	lower(gcptipopessoa) = '" . strtolower($tipo_pessoa) . "'";
		
		if($prptppoid_sub > 0){
			$sqlString .= " AND gcptppoid_sub = $prptppoid_sub"; 
		}
		
		$sqlString .= ";";
		$this->queryExec($sqlString);
		 
		if($this->getNumRows() > 0){
			return $this->getAssoc();
		} else{
			return false;
		}
	}
	
	/**
	 * Retorna o total de contratos de um cliente.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 28/10/2013
	 * @param int $clioid
	 * @return int
	 */
	public function clienteGetTotalContratos($clioid){
		$sqlString = "
			SELECT
				connumero
			FROM
				contrato
			WHERE
				conclioid = $clioid;";
		
		$this->queryExec($sqlString);		
		return (int) $this->getNumRows();
	}
		
	/**
	 * Verifica se o cliente pagador possui titulos em atraso há mais de 15 dias.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 18/10/2013
	 * @param int $clioid
	 * @return boolean
	 */
	public function verificaPendenciaTitulosInterna($clioid){
		$sqlString = "
			SELECT
				titoid
			FROM
				titulo
			INNER JOIN
				nota_fiscal ON nfloid = titnfloid AND nfldt_cancelamento IS NULL
			WHERE
				titclioid = $clioid
			AND
				titdt_pagamento IS NULL
			AND
				((NOW() - titdt_vencimento)::INTERVAL > INTERVAL '15 DAYS');";
        
     	$this->queryExec($sqlString);
    	
    	if($this->getNumRows() > 0){
    		return true;
    	} else{
    		return false;
    	}
	}
	
	/**
	 * Retorna a média da quantidade de dias em atraso do cliente.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 18/10/2013
	 * @param int $clioid (ID do Cliente)
	 * @return int / false
	 */
	public function getMediaAtrasoCliente($clioid){
		$sqlString = "
			SELECT
            	ROUND((SUM(valor) / SUM(titulos)),0) AS media
          	FROM
          		(SELECT
					SUM(titdt_pagamento - titdt_vencimento) AS valor,
                    COUNT(1) as titulos
                FROM
					titulo
            	INNER JOIN
					nota_fiscal ON nfloid = titnfloid AND nfldt_cancelamento IS NULL
                WHERE
					titclioid = $clioid
                AND
                	titdt_pagamento IS NOT NULL
                AND
                	titdt_vencimento < NOW()
                	
                UNION
                
                SELECT
                	SUM(NOW()::DATE - titdt_vencimento::DATE) AS valor,
                    COUNT(1) as titulos
                FROM
                	titulo
            	INNER JOIN
            		nota_fiscal ON nfloid = titnfloid AND nfldt_cancelamento IS NULL
                WHERE
                	titclioid = $clioid
                AND
                	titdt_pagamento IS NULL
                AND
                	titdt_vencimento < NOW()) AS resultado_total;";
		
		
		$this->queryExec($sqlString);
		 
		if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return false;
        }
	}
	
	/**
	 * Retorna os dados do cliente pagador.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 28/10/2013
	 * @param int $prptpcoid
	 * @return array
	 */
	public function clientePagadorGetDados($prptpcoid){
		$sqlString = "
			SELECT
				tpccliente_pagador_monitoramento AS clioid_pagador
            FROM
                tipo_contrato
            WHERE
                tpcoid = $prptpcoid
			AND
				tpccliente_pagador_monitoramento IS NOT NULL;";		
		
		$this->queryExec($sqlString);
		 
		if($this->getNumRows() > 0){
            return $this->getAssoc();
        } else{
            return array();
        }
	}
	
	/**
	 * Retorna um array com as formas de cobrança.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 30/10/2013
	 * @return array
	 */
	public function getFormasCobranca(){
		$sqlString = "
			SELECT
				forcoid,
				forcnome
			FROM
				forma_cobranca;";
				
		$this->queryExec($sqlString);
			
		if($this->getNumRows() > 0){
			return $this->getAll();
		} else{
			return array();
		}
	}
	
	/**
	 * Retorna o total de contratos ativos do cliente pagador.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 30/10/2013
	 * @param int $clioid (ID do cliente)
	 * @param boolean $pagador (Informa se o cliente é ou não o pagador)
	 * @return int
	 */
	public function getTotalContratosAtivos($clioid, $pagador){
		$sqlString = "
			SELECT
				connumero
			FROM
				contrato";
		
		if($pagador){
			$sqlString .= " INNER JOIN tipo_contrato ON tpcoid = conno_tipo";
		}
		
		$sqlString .= "
			WHERE
				condt_exclusao IS NULL
			AND
				concsioid = 1";
		
		if($pagador){
			$sqlString .= " AND tpccliente_pagador_monitoramento = $clioid;";
		} else{
			$sqlString .= " AND conclioid = $clioid;";
		}
		
		$this->queryExec($sqlString);		
		return $this->getNumRows();
	}
	
	/**
	 * Retorna o tempo de relacionamento do cliente (Em meses)
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 30/10/2013
	 * @param int $clioid
	 * @return float
	 */
	public function getTempoRelacionamentoCliente($clioid){
		$sqlString = "
			SELECT
				(extract(month from (age(NOW(), condt_ini_vigencia)))) + 
					(extract(year from (age(NOW(), condt_ini_vigencia))) * 12) AS meses
			FROM
				contrato
			WHERE
				conclioid = $clioid
			AND
				condt_ini_vigencia IS NOT NULL
			ORDER BY
				condt_ini_vigencia ASC;";
		
		$this->queryExec($sqlString);
			
		if($this->getNumRows() > 0){
			return $this->getAssoc();
		} else{
			return 0;
		}
	}
	
	/**
	 * Soma os títulos em atraso do cliente pagador.
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 30/10/2013
	 * @param int $clioid
	 * @return float
	 */
	public function getValorTitulosAtrasados($clioid){
		$sqlString = "
			SELECT
				SUM(titvl_titulo) AS total
			FROM
				titulo
			WHERE
				titclioid = $clioid
			AND
				titdt_pagamento IS NULL
			AND
				((NOW() - titdt_vencimento)::INTERVAL > INTERVAL '15 DAYS');";
		
		$this->queryExec($sqlString);
			
		if($this->getNumRows() > 0){
			return $this->getAssoc();
		} else{
			return 0;
		}
	}
	
	/**
	 * Método retorna o valor médio dos títulos do cliente
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 04/11/2013
	 * @param int $clioid (ID do cliente)
	 * @return float
	 */
	public function getValorMedioTitulosAtivos($clioid){
		$sqlString = "
			SELECT
				ROUND(AVG(titvl_titulo), 2) AS media
			FROM
				titulo
			WHERE
				titclioid = $clioid
			AND
				titdt_cancelamento IS NULL;";
		
		$this->queryExec($sqlString);
		if($this->getNumRows() > 0){
			return $this->getAssoc();
		} else{
			return 0;
		}
	}
	
	/**
	 * Método retorna o valor médio dos titulos do cliente
	 * @author Vinicius Senna [vsenna@brq.com]
	 * @version 13/05/2014
	 * @param int $clioid (ID do cliente)
	 * @return float
	 */
	public function getValorMedioTitulos($clioid){
		$sqlString = "
			SELECT
					COALESCE(ROUND((SUM(titvl_titulo) / COUNT(1)), 2),0) AS media
			FROM
					titulo
            INNER JOIN nota_fiscal on nfloid = titnfloid and nfldt_cancelamento IS NULL
			WHERE
					titclioid = $clioid
		";

		$this->queryExec($sqlString);
		if($this->getNumRows() > 0){
			return $this->getAssoc();
		} else{
			return 0;
		}
	}

	/**
	 * Método retorna o NÚMERO total de títulos ativos do cliente
	 *
	 * @author Bruno B. Affonso [bruno.bonfim@sascar.com.br]
	 * @version 04/11/2013
	 * @param int $clioid (ID do cliente)
	 * @return int Número de títulos
	 */
	public function getNumeroTotalTitulosAtivos($clioid){
		$sqlString = "
			SELECT
				titoid
			FROM
				titulo
			WHERE
				titclioid = $clioid
			AND
				titdt_cancelamento IS NULL;";
		$this->queryExec($sqlString);
		return $this->getNumRows();
	}
	
	/**
	 * Método retorna o VALOR total de títulos ativos do cliente
	 *
	 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
	 * @version 24/10/2013
	 * @param int $clioid (ID do cliente)
	 * @return float valor total de títulos
	 */
	public function getValorTotalTitulosAtivos($clioid){
		$sqlString = "
			SELECT
				SUM(titvl_titulo) as total
			FROM
				titulo
			WHERE
				titclioid = $clioid
			AND
				titdt_cancelamento IS NULL;";
			
		$this->queryExec($sqlString);
		if($this->getNumRows() > 0){
		    return $this->getAssoc();
		} else{
		    return 0;
		}
	}

	/**
     * Retorna o maior atraso de um cliente
     * @author Vinicius Senna <vsenna@brq.com>
     * @version 02/05/2014
     * @param int $titclioid id do cliente
     * @return string / false
     */
	public function clienteBuscaMaiorAtraso($titclioid){

		if((int) $titclioid > 0) {
			$sqlString ="
			SELECT
						(titdt_pagamento::DATE  - titdt_vencimento::DATE) AS dias_atraso
			FROM
						titulo
            INNER JOIN
                        nota_fiscal ON nfloid = titnfloid AND nfldt_cancelamento IS NULL
			WHERE
						titclioid = ".$titclioid."
			AND
						titdt_pagamento IS NOT NULL
			AND
						titdt_vencimento < NOW()
			ORDER BY
						dias_atraso DESC
			LIMIT 		1
			";

			$this->queryExec($sqlString);

			if($this->getNumRows() > 0){
			    return $this->getAssoc();
			} else{
			    return false;
			}
		} else{
			return false;
		}
	}


	/**
	 * Retorna os dias em atraso do cliente
	 * @author Vinicius Senna <vsenna@brq.com>
	 * @version 02/05/2014
	 * @param int $clioid id do cliente
	 * @return string / false
	 */
	public function clienteBuscaDiasAtraso($clioid){

		if((int) $clioid > 0) {

			$sql ="
			SELECT
						(NOW()::DATE - titdt_vencimento::DATE) AS dias_atraso
			FROM
						titulo
            INNER JOIN
                        nota_fiscal ON nfloid = titnfloid AND nfldt_cancelamento IS NULL
			WHERE
						titclioid = ".$clioid."
			AND
						 ((titdt_credito IS NULL) AND (titformacobranca <> 51))
			AND 
				    	 titdt_cancelamento IS NULL		
			AND
						titdt_vencimento < NOW()
			ORDER BY
						dias_atraso DESC
			LIMIT 		1
			";

		    $this->queryExec($sql);
			if($this->getNumRows() > 0){
			    return $this->getAssoc();
			} else{
			    return false;
			}
		} else{
			return false;
		}
	}

	/**
	 * Retorna menor data contrato
	 * @author Vinicius Senna <vsenna@brq.com>
	 * @version 02/05/2014
	 * @param int $clioid id do cliente
	 * @param $pagador
	 * @return string / false
	 */
	public function clienteBuscaMenorDataContrato($clioid, $pagador){

		$sql = '';

		if(!$pagador) {

	        $sql = "
	                SELECT
	                            TO_CHAR(condt_ini_vigencia, 'dd/mm/YYYY') as data
	                FROM
	                            contrato
	                WHERE
	                            conclioid = ".$clioid."
	                AND
	                            condt_ini_vigencia IS NOT NULL
	                ORDER BY
	                            condt_ini_vigencia ASC
	                LIMIT 		1
	                ";
	    } else {

	        $sql ="
	                SELECT
	                            TO_CHAR(condt_ini_vigencia, 'dd/mm/YYYY') as data
	                FROM
	                            contrato
	                INNER JOIN
	                            tipo_contrato ON tpcoid = conno_tipo
	                WHERE
	                            tpccliente_pagador_monitoramento = ".$clioid."
	                AND
	                            condt_ini_vigencia IS NOT NULL
	                ORDER BY
	                            condt_ini_vigencia ASC
	                LIMIT 		1
	                ";

	   	}

   	 	$this->queryExec($sql);
		if($this->getNumRows() > 0){
		    return $this->getAssoc();
		} else{
		    return false;
		}
	}

	/**
	 * Verifica se há pendência financeira
	 * @author Vinicius Senna <vsenna@brq.com>
	 * @version 02/05/2014
	 * @param int $clioid id do cliente
	 * @return boolean
	 */
	public function clienteVerificaInadimplencia($clioid){

		$sql ="
			SELECT
					COUNT(1) as dias_atraso
			FROM
					titulo
            INNER JOIN
                nota_fiscal ON nfloid = titnfloid AND nfldt_cancelamento IS NULL
			WHERE
					titclioid = ".$clioid."
			AND
					 ((titdt_credito IS NULL) AND (titformacobranca <> 51))
			AND 
					 titdt_cancelamento IS NULL		
			AND
					((NOW() - titdt_vencimento)::INTERVAL > INTERVAL '15 DAYS')
			";

		$this->queryExec($sql);

		if($this->getNumRows() > 0){
		    return $this->getAssoc();
		} else{
		    return false;
		}

	}

	/**
	 * Soma total de contratos ativos
	 * @author Vinicius Senna <vsenna@brq.com>
	 * @version 02/05/2014
	 * @param int $clioid id do cliente
	 * @param pagador
	 * @return string /false
	 */
	public function clienteSomarTotalcontratosAtivos($clioid, $pagador){

		$sql = '';

	    if(!$pagador) {

	        $sql ="
	                SELECT
	                        COUNT(1) AS total
	                FROM
	                        contrato
	                WHERE
	                        condt_exclusao IS NULL
	                AND
	                        concsioid = 1
	                AND
	                        conclioid = ".$clioid."
	            ";

	    } else {
	        $sql ="
				SELECT
						COUNT(1) AS total
				FROM
						contrato
	            INNER JOIN
	                    tipo_contrato ON tpcoid = conno_tipo
				WHERE
						condt_exclusao IS NULL
				AND
						concsioid = 1
				AND
						tpccliente_pagador_monitoramento = ".$clioid."
			";
	    }

		$this->queryExec($sql);

		if($this->getNumRows() > 0){
		    return $this->getAssoc();
		} else{
		    return false;
		}

	}
}
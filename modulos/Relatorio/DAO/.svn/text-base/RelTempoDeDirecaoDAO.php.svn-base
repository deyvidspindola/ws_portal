<?php

/**
 * Classe de persist�ncia de dados
 * 
 * @author Denilson Sousa
 * 
 */
class RelTempoDeDirecaoDAO {

    private $conn;
    private $limite_resultados;

        /**
     * Efetua a pesquisa do relat�rio
     * 
     * @author Denilson Sousa
     */
    public function pesquisar($pCampos,$clioid_ult_num = null) {

        // Validacao de ultimo digito do clioid preenchido pois senao vai dar erro fatal na consulta
        if(is_null($clioid_ult_num) || ($clioid_ult_num > 9 || $clioid_ult_num < 0))
            throw new Exception('Falha ao recuperar dados do cliente, tente novamente.');

        $sql = "
            select  
            mentdatapacote as data_envio,
            mentdata as data_chegada, 
            veiplaca, 
            mttdnome, 
            tmttdescricao, 
            motonome, 
            mentmotologin, 
            clinome, 
            mentmensagem,
	    CASE WHEN mttdoid >= 1 and mttdoid <= 9 THEN 'APP SASMDT'
                 ELSE 'Macro'
            END as tipo 
            from motorista, 
            cliente, 
            mensagem_teclado_cli$clioid_ult_num, 
            macro_teclado_td50, 
            tipo_macro_tempo_direcao,
            veiculo
            where
            1=1 
            and mttdoid = mentmttdoid 
            and tmttoid = mttdtmttoid 
            and mentveioid = veioid 
            and mentmotooid = motooid 
            and mentclioid = clioid
	    and $pCampos
            order by mentdata limit $this->limite_resultados
                ";
        //echo $sql;
        //throw new Exception('<pre>'$sql);
        
        if (!$rs = pg_query($this->conn, $sql)) {
            throw new Exception('Falha de conex�o ao tentar realizar a pesquisa.');
	    
        }

        if (pg_num_rows($rs) > 0) {
            return pg_fetch_all($rs);
        }

        return array();
    }

   /**
    * recupa os dados de cliente
    *
    * @param dados do formulario
    * @return array
    */
    public function recuperarCliente($params) {

        $retorno = array();

        $sql = "
            SELECT
                clioid,
                clinome
            FROM
                clientes
            WHERE
                clidt_exclusao IS NULL
            AND
                clinome ILIKE '%". $params ."%'
            ORDER BY
                clinome
            LIMIT
                30
            ";

        if ($rs = pg_query($this->conn, $sql)) {

            $i = 0;
            while ($tupla = pg_fetch_object($rs)) {
                $retorno[$i]['id'] = $tupla->clioid;
                $retorno[$i]['label'] = utf8_encode($tupla->clinome);
                $retorno[$i]['value'] = utf8_encode($tupla->clinome);
                $i++;
            }

        }

        return $retorno;
    } 

    /**
    * recupa os clioid por placa
    *
    * @param placa enviada no formulario
    * @return array
    */
    public function recuperarClioidPorPlaca($placa) {
	$retorno = null;
	
	$sql = "
	select conclioid 
	from veiculo 
	join contrato on contrato.conveioid = veiculo.veioid 
	where 
	veiplaca ilike '$placa'
	and veidt_exclusao is null and condt_exclusao is null
	";
	
	if ($rs = pg_query($this->conn, $sql)) {
		while ($tupla = pg_fetch_object($rs)) {	
			$retorno = $tupla->conclioid;
		}
	}

	return $retorno;

    }
    
    /**
    * seta o limite de resultados do relatorio
    *
    * @param limite_resultados 
    * @return array
    */
    public function setLimiteResultados($limite_resultados) {
        if($limite_resultados>0){
            $this->limite_resultados = $limite_resultados;
            return 1;
        }
        return 0;
    } 


    /**
     * Construtor
     *
     * @author Denilson Sousa
     * 
     */
    public function RelTempoDeDirecaoDAO($conn,$limite_resultados = 1000) {
        $this->conn = $conn;
        $this->limite_resultados = $limite_resultados;
    }

}

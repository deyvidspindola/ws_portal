<?php
namespace module\RoteadorBoleto;

class RoteadorBoletoDAO extends \Infra\ComumDAO {

    public function getTitulo($idTitulo)
    {
    	$query = "SELECT titclioid clioid, tittpetoid, titvl_titulo valor, titvl_desconto desconto, titvl_multa multa, titvl_juros juros 
    		        FROM titulo 
                   WHERE titoid = $idTitulo";
    	return $this->execute($query);
    }

    public function getTituloRetencao($idTitulo)
    {
    	$query = "SELECT titclioid clioid, tittpetoid, titvl_titulo_retencao valor, titvl_desconto desconto, titvl_multa multa, titvl_juros juros 
    		        FROM titulo_retencao 
                   WHERE titoid = $idTitulo";
    	return $this->execute($query);
    }

    public function getTituloConsolidado($idTitulo)
    {
    	$query = "SELECT titcclioid clioid, titctpetoid tittpetoid, titcvl_titulo valor, titcvl_desconto desconto, titcvl_multa multa, titcvl_juros juros 
    		        FROM titulo_consolidado 
                   WHERE titcoid = $idTitulo";
	    return $this->execute($query);
    }

    public function getTituloBoleto($idTitulo)
    {
        /**
         * STI 87096 - 4.2 - Somado os valores titvl_ir, titvl_piscofins, titvl_iss e retornado com o alias 'impostos'
         * descontado o valor destas colunas da soma do valor_cobrado
         * @author douglas.karling.ext
         * @since 07/12/2017
         */
    	$query = "SELECT cli.clioid, titformacobranca forcoid, TO_CHAR(titdt_vencimento,'DD-MM-YYYY') as data_vencimento, titvl_titulo valor, 
    		             titoid, titvl_desconto desconto, titvl_multa multa, titvl_juros juros, 
                         COALESCE(titvl_ir,0) + COALESCE(titvl_piscofins,0) + COALESCE(titvl_iss,0) impostos,
    		             titvl_titulo + titvl_multa + titvl_juros - titvl_desconto - (COALESCE(titvl_ir,0) + COALESCE(titvl_piscofins,0) + COALESCE(titvl_iss,0)) valor_cobrado, 
                         tittpetoid
    		       FROM titulo tit 
                   JOIN clientes cli ON cli.clioid = tit.titclioid 
    		      WHERE tit.titoid = $idTitulo";
	  	return $this->execute($query);
    }

    public function getTituloBoletoRetencao($idTitulo)
    {
    	$query = "SELECT cli.clioid, titformacobranca forcoid, TO_CHAR(titdt_vencimento,'DD-MM-YYYY') as data_vencimento, titvl_titulo_retencao valor, 
    		             titoid, titvl_desconto desconto, titvl_multa multa, titvl_juros juros, 
    		             titvl_titulo_retencao + titvl_multa + titvl_juros - titvl_desconto valor_cobrado,
                         tittpetoid
    		       FROM titulo_retencao tit 
                   JOIN clientes cli ON cli.clioid = tit.titclioid 
    		      WHERE tit.titoid = $idTitulo";
		return $this->execute($query);
    }

    public function getTituloBoletoConsolidado($idTitulo)
    {
    	$query = "SELECT cli.clioid, titcformacobranca forcoid, TO_CHAR(titcdt_vencimento,'DD-MM-YYYY') as data_vencimento, titcvl_titulo valor, 
    		             titcoid titoid, titcvl_desconto desconto, titcvl_multa multa, titcvl_juros juros, 
    		             titcvl_titulo + titcvl_multa + titcvl_juros - titcvl_desconto valor_cobrado,
                         titctpetoid tittpetoid
    		       FROM titulo_consolidado tit 
                   JOIN clientes cli ON cli.clioid = tit.titcclioid 
    		      WHERE tit.titcoid = $idTitulo";
	    return $this->execute($query);
    }

    public function isTituloAtivo($idTitulo)
    {
    	$query = 'SELECT titoid FROM titulo WHERE titdt_credito IS NOT NULL';
    	$this->queryExec($query);
    	return $this->getNumRows() > 0;
    }

    public function isTituloRetencaoAtivo($idTitulo)
    {
    	$query = 'SELECT titoid FROM titulo_retencao WHERE titdt_credito IS NOT NULL';
    	$this->queryExec($query);
    	return $this->getNumRows() > 0;
    }

    public function isTituloConsolidadoAtivo($idTitulo)
    {
    	$query = 'SELECT titcoid FROM titulo_consolidado WHERE titcdt_credito IS NOT NULL';
    	$this->queryExec($query);
    	return $this->getNumRows() > 0;
    }

    public function alteraStatus($idTitulo, $novoStatus)
    {
    	$query = "UPDATE titulo SET titformacobranca = $novoStatus WHERE titoid = $idTitulo";
    	$this->queryExec($query);
    }

    public function alteraStatusRetencao($idTitulo, $novoStatus)
    {
    	$query = "UPDATE titulo_retencao SET titformacobranca = $novoStatus WHERE titoid = $idTitulo";
    	$this->queryExec($query);
    }

    public function alteraStatusConsolidado($idTitulo, $novoStatus)
    {
    	$query = "UPDATE titulo_consolidado SET titcformacobranca = $novoStatus WHERE titcoid = $idTitulo";
    	$this->queryExec($query);
    }

    public function getCodigoCancelamentoCnab()
    {
    	$query = "SELECT tpetoid 
                    FROM tipo_evento_titulo 
                   WHERE tpettipo_evento = 'Remessa' 
                     AND tpetcfbbanco = 33 
                     AND tpetcodigo = 2 
                     AND tpetcob_registrada = true";
    	return $this->execute($query);
    }

    public function cancelarTituloCnab($idTitulo, $codigo)
    {
    	$query = "UPDATE titulo SET tittpetoid = $codigo, titrtcroid = NULL  WHERE titoid = $idTitulo";
    	$this->queryExec($query);
    }

    public function cancelarTituloRetencaoCnab($idTitulo, $codigo)
    {
    	$query = "UPDATE titulo_retencao SET tittpetoid = $codigo, titrtcroid = NULL WHERE titoid = $idTitulo";
        $this->queryExec($query);
    }

    public function cancelarTituloConsolidadoCnab($idTitulo, $codigo)
    {
    	$query = "UPDATE titulo_consolidado SET titctpetoid = $codigo, titcrtcroid = NULL WHERE titcoid = $idTitulo";
    	$this->queryExec($query);
    }
    
    public function cancelarTituloERP($idTitulo)
    {
        $query = "UPDATE titulo 
                     SET titdt_cancelamento = NOW(), 
                         titobs_cancelamento = 'Cancelado para novo registro de boleto no Banco Santander'
                   WHERE titoid = $idTitulo";
        $this->queryExec($query);

        $query = "SELECT titdt_cancelamento FROM titulo WHERE titoid = $idTitulo";
        $result = $this->execute($query);
        
        return (bool) $result->titdt_cancelamento;
    }
        
    public function cancelarTituloRetencaoERP($idTitulo)
    {
        $query = "UPDATE titulo_retencao 
                     SET titdt_cancelamento = NOW(), 
                         titobs_cancelamento = 'Cancelado para novo registro de boleto no Banco Santander'
                   WHERE titoid = $idTitulo";
        $this->queryExec($query);
        
        $query = "SELECT titdt_cancelamento FROM titulo_retencao WHERE titoid = $idTitulo";
        $result = $this->execute($query);
        
        return (bool) $result->titdt_cancelamento;
    }
            
    public function cancelarTituloConsolidadoERP($idTitulo)
    {
        $query = "UPDATE titulo_consolidado 
                     SET titcdt_cancelamento = NOW(), 
                         titcobs_cancelamento = 'Cancelado para novo registro de boleto no Banco Santander'
                   WHERE titcoid = $idTitulo";
        $this->queryExec($query);

        $query = "SELECT titcdt_cancelamento FROM titulo_consolidado WHERE titcoid = $idTitulo";
        $result = $this->execute($query);

        return (bool) $result->titcdt_cancelamento;
    }

    public function getCodigoExpiradoCnab()
    {
    	$query = "SELECT tpetoid 
                    FROM tipo_evento_titulo 
                   WHERE tpettipo_evento = 'Baixa_detalhe' 
                     AND tpetcfbbanco = 33 
    		         AND tpetcodigo IN (12, 13)
                     AND tpetcob_registrada = true";
        return $this->execute($query);
    }

    public function isTituloExpirado($idTitulo, $codigo)
    {
        $codigos = implode(',', (array) $codigo);
    	$query = "SELECT tittpetoid
                    FROM titulo 
                   WHERE titoid = $idTitulo 
                     AND tittpetoid IN ($codigos)";
    	$this->queryExec($query);
    	return $this->getNumRows() > 0;
    }

    public function isTituloRetencaoExpirado($idTitulo, $codigo)
    {
        $codigos = implode(',', (array) $codigo);
    	$query = "SELECT tittpetoid
                    FROM titulo_retencao 
                   WHERE titoid = $idTitulo 
                     AND tittpetoid IN ($codigos)";
    	$this->queryExec($query);
    	return $this->getNumRows() > 0;
    }

    public function isTituloConsolidadoExpirado($idTitulo, $codigo)
    {
        $codigos = implode(',', (array) $codigo);
    	$query = "SELECT titctpetoid
                    FROM titulo_consolidado 
                   WHERE titcoid = $idTitulo 
                     AND titctpetoid IN ($codigos)";
    	$this->queryExec($query);
    	return $this->getNumRows() > 0;
    }

    public function updateDataVencimentoTitulo($idTitulo, $dataVencimento)
    {
        $query = "UPDATE titulo 
                     SET titdt_vencimento = to_date('$dataVencimento', 'DD/MM/YYYY') 
                   WHERE titoid = $idTitulo";
        $this->queryExec($query);
    }

    public function updateDataVencimentoTituloRetencao($idTitulo, $dataVencimento)
    {
        $query = "UPDATE titulo_retencao 
                     SET titdt_vencimento = to_date('$dataVencimento', 'DD/MM/YYYY') 
                   WHERE titoid = $idTitulo";
        $this->queryExec($query);
    }

    public function updateDataVencimentoTituloConsolidado($idTitulo, $dataVencimento)
    {
        $query = "UPDATE titulo_consolidado 
                     SET titcdt_vencimento = to_date('$dataVencimento', 'DD/MM/YYYY') 
                   WHERE titoid = $idTitulo";
        $this->queryExec($query);
    }

    public function getCodigoAtivo()
    {
        $query = "SELECT tpetoid 
                    FROM tipo_evento_titulo 
                   WHERE tpettipo_evento = 'Retorno' 
                     AND tpetcfbbanco = 33 
                     AND tpetcodigo = 2 
                     AND tpetcob_registrada = true";
        return $this->execute($query);
    }
    
    public function isTituloRegistrado($idTitulo)
    {
        $query = "SELECT titoid
                    FROM titulo 
                   WHERE titoid = $idTitulo
                     AND titformacobranca = 84";
        $this->queryExec($query);
        return $this->getNumRows() > 0;
    }
    
    public function isTituloRegistradoRetencao($idTitulo)
    {
        $query = "SELECT titoid
                    FROM titulo_retencao 
                   WHERE titoid = $idTitulo
                     AND titformacobranca = 84";
        $this->queryExec($query);
        return $this->getNumRows() > 0;
    }
    
    public function isTituloRegistradoConsolidado($idTitulo)
    {
        $query = "SELECT titcoid
                    FROM titulo_consolidado 
                   WHERE titcoid = $idTitulo
                     AND titcformacobranca = 84";
        $this->queryExec($query);
        return $this->getNumRows() > 0;
    }

    public function getPcsiDescricao()
    {
        $query = "SELECT pcsidescricao 
                    FROM parametros_configuracoes_sistemas 
              INNER JOIN parametros_configuracoes_sistemas_itens ON pcsipcsoid = pcsoid 
                   WHERE pcsipcsoid = 'COBRANCA_REGISTRADA' AND pcsioid = 'COD_MOVIMENTO_REGISTRADO'";
        return $this->execute($query);
    }

    public function getCodigoBoletoRegistrado($pcsidescricao) 
    {
        $query = "SELECT tpetoid
                    FROM tipo_evento_titulo 
                   WHERE tpetcodigo in ({$pcsidescricao}) 
                     AND tpettipo_evento = 'Retorno' 
                     AND tpetcfbbanco = 33 
                     AND tpetcob_registrada IS TRUE";
        return $this->execute($query);
    }

    private function execute($query)
    {
        $this->queryExec($query);

        if ($this->getNumRows() == 1) {
            return (object) $this->getAssoc();
        }

        if ($this->getNumRows() > 1) {
            $objects = array();

            foreach ($this->getAll() as $row) {
                array_push($objects, (object) $row);
            }

            return $objects;
        }

        return array();
    }
}

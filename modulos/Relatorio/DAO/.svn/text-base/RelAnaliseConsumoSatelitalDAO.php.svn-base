<?php

/**
 * Classe CadDadosEquipamentoDAO.
 * Camada de modelagem de dados.
 *
 * @package  Relatorio
 * @author   Denilson Andre de Sousa  <denilson.sousa@sascar.com.br>
 *
 */
class RelAnaliseConsumoSatelitalDAO {

	/** Conexão com o banco de dados */
	private $conn;

	/** Usuario logado */
	private $usarioLogado;

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	public function __construct($conn) {

		//Seta a conexao na classe
        $this->conn = $conn;
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
                    acsoid, -- Chave primaria da tabela analise_consumo_dados_satelital
                    acsasatno_serie, -- Numero de serie da antena referente a tabela antena_satelital.
                    (acsconsumo_operadora) / 1000 as acsconsumo_operadora, -- Consumo da antena satelital na operadora em Kb
                    acsdata_apuracao, -- Mes de referencia de apuracao das informacoes
                    acsveiplaca, -- Placa do veiculo obtida da tabela veiculo
                    acsveioid, -- id do veiculo da placa obtida da tabela veiculo
                    acsequno_serie, -- Numero de serie do equipamento obtido da tabela equipamento
                    acsequesn, -- ESN do equipamento obtido da tabela equipamento
                    acseveversao, -- Versao do equipamento referente a tabela equipamento_versao
                    acslinpotoid,-- Código do Plano de Operadora a qual a linha pertence, tabela - plano_operadora.
                    acslinnumero, -- Numero do Telefone referente a tabela linha (campo da tabela linha).
                    acscslstatus, -- Status da linha referente a tabela celular_status
                    acslincid, -- Segue definiçao de CID na verdade a sigla correta é ICC-ID sua definiçao é: Cada sim card é identificado internacionalmente pelo ICCID. O ICCID é armazenado no sim card e tem o seu código impresso no corpo do Sim Card O seu número é de ateh 19 ou 20 digitos.
                    acslscdescricao, -- STATUS CID Descricao do Registro referente a tabela linha_status_cid
                    acsasatstatus_fornecedor, -- Status da antena satelital no Fornecedor referente a tabela antena_satelital - (A)tiva, (I)nativa ou Inativa (C)omando 129, (F)alha de Comunicacao, (E)rro de Ativacao, (D)Pendente Desativacao
                    acscsidescricao, -- Situacao do contrato referente a tabela contrato_situacao.
                    acsconnumero, -- ID da tabela contrato, referente ao codigo do contrato cadastrado na base da sascar (campo da tabela contrato)
                    acstpcdescricao, -- Tipo do contrato campo da tabela tipo_contrato
                    acseqcdescricao, -- Classe do contrato campo da tabela equipamento_classe.
                    acscpagvl_servico, -- Valor de monitoramento negociado campo da tabela contrato_pagamento.
                    acscpagmonitoramento, -- Valor de monitoramento campo da tabela contrato_pagamento.
                    acsconsvalor_total, -- Somatorio do valor dos servicos do contrato campo da tabela contrato_servico (consqtde * consvalor).
                    acstimeonsat,-- Tempo configurado de mensagem satelital com ignicao ligada.
                    acstimeoffsat,-- Tempo configurado de mensagem satelital com ignicao desligada.
                    acsconclioid, -- ID da tabela cliente, referente ao codigo do cliente cadastrado na base da sascar (campo da tabela contrato)
                    acsclinome, -- Nome do Cliente (campo da tabela clientes). 
                    acsoploperadora, -- Operadora referente a tabela operadora_linha
                    acsgerenciadoras -- Total de Gerenciadoras direcionadas
				FROM 
                    analise_consumo_dados_satelital
				WHERE 
					1 = 1
                ";

        if ( isset($parametros->veioid) && !empty($parametros->veioid) ) {
            $sql .= " AND
                        acsveioid = " . pg_escape_string( $parametros->veioid );
        }
        if ( isset($parametros->placa) && !empty($parametros->placa) ) {
            $sql .= " AND
                        acsveiplaca ILIKE '%" . pg_escape_string( $parametros->placa ) . "%'";
        }
        if ( isset($parametros->antena) && !empty($parametros->antena) ) {
            $sql .= " AND
                        acsasatno_serie ILIKE '%" . pg_escape_string( $parametros->antena ) . "%'";
        }
        if ( isset($parametros->clioid) && !empty($parametros->clioid) ) {
            $sql .= " AND
                        acsconclioid = " . pg_escape_string( $parametros->clioid );
        }
        if ( isset($parametros->cliente) && !empty($parametros->cliente) ) {
             $sql .= " AND
                         acsclinome ILIKE '%" . pg_escape_string( $parametros->cliente ) . "%'";
        }
        if ( isset($parametros->contrato) && !empty($parametros->contrato) ) {
            $sql .= " AND
                        acsconnumero = " . pg_escape_string( $parametros->contrato );
        }
        if ( isset($parametros->mes) && !empty($parametros->mes) && isset($parametros->ano) && !empty($parametros->ano) ) {
            $sql .= " AND
                        acsdata_apuracao = '" . pg_escape_string( $parametros->ano ) . "-".pg_escape_string( $parametros->mes ). "-01'";
        }

        $sql .= " ORDER BY acsconsumo_operadora DESC, acsclinome ASC";

        //echo('<pre />');
        //echo ($sql);
		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Método para realizar a pesquisa do consolidado do cliente
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisarConsolidado(stdClass $parametros){

		$retorno = array();

		$sql = "SELECT 
                    acsconclioid as clioid, 
                    acsclinome as clinome,  
                    SUM(acsconsumo_operadora) / 1000 AS total,
                    to_char(MIN(acsdata_apuracao), 'MM/YYYY') as min_apuracao,
                    to_char(MAX(acsdata_apuracao), 'MM/YYYY') as max_apuracao
				FROM 
                    analise_consumo_dados_satelital
				WHERE 
					1 = 1
                ";

        if ( isset($parametros->cliente) && !empty($parametros->cliente) ) {
            $sql .= " AND
                        acsclinome ILIKE '%" . pg_escape_string( $parametros->cliente ) . "%'";
        }
         if ( isset($parametros->veioid) && !empty($parametros->veioid) ) {
            $sql .= " AND
                        acsveioid = " . pg_escape_string( $parametros->veioid );
        }
        if ( isset($parametros->placa) && !empty($parametros->placa) ) {
            $sql .= " AND
                        acsveiplaca ILIKE '%" . pg_escape_string( $parametros->placa ) . "%'";
        }
        if ( isset($parametros->antena) && !empty($parametros->antena) ) {
            $sql .= " AND
                        acsasatno_serie ILIKE '%" . pg_escape_string( $parametros->antena ) . "%'";
        }

        if ( isset($parametros->clioid) && !empty($parametros->clioid) ) {
            $sql .= " AND
                        acsconclioid = " . pg_escape_string( $parametros->clioid );
        }
        if ( isset($parametros->contrato) && !empty($parametros->contrato) ) {
            $sql .= " AND
                        acsconnumero = " . pg_escape_string( $parametros->contrato );
        }
        if ( isset($parametros->mes) && !empty($parametros->mes) && isset($parametros->ano) && !empty($parametros->ano) ) {
            $sql .= " AND
                        acsdata_apuracao = '" . pg_escape_string( $parametros->ano ) . "-".pg_escape_string( $parametros->mes ). "-01'";
        }

        $sql .= " GROUP BY acsconclioid, acsclinome
                ORDER BY SUM(acsconsumo_operadora) DESC, acsclinome ASC";

        //echo($sql);

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
	}

	/** 
     * Submete uma query a execucao do SGBD
     * @param  [string] $query
     * @return [bool]
     */
	private function executarQuery($query) {

        if(!$rs = pg_query($this->conn, $query)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $rs;
    }
}
?>

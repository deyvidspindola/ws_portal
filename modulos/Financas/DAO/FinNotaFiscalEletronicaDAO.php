<?php

require_once _MODULEDIR_ . "core/module/Cliente/Model/ClienteModel.php";
require_once _MODULEDIR_ . "core/module/Cliente/Model/DAO/ClienteDAO.php";
use module\Cliente\ClienteModel;
use module\Cliente\ClienteDAO;
use module\Parametro\ParametroCobrancaRegistrada;
use module\BoletoRegistrado\BoletoRegistradoModel;

class FinNotaFiscalEletronicaDAO
{

	private $conn;

	public function __construct($conn)
	{
		$this->conn = $conn;
	}

	public function begin()
	{
		return pg_query($this->conn, "BEGIN");
	}

	public function commit()
	{
		return pg_query($this->conn, "COMMIT");
	}

	public function rollback()
	{
		return pg_query($this->conn, "ROLLBACK");
	}

	public function atualizarSequenciaNumeroRemessa()
	{
		$sql = "
			UPDATE
				sistema
			SET
				sis_seq_envio_rps = sis_seq_envio_rps + 1
		";

		if(!pg_query($this->conn, $sql)){
			throw new Exception("Falha ao atualizar sequência do número da remessa.");
		}
	}

	public function getSequenciaNumeroRemessa()
	{
		$sql = "
			SELECT
				sis_seq_envio_rps AS numero_remessa
			FROM
				sistema
			ORDER BY
				sis_seq_envio_rps
			DESC LIMIT 1
		";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Falha ao recuperar sequência do número da remessa.");
		}

		$result = pg_fetch_object($query);

		return $result->numero_remessa;
	}

	public function pesquisar(
		$periodoFaturamentoInicial,
		$periodoFaturamentoFinal,
		$periodoCancelamentoInicial,
		$periodoCancelamentoFinal,
		$intervaloNotasInicial,
		$intervaloNotasFinal,
		$situacaoRps,
		$numeroCpfCnpj,
		$numeroNfe,
		$numeroNf,
		$somenteNaoEnviadas,
		$numeroResultados = 1000
	){

		$numeroResultados = (int)$numeroResultados;
		$numeroResultados = !empty($numeroResultados) && $numeroResultados < 1000 ? $numeroResultados : 1000;
		$somenteNaoEnviadas = !!(int)$somenteNaoEnviadas;
		$numeroCpfCnpj = !empty($numeroCpfCnpj) ? str_replace(array('-', '.', '/'), '', $numeroCpfCnpj) : null;

		$sql = "
			SELECT
				nfloid AS id_nf,
				nflno_numero AS numero_nf,
				nflserie AS serie_nf,
				clinome AS nome_cliente,
				nflvl_total AS valor_nf,
				nfldt_nota AS data_faturamento,
				nfldt_cancelamento AS data_cancelamento,
				nfekno_nfe AS numero_nfe,
				nfekdt_transmissao_rps AS data_transmissao_rps,
				nfekdt_retorno_rps AS data_retorno_rps,
				nfeklink_nfe as link_nfe
			FROM
				nota_fiscal
			LEFT JOIN
				nota_fiscal_eletronica_kernel ON nfloid = nfeknfloid
			LEFT JOIN
				clientes ON nflclioid = clioid
			LEFT JOIN
				rps_barueri_ocorrencia ON nfloid = rbocnfloid
		";

		$where = array();

		$where[] = "rbocnfloid IS NULL";

		if(!empty($periodoFaturamentoInicial)){
			$where[] = "nfldt_nota >= '$periodoFaturamentoInicial'";
		}

		if(!empty($periodoFaturamentoFinal)){
			$where[] = "nfldt_nota <= '$periodoFaturamentoFinal'";
		}

		if(!empty($periodoCancelamentoInicial)){
			$where[] = "nfldt_cancelamento >= '$periodoCancelamentoInicial'";
		}

		if(!empty($periodoCancelamentoFinal)){
			$where[] = "nfldt_cancelamento <= '$periodoCancelamentoFinal'";
		}

		if(!empty($intervaloNotasInicial)){
			$where[] = "nflno_numero >= $intervaloNotasInicial";
		}

		if(!empty($intervaloNotasFinal)){
			$where[] = "nflno_numero <= $intervaloNotasFinal";
		}

		// situacaoRps

		if(!empty($numeroCpfCnpj)){
			$where[] = "(clino_cpf = '$numeroCpfCnpj' OR clino_cgc = '$numeroCpfCnpj')";
		}

		// Filtro pode estar ativo se for enviado valor de intervalo?
		if(!empty($numeroNfe)){
			$where[] = "nfekno_nfe = $numeroNfe";
		}

		// Filtro pode estar ativo se for enviado valor de intervalo?
		if(!empty($numeroNf)){
			$where[] = "nflno_numero = $numeroNf";
		}

		if($somenteNaoEnviadas){
			$where[] = "nfekno_nfe IS NULL";
			$where[] = "nfekdt_transmissao_rps IS NULL";
		}

		if(!empty($where)){
			$sql .= "WHERE ". implode(" AND ", $where);
		}

		$sql .= " LIMIT $numeroResultados";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Falha ao recuperar informações das notas fiscais.");
		}

		$result = pg_fetch_all($query);

		return $result;

	}


	public function getInformacoesObrigacaoFinanceira($notaFiscalId)
	{
		$sql = "
			SELECT
				ofsgdescricao AS descricao_sub_grupo,
				SUM(nfivl_item) AS valor_item,
				nfitipo AS tipo_item
			FROM
				nota_fiscal
				INNER JOIN nota_fiscal_item ON nfino_numero = nflno_numero AND nfiserie = nflserie
				INNER JOIN obrigacao_financeira ON obroid = nfiobroid
				INNER JOIN obrigacao_financeira_sub_grupo ON obrofsgoid = ofsgoid
			WHERE
				nfloid = $notaFiscalId
			GROUP BY
				ofsgoid, nfitipo
			ORDER BY 
				nfitipo = 'L', ofsgdescricao
		";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Falha ao recuperar discriminação do serviço.");
		}

		$result = pg_fetch_all($query);

		return $result;
	}

	public function getInformacoesNotaFiscal($notaFiscalId)
	{
		$sql = "
			SELECT
				nfloid as nf_id,
				nflserie AS serie_rps,
				nflno_numero AS numero_rps,
				nfldt_emissao AS data_rps,
				nflcodigo_servico AS codigo_servico_prestado,
				nflvlr_imposto as valor_tributos,
				nflvl_total AS valor_nf,
				(SELECT SUM(i.nfivl_item) FROM nota_fiscal_item i WHERE i.nfino_numero = nflno_numero AND i.nfiserie = nflserie AND i.nfids_item = nfids_item AND i.nfivl_item = nfivl_item AND nfitipo <> 'L') AS total_tributados,
				(SELECT SUM(i.nfivl_item) FROM nota_fiscal_item i WHERE i.nfino_numero = nflno_numero AND i.nfiserie = nflserie AND i.nfids_item = nfids_item AND i.nfivl_item = nfivl_item AND nfitipo = 'L') AS total_nao_tributados,
				clitipo AS tipo_documento,
				CASE WHEN clitipo = 'J' THEN clino_cgc ELSE clino_cpf END AS numero_documento,
				clinome AS nome_tomador,
				endlogradouro AS endereco_logradouro,
				endno_numero AS endereco_numero,
				endcomplemento AS endereco_complemento,
				endcidade AS endereco_cidade,
				enduf AS endereco_uf,
				endcep AS endereco_cep,
				endbairro AS endereco_bairro,
				(SELECT COUNT(t.titoid) FROM titulo t WHERE t.titnfloid = nfloid) AS numero_parcelas,
				titdt_vencimento AS data_vencimento_titulo,
				titoid AS titulo_id,
				titvl_titulo AS titulo_valor,
				nfekdt_transmissao_rps AS data_transmissao_rps,
				CASE
					WHEN cliemail_nfe IS NOT NULL THEN cliemail_nfe
					WHEN endemail IS NOT NULL THEN endemail
					ELSE cliemail
				END AS cliemail_nfe,
                cliemail_nfe1,
                cliemail_nfe2,				
				nflinfcomp AS nflinfcomp				
			FROM 
				nota_fiscal
				INNER JOIN titulo ON nfloid = titnfloid
				LEFT JOIN forma_cobranca ON titformacobranca = forcoid
				LEFT JOIN config_banco ON cfbbanco = forccfbbanco
				INNER JOIN clientes ON nflclioid = clioid
				LEFT JOIN endereco ON cliend_cobr = endoid
				LEFT JOIN nota_fiscal_eletronica_kernel ON nfloid = nfeknfloid
			WHERE
				nfloid = $notaFiscalId
			ORDER BY 
				titoid LIMIT 1
		";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Falha ao recuperar informações da nota fiscal.");
		}

		$result = pg_fetch_object($query);

		return $result;
	}

	public function verificarExistenciaNotaFiscalEletronicaKernel($numeroNF, $serieNF)
	{

		$nfiserie = !empty($serieNF) ? "'$serieNF'" : "NULL";

		$sql = "
			SELECT
				nfeknflno_numero
			FROM
				nota_fiscal_eletronica_kernel
			WHERE
				nfeknflno_numero = $numeroNF
				AND
				nfeknflserie = $nfiserie
		";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Falha ao recuperar registro de nota fiscal eletrônica.");
		}

		return !!pg_num_rows($query);
	}

	public function inserirNotaFiscalEletronicaKernel($notaFiscalId, $codigoEmpresa, $codigoUsuario, $numeroNF, $serieNF)
	{

		$nfiserie = !empty($serieNF) ? "'$serieNF'" : "NULL";

		$sql = "
			INSERT INTO
				nota_fiscal_eletronica_kernel(
					nfeknfloid,
					nfektecoid,
					nfekdt_transmissao_rps,
					nfekusuario_envio_rps,
					nfeknflno_numero,
					nfeknflserie
				)
			VALUES
				(
					$notaFiscalId,
					$codigoEmpresa,
					NOW(),
					$codigoUsuario,
					$numeroNF,
					$nfiserie
				)
		";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Falha ao atualizar informações da nota fiscal.");
		}

		$result = pg_fetch_object($query);

		return $result;
	}

	public function atualizarNotaFiscalEletronicaKernel($codigoEmpresa, $codigoUsuario, $numeroNF, $serieNF)
	{
		$nfiserie = !empty($serieNF) ? "'$serieNF'" : "NULL";

		$sql = "
			UPDATE
				nota_fiscal_eletronica_kernel
			SET
				nfektecoid = $codigoEmpresa,
				nfekdt_transmissao_rps = NOW(),
				nfekusuario_envio_rps = $codigoUsuario,
				nfekno_nfe = null
			WHERE
				nfeknflno_numero = $numeroNF
				AND
				nfeknflserie = $nfiserie
		";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Falha ao atualizar informações da nota fiscal.");
		}

		$result = pg_fetch_object($query);

		return $result;
	}

	public function getIdEmpresaAutenticada($autenticacaoSistemaUsuarios){

		$sql = "
			SELECT
				tecoid
			FROM
				tectran
			WHERE
				tecurl_sistema = '$autenticacaoSistemaUsuarios'
			";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Falha ao obter informações do usuário logado.");
		}

		return pg_num_rows($query) > 0 ? pg_fetch_result($query, 0, 'tecoid') : null;

	}

    public function isNFProcessada($numeroRPS, $serieRPS){

    	$nfiserie = !empty($serieRPS) ? "'$serieRPS'" : "NULL";

        $sql = "SELECT nfekno_nfe 
                FROM nota_fiscal_eletronica_kernel 
                WHERE nfeknflno_numero = $numeroRPS AND nfeknflserie = $nfiserie";
		
        if(!$res = pg_query($this->conn, $sql)){
            throw new exception('Erro: Não foi possível encontrar uma nota fiscal');
        }
		if(pg_num_rows($res) > 0){
			if(pg_fetch_result($res, 0, "nfekno_nfe") != "" || pg_fetch_result($res, 0, "nfekno_nfe") != null){
				return true;
			} else {
				return false;
			}
		}else{
			return false;
		}
        
    }

    public function atualizarCamposNfe($numeroNfe, $link_nfe, $numeroRPS, $serieRPS){
        
    	$nfiserie = !empty($serieRPS) ? "'$serieRPS'" : "NULL";

        $sql = "UPDATE nota_fiscal_eletronica_kernel 
                    SET
                    nfekno_nfe = $numeroNfe,
                    nfeklink_nfe = '$link_nfe',
                    nfekdt_retorno_rps = NOW()
                WHERE nfeknflno_numero = $numeroRPS AND nfeknflserie = $nfiserie";
        
        if(!$res = pg_query($this->conn, $sql)){
            throw new exception("Erro ao processar arquivo. Tente Novamente.");
        }

	}

	public function atualizarDataRetornoRps($numeroRPS, $serieRPS){
        
		$nfiserie = !empty($serieRPS) ? "'$serieRPS'" : "NULL";

        $sql = "UPDATE nota_fiscal_eletronica_kernel 
                    SET
                    nfekdt_retorno_rps = NOW()
                WHERE nfeknflno_numero = $numeroRPS AND nfeknflserie = $nfiserie";
        
        if(!$res = pg_query($this->conn, $sql)){
            throw new exception("Erro ao processar arquivo. Tente Novamente.");
        }

	}

	public function buscarTipoContrato($numeroNF, $serieNF)
	{

		$nfiserie = !empty($serieNF) ? "'$serieNF'" : "NULL";

		$sql = "
			SELECT
				connumero AS numero_contrato, 
				prptppoid AS tipo_proposta,
				tppoid_supertipo AS super_tipo_contrato
			FROM
				nota_fiscal_item
			INNER JOIN contrato ON connumero = nficonoid
			INNER JOIN proposta ON prptermo = nficonoid
			LEFT JOIN tipo_proposta ON prptppoid = tppoid
			WHERE
				nfino_numero = $numeroNF
				AND nfiserie = $nfiserie
			GROUP BY 
				connumero, prptppoid, tppoid_supertipo
		";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Erro ao buscar tipo do contrato da nota fiscal.");
		}

		return pg_num_rows($query) > 0 ? pg_fetch_all($query) : array();
	}

	public function getIdTabelaErroByCodigoErro($codigoErro){
		$sql = "SELECT rbceoid FROM rps_barueri_codigo_erro WHERE rbcecodigo = '$codigoErro'";

		if(!$res = pg_query($this->conn, $sql)){
            throw new exception("Erro ao recuperar id erro.");
		}
		
		if(pg_num_rows($res) > 0){
			$id = pg_fetch_result($res, 0, "rbceoid");
			if($id != "" || $id != null){
				return (int)$id;
			} else {
				return null;
			}
		} else {
			return null;
		}		
	}

	public function getDescricaoErroByCodigoErro($codigoErro){
		$sql = "SELECT rbcemensagem FROM rps_barueri_codigo_erro WHERE rbcecodigo = '$codigoErro'";

		if(!$res = pg_query($this->conn, $sql)){
            throw new exception("Erro ao recuperar id erro.");
		}
		
		if(pg_num_rows($res) > 0){
			$mensagem = pg_fetch_result($res, 0, "rbcemensagem");
			if($mensagem != "" || $mensagem != null){
				return $mensagem;
			} else {
				return null;
			}
		} else {
			return null;
		}		
	}

	public function getIdNotaFiscal($numeroNfe, $serieNfe){

		$nfiserie = !empty($serieNfe) ? "'$serieNfe'" : "NULL";

        $sql = "SELECT nfloid
                FROM nota_fiscal
                WHERE nflno_numero = $numeroNfe AND nflserie = $nfiserie";
		
        if(!$res = pg_query($this->conn, $sql)){
			throw new exception('Erro: Não foi possível encontrar um nota fiscal');
        }
        
        if(pg_num_rows($res) > 0){
            return pg_fetch_result($res, 0, 'nfloid');
		}else{
            return null;
        }

	}
	
	public function registrarOcorrencia($idCodigoErro, $idNotaFiscal, $codigoOrigem){

		$sql = "INSERT INTO rps_barueri_ocorrencia (rbocnfloid, rbocrbceoid, rbocdt_inclusao, rbocorigem) 
				VALUES ($idNotaFiscal, $idCodigoErro, NOW(), $codigoOrigem);";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Falha ao atualizar as ocorrências do rps.");
		}

		$result = pg_fetch_object($query);
		return $result;
	}

	public function getNossoNumero($tituloId){

		$codigosMovimentoRegistrado = ParametroCobrancaRegistrada::getCodigosMovimentoRegistrado();
		$stringCodigoMovimentoRegistrados = implode(", ", array_map(function($codigo){ return "'$codigo'"; }, $codigosMovimentoRegistrado));

		$sql = "
			SELECT
				tbrenosso_numero
			FROM
				titulo_boleto_registro
			WHERE
				tbrecd_movimento IN ({$stringCodigoMovimentoRegistrados})
			AND tbretitoid = $tituloId
			ORDER BY tbreoid ASC
			LIMIT 1
			";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Falha ao obter nosso número do boleto.");
		}

		return pg_num_rows($query) > 0 ? pg_fetch_result($query, 0, 'tbrenosso_numero') : null;

	}

	public function liberarRegistroParaRPS($numeroRPS, $serieRPS){

		$nfiserie = !empty($serieRPS) ? "'$serieRPS'" : "NULL";

		$sql = "DELETE FROM nota_fiscal_eletronica_kernel
				WHERE nfeknflno_numero = $numeroRPS AND nfeknflserie = $nfiserie";
		
		if(!$res = pg_query($this->conn, $sql)){
            throw new exception("Erro ao liberar registro para o RPS. Tente Novamente.");
        }
	}

	public function getUltimaDataEmissao()
	{

		$sql = "
			SELECT
				DATE(MAX(nfldt_emissao)) AS ultima_data_emissao
			FROM
				nota_fiscal
			WHERE
				nfldt_emissao IS NOT NULL
				AND nflserie in ('A','SB','V') LIMIT 1
		";

        if(!$query = pg_query($this->conn, $sql)){
			throw new exception("Erro na verificação da data de emissão.");
        }

        return pg_num_rows($query) > 0 ? pg_fetch_result($query, 0, 'ultima_data_emissao') : null;

	}

	public function isUltimaDataEmissaoMaiorQueDataEmissaoInformada($dataEmissaoInformada)
	{

		$sql = "
			SELECT
				CASE WHEN
					DATE(MAX(nfldt_emissao)) > DATE('$dataEmissaoInformada')
				THEN
					'TRUE'
				ELSE
					'FALSE'
				END AS verificacao
			FROM
				nota_fiscal
			WHERE
				nfldt_emissao IS NOT NULL
				AND nflserie in ('A','SB','V') LIMIT 1
		";

        if(!$query = pg_query($this->conn, $sql)){
			throw new exception("Erro na verificação da data de emissão.");
        }

        $res = pg_num_rows($query) > 0 ? pg_fetch_result($query, 0, 'verificacao') : null;

        return $res == 'TRUE';

	}

	public function atualizarDataEmissao($notaFiscalId, $dataEmissao = null)
	{

		$dataEmissao = is_null($dataEmissao) ? "nfldt_nota" : "'$dataEmissao'";

		$sql = "
			UPDATE
				nota_fiscal
			SET
				nfldt_emissao = {$dataEmissao}
			WHERE
				nfloid = {$notaFiscalId}
		";

		if(!$res = pg_query($this->conn, $sql)){
            throw new exception("Erro ao atualizar a data de emissao da nota fiscal.");
        }

        return !!pg_num_rows($res);

	}


	public function pesquisarOcorrencias(
		$periodoFaturamentoInicial,
		$periodoFaturamentoFinal,
		$clienteNome,
		$numeroCpfCnpj,
		$numeroNf
	){
		$sql = "
			SELECT 
				nfloid AS id_nf,
				nflno_numero AS numero_nf,
				nflserie AS serie_nf,
				clinome AS nome_cliente,
				CASE WHEN (clino_cgc IS NOT NULL) THEN
					clino_cgc
				ELSE
					clino_cpf
				END AS cpf_cnpj,
				nflvl_total AS valor_nf,
				nfldt_faturamento AS data_faturamento,
				nfldt_cancelamento AS data_cancelamento,
				nfekno_nfe AS numero_nfe,
				nfekdt_transmissao_rps AS data_transmissao_rps,
				nfekdt_retorno_rps AS data_retorno_rps,
				string_agg(DISTINCT CONCAT(rbcecodigo,' - ',rbcemensagem), '<br/>') AS ocorrencias
			FROM
				nota_fiscal				
			INNER JOIN 
				rps_barueri_ocorrencia ON nfloid=rbocnfloid
			INNER JOIN 
				rps_barueri_codigo_erro ON rbocrbceoid=rbceoid
			LEFT JOIN
				nota_fiscal_eletronica_kernel ON nfloid = nfeknfloid
			LEFT JOIN
				clientes ON nflclioid = clioid
		";

		$where = array();

		if(!empty($periodoFaturamentoInicial)){
			$where[] = "nfldt_faturamento >= '$periodoFaturamentoInicial'";
		}

		if(!empty($periodoFaturamentoFinal)){
			$where[] = "nfldt_faturamento <= '$periodoFaturamentoFinal'";
		}

		if(!empty($numeroCpfCnpj)){
			$where[] = "(clino_cpf = '$numeroCpfCnpj' OR clino_cgc = '$numeroCpfCnpj')";
		}

		if(!empty($numeroNf)){
			$where[] = "nflno_numero = $numeroNf";
		}

		if(!empty($clienteNome)){
			$where[] = "clinome ILIKE '%$clienteNome%'";
		}

		if(!empty($where)){
			$sql .= "WHERE ". implode(" AND ", $where);
		}

		$sql .= " GROUP BY nfloid, clioid, nfeknfloid";
		$sql .= " ORDER BY nfloid DESC";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Falha ao recuperar informações das notas fiscais.");
		}

		$result = pg_fetch_all($query);

		return $result;

	}

	public function liberarRPS($notasFiscais)
	{
		$sql = "
			UPDATE 
				nota_fiscal_eletronica_kernel 
			SET
				nfekdt_transmissao_rps = null, 
				nfekdt_retorno_rps = null, 
				nfekno_nfe = null
			WHERE 
				nfeknfloid IN(".implode(',', $notasFiscais).")
		";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Falha ao liberar RPS para reenvio.");
		}

		$result = pg_fetch_object($query);

		return $result;
	}

	public function removerOcorrencias($notasFiscais)
	{
		$sql = "
			DELETE FROM 
				rps_barueri_ocorrencia 
			WHERE 
				rbocnfloid IN(".implode(',', $notasFiscais).")
		";

		if(!$query = pg_query($this->conn, $sql)){
			throw new Exception("Falha ao remover ocorrências.");
		}

		$result = pg_fetch_object($query);

		return $result;
	}
}
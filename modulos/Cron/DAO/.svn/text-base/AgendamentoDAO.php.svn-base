<?php
require_once _MODULEDIR_ . 'Cron/DAO/CronDAO.php';

/**
 * AgendamentoDAO.php
 *
 * Classe de persistência dos dados de Agendamento
 *
 * @author	Alex Sandro Médice <alex.medice@meta.com.br>
 * @since   20/11/2012
 * @package Cron
 */
class AgendamentoDAO extends CronDAO {

	const USUARIO_ENVIO_AUTOMATICO 		= 4873;
	const STATUS_AUTORIZADO				= 4;
	const STATUS_CLIENTE_NAO_LOCALIZADO = 16;
	const STATUS_VEICULO_INDISPONIVEL	= 105;
	const STATUS_EMAIL_INSUCESSO_1 		= 107;
	const STATUS_EMAIL_INSUCESSO_2 		= 108;
	const STATUS_EMAIL_INSUCESSO_3 		= 109;
	const MESES_RETROATIVOS 			= 6;
	const MIN_TENTATIVAS 				= 3;
	const MAX_TENTATIVAS 				= 5;
	const TIPO_PROPOSTA_1 				= 12;
	const TIPO_PROPOSTA_2				= 14;
    const STATUS_CLIENTE_NAO_LOCALIZADO_DISCADOR = 101;

    public function __construct($conn) {
       parent::__construct($conn);
    }

    public function contatosComInsucessos($date) {

    	$sql = "
    		SELECT * FROM (
				SELECT
    				DISTINCT ON (os.ordoid)
	    			clioid,
	    			os.ordoid 		AS ordem,
    				prptpcoid 		AS contrato_tipo,
    				prptppoid		AS proposta_tipo,
    				tppoid_supertipo AS proposta_supertipo,
	    			clinome 		AS cliente,
	    			cliemail_nfe 	AS email1,
	    			cliemail 		AS email2,
	    			connumero 		AS contrato,
	    			veiplaca 		AS placa,
	    			veichassi 		AS chassi,
	    			mlomodelo 		AS modelo,
	    			(SELECT orsdt_situacao FROM ordem_situacao z WHERE z.orsordoid = os.ordoid ORDER BY orsdt_situacao DESC LIMIT 1) AS ultcontato,
                    (SELECT ostdescricao FROM ordem_servico_item INNER JOIN os_tipo_item ON ositotioid = otioid INNER JOIN os_tipo ON ostoid = otiostoid WHERE ositordoid = os.ordoid AND ositexclusao IS NULL AND ositstatus NOT IN ('X','N') LIMIT 1) AS ordemservico_tipo,
					(SELECT COUNT(*) FROM ordem_situacao WHERE orsordoid = os.ordoid AND orsdt_situacao >= orsdt_situacao - INTERVAL '".self::MESES_RETROATIVOS." month'
                        AND
                            orsstatus IN(
                                        ".self::STATUS_CLIENTE_NAO_LOCALIZADO.",
                                        ".self::STATUS_CLIENTE_NAO_LOCALIZADO_DISCADOR.",
                                        ".self::STATUS_VEICULO_INDISPONIVEL."
                                        )
                    ) AS qtdtentativas
				FROM ordem_situacao
				INNER JOIN ordem_servico os ON os.ordoid = orsordoid
				INNER JOIN clientes ON clioid = ordclioid
				INNER JOIN contrato ON connumero = ordconnumero
				INNER JOIN proposta ON prptermo = ordconnumero
				LEFT JOIN tipo_proposta ON tppoid = prptppoid
				INNER JOIN veiculo ON veioid = conveioid
				INNER JOIN modelo ON mlooid = veimlooid
				WHERE ordstatus = ".self::STATUS_AUTORIZADO."
                --AND CAST(orsdt_situacao AS DATE) = to_date('".$date."', 'YYYY-MM-DD')                
                AND orsdt_situacao::date = '".$date."'
				AND NOT EXISTS (SELECT otiostoid FROM ordem_servico_item INNER JOIN os_tipo_item ON ositotioid = otioid WHERE ositordoid = os.ordoid AND otiostoid = 1 LIMIT 1)
				AND NOT EXISTS (SELECT z.orsordoid FROM ordem_situacao z WHERE z.orsordoid = os.ordoid AND z.orsstatus = ".self::STATUS_EMAIL_INSUCESSO_3." LIMIT 1)
				AND (SELECT z.orsstatus FROM ordem_situacao z WHERE z.orsordoid = os.ordoid ORDER BY orsdt_situacao DESC LIMIT 1)
                    IN(
                        ".self::STATUS_CLIENTE_NAO_LOCALIZADO.",
                        ".self::STATUS_CLIENTE_NAO_LOCALIZADO_DISCADOR.",
                        ".self::STATUS_VEICULO_INDISPONIVEL."
                        )
				) qtdtentativas
			WHERE qtdtentativas BETWEEN ".self::MIN_TENTATIVAS." AND ".self::MAX_TENTATIVAS."
	   	";

    	$rs = $this->query($sql);

    	$rows = array();
    	while($row = pg_fetch_object($rs)) {
    		$rows[$row->clioid][] = new CronVO($row);
    	}

    	return $rows;
    }

	public function contatosComInsucessosSiggo($date) {

    	$sql = "
    		SELECT * FROM (
				SELECT
    				DISTINCT ON (os.ordoid)
	    			clioid,
	    			os.ordoid 		AS ordem,
    				prptpcoid 		AS contrato_tipo,
    				prptppoid		AS proposta_tipo,
    				tppoid_supertipo AS proposta_supertipo,
	    			clinome 		AS cliente,
	    			cliemail_nfe 	AS email1,
	    			cliemail 		AS email2,
	    			connumero 		AS contrato,
	    			veiplaca 		AS placa,
	    			veichassi 		AS chassi,
	    			mlomodelo 		AS modelo,
	    			(SELECT orsdt_situacao FROM ordem_situacao z WHERE z.orsordoid = os.ordoid ORDER BY orsdt_situacao DESC LIMIT 1) AS ultcontato,
	    			--(SELECT ostdescricao FROM ordem_servico_item INNER JOIN os_tipo_item ON ositotioid = otioid INNER JOIN os_tipo ON ostoid = otiostoid WHERE ositordoid = os.ordoid AND otiostoid = 1  LIMIT 1) AS ordemservico_tipo,
	    			(SELECT ostdescricao FROM ordem_servico_item INNER JOIN os_tipo_item ON ositotioid = otioid INNER JOIN os_tipo ON ostoid = otiostoid WHERE ositordoid = os.ordoid AND ositexclusao IS NULL AND ositstatus NOT IN ('X','N') LIMIT 1) AS ordemservico_tipo,
					(SELECT COUNT(*) FROM ordem_situacao WHERE orsordoid = os.ordoid AND orsdt_situacao >= orsdt_situacao - INTERVAL '".self::MESES_RETROATIVOS." month'
                        AND
                            orsstatus IN(
                                        ".self::STATUS_CLIENTE_NAO_LOCALIZADO.",
                                        ".self::STATUS_CLIENTE_NAO_LOCALIZADO_DISCADOR.",
                                        ".self::STATUS_VEICULO_INDISPONIVEL."
                                        )
                    ) AS qtdtentativas
				FROM ordem_situacao
				INNER JOIN ordem_servico os ON os.ordoid = orsordoid
				INNER JOIN clientes ON clioid = ordclioid
				INNER JOIN contrato ON connumero = ordconnumero
				INNER JOIN proposta ON prptermo = ordconnumero
				LEFT JOIN tipo_proposta ON tppoid = prptppoid
				INNER JOIN veiculo ON veioid = conveioid
				INNER JOIN modelo ON mlooid = veimlooid
				WHERE ordstatus = ".self::STATUS_AUTORIZADO."
                --AND CAST(orsdt_situacao AS DATE) = to_date('".$date."', 'YYYY-MM-DD')]
                AND orsdt_situacao::DATE = '".$date."'                
				AND EXISTS (SELECT otiostoid FROM ordem_servico_item INNER JOIN os_tipo_item ON ositotioid = otioid WHERE ositordoid = os.ordoid AND otiostoid = 1 LIMIT 1)
				AND NOT EXISTS (SELECT z.orsordoid FROM ordem_situacao z WHERE z.orsordoid = os.ordoid AND z.orsstatus = ".self::STATUS_EMAIL_INSUCESSO_3." LIMIT 1)
				AND (SELECT z.orsstatus FROM ordem_situacao z WHERE z.orsordoid = os.ordoid ORDER BY orsdt_situacao DESC LIMIT 1)
                    IN(
                        ".self::STATUS_CLIENTE_NAO_LOCALIZADO.",
                        ".self::STATUS_CLIENTE_NAO_LOCALIZADO_DISCADOR.",
                        ".self::STATUS_VEICULO_INDISPONIVEL."
                        )
				AND prptppoid in (".self::TIPO_PROPOSTA_1.",".self::TIPO_PROPOSTA_2.")
				) qtdtentativas
			WHERE qtdtentativas BETWEEN ".self::MIN_TENTATIVAS." AND ".self::MAX_TENTATIVAS."
	   	";

    	$rs = $this->query($sql);

    	$rows = array();
    	while($row = pg_fetch_object($rs)) {
    		$rows[$row->clioid][] = new CronVO($row);
    	}

    	return $rows;
    }

    public function novaSituacaoOs(OrdemSituacaoVO $ordemSituacaoVO) {

    	$ordemSituacaoVO = $this->escape($ordemSituacaoVO);

    	if (empty($ordemSituacaoVO->orsordoid)) {
    		throw new ExceptionDAO('ID da OS não informado');
    	}
    	if (empty($ordemSituacaoVO->orsstatus)) {
    		throw new ExceptionDAO('Status da situação não informado');
    	}
    	if (empty($ordemSituacaoVO->orssituacao)) {
    		throw new ExceptionDAO('Texto da situação da OS não informado');
    	}

    	$sql = "
    		INSERT INTO ordem_situacao
    			(orsordoid, orsstatus, orsusuoid, orssituacao, orsdt_situacao)
    		VALUES
    			(".$ordemSituacaoVO->orsordoid.", ".$ordemSituacaoVO->orsstatus.", ".self::USUARIO_ENVIO_AUTOMATICO.", '".$ordemSituacaoVO->orssituacao."', NOW())
    	";

    	$this->query($sql);
    }

    public function novoLogEnvioEmail(LogEnvioEmailVO $logEnvioEmailVO) {

    	$logEnvioEmailVO = $this->escape($logEnvioEmailVO);

    	if (empty($logEnvioEmailVO->leeordoid)) {
    		throw new ExceptionDAO('ID da OS não informado');
    	}
    	if (empty($logEnvioEmailVO->leesseoid)) {
    		throw new ExceptionDAO('Tipo de email não informado');
    	}
    	if (empty($logEnvioEmailVO->leetipo_log)) {
    		throw new ExceptionDAO('Tipo de LOG não informado');
    	}
    	if (empty($logEnvioEmailVO->leeobs)) {
    		throw new ExceptionDAO('Texto do LOG não informado');
    	}

    	$sql = "
    		INSERT INTO log_envio_email
    			(leeordoid, leesseoid, leetipo_log, leeobs, leedt_envio)
    		VALUES
    			(".$logEnvioEmailVO->leeordoid.", ".$logEnvioEmailVO->leesseoid.", '".$logEnvioEmailVO->leetipo_log."', '".$logEnvioEmailVO->leeobs."', NOW())
    	";

    	$this->query($sql);
    }

    public function getLayoutEmail($tipo) {
    	$tipo = pg_escape_string($tipo);

    	$sql = "SELECT * FROM servico_envio_email WHERE seeoid = " . $tipo;

    	$rs = $this->query($sql);

    	return pg_fetch_object($rs);
    }

    public function getLocalizadorServidor($servidor = null){

    	if (!empty($servidor)){

    		$sql = "
    				SELECT
    					srvlocalizador
    				FROM
    					servidor_email
    				WHERE
    					srvoid = $servidor
    		";

    		$rs = $this->query($sql);

    		$localizador = pg_fetch_object($rs);

    		//echo "<pre>";
    		//print_r ($localizador);exit;

    		return $localizador->srvlocalizador;
	    }

    }

    public function getTituloFuncionalidade($titulo){

    	$sql = "
		    	SELECT
		    		seetoid AS titulo_id, seetseefoid AS funcionalidade_id
		    	FROM
		    		servico_envio_email_titulo
		    	WHERE
		    		seetdescricao = '".$titulo."';
    			";

    	$rs = $this->query($sql);

    	return pg_fetch_object($rs);

    }


    public function getIdPropostaOS($ordoid){

        $sql =" SELECT prpoid
                  FROM proposta
            INNER JOIN contrato ON connumero = prptermo
            INNER JOIN ordem_servico ON connumero = ordconnumero
                 WHERE ordoid = $ordoid  ";

        $rs = $this->query($sql);

        return pg_fetch_object($rs);

    }


    public function registrarHistoricoProposta($texto, $prpoid, $id_usuario){

         // Insere histórico
        $sql = "INSERT INTO proposta_historico (prphprpoid, prphusuoid, prphobs)
                VALUES ($prpoid,  $id_usuario, '" . nl2br($texto) . "') ";

        if (!$result = pg_query($this->conn, $sql)) {
        	throw new Exception ("Falha ao inserir historico da proposta");
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
}
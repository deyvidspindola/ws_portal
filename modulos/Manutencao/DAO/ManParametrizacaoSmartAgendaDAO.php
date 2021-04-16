<?php

/**
 * Classe ManParametrizacaoSmartAgendaDAO.
 * Camada de modelagem de dados.
 *
 * @package  Manutencao
 * @author   ANDRE LUIZ ZILZ <andre.zilz@sascar.com.br>
 *
 */
class ManParametrizacaoSmartAgendaDAO {

	private $conn;
	private $usuarioLogado;

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	public function __construct($conn) {

        $this->conn = $conn;
        $this->usuarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

	}
	public function getUsuarioLogado(){
		return $this->usuarioLogado;
	}

	public function getListaStatusItem() {

		$lista = array(
                            'A' => 'Autorizado',
                            'X' => 'Cancelado',
                            'C' => 'Concluido',
                            'N' => 'Não Autorizado',
                            'E' => 'Não Executado',
                            'P' => 'Pendente'  ) ;
		return $lista;
	}


	public function getListaParametros() {

		$parametros = array(
							'STATUS_ITEM_OS',
							'DURACAO_PADRAO_ATIVIDADE_OFSC',
							'CONSIDERA_TEMPO_ATIVIDADE_OFSC',
							'FATOR_CALCULO_TEMPO_PESO',
							'SEMANAS_LIMITE_PESQUISA',
							'SEMANAS_CALENDARIO',
							'STATUS_OS_PESQUISA',
							'PERIODO_DZERO_MANHA',
							'PERIODO_DZERO_TARDE',
							'PERIODO_DZERO_NOITE',
							'REPOID_SOLICITACAO_FALSA',
							'TEMPO_PREPARACAO_REMESSA',
							'TEMPO_RECEBIMENTO_REMESSA',
							'ANTECIPACAO_RESERVA_MATERIAL');

		return $parametros;
	}

	public function pesquisar(){

		$retorno = array();

		$sql = "SELECT
					pcsioid,
					pcsidescricao,
					pcsiobservacao
				FROM
					parametros_configuracoes_sistemas_itens
				WHERE
                    pcsipcsoid = 'SMART_AGENDA'
                AND
                	pcsioid IN ('" . implode("','", $this->getListaParametros() ) . "')
                AND
					pcsidt_exclusao IS NULL";

		$rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)){

            if( $registro->pcsioid == 'PERIODO_DZERO_MANHA'
                || $registro->pcsioid == 'PERIODO_DZERO_TARDE'
                || $registro->pcsioid == 'PERIODO_DZERO_NOITE' ){

                $periodo = explode(';',$registro->pcsidescricao);
                $retorno[$registro->pcsioid . '_INICIO']['valor']   = $periodo[0];
                $retorno[$registro->pcsioid . '_FIM']['valor']      = $periodo[1];
                $retorno[$registro->pcsioid . '_AGENDA']['valor']   = $periodo[2];
                $retorno[$registro->pcsioid]['valor'] = $registro->pcsidescricao;

            } else if(  $registro->pcsioid == 'STATUS_ITEM_OS' ||  $registro->pcsioid == 'STATUS_OS_PESQUISA') {
            	$retorno[$registro->pcsioid]['valor']   = explode(',',$registro->pcsidescricao);

            } else {
                $retorno[$registro->pcsioid]['valor']  = $registro->pcsidescricao;
            }

            $retorno[$registro->pcsioid]['legenda']    = $registro->pcsiobservacao;

		}

		return $retorno;
	}

	public function pesquisarLog($paginacao = NULL, $ordenacao = NULL){

		$retorno = array();
		$sql = 'SELECT ';

		if (is_null($paginacao)) {
            $sql .= " COUNT(pcsloid) as total ";
        } else {

			$sql .= "
					pcslvalor_original,
					pcslvalor_alterado,
					TO_CHAR(pcsldt_cadastro, 'DD/MM/YYYY HH24:MM') AS pcsldt_cadastro,
					nm_usuario,
					pcslparametro";
		}

		$sql .=	"
				FROM
					parametros_configuracoes_sistemas_log
				INNER JOIN
					usuarios ON (cd_usuario = pcslusuoid_alteracao)
				WHERE
                	pcslparametro IN ('" . implode("','", $this->getListaParametros() ) . "')
                AND
                    pcslparametro_ti IS FALSE";

		if (!is_null($paginacao)) {

            if (!empty($ordenacao)) {
                $sql .=  ' ORDER BY ' . $ordenacao;
            } else {
	        	$sql .=  ' ORDER BY pcsldt_cadastro DESC';
	        }

            $sql .= " LIMIT " . $paginacao->limite . " OFFSET " . $paginacao->offset;
        }

		$rs = $this->executarQuery($sql);

        if (is_null($paginacao)) {
            return pg_fetch_object($rs);
        } else {
            while($registro = pg_fetch_object($rs)){
                $retorno[] = $registro;
            }

            return $retorno;
        }
	}


	public function atualizarParametros($chave, $valor){

		$sql = "UPDATE
					parametros_configuracoes_sistemas_itens
				SET
					pcsidescricao = '" . $valor . "',
					pcsiusoid_cadastro = " . $this->usuarioLogado . "
				WHERE
					pcsipcsoid = 'SMART_AGENDA'
				AND
					pcsioid = '" . $chave ."'" ;

		$rs = $this->executarQuery($sql);

		return true;
	}

	public function gravarLog( $chave, $valorOriginal, $valor ){

		$sql = "INSERT INTO
					parametros_configuracoes_sistemas_log
				(
					pcslparametro,
					pcslvalor_original,
					pcslvalor_alterado,
					pcslusuoid_alteracao
				)
				VALUES
				(
					'". $chave."',
					'". $valorOriginal ."',
					'". $valor ."',
					". $this->usuarioLogado . "
				)" ;

		$rs = $this->executarQuery($sql);

		return true;
	}

	public function recuperarPrestadores() {

		$retorno = array();

		$sql = " SELECT
                repoid,
                repnome
            FROM
                representante
            WHERE
                repexclusao IS NULL
            AND
                (repinstalacao IS TRUE OR repassistencia IS TRUE)
            ORDER BY
                repnome";

        $rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}

		return $retorno;
	}

	public function recuperarStatusOrdemServico() {

		$retorno = array();

		$sql = " SELECT ossoid,
						initcap(ossdescricao) AS ossdescricao
				FROM ordem_Servico_status
				WHERE OSSEXCLUSAO IS NULL
				ORDER BY ossdescricao";

        $rs = $this->executarQuery($sql);

		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		return $retorno;
	}

	public function validarPermissaoPagina(){

        $sql = "SELECT 1
                FROM usuarios
                    JOIN pagina_permissao_cargo ON ppccargooid = usucargooid
                    JOIN pagina_permissao_depto ON ppddepoid = usudepoid
                    JOIN pagina ON pagoid = ppdpagoid AND pagoid = ppcpagoid
                WHERE cd_usuario = ". $this->usuarioLogado ."
                    AND pagurl = 'man_parametrizacao_smart_agenda.php' ";

        $rs = $this->executarQuery($sql);

        if (pg_num_rows($rs) > 0){
            return true;
        } else {
            return false;
        }
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

	private function executarQuery($query) {

        if(!$rs = pg_query($this->conn, $query)) {

            $msgErro = self::MENSAGEM_ERRO_PROCESSAMENTO;

            if( _AMBIENTE_ == 'LOCALHOST' || _AMBIENTE_ == 'DESENVOLVIMENTO' ) {
                $msgErro = "Erro ao processar a query: " . $query;
            }
            throw new ErrorException($msgErro);
        }
        return $rs;
    }
}
?>

<?php

/**
 * Classe TIParametrizacaoSmartAgendaDAO.
 * Camada de modelagem de dados.
 *
 * @package  TI
 * @author   ANDRE LUIZ ZILZ <andre.zilz@sascar.com.br>
 *
 */
class TIParametrizacaoSmartAgendaDAO {

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


	public function getListaParametros() {

        $parametros = array(
                            'VALIDADE_SENHA_SEGUNDOS',
                            'TAMANHO_TIME_SLOT',
                            'LIMPEZA_LOG',
                            'USUARIO_OUTBOUND',
                            'SENHA_OUTBOUND',
                            'LIMITE_REGISTROS_FILA_MENSAGERIA',
                            'USER_TYPE_LOCAL',
                            'REST_GET_ACTIVITY',
                            'REST_UPDATE_ACTIVITY',
                            'REST_CANCEL_ACTIVITY',
                            'REST_DELETE_LINK',
                            'REST_GET_FILE',
                            'REST_GET_TOKEN',
                            'REST_UPDATE_RESOURCE',
                            'REST_CREATE_RESOURCE',
                            'REST_GET_RESOURCE',
                            'REST_GET_LOCATION',
                            'REST_GET_ASSIGNED_LOCATIONS',
                            'REST_CREATE_USER',
                            'REST_UPDATE_USER',
                            'REST_GET_USER',
                            'REST_DELETE_USER');

        switch (_AMBIENTE_ ) {

            case 'PRODUCAO':
                $parametrosDoAmbiente = array(
                                            'PROD_INBOUND',
                                            'PROD_OUTBOUND',
                                            'PROD_CAPACITY',
                                            'PROD_COMPANY',
                                            'PROD_LOGIN',
                                            'PROD_PASSWORD',
                                            'PROD_OFSC_URL',
                                            'PROD_CLIENT_ID',
                                            'PROD_CLIENT_SECRET',
                                            'USER_TYPE_EXTERNO');
                break;
            case 'HOMOLOGACAO':
                $parametrosDoAmbiente = array(
                                            'HOMOLOG_COMPANY',
                                            'HOMOLOG_LOGIN',
                                            'HOMOLOG_PASSWORD',
                                            'HOMOLOG_INBOUND',
                                            'HOMOLOG_OUTBOUND',
                                            'HOMOLOG_CAPACITY',
                                            'HOMOLOG_OFSC_URL',
                                            'HOMOLOG_CLIENT_ID',
                                            'HOMOLOG_CLIENT_SECRET',
                                            'USER_TYPE_TESTE' );
                break;
            case 'TESTE':
                $parametrosDoAmbiente = array(
                                            'TESTE_COMPANY',
                                            'TESTE_LOGIN',
                                            'TESTE_PASSWORD',
                                            'TESTE_INBOUND',
                                            'TESTE_OUTBOUND',
                                            'TESTE_CAPACITY',
                                            'TESTE_OFSC_URL',
                                            'TESTE_CLIENT_ID',
                                            'TESTE_CLIENT_SECRET',
                                            'USER_TYPE_TESTE');
                break;
            default:
                $parametrosDoAmbiente = array(
                                            'DESENV_COMPANY',
                                            'DESENV_LOGIN',
                                            'DESENV_PASSWORD',
                                            'DESENV_INBOUND',
                                            'DESENV_OUTBOUND',
                                            'DESENV_CAPACITY',
                                            'DESENV_OFSC_URL',
                                            'DESENV_CLIENT_ID',
                                            'DESENV_CLIENT_SECRET',
                                            'USER_TYPE_TESTE');
                break;
        }

        $parametros = array_merge($parametros, $parametrosDoAmbiente);

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

            $retorno[$registro->pcsioid]['valor']  = $registro->pcsidescricao;
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
                    pcslparametro_ti IS TRUE";

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
					pcslusuoid_alteracao,
                    pcslparametro_ti
				)
				VALUES
				(
					'". $chave."',
					'". $valorOriginal ."',
					'". $valor ."',
					". $this->usuarioLogado . ",
                    TRUE
				)" ;

		$rs = $this->executarQuery($sql);

		return true;
	}

	public function validarPermissaoPagina(){

        $sql = "SELECT 1
                FROM usuarios
                    JOIN pagina_permissao_cargo ON ppccargooid = usucargooid
                    JOIN pagina_permissao_depto ON ppddepoid = usudepoid
                    JOIN pagina ON pagoid = ppdpagoid AND pagoid = ppcpagoid
                WHERE cd_usuario = ". $this->usuarioLogado ."
                    AND pagurl = 'ti_parametrizacao_smart_agenda.php' ";

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

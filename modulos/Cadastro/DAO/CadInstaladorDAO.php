<?php

/**
 * Classe CadInstaladorDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   LUIZ FERNANDO PONTARA <luiz.pontara.ext@sascar.com.br>
 *
 */
class CadInstaladorDAO {

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
     * Busca dados do Instalador
     * @param  [int] $itloid [ID do Instalador]
     * @return [false / Array]
     */
    public function getInstalador($itloid){

        $sql = "SELECT
                    itloid,
                    itlnome,
                    itlfone,
                    itlno_cpf,
                    itlemail,
                    itladfull,
                    itlcep,
                    itlbairro,
                    itlendereco,
                    itlestado,
                    itlcidade,
                    itlcomplemento,
                    itlfone_sms,
                    repnome
                FROM
                    instalador
                INNER JOIN
                    representante ON (repoid = itlrepoid)
                WHERE
                    itloid = $itloid";

        $result = $this->executarQuery($sql);

        if(pg_num_rows($result) > 0){
            return pg_fetch_object($result);
        }else{
            return false;
        }
    }

    /**
     * Exclui Instalador
     * @param  [int] $itloid [ID do Instalador]
     * @return [bool]
     */
    public function excluirInstalador($itloid){

        $sql = "UPDATE instalador SET itldt_exclusao = NOW() WHERE itloid = $itloid";

        if($this->executarQuery($sql)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * [Verifica se login do instalador existe]
     * @param  [type] $cpf [description]
     * @return [type]      [description]
     */
    public function usuarioInstalador($username) {

        $sql = "SELECT
                    cd_usuario,
                    ds_login,
                    usurefoid
                FROM
                    usuarios
                WHERE
                    ds_login = '".$username."'";

        $result = $this->executarQuery($sql);

        if(pg_num_rows($result) > 0){
            return pg_fetch_all($result);
        }else{
            return false;
        }
    }

    /**
     * Editar usuário
     * @param  [array] $dados [Dados do Representante]
     * @return [bool]
     */
    public function editaUsuario($dados){

        $sql = "UPDATE
                    usuarios
                        SET
                    nm_usuario  = '" . $dados['nm_usuario'] . "',
                    ds_login    = '" . $dados['ds_login']   . "',
                    usurefoid   =  " . $dados['usurefoid']  . ",
                    usuemail    = '" . $dados['usuemail']   . "',
                    dt_exclusao = NULL
                WHERE
                    cd_usuario = " . $dados['cd_usuario'];

        if($this->executarQuery($sql)){
            return true;
        }else{
            return false;
        }
    }

     /**
     * Insere usuario com os dados do instalador
     * @param  [array] $dados [Dados do Representante]
     * @return [bool]
     */
    public function insereUsuario($dados){

        $sqlInsert = "INSERT INTO
                         usuarios(
                            nm_usuario,
                            ds_login,
                            usurefoid,
                            usuemail,
                            usudepoid,
                            usucargooid,
                            usuloginseqad,
                            usuacesso_externo,
                            dt_cadastro
                        ) VALUES (
                            '" . $dados['itlnome']        . "',
                            '" . $dados['ds_login']         . "',
                             " . $dados['repoid']         . ",
                            '" . $dados['itlemail']      . "',
                             " . $dados['usudepoid']      . ",
                             " . $dados['usucargooid']    . ",
                            '" . $dados['usuloginseqad']  . "',
                             " . $dados['usuacesso_externo'] . ",
                             NOW()
                        )RETURNING cd_usuario;";

        if($rs = $this->executarQuery($sqlInsert)){
            if($this->autorizaUsuario(pg_fetch_result($rs,0,'cd_usuario'))) {
                return true;
            }
        }

        return false;

    }

    /**
     * autoriza usuário na intranet
     * @param  [type] $usuario [description]
     * @return [type]          [description]
     */
    public function autorizaUsuario($usuario) {

        global $autenticacaoSistemaUsuarios;

        $sqlInsert = "INSERT INTO
                    usuario_acesso_sistema
                    (
                        uasusuoid,
                        uastecoid,
                        uasusuoid_cadastro
                    )
                    VALUES (" .
                        $usuario. ",
                        (SELECT tecoid FROM tectran WHERE tecurl_sistema =  '" . $autenticacaoSistemaUsuarios . "'),
                        2750
                    );";

        if($this->executarQuery($sqlInsert)){
            return true;
        }

        return false;

    }

    /**
     * Verifica se a integração de usuarios e AD já está concluida para este instalador
     * @param  [int] $repoid [ID do Representante]
     * @return [bool]
     */
    public function integracaoConcluida($itloid){

        $sql = "SELECT itladfull FROM instalador WHERE itloid = ". intval($itloid) ." AND itladfull = true";

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Confirma integração do instalador com AD
     * @param  [int] $repoid [ID do Representante]
     * @return [bool]
     */
    public function concluiIntegracao($itloid){

        $sql = "UPDATE instalador SET itladfull = true WHERE itloid =". intval($itloid);

        if($this->executarQuery($sql)){
            return true;
        }else{
            return false;
        }

    }

    public function existeUsuarioRepresentante($ds_login) {

        $sql = "SELECT EXISTS(
                    SELECT
                       1
                FROM
                    usuarios
                WHERE
                        ds_login = '".$ds_login."'
                AND
                    usudepoid = 9
                    AND
                        dt_exclusao IS NULL
                    ) AS existe";

        $rs = $this->executarQuery($sql);
        $row = pg_fetch_object($rs);

        $isExiste = ($row->existe == 't') ? TRUE : FALSE;

        return $isExiste;
    }

    public function isRepresentanteOFSC( $repoid ) {

        $sql = "SELECT EXISTS(
                     SELECT 1
                FROM
                        representante
                WHERE
                        repoid = ". intval($repoid) ."
                AND
                        repexclusao IS NULL
                AND
                        repofsc IS TRUE
                    ) AS existe";

        $rs = $this->executarQuery($sql);
        $row = pg_fetch_object($rs);

        $isExiste = ($row->existe == 't') ? TRUE : FALSE;

        return $isExiste;
        }

    /**
     * Exclui usuario do Instalador
     * @param  [string] usuario [usuario do Instalador]
     * @return [bool]
     */
    public function excluirUsuario($usuario){

        $sql = "UPDATE usuarios SET dt_exclusao = NOW() WHERE ds_login = '$usuario'";

        if($this->executarQuery($sql)){
            return true;
        }else{
            return false;
        }
    }

     /**
     * Conclui integração com o OFSC
     * @param  [type] $repoid [description]
     * @return [type]         [description]
     */
    public function concluiIntegracaoOracle($itloid) {

        $sql = "UPDATE
                    instalador
                SET itlofsc=TRUE WHERE itloid = ". (int) $itloid;

        $result = $this->executarQuery($sql);
    }

    /**
     * Conclui integração com o OFSC
     * @param  [type] $itloid [description]
     * @return [type]         [description]
     */
    public function cancelaIntegracaoOracle($itloid) {

        $sql = "UPDATE
                    instalador
                SET itlofsc=FALSE WHERE itloid = ". (int) $itloid;

        $result = $this->executarQuery($sql);
    }

    /**
     * Verifica se um determinado instalador já está sincronizado com o OFSC
     * @param  [type] $itloid [description]
     * @return [type]         [description]
     */
    public function instaladorSincronizado($itloid) {
        $sql = "SELECT
                    1
                FROM
                    instalador
                WHERE itlofsc=TRUE  AND itloid = ". $itloid;

        $result = $this->executarQuery($sql);
        if($result && pg_num_rows($result) > 0) {
           return true;
        }

        return false;
    }


	/** Abre a transação */
	public function begin(){
		pg_query($this->conn, 'BEGIN;');
	}

	/** Finaliza um transação */
	public function commit(){
		pg_query($this->conn, 'COMMIT;');
	}

	/** Aborta uma transação */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK;');
	}

	/** Submete uma query a execucao do SGBD */
	private function executarQuery($query) {

        if(!$rs = pg_query($this->conn, $query)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $rs;
    }

    /**
     * cria ponto de salvamento
     * @param  $nome [alias para o savepoint]
     */
    public function savePoint($nome){
        pg_query($this->conn, 'SAVEPOINT ' . $nome);
    }

     /**
     * Aborta ações dentro de um bloco de ponto de salvamento
     * @param  $nome [alias para do savepoint]
     */
    public function rollbackPoint($nome){
        pg_query($this->conn, 'ROLLBACK TO SAVEPOINT ' . $nome);
    }
}
?>

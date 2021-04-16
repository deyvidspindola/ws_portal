<?php

/**
 * Classe CadRepresentanteDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   LUIZ FERNANDO PONTARA <luiz.pontara.ext@sascar.com.br>
 *
 */
class CadRepresentanteDAO {

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
     * Verifica se já existe usuário
     * @param  [type] $usuario [description]
     * @return [type]          [description]
     */
    public function verificaUsuarioExistente($usuario) {

        $sql = "SELECT
                    cd_usuario,
                    ds_login,
                    dt_exclusao
                FROM
                    usuarios
                WHERE
                    ds_login = '" .$usuario. "'";

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0){
            return pg_fetch_object($rs);
        }else{
            return false;
        }
    }

    /**
     * Reativa usuário na base
     * @param  [type] $usuario [description]
     * @return [type]          [description]
     */
    public function reativaUsuario($usuario) {

         $sql = "UPDATE usuarios SET dt_exclusao = NULL WHERE cd_usuario =" . intval($usuario);

        if($this->executarQuery($sql)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Busca dados do Representante
     * @param  [int] $repoid [ID do Representante]
     * @return [false / Array]
     */
    public function getRepresentante($repoid){

        $repoid = (int) $repoid;

        $sql = "SELECT
                    *
                FROM
                    representante
                WHERE
                    repoid = $repoid";

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0){
            return pg_fetch_all($rs);
        }else{
            return false;
        }
    }



    /**
     * Insere usuario com os dados do representante
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
                            '" . $dados['repnome'] . "',
                            '" . $dados['ds_login']       . "',
                             " . $dados['repoid']         . ",
                            '" . $dados['repe_mail']      . "',
                             " . $dados['usudepoid']      . ",
                             " . $dados['usucargooid']    . ",
                            '" . $dados['usuloginseqad']  . "',
                             " . $dados['usuacesso_externo'] . ",
                             NOW()
                        ) RETURNING cd_usuario;";

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
     * Verifica se o Representante possui usuario
     * @param  [int] repoid [ID do Representante]
     * @return [array]
     */
    public function usuarioRepresentante($repoid){

        $sql = "SELECT
                    cd_usuario,
                    ds_login,
                    usurefoid,
                    dt_exclusao
                FROM
                    usuarios
                WHERE
                    usurefoid = $repoid
                AND
                    usudepoid = 9
            ";

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) < 0){
            return false;
        }else{
            return pg_fetch_all($rs);
        }

    }

    /**
     * [existeUsuarioRepresentante description]
     * @param  [string] $ds_login [description]
     * @return [type]           [description]
     */
    public function existeUsuarioRepresentante($ds_login) {
        $sql = "SELECT
                    cd_usuario,
                    ds_login,
                    usurefoid,
                    dt_exclusao
                FROM
                    usuarios
                WHERE
                    ds_login = '".$ds_login."'
                AND
                    usudepoid = 9
                AND
                    dt_exclusao IS NULL
                ";

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) < 0){
            return false;
        }else{
            return pg_fetch_object($rs);
        }
    }

    /**
     * Verifica se o email do representante já existe na tabela de usuarios
     * @param  [string] $email  [email do representante]
     * @return [bool]           [Falso caso exista / True caso não exista]
     */
    public function validaEmail($email){

        $sql = "SELECT 1 FROM usuarios WHERE usuemail = '" . $email . "'";

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0){
            return false;
        }else{
            return true;
        }

    }

    /**
     * Busca os instaladores de um representante
     * @param  [int] $repoid [ID do Representante]
     * @return [false / Array] [Caso não possua instalador retorna false, caso possua retorna instaladores via array]
     */
    public function getInstaladores($repoid){

        $sql = "SELECT
                    itloid,
                    itlnome,
                    itlno_cpf,
                    itlemail,
                    itladfull
                FROM
                    instalador
                WHERE
                    itldt_exclusao IS NULL
                AND
                    itlrepoid = " . intval($repoid);

        $result = $this->executarQuery($sql);

        if(pg_num_rows($result) > 0){
            return pg_fetch_all($result);
        }else{
            return false;
        }
    }

     public function getInstaladoresOFSC($repoid){

        $sql = "SELECT
                    itloid
                FROM
                    instalador
                WHERE
                    itlofsc IS TRUE
                AND
                    itlrepoid = " . intval($repoid);

        $result = $this->executarQuery($sql);

        if(pg_num_rows($result) > 0){
            return pg_fetch_all($result);
        }else{
            return false;
        }
    }


    /**
     * Confirma integração do representante com AD
     * @param  [int] $repoid [ID do Representante]
     * @return [bool]
     */
    public function concluiIntegracao($repoid){

        $sql = "UPDATE representante SET repadfull = true WHERE repoid = $repoid";

        if($this->executarQuery($sql)){
            return true;
        }else{
            return false;
        }

    }

    /**
     * Verifica se a integração de usuarios e AD já está concluida para este representante
     * @param  [int] $repoid [ID do Representante]
     * @return [bool]
     */
    public function integracaoConcluida($repoid){

        $sql = "SELECT repadfull FROM representante WHERE repoid = $repoid AND repadfull = true";

        $rs = $this->executarQuery($sql);

        if(pg_num_rows($rs) > 0){
            return true;
        }else{
            return false;
        }
    }


    /**
     * Exclui Representante
     * @param  [int] $repoid [ID do Representante]
     * @return [bool]
     */
    public function excluirRepresentante($repoid){

        $sql = "UPDATE representante SET repexclusao = NOW() WHERE repoid = $repoid";

        if($this->executarQuery($sql)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Exclui todos os instaladores do representante
     * @param  [int] $repoid [ID do Representante]
     * @return [bool]
     */
    public function excluirInstaladoresRepresenante($repoid){

        $sql = "UPDATE instalador SET itldt_exclusao = NOW() WHERE itlrepoid = $repoid";

        if($this->executarQuery($sql)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Exclui todos os usuarios do representante e instaladores
     * @param  [int] $repoid [ID do Representante]
     * @return [bool]
     */
    public function excluirUsuario($repoid){

        $sql = "UPDATE usuarios SET dt_exclusao = NOW() WHERE usurefoid = $repoid";

        if($this->executarQuery($sql)){
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
    public function concluiIntegracaoInstalador($itloid){

        $sql = "UPDATE instalador SET itladfull = true WHERE itloid =". intval($itloid);

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
    public function usuarioInstalador($user) {

        $sql = "SELECT
                    cd_usuario,
                    ds_login,
                    usurefoid
                FROM
                    usuarios
                WHERE
                    ds_login = '".$user."'";

        $result = $this->executarQuery($sql);

        if(pg_num_rows($result) > 0){
            return pg_fetch_all($result);
        }else{
            return false;
        }
    }

     /**
     * Insere usuario com os dados do instalador
     * @param  [array] $dados [Dados do Representante]
     * @return [bool]
     */
    public function insereUsuarioInstalador($dados){

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
     * Retorna dados do endereco do representante
     * @param  [integer] $repoid [id do representante]
     * @return [type]         [description]
     */
    public function enderecoRepresentante($repoid) {

        $sql = "SELECT
                    endvrua,
                    endvnumero,
                    endvcomplemento,
                    endvbairro,
                    endvcidade,
                    endvcep,
                    endvfone,
                    endvuf,
                    endvddd,
                    endvponto_referencia
                FROM
                    endereco_representante
                WHERE
                    endvrepoid = " . (int) $repoid;

        $result = $this->executarQuery($sql);

        if(pg_num_rows($result) > 0){
            return pg_fetch_object($result);
        }else{
            return false;
        }
    }

    /**
     * Conclui integração com o OFSC
     * @param  [type] $repoid [description]
     * @return [type]         [description]
     */
    public function concluiIntegracaoOracle($repoid) {

        $sql = "UPDATE
                    representante
                SET repofsc=TRUE WHERE repoid = ". (int) $repoid;

        $result = $this->executarQuery($sql);
    }

     /**
     * Conclui integração com o OFSC
     * @param  [type] $repoid [description]
     * @return [type]         [description]
     */
    public function cancelaIntegracaoOracle($repoid) {

        $sql = "UPDATE
                    representante
                SET repofsc=FALSE WHERE repoid = ". (int) $repoid;

        $result = $this->executarQuery($sql);

        $sql = "UPDATE
                    instalador
                SET itlofsc=FALSE WHERE itlrepoid = ". (int) $repoid;

        $result = $this->executarQuery($sql);
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

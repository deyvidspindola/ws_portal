<?php
/**
 * PrnLogAlteracaoSinalGerenciadoraDAO.php
 *
 * DAO
 *
 * @author Rafael Gadotti Bachovas - <rafael.bachovas.ext@sascar.com.br>
 * @package Principal
 * @version 1.0
 * @since 03/07/2017
 */
class PrnLogAlteracaoSinalGerenciadoraDAO{
  private $conn = null;
  
  function __construct(){
    global $conn;
    $this->conn = $conn;
  }
  
  /**
   * Inicia uma transação
   */
  public function beginTransaction(){
		pg_query($this->conn, "BEGIN;");
  }

  /**
   * Confirma uma transação
   */
  public function commitTransaction(){
		pg_query($this->conn, "COMMIT;");
  }
  
  /**
   * Reverte uma transação
   */
  public function rollbackTransaction(){
		pg_query($this->conn, "ROLLBACK;");
  }

  /**
   * Inseri um log do tipo Direcionamento no banco de dados.
   */
  public function inserirLogDirecionamento(
    $idVeiculo,
    $idGerenciadora,
    $idCliente,
    $idEquipamento,
    $prazoDirecionamento,
    $posicaoGerenciadora,
    $ip = null,
    $usuario = null,
    $arrComandos = null
  ){

    try {

      $acao = 'DIRECIONAMENTO';

      $arrData = array();

      $arrData['acao']                = $acao;
      $arrData['idVeiculo']           = $idVeiculo;
      $arrData['idGerenciadora']      = $idGerenciadora;
      $arrData['idCliente']           = $idCliente;
      $arrData['idEquipamento']       = $idEquipamento;
      $arrData['prazoDirecionamento'] = $prazoDirecionamento;
      $arrData['posicaoGerenciadora'] = $posicaoGerenciadora;
      $arrData['ip']                  = $ip;
      $arrData['usuario']             = $usuario;

      $this->beginTransaction();
      $this->inserirLog($arrData, $arrComandos);
      $this->commitTransaction();

    }catch(Exception $e){
      
      $this->rollbackTransaction();

    }

  }

  /**
   * Inseri um log do tipo Retirada no banco de dados.
   */
  public function inserirLogRetirada(
    $idVeiculo,
    $idGerenciadora,
    $idCliente,
    $idEquipamento,
    $posicaoGerenciadora,
    $ip,
    $usuario,
    $arrComandos = null
  ){

    try{

      $acao = 'RETIRADA';

      $arrData = array();

      $arrData['acao']                = $acao;
      $arrData['idVeiculo']           = $idVeiculo;
      $arrData['idGerenciadora']      = $idGerenciadora;
      $arrData['idCliente']           = $idCliente;
      $arrData['idEquipamento']       = $idEquipamento;
      $arrData['posicaoGerenciadora'] = $posicaoGerenciadora;
      $arrData['ip']                  = $ip;
      $arrData['usuario']             = $usuario;

      $this->beginTransaction();
      $this->inserirLog($arrData, $arrComandos);
      $this->commitTransaction();

    }catch(Exception $e){

      $this->rollbackTransaction();

    }

  }

  /**
   * Inseri um log no banco de dados.
   */
  public function inserirLog($arrData, $arrComandos = null){

    if(empty($arrData['acao']) || !in_array($arrData['acao'], array('DIRECIONAMENTO', 'RETIRADA')))
      throw new Exception('Ação inválida');

    if(empty($arrData['idVeiculo']))
      throw new Exception('É necessário informar o ID do veículo.');

    if(empty($arrData['idGerenciadora']))
      throw new Exception('É necessário informar o ID da gerenciadora.');

    if(empty($arrData['idCliente']))
      throw new Exception('É necessário informar o ID do cliente.');

    $acao                     = "'$arrData[acao]'";
    $idCliente                = $arrData['idCliente'];
    $idEquipamento            = !empty($arrData['idEquipamento']) ? $arrData['idEquipamento'] : 'NULL';
    $idVeiculo                = $arrData['idVeiculo'];
    $idGerenciadora           = $arrData['idGerenciadora'];
    $dataPrazoDirecionamento  = !empty($arrData['prazoDirecionamento']) ? "'$arrData[prazoDirecionamento]'" : 'NULL';
    $posicaoGerenciadora      = !empty($arrData['posicaoGerenciadora']) ? $arrData['posicaoGerenciadora'] : 'NULL';
    $ip                       = !empty($arrData['ip']) ? "'$arrData[ip]'" : 'null';
    $usuario                  = !empty($arrData['usuario']) ? "'$arrData[usuario]'" : 'SISTEMA SASCAR';

    $sql = "INSERT INTO
              log_alteracao_sinal_gerenciadora
              (
                lasgacao,
                lasgclioid,
                lasgequoid,
                lasgveioid,
                lasggeroid,
                lasgdt_prazo_direcionamento,
                lasgposicao_gerenciadora,
                lasgip,
                lasgusuario
              )
            VALUES
              (
                $acao,
                $idCliente,
                $idEquipamento,
                $idVeiculo,
                $idGerenciadora,
                $dataPrazoDirecionamento,
                $posicaoGerenciadora,
                $ip,
                $usuario
              )
            RETURNING lasgoid;";

    $query = pg_query($this->conn, $sql);

    if(!$query)
      throw new Exception('Não foi possível efetuar o registro do log de alteração de sinal da gerenciadora.');

    $registro = pg_fetch_object($query);
    $idLogAlteracaoSinalGerenciadora = $registro->lasgoid;

    if(!empty($arrComandos) && is_array($arrComandos)){

      foreach($arrComandos as $logComando){

        $logComando['layout'] = utf8_decode($logComando['layout']);

        if(empty($logComando['id']))
          throw new Exception('É necessário informar o ID log.');

        // if(empty($logComando['layout']) || !in_array($logComando['layout'], array('SEQUENCIAMENTO', 'AÇÃO', 'MACRO', 'ROTOGRAMA')))
        //   throw new Exception('Layout inválido');

        $idComando = $logComando['id'];
        $descricaoLayout = "'$logComando[layout]'";
        $dataExecucao = !empty($logComando['dataExecucao']) ? "'$logComando[dataExecucao]'" : "NULL";
        $dataValidade = !empty($logComando['dataValidade']) ? "'$logComando[dataValidade]'" : "NULL";
        $descricaoStatus = !empty($logComando['status']) ? "'$logComando[status]'" : "'PENDENTE'";

        $sql = "INSERT INTO
                  log_alteracao_sinal_gerenciadora_comandos
                  (
                    lagclasgoid,
                    lagclogcoid,
                    lagcds_layout,
                    lagcdt_execucao,
                    lagcdt_validade,
                    lagcds_status
                  )
                VALUES
                  (
                    $idLogAlteracaoSinalGerenciadora,
                    $idComando,
                    $descricaoLayout,
                    $dataExecucao,
                    $dataValidade,
                    $descricaoStatus
                  )";

        if(!pg_query($this->conn, $sql))
          throw new Exception('Não foi possível efetuar o registro do log de alteração de sinal da gerenciadora.');

      }

    }

    return true;

  }

  /**
   * Seleciona os logs do banco de dados conforme filtro especificado.
   */
  public function selecionarLog(
    $dataInicial = null,
    $dataFinal = null,
    $placaVeiculo = null,
    $veiculo_id = null,
    $idGerenciadora = null,
    $idCliente = null
  ){

    $condicoes = array('1=1');

    $data_inicial = $dataInicial;
    $data_final   = $dataFinal;
    $veiculo      = trim($placaVeiculo);
    $veiculo_id  = trim($veiculo_id);
    $gerenciadora = trim($idGerenciadora);
    $cliente      = trim($idCliente);

    if(!empty($data_inicial) && $data_inicial !== 'null'){
      // $data_inicial = date('Y-m-d', strtotime(date($data_inicial) . ' -1 day'));
      $data_inicial = date('Y-m-d', strtotime(date($data_inicial)));
      $condicoes[] = "lasgdt_solicitacao >= DATE('$data_inicial')";
    }

    if(!empty($data_final) && $data_final !== 'null'){
      $data_final = date('Y-m-d', strtotime(date($data_final) . ' +1 day'));
      // $data_final = date('Y-m-d', strtotime(date($data_final)));
      $condicoes[] = "lasgdt_solicitacao <= DATE('$data_final')";
    }

    if(!empty($veiculo) && $veiculo !== 'null')
      $condicoes[] = "veiplaca = UPPER('$veiculo')";

    if(!empty($veiculo_id) && $veiculo_id !== 'null')
      $condicoes[] = "lasgveioid = $veiculo_id";

    if(!empty($gerenciadora) && $gerenciadora !== 'null' && intval($gerenciadora) !== 0)
      $condicoes[] = "lasggeroid = $gerenciadora";

    if(!empty($cliente) && $cliente !== 'null')
      $condicoes[] = "lasgclioid = $cliente";

    $sql = "
      SELECT
        lasgoid,
        lasgdt_solicitacao AS data_solicitacao,
        lasgacao AS acao,
        lagcdt_execucao AS data_execucao,
        lagcds_layout AS layout,
        lagcds_status AS status,
        lagcdt_validade AS validade,
        veiplaca AS veiculo,
        lasgveioid AS veiculo_id,
        clinome AS cliente,
        lasgequoid AS id_equipamento,
        gernome AS gerenciadora,
        lasgdt_prazo_direcionamento AS prazo_direcionamento,
        lasgip AS ip,
        lasgusuario AS usuario,
        (
          SELECT
            COUNT(lagclasgoid)
          FROM
            log_alteracao_sinal_gerenciadora_comandos
          WHERE
            lagclasgoid = lasgoid
          GROUP BY
            lagclasgoid
        ) AS num_comandos
      FROM
        log_alteracao_sinal_gerenciadora
      LEFT JOIN
        log_alteracao_sinal_gerenciadora_comandos ON lasgoid = lagclasgoid
      LEFT JOIN
        veiculo ON lasgveioid = veioid
      LEFT JOIN
        clientes ON lasgclioid = clioid
      LEFT JOIN
        gerenciadora ON lasggeroid = geroid
      WHERE
        ". implode(' AND ', $condicoes) ."
      ORDER BY
        lasgoid DESC";

    $query = pg_query($this->conn, $sql);

    if(!$query)
      throw new Exception('Não foi possível recuperar o log de alteração de sinal da gerenciadora.');

    $resultado = array();

    while($row = pg_fetch_array($query)){
      $resultado[] = $row;
    }

    return $resultado;

  }

  /**
   * Seleciona os logs de comandos por status.
   */
  public function selecionarLogComadosPorStatus($status){

    $sql = "SELECT
              lagcoid, lagclogcoid
            FROM
              log_alteracao_sinal_gerenciadora_comandos
            WHERE
              lagcds_status = '$status'";

    $query = pg_query($this->conn, $sql);

    if(!$query)
      throw new Exception('Não foi possível recuperar o log de alteração de sinal da gerenciadora.');

    $resultado = array();

    while($row = pg_fetch_array($query)){
      $resultado[] = $row;
    }

    return $resultado;

  }

  /**
   * Seleciona os logs de comandos com status PENDENTE.
   */
  public function selecionarLogComandosStatusPendente(){
    return $this->selecionarLogComadosPorStatus('PENDENTE');
  }

  /**
   * Atualiza um log de comando com status e/ou data de execução e/ou data de validade.
   */
  public function atualizarLogComandos($id, $status = null, $data_execucao = null, $data_validade = null){

    if(empty($status) && empty($data_execucao) && empty($data_validade))
      throw new Exception('Dados inválidos. Não foi possível atualizar o log de comando.');

    /* Atualizar tabela de log com os novos status*/
    $arrUpdateStatement = array();

    if(!empty($status))
      $arrUpdateStatement[] = "lagcds_status = '$status'";

    if(!empty($data_execucao))
      $arrUpdateStatement[] = "lagcdt_execucao = '$data_execucao'";

    if(!empty($data_validade))
      $arrUpdateStatement[] = "lagcdt_validade = '$data_validade'";

    $sql = 'UPDATE
              log_alteracao_sinal_gerenciadora_comandos
            SET
              '. implode(', ', $arrUpdateStatement) .'
            WHERE
              lagcoid = '. $id;

    $query = pg_query($this->conn, $sql);

    if(!$query)
      throw new Exception('Não foi possível atualizar o log de comando.');

    return true;

  }

}
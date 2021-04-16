<?php

/**
 * Classe RelProdutosSolicitadosDAO.
 * Camada de modelagem de dados.
 *
 * @package Relatorio
 * @author  João Paulo Tavares da Silva <joao.silva@meta.com.br>
 *
 */


require_once _MODULEDIR_ . 'Principal/DAO/PrnLogAlteracaoSinalGerenciadoraDAO.php';

class RelDirecionamentoSinalDAO{


    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	private $conn;
    private $usuarioLogado;

	public function __construct(){
		global $conn;
        $this->conn = $conn;
        $this->usuarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
        $this->usuarioLogado = empty($this->usuarioLogado) ? 2750 : intval($this->usuarioLogado);
        $this->daoLog = new PrnLogAlteracaoSinalGerenciadoraDAO();
	}

    public function listarGerenciadoras(){

        $sql = 'SELECT
                    geroid,
                    formata_str(gernome) as descricao
                FROM
                    gerenciadora
                ORDER BY
                    gernome';

        $rs = pg_query($this->conn, $sql);

        $listaItens = array();
        while($row = pg_fetch_object($rs)) {

            $listaItens[] = $row;

            $i++;
        }

        return $listaItens;

    }

    public function getLogDirecionamentoSinal($data_inicial = null, $data_final = null, $veiculo = null, $veiculo_id = null, $gerenciadora = null, $cliente = null){

        return $this->daoLog->selecionarLog($data_inicial, $data_final, $veiculo, $veiculo_id, $gerenciadora, $cliente);

    }

    public function localizarClienteByVeiculoId($id_veiculo){

        $id_veiculo = intval($id_veiculo);

        $sql = "SELECT 
                    clioid, clinome
                FROM contrato
                    LEFT JOIN veiculo ON conveioid = veioid
                    LEFT JOIN clientes ON conclioid = clioid
                WHERE
                    conveioid = $id_veiculo
                LIMIT 1";


        $rs = pg_query($this->conn, $sql);

        $listaItens = array();
        while($row = pg_fetch_object($rs)) {

            $listaItens[] = $row;

            $i++;
        }

        return $listaItens;

    }

    public function localizarClienteByPlacaVeiculo($placa_veiculo){

        $sql = "SELECT 
                    clioid, clinome
                FROM contrato
                    LEFT JOIN veiculo ON conveioid = veioid
                    LEFT JOIN clientes ON conclioid = clioid
                WHERE
                    veiplaca = UPPER('$placa_veiculo')
                LIMIT 1";


        $rs = pg_query($this->conn, $sql);

        $listaItens = array();
        while($row = pg_fetch_object($rs)) {

            $listaItens[] = $row;

            $i++;
        }

        return $listaItens;


    }

    public function localizarCliente($termo){

        $sql = 'SELECT
                    clioid, clinome
                FROM
                    clientes
                WHERE
                    LOWER(clinome) LIKE LOWER(\'%'. $termo .'%\') 
                LIMIT 10';

        $rs = pg_query($this->conn, $sql);

        $listaItens = array();
        while($row = pg_fetch_object($rs)) {

            $listaItens[] = $row;

            $i++;
        }

        return $listaItens;


    }

    public function getNomeCliente($id){

        $sql = 'SELECT
                    clinome
                FROM
                    clientes
                WHERE
                    clioid = '. $id .' 
                LIMIT 1';

        $rs = pg_query($this->conn, $sql);

        $listaItens = array();
        while($row = pg_fetch_object($rs)) {

            $listaItens[] = $row;

            $i++;
        }

        if(count($listaItens) > 0)
            return $listaItens[0]->clinome;

        return false;

    }

    public function getNomeGerenciadora($id){

        $sql = 'SELECT
                    formata_str(gernome) as gernome
                FROM
                    gerenciadora
                WHERE
                    geroid = '. $id .'
                ORDER BY
                    gernome
                LIMIT 1';

        $rs = pg_query($this->conn, $sql);

        $listaItens = array();
        while($row = pg_fetch_object($rs)) {

            $listaItens[] = $row;

            $i++;
        }

        if(count($listaItens) > 0)
            return $listaItens[0]->gernome;

        return false;

    }

    public function begin(){
        $sql = 'BEGIN;';
        if(!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }

    public function rollback(){
        $sql = 'ROLLBACK;';
        if(!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }
    public function commit(){
        $sql = 'COMMIT;';
        if(!pg_query($this->conn, $sql)){
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }
    }
}
?>
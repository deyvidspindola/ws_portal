<?php

namespace modulos\Cadastro\DAO;

require_once _MODULEDIR_ . 'Commun/DAO/AbstractDAO.php';

use Exception;
use modulos\Commun\DAO\AbstractDAO;

/**
 * 
 * @author alexandre.reczcki
 *
 */
class CadVeiculoRoubadoDAO extends AbstractDAO
{
    
    /**
     * Contrutor recebe definições de configuração do serviço de log
     *
     * @param array $arrayLog
     */
    public function __construct(){}
    
    /**
     * Mensagens de ERRO
     */
    const MENSAGEM_ERRO_NAO_CONEXAO = "NÃO FOI POSSIVEL CONECTAR COM O BANCO DE DADOS";
    
    /**
     * 
     * @param int $veiplaca
     * @return NULL|\stdClass[]|\stdClass
     */
    public function buscarVeiculoPorPlaca($veiplaca){
        try {
            $sqlSelect = "
            SELECT  veioid,
                    veiplaca, 
                    ococonnumero, 
                    CASE WHEN veiroid IS NULL THEN 'N' ELSE 'S' END AS jasinalizado
            FROM veiculo
                LEFT JOIN ocorrencia ON ocoveioid = veioid
                LEFT JOIN veiculo_roubado ON veirveioid = veioid
            WHERE  
                UPPER(veiplaca) ILIKE '%{$veiplaca}%' 
                AND veidt_exclusao IS NULL;
            ";
            
            if(! $rs = $this->executarQuery($this->obterConexaoIntranet(), $sqlSelect)){
                return NULL;
            }
            
            $voList = array();
            if(pg_num_rows($rs) > 0){
                $vo = new \stdClass();
                
                $vo->veioid         = (pg_fetch_result($rs, 0, 'veioid'));
                $vo->veiplaca 	    = (pg_fetch_result($rs, 0, 'veiplaca'));
                $vo->ococonnumero   = (pg_fetch_result($rs, 0, 'ococonnumero'));
                $vo->jasinalizado   = (pg_fetch_result($rs, 0, 'jasinalizado'));
                
                $voList[] = $vo;
                
                return $voList;
            }
            
            return NULL;
            
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    /**
     * 
     * @param int $veioid
     * @param string $observacao
     * @throws \Exception
     * @return boolean
     */
    public function inserirVeiculoRoubado($veioid, $observacao){
        try {
            $sql = "INSERT INTO 
                        veiculo_roubado
                    (
                        veiroid, veirveioid, veirsolicitante
                    ) 
                    VALUES
                    (
                        (select max(veiroid)+1 from veiculo_roubado),
                        {$veioid},
                        '{$observacao}'
                    );";
            pg_query(self::obterConexaoIntranet(), $sql);
            return true;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    
    /**
     * @see 'Incluido SQL2 abaixo para remover o veÃ­culo tambem do grid do SASWEB (retirando a data de instalacao do contrato)'
     * 
     * @param int $veioid
     * @throws \Exception
     * @return boolean
     */
    public function removerVeiculoRoubadoGridSasweb($veioid){
        try {
            $sql = "UPDATE contrato SET condt_instalacao = null WHERE conveioid = $veioid;";
            pg_query(self::obterConexaoIntranet(), $sql);
            return true;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    
    /**
     * @see 'Incluido bloco para verificar se e necessario remover o veiculo tambem do SASGC'
     * @param int $veioid
     * @throws \Exception
     * @return boolean
     */
    public function validarExclusaoSASGC($veioid){
        try {
            $sql = "select count(1) as veiplaca from veiculo where veioid = $veioid";
            $res3 = pg_query(self::obterConexaoGerenciadoraBD(),$sql);
            if (pg_fetch_result($res3,0,"veiplaca") > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    
    /**
     * 
     * @param int $veioid
     * @return \stdClass|NULL
     */
    public function validarPlacaPossuiEquipamentoCargoTracck($veioid){
        $sqlCT = "  SELECT
                        equno_serie as serial, 
                        connumero,
                        (SELECT lincid FROM linha INNER JOIN area ON araoid = linaraoid 
                            WHERE linnumero = equno_fone AND arano_ddd = equno_ddd
                        ) as lincid
                    FROM
                        contrato
                    INNER JOIN
                        equipamento ON conequoid = equoid
                    INNER JOIN
                        produto ON equprdoid = prdoid
                    WHERE
                        conveioid = {$veioid}
                        AND prdcargotracck IS TRUE
                ";
        
        $rsCT = pg_query(self::obterConexaoIntranet(), $sqlCT);
        $ctVO = new \stdClass();
        if(pg_num_rows($rsCT) > 0 ){
            $ctVO->serial   = pg_fetch_result($rsCT, 0, 'serial');
            $ctVO->contrato = pg_fetch_result($rsCT, 0, 'connumero');
            $ctVO->ccid     = pg_fetch_result($rsCT, 0, 'lincid');
            
            return $ctVO;
        }
        return NULL;
    }
    
    /**
     * 
     * @param int $veioid
     * @param string $observacao
     * @throws \Exception
     * @return boolean
     */
    public function removerVeiculoRoubadoGridSasgc($veioid, $observacao){
        try {            
            $sqlInsert = "  INSERT INTO 
                                veiculo_roubado_sincroniza 
                            (
                                vrsveiroid, 
                                vrsveirveioid, 
                                vrsveirsolicitante
                            ) VALUES(
                                (select max(vrsveiroid)+1 from veiculo_roubado_sincroniza),
                                {$veioid},
                                '{$observacao}'
                            );";
            
            pg_query(self::obterConexaoGerenciadoraBD(), $sqlInsert);
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return true;
    }
    
    /**
     * 
     * @param string $ccid
     * @return \stdClass|NULL
     */
    public function buscarModuloCargoTracck($ccid){
        
        $sqlUpdate = "select * from module where ccid='{$ccid}';";
        
        $moduloCTVO = new \stdClass();
        if($result = pg_query(self::obterConexaoGerenciadoraCargoTracck(), $sqlUpdate)){
            
            if(pg_num_rows($result) > 0 ){
                $moduloCTVO->id     = pg_fetch_result($result, 0, 'id');
                $moduloCTVO->mod11  = pg_fetch_result($result, 0, 'mod11');
                
                return $moduloCTVO;
            }
            
        }
        return NULL;
    }
    
    /**
     * 
     * @param int $idModulo
     * @throws \Exception
     * @return boolean
     */
    public function removerVeiculoGridCargoTracck($idModulo){
        try {
            $data = date('Y-m-d', strtotime('-10 days'));
            $sqlUpdate = "
                update module set removed = 't', status = 'expired', expiration_date = '{$data}' where id={$idModulo};
                select module_report_refresh_row({$idModulo});
            ";
            if(! pg_query(self::obterConexaoGerenciadoraCargoTracck(), $sqlUpdate)){
                return false;
            }
            return true;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return true;
    }
    
    public function begin($conexao){
        parent::begin(self::obterConexaoIntranet());
        parent::begin(self::obterConexaoGerenciadoraBD());
        parent::begin(self::obterConexaoGerenciadoraCargoTracck());
    }
    
    public function commit($conexao){
        parent::commit(self::obterConexaoIntranet());
        parent::commit(self::obterConexaoGerenciadoraBD());
        parent::commit(self::obterConexaoGerenciadoraCargoTracck());
    }
    
    public function rollback($conexao){
        parent::rollback(self::obterConexaoIntranet());
        parent::rollback(self::obterConexaoGerenciadoraBD());
        parent::rollback(self::obterConexaoGerenciadoraCargoTracck());
    }
    
}
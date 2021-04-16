<?php
/**
 * Classe de persistencia
 *
 * @author	Renato Teixeira Bueno
 * @email	renato.bueno@meta.com.br
 * 
 */
class GerenciadoraDAO {
    
    
    private $conn;
    private $conn_bdcentral;
    
    /*
     * @author	Renato Teixeira Bueno
 	 * @email	renato.bueno@meta.com.br
     * Construtor
     */
    public function GerenciadoraDAO($conn, $conn_bdcentral){
        
        $this->conn = $conn;
        
        $this->conn_bdcentral = $conn_bdcentral;
       
    }
    
    /*
     * @author	Renato Teixeira Bueno
 	 * @email	renato.bueno@meta.com.br
     * Retorna todos os contratos de teste de instalação com um intervalo maior que 2 horas
     */
    public function getContratoTeste(){
        
        $sql =" SELECT 
                    cntioid AS id_contrato_teste_instalacao,
                    cntiordoid AS id_ordem_servico,
                    cntigeroid3 AS id_terceira_gerenciadora
                FROM 
                    contrato_teste_instalacao
                    left join contrato_gerenciadora on congconnumero = cnticonoid
                WHERE (cntivinculo_gerenciadora IS TRUE or conggeroid3 = 614)
                AND (
                    (NOW() - cntidt_vinculo_gerenciadora) > interval '2 hours'
                    or (conggeroid3 = 614 and cntidt_vinculo_gerenciadora is null))";
        if(!$rs = pg_query($this->conn,$sql)){
            throw new Exception('ERRO: Houve um erro ao buscar contratos de teste.');
        }
        
         if(pg_num_rows($rs) > 0){
            return pg_fetch_all($rs);
        }
        
        return false;
        
    }
    
    public function getTestesTecladoByOs($id_os) {
        
        $sql = "SELECT 
                    cntioid AS id_contrato_teste_instalacao, 
                    cntigeroid3 AS id_terceira_gerenciadora
                FROM 
                    contrato_teste_instalacao
                WHERE 
                    cntieptpoid IN (
                        SELECT 
                            eptpoid 
                        FROM 
                            equipamento_projeto_teste_planejado 
                        WHERE eptpepttoid = 37
                    )
                AND 
                    cntivinculo_gerenciadora IS TRUE
                AND 
                    cntiordoid = $id_os";
        
        $rs = pg_query($this->conn, $sql);
        
         if(pg_num_rows($rs) > 0){
            return pg_fetch_all($rs);
        }
        
        return false;
        
    }
    
    /*
     * @author	Renato Teixeira Bueno
 	 * @email	renato.bueno@meta.com.br
     * Retorna o veiculo e o numero do contrato vinculados a OS
     */
    public function getVeiculoContratoByOS($id_ordem_servico){
        
         $sql ="SELECT
                    ordveioid AS id_veiculo_os,
                    ordconnumero AS numero_contrato_os
                FROM
                    ordem_servico
                WHERE
                    ordoid = $id_ordem_servico";
         if(!$rs = pg_query($this->conn,$sql)){
             throw new Exception('ERRO: Houve um erro ao buscar contratos de teste.');
         }
         
         
         if(pg_num_rows($rs)){
            return pg_fetch_all($rs);
         }
         
         return false;
         
    }
    
    /*
     * @author	Renato Teixeira Bueno
 	 * @email	renato.bueno@meta.com.br
     * Atualiza a gerenciadora (desvincula) na base bdcentral pelo id do veiculo
     */
    public function atualizaTerceiraGerenciadoraVeiculo($id_terceira_gerenciadora, $id_veiculo){
             
       $sql = "UPDATE
                    veiculo_sincroniza
                SET
                    vscconggeroid3 = $id_terceira_gerenciadora
                WHERE
                    vscveioid = $id_veiculo";

        $rs = pg_query($this->conn_bdcentral, $sql);
        if(!$rs = pg_query($this->conn_bdcentral,$sql)){
            return array('error' => true, 'message' => 'ERRO: Houve um erro de conexão ao atualizar veiculo_sincroniza.', 'codigo' => '0322');             
        }
        
        /*if(!pg_affected_rows($rs)){
            return array('error' => true, 'message' => 'ERRO: Houve um erro ao atualizar veiculo_sincroniza', 'codigo' => '0321');
        }*/

        return array('error' => false);        
    }
    
    /*
     * @author	Renato Teixeira Bueno
 	 * @email	renato.bueno@meta.com.br
     * Atualiza a gerenciadora (desvincula) na base Intranet pelo numero do contrato
     */
    public function atualizaTerceiraGerenciadoraContrato($id_terceira_gerenciadora, $numero_contrato){
        
        $sql = "SELECT 
                    conggeroid1, conggeroid2 
                FROM 
                    contrato_gerenciadora 
                WHERE 
                    congconnumero = $numero_contrato";
        
        if (!$rs = pg_query($this->conn,$sql)) {
            return array('error' => true, 'message' => 'ERRO: Houve um erro de conexão ao atualizar contratos.', 'codigo' => '0319');
        }
        
        $gerenciadora = pg_fetch_array($rs);
        
        if ($id_terceira_gerenciadora != $gerenciadora['conggeroid1'] && $id_terceira_gerenciadora != $gerenciadora['conggeroid2']) {
            $sql = "UPDATE
                        contrato_gerenciadora
                    SET
                        conggeroid3 = $id_terceira_gerenciadora
                    WHERE
                        congconnumero  = $numero_contrato";

            if(!$rs = pg_query($this->conn,$sql)){
                return array('error' => true, 'message' => 'ERRO: Houve um erro de conexão ao atualizar contratos.', 'codigo' => '0319');
            }
        }

        return array('error' => false);
        
    }
    
    /*
     * @author	Renato Teixeira Bueno
     * @email	renato.bueno@meta.com.br
     * Realizar o desvinculo entre o teste e a gerenciadora 
     */
    public function desvinculaTesteDeGerenciadora($id_contrato_teste_instalacao){
             
        $sql = "UPDATE
                    contrato_teste_instalacao 
                SET
                    cntivinculo_gerenciadora = false,
                    cntidt_vinculo_gerenciadora = NULL,
                    cntigeroid3 = NULL
                WHERE
                    cntioid   = $id_contrato_teste_instalacao
                RETURNING cntioid AS id_contrato_teste_instalacao, cntidt_vinculo_gerenciadora as data_vinculo_gerenciadora ";

        if(!$rs = pg_query($this->conn,$sql)){
            return array('error' => true, 'message' => 'ERRO: Houve um erro de conexão ao atualizar contrato_teste_instalacao.', 'codigo' => '0319');            
        }
        
        if(!pg_affected_rows($rs)){
            return array('error' => true, 'message' => 'ERRO: Houve um erro ao atualizar contrato_teste_instalacao.', 'codigo' => '0320');
        }
        
        if(pg_num_rows($rs) > 0){
            $id_contrato_teste_instalacao = pg_fetch_result($rs, 0, 'id_contrato_teste_instalacao');
            
            return array('error' => false, 'message' => "[". date('d/m/Y h:i:s A') ." ] => Contrato de Teste $id_contrato_teste_instalacao desvinculado.");
        }
    }
     
}
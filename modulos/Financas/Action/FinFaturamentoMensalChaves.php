<?php


/**
 * 
 * @file FinFaturamentoMensalChaves.php
 * @author allan.helfstein.ext
 * @version 06/11/2017 16:07:07
 * @since 06/11/2017 16:07:07
 * @package SASCAR FinFaturamentoMensalChaves.php 
 */


class FinFaturamentoMensalChaves 
{
	
	private $empresa;
	private $view;
	private $dao;

	public function __construct($dao = null)
	{

		$this->dao   		   = (is_object($dao)) ? $this->dao = $dao : NULL;
		$this->empresa 		   = null; 
		$this->view            = array(); 

	}

	public function index()
	{
		try
		{
			//lista todas empresa cadastradas do Grupo Michelin - SELECTBOX (FILTROS - EMPRESA) name="tecoid"
			$this->view['empresas'] = $this->dao->getInformacoesEmpresa();

			//condição se foi enviado o formulário
			if($_SERVER['REQUEST_METHOD'] == "POST")
			{
				//seta os valores para validação 
				$this->dao->setEmpresa(filter_input(INPUT_POST,'tecoid', FILTER_VALIDATE_INT));
			    $this->dao->setDataInicial(filter_input(INPUT_POST,'data_ini_pesquisa'));
				$this->dao->setDataFinal(filter_input(INPUT_POST,'data_fim_pesquisa'));

				if(isset($_POST['consultar']))
				{
					$this->view['resultado'] = $this->dao->getRelatorio();
				}
				elseif (isset($_POST['exportar']))
				{
					$this->dao->getRelatorioCsv();
				}
			}
		}
		catch (Exception $e) 
		{
            $this->view['mensagem'] = $e->getMessage();
        }
		//Incluir a view padrão
        require_once _MODULEDIR_ . "Financas/View/fin_faturamento_nf_chaves/index.php";
	}

}
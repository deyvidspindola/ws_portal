<?php

/** Classes dependencias */
require 'modulos/TI/View/TIResetSenhaView.php';
require 'modulos/TI/DAO/TIResetSenhaDAO.class.php';

/** Lib de envio de email */
include "/lib/phpMailer/class.phpmailer.php";

/**
 * description: Classe responsável por controlar
 * a tela ti_reset_senha.php
 * 
 * @author alexandre.reczcki
 *
 */
class TIResetSenhaController {
	
	/** @var TIResetSenhaDAO */
	private $dao;
	
	/** @view TIResetSenhaView */
	private $view;
		
	public function __construct() {
		$this->dao = new TIResetSenhaDAO();
		$this->view = new TIResetSenhaView();
	}
	
	public function pesquisarAction() {
		$this->view->pesquisarForm();
	}
	
	public function pesquisarUsuario() {
		if ($_POST ['rs_nm_usuario'] != '' || $_POST ['rs_ds_login'] != '') {
			$resultado = $this->dao->pesquisar($_POST ['rs_nm_usuario'], $_POST ['rs_ds_login']);
		}
		
		$this->view->pesquisaResult($resultado);
	}
	
	/**
	 * Mostra o usuario na tela
	 * 
	 * @return ti_reset_senha/usuario_form.php
	 */
	public function mostrarUsuario() {
		if ($_POST ['id_usuario'] != '') {
			$resultado = $this->dao->pesquisarUsuario($_POST ['id_usuario']);
		}
		
		$this->view->mostrarUsuario($resultado);
	}
	
	/**
	 * Efetua o reset da senha do usuário informado
	 * via seleção da tela e envia o email para o usuário
	 * com a senha nova.
	 * 
	 */
	public function resetarSenha() {
		try {
			/** Executa o reset de senha, caso exista um código de usuário selecionado */
			if ($_POST['codigo'] != '') {
				
				$usuario = new TIUsuarioModel();
				$usuario = $this->dao->resetarSenha($_POST ['codigo'], $_POST ['usu_email']);
				echo $usuario->mensagem;
				if ($usuario ->login != ''){
					$retorno = $this->enviarEmailNovaSenha($usuario);
					if ($retorno === 'ErroEnvioEmail'){
						$usuario->email = 'ErroEnvioEmail';
					}
				}
			}
			
			$this->view->pesquisarForm($usuario);

		} catch(Exception $e) {
			echo $e->getMessage();		
		}
	}
	
	/**
	 * description: Enviar email para usuario 
	 * @param TIUsuarioModel $usuario
	 * @return boolean
	 */
	private function enviarEmailNovaSenha(TIUsuarioModel $usuario){
		try {
			$mail = new PHPMailer();
			$mail->IsSMTP ();
			$mail->From = "sistema@sascar.com.br";
			$mail->FromName = "Sistema-Sascar";
			$mail->Subject = "Informações de redefinição de senha";
			
			$caminho = "/tmp/";
			$data = date ( 'dmY' );
			$arquivo = '';
			$email_corpo = "
					
				</br>
				Prezado(a) $usuario->nome,
				</br></br>
				Obrigado pelo seu contato.
				</br>
				Sua senha foi Redefinida:</br></br>
				Usuario: $usuario->login</br>
				Senha: $usuario->senha
				
				</br></br>
				Em caso de duvidas, entre em contato com o ramal 9050.
				</br></br>
				Atenciosamente,</br>
				Suporte Sascar</br>
				=========================================

			";
				
			$mail->MsgHTML($email_corpo);
				
			/** Destinatario Email */
			$mail->AddAddress($usuario->email);
				
			$filename_day = `date "+%d" --date='1 day ago'`;
			$day_temp = `date "+%d"`;
			$day_temp = trim ( $day_temp );
				
			if ($day_temp == '01') {
				$filename_month = `date "+%m" --date='1 month ago'`;
			} else {
				$filename_month = `date "+%m"`;
			}
			$filename_day = trim ( $filename_day );
			$filename_month = trim ( $filename_month );
				
			$filename_year = `date "+%G"`;
			
			if (! $mail->Send ()) {
				//echo 'Erro ao enviar email ' . $usuario->email . '<br/>';
				//echo "Erro: " . $mail->ErrorInfo;
				//$usuario->mensagem = 'Erro ao enviar email ' . $usuario->email . '<br/>' . $mail->ErrorInfo;
				//$usuario->email = 'ErroEnvioEmail';
				return 'ErroEnvioEmail';
			}
				
		} catch(Exception $e) {
			echo $e->getMessage();
			return 'ErroEnvioEmail';
		}
		return true;
	}
	
}
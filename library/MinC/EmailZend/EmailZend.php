<?php
//Commit
/**
*nome: email.php
*Classe: EmailZend
*Descrição: Envio de e-mails atraves da ZF.
*/
class EmailZend
{
  /**atributos*/
  public $_SMTP;
  /**
  * Contrutor com parametros
  *
  * @return instancia
  */
  function __construct(){}
  /**
  * Enviar Emails
  *
  * Envia emails via SMTP autenticado
  *
  * @param  destinatario
  *	@param 	remetente
  *	@param  assunto
  *	@param  mensagem
  *
  */
  function  enviarEmail($destinatario, $remetente,
    $assunto, $mensagem)
  {
    $mail = new Zend_Mail();
    $mail->setBodyHtml($mensagem);
    $mail->setFrom($remetente, 'Namoro em Peso');
    $mail->addTo($destinatario, '');
    $mail->setSubject($assunto);
    $mail->send($this->_SMTP);
  }
  /**
  * Configurar Servidor
  *
  * Configurar servidor de saida SMTP
  *	@param	$server
  *	@param	$usuario
  *	@param	$senha
  *
  */

    function  confServ($server='smtp1.cultura.gov.br')
    {
        $config = array(
                'auth' => 'login',
//      'username' => $usuario,
//      'password' => $senha,
                'port' => 25/*nao precisa*/
        );
        $this->_SMTP = new Zend_Mail_Transport_Smtp($server, $config);
    }
}

?>

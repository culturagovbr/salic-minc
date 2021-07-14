<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of EnviarEmail
 *
 * @author 01129075125
 */
class EnviarEmail
{

    private $destinatario;
    private $nomedestinatario;
    private $assunto;
    private $mensagem;

    public function __construct ($destinatario, $nomedestinatario, $assunto, $mensagem)
    {
        $this->destinatario         = $destinatario;
        $this->nomedestinatario     = $nomedestinatario;
        $this->assunto              = $assunto;
        $this->mensagem             = $mensagem;
    }
    public function enviar()
    {
        require_once('PhpMailer/class.phpmailer.php');

        $mail             = new PHPMailer();
        $mail->IsSMTP(); // Usando a conex�o SMTP
        $mail->Host       = "correio.cultura.gov.br"; // Servidor SMTP
        $mail->SMTPDebug  = 2;                     // Debug do SMTP
        $mail->SetFrom('resposta@cultura.gov.br');// Email Alias do Ministério da cultura
        $mail->Subject    = $this->assunto;
        $mail->MsgHTML($this->mensagem);

        $mail->AddAddress($this->destinatario, $this->nomedestinatario);

        try
        {
            $mail->send();
        }
        catch(exception $e)
        {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
    }
}
?>

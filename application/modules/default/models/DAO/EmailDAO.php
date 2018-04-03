<?php
class EmailDAO extends Zend_Db_Table
{
    public static function enviarEmail($email, $assunto, $texto, $perfil = 'PerfilGrupoPRONAC')
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH .'/configs/application.ini', APPLICATION_ENV);
        $emailDefault = $config->mail->default->toArray();
        $config = $config->mail->transport->toArray();

        $transport = new Zend_Mail_Transport_Smtp($config['host'], $config);
        $mail = new Zend_Mail();

        $mail->setBodyHtml($texto);
        $mail->setFrom($emailDefault['email'], 'Salic BR');
        $mail->addTo($email);
        $mail->setSubject($assunto);
        return $mail->send($transport);
    }

    /**
    * M�todo para buscar e-mails
    * M�dulo Fiscalizar Projetos - Comunicar Proponente da Fiscaliza��o
    * @access public
    * @static
    * @param string $email
    * @param string $texto
    * @return object
    */
    public static function buscarEmailsFiscalizacao($idPronac, $idFiscalizacao)
    {
        $sql = "SELECT i.Descricao AS email
                        FROM sac.dbo.Projetos p
                        INNER JOIN sac.dbo.PreProjeto pr           ON (p.idProjeto = pr.idPreProjeto)
                        INNER JOIN sac.dbo.tbFiscalizacao f        ON (f.IdPRONAC = p.IdPRONAC)
                        INNER JOIN Agentes.dbo.Internet i          ON (i.idAgente = pr.idAgente )
                        WHERE (p.IdPRONAC = $idPronac) AND (f.idFiscalizacao = $idFiscalizacao)
                        UNION ALL
                        SELECT t.Descricao AS email
                        FROM sac.dbo.Projetos p
                        INNER JOIN sac.dbo.tbFiscalizacao f        ON (f.IdPRONAC = p.IdPRONAC)
                        INNER JOIN Agentes.dbo.Internet t   ON (t.idAgente = f.idAgente)
                        WHERE (p.IdPRONAC = $idPronac) AND (f.idFiscalizacao = $idFiscalizacao)";

        return $sql;
    }
}

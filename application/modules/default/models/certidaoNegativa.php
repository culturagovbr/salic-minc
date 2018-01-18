<?php

class certidaoNegativa extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name  = 'CertidoesNegativas';

    public function buscarCertidaoNegativa($cpfcnpj)
    {
        $select = $this->select();
        $select->from(
            array('c' => $this->_name),
            array(new Zend_Db_Expr("
                CASE
                        WHEN c.CodigoCertidao = '49'
                        THEN 'Quita&ccedil;&atilde;o de Tributos Federais'
                        WHEN c.CodigoCertidao = '51'
                        THEN 'FGTS'
                        WHEN c.CodigoCertidao = '52'
                        THEN 'INSS'
                        WHEN c.CodigoCertidao = '244'
                        THEN 'CADIN'
                END AS dsCertidao,
                c.CodigoCertidao, c.DtEmissao, c.DtValidade, c.AnoProjeto+c.Sequencial AS Pronac,
                CASE
                    WHEN c.CodigoCertidao = 244
                    THEN 
                        CASE
                            WHEN c.cdSituacaoCertidao = 0
                            THEN 'Pendente'
                            ELSE 'N&atilde;o Pendente'
                        END
                END AS Situacao,
                CASE
                    WHEN c.CodigoCertidao = 244
                    THEN DateDiff(dy,getdate(),c.DtEmissao)
                    ELSE DateDiff(dy,getdate(),c.DtValidade)
                END AS qtDias
            ")),
            $this->_schema
        )
        ->where('c.CgcCpf = ?', trim($cpfcnpj))
        ->order(2);
        return $this->fetchAll($select);
    }
}

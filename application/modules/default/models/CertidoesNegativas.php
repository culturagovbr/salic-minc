<?php
class CertidoesNegativas extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = 'SAC';
    protected $_schema  = 'SAC';
    protected $_name    = 'CertidoesNegativas';

    /**
     * Metodo para buscar os dados de uma certidao especifica
     * @access public
     * @param string $CgcCpf
     * @param integer $CodigoCertidao
     * @return object
     */
    public function buscarDados($CgcCpf = null, $CodigoCertidao = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("c" => $this->_name),
            array("(c.AnoProjeto+c.Sequencial) AS pronac"
                ,"c.CgcCpf"
                ,"c.CodigoCertidao"
                ,new Zend_Db_Expr("CONVERT(VARCHAR(10), c.DtEmissao, 103) AS dtEmissao")
                ,new Zend_Db_Expr("CONVERT(VARCHAR(10), c.DtEmissao, 108) AS hrEmissao")
                ,new Zend_Db_Expr("CONVERT(VARCHAR(10), c.DtValidade, 103) AS dtValidade")
                ,new Zend_Db_Expr("CONVERT(VARCHAR(10), c.DtValidade, 108) AS hrValidade")
                ,"c.Logon"
                ,"c.idCertidoesnegativas"
                ,"c.cdProtocoloNegativa"
                ,"c.cdSituacaoCertidao")
        );

        // filtra pelo cpf
        if (!empty($CgcCpf)) {
            $select->where("c.CgcCpf = ?", $CgcCpf);
        }

        // filtra pelo codigo da certidao
        if (!empty($CodigoCertidao)) {
            $select->where("c.CodigoCertidao = ?", $CodigoCertidao);
        }

        $select->order("c.idCertidoesnegativas DESC");

        return $this->fetchAll($select);
    } // fecha metodo buscarDados()
}

<?php
/**
 * DAO tbComprovanteBeneficiario
 * @since 16/03/2011
 * @link http://www.cultura.gov.br
 */

class tbComprovanteBeneficiario extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbComprovanteBeneficiario";

    /**
     * M�todo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha m�todo cadastrarDados()


    /**
     * M�todo para consultar os arquivos anexados
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function buscarDocumentosPronac($idpronac, $tpRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a' => $this->_name)
            );
        $select->joinInner(
                    array('d' => 'tbRelatorio'),
                    'd.idRelatorio = a.idRelatorio',
                    array('*'),
                    'SAC.dbo'
                    );
        $select->joinInner(
                    array('e' => 'tbDocumento'),
                    'e.idDocumento = a.idDocumento',
                    array('*'),
                    'BDCORPORATIVO.scCorp'
                    );
        $select->joinInner(
                    array('f' => 'tbArquivo'),
                    'f.idArquivo = e.idArquivo',
                    array('*'),
                    'BDCORPORATIVO.scCorp'
                    );
        $select->joinInner(
                    array('g' => 'tbTipoDocumento'),
                    'g.idTipoDocumento = e.idTipoDocumento',
                    array('dsTipoDocumento'),
                    'SAC.dbo'
                    );
        $select->joinInner(
                    array('h' => 'tbRelatorioTrimestral'),
                    'h.idRelatorio = d.idRelatorio',
                    array(''),
                    'SAC.dbo'
                    );

        $select->where('d.idPRONAC = ?', $idpronac);
        $select->where('d.tpRelatorio = ?', $tpRelatorio);
        $select->where('h.stRelatorioTrimestral = 1');

        return $this->fetchAll($select);
    }

    /**
     * M�todo para consultar os arquivos anexados
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function buscarDocumentosPronac2($idpronac, $tpRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a' => $this->_name)
            );
        $select->joinInner(
                    array('d' => 'tbRelatorio'),
                    'd.idRelatorio = a.idRelatorio',
                    array('*'),
                    'SAC.dbo'
                    );
        $select->joinInner(
                    array('e' => 'tbDocumento'),
                    'e.idDocumento = a.idDocumento',
                    array('*'),
                    'BDCORPORATIVO.scCorp'
                    );
        $select->joinInner(
                    array('f' => 'tbArquivo'),
                    'f.idArquivo = e.idArquivo',
                    array('*'),
                    'BDCORPORATIVO.scCorp'
                    );
        $select->joinInner(
                    array('g' => 'tbTipoDocumento'),
                    'g.idTipoDocumento = e.idTipoDocumento',
                    array('dsTipoDocumento'),
                    'SAC.dbo'
                    );
        $select->joinInner(
                    array('h' => 'tbRelatorioTrimestral'),
                    'h.idRelatorio = d.idRelatorio',
                    array(''),
                    'SAC.dbo'
                    );

        $select->where('d.idPRONAC = ?', $idpronac);
        $select->where('d.tpRelatorio = ?', $tpRelatorio);
        $select->where('h.stRelatorioTrimestral = 2');

        return $this->fetchAll($select);
    }


    /**
     * M�todo para consultar os arquivos anexados
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function buscarDocumentosPronac3($idpronac, $tpRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a' => $this->_name)
            );
        $select->joinInner(
                    array('d' => 'tbRelatorio'),
                    'd.idRelatorio = a.idRelatorio',
                    array('*'),
                    'SAC.dbo'
                    );
        $select->joinInner(
                    array('e' => 'tbDocumento'),
                    'e.idDocumento = a.idDocumento',
                    array('*'),
                    'BDCORPORATIVO.scCorp'
                    );
        $select->joinInner(
                    array('f' => 'tbArquivo'),
                    'f.idArquivo = e.idArquivo',
                    array('*'),
                    'BDCORPORATIVO.scCorp'
                    );
        $select->joinInner(
                    array('g' => 'tbTipoDocumento'),
                    'g.idTipoDocumento = e.idTipoDocumento',
                    array('dsTipoDocumento'),
                    'SAC.dbo'
                    );
        $select->joinInner(
                    array('h' => 'tbRelatorioTrimestral'),
                    'h.idRelatorio = d.idRelatorio',
                    array(''),
                    'SAC.dbo'
                    );

        $select->where('d.idPRONAC = ?', $idpronac);
        $select->where('d.tpRelatorio = ?', $tpRelatorio);
        $select->where('h.stRelatorioTrimestral in (5,7)');

        return $this->fetchAll($select);
    }

    /**
     * M�todo para consultar os arquivos anexados
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function buscarDocumentosPronac4($idpronac, $tpRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a' => $this->_name)
            );
        $select->joinInner(
                    array('d' => 'tbRelatorio'),
                    'd.idRelatorio = a.idRelatorio',
                    array('*'),
                    'SAC.dbo'
                    );
        $select->joinInner(
                    array('e' => 'tbDocumento'),
                    'e.idDocumento = a.idDocumento',
                    array('*'),
                    'BDCORPORATIVO.scCorp'
                    );
        $select->joinInner(
                    array('f' => 'tbArquivo'),
                    'f.idArquivo = e.idArquivo',
                    array('*'),
                    'BDCORPORATIVO.scCorp'
                    );
        $select->joinInner(
                    array('g' => 'tbTipoDocumento'),
                    'g.idTipoDocumento = e.idTipoDocumento',
                    array('dsTipoDocumento'),
                    'SAC.dbo'
                    );
        $select->joinInner(
                    array('h' => 'tbRelatorioTrimestral'),
                    'h.idRelatorio = d.idRelatorio',
                    array(''),
                    'SAC.dbo'
                    );

        $select->where('d.idPRONAC = ?', $idpronac);
        $select->where('d.tpRelatorio = ?', $tpRelatorio);

        return $this->fetchAll($select);
    }
}

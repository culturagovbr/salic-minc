<?php
/**
 * DAO tbDocumentoAceitacao
 * @since 16/03/2011
 * @version 1.0
 * @link http://www.cultura.gov.br
 */

class tbDocumentoAceitacao extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbDocumentoAceitacao";

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
    public function buscarDocumentosPronac($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a' => $this->_name)
            );
        $select->joinInner(
                    array('b' => 'tbRelatorioConsolidado'),
                    'b.idRelatorioConsolidado = a.idRelatorioConsolidado',
                    array('*'),
                    'SAC.dbo'
                    );
        $select->joinInner(
                    array('c' => 'tbRelatorio'),
                    'c.idRelatorio = b.idRelatorio',
                    array('*'),
                    'SAC.dbo'
                    );
        $select->joinInner(
                    array('d' => 'tbDocumento'),
                    'd.idDocumento = a.idDocumento',
                    array('*'),
                    'BDCORPORATIVO.scCorp'
                    );
        $select->joinInner(
                    array('e' => 'tbArquivo'),
                    'e.idArquivo = d.idArquivo',
                    array('*'),
                    'BDCORPORATIVO.scCorp'
                    );
        $select->joinInner(
                    array('f' => 'tbTipoDocumento'),
                    'f.idTipoDocumento = d.idTipoDocumento',
                    array('dsTipoDocumento'),
                    'BDCORPORATIVO.scCorp'
                    );

        $select->where('c.idPRONAC = ?', $idpronac);

        return $this->fetchAll($select);
    }
}

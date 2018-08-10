<?php

class tbFormDocumento extends MinC_Db_Table_Abstract
{
    protected $_schema   = "BDCORPORATIVO";
    protected $_name = 'tbFormDocumento';

    public function buscaNrFormDocumento($idEdital)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array("tbfd" => new Zend_Db_Expr($this->_schema.".".$this->_name)),
                        array(
                                'tbfd.nrFormDocumento'

                             )
                     );
        $select->where('tbfd.idEdital  = ?', $idEdital);
        $select->where('tbfd.idClassificaDocumento = 24');
        
        return $this->fetchRow($select);
    }

    public function salvar($dados)
    {
        $tmpTbFormDocumento = new tbFormDocumento();

        if (isset($dados['nrFormDocumento'])) {
            $tmpRsTbFormDocumento = $tmpTbFormDocumento->find($dados['nrFormDocumento'])->current();
        } else {
            $tmpRsTbFormDocumento = $tmpTbFormDocumento->createRow();
        }

        if (isset($dados['nrVersaoDocumento'])) {
            $tmpRsTbFormDocumento->nrVersaoDocumento = $dados['nrVersaoDocumento'];
        }
        if (isset($dados['nmFormDocumento'])) {
            $tmpRsTbFormDocumento->nmFormDocumento = $dados['nmFormDocumento'];
        }
        if (isset($dados['dsFormDocumento'])) {
            $tmpRsTbFormDocumento->dsFormDocumento = $dados['dsFormDocumento'];
        }
        if (isset($dados['stFormDocumento'])) {
            $tmpRsTbFormDocumento->stFormDocumento = $dados['stFormDocumento'];
        }
        if (isset($dados['dtIniVigDocumento'])) {
            $tmpRsTbFormDocumento->dtIniVigDocumento = $dados['dtIniVigDocumento'];
        }
        if (isset($dados['dtFimVigDocumento'])) {
            $tmpRsTbFormDocumento->dtFimVigDocumento = $dados['dtFimVigDocumento'];
        }
        if (isset($dados['stModalidadeDocumento'])) {
            $tmpRsTbFormDocumento->stModalidadeDocumento = $dados['stModalidadeDocumento'];
        }
        if (isset($dados['nmSiglaFormDocumento'])) {
            $tmpRsTbFormDocumento->nmSiglaFormDocumento = $dados['nmSiglaFormDocumento'];
        }
        if (isset($dados['dtCadastramento'])) {
            $tmpRsTbFormDocumento->dtCadastramento = $dados['dtCadastramento'];
        }
        if (isset($dados['idClassificaDocumento'])) {
            $tmpRsTbFormDocumento->idClassificaDocumento = $dados['idClassificaDocumento'];
        }
        if (isset($dados['idEdital'])) {
            $tmpRsTbFormDocumento->idEdital = $dados['idEdital'];
        }

        $id = $tmpRsTbFormDocumento->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }
}

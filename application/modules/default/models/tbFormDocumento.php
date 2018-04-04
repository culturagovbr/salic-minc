<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tbFormDocumento
 *
 * @author tisomar
 */
class tbFormDocumento extends MinC_Db_Table_Abstract
{
    protected $_banco   = "BDCORPORATIVO";
    protected $_schema  = "BDCORPORATIVO.scQuiz";
    protected $_name = 'tbFormDocumento';

    public function buscaNrFormDocumento($idEdital)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array("tbfd" => new Zend_Db_Expr($this->_banco.".".$this->_schema.".".$this->_name)),
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
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTbFormDocumento = new tbFormDocumento();

        if (isset($dados['nrFormDocumento'])) {
            $tmpRsTbFormDocumento = $tmpTbFormDocumento->find($dados['nrFormDocumento'])->current();
        } else {
            $tmpRsTbFormDocumento = $tmpTbFormDocumento->createRow();
        }

        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
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


        //SALVANDO O OBJETO CRIADO
        $id = $tmpRsTbFormDocumento->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }
}

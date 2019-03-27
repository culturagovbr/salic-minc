<?php

class Readequacao_Model_TbProjetoRecebedorRecursoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Readequacao_Model_DbTable_TbProjetoRecebedorRecurso');
    }

    public function save($model)
    {
        return parent::save($model);
    }

    public function finalizarSolicitacaoReadequacao($arrData)
    {
        try {
            $objProjetoRecebedorRecurso = new Readequacao_Model_TbProjetoRecebedorRecurso();
            
            $objProjetoRecebedorRecurso->setIdProjetoRecebedorRecurso($arrData['idProjetoRecebedorRecurso']);
            $objProjetoRecebedorRecurso->setIdSolicitacaoTransferenciaRecursos($arrData['idSolicitacaoTransferenciaRecursos']);
            $objProjetoRecebedorRecurso->setIdPronacTransferidor($arrData['idPronacTransferidor']);
            $objProjetoRecebedorRecurso->setIdPronacRecebedor($arrData['idPronacRecebedor']);
            $objProjetoRecebedorRecurso->setTpTransferencia($arrData['tpTransferencia']);
            $objProjetoRecebedorRecurso->setDtRecebimento($arrData['dtRecebimento']);
            $objProjetoRecebedorRecurso->setVlRecebido($arrData['vlRecebido']);
            
            $id = $this->save($objProjetoRecebedorRecurso);
            
            if ($this->getMessage()) {
                throw new Exception($this->getMessage());
            }

            return $id;
        } catch (Exception $e) {
            throw $e;
        }
        
    }    
}

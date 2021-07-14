<?php

class Readequacao_Model_TbSolicitacaoTransferenciaRecursosMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Readequacao_Model_DbTable_TbSolicitacaoTransferenciaRecursos');
    }

    public function save($model)
    {
        return parent::save($model);
    }

    public function salvarParecerTecnico($arrData)
    {
        try {

            $objTbSolicitacaoTransferenciaRecursos = new Readequacao_Model_TbSolicitacaoTransferenciaRecursos();

            $objTbSolicitacaoTransferenciaRecursos->setIdSolicitacaoTransferenciaRecursos($arrData['idSolicitacaoTransferenciaRecursos']);
            $objTbSolicitacaoTransferenciaRecursos->setSiAnaliseTecnica($arrData['siAnaliseTecnica']);

            $id = $this->save($objTbSolicitacaoTransferenciaRecursos);

            if ($this->getMessage()) {
                throw new Exception($this->getMessage());
            }

            return $id;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function transferenciaDeRecursosEntreProjetos($idPronac)
    {
        $resultado = new \Readequacao_Model_DbTable_TbProjetoRecebedorRecurso();
        $where = array('idPronacTransferidor = ?' => 131036);
        $queryResult = $resultado->obterTransferenciaRecursosEntreProjetos($where);
        xd($queryResult );
    }
}

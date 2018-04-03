<?php

class Solicitacao_Model_SpSelecionarTecnicoSolicitacao extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'spSelecionarTecnicoSolicitacao';

    /**
     * Busca o técnico responsável por analisar a solicitação de acordo com o projeto ou proposta
     * @param $id - idPronac(projeto) ou idPreProjeto(proposta)
     * @param string $tipo aceita 'projeto ou proposta'
     * @retorno idOrgao, idPerfil, idTecnico
     * @return array|bool
     * @throws Exception
     */

    public function exec($id, $tipo = 'projeto')
    {
        if (empty($id))
            return false;

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "exec " . $this->_banco . "." . $this->_name . " {$id}, '{$tipo}'";
            $resultado = $db->fetchRow($sql);
        } catch (Exception $e) {
            throw $e;
        }

        return $resultado;
    }
}

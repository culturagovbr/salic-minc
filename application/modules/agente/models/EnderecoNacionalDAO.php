<?php

/**
 * Agente_Model_EnderecoNacionalDAO
 *
 * @uses Zend
 * @uses _Db_Table
 * @package Model
 * @author  wouerner <wouerner@gmail.com>
 */
class Agente_Model_EnderecoNacionalDAO extends MinC_Db_Table_Abstract
{
    protected $_name = 'endereconacional';
    protected $_schema = 'agentes';

    /**
     * buscarendereconacional
     *
     * @param mixed $idagente
     * @static
     * @access public
     * @deprecated Utilizar método da DbTable
     */
    public static function buscarEnderecoNacional($idAgente)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $e = array(
            'idEndereco',
            'idAgente',
            'TipoEndereco',
            'TipoLogradouro',
            'Logradouro',
            'Numero',
            'Bairro',
            'Complemento',
            'Cidade',
            'UF',
            'Cep',
            'Municipio',
            'UfDescricao' ,
            'Status',
            'Divulgar' ,
            'Usuario'
        );

        $sql = $db->select()
            ->from('EnderecoNacional', $e, 'AGENTES.dbo')
            ->where('idAgente = ?', $idAgente)
            ;

        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $dados =  $db->fetchAll($sql);

        return $dados;
    }

    /**
     * gravarEnderecoNacional
     *
     * @param mixed $dados
     * @static
     * @access public
     * @return void
     * @deprecated Utilizar método da DbTable
     */
    public static function gravarEnderecoNacional($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $i =  $db->insert('AGENTES.dbo.EnderecoNacional', $dados);
    }

    public function inserir($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $schema = $this->getSchema($this->_schema). '.' .$this->_name;
        $db->insert($schema, $dados);
    }

    /**
     * atualizaEnderecoNacional
     *
     * @param mixed $idAgente
     * @param mixed $dados
     * @static
     * @access public
     * @return void
     * @deprecated Utilizar método da DbTable
     */
    public static function atualizaEnderecoNacional($idAgente, $dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where['idAgente = ?'] = $idAgente;

        $i =  $db->update('AGENTES.dbo.EnderecoNacional', $dados, $where);
    }

    /**
     * deletarEnderecoNacional
     *
     * @param mixed $idEndereco
     * @static
     * @access public
     * @return void
     * @deprecated Utilizar método da DbTable
     *
     */
    public static function deletarEnderecoNacional($idEndereco)
    {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            return $resultado = $db->delete('AGENTES.dbo.EnderecoNacional', array('idEndereco = ? '=> $idEndereco));
        } catch (Zend_Exception $e) {
            throw new Zend_Db_Exception("Erro ao excluir Telefone do Proponente: " . $e->getMessage());
        }
    }

    /**
     * mudaCorrespondencia
     *
     * @param mixed $idAgente
     * @static
     * @access public
     * @return void
     * @deprecated Utilizar método da DbTable
     */
    public static function mudaCorrespondencia($idAgente)
    {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();

            return $resultado = $db->update('AGENTES.dbo.EnderecoNacional', array('Status = ?' => 0), array('idAgente = ?' => $idAgente));
        } catch (Zend_Exception $e) {
            throw new Zend_Db_Exception("Erro ao alterar o Status dos endere�os: " . $e->getMessage());
        }
    }

    /**
     * novaCorrespondencia
     *
     * @param mixed $idAgente
     * @static
     * @access public
     * @return void
     * @deprecated Utilizar método da DbTable
     */
    public static function novaCorrespondencia($idAgente)
    {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "UPDATE AGENTES.dbo.EnderecoNacional set Status = 1
                    WHERE idAgente = ".$idAgente."
                    AND idEndereco = (select MIN(idEndereco) as valor from AGENTES.dbo.EnderecoNacional  where idAgente = ".$idAgente.")";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao alterar o Status dos endere�os: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }
}

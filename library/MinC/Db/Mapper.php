<?php

/**
 * Class MinC_Db_Mapper
 *
 * @name MinC_Db_Mapper
 * @package default
 * @subpackage controllers
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since ${DATE}
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class MinC_Db_Mapper
{
    protected $_isBeginTransaction = false;
    protected $_dbTable;

    protected $arrMessages = array();

    public function getMessage()
    {
        return reset($this->arrMessages);
    }

    public function getMessages()
    {
        return $this->arrMessages;
    }

    public function setMessage($mixMessage, $strIndice = null)
    {
        if (is_array($mixMessage)) {
            $this->arrMessages = array_merge($this->arrMessages, $mixMessage);
        } else {
            if ($strIndice) {
                $this->arrMessages[$strIndice] = $mixMessage;
            } else {
                $this->arrMessages[] = $mixMessage;
            }
        }
        return $this;
    }

    public function setDbTable($dbTable)
    {
        if (!$this->_dbTable && is_string($dbTable)) {
            $dbTable = new $dbTable();
            if (!$dbTable instanceof Zend_Db_Table_Abstract) {
                throw new Exception('Invalid table data gateway provided');
            }
        } else {
            $dbTable = $this->_dbTable;
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     *
     * @name getDbTable
     * @return MinC_Db_Table_Abstract
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  ${DATE}
     */
    public function getDbTable()
    {
//        if (null === $this->_dbTable) {
//            $this->setDbTable('Agente_Model_DbTable_Agentes');
//        }
        return $this->_dbTable;
    }

    /**
     * @see Zend_Db_Adapter_Abstract::beginTransaction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  06/09/2016
     */
    public function beginTransaction()
    {
        $this->_isBeginTransaction = true;
        $this->getDbTable()->getAdapter()->beginTransaction();
        return $this;
    }

    /**
     * @see Zend_Db_Adapter_Abstract::rollBack
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  06/09/2016
     */
    public function rollBack()
    {
        $this->getDbTable()->getAdapter()->rollBack();
        return $this;
    }

    /**
     * @see Zend_Db_Adapter_Abstract::commit
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  06/09/2016
     */
    public function commit()
    {
        $this->getDbTable()->getAdapter()->commit();
        return $this;
    }

    public function findBy($arrData)
    {
        return $this->_dbTable->findBy($arrData);
    }

    /**
     * @see MinC_Db_Table_Abstract::deleteBy
     */
    public function deleteBy(array $arrWhere)
    {
        return $this->getDbTable()->deleteBy($arrWhere);
    }

    public function delete($intId)
    {
        $row = $this->getDbTable()->find($intId)->current();
        if ($row) {
            return $row->delete();
        } else {
            return false;
        }
    }

    public function isValid($model)
    {
        return true;
    }

    /**
     * @todo deixar a pk podendo ser array, atualmente so pode sendo string utilizando o reset.
     */
    public function save($model)
    {
        if ($this->isValid($model)) {

            $table = $this->getDbTable();
            $pk = is_array($table->getPrimary())? reset($table->getPrimary()) : $table->getPrimary();
//            $pk = strtolower($pk);
            $method = 'get' . ucfirst($pk);
            $pkValue = $model->$method();
//            $data = array_filter($model->toArray(), 'strlen');
            $data = array_filter($model->toArray(), function($value){return ($value !== null);});
//            $data = array_filter($model->toArray());($model->toArray());
            try {
                if ($table->getSequence()) {
                    unset($data[$pk]);
                    if (empty($pkValue)) {
                        return $table->insert($data);
                    } else {
                        $data = array_change_key_case($data, CASE_LOWER); // Por causa do IdPronac e idPronac foi preciso fazer essa implementacao.
                        unset($data[strtolower($pk)]); 
                        $table->update($data, array($pk . ' = ?' => $pkValue));
                        return $pkValue;
                    }
                } else {
                    $row = $table->find($pkValue)->current();
                    if (!$row) $row = $table->createRow();
                    unset($data[$pk]);
                    $row->setFromArray($data)->save();
                    return $pkValue;
                }
            } catch (Exception $e) {
                $this->setMessage($e->getMessage());
            }
        } else {
            return false;
        }
    }

    /**
     * Retorna o resultado com chave e valor apenas.
     *
     * @name fetchPairs
     * @param string $key
     * @param string $value
     * @param array $where
     * @param string $order
     * @return array
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  01/09/2016
     */
    public function fetchPairs($key, $value , array $where = [], $order = '')
    {
        return $this->getDbTable()->fetchPairs($key, $value, $where, $order);
    }

}

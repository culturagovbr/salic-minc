<?php

require_once 'Zend/Db/Table/Abstract.php';

abstract class MinC_Db_Table_Abstract extends Zend_Db_Table_Abstract
{

    private $_config;
    protected $_rowClass = "MinC_Db_Table_Row";
    protected $debugMode = false;
    protected $modelDatatable;
    protected $_banco;

    public function __construct($config = array())
    {
        $this->createConnection();

        $this->modelDatatable = new \MinC\Db\Model\DataTable($config);
        parent::__construct($config);
    }

    public function init()
    {
        $this->_name = $this->getName($this->_name);
        $this->_banco = $this->getBanco($this->_banco);
        $this->_schema = $this->getSchema($this->_schema);
    }

    public function getSchema($strSchema = null, $isReturnDb = true, $strNameDb = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        if ($db instanceof Zend_Db_Adapter_Pdo_Mssql) {
            if (is_null($strNameDb)) {
                $strNameDb = 'dbo';
            }

            if ($isReturnDb && strpos($strSchema, '.') === false) {
                if ($strSchema) {
                    $strSchema = $strSchema . "." . $strNameDb;
                } else {
                    $strSchema = $strNameDb;
                }
            } elseif (strpos($strSchema, '.') === false) {
                $strSchema = $strNameDb;
            }
        } else if (!$strSchema) {
            $strSchema = $this->_schema;
        }

        return $strSchema;
    }


    public function setDebugMode($boolean)
    {
        $this->debugMode = $boolean;
    }

    public function getPrimary()
    {
        return (isset($this->_primary)) ? $this->_primary : '';
    }

    public function getSequence()
    {
        return (isset($this->_sequence)) ? $this->_sequence : true;
    }

    private function createConnection()
    {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();

        $db = Zend_Db_Table::getDefaultAdapter();
        $arrConfig = $db->getConfig();
        if ($dbAdapter instanceof Zend_Db_Adapter_Pdo_Mssql && strtolower($arrConfig['dbname']) != strtolower($this->_schema)) {
            if (!empty($dbAdapter)) {
                $dbAdapter->closeConnection();
                unset ($dbAdapter);
            }

            if (!($this->_config instanceof Zend_Config_Ini)) {

                $db = Zend_Db_Table::getDefaultAdapter();
                $arrConfig = $db->getConfig();
                $strDb = str_replace(array('.scAGENTES', '.scSAC', '.dbo', '.scdne', '.scsac', '.scQuiz'), '', $this->_schema);
                $strDb = str_replace('.sccorp', '', strtolower($strDb));

                $arrConfig['dbname'] = strtoupper($strDb);

                $this->_config = new Zend_Config(
                    array_merge(
                        Zend_Registry::get('config')->toArray(),
                        array(
                            'db' => array(
                                'adapter' => 'PDO_MSSQL',
                                'params' => array(
                                    'username' => $arrConfig['username'],
                                    'password' => $arrConfig['password'],
                                    'dbname' => $arrConfig['dbname'],
                                    'host' => $arrConfig['host'],
                                    'port' => $arrConfig['port'],
                                    'charset' => $arrConfig['charset'],
                                    'pdoType' => $arrConfig['pdoType'],
                                )
                            )
                        )
                    )
                );

                Zend_Registry::getInstance()->set('config', $this->_config);
                Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($this->_config->db));

                # Setar o campo texto maior que 4096 caracteres aceitaveis por padrao no PHP
                //$db->query(' SET TEXTSIZE 2147483647 ');
                ini_set('memory_limit', '-1');
            }
        } else if (is_int(strpos($this->_schema, 'scdne'))) {
            $this->_schema = str_replace('.scdne', '', $this->_schema);
        } else if (is_int(strpos($this->_schema, 'scCorp'))) {
            $this->_schema = str_replace('.scCorp', '', $this->_schema);
        } else if (is_int(strpos($this->_schema, 'scsac'))) {
            $this->_schema = str_replace('.scsac', '', $this->_schema);
        } else if (is_int(strpos($this->_schema, 'scSAC'))) {
            $this->_schema = str_replace('.scSAC', '', $this->_schema);
        }
    }

    /**
     * @param string $strName
     * @return string
     *
     * @todo melhorar e amadurecer codigo
     */
    public function getBanco($strName = 'dbo')
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!($db instanceof Zend_Db_Adapter_Pdo_Mssql)) {
            $arrayConfiguracaoes = $db->getConfig();
            $strName = $arrayConfiguracaoes['dbname'];
        }

        return $strName;
    }

    /**
     * @param string $strName
     * @param string $strSchema
     * @return string
     * @todo melhorar e amadurecer codigo
     */
    public function getName($strName = '', $strSchema = '')
    {
        $strName = strtolower($strName);
        return $strName;
    }

    public function getTableName($schema = null, $tableName = null, $isReturnDb = true)
    {
        if ($schema === null) $schema = $this->getSchema($schema, $isReturnDb);
        if ($tableName === null) $tableName = $this->_name;

        return $schema . '.' . $this->getName($tableName);
    }

    public static function getStaticTableName($schema = null, $tableName = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        if ($db instanceof Zend_Db_Adapter_Pdo_Mssql && $schema) {
            $arrayPedacos = explode('.', $schema);
            if (count($arrayPedacos) <= 1) {
                $schema = $schema . '.dbo';
            }
        }

        return $schema . $tableName;
    }

    public function __destruct()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->closeConnection();
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscar($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        try {

            $select = $this->select()->from($this->_name, $this->_getCols(), $this->_schema);

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                if (!is_null($valor)) {
                    $select->where($coluna, $valor);
                }
            }
            $select->order($order);

            // paginacao
            if ($tamanho > -1) {
                $tmpInicio = 0;
                if ($inicio > -1) {
                    $tmpInicio = $inicio;
                }
                $select->limit($tamanho, $tmpInicio);
            }

            if ($this->debugMode === true) {
                xd($select->assemble());
            }

            return $this->fetchAll($select);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function alterar($dados, $where)
    {
        if ($this->debugMode === true) {
            xd($this->dbg($dados, $where));
        }
        $update = $this->update($dados, $where);
        return $update;
    }

    public function apagar($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }

    public function inserir($dados, $dbg = null)
    {
        if ($dbg) {
            xd($this->dbg($dados));
        }
        $insert = $this->insert($dados);
        return $insert;
    }

    public function dbg($dados, $where = null)
    {
        if (!$where) {
            $sql = "INSERT INTO " . $this->_name . " (";
            $keys = array_keys($dados);
            $sql .= implode(',', $keys);
            $sql .= ")\n values ('";
            $values = array_values($dados);
            $sql .= implode("','", $values);
            $sql .= "');";
        } else {
            $sql = "UPDATE " . $this->_name . " SET ";
            foreach ($dados as $coluna => $valor) {
                $sql .= $coluna . " = '" . $valor . "', \n";
            }
            $sql .= "\n" . $where;
        }
        xd($sql);
    }

    /**
     * @param Zend_Db_Table_Abstract::SELECT_WITHOUT_FROM_PART $withFromPart
     * @return MinC_Db_Table_Select
     */
    public function select($withFromPart = self::SELECT_WITHOUT_FROM_PART)
    {
        require_once 'Zend/Db/Table/Select.php';
        $select = new MinC_Db_Table_Select($this);
        if ($withFromPart == self::SELECT_WITH_FROM_PART) {
            $select->from($this->info(self::NAME), $this->_getCols(), $this->info(self::SCHEMA));
        }
        return $select;
    }

    /**
     *
     * @name findBy
     * @param array $where
     * @return array
     */
    public function findBy($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this->_name, $this->_getCols(), $this->_schema);
        self::setWhere($select, $where);
        $result = $this->fetchRow($select);
        return ($result) ? $result->toArray() : array();
    }

    /**
     * Metodo responsavel por adicionar o where dinamicamente no objeto select.
     * Altera o proprio select, usando o select como referencia e nao retornando nada.
     *
     * @name setWhere
     * @param $select - Objeto select da query montada para no final por os parametros do where.
     * @param $where - Array ou string onde a string e considerado uma pk.
     * @return void
     */
    public function setWhere(&$select, $where, $typeWhere = 'where')
    {
        if (is_array($where)) {
            foreach ($where as $columnName => $columnValue) {
                if (is_int(strpos($columnName, '?'))) {
                    if (is_array($columnValue)) {
                        $select->{$typeWhere}($columnName, $columnValue);
                    } else {
                        $select->{$typeWhere}($columnName, trim($columnValue));
                    }
                } elseif (!$columnValue) {
                    $select->{$typeWhere}($columnName);
                } else {
                    $select->{$typeWhere}($columnName . ' = ?', trim($columnValue));
                }
            }
        } else {
            $select->{$typeWhere}(reset($this->getPrimary()) . ' = ?', trim($where));
        }
    }

    /**
     * Deleta varios registros conforme o where.
     * @name deleteBy
     * @param array $arrWhere - Array com os parametros para o where, onde chave do array e a coluna da tabela e o valor do array e o valor da coluna.
     * @return int - Quantidade de rows deletadas.
     * @throws Exception
     */
    public function deleteBy(array $arrWhere)
    {

        if (empty($arrWhere)) throw new Exception('Parametro where vazio, voce nao vai querer deletar tudo da tabela vai?');
        $arrWhereNew = array();
        foreach ($arrWhere as $columnName => $columnValue) {
            $arrWhereNew[] = $this->getAdapter()->quoteInto($columnName . ' = ?', trim($columnValue));
        }
        return $this->delete($arrWhereNew);
    }

    /**
     * @name findAll
     * @param array $where
     * @return array
     */
    public function findAll(array $where = array(), array $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        self::setWhere($select, $where);
        if ($order) {
            $select->order($order);
        }
        $result = $this->fetchAll($select);
        return ($result) ? $result->toArray() : array();
    }

    /**
     * @param Zend_Db_Table_Abstract::SELECT_WITHOUT_FROM_PART $withFromPart
     * @return MinC_Db_Table_Select
     * @return string
     */
    public static function getConcatExpression()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($db instanceof Zend_Db_Adapter_Pdo_Mssql) {
            return ' + ';
        }
        return " || ";
    }

    public function getExpressionDate()
    {
        if ($this->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
            return new Zend_Db_Expr('GETDATE()');
        } else {
            return new Zend_Db_Expr('NOW()');
        }
    }

    public function getExpressionConcat()
    {
        if ($this->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
            return ' + ';
        } else {
            return ' || ';
        }
    }

    public function getExpressionToChar($strColumn, $strFormat = 'DD/MM/YYYY ')
    {
        if ($this->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
            if (is_int($strFormat)) {
                return new Zend_Db_Expr('CONVERT(CHAR(30), ' . $strColumn . ' , ' . $strFormat . ')');
            } else {
                return new Zend_Db_Expr('CONVERT(CHAR(10), ' . $strColumn . ' , 103)');
            }
        } else {
            return new Zend_Db_Expr('TO_CHAR(' . $strColumn . ', \'' . $strFormat . '\')');
        }
    }

    public function getExpressionLimit($intLimit = 1)
    {
        if ($this->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
            return new Zend_Db_Expr("TOP {$intLimit}");
        } else {
            return new Zend_Db_Expr("LIMIT {$intLimit}");
        }
    }

    public function getExpressionTrim($string, $strAlias = null)
    {
        if ($this->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
            $strTrim = 'ltrim';
        } else {
            $strTrim = 'trim';
        }

        if (is_null($strAlias)) {
            return new Zend_Db_Expr("{$strTrim}( {$string})");
        } else {
            return new Zend_Db_Expr("({$strTrim} ({$string})) as {$strAlias}");
        }
    }

    public function getExpressionDateDiff($strColumn, $strColumnTwo, $strDatePart = 'day')
    {
        if ($this->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
            return new Zend_Db_Expr("DATEDIFF ( {$strDatePart} , {$strColumn} , {$strColumnTwo})");
        } else {
            return new Zend_Db_Expr("DATE_PART('{$strDatePart}', {$strColumn}) - DATE_PART('{$strDatePart}', {$strColumnTwo})");
        }
    }

    public function getExpressionDatePart($strColumn, $strDatePart = 'month')
    {
        if ($this->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
            return new Zend_Db_Expr("{$strDatePart}({$strColumn})");
        } else {
            return new Zend_Db_Expr("DATE_PART('{$strDatePart}', {$strColumn})");
        }
    }


    /**
     * Retorna o resultado com chave e valor apenas.
     * @name fetchPairs
     * @param string $key
     * @param string $value
     * @param array $where
     * @param string $order
     * @return array
     */
    public function fetchPairs($key, $value, array $where = [], $order = '')
    {
        if (empty($order)) $order = $value;

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->order($order);

        foreach ($where as $column => $columnValue) {
            if (is_array($columnValue)) {
                $select->where($column . ' IN (?)', $columnValue);
            } else {
                $select->where($column . ' = ?', $columnValue);
            }
        }

        $resultSet = $this->fetchAll($select);
        $resultSet = ($resultSet) ? $resultSet->toArray() : array();
        $entries = array();
        foreach ($resultSet as $row) {
            $entries[$row[$key]] = $row[$value];
        }
        return $entries;
    }

    protected function tratarResultadoOrdenacaoDatatable(\MinC_Db_Table_Select $query): \MinC_Db_Table_Select
    {
        $order = $this->modelDatatable->getOrder();

        if (
            isset($order[0])
            && isset($order[0]['column'])
            && isset($order[0]['dir'])
            && !empty($order[0]['column'])
            && !empty($order[0]['dir'])
        ) {
            $identificadorDaColuna = $order[0]['column'];
            $columns = $this->modelDatatable->getColumns();
            if(isset($columns[$identificadorDaColuna]['name']) && !empty($columns[$identificadorDaColuna]['name'])) {
                $query->order("{$columns[$identificadorDaColuna]['name']} {$order[0]['dir']}");
            }
        }

        return $query;
    }

    protected function tratarLimiteResultadosDatatable(\MinC_Db_Table_Select $query): \MinC_Db_Table_Select
    {
        $length = $this->modelDatatable->getLength();
        $start = $this->modelDatatable->getStart();

        if (!is_null($start) && $length) {
            $start = (int) $start;
            $length = (int) $length;
            $query->limit($length, $start);
        }

        return $query;
    }

    protected function tratarBuscaPorResultadosDatatable(\MinC_Db_Table_Select $query): \MinC_Db_Table_Select
    {
        $queryExterna = $this->select();
        $queryExterna->isUseSchema(false);
        $queryExterna->setIntegrityCheck(false);
        $queryExterna->from(
            ['tabelaTemporaria' => new Zend_Db_Expr("($query)")],
            '*'
        );

        $search = $this->modelDatatable->getSearch();
        $columns = $this->modelDatatable->getColumns();

        if (!empty($search['value']) && count($columns) > 0) {
            foreach($columns as $column) {
                if($column['searchable'] === 'true' && !empty($column['name'])) {

                    $queryExterna->orWhere(
                        "{$column['name']} like ?",
                        "%{$search['value']}%"
                    );
                }
            }
        }

        return $queryExterna;
    }

    protected function filtrarBuscaDatatable(\MinC_Db_Table_Select $query): \MinC_Db_Table_Select
    {
        $query = $this->tratarLimiteResultadosDatatable($query);
        $queryExterna = $this->tratarBuscaPorResultadosDatatable($query);
        $queryExterna = $this->tratarResultadoOrdenacaoDatatable($queryExterna);

        return $queryExterna;
    }

}

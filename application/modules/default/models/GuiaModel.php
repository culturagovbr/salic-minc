<?php 
/**
 *
 */
class GuiaModel
{
    private $guia;
    private $categoria;
    private $nome;
    private $descricao;

    private $table;

    /**
     * @param integer $guia
     * @param integer $categoria
     * @param string $nome
     * @param string $descricao
     */
    public function __construct($guia = null, $categoria = null, $nome = null, $descricao = null)
    {
        $this->guia = $guia;
        $this->categoria = $categoria;
        $this->nome = $nome;
        $this->descricao = $descricao;

        $this->table = new GuiaTable();
    }

    /**
     * @throws Exception
     */
    public function validarCadastrar()
    {
        if (!$this->categoria) {
            throw new Exception('Categoria inválida para cadastro / edição de Guia.');
        }
        if (!$this->nome) {
            throw new Exception('Nome inválido para cadastro / edição de Guia.');
        }
    }

    /**
     * @throws Exception
     */
    public function validarEditar()
    {
        if (!$this->guia) {
            throw new Exception('Identificador inválido para edição da guia.');
        }
    }

    /**
     * Efetua o cadastro da guia
     *
     * @return integer
     */
    public function cadastrar()
    {
        $this->validarCadastrar();
        return $this->guia = $this->table->insert(
            array(
                'idCategoria' => $this->categoria,
                'nmGuia' => $this->nome,
                'txAuxilio' => $this->descricao,
            )
        );
    }

    /**
     * Efetua a atualizacao da guia
     *
     * @return integer
     */
    public function atualizar()
    {
        $this->validarEditar();
        return $this->table->update(
            array(
                'idCategoria' => $this->categoria,
                'nmGuia' => $this->nome,
                'txAuxilio' => $this->descricao,
            ),
            array('idGuia = ?' => $this->guia)
        );
    }

    /**
     * Efetua a delecao da guia
     *
     * @return integer
     */
    public function deletar()
    {
        return $this->table->delete(array('idGuia = ?' => $this->guia));
    }

    /**
     * Pesquisar as guias usando como filtro o identificador da mesma
     *
     * @param integer guia
     * @return array
     */
    public function pesquisar($guia)
    {
        return $this->table->find($guia)->toArray();
    }

    /**
     * Pesquisar as guias usando como filtros: edital, modulo e categoria
     *
     * @param integer categoria
     * @param integer modulo
     * @return array
     */
    public function pesquisarPorEditalModuloCategoria($categoria = null, $modulo = null, $dbg = null)
    {
        $select = $this->table->select();
        $select->setIntegrityCheck(false);
        
        $select->from(
        
            array('g' => 'tbGuia'),
                        array('g.idGuia',
                              'g.nmGuia',
                              'g.txAuxilio',
                              'g.orGuia')
        );
        
        $select->joinInner(
        
            array('c' => 'tbCategoria'),
        
            'c.idCategoria = g.idCategoria',
                            array('c.idCategoria',
                                  'c.idModulo',
                                  'c.nmCategoria')
        );
        

        if ($categoria) {
            $select->where('c.idCategoria= ?', $categoria);
        }
        
        if ($modulo) {
            $select->where('c.idModulo = ?', $modulo);
        }
        
        if ($dbg) {
            xd($select->assemble());
        }
        
        $select->order('idGuia ASC');
        
        
        return $this->table->fetchAll($select)->toArray();
    }

    /**
     *
     */
    public function toStdClass()
    {
        $obj = new stdClass();
        $obj->guia = $this->guia;
        $obj->categoria = $this->categoria;
        $obj->nome = $this->nome;
        $obj->descricao = $this->descricao;
        return $obj;
    }
}

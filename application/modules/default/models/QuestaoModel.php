<?php 
/**
 *
 */
class QuestaoModel
{
    private $questao;
    private $guia;
    private $pergunta;
    private $ordem;

    private $table;

    /**
     * @param integer $questao
     * @param integer $guia
     * @param string $pergunta
     * @param integer $ordem
     */
    public function __construct($questao = null, $guia = null, $pergunta = null, $dsAjuda = null, $idTpResposta = null, $ordem = null)
    {
        $this->questao      = $questao;
        $this->guia         = $guia;
        $this->pergunta     = $pergunta;
        $this->dsAjuda      = $dsAjuda;
        $this->idTpResposta = $idTpResposta;
        $this->ordem        = $ordem;

        $this->table = new QuestaoTable();
    }

    /**
     * @throws Exception
     */
    public function validarCadastrar()
    {
        if (!$this->guia) {
            throw new Exception('Guia inválida para cadastro / edição de questão.');
        }
        if (!$this->pergunta) {
            throw new Exception('Pergunta inválida para cadastro / edição de questão.');
        }
    }

    /**
     * @throws Exception
     */
    public function validarEditar()
    {
        if (!$this->questao) {
            throw new Exception('Identificador inválido para edição de questão.');
        }
    }

    /**
     * Efetua o cadastro da questao
     *
     * @return integer
     */
    public function cadastrar()
    {
        $this->validarCadastrar();
        return $this->questao = $this->table->insert(
            array(
                'idGuia'        => $this->guia,
                'dsQuestao'     => $this->pergunta,
                'dsAjuda'       => $this->dsAjuda,
                'idTpResposta'  => $this->idTpResposta,
                'orQuestao'     => $this->ordem,
            )
        );
    }

    /**
     * Efetua a atualizacao da questao
     *
     * @return integer
     */
    public function atualizar()
    {
        $this->validarEditar();
        return $this->table->update(
            array(
                'idGuia' => $this->guia,
                'dsQuestao' => $this->pergunta,
            ),
            array('idQuestao = ?' => $this->questao)
        );
    }

    /**
     * Efetua a delecao da guia
     *
     * @return integer
     */
    public function deletar()
    {
        $respostaModel = new RespostaModel(null, null, $this->questao);
        $respostaModel->deletarPorQuestao();
        return $this->table->delete(array('idQuestao = ?' => $this->questao));
    }

    /**
     * Pesquisar a questao usando como filtros o identificador da mesma
     *
     * @param integer questao
     * @return array
     */
    public function pesquisar($questao)
    {
        return $this->table->find($questao)->toArray();
    }

    /**
     * Pesquisar as questaos usando como filtros: edital, modulo e categoria
     *
     * @param integer categoria
     * @param integer modulo
     * @param integer edital
     * @return array
     *
     * @todo apos remodelar para "tipo questao" remover esses campos via ZDExpr
     */
    public function pesquisarPorGuiaCategoriaModuloEdital($guia = null, $categoria = null, $modulo = null, $edital = null)
    {
        $select = $this->table->select()->setIntegrityCheck(false)
            ->from(
                array('que' => 'tbQuestao'),
                array(
                    '*',
                    'idTpQuestao' => new Zend_Db_Expr(
                        '(select top 1 tpr.idTpResposta from tbResposta join tbTipoResposta as tpr on tpr.idTpResposta = tbResposta.idTpResposta where tbResposta.idQuestao = que.idQuestao)'
                    ),
                    'dsTpResposta' => new Zend_Db_Expr(
                        '(select top 1 tpr.dsTpResposta from tbResposta join tbTipoResposta as tpr on tpr.idTpResposta = tbResposta.idTpResposta where tbResposta.idQuestao = que.idQuestao)'
                    ),
                )
            )->join(array('gia' => 'tbGuia'), 'gia.idGuia = que.idGuia', array())
            ->join(array('cat' => 'tbCategoria'), 'cat.idCategoria = gia.idCategoria', array())
            ->join(array('modp' => 'tbModuloParticipacao'), 'modp.idModulo = cat.idModulo', array())
            ->order('idGuia ASC')
        ;

        if ($guia) {
            $select->where('gui.idGuia = ?', $guia);
        }
        if ($categoria) {
            $select->where('cat.idCategoria = ?', $categoria);
        }
        if ($modulo) {
            $select->where('cat.idModulo = ?', $modulo);
        }
        if ($edital) {
            $select->where('idEdital = ?', $edital);
        }

        return $this->table->fetchAll($select)->toArray();
    }

    public function buscarQuestoesPorGuia($guia, $dbg = false)
    {
        $select = $this->table->select();
        $select->setIntegrityCheck(false);
        
        $select->from(
        
            array('q' => 'tbQuestao'),
                        array('q.idQuestao',
                              'q.dsQuestao',
                              'q.dsAjuda',
                              'orQuestao' => new Zend_Db_Expr('isnull(q.orQuestao, 0)'))
        );
        
        $select->joinInner(
        
            array('g' => 'tbGuia'),
        
            'q.idGuia = g.idGuia',
                            array('g.idGuia',
                                  'g.nmGuia',
                                  'g.txAuxilio',
                                  'g.idCategoria',
                                  'g.orGuia')
        );

        $select->joinInner(
            array('tr' => 'tbTipoResposta'),
            'q.idTpResposta = tr.idTpResposta',
                            array('tr.idTpResposta',
                                  'tr.dsTpResposta')
        );

        $select->where('q.idGuia = ?', $guia);
        
        $select->order('q.orQuestao');
        $select->order('q.idQuestao');
        
        if ($dbg) {
            xd($select->assemble());
        }

        return $this->table->fetchAll($select)->toArray();
    }

    /**
     *
     */
    public function toStdClass()
    {
        $obj = new stdClass();
        $obj->questao = $this->questao;
        $obj->guia = $this->guia;
        $obj->pergunta = $this->pergunta;
        $obj->ordem = $this->ordem;
        return $obj;
    }
}

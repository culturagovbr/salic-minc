<?php 
class RespostaModel
{
    const TEXT = 4;
    const TEXTAREA = 5;
    const RADIO = 6;
    const CHECKBOX = 7;
    const ANEXO = 9;

    protected $resposta;
    protected $tipoResposta;
    protected $questao;
    protected $nome;

    protected $table;

    /**
     * @param integer $resposta
     * @param string $nome
     */
    public function __construct($resposta = null, $tipoResposta = null, $questao = null, $nome = null)
    {
        $this->resposta = $resposta;
        $this->tipoResposta = $tipoResposta;
        $this->questao = $questao;
        $this->nome = $nome;

        $this->table = new RespostaTable();
    }

    /**
     * @throws Exception
     */
    public function validarCadastrar()
    {
        if (!$this->tipoResposta) {
            throw new Exception('Tipo de resposta inválida para cadastro / edição de resposta.');
        }
        if (!$this->questao) {
            throw new Exception('Questão inválida para cadastro / edição de resposta.');
        }
        if (in_array($this->tipoResposta, array(self::RADIO, self::CHECKBOX)) && !$this->nome) {
            throw new Exception('Nome inválido para cadastro / edição de resposta.');
        }
    }

    /**
     * @throws Exception
     */
    public function validarEditar()
    {
        if (!$this->resposta) {
            throw new Exception('Identificador inválido para edição de resposta.');
        }
    }

    /**
     * Efetua o cadastro da resposta
     *
     * @return integer
     */
    public function cadastrar()
    {
        $this->validarCadastrar();
        return $this->resposta = $this->table->insert(
            array(
                'idTpResposta' => $this->tipoResposta,
                'idQuestao' => $this->questao,
                'dsResposta' => $this->nome,
            )
        );
    }

    /**
     * Efetua a atualizacao da resposta
     *
     * @return integer
     */
    public function atualizar()
    {
        $this->validarEditar();
        return $this->table->update(
            array(
                'idTpResposta' => $this->tipoResposta,
                'dsResposta' => $this->nome,
            ),
            array('idResposta = ?' => $this->resposta)
        );
    }

    /**
     * Efetua a delecao da resposta usando como filtro o identificador da mesma
     *
     * @return integer
     */
    public function deletar()
    {
        return $this->table->delete(array('idResposta = ?' => $this->resposta));
    }

    /**
     * Efetua a delecao da resposta usando como filtro questao
     *
     * @return integer
     */
    public function deletarPorQuestao()
    {
        return $this->table->delete(array('idQuestao = ?' => $this->questao));
    }

    /**
     * Pesquisar a resposta pelo identificador
     *
     * @return array
     */
    public function pesquisar($resposta)
    {
        return $this->table->find($resposta)->toArray();
    }

    /**
     *
     */
    public function pesquisarPorTipoQuestao($tipoResposta = null, $questao = null)
    {
        $select = $this->table->select()->from(array('que' => 'tbResposta'), array('*'));

        if ($tipoResposta) {
            $select->where('idTpResposta = ?', $tipoResposta);
        }
        if ($questao) {
            $select->where('idQuestao = ?', $questao);
        }

        return $this->table->fetchAll($select)->toArray();
    }

    public function toStdClass()
    {
        $obj = new stdClass();
        $obj->resposta = $this->resposta;
        $obj->tipoResposta = $this->tipoResposta;
        $obj->questao = $this->questao;
        $obj->nome = $this->nome;
        return $obj;
    }
}

<?php 
/**
 *
 */
class TipoRespostaModel
{
    private $tipoResposta;
    private $nome;

    private $table;

    /**
     * @param integer $resposta
     * @param string $nome
     */
    public function __construct($tipoResposta = null, $nome = null)
    {
        $this->tipoResposta = $tipoResposta;
        $this->nome = $nome;

        $this->table = new QuestaoTable();
    }

    /**
     * Pesquisar os tipos de resposta
     *
     * @return array
     */
    public function pesquisar()
    {
        $select = $this->table->select()->setIntegrityCheck(false)
            ->from(array('tres' => 'tbTipoResposta'), array('*'))
            ->order('idTpResposta ASC')
        ;
        return $this->table->fetchAll($select)->toArray();
    }

    /**
     *
     */
    public function toStdClass()
    {
        $obj = new stdClass();
        $obj->tipoResposta = $this->tipoResposta;
        $obj->nome = $this->nome;
        return $obj;
    }
}

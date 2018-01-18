<?php

/**
 * Description of CargoAssinantePrestacaoDeConstas
 *
 * @author Mikhail Cavalcanti <mikhail.leite@xti.com.br>
 */
class CargoAssinantePrestacaoDeConstasModel implements ModelInterface
{

    /**
     *
     * @var type
     */
    private $table = null;

    /**
     *
     * @var int
     */
    private $id = null;

    /**
     *
     * @var string
     */
    private $cargo = null;

    /**
     *
     * @var string
     */
    private $justificativa = null;

    /**
     *
     */
    public function __construct()
    {
        //Gambiarra escrota porque eu não quero herdar do GenericModel para poder conectar ao banco que preciso (SAC)
        new CertidoesNegativas();
        $this->table = new CargoAssinantePrestacaoDeContasTable();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCargo()
    {
        return $this->cargo;
    }

    public function getJustificativa()
    {
        return $this->justificativa;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setCargo($cargo)
    {
        $this->cargo = $cargo;
        return $this;
    }

    public function setJustificativa($justificativa)
    {
        $this->justificativa = $justificativa;
        return $this;
    }

    /**
     *
     * @param int $id
     */
    public function buscar($id = null)
    {
        if (empty($id)) {
            return $this->table->fetchAll($this->table->select());
        }
        throw new Exception('Não iplementado');
    }

    /**
     *
     * @param type $id
     * @throws Exception
     */
    public function deletar($id)
    {
        throw new Exception('Não implementado');
    }

    /**
     *
     * @throws Exception
     */
    public function salvar()
    {
        $this->validar();
        $this->table->insert(array(
            'nmCargo' => $this->getCargo(),
            'dsJustificativa' => $this->getJustificativa(),
        ));
    }

    /**
     *
     */
    public function atualizar()
    {
        throw new Exception('Não implementado');
    }

    /**
     *
     * @throws InvalidArgumentException
     */
    private function validar()
    {
        $cargo = $this->getCargo();
        if (empty($cargo)) {
            throw new InvalidArgumentException('Cargo obrigatório');
        }
    }
}

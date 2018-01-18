<?php
/**
 * @author Mikhail Cavalcanti <mikhailcavalcanti@gmail.com>
 */
class FornecedorInvoice extends MinC_Db_Table_Abstract
{
    private $id = null;
    private $nome = null;
    private $endereco = null;
    private $pais = null;

    protected $_banco = 'bdcorporativo';
    protected $_schema = 'bdcorporativo.scSAC';
    protected $_name = 'tbFonecedorExterior';

    public function __construct($id = null, $nome = null, $endereco = null, $pais = null)
    {
        parent::__construct();
        $this->id = $id;
        $this->nome = $nome;
        $this->endereco = $endereco;
        $this->pais = $pais;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getEndereco()
    {
        return $this->endereco;
    }

    public function getPais()
    {
        return $this->pais;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
    }

    public function setPais($pais)
    {
        $this->pais = $pais;
    }
}

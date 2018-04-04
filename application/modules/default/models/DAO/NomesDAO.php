<?php

class NomesDAO extends MinC_Db_Table_Abstract
{
    protected $_name = 'nomes';
    protected $_schema = 'agentes';


    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        parent::init();
    }

    public static function buscarNome($idAgente)
    {
        $sql = "Select idNome, idAgente, TipoNome, Descricao, Status, Usuario From AGENTES.dbo.Nomes Where idAgente =".$idAgente;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $dados =  $db->fetchAll($sql);

        return $dados;
    }


    /**
     * gravarNome
     *
     * @param mixed $idAgente
     * @param mixed $TipoNome
     * @param mixed $Descricao
     * @param mixed $Status
     * @param mixed $Usuario
     * @static
     * @access public
     * @return void
     */
    public static function gravarNome($idAgente, $TipoNome, $Descricao, $Status, $Usuario)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        return $db->insert();

        $sql = "Insert Into AGENTES.dbo.Nomes
            (idAgente, TipoNome, Descricao, Status, Usuario)
            values
            (".$idAgente.", ".$TipoNome.", '".$Descricao."', ".$Status.", ".$Usuario.")";



        $dados =  $db->query($sql);
    }

    /**
     * inserir
     *
     * @param mixed $idAgente
     * @param mixed $TipoNome
     * @param mixed $Descricao
     * @param mixed $Status
     * @param mixed $Usuario
     * @access public
     * @return void
     *
     * @todo remover futuramente.
     */
    public function inserir($idAgente, $TipoNome, $Descricao, $Status, $Usuario)
    {
        $dados = array(
            'idagente' => $idAgente,
            'tiponome' => $TipoNome,
            'descricao' => $Descricao,
            'status' => $Status,
            'usuario' => $Usuario
        );

        return $this->insert($dados);
    }

    public static function atualizaNome($idAgente, $TipoNome, $Descricao, $Status, $Usuario)
    {
        $sql = "Update AGENTES.dbo.Nomes set TipoNome = ".$TipoNome.", Descricao = '".$Descricao."', Status = ".$Status.", Usuario = ".$Usuario."	Where idAgente = ".$idAgente;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $dados =  $db->query($sql);
    }

    public static function atualizaNomeReadequacao($idAgente, $Descricao)
    {
        $sql = "Update AGENTES.dbo.Nomes set Descricao = '".$Descricao."' Where idAgente = ".$idAgente;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->query($sql);
    }
}

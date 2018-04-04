<?php
/**
 * DeslocamentoDAO
 *
 * @uses Zend
 * @uses _Db_Table
 * @author 01129075125
 * @author wouerner <wouerner@gmail.com>
 */
class DeslocamentoDAO extends MinC_Db_Table_Abstract
{
    protected $_banco = 'sac';
    protected $_schema = 'sac';
    protected $_name = 'tbdeslocamento';

    public function __construct()
    {
        parent::__construct();
    }
    public function init()
    {
        parent::init();
    }

    public static function buscarPais()
    {
        $sql = "SELECT * FROM AGENTES.dbo.Pais";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public function pais()
    {
        $sql = "SELECT * FROM " . DeslocamentoDAO::getStaticTableName('agentes', 'pais');

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        if ($resultado) {
            echo '<pre>';
            var_dump($resultado->toArray());
            exit;
        }

        return $resultado;
    }

    /**
     * buscarDeslocamentos
     *
     * @param mixed $idProjeto
     * @param bool $idDeslocamento
     * @static
     * @access public
     * @return void
     * @author wouerner <wouerner@gmail.com>
     */
    public static function buscarDeslocamentos($idProjeto, $idDeslocamento = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $agenteSchema = parent::getStaticTableName('agentes');

        $de = array(
            'de.idDeslocamento',
            'de.idProjeto',
            'de.idPaisOrigem',
            'de.idUFOrigem',
            'de.idMunicipioOrigem',
            'de.idPaisDestino',
            'de.idUFDestino',
            'de.Qtde',
            'de.idUsuario',
            'de.idMunicipioDestino'
        );

        $sql = $db->select()
            ->from(array('de' => 'tbDeslocamento'), $de, parent::getStaticTableName('tbdeslocamento'))
            ->joinLeft(array('paO'=>'Pais'), 'de.idPaisOrigem = paO.idPais', 'paO.Descricao AS PO', $agenteSchema)
            ->joinLeft(array('ufO'=>'UF'), 'de.idUFOrigem = ufO.idUF', 'ufO.Descricao AS UFO', $agenteSchema)
            ->joinLeft(array('muO' => 'Municipios'), 'de.idMunicipioOrigem = muO.idMunicipioIBGE', 'muO.Descricao AS MUO', $agenteSchema)
            ->joinLeft(array('paD' => 'Pais'), 'de.idPaisDestino = paD.idPais', 'paD.Descricao AS PD', $agenteSchema)
            ->joinLeft(array('ufD' => 'UF'), 'de.idUFDestino = ufD.idUF', 'ufD.Descricao AS UFD', $agenteSchema)
            ->joinLeft(array('muD' => 'Municipios '), 'de.idMunicipioDestino = muD.idMunicipioIBGE', 'muD.Descricao AS MUD', $agenteSchema)
            ->where("idProjeto = ?", $idProjeto)
            ;

        if ($idDeslocamento != null) {
            $sql->where('de.idDeslocamento = ?', $idDeslocamento);
        }

        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    /**
     * buscarDeslocamentos
     *
     * @param mixed $idProjeto
     * @param bool $idDeslocamento
     * @static
     * @access public
     * @return void
     * @author wouerner <wouerner@gmail.com>
     */
    public function buscarDeslocamento($idProjeto, $idDeslocamento = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $agenteSchema = $this->getSchema('agentes');

        $de = array(
            'de.iddeslocamento',
            'de.idprojeto',
            'de.idpaisorigem',
            'de.iduforigem',
            'de.idmunicipioorigem',
            'de.idpaisdestino',
            'de.idufdestino',
            'de.qtde',
            'de.idusuario',
            'de.idmunicipiodestino'
        );

        $sql = $db->select()
            ->from(array('de' => $this->_name), $de, $this->_schema)
            ->joinLeft(array('pao'=>'pais'), 'de.idpaisorigem = pao.idpais', 'pao.descricao as po', $agenteSchema)
            ->joinLeft(array('ufo'=>'uf'), 'de.iduforigem = ufo.iduf', 'ufo.descricao as ufo', $agenteSchema)
            ->joinLeft(array('muo' => 'municipios'), 'de.idmunicipioorigem = muo.idmunicipioibge', 'muo.descricao as muo', $agenteSchema)
            ->joinLeft(array('pad' => 'pais'), 'de.idpaisdestino = pad.idpais', 'pad.descricao as pd', $agenteSchema)
            ->joinLeft(array('ufd' => 'uf'), 'de.idufdestino = ufd.iduf', 'ufd.descricao as ufd', $agenteSchema)
            ->joinLeft(array('mud' => 'municipios'), 'de.idmunicipiodestino = mud.idmunicipioibge', 'mud.descricao as mud', $agenteSchema)
            ->where("idprojeto = ?", $idProjeto)
            ;

        if ($idDeslocamento != null) {
            $sql->where('de.iddeslocamento = ?', $idDeslocamento);
        }

        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscarDeslocamentosGeral($where = array(), $order = array())
    {
        $meuWhere = "";
        // adicionando clausulas where
        foreach ($where as $coluna=>$valor) {
            if ($meuWhere != "") {
                $meuWhere .= " AND ";
            }
            $meuWhere .= $coluna.$valor;
        }

        $meuOrder = "";
        // adicionando clausulas order
        foreach ($order as $valor) {
            if ($meuOrder != "") {
                $meuOrder .= " , ";
            } else {
                $meuOrder = " ORDER BY ";
            }
            $meuOrder .= $valor;
        }

        $sql = "SELECT
			    de.idDeslocamento,
			    de.idProjeto,
			    de.idPaisOrigem,
			    paO.Descricao PO,
			    de.idUFOrigem,
			    ufO.Descricao UFO,
			    de.idMunicipioOrigem,
			    muO.Descricao MUO,
			    de.idPaisDestino,
			    paD.Descricao PD,
			    de.idUFDestino,
			    ufD.Descricao UFD,
			    de.idMunicipioDestino,
			    muD.Descricao MUD,
			    de.Qtde,
			    de.idUsuario

			FROM
			    SAC.dbo.tbDeslocamento de
			   	LEFT JOIN AGENTES.dbo.Pais paO		 ON de.idPaisOrigem = paO.idPais
			    LEFT JOIN AGENTES.dbo.UF ufO		 ON de.idUFOrigem = ufO.idUF
			    LEFT JOIN AGENTES.dbo.Municipios muO ON de.idMunicipioOrigem = muO.idMunicipioIBGE
			    LEFT JOIN AGENTES.dbo.Pais paD		 ON de.idPaisDestino = paD.idPais
			    LEFT JOIN AGENTES.dbo.UF ufD		 ON de.idUFDestino = ufD.idUF
			    LEFT JOIN AGENTES.dbo.Municipios muD ON de.idMunicipioDestino = muD.idMunicipioIBGE
			WHERE
			    {$meuWhere}
                            $meuOrder";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }


    public static function salvaDeslocamento($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->insert('SAC.dbo.tbdeslocamento', $dados);
    }

    public static function atualizaDeslocamento($paisOrigem, $uf, $cidade, $paisDestino, $ufD, $cidadeD, $quantidade, $idDeslocamento)
    {
        $sql = "UPDATE SAC.dbo.tbDeslocamento SET idPaisOrigem=".$paisOrigem.", " .
                "idUFOrigem = ".$uf.", " .
                "idMunicipioOrigem = ".$cidade.", " .
                "idPaisDestino = ".$paisDestino.", " .
                "idUFDestino = ".$ufD.", " .
                "idMunicipioDestino = ".$cidadeD.", " .
                "Qtde = ".$quantidade." " .
                "WHERE idDeslocamento = ".$idDeslocamento;


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->query($sql);
    }

    public static function excluiDeslocamento($idDeslocamento)
    {
        $sql ="DELETE FROM SAC.dbo.tbDeslocamento WHERE idDeslocamento = ".$idDeslocamento;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->query($sql);
    }
}

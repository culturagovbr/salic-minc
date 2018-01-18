<?php

/**
 * Class Proposta_Model_DbTable_Abrangencia
 *
 * @name Proposta_Model_DbTable_Abrangencia
 * @package Modules/Agente
 * @subpackage Models/DbTable
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 20/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br

    idAbrangencia
    idProjeto
    idPais
    idUF
    idMunicipioIBGE
    Usuario
    stAbrangencia
    siAbrangencia
    dsJustificativa
    dtInicioRealizacaodtFimRealizacao
 */
class Proposta_Model_DbTable_Abrangencia extends MinC_Db_Table_Abstract
{
    /**
     * _schema
     *
     * @var string
     * @access protected
     */
    protected $_schema = 'sac';

    /**
     * _name
     *
     * @var bool
     * @access protected
     */
    protected $_name = 'abrangencia';

    /**
     * _primary
     *
     * @var bool
     * @access protected
     */
    protected $_primary = 'idAbrangencia';


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
        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('a' => 'abrangencia'), $this->_getCols(), $this->_schema)
            ->join(array('p' => 'pais'), 'a.idpais = p.idpais and a.stabrangencia = 1', 'p.descricao as pais', $this->getSchema('agentes'))
            ->joinLeft(array('u' => 'uf'), '(a.iduf = u.iduf)', 'u.descricao as uf', $this->getSchema('agentes'))
            ->joinLeft(array('m' => 'municipios'), '(a.idmunicipioibge = m.idmunicipioibge)', 'm.descricao as cidade', $this->getSchema('agentes'));
        foreach ($where as $coluna => $valor) {
            $sql->where($coluna . '= ?', $valor);
        }

        $result = $this->fetchAll($sql);
        return ($result) ? $result->toArray() : array();
    }

    public function verificarIgual($idPais, $idUF, $idMunicipio, $idPreProjeto)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('Ab' => $this->_name),
            $this->_getCols(),
            $this->_schema
        );
        $select->where('idProjeto = ?', $idPreProjeto);
        $select->where('idPais = ?', $idPais);
        $select->where("idUF = '?'", $idUF);
        $select->where("idMunicipioibge = '?'", $idMunicipio);
        $select->where('stAbrangencia = ?', 1);
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($select);
    }

    /**
     * Grava registro. Se seja passado um ID ele altera um registro existente
     * @param array $dados - array com dados referentes as colunas da tabela no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @return ID do registro inserido/alterado ou FALSE em caso de erro
     */
    public function salvar($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA

        //DECIDINDO SE INCLUI OU ALTERA UM REGISTRO
        $dados['stAbrangencia'] = 1;
        if (isset($dados['idAbrangencia']) && !empty($dados['idAbrangencia'])) {
            //UPDATE
            $rsAbrangencia = $this->find($dados['idAbrangencia'])->current();
        } else {
            //INSERT
            $dados['idAbrangencia'] = null;
            return $this->insert($dados);
            //$rsAbrangencia = $tblAbrangencia->createRow();
        }

        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (!empty($dados['idProjeto'])) {
            $rsAbrangencia->idProjeto = $dados['idProjeto'];
        }
        if (!empty($dados['idPais'])) {
            $rsAbrangencia->idPais = $dados['idPais'];
        }
        $rsAbrangencia->idUF = $dados['idUF']; //if(!empty($dados['idUF'])) { $rsAbrangencia->idUF = $dados['idUF']; }
        $rsAbrangencia->idMunicipioIBGE = $dados['idMunicipioIBGE'];//if(!empty($dados['idmunicipioibge'])) { $rsAbrangencia->idmunicipioibge = $dados['idmunicipioibge']; }
        if (!empty($dados['Usuario'])) {
            $rsAbrangencia->Usuario = $dados['Usuario'];
        }
        $rsAbrangencia->stAbrangencia = 1;

        //SALVANDO O OBJETO
        $id = $rsAbrangencia->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }

    /**
     * Apaga locais de ralizacao a partir do ID do PreProjeto
     * @param number $idProjeto - ID do PerProjeto ao qual as lcoaliza��es est�o vinculadas
     * @return true or false
     * @todo colocar padrao ORM
     */
    public function excluirPeloProjeto($idProjeto)
    {
        $sql = "DELETE FROM SAC.dbo.Abrangencia WHERE idProjeto = " . $idProjeto . " AND stAbrangencia = 1";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        if ($db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * abrangenciaProjeto
     *
     * @param bool $retornaSelect
     * @access public
     * @return void
     * @todo retirar Zend_Db_Expr
     */
    public function abrangenciaProjeto($retornaSelect = false)
    {
        $selectAbrangencia = $this->select();
        $selectAbrangencia->setIntegrityCheck(false);
        $selectAbrangencia->from(
            array($this->_name),
            array(
                'idAbrangencia' => new Zend_Db_Expr('min(idAbrangencia)'),
                'idProjeto',
                'idUF',
                'idMunicipioIBGE'
            )
        );
        $selectAbrangencia->group('idProjeto');
        $selectAbrangencia->group('idUF');
        $selectAbrangencia->group('idMunicipioIBGE');

        if ($retornaSelect) {
            return $selectAbrangencia;
        } else {
            return $this->fetchAll($selectAbrangencia);
        }
    }

    /**
     * abrangenciaProjetoPesquisa
     *
     * @param bool $retornaSelect
     * @param bool $where
     * @access public
     * @return void
     * @todo retirar Zend_Db_Expr
     */
    public function abrangenciaProjetoPesquisa($retornaSelect = false, $where = array())
    {
        $selectAbrangencia = $this->select();
        $selectAbrangencia->setIntegrityCheck(false);
        $selectAbrangencia->from(
            array('abr' => $this->_name),
            array(
                'idAbrangencia' => new Zend_Db_Expr('min(idAbrangencia)'),
                'idProjeto'
            )
        );

        $selectAbrangencia->joinInner(
            array('mun' => 'Municipios'),
            "mun.idUFIBGE = abr.idUF and mun.idMunicipioIBGE = abr.idMunicipioIBGE",
            array(),
            'AGENTES.dbo'
        );
        $selectAbrangencia->joinInner(
            array('uf' => 'UF'),
            "uf.idUF = abr.idUF",
            array(),
            'AGENTES.dbo'
        );
        $selectAbrangencia->where('abr.stAbrangencia = ?', 1);

        foreach ($where as $coluna => $valor) {
            $selectAbrangencia->where($coluna, $valor);
        }

        $selectAbrangencia->group('idProjeto');

        if ($retornaSelect) {
            return $selectAbrangencia;
        } else {
            return $this->fetchAll($selectAbrangencia);
        }
    }

    /**
     * M�todo para cadastrar
     * @access public
     * @
     * @param array $dados
     * @return bool
     */
    public function cadastrar($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("SAC.dbo.Abrangencia", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo cadastrar()


    /**
     * M�todo para excluir
     * @access public
     * @
     * @param array $dados
     * @return bool
     */
    public function excluir($where)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where = array("idAbrangencia = ? " => $where, "stAbrangencia = ?" => 1);

        // limpa a associa��o antes de excluir
        $alterar = $db->update("SAC.dbo.tbAbrangencia", array("idAbrangenciaAntiga" => null), array("idAbrangenciaAntiga = ? " => $where));

        $excluir = $db->delete("SAC.dbo.Abrangencia", $where);

        if ($excluir) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo excluir()


    /**
     * metodo para alterar
     * @access public
     * @
     * @param array $dados
     * @return bool
     * @todo verificar impactos e remover, faz a mesma coisa da abstract
     */
    public function alterar($dados, $where, $dbg = false)
    {
        $where = "idAbrangencia = $where";
        $alterar = $this->update($dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    } // fecha metodo alterar()


    public function buscarAbrangenciasAtuais($idProjeto, $idPais, $idUF, $idMunicipioIBGE)
    {
        $sql = "SELECT * from SAC.dbo.Abrangencia
                    WHERE
                        idProjeto = $idProjeto
                        and idPais = $idPais
                        and idUF = $idUF
                        and idMunicipioIBGE = $idMunicipioIBGE
                        and stAbrangencia = 1
                    ";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }


    public function buscarDadosAbrangenciaAlteracao($idpedidoalteracao, $avaliacao)
    {
        if ($avaliacao == "SEM_AVALIACAO") {
            $sql = "
            SELECT *, CAST(dsjustificativa AS text) as dsjustificativa FROM
            (
            SELECT
                    distinct (abran.idAbrangencia),
                    pais.Descricao pais,
                    uf.Descricao as uf,
                    mun.Descricao as mun,
                    abran.tpAcao as tpoperacao,
                    tpa.dsjustificativa,
taipa.idAvaliacaoItemPedidoAlteracao,
asipa.idAvaliacaoSubItemPedidoAlteracao,
asipa.stAvaliacaoSubItemPedidoAlteracao as avaliacao,
--CAST(asipa.dsAvaliacaoSubItemPedidoAlteracao AS TEXT) as dsAvaliacao
asipa.dsAvaliacaoSubItemPedidoAlteracao as dsAvaliacao,
abran.dsExclusao
                FROM
                    SAC.dbo.tbAbrangencia abran
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto proj on proj.idPedidoAlteracao = abran.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos pr on pr.IdPRONAC = proj.IdPRONAC
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpa on tpa.idPedidoAlteracao = abran.idPedidoAlteracao
                    INNER JOIN BDCORPORATIVO.scSAC.tbTipoAlteracaoProjeto ta on ta.tpAlteracaoProjeto = tpa.tpAlteracaoProjeto
                    INNER JOIN SAC.dbo.Abrangencia ab on ab.idProjeto = pr.idProjeto AND ab.stAbrangencia = 1
                    INNER JOIN Agentes.dbo.Pais	pais on pais.idPais = abran.idPais
            LEFT JOIN AGENTES.dbo.Uf uf on uf.idUF = abran.idUF
            LEFT JOIN AGENTES.dbo.Municipios mun on mun.idMunicipioIBGE = abran.idMunicipioIBGE
--INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao taipa ON taipa.idPedidoAlteracao = tpa.idPedidoAlteracao
--INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao taaipa ON taipa.idAvaliacaoItemPedidoAlteracao = taaipa.idAvaliacaoItemPedidoAlteracao
LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao taipa ON taipa.idPedidoAlteracao = tpa.idPedidoAlteracao
LEFT JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao taaipa ON taipa.idAvaliacaoItemPedidoAlteracao = taaipa.idAvaliacaoItemPedidoAlteracao

LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao asipa ON (taipa.idAvaliacaoItemPedidoAlteracao = asipa.idAvaliacaoItemPedidoAlteracao
	AND asipa.idAvaliacaoSubItemPedidoAlteracao = abran.idAbrangencia )
                WHERE
                    proj.IdPRONAC = $idpedidoalteracao and tpa.tpAlteracaoProjeto = 4 and taipa.tpAlteracaoProjeto = 4 and abran.tpAcao != 'N'
               --ORDER BY pais.Descricao, uf.Descricao, mun.Descricao, taipa.idAvaliacaoItemPedidoAlteracao DESC
            ) as minhaTabela
            ORDER BY pais, uf, mun, idAvaliacaoItemPedidoAlteracao DESC
            ";
        } // fecha if
        else {
            $sql = "
            SELECT *, CAST(dsjustificativa AS text) as dsjustificativa FROM
            (
            SELECT
                    distinct (abran.idAbrangencia),
                    pais.Descricao pais,
                    uf.Descricao as uf,
                    mun.Descricao as mun,
                    abran.tpAcao as tpoperacao,
                    tpa.dsjustificativa,
taipa.idAvaliacaoItemPedidoAlteracao,
abran.dsExclusao,
tasia.idAvaliacaoSubItemPedidoAlteracao,
tasipa.stAvaliacaoSubItemPedidoAlteracao as avaliacao,
tasipa.dsAvaliacaoSubItemPedidoAlteracao as dsAvaliacao,
taipa.stAvaliacaoItemPedidoAlteracao
                FROM
                    SAC.dbo.tbAbrangencia abran
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto proj on proj.idPedidoAlteracao = abran.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos pr on pr.IdPRONAC = proj.IdPRONAC
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpa on tpa.idPedidoAlteracao = abran.idPedidoAlteracao
                    INNER JOIN BDCORPORATIVO.scSAC.tbTipoAlteracaoProjeto ta on ta.tpAlteracaoProjeto = tpa.tpAlteracaoProjeto
                    INNER JOIN SAC.dbo.Abrangencia ab on ab.idProjeto = pr.idProjeto AND ab.stAbrangencia = 1
                    INNER JOIN Agentes.dbo.Pais	pais on pais.idPais = abran.idPais
            LEFT JOIN AGENTES.dbo.Uf uf on uf.idUF = abran.idUF
            LEFT JOIN AGENTES.dbo.Municipios mun on mun.idMunicipioIBGE = abran.idMunicipioIBGE
--INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao taipa ON taipa.idPedidoAlteracao = tpa.idPedidoAlteracao
--INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao taaipa ON taipa.idAvaliacaoItemPedidoAlteracao = taaipa.idAvaliacaoItemPedidoAlteracao
LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao taipa ON taipa.idPedidoAlteracao = tpa.idPedidoAlteracao
LEFT JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao taaipa ON taipa.idAvaliacaoItemPedidoAlteracao = taaipa.idAvaliacaoItemPedidoAlteracao

LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao asipa ON (taipa.idAvaliacaoItemPedidoAlteracao = asipa.idAvaliacaoItemPedidoAlteracao
	AND asipa.idAvaliacaoSubItemPedidoAlteracao = abran.idAbrangencia )

LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemAbragencia tasia ON (tasia.idAbrangencia = abran.idAbrangencia AND tasia.idAvaliacaoItemPedidoAlteracao = taipa.idAvaliacaoItemPedidoAlteracao)
LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao tasipa ON (tasipa.idAvaliacaoSubItemPedidoAlteracao = tasia.idAvaliacaoSubItemPedidoAlteracao AND tasipa.idAvaliacaoItemPedidoAlteracao = taipa.idAvaliacaoItemPedidoAlteracao)
                WHERE
                    proj.IdPRONAC = $idpedidoalteracao and tpa.tpAlteracaoProjeto = 4 and taipa.tpAlteracaoProjeto = 4 and abran.tpAcao != 'N'
                    --AND taipa.stAvaliacaoItemPedidoAlteracao in ('EA', 'AG')
               --ORDER BY pais.Descricao, uf.Descricao, mun.Descricao, taipa.idAvaliacaoItemPedidoAlteracao DESC
            ) as minhaTabela
            ORDER BY pais, uf, mun, idAvaliacaoItemPedidoAlteracao DESC
            ";
        } // fecha else

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }


    public function buscarDadosAbrangenciaAlteracaoCoord($idpedidoalteracao, $avaliacao)
    {
        if ($avaliacao == "SEM_AVALIACAO") {
            $sql = "SELECT * , CAST(dsjustificativa AS text) AS dsjustificativa , CAST(dsjustificativa AS text) AS dsjustificativa  FROM  (
                    SELECT
                    distinct (abran.idAbrangencia),
                    pais.Descricao pais,
                    uf.Descricao as uf,
                    mun.Descricao as mun,
                    abran.tpAcao as tpoperacao,
                    tpa.dsjustificativa,
taipa.idAvaliacaoItemPedidoAlteracao,
asipa.idAvaliacaoSubItemPedidoAlteracao,
asipa.stAvaliacaoSubItemPedidoAlteracao as avaliacao,
asipa.dsAvaliacaoSubItemPedidoAlteracao,
abran.dsExclusao
                FROM
                    SAC.dbo.tbAbrangencia abran
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto proj on proj.idPedidoAlteracao = abran.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos pr on pr.IdPRONAC = proj.IdPRONAC
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpa on tpa.idPedidoAlteracao = abran.idPedidoAlteracao
                    INNER JOIN BDCORPORATIVO.scSAC.tbTipoAlteracaoProjeto ta on ta.tpAlteracaoProjeto = tpa.tpAlteracaoProjeto
                    INNER JOIN SAC.dbo.Abrangencia ab on ab.idProjeto = pr.idProjeto AND ab.stAbrangencia = 1
                    INNER JOIN Agentes.dbo.Pais	pais on pais.idPais = abran.idPais
            LEFT JOIN AGENTES.dbo.Uf uf on uf.idUF = abran.idUF
            LEFT JOIN AGENTES.dbo.Municipios mun on mun.idMunicipioIBGE = abran.idMunicipioIBGE
--INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao taipa ON taipa.idPedidoAlteracao = tpa.idPedidoAlteracao
--INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao taaipa ON taipa.idAvaliacaoItemPedidoAlteracao = taaipa.idAvaliacaoItemPedidoAlteracao
LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao taipa ON taipa.idPedidoAlteracao = tpa.idPedidoAlteracao
LEFT JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao taaipa ON taipa.idAvaliacaoItemPedidoAlteracao = taaipa.idAvaliacaoItemPedidoAlteracao

LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao asipa ON (taipa.idAvaliacaoItemPedidoAlteracao = asipa.idAvaliacaoItemPedidoAlteracao
	AND asipa.idAvaliacaoSubItemPedidoAlteracao = abran.idAbrangencia )
                WHERE
                    proj.IdPRONAC = $idpedidoalteracao and tpa.tpAlteracaoProjeto = 4 and abran.tpAcao != 'N'
               ) AS TABELA ORDER BY pais, uf, mun, idAvaliacaoItemPedidoAlteracao DESC ";
        } // fecha if
        else {
            $sql = "SELECT * , CAST(dsjustificativa AS text) AS dsjustificativa, CAST(dsAvaliacao AS text) AS dsAvaliacao  FROM  (
                    SELECT
                    distinct (abran.idAbrangencia),
                    pais.Descricao pais,
                    uf.Descricao as uf,
                    mun.Descricao as mun,
                    abran.tpAcao as tpoperacao,
                    tpa.dsjustificativa,
                    taipa.idAvaliacaoItemPedidoAlteracao,
                    abran.dsExclusao,
                    tasia.idAvaliacaoSubItemPedidoAlteracao,
                    tasipa.stAvaliacaoSubItemPedidoAlteracao as avaliacao,
                    tasipa.dsAvaliacaoSubItemPedidoAlteracao as dsAvaliacao,
                    taipa.stAvaliacaoItemPedidoAlteracao
                FROM
                    SAC.dbo.tbAbrangencia abran
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto proj on proj.idPedidoAlteracao = abran.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos pr on pr.IdPRONAC = proj.IdPRONAC
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpa on tpa.idPedidoAlteracao = abran.idPedidoAlteracao
                    INNER JOIN BDCORPORATIVO.scSAC.tbTipoAlteracaoProjeto ta on ta.tpAlteracaoProjeto = tpa.tpAlteracaoProjeto
                    INNER JOIN SAC.dbo.Abrangencia ab on ab.idProjeto = pr.idProjeto AND ab.stAbrangencia = 1
                    INNER JOIN Agentes.dbo.Pais	pais on pais.idPais = abran.idPais
            LEFT JOIN AGENTES.dbo.Uf uf on uf.idUF = abran.idUF
            LEFT JOIN AGENTES.dbo.Municipios mun on mun.idMunicipioIBGE = abran.idMunicipioIBGE
--INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao taipa ON taipa.idPedidoAlteracao = tpa.idPedidoAlteracao
--INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao taaipa ON taipa.idAvaliacaoItemPedidoAlteracao = taaipa.idAvaliacaoItemPedidoAlteracao
LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao taipa ON taipa.idPedidoAlteracao = tpa.idPedidoAlteracao
LEFT JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao taaipa ON taipa.idAvaliacaoItemPedidoAlteracao = taaipa.idAvaliacaoItemPedidoAlteracao

LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao asipa ON (taipa.idAvaliacaoItemPedidoAlteracao = asipa.idAvaliacaoItemPedidoAlteracao
	AND asipa.idAvaliacaoSubItemPedidoAlteracao = abran.idAbrangencia )

LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemAbragencia tasia ON (tasia.idAbrangencia = abran.idAbrangencia AND tasia.idAvaliacaoItemPedidoAlteracao = taipa.idAvaliacaoItemPedidoAlteracao)
LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao tasipa ON (tasipa.idAvaliacaoSubItemPedidoAlteracao = tasia.idAvaliacaoSubItemPedidoAlteracao AND tasipa.idAvaliacaoItemPedidoAlteracao = taipa.idAvaliacaoItemPedidoAlteracao)
                WHERE
                    proj.IdPRONAC = $idpedidoalteracao and tpa.tpAlteracaoProjeto = 4  and abran.tpAcao != 'N'
                    --AND taipa.stAvaliacaoItemPedidoAlteracao in ('EA', 'AG')
                ) as tabelas ORDER BY pais, uf, mun, idAvaliacaoItemPedidoAlteracao DESC  ";
        } // fecha else

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }


    public function buscarDadosAbrangencia($idpedidoalteracao)
    {
        $sql = "select
                    distinct (abran.idAbrangencia),
                    pais.Descricao pais,
                    uf.Descricao as uf,
                    mun.Descricao as mun,
                    paxta.dsJustificativa
                from
                    SAC.dbo.Abrangencia abran
                    INNER JOIN SAC.dbo.Projetos pro on pro.idProjeto = abran.idProjeto
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto pap on pap.IdPRONAC = pro.IdPRONAC
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao paxta on paxta.idPedidoAlteracao = pap.idPedidoAlteracao
                    INNER JOIN BDCORPORATIVO.scSAC.tbTipoAlteracaoProjeto tap on tap.tpAlteracaoProjeto = paxta.tpAlteracaoProjeto
                    INNER JOIN AGENTES.dbo.Uf uf on uf.idUF = abran.idUF
                    INNER JOIN AGENTES.dbo.Municipios mun on mun.idMunicipioIBGE = abran.idMunicipioIBGE
                    INNER JOIN Agentes.dbo.Pais	pais on pais.idPais = abran.idPais
                where
                    pro.IdPRONAC  = $idpedidoalteracao and tap.tpAlteracaoProjeto = 4 and abran.stAbrangencia = 1
                ";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public function buscarDadosAbrangenciaSolicitada($idpedidoalteracao)
    {
        $sql = "SELECT pais.Descricao pais,
                            uf.Descricao uf,
                            mun.Descricao mun,
                            paxta.dsJustificativa
                    FROM
                        AGENTES.dbo.Pais pais,
                        AGENTES.dbo.UF uf,
                        AGENTES.dbo.Municipios mun,
                        SAC.dbo.tbAbrangencia ta,
                        BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tpa,
                        BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao paxta
                    WHERE
                        tpa.idPronac = $idpedidoalteracao AND
                        uf.idUF = ta.idUF AND
                        mun.idMunicipioIBGE = ta.idMunicipioIBGE and
                        pais.idPais = ta.idPais AND
                        ta.idPedidoAlteracao = tpa.idPedidoAlteracao AND
                        paxta.idPedidoAlteracao = tpa.idPedidoAlteracao
                        --AND paxta.tpAlteracaoProjeto = 4
                        ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public function buscarDadosAbrangenciaSolicitadaLocal($idpedidoalteracao, $tpAcao = null)
    {
        $sql = "SELECT tpa.idPedidoAlteracao,
            				pais.Descricao pais,
                            uf.Descricao uf,
                            mun.Descricao mun,
                            paxta.dsJustificativa
                    FROM
                        AGENTES.dbo.Pais pais,
                        AGENTES.dbo.UF uf,
                        AGENTES.dbo.Municipios mun,
                        SAC.dbo.tbAbrangencia ta,
                        BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tpa,
                        BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao paxta
                    WHERE
                        tpa.idPronac = $idpedidoalteracao AND
                        uf.idUF = ta.idUF AND
                        mun.idMunicipioIBGE = ta.idMunicipioIBGE and
                        pais.idPais = ta.idPais AND
                        ta.idPedidoAlteracao = tpa.idPedidoAlteracao AND
                        paxta.idPedidoAlteracao = tpa.idPedidoAlteracao
                        AND paxta.tpAlteracaoProjeto = 4
                        ";

        if (!empty($tpAcao)) :
            $sql .= " AND ta.tpAcao = '" . $tpAcao . "'";
        endif;


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    /**
     * M�todo para avaliar o local de realiza��o
     * @access public
     * @
     * @param $dados array
     * @return boolean
     */
    public function avaliarLocalRealizacao($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo avaliarLocalRealizacao()


    /**
     * M�todo para verificar se o loca de realiza��o j� existe
     */
    public function verificarLocalRealizacao($idProjeto, $idMunicipio)
    {
        $sql = "SELECT idMunicip�oIBGE FROM Abrangencia WHERE idProjeto=$idProjeto AND stAbrangencia = 1 AND idMunicipioIBGE=$idMunicipio";
        return $sql;
    }

    public function AbrangenciaGeografica($id_projeto)
    {
//         Antigo SQL
//        $sql = "SELECT CASE a.idPais
//                WHEN 0 THEN 'N&atilde;o &eacute; possivel informar o local de realiza&ccedil;&atilde;o do projeto'
//                ELSE p.Descricao
//                END as Pais,u.Descricao as UF,m.Descricao as Cidade,x.DtInicioDeExecucao,x.DtFinalDeExecucao
//                FROM  sac.Abrangencia a
//                INNER JOIN sac.PreProjeto x on (a.idProjeto = x.idPreProjeto)
//                LEFT JOIN Agentes.Pais p on (a.idPais=p.idPais)
//                LEFT JOIN Agentes.Uf u on (a.idUF=u.idUF)
//                LEFT JOIN Agentes.Municipios m on (a.idMunicipioIBGE=m.idMunicipioIBGE)
//                WHERE idProjeto=".$id_projeto." AND a.stAbrangencia = 1
//                ORDER BY p.Descricao,u.Descricao,m.Descricao";

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr("
                    CASE WHEN a.idpais = 0 THEN 'N&atilde;o &eacute; possivel informar o local de realiza&ccedil;&atilde;o do projeto'
                        ELSE p.Descricao
                    END as Pais"),
                new Zend_Db_Expr("CASE a.pais WHEN 0 THEN 'N&atilde;o &eacute; possivel informar o local de realiza&ccedil;&atilde;o do projeto'
                        ELSE p.Descricao END as Pais"),
                'Pais' => new Zend_Db_Expr("CASE WHEN a.idpais = 0 THEN 'N&atilde;o &eacute; possivel informar o local de realiza&ccedil;&atilde;o do projeto'
                     ELSE p.Descricao
                      END"),
                'u.Descricao as UF',
                'm.Descricao as Cidade',
                'x.dtInicioDeExecucao',
                'x.dtfinaldeexecucao'
            ),
            $this->_schema
        );

        $select->joinInner(
            array('x' => 'PreProjeto'),
            'a.idProjeto = x.idPreProjeto',
            null,
            $this->_schema
        );

        $select->joinLeft(
            array('p' => $this->getName('Pais')),
            'a.idPais = p.idPais',
            null,
            $this->getSchema('agentes')
        );

        $select->joinLeft(
            array('u' => $this->getName('Uf')),
            'a.idUF = u.idUF',
            null,
            $this->getSchema('agentes')
        );

        $select->joinLeft(
            array('m' => $this->getName('Municipios')),
            'a.idMunicipioIBGE = m.idMunicipioIBGE',
            null,
            $this->getSchema('agentes')
        );

        $select->where('idProjeto= ?', $id_projeto);
        $select->where('a.stAbrangencia = ?', 1);
        $select->order(array('p.descricao', 'u.descricao', 'm.descricao'));

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }

        return $db->fetchAll($select);
    }

    public function buscarUfRegionalizacao($idPreProjeto)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                'idPreProjeto'=>'a.idProjeto',
            ),
            $this->_schema
        );
        $select->joinInner(array('uf' => 'UF'), 'uf.idUF = a.idUF', array('idUF'=>'uf.idUF', 'UF'=>'uf.Sigla'), $this->getSchema('agentes'));
        $select->joinInner(array('mun' => 'Municipios'), 'mun.idMunicipioIBGE = a.idMunicipioIBGE', array('idMunicipio'=>'mun.idMunicipioIBGE', 'Municipio'=>'mun.Descricao'), $this->getSchema('agentes'));
        $select->where('a.idProjeto = ?', $idPreProjeto);
        $select->where("uf.Regiao = 'Sul' OR uf.Regiao = 'Sudeste'");
        $select->order('a.idProjeto DESC');
        $select->limit(1);

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
//
        return $db->fetchRow($select);
    }
} // fecha class AvaliacaoSubItemPlanoDistribuicaoDAO

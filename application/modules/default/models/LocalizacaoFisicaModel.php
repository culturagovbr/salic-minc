<?php
class LocalizacaoFisicaModel extends MinC_Db_Table_Abstract
{
    /**
     * @var boolean
     */
    const MOSTRAR_APENAS_ULTIMA_LOCALIZACAO = true;

    /**
     *
     * @var unknown
     */
    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "LocalizacaoFisica";

    /**
     *
     */
    public function getTecnicos($orgaos)
    {
        $tecnicos = array();
        $usuariosOrgaosGrupos = new Usuariosorgaosgrupos();
        $usuarios = $usuariosOrgaosGrupos->buscarUsuariosOrgaosGruposSigla(array('usu_orgao in (?)' => $orgaos, 'usu_status = ?' => 1), 'u.usu_nome');
        foreach ($usuarios as $usuario) {
            if (in_array($usuario->usu_codigo, $tecnicos)) {
                continue;
            }
            $tecnicos[$usuario->usu_codigo] = $usuario;
        }
        return $tecnicos;
    }

    /**
     *
     */
    public function getVinculadas($orgaosUsuario)
    {
        $orgaoModel = new Orgaos();
        $orgaos = $orgaoModel->pesquisarTodosOrgaos();
        $result = array();
        foreach ($orgaos as $index => $orgao) {
            if (in_array($orgao->Codigo, $orgaosUsuario)) {
                $result[] = $orgaos[$index];
            }
        }
        return $result;
    }

    /**
     * Pesquisa os projetos / localizacao de acordo com o pronac informado
     */
    public function pesquisarPorPronac($idPronac, $orgaosUsuario, $showOnlyLast = false)
    {
        $select = $this->pesquisarProjeto($showOnlyLast);
        $select->where('proj.AnoProjeto + proj.Sequencial = ?', $idPronac);
        $select->where('Orgao in (?)', $orgaosUsuario);
        return Zend_Paginator::factory($select);
    }

    /**
     * Pesquisa os projetos / localizacao de acordo com o tecnico
     */
    public function pesquisarPorTecnico($idTecnico, $orgaosUsuario, $showOnlyLast = false)
    {
        $select = $this->pesquisarProjeto($showOnlyLast = false);
        $select->where('proj.Logon = ?', $idTecnico);
        $select->where('Orgao in (?)', $orgaosUsuario);
        return Zend_Paginator::factory($select);
    }

    /**
     * Pesquisa os projetos / localizacao de acordo com a vinculada
     */
    public function pesquisarPorVinculada($idVinculada, $orgaosUsuario, $showOnlyLast = false)
    {
        $select = $this->pesquisarProjeto($showOnlyLast = false);
        $select->where('Orgao in (?)', $orgaosUsuario);
        $select->where('proj.Orgao = ?', $idVinculada);
        return Zend_Paginator::factory($select);
    }

    /**
     *
     */
    public function pesquisar($idPronac, $showOnlyLast = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('loc' => $this->_name),
            array(
                '*',
                'Localizacao' => 'loc.Localizacao',
                'TecnicoAntigoNome' => new Zend_Db_Expr('(SELECT top 1 usu_nome FROM TABELAS.dbo.Usuarios tecnico WHERE tecnico.usu_codigo = loc.TecnicoAntigo)'),
                'TecnicoAtualNome' => new Zend_Db_Expr('(SELECT top 1 usu_nome FROM TABELAS.dbo.Usuarios tecnico WHERE tecnico.usu_codigo = loc.TecnicoAtual)'),
            ),
            'sac.dbo'
        );
        $select->where('Pronac = ?', $idPronac);
        $select->order('DataCriacao DESC');
        return $this->fetchAll($select);
    }

    /**
     *
     */
    private function pesquisarProjeto($showOnlyLast = false)
    {
        $fields = array(
            '*',
            'pronac' => new Zend_Db_Expr('proj.AnoProjeto + proj.Sequencial'),
            'NomeTecnico' => new Zend_Db_Expr('(SELECT top 1 usu_nome FROM TABELAS.dbo.Usuarios tecnico WHERE tecnico.usu_codigo = proj.Logon)'),
            'Localizacao' => new Zend_Db_Expr("(SELECT top 1 Localizacao FROM SAC.dbo.{$this->_name} loc WHERE loc.IdPronac = proj.IdPronac ORDER BY loc.DataCriacao DESC)"),
        );
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('proj' => "{$this->_schema}.Projetos"), $fields, 'sac.dbo');
        $select->joinInner(array('a' => 'Agentes'), 'a.CNPJCPF = proj.CgcCpf', array('a.idAgente', 'a.CNPJCPF'), 'AGENTES.dbo');
        $select->joinInner(array('n' => 'Nomes'), 'n.idAgente = a.idAgente', array('n.Descricao AS NomeProponente'), 'AGENTES.dbo');
        $select->joinInner(array('orgao' => 'Orgaos'), 'orgao.Codigo = proj.Orgao', array('orgao.Sigla as orgaoNome'), 'sac.dbo');
        $select->joinInner(array('orgaoPai' => 'Orgaos'), 'orgaoPai.org_codigo = orgao.idSecretaria', array('orgaoPai.org_sigla as orgaoPaiNome'), 'tabelas.dbo');
        $select->order('pronac DESC');
        return $select;
    }

    /**
     * @return Zend_Db_Table_Row
     */
    public function pesquisarProjetoOrgaoProPronac($idPronac)
    {
        $select = $this->pesquisarProjeto();
        $select->where('proj.AnoProjeto + proj.Sequencial = ?', $idPronac);
        return $this->fetchRow($select);
    }

    public function localizarProjetos($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => 'Projetos'),
            array(
                new Zend_Db_Expr('p.idPronac, p.AnoProjeto+p.Sequencial AS Pronac, p.NomeProjeto, p.CgcCpf'),
                'NomeTecnico' => new Zend_Db_Expr('(SELECT top 1 usu_nome FROM TABELAS.dbo.Usuarios tecnico WHERE tecnico.usu_codigo = p.Logon)'),
            'Localizacao' => new Zend_Db_Expr("(SELECT Localizacao FROM SAC.dbo.LocalizacaoFisica loc WHERE loc.IdPronac = p.IdPronac AND id = (SELECT max(id) FROM SAC.dbo.LocalizacaoFisica loc1 WHERE loc1.IdPronac = p.IdPronac))")
           ),
            'SAC.dbo'
        );

        $select->joinInner(
            array('a' => 'Interessado'),
            'a.CgcCpf = p.CgcCpf',
            array('a.Nome AS NomeProponente'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('o' => 'Orgaos'),
            'o.Codigo = p.Orgao',
            array('Codigo','Sigla'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('org' => 'Orgaos'),
            'org.org_codigo = o.idSecretaria',
            array(''),
            'TABELAS.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            return $this->fetchAll($select)->count();
        }

        //adicionando linha order ao select
        $select->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }
        return $this->fetchAll($select);
    }
}

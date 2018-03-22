<?php

class Autenticacao_Model_FnVerificarPermissao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'dbo.fnVerificarPermissao';

    public function verificarPermissaoProjeto($idPronac, $idUsuarioLogado)
    {
        $select = new Zend_Db_Expr("SELECT SAC.dbo.fnVerificarPermissao(2,'',$idUsuarioLogado,$idPronac) as Permissao");
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchRow($select);
    }

    /**
     * verificarPermissaoProposta
     *
     * @param mixed $idPreProjeto
     * @param mixed $idUsuarioLogado
     * @param boolean $validarSeTemProjeto
     * @access public
     * @return mixed
     * @todo Verificar local para metodo.
     * SAC.dbo.fnVerificarPermissao --> SP removida
     */
    public function verificarPermissaoProposta($idPreProjeto, $idUsuarioLogado, $validarSeTemProjeto = true)
    {
        $db = Zend_Db_Table::getAdapter();

        $permissao = 0;

        $queryUsuarioLogado = $db->select();
        $queryUsuarioLogado->from(
            'SGCacesso',
            'cpf',
            $this->getSchema('controledeacesso')
        );
        $queryUsuarioLogado->where('IdUsuario = ?', $idUsuarioLogado);
        $cpfLogado = $db->fetchOne($queryUsuarioLogado);

        $queryProposta = $db->select();
        $queryProposta->from(
            array('a' => 'preprojeto'),
            array('a.idAgente', 'a.idUsuario'),
            $this->getSchema('sac')
        );
        $queryProposta->joinInner(
            array('b' => 'agentes'),
            '(a.idAgente = b.idAgente)',
            array('b.cnpjcpf', 'b.tipopessoa'),
            $this->getSchema('agentes')
        );
        $queryProposta->where('a.idPreProjeto = ?', $idPreProjeto);

        if ($validarSeTemProjeto == true) {
            $queryPropostaComProjeto = $db->select()
                ->from(
                    ['pr' => 'projetos'],
                    ['idPronac'],
                    $this->getSchema('sac'))
                ->where('a.idPreProjeto = pr.idProjeto', '')
                ->where('pr.Situacao != ?', Projeto_Model_Situacao::PROJETO_LIBERADO_PARA_AJUSTES);

            $queryProposta->where(new Zend_Db_Expr("NOT EXISTS({$queryPropostaComProjeto})"));
        }
        $proposta = $db->fetchRow($queryProposta);

        if (empty($proposta)) {
            return 0;
        }

        if ($proposta['tipopessoa'] == 0) {
            if ($cpfLogado == $proposta['cnpjcpf'] || $proposta['idUsuario'] == $idUsuarioLogado) {
                $permissao = 1;
            }
        }

        if ($proposta['tipopessoa'] == 1) {

            if (!empty($proposta['cnpjcpf'])) {
                $queryDirigente = $db->select()
                    ->from(array('a' => 'vinculacao'), null, $this->getSchema('agentes'))
                    ->join(array('b' => 'agentes'), '(a.idagente = b.idagente)', 'b.cnpjcpf', $this->getSchema('agentes'))
                    ->join(array('c' => 'agentes'), '(a.idvinculoprincipal = c.idagente)', null, $this->getSchema('agentes'))
                    ->join(array('d' => 'visao'), '(d.idagente = a.idagente)', null, $this->getSchema('agentes'))
                    ->where('b.cnpjcpf = ?', $cpfLogado)
                    ->where('c.cnpjcpf = ?', $proposta['cnpjcpf'])
                    ->where('d.visao = 198');
                $dirigenteCpf = $db->fetchOne($queryDirigente);

                if (!empty($dirigenteCpf)) {

                    if ($cpfLogado == $dirigenteCpf || $proposta['idUsuario'] == $idUsuarioLogado) {
                        $permissao = 1;
                    }
                }
            }

            if ($permissao == 0) {
                $sql = $db->select()
                    ->from(array('a' => 'preprojeto'), 'a.idAgente', $this->getSchema('sac'))
                    ->join(array('b' => 'agentes'), '(a.idAgente = b.idAgente)', null, $this->getSchema('agentes'))
                    ->join(array('c' => 'tbvinculoproposta'), '(a.idPreProjeto = c.idPreProjeto)', null, $this->getSchema('agentes'))
                    ->join(array('d' => 'tbvinculo'), '(c.idVinculo = d.idVinculo)', null, $this->getSchema('agentes'))
                    ->join(array('e' => 'sgcacesso'), '(d.idUsuarioResponsavel = e.idUsuario)', null, $this->getSchema('controledeacesso'))
                    ->where('c.siVinculoProposta = 2')
                    ->where('e.IdUsuario = ?', $idUsuarioLogado)
                    ->where('a.idPreProjeto = ?', $idPreProjeto);

                $idAgente = $db->fetchRow($sql);

                if (!empty($idAgente)) {
                    $permissao = 1;
                }
            }
        }

        return $permissao;
    }

    public function verificarPermissaoAdministrativo($idUsuarioLogado)
    {
        $select = new Zend_Db_Expr("SELECT SAC.dbo.fnVerificarPermissao(0,'',$idUsuarioLogado,'') as Permissao");
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchRow($select);
    }
}

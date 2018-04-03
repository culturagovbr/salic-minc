<?php
/**
 * DAO vwUsuariosOrgaosGrupos
 * @since 13/09/2013
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class vwUsuariosOrgaosGrupos extends MinC_Db_Table_Abstract
{

    /* dados da tabela */
    protected $_banco  = 'TABELAS';
    protected $_schema = 'TABELAS';
    protected $_name   = 'vwUsuariosOrgaosGrupos';
    protected $_primary = 'usu_codigo';


    /**
     * Metodo para buscar as unidades autorizadas do usuario do sistema
     * @access public
     * @param @usu_codigo (codigo do usuario)
     * @param @sis_codigo (codigo sistema)
     * @param @gru_codigo (codigo do grupo)
     * @param @uog_orgao  (codigo do Orgao)
     * @return object
     */
    public function carregarPorAdmissibilidade()
    {
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            'vwusuariosorgaosgrupos',
            array(
                'usu_codigo',
                'usu_nome',
                'usu_orgao'
            , 'usu_orgaolotacao'
            , 'uog_orgao'
            , 'org_siglaautorizado'
            , 'org_nomeautorizado'
            , 'gru_codigo'
            , 'gru_nome'
            , 'org_superior'
            , 'uog_status'
            , 'id_unico'
            ),
            $this->_schema
        );
//        $sql->where('usu_codigo = ?', $usu_codigo);
//        $sql->where('uog_status = ?', 1);
//
//        if (!empty($sis_codigo)) {
//            $sql->where('sis_codigo = ?', $sis_codigo);
//        }
//        if (!empty($gru_codigo)) {
//            $sql->where('gru_codigo = ?', $gru_codigo);
//        }
//        if (!empty($uog_orgao)) {
//            $sql->where('uog_orgao = ?', $uog_orgao);
//        }
//        $sql->where('gru_codigo <> ?', 129);
        $sql->where("gru_nome LIKE '%Admissibilidade%'");
//        $sql->order('org_siglaautorizado ASC');
//        $sql->order('gru_nome ASC');
        $sql->order('usu_nome ASC');
//        d($sql->__toString());
        return $this->fetchAll($sql);
    }

    /**
     * Metodo para buscar as unidades autorizadas do usuario do sistema
     * @access public
     * @param @usu_codigo (codigo do usuario)
     * @param @sis_codigo (codigo sistema)
     * @param @gru_codigo (codigo do grupo)
     * @param @uog_orgao  (codigo do orgao)
     * @return object
     */
    public function carregarPorPareceristaGrupo($intIdUnidade)
    {
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            'vwusuariosorgaosgrupos',
            array(
                'usu_codigo',
                'usu_nome',
                'usu_orgao'
            , 'usu_orgaolotacao'
            , 'uog_orgao'
            , 'org_siglaautorizado'
            , 'org_nomeautorizado'
            , 'gru_codigo'
            , 'gru_nome'
            , 'org_superior'
            , 'uog_status'
            , 'id_unico'
            ),
            $this->_schema
        );
        $sql->where("gru_nome LIKE '%Parecerista%'");
        $sql->where("org_superior = ?", $intIdUnidade);
        $sql->order('org_siglaautorizado ASC');
        $sql->order('gru_nome ASC');
        $sql->order('usu_nome ASC');
        $arrResult = $this->fetchAll($sql);
        $arrNew = array();
        foreach ($arrResult as $arrValue) {
            $arrNew[$arrValue['gru_nome']][] = $arrValue->toArray();
        }
        return $arrNew;
    }

    public function carregarPorPareceristaGrupoFetchPairs($intIdUnidade)
    {
        $arrResult = self::carregarPorPareceristaGrupo($intIdUnidade);
        $arrNew = array();
        foreach ($arrResult as $strKey => $arrGrupo) {
            foreach ($arrGrupo as $arrValue) {
                $arrNew[$strKey][$arrValue['usu_codigo']] = utf8_encode($arrValue['usu_nome']);
            }
        }
        return $arrNew;
    }


    /**
     * Metodo para buscar as unidades autorizadas do usuario do sistema
     * @access public
     * @param @usu_codigo (codigo do usuario)
     * @param @sis_codigo (codigo sistema)
     * @param @gru_codigo (codigo do grupo)
     * @param @uog_orgao  (codigo do orgao)
     * @return object
     */
    public function carregarUnidade()
    {
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            'Orgaos',
            array(
                'Codigo',
                'Sigla',
            ),
            $this->getSchema('sac')
        );
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $arrAuth = array_change_key_case((array) $auth->getIdentity());
        $intUsuOrgao = $arrAuth['usu_orgao'];
        if ($intUsuOrgao == 91) {
            $sql->where('idSecretaria = 91');
            $sql->where('Codigo <> 91');
        } else {
            $sql->where('vinculo = 1');
        }
        $sql->where('status = 0');
        $sql->where('stVinculada = 1');
        $sql->order('Sigla ASC');
        $arrResult = $this->fetchAll($sql);
        return $arrResult;
    }

    public function carregarUsuariosPorUnidade($intIdUnidade)
    {
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            'vwusuariosorgaosgrupos',
            array(
                'usu_codigo',
                'usu_nome',
                'usu_orgao',
                'usu_orgaolotacao',
            ),
            $this->_schema
        );
        $sql->where("sis_codigo = ?", 21);
        $sql->where("gru_codigo = ?", 94);
        $sql->where("uog_status = ?", 1);
        $sql->where("uog_orgao = ?", $intIdUnidade);
        $sql->order('usu_orgao ASC');
        $sql->order('usu_nome ASC');
        $arrResult = $this->fetchAll($sql);
        $arrNew = array();
        foreach ($arrResult->toArray() as $arrValue) {
            $arrNew[utf8_encode($arrValue['usu_orgaolotacao'])][$arrValue['usu_codigo']] = utf8_encode($arrValue['usu_nome']);
        }
        return $arrNew;
    }

    public function carregarUsuariosPorUnidadeIphan($intIdUnidade)
    {
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            'vwusuariosorgaosgrupos',
            array(
                'usu_codigo',
                'usu_nome',
                'usu_orgao',
                'usu_orgaolotacao',
            ),
            $this->_schema
        );
        $sql->where("sis_codigo = ?", 21);
        $sql->where("gru_codigo = ?", 94);
        $sql->where("uog_status = ?", 1);
        $sql->where("uog_orgao = ?", $intIdUnidade);
        $sql->order('usu_orgao ASC');
        $sql->order('usu_nome ASC');
        $arrResult = $this->fetchAll($sql);
        $arrNew = array();
        foreach ($arrResult->toArray() as $arrValue) {
            $arrNew[utf8_encode($arrValue['usu_orgaolotacao'])][$arrValue['usu_codigo']] = utf8_encode($arrValue['usu_nome']);
        }
        return $arrNew;
    }

    public function buscarUsuarios($codPerfil, $codOrgao)
    {
        $sql = "exec SAC.dbo.paUsuariosDoPerfil $codPerfil, $codOrgao ";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public function carregarTecnicosPorUnidadeEGrupo($intIdUnidade, $intIdGrupo)
    {
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            'vwusuariosorgaosgrupos',
            array(
                'usu_codigo',
                'usu_nome',
                'usu_orgao',
                'usu_orgaolotacao',
                'org_superior',
            ),
            $this->_schema
        );
        $sql->where("uog_status = ?", 1);
        $sql->where("sis_codigo = ?", 21);
        $sql->where("uog_orgao = ?", $intIdUnidade);
        $sql->where("gru_codigo = ?", $intIdGrupo);
        $sql->order('usu_orgao ASC');
        $sql->order('usu_nome ASC');
        $arrResult = $this->fetchAll($sql);
        return $arrResult;
    }

    public function carregarTecnicosPorUnidade($idUnidade)
    {
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->distinct();
        $sql->from(
            'vwusuariosorgaosgrupos',
            array
            (
                'usu_codigo',
                'usu_nome',
            ),
            $this->_schema
        );
        $sql->where("uog_status = ?", 1);
        $sql->where("sis_codigo = ?", 21);
        $sql->where("uog_orgao = ?", $idUnidade);

        $sql->order(2);
        $arrResult = $this->fetchAll($sql);
        return $arrResult;
    }
}

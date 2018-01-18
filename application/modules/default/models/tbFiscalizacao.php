<?php

/**
 * Description of Projetos
 *
 * @author Andr� Nogueira Pereira
 */
class tbFiscalizacao extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbFiscalizacao';
    protected $_schema = 'SAC';
    protected $_banco = 'SAC';

    public function buscarAtoresFiscalizacao($idPronac, $idusuario=null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('fisc'=>$this->_name),
                        array()
                      );

        $select->joinInner(
                            array('nm'=>'Nomes'),
                            'nm.idAgente = fisc.idAgente',
                            array('Nome'=>'nm.Descricao'),
                            'AGENTES.dbo'
                           );
        $select->joinInner(
                            array('ag'=>'Agentes'),
                            'ag.idAgente = fisc.idAgente',
                            array('ag.idAgente'),
                            'AGENTES.dbo'
                           );
        $select->joinInner(
                            array('usu'=>'Usuarios'),
                            'ag.CNPJCPF = usu.usu_identificacao',
                            array(),
                            'TABELAS.dbo'
                           );
        $select->joinInner(
                            array('uog'=>'UsuariosXOrgaosXGrupos'),
                            'usu.usu_codigo = uog.uog_usuario and uog.uog_status = 1',
                            array(),
                            'TABELAS.dbo'
                           );
        $select->joinInner(
                            array('org'=>'Orgaos'),
                            'org.Codigo = uog.uog_orgao',
                            array('Orgao'=>'org.Sigla'),
                            'SAC.dbo'
                           );
        $select->joinInner(
                            array('gru'=>'Grupos'),
                            'gru.gru_codigo = uog.uog_grupo',
                            array('Perfil'=>'gru.gru_nome','cdPerfil'=>'gru.gru_codigo'),
                            'TABELAS.dbo'
                           );
        /*$select->joinInner(
                            array('nm2'=>'Nomes'),
                            'nm2.idAgente = fisc.idAgente',
                            array('Nome2'=>'nm2.Descricao'),
                            'AGENTES.dbo'
                           );*/
        /*$select->joinInner(
                            array('ag2'=>'Agentes'),
                            'ag2.idAgente = fisc.idAgente',
                            array('idAgente2'=>'ag2.idAgente'),
                            'AGENTES.dbo'
                           );*/
        /*$select->joinInner(
                            array('usu2'=>'Usuarios'),
                            'ag2.CNPJCPF = usu2.usu_identificacao',
                            array(),
                            'TABELAS.dbo'
                           );*/
        /*$select->joinInner(
                            array('uog2'=>'UsuariosXOrgaosXGrupos'),
                            'usu2.usu_codigo = uog2.uog_usuario and uog2.uog_status = 1',
                            array(),
                            'TABELAS.dbo'
                           );*/
        /*$select->joinInner(
                            array('org2'=>'Orgaos'),
                            'org2.Codigo = uog2.uog_orgao',
                            array('Orgao2'=>'org2.Sigla'),
                            'SAC.dbo'
                           );*/
        /*$select->joinInner(
                            array('gru2'=>'Grupos'),
                            'gru2.gru_codigo = uog2.uog_grupo',
                            array('Perfil2'=>'gru2.gru_nome','cdPerfil2'=>'gru2.gru_codigo'),
                            'TABELAS.dbo'
                           );*/

        //$select->where('gru.gru_codigo = 135');
        //$select->where('gru2.gru_codigo = 134');
        $select->where('gru.gru_codigo = 135 or gru.gru_codigo = 134');
        //$select->where('usu.usu_codigo <> ?', $idusuario);
        $select->where('fisc.IdPRONAC = ?', $idPronac);




        return $this->fetchAll($select);
    }
}

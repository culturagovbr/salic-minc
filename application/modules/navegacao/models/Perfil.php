<?php

class Navegacao_Model_Perfil extends MinC_Db_Model
{
    private $usu_orgao;
    private $usu_orgao_lotacao;
    private $uog_orgao;
    private $orgao_sigla_autorizada;
    private $org_nome_autorizado;
    private $gru_codigo;
    private $nome_grupo;
    private $org_superior;
    private $uog_status;
    private $id_unico;

    public function __construct($params)
    {
        $this->usu_orgao = $params['usu_orgao'];
        $this->usu_orgao_lotacao = $params['usu_orgaolotacao'];
        $this->uog_orgao = $params['uog_orgao'];
        $this->orgao_sigla_autorizada = $params['org_siglaautorizado'];
        $this->org_nome_autorizado = $params['org_nomeautorizado'];
        $this->gru_codigo = $params['gru_codigo'];
        $this->nome_grupo = $params['gru_nome'];
        $this->org_superior = $params['org_superior'];
        $this->uog_status = $params['uog_status'];
        $this->id_unico = $params['id_unico'];
    }

    public function getUsuOrgao()
    {
        return $this->usu_orgao;
    }

    public function getUsuOrgaoLotacao()
    {
        return $this->usu_orgao_lotacao;
    }

    public function getUogOrgao()
    {
        return $this->uog_orgao;
    }

    public function getOrgaoSiglaAutorizada()
    {
        return $this->orgao_sigla_autorizada;
    }

    public function getOrgNomeAutorizado()
    {
        return $this->org_nome_autorizado;
    }

    public function getGruCodigo()
    {
        return $this->gru_codigo;
    }

    public function getNomeGrupo()
    {
        return $this->nome_grupo;
    }

    public function getOrgSuperior()
    {
        return $this->org_superior;
    }

    public function getUogStatus()
    {
        return $this->uog_status;
    }

    public function getIdUnico()
    {
        return $this->id_unico;
    }

}

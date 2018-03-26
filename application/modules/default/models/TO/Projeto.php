<?php
/*
 * Created on 19/05/2010
 * Politec MINC - Thiago Lenin
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class Projeto
{
    // Atributos do classe Projeto
    private $pronac;
    private $nomeProjeto;
    private $CgcCpf;
    private $situacao;
    private $modalidade;
    private $ResumoProjeto;
    private $mecanismo;
    private $processo;
    private $solicitadoUfir;
    private $solicitadoReal;
    private $solicitadoCusteiroUfir;
    private $solicitadoCusteiroReal;
    private $solicitadoCapitalUfir;
    private $solicitadoCapitalReal;
    private $dataSaida;
    private $dataRetorno;
    private $usuarioLog;
    
    // Esta função tem por objetivo instancia o objeto AreaCultural
    public function setAreaCultural(AreaCultural $areacultural)
    {
        $this->areacultural = $areacultural;
    }
    
    // Esta função tem por objetivo instancia o objeto SeguimentoCultural
    public function setSeguimentoCultural(SeguimentoCultural $seguimentocultural)
    {
        $this->areacultural = $seguimentocultural;
    }
    
    // $nomeProjeto
    public function setNomeProjeto($nomeProjeto)
    {
        $this->nomeProjeto = $nomeProjeto;
    }
    
    public function getNomeProjeto()
    {
        return $this->nomeProjeto;
    }
    
    // $pronac
    public function setPronac($pronac)
    {
        $this->pronac = $pronac;
    }
    
    public function getPronac()
    {
        return $this->pronac;
    }
    
    // $CgcCpf
    public function setCgcCpf($CgcCpf)
    {
        $this->CgcCpf = $CgcCpf;
    }
    
    public function getCgcCpf()
    {
        return $this->CgcCpf;
    }
    
    // $situacao
    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;
    }
    
    public function getSituacao()
    {
        return $this->situacao;
    }
    
    // $modalidade
    public function setModalidade($modalidade)
    {
        $this->modalidade = $modalidade;
    }
    
    public function getModalidade()
    {
        return $this->modalidade;
    }
    
    // $ResumoProjeto
    public function setResumoProjeto($ResumoProjeto)
    {
        $this->ResumoProjeto = $ResumoProjeto;
    }
    
    public function getResumoProjeto()
    {
        return $this->ResumoProjeto;
    }
    
    // $mecanismo
    public function setMecanismo($mecanismo)
    {
        $this->mecanismo = $mecanismo;
    }
    
    public function getMecanismo()
    {
        return $this->mecanismo;
    }
    
    // $processo
    public function setProcesso($processo)
    {
        $this->processo = $processo;
    }
    
    public function getProcesso()
    {
        return $this->processo;
    }
    
    // $solicitadoUfir
    public function setSolicitadoUfir($solicitadoUfir)
    {
        $this->solicitadoUfir = $solicitadoUfir;
    }
    
    public function getSolicitadoUfir()
    {
        return $this->solicitadoUfir;
    }
    
    // $solicitadoReal
    public function setSolicitadoReal($solicitadoReal)
    {
        $this->solicitadoReal = $solicitadoReal;
    }
    
    public function getSolicitadoReal()
    {
        return $this->solicitadoReal;
    }
    
    // $solicitadoCusteiroUfir
    public function setSolicitadoCusteiroUfir($solicitadoCusteiroUfir)
    {
        $this->solicitadoCusteiroUfir = $solicitadoCusteiroUfir;
    }
    
    public function getSolicitadoCusteiroUfir()
    {
        return $this->solicitadoCusteiroUfir;
    }
    
    // $solicitadoCusteiroReal
    public function setSolicitadoCusteiroReal($solicitadoCusteiroReal)
    {
        $this->solicitadoCusteiroReal = $solicitadoCusteiroReal;
    }
    
    public function getSolicitadoCusteiroReal()
    {
        return $this->solicitadoCusteiroReal;
    }
    
    // $solicitadoCapitalUfir
    public function setSolicitadoCapitalUfir($solicitadoCapitalUfir)
    {
        $this->solicitadoCapitalUfir = $solicitadoCapitalUfir;
    }
    
    public function getSolicitadoCapitalUfir()
    {
        return $this->solicitadoCapitalUfir;
    }
    
    // $solicitadoCapitalReal
    public function setSolicitadoCapitalReal($solicitadoCapitalReal)
    {
        $this->solicitadoCapitalReal = $solicitadoCapitalReal;
    }
    
    public function getSolicitadoCapitalReal()
    {
        return $this->solicitadoCapitalReal;
    }
    
    // $dataSaida
    public function setDataSaida($dataSaida)
    {
        $this->dataSaida = $dataSaida;
    }
    
    public function getDataSaida()
    {
        return $this->dataSaida;
    }
    
    // $dataRetorno
    public function setDataRetorno($dataRetorno)
    {
        $this->dataRetorno = $dataRetorno;
    }
    
    public function getDataRetorno()
    {
        return $this->dataRetorno;
    }
    
    // $usuarioLog
    public function setUsuarioLog($usuarioLog)
    {
        $this->usuarioLog = $usuarioLog;
    }
    
    public function getUsuarioLog()
    {
        return $this->usuarioLog;
    }
}

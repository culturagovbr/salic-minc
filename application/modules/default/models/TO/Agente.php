<?php
class Agente extends Visao
{
    private $cpf;
    private $cnpjCPFSuperior;
    private $dataAtualizacao;
    private $dataCadastro;
    private $usuarioLog;
    
    public function setCpf(Agente $cpf)
    {
        $this->cpf = $cpf;
    }
    
    public function getCpf()
    {
        return $this->cpf;
    }
    
    public function setStatus(Agente $status)
    {
        $this->status = $status;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function setDataAtualizacao(Agente $dataAtualizacao)
    {
        $this->dataAtualizacao = $dataAtualizacao;
    }
    
    public function getDataAtualizacao()
    {
        return $this->dataAtualizacao;
    }
    
    public function setDataCadastro(Agente $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }
    
    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }
    
    public function setUsuarioLog(Agente $usuarioLog)
    {
        $this->usuarioLog = $usuarioLog;
    }
    
    public function getUsuarioLog()
    {
        return $this->usuarioLog;
    }
    
    public function setProjeto(Projeto $projeto)
    {
        $this->projeto = $projeto;
    }
    
    public function getProjeto()
    {
        return $this->projeto;
    }
    
    public function setMensagemProjeto(MensagemProjeto $mensagemProjeto)
    {
        $this->mensagemProjeto = $mensagemProjeto;
    }
    
    public function getMensagemProjeto()
    {
        return $this->mensagemProjeto;
    }
    
    public function setEndereco(Endereco $endereco)
    {
        $this->endereco = $endereco;
    }
    
    public function getEndereco()
    {
        return $this->endereco;
    }
    
    public function setTitulacaoConselheiro(TitulacaoConselheiro $titulacaoConselheiro)
    {
        $this->titulacaoConselheiro = $titulacaoConselheiro;
    }
    
    public function getTitulacaoConselheiro()
    {
        return $this->titulacaoConselheiro;
    }
}

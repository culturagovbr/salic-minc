<?php
class Arquivo
{
    private $nomeArquivo;
    private $extensaoArquivo;
    private $binario;
    private $dataEnvio;
    private $status;
    private $hashArquivo;
    
    
    public function setNomeArquivo(Arquivo $nomeArquivo)
    {
        $this->nomeArquivo = $nomeArquivo;
    }
    
    public function getNomeArquivo()
    {
        return $this->nomeArquivo;
    }
    
    public function setExtensaoArquivo(Arquivo $extensaoArquivo)
    {
        $this->extensaoArquivo = $extensaoArquivo;
    }
    
    public function getExtensaoArquivo()
    {
        return $this->extensaoArquivo;
    }
    
    public function setBinario(Arquivo $binario)
    {
        $this->binario = $binario;
    }
    
    public function getBinario()
    {
        return $this->binario;
    }
    
    public function setDataEnvio(Arquivo $dataEnvio)
    {
        $this->dataEnvio = $dataEnvio;
    }
    
    public function getDataEnvio()
    {
        return $this->dataEnvio;
    }
    
    public function setStatus(Arquivo $status)
    {
        $this->status = $status;
    }
    
    public function getNomeArquivo()
    {
        return $this->status;
    }
    
    public function setHashArquivo(Arquivo $hashArquivo)
    {
        $this->hashArquivo = $hashArquivo;
    }
    
    public function getHashArquivo()
    {
        return $this->hashArquivo;
    }
}

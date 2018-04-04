<?php
class ComprovanteExecucaoFisica
{
    private $titulo;
    private $descricao;
    private $dataEnvio;
    private $statusAtivacao;
    private $dataAvaliacao;
    
    
    public function setTitulo(ComprovanteExecucaoFisica $titulo)
    {
        $this->titulo = $titulo;
    }
    
    public function getTitulo()
    {
        return $this->titulo;
    }
    
    public function setDescricao(ComprovanteExecucaoFisica $descricao)
    {
        $this->descricao = $descricao;
    }
    
    public function getDescricao()
    {
        return $this->descricao;
    }
    
    public function setDataEnvio(ComprovanteExecucaoFisica $dataEnvio)
    {
        $this->dataEnvio = $dataEnvio;
    }
    
    public function getDataEnvio()
    {
        return $this->dataEnvio;
    }
    
    public function setStatusAtivacao(ComprovanteExecucaoFisica $statusAtivacao)
    {
        $this->statusAtivacao = $statusAtivacao;
    }
    
    public function getStatusAtivacao()
    {
        return $this->statusAtivacao;
    }
    
    public function setDataAvaliacao(ComprovanteExecucaoFisica $dataAvaliacao)
    {
        $this->dataAvaliacao = $dataAvaliacao;
    }
    
    public function getDataAvaliacao()
    {
        return $this->dataAvaliacao;
    }
    
    public function setArquivo(Arquivo $arquivo)
    {
        $this->arquivo = $arquivo;
    }
    
    public function getArquivo()
    {
        return $this->arquivo;
    }
    
    public function setTipoDocumento(TipoDocumento $tipodocumento)
    {
        $this->tipoDocumento = $tipodocumento;
    }
    
    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }
    
    public function setEstadoAvaliacao(EstadoAvaliacao $estadoAvaliacao)
    {
        $this->estadoAvaliacao = $estadoAvaliacao;
    }
    
    public function getEstadoAvaliacao()
    {
        return $this->estadoAvaliacao;
    }
    
    public function setPedidoAlteracaoComprovanteExecucaoFisica(
                       PedidoAlteracaoComprovanteExecucaoFisica $pedidoAlteracaoComprovanteExecucaoFisica
    
    ) {
        $this->pedidoAlteracaoComprovanteExecucaoFisica = $pedidoAlteracaoComprovanteExecucaoFisica;
    }
    
    public function getPedidoAlteracaoComprovanteExecucaoFisica()
    {
        return $this->pedidoAlteracaoComprovanteExecucaoFisica;
    }
    
    public function setAgente(Agente $agente)
    {
        $this->agente = $agente;
    }
    
    public function getAgente()
    {
        return $this->agente;
    }
}

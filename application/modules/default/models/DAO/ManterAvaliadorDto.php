<?php

class ManterAvaliadorDto
{
    private $nome;
    private $email;
    private $cpf;
    private $idUsuario;
    private $idAgente;
    private $idEdital;
    private $arrayEditais;
    
    public function setNome($nome)
    {
        $this->nome = utf8_decode($nome);
    }
    public function getNome()
    {
        return $this->nome;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getEmail()
    {
        return $this->email;
    }


    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
    }
    public function getCpf()
    {
        return $this->cpf;
    }

    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
    }
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    public function setIdAgente($idAgente)
    {
        $this->idAgente = $idAgente;
    }
    public function getIdAgente()
    {
        return $this->idAgente;
    }

    public function setIdEdital($idEdital)
    {
        $this->idEdital = $idEdital;
    }
    public function getIdEdital()
    {
        return $this->idEdital;
    }

    public function setArrayEditais($arrayEditais)
    {
        $this->arrayEditais = $arrayEditais;
    }
    public function getArrayEditais()
    {
        return $this->arrayEditais;
    }
}

<?php

namespace MinC\Assinatura\Autenticacao;

interface IAdapter
{
    public function autenticar() : bool;

    public function obterInformacoesAssinante() : array;

    /**
     * @return array
     */
    public function obterTemplateAutenticacao();

}
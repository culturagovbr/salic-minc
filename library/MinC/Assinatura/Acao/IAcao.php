<?php

namespace MinC\Assinatura\Acao;

interface IAcao
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura);
}
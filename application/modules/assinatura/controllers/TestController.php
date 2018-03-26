<?php

class Assinatura_TestController extends Assinatura_GenericController
{
    public function indexAction()
    {
        xd("Teste >> M&oacute;dulo " .  ucfirst($this->moduleName));
    }
}

<?php

class Arquivo_TestController extends Arquivo_GenericController {

    public function indexAction () {
        xd("Teste >> M&oacute;dulo " .  ucfirst($this->moduleName));
    }
}

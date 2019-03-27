<?php

namespace Application\Modules\Navegacao\Service;

class Footer
{
    private $request;
    private $response;

    function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function buscarVersao()
    {
        return $this->getGitFullVersion();
    }

    private function getGitFullVersion() {
        exec("git describe --tags --abbrev=0", $tagNumber);
        exec("git rev-parse --abbrev-ref HEAD", $branchName);
        exec("git rev-parse --short HEAD", $commit);

        return $tagNumber;
    }

}

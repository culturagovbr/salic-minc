<?php

class MinC_View_Helper_Vcs extends Zend_View_Helper_Abstract
{
    public function vcs() {
        $vcs = $this->getVcs();
        if ($vcs) {
            $functionName = "get" . ucfirst($vcs) . "FullVersion";
            if (is_callable(array($this, $functionName))) {
                return $this->$functionName();
            }
        }
    }

    private function getVcs() {
        $folders = array('.git', '.svn');
        foreach ($folders as $vcs) {
            if (is_dir(realpath(constant('APPLICATION_PATH') . "/../{$vcs}"))) {
                return substr($vcs, 1);
            }
        }
    }

    /**
     * Retorna a versao do projeto, seja branch ou tag
     */
    private function getGitFullVersion() {
        exec("git describe --tags --abbrev=0", $tagNumber);
        exec("git rev-parse --abbrev-ref HEAD", $branchName);
        exec("git rev-parse --short HEAD", $commit);

        return "Vers&atilde;o: <a class=\"tooltipped\" data-position=\"top\" data-delay=\"50\" data-tooltip=\"Branch: ". array_pop($branchName) . "\" target=\"_blank\" href='https://github.com/culturagovbr/salic-minc/releases'>"
        . array_pop($tagNumber) . "</a>";
    }

    /**
     * Retorna a versao do projeto, seja branch ou tag
     */
    private function getSvnFullVersion() {
        exec("svn info | grep \"Revision\" | awk '{print $2}'", $revisionNumber);
        exec("svn info | grep '^URL:' | egrep -o '(tags|branches)/[^/]+|trunk' | egrep -o '[^/]+$'", $branchName);

        return "Branch|Tag: " . array_pop($branchName) . " Revis&atilde;o: " . array_pop($revisionNumber);
    }
}

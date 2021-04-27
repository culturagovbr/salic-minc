<?php
class MinC_View_Helper_GitTag extends Zend_View_Helper_Abstract
{
    public function gitTag() {
        exec("git describe --tags --abbrev=0", $tagNumber);
        return $tagNumber[0];
    }
}
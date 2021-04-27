<?php

class MinC_View_Helper_PageTitle extends Zend_View_Helper_Abstract
{
    public function PageTitle($title, $breadCrumb = [])
    {
           ?>
            <div class="page-title">
                <div class="row">
                    <div class="col s12 m9 l10">
                        <h1><?= $title ?></h1>
                        <?= !empty($breadCrumb) ? gerarNovoBreadCrumb($breadCrumb) : ''; ?>
                    </div>
                    <div class="col s12 m3 l2 right-align">
                        <a href="javascript:voltar();" title="P&aacute;gina Anterior" title="P&aacute;gina Anterior"
                           class="btn small grey lighten-3 grey-text z-depth-0 chat-toggle"><i class="material-icons">arrow_back</i>
                        </a>
                    </div>
                </div>
            </div>
        <?php
    }
}

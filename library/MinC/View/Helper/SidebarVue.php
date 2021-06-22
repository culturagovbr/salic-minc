<?php

class MinC_View_Helper_SidebarVue extends Zend_View_Helper_Abstract
{
    public function SidebarVue($urlAjax = '')
    {
        ?>

        <aside id="sidebar-vue">
            <sidebar-menu
                :url-ajax="urlAjax"
            ></sidebar-menu>
        </aside>

        <script src="/public/js/vue.js" type="text/javascript"></script>
        <script src="/public/scripts/components/carregando.vue.js" type="text/javascript"></script>
        <script src="/public/scripts/components/sidebar-menu.vue.js" type="text/javascript"></script>

        <script>
            new Vue({
                el: '#sidebar-vue',
                data: {
                    urlAjax: '<?= !empty($urlAjax) ? $urlAjax : ""; ?>'
                }
            })
        </script>

        <?php
    }
}
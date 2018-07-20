Vue.component('sl-planilha-produtos', {
    props: ['produtos', 'idpronac'],
    template: `
        <div>
            <div v-for="(produto, index) in produtos">
                {{produto}}
            </div>
        </div>
    `,
    // template: `
    //     <ul class="collapsible no-margin" data-collapsible="expandable" id="produtos-collapsible">
    //         <li class="active" v-for="(produto, index) in produtos">
    //             <div class="collapsible-header green-text" v-bind:class="{ active: isExpanded(produto.cdProduto) }" >
    //                 <i class="material-icons">perm_media</i>
    //                 {{ produto.produto }}
    //             </div>
    //             <div class="collapsible-body no-padding">
    //                 <ul class="collapsible no-margin no-border" data-collapsible="expandable">
    //                     <sl-planilha-etapas
    //                         :etapa="etapa"
    //                         v-for="(etapa, index) in produto.etapa"
    //                         :idpronac="idpronac"
    //                         :cdProduto="produto.cdProduto"
    //                         :key="index"
    //                     ></sl-planilha-etapas>
    //                 </ul>
    //             </div>
    //         </li>
    //     </ul>
    // `,
    mounted: function () {
        this.iniciarCollapsible();
    },
    methods: {
        iniciarCollapsible: function () {
            $3('#produtos-collapsible.collapsible').each(function() {
                $3(this).collapsible();
            });
        },
        isExpanded: function(cdProduto) {
            const url = new URL(window.location.href);
            const cdProdutoByUrl = url.searchParams.get('cdProduto');

            return cdProduto === Number(cdProdutoByUrl);
        },
    }
})

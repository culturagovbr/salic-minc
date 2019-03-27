Vue.component('sl-planilha-produtos', {
    props: ['produtos', 'idpronac', 'documento'],
    template: `
        <ul class="collapsible no-margin" data-collapsible="expandable" id="produtos-collapsible">
            <li class="active" v-for="(produto, index) in produtos">
                <div class=" active collapsible-header green-text" v-bind:class="{ active: isExpanded(produto.cdProduto) }" >
                    <i class="material-icons">perm_media</i>
                    {{ produto.produto }}
                </div>
                <div class="collapsible-body no-padding">
                    <ul class="collapsible no-margin no-border" data-collapsible="expandable">
                        <slot :produto="produto">
                            <sl-planilha-etapas
                                v-for="(etapa, index) in produto.etapa"
                                :etapa="etapa"
                                :idpronac="idpronac"
                                :cdProduto="produto.cdProduto"
                                :key="index"
                            ></sl-planilha-etapas>
                        </slot>
                    </ul>
                </div>
            </li>
        </ul>
    `,
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

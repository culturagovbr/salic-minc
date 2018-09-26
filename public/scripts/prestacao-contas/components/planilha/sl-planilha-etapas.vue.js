Vue.component('sl-planilha-etapas', {
    props: ['etapa', 'cdProduto', 'idpronac', "documento"],
    template: `
        <li class="active">
            <div class="collapsible-header orange-text active" style="padding-left: 30px;">
                <i class="material-icons">label</i>
                {{ etapa.etapa }}
            </div>
            <div class="collapsible-body no-padding">
                <ul class="collapsible no-margin no-border" data-collapsible="expandable">
                    <slot :etapa="etapa" :produto="cdProduto">
                        <sl-planilha-ufs
                            v-for="(estado, index) in etapa.UF"
                            :key="index"
                            :idpronac="idpronac"
                            :estado="estado"
                            :cdEtapa="etapa.cdEtapa"
                            :cdProduto="cdProduto"
                        ></sl-planilha-ufs>
                    </slot>
                </ul>
            </div>
        </li>
    `,
    mounted: function () {
        this.iniciarCollapsible();
    },
    methods: {
        iniciarCollapsible: function () {
            $3('.collapsible').each(function() {
                $3(this).collapsible();
            });
        }
    }
})

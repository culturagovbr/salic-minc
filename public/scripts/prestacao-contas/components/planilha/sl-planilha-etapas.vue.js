Vue.component('sl-planilha-etapas', {
    props: ['etapa', 'cdProduto', 'idpronac'],
    template: `
        <li class="active">
            <div class="collapsible-header orange-text active" style="padding-left: 30px;">
                <i class="material-icons">label</i>
                {{ etapa.etapa }}
            </div>
            <div class="collapsible-body no-padding">
                <ul class="collapsible no-margin no-border" data-collapsible="expandable">
                    <sl-planilha-ufs
                        v-for="(estado, index) in etapa.UF"
                        :idpronac="idpronac"
                        :estado="estado"
                        :cdEtapa="etapa.cdEtapa"
                        :cdProduto="cdProduto"
                        :key="index"
                    ></sl-planilha-ufs>
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

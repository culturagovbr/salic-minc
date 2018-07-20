Vue.component('sl-planilha-ufs', {
    props: ['estado', 'cdEtapa', 'cdProduto', 'idpronac'],
    template: ` <li class="active">
                        <div class="collapsible-header blue-text active" style="padding-left: 50px;">
                            <i class="material-icons">place</i>
                            {{ estado.Uf }}
                        </div>
                        <div class="collapsible-body no-padding">
                            <ul class="collapsible no-margin no-border" data-collapsible="expandable">
                                    <sl-planilha-cidades
                                        v-for="(cidade, index) in estado.cidade"
                                        :cidade="cidade"
                                        :idpronac="idpronac"
                                        :uf="estado.Uf"
                                        :cdEtapa="cdEtapa"
                                        :cdProduto="cdProduto"
                                        :key="estado.cdUf"
                                        :id="estado.cdUf"
                                    ></sl-planilha-cidades>
                            </ul>
                        </div>
                    </li>
                     `,
    methods: {
        iniciarCollapsible: function () {
            $3('.collapsible').each(function() {
                $3(this).collapsible();
            });
        }
    }
})
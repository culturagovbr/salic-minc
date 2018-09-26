Vue.component('sl-planilha-itens', {
    template: `
        <table class="bordered">
            <thead>
                <tr>
                    <th>Item de Custo</th>
                    <th style="text-align: right">Valor Aprovado</th>
                    <th style="text-align: right">Valor Comprovado</th>
                    <th style="text-align: right">Valor a Comprovar</th>
                    <th style="text-align: center"> </th>
                </tr>
            </thead>
            <tbody>
                <tr 
                   :item="item"
                   v-for="(item, index) in itens"
                   :key="index"
                   v-if="isObject(item)"
                >
                    <td>
                        {{ item.item }}
                    </td>
                    <td style="text-align: right"> R$ {{ converterParaReal(item.varlorAprovado) }} </td>
                    <td style="text-align: right"> R$ {{ converterParaReal(item.varlorComprovado) }} </td>
                    <td style="text-align: right"> R$ {{ converterParaReal(item.varlorAprovado - item.varlorComprovado)  }} </td>
                    <td style="text-align: right">
                        <div v-if="(tecnico)">
                            <div v-if="(item.varlorComprovado > 0)">
                                <div v-if="(!documento)">
                                    <sl-planilha-button
                                        :typeButton="url(item.idPlanilhaAprovacao, item.idPlanilhaItens, item.stItemAvaliado)"
                                    >
                                    </sl-planilha-button>
                                </div>
                                <div v-else>
                                </div>
                            </div>
                            <div v-else>
                               sem comprovantes.
                            </div>
                        </div>
                        <div v-else>
                            <sl-planilha-button
                                :typeButton="url(item.idPlanilhaAprovacao, item.idPlanilhaItens, item.stItemAvaliado)"
                            >
                            </sl-planilha-button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    `,
    props: [
        'itens',
        'idpronac',
        'uf',
        'cdproduto',
        'cdcidade',
        'cdetapa',
        'stitemavaliado',
        'tecnico',
        'documento'
    ],
    mounted() {
        $3('ul.tabs').tabs();
    },
    updated() {
        $3('ul.tabs').tabs();
    },
    methods: {
        isObject: function (el) {
            return typeof el === "object";
        },
        converterParaReal: function (value) {
            value = parseFloat(value);
            return numeral(value).format('0,0.00');
        },
        url: function(idPlanilhaAprovacao, idPlanilhaItens, stItemAvaliado) {
            if (stItemAvaliado === undefined) {
                return this.urlDoProponente(idPlanilhaAprovacao, idPlanilhaItens);
            }

            return this.urlDoTecnico(idPlanilhaAprovacao, idPlanilhaItens, stItemAvaliado);
        },
        urlDoTecnico: function(idPlanilhaAprovacao, idPlanilhaItens, stItemAvaliado) {
            let url = '/prestacao-contas/analisar/comprovante'
                + '/idPronac/' + this.idpronac
                + '/uf/' + this.uf
                + '/produto/' + this.cdproduto
                + '/idmunicipio/' + this.cdcidade
                + '/idPlanilhaItem/' + idPlanilhaItens
                + '/etapa/' + this.cdetapa;

                if (this.stitemavaliado) {
                    url += '/stItemAvaliado/' + this.stitemavaliado;
                }

            return { url: url, icon: 'gavel', colorButton: 'red' };
        },
        urlDoProponente: function(idPlanilhaAprovacao, idPlanilhaItens) {
            const url = '/prestacao-contas/gerenciar/comprovar-pagamento'
                + '/idpronac/' + this.idpronac
                + '/uf/' + this.uf
                + '/produto/' + this.cdproduto
                + '/cidade/' + this.cdcidade
                + '/etapa/' + this.cdetapa
                + '/idPlanilhaAprovacao/' + idPlanilhaAprovacao
                + '/idPlanilhaItens/' + idPlanilhaItens;

            return { url: url, icon: 'attach_money', colorButton: 'teal' };
        },
    },
})

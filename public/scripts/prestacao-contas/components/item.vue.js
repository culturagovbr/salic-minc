numeral.locale('pt-br');

function converteParaReal(value) {
    value = parseFloat(value);
    return numeral(value).format('0,0.00');
}

Vue.component('item', {
    props: ['idpronac', 'uf', 'etapa', 'cidade', 'produto', 'idplanilhaitens'],
    template: `<div class="col s12 m12" :informacoes = "informacoes">
                    <div class="card horizontal">
                        <div class="card-stacked">
                            <div class="center-align card-title lighten-4">
                                Item: {{ informacoes.Item }}
                            </div>
                            <template v-if="!loading">
                                <div class="card-content">
                                    <table class="bordered">
                                        <thead>
                                            <tr>
                                                <th>Produto</th>
                                                <th>Etapa</th>
                                                <th>UF</th>
                                                <th>Cidade</th>
                                                <th>Itens de Custo</th>
                                                <th style="text-align: right">Aprovado</th>
                                                <th style="text-align: right">Total Comprovado</th>
                                                <th style="text-align: right">Faltando Comprovar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td v-html="informacoes.Produto"></td>
                                                <td>{{ informacoes.Etapa }}</td>
                                                <td>{{ informacoes.uf }}</td>
                                                <td>{{ informacoes.cidade }}</td>
                                                <td>{{ informacoes.Item }}</td>
                                                <td style="text-align: right">R$ {{ converterParaReal(informacoes.vlAprovado) }}</td>
                                                <td style="text-align: right">R$ {{ converterParaReal(informacoes.vlComprovado) }}</td>
                                                <td style="text-align: right">R$ {{ converterParaReal(informacoes.vlAprovado - informacoes.vlComprovado) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                            <template v-else>
                                <div class="card-content">
                                    <div class="preloader-wrapper small active">
                                        <div class="spinner-layer spinner-green-only">
                                            <div class="circle-clipper left">
                                                <div class="circle"></div>
                                            </div><div class="gap-patch">
                                                <div class="circle"></div>
                                            </div><div class="circle-clipper right">
                                                <div class="circle"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>`,
    created: function () {
        let vue = this;
        this.$root.$on('novo-comprovante-nacional', function(data) {
            vue.informacoes.vlComprovado = parseFloat(vue.informacoes.vlComprovado) + parseFloat(data.valor);
        })

        this.$root.$on('atualizado-comprovante-nacional', function(data) {
            vue.informacoes.vlComprovado = (parseFloat(vue.informacoes.vlComprovado) - parseFloat(data.valorAntigo)) + parseFloat(data.valor);
        })

        this.$root.$on('excluir-comprovante-nacional', function(data) {
            vue.informacoes.vlComprovado = parseFloat(vue.informacoes.vlComprovado) - parseFloat(data.valor);
        })

        this.$root.$on('novo-comprovante-internacional', function(data) {
            vue.informacoes.vlComprovado = parseFloat(vue.informacoes.vlComprovado) + parseFloat(data.valor);
        })

        this.$root.$on('atualizado-comprovante-internacional', function(data) {
            console.log(data.valorAntigo, data.valor);
            vue.informacoes.vlComprovado = (parseFloat(vue.informacoes.vlComprovado) - parseFloat(data.valorAntigo)) + parseFloat(data.valor);
        })

    },
    mounted: function () {
        let vue = this;
        $3.ajax(
            {
                url: "/prestacao-contas/pagamento/item/idpronac/" + this.idpronac
                + "/uf/" + this.uf
                + "/etapa/" + this.etapa
                + "/cidade/" + this.cidade
                + "/produto/"+ this.produto
                + "/idPlanilhaItens/" + this.idplanilhaitens,
                beforeSend: function() {
                    vue.loading = true;
                },
                complete: function() {
                    vue.loading = false;
                }
            }
        )
        .done(function( data ) {
            vue.$data.informacoes = data;
        });
    },
    data: function () {
        return {
            informacoes: [],
            loading: false
        };
    },
    methods:{
        converterParaReal: function (value) {
            value = parseFloat(value);
            return numeral(value).format('0,0.00');
        }
    }
})

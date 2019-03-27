(function (global, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['../numeral'], factory);
    } else if (typeof module === 'object' && module.exports) {
        factory(require('../numeral'));
    } else {
        factory(global.numeral);
    }
}(this, function (numeral) {
    numeral.register('locale', 'pt-br', {
        delimiters: {
            thousands: '.',
            decimal: ','
        },
        abbreviations: {
            thousand: 'mil',
            million: 'milhões',
            billion: 'b',
            trillion: 't'
        },
        ordinal: function (number) {
            return 'º';
        },
        currency: {
            symbol: 'R$'
        }
    });
}));

numeral.locale('pt-br');

Vue.component('salic-proposta-planilha-orcamentaria', {
    template: `
    <div v-if="planilha" class="planilha-orcamentaria card">
        <ul class="collapsible no-margin" data-collapsible="expandable">
            <li v-for="(fontes, fonte) of planilha" v-if="isObject(fontes)">
                <div class="collapsible-header active red-text fonte">
                    <i class="material-icons">beenhere</i>{{fonte}}<span class="badge">R$ {{fontes.total | filtroFormatarParaReal}}</span>
                </div>
                <div class="collapsible-body no-padding">
                    <ul class="collapsible no-border no-margin" data-collapsible="expandable">
                        <li v-for="(produtos, produto) of fontes" v-if="isObject(produtos)">
                            <div class="collapsible-header active green-text" style="padding-left: 30px;">
                                <i class="material-icons">perm_media</i>{{produto}}<span class="badge">R$ {{produtos.total | filtroFormatarParaReal}}</span>
                            </div>
                            <div class="collapsible-body no-padding no-border">
                                <ul class="collapsible no-border no-margin" data-collapsible="expandable">
                                    <li v-for="(etapas, etapa) of produtos" v-if="isObject(etapas)">
                                         <div class="collapsible-header active orange-text" style="padding-left: 50px;">
                                            <i class="material-icons">label</i>{{etapa}}<span class="badge">R$ {{etapas.total | filtroFormatarParaReal}}</span>
                                        </div>
                                        <div class="collapsible-body no-padding no-border">
                                            <ul class="collapsible no-border no-margin" data-collapsible="expandable">
                                                <li v-for="(locais, local) of etapas" v-if="isObject(locais)">
                                                     <div class="collapsible-header active blue-text" style="padding-left: 70px;">
                                                        <i class="material-icons">place</i>{{local}} <span class="badge">R$ {{locais.total | filtroFormatarParaReal}}</span>
                                                    </div>
                                                    <div class="collapsible-body no-padding margin20 scroll-x">
                                                        <table class="bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Item</th>
                                                                    <th>Dias</th>
                                                                    <th>Qtde</th>
                                                                    <th>Ocor.</th>
                                                                    <th>Vl. Unit&aacute;rio</th>
                                                                    <th>Vl. Solicitado</th>
                                                                    <th>#</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="row of locais.itens" 
                                                                    :key="row.idPlanilhaProposta"  
                                                                    v-if="isObject(row)"
                                                                    v-bind:class="{'orange lighten-2': ultrapassaValor(row)}"
                                                                >
                                                                    <td>{{row.Seq}}</td>
                                                                    <td>{{row.Item}}</td>
                                                                    <td>{{row.QtdeDias}}</td>
                                                                    <td>{{row.Quantidade}}</td>
                                                                    <td>{{row.Ocorrencia}}</td>
                                                                    <td>{{ row.vlUnitario | filtroFormatarParaReal}}</td>
                                                                    <td>{{ row.vlSolicitado | filtroFormatarParaReal}}</td>
                                                                    <td>
                                                                        <a  v-if="row.JustProponente.length > 3"
                                                                            class="tooltipped"
                                                                            data-position="left"
                                                                            data-delay="50"
                                                                            v-bind:data-tooltip="row.JustProponente"
                                                                            ><i class="material-icons tiny">message</i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </li> 
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
        <div class="card-action">
             <span><b>Valor total do projeto:</b> R$ {{planilha.total | filtroFormatarParaReal}}</span>
        </div>
    </div>
    <div v-else>Nenhuma planilha encontrada</div>
    `,
    data: function () {
        return {
            planilha: []
        }
    },
    props: [
        'idpreprojeto',
        'arrayPlanilha'
    ],
    mounted: function () {
        if (typeof this.idpreprojeto != 'undefined') {
            this.fetch(this.idpreprojeto);
        }

        if (typeof this.arrayPlanilha != 'undefined') {
            this.planilha = this.arrayPlanilha;
        }
    },
    updated() {
        this.iniciarCollapsible();
    },
    watch: {
        arrayPlanilha: function (value) {
            this.planilha = value;
            this.iniciarCollapsible();
        }
    },
    methods: {
        fetch: function (id) {
            if (id) {
                let self = this;
                $3.ajax({
                    url: '/proposta/visualizar/obter-planilha-orcamentaria-proposta/idPreProjeto/' + id
                }).done(function (response) {
                    self.planilha = response.data;
                });
            }
        },
        formatar_data: function (date) {

            date = moment(date).format('DD/MM/YYYY');

            return date;
        },
        isObject: function (el) {
            return typeof el === "object";
        },
        iniciarCollapsible: function () {
            $3('.planilha-orcamentaria .collapsible').each(function() {
                $3(this).collapsible();
            });
        },
        ultrapassaValor: function (row) {
            return row.stCustoPraticado == true;

        },
        converterParaReal: function (value) {
           return this.$options.filters.filtroFormatarParaReal(value);
        }
    },
    filters: {
        filtroFormatarParaReal: function (value) {
            value = parseFloat(value);
            return numeral(value).format('0,0.00');
        }
    }
});


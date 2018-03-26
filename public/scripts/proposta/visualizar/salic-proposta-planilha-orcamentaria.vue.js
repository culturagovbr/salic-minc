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
            <li v-for="(fontes, fonte) of planilhaCompleta" v-if="isObject(fontes)">
                <div class="collapsible-header active red-text fonte" :class="converterStringParaClasseCss(fonte)">
                    <i class="material-icons">beenhere</i>{{fonte}}<span class="badge">R$ {{fontes.total}}</span>
                </div>
                <div class="collapsible-body no-padding">
                    <ul class="collapsible no-border no-margin" data-collapsible="expandable">
                        <li v-for="(produtos, produto) of fontes" v-if="isObject(produtos)">
                            <div class="collapsible-header active green-text" style="padding-left: 30px;" :class="converterStringParaClasseCss(produto)">
                                <i class="material-icons">perm_media</i>{{produto}}<span class="badge">R$ {{produtos.total}}</span>
                            </div>
                            <div class="collapsible-body no-padding no-border">
                                <ul class="collapsible no-border no-margin" data-collapsible="expandable">
                                    <li v-for="(etapas, etapa) of produtos" v-if="isObject(etapas)">
                                         <div class="collapsible-header active orange-text" style="padding-left: 50px;" :class="converterStringParaClasseCss(etapa)">
                                            <i class="material-icons">label</i>{{etapa}}<span class="badge">R$ {{etapas.total}}</span>
                                        </div>
                                        <div class="collapsible-body no-padding no-border">
                                            <ul class="collapsible no-border no-margin" data-collapsible="expandable">
                                                <li v-for="(locais, local) of etapas" v-if="isObject(locais)">
                                                     <div class="collapsible-header active blue-text" style="padding-left: 70px;" :class="converterStringParaClasseCss(local)">
                                                        <i class="material-icons">place</i>{{local}} <span class="badge">R$ {{locais.total}}</span>
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
                                                                <tr v-for="row of locais" 
                                                                    :key="row.idPlanilhaProposta"  
                                                                    v-if="isObject(row)"
                                                                    v-bind:class="{'orange lighten-2': ultrapassaValor(row)}"
                                                                >
                                                                    <td>{{row.Seq}}</td>
                                                                    <td>{{row.Item}}</td>
                                                                    <td>{{row.QtdeDias}}</td>
                                                                    <td>{{row.Quantidade}}</td>
                                                                    <td>{{row.Ocorrencia}}</td>
                                                                    <td>{{converterParaReal(row.vlUnitario)}}</td>
                                                                    <td>{{converterParaReal(row.vlSolicitado)}}</td>
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
             <span><b>Valor total do projeto:</b> R$ {{planilhaCompleta.total}}</span>
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
    computed: {
        planilhaCompleta: function () {

            if (!this.planilha) {
                return 0;
            }

            let novaPlanilha = {}, totalProjeto = 0, totalFonte = 0, totalProduto = 0, totalEtapa = 0, totalLocal = 0;

            novaPlanilha = this.planilha;
            Object.entries(this.planilha).forEach(([fonte, produtos]) => {
                totalFonte = 0;
                Object.entries(produtos).forEach(([produto, etapas]) => {
                    totalProduto = 0;
                    Object.entries(etapas).forEach(([etapa, locais]) => {
                        totalEtapa = 0;
                        Object.entries(locais).forEach(([local, itens]) => {
                            totalLocal = 0;
                            Object.entries(itens).forEach(([column, cell]) => {
                                totalLocal += cell.vlSolicitado;
                            });
                            this.$set(this.planilha[fonte][produto][etapa][local], 'total',  numeral(totalLocal).format('0,0.00'));
                            totalEtapa += totalLocal;
                        });
                        this.$set(this.planilha[fonte][produto][etapa], 'total', numeral(totalEtapa).format('0,0.00'));
                        totalProduto += totalEtapa;
                    });
                    this.$set(this.planilha[fonte][produto], 'total', numeral(totalProduto).format('0,0.00'));
                    totalFonte += totalProduto;
                });
                this.$set(this.planilha[fonte], 'total', numeral(totalFonte).format('0,0.00'));
                totalProjeto += totalFonte;
            });
            this.$set(novaPlanilha, 'total', numeral(totalProjeto).format('0,0.00'));

            return novaPlanilha;
        }
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
                let vue = this;
                $3.ajax({
                    url: '/proposta/visualizar/obter-planilha-orcamentaria-proposta/idPreProjeto/' + id
                }).done(function (response) {
                    vue.proposta = response.data;
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
        converterStringParaClasseCss: function(text) {
            return text.toString().toLowerCase().trim()
                .replace(/&/g, '-and-')
                .replace(/[\s\W-]+/g, '-');
        },
        ultrapassaValor: function (row) {
            return row.stCustoPraticado == true;

        },
        converterParaReal: function (value) {
            value = parseFloat(value);
            return numeral(value).format('0,0.00');
        }
    }
});


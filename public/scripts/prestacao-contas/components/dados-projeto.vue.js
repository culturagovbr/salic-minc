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

function converteParaReal(value) {
    value = parseFloat(value);
    return numeral(value).format('0,0.00');
}

Vue.component(
    'dados-projeto', {
    template: `
        <div class="col s12 m12" :informacoes = "informacoes">
            <div class="card horizontal">
                <template v-if="!loading">
                    <div class="card-stacked">
                        <div class="center-align card-content  lighten-4">
                            <span class="card-title">
                                Projeto: {{ informacoes.Pronac }} - {{ informacoes.NomeProjeto }}
                            </span>
                        </div>
                        <div class="card-content">
                            <table class="bordered">
                                <thead>
                                    <tr>
                                        <th v-html>Data Inicio da execu&ccedil;&atilde;o</th>
                                        <th v-html>Data Final da execu&ccedil;&atilde;o</th>
                                        <th class="right-align">Valor Aprovado</th>
                                        <th class="right-align">Valor Comprovado</th>
                                        <th class="right-align">Valor a Comprovar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ dataInicio }}</td>
                                        <td>{{ dataFim }}</td>
                                        <td class="right-align">R$ {{ converterParaReal(informacoes.vlAprovado) }}</td>
                                        <td class="right-align">R$ {{ converterParaReal(informacoes.vlComprovado) }}</td>
                                        <td class="right-align">R$ {{ converterParaReal(informacoes.vlComprovar) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-action">
                            <a  :href="'/consultardadosprojeto/index?idPronac=' + idpronac " target="_blank"
                                class="btn waves-effect waves-dark white-text">Ver Projeto
                            </a>
                        </div>
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
    `,
    props: ['idpronac'],
    mounted: function () {
        let vue = this;

        $3.ajax({
            url: "/prestacao-contas/pagamento/planilha-dados-projeto/idpronac/" + this.idpronac,
            beforeSend: function() {
                vue.loading = true;
            },
            complete: function(){
                vue.loading = false;
            }
        }).done(function( data ) {
            vue.$data.informacoes = data;
        });
    },
    computed: {
        dataInicio() {
            return moment(this.informacoes.dtInicioExecucao).format('DD/MM/YYYY');
        },
        dataFim() {
            return moment(this.informacoes.dtFimExecucao).format('DD/MM/YYYY');
        }
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
        },
        moment: function (value) {
            return moment();
        }
    }
})

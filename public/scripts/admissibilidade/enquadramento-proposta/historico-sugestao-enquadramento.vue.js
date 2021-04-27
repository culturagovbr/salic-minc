Vue.component('historico-sugestao-enquadramento-proposta', {
    template: `
        <div v-if="id_preprojeto && sugestoes_enquadramento">
        
            <div class="row">
                            <div class="input-field col s12 m12 center" style="margin-bottom:80px">
                                <a class="waves-effect waves-light btn modal-trigger"
                                   href="#dialog-sugestoes-enquadramento">
                                    <button class="btn waves-effect waves-light enquadrarProposta"
                                            type="button" id="botaoSugestoesEnquadramento">
                                        Visualizar Hist&oacute;rico
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="conteudoImprimir modal modal-fixed-footer" id="dialog-sugestoes-enquadramento"
                             align="center" style="width: 1150px;">
                            <div class="modal-content">
                            
                                <h4>Hist\u00F3rico de Sugest\u00F3es de Enquadramento.</h4>
                                <div class="row">
                                    <table class="tabela striped" id="historicoSugestoes" style="border: 0px solid gray">
                                        <thead>
                                        <tr class="destacar">
                                            <td class="center" width="10%"><b>N\u00FAmero</b></td>
                                            <td class="center" width="10%"><b>Data</b></td>
                                            <td class="center" width="20%"><b>Avaliador</b></td>
                                            <td class="center" width="20%"><b>Unidade</b></td>
                                            <td class="center" width="10%"><b>\u00C1rea</b></td>
                                            <td class="center" width="10%"><b>Segmento</b></td>
                                            <td class="center" width="10%"><b>Enquadramento</b></td>
                                            <td class="center"><b>Parecer</b></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(sugestao_enquadramento, indice) of sugestoes_enquadramento" 
                                                :key="sugestao_enquadramento.id_sugestao_enquadramento" >
                                                <td align="left">{{ indice+1 }}</td>
                                                <td align="left">{{ formatar_data(sugestao_enquadramento.data_avaliacao) }}</td>
                                                <td align="left">{{ sugestao_enquadramento.usu_nome }}</td>
                                                <td align="left">{{ sugestao_enquadramento.org_sigla }} - {{ sugestao_enquadramento.gru_nome }}</td>
                                                <td align="center" v-if="sugestao_enquadramento.area != null && sugestao_enquadramento.area != ''" 
                                                    class="center">{{sugestao_enquadramento.area}}</td>
                                                <td align="center" v-if="sugestao_enquadramento.area == null || sugestao_enquadramento.area == ''" 
                                                    class="center"> - </td>
                                                <td align="center" v-if="sugestao_enquadramento.segmento != null && sugestao_enquadramento.segmento != ''" 
                                                    class="center">{{sugestao_enquadramento.segmento}}</td>
                                                <td align="center" v-if="sugestao_enquadramento.segmento == null || sugestao_enquadramento.segmento == ''" 
                                                    class="center"> - </td>
                                                <td align="center" 
                                                    v-if="sugestao_enquadramento.tp_enquadramento != null && sugestao_enquadramento.tp_enquadramento == 1" 
                                                    class="center">Artigo 26</td>
                                                <td align="center" 
                                                    v-if="sugestao_enquadramento.tp_enquadramento != null && sugestao_enquadramento.tp_enquadramento != 1" 
                                                    class="center">Artigo 18</td>
                                                <td align="center" 
                                                    v-if="sugestao_enquadramento.tp_enquadramento == null" 
                                                    class="center"> - </td>
                                                <td align="left" nowrap v-html="sugestao_enquadramento.descricao_motivacao"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                        <div class="modal-footer">
                            <button class="btn waves-effect waves-light modal-action modal-close "
                                    type="button">
                                Fechar
                            </button>
                        </div>
                        </div>
                    </div>
    `,
    data: function () {
        return {
            sugestoes_enquadramento: {
                type: Object,
                default: function () {
                    return {}
                }
            }
        }
    },
    props: [
        'id_preprojeto',
        'dados'
    ],
    computed: {
        dados : function (data) {
            this.sugestoes_enquadramento = data
        }
    },
    mounted: function () {
        if(typeof this.dados == 'undefined' && typeof this.id_preprojeto != 'undefined') {
            this.buscar_dados()
        }
    },
    methods: {
        buscar_dados: function () {

            let vue = this

            $3.ajax({
                url: '/admissibilidade/enquadramento-proposta/obter-historico-sugestao-enquadramento-ajax?id_preprojeto='
                + vue.id_preprojeto
            }).done(function (response) {
                vue.sugestoes_enquadramento = response.sugestoes_enquadramento
            })
        },
        formatar_data: function (date) {

            date = moment(date).format('DD/MM/YYYY')

            return date
        }
    }
})
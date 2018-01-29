Vue.component('salic-proposta-planilha-orcamentaria', {
    template: `<div class="local-realizacao-deslocamento">
            <div class="card">
                <div class="card-content">
                    <h2>Planilha Or&ccedil;ament&aacute;ria</h2>
                        
                    <div v-if="proposta">
                        <div v-for="(recursos, fonte) of proposta">
                            <div tipo="fonte">
                                <h3 class="red-text">{{fonte}}</h3>
                                <div v-for="(produtos, produto) of recursos">
                                    <div tipo="produto">
                                        <h4 class="orange-text" v-html="produto"></h4>
                                         <div v-for="(etapas, etapa) of produtos">
                                            <div tipo="etapa">
                                                <h5 class="green-text" v-html="etapa"></h5>
                                                 <div v-for="(locais, local) of etapas">
                                                    <div tipo="local">
                                                        <h6 class="blue-text" v-html="local"></h6>
                                                         <table class="bordered">
                                                            <tbody>
                                                                <tr>
                                                                    <th>&nbsp;</th>
                                                                    <th>Item</th>
                                                                    <th>Dias</th>
                                                                    <th>Qtde</th>
                                                                    <th>Ocor.</th>
                                                                    <th>Vl. Unit&aacute;rio</th>
                                                                    <th>Vl. Solicitado</th>
                                                                </tr>
                                                                <tr v-for="row of locais">
                                                                    <td>{{row.Seq}}</td>
                                                                    <td>{{row.Item}}</td>
                                                                    <td>{{row.QtdeDias}}</td>
                                                                    <td>{{row.Quantidade}}</td>
                                                                    <td>{{row.Ocorrencia}}</td>
                                                                    <td>{{row.vlUnitario}}</td>
                                                                    <td>{{row.vlSolicitado}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div> 
                                                </div> 
                                            </div> 
                                        </div>
                                     </div> 
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div v-else>Nenhuma planilha encontrada</div>
                </div>
            </div>
        </div>
    `,
    data: function () {
        return {
            proposta: [],
            itensIncentivo: []
        }

    },
    props: [
        'idpreprojeto',
        'planilha'
    ],
    mounted: function () {
        if (typeof this.idpreprojeto != 'undefined') {
            this.fetch(this.idpreprojeto);
        }
        // if (typeof this.planilha != 'undefined') {
        //     this.proposta = this.planilha;
        //     console.log(this.planilha);
        // }
    },
    computed: {

    },
    watch: {
        idpreprojeto: function (value) {
            this.fetch(value);
        },
        planilha: function (value) {
            this.proposta = value;
        }
    },
    methods: {
        fetch: function (id) {
            if (id) {
                let vue = this;
                $3.ajax({
                    url: '/proposta/visualizar/obter-local-realizacao-deslocamento/idPreProjeto/' + id
                }).done(function (response) {
                    vue.proposta = response.data;
                });
            }
        },
        formatar_data: function (date) {

            date = moment(date).format('DD/MM/YYYY');

            return date;
        },
        valorTotalItem: function (item) {
            return item.Quantidade * item.Ocorrencia * item.ValorUnitario;
        },
        atribuirIncentivo: function (item) {
            this.itensIncentivo.push(item);
        }
    }
});
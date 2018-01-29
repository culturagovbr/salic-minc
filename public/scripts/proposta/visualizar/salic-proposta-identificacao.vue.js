Vue.component('salic-proposta-identificacao', {
    template: `
    <div class="dados-basicos" v-if="proposta">
        <div class="card">
            <div class="card-content">
                <h5>Identifica&ccedil;&atilde;o</h5>
                <div class="row">
                    <div class="col s12 l3 m3">
                        <b>Nº da proposta</b><br>
                        {{ proposta.idPreProjeto }}
                    </div>
                    <div class="col s12 l3 m6">
                        <b>Nome Projeto</b><br>
                        {{ proposta.NomeProjeto }}
                    </div>
                    <div class="col s12 l3 m3">
                        <b>Pronac</b><br>
                        {{ proposta.PRONAC }}
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 l3 m3">
                        <b>CNPJ/CPF</b><br>
                        {{ proposta.CNPJCPF }}
                    </div>
                    <div class="col s12 l6 m6">
                        <b>Proponente</b><br>
                        {{ proposta.NomeAgente }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <h5>Informa&ccedil;&otilde;es complementares</h5>
                <div class="row">
                    <div class="col s12 l3 m3">
                        <b>Mecanismo</b><br>
                        {{ Mecanismo }}
                    </div>
                    <div class="col s12 l3 m3">
                        <b>In&iacute;cio Execu&ccedil;&atilde;o</b><br>
                        {{ DtInicioDeExecucao }}
                    </div>
                    <div class="col s12 l3 m3">
                        <b>Final Execu&ccedil;&atilde;o</b><br>
                        {{ DtFinalDeExecucao }}
                    </div>
                    <div class="col s12 l3 m3">
                        <b>Dt. Fixa</b><br>
                        {{ stDataFixa }}
                    </div>
                </div>
                <div class="divider"></div>
                <div class="row">
                    <div class="col s12 l3 m3">
                        <b>Ag&ecirc;ncia banc&aacute;ria</b><br>
                        {{ proposta.AgenciaBancaria }}
                    </div>
                    <div class="col s12 l3 m3">
                        <b>É proposta audiovisual</b><br>
                        {{ AreaAbrangencia }}
                    </div>
                    <div class="col s12 l3 m3">
                        <b>Prorroga&ccedil;&atilde;o autom&aacute;tica</b><br>
                        {{ tpProrrogacao }}
                    </div>
                    <div class="col s12 l3 m3">
                        <b>Tipo de execu&ccedil;&atilde;o</b><br>
                        {{ proposta.TipoExecucao }}
                    </div>
                </div>
            </div>
        </div>
        <div v-if="proposta.NrAtoTombamento && proposta.NrAtoTombamento.length > 1" class="card">
            <div class="card-content">
                <h5>Tombamento</h5>
                <div class="row">
                    <div class="col s12 l4 m4">
                        <b>Nº Ato</b><br>
                        {{ proposta.NrAtoTombamento }}
                    </div>
                    <div class="col s12 l4 m4">
                        <b>Dt. Ato</b><br>
                        {{ DtAtoTombamento }}
                    </div>
                    <div class="col s12 l4 m4">
                        <b>Esfera</b><br>
                        {{ EsferaTombamento }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    `,
    props: {
        idpreprojeto: null,
        proposta: {
            type: Object,
            default: function () {
                return {}
            }
        }
    },
    mounted: function () {
    },
    methods: {
        label_sim_ou_nao: function (valor) {
            if (valor == 1)
                return 'Sim';
            else
                return 'Não';
        },
        label_mecanismo: function (valor) {
            switch (valor) {
                case '1':
                    return 'Mecenato';
                    break;
                default:
                    return 'Inválido';
                    break;
            }
        },
        formatar_data: function (date) {

            date = moment(date).format('DD/MM/YYYY');

            return date;
        },
        label_esfera: function (esfera) {

            let string;

            switch (esfera) {
                case '1':
                    string = 'Municipal';
                    break;
                case '2':
                    string = 'Estadual';
                    break;
                case '3':
                    string = 'Federal';
                    break;
                default:
                    string = 'Não informada';
                    break;
            }

            return string;
        }
    },
    computed: {
        stDataFixa: function () {
            return this.label_sim_ou_nao(this.proposta.stDataFixa);
        },
        AreaAbrangencia: function () {
            return this.label_sim_ou_nao(this.proposta.AreaAbrangencia);
        },
        tpProrrogacao: function () {
            return this.label_sim_ou_nao(this.proposta.tpProrrogacao);
        },
        Mecanismo: function () {
            return this.label_mecanismo(this.proposta.Mecanismo);
        },
        DtInicioDeExecucao: function () {
            return this.formatar_data(this.proposta.DtInicioDeExecucao);
        },
        DtFinalDeExecucao: function () {
            return this.formatar_data(this.proposta.DtFinalDeExecucao);
        },
        DtAtoTombamento: function () {
            return this.formatar_data(this.proposta.DtAtoTombamento);
        },
        EsferaTombamento: function () {
            return this.label_esfera(this.proposta.EsferaTombamento);
        }
    }
});
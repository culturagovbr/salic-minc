<template>
    <div
        v-if="proposta"
        class="dados-basicos">
        <div class="card">
            <div class="card-content">
                <h5>Identifica&ccedil;&atilde;o</h5>
                <div class="row">
                    <div
                        v-if="proposta.PRONAC"
                        class="col s12 l3 m3">
                        <b>Pronac</b><br>
                        {{ proposta.PRONAC }}
                    </div>
                    <div
                        v-if="proposta.idPreProjeto"
                        class="col s12 l3 m3">
                        <b>N&ordm; da proposta</b><br>
                        {{ proposta.idPreProjeto }}
                    </div>
                    <div class="col s12 l6 m6">
                        <b>Nome Projeto</b><br>
                        <SalicTextoSimples :texto="proposta.NomeProjeto"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <h5>Tipicidade e seus limites orçamentários</h5>
                <div class="row">
                    <div class="col s12 l6 m6">
                        <b>Tipicidade</b><br>
                        <SalicTextoSimples :texto="proposta.DescricaoTipicidade"/>
                    </div>
                    <div class="col s12 l6 m6">
                        <b>Tipologia</b><br>
                        <SalicTextoSimples :texto="proposta.DescricaoTipologia"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <h5>Informa&ccedil;&otilde;es complementares</h5>
                <div class="row">
                    <div
                        v-if="proposta.Mecanismo"
                        class="col s12 l3 m3">
                        <b>Mecanismo</b><br>
                        {{ mecanismo }}
                    </div>
                    <div
                        v-if="proposta.DtInicioDeExecucao"
                        class="col s12 l3 m3">
                        <b>In&iacute;cio Execu&ccedil;&atilde;o</b><br>
                        {{ dtInicioDeExecucao }}
                    </div>
                    <div
                        v-if="proposta.DtFinalDeExecucao"
                        class="col s12 l3 m3">
                        <b>Final Execu&ccedil;&atilde;o</b><br>
                        {{ dtFinalDeExecucao }}
                    </div>
                    <div
                        v-if="proposta.stDataFixa"
                        class="col s12 l3 m3">
                        <b>Dt. Fixa</b><br>
                        {{ stDataFixa }}
                    </div>
                </div>
                <div class="divider"/>
                <div class="row">
                    <div class="col s12 l3 m3">
                        <b>Ag&ecirc;ncia banc&aacute;ria</b><br>
                        <SalicTextoSimples :texto="proposta.AgenciaBancaria"/>
                    </div>
                    <div
                        v-if="proposta.AreaAbrangencia"
                        class="col s12 l3 m3">
                        <b>&Eacute; proposta audiovisual</b><br>
                        {{ areaAbrangencia }}
                    </div>
                    <div
                        v-if="proposta.tpProrrogacao"
                        class="col s12 l3 m3">
                        <b>Prorroga&ccedil;&atilde;o autom&aacute;tica</b><br>
                        {{ tpProrrogacao }}
                    </div>
                    <div class="col s12 l3 m3">
                        <b>Tipo de execu&ccedil;&atilde;o</b><br>
                        <SalicTextoSimples :texto="proposta.TipoExecucao"/>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="proposta.NrAtoTombamento && proposta.NrAtoTombamento.length > 1"
            class="card">
            <div class="card-content">
                <h5>Tombamento</h5>
                <div class="row">
                    <div class="col s12 l4 m4">
                        <b>Nº Ato</b><br>
                        {{ proposta.NrAtoTombamento }}
                    </div>
                    <div class="col s12 l4 m4">
                        <b>Dt. Ato</b><br>
                        {{ dtAtoTombamento }}
                    </div>
                    <div class="col s12 l4 m4">
                        <b>Esfera</b><br>
                        {{ esferaTombamento }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment';
import SalicTextoSimples from '@/components/SalicTextoSimples';
import { utils } from '@/mixins/utils';

export default {
    name: 'PropostaIdentificacao',
    components: {
        SalicTextoSimples,
    },
    mixins: [
        utils,
    ],
    props: {
        idpreprojeto: {
            type: null,
            default: null,
        },
        proposta: {
            type: Object,
            default() {
                return {};
            },
        },
    },
    computed: {
        stDataFixa() {
            return this.label_sim_ou_nao(this.proposta.stDataFixa);
        },
        areaAbrangencia() {
            return this.label_sim_ou_nao(this.proposta.AreaAbrangencia);
        },
        tpProrrogacao() {
            return this.label_sim_ou_nao(this.proposta.tpProrrogacao);
        },
        mecanismo() {
            return this.labelMecanismo(this.proposta.Mecanismo);
        },
        dtInicioDeExecucao() {
            return this.formatar_data(this.proposta.DtInicioDeExecucao);
        },
        dtFinalDeExecucao() {
            return this.formatar_data(this.proposta.DtFinalDeExecucao);
        },
        dtAtoTombamento() {
            return this.formatar_data(this.proposta.DtAtoTombamento);
        },
        esferaTombamento() {
            return this.labelEsfera(this.proposta.EsferaTombamento);
        },
    },
    methods: {
        labelMecanismo(valor) {
            switch (valor) {
            case '1':
            case 1:
                return 'Mecenato';
            default:
                return 'Inv\xE1lido';
            }
        },
        formatar_data(date) {
            const dateValue = moment(date).format('DD/MM/YYYY');

            return dateValue;
        },
        labelEsfera(esfera) {
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
                string = 'N\xE3o informada';
                break;
            }

            return string;
        },
    },
};
</script>

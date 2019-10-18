<template>
    <v-container
        fluid
        grid-list-md
        class="ma-0"
    >
        <v-layout
            row
            wrap
        >
            <v-flex
                d-flex
                xs12
                sm12
                md12
            >
                <v-card>
                    <v-card-title
                        primary
                        class="title"
                    >
                        Identificação
                    </v-card-title>
                    <v-card-text>
                        <v-layout
                            row
                            wrap
                        >
                            <v-flex
                                v-if="proposta.PRONAC"
                                xs12
                                sm3
                                md3
                            >
                                <b>Pronac</b><br>
                                <a :href="`/projeto/#/${proposta.idPronac}`">
                                    {{ proposta.PRONAC }}
                                </a>
                            </v-flex>
                            <v-flex
                                v-if="proposta.idPreProjeto"
                                xs12
                                sm3
                                md3
                            >
                                <b>N&ordm; da proposta</b><br>
                                {{ proposta.idPreProjeto }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm6
                                md6
                            >
                                <b>Nome Projeto</b><br>
                                <salic-texto-simples :texto="proposta.NomeProjeto" />
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex
                d-flex
                xs12
                sm12
                md12
            >
                <v-card>
                    <v-card-title
                        primary
                        class="title"
                    >
                        Tipicidade e seus limites orçamentários
                    </v-card-title>
                    <v-card-text>
                        <v-layout row>
                            <v-flex
                                v-if="proposta.Mecanismo"
                                xs12
                                sm12
                                md3
                            >
                                <b>Tipicidade</b><br>
                                <salic-texto-simples :texto="proposta.DescricaoTipicidade" />
                            </v-flex>
                            <v-flex
                                v-if="proposta.DtInicioDeExecucao"
                                xs12
                                sm12
                                md3
                            >
                                <b>Tipologia</b><br>
                                <salic-texto-simples :texto="proposta.DescricaoTipologia" />
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex
                d-flex
                xs12
                sm12
                md12
            >
                <v-card>
                    <v-card-title
                        primary
                        class="title"
                    >
                        Informações complementares
                    </v-card-title>
                    <v-card-text>
                        <v-layout row>
                            <v-flex
                                v-if="proposta.Mecanismo"
                                xs12
                                sm12
                                md3
                            >
                                <b>Mecanismo</b><br>
                                {{ proposta.Mecanismo | formatarLabelMecanismo }}
                            </v-flex>
                            <v-flex
                                v-if="proposta.DtInicioDeExecucao"
                                xs12
                                sm12
                                md3
                            >
                                <b>In&iacute;cio Execu&ccedil;&atilde;o</b><br>
                                {{ proposta.DtInicioDeExecucao | formatarData }}
                            </v-flex>
                            <v-flex
                                v-if="proposta.DtFinalDeExecucao"
                                xs12
                                sm12
                                md3
                            >
                                <b>Final Execu&ccedil;&atilde;o</b><br>
                                {{ proposta.DtFinalDeExecucao | formatarData }}
                            </v-flex>
                            <v-flex
                                v-if="proposta.stDataFixa"
                                xs12
                                sm12
                                md3
                            >
                                <b>Dt. Fixa</b><br>
                                {{ proposta.stDataFixa | formatarLabelSimOuNao }}
                            </v-flex>
                        </v-layout>
                        <v-divider />
                        <v-layout>
                            <v-flex
                                xs12
                                sm12
                                md3
                            >
                                <b>Ag&ecirc;ncia banc&aacute;ria</b><br>
                                <salic-texto-simples :texto="proposta.AgenciaBancaria" />
                            </v-flex>
                            <v-flex
                                v-if="proposta.AreaAbrangencia"
                                xs12
                                sm12
                                md3
                            >
                                <b>&Eacute; proposta audiovisual</b><br>
                                {{ proposta.AreaAbrangencia | formatarLabelSimOuNao }}
                            </v-flex>
                            <v-flex
                                v-if="proposta.tpProrrogacao"
                                xs12
                                sm12
                                md3
                            >
                                <b>Prorroga&ccedil;&atilde;o autom&aacute;tica</b><br>
                                {{ proposta.tpProrrogacao | formatarLabelSimOuNao }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm12
                                md3
                            >
                                <b>Tipo de execu&ccedil;&atilde;o</b><br>
                                <salic-texto-simples :texto="proposta.TipoExecucao" />
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex xs12>
                <v-card
                    v-if="proposta && proposta.NrAtoTombamento && proposta.NrAtoTombamento.length > 1"
                >
                    <v-card-title
                        primary
                        class="title"
                    >
                        Tombamento
                    </v-card-title>
                    <v-card-text>
                        <v-layout row>
                            <v-flex
                                xs12
                                sm12
                                md3
                            >
                                <b>Nº Ato</b><br>
                                {{ proposta.NrAtoTombamento }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm12
                                md3
                            >
                                <b>Dt. Ato</b><br>
                                {{ proposta.DtAtoTombamento | formatarData }}
                            </v-flex>
                            <v-flex
                                xs12
                                sm12
                                md3
                            >
                                <b>Esfera</b><br>
                                {{ proposta.EsferaTombamento | formatarLabelEsfera }}
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import SalicTextoSimples from '@/components/SalicTextoSimples';
import { utils } from '@/mixins/utils';

export default {
    name: 'PropostaIdentificacao',
    components: {
        SalicTextoSimples,
    },
    mixins: [utils],
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
};
</script>

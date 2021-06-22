<template>
    <div>
        <v-card>
            <v-card-text>
                <v-container
                    fluid
                    grid-list-md
                    text-xs-left
                >
                    <v-layout
                        justify-space-around
                        row
                        wrap>
                        <v-flex
                            lg12
                            dark
                            class="text-xs-left">
                            <b><h4>PARECER DE AVALIAÇÃO TÉCNICA</h4></b>
                            <v-divider class="pb-2"/>
                        </v-flex>
                        <v-flex>
                            <span><b>Parecer</b></span>
                            <p v-html="dados.dadosRelatorio.dsInformacaoAdicional"/>
                        </v-flex>
                        <v-flex>
                            <span><b>Recomendações</b></span>
                            <p v-html="dados.dadosRelatorio.dsOrientacao"/>
                        </v-flex>
                        <v-flex>
                            <span><b>Conclusão</b></span>
                            <p v-html="dados.dadosRelatorio.dsConclusao"/>
                        </v-flex>
                        <v-flex>
                            <span><b>MEDIDAS PREVENTIVAS QUANTO A IMPACTOS AMBIENTAIS</b></span>
                            <p v-html="dados.dadosRelatorio.dsMedidasPreventivas"/>
                        </v-flex>
                    </v-layout>
                    <v-layout
                        justify-space-around
                        row
                        wrap>
                        <v-flex
                            lg12
                            dark
                            class="text-xs-left">
                            <b><h4>AVALIAÇÃO</h4></b>
                            <v-divider class="pb-2"/>
                        </v-flex>
                        <v-flex>
                            <span><b>Resultado</b></span>
                            <p>
                                {{ dados.dadosRelatorio.stResultadoAvaliacao | tipoResultadoAvaliacao }}
                            </p>
                        </v-flex>
                    </v-layout>
                    <v-layout
                        justify-space-around
                        row
                        wrap>
                        <v-flex>
                            <span><b>Avaliadores</b></span>
                            <p>
                                {{ dados.tecnicoAvaliador.usu_nome }} - {{ dados.tecnicoAvaliador.usu_identificacao | cnpjFilter }}
                            </p>
                            <p>
                                {{ dados.chefiaImediata.usu_nome }} - {{ dados.chefiaImediata.usu_identificacao | cnpjFilter }}
                            </p>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
        </v-card>

    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';
import cnpjFilter from '@/filters/cnpj';

export default {
    name: 'ParecerAvaliacaoTecnica',
    filters: {
        cnpjFilter,
        tipoResultadoAvaliacao(stResultadoAvaliacao) {
            let tpResultadoAvaliacao = '';
            switch (stResultadoAvaliacao) {
            case 'A':
                tpResultadoAvaliacao = 'Cumpriu o objeto e objetivos';
                break;
            case 'R':
                tpResultadoAvaliacao = 'Não cumpriu o objeto e objetivos';
                break;
            case 'P':
                tpResultadoAvaliacao = 'Cumpriu parcialmente o objeto e objetivos';
                break;
            default:
                tpResultadoAvaliacao = '';
            }
            return tpResultadoAvaliacao;
        },
    },
    mixins: [utils],
    data() {
        return {
            pagination: {
                sortBy: '',
                descending: true,
            },
        };
    },
    computed: {
        ...mapGetters({
            dados: 'prestacaoContas/relatorioCumprimentoObjeto',
        }),
    },
};
</script>

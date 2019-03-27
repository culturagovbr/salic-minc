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
                            <b><h4>ETAPAS DE TRABALHO</h4></b>
                            <v-divider class="pb-2"/>
                        </v-flex>
                        <v-flex>
                            <span><b>ETAPAS DE TRABALHO EXECUTADAS</b></span>
                            <p v-html="dados.dadosRelatorio.dsEtapasConcluidas"/>
                        </v-flex>
                        <v-flex>
                            <span><b>MEDIDAS DE ACESSIBILIDADE, NOS TERMOS DA PORTARIA DE APROVAÇÃO</b></span>
                            <p v-html="dados.dadosRelatorio.dsMedidasAcessibilidade"/>
                        </v-flex>
                        <v-flex>
                            <span><b>MEDIDAS DE DEMOCRATIZAÇÃO DO ACESSO, NOS TERMOS DA PORTARIA DE APROVAÇÃO</b></span>
                            <p v-html="dados.dadosRelatorio.dsMedidasFruicao"/>
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
                            <span><b>EMPREGOS E QUALIFICAÇÕES DECORRENTES DO PROJETO</b></span>
                        </v-flex>
                        <v-flex>
                            <span><b>Diretos</b></span>
                            <p>
                                {{ dados.dadosRelatorio.qtEmpregosDiretos }}
                            </p>
                        </v-flex>
                        <v-flex>
                            <span><b>Indiretos</b></span>
                            <p>
                                {{ dados.dadosRelatorio.qtEmpregosIndiretos }}
                            </p>
                        </v-flex>
                        <v-flex>
                            <span><b>Total</b></span>
                            <p>
                                {{ dados.dadosRelatorio.qtEmpregosDiretos + dados.dadosRelatorio.qtEmpregosIndiretos }}
                            </p>
                        </v-flex>
                    </v-layout>

                    <v-layout
                        justify-space-around
                        row
                        wrap>
                        <v-flex>
                            <span><b>Geração de Empregos</b></span>
                            <p v-html="dados.dadosRelatorio.dsGeracaoEmpregos"/>
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

export default {
    name: 'EtapaTrabalho',
    mixins: [utils],
    data() {
        return {
            pagination: {
                sortBy: '',
                descending: true,
            },
            headers: [
                {
                    text: 'Produto',
                    align: 'left',
                    value: 'Produto',
                },
                {
                    text: 'Patrocionador',
                    align: 'left',
                    value: 'QtdePatrocinador',
                },
                {
                    text: 'Divulgação',
                    align: 'right',
                    value: 'QtdeProponente',
                },
                {
                    text: 'Beneficiários',
                    align: 'right',
                    value: 'QtdeOutros',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dados: 'prestacaoContas/relatorioCumprimentoObjeto',
        }),
    },
};
</script>

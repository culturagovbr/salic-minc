<template>
    <v-container fluid>
        <v-card>
            <v-card-title primary-title>
                <h3>TÍTULO A SER DEFINIDO</h3>
            </v-card-title>
            <v-card-text>
                <p v-if="existeDiligencia">Existe Diligência para esse projeto. Acesse <router-link to="#">aqui</router-link>.</p>
                <p v-else>Sem Observações.</p>
            </v-card-text>
            <v-card-actions>
                <v-btn color="success" to="#">VER PROJETO</v-btn>
                <v-btn color="success" to="#">CONSOLIDAÇÃO</v-btn>
            </v-card-actions>
        </v-card>
        <v-card class="mt-3" flat>
            <!-- PRODUTO -->
            <v-expansion-panel
                expand
                v-if="this.getPlanilha != undefined && Object.keys(this.getPlanilha)"
            >
                <v-expansion-panel-content
                    v-for="(produto,i) in getPlanilha"
                    :key="i"
                >
                    <v-layout slot="header" class="green--text">
                        <v-icon class="mr-3 green--text">perm_media</v-icon>
                        {{ produto.produto }}
                    </v-layout>

                        <!-- ETAPA -->
                        <v-expansion-panel class="pl-3 elevation-0"
                            expand
                        >
                            <v-expansion-panel-content
                                v-for="(etapa,i) in produto.etapa"
                                :key="i"
                            >
                                <v-layout slot="header" class="orange--text">
                                    <v-icon class="mr-3 orange--text">label</v-icon>
                                    {{ etapa.etapa }}
                                </v-layout>

                                <!-- UF -->
                                <v-expansion-panel
                                    class="pl-3 elevation-0"
                                    expand
                                >
                                    <v-expansion-panel-content
                                        v-for="(uf,i) in etapa.UF"
                                        :key="i"
                                    >
                                        <v-layout slot="header" class="blue--text">
                                            <v-icon class="mr-3 blue--text">place</v-icon>
                                            {{ uf.Uf }}
                                        </v-layout>

                                        <!-- CIDADE -->
                                        <v-expansion-panel
                                            class="pl-3 elevation-0"
                                            expand
                                        >
                                            <v-expansion-panel-content
                                                v-for="(cidade,i) in uf.cidade"
                                                :key="i"
                                            >
                                                <v-layout slot="header" class="blue--text">
                                                    <v-icon class="mr-3 blue--text">place</v-icon>
                                                    {{ cidade.cidade }}
                                                </v-layout>
 <v-tabs
        color="cyan"
        dark
        slider-color="yellow"
      >
        <v-tab
          ripple
        >
         Todos
        </v-tab>
        <v-tab
          ripple
        >
         Aguardando Analise
        </v-tab>
        <v-tab
          ripple
        >
         Avaliado
        </v-tab>
        <v-tab
          ripple
        >
         Impugnados
        </v-tab>

        <v-tab-item>
            <template v-if="cidade.itens != undefined && typeof cidade.itens[1] !== 'undefined'">
                {{Object.values(cidade.itens[1])}}
                <v-data-table
                    :headers="[{text:'item', value:'item'}]"
                    :items="Object.values(cidade.itens[1])"
                    hide-actions
                    class="elevation-1"
                >
                    <template slot="items" slot-scope="props">
                        <td>{{ props.item.item }}</td>
                    </template>
                </v-data-table>
            </template>
        </v-tab-item>
      </v-tabs>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                </v-expansion-panel-content>
            </v-expansion-panel>
        </v-card>

        <v-card class="mt-3 pa-3" color="teal">
            <table class="white--text font-weight-bold" width="100%">
                <tr>
                    <td>Valor Aprovado</td>
                    <td>R$ 00,00</td>
                </tr>
                <tr>
                    <td>Valor Comprovado</td>
                    <td>R$ 00,00</td>
                </tr>
                <tr>
                    <td>Valor a Comprovar</td>
                    <td>R$ 00,00</td>
                </tr>
            </table>
        </v-card>
    </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import ModalTemplate from '@/components/modal';

export default {
    name: 'Painel',
    data() {
        return {
            existeDiligencia: true,
            produtos: this.planilha,
        };
    },
    computed: {
        valorAprovado() { },
        valorComprovado() { },
        valorAComprovar() { },
        ...mapGetters({
            getPlanilha: 'avaliacaoResultados/planilha',
        }),
    },
    props: {
        idpronac: '132451',
    },
    mounted() {
        this.setPlanilha();
    },
    components: {
        ModalTemplate,
    },
    methods: {
        ...mapActions({
            setPlanilha: 'avaliacaoResultados/planilha',
        }),
    },
};
</script>

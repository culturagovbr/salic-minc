<template>
    <v-dialog
        v-model="dialog"
        fullscreen
        transition="dialog-bottom-transition">

        <v-tooltip
            v-if="typeof obj.dsParecer === 'undefined'"
            slot="activator"
            bottom>
            <v-btn
                slot="activator"
                flat
                icon
                round
                @click.native="sincState(idPronac)">
                <v-icon
                    :color="statusButton(obj,laudo).color"
                    :change="statusButton(obj,laudo).color"
                    class="material-icons">{{ statusButton(obj,laudo).icon }}</v-icon>
            </v-btn>
            <span>{{ statusButton(obj,laudo).texto }}</span>
        </v-tooltip>
        <v-btn
            v-else
            slot="activator"
            :color="statusButton(obj,laudo).color"
            round
            dark
            @click.native="sincState(idPronac)">
            <v-icon class="material-icons">{{ statusButton(obj,laudo).icon }}</v-icon>
            <span>&nbsp;{{ statusButton(obj,laudo).texto }}</span>
        </v-btn>

        <v-card>
            <v-toolbar
                dark
                color="#0a420e !important"
                fixed
            >
                <v-btn
                    icon
                    dark
                    @click.native="dialog = false">
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>Avaliação de Resultados - Visualizar Parecer</v-toolbar-title>
            </v-toolbar>
            <carregando-vuetify
                v-if="loading"
                class="mt-5"
                text="Carregando parecer" />

            <v-container v-else-if="parecerObjeto === null">
                <v-alert
                    :value="true"
                    type="warning"
                    class="mt-5 black--text"
                >
                    Avaliação do cumprimento do objeto não encontrada!
                </v-alert>
            </v-container>

            <v-container
                v-else
                grid-list-sm>
                <v-layout
                    row
                    wrap
                    class="mt-5"
                >
                    <v-flex
                        xs12
                        sm12
                        md12>
                        <p><b>Projeto:</b> {{ projeto.AnoProjeto }}{{ projeto.Sequencial }} -- {{ projeto.NomeProjeto }}</p>
                    </v-flex>
                    <v-flex
                        v-if="proponente.CgcCpf || proponente.Nome"
                        xs12
                        sm12
                        md12>
                        <p><b>Proponente:</b> {{ proponente.CgcCpf | cnpjFilter }} -- {{ proponente.Nome }}</p>
                    </v-flex>
                </v-layout>
                <v-divider/>
            </v-container>

            <v-container
                v-if="parecerObjeto !== null"
                grid-list-sm>
                <h2 class="text-sm-center">Parecer de avaliação do cumprimento do objeto</h2>

                <v-layout
                    wrap
                    align-center>
                    <v-flex
                        xs12
                        sm12
                        md12>
                        <div>
                            <p><b>Manifestação: </b>{{ parecerObjeto.dsManifestacaoObjeto }}</p>
                        </div>
                    </v-flex>
                    <v-flex
                        xs12
                        sm12
                        md12>
                        <div>
                            <h4>Parecer: </h4>
                            <p v-html="parecerObjeto.dsParecerDeCumprimentoDoObjeto"/>
                        </div>
                    </v-flex>
                </v-layout>
                <v-divider/>
            </v-container>
            <div v-if="Object.keys(parecerTecnico).length !== 0">
                <h2 class="text-sm-center">Parecer técnico de avaliação financeira</h2>
                <v-container grid-list-sm>
                    <v-layout
                        wrap
                        align-center>
                        <v-flex
                            xs12
                            sm12
                            md12>
                            <div>
                                <p v-if="parecerTecnico.siManifestacao == 'A'"><b>Manifestação: </b> Aprovado</p>
                                <p v-else-if="parecerTecnico.siManifestacao == 'P'"><b>Manifestação: </b> Aprovado com ressalva</p>
                                <p v-else-if="parecerTecnico.siManifestacao == 'R'"><b>Manifestação: </b> Reprovado</p>
                            </div>
                        </v-flex>
                        <v-flex
                            xs12
                            sm12
                            md12>
                            <div>
                                <h4>Parecer: </h4>
                                <p v-html="parecerTecnico.dsParecer"/>
                            </div>
                        </v-flex>
                    </v-layout>
                    <v-divider/>
                </v-container>
            </div>
            <div v-if="parecerLaudoFinal.items && parecerLaudoFinal.items.length !== 0">
                <h2 class="text-sm-center">Laudo Final da Avaliação de Resultado</h2>
                <v-container grid-list-sm>
                    <v-layout
                        wrap
                        align-center>
                        <v-flex
                            xs12
                            sm12
                            md12>
                            <div>
                                <p v-if="parecerLaudoFinal.items.siManifestacao == 'A'"><b>Manifestação: </b> Aprovado</p>
                                <p v-else-if="parecerLaudoFinal.items.siManifestacao == 'P'"><b>Manifestação: </b> Aprovado com ressalva</p>
                                <p v-else-if="parecerLaudoFinal.items.siManifestacao == 'R'"><b>Manifestação: </b> Reprovado</p>
                            </div>
                        </v-flex>
                        <v-flex
                            xs12
                            sm12
                            md12>
                            <div>
                                <h4>Laudo: </h4>
                                <div v-html="parecerLaudoFinal.items.dsLaudoFinal"/>
                            </div>
                        </v-flex>
                    </v-layout>
                    <v-divider/>
                </v-container>
            </div>

        </v-card>
    </v-dialog>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import cnpjFilter from '@/filters/cnpj';
import CarregandoVuetify from '@/components/CarregandoVuetify';

export default {
    name: 'VisualizarParecer',
    components: { CarregandoVuetify },
    filters: {
        cnpjFilter,
    },
    props: {
        idPronac: {
            type: [Number, String],
            required: true,
        },
        obj: {
            type: Object,
            default: () => {},
        },
        laudo: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            dialog: false,
            loading: false,
        };
    },
    computed: {
        ...mapGetters({
            proponente: 'avaliacaoResultados/proponente',
            parecerTecnico: 'avaliacaoResultados/parecer',
            parecerObjeto: 'avaliacaoResultados/objetoParecer',
            projeto: 'avaliacaoResultados/projeto',
            parecerLaudoFinal: 'avaliacaoResultados/getParecerLaudoFinal',
        }),
    },
    methods: {
        ...mapActions({
            requestEmissaoParecerAction: 'avaliacaoResultados/getDadosEmissaoParecer',
            getLaudoFinalAction: 'avaliacaoResultados/getLaudoFinal',
        }),
        sincState(id) {
            this.loading = true;
            this.requestEmissaoParecerAction(id)
                .finally(() => {
                    this.loading = false;
                });
            this.getLaudoFinalAction(id);
        },
        statusButton(obj, laudo) {
            let status = {};

            if (laudo && obj.siManifestacao === null) {
                status = {
                    color: 'red',
                    icon: 'sentiment_very_dissatisfied',
                    texto: 'Objeto Reprovado',
                };
            } else
            if (typeof obj.dsParecer === 'undefined') {
                status = {
                    color: '',
                    icon: 'filter_frames',
                    texto: 'Visualizar Objeto',
                };
            } else if (typeof obj.dsParecer !== 'undefined' && obj.siManifestacao === 'A') {
                status = {
                    color: 'green darken-4',
                    icon: 'mood',
                    texto: 'Aprovado',
                };
            } else if (typeof obj.dsParecer !== 'undefined' && obj.siManifestacao === 'P') {
                status = {
                    color: 'green lighten-1',
                    icon: 'sentiment_satisfied_alt',
                    texto: 'Aprovado com ressalva',
                };
            } else if (typeof obj.dsParecer !== 'undefined' && obj.siManifestacao === 'R') {
                status = {
                    color: 'red',
                    icon: 'sentiment_very_dissatisfied',
                    texto: 'Reprovado',
                };
            }

            return status;
        },
    },
};
</script>

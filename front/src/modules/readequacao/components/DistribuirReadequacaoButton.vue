<template>
    <v-layout>
        <v-tooltip bottom>
            <v-icon
                slot="activator"
                color="blue darken-3"
                class="material-icons"
                @click.stop="dialog = true"
            >
                forward
            </v-icon>
            <span>Distribuir Readequação</span>
        </v-tooltip>
        <v-dialog
            v-model="dialog"
            fullscreen
            hide-overlay
            transition="dialog-bottom-transition"
            scrollable
            @keydown.esc="dialog = false"
        >
            <v-card
                v-if="loading"
            >
                <carregando
                    :text="'Montando distribuir readequação...'"
                    class="mt-5"
                />
            </v-card>
            <v-card
                v-else
            >
                <v-card tile>
                    <v-toolbar
                        card
                        dark
                        color="primary"
                    >
                        <v-btn
                            icon
                            dark
                            @click="dialog = false"
                        >
                            <v-icon>close</v-icon>
                        </v-btn>
                        <v-toolbar-title>
                            Avaliar readequação -
                            {{ dadosReadequacao.idReadequacao }} - {{ dadosReadequacao.NomeProjeto }} - {{ dadosReadequacao.tpReadequacao }}
                        </v-toolbar-title>
                        <v-spacer />
                        <v-toolbar-items>
                            <v-btn
                                dark
                                flat
                                @click="salvarAnalise"
                            >
                                <v-icon left>
                                    save
                                </v-icon>
                                Salvar
                            </v-btn>
                        </v-toolbar-items>
                    </v-toolbar>
                    <v-card-text>
                        <v-form
                            ref="form"
                            v-model="valid"
                            lazy-validation
                        >
                            <v-layout wrap>
                                <v-flex
                                    xs12
                                    sm12
                                    md12
                                >
                                    <v-radio-group
                                        v-model="readequacaoEditada.stAtendimento"
                                    >
                                        <v-radio
                                            :label="`Receber`"
                                            :value="`D`"
                                            color="green"
                                        />
                                        <v-radio
                                            :label="`Rejeitar`"
                                            :value="`I`"
                                            color="green"
                                        />
                                        <v-radio
                                            :label="`Devolver ao proponente`"
                                            :value="`E`"
                                            color="green"
                                        />
                                    </v-radio-group>
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm12
                                    md12
                                >
                                    <p><b>Parecer sobre a solicitação de readequação</b></p>
                                    <s-editor-texto
                                        v-model="readequacaoEditada.dsAvaliacao"
                                        :placeholder="'Parecer sobre a solicitação de readequação'"
                                        :min-char="minChar"
                                        @editor-texto-counter="validateText($event)"
                                    />
                                </v-flex>
                            </v-layout>
                            <v-subheader class="pa-0">
                                Todos os campos s&atilde;o obrigat&oacute;rios
                            </v-subheader>
                            <v-layout
                                row
                                justify-center
                            >
                                <v-btn
                                    color="primary"
                                    @click="salvarAnalise()"
                                >
                                    <v-icon left>
                                        save
                                    </v-icon>
                                    Salvar
                                </v-btn>
                                <v-btn
                                    v-if="finalizarDisponivel"
                                    dark
                                    color="blue accent-4"
                                    @click="dialogFinalizar = true"
                                >
                                    Finalizar
                                    <v-icon right>
                                        gavel
                                    </v-icon>
                                </v-btn>
                                <v-btn
                                    @click="dialog = false"
                                >
                                    Fechar
                                    <v-icon right>
                                        close
                                    </v-icon>
                                </v-btn>
                            </v-layout>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
import { mapActions } from 'vuex';
import SEditorTexto from '@/components/SalicEditorTexto';
import Carregando from '@/components/CarregandoVuetify';

export default {
    name: 'DistribuirReadequacaoButton',
    components: {
        Carregando,
        SEditorTexto,
    },
    props: {
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            finalizarDisponivel: false,
            valid: false,
            loading: true,
            dialog: false,
            minChar: 10,
            readequacaoEditada: {
                idPronac: 0,
                idReadequacao: 0,
                idTipoReadequacao: '',
                dsAvaliacao: '',
                stAtendimento: '',
            },
        };
    },
    mounted() {
        this.inicializarReadequacaoEditada();
    },
    methods: {
        ...mapActions({
            updateReadequacao: 'readequacao/updateReadequacao',
            mensagemSucesso: 'noticias/mensagemSucesso',
        }),
        inicializarReadequacaoEditada() {
            this.readequacaoEditada = {
                idPronac: this.dadosReadequacao.idPronac,
                idReadequacao: this.dadosReadequacao.idReadequacao,
                idTipoReadequacao: this.dadosReadequacao.idTipoReadequacao,
                dsAvaliacao: this.dadosReadequacao.dsAvaliacao || '',
                stAtendimento: this.dadosReadequacao.stAtendimento,
            };
            this.loading = false;
        },
        salvarAnalise() {
            this.updateReadequacao(this.readequacaoEditada).then(() => {
                this.mensagemSucesso('Readequação salva com sucesso!');
            });
        },
        validateText(e) {
            this.textIsValid = e >= this.minChar;
        },
    },
};
</script>

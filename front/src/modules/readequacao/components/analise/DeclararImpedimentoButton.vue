<template>
    <v-layout>
        <v-tooltip
            bottom
        >
            <v-icon
                slot="activator"
                color="orange accent-4"
                @click.stop="dialog = true"
            >
                voice_over_off
            </v-icon>
            <span>Declarar impedimento</span>
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
                            Declarar impedimento
                            {{ dadosReadequacao.idReadequacao }} - {{ dadosReadequacao.NomeProjeto }} - {{ dadosReadequacao.tpReadequacao }}
                        </v-toolbar-title>
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
                                    <s-editor-texto
                                        v-model="dsObservacao"
                                        :placeholder="'Motivo do impedimento de análise de readequação'"
                                        :min-char="minChar"
                                        @editor-texto-counter="validateText($event)"
                                    />
                                </v-flex>
                            </v-layout>
                            <v-subheader class="pa-0 pt-5">
                                Todos os campos s&atilde;o obrigat&oacute;rios
                            </v-subheader>
                            <v-layout
                                row
                                justify-center
                            >
                                <v-btn
                                    dark
                                    color="orange accent-4"
                                    @click="enviarDeclararImpedimento()"
                                >
                                    <v-icon left>
                                        send
                                    </v-icon>
                                    Declarar impedimento
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
    name: 'DeclararImpedimentoButton',
    components: {
        SEditorTexto,
        Carregando,
    },
    props: {
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            textIsValid: false,
            valid: false,
            dialog: false,
            loading: false,
            minChar: 10,
            dsObservacao: '',
        };
    },
    methods: {
        ...mapActions({
            declararImpedimento: 'readequacao/declararImpedimento',
        }),
        validateText(e) {
            this.textIsValid = e >= this.minChar;
        },
        enviarDeclararImpedimento() {
            // TODO
        },
    },
};
</script>

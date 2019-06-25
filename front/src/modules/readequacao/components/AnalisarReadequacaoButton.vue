<template>
    <v-layout>
        <v-btn
            dark
            icon
            flat
            small
            color="#212121"
            @click.stop="dialog = true"
        >
            <v-tooltip bottom>
                <v-icon slot="activator">
                    gavel
                </v-icon>
                <span>Analisar Readequação</span>
            </v-tooltip>
        </v-btn>
        <v-dialog
            v-model="dialog"
            fullscreen
            hide-overlay
            transition="dialog-bottom-transition"
            @keydown.esc="dialog = false"
        >
            <v-card>
                <v-toolbar
                    dark
                    color="primary"
                    fixed
                >
                    <v-btn
                        icon
                        dark
                        @click="dialog = false"
                    >
                        <v-icon>
                            close
                        </v-icon>
                    </v-btn>
                    <v-toolbar-title>Readequação - {{ dadosReadequacao.dsTipoReadequacao }}</v-toolbar-title>
                    <v-spacer/>
                    <v-toolbar-title>{{ dadosProjeto.Pronac }} - {{ dadosProjeto.NomeProjeto }}</v-toolbar-title>
                </v-toolbar>
                <v-layout
                    row
                    wrap
                    class="mt-5"
                >
                    <v-flex
                        v-if="loading"
                        xs10
                        offset-xs1
                    >
                        <carregando
                            :text="'Montando edição de readequação...'"
                            class="mt-5"
                        />
                    </v-flex>
                    <v-flex
                        v-else
                        xs10
                        offset-xs1
                    >
                        <v-card>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
import Carregando from '@/components/CarregandoVuetify';
import validarFormulario from '../mixins/validarFormulario';
import verificarPerfil from '../mixins/verificarPerfil';

export default {
    name: 'AnalisarReadequacaoButton',
    components: {
        Carregando,
    },
    mixins: [
        validarFormulario,
        verificarPerfil,
    ],
    props: {
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
        dadosProjeto: {
            type: Object,
            default: () => {},
        },
        minChar: {
            type: Object,
            default: () => {},
        },
        perfisAceitos: {
            type: Array,
            default: () => [],
        },
        perfil: {
            type: [Number, String],
            default: 0,
        },
    },
    data() {
        return {
            dialog: false,
            loading: false,
        };
    },
};
</script>

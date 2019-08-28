<template>
    <v-layout>
        <template
            v-if="loading"
        >
            <carregando
                :text="'Montando comparação'"
                class="mt-5 pb-4"
            />
        </template>
        <template v-else>
            <v-layout
                row
                wrap
            >
                <v-flex
                    v-if="tipoComponente === 'textarea'"
                    xs12
                    class="text-xs-center"
                >
                    <v-btn
                        class="blue darken-1"
                        dark
                        @click="dialog = true"
                    >
                        <v-icon>
                            subject
                        </v-icon>
                        <p class="ma-2">
                            Visualizar com formatação original
                        </p>
                    </v-btn>
                </v-flex>
                <v-flex
                    xs12
                >
                    <campo-diff
                        :original-text="original.valor"
                        :changed-text="changed.dsSolicitacao"
                        :method="'diffWordsWithSpace'"
                    />
                </v-flex>
            </v-layout>
            <v-dialog
                v-model="dialog"
                max-width="950"
            >
                <v-card>
                    <v-card-title>
                        <h3>Solicitação - formatação original</h3>
                    </v-card-title>
                    <v-card-text v-html="changed.dsSolicitacao" />
                </v-card>
            </v-dialog>
        </template>
    </v-layout>
</template>
<script>
import Carregando from '@/components/CarregandoVuetify';
import CampoDiff from '@/components/CampoDiff';

export default {
    name: 'ComparacaoTextual',
    components: {
        Carregando,
        CampoDiff,
    },
    props: {
        original: {
            type: Object,
            default: () => {},
        },
        changed: {
            type: Object,
            default: () => {},
        },
        tipoComponente: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            loading: true,
            dialog: false,
        };
    },
    mounted() {
        this.loading = false;
    },
};
</script>

<template>
    <v-expansion-panel
        popout
        focusable>
        <v-expansion-panel-content class="elevation-1">
            <v-layout
                slot="header"
                class="primary--text">
                <v-icon class="mr-3 primary--text">{{ icon }}</v-icon>
                <span v-html="label"/>
            </v-layout>
            <v-card>
                <v-card-text>
                    <slot
                        :texto="texto"
                        :itens="itens"
                    >
                        <tabela-dados-complementares
                            :texto="texto"/>
                    </slot>
                </v-card-text>
            </v-card>
        </v-expansion-panel-content>
    </v-expansion-panel>
</template>

<script>
import { utils } from '@/mixins/utils';
import TabelaDadosComplementares from './TextoDadosComplementares';

export default {
    name: 'DadosComplementares',
    components: { TabelaDadosComplementares },
    mixins: [utils],
    props: {
        label: {
            type: String,
            default: '',
        },
        icon: {
            type: String,
            default: 'subject',
        },
        itens: {
            type: [Array],
            default: () => [],
        },
        texto: {
            type: String,
            default: 'subject',
        },
    },
};
</script>

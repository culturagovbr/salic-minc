<template>
    <v-container
        fluid
        class="pa-0"
    >
        <v-flex
            xs12
            sm6
            class="py-2"
        >
            <v-btn-toggle
                v-model="opcoesDeVisualizacao"
                multiple
                @change="$emit('input', $event)"
            >
                <v-tooltip bottom>
                    <v-btn
                        slot="activator"
                        flat
                    >
                        <v-icon>calendar_view_day</v-icon>
                    </v-btn>
                    <span>
                        Mostrar planilha completa
                    </span>
                </v-tooltip>
                <v-tooltip bottom>
                    <v-btn
                        slot="activator"
                        flat
                    >
                        <v-icon>compare</v-icon>
                    </v-btn>
                    <span>
                        Comparar planilhas
                    </span>
                </v-tooltip>
                <v-tooltip bottom>
                    <v-btn
                        slot="activator"
                        flat
                    >
                        <v-icon>list</v-icon>
                    </v-btn>
                    <span>
                        Apenas itens
                    </span>
                </v-tooltip>
                <v-tooltip bottom>
                    <v-btn
                        slot="activator"
                        flat
                    >
                        <v-icon>select_all</v-icon>
                    </v-btn>
                    <span>
                        Habilitar modo seleção
                    </span>
                </v-tooltip>
            </v-btn-toggle>
        </v-flex>
    </v-container>
</template>
<script>

export default {
    name: 'AnaliseDeCustosButtons',
    props: {
        value: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            opcoesDeVisualizacao: [],
        };
    },
    watch: {
        value(val) {
            this.opcoesDeVisualizacao = val;
        },
    },
    mounted() {
        this.opcoesDeVisualizacao = this.value;
    },
};
</script>

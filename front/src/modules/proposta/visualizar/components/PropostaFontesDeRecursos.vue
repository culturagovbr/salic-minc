<template>
    <v-card>
        <v-card-title
            primary
            class="title"
        >
            Valor por fonte de recurso
        </v-card-title>
        <v-card-text>
            <v-data-table
                v-if="dado"
                :headers="headers"
                :items="dado.lines"
                class="elevation-1"
            >
                <template
                    slot="items"
                    slot-scope="props"
                >
                    <td>{{ props.item.Descricao }}</td>
                    <td class="text-xs-right">
                        {{ props.item.Valor }}
                    </td>
                </template>
            </v-data-table>
            <div v-else>
                Nenhuma fonte encontrada
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'PropostaFontesDeRecursos',
    props: {
        idpreprojeto: {
            type: [Number, String],
            default: null,
        },
    },
    data() {
        return {
            headers: [
                { text: 'Fonte Recurso', value: 'Descricao' },
                { text: 'Valor (R$)', align: 'center', value: 'Valor' },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dado: 'proposta/fontesDeRecursos',
        }),
    },
    watch: {
        idpreprojeto(value) {
            this.buscaFontesDeRecursos(value);
        },
    },
    mounted() {
        if (this.idpreprojeto) {
            this.buscaFontesDeRecursos(this.idpreprojeto);
        }
    },
    methods: {
        ...mapActions({
            buscaFontesDeRecursos: 'proposta/buscaFontesDeRecursos',
        }),
    },
};
</script>

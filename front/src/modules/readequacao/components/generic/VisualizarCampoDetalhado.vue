<template>
    <v-dialog
        :value="dialog"
        transition="dialog-bottom-transition"
        max-width="800"
        @update:returnValue="fecharVisualizacao"
    >
        <v-card>
            <v-card-title
                class="headline green lighten-2 white--text"
                primary-title
            >
                <v-btn
                    fab
                    small
                >
                    <v-icon class="green--text">
                        assignment
                    </v-icon>
                </v-btn>
                {{ dados.titulo }}
            </v-card-title>
            <v-card-text>
                {{ dados.descricao }}
            </v-card-text>
            <v-divider/>
            <v-card-actions>
                <v-spacer/>
                <v-btn
                    color="primary"
                    flat
                    @click="fecharVisualizacao"
                >
                    OK
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    name: 'VisualizarCampoDetalhado',
    props: {
        dialog: { type: Boolean, default: false },
        dados: { type: Object, default: () => {} },
    },
    methods: {
        fecharVisualizacao() {
            this.$emit('fechar-visualizacao');
        },
    },
};
</script>

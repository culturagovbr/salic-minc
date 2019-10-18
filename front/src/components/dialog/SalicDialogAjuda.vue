<template>
    <v-dialog
        v-model="dialog"
        max-width="800px"
        @keydown.esc="dialog = false"
    >
        <v-card>
            <v-card-title
                class="headline grey lighten-2"
                primary-title
            >
                {{ titulo }}
            </v-card-title>
            <v-card-text
                class="pa-4"
                v-text="mensagem"
            />
            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="primary"
                    text
                    @click="dialog = !dialog"
                >
                    Ok
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
/**
   * Dialog de Confirmacao
   *
   * Como utilizar:
   * Não é necessário chamar o componente, basta chamar:
   * this.$root.$ajuda('Deseja confirmar a alteração?')
   *
   */

export default {
    name: 'DialogAjuda',
    data() {
        return {
            dialog: false,
            mensagem: null,
            titulo: null,
        };
    },
    methods: {
        open(
            mensagem = '',
            titulo = 'Ajuda',
        ) {
            this.dialog = true;
            this.mensagem = mensagem;
            this.titulo = titulo;
        },
    },
};
</script>

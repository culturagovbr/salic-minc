<template>
    <v-dialog
        v-model="dialog"
        :max-width="options.width"
        :style="{ zIndex: options.zIndex }"
        persistent
        @keydown.esc="response(false)"
    >
        <v-card>
            <v-card-title class="headline">
                {{ title }}
            </v-card-title>
            <v-card-text v-text="message" />
            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="secondary"
                    text
                    @click="response(false)"
                >
                    {{ buttonCancel }}
                </v-btn>
                <v-btn
                    color="primary"
                    text
                    @click="response(true)"
                >
                    {{ buttonConfirm }}
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
   * this.$root.$confirm('Deseja confirmar a alteração?', 'Deletar item', { color: 'red' }).then((confirm) => {})
   * Ou use await:
   * if (await this.$root.$confirm('Deseja confirmar a alteração?', 'Deletar item', { color: 'red' })) {
   *   // sim
   * }
   * else {
   *   // cancelar
   * }
   */

export default {
    name: 'DialogConfirmacao',
    data() {
        return {
            dialog: false,
            resolve: null,
            reject: null,
            message: null,
            buttonCancel: null,
            buttonConfirm: null,
            title: null,
            options: {
                color: 'primary',
                width: 300,
                zIndex: 200,
            },
        };
    },
    methods: {
        open(
            message = 'Deseja confirmar a alteração?',
            title = 'Atenção',
            options,
            buttonConfirm = 'Confirmar',
            buttonCancel = 'Cancelar',
        ) {
            this.dialog = true;
            this.message = message;
            this.title = title;
            this.options = Object.assign(this.options, options);
            this.buttonConfirm = buttonConfirm;
            this.buttonCancel = buttonCancel;
            return new Promise((resolve, reject) => {
                this.resolve = resolve;
                this.reject = reject;
            });
        },
        response(value) {
            this.resolve(value);
            this.dialog = false;
        },
    },
};
</script>

<template>
    <v-dialog
        v-model="dialog"
        v-bind="$attrs"
        persistent
        width="300"
    >
        <v-card
            color="primary"
            dark
        >
            <v-card-text>
                {{ label }}
                <v-progress-linear
                    indeterminate
                    color="white"
                    class="mb-0"
                />
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'SalicProgressoDialog',
    props: {
        label: {
            type: String,
            default: 'Aguarde, carregando...',
        },
        value: {
            type: Boolean,
            default: false,
        },
        text: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            dialog: false,
        };
    },
    watch: {
        value(val) {
            this.dialog = val;
        },
        dialog(val) {
            this.$emit('input', val);
        },
    },
};
</script>

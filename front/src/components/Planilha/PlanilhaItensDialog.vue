<template>
    <v-dialog
        v-model="dialog"
        v-bind="$attrs"
        max-width="500px"
    >
        <v-card>
            <v-card-title>
                <span class="headline">
                    {{ title }}
                </span>
            </v-card-title>

            <v-card-text>
                <v-container
                    fluid
                    pa-0
                    grid-list-md
                >
                    <slot
                        :item="item"
                    >
                        <v-layout wrap>
                            <v-flex
                                xs12
                                v-html="item.JustProponente"
                            />
                        </v-layout>
                    </slot>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="blue darken-1"
                    flat
                    @click="close"
                >
                    Fechar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'SPlanilhaDialog',
    props: {
        title: {
            type: String,
            default: 'Justificativa',
        },
        value: {
            type: Boolean,
            default: false,
        },
        item: {
            type: Object,
            default: () => {},
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
    methods: {
        close() {
            this.dialog = false;
        },
    },
};
</script>

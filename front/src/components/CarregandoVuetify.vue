<template>
    <v-layout
        column
        class="pb-3">
        <v-flex xs12>
            <div class="text-xs-center">
                <div style="padding: 20px">
                    <h3
                        :class="definedClass"
                        v-html="text"
                    />
                </div>
                <v-progress-circular
                    :size="progressSize"
                    indeterminate
                    color="primary"
                />
            </div>
        </v-flex>
    </v-layout>
</template>

<script>
export default {
    name: 'CarregandoVuetify',
    props: {
        text: {
            type: String,
            default: 'aaaaaadadsadasdasdasdasdasd',
        },
        definedClass: {
            type: String,
            default: '',
        },
        size: {
            type: String,
            default: 'large',
        },
    },
    data() {
        return {
            sizeNum: {
                small: 20,
                medium: 35,
                large: 50,
            },
        };
    },
    computed: {
        progressSize() {
            return this.sizeNum[this.size];
        },
    },
};
</script>

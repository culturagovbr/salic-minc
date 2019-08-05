<template>
    <v-layout column>
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
            default: '',
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
    computed: {
        progressSize() {
            return this.sizeNum[this.size];
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
};
</script>

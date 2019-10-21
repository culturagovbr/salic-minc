<template>
    <v-card>
        <v-card-title>
            <div class="layout row ma-0 justify-space-between pb-1">
                <div class="subheading">
                    {{ title }}
                </div>

                <div class="grey--text"><span v-if="value">{{ value }}%</span> <v-icon
                    :color="color"
                    mini
                >{{ icon }}</v-icon></div>
            </div>
        </v-card-title>
        <v-card-text class="pt-2">
            <h3 class="headline">
                {{ subTitle }}
            </h3>
            <v-progress-linear
                :value="value"
                :color="color"
                :indeterminate="loading"
                height="5"
            />
            <span
                class="caption"
                v-html="caption"
            />
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    props: {
        icon: {
            type: String,
            default: '',
        },
        title: {
            type: String,
            default: '',
        },
        subTitle: {
            type: String,
            default: '',
        },
        headline: {
            type: String,
            default: '',
        },
        value: {
            type: Number,
            default: 0,
        },
        color: {
            type: String,
            default: '',
        },
        loading: {
            type: Boolean,
            default: false,
        },
    },
    computed: {
        caption() {
            return `${this.headline}`;
        },
    },
};
</script>

<style></style>

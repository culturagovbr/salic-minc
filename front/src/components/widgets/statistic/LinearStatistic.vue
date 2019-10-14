<template>
    <v-card>
        <v-card-title>
            <div class="layout row ma-0 justify-space-between pb-1">
                <div class="subheading">
                    {{ title }}
                </div>

                <div class="grey--text"><span v-if="value">{{ value }}%</span> <v-icon
                    mini
                    :color="color"
                >{{ icon }}</v-icon></div>
            </div>
        </v-card-title>
        <v-card-text class="pt-2">
            <h3 class="headline">
                {{ subTitle }}
            </h3>
            <v-progress-linear
                :value="value"
                height="5"
                :color="color"
                :indeterminate="loading"
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
        icon: String,
        title: String,
        subTitle: String,
        headline: String,
        value: Number,
        color: String,
        loading: Boolean,
    },
    computed: {
        caption() {
            return `${this.headline}`;
        },
    },
};
</script>

<style></style>

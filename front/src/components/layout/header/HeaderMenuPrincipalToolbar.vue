<template>
    <v-toolbar-items class="hidden-sm-and-down">
        <v-menu
            v-for="(item, index) in dadosMenu"
            :key="index"
            offset-y
        >
            <v-btn
                v-if="item.menu"
                slot="activator"
                flat
                dark
                small
                class="caption"
            >
                <span v-html="item.label" />
            </v-btn>
            <v-list
                v-if="item.menu"
                class="pa-0"
            >
                <v-list-tile
                    v-for="(sub, index) in item.menu"
                    :key="index"
                    :href="url(sub.url)"
                >
                    <v-list-tile-title>
                        <span v-html="sub.label" />
                    </v-list-tile-title>
                </v-list-tile>
            </v-list>
            <v-btn
                v-else
                :href="url(item.url)"
                flat
                dark
                small
                class="caption"
            >
                <span v-html="item.label" />
            </v-btn>
        </v-menu>
    </v-toolbar-items>
</template>

<script>
export default {
    name: 'HeaderMenuPrincipalToolbar',
    props: {
        dadosMenu: {
            type: [Array, Object],
            required: true,
        },
    },
    methods: {
        url(arrUrl) {
            let url = `/${arrUrl.module}/${arrUrl.controller}/${arrUrl.action}`;
            Object.keys(arrUrl).forEach((index) => {
                if (index !== 'module' && index !== 'controller' && index !== 'action') {
                    url += `/${index}/${arrUrl[index]}`;
                }
            });

            return url;
        },
    },
};
</script>

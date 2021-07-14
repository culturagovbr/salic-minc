<template>
    <v-navigation-drawer
        v-model="drawerRight"
        fixed
        temporary
        right
    >
        <v-list class="my-1">
            <template v-for="(item) in dadosMenu">
                <v-list-group
                    v-if="item.menu"
                    :key="item.index"
                    :prepend-icon="item.icon"
                >
                    <v-list-tile slot="activator">
                        <v-list-tile-title><span v-html="item.label" /></v-list-tile-title>
                    </v-list-tile>

                    <template v-for="(subMenu) in item.menu">
                        <v-list-group
                            v-if="subMenu.menu"
                            :key="subMenu.index"
                            no-action
                            sub-group
                        >
                            <v-list-tile slot="activator">
                                <v-list-tile-title>{{ subMenu.label }}</v-list-tile-title>
                            </v-list-tile>

                            <v-list-tile
                                v-for="(subitem, i) in subMenu.menu"
                                :key="i"
                            >
                                <v-list-tile-title v-text="subitem.label" />
                                <v-list-tile-action if="subitem.icon">
                                    <v-icon
                                        v-if="subitem.icon"
                                        v-text="subitem.icon"
                                    />
                                </v-list-tile-action>
                            </v-list-tile>
                        </v-list-group>
                        <v-list-tile
                            v-else
                            :key="subMenu.index"
                            :href="url(subMenu.url)"
                        >
                            <span v-html="subMenu.label" />
                        </v-list-tile>
                    </template>
                </v-list-group>
                <v-list-tile
                    v-else
                    :key="item.index"
                    :href="url(item.url)"
                >
                    <v-list-tile-action v-if="item.icon">
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-title><span v-html="item.label" /></v-list-tile-title>
                </v-list-tile>
                <v-divider :key="item.index" />
            </template>
        </v-list>
    </v-navigation-drawer>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'MenuPrincipal',
    props: {
        dadosMenu: {
            type: [Array, Object],
            required: true,
        },
    },
    data() {
        return {
            drawerRight: false,
        };
    },
    computed: {
        ...mapGetters({
            statusSidebarDireita: 'layout/getStatusSidebarDireita',
        }),
    },
    watch: {
        statusSidebarDireita(value) {
            this.drawerRight = value;
        },
        drawerRight(value) {
            this.atualizarStatusSidebar(value);
        },
    },
    methods: {
        ...mapActions({
            atualizarStatusSidebar: 'layout/atualizarStatusSidebarDireita',
        }),
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

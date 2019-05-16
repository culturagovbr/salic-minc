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
                        <v-list-tile-title><span v-html="item.label"/></v-list-tile-title>
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
                                :href="'/' +
                                    subitem.url.module + '/' +
                                    subitem.url.controller + '/' +
                                subitem.url.action"
                            >
                                <v-list-tile-title v-text="subitem.label"/>
                                <v-list-tile-action if="subitem.icon">
                                    <v-icon
                                        v-if="subitem.icon"
                                        v-text="subitem.icon"/>
                                </v-list-tile-action>
                            </v-list-tile>
                        </v-list-group>
                        <v-list-tile
                            v-else
                            :key="subMenu.index"
                            :href="'/' +
                                subMenu.url.module + '/' +
                                subMenu.url.controller + '/' +
                            subMenu.url.action">
                            <span v-html="subMenu.label"/>
                        </v-list-tile>
                    </template>
                </v-list-group>
                <v-list-tile
                    v-else
                    :key="item.index"
                    :href="'/' +
                        item.url.module + '/' +
                        item.url.controller + '/' +
                    item.url.action"
                >
                    <v-list-tile-action v-if="item.icon">
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-title><span v-html="item.label"/></v-list-tile-title>
                </v-list-tile>
                <v-divider :key="item.index"/>
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
    },
};
</script>

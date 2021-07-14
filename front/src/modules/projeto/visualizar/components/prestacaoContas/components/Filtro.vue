<template>
    <div>
        <v-flex>
            <v-combobox
                v-model="search"
                :label="label"
                :items="items"
                v-bind="$attrs"
                clearable
                chips
            >
                <template
                    slot="selection"
                    slot-scope="data"
                >
                    <v-chip
                        :selected="data.selected"
                        :disabled="data.disabled"
                        :key="JSON.stringify(data.item)"
                        class="v-chip--select-multi"
                        @input="data.parent.selectItem(data.item)"
                    >
                        <v-avatar
                            class="primary white--text"
                            v-text="data.item.slice(0, 1).toUpperCase()"
                        />
                        {{ data.item }}
                    </v-chip>
                </template>
            </v-combobox>
        </v-flex>
    </div>
</template>
<script>

export default {
    name: 'Filtro',
    props: {
        label: {
            type: String,
            default: '',
        },
        items: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            search: '',
        };
    },
    watch: {
        search(val) {
            this.$emit('eventoSearch', val);
        },
    },
};
</script>

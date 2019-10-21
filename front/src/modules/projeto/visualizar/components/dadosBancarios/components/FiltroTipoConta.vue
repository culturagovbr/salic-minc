<template>
    <v-combobox
        v-model="search"
        :items="items"
        label="Tipo da Conta"
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
</template>
<script>

export default {
    name: 'FiltroTipoConta',
    data() {
        return {
            search: '',
            items: [
                'Captação',
                'Movimentação',
            ],
        };
    },
    watch: {
        search(val) {
            this.$emit('eventoSearch', val);
        },
    },
};
</script>

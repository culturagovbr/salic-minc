<template>
    <v-card
        class="mb-2">
        <v-card-title
            primary
            class="title">Endereços</v-card-title>
        <v-data-table
            :headers="headers"
            :items="enderecos"
            class="elevation-1"
        >
            <template
                slot="items"
                slot-scope="props">
                <td>{{ props.item.tipoendereco }}</td>
                <td>{{ props.item.dstipologradouro }}</td>
                <td>{{ props.item.logradouro }}</td>
                <td class="text-xs-right">{{ props.item.numero }}</td>
                <td>{{ props.item.complemento }}</td>
                <td>{{ props.item.bairro }}</td>
                <td>{{ props.item.municipio }}</td>
                <td class="text-xs-center">{{ props.item.uf }}</td>
                <td>{{ props.item.cep | formatarCep }}</td>
            </template>
        </v-data-table>
    </v-card>
</template>
<script>
import { utils } from '@/mixins/utils';

export default {
    name: 'AgenteEndereco',
    mixins: [utils],
    props: {
        enderecos: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            headers: [
                { text: 'Tipo de Endereço', value: 'tipoendereco' },
                { text: 'Tipo do Logradouro', value: 'dstipologradouro' },
                { text: 'Logradouro', value: 'logradouro' },
                { text: 'Número', align: 'center', value: 'numero' },
                { text: 'Complemento', value: 'complemento' },
                { text: 'Bairro', value: 'bairro' },
                { text: 'Cidade', value: 'municipio' },
                { text: 'UF', align: 'center', value: 'uf' },
                { text: 'Cep', value: 'cep' },
            ],
        };
    },
};
</script>

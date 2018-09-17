<template>
    <div class="itens">
        <table class="bordered">
            <thead>
            <tr>
                <th class="center-align">#</th>
                <th class="left-align">Item</th>
                <th class="left-align">Unidade</th>
                <th class="center-align">Dias</th>
                <th class="center-align">Qtde</th>
                <th class="center-align">Ocor.</th>
                <th>Vl. Unit&aacute;rio</th>
                <th>Vl. Solicitado</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="row of table"
                :key="row.idPlanilhaProposta"
                v-if="isObject(row)"
                :class="definirClasseItem(row)">
                <td class="center-align">{{ row.Seq }}</td>
                <td class="left-align">{{ row.Item }}</td>
                <td class="center-align">{{ row.Unidade }}</td>
                <td class="center-align">{{ row.QtdeDias }}</td>
                <td class="center-align">{{ row.Quantidade }}</td>
                <td class="center-align">{{ row.Ocorrencia }}</td>
                <td class="right-align">{{ row.vlUnitario | filtroFormatarParaReal }}</td>
                <td>{{ row.vlSolicitado | filtroFormatarParaReal }}</td>
                <td>
                    <a v-if="row.JustProponente.length > 3"
                       class="tooltipped"
                       data-position="left"
                       data-delay="50"
                       v-bind:data-tooltip="row.JustProponente"
                    ><i class="material-icons tiny">message</i>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import planilhas from '@/mixins/planilhas';

    export default {
        mixins: [planilhas],
        props: {
            table: {},
        },
    };
</script>

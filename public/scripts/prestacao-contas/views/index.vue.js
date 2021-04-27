const Index = {
    template: `
        <div>
          <v-data-table
              v-model="selected"
              :headers="headers"
              :items="desserts"
              :pagination.sync="pagination"
              select-all
              item-key="name"
              class="elevation-1"
            >
              <template slot="headers" slot-scope="props">
                <tr>
                  <th
                    v-for="header in props.headers"
                    :key="header.text"
                    :class="['text-xs-center column sortable', pagination.descending ? 'desc' : 'asc', header.value === pagination.sortBy ? 'active' : '']"
                    @click="changeSort(header.value)"
                  >
                    <v-icon small>arrow_upward</v-icon>
                    {{ header.text }}
                  </th>
                </tr>
              </template>
              <template slot="items" slot-scope="props">
                <tr :active="props.selected" @click="props.selected = !props.selected">
                  <td class="text-xs-center">{{ props.item.pronac }}</td>
                  <td class="text-xs-center">{{ props.item.projeto }}</td>
                  <td class="text-xs-center">{{ props.item.dtRecebimento }}</td>
                  <td class="text-xs-center">
                    <v-btn icon class="mx-0" to="analisar/1">
                      <v-icon color="teal">edit</v-icon>
                    </v-btn>
                  </td>
                </tr>
              </template>
            </v-data-table>
        </div>
    `,
    mounted: function () {},
    data: function () {
        return {
            pagination: {
              sortBy: 'name'
            },
            selected: [],
            headers: [
              { text: 'PRONAC', value: 'pronac'},
              { text: 'Projeto', value: 'projeto' },
              { text: 'Dt. Recebimento', value: 'dtRecebimento' },
              { text: 'Analisar', value: 'analisar' },
            ],
            desserts: [
              {
                name: '123123',
                pronac: '123123',
                projeto: 'Projeto 123123',
                dtRecebimento: '12/12/2000',
                analisar: null,
              },
            ]
        }
    }
}

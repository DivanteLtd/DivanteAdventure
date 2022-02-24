<template>
  <v-data-table mobile-breakpoint="0"
                :items-per-page="5"
                :items="overtimes"
                :headers="headers"
                hide-default-footer>
    <template v-slot:item="{ item }">
      <tr >
        <td>{{ item.name }}</td>
        <td>{{ item.code }}</td>
        <td>{{ item.hours }}</td>
        <td>{{ item.percentage }}%</td>
        <td>{{ item.timeInfo }}</td>
      </tr>
    </template>
  </v-data-table>
</template>

<script>
  export default {
    name: 'EvidenceOvertimeTable',
    props: {
      overtimes: { type: Array, default: () => ([]) },
    },
    data() { return {
      headers: [
        {
          text: this.$t('Project name'),
          sortable: false,
          align: 'center',
        }, {
          text: this.$t('Project code'),
          sortable: false,
          align: 'center',
        }, {
          text: this.$t('Hours'),
          sortable: false,
          align: 'center',
        }, {
          text: this.$t('Rate'),
          sortable: false,
          align: 'center',
        }, {
          text: this.$t('Days with overtimes'),
          sortable: false,
          align: 'center',
        },
      ],
    };},
    i18n: { messages: {
      pl: {
        'Project code': 'Kod projektu',
        'Project name': 'Nazwa projektu',
        'Rate': 'Stawka',
        'Hours': 'Godziny',
        'Days with overtimes': 'Dni z dodatkowymi godzinami',
      },
    } },
  };
</script>
<style scoped>
  td {
    text-align: center;
  }
</style>

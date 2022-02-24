<template>
  <v-data-table mobile-breakpoint="0" :items-per-page="50"
                :items="items"
                :headers="headers"
                hide-default-footer>
    <template v-slot:item="{ item, index }">
      <evidence-overtime-row
        :item="item"
        :items-count="items.length"
        @add-row="() => addRow()"
        @remove-row="() => removeRow(index)"
        @update:item="item => reloadItem(item, index)"/>
    </template>
  </v-data-table>
</template>

<script>
  import EvidenceOvertimeRow from './EvidenceOvertimeRow';

  export default {
    name: 'EvidenceOvertimeEntries',
    components: { EvidenceOvertimeRow },
    props: {
      value: { type: Array, default: () => ([]) },
    },
    data() { return {
      items: this.value,
      headers: [
        {
          text: this.$t('Project'),
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
          width: 250,
        }, {
          sortable: false,
          width: '30px',
        },
      ],
    };},
    methods: {
      addRow() {
        this.$set(this.items, this.items.length, {});
        this.$emit('input', this.items);
      },
      removeRow(index) {
        this.items.splice(index, 1);
        this.$emit('input', this.items);
      },
      reloadItem(item, index) {
        this.$set(this.items, index, item);
        this.$emit('input', this.items);
      },
    },
    i18n: { messages: {
      pl: {
        'Project': 'Projekt',
        'Hours': 'Godziny',
        'Rate': 'Stawka',
        'Days with overtimes': 'Dni z dodatkowymi godzinami',
      },
    } },
  };
</script>

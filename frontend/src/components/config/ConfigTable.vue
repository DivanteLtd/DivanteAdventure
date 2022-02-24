<template>
  <v-data-table :loading="loading"
                :items-per-page="15"
                :items="translatedEntries"
                :headers="headers"
                item-key="key">
    <template v-slot:item="{ item }">
      <config-row :item="item"/>
    </template>
  </v-data-table>
</template>

<script>
  import ConfigRow from './ConfigRow';
  import { mapState } from 'vuex';

  export default {
    name: 'ConfigTable',
    components: { ConfigRow },
    props: {
      loading: { type: Boolean, default: false },
      content: { type: Boolean, default: false },
    },
    data() {
      return {
        headers: [
          {
            text: this.$t('Group'),
            value: 'group_i18n',
            align: 'center',
            sortable: true,
          },
          {
            text: this.$t('Key'),
            value: 'name_i18n',
            align: 'center',
            sortable: true,
          },
          {
            text: this.$t('Value'),
            value: 'value',
            align: 'center',
            sortable: true,
          },
          {
            text: this.$t('Last update time'),
            value: 'createdAt',
            align: 'center',
            sortable: true,
          },
          {
            text: this.$t('Last update author'),
            value: 'createdAt',
            align: 'center',
            sortable: false,
          },
          {
            text: this.$t('Actions'),
            value: 'key',
            align: 'center',
            sortable: false,
          },
        ],
      };
    },
    computed: {
      ...mapState({
        entries: state => state.Config.config,
        contentEntries: state => state.Config.contentConfig,
      }),
      translatedEntries() {
        return this.content ? this.contentEntries.map(this.mapEntry) : this.entries.map(this.mapEntry);
      },
    },
    methods: {
      mapEntry(item) {
        return {
          ...item,
          group_i18n: this.$t(`config.groups.${item.group}`),
          name_i18n: this.$t(`config.entries.${item.group}.${item.name}`),
        };
      },
    },
    i18n: {
      messages: {
        pl: {
          'Group': 'Grupa',
          'Key': 'Klucz',
          'Actions': 'Akcje',
          'Value': 'Wartość',
          'Last update time': 'Czas ostatniej zmiany',
          'Last update author': 'Autor ostatniej zmiany',
        },
      },
    },
  };
</script>

<template>
  <v-dialog id="config-history-dialog" v-model="dialogVisible" width="600" persistent>
    <v-card>
      <v-card-title class="title">
        <span>{{ $t('Configuration history') }}</span>
        <v-spacer/>
        <v-btn @click="dialogVisible = false" icon rounded><v-icon>clear</v-icon></v-btn>
      </v-card-title>
      <v-card-text>
        <v-list two-line>
          <config-history-entry v-for="(item, index) in sortedEntries"
                                :key="index"
                                :entry="item"
                                @restored="dialogVisible = false"/>
        </v-list>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  import ConfigHistoryEntry from './ConfigHistoryEntry';

  export default {
    name: 'ConfigHistoryDialog',
    components: { ConfigHistoryEntry },
    props: {
      value: { type: Array, required: true },
    },
    computed: {
      sortedEntries() {
        return this.value.sort((a, b) => b.createdAt - a.createdAt);
      },
      dialogVisible: {
        get() {
          return this.value.length > 0;
        },
        set(val) {
          this.$emit('input', val ? this.value : []);
        },
      },
    },
    i18n: {
      messages: {
        pl: {
          'Configuration history': 'Historia konfiguracji',
        },
      },
    },
  };
</script>

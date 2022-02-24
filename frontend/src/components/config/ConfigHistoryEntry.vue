<template>
  <v-list-item>
    <v-list-item-content>
      <v-list-item-title>{{ entry.value }}</v-list-item-title>
      <v-list-item-subtitle>{{ author }} - {{ time }}</v-list-item-subtitle>
    </v-list-item-content>
    <v-list-item-icon v-if="entry.replacedAt">
      <v-tooltip right>
        <template v-slot:activator="{ on }">
          <v-btn @click="restore" v-on="on" :loading="restoring" icon><v-icon>undo</v-icon></v-btn>
        </template>
        {{ $t('Restore') }}
      </v-tooltip>
    </v-list-item-icon>
  </v-list-item>
</template>

<script>
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'ConfigHistoryEntry',
    props: {
      entry: { type: Object, required: true },
    },
    data() {
      return {
        restoring: false,
      };
    },
    computed: {
      author() {
        return `${this.entry.responsible.name} ${this.entry.responsible.lastName}`;
      },
      time() {
        return moment.unix(this.entry.createdAt).format('D MMM YYYY HH:mm:ss');
      },
    },
    methods: {
      async restore() {
        this.restoring = true;
        const { key, value } = this.entry;
        await this.$store.dispatch('Config/updateEntry', { key, value });
        this.restoring = false;
        this.$emit('restored');
      },
    },
    i18n: {
      messages: {
        pl: {
          Restore: 'Przywróć',
        },
      },
    },
  };
</script>

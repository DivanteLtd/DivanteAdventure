<template>
  <v-list-item>
    <v-list-item-content>
      <v-list-item-title>
        <b>{{ assignment.category ? assignment.category : '' }}</b>
        {{ assignment.manufacturer ? assignment.manufacturer : '' }}
        (s/n <i>{{ assignment.serialNumber ? assignment.serialNumber : '' }}</i>)
      </v-list-item-title>
      <v-list-item-subtitle>
        {{ assignment.model ? assignment.model : '' }}
      </v-list-item-subtitle>
    </v-list-item-content>
    <v-tooltip left>
      <template v-slot:activator="{ on }">
        <v-icon v-on="on" medium>info</v-icon>
      </template>
      {{ getInfo() }}
    </v-tooltip>
  </v-list-item>
</template>

<script>
  import moment from '@divante-adventure/work-moment';

  export default {
    name: 'HardwareDataRow',
    props: {
      assignment: { type: Object, required: true },
    },
    data() {
      return {
        moment,
      };
    },
    methods: {
      getInfo() {
        if (this.assignment.agreementSigned === undefined) {
          return this.$t('no-data-info');
        } else if (this.assignment.agreementSigned === 'not signed') {
          return this.$t('no-signed-info');
        } else {
          return this.$t('data-info', { date: this.assignment.agreementSigned });
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'no-data-info': 'Umowa użyczenia sprzętu podpisana. Jeśli potrzebujesz ją przejrzeć, skontaktuj się z działem ADM.',
          'data-info': 'Umowa użyczenia sprzętu podpisana w dniu {date}. Znajdziesz ją na swojej skrzynce mailowej.',
          'no-signed-info': 'Umowa użyczenia sprzętu w trakcie podpisywania. Zostaniesz odpowiednio poinformowany.',
        },
        en: {
          'no-data-info': 'Hardware lending agreement signed. If you want to check agreement, contact with ADM department.',
          'data-info': 'Hardware lending agreement signed at {date}. You can find it on your mailbox.',
          'no-signed-info': 'Hardware lending agreement in progress. You will be informed accordingly',
        },
      },
    },
  };
</script>

<template>
  <v-menu offset-y>
    <template v-slot:activator="{ on }">
      <v-btn v-on="on" icon>
        <v-icon>more_vert</v-icon>
      </v-btn>
    </template>
    <v-list>
      <v-list-item v-for="(item, index) in moreMenu" :key="index" @click="item.clickAction">
        <v-list-item-title>{{ item.title }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
  import { eventNames, EventBus } from '../../../eventbus';

  export default {
    name: 'MarketingMoreMenu',
    computed: {
      moreMenu() {
        const menu = [];
        menu.push({
          title: this.$t('Consents'),
          clickAction: this.manageAgreements,
        });
        return menu;
      },
    },
    methods: {
      manageAgreements() {
        EventBus.$emit(eventNames.agreementForm, true);
      },
    },
    i18n: { messages: {
      pl: {
        Consents: 'Zgody',
      },
    },
    },
  };
</script>

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
    name: 'AgreementMoreMenu',
    computed: {
      moreMenu() {
        const menu = [];
        menu.push({
          title: this.$t('Consents'),
          clickAction: this.manageAgreements,
        });
        menu.push({
          title: this.$t('Attachments'),
          clickAction: this.manageAttachment,
        });
        return menu;
      },
    },
    methods: {
      manageAttachment() {
        EventBus.$emit(eventNames.attachmentForm);
      },
      manageAgreements() {
        EventBus.$emit(eventNames.agreementForm);
      },
    },
    i18n: { messages: {
      pl: {
        Attachments: 'Załączniki',
        Consents: 'Zgody',
      },
    },
    },
  };
</script>

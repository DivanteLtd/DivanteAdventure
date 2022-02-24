<template>
  <div>
    <v-menu v-if="moreMenu.length > 0" offset-y>
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" icon>
          <v-icon>more_vert</v-icon>
        </v-btn>
      </template>
      <v-list>
        <v-list-item v-for="(item, index) in moreMenu" :key="index" @click="item.clickAction">
          <v-list-item-title id="tribe-more-menu-button">{{ item.title }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
    <tribe-form :item="tribe" v-model="dialogVisible" v-if="dialogVisible"/>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import TribeForm from '../TribeForm';

  const SLACK_STATUS_DISABLED = 0;

  export default {
    name: 'TribeMoreMenu',
    components: { TribeForm },
    data() {
      return {
        dialogVisible: false,
        tribe: {},
      };
    },
    computed: {
      ...mapGetters({
        isTribeMaster: 'Authorization/isTribeMaster',
      }),
      moreMenu() {
        const menu = [];
        if (this.isTribeMaster) {
          menu.push({
            title: this.$t('Add'),
            clickAction: this.addTribe,
          });
        }
        return menu;
      },
    },
    methods: {
      addTribe() {
        this.tribe = {
          id: null,
          responsible: [],
          slackStatus: SLACK_STATUS_DISABLED,
          connectedToSlack: false,
          name: '',
          description: '',
          employeesCount: 0,
          url: '',
          photoUrl: '',
          hrEmail: '',
          isVirtual: false,
          sickLeaveProjectId: '',
          sickLeaveCategoryId: '',
          freeDayProjectId: '',
          freeDayCategoryId: '',
        };
        this.dialogVisible = true;
      },
    },
    i18n: { messages: {
      pl: {
        Add: 'Dodaj',
      },
    },
    },
  };
</script>

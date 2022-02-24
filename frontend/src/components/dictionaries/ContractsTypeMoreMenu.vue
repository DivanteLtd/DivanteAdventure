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
          <v-list-item-title id="project-more-menu-button">{{ item.title }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { EventBus, eventNames } from '../../eventbus';

  export default {
    name: 'ContractsTypeMoreMenu',
    computed: {
      ...mapGetters({
        isManagerRole: 'Authorization/isManager',
        isAdmin: 'Authorization/isSuperAdmin',
      }),
      moreMenu() {
        const menu = [];
        if (this.isAdmin) {
          menu.push({
            title: this.$t('Create new contract type'),
            clickAction: this.addContractType,
          });
        }
        return menu;
      },
    },
    methods: {
      addContractType() {
        EventBus.$emit(eventNames.addContractTYpe);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Create new contract type': 'Stwórz nowy typ kontraktu',
        },
      },
    },
  };
</script>

<style scoped>

</style>

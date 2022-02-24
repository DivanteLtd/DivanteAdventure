<template>
  <v-menu origin="center center" :nudge-bottom="10" transition="scale-transition" offset-y>
    <template v-slot:activator="{ on }">
      <v-btn class="ma-1" icon large text v-on="on">
        <v-avatar size="30px" v-if="employee.photo">
          <v-img :src="employee.photo" :alt="employee.name" @error="employee.photo = ''"/>
        </v-avatar>
        <v-icon v-else large>perm_identity</v-icon>
      </v-btn>
    </template>
    <v-list class="pa-0">
      <v-list-item ripple="ripple" rel="noopener" to="/logout">
        <v-list-item-action>
          <v-icon>exit_to_app</v-icon>
        </v-list-item-action>
        <v-list-item-content>
          <v-list-item-title>{{ $t('Logout') }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
  import { mapState } from 'vuex';

  export default {
    name: 'AppMenu',
    computed: {
      ...mapState({
        employee: state => state.Employees.loggedEmployee,
      }),
    },
    i18n: {
      messages: {
        pl: {
          Logout: 'Wyloguj się',
        },
      },
    },
  };
</script>

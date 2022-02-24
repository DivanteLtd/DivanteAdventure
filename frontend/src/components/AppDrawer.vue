<template>
  <v-navigation-drawer
    id="appDrawer"
    :mini-variant.sync="mini"
    fixed
    :dark="$vuetify.dark"
    app
    v-model="drawer"
    width="260"
  >
    <v-app-bar color="primary" class="d-flex align-center" style="color: #ffffff">
      <v-img :src="computeLogo" max-width="36" alt="Adventure" @click="openDashboard" class="app-bar__pointer"/>
      <v-toolbar-title @click="openDashboard" class="app-bar__pointer ml-0 pl-3 mt-1">
        <v-col class="ma-0 pa-0">Adventure</v-col>
        <v-col class="ma-0 pa-0 subtitle-2">- Open HRM</v-col>
      </v-toolbar-title>
    </v-app-bar>
    <vue-perfect-scrollbar class="drawer-menu--scroll" :settings="scrollSettings">
      <v-list dense expand>
        <v-row no-gutters class="toolbar-search" v-if="$vuetify.breakpoint.xs">
          <search-field class="pa-2 search-field-mobile"/>
        </v-row>
        <template v-for="group in menu">
          <!-- Group start -->
          <template v-if="hasAccess(group.roles || [], group.contracts || [])">
            <v-subheader :key="group.key">
              {{ $t(group.key) }}
            </v-subheader>
            <template v-for="item in (group.children || [])">
              <template v-if="hasAccess(item.roles || [], item.contracts || [])">
                <!-- if item doesn't have any children -->
                <template v-if="(item.children || []).length === 0">
                  <v-list-item :key="item.key" :to="item.url">
                    <v-list-item-action>
                      <v-icon>{{ item.icon }}</v-icon>
                    </v-list-item-action>
                    <v-list-item-title>
                      {{ $t(item.key) }}
                    </v-list-item-title>
                  </v-list-item>
                </template>
                <!-- if item has children -->
                <template v-else>
                  <v-list-group :prepend-icon="item.icon" class="pa-0" :key="item.key">
                    <template v-slot:activator>
                      <v-list-item-title>{{ $t(item.key) }}</v-list-item-title>
                    </template>
                    <v-list-item v-for="subitem in item.children" :to="subitem.url" :key="subitem.key">
                      <v-list-item-action/>
                      <v-list-item-title>
                        {{ $t(subitem.key) }}
                      </v-list-item-title>
                    </v-list-item>
                  </v-list-group>
                </template>
              </template>
            </template>
          </template>
          <!-- Group end -->
        </template>
      </v-list>
    </vue-perfect-scrollbar>
  </v-navigation-drawer>
</template>
<script>
  import menu from '../util/menu.json';
  import VuePerfectScrollbar from 'vue-perfect-scrollbar';
  import SearchField from '../components/globalSearch/SearchField';
  import { mapState } from 'vuex';

  export default {
    name: 'AppDrawer',
    components: {
      VuePerfectScrollbar,
      SearchField,
    },
    props: {
      expanded: {
        type: Boolean,
        default: true,
      },
    },
    data: () => ({
      mini: false,
      drawer: true,
      menu,
      scrollSettings: {
        maxScrollbarLength: 160,
      },
    }),
    computed: {
      ...mapState({
        loggedEmployee: state => state.Employees.loggedEmployee,
      }),
      computeGroupActive() {
        return true;
      },
      computeLogo() {
        return process.env.NODE_ENV === 'production' ? '/static/img/dvnt_logo.png' : '../../static/dvnt_logo.png';
      },
      sideToolbarColor() {
        return this.$vuetify.options.extra.sideNav;
      },
    },
    methods: {
      hasAccess(roles, contracts) {
        const employeeContract = (this.loggedEmployee || {}).contract || {};
        const contractName = employeeContract.name || '';
        let roleSafe = roles.length === 0;
        const contractSafe = contracts.length === 0 || contracts.includes(contractName);
        for(const role of roles) {
          if (this.$store.getters['Authorization/isAuthorized'](role)) {
            roleSafe = true;
          }
        }
        return roleSafe && contractSafe;
      },
      openDashboard() {
        if(this.$router.currentRoute.name !== 'Dashboard') {
          this.$router.push('/dashboard');
        }
      },
    },
    created() {
      window.getApp.$on('APP_DRAWER_TOGGLED', () => {
        this.drawer = (!this.drawer);
      });
    },
  };
</script>
<style scoped>
  .search-field-mobile{
    margin: 0 !important;
    height: 55px;
  }
  .app-bar__pointer{
    cursor: pointer;
  }
</style>
<style>
  .toolbar-search .v-input__slot{
    background: rgba(0,0,0,.16) !important;
  }
  .toolbar-search input{
    color: rgba(0,0,0,.87) !important;
  }
  .toolbar-search label{
    color: rgba(0,0,0,.87) !important;
  }
</style>

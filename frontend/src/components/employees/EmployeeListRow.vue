<template>
  <tr>
    <template v-if="loading">
      <td colspan="8">
        <v-progress-linear height="6" indeterminate/>
      </td>
    </template>
    <template v-else>
      <td class="pb-1 pt-1 pl-0 pr-0 ma-0 first-rows" @click="rowClicked">
        <v-avatar v-if="item.photo && !photoLoadError" class="ml-5">
          <img
            v-if="item.photo"
            :alt="item.name"
            :src="item.photo"
            @error="photoLoadError = true"
          />
        </v-avatar>
        <v-avatar v-else class="ml-5">
          <v-icon large>perm_identity</v-icon>
        </v-avatar>
      </td>
      <td class="centered mr-0 pr-0 first-rows" @click="rowClicked">
        <v-tooltip v-if="item.freeToday" class="ml-4" right>
          <template v-slot:activator="{ on }">
            <v-chip v-on="on" color="red" dark>
              <v-icon>work_off</v-icon>
            </v-chip>
          </template>
          {{ $t('Not available today') }}
        </v-tooltip>
        <v-tooltip v-if="location === TRIP && !item.freeToday" class="ml-4" right>
          <template v-slot:activator="{ on }">
            <v-chip v-on="on" color="rgba(141, 60, 2, 1)" dark>
              <v-icon>emoji_transportation</v-icon>
            </v-chip>
          </template>
          {{ $t('In trip') }}
        </v-tooltip>
        <v-tooltip v-if="location === REMOTELY && !item.freeToday" class="ml-4" right>
          <template v-slot:activator="{ on }">
            <v-chip v-on="on" color="rgba(231, 128, 54, 1)" dark>
              <v-icon>home</v-icon>
            </v-chip>
          </template>
          {{ $t('Work remotely') }}
        </v-tooltip>
      </td>
      <td class="centered" @click="rowClicked">
        {{ item.lastName }} {{ item.name }}
      </td>
      <td v-if="isSuperAdmin" class="centered" @click="rowClicked">
        <employee-list-contract-info :employee="item"/>
      </td>
      <td class="centered" @click="rowClicked">
        {{ (item.tribe || {}).name }}<br/>
        <i>{{ (item.level || {}).name }} {{ (item.position || {}).name }}</i>
      </td>
      <td class="centered" @click="rowClicked">
        {{ item.phone }}
      </td>
      <td class="centered" @click="rowClicked">
        {{ item.licencePlate }}
      </td>
      <td v-if="isTribeMaster" class="centered">
        <employee-list-actions :employee="item" @delete-start="loading = true" @delete-end="loading = false"/>
      </td>
    </template>
  </tr>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import { mapGetters } from 'vuex';
  import EmployeeListContractInfo from './EmployeeListContractInfo';
  import EmployeeListActions from './EmployeeListActions';

  const REMOTELY = 1;
  const TRIP = 2;

  export default {
    name: 'EmployeeListRow',
    components: { EmployeeListActions, EmployeeListContractInfo },
    props: {
      item: { type: Object, required: true },
      location: { type: Number, required: true },
    },
    data() { return {
      REMOTELY,
      TRIP,
      loading: false,
      photoLoadError: false,
    };},
    computed: {
      ...mapGetters({
        isTribeMaster: 'Authorization/isTribeMaster',
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
    },
    watch: {
      item() {
        this.photoLoadError = false;
      },
    },
    methods: {
      rowClicked() {
        EventBus.$emit(eventNames.showEmployeeWindow, this.item);
      },
    },
    mounted() {
      EventBus.$on(eventNames.deleteEmployeeAfter, () => {
        this.loading = false;
      });
    },
    i18n: { messages: {
      pl: {
        'Not available today': 'Dziś niedostępny',
        'Work remotely': 'Praca zdalna',
        'In trip': 'W podróży',
      },
    } },
  };
</script>

<style scoped>
  img.photo {
    text-align: center;
    mix-blend-mode: multiply;
  }
  td.centered {
    text-align: center;
    cursor: pointer;
  }
  td.first-rows {
    width: 5em;
  }
</style>

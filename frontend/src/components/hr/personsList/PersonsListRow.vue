<template>
  <tr>
    <template v-if="loading">
      <td colspan="10">
        <v-progress-linear height="6" indeterminate/>
      </td>
    </template>
    <template v-else>
      <td class="pa-1" @click="rowClicked">
        <v-avatar v-if="item.photo" class="ml-5">
          <img
            v-if="item.photo !== ''"
            :src="item.photo"
            :alt="item.name"
            :title="item.name"
            @error="item.photo = ''"
            class="photo"/>
        </v-avatar>
        <v-avatar v-else class="ml-5">
          <v-icon large>perm_identity</v-icon>
        </v-avatar>
      </td>
      <td>
        <v-tooltip v-if="item.freeToday" class="ml-4" right>
          <template v-slot:activator="{ on }">
            <v-chip v-on="on" color="red" dark outlined>
              <v-icon>work_off</v-icon>
            </v-chip>
          </template>
          {{ $t('Not available today') }}
        </v-tooltip>
      </td>
      <td class="centered" @click="rowClicked">
        {{ item.lastName }} {{ item.name }}
      </td>
      <td class="centered" @click="rowClicked">
        {{ item.dateOfBirth }}
      </td>
      <td class="centered" @click="rowClicked">
        {{ genderLabel }}
      </td>
      <td class="centered" @click="rowClicked">
        {{ item.email }}
      </td>
      <td class="centered" v-if="item.workMode" @click="rowClicked">
        {{ $t(getWorkMode(item.workMode)) }}
      </td>
      <td class="centered" v-else @click="rowClicked">
        <v-icon color="error">highlight_off</v-icon>
      </td>
      <td class="centered" @click="rowClicked">
        {{ (item.level || {}).name }} {{ (item.position || {}).name }}
      </td>
      <td class="centered" @click="rowClicked">
        <employee-list-contract-info :employee="item"/>
      </td>
      <td class="centered" @click="rowClicked">
        {{ hiredAt }}
      </td>
      <td class="centered" @click="rowClicked">
        {{ hiredTo }}
      </td>
      <td class="centered">
        <v-tooltip v-if="!item.hiredTo" right>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" @click="deleteUser = true" icon>
              <v-icon>delete</v-icon>
            </v-btn>
          </template>
          {{ $t('Delete') }}
        </v-tooltip>
      </td>
    </template>
    <end-cooperation-employee-editor v-model="deleteUser" v-if="deleteUser" :employee="item" :is-edit="false"/>
  </tr>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import moment from '@divante-adventure/work-moment';
  import EndCooperationEmployeeEditor from '../../../pages/hr/EndCooperationEmployeeEditor';
  import EmployeeListContractInfo from '../../employees/EmployeeListContractInfo';
  import { getWorkMode } from '../../../util/employee';

  export default {
    name: 'PersonsListRow',
    components: { EndCooperationEmployeeEditor, EmployeeListContractInfo },
    props: {
      item: { type: Object, required: true },
    },
    data() {
      return {
        getWorkMode,
        loading: false,
        deleteUser: false,
      };
    },
    computed: {
      hiredAt() {
        if (this.item.hiredAt) {
          return moment(this.item.hiredAt).format('D MMM YYYY');
        }
        return '';
      },
      hiredTo() {
        if (this.item.hiredTo) {
          return moment(this.item.hiredTo).format('D MMM YYYY');
        }
        return '';
      },
      genderLabel() {
        if (this.item.gender === 0) {
          return this.$t('Man');
        } else if (this.item.gender === 1) {
          return this.$t('Woman');
        } else {
          return '';
        }
      },
    },
    methods: {
      rowClicked() {
        EventBus.$emit(eventNames.showEmployeeWindow, this.item);
      },
    },
    mounted() {

    },
    i18n: { messages: {
      pl: {
        'None': 'Brak',
        'Man': 'Mężczyzna',
        'Woman': 'Kobieta',
        'Not available today': 'Dziś niedostępny',
        'Delete': 'Usuń',
        'Work from office': 'Praca z biura',
        'Work remotely': 'Praca zdalna',
        'Work partial remotely': 'Praca cześciowo zdalna',
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
</style>

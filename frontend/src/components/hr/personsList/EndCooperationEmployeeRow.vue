<template>
  <tr>
    <template>
      <td>
        {{ item.name }}
      </td>
      <td>
        {{ item.lastName }}
      </td>
      <td>
        {{ item.position }}
      </td>
      <td>
        {{ $t(item.whoEndedCooperation) }}
      </td>
      <td>
        {{ item.nextCompany }}
      </td>
      <td v-if="item.exitInterview">
        <v-icon color="success">check_circle_outline</v-icon>
      </td>
      <td v-else>
        <v-icon color="error">highlight_off</v-icon>
      </td>
      <td v-if="item.checklist">
        <v-icon color="success">check_circle_outline</v-icon>
      </td>
      <td v-else >
        <v-icon color="error">highlight_off</v-icon>
      </td>
      <td>
        {{ item.comment }}
      </td>
      <td>
        {{ item.dismissDate }}
      </td>
      <td>
        <v-menu offset-y>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" icon>
              <v-icon>more_vert</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item @click="editUser = true">
              <v-list-item-title>{{ $t('Edit') }}</v-list-item-title>
            </v-list-item>
            <v-list-item @click="deletePerson">
              <v-list-item-title>{{ $t('Delete') }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </td>
    </template>
    <end-cooperation-employee-editor v-if="editUser" v-model="editUser" :employee="item" :is-edit="true"/>
  </tr>
</template>

<script>
  import EndCooperationEmployeeEditor from '../../../pages/hr/EndCooperationEmployeeEditor';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'EndCooperationEmployeeRow',
    components: { EndCooperationEmployeeEditor },
    props: {
      item: { type: Object, required: true },
    },
    data() { return {
      editUser: false,
    };},
    methods: {
      async deletePerson() {
        await this.$store.dispatch('Hr/deleteEmployeeToDismiss', this.item.id);
        EventBus.$emit(eventNames.hrPersonListReload);
      },
    },
    i18n: {
      messages: {
        pl: {
          Edit: 'Edytuj',
          Delete: 'Usuń',
        },
      },
    },
  };
</script>
<style scoped>
  td{
    text-align: center;
  }
</style>

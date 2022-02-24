<template>
  <v-chip :disabled="employee.name === undefined" @click.stop="employeeClicked"
          class="pointer" :class="{'ma-0': $vuetify.breakpoint.xs}" pill>
    <v-avatar v-if="employee.photo !== '' && employee.photo"
              class="pointer" left>
      <img :src="employee.photo"
           @error="employee.photo = ''"/>
    </v-avatar>
    <v-btn v-else
           class="ma-0 mr-1"
           icon small>
      <v-icon>perm_identity</v-icon>
    </v-btn>
    <span class="pointer">
      <span v-if="!employee.lastName">
        {{ employee.name }}
      </span>
      <span v-else-if="lastNameFirst">
        {{ employee.lastName || employee.lastName }} {{ employee.name }}
      </span>
      <span v-else>
        {{ employee.name }} {{ employee.lastName || employee.lastName }}
      </span>
    </span>
  </v-chip>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';

  export default {

    name: 'EmployeeChip',
    props: {
      employee: { type: Object, required: true },
      lastNameFirst: { type: Boolean, default: false },
    },
    methods: {
      employeeClicked() {
        EventBus.$emit(eventNames.showEmployeeWindow, this.employee);
      },
    },
  };
</script>
<style scoped>
  .pointer {
    cursor: pointer !important;
  }
</style>

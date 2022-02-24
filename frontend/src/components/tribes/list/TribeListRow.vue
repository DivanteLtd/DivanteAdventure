<template>
  <tr>
    <td class="centered" @click="rowClicked">
      <v-avatar class="tribe__avatar" v-if="tribe.photoUrl !== ''" tile>
        <img
          :src="tribe.photoUrl"
          :alt="tribe.name"
          :title="tribe.name"
          @error="tribe.photoUrl = ''"/>
      </v-avatar>
      <span v-else>{{ tribe.name }}</span>
    </td>
    <td class="centered" @click="rowClicked">
      {{ tribe.url }}
    </td>
    <td @click="rowClicked">
      <div class="d-flex justify-center align-center" >
        <span>{{ tribe.employeesCount }} </span>
        <v-icon>perm_identity</v-icon>
      </div>
    </td>
  </tr>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'TribeListRow',
    props: {
      tribe: { type: Object, required: true },
    },
    methods: {
      rowClicked() {
        EventBus.$emit(eventNames.showTribeWindow, this.tribe);
      },
    },
  };
</script>

<style scoped>
  img.logo {
    height: 36px;
    max-width: 150px;
    text-align: center;
    mix-blend-mode: multiply;
  }
  td.centered {
    text-align: center;
    cursor: pointer;
  }
  span.next-to-icon {
    position: relative;
    bottom: 4px;
  }
  .tribe__avatar{
    max-width: 36px !important;
    max-height: 36px;
  }
  td{
    min-width: 150px;
  }
</style>

<template>
  <tr>
    <td class="centered">
      {{ iSO.lastName + ' ' + iSO.name }}
    </td>
    <td class="centered">
      {{ iSO.email }}
    </td>
    <td v-for="(agreement, key) in agreements.filter(val => val.type === agreementsType.TYPE_ISO)"
        class="centered" :key="key">
      <span v-if="isAgree(agreement)">
        <v-icon color="success">check_circle_outline</v-icon>
      </span>
      <span v-else>
        <v-icon color="error">highlight_off</v-icon>
      </span>
    </td>
  </tr>
</template>

<script>
  import { mapState } from 'vuex';
  import { agreementsType } from '../../../util/agreements';

  export default {
    name: 'ISOAcceptationListRow',
    props: {
      iSO: { type: Object, required: true },
    },
    data() {
      return {
        agreementsType,
      };
    },
    computed: {
      ...mapState({
        agreements: state => state.Agreements.agreements,
      }),
    },
    methods: {
      isAgree(agreement) {
        return this.iSO.description.filter(val => (Number(val) === agreement.id)).length > 0;
      },
    },
  };
</script>
<style scoped>
  td.centered {
    text-align: center;
    cursor: pointer;
  }
</style>

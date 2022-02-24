<template>
  <tr>
    <td class="centered">
      {{ gDPR.lastName + ' ' + gDPR.name }}
    </td>
    <td class="centered">
      {{ gDPR.email }}
    </td>
    <td v-for="(agreement, key) in agreements.filter(val => val.type === agreementsType.TYPE_GDPR)"
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
  import { agreementsType } from '../../util/agreements';

  export default {
    name: 'GDPRAcceptationListRow',
    props: {
      gDPR: { type: Object, required: true },
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
        return this.gDPR.description.filter(val => (Number(val) === agreement.id)).length > 0;
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

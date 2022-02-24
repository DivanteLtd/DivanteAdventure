<template>
  <tr>
    <td class="centered">
      {{ marketing.lastName + ' ' + marketing.name }}
    </td>
    <td class="centered">
      {{ marketing.email }}
    </td>
    <td v-for="(consent, key) in marketingConsents" class="centered" :key="key">
      <span v-if="isAgree(consent)">
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

  export default {
    name: 'GDPRAcceptationListRow',
    props: {
      marketing: { type: Object, required: true },
    },
    computed: {
      ...mapState({
        marketingConsents: state => state.Agreements.marketingConsents,
      }),
    },
    methods: {
      isAgree(consent) {
        return this.marketing.description.filter(val => val === consent.id).length > 0;
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

<template>
  <v-app id="page-500">
    <v-container fluid fill-height>
      <v-row no-gutters align-center justify-center >
        <div class="mr-3 hidden-sm-and-down">
          <v-img src="/static/error/500.svg" alt=""/>
        </div>
        <div class="text-md-center">
          <h1>500</h1>
          <h2 class="my-3 headline ">{{ $t('Server error.') }}</h2>
          <div>
            <v-btn color="primary" @click="goHome">{{ $t('Go home') }}</v-btn>
          </div>
        </div>
      </v-row>
    </v-container>
  </v-app>
</template>

<script>
  export default {
    methods: {
      goHome() {
        this.$router.push({ path: '/' });
      },
    },
    i18n: { messages: {
      pl: {
        'Server error.': 'Błąd serwera.',
        'Go home': 'Wróć na stronę główną',
      },
    } },
  };
</script>
<style scoped lang="css">
  h1 {
    font-size: 150px;
    line-height: 150px;
    font-weight: 700;
    color: #252932;
    text-shadow: rgba(61, 61, 61, 0.3) 1px 1px, rgba(61, 61, 61, 0.2) 2px 2px, rgba(61, 61, 61, 0.3) 3px 3px;
  }
</style>

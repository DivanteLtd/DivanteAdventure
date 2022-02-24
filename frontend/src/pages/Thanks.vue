<template>
  <v-app id="page-thanks" class="primary">
    <v-content>
      <v-container fluid fill-height>
        <v-row no-gutters class="d-flex justify-center align-center">
          <v-col cols="12" sm="8" md="4" lg="4">
            <v-card class="elevation-1 pa-4">
              <v-card-text>
                <div class="layout column align-center">
                  <v-img src="/static/img/dvnt_logo_black.png" alt="Adventure" width="120" height="120"/>
                  <h1 class="flex my-4 primary--text text-center">{{ $t('Thank you for filling in the form') }}</h1>
                </div>
              </v-card-text>
              <v-card-actions>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
  export default {
    name: 'Thanks',
    i18n: { messages: {
      pl: {
        'Thank you for filling in the form': 'Dziękuję za wypełnienie formularza',
      },
    } },
  };
</script>

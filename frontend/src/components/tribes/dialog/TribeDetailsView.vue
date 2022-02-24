<template>
  <v-container>
    <v-row no-gutters wrap>
      <v-col v-if="tribe.photoUrl !== ''" cols="12" sm="6" md="5">
        <img :src="tribe.photoUrl"
             :alt="tribe.name"
             :title="tribe.name"
             @error="tribe.photoUrl = ''"
             class="logo"/>
      </v-col>
      <v-col cols="12" sm="6" md="7">
        <v-list two-line>
          <v-list-item>
            <v-list-item-action>
              <v-icon>people</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>
                {{ tribe.name }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ tribe.description }}
              </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <v-list-item v-if="tribe.url !== ''" :href="tribe.url" target="_blank">
            <v-list-item-action>
              <v-icon>link</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>
                {{ tribe.url }}
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  export default {
    name: 'TribeDetailsView',
    props: {
      tribe: { type: Object, required: true },
    },
  };
</script>

<style scoped>
  img.logo {
    max-width: 100%;
    max-height: 300px;
  }
</style>

<template>
  <v-container grid-list-xl fluid>
    <v-card>
      <v-app-bar class="elevation-0" flat color="transparent" :height="checkIfMobile">
        <v-row no-gutters wrap class="align-center">
          <v-col cols="12" sm="5" md="3">
            <v-toolbar-title>
              <h5> {{ title }} </h5>
            </v-toolbar-title>
          </v-col>
          <v-spacer />
          <v-col v-if="!!this.$slots.options" cols="12" sm="7" md="6">
            <slot name="options"/>
          </v-col>
        </v-row>
      </v-app-bar>
      <v-divider/>
      <v-card-text class="pa-0">
        <slot/>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
  export default {
    name: 'PageCard',
    props: {
      title: { type: String, default: '' },
    },
    computed: {
      checkIfMobile() {
        return this.$vuetify.breakpoint.xs && this.$slots.options ? 100 : 55;
      },
    },
  };
</script>

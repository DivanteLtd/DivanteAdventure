<template>
  <v-expansion-panels multiple>
    <v-expansion-panel>
      <v-expansion-panel-header>
        <v-toolbar-title>
          <h5>{{ title }}</h5>
        </v-toolbar-title>
      </v-expansion-panel-header>
      <v-expansion-panel-content>
        <v-card flat>
          <v-card-text class="pa-0">
            <v-data-table mobile-breakpoint="0"
                          disable-pagination
                          :items="agreements"
                          :class="{'pa-3': $vuetify.breakpoint.smAndUp}"
                          hide-default-header
                          hide-default-footer>
              <template v-slot:item="{ item }">
                <agreement-form-row :agreement="item" @reload="reloadAgreements"/>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-expansion-panel-content>
    </v-expansion-panel>
  </v-expansion-panels>
</template>

<script>
  import AgreementFormRow from './AgreementFormRow';

  export default {
    name: 'AgreementFormCard',
    components: { AgreementFormRow },
    props: {
      agreements: { type: Array, required: true },
      title: { type: String, required: true },
    },
    methods: {
      reloadAgreements() {
        this.$store.dispatch('Agreements/loadAgreements');
        this.$store.dispatch('Agreements/loadMarketingConsents');
      },
    },
  };
</script>

<template>
  <v-expansion-panels v-model="expandContent" multiple>
    <v-expansion-panel>
      <v-expansion-panel-header>
        <v-toolbar-title>
          <h5>{{ title }}</h5>
        </v-toolbar-title>
      </v-expansion-panel-header>
      <v-expansion-panel-content>
        <v-card flat>
          <v-col cols="12" v-if="agreements.length" row wrap :class="{'mb-1': $vuetify.breakpoint.smAndUp}">
            <agreement-info-top :agreement-type="agreements[0].type"/>
          </v-col>
          <v-card-text class="pa-0">
            <v-data-table mobile-breakpoint="0"
                          v-if="employee.contract"
                          disable-pagination
                          :headers="headers"
                          :items="agreements"
                          v-model="selected"
                          :class="{'pa-4': $vuetify.breakpoint.smAndUp}"
                          :no-data-text="loading ? $t('Loading data...') : $t('No data available')"
                          :loading="loading"
                          show-select hide-default-footer>
              <template v-slot:item="{ item, isSelected, select }">
                <agreement-row :agreement="item" v-model="isSelected" @select="select"/>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-expansion-panel-content>
    </v-expansion-panel>
  </v-expansion-panels>
</template>

<script>
  import AgreementRow from './AgreementRow';
  import AgreementInfoTop from './AgreementInfoTop';

  export default {
    name: 'AgreementCard',
    components: { AgreementRow, AgreementInfoTop },
    props: {
      employee: { type: Object, required: true },
      agreements: { type: Array, required: true },
      value: { type: Array, default: () => ([]) },
      title: { type: String, required: true },
      loading: { type: Boolean, default: false },
    },
    data() {
      return {
        headers: [{
          text: this.$t('Select all consents'),
          align: 'left',
          sortable: false,
        }, {
          text: this.$t('Attachment'),
          align: 'center',
          value: 'tags',
          sortable: false,
        }],
      };
    },
    computed: {
      selected: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
      expandContent: {
        get() {
          return [this.agreements.some(element => element.accepted === 0) ? 0 : ''];
        },
        set() {},
      },
    },
    i18n: {
      messages: {
        pl: {
          'Attachment': 'Załącznik',
          'Select all consents': 'Zaznacz wszystkie zgody',
          'No data available': 'Brak danych',
          'Loading data...': 'Wczytywanie...',
        },
      },
    },
  };
</script>

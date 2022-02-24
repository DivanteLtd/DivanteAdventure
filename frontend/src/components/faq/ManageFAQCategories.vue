<template>
  <v-dialog v-model="modelWrapper" width="1000">
    <v-card id="agreement-form">
      <v-row no-gutters wrap class="justify-center">
        <v-col cols="12">
          <v-card-title class="headline">
            <span>{{ $t('Manage FAQ Categories') }}</span>
          </v-card-title>
        </v-col>
        <v-col cols="12" sm="6" md="4" class="pa-4">
          <v-btn block color="primary" @click="addCategory">
            {{ $t('Add category') }}
          </v-btn>
        </v-col>
      </v-row>
      <v-progress-linear height="6" v-if="loading" indeterminate/>
      <f-a-q-form-table v-else/>
      <v-card-actions>
        <v-spacer/>
        <v-btn color="red" text @click="modelWrapper = false">
          {{ $t('Cancel') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import FAQFormTable from './FAQFormTable';

  export default {
    name: 'ManageFAQCategories',
    components: { FAQFormTable },
    props: {
      value: { type: Boolean, required: true },
    },
    data() {
      return {
        loading: false,
      };
    },
    computed: {
      modelWrapper: {
        get() {
          return this.value;
        },
        set(val) {
          this.$emit('input', val);
        },
      },
    },
    methods: {
      addCategory() {
        return EventBus.$emit(eventNames.fAQCategoryForm);
      },
    },
    async mounted() {
      this.modelWrapper = true;
      this.loading = true;
      await this.$store.dispatch('FAQ/loadFAQCategories');
      this.loading = false;
      await this.$store.dispatch('Employees/loadEmployees');
    },
    i18n: {
      messages: {
        pl: {
          'Manage FAQ Categories': 'Zarządzaj kategoriami FAQ',
          'Add category': 'Dodaj kategorię',
          'Cancel': 'Anuluj',
        },
      },
    },
  };
</script>

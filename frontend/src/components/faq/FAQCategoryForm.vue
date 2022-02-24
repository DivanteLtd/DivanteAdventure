<template>
  <v-dialog id="dialog-faq-category" v-model="dialogVisible" v-if="dialogVisible" width="600">
    <v-card>
      <v-app-bar color="transparent" class="headline" flat >
        <v-row no-gutters :class="{'potential-employee-title': $vuetify.breakpoint.xs}" >
          {{ category.id ? $t('Edit FAQ category') : $t('New FAQ category') }}
        </v-row>
        <v-spacer/>
        <v-btn icon @click="dialogVisible = false"><v-icon>close</v-icon></v-btn>
      </v-app-bar>
      <v-divider/>
      <f-a-q-category-form-content :category="category" @close="close"/>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import FAQCategoryFormContent from './FAQCategoryFormContent';

  export default {
    name: 'FAQCategoryForm',
    components: { FAQCategoryFormContent },
    data() {
      return {
        dialogVisible: false,
        category: {},
      };
    },
    methods: {
      show(category) {
        if (typeof category !== 'undefined') {
          this.category = category;
        } else {
          this.category = {};
        }
        this.dialogVisible = true;
      },
      close() {
        this.dialogVisible = false;
      },
    },
    mounted() {
      EventBus.$on(eventNames.fAQCategoryForm, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'New FAQ category': 'Nowa kategoria FAQ',
          'Edit FAQ category': 'Edytuj kategorię FAQ',
        },
      },
    },
  };
</script>

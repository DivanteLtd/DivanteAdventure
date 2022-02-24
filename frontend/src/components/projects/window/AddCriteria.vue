<template>
  <v-dialog v-model="dialogVisible" width="800" persistent>
    <v-card>
      <v-card-title class="headline">
        <span>{{ editFlag ? $t('Edit criteria') : $t('Add criteria') }}</span>
      </v-card-title>
      <v-divider/>
      <v-card-text class="pt-0 pb-0">
        <v-row no-gutters wrap>
          <v-col class="pa-4">
            <v-text-field v-model="namePl"
                          :rules="rules.required"
                          :label="$t('Criteria in Polish')"
                          required
                          class="required"/>
            <v-text-field v-model="nameEn"
                          :rules="rules.required"
                          :label="$t('Criteria in English')"
                          required
                          class="required"/>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn :disabled="!(namePl && nameEn)"
               color="blue" @click="editFlag ? editCriteria() : addCriteria()" text>
          {{ $t('Save') }}
        </v-btn>
        <v-btn text @click="dialogVisible = false">
          {{ $t('Close') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'AddCriteria',
    data() {
      return {
        dialogVisible: false,
        editFlag: false,
        namePl: '',
        nameEn: '',
        id: 0,
        rules: {
          required: [
            v => !!v || v === 0 || this.$t('This field is required'),
          ],
        },
      };
    },
    methods: {
      async addCriteria() {
        const newCriteria = {
          namePl: this.namePl,
          nameEn: this.nameEn,
        };
        try {
          await this.$store.dispatch('Criteria/addCriteria', newCriteria)
            .then(() => this.$store.dispatch('Criteria/loadCriteria')
              .then(() => this.$store.commit('showSnackbar', {
                text: this.$t('Criteria have been saved'),
                color: 'success',
              })));
          this.dialogVisible = false;
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Criteria with given name already exist'),
            color: 'error',
          });
        }
      },
      async editCriteria() {
        const editedCriteria = {
          namePl: this.namePl,
          nameEn: this.nameEn,
          id: this.id,
        };
        try {
          await this.$store.dispatch('Criteria/editCriteria', editedCriteria)
            .then(() => this.$store.dispatch('Criteria/loadCriteria')
              .then(() => this.$store.commit('showSnackbar', {
                text: this.$t('Criteria have been edited'),
                color: 'success',
              })));
          this.dialogVisible = false;
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Criteria with given name already exist'),
            color: 'error',
          });
        }
      },
      show(criteria) {
        this.editFlag = false;
        if (criteria) {
          this.editFlag = true;
          this.id = criteria.id;
          this.nameEn = criteria.nameEn;
          this.namePl = criteria.namePl;
        } else {
          this.nameEn = '';
          this.namePl = '';
        }
        this.dialogVisible = true;
      },
    },
    mounted() {
      EventBus.$on(eventNames.addCriteria, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Save': 'Zapisz',
          'Close': 'Zamknij',
          'Criteria in Polish': 'Kryteria po polsku',
          'Criteria in English': 'Kryteria po angielsku',
          'Criteria have been saved': 'Kryteria zostały zapisane',
          'Add criteria': 'Dodaj kryteria',
          'Edit criteria': 'Edytuj kryteria',
          'This field is required': 'To pole jest wymagane',
          'Criteria have been edited': 'Kryteria zostały zmienione',
          'Criteria with given name already exist': 'Kryteria o tej nazwie już istnieją',
        },
      },
    },
  };
</script>
<style scoped>
  .required >>> label::after {
    content: "*";
  }
</style>

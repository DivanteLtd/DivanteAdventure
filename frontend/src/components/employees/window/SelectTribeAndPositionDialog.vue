<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <v-card>
      <v-card-title>
        <span class="headline">{{ $t('Assign tribe and position') }}</span>
      </v-card-title>
      <v-card-text>
        <v-autocomplete :no-data-text="$t('No tribes available')"
                        v-model="tribe"
                        :label="$t('Tribe')"
                        :items="tribes"
                        item-text="name"
                        prepend-icon="supervisor_account"
                        return-object/>
        <v-autocomplete v-if="positions.length > 0"
                        v-model="position"
                        :label="$t('Position')"
                        :no-data-text="$t('No positions available')"
                        :items="positions"
                        prepend-icon="work"
                        item-text="name"
                        return-object/>
        <v-autocomplete :no-data-text="$t('No levels available')"
                        v-if="levels.length > 0"
                        :label="$t('Level')"
                        :items="levels"
                        prepend-icon="reorder"
                        item-text="name"
                        v-model="level"
                        return-object/>
      </v-card-text>
      <v-card-actions>
        <v-spacer/>
        <v-btn color="blue darken-1"
               :disabled="formInvalid"
               :loading="loading"
               @click="assign"
               text>
          {{ $t('Save') }}
        </v-btn>
        <v-btn text @click="dialog = false">{{ $t('Close') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { mapState } from 'vuex';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'SelectTribeAndPositionDialog',
    data() {
      return {
        dialog: false,
        tribe: null,
        position: null,
        level: null,
        loading: false,
        employee: {
          id: -1,
        },
      };
    },
    computed: {
      ...mapState({
        tribes: state => state.Tribes.tribes,
      }),
      positions() {
        const tribe = this.tribe || {};
        return tribe.positions || [];
      },
      levels() {
        const position = this.position || {};
        const strategy = position.strategy || {};
        return strategy.levels || [];
      },
      formInvalid() {
        return (this.tribe === null)
          || (this.position === null && this.positions.length > 0)
          || (this.level === null && this.levels.length > 0);
      },
    },
    watch: {
      tribe() {
        this.position = null;
        this.level = null;
      },
      position() {
        this.level = null;
      },
    },
    methods: {
      show(employee) {
        if (this.dialog) {
          return;
        }
        this.employee = employee;
        this.tribe = null;
        this.loading = false;
        this.dialog = true;
      },
      async assign() {
        this.loading = true;
        try {
          await this.$store.dispatch('Employees/assignToTribe', {
            idEmployee: this.employee.id,
            idTribe: this.tribe.id,
          });
          if (this.position !== null) {
            const positionAssignData = {
              id: this.employee.id,
              position: {
                id: this.position.id,
              },
            };
            if (this.level !== null) {
              positionAssignData.level = {
                id: this.level.id,
              };
            }
            await this.$store.dispatch('Employees/saveEmployee', positionAssignData);
          }
          EventBus.$emit(eventNames.employeeEdited);
          this.dialog = false;
        } catch (e) {
          this.loading = false;
        }
      },
    },
    mounted() {
      EventBus.$on(eventNames.showTribeAssignToEmployeeWindow, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Assign tribe and position': 'Przypisz praktykę i stanowisko',
          'No tribes available': 'Brak dostępnych praktyk',
          'Tribe': 'Praktyka',
          'No positions available': 'Brak dostępnych stanowisk',
          'Position': 'Stanowisko',
          'No levels available': 'Brak dostępnych poziomów',
          'Level': 'Poziom',
          'Save': 'Zapisz',
          'Close': 'Zamknij',
        },
        en: {
          'Tribe': 'Practice',
          'No tribes available': 'No practice available',
        },
      },
    },
  };
</script>

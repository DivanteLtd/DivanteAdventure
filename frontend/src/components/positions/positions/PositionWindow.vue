<template>
  <v-dialog v-model="dialogVisible" width="1000">
    <v-card id="dialog-position-form">
      <v-app-bar class="headline" color="transparent" flat>
        <span :class="{'title-mobile': $vuetify.breakpoint.xs}">
          {{ editing ? $t('Update position') : $t('Create new position') }}
        </span>
        <v-spacer/>
        <v-btn @click="dialogVisible = false" icon><v-icon>clear</v-icon></v-btn>
      </v-app-bar>
      <v-divider/>
      <v-card-text>
        <v-text-field v-model="position.name" :label="$t('Name')"/>
        <v-alert type="info" :value="position.employeeCount > 0" class="mb-3">
          {{ $t('strategy-locked-info') }}
        </v-alert>
        <v-autocomplete :disabled="position.employeeCount > 0"
                        v-model="position.strategyId"
                        :items="strategies"
                        :label="$t('Leveling structure')"
                        item-value="id"
                        :item-text="createText"
                        append-outer-icon="add"
                        @click:append-outer="createNewStrategy">
        </v-autocomplete>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="red" text @click="dialogVisible = false">
          {{ $t('Cancel') }}
        </v-btn>
        <v-btn color="blue" text @click="save()" :disabled="!formValid" :loading="loading">
          {{ editing ? $t('Save') : $t('Create') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import { mapState } from 'vuex';

  export default {
    name: 'PositionWindow',
    data() {
      return {
        dialogVisible: false,
        editing: false,
        loading: false,
        position: {
          id: -1,
          name: '',
          strategyId: '',
          employeeCount: 0,
        },
      };
    },
    computed: {
      ...mapState({
        strategies: state => state.Positions.strategies,
      }),
      formValid() {
        const correctName = this.position.name.trim().length > 0;
        const strategyId = this.position.strategyId;
        const correctStrategy = (typeof strategyId === 'string' && strategyId.trim().length > 0)
          || (typeof strategyId === 'number' && strategyId > 0);
        return correctName && correctStrategy;
      },
    },
    methods: {
      createNewStrategy() {
        EventBus.$emit(eventNames.createStrategy);
      },
      createText(item) {
        const name = item.name;
        const levels = item.levels.map(level => level.name).join(', ');
        return `${name} (${levels})`;
      },
      show(data) {
        if (this.dialogVisible) {
          return;
        }
        if (typeof data === 'undefined') {
          this.position.id = -1;
          this.position.name = '';
          this.position.strategyId = '';
          this.position.employeeCount = 0;
          this.editing = false;
        } else {
          this.position.id = data.id;
          this.position.name = data.name;
          this.position.strategyId = data.strategy.id;
          this.position.employeeCount = data.employeeCount;
          this.editing = true;
        }
        this.dialogVisible = true;
      },
      async save() {
        this.loading = true;
        const name = this.position.name;
        const strategyId = this.position.strategyId;
        if (this.editing) {
          const id = this.position.id;
          await this.$store.dispatch('Positions/updatePosition', { id, name, strategyId });
        } else {
          await this.$store.dispatch('Positions/createPosition', { name, strategyId });
        }
        await Promise.all([
          this.$store.dispatch('Positions/loadPositions'),
          this.$store.dispatch('Positions/loadStrategies'),
        ]);
        this.dialogVisible = false;
        this.loading = false;
      },
    },
    mounted() {
      EventBus.$on(eventNames.createPosition, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Update position': 'Edycja stanowiska',
          'Create new position': 'Tworzenie nowego stanowiska',
          'Cancel': 'Anuluj',
          'Save': 'Zapisz',
          'Create': 'Utwórz',
          'Leveling structure': 'Struktura poziomów',
          'Name': 'Nazwa',
          'strategy-locked-info': 'Istnieją osoby przypisane do tego stanowiska - zmiana struktury poziomów jest wyłączona.',
        },
        en: {
          'strategy-locked-info': 'There are people using this position - changing leveling structure is disabled.',
        },
      },
    },
  };
</script>
<style scoped>
  .title-mobile{
    font-size: medium;
    line-height: initial;
  }
</style>

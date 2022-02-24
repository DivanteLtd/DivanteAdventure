<template>
  <v-dialog v-model="dialogVisible" width="1000">
    <v-card id="dialog-strategy-form">
      <v-app-bar class="headline" color="transparent" flat>
        <span :class="{'title-mobile': $vuetify.breakpoint.xs}">
          {{ editing ? $t('Update leveling structure') : $t('Create new leveling structure') }}
        </span>
        <v-spacer/>
        <v-btn @click="dialogVisible = false" icon><v-icon>clear</v-icon></v-btn>
      </v-app-bar>
      <v-divider/>
      <strategy-window-content :name.sync="strategy.name"
                               :levels.sync="strategy.levels"
                               :deleted-levels.sync="deletedLevels"/>
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
  import StrategyWindowContent from './StrategyWindowContent';

  export default {
    name: 'StrategyWindow',
    components: { StrategyWindowContent },
    data() {
      return {
        dialogVisible: false,
        editing: false,
        loading: false,
        strategy: {
          id: -1,
          name: '',
          levels: [],
        },
        deletedLevels: [],
      };
    },
    computed: {
      formValid() {
        const correctNameLength = this.strategy.name.trim().length > 0;
        const correctStrategiesCount = this.strategy.levels
          .filter(level => level.name.trim().length > 0)
          .length;
        const allStrategiesCount = this.strategy.levels.length;
        return correctNameLength && correctStrategiesCount === allStrategiesCount;
      },
    },
    methods: {
      show(data) {
        if (this.dialogVisible) {
          return;
        }
        if (typeof data === 'undefined') {
          this.strategy.id = -1;
          this.strategy.name = '';
          this.strategy.levels = [];
          this.editing = false;
        } else {
          this.strategy.id = data.id;
          this.strategy.name = data.name;
          this.strategy.levels = JSON.parse(JSON.stringify(data.levels)) // deep copy
            .sort((a, b) => a.priority - b.priority);
          this.editing = true;
        }
        this.deletedLevels = [];
        this.loading = false;
        this.dialogVisible = true;
      },
      async save() {
        this.loading = true;
        if (this.editing) {
          const deleteLevelPromises = this.deletedLevels
            .filter(level => level.id !== -1)
            .map(level => level.id)
            .map(id => this.$store.dispatch('Positions/deleteLevel', id));
          const updateLevelPromises = this.strategy.levels
            .map((level, index) => ({ ...level, priority: index }))
            .filter(level => level.id !== -1)
            .map(level => this.$store.dispatch('Positions/updateLevel', level));
          const strategyId = this.strategy.id;
          const createLevelPromises = this.strategy.levels
            .map((level, index) => ({ ...level, priority: index }))
            .filter(level => level.id === -1)
            .map(level => level.name)
            .map(name => this.$store.dispatch('Positions/createLevel', { name, strategyId }));
          const updateStrategyNamePromise = this.$store.dispatch('Positions/updateStrategy', this.strategy);

          await Promise.all(deleteLevelPromises);
          await Promise.all(updateLevelPromises);
          await Promise.all(createLevelPromises);
          await updateStrategyNamePromise;
        } else {
          const { id } = await this.$store.dispatch('Positions/createStrategy', this.strategy);
          const createLevelPromises = this.strategy.levels
            .map(level => level.name)
            .map(name => this.$store.dispatch('Positions/createLevel', { name, strategyId: id }));
          await Promise.all(createLevelPromises);
        }
        await Promise.all([
          this.$store.dispatch('Positions/loadPositions'),
          this.$store.dispatch('Positions/loadStrategies'),
        ]);
        this.dialogVisible = false;
      },
    },
    mounted() {
      EventBus.$on(eventNames.createStrategy, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Update leveling structure': 'Edycja struktury poziomów',
          'Create new leveling structure': 'Tworzenie nowej struktury poziomów',
          'Cancel': 'Anuluj',
          'Save': 'Zapisz',
          'Create': 'Utwórz',
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

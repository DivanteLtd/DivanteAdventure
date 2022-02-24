<template>
  <tr>
    <td>{{ strategy.name }}</td>
    <td>{{ levels }}</td>
    <td>{{ positions }}</td>
    <td class="text-center">
      <div class="d-flex justify-center">
        <v-tooltip left>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" @click="editStrategy" icon>
              <v-icon>edit</v-icon>
            </v-btn>
          </template>
          {{ $t('Edit leveling structure') }}
        </v-tooltip>
        <v-tooltip v-if="canDelete" left>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" :disabled="!canBeDeleted" @click="deleteStrategy" :loading="deletingInProgress" icon>
              <v-icon>delete</v-icon>
            </v-btn>
          </template>
          {{ canBeDeleted ? $t('Delete leveling structure') : $t('delete-error') }}
        </v-tooltip>
      </div>
    </td>
  </tr>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import { mapGetters } from 'vuex';

  export default {
    name: 'StrategiesRow',
    props: {
      strategy: { type: Object, required: true },
    },
    data() {
      return {
        deletingInProgress: false,
      };
    },
    computed: {
      ...mapGetters({
        canDelete: 'Authorization/isTribeMaster',
      }),
      levels() {
        return this.strategy.levels
          .sort((a, b) => a.priority - b.priority)
          .map(level => level.name)
          .join(', ');
      },
      positions() {
        return this.strategy.positions.map(position => position.name).join(', ');
      },
      canBeDeleted() {
        const levelsUsed = this.strategy.levels.map(level => level.employeeCount).reduce((a, b) => a + b, 0);
        const positionsUsed = this.strategy.positions.map(pos => pos.employeeCount).reduce((a, b) => a + b, 0);
        return levelsUsed + positionsUsed === 0;
      },
    },
    methods: {
      async deleteStrategy() {
        this.deletingInProgress = true;
        if (!this.canBeDeleted) {
          this.$store.commit('showSnackbar', { text: this.$t('delete-error'), color: 'red' });
        } else {
          const levelDeletePromises = this.strategy.levels
            .map(level => this.$store.dispatch('Positions/deleteLevel', level.id));
          const positionDeletePromises = this.strategy.positions
            .map(position => this.$store.dispatch('Positions/deletePosition', position.id));
          await Promise.all(levelDeletePromises);
          await Promise.all(positionDeletePromises);
          await this.$store.dispatch('Positions/deleteStrategy', this.strategy.id);
          await Promise.all([
            this.$store.dispatch('Positions/loadStrategies'),
            this.$store.dispatch('Positions/loadPositions'),
          ]);
        }
        this.deletingInProgress = false;
      },
      editStrategy() {
        EventBus.$emit(eventNames.createStrategy, this.strategy);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Delete leveling structure': 'Usuń strukturę poziomów',
          'Edit leveling structure': 'Edytuj strukturę poziomów',
          'delete-error': 'Istnieją osoby przypisane do stanowisk w ramach tej struktury poziomów. Przenieś ich do innych stanowisk, aby usunąć strukturę.',
        },
        en: {
          'delete-error': 'There are people using positions in that leveling structure. Move them to different positions to delete structure.',
        },
      },
    },
  };
</script>

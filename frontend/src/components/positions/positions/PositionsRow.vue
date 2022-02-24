<template>
  <tr>
    <td>{{ position.name }}</td>
    <td>{{ position.strategy.name }}</td>
    <td>{{ availableLevels }}</td>
    <td style="text-align: right;">
      <div class="position-row">
        <v-tooltip left>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" @click="editPosition" icon>
              <v-icon>edit</v-icon>
            </v-btn>
          </template>
          {{ $t('Edit position') }}
        </v-tooltip>
        <v-tooltip v-if="canDelete" left>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on"
                   @click="deletePosition"
                   :loading="deletingInProgress"
                   :disabled="!canBeDeleted"
                   icon>
              <v-icon>delete</v-icon>
            </v-btn>
          </template>
          {{ canBeDeleted ? $t('Delete position') : $t('delete-error') }}
        </v-tooltip>
      </div>
    </td>
  </tr>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import { mapGetters } from 'vuex';

  export default {
    name: 'PositionsRow',
    props: {
      position: { type: Object, required: true },
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
      availableLevels() {
        return this.position.strategy.levels
          .sort((a, b) => a.priority - b.priority)
          .map(level => level.name)
          .join(', ');
      },
      canBeDeleted() {
        return this.position.employeeCount === 0;
      },
    },
    methods: {
      async deletePosition() {
        this.deletingInProgress = true;
        if (!this.canBeDeleted) {
          this.$store.commit('showSnackbar', { text: this.$t('delete-error'), color: 'red' });
        } else {
          await this.$store.dispatch('Positions/deletePosition', this.position.id);
          await Promise.all([
            this.$store.dispatch('Positions/loadPositions'),
            this.$store.dispatch('Positions/loadStrategies'),
          ]);
        }
        this.deletingInProgress = false;
      },
      editPosition() {
        EventBus.$emit(eventNames.createPosition, this.position);
      },
    },
    i18n: {
      messages: {
        pl: {
          'Delete position': 'Usuń stanowisko',
          'Edit position': 'Edytuj stanowisko',
          'delete-error': 'Istnieją osoby przypisane do tego stanowiska. Przenieś je do innego stanowiska przed usunięciem.',
        },
        en: {
          'delete-error': 'There are people using this position. Move them to another positions before deleting it.',
        },
      },
    },
  };
</script>
<style scoped>
  .position-row{
    display: flex;
    justify-content: flex-end;
  }
</style>

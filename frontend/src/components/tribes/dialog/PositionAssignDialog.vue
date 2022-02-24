<template>
  <v-dialog v-model="dialogVisible" max-width="600">
    <v-card>
      <v-card-title>
        <span class="headline">{{ $t('Assign position') }}</span>
      </v-card-title>
      <v-card-text>
        <v-autocomplete :no-data-text="$t('No positions available')"
                        :loading="loading"
                        :items="filteredPositions"
                        item-value="id"
                        item-text="name"
                        v-model="position"/>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1"
               :disabled="formInvalid"
               :loading="loading"
               @click="assign"
               text>
          {{ $t('Save') }}
        </v-btn>
        <v-btn text @click="dialogVisible = false">{{ $t('Close') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../../eventbus';
  import { mapState } from 'vuex';

  export default {
    name: 'PositionAssignDialog',
    data() {
      return {
        dialogVisible: false,
        loading: false,
        position: null,
        tribe: {
          id: -1,
          positions: [],
        },
      };
    },
    computed: {
      ...mapState({
        positions: state => state.Positions.positions,
      }),
      formInvalid() {
        return this.position === null;
      },
      filteredPositions() {
        const available = this.positions;
        const usedIds = this.tribe.positions.map(position => position.id);
        return available.filter(position => !usedIds.includes(position.id));
      },
    },
    methods: {
      async assign() {
        this.loading = true;
        await this.$store.dispatch('Positions/assignToTribe', { tribeId: this.tribe.id, positionId: this.position });
        EventBus.$emit(eventNames.refreshTribeWindow);
        this.dialogVisible = false;
      },
      async show(tribe) {
        if (this.dialogVisible) {
          return;
        }
        this.tribe = tribe;
        this.position = null;
        this.dialogVisible = true;
        this.loading = true;
        await this.$store.dispatch('Positions/loadPositions');
        this.loading = false;
      },
    },
    mounted() {
      EventBus.$on(eventNames.showPositionAssignToTribeWindow, this.show);
    },
    i18n: {
      messages: {
        pl: {
          'Assign position': 'Przypisz stanowisko',
          'Close': 'Zamknij',
          'Save': 'Zapisz',
          'No positions available': 'Brak dostępnych stanowisk',
        },
      },
    },
  };
</script>

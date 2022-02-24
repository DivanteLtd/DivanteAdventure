<template>
  <div>
    <v-progress-linear class="mt-3" height="6" v-if="loading" indeterminate/>
    <leaders-structure-form/>
    <v-container grid-list-xl="2" fluid>
      <v-card-title>
        <span class="headline">{{ $t('Leader structures') }}</span>
      </v-card-title>
      <v-row no-gutters align-start justify-start wrap>
        <v-col v-for="(itemStructures, index) in leaderStructures" :key="index" cols="12" sm="6" md="3">
          <v-card class="ml-2 mb-3">
            <v-card-text>
              <v-list-item-title>
                {{ $t('Leader') }}
                <employee-chip v-if="typeof(itemStructures.leader) === 'object'" :employee="itemStructures.leader" />
                <v-divider class="mt-4"/>
              </v-list-item-title>
              <padawans-row v-for="(item, idx) in itemStructures.structures" @delete="deletePadawan" :key="idx"
                            :leader-id="itemStructures.leader.id" :item="item"/>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
  import EmployeeChip from '../../utils/EmployeeChip';
  import LeadersStructureForm from './LeadersStructureForm';
  import PadawansRow from './PadawansRow';
  import { mapState } from 'vuex';

  export default {
    name: 'LeadersStructure',
    components: { EmployeeChip, LeadersStructureForm, PadawansRow },
    props: {
      loading: { type: Boolean, required: true },
    },
    computed: {
      ...mapState({
        leaderStructures: state => state.Employees.leaderStructures,
      }),
    },
    methods: {
      async deletePadawan(data) {
        try {
          await this.$store.dispatch('Employees/deletePadawan', data);
          this.$store.commit('showSnackbar', {
            text: this.$t('Person has been deleted'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Person cannot be deleted'),
            color: 'error',
          });
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Leader structures': 'Struktury liderów',
          'Leader': 'Lider',
          'Person has been deleted': 'Osoba została usunięta',
          'Person can not be deleted': 'Osoba nie może zostać usunięta',
        },
      },
    },
  };
</script>

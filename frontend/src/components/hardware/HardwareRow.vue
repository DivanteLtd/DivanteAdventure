<template>
  <tr :class="{ showPointer:!generated && isSuperAdmin }">
    <template v-if="loading">
      <td colspan="10">
        <v-progress-linear indeterminate/>
      </td>
    </template>
    <template v-else>
      <td @click="openHardwareDialog">
        {{ item.name }}
      </td>
      <td @click="openHardwareDialog">
        {{ item.lastName }}
      </td>
      <td @click="openHardwareDialog">
        {{ item.contract }}
      </td>
      <td @click="openHardwareDialog">
        {{ item.category }}
      </td>
      <td @click="openHardwareDialog">
        {{ item.manufacturer }}
      </td>
      <td @click="openHardwareDialog">
        {{ item.model }}
      </td>
      <td @click="openHardwareDialog">
        {{ item.serialNumber }}
      </td>
      <td v-if="generated">
        {{ item.signedByHelpdesk ? formatDate(item.signedByHelpdesk) : 'X' }}
      </td>
      <td v-if="generated">
        {{ item.signedByUser ? formatDate(item.signedByUser) : 'X' }}
      </td>
      <td v-if="!generated">
        <v-tooltip left>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" @click="deleteDialogVisible = true" icon>
              <v-icon>delete</v-icon>
            </v-btn>
          </template>
          {{ $t('Delete') }}
        </v-tooltip>
        <confirm-dialog v-model="deleteDialogVisible"
                        v-if="deleteDialogVisible"
                        @yes="deleteEntry"
                        :question="$t('Do you really want to delete this hardware lending agreement entry?')"
                        yes-color="red"/>
      </td>
    </template>
  </tr>
</template>
<script>
  import { EventBus, eventNames } from '../../eventbus';
  import moment from '@divante-adventure/work-moment';
  import ConfirmDialog from '../utils/ConfirmDialog';
  import { mapGetters } from 'vuex';

  export default {
    name: 'HardwareRow',
    components: { ConfirmDialog },
    props: {
      item: { type: Object, required: true },
      generated: { type: Boolean, required: true },
    },
    data() {
      return {
        deleteDialogVisible: false,
        loading: false,
      };
    },
    computed: {
      ...mapGetters({
        isSuperAdmin: 'Authorization/isSuperAdmin',
      }),
    },
    methods: {
      async deleteEntry() {
        this.$emit('changeLoading');
        try {
          await this.$store.dispatch('Hardware/deleteEntry', this.item.id);
          this.$store.commit('showSnackbar', {
            text: this.$t('Hardware lending agreement entry has been deleted'),
            color: 'success',
          });
        } catch (e) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Hardware lending agreement entry cannot be deleted'),
            color: 'error',
          });
        }
        await this.$store.dispatch('Hardware/loadHardwareAgreements');
        this.$emit('changeLoading');
      },
      openHardwareDialog() {
        if (!this.generated && this.isSuperAdmin) {
          EventBus.$emit(eventNames.showHardwareDialog, this.item);
        }
      },
      formatDate(date) {
        return moment(date).format(this.$t('YYYY-MM-DD'));
      },
    },
    i18n: {
      messages: {
        pl: {
          'Delete': 'Usuń',
          'Hardware lending agreement entry has been deleted': 'Wpis umowy użyczenia sprzętu został usunięty',
          'Hardware lending agreement entry cannot be deleted': 'Wpis umowy użyczenia sprzętu nie został usunięty',
          'Do you really want to delete this hardware lending agreement entry?': 'Czy na pewno chcesz usunąć wpis umowy użyczenia sprzętu?',
        },
      },
    },
  };
</script>
<style scoped>
    td {
      min-width: 120px;
    }
    tr.showPointer {
      cursor: pointer;
    }
</style>

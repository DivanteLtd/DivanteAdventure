<template>
  <div v-if="isManager">
    <v-tooltip class="ml-2" bottom>
      <template v-slot:activator="{ on }">
        <v-btn v-on="on" @click="download" icon>
          <v-icon>save_alt</v-icon>
        </v-btn>
      </template>
      {{ $t('Download report') }}
    </v-tooltip>
    <loading-dialog :visible="visible"/>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import LoadingDialog from '../utils/LoadingDialog';

  export default {
    name: 'ProjectListActions',
    components: { LoadingDialog },
    props: {
      project: { type: Object, required: true },
    },
    data() {
      return {
        visible: false,
      };
    },
    computed: {
      ...mapGetters({
        isManager: 'Authorization/isManager',
      }),
    },
    methods: {
      download() {
        this.visible = true;
        this.$store.dispatch('Projects/downloadReport', this.project.id).finally(() => {
          this.visible = false;
        });
      },
    },
    i18n: {
      messages: {
        pl: {
          'Download report': 'Pobierz raport',
        },
      },
    },

  };
</script>

<style scoped>

</style>

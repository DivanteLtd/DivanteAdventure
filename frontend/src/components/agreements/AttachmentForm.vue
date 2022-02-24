<template>
  <v-dialog v-model="dialogVisible" width="600">
    <v-card>
      <v-row no-gutters wrap class="justify-center">
        <v-col cols="12">
          <v-card-title class="headline">
            <span>{{ $t('Add / delete attachments') }}</span>
          </v-card-title>
        </v-col>
        <v-col cols="12" class="pa-4">
          <file-uploader :title="$t('Add attachments')" :selected-callback="addFile"/>
        </v-col>
        <v-col>
          <attachment-info/>
        </v-col>
        <v-col cols="12">
          <v-list dense v-if="fileNames">
            <v-list-item v-for="(name, index) in fileNames" :key="index">
              <v-list-item-action>
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn v-on="on" @click="deleteByIndex(index)" icon>
                      <v-icon color="red">highlight_off</v-icon>
                    </v-btn>
                  </template>
                  {{ $t('Delete') }}
                </v-tooltip>
              </v-list-item-action>
              <v-list-item-content>{{ name }}</v-list-item-content>
            </v-list-item>
          </v-list>
        </v-col>
      </v-row>
      <v-card-actions class="justify-center">
        <v-btn class="card-action__button" v-if="files.length > 0" color="success" @click="addAttachments">
          {{ $t('Add') }}
        </v-btn>
      </v-card-actions>
      <v-card-title>
        <span>{{ $t('List of current attachments') }}</span>
      </v-card-title>
      <v-card-text class="pa-0">
        <v-data-table mobile-breakpoint="0"
                      disable-pagination
                      :no-data-text="loading ? $t('Loading data...') : $t('No data available.')"
                      :items="agreementAttachments"
                      hide-default-footer>
          <template v-slot:item="{ item }">
            <attachment-form-row @deleted="reloadList" :item="item"/>
          </template>
        </v-data-table>
      </v-card-text>
      <v-card-actions>
        <v-spacer/>
        <v-btn color="red" text @click="dialogVisible = false">
          {{ $t('Cancel') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import { EventBus, eventNames } from '../../eventbus';
  import FileUploader from '../utils/FileUploader';
  import { mapState } from 'vuex';
  import AttachmentInfo from '../../components/agreements/AttachmentInfo';
  import AttachmentFormRow from './AttachmentFormRow';


  export default {
    name: 'AttachmentForm',
    components: { AttachmentFormRow, FileUploader, AttachmentInfo },
    props: {
      value: { type: Array, default: () => [] },
    },
    data() { return {
      dialogVisible: false,
      loading: false,
      files: this.value,
    };},
    computed: {
      ...mapState({
        agreementAttachments: state => state.Agreements.attachments,
      }),
      fileNames() {
        return this.files.map(file => file.name);
      },
    },
    methods: {
      show() {
        if (!this.dialogVisible) {
          this.loading = true;
          this.$store.dispatch('Agreements/loadAgreementAttachments').finally(() => {
            this.loading = false;
          });
          this.dialogVisible = true;
          this.files = [];
          this.$emit('input', this.files);
        }
      },
      reloadList() {
        this.$store.dispatch('Agreements/loadAgreementAttachments');
      },
      deleteByIndex(index) {
        this.files.splice(index, 1);
        this.$emit('input', this.files);
      },
      addFile(file) {
        if (/^(pl|en)_/gm.test(file.name) === false) {
          this.$store.commit('showSnackbar', {
            text: this.$t('Attachment name is not valid'),
            color: 'error',
          });
        } else {
          this.files.push(file);
          this.$emit('input', this.files);
        }
      },
      async addAttachments() {
        const checkAttachmentsName = this.agreementAttachments.map(value => value.name);
        let flag = false;
        this.files.forEach(val => {
          if (checkAttachmentsName.includes(val.name)) {
            this.$store.commit('showSnackbar', {
              text: this.$t('Attachment already exist'),
              color: 'error',
            });
            flag = true;
          }
        });
        if (flag === false) {
          try {
            const result = await this.$store.dispatch('Agreements/addAttachment', this.files);
            if (JSON.stringify(result).includes('error')) {
              throw new Error(result);
            }
            this.$store.commit('showSnackbar', {
              text: this.files.length > 1 ? this.$t('Attachments has been added')
                : this.$t('Attachment has been added'),
              color: 'success',
            });
            this.files = [];
          } catch (e) {
            this.$store.commit('showSnackbar', {
              text: this.files.length > 1 ? this.$t('Attachments can not be added')
                : this.$t('Attachment can not be added'),
              color: 'error',
            });
          }
        }
      },
    },
    mounted() {
      EventBus.$on(eventNames.attachmentForm, this.show);
    },
    i18n: { messages: {
      pl: {
        'Add / delete attachments': 'Dodaj / usuń załączniki',
        'Add attachments': 'Dodaj załączniki',
        'Attachment already exist': 'Załącznik już istnieje',
        'Attachment has been added': 'Załącznik został dodany',
        'Attachment can not be added': 'Załącznik nie został dodany',
        'Attachments has been added': 'Załączniki zostały dodane',
        'Attachments can not be added': 'Załączniki nie zostały dodane',
        'List of current attachments': 'Lista obecnych załączników',
        'Cancel': 'Anuluj',
        'Add': 'Dodaj',
        'No data available.': 'Brak danych.',
        'Loading data...': 'Wczytywanie...',
        'Attachment name is not valid': 'Nazwa pliku nie jest poprawna',
      },
    } },
  };
</script>
<style scoped>
  .card-action__button {
    width: 80%;
  }
</style>

<template>
  <div>
    <v-container class="pa-0 mt-1">
      <v-row no-gutters wrap>
        <v-col class="mr-3" cols="1">
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-avatar v-on="on">
                <v-icon>help_outline</v-icon>
              </v-avatar>
            </template>
            {{ $t('help_message') }}
          </v-tooltip>
        </v-col>
        <v-col class="mt-2" cols="10">
          <file-uploader :title="$t('Add attachment')" :selected-callback="addFile"/>
        </v-col>
        <v-col cols="12">
          <v-list dense>
            <v-list-item v-for="(name, index) in fileNames" :key="index">
              <v-list-item-action>
                <v-btn icon @click="deleteByIndex(index)">
                  <v-icon color="red">highlight_off</v-icon>
                </v-btn>
              </v-list-item-action>
              <v-list-item-content>{{ name }}</v-list-item-content>
            </v-list-item>
          </v-list>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
  import FileUploader from '../../utils/FileUploader';
  import { EventBus, eventNames } from '../../../eventbus';

  export default {
    name: 'Attachments',
    components: { FileUploader },
    props: {
      value: { type: Array, default: () => [] },
    },
    data() { return {
      files: this.value,
    };},
    computed: {
      fileNames() {
        return this.files.map(file => file.name);
      },
    },
    methods: {
      deleteByIndex(index) {
        this.files.splice(index, 1);
        this.$emit('input', this.files);
      },
      clear() {
        this.files = [];
        this.$emit('input', this.files);
      },
      addFile(file) {
        this.files.push(file);
        this.$emit('input', this.files);
      },
    },
    mounted() {
      EventBus.$on(eventNames.createNewLeaveRequest, this.clear);
    },
    i18n: { messages: {
      pl: {
        'Add attachment': 'Dodaj załącznik',
        'help_message': 'Załącznikiem może być dowolne zaświadczenie wydane przez lekarza, potwierdzające datę niedyspozycji',
      },
      en: {
        help_message: 'An attachment may be any attestation issued by a doctor confirming the date of unavailability',
      },
    } },
  };
</script>

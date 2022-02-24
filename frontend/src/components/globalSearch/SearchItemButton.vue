<template>
  <v-chip color="indigo" text-color="white" @click.stop="click">
    <v-avatar>
      <v-img v-if="imageType === 'img'" :src="button.img.value" @error="button.img.value = ''"/>
      <v-icon class="ml-2 mr-2" v-else-if="imageType === 'icon'">{{ button.img.value }}</v-icon>
    </v-avatar>
    {{ label }}
  </v-chip>
</template>

<script>
  export default {
    name: 'SearchItemButton',
    props: {
      button: { type: Object, required: true },
    },
    computed: {
      label() {
        const labelObj = this.button.displayLabel;
        if (typeof labelObj === 'string') {
          switch (labelObj) {
            case 'CoE': return this.$t('Leave days');
            case 'CLC LUMP SUM':
            case 'B2B LUMP SUM': return this.$t('Free days');
            default: return this.$t('Unavailability days');
          }
        } else if (typeof labelObj === 'object' && labelObj.type === 'text') {
          return labelObj.value;
        } else if (typeof labelObj === 'object' && labelObj.type === 'i18n') {
          return this.$t(labelObj.value);
        }
        return '';
      },
      imageType() {
        const img = this.button.img;
        if (typeof img === 'object') {
          return img.type;
        }
        return 'null';
      },
    },
    methods: {
      async click() {
        const link = this.button.link;
        if (typeof link === 'string') {
          await this.$router.push(link);
          if (this.$route.name === 'free-days-id') {
            window.location.reload();
          }
        } else if (typeof link === 'object' && link.type === 'local') {
          await this.$router.push(link.value);
        } else if (typeof link === 'object' && link.type === 'global') {
          window.location.href = link.value;
        }
      },
    },
    i18n: {
      messages: {
        pl: {
          'Leave days': 'Dni urlopowe',
          'Free days': 'Dni wolne',
          'Unavailability days': 'Dni niedostępności',
        },
      },
    },
  };
</script>

import { mapState } from 'vuex';

export default {
  computed: {
    ...mapState({
      apiClient: state => state.apiClient,
    }),
  },
};

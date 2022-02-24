export default {
    namespaced: true,
    state: {
        contractsType: [],
    },
    mutations: {
        setContractsType(state, contractsType) {
            state.contractsType = contractsType;
        },
    },
    actions: {
        async load(context) {
            try {
                const contractsType = await context.rootState.apiClient.dictionaries.listContractsType();
                context.commit('setContractsType', contractsType);
            } catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
        async create(context, data) {
            try{
                await context.rootState.apiClient.dictionaries.createContractType(data);
            }catch (e) {
                context.dispatch('handleErrorResponse', e, { root: true });
                throw e;
            }
        },
    },
};

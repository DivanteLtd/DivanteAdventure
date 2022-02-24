// eslint-disable-next-line no-unused-vars
import RestClient from '../restClient';

/**
 * @param {RestClient} restClient
 */
export default restClient => ({
  getGitlabProjects: () => restClient.get('integrations/gitlab'),
});

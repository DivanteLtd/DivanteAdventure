export function getWorkMode(workMode) {
  switch(workMode) {
    case 1: return 'Work from office';
    case 2: return 'Work remotely';
    case 3: return 'Work partial remotely';
    default: return '';
  }
}

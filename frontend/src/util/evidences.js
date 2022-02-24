export const evidenceStatus = {
  APPROVAL_NOT_REQUIRED: 0,
  AWAITS_APPROVAL: 1,
  NOT_APPROVED: 2,
  SENT: 3,
};

export function getColor(status) {
  switch(status) {
    case evidenceStatus.APPROVAL_NOT_REQUIRED: return '#00AA00';
    case evidenceStatus.AWAITS_APPROVAL: return '#0000FF';
    case evidenceStatus.NOT_APPROVED: return '#FF0000';
    case evidenceStatus.SENT: return '#00AA00';
    default: return null;
  }
}

export function getIcon(status) {
  switch(status) {
    case evidenceStatus.APPROVAL_NOT_REQUIRED: return 'check_circle';
    case evidenceStatus.AWAITS_APPROVAL: return 'timelapse';
    case evidenceStatus.NOT_APPROVED: return 'highlight_off';
    case evidenceStatus.SENT: return 'check_circle';
    default: return null;
  }
}

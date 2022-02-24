export const contractsName = {
  B2B_LUMP_SUM: {
    id: '1',
    useSickLeaveAttachments: true,
  },
  B2B_HOURLY: {
    id: '2',
    useSickLeaveAttachments: true,
  },
  CLC_LUMP_SUM: {
    id: '3',
    useSickLeaveAttachments: false,
  },
  CLC_HOURLY: {
    id: '4',
    useSickLeaveAttachments: false,
  },
  CoE: {
    id: '5',
    useSickLeaveAttachments: false,
  },
  OUTSOURCE: {
    id: '6',
    useSickLeaveAttachments: false,
  },
};

const DEFAULT = {
  id: '-1',
  useSickLeaveAttachments: false,
};

export function getContractDataByEmployee(employee) {
  if (typeof employee === 'undefined' || typeof employee.contract === 'undefined') {
    return DEFAULT;
  }
  switch(employee.contract.id) {
    case 1: return contractsName.B2B_LUMP_SUM;
    case 2: return contractsName.B2B_HOURLY;
    case 3: return contractsName.CLC_LUMP_SUM;
    case 4: return contractsName.CLC_HOURLY;
    case 5: return contractsName.CoE;
    case 6: return contractsName.OUTSOURCE;
    default: return DEFAULT;
  }
}

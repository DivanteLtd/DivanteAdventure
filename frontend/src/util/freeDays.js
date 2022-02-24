/*
  If user is using old browser with no implementation of Array.isArray function (which was implemented since
  Chrome 5, Firefox 4.0, IE 9, Opera 10.5 and Safari 5), create it now for backward compatibility.
 */
if (typeof(Array.isArray) === 'undefined') {
  Array.isArray = function(obj) {
    return Object.prototype.toString.call(obj) === '[object Array]';
  };
}

export const leaveDaysTypes = {
  freePaid: 0,
  freeUnpaid: 1,
  leavePaid: 2,
  leaveUnpaid: 3,
  leaveRequest: 4,
  leaveOccasional: 5,
  leaveCare: 6,
  sickLeavePaid: 7,
  sickLeaveUnpaid: 8,
  overtime: 9,
  additionalHours: 10,
  unavailability: 11,
};

export const leaveRequestsStatuses = {
  pending: 0,
  accepted: 1,
  rejected: 2,
  pendingResignation: 3,
  resigned: 4,
  planned: 5,
};

export const leaveDaysStatuses = {
  active: 0,
  canceled: 1,
  pendingResignation: 2,
  resigned: 3,
};

export function getDayNameByType(dayType) {
  switch (dayType) {
    case leaveDaysTypes.freePaid:
      return this.$t('Paid free day');
    case leaveDaysTypes.freeUnpaid:
      return this.$t('Unpaid free day');
    case leaveDaysTypes.leavePaid:
      return this.$t('Paid leave day');
    case leaveDaysTypes.leaveUnpaid:
      return this.$t('Unpaid leave day');
    case leaveDaysTypes.leaveRequest:
      return this.$t('Leave day on request');
    case leaveDaysTypes.leaveOccasional:
      return this.$t('Leave day on occasion');
    case leaveDaysTypes.leaveCare:
      return this.$t('Care leave day');
    case leaveDaysTypes.sickLeavePaid:
      return this.$t('Sick leave');
    case leaveDaysTypes.sickLeaveUnpaid:
      return this.$t('Unpaid sick leave');
    case leaveDaysTypes.overtime:
      return this.$t('Taking back overtime');
    case leaveDaysTypes.additionalHours:
      return this.$t('Taking back additional hours');
    case leaveDaysTypes.unavailability:
      return this.$t('Unavailability day');
    default:
      return 'N/A';
  }
}

export function getRequiredContract(dayType) {
  switch(dayType) {
    case leaveDaysTypes.freePaid:
    case leaveDaysTypes.freeUnpaid:
    case leaveDaysTypes.additionalHours:
      return ['CLC LUMP SUM', 'B2B LUMP SUM'];
    case leaveDaysTypes.leavePaid:
    case leaveDaysTypes.leaveUnpaid:
    case leaveDaysTypes.leaveRequest:
    case leaveDaysTypes.leaveOccasional:
    case leaveDaysTypes.leaveCare:
    case leaveDaysTypes.overtime:
      return ['CoE'];
    case leaveDaysTypes.sickLeavePaid:
    case leaveDaysTypes.sickLeaveUnpaid:
      return ['CoE', 'CLC LUMP SUM', 'B2B LUMP SUM'];
    case leaveDaysTypes.unavailability:
      return ['CLC HOURLY', 'B2B HOURLY'];
    default:
      return [];
  }
}

export function canUseLeaveDays(employee) {
  if (!employee.hasOwnProperty('contract')) {
    return false;
  }
  const contractName = employee.contract.name;
  return getRequiredContract(leaveDaysTypes.leavePaid).includes(contractName);
}
export function canUseUnavailabilityDays(employee) {
  if (!employee.hasOwnProperty('contract')) {
    return false;
  }
  const contractName = employee.contract.name;
  return getRequiredContract(leaveDaysTypes.unavailability).includes(contractName);
}

export function canUseFreeDays(employee) {
  if (!employee.hasOwnProperty('contract')) {
    return false;
  }
  const contractName = employee.contract.name;
  return getRequiredContract(leaveDaysTypes.freePaid).includes(contractName);
}

export function countUsedLeaveCareHoursInPeriod(period, leaveCare) {
  if (typeof (period) !== 'object') {
    return 0;
  }

  const acceptedRequestStatus = [
    leaveRequestsStatuses.accepted,
    leaveRequestsStatuses.pendingResignation,
    leaveRequestsStatuses.pending,
  ];

   return (period.requests || [])
      .filter(request => acceptedRequestStatus.includes(request.status))
       .map(request => request.days)
       .reduce((a, b) => [...a, ...b], [])
       .filter(day => day.type === leaveCare && day.status === leaveDaysStatuses.active)
       .map(day => day.hours)
       .reduce((a, b) => a + b, 0);
}

export function countUsedLeaveDaysInPeriod(period, allowedTypes) {
  let typesForFilter = leaveDaysTypes;
  if (Array.isArray(allowedTypes) && allowedTypes !== []) {
    typesForFilter = allowedTypes;
  }
  if (typeof(period) !== 'object') {
    return 0;
  }

  const acceptedRequestStatus = [
      leaveRequestsStatuses.accepted,
      leaveRequestsStatuses.pendingResignation,
      leaveRequestsStatuses.pending,
      leaveRequestsStatuses.planned,
  ];

  return (period.requests || [])
      .filter(request => acceptedRequestStatus.includes(request.status))
      .map(request => (request.days || [])
              .filter(day => typesForFilter.includes(day.type)
                  && (day.status === leaveDaysStatuses.active || day.status === leaveDaysStatuses.pendingResignation))
              .length)
      .reduce((a, b) => a + b, 0);
}

function getPaidDaysTypesByEmployee(employee) {
  if (canUseLeaveDays(employee)) {
    return [ leaveDaysTypes.leavePaid, leaveDaysTypes.leaveRequest ];
  }
  else if (canUseFreeDays(employee)) {
    return [ leaveDaysTypes.freePaid ];
  }
  else {
    return [];
  }
}

export function getAvailablePaidDays(period) {
  const paidDaysTypes = getPaidDaysTypesByEmployee(period.employee);
  return period.freeDays - countUsedLeaveDaysInPeriod(period, paidDaysTypes);
}

export function getAvailableDaysOnRequest(period) {
  return 4 - countUsedLeaveDaysInPeriod(period, [leaveDaysTypes.leaveRequest]);
}

export function getAvailableCareLeavesHours(period) {
  return 16 - countUsedLeaveCareHoursInPeriod(period, leaveDaysTypes.leaveCare);
}

/**
 *
 * @param period
 * @param request
 * @return string|boolean
 */
export function verifyNewRequest(period, request) {
  const isUop = canUseLeaveDays(period.employee);

  // checking paid days
  const paidDaysTypes = getPaidDaysTypesByEmployee(period.employee);
  const usedPaidDays = request.days.filter(day => paidDaysTypes.includes(day.type)).length;
  const availablePaidDays = getAvailablePaidDays(period);
  if (request.days[0].type !== leaveDaysTypes.sickLeavePaid) {
    if (usedPaidDays > availablePaidDays) {
      return isUop ? 'You have used too many paid leave days' : 'You have used too many paid free days';
    }
  }


  if (isUop) {
    // checking leave on requests
    const usedDaysOnRequest = request.days.filter(day => day.type === leaveDaysTypes.leaveRequest).length;
    const availableDaysOnRequest = getAvailableDaysOnRequest(period);
    if (usedDaysOnRequest > availableDaysOnRequest) {
      return 'You have used too many leaves on request';
    }

    // checking care leave days
    if (request.days[0].type === leaveDaysTypes.leaveCare) {
      const usedHoursRequest = request.days.hours.reduce((a, b) => Number(a) + Number(b));
      const availableCareLeavesHours = getAvailableCareLeavesHours(period);
      if (usedHoursRequest > availableCareLeavesHours) {
        return 'You have used too many care leaves hours';
      }
    }
    return true;
  }
  else {
    // If there are too many paid sick leaves, replace them with unpaid sick leaves
    const usedLeaveDays = countUsedLeaveDaysInPeriod(period, [leaveDaysTypes.sickLeavePaid]);
    let availablePaidSickLeaves = (period.sickLeaveDays || 0) - usedLeaveDays;
    for (const day of request.days) {
      if (day.type === leaveDaysTypes.sickLeavePaid) {
        if (availablePaidSickLeaves <= 0) {
          day.type = leaveDaysTypes.sickLeaveUnpaid;
        } else {
          availablePaidSickLeaves--;
        }
      }
    }
  }
  return true;
}

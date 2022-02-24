/**
 * Roles structure. In every key:value pair key describes role that is higher than all roles in values.
 */
const ROLES_STRUCTURE = {
  ROLE_USER: [],
  ROLE_HR: [ 'ROLE_USER' ],
  ROLE_MANAGER: [ 'ROLE_USER' ],
  ROLE_TRIBE_MASTER: [ 'ROLE_HR', 'ROLE_MANAGER' ],
  ROLE_HELPDESK: [ 'ROLE_USER' ],
  ROLE_SUPER_ADMIN: [ 'ROLE_TRIBE_MASTER', 'ROLE_HELPDESK' ],
};

/**
 * Special roles that should be marked in checkboxes instead of selects.
 * @type {string[]}
 */
const SPECIAL_ROLES = [ 'ROLE_HR', 'ROLE_HELPDESK' ];

export const TOP_ROLE = 'ROLE_SUPER_ADMIN';
export const BOTTOM_ROLE = 'ROLE_USER';

/** @returns {string[]} */
function getAllRoles() {
  const roles = [];
  for (const role in ROLES_STRUCTURE) {
    if (ROLES_STRUCTURE.hasOwnProperty(role)) {
      roles.push(role);
    }
  }
  return roles;
}

function calculateRoles(role, queue = []) {
  if (queue.includes(role)) {
    return {};
  }
  queue.push(role);
  const childrenRoles = ROLES_STRUCTURE[role];
  const result = [ role ];
  childrenRoles.forEach(r => result.push(...calculateRoles(r, queue)));
  queue.pop();
  return result;
}

function calculateRolesTree() {
  const tree = {};
  getAllRoles().forEach(role => {
    tree[role] = calculateRoles(role);
  });
  return tree;
}

export const appRoles = calculateRolesTree(TOP_ROLE);


/**
 * Type definition of RoleI18n
 * @typedef {Object} RoleI18n
 * @property {number} id
 * @property {string} i18nName
 * @property {boolean} special
 */
/**
 * @returns {RoleI18n[]}
 */
function getAllRolesI18n() {
  const allRoles = getAllRoles();
  const allRolesObject = [];
  for (let id = 0; id < allRoles.length; id++) {
    const i18nName = allRoles[id];
    const special = SPECIAL_ROLES.includes(i18nName);
    allRolesObject.push({ id, i18nName, special });
  }
  return allRolesObject;
}

export const allRolesI18n = getAllRolesI18n();

/**
 * @param {string[]} userRoles
 * @param  {boolean} excludeSpecial
 * @return RoleI18n
 */
export function getTopRole(userRoles, excludeSpecial = false) {
  if (userRoles.length === 0) {
    return getAllRolesI18n().filter(role => role.i18nName === BOTTOM_ROLE)[0];
  }
  if (userRoles.includes(TOP_ROLE)) {
    return getAllRolesI18n().filter(role => role.i18nName === TOP_ROLE)[0];
  }
  const roles = getAllRolesI18n()
    .filter(role => !excludeSpecial || !role.special)
    .filter(role => userRoles.includes(role.i18nName));
  let topRole = {};
  let topCount = -1;
  roles.forEach(role => {
    const childrenCount = appRoles[role.i18nName].length;
    if (childrenCount > topCount) {
      topRole = role;
      topCount = childrenCount;
    }
  });
  return topRole;
}

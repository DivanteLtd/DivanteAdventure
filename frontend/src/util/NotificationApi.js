export const PermissionState = {
  GRANTED: 'granted',
  DEFAULT: 'default',
  DENIED: 'denied',
};

const vibrate = [ 200, 100 ];
const icon = '/static/dvnt-logo-black.png';

export function hasGrantedPermission() {
  return Notification.permission === PermissionState.GRANTED;
}

export default function showNotification(title, body) {
  if (hasGrantedPermission()) {
    navigator.serviceWorker.ready.then(registration => {
      registration.showNotification(title, { body, vibrate, icon });
    });
  }
}

export function enableNotifications() {
  if (!hasGrantedPermission()) {
    return Notification.requestPermission();
  }
  return new Promise();
}

import moment from '@divante-adventure/work-moment';

function leadingZero(val) {
  if (val < 10) {
    return `0${val}`;
  } else {
    return val;
  }
}

export default function getTimeToReset(currentTime) {
  const polishTime = moment(currentTime).zone('+02:00');
  const seconds = (60 - polishTime.seconds()) % 60;
  const minutes = (60 - polishTime.minutes() - (seconds ? 1 : 0)) % 60;
  const hours = (24 - polishTime.hours() - (minutes || seconds ? 1 : 0)) % 6;
  return `${leadingZero(hours)}:${leadingZero(minutes)}:${leadingZero(seconds)}`;
}

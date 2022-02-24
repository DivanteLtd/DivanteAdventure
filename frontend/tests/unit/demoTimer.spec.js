import demoTimer from '../../src/util/demoTimer';
import moment from '@divante-adventure/work-moment';

describe('demoTimer.js', () => {
  test('at 5:00:00 will return 01:00:00', () => {
    const now = moment().zone('+02:00').set({ hour: 5, minute: 0, second: 0 });
    const result = demoTimer(now);
    expect(result).toBe('01:00:00');
  });
  test('at 5:45:00 will return 00:15:00', () => {
    const now = moment().zone('+02:00').set({ hour: 5, minute: 45, second: 0 });
    const result = demoTimer(now);
    expect(result).toBe('00:15:00');
  });
  test('at 5:45:50 will return 00:14:10', () => {
    const now = moment().zone('+02:00').set({ hour: 5, minute: 45, second: 50 });
    const result = demoTimer(now);
    expect(result).toBe('00:14:10');
  });
  test('at 6:00:01 will return 05:59:59', () => {
    const now = moment().zone('+02:00').set({ hour: 6, minute: 0, second: 1 });
    const result = demoTimer(now);
    expect(result).toBe('05:59:59');
  });
  test('at 6:15:00 will return 05:45:00', () => {
    const now = moment().zone('+02:00').set({ hour: 6, minute: 15, second: 0 });
    const result = demoTimer(now);
    expect(result).toBe('05:45:00');
  });
  test('at 7:00:00 will return 05:00:00', () => {
    const now = moment().zone('+02:00').set({ hour: 7, minute: 0, second: 0 });
    const result = demoTimer(now);
    expect(result).toBe('05:00:00');
  });
});

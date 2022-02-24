import { getColorPercentage } from '../../src/util/colors';

describe('colors.js', () => {
  test('getColorPercentage on no colors passed returns white', () => {
    expect(getColorPercentage(0, [])).toBe('#FFFFFF');
    expect(getColorPercentage(23, [])).toBe('#FFFFFF');
    expect(getColorPercentage(76, [])).toBe('#FFFFFF');
    expect(getColorPercentage(100, [])).toBe('#FFFFFF');
  });

  test('getColorPercentage on one color passed returns that color', () => {
    const color = '#582593';
    expect(getColorPercentage(0, [color])).toBe(color);
    expect(getColorPercentage(23, [color])).toBe(color);
    expect(getColorPercentage(76, [color])).toBe(color);
    expect(getColorPercentage(100, [color])).toBe(color);
  });

  test('getColorPercentage returns margin data', () => {
    const threeColors = [
      '#FF0000', // 0%
      '#FFFF00', // 50%
      '#00FF00', // 100%
    ];
    expect(getColorPercentage(-10, threeColors)).toBe('#FF0000');
    expect(getColorPercentage(0, threeColors)).toBe('#FF0000');
    expect(getColorPercentage(100, threeColors)).toBe('#00FF00');
    expect(getColorPercentage(110, threeColors)).toBe('#00FF00');

    const fiveColors = [
        '#FF0000', // 0%
        '#FFFF00', // 25%
        '#00FF00', // 50%
        '#00FFFF', // 75%
        '#0000FF', // 100%
    ];
    expect(getColorPercentage(-10, fiveColors)).toBe('#FF0000');
    expect(getColorPercentage(0, fiveColors)).toBe('#FF0000');
    expect(getColorPercentage(100, fiveColors)).toBe('#0000FF');
    expect(getColorPercentage(110, fiveColors)).toBe('#0000FF');
  });

  test('getColorPercentage returns middle data', () => {
    const threeColors = [
      '#FF0000', // 0%
      '#FFFF00', // 50%
      '#00FF00', // 100%
    ];
    expect(getColorPercentage(50, threeColors)).toBe('#FFFF00');

    const fiveColors = [
      '#FF0000', // 0%
      '#FFFF00', // 25%
      '#00FF00', // 50%
      '#00FFFF', // 75%
      '#0000FF', // 100%
    ];
    expect(getColorPercentage(25, fiveColors)).toBe('#FFFF00');
    expect(getColorPercentage(50, fiveColors)).toBe('#00FF00');
    expect(getColorPercentage(75, fiveColors)).toBe('#00FFFF');
  });

  test('getColorPercentage returns calculated data', () => {
    const threeColors = [
      '#FF0000', // 0%
      '#FFFF00', // 50%
      '#00FF00', // 100%
    ];
    expect(getColorPercentage(10, threeColors)).toBe('#FF3300');
    expect(getColorPercentage(20, threeColors)).toBe('#FF6600');
    expect(getColorPercentage(25, threeColors)).toBe('#FF8000');
    expect(getColorPercentage(30, threeColors)).toBe('#FF9900');
    expect(getColorPercentage(40, threeColors)).toBe('#FFCC00');
    expect(getColorPercentage(60, threeColors)).toBe('#CCFF00');
    expect(getColorPercentage(70, threeColors)).toBe('#99FF00');
    expect(getColorPercentage(75, threeColors)).toBe('#80FF00');
    expect(getColorPercentage(80, threeColors)).toBe('#66FF00');
    expect(getColorPercentage(90, threeColors)).toBe('#33FF00');

    const fiveColors = [
      '#FF0000', // 0%
      '#FFFF00', // 25%
      '#00FF00', // 50%
      '#00FFFF', // 75%
      '#0000FF', // 100%
    ];
    expect(getColorPercentage(10, fiveColors)).toBe('#FF6600');
    expect(getColorPercentage(20, fiveColors)).toBe('#FFCC00');
    expect(getColorPercentage(30, fiveColors)).toBe('#CCFF00');
    expect(getColorPercentage(40, fiveColors)).toBe('#66FF00');
    expect(getColorPercentage(50, fiveColors)).toBe('#00FF00');
    expect(getColorPercentage(60, fiveColors)).toBe('#00FF66');
    expect(getColorPercentage(70, fiveColors)).toBe('#00FFCC');
    expect(getColorPercentage(80, fiveColors)).toBe('#00CCFF');
    expect(getColorPercentage(90, fiveColors)).toBe('#0066FF');
  });
});

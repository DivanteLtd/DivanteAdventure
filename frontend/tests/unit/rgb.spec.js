import RGB from '../../src/util/rgb';

/**
 * @param {number} min (included)
 * @param {number} max (included)
 * @returns {number}
 */
function rand(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

describe('RGB class', () => {
  test('Red passed correctly', () => {
    const red = rand(0, 255);
    const green = rand(0, 255);
    const blue = rand(0, 255);
    const rgb = new RGB(red, green, blue);
    expect(rgb.getRed()).toBe(red);
  });

  test('Green passed correctly', () => {
    const red = rand(0, 255);
    const green = rand(0, 255);
    const blue = rand(0, 255);
    const rgb = new RGB(red, green, blue);
    expect(rgb.getGreen()).toBe(green);
  });

  test('Blue passed correctly', () => {
    const red = rand(0, 255);
    const green = rand(0, 255);
    const blue = rand(0, 255);
    const rgb = new RGB(red, green, blue);
    expect(rgb.getBlue()).toBe(blue);
  });

  test('Converting RGB to hex', () => {
    const red = 0x34;
    const green = 0xF9;
    const blue = 0x1C;
    const rgb = new RGB(red, green, blue);
    expect(rgb.toHex().toUpperCase()).toBe('#34F91C'.toUpperCase());
  });

  test('Converting hex to RGB', () => {
    const hex = '#C19F43';
    const rgb = RGB.fromHex(hex);
    expect(rgb.getRed()).toBe(0xC1);
    expect(rgb.getGreen()).toBe(0x9F);
    expect(rgb.getBlue()).toBe(0x43);
  });

  test('Adding two RGBs', () => {
    const a = new RGB(rand(0, 255), rand(0, 255), rand(0, 255));
    const b = new RGB(rand(0, 255), rand(0, 255), rand(0, 255));
    const expectedResult = new RGB(
        a.getRed() + b.getRed(),
        a.getGreen() + b.getGreen(),
        a.getBlue() + b.getBlue(),
    );
    const result = a.add(b);
    expect(result.getRed()).toBe(expectedResult.getRed());
    expect(result.getGreen()).toBe(expectedResult.getGreen());
    expect(result.getBlue()).toBe(expectedResult.getBlue());
  });

  test('Subtracting two RGBs', () => {
    const a = new RGB(rand(0, 255), rand(0, 255), rand(0, 255));
    const b = new RGB(rand(0, 255), rand(0, 255), rand(0, 255));
    const expectedResult = new RGB(
        a.getRed() - b.getRed(),
        a.getGreen() - b.getGreen(),
        a.getBlue() - b.getBlue(),
    );
    const result = a.subtract(b);
    expect(result.getRed()).toBe(expectedResult.getRed());
    expect(result.getGreen()).toBe(expectedResult.getGreen());
    expect(result.getBlue()).toBe(expectedResult.getBlue());
  });

  test('Multiplying RGB by scalar', () => {
    const a = new RGB(rand(0, 255), rand(0, 255), rand(0, 255));
    const scalar = rand(5, 100);
    const expectedResult = new RGB(
        a.getRed() * scalar,
        a.getGreen() * scalar,
        a.getBlue() * scalar
    );
    const result = a.multiplyByScalar(scalar);
    expect(result.getRed()).toBe(expectedResult.getRed());
    expect(result.getGreen()).toBe(expectedResult.getGreen());
    expect(result.getBlue()).toBe(expectedResult.getBlue());
  });
});

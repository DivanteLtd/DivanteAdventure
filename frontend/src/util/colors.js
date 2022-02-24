import RGB from './rgb';

/**
 * @param {string} otherHexColor
 * @returns {string}
 */
export function getHexContractColor(otherHexColor) {
  return RGB.fromHex(otherHexColor).toContractColor().toHex();
}

/**
 * @param {number} percentage from 0 to 100
 * @param {string[]} colorsArray hex colors
 * @returns {string} hex color
 */
export function getColorPercentage(percentage, colorsArray) {
  // first checks
  if (colorsArray.length === 0) {
    return '#FFFFFF';
  }
  if (colorsArray.length === 1) {
    return colorsArray[0];
  }
  // percentage must be between 0 and 100
  if (percentage < 0) {
    percentage = 0;
  } else if (percentage > 100) {
    percentage = 100;
  }

  const diff = 100 / (colorsArray.length - 1);
  let previousColorIndex = 0;
  let previousColorPercentageVal = 0;
  let nextColorIndex = 1;
  let nextColorPercentageVal = diff;

  while (percentage >= nextColorPercentageVal) {
    previousColorIndex = nextColorIndex;
    previousColorPercentageVal = nextColorPercentageVal;
    nextColorIndex++;
    nextColorPercentageVal += diff;
  }

  // Percentage lies on on one of the supplied colors
  if (percentage === previousColorPercentageVal) {
    return colorsArray[previousColorIndex];
  }

  // Calculating percentage relative to previous and next color percentages, i.e. if previous color percentage is
  // 50%, next color percentage is 75% and supplied percentage is 60%, than this supplied percentage divides area between
  // 50% and 75% on a value of 40%.

  nextColorPercentageVal -= previousColorPercentageVal;
  percentage -= previousColorPercentageVal;
  percentage *= (100 / nextColorPercentageVal);

  // divide hex colors into base RGB elements.
  const previousColorRgb = RGB.fromHex(colorsArray[previousColorIndex]);
  const nextColorRgb = RGB.fromHex(colorsArray[nextColorIndex]);
  const colorsDiff = nextColorRgb.subtract(previousColorRgb).multiplyByScalar(percentage / 100);
  return previousColorRgb.add(colorsDiff).toHex();
}

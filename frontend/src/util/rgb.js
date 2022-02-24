export default class RGB {
  /**
   * @param {number} red
   * @param {number} green
   * @param {number} blue
   */
  constructor(red, green, blue) {
    this._r = red;
    this._g = green;
    this._b = blue;
  }

  /** @returns {number} */
  getRed() {
    return this._r;
  }

  /** @returns {number} */
  getGreen() {
    return this._g;
  }

  /** @returns {number} */
  getBlue() {
    return this._b;
  }

  /** @returns {string} */
  toHex() {
    function componentToHex(c) {
      c = Math.round(c);
      if (c < 0) {
        c = 0;
      } else if (c > 255) {
        c = 255;
      }
      const hex = c.toString(16);
      return hex.length === 1 ? `0${hex}` : hex;
    }
    const redHex = componentToHex(this.getRed());
    const greenHex = componentToHex(this.getGreen());
    const blueHex = componentToHex(this.getBlue());
    return `#${redHex}${greenHex}${blueHex}`.toUpperCase();
  }

  /**
   * @param {RGB} rgb
   * @returns {RGB}
   */
  add(rgb) {
    return new RGB(
        this.getRed() + rgb.getRed(),
        this.getGreen() + rgb.getGreen(),
        this.getBlue() + rgb.getBlue(),
    );
  }

  /**
   * @param {RGB} rgb
   * @returns {RGB}
   */
  subtract(rgb) {
    return new RGB(
        this.getRed() - rgb.getRed(),
        this.getGreen() - rgb.getGreen(),
        this.getBlue() - rgb.getBlue(),
    );
  }

  /**
   * @param {number} scalar
   * @returns {RGB}
   */
  multiplyByScalar(scalar) {
    return new RGB(
        this.getRed() * scalar,
        this.getGreen() * scalar,
        this.getBlue() * scalar,
    );
  }

  /** @returns {RGB} */
  toContractColor() {
    const luminance = (0.299 * this.getRed() + 0.587 * this.getGreen() + 0.114 * this.getBlue()) / 255;
    return luminance > 0.5 ? new RGB(0, 0, 0) : new RGB(255, 255, 255);
  }

  /**
   * @param {string} hex
   * @returns {?RGB}
   */
  static fromHex(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex.toLowerCase());
    if (result) {
      return new RGB(
        parseInt(result[1], 16),
        parseInt(result[2], 16),
        parseInt(result[3], 16),
      );
    }
    return null;
  }
}

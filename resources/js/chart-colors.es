(global => {
  // polyfill
  if (!Number.isInteger) {
    Number.isInteger = v => typeof v === 'number' && isFinite(v) && Math.floor(v) === v;
  }

  // polyfill : substr negative index
  if ('ab'.substr(-1) !== 'b') {
    // eslint-disable-next-line no-extend-native
    String.prototype.substr = (function (substr) {
      return function (start, length) {
        return substr.call(
          this,
          start < 0 ? this.length + start : start,
          length
        );
      };
    }(String.prototype.substr));
  }

  const rgb = function (r, g, b) {
    const conv = function (val) {
      if (!Number.isInteger(val)) {
        throw new TypeError();
      }
      if (val < 0 || val > 255) {
        throw new RangeError();
      }
      return ('0' + val.toString(16)).substr(-2);
    };
    return `#${conv(r)}${conv(g)}${conv(b)}`;
  };

  // https://jfly.uni-koeln.de/colorset/CUD_color_set_GuideBook_2018.pdf
  const COLORS = [
    rgb(0, 90, 255), // 青
    rgb(246, 170, 0), // オレンジ
    rgb(119, 217, 168), // 明るい緑
    rgb(255, 241, 0), // 黄
    rgb(191, 228, 255), // 明るい空色
    rgb(128, 64, 0) // 茶色
  ];

  const PATTERNS = [
    'line-vertical',
    'line',
    'zigzag-vertical',
    'zigzag',
    'plus',
    'cross',
    'dot',
    'ring',
    'disc',
    'diamond-box',
    'diamond',
    'triangle',
    'box',
    'square',
    'dash'
  ];

  global.getChartColor = function (index) {
    if (!Number.isInteger(index)) {
      throw new TypeError();
    }

    if (index < 0) {
      throw new RangeError();
    }

    // console.log({
    //   pattern: PATTERNS[index % PATTERNS.length],
    //   color: COLORS[index % COLORS.length]
    // });

    return global.pattern.draw(
      PATTERNS[index % PATTERNS.length],
      COLORS[index % COLORS.length]
    );
  };

  global.getChartColors = function (num) {
    if (!Number.isInteger(num)) {
      throw new TypeError();
    }

    if (num < 0) {
      throw new RangeError();
    }

    const results = [];
    for (let i = 0; i < num; ++i) {
      results.push(global.getChartColor(i));
    }

    return results;
  };
})(window);

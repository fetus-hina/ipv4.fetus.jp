if (!String.prototype.trim) {
  // eslint-disable-next-line no-extend-native
  String.prototype.trim = function () {
    this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
  };
}

(($, global) => {
  const { Chart, getChartColors } = global;

  const asciiToRegionalIndicator = ascii => String.fromCodePoint(0x1F1E6 - 0x61 + ascii.charCodeAt(0));

  const ccSymbol = (cc) => {
    if (!String.fromCodePoint) {
      return '';
    }

    cc = String(cc);
    if (!cc.match(/^[a-wyz][a-z]$/u)) {
      return '';
    }

    return asciiToRegionalIndicator(cc.substr(0, 1)) + asciiToRegionalIndicator(cc.substr(1, 1));
  };

  const convertJsonToData = (json) => {
    const results = {
      datasets: [{
        backgroundColor: getChartColors(json.length),
        data: json.map(v => v.rate)
      }],
      labels: json.map(v => `${ccSymbol(v.cc)} ${v.name}`.trim())
    };
    return results;
  };

  const getLocale = ($element) => {
    const lang = $element.closest('[lang]').attr('lang');
    return lang && String(lang).match(/^[a-z]{2,}-[A-Z]{2,}/u)
      ? lang
      : null;
  };

  $.fn.chartAllocationSummary = function () {
    const elements = this;
    $.ajax('/api/allocation-summary', { method: 'GET' }).then(json => {
      const config = {
        data: convertJsonToData(json),
        options: {
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  const fmt = new Intl.NumberFormat(
                    getLocale($(elements)) ?? 'ja-JP',
                    {
                      maximumFractionDigits: 5,
                      minimumFractionDigits: 5,
                      style: 'percent'
                    }
                  );
                  return `${context.label} (${fmt.format(context.raw)})`;
                }
              }
            }
          },
          responsive: true
        },
        type: 'doughnut'
      };

      elements.each(function () {
        const $element = $(this);
        $element.data(
          'chart-js',
          new Chart($element, config)
        );
      });
    });
    return elements;
  };
})(jQuery, window);

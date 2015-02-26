// ------------------------------------------------------------
//  LEGEND PLUGIN FOR JQPLOT
//  Render legend on canvas
// ------------------------------------------------------------

(function ($) {

    $.jqplot.CanvasLegendRenderer = function (options) {
        // canvas for the legend
        this.legendCanvas = null;
        // is it a legend for a single metric only (pie chart)?
        this.singleMetric = false;
        // render the legend?
        this.show = false;

        $.extend(true, this, options);
    };

    $.jqplot.CanvasLegendRenderer.init = function (target, data, opts) {
        // add plugin as an attribute to the plot
        var options = opts || {};
        this.plugins.canvasLegend = new $.jqplot.CanvasLegendRenderer(options.canvasLegend);

        // add padding above the grid
        // legend will be put there
        if (this.plugins.canvasLegend.show) {
            options.gridPadding = {
                top: 21
            };
        }

    };

    // render the legend
    $.jqplot.CanvasLegendRenderer.postDraw = function () {
        var plot = this;
        var legend = plot.plugins.canvasLegend;

        if (!legend.show) {
            return;
        }

        // initialize legend canvas
        var padding = {top: 0, right: this._gridPadding.right, bottom: 0, left: this._gridPadding.left};
        var dimensions = {width: this._plotDimensions.width, height: this._gridPadding.top};
        var width = this._plotDimensions.width - this._gridPadding.left - this._gridPadding.right;

        legend.legendCanvas = new $.jqplot.GenericCanvas();
        this.eventCanvas._elem.before(legend.legendCanvas.createElement(
            padding, 'jqplot-legend-canvas', dimensions, plot));
        legend.legendCanvas.setContext();

        var ctx = legend.legendCanvas._ctx;
        ctx.save();
        ctx.font = '11px ';

        // render series names
        var x = 0;
        var series = plot.legend._series;
        for (var i = 0; i < series.length; i++) {
            var s = series[i];
            var label;
            if (legend.labels && legend.labels[i]) {
                label = legend.labels[i];
            } else {
                label = s.label.toString();
            }

            ctx.fillStyle = s.color;
            if (legend.singleMetric) {
                ctx.fillStyle = legend.singleMetricColor;
            }

            ctx.fillRect(x, 10, 10, 2);
            x += 15;

            var nextX = x + ctx.measureText(label).width + 20;

            if (nextX + 70 > width) {
                ctx.fillText("[...]", x, 15);
                x += ctx.measureText("[...]").width + 20;
                break;
            }

            ctx.fillText(label, x, 15);
            x = nextX;
        }

        legend.width = x;

        ctx.restore();
    };

    $.jqplot.preInitHooks.push($.jqplot.CanvasLegendRenderer.init);
    $.jqplot.postDrawHooks.push($.jqplot.CanvasLegendRenderer.postDraw);

})(jQuery);
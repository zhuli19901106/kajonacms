// ------------------------------------------------------------
//  PIE CHART LEGEND PLUGIN FOR JQPLOT
//  Render legend inside the pie graph
// ------------------------------------------------------------

(function ($) {

    $.jqplot.PieLegend = function () {
    };


    $.jqplot.PieLegend.prototype.init = function(options) {

        // canvas for the legend
        this.pieLegendCanvas = null;

        // render the legend?
        this.show = false;

        $.extend(true, this, options);
    };

    // called with scope of plot
    function preInit(target, data, options) {
        if(isPieBeingRendered(options)) {
            options.legend.renderer = $.jqplot.PieLegend;
        }
    };

    $.jqplot.PieLegend.prototype.draw = function() {

    };

    $.jqplot.PieLegend.prototype.pack = function() {

    };


    var isPieBeingRendered = function(options) {

        var setopts = false;
        if (options.seriesDefaults.renderer == $.jqplot.PieRenderer) {
            setopts = true;
        }
        else if (options.series) {
            for (var i=0; i < options.series.length; i++) {
                if (options.series[i].renderer == $.jqplot.PieRenderer) {
                    setopts = true;
                }
            }
        }

        return setopts;
    }

    // called with scope of plot
    var postDrawPieLegend = function() {
        debugger;
        var plot = this;
        var legend = plot.options.legend;

        if (!legend.show || !isPieBeingRendered(plot.options)) {
            return;
        }

        var series = plot.series[0];
        var angles = series._sliceAngles;
        var radius = series._diameter / 2;
        var center = series._center;
        var colors = this.seriesColors;

        // concentric line angles
        var lineAngles = [];
        for (var i = 0; i < angles.length; i++) {
            lineAngles.push((angles[i][0] + angles[i][1]) / 2 + Math.PI / 2);
        }

        // labels
        var labels = [];
        var data = series._plotData;
        for (i = 0; i < data.length; i++) {
            labels.push(data[i][0]);
        }

        // initialize legend canvas
        legend.pieLegendCanvas = new $.jqplot.GenericCanvas();
        plot.series[0].canvas._elem.before(legend.pieLegendCanvas.createElement(
            plot._gridPadding, 'jqplot-pie-legend-canvas', plot._plotDimensions, plot));
        legend.pieLegendCanvas.setContext();

        var ctx = legend.pieLegendCanvas._ctx;
        ctx.save();

        ctx.font = '11px ';

        // render labels
        var height = legend.pieLegendCanvas._elem.height();
        var x1, x2, y1, y2, lastY2 = false, right, lastRight = false;
        for (i = 0; i < labels.length; i++) {
            var label = labels[i];

            ctx.strokeStyle = colors[i % colors.length];
            ctx.lineCap = 'round';
            ctx.lineWidth = 1;

            // concentric line
            x1 = center[0] + Math.sin(lineAngles[i]) * (radius);
            y1 = center[1] - Math.cos(lineAngles[i]) * (radius);

            x2 = center[0] + Math.sin(lineAngles[i]) * (radius + 7);
            y2 = center[1] - Math.cos(lineAngles[i]) * (radius + 7);

            right = x2 > center[0];

            // move close labels
            if (lastY2 !== false && lastRight == right && (
                (right && y2 - lastY2 < 13) ||
                (!right && lastY2 - y2 < 13))) {

                if (x1 > center[0]) {
                    // move down if the label is in the right half of the graph
                    y2 = lastY2 + 13;
                } else {
                    // move up if in left halt
                    y2 = lastY2 - 13;
                }
            }

            if (y2 < 4 || y2 + 4 > height) {
                continue;
            }

            ctx.beginPath();
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);

            ctx.closePath();
            ctx.stroke();

            // horizontal line
            ctx.beginPath();
            ctx.moveTo(x2, y2);
            if (right) {
                ctx.lineTo(x2 + 5, y2);
            } else {
                ctx.lineTo(x2 - 5, y2);
            }

            ctx.closePath();
            ctx.stroke();

            lastY2 = y2;
            lastRight = right;

            // text
            if (right) {
                var x = x2 + 9;
            } else {
                var x = x2 - 9 - ctx.measureText(label).width;
            }

            ctx.fillStyle = legend.labelColor;
            ctx.fillText(label, x, y2 + 3);
        }

        ctx.restore();
    };


    $.jqplot.preInitHooks.push(preInit);
    $.jqplot.postDrawHooks.push(postDrawPieLegend);

})(jQuery);
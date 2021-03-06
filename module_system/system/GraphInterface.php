<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\System;

/**
 * Interface for all chart-engines.
 * Concrete instances may be returned by GraphFactory.
 * This interface defines only the least subset of methods, so each implementation may
 * provide additional methods.
 *
 * @author sidler@mulchprod.de
 * @since 3.4
 * @see GraphFactory
 * @package module_system
 */
interface GraphInterface
{

    /**
     * Used to create a bar-chart.
     * For each set of bar-values you can call this method once.
     * This means, calling this method twice creates a grouped bar chart
     * A sample-code could be:
     *  $objGraph = new Graph();
     *  $objGraph->setStrXAxisTitle("x-axis");
     *  $objGraph->setStrYAxisTitle("y-axis");
     *  $objGraph->setStrGraphTitle("Test Graph");
     *
     *  //simple array
     *      $objGraph->addBarChartSet(array(1,2,4,5) "serie 1");
     *
     * //datapoints array
     *      $objDataPoint1 = new GraphDatapoint(1);
     *      $objDataPoint2 = new GraphDatapoint(2);
     *      $objDataPoint3 = new GraphDatapoint(4);
     *      $objDataPoint4 = new GraphDatapoint(5);
     *
     *      //set action handler example
     *      $objDataPoint1->setObjActionHandler("<javascript code here>");
     *      $objDataPoint1->getObjActionHandlerValue("<value_object> e.g. some json");
     *
     *      $objGraph->addBarChartSet(array($objDataPoint1, $objDataPoint2, $objDataPoint3, $objDataPoint4) "serie 1");
     *
     *
     * @param array $arrValues - an array with simple values or an array of data points (GraphDatapoint).
     *                           The advantage of a data points are that action handlers can be defined for each data point which will be executed when clicking on the data point in the chart.
     * @param string $strLegend
     * @param bool $bitWriteValues Enables the rendering of values on top of the graphs
     */
    public function addBarChartSet($arrValues, $strLegend, $bitWriteValues = false);

    /**
     * Used to create a stacked bar-chart.
     * For each set of bar-values you can call this method once.
     * A sample-code could be:
     *  $objGraph = new Graph();
     *  $objGraph->setStrXAxisTitle("x-axis");
     *  $objGraph->setStrYAxisTitle("y-axis");
     *  $objGraph->setStrGraphTitle("Test Graph");
     *
     *
     *  //simple array
     *      $objGraph->addStackedBarChartSet(array(1,2,4,5) "serie 1");
     *      $objGraph->addStackedBarChartSet(array(1,2,4,5) "serie 2");
     *
     * //datapoints array
     *      $objDataPoint1 = new GraphDatapoint(1);
     *      $objDataPoint2 = new GraphDatapoint(2);
     *      $objDataPoint3 = new GraphDatapoint(4);
     *      $objDataPoint4 = new GraphDatapoint(5);
     *
     *      //set action handler example
     *      $objDataPoint1->setObjActionHandler("<javascript code here>");
     *      $objDataPoint1->getObjActionHandlerValue("<value_object> e.g. some json");
     *
     *      $objGraph->addStackedBarChartSet(array($objDataPoint1, $objDataPoint2, $objDataPoint3, $objDataPoint4) "serie 1");
     *
     *
     * @param array $arrValues - an array with simple values or an array of data points (GraphDatapoint).
     *                           The advantage of a data points are that action handlers can be defined for each data point which will be executed when clicking on the data point in the chart.
     * @param string $strLegend
     */
    public function addStackedBarChartSet($arrValues, $strLegend);

    /**
     * Registers a new plot to the current graph. Works in line-plot-mode only.
     * Add a set of linePlot to a graph to get more then one line.
     * If you created a bar-chart before, it it is possible to add line-plots on top of
     * the bars. Nevertheless, the scale is calculated out of the bars, so make
     * sure to remain inside the visible range!
     * A sample-code could be:
     *  $objGraph = new Graph();
     *  $objGraph->setStrXAxisTitle("x-axis");
     *  $objGraph->setStrYAxisTitle("y-axis");
     *  $objGraph->setStrGraphTitle("Test Graph");
     *
     *  //simple array
     *      $objGraph->addLinePlot(array(1,4,6,7,4), "serie 1");
     *
     * //datapoints array
     *      $objDataPoint1 = new GraphDatapoint(1);
     *      $objDataPoint2 = new GraphDatapoint(2);
     *      $objDataPoint3 = new GraphDatapoint(4);
     *      $objDataPoint4 = new GraphDatapoint(5);
     *
     *      //set action handler example
     *      $objDataPoint1->setObjActionHandler("<javascript code here>");
     *      $objDataPoint1->getObjActionHandlerValue("<value_object> e.g. some json");
     *
     *      $objGraph->addLinePlot(array($objDataPoint1, $objDataPoint2, $objDataPoint3, $objDataPoint4) "serie 1");
     *
     *
     * @param array $arrValues - an array with simple values or an array of data points (GraphDatapoint).
     *                           The advantage of a data points are that action handlers can be defined for each data point which will be executed when clicking on the data point in the chart.
     * @param string $strLegend the name of the single plot
     */
    public function addLinePlot($arrValues, $strLegend);

    /**
     * Creates a new pie-chart. Pass the values as the first param. If
     * you want to use a legend and / or Colors use the second and third param.
     * Make sure the array have the same number of elements, ohterwise they won't
     * be uses.
     * A sample-code could be:
     *  $objChart = new Graph();
     *  $objChart->setStrGraphTitle("Test Pie Chart");
     *
     * //simple array
     *      $objChart->createPieChart(array(2,6,7,3), array("val 1", "val 2", "val 3", "val 4"));
     *
     * //datapoints array
     *      $objDataPoint1 = new GraphDatapoint(1);
     *      $objDataPoint2 = new GraphDatapoint(2);
     *      $objDataPoint3 = new GraphDatapoint(4);
     *      $objDataPoint4 = new GraphDatapoint(5);
     *
     *      //set action handler example
     *      $objDataPoint1->setObjActionHandler("<javascript code here>");
     *      $objDataPoint1->getObjActionHandlerValue("<value_object> e.g. some json");
     *
     *      $objGraph->createPieChart(array($objDataPoint1, $objDataPoint2, $objDataPoint3, $objDataPoint4) , array("val 1", "val 2", "val 3", "val 4"), "serie 1");
     *
     *
     * @param array $arrValues - an array with simple values or an array of data points (GraphDatapoint).
     *                           The advantage of a data points are that action handlers can be defined for each data point which will be executed when clicking on the data point in the chart.
     * @param array $arrLegends
     */
    public function createPieChart($arrValues, $arrLegends);

    /**
     * Does the magic. Creates all necessary stuff and finally
     * sends the graph directly (!!!) to the browser.
     * Execution should be terminated afterwards.
     * <b>Please note that not all chart-engines support this method.</b>
     *
     * @deprecated use GraphInterface::renderGraph() instead
     */
    public function showGraph();

    /**
     * Does the magic. Creates all necessary stuff and finally
     * saves the graph to the specified filename
     * <b>Please note that not all chart-engines support this method.</b>
     *
     * @deprecated use GraphInterface::renderGraph() instead
     */
    public function saveGraph($strFilename);

    /**
     * Common way to get a chart. The engine should save the chart
     * to the filesystem (if required) and returns the chart with a complete
     * code to embed the chart into a html-page.
     * Please be aware that the method may return a large amount of code depending on
     * the type of engine - from a simple img-tag up to a full js-logic.
     *
     * @since 4.0
     * @return mixed
     */
    public function renderGraph();

    /**
     * Set the title of the x-axis
     *
     * @param string $strTitle
     */
    public function setStrXAxisTitle($strTitle);

    /**
     * Set the title of the y-axis
     *
     * @param string $strTitle
     */
    public function setStrYAxisTitle($strTitle);

    /**
     * Set the title of the graph
     *
     * @param string $strTitle
     */
    public function setStrGraphTitle($strTitle);

    /**
     * Set the color of the margin-areas, so the color of the area not being
     * the plot-area.
     * In most cases this is the background.
     *
     * @param string $strColor in hex-values: #ccddee
     */
    public function setStrBackgroundColor($strColor);

    /**
     * Set the total width of the chart
     *
     * @param int $intWidth
     */
    public function setIntWidth($intWidth);

    /**
     * Set the total height of the chart
     *
     * @param int $intHeight
     */
    public function setIntHeight($intHeight);

    /**
     * Set the labels to be used for the x-axis.
     * Make sure to set them before adding datasets!
     *
     * @param array $arrXAxisTickLabels array of string to be used as labels
     * @param int $intNrOfWrittenLabels the amount of x-axis labels to be printed
     */
    public function setArrXAxisTickLabels($arrXAxisTickLabels, $intNrOfWrittenLabels = 12);


    /**
     * Sets if to render a legend or not
     *
     * @param bool $bitRenderLegend
     */
    public function setBitRenderLegend($bitRenderLegend);

    /**
     * Set the font to be used in the chart
     *
     * @param string $strFont
     */
    public function setStrFont($strFont);

    /**
     * Set the color of the fonts used in the chart
     *
     * @param string $strFontColor
     */
    public function setStrFontColor($strFontColor);


    /**
     * Sets the angle to be used for rendering the x-axis lables
     *
     * @param int $intXAxisAngle
     */
    public function setIntXAxisAngle($intXAxisAngle);


    /**
     * Setter for setting custom series colors.
     *
     * @param array $arrSeriesColors
     *
     * @return mixed
     */
    public function setArrSeriesColors($arrSeriesColors);


}

﻿/*
 * Copyright (c) 2016 The MITRE Corporation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

var ECharts_controller = (function () {
	'use strict';

	return {


		initialize: function () {

			var self = this;

			$('.echarts_div').each(function (element) {

				var option = "";

				eval("option = " + this.textContent);

				this.textContent = '';

				if (!option) {
					console.log("ECharts: the JSON could not be parsed. Make sure it starts and end with curly braces : { your json }");
				}

				console.log(option);

				$(this).show();

				var myChart = echarts.init(this);

				// Display the chart using the configuration items and data just specified.
				myChart.setOption(option);
			});


			$('.echarts_economical_div').each(function (element) {

				var stackedBarsOptions = "";
				const formatUtil = echarts.format;

				eval("stackedBarsOptions = " + this.textContent);
				stackedBarsOptions['tooltip'] =
				{
					formatter: function (info) {
						return info.seriesName + '<br>\n<b>' + info.value + ' €</b>';
					}
				};

				var drilldownData = "";

				this.textContent = '';

				// Retrieve the JSON data from the hidden HTML element
				var drilldownDataElement = document.getElementById('drilldownData_' + this.id.split('_')[1]);
				if (drilldownDataElement) {
					drilldownData = JSON.parse(drilldownDataElement.textContent);
				}

				if (!stackedBarsOptions) {
					console.log("ECharts: the JSON could not be parsed. Make sure it starts and end with curly braces : { your json }");
				}

				console.log(stackedBarsOptions);

				$(this).show();

				var myChart = echarts.init(this);

				// Display the chart using the configuration items and data just specified.
				myChart.setOption(stackedBarsOptions);

				myChart.on('click', (event) => {
					if (event.name) {
						if (undefined === event.data.groupId)
							return;

						var anneeToShow = event.name;
						var barreToShow = event.data.groupId;

						console.log("Showing treemap for " + barreToShow + " " + anneeToShow);

						var seriesToHide = [];
						myChart.getOption().series.forEach(function (series) {
							seriesToHide.push({
								id: series.id,
								data: []
							});
						});
						myChart.setOption({
							series: seriesToHide
						});

						var treeMapOptions = {
							animationDurationUpdate: 500,
							title: {
								text: "Détail des " + barreToShow.toLowerCase(),
								subtext: "Année " + anneeToShow,
								textAlign: 'center',
								left: '50%'
							},
							xAxis: { show: false },
							yAxis: { show: false },
							series: [drilldownData[anneeToShow][barreToShow]],
							tooltip: {
								formatter: function (info) {
									return info.name + ' : ' + formatUtil.addCommas(info.value) + ' €';
								}
							},
							label: {
								position: 'insideTopLeft',
								lineHeight: 17
							},
							graphic: [
								{
									type: 'text',
									left: 50,
									top: 20,
									style: {
										text: '< Retour',
										fontSize: 18
									},
									onclick: function () {
										myChart.setOption(stackedBarsOptions, true);
									}
								}
							]
						};

						console.log(treeMapOptions);
						myChart.setOption(treeMapOptions);
					}
				});
			});
		}


	}; // return line 26
}());

window.EChartsController = ECharts_controller;

(function () {
	$(document)
		.ready(function () {
			window.EChartsController.initialize();
		});
}());


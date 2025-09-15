/*
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
				let div = this;

				if (this.dataset.jsontitle) {
					let title = this.dataset.jsontitle;
					let jsonPageURL = '/wiki/' + encodeURIComponent(title);

					$.ajax({
						type: "GET",
						url: jsonPageURL,
						data: { "action": "raw" },
						dataType: 'json',
						success: function (jsondata) {

							if (jsondata.series == undefined) {
								// This is not a standard ECharts chart, but a rotation chart

								//Remove the height of the div
								$(div).removeAttr('style');
								$("#" + div.id + "_container").removeAttr('style');

								let renderer = new window.RotationRenderer(div.id, jsondata);
								renderer.render();
								$(div).show();

								// Add an edit button when the user is connected and has edit rights (wgIsProbablyEditable is the quick and dirty trick for that): 
								self.addEditButton(div.id, title);

							} else {
								self.buildChart(div, jsondata, title);
							}

						},
						error: function(xhr, error, e) {
							if (xhr.status == 404) {
								// The page may not exist yet - let's suggest to create it:
								self.addEditButton(div.id, title);
								if (mw.config.get('wgIsProbablyEditable')) {
									$(div).show();
									$(div).append(`<div style="text-align: center;margin: auto 10%;background-color: #CCC;height: 300px;padding-top: 135px;">
										<a target="_blank" href="/extensions/ECharts/editor/editor.html?wiki=${encodeURIComponent(title)}" class="mw-editsection-visualeditor" title="${mw.msg('echarts-edit-graph')}">${mw.msg('echarts-edit-missing-graph')}</a>
									</div>`);
								} else {
									// If the page is not editable just remove the graph from the DOM
									$("#" + div.id + "_container").remove();
								}
							}
						}
					});
				}
				else {
					eval("option = " + this.textContent);
					this.textContent = '';
					self.buildChart(div, option, "embedded");
				}
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

				if ($(this).width() > $( window ).width()) {
					$(this).css('width', '100%');
					$(this).parent().css('width', '100%');
				}
	
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
							legend: { show: false },
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
									"type": 'group',
									"left": 'center',
									"bottom": 45,
									"children": [
										{
											type: 'rect',
											z: 100,
											left: 'center',
											top: 'middle',
											shape: {
												width: 290,
												height: 25,
												r: [7]
											},
											style: {
												fill: '#fff',
												stroke: '#555',
												lineWidth: 1,
												shadowBlur: 8,
												shadowOffsetX: 3,
												shadowOffsetY: 3,
												shadowColor: 'rgba(0,0,0,0.2)'
											}
										},
										{
											type: 'text',
											z: 100,
											left: 'center',
											top: 'middle',
											style: {
												fill: '#333',
												width: 290,
												text: "🔙 Revenir au bilan économique",
												font: '14px Microsoft YaHei'
											}
										}
									],
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
		},

		buildChart: function (div, option, pageTitle) {
			if (!option) {
				console.log("ECharts: the JSON could not be parsed. Make sure it starts and end with curly braces : { your json }");
			}

			// Override some of the formatters with functions for enhanced functionality
			switch (option?.tooltip?.formatter) {
				case 'rotation':
					// Show in the tooltip the description that accompanies this item
					option.tooltip.extraCssText = "text-wrap: wrap;";
					option.tooltip.className = "rotation-tooltip";
					option.tooltip.formatter = (item) => {
						return item.marker + "<b>" + item.name + "</b><br>" + item.data.description;
					};
					break;

				case 'assolement':
					// Show in the tooltip: Colza : 32ha (19%)
					option.tooltip.formatter = (info) => {
						let value = info.value;
						let name = info.name;
						let percent = '(' + Math.round((100 * value) / info.treeAncestors[0].value) + '%)';
						return name + ' : ' + value + 'ha ' + percent;
					};
					break;

				default:
					break;
			}

			console.log("Chart stored in page " + pageTitle);
			console.log(option);

			if ($(div).width() > $( window ).width()) {
				$(div).css('width', '100%');
				$(div).parent().css('width', '100%');
			}

			$(div).show();

			var myChart = echarts.init(div);

			// Display the chart using the configuration items and data just specified.
			myChart.setOption(option);
		},

		addEditButton: function(divId, title) {
			if (mw.config.get('wgIsProbablyEditable')) {
				$("#" + divId + "_container").append(`<span class="mw-editsection">
					<span class="mw-editsection-bracket">[</span><a target="_blank" href="/extensions/ECharts/editor/editor.html?wiki=${encodeURIComponent(title)}" class="mw-editsection-visualeditor" title="${mw.msg('echarts-edit-graph')}">${mw.msg('echarts-edit-graph')}</a>
					<span class="mw-editsection-bracket">]</span></span>`);
			}	
		}
		
	}; // return line 26
}());

window.EChartsController = ECharts_controller;

(function () {
	$(document)
		.ready(function () {
			window.EChartsController.initialize();
			
			// resize all charts when the windows is resized
			$(window).on('resize', OO.ui.debounce(function() {
				$(".charts").each(function(){
					var id = $(this).attr('_echarts_instance_');
					window.echarts.getInstanceById(id).resize();
				});
			}, 500));
		});
}());


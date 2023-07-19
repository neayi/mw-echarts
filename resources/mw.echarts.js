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

				eval("option = " + this.textContent);

				this.textContent = '';

				if (!option) {
					console.log("ECharts: the JSON could not be parsed. Make sure it starts and end with curly braces : { your json }");
				}

				$(this).show();

				var myChart = echarts.init(this);

				// Display the chart using the configuration items and data just specified.
				myChart.setOption(option);
			});


			$('.echarts_economical_div').each(function (element) {

				var option = "";
				var drilldownData = "";

				eval(this.textContent);

				this.textContent = '';

				if (!option) {
					console.log("ECharts: the JSON could not be parsed. Make sure it starts and end with curly braces : { your json }");
				}

				$(this).show();

				var myChart = echarts.init(this);

				// Display the chart using the configuration items and data just specified.
				myChart.setOption(option);

				myChart.on('click', (event) => {
					if (event.data && event.data.groupId) {
						const subData = drilldownData.find((data) => data.dataGroupId === event.data.groupId);
						if (!subData)
							return;

						const treemapGroupedData = self.groupByType(subData.data);

						// Calculate the sum of values for each parent node in the subData
						const sumByParent = self.calculateSumByParent(subData.data);
						const treemapData = self.createTreemapData(treemapGroupedData, event.seriesName, sumByParent);

						// Set the treemap series configuration
						const treemapSeries = [
							{
								type: 'treemap',
								breadcrumb: { show: true },
								data: treemapData
							}
						];

						if (myChart.getOption().series[0].type === 'bar') {
							// Hide the bars of "groupId charges" chart
							myChart.setOption({
								series: [
									{
										id: 'produits',
										dataGroupId: '',
										data: []
									},
									{
										id: 'charges',
										dataGroupId: '',
										data: []
									}
								]
							});
						}

						myChart.setOption({
							animationDurationUpdate: 500,
							title: {
								text: "Détail des " + event.seriesName.toLowerCase(),
								subtext: "Année " + event.data.groupId,
								textAlign: 'center',
								left: '50%'
							},
							xAxis: { show: false },
							yAxis: { show: false },
							series: treemapSeries,
							tooltip: { show: false },
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
										myChart.setOption(option, true);
									}
								}
							]
						});
					}
				});
			});
		},

		// Create a function to group the data by 'typeDeDonnee'
		groupByType: function (data) {
			const groupedData = {};
			for (const item of data) {
				const { typeDeDonnee } = item;
				if (!(typeDeDonnee in groupedData)) {
					groupedData[typeDeDonnee] = [];
				}
				groupedData[typeDeDonnee].push(item);
			}
			return groupedData;
		},

		// Function to calculate the sum of values for each parent node in the drilldownData
		calculateSumByParent: function (data) {
			const sumByParent = {};
			for (const item of data) {
				const { typeDeDonnee, value } = item;
				if (!(typeDeDonnee in sumByParent)) {
					sumByParent[typeDeDonnee] = 0;
				}
				sumByParent[typeDeDonnee] += value[0];
			}
			return sumByParent;
		},

		// Create a function to fill in the treemapData
		createTreemapData: function (data, seriesName, sumByParent) {
			if (seriesName === "Produits") {
				const treemapData = [
					{
						name: 'Aides',
						itemStyle: { color: '#A4CC69', borderWidth: 1 },
						label: { show: true, position: 'inside' },
						children: data['Aides'].map((item) => ({
							name: `${item.name}\n${item.value[0]} €`,
							value: item.value
						}))
					},
					{
						name: "Détail Chiffre d'affaire",
						itemStyle: { color: '#88A8CB', borderWidth: 1 },
						label: { show: true, position: 'inside' },
						children: data["Détail Chiffre d'affaire"].map((item) => ({
							name: `${item.name}\n${item.value[0]} €`,
							value: item.value
						}))
					}
				];
				// Add the sum value to each parent node in treemapData
				const treemapDataWithSum = treemapData.map((item) => {
					const name = item.name.split('\n')[0]; // Extract the parent node name
					if (sumByParent[name]) {
						item.name = `${name}\n${sumByParent[name]} €`;
					}
					return item;
				});

				return treemapDataWithSum;
			}
			else {
				const treemapData = [
					{
						name: 'Charges de structure',
						itemStyle: { color: '#F5A893', borderWidth: 1 },
						label: { show: true, position: 'inside' },
						children: data['Charges de structure'].map((item) => ({
							name: `${item.name}\n${item.value[0]} €`,
							value: item.value
						}))
					},
					{
						name: 'EBE',
						itemStyle: { color: '#F8B26D', borderWidth: 1 },
						label: { show: true, position: 'inside' },
						children: data['EBE'].map((item) => ({
							name: `${item.name}\n${item.value[0]} €`,
							value: item.value
						}))
					},
					{
						name: 'Charges opérationnelles',
						itemStyle: { color: '#F28960', borderWidth: 1 },
						label: { show: true, position: 'inside' },
						children: data['Charges opérationnelles'].map((item) => ({
							name: `${item.name}\n${item.value[0]} €`,
							value: item.value
						}))
					},
					{
						name: 'Charges de personnels',
						itemStyle: { color: '#FDCF74', borderWidth: 1 },
						label: { show: true, position: 'inside' },
						children: data['Charges de personnels'].map((item) => ({
							name: `${item.name}\n${item.value[0]} €`,
							value: item.value
						}))
					}
				];
				// Add the sum value to each parent node in treemapData
				const treemapDataWithSum = treemapData.map((item) => {
					const name = item.name.split('\n')[0]; // Extract the parent node name
					if (sumByParent[name]) {
						item.name = `${name}\n${sumByParent[name]} €`;
					}
					return item;
				});

				return treemapDataWithSum;
			}
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


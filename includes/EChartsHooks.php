<?php
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

namespace MediaWiki\Extension\ECharts;

use OutputPage;
use Skin;
use Parser;

class EChartsHooks implements
	\MediaWiki\Hook\ParserFirstCallInitHook
{
	private static $id = 1;

	/**
	 * Register parser hooks to add the echarts keyword
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit
	 * @see https://www.mediawiki.org/wiki/Manual:Parser_functions
	 * @param Parser $parser
	 * @throws \MWException
	 */
	public function onParserFirstCallInit($parser)
	{

		// Add the following to a wiki page to see how it works:
		// {{#echarts: {some json} }}
		$parser->setFunctionHook('echarts', [self::class, 'parserFunctionEcharts']);

		// This parser function will show an economic chart on multiple years :
		// {{#economic_charts:
		// 	  Vente de produits végétaux 2016 = 7000
		// 	| Subvention 2016 = 30000
		// 	| Vente autres produits 2017 = 100
		// 	| Autres aides 2017 = 2000
		// 	| Fournitures diverses 2017 = 1500
		// 	| Bâches et voiles 2017 = 1000
		// 	| Produits de traitements 2017 = 80
		// 	| Terreau 2017 = 350
		// 	| Achat des légumes 2017 = 100
		// 	| Semences et plants 2017 = 2000
		// 	| Assurances 2017 = 600
		// 	| Certification 2017 = 300
		// 	| Frais de gestion 2017 = 800
		// 	| Eau, gaz, électricité 2017 = 1200
		// 	| Entretien matériel 2017 = 150
		// 	| Carburant 2017 = 200
		// 	| Cotisations exploitants 2017 = 1980
		// 	| EBE 2017 = 28840
		// 	| Prélèvements privés 2017 =
		// 	}}
		$parser->setFunctionHook('economic_charts', [self::class, 'parserFunctionEconomicCharts']);

		return true;
	}

	/**
	 * Parser function handler for {{#echarts: .. | .. }}
	 *
	 * @param Parser $parser
	 * @param string $value
	 * @param string ...$args
	 * @return string HTML to insert in the page.
	 */
	public static function parserFunctionEcharts(Parser $parser, string $value, ...$args)
	{
		$parser->getOutput()->addModules(['ext.mwecharts']);

		array_unshift($args, $value);

		$width = '100%';
		$height = '400px';
		$container_classes = '';

		$json_parts = array();

		// try to find a few specific parameters to the template call:
		foreach ($args as $k => $v) {
			$parts = explode('=', $v);

			$key = strtolower(trim($parts[0]));

			switch ($key) {
				case 'width':
					$width = $parts[1];
					break;

				case 'height':
					$height = $parts[1];
					break;

				case 'align':
					$container_classes .= "float-md-" . trim($parts[1]) . ' ';
					break;

				default:
					$json_parts[] = $v;
					break;
			}
		}

		$json = implode('|', $json_parts);

		$thisId = self::$id++;

		$ret = '<div id="echart_' . $thisId . '_container"  class="' . $container_classes . '" style="width:' . $width . '; height:' . $height . '"><div id="echart_' . $thisId . '" class="echarts_div" style="width:' . $width . '; height:' . $height . '; display:none;">' . $json . '</div></div>';

		return $ret;
	}

	/**
	 * Parser function handler for {{#economic_charts: .. | .. }}
	 *
	 * @param Parser $parser
	 * @param string $value
	 * @param string ...$args
	 * @return string HTML to insert in the page.
	 */
	public static function parserFunctionEconomicCharts(Parser $parser, string $value, ...$args)
	{
		$parser->getOutput()->addModules(['ext.mwecharts']);

		array_unshift($args, $value);

		$width = '100%';
		$height = '700px';

		$mainCategories = [
			"Chiffre d'affaire" => ['color' => '#afd095', 'stack' => "Produits"],
			"Aides" => ['color' => '#729fcf', 'stack' => "Produits"],

			"Charges opérationnelles" => ['color' => '#F28960', 'stack' => "Charges"],
			"Charges de structure" => ['color' => '#F5A893', 'stack' => "Charges"],
			"Charges de personnel" => ['color' => '#FDCF74', 'stack' => "Charges"],
			"EBE" => ['color' => '#F8B26D', 'stack' => "Charges"],
//			"Prélèvements privés" => ['color' => '#DB8129', 'stack' => "Charges"],
		];

		// Define an array of valid parameters for "Produits" bar
		$parametersDefinition['Produits'] = [
			"Aides" => "Aides",
			"Autres aides" => "Aides",
			"Aide à la certification" => "Aides",
			"DPU, DPB" => "Aides",
			"Subvention" => "Aides",
			"Subventions" => "Aides",

			"Chiffre d'affaire" => "Chiffre d'affaire",
			"Vente autres produits" => "Chiffre d'affaire",
			"Vente de produits végétaux" => "Chiffre d'affaire",
			"Vente de marchandises (achat-revente)" => "Chiffre d'affaire",
			"Vente de marchandises" => "Chiffre d'affaire",
			"Produits financiers" => "Chiffre d'affaire",
		];

		// Define an array of valid parameters for "Charges" bar
		$parametersDefinition['Charges'] = [
			"Prélèvements privés" => "EBE",
			"EBE" => "EBE",
			"Salariés" => "Charges de personnel",
			"Cotisations salariés" => "Charges de personnel",
			"Cotisations exploitants" => "Charges de personnel",
			"Carburant" => "Charges de structure",
			"Entretien matériel" => "Charges de structure",
			"Eau, gaz, électricité" => "Charges de structure",
			"Frais de gestion" => "Charges de structure",
			"Certification" => "Charges de structure",
			"Fermage" => "Charges de structure",
			"Assurances" => "Charges de structure",
			"Autres" => "Charges de structure",
			"Fournitures diverses" => "Charges opérationnelles",
			"Travaux par tiers" => "Charges opérationnelles",
			"Bâches et voiles" => "Charges opérationnelles",
			"Produits de traitements" => "Charges opérationnelles",
			"Terreau" => "Charges opérationnelles",
			"Achat des légumes (revente)" => "Charges opérationnelles",
			"Achat des légumes" => "Charges opérationnelles",
			"Fertilisation (MO)" => "Charges opérationnelles",
			"Fertilisation" => "Charges opérationnelles",
			"Semences et plants" => "Charges opérationnelles",
		];

		// Build an array of valid paramters:
		$validParameters = array();
		foreach ($parametersDefinition as $parametersList)
			foreach ($parametersList as $k => $v)
				$validParameters[strtolower($k)] = $k;

		// try to find a few specific parameters to the template call:
		foreach ($args as $k => $v) {
			$parts = explode('=', $v);
			$key = trim($parts[0]);

			$paramYearParts = explode(' ', $key);
			$param = $paramYearParts[0];

			switch (strtolower($param)) {
				case 'width':
					$width = $parts[1];
					if (strpos($width, 'px') === false)
						$width .= 'px';
					break;

				case 'height':
					$height = $parts[1];
					if (strpos($height, 'px') === false)
						$height .= 'px';
					break;

				default:
					$matches = array();
					if (preg_match('@^(.*) +([0-9]{4})@', $key, $matches)) {
						$param = strtolower(trim($matches[1]));
						$year = $matches[2];

						if (!isset($validParameters[$param]))
						{
							$ret = "<pre>Ce paramètre n'est pas reconnu : '''$key'''\n\n";
							$ret .= "Les paramètres doivent faire partie de la liste suivante :\n";
							$ret .= "* " . implode("\n* ", $validParameters) . "\n</pre>";
							return $ret;
						}

						// Also replace commas with dots for the sake of json
						$parameters[$year][$validParameters[$param]] = (float)trim(str_replace(',', '.', $parts[1]));
					}
					break;
			}
		}

		ksort($parameters);

		$thisId = self::$id++;

		// Build the series:
		$stackBarSeries = [];

		// Loop through each year's data in $parameters
		$subSums = [];
		foreach ($parameters as $year => $data) {

			// Loop through each parameter's data for the current year
			foreach ($data as $param => $value) {
				$subCategory = $parametersDefinition['Charges'][$param] ?? $parametersDefinition['Produits'][$param];

				if (!isset($subSums[$year][$subCategory]))
					$subSums[$year][$subCategory] = 0;

				$subSums[$year][$subCategory] += (float)$value;
			}
		}

		foreach ($mainCategories as $name => $aCategory)
		{
			$series = [
				"type" => "bar",
				'itemStyle' => [
					'color' => $aCategory['color']
				],
				'label' => [
					'show' => true,
					'position' => 'inside',
					'formatter' => '{c} €'
				],
      			'emphasis'=> [
					'focus'=> 'series'
					],
				"name" => $name,
				"stack" => $aCategory['stack'],
				"id" => $name
			];

			$values = [];
			foreach ($parameters as $year => $v) {
				if (isset($subSums[$year][$name]))
					$values[] = ['groupId' => $aCategory['stack'], 'value' => $subSums[$year][$name]];
				else
					$values[] = ['groupId' => $aCategory['stack'], 'value' => 0];
			}

			$series['data'] = $values;

			$stackBarSeries[] = $series;
		}

		$emptyTooltip = (object) [];
		$option = [
			"tooltip" => $emptyTooltip,
			"title" => [
				"text" => "Évolution du bilan financier",
				"subtext" => "Cliquer sur une barre pour voir le détail",
				"textAlign" => "center",
				"left" => "50%"
			],
			"xAxis" => [
				"type" => "category",
				"data" => array_map('strval', array_keys($parameters)),
			],
			"yAxis" => [
				"type" => "value",
				"axisLabel" => [
					"formatter" => "{value} €"
				]
			],
			'grid' => [ 'containLabel' => true ],
			"series" => $stackBarSeries
		];

		// Now build the drilldown data (for the treemap)
		$drilldownData = [];

		// Loop through each year's data in $parameters
		foreach ($parameters as $year => $data) {

			$drilldownData[$year]['Produits'] = self::getTreeMapSeries("Produits $year");
			$drilldownData[$year]['Charges'] = self::getTreeMapSeries("Charges $year");

			foreach ($mainCategories as $name => $aCategory) {

				foreach ($parametersDefinition as $l1Category => $paramsDefs)
				{
					if ($aCategory['stack'] == $l1Category)
					{
						$treeMapSubCategory = null;

						foreach ($paramsDefs as $paramName => $mainCategory) {
							if ($mainCategory != $name)
								continue;

							if (!empty($parameters[$year][$paramName]))
							{
								if (!$treeMapSubCategory)
									$treeMapSubCategory = self::getTreeMapItem($name, 0, $aCategory['color'], 1); // For Aides

								$treeMapSubCategory['children'][] = self::getTreeMapItem($paramName, $parameters[$year][$paramName]);
								$treeMapSubCategory['value'] += $parameters[$year][$paramName];
							}

						}

						if ($treeMapSubCategory)
						{
							self::addValueToName($treeMapSubCategory);

							$drilldownData[$year][$l1Category]['data'][] = $treeMapSubCategory;
							$drilldownData[$year][$l1Category]['value'] += $treeMapSubCategory['value'];
						}
					}
				}
			}

			self::addValueToName($drilldownData[$year]['Produits']);
			self::addValueToName($drilldownData[$year]['Charges']);
		}

		// Add a last check on the consistency of the data:
		foreach ($drilldownData as $year => $data)
		{
			$ratio = $data['Produits']['value'] / $data['Charges']['value'];
			if ($ratio < 0.9 || $ratio > 1.1)
			{
				$ret = "<pre>Attention, vos données pour $year ne sont pas équilibrées entre les produits et les charges.<br>NB : Si vous avez spécifié les prélévements privés, il faut les déduire de l'EBE !</pre>";
				return $ret;
			}
		}


		// Convert the updated $option array to JSON format
		$JS = json_encode($option, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		$drilldownDataJSON = json_encode($drilldownData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		$ret = '<div id="echart_' . $thisId . '_container"  style="width:' . $width . '; height:' . $height . '"><div id="echart_' . $thisId . '" class="echarts_economical_div" style="width:' . $width . '; height:' . $height . '; display:none;">' . $JS . '</div></div>';
		$ret .= '<div id="drilldownData_' . $thisId . '" style="display:none;">' . htmlentities($drilldownDataJSON) . '</div>';

		return [$ret, 'noparse' => true, 'isHTML' => true];
	}

	private static function getTreeMapSeries($name)
	{
		$item = [
			'name' => $name,
			'value' => 0,
			'type' =>'treemap',
			'breadcrumb' => [
				'height' => 34,
				'itemStyle'=> [ 'textStyle'=> [ 'lineHeight'=> 15 ] ]
			],
      		'data' => []
		];

		return $item;
	}

	private static function getTreeMapItem($name, $value = 0, $color = '', $borderWidth = 0)
	{
		$item = [
			'name' => $name,
			'value' => $value,
			'label' => [
				'show' => true,
				'position' => 'inside',
				'overflow' => 'break'
			],
			'children' => []
		];

		if (!empty($color))
			$item['itemStyle']['color'] = $color;
		if (!empty($borderWidth))
			$item['itemStyle']['borderWidth'] = $borderWidth;

		return $item;
	}

	private static function addValueToName(&$item)
	{
		$item['name'] .= "\n" . $item['value'] . ' €';
	}
}

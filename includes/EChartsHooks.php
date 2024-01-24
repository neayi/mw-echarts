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

		$definitions = json_decode(file_get_contents(__DIR__ . '/../definitions/compta_defs_fr.html'), true);
		$temp = $definitions['Charges'];
		unset($definitions['Charges']);
		$definitions['Charges'] = $temp;
		$temp = $definitions['Soldes de gestion'];
		unset($definitions['Soldes de gestion']);
		$definitions['Soldes de gestion'] = $temp;

		$postesSoldeDeGestion = $definitions['Soldes de gestion']['Soldes de gestion']['postes'];

		foreach ($definitions as $barstacks => $barStackElements)
		{
			foreach ($barStackElements as $name => $aCategory)
			{
				if (empty($aCategory['autres postes']))
					continue;

				$definitions[$barstacks][$name]['postes'] = array_merge($definitions[$barstacks][$name]['postes'], $definitions[$barstacks][$name]['autres postes']);
			}
		}

		// Build an array of valid paramters:
		$validParameters = array();
		foreach ($definitions as $barstacks => $barStackElements)
		{
			if ($barstacks == "Soldes de gestion")
				$barstacks = 'Charges';

			foreach ($barStackElements as $name => $aCategory)
			{
				foreach ($aCategory['postes'] as $aPoste)
					$validParameters[strtolower($aPoste)] = $aPoste;
			}
		}

		$validParameters['ebe'] = 'EBE';
		 
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

		$options = self::buildStackBarOptions($parameters, $definitions);
		$drilldownData = self::buildDrillDownData($parameters, $definitions);

		// Add a last check on the consistency of the data:
		foreach ($drilldownData as $year => $data)
		{
			$ratio = $data['Produits']['value'] / $data['Charges']['value'];
			if ($ratio < 0.9 || $ratio > 1.1)
			{
				// Check that the inconsistency doesn't come from the other parts of the Soldes de gestion:
				$soldeGestion = 0;
				foreach ($postesSoldeDeGestion as $aPoste)
					$soldeGestion += $parameters[$year][$aPoste] ?? 0;

				if ($data['Charges']['value'] - $data['Produits']['value'] == $soldeGestion)
				{
					// Let's fix the graph then
					if (isset($parameters[$year]['EBE']))
						$parameters[$year]['EBE'] -= $soldeGestion;
					else if (isset($parameters[$year]['EBE total']))
						$parameters[$year]['EBE total'] -= $soldeGestion;

					$options = self::buildStackBarOptions($parameters, $definitions);
					$drilldownData = self::buildDrillDownData($parameters, $definitions);				
				}
				else
				{
					$ret = "<pre>Attention, vos données pour $year ne sont pas équilibrées entre les produits et les charges.<br>NB : Si vous avez spécifié les prélévements privés, il faut les déduire de l'EBE !</pre>";
					return $ret;
				}
			}
		}

		// Convert the updated $option array to JSON format
		$JS = json_encode($options, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		$drilldownDataJSON = json_encode($drilldownData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		$ret = '<div id="echart_' . $thisId . '_container"  style="width:' . $width . '; height:' . $height . '"><div id="echart_' . $thisId . '" class="echarts_economical_div" style="width:' . $width . '; height:' . $height . '; display:none;">' . $JS . '</div></div>';
		$ret .= '<div id="drilldownData_' . $thisId . '" style="display:none;">' . htmlentities($drilldownDataJSON) . '</div>';

		return [$ret, 'noparse' => true, 'isHTML' => true];
	}
	
	private static function buildStackBarOptions($parameters, $definitions)
	{
		// Build the series:
		$stackBarSeries = [];

		// Loop through each year's data in $parameters
		$subSums = [];
		foreach ($parameters as $year => $data) {

			// Loop through each parameter's data for the current year
			foreach ($data as $param => $value) {
				$subCategory = self::findCategorieForPoste($definitions, $param);

				if (!isset($subSums[$year][$subCategory]))
					$subSums[$year][$subCategory] = 0;

				$subSums[$year][$subCategory] += (float)$value;
			}
		}

		foreach ($definitions as $barstacks => $barStackElements)
		{
			if ($barstacks == "Soldes de gestion")
				$barstacks = 'Charges';

			foreach ($barStackElements as $name => $aCategory)
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
					"stack" => $barstacks,
					"id" => $name
				];
	
				$values = [];
				foreach ($parameters as $year => $v) {
					if (isset($subSums[$year][$name]))
						$values[] = ['groupId' => $barstacks, 'value' => $subSums[$year][$name]];
					else
						$values[] = ['groupId' => $barstacks, 'value' => 0];
				}
	
				$series['data'] = $values;
	
				$stackBarSeries[] = $series;
			}
		}

		$emptyTooltip = (object) [];
		$options = [
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

		return $options;
	}

	private static function buildDrillDownData($parameters, $definitions)
	{
		// Now build the drilldown data (for the treemap)
		$drilldownData = [];

		// Loop through each year's data in $parameters
		foreach ($parameters as $year => $data) {

			$drilldownData[$year]['Produits'] = self::getTreeMapSeries("Produits $year");
			$drilldownData[$year]['Charges'] = self::getTreeMapSeries("Charges $year");

			foreach ($definitions as $barstacks => $barStackElements)
			{
				if ($barstacks == "Soldes de gestion")
					$barstacks = 'Charges';

				foreach ($barStackElements as $mainCategory => $aCategory)
				{
					$treeMapSubCategory = null;

					foreach ($aCategory['postes'] as $paramName)
					{
						if (!empty($data[$paramName]))
						{
							if (!$treeMapSubCategory)
								$treeMapSubCategory = self::getTreeMapItem($mainCategory, 0, $aCategory['color'], 1); // For Aides

							$treeMapSubCategory['children'][] = self::getTreeMapItem($paramName, $data[$paramName]);
							$treeMapSubCategory['value'] += $data[$paramName];
						}
					}

					if ($treeMapSubCategory)
					{
						self::addValueToName($treeMapSubCategory);

						$drilldownData[$year][$barstacks]['data'][] = $treeMapSubCategory;
						$drilldownData[$year][$barstacks]['value'] += $treeMapSubCategory['value'];
					}
				}

			}

			self::addValueToName($drilldownData[$year]['Produits']);
			self::addValueToName($drilldownData[$year]['Charges']);
		}

		return $drilldownData;
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

	private static function findCategorieForPoste($definitions, $posteToLookup)
	{
		foreach ($definitions as $barStackElements)
		{
			foreach ($barStackElements as $name => $aCategory)
			{
				foreach ($aCategory['postes'] as $aPoste)
					if ($posteToLookup == $aPoste)
						return $name;
			}
		}

		return false;
	}
}

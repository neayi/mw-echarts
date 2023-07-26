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
	public function onParserFirstCallInit( $parser ) {

		// Add the following to a wiki page to see how it works:
		// {{#echarts: {some json} }}
		$parser->setFunctionHook( 'echarts', [ self::class, 'parserFunctionEcharts' ] );

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
		$parser->setFunctionHook( 'economic_charts', [ self::class, 'parserFunctionEconomicCharts' ] );

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
	public static function parserFunctionEcharts( Parser $parser, string $value, ...$args ) {

		$parser->getOutput()->addModules( 'ext.echarts' );
		$parser->getOutput()->addModules( 'ext.mwecharts' );

		array_unshift($args, $value);

		$width = '100%';
		$height = '400px';
		$container_classes = '';

		$json_parts = array();

		// try to find a few specific parameters to the template call:
		foreach ($args as $k => $v)
		{
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

		$ret = '<div id="echart_'. $thisId . '_container"  class="'.$container_classes.'" style="width:'.$width.'; height:'.$height.'"><div id="echart_'. $thisId . '" class="echarts_div" style="width:'.$width.'; height:'.$height.'; display:none;">'.$json.'</div></div>';

		return $ret;
	}

	/**
	 * Parser function handler for {{#parserFunctionEconomicCharts: .. | .. }}
	 *
	 * @param Parser $parser
	 * @param string $value
	 * @param string ...$args
	 * @return string HTML to insert in the page.
	 */
	public static function parserFunctionEconomicCharts( Parser $parser, string $value, ...$args ) {

		$parser->getOutput()->addModules( 'ext.echarts' );
		$parser->getOutput()->addModules( 'ext.mwecharts' );

		array_unshift($args, $value);

		$width = '100%';
		$height = '400px';

		// Define an array of valid parameters for "Produits" bar
		$validParametersProduits = [
		    "Aides", 
		    "Chiffre d'affaire", 
		    "DPU, DPB", 
		    "Vente autres produits", 
		    "Vente de produits végétaux",
		];

		// Define an array of valid parameters for "Charges" bar
		$validParametersCharges = [
		    "Prélèvements privés", 
		    "EBE", 
		    "Salariés", 
		    "Cotisations salariés", 
		    "Cotisations exploitants", 
		    "Carburant",
			"Entretien matériel", 
			"Eau, gaz, électricité", 
			"Frais de gestion", 
			"Certification", 
			"Fermage", 
			"Assurances", 
			"Autres", 
			"Fournitures diverses", 
			"Travaux par tiers", 
			"Bâches et voiles", 
			"Produits de traitements", 
			"Terreau", 
			"Achat des légumes (revente)", 
			"Fertilisation (MO)", 
			"Semences et plants",
		];

		// try to find a few specific parameters to the template call:
		foreach ($args as $k => $v)
		{
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
					if (preg_match('/[0-9]{4}/', $key, $matches)) {
						$year = $matches[0];
						$param = trim(str_replace($year, '', $key));
						// Check if the parameter belongs to "Produits" or "Charges" bar
						if (in_array($param, $validParametersProduits)) {
							$parameters[$year][$param] = str_replace(',', '.', $parts[1]);
						} elseif (in_array($param, $validParametersCharges)) {
							$parameters[$year][$param] = str_replace(',', '.', $parts[1]);
						} else {
							$ret = "Ce paramètre n'est pas reconnu : $key";
							return $ret;
						}
					}
					break;
			}
		}

		$thisId = self::$id++;

		// Extract the years from the $parameters array
		$years = array_keys($parameters);

		// Convert each year to a string
		$years = array_map('strval', $years);

		// initialize the drilldownData array
		$drilldownData = [];

		// Loop through each year's data in $parameters
		foreach ($parameters as $year => $data) {
		    // Initialize the data array for the current year
		    $yearData = [
		        'dataGroupId' => (string) $year,
		        'data' => [],
		    ];
		
		    // Loop through each parameter's data for the current year
		    foreach ($data as $param => $value) {
		        // Determine the 'typeDeDonnee' based on the parameter name
		        switch ($param) {
		            case 'DPU, DPB':
						case 'Aides':
		                	$typeDeDonnee = 'Aides';
		                	break;
		            case 'Vente autres produits':
		            case 'Vente de produits végétaux':
		                $typeDeDonnee = 'Détail Chiffre d\'affaire';
		                break;
		            case "Chiffre d'affaire":
		                $typeDeDonnee = "Chiffre d'affaire";
		                break;
		            case 'Prélèvements privés':
		            case 'EBE':
		                $typeDeDonnee = 'EBE';
		                break;
		            case 'Salariés':
		            case 'Cotisations salariés':
		            case 'Cotisations exploitants':
		                $typeDeDonnee = 'Charges de personnels';
		                break;
		            case 'Carburant':
		            case 'Entretien matériel':
		            case 'Eau, gaz, électricité':
		            case 'Frais de gestion':
		            case 'Certification':
		            case 'Fermage':
		            case 'Assurances':
		            case 'Autres':
		                $typeDeDonnee = 'Charges de structure';
		                break;
		            default:
		                $typeDeDonnee = 'Charges opérationnelles';
		                break;
		        }
			
		        // Create a new data entry for the parameter
		        $paramData = [
		            'name' => $param,
		            'typeDeDonnee' => $typeDeDonnee,
		            'value' => [(float) $value], // Assuming the value is a single value, you can adjust this accordingly
		        ];
			
		        // Add the parameter data to the year's data array
		        $yearData['data'][] = $paramData;
		    }
		
		    // Add the year's data to the $drilldownData array
		    $drilldownData[] = $yearData;
		}

		// Initialize arrays to store data for each bar
		$produitsData = [];
		$chargesData = [];

		// Loop through $drilldownData to separate data for each bar
		foreach ($drilldownData as $dataGroup) {
		    $year = $dataGroup['dataGroupId'];
		
		    // Initialize variables to store the sum of products and charges for each year
		    $sumProduits = 0;
		    $sumCharges = 0;
		
		    foreach ($dataGroup['data'] as $data) {
		        $name = $data['name'];
		        $value = $data['value'][0];
			
		        // Check if the parameter belongs to "Produits" or "Charges" bar
		        if (in_array($name, $validParametersProduits)) {
		            // Accumulate the value for produits
		            $sumProduits += (float) $value;
		        } else {
		            // Accumulate the value for charges
		            $sumCharges += (float) $value;
		        }
		    }
		
		    // Store the sum of produits and charges in the respective arrays
		    $produitsData[] = ['value' => $sumProduits, 'groupId' => $year ];
		    $chargesData[] = ['value' => $sumCharges, 'groupId' => $year];
		}

		$emptyTooltip = (object) [];
		$option = [
		    "tooltip" => $emptyTooltip,
		    "title" => [
		        "text" => "Evolution du bilan financier",
		        "subtext" => "Cliquer sur une barre pour voir le détail",
		        "textAlign" => "center",
		        "left" => "50%"
		    ],
		    "xAxis" => [
		        "type" => "category",
		        "data" => $years,
		    ],
		    "yAxis" => [
		        "type" => "value",
		        "axisLabel" => [
		            "formatter" => "{value} €"
		        ]
		    ],
		    "series" => [
		        [
		            "type" => "bar",
		            "id" => "produits",
		            "name" => "Produits",
		            "data" => $produitsData,
		        ],
		        [
		            "type" => "bar",
		            "id" => "charges",
		            "name" => "Charges",
		            "data" => $chargesData,
		        ]
		    ]
		];

		// Convert the updated $option array to JSON format
		$JS = json_encode($option, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		$drilldownDataJSON = json_encode($drilldownData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		$parameters = json_encode($parameters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		$ret = '<div id="echart_'. $thisId . '_container"  style="width:'.$width.'; height:'.$height.'"><div id="echart_'. $thisId . '" class="echarts_economical_div" style="width:'.$width.'; height:'.$height.'; display:none;">'.$JS.'</div></div>';

		$ret .= '<div id="drilldownData_' . $thisId . '" style="display:none;">' . htmlentities($drilldownDataJSON) . '</div>';

		$ret .= 'Parameters : <pre>' . print_r($parameters, true) . '</pre>';

		$ret .= 'drilldownData : <pre>' . print_r($drilldownDataJSON, true) . '</pre>';

		return [ $ret, 'noparse' => true, 'isHTML' => true ];
	}
}
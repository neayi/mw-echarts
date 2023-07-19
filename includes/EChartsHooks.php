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
		$container_classes = '';

		$json_parts = array();

		// try to find a few specific parameters to the template call:
		foreach ($args as $k => $v)
		{
			$parts = explode('=', $v);

			$key = trim($parts[0]);

			switch (strtolower($key)) {
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
					if (preg_match('/[0-9]{4}/', $key, $matches))
					{
						$year = $matches[0];
						$param = trim(str_replace($year, '', $key));
						$parameters[$year][$param] = $parts[1];
					}

					break;
			}
		}


		$thisId = self::$id++;

		$JS = <<<'JS_HERE'
		option = {
			tooltip: {},
			title: {
			  text: "Evolution du bilan financier",
			  subtext : "Cliquer sur une barre pour voir le détail",
			  textAlign: 'center',
			  left: '50%'
			},
			xAxis: {
			  type: 'category',
			  data: ['2016', '2017', '2018', '2019', '2020']
			},
			yAxis: {
			  type: 'value',
			  axisLabel: {
				formatter: '{value} €'
			  }
			},
			series: [
			  {
				type: 'bar',
				id: 'produits',
				name: 'Produits',
				data: [
				  { value: 29600, groupId: '2016' },
				  { value: 40750, groupId: '2017' },
				  { value: 30418, groupId: '2018' },
				  { value: 45649, groupId: '2019' },
				  { value: 63779, groupId: '2020' }
				]
			  },
			  {
				type: 'bar',
				id: 'charges',
				name: 'Charges',
				data: [
				  { value: 34484, groupId: '2016' },
				  { value: 49140, groupId: '2017' },
				  { value: 41888, groupId: '2018' },
				  { value: 56504, groupId: '2019' },
				  { value: 85607, groupId: '2020' }
				],
			  }
			]
		  };

		  drilldownData = [
			{
			  dataGroupId: '2016',
			  data: [
				{
				  name: 'DPU, DPB',
				  typeDeDonnee: 'Aides',
				  value: [500]
				},
				{
				  name: 'Vente autres produits',
				  typeDeDonnee: "Détail Chiffre d'affaire",
				  value: [650]
				},
				{
				  name: 'Vente de produits végétaux',
				  typeDeDonnee: "Détail Chiffre d'affaire",
				  value: [71096]
				},
				{
				  name: 'Prélèvements privés',
				  typeDeDonnee: 'EBE',
				  value: [29264.25]
				},
				{
				  name: 'EBE',
				  typeDeDonnee: 'EBE',
				  value: [13869]
				},
				{
				  name: 'Salariés',
				  typeDeDonnee: 'Charges de personnels',
				  value: [770]
				},
				{
				  name: 'Cotisations salariés',
				  typeDeDonnee: 'Charges de personnels',
				  value: [0]
				},
				{
				  name: 'Cotisations exploitants',
				  typeDeDonnee: 'Charges de personnels',
				  value: [0]
				},
				{
				  name: 'Carburant',
				  typeDeDonnee: 'Charges de structure',
				  value: [700]
				},
				{
				  name: 'Entretien matériel',
				  typeDeDonnee: 'Charges de structure',
				  value: [1000]
				},
				{
				  name: 'Eau, gaz, électricité',
				  typeDeDonnee: 'Charges de structure',
				  value: [50]
				},
				{
				  name: 'Frais de gestion',
				  typeDeDonnee: 'Charges de structure',
				  value: [200]
				},
				{
				  name: 'Certification',
				  typeDeDonnee: 'Charges de structure',
				  value: [400]
				},
				{
				  name: 'Fermage',
				  typeDeDonnee: 'Charges de structure',
				  value: [4000]
				},
				{
				  name: 'Assurances',
				  typeDeDonnee: 'Charges de structure',
				  value: [600]
				},
				{
				  name: 'Autres',
				  typeDeDonnee: 'Charges de structure',
				  value: [3000]
				},
				{
				  name: 'Fournitures diverses',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [1000]
				},
				{
				  name: 'Travaux par tiers',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [550]
				},
				{
				  name: 'Bâches et voiles',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [1500]
				},
				{
				  name: 'Produits de traitements',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [123]
				},
				{
				  name: 'Terreau',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Achat des légumes',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [3000]
				},
				{
				  name: 'Fertilisation',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [118]
				},
				{
				  name: 'Semences et plants',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [3604]
				}
			  ]
			},
			{
			  dataGroupId: '2017',
			  data: [
				{
				  name: 'DPU, DPB',
				  typeDeDonnee: 'Aides',
				  value: [500]
				},
				{
				  name: 'Vente autres produits',
				  typeDeDonnee: "Détail Chiffre d'affaire",
				  value: [650]
				},
				{
				  name: 'Vente de produits végétaux',
				  typeDeDonnee: "Détail Chiffre d'affaire",
				  value: [71096]
				},
				{
				  name: 'Prélèvements privés',
				  typeDeDonnee: 'EBE',
				  value: [29264.25]
				},
				{
				  name: 'EBE',
				  typeDeDonnee: 'EBE',
				  value: [25380]
				},
				{
				  name: 'Salariés',
				  typeDeDonnee: 'Charges de personnels',
				  value: [0]
				},
				{
				  name: 'Cotisations salariés',
				  typeDeDonnee: 'Charges de personnels',
				  value: [0]
				},
				{
				  name: 'Cotisations exploitants',
				  typeDeDonnee: 'Charges de personnels',
				  value: [3000]
				},
				{
				  name: 'Carburant',
				  typeDeDonnee: 'Charges de structure',
				  value: [600]
				},
				{
				  name: 'Entretien matériel',
				  typeDeDonnee: 'Charges de structure',
				  value: [2000]
				},
				{
				  name: 'Eau, gaz, électricité',
				  typeDeDonnee: 'Charges de structure',
				  value: [1200]
				},
				{
				  name: 'Frais de gestion',
				  typeDeDonnee: 'Charges de structure',
				  value: [0]
				},
				{
				  name: 'Certification',
				  typeDeDonnee: 'Charges de structure',
				  value: [500]
				},
				{
				  name: 'Fermage',
				  typeDeDonnee: 'Charges de structure',
				  value: [3600]
				},
				{
				  name: 'Assurances',
				  typeDeDonnee: 'Charges de structure',
				  value: [800]
				},
				{
				  name: 'Autres',
				  typeDeDonnee: 'Charges de structure',
				  value: [1000]
				},
				{
				  name: 'Fournitures diverses',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [200]
				},
				{
				  name: 'Travaux par tiers',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Bâches et voiles',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Produits de traitements',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Terreau',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Achat des légumes',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [4940]
				},
				{
				  name: 'Fertilisation',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [670]
				},
				{
				  name: 'Semences et plants',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [5250]
				}
			  ]
			},
			{
			  dataGroupId: '2018',
			  data: [
				{
				  name: 'DPU, DPB',
				  typeDeDonnee: 'Aides',
				  value: [500]
				},
				{
				  name: 'Vente autres produits',
				  typeDeDonnee: "Détail Chiffre d'affaire",
				  value: [650]
				},
				{
				  name: 'Vente de produits végétaux',
				  typeDeDonnee: "Détail Chiffre d'affaire",
				  value: [71096]
				},
				{
				  name: 'Prélèvements privés',
				  typeDeDonnee: 'EBE',
				  value: [29264.25]
				},
				{
				  name: 'EBE',
				  typeDeDonnee: 'EBE',
				  value: [25543]
				},
				{
				  name: 'Salariés',
				  typeDeDonnee: 'Charges de personnels',
				  value: [0]
				},
				{
				  name: 'Cotisations salariés',
				  typeDeDonnee: 'Charges de personnels',
				  value: [0]
				},
				{
				  name: 'Cotisations exploitants',
				  typeDeDonnee: 'Charges de personnels',
				  value: [600]
				},
				{
				  name: 'Carburant',
				  typeDeDonnee: 'Charges de structure',
				  value: [1137]
				},
				{
				  name: 'Entretien matériel',
				  typeDeDonnee: 'Charges de structure',
				  value: [919]
				},
				{
				  name: 'Eau, gaz, électricité',
				  typeDeDonnee: 'Charges de structure',
				  value: [912]
				},
				{
				  name: 'Frais de gestion',
				  typeDeDonnee: 'Charges de structure',
				  value: [170]
				},
				{
				  name: 'Certification',
				  typeDeDonnee: 'Charges de structure',
				  value: [500]
				},
				{
				  name: 'Fermage',
				  typeDeDonnee: 'Charges de structure',
				  value: [3000]
				},
				{
				  name: 'Assurances',
				  typeDeDonnee: 'Charges de structure',
				  value: [600]
				},
				{
				  name: 'Autres',
				  typeDeDonnee: 'Charges de structure',
				  value: [3800]
				},
				{
				  name: 'Fournitures diverses',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [277]
				},
				{
				  name: 'Travaux par tiers',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Bâches et voiles',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Produits de traitements',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Terreau',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Achat des légumes',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [730]
				},
				{
				  name: 'Fertilisation',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [100]
				},
				{
				  name: 'Semences et plants',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [3600]
				}
			  ]
			},
			{
			  dataGroupId: '2019',
			  data: [
				{
				  name: 'DPU, DPB',
				  typeDeDonnee: 'Aides',
				  value: [500]
				},
				{
				  name: 'Vente autres produits',
				  typeDeDonnee: "Détail Chiffre d'affaire",
				  value: [650]
				},
				{
				  name: 'Vente de produits végétaux',
				  typeDeDonnee: "Détail Chiffre d'affaire",
				  value: [71096]
				},
				{
				  name: 'Prélèvements privés',
				  typeDeDonnee: 'EBE',
				  value: [29264.25]
				},
				{
				  name: 'EBE',
				  typeDeDonnee: 'EBE',
				  value: [33919]
				},
				{
				  name: 'Salariés',
				  typeDeDonnee: 'Charges de personnels',
				  value: [0]
				},
				{
				  name: 'Cotisations salariés',
				  typeDeDonnee: 'Charges de personnels',
				  value: [0]
				},
				{
				  name: 'Cotisations exploitants',
				  typeDeDonnee: 'Charges de personnels',
				  value: [600]
				},
				{
				  name: 'Carburant',
				  typeDeDonnee: 'Charges de structure',
				  value: [1338]
				},
				{
				  name: 'Entretien matériel',
				  typeDeDonnee: 'Charges de structure',
				  value: [2655]
				},
				{
				  name: 'Eau, gaz, électricité',
				  typeDeDonnee: 'Charges de structure',
				  value: [537]
				},
				{
				  name: 'Frais de gestion',
				  typeDeDonnee: 'Charges de structure',
				  value: [0]
				},
				{
				  name: 'Certification',
				  typeDeDonnee: 'Charges de structure',
				  value: [500]
				},
				{
				  name: 'Fermage',
				  typeDeDonnee: 'Charges de structure',
				  value: [990]
				},
				{
				  name: 'Assurances',
				  typeDeDonnee: 'Charges de structure',
				  value: [600]
				},
				{
				  name: 'Autres',
				  typeDeDonnee: 'Charges de structure',
				  value: [1475]
				},
				{
				  name: 'Fournitures diverses',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [4534]
				},
				{
				  name: 'Travaux par tiers',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Bâches et voiles',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Produits de traitements',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Terreau',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Achat des légumes',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [2393]
				},
				{
				  name: 'Fertilisation',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [1000]
				},
				{
				  name: 'Semences et plants',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [5963]
				}
			  ]
			},
			{
			  dataGroupId: '2020',
			  data: [
				{
				  name: 'DPU, DPB',
				  typeDeDonnee: 'Aides',
				  value: [500]
				},
				{
				  name: 'Vente autres produits',
				  typeDeDonnee: "Détail Chiffre d'affaire",
				  value: [650]
				},
				{
				  name: 'Vente de produits végétaux',
				  typeDeDonnee: "Détail Chiffre d'affaire",
				  value: [71096]
				},
				{
				  name: 'Prélèvements privés',
				  typeDeDonnee: 'EBE',
				  value: [29264.25]
				},
				{
				  name: 'EBE',
				  typeDeDonnee: 'EBE',
				  value: [53104]
				},
				{
				  name: 'Salariés',
				  typeDeDonnee: 'Charges de personnels',
				  value: [0]
				},
				{
				  name: 'Cotisations salariés',
				  typeDeDonnee: 'Charges de personnels',
				  value: [0]
				},
				{
				  name: 'Cotisations exploitants',
				  typeDeDonnee: 'Charges de personnels',
				  value: [600]
				},
				{
				  name: 'Carburant',
				  typeDeDonnee: 'Charges de structure',
				  value: [502]
				},
				{
				  name: 'Entretien matériel',
				  typeDeDonnee: 'Charges de structure',
				  value: [1122]
				},
				{
				  name: 'Eau, gaz, électricité',
				  typeDeDonnee: 'Charges de structure',
				  value: [2327]
				},
				{
				  name: 'Frais de gestion',
				  typeDeDonnee: 'Charges de structure',
				  value: [555]
				},
				{
				  name: 'Certification',
				  typeDeDonnee: 'Charges de structure',
				  value: [500]
				},
				{
				  name: 'Fermage',
				  typeDeDonnee: 'Charges de structure',
				  value: [1007]
				},
				{
				  name: 'Assurances',
				  typeDeDonnee: 'Charges de structure',
				  value: [600]
				},
				{
				  name: 'Autres',
				  typeDeDonnee: 'Charges de structure',
				  value: [2235]
				},
				{
				  name: 'Fournitures diverses',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [12271]
				},
				{
				  name: 'Travaux par tiers',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [145]
				},
				{
				  name: 'Bâches et voiles',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [3600]
				},
				{
				  name: 'Produits de traitements',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Terreau',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [200]
				},
				{
				  name: 'Achat des légumes',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [0]
				},
				{
				  name: 'Fertilisation',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [240]
				},
				{
				  name: 'Semences et plants',
				  typeDeDonnee: 'Charges opérationnelles',
				  value: [6599]
				}
			  ]
			}
		  ];


JS_HERE;

		$ret = '<div id="echart_'. $thisId . '_container"  style="width:'.$width.'; height:'.$height.'"><div id="echart_'. $thisId . '" class="echarts_economical_div" style="width:'.$width.'; height:'.$height.'; display:none;">'.$JS.'</div></div>';

		$ret .= 'Parameters : <pre>' . print_r($parameters, true) . '</pre>';

		return [ $ret, 'noparse' => true, 'isHTML' => true ];

	}
}

option = 
  {
    tooltip: {},
    xAxis: {
      type: 'category',
      splitLine: { show: false },
      data: ['2016', '2017', '2018', '2019', '2020']
    },
    yAxis: {
        type: 'value',
        axisLabel: {
          formatter: '{value} €'
        }
    },
    dataGroupId: '',
    animationDurationUpdate: 500,
    series: [
      {
        type: 'bar',
        id: 'produits',
        name: 'Produits',
        data: [
          {
            value: 29600,
            groupId: '2016'
          },
          {
            value: 40750,
            groupId: '2017'
          },
          {
            value: 30418,
            groupId: '2018'
          },
          {
            value: 45649,
            groupId: '2019'
          },
          {
            value: 63779,
            groupId: '2020'
          },
        ],
        universalTransition: {
          enabled: true,
          divideShape: 'clone'
        }
      },
      {
        type: 'bar',
        id: 'charges',
        name: 'Charges',
        data: [
          {
            value: 34484,
            groupId: '2016'
          },
          {
            value: 49140,
            groupId: '2017'
          },
          {
            value: 41888,
            groupId: '2018'
          },
          {
            value: 56504,
            groupId: '2019'
          },
          {
            value: 85607,
            groupId: '2020'
          },
        ],
        universalTransition: {
          enabled: true,
          divideShape: 'clone'
        }
      }
    ],
  };

  const drilldownData = [
    {
      dataGroupId: '2016',
      data: [
        {
          name: 'DPU, DPB',
          typeDeDonnee: "Aides",
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
          typeDeDonnee: "Prélèvements privés",
          value: [29264.25]
        },
        {
          name: 'EBE',
          typeDeDonnee: "EBE",
          value: [13869]
        },
        {
          name: 'Salariés',
          typeDeDonnee: "Charges de personnels",
          value: [770]
        },
        {
          name: 'Cotisations salariés',
          typeDeDonnee: "Charges de personnels",
          value: [0]
        },
        {
          name: 'Cotisations exploitants',
          typeDeDonnee: "Charges de personnels",
          value: [0]
        },
        {
          name: 'Carburant',
          typeDeDonnee: "Charges de structure",
          value: [700]
        },
        {
          name: 'Entretien matériel',
          typeDeDonnee: "Charges de structure",
          value: [1000]
        },
        {
          name: 'Eau, gaz, électricité',
          typeDeDonnee: "Charges de structure",
          value: [50]
        },
        {
          name: 'Frais de gestion',
          typeDeDonnee: "Charges de structure",
          value: [200]
        },
        {
          name: 'Certification',
          typeDeDonnee: "Charges de structure",
          value: [400]
        },
        {
          name: 'Fermage',
          typeDeDonnee: "Charges de structure",
          value: [4000]
        },
        {
          name: 'Assurances',
          typeDeDonnee: "Charges de structure",
          value: [600]
        },
        {
          name: 'Autres',
          typeDeDonnee: "Charges de structure",
          value: [3000]
        },
        {
          name: 'Fournitures diverses',
          typeDeDonnee: "Charges opérationnelles",
          value: [1000]
        },
        {
          name: 'Travaux par tiers',
          typeDeDonnee: "Charges opérationnelles",
          value: [550]
        },
        {
          name: 'Bâches et voiles',
          typeDeDonnee: "Charges opérationnelles",
          value: [1500]
        },
        {
          name: 'Produits de traitements',
          typeDeDonnee: "Charges opérationnelles",
          value: [123]
        },
        {
          name: 'Terreau',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Achat des légumes',
          typeDeDonnee: "Charges opérationnelles",
          value: [3000]
        },
        {
          name: 'Fertilisation',
          typeDeDonnee: "Charges opérationnelles",
          value: [118]
        },
        {
          name: 'Semences et plants',
          typeDeDonnee: "Charges opérationnelles",
          value: [3604]
        },
      ]
    },
    {
      dataGroupId: '2017',
      data: [
        {
          name: 'DPU, DPB',
          typeDeDonnee: "Aides",
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
          typeDeDonnee: "Prélèvements privés",
          value: [29264.25]
        },
        {
          name: 'EBE',
          typeDeDonnee: "EBE",
          value: [25380]
        },
        {
          name: 'Salariés',
          typeDeDonnee: "Charges de personnels",
          value: [0]
        },
        {
          name: 'Cotisations salariés',
          typeDeDonnee: "Charges de personnels",
          value: [0]
        },
        {
          name: 'Cotisations exploitants',
          typeDeDonnee: "Charges de personnels",
          value: [3000]
        },
        {
          name: 'Carburant',
          typeDeDonnee: "Charges de structure",
          value: [600]
        },
        {
          name: 'Entretien matériel',
          typeDeDonnee: "Charges de structure",
          value: [2000]
        },
        {
          name: 'Eau, gaz, électricité',
          typeDeDonnee: "Charges de structure",
          value: [1200]
        },
        {
          name: 'Frais de gestion',
          typeDeDonnee: "Charges de structure",
          value: [0]
        },
        {
          name: 'Certification',
          typeDeDonnee: "Charges de structure",
          value: [500]
        },
        {
          name: 'Fermage',
          typeDeDonnee: "Charges de structure",
          value: [3600]
        },
        {
          name: 'Assurances',
          typeDeDonnee: "Charges de structure",
          value: [800]
        },
        {
          name: 'Autres',
          typeDeDonnee: "Charges de structure",
          value: [1000]
        },
        {
          name: 'Fournitures diverses',
          typeDeDonnee: "Charges opérationnelles",
          value: [200]
        },
        {
          name: 'Travaux par tiers',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Bâches et voiles',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Produits de traitements',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Terreau',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Achat des légumes',
          typeDeDonnee: "Charges opérationnelles",
          value: [4940]
        },
        {
          name: 'Fertilisation',
          typeDeDonnee: "Charges opérationnelles",
          value: [670]
        },
        {
          name: 'Semences et plants',
          typeDeDonnee: "Charges opérationnelles",
          value: [5250]
        },
      ]
    },
    {
      dataGroupId: '2018',
      data: [
        {
          name: 'DPU, DPB',
          typeDeDonnee: "Aides",
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
          typeDeDonnee: "Prélèvements privés",
          value: [29264.25]
        },
        {
          name: 'EBE',
          typeDeDonnee: "EBE",
          value: [25543]
        },
        {
          name: 'Salariés',
          typeDeDonnee: "Charges de personnels",
          value: [0]
        },
        {
          name: 'Cotisations salariés',
          typeDeDonnee: "Charges de personnels",
          value: [0]
        },
        {
          name: 'Cotisations exploitants',
          typeDeDonnee: "Charges de personnels",
          value: [600]
        },
        {
          name: 'Carburant',
          typeDeDonnee: "Charges de structure",
          value: [1137]
        },
        {
          name: 'Entretien matériel',
          typeDeDonnee: "Charges de structure",
          value: [919]
        },
        {
          name: 'Eau, gaz, électricité',
          typeDeDonnee: "Charges de structure",
          value: [912]
        },
        {
          name: 'Frais de gestion',
          typeDeDonnee: "Charges de structure",
          value: [170]
        },
        {
          name: 'Certification',
          typeDeDonnee: "Charges de structure",
          value: [500]
        },
        {
          name: 'Fermage',
          typeDeDonnee: "Charges de structure",
          value: [3000]
        },
        {
          name: 'Assurances',
          typeDeDonnee: "Charges de structure",
          value: [600]
        },
        {
          name: 'Autres',
          typeDeDonnee: "Charges de structure",
          value: [3800]
        },
        {
          name: 'Fournitures diverses',
          typeDeDonnee: "Charges opérationnelles",
          value: [277]
        },
        {
          name: 'Travaux par tiers',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Bâches et voiles',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Produits de traitements',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Terreau',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Achat des légumes',
          typeDeDonnee: "Charges opérationnelles",
          value: [730]
        },
        {
          name: 'Fertilisation',
          typeDeDonnee: "Charges opérationnelles",
          value: [100]
        },
        {
          name: 'Semences et plants',
          typeDeDonnee: "Charges opérationnelles",
          value: [3600]
        },
      ]
    },
    {
      dataGroupId: '2019',
      data: [
        {
          name: 'DPU, DPB',
          typeDeDonnee: "Aides",
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
          typeDeDonnee: "Prélèvements privés",
          value: [29264.25]
        },
        {
          name: 'EBE',
          typeDeDonnee: "EBE",
          value: [33919]
        },
        {
          name: 'Salariés',
          typeDeDonnee: "Charges de personnels",
          value: [0]
        },
        {
          name: 'Cotisations salariés',
          typeDeDonnee: "Charges de personnels",
          value: [0]
        },
        {
          name: 'Cotisations exploitants',
          typeDeDonnee: "Charges de personnels",
          value: [600]
        },
        {
          name: 'Carburant',
          typeDeDonnee: "Charges de structure",
          value: [1338]
        },
        {
          name: 'Entretien matériel',
          typeDeDonnee: "Charges de structure",
          value: [2655]
        },
        {
          name: 'Eau, gaz, électricité',
          typeDeDonnee: "Charges de structure",
          value: [537]
        },
        {
          name: 'Frais de gestion',
          typeDeDonnee: "Charges de structure",
          value: [0]
        },
        {
          name: 'Certification',
          typeDeDonnee: "Charges de structure",
          value: [500]
        },
        {
          name: 'Fermage',
          typeDeDonnee: "Charges de structure",
          value: [990]
        },
        {
          name: 'Assurances',
          typeDeDonnee: "Charges de structure",
          value: [600]
        },
        {
          name: 'Autres',
          typeDeDonnee: "Charges de structure",
          value: [1475]
        },
        {
          name: 'Fournitures diverses',
          typeDeDonnee: "Charges opérationnelles",
          value: [4534]
        },
        {
          name: 'Travaux par tiers',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Bâches et voiles',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Produits de traitements',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Terreau',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Achat des légumes',
          typeDeDonnee: "Charges opérationnelles",
          value: [2393]
        },
        {
          name: 'Fertilisation',
          typeDeDonnee: "Charges opérationnelles",
          value: [1000]
        },
        {
          name: 'Semences et plants',
          typeDeDonnee: "Charges opérationnelles",
          value: [5963]
        },
      ]
    },
    {
      dataGroupId: '2020',
      data: [
        {
          name: 'DPU, DPB',
          typeDeDonnee: "Aides",
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
          typeDeDonnee: "Prélèvements privés",
          value: [29264.25]
        },
        {
          name: 'EBE',
          typeDeDonnee: "EBE",
          value: [53104]
        },
        {
          name: 'Salariés',
          typeDeDonnee: "Charges de personnels",
          value: [0]
        },
        {
          name: 'Cotisations salariés',
          typeDeDonnee: "Charges de personnels",
          value: [0]
        },
        {
          name: 'Cotisations exploitants',
          typeDeDonnee: "Charges de personnels",
          value: [600]
        },
        {
          name: 'Carburant',
          typeDeDonnee: "Charges de structure",
          value: [502]
        },
        {
          name: 'Entretien matériel',
          typeDeDonnee: "Charges de structure",
          value: [1122]
        },
        {
          name: 'Eau, gaz, électricité',
          typeDeDonnee: "Charges de structure",
          value: [2327]
        },
        {
          name: 'Frais de gestion',
          typeDeDonnee: "Charges de structure",
          value: [555]
        },
        {
          name: 'Certification',
          typeDeDonnee: "Charges de structure",
          value: [500]
        },
        {
          name: 'Fermage',
          typeDeDonnee: "Charges de structure",
          value: [1007]
        },
        {
          name: 'Assurances',
          typeDeDonnee: "Charges de structure",
          value: [600]
        },
        {
          name: 'Autres',
          typeDeDonnee: "Charges de structure",
          value: [2235]
        },
        {
          name: 'Fournitures diverses',
          typeDeDonnee: "Charges opérationnelles",
          value: [12271]
        },
        {
          name: 'Travaux par tiers',
          typeDeDonnee: "Charges opérationnelles",
          value: [145]
        },
        {
          name: 'Bâches et voiles',
          typeDeDonnee: "Charges opérationnelles",
          value: [3600]
        },
        {
          name: 'Produits de traitements',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Terreau',
          typeDeDonnee: "Charges opérationnelles",
          value: [200]
        },
        {
          name: 'Achat des légumes',
          typeDeDonnee: "Charges opérationnelles",
          value: [0]
        },
        {
          name: 'Fertilisation',
          typeDeDonnee: "Charges opérationnelles",
          value: [240]
        },
        {
          name: 'Semences et plants',
          typeDeDonnee: "Charges opérationnelles",
          value: [6599]
        },
      ]
    }
  ];

  const seriesData1 = [
    {
      name: 'Aides',
      type: 'pie',
      radius: [0, '30%'],
      center: ['25%', '50%'],
      label: { show: true, position: 'outside', formatter: '{b} : {c} €' },
      universalTransition: {
        enabled: true,
        divideShape: 'clone'
      },
      emphasis: { focus: 'series' },
      itemStyle: {},
      data: []
    }
  ];

  const seriesData2 = [
    {
      name: 'Charges',
      type: 'pie',
      radius: [0, '30%'],
      center: ['75%', '50%'],
      label: { show: true, position: 'outside', formatter: '{b} : {c} €' },
      universalTransition: {
        enabled: true,
        divideShape: 'clone'
      },
      emphasis: { focus: 'series' },
      itemStyle: {},
      data: []
    }
  ];
  
  const pieOption = [
      ...seriesData1,
      ...seriesData2
  ];

  myChart.on('click', (event) => {
  if (event.data) {
    const subData = drilldownData.find((data) => data.dataGroupId === event.data.groupId);

    if (!subData) {
      return;
    }

    const pieData1 = subData.data.filter(
      (item) => item.typeDeDonnee === 'Aides' || item.typeDeDonnee === "Détail Chiffre d'affaire"
    );
    const pieData2 = subData.data.filter(
      (item) => item.typeDeDonnee !== 'Aides' && item.typeDeDonnee !== "Détail Chiffre d'affaire"
    );

    const barOption = subData.data.map((item) => {
      var ret = {
        name: item.name,
        type: 'bar',
        barWidth: "30%",
        label: { show: true, position: 'right', formatter: '{a} : {c} €' }, emphasis: { focus: 'series' },
        itemStyle: {},
        data: item.value
      };

      switch (item.name) {
        case 'DPU, DPB':
          case 'Vente autres produits':
            case 'Vente de produits végétaux':
              ret.stack = "Détail Chiffre d'affaire";
              break;
        default:
          ret.stack = "Détail de charges";
      }

      switch (item.typeDeDonnee) {
        case 'Charges opérationnelles':
          ret.itemStyle.color = '#F28960';
          break;
        case 'Charges de structure':
          ret.itemStyle.color = '#F5A893';
          break;
        case 'Charges de personnels':
          ret.itemStyle.color = '#FDCF74';
          break;
        case 'EBE':
          ret.itemStyle.color = '#F8B26D';
          break;
        case "Prélèvements privés":
          ret.itemStyle.color = '#FEFBFA';
          break;
        case "Détail Chiffre d'affaire":
          ret.itemStyle.color = '#88A8CB';
          break;
        case "Aides":
          ret.itemStyle.color = '#A4CC69';
          break;
        default:
          //
      }
      return ret;
    });

    pieData1.forEach((item) => {
      const label = item.value[0] !== 0 ? { show: true, position: 'outside', formatter: '{b} : {c} €' } : { show: false };
    
      const dataItem = {
        value: item.value[0],
        name: item.name,
        label,
        itemStyle: {} // Add itemStyle property
      };

      // Set color based on typeDeDonnee value
      switch (item.typeDeDonnee) {
        case 'Aides':
          dataItem.itemStyle.color = '#A4CC69';
          break;
        case "Détail Chiffre d'affaire":
          dataItem.itemStyle.color = '#88A8CB';
          break;
        default:
          // Handle other cases if needed
          // dataItem.itemStyle.color = ...
      }
      seriesData1[0].data.push(dataItem);
    });

    pieData2.forEach((item) => {
      const label = item.value[0] !== 0 ? { show: true, position: 'outside', formatter: '{b} : {c} €' } : { show: false };

      const dataItem = {
        value: item.value[0],
        name: item.name,
        label,
        itemStyle: {} // Add itemStyle property
      };

      // Set color based on typeDeDonnee value
      switch (item.typeDeDonnee) {
        case 'Charges opérationnelles':
          dataItem.itemStyle.color = '#F28960';
          break;
        case 'Charges de structure':
        dataItem.itemStyle.color = '#F5A893';
        break;
        case 'Charges de personnels':
          dataItem.itemStyle.color = '#FDCF74';
          break;
        case 'EBE':
          dataItem.itemStyle.color = '#F8B26D';
          break;
        case "Prélèvements privés":
            dataItem.itemStyle.color = '#FDFBFF';
            break;
        default:
          // Handle other cases if needed
          // dataItem.itemStyle.color = ...
      }
      seriesData2[0].data.push(dataItem);
    });
    
    let currentOption = pieOption;

    myChart.setOption({
      xAxis: [
        {
          data: [
            "Analyse économique"
          ],
          type: "category"
        }
      ],
      animationDurationUpdate: 500,
      tooltip: {
        trigger: 'item',
        formatter: '{b}: {c} €',
      },
      series: currentOption,
      graphic: [
        {
          type: 'text',
          left: 50,
          top: 20,
          style: {
            text: 'Back',
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
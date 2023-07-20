local p = {}

  -- on insère les valeurs des paramètres, s'il y a une correspondance entre name des bar et paramDecodes[annee][key] 
  -- alors mettre dans options.options {title= {text=" .. annee " Répartition" .. "}, 
  -- et dans options.options.series { data= {' .. value .. '}}
  -- si la valeur est nulle alors on met data = {0}, label={show=false} },
function p.getSeries(args, annee, anneeData)
  if type(args) ~= 'table' then
    error("Type of args must be a table")
  end
  if type(annee) ~= "string" then
    error("Type of annee must be a string")
  end
  if type(anneeData) ~= "table" then
    error("Type of anneeData must be a table")
  end
  -- iterate over the years, adds year + Répartition in the JSON
  local barJson = {title = {text = annee .. ' Répartition'} ,series = {}}
  -- Iterate over the keys of the keyData table
  for _, bar in pairs(args) do
    for key, value in pairs(anneeData) do
    -- If key exists in clesDeSerie then if the value is not equal to "" add it to the data
    -- ensure that the first single match is taken into account
      if key == bar.name then
        local valueJson = {data= {}}
        if value ~= "" then
          valueJson.data = {tonumber(value)}
        else
          -- else add it but it is not shown
          valueJson.data = {0}
          valueJson.label = {show=false}
        end
        table.insert(barJson.series, valueJson)
      end
    end
  end
  return barJson
end

function p.appendBar(args, sorte, titre, width, stacked, coloring)
  if type(args) ~= 'table' then
    error("Type of args must be a table")
  end
  if type(sorte) ~= "string" or type(titre) ~= "string" or type(width) ~= "string" or type(stacked) ~= "string" or type(coloring) ~= "string" then
    error("All arguments other than args must be strings")
  end
  table.insert(args, { name= titre, type= sorte, barWidth= width, stack= stacked,
  label= { show= true, position= 'inside', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
   color=coloring
  }
})
end

function p.decodeTemplate(args)
  if type(args) ~= 'table' then
    error("Type of args must be a table")
  end
  table.sort(args)
  local paramDecodes = {}
  for key, value in pairs(args) do
    for nom_parametre, annee in string.gmatch(key, "(.+) (%d%d%d%d)") do
      if type(paramDecodes[annee]) ~= 'table' then
        paramDecodes[annee] = {}
      end
      paramDecodes[annee][nom_parametre] = value
    end
  end
  return paramDecodes
end

function p.makeSomething( frame )
  local options = {
    baseOption= {
      timeline= {
      axisType= 'category',
      autoPlay= true,
      playInterval= 5000,
      data = {}
    },
    grid= {
      left= '3%',
      right= '4%',
      bottom= '10%',
      containLabel= true
    },
    xAxis= {
      {
        type= 'category',
        data= {'Analyse économique'}
      }
    },
    yAxis= {
      {
        type= 'value',
        axisLabel= {
          formatter= '{value} €'
        }
      },
    },
    series= {
      { name= "Aides", type= 'bar', barWidth= "30%", stack= "Chiffre d'affaire",
        label= { show= true, position= 'inside', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#A4CC69'
        }
      },
      { name= "DPU, DPB", type= 'bar', barWidth= "10%", stack= "Détail Chiffre d'affaire",
        label= { show= true, position= 'inside', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#A4CC69'
        }
      },
      { name= "Chiffre d'affaire", type= 'bar', barWidth= "30%", stack= "Chiffre d'affaire",
        label= { show= true, position= 'inside', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#88A8CB'
        }
      },
      { name= "Vente autres produits", type= 'bar', barWidth= "10%", stack= "Détail Chiffre d'affaire",
        label= { show= true, position= 'inside', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#88A8CB'
        }
      },
      { name= "Vente de produits végétaux", type= 'bar', barWidth= "10%", stack= "Détail Chiffre d'affaire",
        label= { show= true, position= 'inside', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#88A8CB'
        }
      },
      { name= "Prélèvements privés", type= 'bar', barWidth= "30%", stack= "Charges",
        label= { show= true, position= 'inside', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#FEFBFA'
        }
      },
      { name= "Prélèvements privés2", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'inside', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#FEFBFA'
        }
      },
      { name= "EBE2", type= 'bar', barWidth= "30%", stack= "Charges",
        label= { show= true, position= 'inside', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#FDCF74'
        }
      },
      { name= "Charges de personnels", type= 'bar', barWidth= "30%", stack= "Charges",
        label= { show= true, position= 'inside', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#FDCF74'
        }
      },
      { name= "Charges de structure", type= 'bar', barWidth= "30%", stack= "Charges",
        label= { show= true, position= 'inside', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F5A893'
        }
      },
      { name= "Charges opérationnelles", type= 'bar', barWidth= "30%", stack= "Charges",
        label= { show= true, position= 'inside', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F28960'
        }
      },
      { name= "EBE", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'inside', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle= {
         color= '#F8B26D'
        }
      },
      { name= "Salariés", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#FDCF74'
        }
      },
      { name= "Cotisations salariés", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#FDCF74'
        }
      },
      { name= "Cotisations exploitants", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#FDCF74'
        }
      },
      { name= "Carburant", type= 'bar', barWidth= "10%", stack= "Détail de charges", 
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F5A893'
        }
      },
      { name= "Entretien matériel", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F5A893'
        }
      },
      { name= "Eau, gaz, électricité", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F5A893'
        }
      },
      { name= "Frais de gestion", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F5A893'
        }
      },
      { name= "Certification", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' },emphasis= { focus= 'series' }, itemStyle={
         color='#F5A893'
        }
      },
      { name= "Fermage", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F5A893'
        }
      },
      { name= "Assurances", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F5A893'
        }
      },
      { name= "Autres", type= 'bar', barWidth= "10%",  stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F5A893'
        }
      },
      { name= "Fournitures diverses", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F28960'
        }
      },
      { name= "Travaux par tiers", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F28960'
        }
      },
      { name= "Bâches et voiles", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F28960'
        }
      },
      { name= "Produits de traitements", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F28960'
        }
      },
      { name= "Terreau", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F28960'
        }
      },
      { name= "Achat des légumes", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F28960'
        }
      },
      { name= "Fertilisation", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F28960'
        }
      },
      { name= "Semences et plants", type= 'bar', barWidth= "10%", stack= "Détail de charges",
        label= { show= true, position= 'left', formatter= '{a}\n{c} €' }, emphasis= { focus= 'series' }, itemStyle={
         color='#F28960'
        }
      },
    },
  },
  options= {}
  };
  -- On accède aux paramètres de la template :
  local args = frame:getParent().args
  local txt0 = "== Paramètres de la template ==\n" .. mw.text.jsonEncode(args, mw.text.JSON_PRETTY)
  local paramDecodes = p.decodeTemplate(args)
  txt0 = txt0 .. "\n== Paramètres décodés ==\n" .. mw.text.jsonEncode(paramDecodes, mw.text.JSON_PRETTY)  .. "\n\n"

  -- Iterate over the keys of paramDecodes and add the years to the year.data table and year + Répartition in the JSON
  for year, keyData in pairs(paramDecodes) do
    table.insert(options.baseOption.timeline.data, year)
    --p.appendBar(options.baseOption.series, "bar", "test", "10%", "Charges", "#F28960")
    table.insert(options.options, p.getSeries(options.baseOption.series, year, keyData))
  end

  local jsonTXT = mw.text.jsonEncode(options, mw.text.JSON_PRETTY)
  txt0 = txt0 .. "\n== JSON généré ==\n" .. jsonTXT
  txt0 = txt0 .. "\n== Graph ==\n" .. frame:preprocess("{{#echarts:height=600px|option = " .. jsonTXT .. " }}")

  return txt0
  end

return p
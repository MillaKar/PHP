<?php

function createColumnHeaders($columns2Include) {
    $t = "<thead>";
    $t = "<tr>";
    for($i = 0; $i < count($columns2Include); $i++ ) {
        $val = $columns2Include[$i];
        $t .= '<th scope="col">'.$val."</th>";
    }
    $t .= "</tr></thead>";    
    return $t;
}

function createTableRow($product,$columns2Include,$columnNamesMap) {
    $t = "<tr>";
    for($i = 0; $i < count($columns2Include); $i++ ) {
        $columnName = $columns2Include[$i];
        $item = $product[ $columnNamesMap[$columnName]];
        if($i == 0) {
            $t .= '<th scope="row">'.$item."</td>";
        } else {
            $t .= "<td>".$item."</td>";
        }
    }
    $t .= "</tr>";    
    return $t;    
}
/**
 * Creates a html-table from alko products
 * @param type $products array of products
 * @param type $columns2Include names of columns to include
 * @param type $columnNamesMap column names to index mas
 * @param $filters['LIMIT'] max nbr of items to include
 *        $filters['PAGE'] page to start from
 *        $filters['TYPE'] product type
 *        $filters['COUNTRY'] product country
 *        $filters['PRICELOW'] price low limit
 *        $filters['PRICEHIGH'] price high limit
 * @return string html table 
 */
function createAlkoProductsTable($products, $columns2Include, $columnNamesMap, $filters, $tblId) {
    $limitCounter = 0;
    $limitCounterLow = $filters['LIMIT']*$filters['PAGE'];
    $limitCounterHigh = $limitCounterLow + $filters['LIMIT'];
    
    if($tblId != null) {
        $t = "<table id=\"$tblId\" class=\"table\">";    
    } else {
        $t = '<table class="table">';    
    }
    $t .= createColumnHeaders($columns2Include); 
    $t .= '<tbody>';
    for($i = 0; $i < count($products); $i++) {
        $product = $products[$i];
        
        // filters
        if($filters['TYPE'] != null){
            if($product[$columnNamesMap['Tyyppi']] !== $filters['TYPE']) {
                continue;
            }
        }
        if($filters['COUNTRY'] != null){
            if($product[$columnNamesMap['Valmistusmaa']] !== $filters['COUNTRY']) {
                continue;
            }
        }
        
        if(!empty($filters['PRICE'])){
        $parts = explode('-', $filters['PRICE']);   // esim. "10-20" → ['10','20']
        $minPrice = (float)$parts[0];
        $maxPrice = (float)$parts[1];
        $price = (float)$product[$columnNamesMap['Hinta']];
        if($price < $minPrice || $price > $maxPrice) continue;
    }

        if(!empty($filters['SIZE'])){
        $csvSize = str_replace([' ', ',', 'l', 'L'], ['', '.', '', ''], $product[$columnNamesMap['Pullokoko']]);
        $filterSize = str_replace(',', '.', $filters['SIZE']);
        if ((float)$csvSize !== (float)$filterSize) continue;
    }

        if(!empty($filters['ENERGY'])) {
        $parts = explode('-', $filters['ENERGY']);  // esim. "50-100" → ['50','100']
        $minEnergy = (float)$parts[0];
        $maxEnergy = (float)$parts[1];
        $energyValue = (float)$product[$columnNamesMap['Energia kcal/100 ml']];
        if($energyValue < $minEnergy || $energyValue > $maxEnergy) continue;
    }


        
        // limit items to include into table
        $limitCounter++;
        if($limitCounter > $limitCounterLow) {
            $t .= createTableRow($product,$columns2Include,$columnNamesMap);
            if($limitCounter >= $limitCounterHigh) {
                break;
            }
        }
    }
    $t .= '</tbody>';
    $t .= "</table>";
    return $t;
}

function generateView($alkoData, $filters, $tblId=null) {
    global $columns2Include, $columnNamesMap;
    
    if($filters['MODE'] === 'view') {
        $alkoProductTable = createAlkoProductsTable(
            $alkoData, $columns2Include, $columnNamesMap, $filters, $tblId);
        return $alkoProductTable;
    } else if ($filters['MODE'] === 'update') {
        // TODO: Update the csv file
        return "<h2>Update csv file from original source</h2>";
    } else {
        // TODO unknown command
        return "<h2>Unknown command </h2>";
    }
}

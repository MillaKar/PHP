<script>
function setCookies() {
    const country = document.getElementById("country").value;
    const type = document.getElementById("type").value;
    const size = document.getElementById("size").value;
    const price = document.getElementById("price").value;
    const energy = document.getElementById("energy").value;

    function setCookie(name, value) {
        if (!value) {
            document.cookie = name + "=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT";
        } else {
            document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + "; path=/";
        }
    }

    setCookie("country", country);
    setCookie("type", type);
    setCookie("size", size);
    setCookie("price", price);
    setCookie("energy", energy);

    const params = new URLSearchParams();
    if (country) params.set("country", country);
    if (type) params.set("type", type);
    if (size) params.set("size", size);
    if (price) params.set("price", price);
    if (energy) params.set("energy", energy);

    window.location = "./index.php?page=0" + (params.toString() ? "&" + params.toString() : "");
    }

    function resetFilters() {
        const filterNames = ["country", "type", "size", "price", "energy"];
        filterNames.forEach(name => {
            document.cookie = name + "=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT";
        });
        window.location = "./index.php?page=0";
    }

</script>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Alkon hinnasto</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <?php
        //require("urlHandler.php");
        //require("handlePriceList.php");
        require("model.php");
        require_once("controller.php");
        $alkoData = initModel();
        $filters = handleRequest();
        $alkoProductTable = generateView($alkoData, $filters, 'products');
        
        $rowsFound = count($alkoData);
        // echo "<h1>Alkon hinnasto $priceListDate Total rows found $rowsFound</h1>";
        require_once __DIR__ . '/vendor/shuchkin/simplexlsx/src/SimpleXLSX.php';

        $remote_xlsx_url = "https://www.alko.fi/INTERSHOP/static/WFS/Alko-OnlineShop-Site/-/Alko-OnlineShop/fi_FI/Alkon%20Hinnasto%20Tekstitiedostona/alkon-hinnasto-tekstitiedostona.xlsx";
        $tempFile = tempnam(sys_get_temp_dir(), 'alko_');
        file_put_contents($tempFile, file_get_contents($remote_xlsx_url));

        $paivitysAika = file_exists("hinnasto_paivitysaika.txt")
            ? trim(file_get_contents("hinnasto_paivitysaika.txt"))
            : "Ei vielä päivitystä suoritettu";

        echo "<div id='tbl-header' class='alert alert-success' role=''>
                Alkon hinnasto – viimeksi päivitetty: $paivitysAika<br>
                (Total items $rowsFound)
            </div>";

        $currpage = isset($filters['PAGE']) ? $filters['PAGE'] : 0;
        $prevpage = ($currpage > 0 ? $currpage-1 : 0); // $filters['LIMIT'] ? $currpage-$filters['LIMIT'] : 0; // previous page (0 is the minimum)
        $nextpage = $currpage = $currpage+1; //$filters['LIMIT'];    // next page (no max checked ;) )
        $paramStr = "";
        foreach (['country','type','size','price','energy'] as $f) {
            if (!empty($_COOKIE[$f])) $paramStr .= "&$f=" . urlencode($_COOKIE[$f]);
                }

        echo '<div class="text-center my-3">';

        echo "<input type=button onClick=\"location.href='./index.php?page=".$prevpage.$paramStr."'\" value='Edellinen'>";
        echo "<input type=button onClick=\"location.href='./index.php?page=".$nextpage.$paramStr."'\" value='Seuraava'>";
        ?>

        <?php
        $selected_country = $_GET['country'] ?? $_COOKIE['country'] ?? "";
        $selected_type    = $_GET['type']    ?? $_COOKIE['type']    ?? "";
        $selected_size    = $_GET['size']    ?? $_COOKIE['size']    ?? "";
        $selected_price   = $_GET['price']   ?? $_COOKIE['price']   ?? "";
        $selected_energy  = $_GET['energy']  ?? $_COOKIE['energy']  ?? "";
        ?>

        <form class='d-inline-block' onsubmit='setCookies(); return false;'>

        <select name='country' id='country' class='form-control d-inline-block w-auto mx-1'>
            <option value=''>--- Valmistusmaa ---</option>
            <option value='Alankomaat'   <?= $selected_country == "Alankomaat"   ? "selected" : "" ?>>Alankomaat</option>
            <option value='Alkuperämaa vaihtelee'   <?= $selected_country == "Alkuperämaa vaihtelee"   ? "selected" : "" ?>>Alkuperämaa vaihtelee</option>
            <option value='Argentiina'   <?= $selected_country == "Argentiina"   ? "selected" : "" ?>>Argentiina</option>
            <option value='Armenia'   <?= $selected_country == "Armenia"   ? "selected" : "" ?>>Armenia</option>
            <option value='Australia'   <?= $selected_country == "Australia"   ? "selected" : "" ?>>Australia</option>
            <option value='Barbados'   <?= $selected_country == "Barbados"   ? "selected" : "" ?>>Barbados</option>
            <option value='Belgia'   <?= $selected_country == "Belgia"   ? "selected" : "" ?>>Belgia</option>
            <option value='Brasilia'   <?= $selected_country == "Brasilia"   ? "selected" : "" ?>>Brasilia</option>
            <option value='Bulgaria'   <?= $selected_country == "Bulgaria"   ? "selected" : "" ?>>Bulgaria</option>
            <option value='Chile'   <?= $selected_country == "Chile"   ? "selected" : "" ?>>Chile</option>
            <option value='Dominikaaninen tasavalta'   <?= $selected_country == "Dominikaaninen tasavalta"   ? "selected" : "" ?>>Dominikaaninen tasavalta</option>
            <option value='Englanti'   <?= $selected_country == "Englanti"   ? "selected" : "" ?>>Englanti</option>
            <option value='Espanja' <?= $selected_country == "Espanja" ? "selected" : "" ?>>Espanja</option>
            <option value='Etelä-Afrikka'   <?= $selected_country == "Etelä-Afrikka"   ? "selected" : "" ?>>Etelä-Afrikka</option>
            <option value='Etelä-Korea'   <?= $selected_country == "Etelä-Korea"   ? "selected" : "" ?>>Etelä-Korea</option>
            <option value='Euroopan unioni'   <?= $selected_country == "Euroopan unioni"   ? "selected" : "" ?>>Euroopan unioni</option>
            <option value='Georgia'   <?= $selected_country == "Georgia"   ? "selected" : "" ?>>Georgia</option>
            <option value='Guatemala'   <?= $selected_country == "Guatemala"   ? "selected" : "" ?>>Guatemala</option>
            <option value='Guyana'   <?= $selected_country == "Guyana"   ? "selected" : "" ?>>Guyana</option>
            <option value='Intia'   <?= $selected_country == "Intia"   ? "selected" : "" ?>>Intia</option>
            <option value='Irlanti'   <?= $selected_country == "Irlanti"   ? "selected" : "" ?>>Irlanti</option>
            <option value='Italia' <?= $selected_country == "Italia" ? "selected" : "" ?>>Italia</option>
            <option value='Itävalta' <?= $selected_country == "Itävalta" ? "selected" : "" ?>>Itävalta</option>
            <option value='Jamaika'   <?= $selected_country == "Jamaika"   ? "selected" : "" ?>>Jamaika</option>
            <option value='Japani' <?= $selected_country == "Japani" ? "selected" : "" ?>>Japani</option>
            <option value='Kanada'   <?= $selected_country == "Kanada"   ? "selected" : "" ?>>Kanada</option>
            <option value='Kiina'   <?= $selected_country == "Kiina"   ? "selected" : "" ?>>Kiina</option>
            <option value='Kolumbia'   <?= $selected_country == "Kolumbia"   ? "selected" : "" ?>>Kolumbia</option>
            <option value='Kreikka' <?= $selected_country == "Kreikka" ? "selected" : "" ?>>Kreikka</option>
            <option value='Kroatia' <?= $selected_country == "Kroatia" ? "selected" : "" ?>>Kroatia</option>
            <option value='Kuuba'   <?= $selected_country == "Kuuba"   ? "selected" : "" ?>>Kuuba</option>
            <option value='Kypros'   <?= $selected_country == "Kypros"   ? "selected" : "" ?>>Kypros</option>
            <option value='Latvia'   <?= $selected_country == "Latvia"   ? "selected" : "" ?>>Latvia</option>
            <option value='Libanon' <?= $selected_country == "Libanon" ? "selected" : "" ?>>Libanon</option>
            <option value='Liettua'   <?= $selected_country == "Liettua"   ? "selected" : "" ?>>Liettua</option>
            <option value='Luxemburg'   <?= $selected_country == "Luxemburg"   ? "selected" : "" ?>>Luxemburg</option>
            <option value='Martinique'   <?= $selected_country == "Martinique"   ? "selected" : "" ?>>Martinique</option>
            <option value='Mauritius'   <?= $selected_country == "Mauritius"   ? "selected" : "" ?>>Mauritius</option>
            <option value='Meksiko'   <?= $selected_country == "Meksiko"   ? "selected" : "" ?>>Meksiko</option>
            <option value='Moldova' <?= $selected_country == "Moldova" ? "selected" : "" ?>>Moldova</option>
            <option value='Muu alkuperämaa'   <?= $selected_country == "Muu alkuperämaa"   ? "selected" : "" ?>>Muu alkuperämaa</option>
            <option value='Nicaragua'   <?= $selected_country == "Nicaragua"   ? "selected" : "" ?>>Nicaragua</option>
            <option value='Norja'   <?= $selected_country == "Norja"   ? "selected" : "" ?>>Norja</option>
            <option value='Panama'   <?= $selected_country == "Panama"   ? "selected" : "" ?>>Panama</option>
            <option value='Peru'   <?= $selected_country == "Peru"   ? "selected" : "" ?>>Peru</option>
            <option value='Pohjois-Irlanti'   <?= $selected_country == "Pohjois-Irlanti"   ? "selected" : "" ?>>Pohjois-Irlanti</option>
            <option value='Pohjois-Makedonia' <?= $selected_country == "Pohjois-Makedonia" ? "selected" : "" ?>>Pohjois-Makedonia</option>
            <option value='Portugali' <?= $selected_country == "Portugali" ? "selected" : "" ?>>Portugali</option>
            <option value='Puerto Rico'   <?= $selected_country == "Puerto Rico"   ? "selected" : "" ?>>Puerto Rico</option>
            <option value='Puola'   <?= $selected_country == "Puola"   ? "selected" : "" ?>>Puola</option>
            <option value='Ranska' <?= $selected_country == "Ranska" ? "selected" : "" ?>>Ranska</option>
            <option value='Romania' <?= $selected_country == "Romania" ? "selected" : "" ?>>Romania</option>
            <option value='Ruotsi' <?= $selected_country == "Ruotsi" ? "selected" : "" ?>>Ruotsi</option>
            <option value='Saksa' <?= $selected_country == "Saksa" ? "selected" : "" ?>>Saksa</option>
            <option value='Serbia' <?= $selected_country == "Serbia" ? "selected" : "" ?>>Serbia</option>
            <option value='Skotlanti' <?= $selected_country == "Skotlanti" ? "selected" : "" ?>>Skotlanti</option>
            <option value='Slovakia' <?= $selected_country == "Slovakia" ? "selected" : "" ?>>Slovakia</option>
            <option value='Slovenia' <?= $selected_country == "Slovenia" ? "selected" : "" ?>>Slovenia</option>
            <option value='Suomi'   <?= $selected_country == "Suomi"   ? "selected" : "" ?>>Suomi</option>
            <option value='Sveitsi' <?= $selected_country == "Sveitsi" ? "selected" : "" ?>>Sveitsi</option>
            <option value='Syyria'   <?= $selected_country == "Syyria"   ? "selected" : "" ?>>Syyria</option>
            <option value='Suomi'   <?= $selected_country == "Suomi"   ? "selected" : "" ?>>Suomi</option>
            <option value='Taiwan' <?= $selected_country == "Taiwan" ? "selected" : "" ?>>Taiwan</option>
            <option value='Tanska'   <?= $selected_country == "Tanska"   ? "selected" : "" ?>>Tanska</option>
            <option value='Thaimaa'   <?= $selected_country == "Thaimaa"   ? "selected" : "" ?>>Thaimaa</option>
            <option value='Trinidad ja Tobago'   <?= $selected_country == "Trinidad ja Tobago"   ? "selected" : "" ?>>Trinidad ja Tobago</option>
            <option value='Tsekki'   <?= $selected_country == "Tsekki"   ? "selected" : "" ?>>Tsekki</option>
            <option value='Turkki' <?= $selected_country == "Turkki" ? "selected" : "" ?>>Turkki</option>
            <option value='Ukraina' <?= $selected_country == "Ukraina" ? "selected" : "" ?>>Ukraina</option>
            <option value='Unkari' <?= $selected_country == "Unkari" ? "selected" : "" ?>>Unkari</option>
            <option value='Uruguay' <?= $selected_country == "Uruguay" ? "selected" : "" ?>>Uruguay</option>
            <option value='Uusi-Seelanti' <?= $selected_country == "Uusi-Seelanti" ? "selected" : "" ?>>Uusi-Seelanti</option>
            <option value='Venezuela'   <?= $selected_country == "Venezuela"   ? "selected" : "" ?>>Venezuela</option>
            <option value='Viro'   <?= $selected_country == "Viro"   ? "selected" : "" ?>>Viro</option>
            <option value='Wales'   <?= $selected_country == "Wales"   ? "selected" : "" ?>>Wales</option>
            <option value='Yhdysvallat' <?= $selected_country == "Yhdysvallat" ? "selected" : "" ?>>Yhdysvallat</option>
        </select>

        <select name='type' id='type' class='form-control d-inline-block w-auto mx-1'>
            <option value=''>--- Tyyppi ---</option>
            <option value='alkoholittomat' <?= $selected_type == "alkoholittomat" ? "selected" : "" ?>>Alkoholittomat</option>
            <option value='brandyt, armanjakit ja calvadosit' <?= $selected_type == "brandyt, armanjakit ja calvadosit" ? "selected" : "" ?>>Brandyt, armanjakit ja calvadosit</option>
            <option value='ginit ja maustetut viinat' <?= $selected_type == "ginit ja maustetut viinat" ? "selected" : "" ?>>Ginit ja maustetut viinat</option>
            <option value='hanapakkaukset' <?= $selected_type == "hanapakkaukset" ? "selected" : "" ?>>Hanapakkaukset</option>
            <option value='juomasekoitukset' <?= $selected_type == "juomasekoitukset" ? "selected" : "" ?>>Juomasekoitukset</option>
            <option value='jälkiruokaviinit, väkevöidyt ja muut viinit' <?= $selected_type == "jälkiruokaviinit, väkevöidyt ja muut viinit" ? "selected" : "" ?>>Jälkiruokaviinit, väkevöidyt ja muut viinit</option>
            <option value='konjakit' <?= $selected_type == "konjakit" ? "selected" : "" ?>>Konjakit</option>
            <option value='kuohuviinit ja samppanjat' <?= $selected_type == "kuohuviinit ja samppanjat" ? "selected" : "" ?>>Kuohuviinit ja samppanjat</option>
            <option value='liköörit ja katkerot' <?= $selected_type == "liköörit ja katkerot" ? "selected" : "" ?>>Liköörit ja katkerot</option>
            <option value='oluet' <?= $selected_type == "oluet" ? "selected" : "" ?>>Oluet</option>
            <option value='punaviinit'  <?= $selected_type == "punaviinit"  ? "selected" : "" ?>>Punaviini</option>
            <option value='rommit'  <?= $selected_type == "rommit"  ? "selected" : "" ?>>Rommit</option>
            <option value='roseeviinit'  <?= $selected_type == "roseeviinit"  ? "selected" : "" ?>>Roseeviinit</option>
            <option value='siiderit'  <?= $selected_type == "siiderit"  ? "selected" : "" ?>>Siiderit</option>
            <option value='valkoviinit' <?= $selected_type == "valkoviinit" ? "selected" : "" ?>>Valkoviinit</option>
            <option value='viinijuomat'  <?= $selected_type == "viinijuomat"  ? "selected" : "" ?>>Viinijuomat</option>
            <option value='viskit'      <?= $selected_type == "viskit"      ? "selected" : "" ?>>Viskit</option>
            <option value='vodkat ja viinat'  <?= $selected_type == "vodkat ja viinat"  ? "selected" : "" ?>>Vodkat ja viinat</option>
        </select>

        <select name='size' id='size' class='form-control d-inline-block w-auto mx-1'>
            <option value=''>--- Pullon koko ---</option>
            <option value='0.02-0.1'     <?= $selected_price == "0.02-0.1"     ? "selected" : "" ?>>0.02-0.1 L</option>
            <option value='0.1-0.33' <?= $selected_size == "0.1-0.33" ? "selected" : "" ?>>0.1-0.33 L</option>
            <option value='0.33-0.5'  <?= $selected_size == "0.33-0.5"  ? "selected" : "" ?>>0.33-0.5 L</option>
            <option value='0.5-0.75' <?= $selected_size == "0.5-0.75" ? "selected" : "" ?>>0.5-0.65 L</option>
            <option value='0.75-1.0'  <?= $selected_size == "0.75-1.0"  ? "selected" : "" ?>>0.65-1.0 L</option>
            <option value='1.0-3.00'  <?= $selected_size == "1.0-3.00"  ? "selected" : "" ?>>1.0-3.00 L</option>
            <option value='3.00-50'  <?= $selected_size == "3.00-50"  ? "selected" : "" ?>>3.00+ L</option>
        </select>

        <select name='price' id='price' class='form-control d-inline-block w-auto mx-1'>
            <option value=''>--- Hinta ---</option>
            <option value='0-10'     <?= $selected_price == "0-10"     ? "selected" : "" ?>>0–10 €</option>
            <option value='10-20'    <?= $selected_price == "10-20"    ? "selected" : "" ?>>10–20 €</option>
            <option value='20-30'    <?= $selected_price == "20-30"    ? "selected" : "" ?>>20–30 €</option>
            <option value='30-50'    <?= $selected_price == "30-50"    ? "selected" : "" ?>>30–50 €</option>
            <option value='50-100'   <?= $selected_price == "50-100"   ? "selected" : "" ?>>50–100 €</option>
            <option value='100-9999' <?= $selected_price == "100-9999" ? "selected" : "" ?>>100+ €</option>
        </select>

        <select name='energy' id='energy' class='form-control d-inline-block w-auto mx-1'>
            <option value=''>--- Energia kcal/100 ml ---</option>
            <option value='0-50'     <?= $selected_energy == "0-50"     ? "selected" : "" ?>>0–50 kcal</option>
            <option value='50-100'   <?= $selected_energy == "50-100"   ? "selected" : "" ?>>50–100 kcal</option>
            <option value='100-150'  <?= $selected_energy == "100-150"  ? "selected" : "" ?>>100–150 kcal</option>
            <option value='150-200'  <?= $selected_energy == "150-200"  ? "selected" : "" ?>>150–200 kcal</option>
            <option value='200-9999' <?= $selected_energy == "200-9999" ? "selected" : "" ?>>200+ kcal</option>
        </select>

        <input type='button' class='btn btn-secondary mx-2' onClick='setCookies()' value='Suodata'>
        <input type='button' class='btn btn-warning mx-2' onClick='resetFilters()' value='Poista valinnat'>
        <input type='button' class='btn btn-warning mx-2' onClick="location.href='update.php'" value='Päivitä hinnasto'>

        </form>
        <?php

        echo '</div>';
        // display products table here
        echo $alkoProductTable;
   
        //testXlxs();
        //fetchXlxs($remote_filename_xlsl, $local_filename_xlsl);
        ?>
    </body>
</html>

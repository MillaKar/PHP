<?php

// url parameters must have correct values if not defined in url


function handleRequest() {
    
    $filters['MODE'] = $_GET['mode'] ?? 'view';
    $filters['TYPE'] = $_GET['type'] ?? ($_COOKIE['type'] ?? null);
    $filters['LIMIT'] = $_GET['limit'] ?? 25;
    $filters['PAGE'] = $_GET['page'] ?? 0;
    $filters['COUNTRY'] = $_GET['country'] ?? null;
    $filters['PRICE'] = $_GET['price'] ?? ($_COOKIE['price'] ?? null);
    $filters['SIZE'] = $_GET['size'] ?? ($_COOKIE['size'] ?? null);
    $filters['ENERGY'] = $_GET['energy'] ?? ($_COOKIE['energy'] ?? null);
    $filters['PRICELOW'] = $_GET['minprice'] ?? null;
    $filters['PRICEHIGH'] = $_GET['maxprice'] ?? null;

    return $filters;    
}

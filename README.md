## Vending machine by Prompt

### Pre-install

`run composer install`
 
### Tests: `./bin/vendor/phpunit`

### How to run: 

```prompt
php -f ./src/index.php

-> 

Insert available command:

availableProducts
exit
help
service


Example: service help menu
service help
-> 
Service Menu:
 service +
 - enable-maintenance
 - disable-maintenance
 - get-stock
 - add-stock
 - get-coins
 - add-coins
 - help



Example 1: Buy Soda with exact change
1, 0.25, 0.25, GET-SODA
-> 

Soda
Exact price


Example 2: Start adding money, but user ask for return coin
0.10, 0.10, RETURN-COIN
-> 
Return coins
0.10, 0.10


Example 3: Buy Water without exact change
1, GET-WATER
->
Water
Return change 0.35
Return 0.25 x 1
Return 0.1 x 1


Example 4: Add 3 soda's
service add-stock soda 3
-> Soda - 1.5 - 4 Units


Example 5: Add not existing coin
service add-coins .5 2

-> Invalid coin

Example 5: Add existing coin
service add-coins .25 2

-> 0.25 - 12 Units


```


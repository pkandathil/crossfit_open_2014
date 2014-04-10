Crossfit Open 2014
==================

Use this script to screen scrape the data for the 2014 crossfit open.


How to use for gathering data
==================
- Open crossfit.php
- Set the $TOTAL_PAGES and $GENDER variables
- Go to your terminal
```sh
php crossfit.php
```
- The results will be output in the same folder as stats-<gender>.csv. 


How to use for benchmarking
===================
- Open crossfit.php
- Set the $TOTAL_PAGES. It will determine the number of times code is executed. 
- Set 
```php
$GET_DATA_FROM_FILE = true;
```

- Go to your terminal
```sh
php crossfit.php
```
OR
```sh
hhvm crossfit.php
```
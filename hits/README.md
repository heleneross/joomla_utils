joomla_utils
============


# hits module helper #

generic module helper to count hits ie. when module is rendered.

copy `helper.php` into your module folder and add this line at the start of your main module file mod_xxx

    require_once dirname(__FILE__) . '/helper.php';

call it in the template file for your module ie. after the module has rendered either with no parameters, in which case it only counts hits

    modHelper::hits();

or by passing it a string with the name of the item you want logging - ie. I set
 
    $source = 'cached'; or $source = 'uncached';

depending on whether the module contents were retrieved from the cache or not

    modHelper::hits($source);

the `helper.php` will create a file in the module directory called `hits.php`, which is basically an ini file.

It will add the updated time to the file and increment hits by one, it will also increment the value for the key (if passed) by one

the contents of the hits file will look a little like:
    ;<?php die("Access Denied");
    created=Thu, 17 Apr 2014 19:09:23 +0200
    updated=Thu, 17 Apr 2014 19:10:47 +0200
    hits=3
    cached=2
    uncached=1



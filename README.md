Code 215: evofw
===============

# About
I created this framework when I started coding lcnlegacy.com to help me speed up development time.

# Using evofw
Create a new config file

    <?php
    $config=array(
        'default' => array(
            'path'=>'inc/site/',
            'database'=>array(
                'default'=>array(
                    'host'=>'localhost',
                    'user'=>'username',
                    'pass'=>'password',
                    'name'=>'databasename', 
                ),
            ),
            'default_system' => 'site',  //this is the default class to call (site.php)      
            'autoLoadDB'=>true,
        )
    );
    ?>  

Create your default page

    <?php
    define('confFile', 'location_of_config_file');
    include('path_to_evofw/src/system.php');
    $site=new System;
    //The following in how I personally load pages. Will take index.php?page=site&action=test and load the action_test() in site.php
    $page=$site->getVaule('page');
    $action=$site->getValue('action');
    $site->load($page, $action)
    ?>

#Examples
* [LoLStats](https://bitbucket.org/sean111/lolstats) - A site I built to display LoL data that I collect from LoLKing

# License
I am currently looking into which OS license I should put this under. Currently it's free to use as long as you don't sell the code.

# Helping
No one is perfect and I am definitally far from it. If you see something you would like to fix please fork the code and send me a pull request. 

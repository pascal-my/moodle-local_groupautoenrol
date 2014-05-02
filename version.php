<?php
defined('MOODLE_INTERNAL') || die();

$plugin->version = 2014042202;    // The (date) version of this module + 2 extra digital for daily versions
                                  // This version number is displayed into /admin/forms.php
$plugin->requires = 2013040500;   // Requires this Moodle version - at least 2.0
$plugin->cron = 0;
$plugin->component = 'local_groupautoenrol';
$plugin->release = '1.2';
$plugin->maturity = MATURITY_BETA;

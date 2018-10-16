<?php

if( version_compare( PHP_VERSION, '7.0.0' ) < 0 ) {
  class_alias(
    '\IronBound\WP_Notifications\Strategy\Null_Mail',
    '\IronBound\WP_Notifications\Strategy\Null'
  );
}

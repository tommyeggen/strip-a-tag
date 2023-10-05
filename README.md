# strip-a-tag

Remove a given html-tag from any html input.


```PHP
<?php

require 'strip_a_tag.php';

echo strip_a_tag(file_get_contents('content.html'), 'a', false);

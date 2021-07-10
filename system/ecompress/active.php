<?php

session_start();

$_SESSION['reset'] = ($_GET['reset'] == "true") ? 'true' : 'false';

echo "Reset caches: ".($_SESSION['reset']);


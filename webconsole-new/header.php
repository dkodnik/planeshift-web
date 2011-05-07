<?php
  $time1 = microtime(true);
?><!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<meta name="author" content="PlaneShift MMORPG" />
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="./global.css" />
<title>PlaneShift - Administrator Console (<?php
  echo gethostname();
  echo ")";

  if (isset($header))
    echo ": $Header";
?>
</title>
<script type="text/javascript" src="./jquery.js"></script> 
</head>
<body>
<div class="container">

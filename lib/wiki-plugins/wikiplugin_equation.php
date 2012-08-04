<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_equation.php 40035 2012-03-04 21:22:53Z gezzzan $

function wikiplugin_equation_info()
{
	return array(
		'name' => tra('Equation'),
		'documentation' => 'PluginEquation',
		'description' => tra('Render an equation written in LaTeX syntax as an image.'),
		'prefs' => array('wikiplugin_equation'),
		'body' => tra('equation'),
		'icon' => 'img/icons/sum.png',
		'params' => array(
			'size' => array(
				'required' => false,
				'name' => tra('Size'),
				'description' => tra('Size expressed as a percentage of the normal size. 100 produces the default size. 200 produces an image twice as large.'),
				'default' => 100,
				'filter' => 'digits',
			),
		),
	);
}

function wikiplugin_equation($data, $params)
{
	if (empty($data)) return '';
    extract($params, EXTR_SKIP);
    if (empty($size)) $size = 100;

    $latexrender_path = getcwd() . "/lib/equation"; 
    include_once($latexrender_path . "/class.latexrender.php");
    $latexrender_path_http = "lib/equation";
    $latex = new LatexRender($latexrender_path."/pictures", $latexrender_path_http."/pictures", $latexrender_path."/tmp");
    $latex->_formula_density = 120 * $size / 100;

	extract($params, EXTR_SKIP);

	$data=html_entity_decode(trim($data), ENT_QUOTES);

    $url = $latex->getFormulaURL($data);
    $alt = "~np~" . $data . "~/np~";

    if ($url != false) {
        $html = "<img src=\"$url\" alt=\"$alt\" style=\"vertical-align:middle\">";
    } else {
        $html = "__~~#FF0000:Unparseable or potentially dangerous latex formula. Error {$latex->_errorcode} {$latex->_errorextra}~~__";
    }
	return $html;
}

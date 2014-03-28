<?php
/* zKillboard
 * Copyright (C) 2012-2013 EVE-KILL Team and EVSCO.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Load Twig globals
$app->view(new \Slim\Extras\Views\Twig());

// Theme
$viewtheme = null;
if(User::isLoggedIn())
	$viewtheme = UserConfig::get("viewtheme");
$cachepath = "cache/templates/" . ($viewtheme ? $viewtheme : "bootstrap");

\Slim\Extras\Views\Twig::$twigOptions = array(
    'charset'           => 'utf-8',
    'cache'             => $cachepath,
    'auto_reload'       => true,
    'strict_variables'  => false,
    'autoescape'        => true
);

$twig = $app->view()->getEnvironment();

$uri = $_SERVER["REQUEST_URI"];
$explode = explode("/", $uri);
foreach($explode as $key => $ex)
{

    if(in_array($ex, array("year", "month", "page")))
    {
        // find the key for the page array
        unset($explode[$key]);
        unset($explode[$key+1]);
    }

}

$actualURI = implode("/", $explode);
$twig->addGlobal("actualURI", $actualURI);
$uriParams = Util::convertUriToParameters();
$twig->addGlobal("year", (isset($uriParams["year"]) ? $uriParams["year"] : date("Y")));
$twig->addGlobal("month", (isset($uriParams["month"]) ? $uriParams["month"] : date("m")));
// Twig globals
$twig->addGlobal("image_character", $imageServer."Character/");
$twig->addGlobal("image_corporation", $imageServer."Corporation/");
$twig->addGlobal("image_alliance", $imageServer."Alliance/");
$twig->addGlobal("image_item", $imageServer."Type/");
$twig->addGlobal("image_ship", $imageServer."Render/");

$twig->addGlobal("siteurl", $baseAddr);
$twig->addGlobal("fullsiteurl", $fullAddr);
$twig->addGlobal("requesturi", $_SERVER["REQUEST_URI"]);
$twig->addGlobal("topad", zKillboard::top());
$twig->addGlobal("bottomad", zKillboard::bottom());
$twig->addGlobal("mobiletopad", zKillboard::mobileTop());
$twig->addGlobal("mobilebottomad", zKillboard::mobileBottom());
$twig->addGlobal("igbtopad", zKillboard::igbTop());
$twig->addGlobal("igbbottomad", zKillboard::igbBottom());
$twig->addGlobal("analytics", zKillboard::analytics());
$twig->addGlobal("disqusLoad", $disqus);
if($disqus)
{
    $twig->addGlobal("disqusShortName", $disqusShortName);
    $twig->addglobal("disqus", Disqus::init());
}

$detect = new Mobile_Detect();
$twig->addGlobal("isMobile", ($detect->isMobile() ? true : false));
$twig->addGlobal("isTablet", ($detect->isTablet() ? true : false));

$twig->addExtension(new UserGlobals());

$twig->addFunction(new Twig_SimpleFunction("pageTimer", "Util::pageTimer"));
$twig->addFunction(new Twig_SimpleFunction("queryCount", "Db::getQueryCount"));
$twig->addFunction(new Twig_SimpleFunction("isActive", "Util::isActive"));
$twig->addFunction(new Twig_SimpleFunction("pluralize", "Util::pluralize"));
$twig->addFunction(new Twig_SimpleFunction("calcX", "Util::calcX"));
$twig->addFunction(new Twig_SimpleFunction("calcY", "Util::calcY"));
$twig->addFunction(new Twig_SimpleFunction("formatIsk", "Util::formatIsk"));
$twig->addFunction(new Twig_SimpleFunction("shortNum", "Util::formatIsk"));
$twig->addFunction(new Twig_SimpleFunction("shortString", "Util::shortString"));
$twig->addFunction(new Twig_SimpleFunction("truncate", "Util::truncate"));
$twig->addFunction(new Twig_SimpleFunction("chart", "Chart::addChart"));
$twig->addFunction(new Twig_SimpleFunction("getMonth", "Util::getMonth"));
$twig->addFunction(new Twig_SimpleFunction("getLongMonth", "Util::getLongMonth"));
$twig->addFunction(new Twig_SimpleFunction("isMaintenance", "Util::isMaintenanceMode"));
$twig->addFunction(new Twig_SimpleFunction("getMaintenanceReason", "Util::getMaintenanceReason"));
$twig->addFunction(new Twig_SimpleFunction("getNotification", "Util::getNotification"));

$igb = false;
if(stristr(@$_SERVER["HTTP_USER_AGENT"], "EVE-IGB"))
	$igb = true;
$twig->addGlobal("eveigb", $igb);

<?php

namespace RMSCatalog\Controllers;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Sitemap
{
	/**
	 * @type  \Silex\Application
	 */
	protected $app;

	/**
	 * A method
	 * 
	 * @param  \Silex\Application	$app	The Silex app
	 * @return string						The rendered view
	 */
	public function get(\Silex\Application $app)
	{
		$this->app = $app;

		return $app['twig']->render('sitemap.twig', array('urls' => $urls));
	}

}

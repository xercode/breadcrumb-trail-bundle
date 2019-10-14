<?php

/*
 * This file is part of the xeBook package.
 *
 * (c) Xercode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace xeBook\Bundle\BreadcrumbTrailBundle\BreadcrumbTrail;

final class Breadcrumb
{
    /**
     * @var string Title of the breadcrumb
     */
    public $title;

    /**
     * @var string Url of the breadcrumb
     */
    public $url;

    /**
     * @var mixed Additional attributes for the breadcrumb
     */
    public $attributes;

    /**
     * Constructor.
     *
     * @param string $title Title of the breadcrumb
     * @param string $url Url of the breadcrumb
     * @param mixed $attributes Additional attributes for the breadcrumb
     */
    public function __construct($title, $url = null, $attributes = array())
    {
        $this->title = $title;
        $this->url = $url;
        $this->attributes = $attributes;
    }
}

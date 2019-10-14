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

namespace xeBook\Bundle\BreadcrumbTrailBundle\Twig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use xeBook\Bundle\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;
use Twig\Environment as TwigEnvironment;

final class BreadcrumbTrailExtension extends AbstractExtension
{
    /**
     * @var Trail
     */
    private $trail;

    /**
     * @var TwigEnvironment
     */
    private $twigEnvironment;

    /**
     * BreadcrumbTrailExtension constructor.
     *
     * @param Trail           $trail
     * @param TwigEnvironment $twigEnvironment
     */
    public function __construct(Trail $trail, TwigEnvironment $twigEnvironment)
    {
        $this->trail      = $trail;
        $this->twigEnvironment = $twigEnvironment;
    }


    public function getFunctions()
    {
        return [
            new TwigFunction("xeBook_breadcrumb_trail_render", array($this, "renderBreadcrumbTrail"), array("is_safe" => array("html"))),

        ];
    }

    /**
     * Renders the breadcrumb trail in a list
     *
     * @param null $template
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderBreadcrumbTrail($template = null)
    {
        return $this->twigEnvironment->render(
            $template === null ? $this->trail->getTemplate() : $template,
            [ 'breadcrumbs' => $this->trail ]
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "xeBook_breadcrumb_trail";
    }
}

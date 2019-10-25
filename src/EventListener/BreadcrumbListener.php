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

namespace xeBook\Bundle\BreadcrumbTrailBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
//use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use xeBook\Bundle\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use xeBook\Bundle\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent as ControllerEvent;
final class BreadcrumbListener
{
    /**
     * @var Reader An Reader instance
     */
    protected $reader;

    /**
     *
     * @var Trail An Trail instance
     */
    protected $breadcrumbTrail;

    /**
     * Constructor.
     *
     * @param Reader $reader An Reader instance
     * @param Trail $breadcrumbTrail An Trail instance
     */
    public function __construct(Reader $reader, Trail $breadcrumbTrail)
    {
        $this->reader = $reader;
        $this->breadcrumbTrail = $breadcrumbTrail;
    }

    /**
     * @param ControllerEvent $event A ControllerEvent instance
     * @throws \ReflectionException
     */
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (is_callable($controller) && method_exists($controller, '__invoke')) {
            $controller = [$controller, '__invoke'];
        }

        if (!is_array($controller)) {
            return;
        }

        // Annotations from class
        $class = new \ReflectionClass($controller[0]);

        // Manage JMSSecurityExtraBundle proxy class
        if (false !== $className = $this->getRealClass($class->getName())) {
            $class = new \ReflectionClass($className);
        }

        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class));
        }

        if ($event->getRequestType() == HttpKernelInterface::MASTER_REQUEST) {
            $this->breadcrumbTrail->reset();

            // Annotations from class
            $this->addBreadcrumbsFromAnnotations($this->reader->getClassAnnotations($class));

            // Annotations from method
            $method = $class->getMethod($controller[1]);
            $this->addBreadcrumbsFromAnnotations($this->reader->getMethodAnnotations($method));
        }
    }

    /**
     * Add Breadcrumb from annotations to the trail.
     *
     * @param array $annotations Array of Breadcrumb annotations
     */
    private function addBreadcrumbsFromAnnotations(array $annotations)
    {
        // requirements (@Breadcrumb)
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Breadcrumb) {
                $template = $annotation->getTemplate();
                $title = $annotation->getTitle();

                if ($template != null) {
                    $this->breadcrumbTrail->setTemplate($template);
                    if ($title === null) {
                        continue;
                    }
                }

                $this->breadcrumbTrail->add(
                    $title,
                    $annotation->getRouteName(),
                    $annotation->getRouteParameters(),
                    $annotation->getRouteAbsolute(),
                    $annotation->getPosition(),
                    $annotation->getAttributes()
                );
            }
        }
    }

    private function getRealClass($className)
    {
        if (false === $pos = strrpos($className, '\\__CG__\\')) {
            return false;
        }

        return substr($className, $pos + 8);
    }
}

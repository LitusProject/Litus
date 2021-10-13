<?php

namespace CommonBundle\Component\Mvc\View\Http;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ModelInterface as ViewModel;

class InjectTemplateListener implements ListenerAggregateInterface
{
    /**
     * FilterInterface/inflector used to normalize names for use as template identifiers
     *
     * @var mixed
     */
    protected $inflector;

    /**
     * Listeners we've registered
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * Attach listeners
     *
     * @param  Events $events
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'injectTemplate'), $priority);
    }

    /**
     * Detach listeners
     *
     * @param  Events $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Inject a template into the view model, if none present
     *
     * Template is derived from the controller found in the route match, and,
     * optionally, the action, if present.
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function injectTemplate(MvcEvent $e)
    {
        $model = $e->getResult();
        if (!$model instanceof ViewModel) {
            return;
        }

        $template = $model->getTemplate();
        if ($template != '') {
            return;
        }

        $routeMatch = $e->getRouteMatch();
        $controller = $e->getTarget();
        if (is_object($controller)) {
            // phpcs:disable SlevomatCodingStandard.Classes.ModernClassNameReference.ClassNameReferencedViaFunctionCall
            $controller = get_class($controller);
            // phpcs:enable
        }
        if (!$controller) {
            $controller = $routeMatch->getParam('controller', '');
        }

        $module = $this->deriveModuleNamespace($controller);
        $template .= $this->inflectName($module);

        $controller = $this->deriveControllerClass($controller);
        $template .= $this->inflectName($controller);

        $action = $routeMatch->getParam('action');
        if ($action !== null) {
            $template .= '/' . $this->inflectName($action);
        }
        $model->setTemplate($template);
    }

    /**
     * Inflect a name to a normalized value
     *
     * @param  string $name
     * @return string
     */
    protected function inflectName($name)
    {
        if (!$this->inflector) {
            $this->inflector = new CamelCaseToDashFilter();
        }
        $name = $this->inflector->filter($name);

        return strtolower($name);
    }

    /**
     * Determine the top-level namespace of the controller
     *
     * @param  string $controller
     * @return string
     */
    protected function deriveModuleNamespace($controller)
    {
        if (!strstr($controller, 'Bundle\\')) {
            return '';
        }
        $module = substr($controller, 0, strpos($controller, 'Bundle\\'));

        return $module . '/';
    }

    /**
     * Determine the name of the controller
     *
     * Strip the namespace, and the suffix "Controller" if present.
     *
     * @param  string $controller
     * @return string
     */
    protected function deriveControllerClass($controller)
    {
        if (strstr($controller, 'Controller\\')) {
            $controller = substr($controller, strrpos($controller, 'Controller\\') + strlen('Controller\\'));
        }

        if (10 < strlen($controller)
            && (substr($controller, -10) == 'Controller')
        ) {
            $controller = substr($controller, 0, -10);
        }

        return str_replace('\\', '/', $controller);
    }
}

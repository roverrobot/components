<?

require_once dirname(__FILE__) . '/component_manager.php';
require_once dirname(__FILE__) . '/action.php';

define(DOKU_ACTION_ROOT, '/commands');

class ConflictActionException extends Exception {
	private $first = '';
	private $second = '';
	public function first() { return $this->first; }
	public function second() { return $this->second; }
	public function __construct($first, $second) {
		$this->first = $first;
		$this->second = $second;
	}
}

class Doku_Action_Manager extends Doku_Component_Manager {
    // this array maps action names to their preprocessors
    private $preprocessors = array();
    // this array maps action names to their postprocessors
    private $postprocessors = array();
    // this holds the renderers
    private $renderers = array();
    // this holds the handlers
    private $handlers = array();
    // this holds the renderer
    private $renderer = NULL;
    // this holds the handler
    private $handler = NULL;

    // create an object and check if it responds to the correct action
    private static function create($class, $action) {
        $handler = new $class;
        return ($handler->action() != $action) ? NULL : $handler;
    }

    /**
     * handles a new class that is loaded in
     * @param string $class the name of the new class.
     */
	protected function handle($class) {
        if (is_subclass_of($class, 'Doku_Action_Preprocessor')) {
            $this->preprocessors[] = $class;
            return;
        }
        if (is_subclass_of($class, 'Doku_Action_Postprocessor')) {
            $this->postprocessors[] = $class;
            return;
        }
        if (is_subclass_of($class, 'Doku_Action')) {
            $this->handlers[] = $class;
            return;
        }
        if (is_subclass_of($class, 'Doku_Action_Renderer')) {
            $this->renderers[] = $class;
            return;
    	}
	}

	// filter the classes and populate an array of handlers that
	// can handle the action, and is not extended
	private function filter($classes, $action) {
		$handlers = array();
		foreach ($classes as $class) {
			$handler = $this->create($class, $action);
			if (!$handler) continue;
			if (!$handlers) {
				$handlers[] = $handler;
				continue;
			}
			if ($handler->action() == $action) {
				$handlers = array_map(function($old) use($handler) {
					return (is_subclass_of($handler, get_class($old))) ? $handler : $old;
				}, $handlers);
			}
		}
		return $handlers;
	}

	// filter the classes and find the handler that
	// can handle the action, and is not extended
	private function unique($classes, $action) {
		$handler = NULL;
		foreach ($classes as $class) {
			$new = $this->create($class, $action);
			if (!$handler) {
				$handler = $new;
				continue;
			}
			if ($new && $new->action() == $action) {
				if (is_subclass_of($new, get_class($handler)))
					$handler = $new;
				else if (!is_subclass_of($handler, get_class($new)))
					throw new ConflictActionException($handler, $new);
			}
		}
		return $handler;
	}

	/**
	 * perform the action
	 * the action name must be plugin_name.action_name.
	 * @global string $ID the page ID
	 * @global array $INFO the page information array
	 * @param string $action the action to be peformed;
	 * @return bool whether the action has been performed (regardless of being successful).
	 */
	public function act($action) {
		if (is_array($action)) {
			$result = TRUE;
			foreach ($action as $act => $x) {
				$result = $result && $this->act($act);
				if (!result) return FALSE;
			}
			return TRUE;
		}
		$this->handler = NULL;
		$this->renderer = NULL;
		$this->preprocessors = array();
		$this->postprocessors = array();
		$this->handlers = array();
		$this->renderers = array();

		$components = explode('.', $action);
		if (count($components) <= 1) return FALSE;
		$plugin = array_shift($components);
		$action = implode('.', $components);
		$path = DOKU_PLUGIN  . $plugin . DOKU_ACTION_ROOT;
		$this->load($path, $action);

		$this->preprocessors = $this->filter($this->preprocessors, $action);
		$this->postprocessors = $this->filter($this->postprocessors, $action);
		$this->handler = $this->unique($this->handlers, $action);
		$this->renderer = $this->unique($this->renderers, $action);

        // check if the action is disabled
        if (!actionOK($action)) {
            msg('action disabled: ' . htmlspecialchars($action), -1);
            return self::act("show");
        }
        foreach ($this->preprocessors as $preprocessor)
            $preprocessor->process();

        // check if we can handle the action
        if (!$this->handler) return FALSE;
 
        global $INFO;
        // check permission
        if ($this->handler->permission_required() > $INFO['perm'])
            return $this->act('denied');

        // handle the action
        $new_action = $this->handler->handle();

        // postprocess
        foreach ($this->postprocessors as $postprocessor)
            $postprocessor->process();

        // handle the next action
        if ($new_action && $new_action !== $action)
            return $this->act($new_action);

        return TRUE;
	}

    /**
     * Doku_Action public interface to render the result of an action
     * 
     * @param type $action the action to display
     * @return boolean whether the results has been successfully displayed
     */
    public function render($action) {
        if (!$this->renderer) return FALSE;
		if (is_array($action)) {
			$result = '';
			foreach ($action as $act => $x)
				$result .= $this->render($act);
			return $result;
		}

        ob_start();
        $this->renderer->xhtml();
        $html_output = ob_get_clean();

        trigger_event('TPL_CONTENT_DISPLAY', $html_output, 'ptln');
        return !empty($html_output);
    }
}
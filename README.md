# components
A DokuWiki helper plugin for easily implementing new actions and new ajax functions in plugins

## AJAX calls
To add a new AJAX call handler for a plugin, (for example, an AJAX function call some_plugin.some_call), one needs to put a script some_call.php in the ajax directory under the some_plugin file structure. In this PHP script you need to declare a class that extends the Doku_AJAX class (see its definition in lib/ajax.php, and an example in ajax/example.php).

### AJAX methods
The class must implement 3 methods:

1. `public function name() { return 'some_call'; };`
  * it returns the name of the AJAX call (here, 'some_call'). Note that the actual call is some_plugin.some_call;

2. `protected function auth($params) { return ...; }`
  * here $params is the params that got passed from the client, and it returns TRUE if the user is authorized to call this function, and FALSE otherwise;

3. `protected function call($params) { return ...; }`
  * here $params is the same as above, and the return value is the response to be sent to the client. The return value could be an int, a float, a bool, a strng, or an array.

### The constructor
This class must also define a constructor
```
public function __construct() {
    parent::__construct(
        // the required parameters to the AJAX call
        array(
            'param_1' => 'type 1',
            'param_2' => 'type 2' // etc .
        ),
        // some optional parameters
        array(
            'optional1' => 'type_3' // etc
        )
    );
}
```

### The client side
For the AJAX call, you should use
```
jQuery.ajax(DOKU_BASE.concat('lib/exe/ajax.php'), {
	data: {
		call: 'some_plugin.some_call',
		sectok: sectok,
		param1: some_value1,
		param2: some_value2
		// optionally, include
		// optional1: some_value3
		// note that javascript arrays must use the form
		// some_array: JSON.stringify(some_array)
	}
}).done(function(data) {
	// the data is the returned data from the call function
});
```

### An Example

Try put `<slice from="1" to="10"/>` in your wiki page. This example is included here in
- syntax/slice.php
- script.js
- ajax/example.php

## Action handlers and renderers
See lib/action.php for the definitions of the action handlers, renderers. 
We can also define preprocessors to change the behavior of an action handler, and
define postprocessors to change the behavior of the renderer by changing the
global variable values that the handler and renderer depend on. These handlers must
be put under the commands folder of a plugin, with the script filename the same as
the command name. For example, commands/example.php is the randler for the command 
`pluginname.example`. These files can be put in subdirectories of arbitrary depth to
avoid colliding in file names. You can define multiple pre and postprocessors, but
you can only define at most a single action handler and at most a single renderer.

### Extending a previously defined action
If you extend a hander/renderer/preprocessor/postprocessor, then the subclass replaces 
the parent class.

The key methods:
- `public function action() { return ...; }`
  - This defines the action name that these handlers respond to. Note the full action name is pluginname.actionname
- `public function permission_required() { return ...; }`
  - This returns the minimum required permission for the user to perform this action.
- `public function handle() { ... } 
  - This is the logic that handles the action. If you want to chain performing another action, return the name of that action. For example, you may want to show the page after handling is done, then return 'show'. The handler is called after all preprocessors and before all postprocessors.
- `public function xhtml() { ... }`
  - This is the renderer logic for the action. The randerer is called after all post processors
- `public function process() { ... }`
  - This is the logic for the preprocessors and postprocessors.

### Example:
See the `commands/example.php` file for an example. You can put
```
[[?do=components.example&tag=pre]]
[[?do=components.example&tag=div]]
```
in your wiki text to play with this action.

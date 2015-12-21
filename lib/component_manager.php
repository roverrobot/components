<?php

/**
 * Component_Manager is the parent class of all component managers
 * A component manager manages all components of the same types, for
 * example, all action handlers, or all ajax handlers.
 * @author Junling Ma <junlingm@gmail.com>
 */
abstract class Doku_Component_Manager {
    /**
     * handles a new class that is loaded in
     * @param string $class the name of the new class.
     */
    abstract protected function handle($class);

    protected function load($dir, $name='') {
        $old_classes = array_flip(get_declared_classes());
        $this->load_dir($dir, $name);
        $new_classes = get_declared_classes();
        foreach ($new_classes as $class) {
            // check if this is an abstract class, or bultin class
            $ref_class = new ReflectionClass($class);
            if ($ref_class->isAbstract()) continue;
            if ($ref_class->isInternal()) continue;
            if (!isset($old_classes[$class])) $this->handle($class);
        }
    }

    private function load_dir($dir, $name) {
        if (!is_dir($dir)) return;
        // read the entrys of $dir one by one
        $dh = dir($dir);
        if ($name && strtolower(substr($name, -4)) != '.php') $name .= '.php';
        $subdirs = array();
        while (false !== ($entry = $dh->read())) {
            // skip hidden files
            if ($entry[0] == '.') continue;
            $path = $dir . '/' . $entry;
            if (is_dir($path)) {
                array_push($subdirs, $path);
                continue;
            }

            if (strtolower(substr($entry, -4)) != '.php') continue;

            if (!$name || strtolower($entry) == strtolower($name))
                include_once($dir . '/' . $entry);
        }
        $dh->close();

        // load scripts in subdirs recursively
        foreach ($subdirs as $subdir) $this->load_dir($subdir, $action);
    }
}

# LIP Framework (alpha)

Since 12/07/01

## How to use.

    $LIP = new LIP_Boot();
    $controler = $LIP->get_control( $mode, $func );

### URL Analayze

    define( "RIP_AUTO_CONTROL", TRUE );
    new LIP_Boot();

### Contoroler

    class LC_Contoroler extends LIP_Controler {
    	function LC_Contoroler() {
    		parent::LIP_Controler();
    	}

    	function action( $foo, $bar ) {
    		// Action
    	}
    }

if use modrewrite

    http://example.com/controler/action/foo/bar/

if use pathinfo

    http://example.com/index.php/controler/action/foo/bar/

### Model

    class LM_Model extends LIP_Model {
    	function LM_Model() {
    		parent::LIP_Model();
    	}
    }

in Contoroler Class

    $model = $this->load_model( "model" );

#### *Caution

Using Database Server is 'MySQL' or 'SQLite' only...

### View

Template engine is not included. Because this aims to create a light system.

in Contoroler Class

    // Loaded /app/view/path/to.php
    $this->set_template( "path/to" );
    echo $this->view();

    // Loaded /app/view/path/to.xml
    $this->set_template( "path/to", "xml" );
    echo $this->view();

or

    // Loaded /app/view/path/to.php
    $this->view( "path/to" );

## History

- 2012/07/09 v0.0.1 Published
- 2012/12/18 v0.0.2 Published

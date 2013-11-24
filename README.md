composer-eclipse-script
=======================
I wrote a simple composer script, which can help you to create Eclipse configuration files. It is similiar to maven-eclipse-plugin.

First, you have to add a requiment to your ``composer.json``:

    "repositories": [{
        "type": "git",
        "url": "https://github.com/rosiecki/composer-eclipse-script.git
    }],
    "require-dev": {
        "rosiecki/composer-eclipse-script": "1.0"
    }
    
and define some hooks:

    "scripts": {
        "post-update-cmd"         : "Composer\\Eclipse::eclipse",
        "post-create-project-cmd" : "Composer\\Eclipse::eclipse",
        "post-install-cmd"        : "Composer\\Eclipse::eclipse"
    }

and that's all. Every time you run one of the following commands:

*    ``composer update``
*    ``composer create-project``
*    ``composer install``

your hook will generate some necessary files:

*    ``.project``
*    ``.buildpath`` 
*    ``.settings/org.eclipse.php.core.prefs``

I have tested the script only on Eclipse Kepler with PDT installed, so if you run into any problems, don't hesitate to drop me a line! I will try to help you as much as I can. 

By the way, if you want to override the default builders or natures in the ``.project`` file, add this section to ``composer.json``:

    "extra": {
        "eclipse": {
            "builders": [
            	"org.eclipse.wst.validation.validationbuilder",
            	"org.eclipse.dltk.core.scriptbuilder",
            	"org.eclipse.wst.common.project.facet.core.builder"
           	],
           	"natures": [
           		"org.eclipse.wst.common.project.facet.core.nature",
           		"org.eclipse.php.core.PHPNature"
           	]
        }
    }
    
Have fun!

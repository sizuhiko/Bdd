<?php

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class InitShell extends Shell {
    public function main() {
        $Folder = new Folder(dirname(dirname(dirname(__FILE__))) . DS . "features");
        $this->out("copy ".$Folder->pwd()." to Cake Root...");
        $Folder->copy(array('to' => ROOT . DS . "features"));

        $File = new File(dirname(__FILE__) . DS . "behat.yml.default");
        $this->out("copy ".$File->name()." to App/Config...");
        $File->copy(APP . DS . "Config".  DS. "behat.yml");

    }

}

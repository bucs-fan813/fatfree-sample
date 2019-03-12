<?php 
class Controller {
    private $f3;
    public function __construct()
    {
        $this->f3 = \Base::instance();
        //$f3 = $this->f3;
        //$prefix = $f3->get('prefix');
        //k($this);
        //parent::__construct(\Base::instance()->get('DB'), 'user');
        
    }
}

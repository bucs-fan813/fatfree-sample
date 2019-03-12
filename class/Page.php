<?php 
class Page extends Controller {
    function home() {
        //echo 'I cannot object to an object';
        //$this->f3 = \Base::instance();
        //$f3 = $this->f3;
        //$f3->set('name','world! its working');
        //k($this);
        $user = new \User();
        $user->authorize();
        //F3::set('COOKIE.f3auth', F3::get('COOKIE.f3authstate'));
        //F3::set('COOKIE.f3authstate', $this->f3->get('schema.name'));
        //echo \Template::instance()->render('index.html');
        //k($f3);
        F3::set('JAR.expire', 0);
        
        //$db = new \DB\SQL('mysql:host=localhost;dbname=test_db', 'root', 'root');
//         $user = new \DB\SQL\Mapper($db, 'users');
//         $auth = new \Auth($user, array('id'=>'user_id', 'pw'=>'password'));
//         $auth->basic(); // a network login prompt will display to authenticate the user
    }
}

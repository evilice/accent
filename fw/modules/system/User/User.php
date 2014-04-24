<?php
/**
 * Description of User
 *
 * @author goncharovsv
 */
class User {
    private static $instance = null;
    private $user = null;
    private $roles;
    
    private function __construct() {
        if(isset($_SESSION['user'])) {
            $this->user = $_SESSION['user'];
            $db = new SQLConstructor();
            $db->update('users', ['lastvisit'=>time()], ['id'=>(int)$this->user['id']]);
            $db->db()->execute();
        } else {
            $this->user = [
                'id'=>0,
                'name'=>'anonym',
                'perms'=>$this->getPermsByRole(1)
            ];
            $_SESSION['user']=$this->user;
        }
    }

    /**
     * 
     * @return \User
     */
    public static function getInstance() {
        if(self::$instance == null) self::$instance = new User();
        return self::$instance;
    }

    /**
     * Авторизация пользователя
     * @param String $name
     * @param String $pass
     */
    public function authorization($name, $pass) {
        if($this->user['id'] == 0) {
            $db = new SQLConstructor();
            //--- Поиск пользователя ---
            $this->user = $db->find('users', ['name'=>$name, 'pass'=>md5(md5($pass))])->map();
            if($this->user) {
                $this->user['perms'] = $this->perms(intval($this->user['id']));
                $_SESSION['user'] = $this->user;
                $this->setLastVisit();
            }
        }
    }
    
    /**
     * Изменение даты последнего входа
     */
    public function setLastVisit() {
        if($this->user) {
            $db = new SQLConstructor();
            $db->update('users', [time()], ['id'=>intval($this->user['id'])], ['lastvisit']);
            $db->db()->execute();
        }
    }
    
    /**
     * Выход пользователя
     */
    public function logout() { unset($_SESSION['user']); }
    
    /**
     * Права пользователя
     * @param int $uid
     * @return Array
     */
    public function perms($uid = false) {
        if($uid == false && $this->user) return $this->user['perms'];
        
        $perms = [];
        $uid = ($uid)?$uid:(($this->user)?$this->user['id']:false);
        if($uid) {
            $db = new DB(SQL::fill('user_perms', $uid));
            $list = $db->maps();
            foreach ($list as $k=>$v)
                $perms[] = $v['permission'];
        }
        return $perms;
    }

    /**
     * Имя пользователя
     * @return null
     */
    public function getName() {
        return ($this->user['id']!=0)?$this->user['name']:null;
    }
    
    /**
     * Проверка прав доступа к модулю
     * @param Array $mod
     * @return boolean
     */
    public function checkAccess($mod) {
        if($mod) {
            if($this->user['perms']) {
                return (array_intersect($mod, $this->user['perms']) || $this->user['id'] == 1)?true:false;
            } else return false;
        } else return true;
    }
    
    /**
     * Проверка правила доступа
     * 
     * @param string $perm
     * @return bool
     */
    public function checkPerm($perm) {
        return in_array($perm, $this->user['perms']);
    }
    
    public function id() {
        if($this->user) return (int)$this->user['id'];
        else return false;
    }
    
    /**
     * Список прав для роли
     * @param Int $rid
     * @return Array
     */
    public function getPermsByRole($rid) {
        $list = [];
        $db = new DB(SQL::fill('role_perms', $rid));
        $res = $db->maps();
        foreach ($res as $k=>$v)
            $list[] = $v['permission'];
        return $list;
    }
}
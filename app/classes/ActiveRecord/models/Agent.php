<?php


use ActiveRecord;


class Agent  extends ActiveRecord\Model
{
    public $table_name = 'cscart_users';
    public $primary_key = 'user_id';
    public $user_id;
    public $surname;
    public $name;
    public $midname;
    public $city;
    public $phone;
    public $id_superagent;
    public $email;
    public $agent_contract_id;
    public $password;
    public $login;
    public $status;


    // a person can have many orders and payments
    static $has_many = array(
        array('orders'),
        array('clients'),
        array('subagents'),
    );

    static $belongs_to = array(
        array('cities'),
        array('subagents'),
        array('contracts'),
        array('agent_statuses')
    );

    // must have a name and a state
    static $validates_presence_of = array(
        array('name'),/* array('state')*/);

    public static function findByPk($pk) {
        db_query('SELECT * FROM ' .
            self::table_name() .
            ' WHERE ' . self::get_primary_key() . ' = ?i', $pk
        );
    }


}
?>
<?php

namespace KubaStanclik\SimpleOrm;

use KubaStanclik\SimpleOrm\class\SyntaxPainter;
use KubaStanclik\SimpleOrm\class\FormatOutput;

class SimpleOrm
{

    /**
     * @var mysqli
     */
    private mysqli $db;
    /**
     * @var DB
     */
    private $result;
    /**
     * @var string
     */
    private string $query;
    /**
     * @var array
     */
    private array $history;
    /**
     * @var bool
     */
    private bool $showError;

    /**
     * @param $params
     */
    public function __construct($params) {
        $this->db = new mysqli($params['host'],$params['user'],$params['pass'],$params['dbnm']);
        $this->history = array();
        $this->showError = $params['error'];
        if(isset($params['error'])) {
            if ( $params['error'] == true ) {
                $this->showError = true;
            }else{
                $this->showError = false;
            }
        }else{
            $this->showError = true;
        }
    }

    /**
     * Execute the query.
     *
     * @param $callback
     * @return $this
     */
    public function exe($callback = null) {

        $this->result = $this->db->query($this->query);

        if ( $callback ) {
            $callback();
        }

        return $this;
    }

    /**
     * Simple select.
     *
     * @param $what
     * @param $from
     * @return $this
     */
    public function select($what, $from) {

        $this->query = "select {$what} from {$from}";

        return $this;
    }

    /**
     * Query condition
     *
     * @param $what
     * @param $operator
     * @param $value
     * @return $this
     */
    public function where($what, $operator, $value) {
        if ( gettype($value) == 'string') {
            $this->query = $this->query . " " . "where {$what} {$operator} '{$value}'";
        }else{
            $this->query = $this->query . " " . "where {$what} {$operator} {$value}";
        }

        return $this;
    }

    /**
     * "AND" operator to query condition
     *
     * @param $what
     * @param $operator
     * @param $value
     * @return $this
     */
    public function and($what, $operator, $value) {
        if ( gettype($value) == 'string') {
            $this->query = $this->query . " " . "and {$what} {$operator} '{$value}'";
        }else{
            $this->query = $this->query . " " . "and {$what} {$operator} {$value}";
        }

        return $this;
    }


    /**
     * Convert result to array.
     *
     * @return array
     */
    public function asRow() {

        $array = array();

        while($r = $this->result->fetch_row()) {
            $array[] = $r;
        }

        return $array;

    }

    /**
     *  Convert result to assoc array.
     *
     * @return array
     */
    public function assoc() {

        $array = array();

        while($r = $this->result->fetch_assoc()) {
            $temp = array();
            foreach($r as $key=>$val){
                $temp[$key] = $val;
            }
            $array[] = $temp;
        }

        return $array;

    }

    /**
     * Convert result to object.
     *
     * @return stdClass
     */
    public function asObject() {

        $array = new stdClass();

        while($r = $this->result->fetch_assoc()) {
            foreach($r as $key=>$val){
                $array->$key = $val;
            }
        }

        return $array;

    }

    /**
     * Set limit to query.
     *
     * @param $limit
     * @return $this
     */
    public function limit($limit) {
        $this->query = $this->query . " limit {$limit}";

        return $this;
    }

    /**
     * Set order to query
     *
     * @param $column
     * @param $sort
     * @return $this
     */
    public function orderBy($column, $sort) {
        $this->query = $this->query . " order by {$column} $sort";
        return $this;
    }

    /**
     * Get data using specified value.
     *
     * @param $table
     * @param $column
     * @param $matchValue
     * @return $this|false
     */
    public function getOrDie($table, $column, $matchValue) {
        if ( gettype($matchValue) == 'string') {
            $this->result = $this->db->query("select * from {$table} where {$column} = '{$matchValue}'");
        }else{
            $this->result = $this->db->query("select * from {$table} where {$column} = {$matchValue}");
        }

        if ( $this->result->num_rows > 0) {
            return $this->exe();
        }else{
            return false;
        }

    }

    /**
     * Insert data query.
     *
     * @param $table
     * @param array $arr
     * @return $this
     */
    public function insert($table, array $arr) {
        $keys = array_keys($arr);
        $keys = implode(',',$keys);
        $val = array();
        foreach($arr as $a) {
            if (gettype($a) == 'string') {
                $val[] = "'{$a}'";
            }else{
                $val[] = $a;
            }
        }
        $val = implode(',',$val);

        $this->query = "insert into {$table} ({$keys}) values ({$val})";

        return $this;
    }

    /**
     * @param $table
     * @param $distinct
     * @param $as
     * @return $this
     */
    public function counts($table, $distinct ='' , $as = 'c') {

        if ( $distinct !== '' ) {
            $this->query = "select distinct($distinct),count(*)  as {$as} from {$table}";
        }else{
            $this->query = "select count(*) as c from {$table}";
        }
        return $this;
    }

    /**
     * @param $table
     * @param array $arr
     * @return $this
     */
    public function update($table, array $arr) {

        $prepare = array();

        foreach($arr as $key=>$val) {
            $prepare[] = "{$key} = {$val}";
        }

        $string = implode(',',$prepare);

        $this->query = "update {$table} set " . $string;

        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second)
    {
        $this->query = $this->query . ' left join ' . $table . ' on ' . $first . ' ' . $operator . ' ' . $second;

        return $this;
    }

    public function Join(string $table, string $first, string $operator, string $second)
    {
        $this->query = $this->query . ' inner join ' . $table . ' on ' . $first . ' ' . $operator . ' ' . $second;

        return $this;
    }


    /**
     * @param string $table
     * @return $this
     */
    public function getKeys(string $table) {

        $this->query = "describe {$table}";

        $this->result = $this->db->query($this->query);

        return $this;

    }

    /**
     * @param $query
     * @return $this
     */
    public function raw($query) {

        $this->query = $query;

        return $this;
    }

    /**
     * @return void
     */
    public function dumpRaw():void {
        SyntaxPainter::paint($this->query);
    }

    /**
     * @return void
     */
    public function dump():void {
        $toDump = $this->exe()->assoc();
        FormatOutput::format($toDump);
    }

    public function debug() {
        SyntaxPainter::paint($this->query);
        $toDump = $this->exe()->assoc();
        FormatOutput::format($toDump);
    }

    /**
     * @return void
     */
    public function history() {
        print_r($this->history);
    }
}
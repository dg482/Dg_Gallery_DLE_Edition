<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */


class module_db {

    public static $mysql_error;
    private $connected;
    public $query_num;

    /**
     *
     */
    public function __construct() {
        $this->db_auth['user'] = DBUSER;
        $this->db_auth['pass'] = DBPASS;
        $this->db_auth['dbname'] = DBNAME;
        $this->db_auth['host'] = DBHOST;
        if (extension_loaded('mysqli')) {
            $this->mysqlL = true;
            $this->mysql_extend = 'MySQLi';
        }
        if (!defined('COLLATE')) {
            define("COLLATE", "cp1251");
        }
        $this->queryText = array();
    }

    /**
     * @param $query
     * @param bool $show_error
     * @return mixed
     */
    public function query($query, $show_error = true) {
        if ($query == '') {
            return;
        }

        model_debug::setQuery($query);

        $time_before = $this->get_real_time();
        if (!$this->connected) {
            $this->connect();
        }
        if (!$this->mysqlL) {
            if (!($this->query_id = mysql_query($query, $this->db_id))) {
                self::$mysql_error['query'] = $query;
                self::$mysql_error['error_num'] = mysql_errno();
                self::$mysql_error['error_descr'] = mysql_error();
                print_r(self::$mysql_error);
                die();
            }
        } else {
            if (!($this->query_id = mysqli_query($this->db_id, $query))) {
                self::$mysql_error['query'] = $query;
                self::$mysql_error['error_num'] = mysqli_errno($this->db_id);
                self::$mysql_error['error_descr'] = mysqli_error($this->db_id);
                print_r(self::$mysql_error);
                die();
            }
        }
        model_debug::$MySQL_time_taken += $this->get_real_time() - $time_before;
        $this->query_num++;
        return $this->query_id;
    }

    /**
     * @param $query
     * @param bool $multi
     * @return array|void
     */
    public function super_query($query, $multi = false) {
        if (!$multi) {
            $this->query($query);
            $data = $this->get_row();
            $this->free();
            return $data;
        } else {
            $this->query($query);
            $rows = array();
            while ($row = $this->get_row()) {
                $rows[] = $row;
            }
            $this->free();
            return $rows;
        }
    }

    /**
     * @param string $query_id
     */
    public function free($query_id = '') {
        if ($query_id == '')
            $query_id = $this->query_id;
        if (!$this->mysqlL) {
            @mysql_free_result($query_id);
        } else {
            @mysqli_free_result($query_id);
        }
    }

    public function ErrorHandler($q) {
        var_dump(self::$mysql_error, $q);
    }

    /**
     * @return bool
     */
    public function Connect() {
        $time_before = $this->get_real_time();
        if (!$this->mysqlL) {
            if (!$this->db_id = mysql_connect($this->db_auth['host'], $this->db_auth['user'], $this->db_auth['pass'])) {
                self::$mysql_error['error_num'] = mysql_errno();
                self::$mysql_error['error_descr'] = mysql_error();
            }
            if (!mysql_select_db($this->db_auth['dbname'], $this->db_id)) {
                self::$mysql_error['error_num'] = mysql_errno();
                self::$mysql_error['error_descr'] = mysql_error();
            }
            $this->mysql_version = mysql_get_server_info();
            if (version_compare($this->mysql_version, '4.1', ">="))
                mysql_query("/*!40101 SET NAMES '" . COLLATE . "' */");
            if (!$this->db_id) {
                self::$mysql_error['error_num'] = mysql_errno();
                self::$mysql_error['error_descr'] = mysql_error();
            }
        } else {
            $db_location = explode(":", $this->db_auth['host']);
            if (isset($db_location[1])) {
                $this->db_id = @mysqli_connect($db_location[0], $this->db_auth['user'], $this->
                    db_auth['pass'], $this->db_auth['dbname'], $db_location[1]);
            } else {
                $this->db_id = @mysqli_connect($db_location[0], $this->db_auth['user'], $this->
                    db_auth['pass'], $this->db_auth['dbname']);
            }
            if (!$this->db_id) {
                self::$mysql_error['error_num'] = '';
                self::$mysql_error['error_descr'] = mysqli_connect_error();
            } else {
                $this->mysql_version = mysqli_get_server_info($this->db_id);
                mysqli_query($this->db_id, "SET NAMES '" . COLLATE . "'");
            }
        }
        $this->connected = true;
        $this->MySQL_time_taken += $this->get_real_time() - $time_before;
        return true;
    }

    /**
     * @return float
     */
    private function get_real_time() {
        list($seconds, $microSeconds) = explode(' ', microtime());
        return ((float) $seconds + (float) $microSeconds);
    }

    /**
     * @param string $id
     * @return int|void
     */
    public function num_rows($id = '') {
        if ($id == '')
            $id = $this->query_id;
        if (!$this->mysqlL) {
            return mysql_num_rows($id);
        } else {
            return mysqli_num_rows($id);
        }
    }

    /**
     * @param string $id
     * @return array|void
     */
    public function get_row($id = '') {
        if ($id == '')
            $id = $this->query_id;
        if (!$this->mysqlL) {
            return mysql_fetch_assoc($id);
        } else {
            return mysqli_fetch_assoc($id);
        }
    }

    /**
     * @param string $id
     * @return array|void
     */
    function get_array($id = '') {
        if ($id == '')
            $id = $this->query_id;
        if (!$this->mysqlL) {
            return mysql_fetch_array($id);
        } else {
            return mysqli_fetch_array($id);
        }
    }

    /**
     * @return int|void
     */
    public function insert_id() {
        if (!$this->mysqlL) {
            return mysql_insert_id($this->db_id);
        } else {
            return mysqli_insert_id($this->db_id);
        }
    }

    /**
     * @param $source
     * @return string|void
     */
    public function safesql($source) {
        if (!$this->mysqlL) {
            if ($this->db_id)
                return mysql_real_escape_string($this->db_id, $source);
            else
                return mysql_escape_string($source);
        } else {
            if ($this->db_id)
                return mysqli_real_escape_string($this->db_id, $source);
            else
                return mysql_escape_string($source);
        }
    }

}

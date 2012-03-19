<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

class model_config {

    /**
     * @var array
     * */
    public $arr = array();
    /**
     * @var string
     * */
    private $filename;

    /**
     *
     */
    public function initArray() {
        $this->arr = parse_ini_file($this->filename, true);
    }

    /**
     * @param $filename
     * @param string $section
     * @return array|null
     */
    public function setConfig($filename, $section = 'production') {
        if ($this->load($filename)) {
            return $this->getSection($section);
        }
    }

    /**
     * @param $section
     * @return array|null
     */
    public function getSection($section) {
        $tmp = $this->arr[$section];
        $new_section = array();
        if (is_array($tmp)) {
            foreach ($tmp as $k => $v) {
                $tmp_key = explode('.', $k);
                if (count($tmp_key) == 2) {
                    $new_section[$tmp_key[0]][$tmp_key[1]] = $v;
                }
                if (count($tmp_key) == 3) {
                    $new_section[$tmp_key[0]][$tmp_key[1]][$tmp_key[2]] = $v;
                }
                if (count($tmp_key) == 4) {
                    $new_section[$tmp_key[0]][$tmp_key[1]][$tmp_key[2]][$tmp_key[3]] = $v;
                }
            }
            return $new_section;
        } else {
            return null;
        }
    }

    /**
     * @param $file
     * @return bool
     * @throws Exception
     */
    public function load($file) {
        $result = true;
        if ($this->arr) {
            return true;
        }
        $this->filename = $file;
        if (file_exists($file) && is_readable($file)) {
            $this->initArray();
        } else {
            $result = false;
            throw new Exception('error configuration');
        }
        return $result;
    }

    /**
     * @param $section
     * @param $key
     * @param string $def
     * @return string
     */
    public function read($section, $key, $def = '') {
        if (isset($this->arr[$section][$key])) {
            return $this->arr[$section][$key];
        } else
            return $def;
    }

    /**
     * model_config::write()
     *
     * @param mixed $section
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function write($section, $key, $value) {
        if (is_bool($value))
            $value = $value ? 1 : 0;
        $this->arr[$section][$key] = $value;
    }

    /**
     * @param $section
     */
    public function eraseSection($section) {
        if (isset($this->arr[$section]))
            unset($this->arr[$section]);
    }

    /**
     * @param $section
     * @param $key
     */
    public function deleteKey($section, $key) {
        if (isset($this->arr[$section][$key]))
            unset($this->arr[$section][$key]);
    }

    /**
     * @param $array
     * @return array
     */
    public function readSections(&$array) {
        $array = array_keys($this->arr);
        return $array;
    }

    /**
     * @param $section
     * @param $array
     * @return array
     */
    public function readKeys($section, &$array) {
        if (isset($this->arr[$section])) {
            $array = array_keys($this->arr[$section]);
            return $array;
        }
        return array();
    }

    /**
     * @return bool
     */
    public function updateFile() {
        $result = '';
        foreach ($this->arr as $sname => $section) {
            $result .= '[' . $sname . ']' . _BR_;
            foreach ($section as $key => $value) {
                $result .= $key . '=' . $value . _BR_;
            }
            $result .= _BR_;
        }
        #file_p_contents($this->filename, $result);
        return true;
    }

    /**
     * @param $config
     */
    public function saveToArray($config) {
        $file_config = ROOT_DIR . '/DGGallery/app/config/config_gallery.php';

        if (file_exists($file_config))
            $config_gallery = (include $file_config);
        if (is_array($config)) {

            $handler = fopen($file_config, "wb+");
            foreach ($config as $new_name => $new_value) {

                $new_value = trim(stripslashes($new_value));

                $config_gallery[$new_name] = $new_value;
            }
            fwrite($handler, "<?PHP \n\n//Configurations D.G. Gallery 1.5\n\n return array (\n\n");
            foreach ($config_gallery as $name => $value) {
                if ($value != 'undefined') {
                    fwrite($handler, "    '{$name}' => \"{$value}\",\n\n");
                } else {
                    fwrite($handler, "    '{$name}' => \"\",\n\n");
                }
            }
            fwrite($handler, ");\n\n");
            fclose($handler);
        }
    }

}

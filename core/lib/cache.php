<?php

/**
 *
 * Setup:
 *
    edit the singleton() metod
    and define the list of memcached servers in a 2-d array
    in the format
    array(
        array('192.168.0.1'=>'11211'),
        array('192.168.0.2'=>'11211'),
    );
 *
 *
 * Usage:
 *
<?php
//include the class name
include ('memcache.class.php');

//store the variable
Cache::set('key','abc');

//increment/decrement the integer value
Cache::increment('key');
Cache::decrement('key');

//fetch the value by it's key
echo Cache::get('key');


//delete the data
echo Cache::delete('key');

//Clear the cache memory on all servers
Cache::flush();

?>

Cache::replace() and Cache::add are implemented also.

More information can be obtained here:
http://www.danga.com/memcached/
http://www.php.net/memcache

*/

/**
 * The class makes it easier to work with memcached servers and provides hints in the IDE like Zend Studio
 * @author Grigori Kochanov http://www.grik.net/
 * @version 1
 *
 */
class Cache {
/**
 * Resources of the opend memcached connections
 * @var array [memcache objects]
 */
protected $mc_servers = array();
/**
 * Quantity of servers used
 * @var int
 */
protected $mc_servers_count;

static $instance;
static $enabled = null;

/**
 * Singleton to call from all other functions
 */
static function singleton(){
    //Write here where from to get the servers list from, like
    // global $servers
    $servers = (array)Config::get('memcache_servers');

    self::$instance ||
        self::$instance = new Cache($servers);
    return self::$instance;
}

static function enabled ()
{
	if (!is_bool (self::$enabled)) {
		self::singleton();
	}
	return self::$enabled;
}

/**
 * Accepts the 2-d array with details of memcached servers
 *
 * @param array $servers
 */
protected function __construct(array $servers){
    if (!$servers || !extension_loaded ('Memcache')){
    	$this->mc_servers[0]=null;
    	$this->mc_servers_count = 0;
    	self::$enabled = false;
    	return;

        trigger_error('No memcache servers to connect',E_USER_WARNING);
    }
    for ($i = 0, $n = count($servers); $i < $n; ++$i){
        ($con = @memcache_connect(key($servers[$i]), current($servers[$i]))) &&
	        $this->mc_servers[] = $con;
    }

    $this->mc_servers_count = count($this->mc_servers);
    if (!$this->mc_servers_count){
        $this->mc_servers[0]=null;
    	self::$enabled = false;
    } else {
    	self::$enabled = true;
		if (isset ($_GET['flushcache'])) {
			self::flush();
		}
    }
}
/**
 * Returns the resource for the memcache connection
 *
 * @param string $key
 * @return object memcache
 */
protected function getMemcacheLink($key){
    if ( $this->mc_servers_count <2 ){
        //no servers choice
        return $this->mc_servers[0];
    }
    return $this->mc_servers[(crc32($key) & 0x7fffffff)%$this->mc_servers_count];
}

/**
 * Clear the cache
 *
 * @return void
 */
static function flush() {
    $x = self::singleton()->mc_servers_count;
    for ($i = 0; $i < $x; ++$i){
        $a = self::singleton()->mc_servers[$i];
        self::singleton()->mc_servers[$i]->flush();
    }
}

static function setKey ($key) {
	return md5 ($key) . Config::get ('site_id') . Config::get ('url') . Config::get ('lang_current') . Params::$_GET['page'];
}

/**
 * Returns the value stored in the memory by it's key
 *
 * @param string $key
 * @return mix
 */
static function get($key) {
	if (self::enabled()) {
		$key = self::setKey ($key);
		if ($ret = self::singleton()->getMemcacheLink($key)->get($key)) {
			if (self::singleton()->getMemcacheLink($key . "_exp")->get($key . "_exp"))
				return $ret;
			self::singleton()->getMemcacheLink($key . "_exp")->set($key . "_exp", "true", MEMCACHE_COMPRESSED, 30);
		}
	}

	return false;
}

/**
 * Store the value in the memcache memory (overwrite if key exists)
 *
 * @param string $key
 * @param mix $var
 * @param bool $compress
 * @param int $expire (seconds before item expires)
 * @return bool
 */
static function set($key, $var, $compress=true, $expire=null) {
	if (self::enabled()) {
		$key = self::setKey ($key);
		$var = is_scalar($var) ? (string)$var : $var;
		self::singleton()->getMemcacheLink($key)->set($key, $var, $compress?MEMCACHE_COMPRESSED:null, 1800);
		return self::singleton()->getMemcacheLink($key . "_exp")->set($key . "_exp", "true", $compress?MEMCACHE_COMPRESSED:null, $expire?$expire:(Config::get ('memcache_timeout') + rand(0,30)));
	}

	return false;
}
/**
 * Set the value in memcache if the value does not exist; returns FALSE if value exists
 *
 * @param sting $key
 * @param mix $var
 * @param bool $compress
 * @param int $expire
 * @return bool
 */
static function add($key, $var, $compress=true, $expire=null) {
	if (self::enabled()) {
		$key = self::setKey ($key);
		$var = is_scalar($var) ? (string)$var : $var;
		return self::singleton()->getMemcacheLink($key)->add($key, $var, $compress?MEMCACHE_COMPRESSED:null, $expire?$expire:(Config::get ('memcache_timeout') + rand(0,30)));
	}

	return false;
}

/**
 * Replace an existing value
 *
 * @param string $key
 * @param mix $var
 * @param bool $compress
 * @param int $expire
 * @return bool
 */
static function replace($key, $var, $compress=true, $expire=null) {
	if (self::enabled()) {
		$key = self::setKey ($key);
		$var = is_scalar($var) ? (string)$var : $var;
		return self::singleton()->getMemcacheLink($key)->replace($key, $var, $compress?MEMCACHE_COMPRESSED:null, $expire?$expire:(Config::get ('memcache_timeout') + rand(0,30)));
	}

	return false;
}
/**
 * Delete a record or set a timeout
 *
 * @param string $key
 * @param int $timeout
 * @return bool
 */
static function delete($key, $timeout=0) {
	if (self::enabled()) {
		$key = self::setKey ($key);
		return self::singleton()->getMemcacheLink($key)->delete($key, $timeout);
	}

	return false;
}
/**
 * Increment an existing integer value
 *
 * @param string $key
 * @param mix $value
 * @return bool
 */
static function increment($key, $value=1) {
	if (self::enabled()) {
		$key = self::setKey ($key);
		return self::singleton()->getMemcacheLink($key)->increment($key, $value);
	}

	return false;
}

/**
 * Decrement an existing value
 *
 * @param string $key
 * @param mix $value
 * @return bool
 */
static function decrement($key, $value=1) {
	if (self::enabled()) {
		$key = self::setKey ($key);
		return self::singleton()->getMemcacheLink($key)->decrement($key, $value);
	}

	return false;
}


//class end
}

?>

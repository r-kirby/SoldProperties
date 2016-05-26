<?php
/**
 * Description of PropertiesDBConnector
 *
 * @author Ryan Kirby - Nature Coast Web Design & Marketing, Inc.
 * 
 * This class attaches each property from the SoldProperties class to the database
 * that stores all of the information. The variables and functions here are ALL
 * related to the connection to the database, and all returned values will be 
 * instances of the SoldProperties class. BOTH CLASSSES ARE REQUIRED FOR PROPER
 * FUNCTIONALITY. For additional information on the SoldProperties class, please
 * see the SoldProperties.php script that should be packaged with this one.
 * Have fun!
 */
namespace SoldProperties;

use \PDO;

class PropertiesDBConnector {
    protected static $db_host = 'localhost';
    protected static $db_username = '';
    protected static $db_password = '';
    protected static $db_name = '';
    protected static $dbcnx = '';
    private static $db_properties_table = 'tbl_properties';
    private static $db_photos_table = 'tbl_photos';
   protected $db_properties_id_col = 'id';
   protected $db_properties_subdivision_col = 'subdivision';
   protected $db_properties_mls_id_col = 'mls_id';
   protected $db_properties_list_price_col = 'list_price';
   protected $db_properties_close_price_col = 'close_price';
   protected $db_properties_days_on_market_col = 'days_market';
   protected $db_properties_address_col = 'address';
   protected $db_properties_city_col = 'city';
   protected $db_properties_bedrooms_col = 'beds';
   protected $db_properties_bathrooms_col = 'baths';
   protected $db_properties_square_feet_col = 'sqft';
   protected $db_properties_year_built_col = 'year_built';
   protected $db_properties_pool_col = 'pool';
   protected $db_properties_description_col = 'descr';
   protected $db_photos_id_col = 'id';
   protected $db_photos_property_id_col = 'property_id';
   protected $db_photos_link_col = 'link';
   protected $db_photos_main_photo_col = 'main_photo';
   protected $db_photos_order_col = 'sort_order';
    
    public function __construct($db_host=NULL,$db_username=NULL,$db_pass=NULL,$db_name=NULL) {
        if(!empty($db_host) && !empty($db_username)&& !empty($db_pass)&& !empty($db_name)){
            self::$db_host = $db_host;
            self::$db_name = $db_name;
            self::$db_password = $db_pass;
            self::$db_username = $db_username;
        }
    }
    
    public function setColNames($table,$names){
        if(strtolower($table)=='properties'){$this->setPropertyColValues($names);}
        elseif(strtolower($table)=='photos'){$this->setPhotosColValues($names);}
    }
    
    private function setPropertyColValues($values){
        if(!is_array($values)){return FALSE;}
        foreach($values as $key=>$value){
            switch(strtolower($key)){
                case 'id':
                    $this->setDBPropertiesID($value);
                    break;
                case 'subdivision':
                    $this->setDBPropertiesSubdivision($value);
                    break;
                case 'mls_id':
                    $this->setDBPropertiesMLSID($value);
                    break;
                case 'list_price':
                    $this->setDBPropertiesListPrice($value);
                    break;
                case 'close_price':
                    $this->setDBPropertiesClosePrice($value);
                    break;
                case 'days_on_market':
                    $this->setDBPropertiesDaysOnMarket($value);
                    break;
                case 'address':
                    $this->setDBPropertiesAddress($value);
                    break;
                case 'city':
                    $this->setDBPropertiesCity($value);
                    break;
                case 'bedrooms':
                    $this->setDBPropertiesBedrooms($value);
                    break;
                case 'bathrooms':
                    $this->setDBPropertiesBathrooms($value);
                    break;
                case 'square_feet':
                    $this->setDBPropertiesSquareFeet($value);
                    break;
                case 'year_built':
                    $this->setDBPropertiesYearBuilt($value);
                    break;
                case 'pool':
                    $this->setDBPropertiesPool($value);
                    break;
                case 'description':
                    $this->setDBPropertiesDescription($value);
                    break;
            }
        }
    }
    private function setPhotosColValues($values){
        if(!is_array($values)){return FALSE;}
        foreach($values as $key=>$value){
            switch(strtolower($key)){
                case 'id':
                    $this->setDBPhotosID($value);
                    break;
                case 'property_id':
                    $this->setDBPhotosPropertyID($value);
                    break;
                case 'link':
                    $this->setDBPhotosLink($value);
                    break;
                case 'main_photo':
                    $this->setDBPhotosMainPhoto($value);
                    break;
                case 'sort_order':
                    $this->setDBPhotosOrder($value);
                    break;
            }
        }
    }
    
    public function setDBPropertiesID($new_value){$this->db_properties_id_col = $new_value;}
    public function setDBPropertiesSubdivision($new_value){$this->db_properties_subdivision_col = $new_value;}
    public function setDBPropertiesMLSID($new_value){$this->db_properties_mls_id_col = $new_value;}
    public function setDBPropertiesListPrice($new_value){$this->db_properties_list_price_col = $new_value;}
    public function setDBPropertiesClosePrice($new_value){$this->db_properties_close_price_col = $new_value;}
    public function setDBPropertiesDaysOnMarket($new_value){$this->db_properties_days_on_market_col = $new_value;}
    public function setDBPropertiesAddress($new_value){$this->db_properties_address_col = $new_value;}
    public function setDBPropertiesCity($new_value){$this->db_properties_city_col = $new_value;}
    public function setDBPropertiesBedrooms($new_value){$this->db_properties_bedrooms_col = $new_value;}
    public function setDBPropertiesBathrooms($new_value){$this->db_properties_bathrooms_col = $new_value;}
    public function setDBPropertiesSquareFeet($new_value){$this->db_properties_square_feet_col = $new_value;}
    public function setDBPropertiesYearBuilt($new_value){$this->db_properties_year_built_col = $new_value;}
    public function setDBPropertiesPool($new_value){$this->db_properties_pool_col = $new_value;}
    public function setDBPropertiesDescription($new_value){$this->db_properties_description_col = $new_value;}
    
    public function setDBPhotosID($new_value){$this->db_photos_id_col = $new_value;}
    public function setDBPhotosPropertyID($new_value){$this->db_photos_property_id_col = $new_value;}
    public function setDBPhotosLink($new_value){$this->db_photos_link_col = $new_value;}
    public function setDBPhotosMainPhoto($new_value){$this->db_photos_main_photo_col = $new_value;}
    public function setDBPhotosOrder($new_value){$this->db_photos_order_col = $new_value;}
    
    public function setPropertiesTableName($new_value){self::$db_properties_table = $new_value;}
    public function setPhotosTableName($new_value){static::$db_photos_table = $new_value;}
    
    public function setDatabaseHost($new_value){static::$db_host = $new_value;}
    public function setDatabaseName($new_value){static::$db_name = $new_value;}
    public function setDatabasePassword($new_value){static::$db_password = $new_value;}
    public function setDatabaseUsername($new_value){static::$db_username = $new_value;}
    
    protected function getPropertyTable(){return self::$db_properties_table;}
    protected function getPhotosTable(){return self::$db_photos_table;}
    
    private function connectToDatabse(){
        if(empty(static::$db_host) && empty(static::$db_username)&& empty(static::$db_password)&& empty(static::$db_name)){return FALSE;}
        try {
           $dbcnx = new \PDO("mysql:host=".static::$db_host.";dbname=".static::$db_name.";charset=utf8", static::$db_username, static::$db_password);
           $dbcnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           $dbcnx->setAttribute( PDO::ATTR_EMULATE_PREPARES, FALSE);
           return $dbcnx;
        } catch(PDOException $e) {
           throw new Exception("There was a problem connecting to the database. Please try again momentarily.".PHP_EOL, NULL, NULL);
        }
    }
    
    protected function checkDatabaseConnection(){
        if(!is_object(static::$dbcnx)){
            try{
                static::$dbcnx = $this->connectToDatabse();
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }
        }
    }
    
    public function getAllSubdivisions(){
        try{$this->checkDatabaseConnection();}
        catch(Exception $e){return $e->getMessage();}
        try{
            $get_subdivisions_query = static::$dbcnx->query('SELECT DISTINCT('.$this->db_properties_subdivision_col.') AS subdivision FROM '.self::$db_properties_table.' ORDER BY '.$this->db_properties_subdivision_col);
        } catch (Exception $ex) {
            return 'There was a problem retrieving the subdivisions. Please try again momentarily.';
        }
        $all_subdivisions_array = array();
        while($row = $get_subdivisions_query->fetch(PDO::FETCH_ASSOC)){
            $all_subdivisions_array[]=$row['subdivision'];
        }
        return $all_subdivisions_array;
    }
    
    public function searchPropertiesBySubdivision($search_params){
        if(!is_array($search_params)){$search_params = explode(',', $search_params);}
        try{$this->checkDatabaseConnection();}
        catch(Exception $e){return $e->getMessage();}
        $query_array = $this->generateSearchQuery($search_params);
        try{
            $search_properties_query = static::$dbcnx->prepare('SELECT * FROM '.self::$db_properties_table.' WHERE '.$query_array[0]);
            $search_properties_query->execute($query_array[1]);
        } catch (PDOException $ex) {
            return 'There was a problem conducting your search. Please check your parameters and try again.';
        }
        $all_found_properties = array();
        while($row = $search_properties_query->fetch(PDO::FETCH_ASSOC)){
            $all_found_properties[] = new SoldProperties($row[$this->db_properties_id_col],$row);
        }
        return $all_found_properties;
    }
    
    public function getIndividualProperty($listing_id){
        try{$this->checkDatabaseConnection();}
        catch(Exception $e){return $e->getMessage();}
        try{
            $search_properties_query = static::$dbcnx->prepare('SELECT * FROM '.self::$db_properties_table.' WHERE '.$this->db_properties_id_col.' = :id LIMIT 1');
            $search_properties_query->bindParam(':id',$listing_id);
            $search_properties_query->execute();
        } catch (Exception $ex) {
            return 'There was a problem conducting your search. Please check your parameters and try again.';
        }
        $property_info = $search_properties_query->fetch(PDO::FETCH_ASSOC);
        return new SoldProperties($property_info[$this->db_properties_id_col],$property_info);
    }
    
    private function generateSearchQuery($search_params){
        if(!is_array($search_params)){return FALSE;}
        $where_clause = NULL;
        $where_array = array();
        foreach($search_params as $subdivision){
            if(!empty($where_clause)){$where_clause .= 'OR ';}
            $where_clause .= $this->db_properties_subdivision_col.' LIKE ? ';
            $where_array[]=$subdivision;
        }
        return array($where_clause,$where_array);
    }
    
    public function getAllPhotos($listing_id){
        try{$this->checkDatabaseConnection();}
        catch(Exception $e){return $e->getMessage();}
        try{
            $all_photos_query = static::$dbcnx->prepare('SELECT '.$this->db_photos_link_col.' FROM '.self::$db_photos_table.' WHERE '.$this->db_photos_property_id_col.' = :id ORDER BY '.$this->db_photos_order_col);
            $all_photos_query->bindParam(':id',$listing_id);
            $all_photos_query->execute();
        } catch (Exception $ex) {
            return 'There was a problem retrieving the photos for this listing. Please try again momentarily.';
        }
        $all_photos_array = array();
        while($photo = $all_photos_query->fetch(PDO::FETCH_ASSOC)){$all_photos_array[]=$photo[$this->db_photos_link_col];}
        return $all_photos_array;
    }
    
    public function getMainPhoto($listing_id){
        try{$this->checkDatabaseConnection();}
        catch(Exception $e){return $e->getMessage();}
        try{
            $all_photos_query = static::$dbcnx->prepare('SELECT '.$this->db_photos_link_col.' FROM '.self::$db_photos_table.' WHERE '.$this->db_photos_property_id_col.' = :id AND '.$this->db_photos_main_photo_col.' = \'y\' ORDER BY '.$this->db_photos_order_col.' LIMIT 1');
            $all_photos_query->bindParam(':id',$listing_id);
            $all_photos_query->execute();
        } catch (Exception $ex) {
            return 'There was a problem retrieving the photos for this listing. Please try again momentarily.';
        }
        $photo = $all_photos_query->fetch(PDO::FETCH_ASSOC);
        return $photo[$this->db_photos_link_col];
    }
}

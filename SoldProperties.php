<?php
/**
 * Description of SoldProperties
 *
 * @author Ryan Kirby - Nature Coast Web Design & Marketing, Inc.
 * 
 * Each instance of this class is going to be a property from the database. The 
 * corresponding DB Connector class will be the one that interfaces with the 
 * database and returns all of the required information. This class will be able
 * to print out both a summary view and detail view of itself. Implementation will
 * occur in conjunction with the connector class. BOTH ARE REQUIRED FOR USAGE.
 * For additional information on the DB Connector class, please see the 
 * PropertiesDBConnector.php file that should be packaged with this script. 
 * Have fun!
 */
namespace SoldProperties;

class SoldProperties{
    public $id = NULL;
    public $subdivision = NULL;
    public $mls_id = NULL;
    public $list_price = NULL;
    public $close_price = NULL;
    public $days_on_market = NULL;
    public $other_photos = array();
    public $address = NULL;
    public $city = NULL;
    public $bedrooms = NULL;
    public $bathrooms = NULL;
    public $square_feet = NULL;
    public $year_built = NULL;
    public $pool = NULL;
    public $description = NULL;
    
    public function __construct($id=NULL,$other_links = NULL) {
        $this->id = $id;
        if(is_array($other_links)){
            $this->setValuesFromArray($other_links);
        }
    }
    
    private function setValuesFromArray($values){
        if(!is_array($values)){return FALSE;}
        foreach($values as $key=>$value){
            switch(strtolower($key)){
                case 'subdivision':
                    $this->setSubdivision($value);
                    break;
                case 'mls_id':
                    $this->setMLSID($value);
                    break;
                case 'list_price':
                    $this->setListPrice($value);
                    break;
                case 'close_price':
                    $this->setClosePrice($value);
                    break;
                case 'days_on_market':
                case 'days_market':
                    $this->setDaysOnMarket($value);
                    break;
                case 'address':
                    $this->setAddress($value);
                    break;
                case 'city':
                    $this->setCity($value);
                    break;
                case 'bedrooms':
                case 'beds':
                    $this->setBedrooms($value);
                    break;
                case 'bathrooms':
                case 'baths':
                    $this->setBathrooms($value);
                    break;
                case 'square_feet':
                case 'square_ft':
                case 'sqft':
                    $this->setSquareFeet($value);
                    break;
                case 'year_built':
                    $this->setYearBuilt($value);
                    break;
                case 'pool':
                    $this->setPool($value);
                    break;
                case 'description':
                case 'descr':
                    $this->setDescription($value);
                    break;
                case 'other_photos':
                    $this->setOtherPhotos($value);
                    break;
            }
        }
    }
    
    public function setSubdivision($new_value){$this->subdivision = $new_value;}
    public function setMLSID($new_value){$this->mls_id = $new_value;}
    public function setListPrice($new_value){$this->list_price = $new_value;}
    public function setClosePrice($new_value){$this->close_price = $new_value;}
    public function setDaysOnMarket($new_value){$this->days_on_market = $new_value;}
    public function setAddress($new_value){$this->address = $new_value;}
    public function setCity($new_value){$this->city = $new_value;}
    public function setBedrooms($new_value){$this->bedrooms = $new_value;}
    public function setBathrooms($new_value){$this->bathrooms = $new_value;}
    public function setSquareFeet($new_value){$this->square_feet = $new_value;}
    public function setYearBuilt($new_value){$this->year_built= $new_value;}
    public function setPool($new_value){$this->pool = $new_value;}
    public function setDescription($new_value){$this->description= $new_value;}
    public function setOtherPhotos($new_value){
        if(is_array($new_value)){$this->other_photos= $new_value;}
        else{$this->other_photos[] = $new_value;}
        
    }
    
    public function printDetail(){
        echo '<p>Detail for '.$this->id.'</p>'.PHP_EOL;
    }
    
    public function printSummary(){
        echo '<p>Summary list for '.$this->id.'</p>'.PHP_EOL;
    }
    
}

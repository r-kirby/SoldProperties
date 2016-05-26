<?php
/**
 * Description of PropertiesDBAuthConnector
 *
 * @author Ryan Kirby - Nature Coast Web Design & Marketing, Inc.
 * 
 * This class extends the PropertiesDBConnector and allows for admin level changes
 * to be made to the listings themselves. THIS CLASS SHOULD ONLY BE ACCESSED FROM
 * AN AUTHORIZED SCRIPT. UNAUTHORIZED ACCESS TO THIS CLASS CAN HAVE NEGATIVE
 * EFFECTS ON YOUR DATABASE, INCLUDING COMPLETE COMPROMISE. USER ASSUMES ALL RISK.
 * Have fun!
 */
namespace SoldProperties;

use \PDO;

class PropertiesDBAuthConnector extends PropertiesDBConnector {
    
    private static $http_photos_location = '';
    private static $absolute_photos_directory = '';
    
    public function __construct($db_host=NULL,$db_username=NULL,$db_pass=NULL,$db_name=NULL) {
        parent::__construct($db_host, $db_username, $db_pass, $db_name);
    }
    
    public function setHttpPhotosLocation($new_value){static::$http_photos_location = rtrim($new_value,'/').'/';}
    public function setPhotosDirectory($new_value){static::$absolute_photos_directory = rtrim($new_value,'/').'/';}
    
    public function getAllListingsAdmin(){
        try{parent::checkDatabaseConnection();}
        catch(\Exception $e){return $e->getMessage();}
        try{
            $get_properties_query = static::$dbcnx->query('SELECT * FROM '.static::getPropertyTable().' ORDER BY '.$this->db_properties_subdivision_col);
        } catch (PDOException $ex) {
            return $ex->getMessage();
        }
        $all_listings = array();
        while($listing = $get_properties_query->fetch(PDO::FETCH_ASSOC)){
            $all_listings[] = new SoldProperties($listing[$this->db_properties_id_col],$listing);
        }
        return $all_listings;
    }
    
    public function updateListing($listing){
        try{parent::checkDatabaseConnection();}
        catch(\Exception $e){return $e->getMessage();}
        if(!is_object($listing)){return FALSE;}
        $update_array = $this->generateUpdateQuery($listing);
        $update_array[1]['id']=$listing->id;
        try{
            $update_query = parent::$dbcnx->prepare($update_array[0].' WHERE '.$this->db_properties_id_col.' = :id');
            $update_query->execute($update_array[1]);
        } catch (PDOException $ex) {
            return 'There was a problem updating the listing. Please try again momentarily.';
        }
        return TRUE;
    }
    
    public function createListing($listing){
        try{parent::checkDatabaseConnection();}
        catch(\Exception $e){return $e->getMessage();}
        if(!is_object($listing)){return FALSE;}
        $create_array = $this->generateCreateQuery($listing);
        try{
            $insert_query = parent::$dbcnx->prepare($create_array[0]);
            $insert_query->execute($create_array[1]);
        } catch (PDOException $ex) {
            return 'There was a problem inserting the new listing. Please try again momentarily.';
        }
        return TRUE;
    }
    
    private function generateUpdateQuery($listing){
        $set_array = $this->generateQuery($listing);
        return array('UPDATE '.$this->getPropertyTable().' SET '.$set_array[0],$set_array[1]);
    }
    
    private function generateCreateQuery($listing){
        $set_array = $this->generateQuery($listing);
        return array('INSERT INTO '.$this->getPropertyTable().' SET '.$set_array[0],$set_array[1]);
    }
    
    private function generateQuery($listing){
        $set_clause_array = array();
        $set_clause_string = NULL;
        if(!empty($listing->subdivision)){
            $set_clause_array['subdivision']=$listing->subdivision;
            $set_clause_string .= ' '.$this->db_properties_subdivision_col.' = :subdivision,';
        }
        if(!empty($listing->mls_id)){
            $set_clause_array['mls_id']=$listing->mls_id;
            $set_clause_string .= ' '.$this->db_properties_mls_id_col.' = :mls_id,';
        }
        if(!empty($listing->list_price)){
            $set_clause_array['list_price']=$listing->list_price;
            $set_clause_string .= ' '.$this->db_properties_list_price_col.' = :list_price,';
        }
        if(!empty($listing->close_price)){
            $set_clause_array['close_price']=$listing->close_price;
            $set_clause_string .= ' '.$this->db_properties_close_price_col.' = :close_price,';
        }
        if(!empty($listing->days_on_market)){
            $set_clause_array['days_market']=$listing->days_on_market;
            $set_clause_string .= ' '.$this->db_properties_days_on_market_col.' = :days_market,';
        }
        if(!empty($listing->address)){
            $set_clause_array['address']=$listing->address;
            $set_clause_string .= ' '.$this->db_properties_address_col.' = :address,';
        }
        if(!empty($listing->city)){
            $set_clause_array['city']=$listing->city;
            $set_clause_string .= ' '.$this->db_properties_city_col.' = :city,';
        }
        if(!empty($listing->bedrooms)){
            $set_clause_array['bedrooms']=$listing->bedrooms;
            $set_clause_string .= ' '.$this->db_properties_bedrooms_col.' = :bedrooms,';
        }
        if(!empty($listing->bathrooms)){
            $set_clause_array['bathrooms']=$listing->bathrooms;
            $set_clause_string .= ' '.$this->db_properties_bathrooms_col.' = :bathrooms,';
        }
        if(!empty($listing->square_feet)){
            $set_clause_array['sqft']=$listing->square_feet;
            $set_clause_string .= ' '.$this->db_properties_square_feet_col.' = :sqft,';
        }
        if(!empty($listing->year_built)){
            $set_clause_array['year_built']=$listing->year_built;
            $set_clause_string .= ' '.$this->db_properties_year_built_col.' = :year_built,';
        }
        if(!empty($listing->pool)){
            $set_clause_array['pool']=$listing->pool;
            $set_clause_string .= ' '.$this->db_properties_pool_col.' = :pool,';
        }
        if(!empty($listing->description)){
            $set_clause_array['descr']=$listing->description;
            $set_clause_string .= ' '.$this->db_properties_description_col.' = :descr,';
        }
        return array(rtrim($set_clause_string,','),$set_clause_array);
    }
    
    public function getAllPhotos($listing_id){        
        try{parent::checkDatabaseConnection();}
        catch(\Exception $e){return $e->getMessage();}
        try{
            $all_photos_query = static::$dbcnx->prepare('SELECT * FROM '.parent::getPhotosTable().' WHERE '.$this->db_photos_property_id_col.' = :id ORDER BY '.$this->db_photos_order_col);
            $all_photos_query->bindParam(':id',$listing_id);
            $all_photos_query->execute();
        } catch (\PDOException $ex) {
            return 'There was a problem retrieving the photos for this listing. Please try again momentarily.';
        }
        $all_photos_array = array();
        while($photo = $all_photos_query->fetch(PDO::FETCH_ASSOC)){$all_photos_array[]=array('link'=>$photo[$this->db_photos_link_col],'main'=>$photo[$this->db_photos_main_photo_col],'order'=>$photo[$this->db_photos_order_col],'id'=>$photo[$this->db_photos_id_col]);}
        return $all_photos_array;
    }
    
    public function updatePhotoOrder($listing_id,$update_order_array){        
        try{parent::checkDatabaseConnection();}
        catch(\Exception $e){return $e->getMessage();}
        $photo_id = $photo_order = NULL;
        try{
            $update_order_query = static::$dbcnx->prepare('UPDATE '.parent::getPhotosTable().' SET '.$this->db_photos_order_col.' = :order WHERE '.$this->db_photos_id_col . ' = :id AND '.$this->db_photos_property_id_col .' = :property_id LIMIT 1');
            $update_order_query->bindParam(':id',$photo_id);
            $update_order_query->bindParam(':order',$photo_order);
            $update_order_query->bindParam(':property_id',$listing_id);
        } catch (\PDOException $ex) {
            return 'There was a problem updating the photo order';
        }
        foreach($update_order_array as $order){
            $photo_id = $order['id'];
            $photo_order = $order['order'];
            try{
                $update_order_query->execute();
            } catch (\Exception $ex) {
                throw new \Exception('There was a problem updating the photo order. Please try again momentarily.');
            }
        }
        return TRUE;
    }
    
    public function updatePrimaryPhoto($listing_id,$primary_photo_id){        
        try{parent::checkDatabaseConnection();}
        catch(\Exception $e){throw new Exception($e->getMessage(),$e->getCode(),NULL);}
        if(!is_numeric($primary_photo_id)){
            throw new Exception('Invalid Photo ID', NULL, NULL);
        }
        try{
            $reset_main_query = static::$dbcnx->prepare('UPDATE '.parent::getPhotosTable().' SET '.$this->db_photos_main_photo_col.' = \'N\' WHERE '.$this->db_photos_property_id_col .' = :property_id');
            $reset_main_query->bindParam(':property_id',$listing_id);
            $reset_main_query->execute();
        } catch (\PDOException $ex) {
            throw new Exception('There was a problem resetting the main photo.',NULL,NULL);
        }
        try{
            $set_main_photo_query = static::$dbcnx->prepare('UPDATE '.parent::getPhotosTable().' SET '.$this->db_photos_main_photo_col.' = \'Y\' WHERE '.$this->db_photos_property_id_col .' = :property_id AND '.$this->db_photos_id_col.' = :photo_id LIMIT 1');
            $set_main_photo_query->bindParam(':property_id',$listing_id);
            $set_main_photo_query->bindParam(':photo_id',$primary_photo_id);
            $set_main_photo_query->execute();
        } catch (\PDOException $ex) {
            throw new Exception('There was a problem resetting the main photo.',NULL,NULL);
        }
        return TRUE;
    }
    
    public function deleteListing($listing_id){
        try{parent::checkDatabaseConnection();}
        catch(\Exception $e){return $e->getMessage();}
        try{
           $this->deleteAllPhotos($listing_id);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), NULL, NULL);
        }
        try{
            $delete_info_query = static::$dbcnx->prepare('DELETE FROM '.parent::getPropertyTable().' WHERE '.$this->db_properties_id_col. ' = :id LIMIT 1');
            $delete_info_query->bindParam(':id',$listing_id);
            $delete_info_query->execute();
        } catch (\PDOException $ex) {
            throw new \Exception('There was a problem deleting this listing. Please try again momentarily.',NULL,NULL);
        }
        return TRUE;
    }
    
    private function deleteAllPhotos($listing_id){
        $all_photos = $this->getAllPhotos($listing_id);
        foreach($all_photos as $photo){
            if(is_file(static::$absolute_photos_directory.$photo['link'])){
                unlink(static::$absolute_photos_directory.$photo['link']);
            }
        }
        try{
            $delete_info_query = static::$dbcnx->prepare('DELETE FROM '.parent::getPhotosTable().' WHERE '.$this->db_photos_property_id_col. ' = :id');
            $delete_info_query->bindParam(':id',$listing_id);
            $delete_info_query->execute();
        } catch (\PDOException $ex) {
            throw new Exception('There was a problem deleting the photo records. Please try again momentarily.');
        }
        return TRUE;
    }
    
    public function deleteIndividualPhoto($listing_id,$photo_id){
        try{parent::checkDatabaseConnection();}
        catch(\Exception $e){return $e->getMessage();}
        try{
            $get_photo_query = static::$dbcnx->prepare('SELECT '.$this->db_photos_link_col.' FROM '.parent::getPhotosTable().' WHERE '.$this->db_photos_property_id_col.' = :property_id AND '.$this->db_photos_id_col.' =:photo_id LIMIT 1');
            $get_photo_query->bindParam(':property_id',$listing_id);
            $get_photo_query->bindParam(':photo_id',$photo_id);
            $get_photo_query->execute();
        } catch (\PDOException $ex) {
            throw new \Exception('There was a problem retrieving the photos for this listing. Please try again momentarily.',NULL,NULL);
        }
        $requested_photo = $get_photo_query->fetch(PDO::FETCH_ASSOC);
        if(is_file(static::$absolute_photos_directory.$requested_photo[$this->db_photos_link_col])){
            unlink(static::$absolute_photos_directory.$requested_photo[$this->db_photos_link_col]);
        }
        try{
            $delete_photo_query = static::$dbcnx->prepare('DELETE FROM '.parent::getPhotosTable().' WHERE '.$this->db_photos_property_id_col.' = :property_id AND '.$this->db_photos_id_col.' =:photo_id LIMIT 1');
            $delete_photo_query->bindParam(':property_id',$listing_id);
            $delete_photo_query->bindParam(':photo_id',$photo_id);
            $delete_photo_query->execute();
        } catch (\PDOException $ex) {
            throw new Exception('There was a problem removing the photo record. Please try again momentarily.', '', '');
        }
        return TRUE;
    }
    
    public function saveNewPhoto($listing_id, $photo_link){
        try{parent::checkDatabaseConnection();}
        catch(\Exception $e){return $e->getMessage();}
        try{
            $get_photo_query = static::$dbcnx->prepare('INSERT INTO '.parent::getPhotosTable().' SET '.$this->db_photos_property_id_col.' = :property_id, '.$this->db_photos_link_col.' =:photo_link');
            $get_photo_query->bindParam(':property_id',$listing_id);
            $get_photo_query->bindParam(':photo_link',$photo_link);
            $get_photo_query->execute();
        } catch (\PDOException $ex) {
            throw new \Exception('There was a problem inserting the photo for this listing. Please try again momentarily.',NULL,NULL);
        }
    }
}

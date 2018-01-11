<?php
require_once dirname(__FILE__).'/openerplib/xmlrpc.inc';
include dirname(__FILE__).'/../../prestaerp/erp_product_category.php';
include dirname(__FILE__).'/../../prestaerp/erp_product.php';
class ManageProviderCli
{
    /**
     *
     *
     */
    protected $current_currency;
    protected $actual_change;
    protected $current_supplier;
    protected $suppliers = array();
    protected $weeks = array('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche');
    protected $currentFile = null;
	protected $client = null;
	protected $userId = null;
	protected $sock = null;
    /**
     *
     *
     */
    public function __construct()
    {
        $result = Db::getInstance()->executeS('SELECT distinct id_supplier FROM `'._DB_PREFIX_.'manage_provider_supplier_configuration`');
        foreach ($result as $key => $value) {
            $this->suppliers[] = $value['id_supplier'];
        }
        $this->current_currency = $this->_getChangeRate();

        /*
		$this->client = new xmlrpc_client(Configuration::get("Url") . ":" . Configuration::get("Port") . "/xmlrpc/object");
		$this->sock   = new xmlrpc_client(Configuration::get("Url") . ":" . Configuration::get("Port") . "/xmlrpc/common");
		$msg    = new xmlrpcmsg('login');
		$msg->addParam(new xmlrpcval(Configuration::get("Database"), 'string'));
		$msg->addParam(new xmlrpcval(Configuration::get("Username"), 'string'));
		$msg->addParam(new xmlrpcval(Configuration::get("Password"), 'string'));
		$resp = $this->sock->send($msg);
		$this->userId = $resp->value()->scalarval();
		$productErp     = new erp_product();*/
    }
    protected function _getChangeRate()
    {
        $euro  = 0;
        return 1;
        $xml = @simplexml_load_file('http://vcvbank.com/rivo/xchangeconfiguration/scratch.php');
        $cours = @current($xml);
        foreach ($cours as $key => $value) {
                if( $value->sigle == "EUR" ){
                    $euro = (float)$value->MidCloture > 0 ? (float)$value->MidCloture : (float)$value->MidOuverture;
                break;
                }
        }
        if( $euro < 3300 )
            $euro = 3300;
        return $euro;
    }
    public function getCSVInformation( $id_supplier )
    {
        $info = array();
        $query = 'select * FROM `'._DB_PREFIX_.'manage_provider_csv_configuration` WHERE id_supplier='.$id_supplier;
        $csv = Db::getInstance()->executeS( $query );
        foreach ($csv as  $value) {
            $info[$value['cle']] = $value['valeur'];
        }
        return $info;
    }
        protected function getRemoteCSV( $id_supplier )
    {
        $result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'manage_provider_supplier_configuration` WHERE id_supplier='.$id_supplier );
        if( $result['type'] == 'ftp')
        {
            $conn_id = ftp_connect($result['host']);
            // Identification avec un nom d'utilisateur et un mot de passe
            $login_result = ftp_login($conn_id, $result['username'], $result['password']);
            if ((!$conn_id) || (!$login_result)) {
                Logger::AddLog("La connexion FTP a échoué", 3, NULL, Supplier::getNameById($id_supplier));
                return false;
            }

            $local_file = dirname(__FILE__) . '/'.$id_supplier.'.CSV';
            @unlink($local_file);

            // Ouverture du fichier pour écriture
            $handle = fopen($local_file, 'w');

            // Tente de téléchargement le fichier $remote_file et de le sauvegarder dans $handle
            if (ftp_fget($conn_id, $handle, $result['filename'], FTP_ASCII, 0)) {
                if($result['filename'])
                return true;
            }

        } else {
            $file = @fopen($result['filename'], "rb");
            if ( $file ) 
            {
                Logger::AddLog("La connexion HTTP a échoué", 3, NULL, Supplier::getNameById($id_supplier));
                return false;
            }
            $local_file = dirname(__FILE__) . '/'.$id_supplier.'.CSV';
            @unlink($local_file);

            // Ouverture du fichier pour écriture
            $handle = fopen($local_file, 'w');
            while(!feof($file)) {
                fwrite($handle, fread($file, 1024 * 8 ), 1024 * 8 );
            }
            fclose($handle);
            fclose($file);
            return true;
        }

    }
    protected function get_redirect_url($url)
    {
        $redirect_url = null; 
     
        $url_parts = @parse_url($url);
        if (!$url_parts) return false;
        if (!isset($url_parts['host'])) return false; //can't process relative URLs
        if (!isset($url_parts['path'])) $url_parts['path'] = '/';
          
        $sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int)$url_parts['port'] : 80), $errno, $errstr, 30);
        if (!$sock) return false;
          
        $request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?'.$url_parts['query'] : '') . " HTTP/1.1\r\n"; 
        $request .= 'Host: ' . $url_parts['host'] . "\r\n"; 
        $request .= "Connection: Close\r\n\r\n"; 
        fwrite($sock, $request);
        $response = '';
        while(!feof($sock)) $response .= fread($sock, 8192);
        fclose($sock);
     
        if (preg_match('/^Location: (.+?)$/m', $response, $matches)){
            if ( substr($matches[1], 0, 1) == "/" )
                return $url_parts['scheme'] . "://" . $url_parts['host'] . trim($matches[1]);
            else
                return trim($matches[1]);
      
        } else {
            return false;
        }
     
    }
    protected function getIdCategoryMapped( $category_name, $id_supplier )
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'manage_provider_maping` WHERE CSV_maping='."'".pSQL($category_name)."' AND id_supplier = ".$id_supplier;
        $row = Db::getInstance()->getRow( $sql );
        if(!empty($row))
            return $row['id_category'];
        return false;
    }
    protected function addCategory( $id_parent, $category_name, $level_depth )
    {
        Db::getInstance()->insert('category', array(
            'id_parent' => $id_parent,
            'level_depth' => $level_depth,
            'active' => 1,
            'date_add' => date('Y-m-d H:i:s'),
        ));
        $idCategory1 = Db::getInstance()->Insert_ID();
        Db::getInstance()->insert('category_lang', array(
            'id_category' => $idCategory1,
            'id_shop' => 1,
            'id_lang' => 2,
            'name' => pSQL($category_name),
            'link_rewrite' => Tools::link_rewrite( $category_name ),
        ));
        Db::getInstance()->insert('category_shop', array(
            'id_category' => $idCategory1,
            'id_shop' => 1,
        ));
        for ($j=1; $j<=3; $j++)
        {
            Db::getInstance()->insert('category_group', array(
                'id_category' => $idCategory1,
                'id_group' => $j,
            ));
        }
        return $idCategory1;
    }
    protected function addProductShop($productID,$id_supplier,$id_manufacturer,$id_category, $price,$name, $description,

                                     $weight,$reference,$imageURL,$ean13, $id_shop){
        $dataProductShop = array(        
            'id_product' => $productID,
            'id_shop' => $id_shop,
            'id_category_default' => $id_category,
            'price' => $this->getPrice( $id_supplier, $id_category , $price , $weight, $id_shop),
            'wholesale_price' => $price * $this->current_currency,
            'active' => 1,  
            'date_add' => pSQL(date('Y-m-d H:i:s')),
            'id_tax_rules_group' => ((int)$id_shop > 1 ? 7 : 0 ),     
        );
        Db::getInstance()->insert('product_shop', $dataProductShop);

        $dataProductLang = array(
            'id_product' => $productID,
            'id_shop' => $id_shop,
            'id_lang' => 2,
            'description' => pSQL($description),
            'link_rewrite' => Tools::link_rewrite($name),
            'name' => pSQL($name),
            'available_later' => 'Sur commande', 
        );    
        Db::getInstance()->insert('product_lang', $dataProductLang);
         $sql = "SELECT * 
            FROM ps_image 
            WHERE id_product = " . $productID;
        if ($row = Db::getInstance()->getRow($sql)) {
            $dataImageShop = array(                    
                'id_image' => $row['id_image'],          
                'id_shop' => $id_shop,
                'cover' => 1,
            );
            Db::getInstance()->insert('image_shop', $dataImageShop);
        }

         Db::getInstance()->AutoExecute(_DB_PREFIX_.'stock_available', array(
            'id_product' => $productID,
            'id_product_attribute' => 0,
            'id_shop' => $id_shop,
            'id_shop_group' => 0,
            'quantity' => 0,
            'depends_on_stock' => 0,
            'out_of_stock' => 1
         ), 'INSERT');   

    }
    protected function addProduct( $id_supplier,$id_manufacturer,$id_category, $price,$name, $description,

                                     $weight,$reference,$imageURL,$ean13)
    {    
        $dataProduct = array(
            'id_supplier' => $id_supplier,
            'date_add' => pSQL(date('Y-m-d H:i:s')),
            'ean13'=>pSQL($ean13),
           // 'wholesale_price'=> calculDePrixPublic($col),
            'id_category_default' => $id_category,
            'price' => $this->getPrice( $id_supplier, $id_category , $price ,$weight),
            'wholesale_price' => $price * $this->current_currency,
            'id_manufacturer' => $id_manufacturer,
            'reference' => pSQL($reference),
            'supplier_reference' => pSQL($reference), 
            'weight' => ($weight /1000), // Le poids est en KG dans PS.
            'active' => 1
        );    
    
        Db::getInstance()->insert('product', $dataProduct);
        $productID = Db::getInstance()->Insert_ID(); 
        // Réassigner dans la catégorie
        Db::getInstance()->delete('category_product', 'id_category = ' . $id_category . ' AND id_product = ' . $productID);
        $dataCategoryProduct = array(
            'id_category' => $id_category,
            'id_product' => $productID,
        );
        Db::getInstance()->insert('category_product', $dataCategoryProduct);  
        Tag::addTags(2,$productID,"$name,Madagascar,La reunion,Vente,en ligne");
    
    // Image
    // Tester s'il y a déjà une image, ne pas télécharger sinon pour la bande passante
    $sql = "SELECT * 
            FROM ps_image 
            WHERE id_product = " . $productID;
    if ($row = Db::getInstance()->getRow($sql)) {
        // Ne rien faire car l'image existe déjà
    } else {
        $dataImage = array(                    
            'id_product' => $productID,            
            'cover' => 1,        
        );
        Db::getInstance()->insert('image', $dataImage);
        $imageID = Db::getInstance()->Insert_ID();
        $dataImageLang = array(
            'id_image' => $imageID,
            'id_lang' => 2,
            'legend' => pSQL($name),
        );
        Db::getInstance()->insert('image_lang', $dataImageLang);
    
        
        $image = new Image($imageID);
        $url =  $this->get_redirect_url($imageURL);
        if(preg_match("/novisuel/", $url) || $url == "" ){
            $pathSansFormat = $image->getPathForCreation();
            $path = $pathSansFormat . '.' . $image->image_format;
            //test si l'url contient novisuel 
            exec("wget --force-directories -O $path $imageURL");
            $imagesTypes = ImageType::getImagesTypes('products');
            foreach ($imagesTypes as $k => $imageType)
            {
                $imageResized = $pathSansFormat.'-'.stripslashes($imageType['name']) . '.' . $image->image_format;  
                ImageManager::resize($path, $imageResized, $imageType['width'], $imageType['height']);
                @chmod($imageResized, 0777);
            }
        } else {
            $dataImageNoImage = array(
                'id_product' => $productID,
                'url' => $col[15]
            );
            Db::getInstance()->insert('novisual', $dataImageNoImage);
            $pathSansFormat = $image->getPathForCreation();
            $path = $pathSansFormat . '.' . $image->image_format;
            //test si l'url contient novisuel 
            copy(dirname(__FILE__).'/default.gif',$path);
            $imagesTypes = ImageType::getImagesTypes('products');
            foreach ($imagesTypes as $k => $imageType)
            {
                $imageResized = $pathSansFormat.'-'.stripslashes($imageType['name']) . '.' . $image->image_format;  
                ImageManager::resize($path, $imageResized, $imageType['width'], $imageType['height']);
                @chmod($imageResized, 0777);
            }
        }
    }   
    
    // OutOfStock Management
    StockAvailable::setProductOutOfStock($productID, 1);
    
    // Indexation
    Search::indexation(false, $productID);
//	    $productErp     = new erp_product();
    //$check       = $productErp->check_specific_product($productID,-5,$this->userId, $this->sock, $this->client, "Cron Auto");
//	var_dump($check);
    try{
        $product  = new Product($productID,true,2);
        if($weight > 45000){
            $product->available_now = '45 jours ouvrable';
        }else{
            $product->available_now = '9 jours ouvrable';
        }
    $product->name = $name;
    $product->save();
	
	
                    }catch(Exception $e){
                        var_dump($e);
                    }
					
					
    return $productID;

    }
     protected function addCategoryPriceDefault($id_supplier,$id_category,$id_shop=1)
    {
        $data = array(
                'id_category'=>$id_category,
                "id_supplier"=>$id_supplier,
                "marge" => 0.3 ,
                "id_shop" => $id_shop,
                "douane" => 0.15,
                "type_douane" => 0,
                "type_marge" =>0,
                "poid" => 0.0137,
                "frais" => 8);
        Db::getInstance()->insert('manageprice',$data);
        return $data;
    }
    protected function getPrice( $id_supplier, $id_category , $priceCSVHT, $weight, $id_shop = 1 )
    {
        $prix  = 0 ;
        $query = 'SELECT * FROM `'._DB_PREFIX_.'manageprice`  WHERE id_supplier='.$id_supplier.' AND id_category='.$id_category.' AND id_shop='.$id_shop;
        $result = Db::getInstance()->getRow( $query );
        if(empty($result))
            $result = $this->addCategoryPriceDefault($id_supplier,$id_category,$id_shop);
        $priceHt = $priceCSVHT * $this->current_currency;
        $prixPoid = $weight  * (float)$result['poid'] ;
        $price =  $priceHt + $prixPoid;
        //prendre douane;

        $query = 'SELECT * FROM `'._DB_PREFIX_.'manageprice_default`  
                WHERE supplier_id='.$id_supplier.' 
                AND shop_id = '.$id_shop;
         $result2 = Db::getInstance()->getRow( $query );
         if((float)$result2['min_price'] > $priceHt  && $weight > $result2['min_weight']  && $weight <= $result2['max_weight'] ){
            if($result['type_douane'] == 0)
                $douane = $price * (float)$result['douane'];
            else 
                $douane = (float)$result['douane'];
            if($result['type_marge'] == 0)
                $marge = $price * (float)$result['marge'];
            else 
                $marge = (float)$result['marge'];
            return  ( $marge + $price ) +  $result2['default_transport'];
         }

        if($result['type_douane'] == 0)
            $douane = $price * (float)$result['douane'];
        else 
            $douane = (float)$result['douane'];
        if($result['type_marge'] == 0)
            $marge = $price * (float)$result['marge'];
        else 
            $marge = (float)$result['marge'];
        return ($douane  + $marge + $price ) +  $result['frais'];
        /*
$query = 'SELECT * FROM `'._DB_PREFIX_.'manageprice`  
            WHERE id_supplier= 62 
            AND id_shop = '.$id_shop.'
            AND id_category='.$id_category;
        $result = Db::getInstance()->getRow( $query );
        
        if(!empty($result)){
            $query = 'SELECT * FROM `'._DB_PREFIX_.'manageprice_default`  
                WHERE supplier_id= 62 
                AND shop_id = '.$id_shop;
            $result2 = Db::getInstance()->getRow( $query );
            $priceHt = $prixHT;
            $prixPoid = (float)$poidsv * 1000  * (float)$result['poid'] ;
            $price =  $priceHt + $prixPoid;
            if($result2){
                if((float)$result2['min_price'] > $prixHT  && ($poidsv*1000) > $result2['min_weight']  && ($poidsv*1000) <= $result2['max_weight'] ){
                    if($result['type_douane'] == 0)
                        $douane = $price * (float)$result['douane'];
                    else 
                        $douane = (float)$result['douane'];
                    if($result['type_marge'] == 0)
                        $marge = $price * (float)$result['marge'];
                    else 
                        $marge = (float)$result['marge'];
                    return  ( $marge + $price ) +  $result2['default_transport'];

                }else{
                                //prendre douane;
                    if($result['type_douane'] == 0)
                        $douane = $price * (float)$result['douane'];
                    else 
                        $douane = (float)$result['douane'];
                    if($result['type_marge'] == 0)
                        $marge = $price * (float)$result['marge'];
                    else 
                        $marge = (float)$result['marge'];
                    return ($douane  + $marge + $price ) +  $result['frais'];
                }
            }else{
                            //prendre douane;
            if($result['type_douane'] == 0)
                $douane = $price * (float)$result['douane'];
            else 
                $douane = (float)$result['douane'];
            if($result['type_marge'] == 0)
                $marge = $price * (float)$result['marge'];
            else 
                $marge = (float)$result['marge'];
            return ($douane  + $marge + $price ) +  $result['frais'];
            }

        */
    }
    protected function isDayToUpdate( $id_supplier )
    {
        $day = date('w');
        $result = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'manage_provider_import_action` ` WHERE id_supplier='.$id_supplier );   
        $date  = array();
        foreach ($result as $key => $value) {
            $date[] = array_search( $value['day'], $this->weeks);
        }
        if( in_array($day, $date))
            return true;
        return false;
    }
    public function getCategoriesColumns( $id_supplier )
    {
        $columns = array();
        $query = 'SELECT * FROM `'._DB_PREFIX_.'manage_provider_categories` WHERE id_supplier = '.$id_supplier.' ORDER BY flag';
        $results = Db::getInstance()->executeS($query);
        foreach ($results as $key => $value) {
            $columns[] = $value['column'];
        }
        return $columns;
    }
    protected function getCategoryMapping( $id_supplier, $category_name )
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'manage_provider_maping` WHERE id_supplier = '.$id_supplier.' AND CSV_maping = '.$category_name;
        $row = Db::getInstance()->getRow( $sql );
        if( !empty($row) )
            return $row['id_category'];

        return false;
    }
    protected function getIdCategory( $categories ,$id_supplier)
    {
        $id_parent = 46;
        $last_category = 0;
        $current_leveldepth = 2;
        foreach ($categories as $key => $value) {
            $mappedid = $this->getIdCategoryMapped($value,$id_supplier);
            if($mappedid != false)
            {
                $last_category = $mappedid;
                continue;
            }
            if($last_category > 0 )
                $id_parent = $last_category;
            $sql = "SELECT ps_category_lang.id_category, ps_category.level_depth 
            FROM ps_category
            LEFT JOIN ps_category_lang ON ps_category.id_category = ps_category_lang.id_category
            WHERE ps_category_lang.name = '" . addslashes(  $value ) . "' 
                  AND ps_category_lang.id_lang = 2";
            $row = $row = Db::getInstance()->getRow($sql);
            if (!empty($row)) 
            {
                $last_category = $row['id_category'];
                $current_leveldepth = $row['level_depth'];
            }else{
                $last_category = $this->addCategory( $id_parent, $value, $current_leveldepth + 1 );
            }
        }
        return $last_category;
    }
    private function getSupplierShop($id_supplier){
        $ids = array();
        $query = "SELECT * FROM ps_supplier_shop WHERE id_supplier = ".$id_supplier;
        $results = Db::getInstance()->ExecuteS($query);
        foreach ($results as $result) {
            $ids[] = $result['id_shop'];
        }
        return $ids;
                   
    }
    public function treat(  )
    {
        foreach ($this->suppliers as $id_supplier) {
            echo "Begin supplier $id_supplier\n";
            if( !$this->isDayToUpdate($id_supplier) )
                continue;
            if(!$this->getRemoteCSV( $id_supplier ))
                continue;
            echo $sql = 'DELETE FROM `'._DB_PREFIX_.'manage_provider_product` WHERE id_supplier='.$id_supplier;   
            Db::getInstance()->execute($sql);
            echo "deleted\n";
            $categoriesColumns = $this->getCategoriesColumns( $id_supplier );
            $local_file = dirname(__FILE__) . '/'.$id_supplier.'.CSV';
            $fh = fopen($local_file, "r");
            $totalLines = intval(exec('wc -l ' . $local_file));
            $info = $this->getCSVInformation( $id_supplier );
            echo "total line $totalLines\n";
            $i=0;
            while ($line = fgets($fh)) {
                echo "traitement $i sur $totalLines\n";
                usleep(2);
                $i++;
                $line = utf8_encode($line);
                $col = explode  (';', $line ); 
                $categories = array();
                foreach ($categoriesColumns as  $value) {
                    $categories[] = $col[$value];
                }
                if ($i == 1) 
                    continue;
                if(trim(strtoupper($col[$info['manufacturer']])) == "CASINO")
                    continue;
                if (((float)$col[$info['weight']]) === 0 )
                    continue;
                if( Manufacturer::getIdByName( strtoupper( trim($col[$info['manufacturer']] ) ) ) === false && strlen( trim($col[$info['manufacturer']]) ) > 2  ){
                    Db::getInstance()->insert('manufacturer', array(
                        'name'=>pSQL(strtoupper(trim($col[$info['manufacturer']]) )),
                        'active'=>1,
                        'date_add'=>date('Y-m-d H:i:s'),
                        'date_upd'=>date('Y-m-d H:i:s'),
                    ));
                }
                Db::getInstance()->insert('manage_provider_product', array(
                    'reference' => pSQL($col[$info['reference']]),
                    'id_supplier' => $id_supplier,
                ));
                echo "\n traitement reference ".$col[$info['reference']]."\n";
                $sql = "SELECT * FROM ps_product WHERE supplier_reference = '" . pSQL($col[$info['reference']]) . "' AND reference = '" . pSQL($col[$info['reference']]) . "' AND id_supplier=".$id_supplier ; 
                $id_shops = $this->getSupplierShop($id_supplier);
                // Si le produit existe
                if ($row = Db::getInstance()->getRow($sql)) {
                    Db::getInstance()->execute('DELETE FROM  '._DB_PREFIX_."specific_price  WHERE id_product = $row[id_product]");
                    SpecificPrice::deleteByProductId($row['id_product']);
                    if( (int)Product::getQuantity( $row['id_product'] )  > 0 ){
                        echo "Quantité existant on ne met pas a jour \n";
                        continue;
                    }
                    try{
                        $product  = new Product($row['id_product'],false,2);
                        if($col[$info['weight']] > 45000){
                            $product->available_now = '45 jours ouvrables';
                        }else{
                            $product->available_now = '9 jours ouvrables';
                        }
                        // $product->name =$col[$info['name']];
                        $product->save();
                    }catch(Exception $e){
                            echo "exeption in save product ".$e->getMessage();
                    }
                    foreach ($id_shops as $key => $id_for_shop) {
                        SpecificPrice::deleteByProductId($row['id_product']);
                        $m_prix= $this->getPrice( $id_supplier, $row['id_category_default'] , $col[$info['price']],$col[$info['weight']] ,$id_for_shop);
                        echo "\nprix shop $id_for_shop  $m_prix \n";
                        echo $query_del = 'SELECT count(id_product) as nb,price  FROM '._DB_PREFIX_.'product_shop where id_product='.$row['id_product'].' AND id_shop = '.$id_for_shop;
                        echo "\n\n\n";
                        $count_product_shop = Db::getInstance()->getRow($query_del);
                        if( $count_product_shop['nb'] > 0){
                            $val_reference = round((float)$count_product_shop['price'] - $m_prix);
                            $percentage =round(  ($val_reference * 100 /(float)$count_product_shop['price'] ) );
                            if( $percentage >= 15 ){
                                $specificPrice =  new SpecificPrice();
                                $specificPrice->id_product = (int)$row['id_product'];
                                $specificPrice->id_product_attribute = 0;
                                $specificPrice->id_shop = $id_for_shop;
                                $specificPrice->id_currency = 0;
                                $specificPrice->id_country = 0;
                                $specificPrice->id_group = 0;
                                $specificPrice->id_customer = 0;
                                $specificPrice->price = -1;
                                $specificPrice->from_quantity = 1;
                                $specificPrice->reduction = $val_reference;
                                $specificPrice->reduction_type = 'amount';
                                $specificPrice->from = '0000-00-00 00:00:00';
                                $specificPrice->to = '0000-00-00 00:00:00';
                                $specificPrice->add();
                            }else{
                                $dataProduct = array(
                                        'price' => $m_prix,
                                        'wholesale_price' => $col[$info['price']]  * $this->current_currency,   
                                        'quantity' => 0,
                                        'price' => $m_prix,
                                        'ean13'=>$col[$info['ean13']],
                                        'active'=>1,
                                        'id_manufacturer' => Manufacturer::getIdByName( strtoupper( trim($col[$info['manufacturer']] ) ) ),
                                    );
                                     $dataProductShop = array(  
                                        'wholesale_price' => $col[$info['price']]  * $this->current_currency,  
                                        'price' => $m_prix,
                                        'active' => 1,
                                        'id_shop' => $id_for_shop,
                                        'id_tax_rules_group' => ((int)$id_for_shop > 1 ? 6 : 0 ),
                                    );
                                    Db::getInstance()->update('product_shop', $dataProductShop, 'id_product = ' . $row['id_product'].' AND id_shop = '.$id_for_shop);
                                    Db::getInstance()->update('product', $dataProduct, 'id_product = ' . $row['id_product']);
                            }
                        }else{
                            $this->addProductShop($row['id_product'],$id_supplier,Manufacturer::getIdByName( strtoupper( trim($col[$info['manufacturer']] ) ) ),$id_category, $col[$info['price']], $col[$info['name']], $col[$info['description']],$col[$info['weight']],$col[$info['reference']],$col[$info['image']],$col[$info['ean13']],$id_for_shop);
                        }
                    }
                } else {
                    echo "Ajout d'un nouveau produit\n";
                    $id_category = $this->getIdCategory( $categories , $id_supplier );
                    $id_product = $this->addProduct( $id_supplier,Manufacturer::getIdByName( strtoupper( trim($col[$info['manufacturer']] ) ) ), $id_category, $col[$info['price']], $col[$info['name']], $col[$info['description']], $col[$info['weight']],$col[$info['reference']],$col[$info['image']],$col[$info['ean13']]);
                    foreach ($id_shops as $key => $id_shop) {
                        $this->addProductShop($id_product,$id_supplier,Manufacturer::getIdByName( strtoupper( trim($col[$info['manufacturer']] ) ) ),$id_category, $col[$info['price']], $col[$info['name']], $col[$info['description']],$col[$info['weight']],$col[$info['reference']],$col[$info['image']],$col[$info['ean13']],$id_shop);
                    }

                }
            }
            echo $sql = "SELECT * 
            FROM ps_product 
            WHERE id_supplier = ".$id_supplier." AND reference  NOT IN (SELECT reference FROM ps_manage_provider_product )
            AND supplier_reference  NOT IN (SELECT reference FROM ps_manage_provider_product );";
            $results = Db::getInstance()->ExecuteS($sql);
            $i = 0;
            foreach ($results as $result) {
                if((int)Product::getQuantity( $result['id_product'] )  > 0 )
                    continue;
                    echo "\n\ndesactiver product ".$result["id_product"]." reference ".$result["reference"];
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_."product_shop SET active=0,redirect_type='404' WHERE id_product = $result[id_product]");
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_."product SET active=0,redirect_type='404' WHERE id_product = $result[id_product]");
                   // Db::getInstance()->update('product', array('active' => 0,'redirect_type'=>'404'), 'id_product = ' . $result['id_product']);
            } 
        }
        Logger::AddLog("Mise a jour terminer le ".date('d-m-Y hh:ss'), 3, NULL, Supplier::getNameById($id_supplier));
        exec("nohup chmod -Rfv 777 /home/exeia/img/* > /dev/null 2>&1");  
    }
	public function updatePricetoErp($prst_product_id,$new_price,$supplier_price){
		$product = Db::getInstance()->getRow("SELECT `erp_product_id`,`is_synch`,`prestashop_product_total_price` from `" . _DB_PREFIX_ . "erp_product_merge` where `prestashop_product_id`=".$prst_product_id);
		if ($product['erp_product_id'] > 0) {
			$context = array(
            'prestashop' => new xmlrpcval('prestashop', "string")
        );
        $erp_product_list = array(
            new xmlrpcval($product['erp_product_id'], 'int')
        );
        $arrayVal         = array(
            'list_price' => new xmlrpcval($new_price, "string"),
        );
		
            
        $msg_ser          = new xmlrpcmsg('execute');
        $msg_ser->addParam(new xmlrpcval(Configuration::getGlobalValue("Database"), "string"));
        $msg_ser->addParam(new xmlrpcval($this->userId, "int"));
        $msg_ser->addParam(new xmlrpcval(Configuration::getGlobalValue("Password"), "string"));
        $msg_ser->addParam(new xmlrpcval("product.product", "string"));
        $msg_ser->addParam(new xmlrpcval("write", "string"));
        $msg_ser->addParam(new xmlrpcval($erp_product_list, "array"));
        $msg_ser->addParam(new xmlrpcval($arrayVal, "struct"));
		$msg_ser->addParam(new xmlrpcval($context, "struct"));
		$resp = $this->client->send($msg_ser);
		}
    }
}
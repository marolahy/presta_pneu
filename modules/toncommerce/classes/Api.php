<?php

if(!class_exists('Curl'))

require_once dirname(__FILE__).'/Curl.php';



class TonCommerceApi

{

    /** API call base URL */

    const API_BASE_URL = 'https://api.toncommerce.net/';

    public function construct(){

        //$this->curl->setopt(CURLOPT_HEADER,["Authorization"=>Configuration::get('TONCOMMERCE_API_KEY', '')]);

    }



    public function getCategories()

    {

        $curl = new Curl();

        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));

        $curl->setopt(CURLOPT_RETURNTRANSFER,true);

        $curl->get( self::API_BASE_URL.'categories');

        $response = Tools::jsonDecode($curl->response);

        if($response->statut === "ok" ){

            return $response->categories;

        }

        return false;

    }

    public function getPrixReference()
    {

        $curl = new Curl();

        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));

        $curl->setopt(CURLOPT_RETURNTRANSFER,true);

        $curl->get( self::API_BASE_URL.'prix_reference');

        $response = Tools::jsonDecode($curl->response);
        $data = array();

        if($response->statut === "ok" ){


            foreach( $response->prix_reference as $value ){
                $class =  new stdClass;
                $class->prix_ht = $value->prix_ht;
                $class->com_ht = $value->com_ht;
                $data[$value->reference] = $class;
            }

            return $data;


        }

        return false;

    }
    public function getStockReference()
    {

        $curl = new Curl();

        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));

        $curl->setopt(CURLOPT_RETURNTRANSFER,true);

        $curl->get( self::API_BASE_URL.'stock_reference');

        $response = Tools::jsonDecode($curl->response);
        $return = array();
        if($response->statut === "ok" ){

             foreach($response->stock_reference as $stock_reference){
                $reference = explode('_',$stock_reference->reference_declinaison);
                $class = new stdClass();
                $class->id =  $reference[1];
                $class->stock = $stock_reference->stock;
                $return[$reference[0]][] = $class;
             }
             return $return;

        }

        return false;

    }


    public function getCategoryProducts($category_id)

    {

        $curl = new Curl();

        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));

        $curl->setopt(CURLOPT_RETURNTRANSFER,true);

        $curl->get( self::API_BASE_URL.'categories/'.$category_id.'/articles');

        $response = Tools::jsonDecode($curl->response);

        if($response->statut === "ok" ){

            return $response->articles;

        }

        return false;



    }

    public function getProduct($reference)

    {

        $curl = new Curl();

        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));

        $curl->setopt(CURLOPT_RETURNTRANSFER,true);

        $curl->get( self::API_BASE_URL.'articles/'.$reference);

        $response = Tools::jsonDecode($curl->response);

        if($response->statut === "ok" ){

            return $response->article;

        }

        return false;

    }

    public function getContact()

    {

        $curl = new Curl();

        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));

        $curl->setopt(CURLOPT_RETURNTRANSFER,true);

        $curl->get( self::API_BASE_URL.'contact');

        $response = Tools::jsonDecode($curl->response);

        if($response->statut === "ok" ){



            Db::getInstance()->update('contact',array(

                'email'=>$response->contact,

            ),"customer_service = 1");

        }

    }

    public function calculPrice($price, $commission)
    {

        return $price - $commission;

    }

    public function getQuantityWithoutDeclinaison($reference ){

        $curl = new Curl();

        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));

        $curl->setopt(CURLOPT_RETURNTRANSFER,true);

        $curl->get( self::API_BASE_URL.'articles/'.$reference.'/stock');

        $response = Tools::jsonDecode($curl->response);

        if($response->statut === "ok" ){

            return (int)$response->article->declinaisons[0]->stock;

        }

        return false;

    }

    public function getQuantityWithDeclinaison($reference,$declinaison_id ){

        $curl = new Curl();

        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));

        $curl->setopt(CURLOPT_RETURNTRANSFER,true);

        $curl->get( self::API_BASE_URL.'articles/'.$reference.'/stock');

        $response = Tools::jsonDecode($curl->response);

        if($response->statut === "ok" ){

            foreach ($response->article->declinaisons as $declinaison) {

                if( (int)$declinaison->id === (int)$declinaison_id )

                    return (int)$declinaison->stock;

            }

        }

        return false;

    }



   public function updateOrderStatus()
   {


        $data = Db::getInstance()->executeS("SELECT * FROM "._DB_PREFIX_."toncommerce_payment_order WHERE active = 1");

        foreach ($data as $value) {

            $curl = new Curl();

            $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));

            $curl->setopt(CURLOPT_RETURNTRANSFER,true);

            $curl->get(self::API_BASE_URL.'commandes/'.$value['id_payment']);

            $response = Tools::jsonDecode($curl->response);

            if($response->statut === "ok" ){

                $response = $response->commande;

                $order = new Order($value['id_order']);

                if (trim($response->etat) == 'commande validée'){

                    echo "\nato  $value[id_order] ===> $response->etat $response->paiement_etat  $response->expedition_etat\n";

                    switch(trim($response->paiement_etat)){

                        case 'partiellement remboursé':

                            if((int)$order->getCurrentState() !==  (int)Configuration::get('TONCOMMERCE_OS_partiellement_rembourse') )

                                $order->setCurrentState(Configuration::get('TONCOMMERCE_OS_partiellement_rembourse'));

                            break;

                        case 'remboursé':

                            if((int)$order->getCurrentState() !==  (int)Configuration::get('TONCOMMERCE_OS_rembourse') )

                                $order->setCurrentState(Configuration::get('TONCOMMERCE_OS_rembourse'));

                            break;

                        case 'opposition':

                            if((int)$order->getCurrentState() !==  (int)Configuration::get('TONCOMMERCE_OS_opposition') )

                                $order->setCurrentState(Configuration::get('TONCOMMERCE_OS_opposition'));

                            break;

                        case 'reçu':

                            switch($response->expedition_etat){

                                case 'partiellement expédié':

                                case 'expédié':

                                    if((int)$order->getCurrentState() !==  (int)Configuration::get('TONCOMMERCE_OS_expedie') )

                                        $order->setCurrentState(Configuration::get('TONCOMMERCE_OS_expedie'));

                                break;

                                case 'livré':

                                    $order->setCurrentState(Configuration::get('TONCOMMERCE_OS_livre'));

                                    Db::getInstance()->update('toncommerce_payment_order',array(

                                        'active'=>0,

                                    ),"id_toncommerce_order = $value[id_toncommerce_order]");

                                break;

                                default:

                                    if((int)$order->getCurrentState() !==  (int)Configuration::get('TONCOMMERCE_OS_recu') )

                                        $order->setCurrentState(Configuration::get('TONCOMMERCE_OS_recu'));

                                break;

                            }

                            break;

                        default:

                            if((int)$order->getCurrentState() !==  (int)Configuration::get('TONCOMMERCE_OS_attente') )

                                $order->setCurrentState(Configuration::get('TONCOMMERCE_OS_attente'));

                        break;



                    }

                }elseif($response->etat == 'commande annulée' || $response->etat == 'commande refusée' ){

                        $order->setCurrentState(Configuration::get('TONCOMMERCE_OS_annule'));

                        Db::getInstance()->update('toncommerce_payment_order',array(

                            'active'=>0,

                        ),"id_toncommerce_order = $value[id_toncommerce_order]");

                       //$query = "UPDATE "._DB_PREFIX_."toncommerce_payment_order SET active=0 WHERE id_toncommerce_order =".$order['id_order'];

                       // Db::getInstance()->execute($query);



                }

            }

        }

   }



    public function getAttributeCombinaison($data_combinaison)

    {

        $attributes = array();

        if(count($data_combinaison) ===  1 )

            return true;

        foreach ($data_combinaison as $value) {

            if($value->nom === 'defaut')

                continue;

            $attributes[] = $this->getOrAddAttribute($value->nom);

        }

        return $attributes;

    }

    public function getCategoriestoImport(){

        $query = 'SELECT * FROM `'._DB_PREFIX_.'toncommerce_categories`';

        $categories = array();

        $rows = Db::getInstance()->executeS($query);

        foreach ($rows as $row) {

            $categories[] = $row['id_category_from_ton_commerce'];

        }

        return $categories;

    }

    public function updateCountryActive(){

        $curl = new Curl();

        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));

        $curl->setopt(CURLOPT_RETURNTRANSFER,true);

        $curl->get( self::API_BASE_URL.'pays');

        $response = Tools::jsonDecode($curl->response);

        if($response->statut === "ok" ){

            foreach ($response->pays as $pays) {

                $query = 'UPDATE `'._DB_PREFIX_.'country` SET active = '.( $pays->etat === "actif" ? 1 : 0 )

                         .' WHERE id_country IN ( SELECT id_country FROM `'._DB_PREFIX_.'country_lang` WHERE '

                         .' LOWER(name) = '."'".strtolower($pays->nom)."')";

                Db::getInstance()->execute($query);

            }

        }

        return false;





    }

    public function checkProductIfAlreadyImported( $ref ){

        $query = 'SELECT COUNT(*) as id FROM `'._DB_PREFIX_.'toncommerce_product_mapping` WHERE reference=\''.$ref.'\'';

            $row = Db::getInstance()->getRow($query);

        if((int)$row['id'] > 0 )

            return true;

        else 

            return false;

    }


    public function addProductToPrestashop($reference,$categorie){
        $article = $this->getProduct($prod->reference);

    }

    public function importProduct()

    {

        $categories = $this->getCategories();

        $categories_to_check = $this->getCategoriestoImport();

        $id_lang = Context::getContext()->language->id;

        foreach ($categories as $categorie ) {

            if(!in_array($categorie->id, $categories_to_check))

                continue;
            $products = $this->getCategoryProducts($categorie->id);
            foreach ($products as $prod) {
                $this->insertProduct($prod->reference,$categorie->id);
            }
                    
        }

    }

    /**

     * copyImg copy an image located in $url and save it in a path

     * according to $entity->$id_entity .

     * $id_image is used if we need to add a watermark

     *

     * @param int $id_entity id of product or category (set in entity)

     * @param int $id_image (default null) id of the image if watermark enabled.

     * @param string $url path or url to use

     * @param string $entity 'products' or 'categories'

     * @param bool $regenerate

     * @return bool

     */

    protected  function copyImg($id_entity, $id_image = null, $url = '', $entity = 'products', $regenerate = true)

    {

        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');

        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));



        switch ($entity) {

            default:

            case 'products':

                $image_obj = new Image($id_image);

                $path = $image_obj->getPathForCreation();

                break;

            case 'categories':

                $path = _PS_CAT_IMG_DIR_.(int)$id_entity;

                break;

            case 'manufacturers':

                $path = _PS_MANU_IMG_DIR_.(int)$id_entity;

                break;

            case 'suppliers':

                $path = _PS_SUPP_IMG_DIR_.(int)$id_entity;

                break;

            case 'stores':

                $path = _PS_STORE_IMG_DIR_.(int)$id_entity;

                break;

        }



        $url = urldecode(trim($url));

        $parced_url = parse_url($url);



        if (isset($parced_url['path'])) {

            $uri = ltrim($parced_url['path'], '/');

            $parts = explode('/', $uri);

            foreach ($parts as &$part) {

                $part = rawurlencode($part);

            }

            unset($part);

            $parced_url['path'] = '/'.implode('/', $parts);

        }



        if (isset($parced_url['query'])) {

            $query_parts = array();

            parse_str($parced_url['query'], $query_parts);

            $parced_url['query'] = http_build_query($query_parts);

        }



        if (!function_exists('http_build_url')) {

            require_once(_PS_TOOL_DIR_.'http_build_url/http_build_url.php');

        }



        $url = http_build_url('', $parced_url);



        $orig_tmpfile = $tmpfile;



        if (Tools::copy($url, $tmpfile)) {

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.

            if (!ImageManager::checkImageMemoryLimit($tmpfile)) {

                @unlink($tmpfile);

                return false;

            }

            $tgt_width = $tgt_height = 0;

            $src_width = $src_height = 0;

            $error = 0;

            ImageManager::resize($tmpfile, $path.'.jpg', null, null, 'jpg', false, $error, $tgt_width, $tgt_height, 5, $src_width, $src_height);

            $images_types = ImageType::getImagesTypes($entity, true);



            if ($regenerate) {

                $previous_path = null;

                $path_infos = array();

                $path_infos[] = array($tgt_width, $tgt_height, $path.'.jpg');

                foreach ($images_types as $image_type) {

                    $tmpfile = self::get_best_path($image_type['width'], $image_type['height'], $path_infos);



                    if (ImageManager::resize(

                        $tmpfile,

                        $path.'-'.stripslashes($image_type['name']).'.jpg',

                        $image_type['width'],

                        $image_type['height'],

                        'jpg',

                        false,

                        $error,

                        $tgt_width,

                        $tgt_height,

                        5,

                        $src_width,

                        $src_height

                    )) {

                        // the last image should not be added in the candidate list if it's bigger than the original image

                        if ($tgt_width <= $src_width && $tgt_height <= $src_height) {

                            $path_infos[] = array($tgt_width, $tgt_height, $path.'-'.stripslashes($image_type['name']).'.jpg');

                        }

                        if ($entity == 'products') {

                            if (is_file(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'.jpg')) {

                                unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'.jpg');

                            }

                            if (is_file(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'_'.(int)Context::getContext()->shop->id.'.jpg')) {

                                unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_entity.'_'.(int)Context::getContext()->shop->id.'.jpg');

                            }

                        }

                    }

                    if (in_array($image_type['id_image_type'], $watermark_types)) {

                        Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));

                    }

                }

            }

        } else {

            @unlink($orig_tmpfile);

            return false;

        }

        unlink($orig_tmpfile);

        return $id_image;

    }

    protected static function get_best_path($tgt_width, $tgt_height, $path_infos)

    {

        $path_infos = array_reverse($path_infos);

        $path = '';

        foreach ($path_infos as $path_info) {

            list($width, $height, $path) = $path_info;

            if ($width >= $tgt_width && $height >= $tgt_height) {

                return $path;

            }

        }

        return $path;

    }

    public function generateAttribute($product, $tab)

    {

            if (count($tab) && Validate::isLoadedObject($product)) {

                self::setAttributesImpacts($product->id,$tab,$product->price);

                $combinations = array_values(AdminAttributeGeneratorController::createCombinations($tab));

                $values = array_values(array_map(array($this, 'addAttribute'), $combinations, 0,  0,$product));

                if ($product->depends_on_stock == 0) {

                    $attributes = Product::getProductAttributesIds($product->id, true);

                    foreach ($attributes as $attribute) {

                        StockAvailable::removeProductFromStockAvailable($product->id, $attribute['id_product_attribute'], Context::getContext()->shop);

                    }

                }



                SpecificPriceRule::disableAnyApplication();



                $product->deleteProductAttributes();

                $product->generateMultipleCombinations($values, $combinations);



                // Reset cached default attribute for the product and get a new one

                Product::getDefaultAttribute($product->id, 0, true);

                Product::updateDefaultAttribute($product->id);



                // @since 1.5.0

                if ($product->depends_on_stock == 0) {

                    $attributes = Product::getProductAttributesIds($product->id, true);

                    $quantity = (int)Tools::getValue('quantity');

                    foreach ($attributes as $attribute) {

                        if (Shop::getContext() == Shop::CONTEXT_ALL) {

                            $shops_list = Shop::getShops();

                            if (is_array($shops_list)) {

                                foreach ($shops_list as $current_shop) {

                                    if (isset($current_shop['id_shop']) && (int)$current_shop['id_shop'] > 0) {

                                        StockAvailable::setQuantity($product->id, (int)$attribute['id_product_attribute'], $quantity, (int)$current_shop['id_shop']);

                                    }

                                }

                            }

                        } else {

                            StockAvailable::setQuantity($product->id, (int)$attribute['id_product_attribute'], $quantity);

                        }

                    }

                } else {

                    StockAvailable::synchronize($product->id);

                }



                SpecificPriceRule::enableAnyApplication();

                SpecificPriceRule::applyAllRules(array((int)$product->id));

            } else {

                echo "cannot generate attribute";

            }

    }

    protected static function setAttributesImpacts($id_product, $tab,$price,$weight=0)

    {

        $attributes = array();

        foreach ($tab as $value) {

            $attributes[] = '('.(int)$id_product.', '.(int)$value.', '.(float)$price.', '.(float)$weight.')';

        }



        return Db::getInstance()->execute('

        INSERT INTO `'._DB_PREFIX_.'attribute_impact` (`id_product`, `id_attribute`, `price`, `weight`)

        VALUES '.implode(',', $attributes).'

        ON DUPLICATE KEY UPDATE `price` = VALUES(price), `weight` = VALUES(weight)');

    }

    protected function addAttribute($attributes, $price = 0, $weight = 0,$product)

    {

        foreach ($attributes as $attribute) {

            $price += (float)preg_replace('/[^0-9.-]/', '', str_replace(',', '.', Tools::getValue('price_impact_'.(int)$attribute)));

            $weight += (float)preg_replace('/[^0-9.]/', '', str_replace(',', '.', Tools::getValue('weight_impact_'.(int)$attribute)));

        }

        if ($product->id) {

            return array(

                'id_product' => (int)$product->id,

                'price' => (float)$price,

                'weight' => (float)$weight,

                'ecotax' => 0,

                'quantity' => (int)$product->quantity,

                'reference' => 'TC_'.pSQL($product->reference),

                'default_on' => 0,

                'available_date' => '0000-00-00'

            );

        }

        return array();

    }

    function getOrAddAttributeGroup(){

        $query = 'SELECT COALESCE(id_attribute_group,0) as id FROM `'._DB_PREFIX_.'attribute_group_lang` WHERE name=\'declinaison\' AND id_lang = '.Context::getContext()->language->id;

            $row = Db::getInstance()->getRow($query);

        if((int)$row['id'] > 0 )

            return (int)$row['id'];

        $newAttGroup =new AttributeGroup ();

        $newAttGroup->name[Context::getContext()->language->id] = 'declinaison';

        $newAttGroup->public_name[Context::getContext()->language->id] = 'Declinaison';

        $newAttGroup->is_color_group = 0;

        $newAttGroup->group_type = 'select';

        $newAttGroup->position = AttributeGroup::getHigherPosition() + 1;

        $newAttGroup->save();

        return (int)$newAttGroup->id;

    }

    function getOrAddAttribute($name){

        $id_attribute_group = $this->getOrAddAttributeGroup();

         $query = 'SELECT COALESCE(att.id_attribute,0) as id FROM `'._DB_PREFIX_.'attribute_lang` lang '

              .'INNER JOIN '._DB_PREFIX_.'attribute att ON att.id_attribute = lang.id_attribute '

             .' WHERE lang.name=\''.pSQL($name).'\' AND lang.id_lang = '.Context::getContext()->language->id

             .' AND att.id_attribute_group = '.$id_attribute_group;

            $row = Db::getInstance()->getRow($query);

        if((int)$row['id'] > 0 )

            return (int)$row['id'];

        $newAtt = new Attribute ();

        $newAtt->id_attribute_group = $id_attribute_group;

        $newAtt->name[Context::getContext()->language->id] = $name;

        $newAtt->save();

        return (int)$newAtt->id;

    }

    public function getDefaultCategorieId( $category_id )

    {

        $sql= 'SELECT * FROM '._DB_PREFIX_.'toncommerce_categories_mapping WHERE id_category_from_ton_commerce='.$category_id;

        $row = Db::getInstance()->getRow($sql);

        if( $row )

            return (int) $row['id_category'];        

        return Category::getRootCategory()->id;

    }

    public function getNoPhoto( $photos ){

        foreach ($photos as $photo) {

            if( $photo->principale === 'oui')

                return false;

        }

        return true;

    }

    public static function normalizeChars($s) {

        $replace = array(

            'ъ'=>'-', 'Ь'=>'-', 'Ъ'=>'-', 'ь'=>'-',

            'Ă'=>'A', 'Ą'=>'A', 'À'=>'A', 'Ã'=>'A', 'Á'=>'A', 'Æ'=>'A', 'Â'=>'A', 'Å'=>'A', 'Ä'=>'Ae',

            'Þ'=>'B',

            'Ć'=>'C', 'ץ'=>'C', 'Ç'=>'C',

            'È'=>'E', 'Ę'=>'E', 'É'=>'E', 'Ë'=>'E', 'Ê'=>'E',

            'Ğ'=>'G',

            'İ'=>'I', 'Ï'=>'I', 'Î'=>'I', 'Í'=>'I', 'Ì'=>'I',

            'Ł'=>'L',

            'Ñ'=>'N', 'Ń'=>'N',

            'Ø'=>'O', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'Oe',

            'Ş'=>'S', 'Ś'=>'S', 'Ș'=>'S', 'Š'=>'S',

            'Ț'=>'T',

            'Ù'=>'U', 'Û'=>'U', 'Ú'=>'U', 'Ü'=>'Ue',

            'Ý'=>'Y',

            'Ź'=>'Z', 'Ž'=>'Z', 'Ż'=>'Z',

            'â'=>'a', 'ǎ'=>'a', 'ą'=>'a', 'á'=>'a', 'ă'=>'a', 'ã'=>'a', 'Ǎ'=>'a', 'а'=>'a', 'А'=>'a', 'å'=>'a', 'à'=>'a', 'א'=>'a', 'Ǻ'=>'a', 'Ā'=>'a', 'ǻ'=>'a', 'ā'=>'a', 'ä'=>'ae', 'æ'=>'ae', 'Ǽ'=>'ae', 'ǽ'=>'ae',

            'б'=>'b', 'ב'=>'b', 'Б'=>'b', 'þ'=>'b',

            'ĉ'=>'c', 'Ĉ'=>'c', 'Ċ'=>'c', 'ć'=>'c', 'ç'=>'c', 'ц'=>'c', 'צ'=>'c', 'ċ'=>'c', 'Ц'=>'c', 'Č'=>'c', 'č'=>'c', 'Ч'=>'ch', 'ч'=>'ch',

            'ד'=>'d', 'ď'=>'d', 'Đ'=>'d', 'Ď'=>'d', 'đ'=>'d', 'д'=>'d', 'Д'=>'D', 'ð'=>'d',

            'є'=>'e', 'ע'=>'e', 'е'=>'e', 'Е'=>'e', 'Ə'=>'e', 'ę'=>'e', 'ĕ'=>'e', 'ē'=>'e', 'Ē'=>'e', 'Ė'=>'e', 'ė'=>'e', 'ě'=>'e', 'Ě'=>'e', 'Є'=>'e', 'Ĕ'=>'e', 'ê'=>'e', 'ə'=>'e', 'è'=>'e', 'ë'=>'e', 'é'=>'e',

            'ф'=>'f', 'ƒ'=>'f', 'Ф'=>'f',

            'ġ'=>'g', 'Ģ'=>'g', 'Ġ'=>'g', 'Ĝ'=>'g', 'Г'=>'g', 'г'=>'g', 'ĝ'=>'g', 'ğ'=>'g', 'ג'=>'g', 'Ґ'=>'g', 'ґ'=>'g', 'ģ'=>'g',

            'ח'=>'h', 'ħ'=>'h', 'Х'=>'h', 'Ħ'=>'h', 'Ĥ'=>'h', 'ĥ'=>'h', 'х'=>'h', 'ה'=>'h',

            'î'=>'i', 'ï'=>'i', 'í'=>'i', 'ì'=>'i', 'į'=>'i', 'ĭ'=>'i', 'ı'=>'i', 'Ĭ'=>'i', 'И'=>'i', 'ĩ'=>'i', 'ǐ'=>'i', 'Ĩ'=>'i', 'Ǐ'=>'i', 'и'=>'i', 'Į'=>'i', 'י'=>'i', 'Ї'=>'i', 'Ī'=>'i', 'І'=>'i', 'ї'=>'i', 'і'=>'i', 'ī'=>'i', 'ĳ'=>'ij', 'Ĳ'=>'ij',

            'й'=>'j', 'Й'=>'j', 'Ĵ'=>'j', 'ĵ'=>'j', 'я'=>'ja', 'Я'=>'ja', 'Э'=>'je', 'э'=>'je', 'ё'=>'jo', 'Ё'=>'jo', 'ю'=>'ju', 'Ю'=>'ju',

            'ĸ'=>'k', 'כ'=>'k', 'Ķ'=>'k', 'К'=>'k', 'к'=>'k', 'ķ'=>'k', 'ך'=>'k',

            'Ŀ'=>'l', 'ŀ'=>'l', 'Л'=>'l', 'ł'=>'l', 'ļ'=>'l', 'ĺ'=>'l', 'Ĺ'=>'l', 'Ļ'=>'l', 'л'=>'l', 'Ľ'=>'l', 'ľ'=>'l', 'ל'=>'l',

            'מ'=>'m', 'М'=>'m', 'ם'=>'m', 'м'=>'m',

            'ñ'=>'n', 'н'=>'n', 'Ņ'=>'n', 'ן'=>'n', 'ŋ'=>'n', 'נ'=>'n', 'Н'=>'n', 'ń'=>'n', 'Ŋ'=>'n', 'ņ'=>'n', 'ŉ'=>'n', 'Ň'=>'n', 'ň'=>'n',

            'о'=>'o', 'О'=>'o', 'ő'=>'o', 'õ'=>'o', 'ô'=>'o', 'Ő'=>'o', 'ŏ'=>'o', 'Ŏ'=>'o', 'Ō'=>'o', 'ō'=>'o', 'ø'=>'o', 'ǿ'=>'o', 'ǒ'=>'o', 'ò'=>'o', 'Ǿ'=>'o', 'Ǒ'=>'o', 'ơ'=>'o', 'ó'=>'o', 'Ơ'=>'o', 'œ'=>'oe', 'Œ'=>'oe', 'ö'=>'oe',

            'פ'=>'p', 'ף'=>'p', 'п'=>'p', 'П'=>'p',

            'ק'=>'q',

            'ŕ'=>'r', 'ř'=>'r', 'Ř'=>'r', 'ŗ'=>'r', 'Ŗ'=>'r', 'ר'=>'r', 'Ŕ'=>'r', 'Р'=>'r', 'р'=>'r',

            'ș'=>'s', 'с'=>'s', 'Ŝ'=>'s', 'š'=>'s', 'ś'=>'s', 'ס'=>'s', 'ş'=>'s', 'С'=>'s', 'ŝ'=>'s', 'Щ'=>'sch', 'щ'=>'sch', 'ш'=>'sh', 'Ш'=>'sh', 'ß'=>'ss',

            'т'=>'t', 'ט'=>'t', 'ŧ'=>'t', 'ת'=>'t', 'ť'=>'t', 'ţ'=>'t', 'Ţ'=>'t', 'Т'=>'t', 'ț'=>'t', 'Ŧ'=>'t', 'Ť'=>'t', '™'=>'tm',

            'ū'=>'u', 'у'=>'u', 'Ũ'=>'u', 'ũ'=>'u', 'Ư'=>'u', 'ư'=>'u', 'Ū'=>'u', 'Ǔ'=>'u', 'ų'=>'u', 'Ų'=>'u', 'ŭ'=>'u', 'Ŭ'=>'u', 'Ů'=>'u', 'ů'=>'u', 'ű'=>'u', 'Ű'=>'u', 'Ǖ'=>'u', 'ǔ'=>'u', 'Ǜ'=>'u', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'У'=>'u', 'ǚ'=>'u', 'ǜ'=>'u', 'Ǚ'=>'u', 'Ǘ'=>'u', 'ǖ'=>'u', 'ǘ'=>'u', 'ü'=>'ue',

            'в'=>'v', 'ו'=>'v', 'В'=>'v',

            'ש'=>'w', 'ŵ'=>'w', 'Ŵ'=>'w',

            'ы'=>'y', 'ŷ'=>'y', 'ý'=>'y', 'ÿ'=>'y', 'Ÿ'=>'y', 'Ŷ'=>'y',

            'Ы'=>'y', 'ž'=>'z', 'З'=>'z', 'з'=>'z', 'ź'=>'z', 'ז'=>'z', 'ż'=>'z', 'ſ'=>'z', 'Ж'=>'zh', 'ж'=>'zh'
            ,'*'=>'', '>' => '&gt; ', '<' => '&lt;'

        );

        return strtr($s, $replace);

    }

    public function addFeature($product,$carat){

            if(count($carat)!=2 && !is_array($carat))

                return;

            $idFeatureValue = false;

            $input =  trim(self::normalizeChars($carat[0]));

            $value = strlen(trim($carat[1])) > 250 ? self::normalizeChars(trim(substr($carat[1],0,250))) : self::normalizeChars(trim($carat[1]));

            if(trim($value)=="")

                return;

            $query = "SELECT * FROM `"._DB_PREFIX_."feature_lang` WHERE name ='".pSQL( $input )."'";

            $feature_row = Db::getInstance()->getRow( $query );

            if($feature_row){

                $query = "SELECT * FROM `"._DB_PREFIX_."feature_value_lang` WHERE value ='".pSQL( $value )."'";

                $feature_value = Db::getInstance()->getRow( $query );

                if($feature_value){

                    //$product->addFeaturesToDB($feature_row['id_feature'],$feature_value['id_feature_value'],null,1);

                    $idFeatureValue = (int)$feature_value['id_feature_value'];

                     Product::addFeatureProductImport($product->id,(int)$feature_row['id_feature'],(int)$feature_value['id_feature_value']);

                }else{

                    $idFeatureValue = FeatureValue::addFeatureValueImport((int)$feature_row['id_feature'], $value, $product->id,1);

                    Product::addFeatureProductImport($product->id,(int)$feature_row['id_feature'],(int)$idFeatureValue);

                }



            }else{

                $feature_id = Feature::addFeatureImport( $input );

                $idFeatureValue = FeatureValue::addFeatureValueImport($feature_id, $value, $product->id,1);

            }

    }

    public function importCGV( )

    {

        $curl = new Curl();

        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));

        $curl->setopt(CURLOPT_RETURNTRANSFER,true);

        $curl->get( self::API_BASE_URL.'/cgv');

        $response = Tools::jsonDecode($curl->response);

        if($response->statut === "ok" ){

            $id_cms = Configuration::get('PS_CONDITIONS_CMS_ID',0);

            $id_lang = Context::getContext()->language->id;

            $cms = new CMS($id_cms,$id_lang);

            $content = $response->cgv;

            $content = str_replace('[titre]','<h1 class="page-heading">',$content);

            $content = str_replace('[/titre]','</h1>',$content);

            $content = str_replace('[texte]','<p class="bottom-indent">',$content);

            $content = str_replace('[/texte]','</p>',$content);

            $content = str_replace('[b]','<b>',$content);

            $content = str_replace('[/b]','</b>',$content);

            $content = str_replace('[i]','<i>',$content);

            $content = str_replace('[/i]','</i>',$content);

            $content = str_replace('[u]','<u>',$content);

            $content = str_replace('[/u]','</u>',$content);

            $content = str_replace('[s]','<s>',$content);

            $content = str_replace('[/s]','</s>',$content);

            $domaine  = Configuration::get('PS_SHOP_DOMAIN','');

            $nom_site  = Configuration::get('PS_SHOP_NAME','');

            $content = str_replace('[domaine]',$domaine,$content);

            $content = str_replace('[nom_site]',$nom_site,$content);

            $cms->content = $content;

            $cms->save();

        }

        

    }

    public function getMentionLegalId(){

        $query = 'SELECT *  FROM `'._DB_PREFIX_.'cms_lang` WHERE meta_title LIKE \'%mention%\'';

        $row = Db::getInstance()->getRow($query);

        if($row)

            return (int)$row['id_cms'];

        return 0;

    }

    public function importMentionLegale()

    {

        $this->getContact();

        $curl = new Curl();

        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));

        $curl->setopt(CURLOPT_RETURNTRANSFER,true);

        $curl->get( self::API_BASE_URL.'/mentionslegales');

        $response = Tools::jsonDecode($curl->response);

        if($response->statut === "ok" ){

            $id_cms = $this->getMentionLegalId();

            $id_lang = Context::getContext()->language->id;

            $cms = new CMS($id_cms,$id_lang);

            $content = $response->mentionslegales;

            $content = str_replace('[titre]','<h1 class="page-heading">',$content);

            $content = str_replace('[/titre]','</h1>',$content);

            $content = str_replace('[texte]','<p class="bottom-indent">',$content);

            $content = str_replace('[/texte]','</p>',$content);

            $content = str_replace('[b]','<b>',$content);

            $content = str_replace('[/b]','</b>',$content);

            $content = str_replace('[i]','<i>',$content);

            $content = str_replace('[/i]','</i>',$content);

            $content = str_replace('[u]','<u>',$content);

            $content = str_replace('[/u]','</u>',$content);

            $content = str_replace('[s]','<s>',$content);

            $content = str_replace('[/s]','</s>',$content);

            $domaine  = Configuration::get('PS_SHOP_DOMAIN','');

            $nom_site  = Configuration::get('PS_SHOP_NAME','');

            $content = str_replace('[domaine]',$domaine,$content);

            $content = str_replace('[nom_site]',$nom_site,$content);

            $cms->content = $content;

            $cms->save();

        }

    }

    public function isFirstRunning()
    {

         
         if( $this->getNbreProductAdded() > 0 )
            return false;

        return true;

    }
    public function getNbreProductAdded()
    {
        $query = "SELECT  COUNT(*) as nbre FROM `"._DB_PREFIX_."toncommerce_product_mapping`";
         $row = Db::getInstance()->getRow($query);
         return (int)$row['nbre'];

    }
    public function deleteProduct($ref){

        $query = "SELECT * FROM `"._DB_PREFIX_."toncommerce_product_mapping` WHERE reference ='".$ref."'";
        $product = Db::getInstance()->getRow($query);
        if($product){
            $product = new Product($product['id_product']);
            $product->active = 0;
            $product->save();
        }
        echo 'DELETE FROM '._DB_PREFIX_."toncommerce_product_mapping WHERE  reference ='".$ref."'";
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_."toncommerce_product_mapping WHERE  reference ='".$ref."'" );

    }
    public function getAllReferenceFromPrestashop(){
        $reference = array();
        $query = "SELECT  DISTINCT reference FROM `"._DB_PREFIX_."toncommerce_product_mapping`";
         $rows = Db::getInstance()->executeS($query);
         foreach ($rows as $row) {
             $reference[] = $row['reference'];
         }

         return $reference;

    }
    public function getCategorieByRef($ref,$array){
        foreach ($array as $key => $value) {
            if(in_array($ref, $value))
                return $key;
        }

        return false;
    }
    public function getAllReferenceProduct()
    {
        $reference = array();
        $reference['all'] = array();
        $reference['products'] = array();
        $categories = $this->getCategories();
        $categories_to_check = $this->getCategoriestoImport();
        $id_lang = Context::getContext()->language->id;
        foreach ($categories as $categorie ) {
            if(!in_array($categorie->id, $categories_to_check))
             continue;
            $products = $this->getCategoryProducts($categorie->id);
            foreach ($products as $prod) {
                $reference['all'][] = $prod->reference;
                $reference['products'][$categorie->id][] = $prod->reference;
            }
        }
        return $reference;
    }
    public function insertProduct($ref,$cat_id)
    {

        $query = "SELECT * from "._DB_PREFIX_."toncommerce_product_mapping where reference = '$ref' ";
        $row = Db::getInstance()->getRow($query);
        if($row)
            return;

        $id_lang = Context::getContext()->language->id;
        $article = $this->getProduct($ref);               
        $product = new Product();
        $product->name[$id_lang] = $article->designation;
        $product->reference = 'TC_'.$article->reference;
        $description = str_replace('[b]', '<b>', $article->description);
        $description = str_replace('\n\r', '<br />', $description);
        $description = str_replace('[/b]', '</b>', $description);
        $product->description[$id_lang] = $description;
        $resume = str_replace('[b]', '<b>', $article->resume);
        $resume = str_replace('[/b]', '</b>', $resume);
        $product->description_short[$id_lang] = $resume;
        $product->link_rewrite[$id_lang] = $article->var_url;
        $product->short_description[$id_lang] = $resume;
        $product->price =(float) $article->prix_ht;
        $product->wholesale_price = $this->calculPrice((float)$article->prix_ht,(float)$article->com_ht);
        $product->active = false;
        $id_category = $this->getDefaultCategorieId( $cat_id );
        $product->save();
        
        $carateristiques = explode("\r\n",$article->caracteristiques);
        echo $article->caracteristiques;
        $count = 0;
            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'feature_product WHERE  id_product = '.(int)$product->id );
        foreach ($carateristiques as $carateristique) {
            $carat = explode(":", trim($carateristique));
            var_dump($carat);
            if(count($carat) == 2 ){
                $this->addFeature($product,$carat);
            }else{
                if(strlen($carat[0]) > 0)
            $this->addFeature($product,array('autre carateristique',$carat[0]));

            }

        }
        $i = 0;
        $no_photo = $this->getNoPhoto($article->photos);
        foreach ($article->photos as $photo) {
            $image = new Image();
            $image->id_product = $product->id;
            $image->position = $i;
            if( $photo->principale === 'oui')

                $image->cover = true;

            if($no_photo && $i== 0)

                $image->cover = true;

            $image->save();

            $this->copyImg($product->id, $image->id,$photo->url_600x600);

            Db::getInstance()->insert('toncommerce_image_mapping',array('id_image'=>$image->id,'id_image_website'=>$photo->id));

            $i++;

        }

        $attributes = $this->getAttributeCombinaison($article->declinaisons);

        if( $attributes !== true){

            if(!$product->productAttributeExists($attributes))

            {

                $i= 0;

                foreach ($article->declinaisons as $declinaison) {

                    $attributes = array();

                    $attributes[] = $this->getOrAddAttribute($declinaison->nom);

                    $idProductAttribute = $product->addProductAttribute(0, 0, 0, 0, 0, "", $article->reference,

            null, $declinaison->code_ean, false, null,  null, 1, 'toncommerce');

                    $attributes = array_unique($attributes);

                    $product->addAttributeCombinaison($idProductAttribute, $attributes);



                StockAvailable::setQuantity($product->id, $idProductAttribute, $this->getQuantityWithDeclinaison($article->reference,$declinaison->id));

                 Db::getInstance()->insert('toncommerce_product_attribute_mapping',array('id_product'=>$product->id,'id_attribute'=>$idProductAttribute,'id_declinaison' => $declinaison->id));

                 if($i==0){

                    $product->setDefaultAttribute($idProductAttribute);

                 }



                 $i++;

                };

               

            }

        }else{

             StockAvailable::setQuantity($product->id, 0, $this->getQuantityWithoutDeclinaison($article->reference));

        }
        $query = "DELETE FROM "._DB_PREFIX_."category_product where id_product = '".$product->id."' AND  id_category='".$id_category."'";
        Db::getInstance()->execute($query);
        $product->addToCategories(array($id_category));
        $product->id_category_default = $id_category;
        $product->save();

        Db::getInstance()->update('product_shop',array('wholesale_price'=>$product->wholesale_price),'id_product = '.$product->id);
        Db::getInstance()->insert('toncommerce_product_mapping',array('reference'=>$article->reference,'id_product'=>$product->id,'commission' => (float)$article->com_ht,'date_add'=>date("Y-m-d H:i:s"),'date_upt'=>date("Y-m-d H:i:s")));

    }
    public function updateStock()
    {
        $curl = new Curl();

        $curl->setHeader("Authorization",Configuration::get('TONCOMMERCE_API_KEY', ''));

        $curl->setopt(CURLOPT_RETURNTRANSFER,true);

        $curl->get( self::API_BASE_URL.'stock_reference');

        $liste_produits_stock = Tools::jsonDecode($curl->response);

        if($liste_produits_stock->statut === "ok" ){
            Db::getInstance()->execute('TRUNCATE '._DB_PREFIX_."toncommerce_stock" );
            foreach ($liste_produits_stock->stock_reference as $value)
            {
                Db::getInstance()->insert('toncommerce_stock',array('reference_declinaison'=>$value->reference_declinaison,'stock'=>$value->stock));
            }
            echo $query = "SELECT reference, id_product, id_product_attribute FROM "._DB_PREFIX_."product_attribute";
            $declinaisons = Db::getInstance()->executeS($query);
            foreach ($declinaisons as $declinaison) 
            {
            echo  $query = "SELECT stock FROM "._DB_PREFIX_."toncommerce_stock where reference_declinaison = '".$declinaison['reference']."'";
             $stock_declinaison = Db::getInstance()->getRow($query);
             $bdd=null;
             
             // Si une declinaison de produit TC a la meme reference que Prestashop alors on met a jour
             if($stock_declinaison)
             {
             // id_product , id_product_attribute , quantity , id_shop: null
              StockAvailable::setQuantity((int)$declinaison->id_product,(int)$declinaison->id_product_attribute,(int)$stock_declinaison['stock'],null);
             }
            }
            $query = "SELECT reference, id_product FROM "._DB_PREFIX_."product";
            $produits = Db::getInstance()->executeS($query);
            foreach ($produits as $produit) 
            {
             $stock_produit='';
             $query = "SELECT sum(stock) as stock_total FROM "._DB_PREFIX_."toncommerce_stock where reference_declinaison LIKE '".$produit['reference']."%'";
             $stock_produit=Db::getInstance()->getRow($query);
            
                  if($stock_produit)
                 {

                    StockAvailable::setQuantity((int)$produit['id_product'],0,(int)$stock_produit['stock_total'],null);
                 }

            }
        }
    }

    public function updateProduct(){
        $start_time = microtime(TRUE);
        
        $id_lang = Context::getContext()->language->id;
        echo "get all product from PS<br />";
        $query = "SELECT SQL_CACHE id_product, reference,id_toncommerce_product from "._DB_PREFIX_."toncommerce_product_mapping ORDER BY date_upt DESC";
        $results =  Db::getInstance()->executeS($query);
        $end_time = microtime(TRUE);
        $bechmark = $end_time - $start_time;
        echo "finish all get Product $bechmark <br />";
        echo 'get Stock reference <br />';
        $stock_reference = $this->getStockReference();
        $end_time = microtime(TRUE);
        $bechmark = $end_time - $start_time;
        echo "get Stock reference $bechmark <br />";
        echo 'get price reference <br />';
        $prix_reference = $this->getPrixReference();
        $end_time = microtime(TRUE);
        $bechmark = $end_time - $start_time;
        echo "price referenc $bechmark <br />";
        echo "begin treat : <br />";
        $final = array_map(function($product) use ($prix_reference,$stock_reference){
            $data = $product;
            if(isset($prix_reference[$data['reference']])){
                $data['prix_ht'] = $prix_reference[$data['reference']]->prix_ht;
                $data['com_ht'] = $prix_reference[$data['reference']]->com_ht;
            }
            if(isset($stock_reference[$data['reference']])){
                $data['declinaisons'] = $stock_reference[$data['reference']];
            }
            return $data;
        }, $results);
        foreach ($final as $data) {   
        echo "start treat, $data[reference]<br />";      
            if(count($data['declinaisons']) == 1){
                StockAvailable::setQuantity($data['id_product'], 0, $data['declinaisons'][0]->stock);
            }else{
                foreach ( $data['declinaisons'] as $declinaison) {
                    $query = "SELECT * FROM `"._DB_PREFIX_."toncommerce_product_attribute_mapping` WHERE id_product ='".$data['id_product']."' AND id_declinaison ='".$declinaison->id."'";
                     $product_attribute = Db::getInstance()->getRow($query);
                     StockAvailable::setQuantity($data['id_product'], $product_attribute['id_attribute'], $declinaison->stock);
                }

            }
            $produit = new Product($data['id_product']);
            $produit->wholesale_price = $this->calculPrice((float)$data['prix_ht'],(float)$data['com_ht']);
            Db::getInstance()->update('product_shop',array('wholesale_price'=>$produit->wholesale_price),'id_product = '.$data['id_product']);
            
            if($produit->price < ((float) $data['prix_ht'] - (float)$data['com_ht']) || $produit->price > ((float) $data['prix_ht'] + (float)$data['com_ht'])){
                $produit->price = (float) $data['prix_ht'];

            }
            $produit->save();
            Db::getInstance()->update('toncommerce_product_mapping',array("date_upt"=>date('Y-m-d h:m:s')),"id_toncommerce_product = $data[id_toncommerce_product]");
            $end_time = microtime(TRUE);
            $bechmark = $end_time - $start_time;
            echo "finish $data[reference] $bechmark <br />";
        }

    }


}


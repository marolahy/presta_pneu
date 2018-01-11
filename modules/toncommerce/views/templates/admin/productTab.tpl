<div class="row">

                  <div class="col-md-12">
                    <h2>Prix
                      <span class="help-box" data-toggle="popover" data-content="C'est le prix auquel vous comptez vendre votre produit à vos clients. Le prix TTC sera ajusté en fonction de la règle de taxe appliquée." data-original-title="" title=""></span>
                    </h2>
                  </div>

                  <div class="col-md-12 form-group">
                    <div class="row">

                      <div class="col-xl-2 col-lg-3">
                        <label class="form-control-label">Montant HT</label>
                        
                        <div class="input-group money-type">
                            <span class="input-group-addon">€ </span>
        <input type="text" id="form_step2_price" name="form[step2][price]" data-display-price-precision="6" class="form-control" value="16.470000">    </div>
                      </div>
                      <div class="col-xl-2 col-lg-3">
                        <label class="form-control-label">Montant TTC</label>
                        
                        <div class="input-group money-type">
                            <span class="input-group-addon">€ </span>
        <input type="text" id="form_step2_price_ttc" name="form[step2][price_ttc]" class="form-control">    </div>
                      </div>

                      <div class="col-xl-4 col-lg-6 col-xl-offset-1 col-lg-offset-0">
                        <label class="form-control-label">
                          Prix unitaire (HT)
                          <span class="help-box" data-toggle="popover" data-content="Certains produits produits peuvent se vendre par unité (par litre, par kilo, etc.) et il s'agit ici du prix pour une unité. Par exemple, si vous vendez du tissu, vous devez indiquer le prix au mètre." data-original-title="" title=""></span>
                        </label>
                        <div class="row">
                          <div class="col-md-6">
                            
                            <div class="input-group money-type">
                            <span class="input-group-addon">€ </span>
        <input type="text" id="form_step2_unit_price" name="form[step2][unit_price]" class="form-control" value="0.000000">    </div>
                          </div>
                          <div class="col-md-6">
                            
                            <input type="text" id="form_step2_unity" name="form[step2][unity]" placeholder="Par kilo, par litre" class="form-control">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2 col-md-offset-1 hide">
                        <label class="form-control-label">Éco-participation (TTC)</label>
                        
                        <div class="input-group money-type">
                            <span class="input-group-addon">€ </span>
        <input type="text" id="form_step2_ecotax" name="form[step2][ecotax]" data-eco-tax-rate="0" class="form-control" value="0.000000">    </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="row form-group">
                      <div class="col-md-4">
                        <label class="form-control-label">Règle de taxe</label>
                        
                        <select id="form_step2_id_tax_rules_group" name="form[step2][id_tax_rules_group]" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true">            <option value="0" data-rates="0" data-computation-method="0">Aucune taxe</option>            <option value="5" data-rates="20" data-computation-method="0">EU VAT For Virtual Products</option>            <option value="2" data-rates="10" data-computation-method="0">FR Taux réduit (10%)</option>            <option value="3" data-rates="5.5" data-computation-method="0">FR Taux réduit (5.5%)</option>            <option value="1" data-rates="20" data-computation-method="0" selected="selected">FR Taux standard (20%)</option>            <option value="4" data-rates="2.1" data-computation-method="0">FR Taux super réduit (2.1%)</option></select><span class="select2 select2-container select2-container--prestakit" dir="ltr" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-form_step2_id_tax_rules_group-container"><span class="select2-selection__rendered" id="select2-form_step2_id_tax_rules_group-container" title="FR Taux standard (20%)">FR Taux standard (20%)</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                      </div>
                      <div class="col-md-8">
                        <label class="form-control-label">&nbsp;</label>
                        <a class="form-control-static external-link" href="http://localhost/toncom/admin017sq9cc4/index.php?controller=AdminTaxes&amp;token=e5d7d4cc104b89ad64648877224fccae">
                          <i class="material-icons">open_in_new</i> Gérer les règles de taxe
                        </a>
                      </div>
                      <div class="col-md-12 p-t-1">
                        <div class="checkbox">                                        <label><input type="checkbox" id="form_step2_on_sale" name="form[step2][on_sale]" value="1">
Afficher un bandeau "Promo !" sur la fiche produit et sur les listes de produits.</label>
    </div>
                      </div>
                      <div class="col-md-12">
                        <div class="row">
                          <div class="col-xl-5 col-lg-12">
                            <div class="alert alert-info" role="alert">
                              <i class="material-icons">help</i>
                              <p class="alert-text">
                                Prix de vente final : <strong><span id="final_retail_price_ti">19,76&nbsp;€</span> TTC</strong> / <span id="final_retail_price_te">16,47&nbsp;€</span> HT
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-12">
                        <h2>
                          Prix d'achat
                          <span class="help-box" data-toggle="popover" data-content="Le prix d'achat est prix que vous avez payé pour vous procurer le produit. N'incluez pas la taxe. Le prix d'achat doit être inférieur à votre prix de vente : la différence entre les deux constitue votre marge." data-original-title="" title=""></span>
                        </h2>
                      </div>
                      <div class="col-xl-2 col-lg-3 form-group">
                        <label class="form-control-label">Montant HT</label>
                        
                        <div class="input-group money-type">
                            <span class="input-group-addon">€ </span>
        <input type="text" id="form_step2_wholesale_price" name="form[step2][wholesale_price]" class="form-control" value="0.000000">    </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-12">
                        <h2>
                          Prix spécifiques
                          <span class="help-box" data-toggle="popover" data-content="Vous pouvez définir des prix spécifiques pour des clients appartenant à différents groupes, différents pays, etc." data-original-title="" title=""></span>
                        </h2>
                      </div>
                      <div class="col-md-12">
                        <div id="specific-price" class="m-b-2">
                          <a class="btn btn-action m-b-2" data-toggle="collapse" href="#specific_price_form" aria-expanded="false">
                            <i class="material-icons">add_circle</i>
                            Ajouter un prix spécifique
                          </a>
                          <table id="js-specific-price-list" class="table table-striped hide seo-table" data="/toncom/admin017sq9cc4/index.php/specific-price/list/1?_token=COadL-ivnZDl1C5pHTryRD-iQ3ieJXkiIY7mH9Vd60E" data-action-delete="/toncom/admin017sq9cc4/index.php/specific-price/delete/1?_token=COadL-ivnZDl1C5pHTryRD-iQ3ieJXkiIY7mH9Vd60E">
                            <thead>
                            <tr>
                              <th>Règle</th>
                              <th>Déclinaison</th>
                              <th>Devise</th>
                              <th>Pays</th>
                              <th>Groupe</th>
                              <th>Client</th>
                              <th>Prix fixé</th>
                              <th>Impact</th>
                              <th>Période</th>
                              <th>Du</th>
                              <th></th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                          </table>
                        </div>
                        <div class="collapse" id="specific_price_form" data-action="/toncom/admin017sq9cc4/index.php/specific-price/add?_token=COadL-ivnZDl1C5pHTryRD-iQ3ieJXkiIY7mH9Vd60E">
                          <div class="card card-block">
  <h4><b>Conditions des prix spécifiques</b></h4>
  

        <input type="hidden" id="form_step2_specific_price_sp_id_shop" name="form[step2][specific_price][sp_id_shop]" class="form-control" value="1">
  
  <div class="row">
    <div class="col-md-3">
      <fieldset class="form-group">
        <label>Pour</label>
        
        <select id="form_step2_specific_price_sp_id_currency" name="form[step2][specific_price][sp_id_currency]" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true"><option value="">Toutes les devises</option>            <option value="1">Euro</option></select><span class="select2 select2-container select2-container--prestakit" dir="ltr" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-form_step2_specific_price_sp_id_currency-container"><span class="select2-selection__rendered" id="select2-form_step2_specific_price_sp_id_currency-container" title="Toutes les devises">Toutes les devises</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
      </fieldset>
    </div>
    <div class="col-md-3">
      <fieldset class="form-group">
        <label>&nbsp;</label>
        
        <select id="form_step2_specific_price_sp_id_country" name="form[step2][specific_price][sp_id_country]" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true"><option value="">Tous les pays</option>            <option value="231">Afghanistan</option>            <option value="30">Afrique du Sud</option>            <option value="244">Åland, Îles</option>            <option value="230">Albanie</option>            <option value="38">Algérie</option>            <option value="1">Allemagne</option>            <option value="40">Andorre</option>            <option value="41">Angola</option>            <option value="42">Anguilla</option>            <option value="232">Antarctique</option>            <option value="43">Antigua et Barbuda</option>            <option value="157">Antilles Néerlandaises</option>            <option value="188">Arabie Saoudite</option>            <option value="44">Argentine</option>            <option value="45">Arménie</option>            <option value="46">Aruba</option>            <option value="24">Australie</option>            <option value="2">Autriche</option>            <option value="47">Azerbaïdjan</option>            <option value="48">Bahamas</option>            <option value="49">Bahreïn</option>            <option value="50">Bangladesh</option>            <option value="51">Barbade</option>            <option value="52">Bélarus</option>            <option value="3">Belgique</option>            <option value="53">Belize</option>            <option value="54">Bénin</option>            <option value="55">Bermudes</option>            <option value="56">Bhoutan</option>            <option value="34">Bolivie</option>            <option value="233">Bosnie-Herzégovine</option>            <option value="57">Botswana</option>            <option value="234">Bouvet, Île</option>            <option value="58">Brésil</option>            <option value="59">Brunéi Darussalam</option>            <option value="236">Bulgarie</option>            <option value="60">Burkina Faso</option>            <option value="61">Burma (Myanmar)</option>            <option value="62">Burundi</option>            <option value="237">Caïmans, Îles</option>            <option value="63">Cambodge</option>            <option value="64">Cameroun</option>            <option value="4">Canada</option>            <option value="65">Cap-Vert</option>            <option value="66">Centrafricaine, République</option>            <option value="68">Chili</option>            <option value="5">Chine</option>            <option value="238">Christmas, Île</option>            <option value="76">Chypre</option>            <option value="239">Cocos (Keeling), Îles</option>            <option value="69">Colombie</option>            <option value="70">Comores</option>            <option value="72">Congo, Rép.</option>            <option value="71">Congo, Rép. Dém.</option>            <option value="240">Cook, Îles</option>            <option value="28">Corée du Sud</option>            <option value="121">Corée, Rép. Populaire Dém. de</option>            <option value="73">Costa Rica</option>            <option value="32">Côte d'Ivoire</option>            <option value="74">Croatie</option>            <option value="75">Cuba</option>            <option value="20">Danemark</option>            <option value="77">Djibouti</option>            <option value="78">Dominica</option>            <option value="82">Égypte</option>            <option value="83">El Salvador</option>            <option value="217">Émirats Arabes Unis</option>            <option value="81">Équateur</option>            <option value="85">Érythrée</option>            <option value="6">Espagne</option>            <option value="86">Estonie</option>            <option value="21">États-Unis</option>            <option value="87">Éthiopie</option>            <option value="88">Falkland, Îles</option>            <option value="89">Féroé, Îles</option>            <option value="90">Fidji</option>            <option value="7">Finlande</option>            <option value="8">France</option>            <option value="91">Gabon</option>            <option value="92">Gambie</option>            <option value="93">Géorgie</option>            <option value="196">Géorgie du Sud et les Îles Sandwich du Sud</option>            <option value="94">Ghana</option>            <option value="97">Gibraltar</option>            <option value="9">Grèce</option>            <option value="95">Grenade</option>            <option value="96">Groenland</option>            <option value="98">Guadeloupe</option>            <option value="99">Guam</option>            <option value="100">Guatemala</option>            <option value="101">Guernesey</option>            <option value="102">Guinée</option>            <option value="84">Guinée Équatoriale</option>            <option value="103">Guinée-Bissau</option>            <option value="104">Guyana</option>            <option value="241">Guyane Française</option>            <option value="105">Haîti</option>            <option value="106">Heard, Île et Mcdonald, Îles</option>            <option value="108">Honduras</option>            <option value="22">Hong-Kong</option>            <option value="143">Hongrie</option>            <option value="35">Ile Maurice</option>            <option value="223">Îles Vierges Britanniques</option>            <option value="224">Îles Vierges des États-Unis</option>            <option value="110">Inde</option>            <option value="111">Indonésie</option>            <option value="112">Iran</option>            <option value="113">Iraq</option>            <option value="26">Irlande</option>            <option value="109">Islande</option>            <option value="29">Israël</option>            <option value="10">Italie</option>            <option value="115">Jamaique</option>            <option value="11">Japon</option>            <option value="116">Jersey</option>            <option value="117">Jordanie</option>            <option value="118">Kazakhstan</option>            <option value="119">Kenya</option>            <option value="123">Kirghizistan</option>            <option value="120">Kiribati</option>            <option value="122">Koweït</option>            <option value="124">Laos</option>            <option value="127">Lesotho</option>            <option value="125">Lettonie</option>            <option value="126">Liban</option>            <option value="128">Libéria</option>            <option value="129">Libyenne, Jamahiriya Arabe</option>            <option value="130">Liechtenstein</option>            <option value="131">Lituanie</option>            <option value="12">Luxembourg</option>            <option value="132">Macao</option>            <option value="133">Macédoine</option>            <option value="134">Madagascar</option>            <option value="136">Malaisie</option>            <option value="135">Malawi</option>            <option value="137">Maldives</option>            <option value="138">Mali</option>            <option value="139">Malte</option>            <option value="114">Man, Île de</option>            <option value="163">Mariannes du Nord, Îles</option>            <option value="152">Maroc</option>            <option value="140">Marshall, Îles</option>            <option value="141">Martinique</option>            <option value="142">Mauritanie</option>            <option value="144">Mayotte</option>            <option value="145">Mexique</option>            <option value="146">Micronésie</option>            <option value="147">Moldova</option>            <option value="148">Monaco</option>            <option value="149">Mongolie</option>            <option value="150">Monténégro</option>            <option value="151">Montserrat</option>            <option value="153">Mozambique</option>            <option value="154">Namibie</option>            <option value="155">Nauru</option>            <option value="156">Népal</option>            <option value="159">Nicaragua</option>            <option value="160">Niger</option>            <option value="31">Nigeria</option>            <option value="161">Niué</option>            <option value="162">Norfolk, Île</option>            <option value="23">Norvège</option>            <option value="158">Nouvelle-Calédonie</option>            <option value="27">Nouvelle-Zélande</option>            <option value="235">Océan Indien, Territoire Britannique de L'</option>            <option value="164">Oman</option>            <option value="215">Ouganda</option>            <option value="219">Ouzbékistan</option>            <option value="165">Pakistan</option>            <option value="166">Palaos</option>            <option value="167">Palestinien Occupé, Territoire</option>            <option value="168">Panama</option>            <option value="169">Papouasie-Nouvelle-Guinée</option>            <option value="170">Paraguay</option>            <option value="13">Pays-bas</option>            <option value="171">Pérou</option>            <option value="172">Philippines</option>            <option value="173">Pitcairn</option>            <option value="14">Pologne</option>            <option value="242">Polynésie Française</option>            <option value="174">Porto Rico</option>            <option value="15">Portugal</option>            <option value="175">Qatar</option>            <option value="79">République Dominicaine</option>            <option value="16">République Tchèque</option>            <option value="176">Réunion, Île de la</option>            <option value="36">Roumanie</option>            <option value="17">Royaume-Uni</option>            <option value="177">Russie, Fédération de</option>            <option value="178">Rwanda</option>            <option value="226">Sahara Occidental</option>            <option value="179">Saint-Barthélemy</option>            <option value="180">Saint-Kitts-et-Nevis</option>            <option value="186">Saint-Marin</option>            <option value="182">Saint-Martin</option>            <option value="183">Saint-Pierre-et-Miquelon</option>            <option value="107">Saint-Siege (État de la Cité du Vatican)</option>            <option value="184">Saint-Vincent-et-Les Grenadines</option>            <option value="181">Sainte-Lucie</option>            <option value="194">Salomon, Îles</option>            <option value="185">Samoa</option>            <option value="39">Samoa Américaines</option>            <option value="187">Sao Tomé-et-Principe</option>            <option value="189">Sénégal</option>            <option value="190">Serbie</option>            <option value="191">Seychelles</option>            <option value="192">Sierra Leone</option>            <option value="25">Singapour</option>            <option value="37">Slovaquie</option>            <option value="193">Slovénie</option>            <option value="195">Somalie</option>            <option value="198">Soudan</option>            <option value="197">Sri Lanka</option>            <option value="18">Suède</option>            <option value="19">Suisse</option>            <option value="199">Suriname</option>            <option value="200">Svalbard et Île Jan Mayen</option>            <option value="201">Swaziland</option>            <option value="202">Syrienne</option>            <option value="204">Tadjikistan</option>            <option value="203">Taïwan</option>            <option value="205">Tanzanie</option>            <option value="67">Tchad</option>            <option value="243">Terres Australes Françaises</option>            <option value="206">Thaïlande</option>            <option value="80">Timor oriental</option>            <option value="33">Togo</option>            <option value="207">Tokelau</option>            <option value="208">Tonga</option>            <option value="209">Trinité-et-Tobago</option>            <option value="210">Tunisie</option>            <option value="212">Turkménistan</option>            <option value="213">Turks et Caiques, Îles</option>            <option value="211">Turquie</option>            <option value="214">Tuvalu</option>            <option value="216">Ukraine</option>            <option value="218">Uruguay</option>            <option value="220">Vanuatu</option>            <option value="221">Venezuela</option>            <option value="222">Vietnam</option>            <option value="225">Wallis et Futuna</option>            <option value="227">Yémen</option>            <option value="228">Zambie</option>            <option value="229">Zimbabwe</option></select><span class="select2 select2-container select2-container--prestakit" dir="ltr" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-form_step2_specific_price_sp_id_country-container"><span class="select2-selection__rendered" id="select2-form_step2_specific_price_sp_id_country-container" title="Tous les pays">Tous les pays</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
      </fieldset>
    </div>
    <div class="col-md-3">
      <fieldset class="form-group">
        <label>&nbsp;</label>
        
        <select id="form_step2_specific_price_sp_id_group" name="form[step2][specific_price][sp_id_group]" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true"><option value="">Tous les groupes</option>            <option value="1">Visiteur</option>            <option value="2">Invité</option>            <option value="3">Client</option></select><span class="select2 select2-container select2-container--prestakit" dir="ltr" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-form_step2_specific_price_sp_id_group-container"><span class="select2-selection__rendered" id="select2-form_step2_specific_price_sp_id_group-container" title="Tous les groupes">Tous les groupes</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
      </fieldset>
    </div>
    <div class="col-md-6">
      <fieldset class="form-group">
        <label>Client</label>
        
            
    <span class="twitter-typeahead" style="position: relative; display: inline-block;"><input type="text" id="form_step2_specific_price_sp_id_customer" class="form-control typeahead form_step2_specific_price_sp_id_customer tt-input" placeholder="Tous les clients" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top;"><pre aria-hidden="true" style="position: absolute; visibility: hidden; white-space: pre; font-family: &quot;Open Sans&quot;, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; word-spacing: 0px; letter-spacing: 0px; text-indent: 0px; text-rendering: auto; text-transform: none;"></pre><div class="tt-menu" style="position: absolute; top: 100%; left: 0px; z-index: 100; display: none;"><div class="tt-dataset tt-dataset-1"></div></div></span>
    <ul id="form_step2_specific_price_sp_id_customer-data" class="typeahead-list nostyle col-xs-12"></ul>
    <script>
        $( document ).ready(function() {
            //remove collection item
            $(document).on( 'click', '#form_step2_specific_price_sp_id_customer-data .delete', function(e) {
                e.preventDefault();
                var _this = $(this);

                modalConfirmation.create(translate_javascripts['Are you sure to delete this?'], null, {
                    onContinue: function(){
                        _this.parent().remove();
                    }
                }).show();
            });

            //define source
            this['form_step2_specific_price_sp_id_customer_source'] = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.whitespace,
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                identify: function(obj) {
                    return obj.id_customer;
                },
                remote: {
                    url: 'http://localhost/toncom/admin017sq9cc4/index.php?controller=AdminCustomers&token=2a5f67ec96074c87d06505eb13271494&sf2=1&ajax=1&tab=AdminCustomers&action=searchCustomers&customer_search=%QUERY',
                    cache: false,
                    wildcard: '%QUERY',
                    transform: function(response){
                        if(!response){
                            return [];
                        }
                        return response;
                    }
                }
            });

            //define typeahead
            $('#form_step2_specific_price_sp_id_customer').typeahead({
                limit: 200,
                minLength: 2,
                highlight: true,
                cache: false,
                hint: false,
            }, {
                display: 'fullname_and_email',
                source: this['form_step2_specific_price_sp_id_customer_source'],
                limit: 30,
                templates: {
                    suggestion: function(item){
                        return '<div>'+ item.fullname_and_email +'</div>'
                    }
                }
            }).bind('typeahead:select', function(ev, suggestion) {

                //if collection length is up to limit, return
                if(1 != 0 && $('#form_step2_specific_price_sp_id_customer-data li').length >= 1){
                    return;
                }

                var value = suggestion.id_customer;
                if (suggestion.id_product_attribute) {
                    value = value+','+suggestion.id_product_attribute;
                }

                var html = '<li>';
                html += sprintf('<div class="title col-md-10">%s</div><button type="button" class="btn btn-danger delete"><i class="material-icons">delete</i></button>', suggestion.fullname_and_email);
                html += '<input type="hidden" name="form[step2][specific_price][sp_id_customer][data][]" value="' + value + '" />';
                html += '</li>';
                $('#form_step2_specific_price_sp_id_customer-data').append(html);

            }).bind('typeahead:close', function(ev) {
                $(ev.target).val('');
            });
        });
    </script>

      </fieldset>
    </div>
  </div>
  <div class="row">
    <div id="specific-price-combination-selector" class="col-md-6 hide" style="display: none;">
      <fieldset class="form-group">
        <label>Déclinaisons</label>
        
        <select id="form_step2_specific_price_sp_id_product_attribute" name="form[step2][specific_price][sp_id_product_attribute]" data-action="/toncom/admin017sq9cc4/index.php/combination/product-combinations/1?_token=COadL-ivnZDl1C5pHTryRD-iQ3ieJXkiIY7mH9Vd60E" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true"><option value="">Appliquer à toutes les déclinaisons</option></select><span class="select2 select2-container select2-container--prestakit" dir="ltr" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-form_step2_specific_price_sp_id_product_attribute-container"><span class="select2-selection__rendered" id="select2-form_step2_specific_price_sp_id_product_attribute-container" title="Appliquer à toutes les déclinaisons">Appliquer à toutes les déclinaisons</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
      </fieldset>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-3">
      <fieldset class="form-group">
        <label>Dispo à partir du</label>
        
            <div class="input-group datepicker"><input type="text" class="form-control" id="form_step2_specific_price_sp_from" name="form[step2][specific_price][sp_from]" placeholder="YYYY-MM-DD"><div class="input-group-addon"><i class="material-icons">date_range</i></div></div>
      </fieldset>
    </div>
    <div class="col-md-3">
      <fieldset class="form-group">
        <label>jusqu'au</label>
        
            <div class="input-group datepicker"><input type="text" class="form-control" id="form_step2_specific_price_sp_to" name="form[step2][specific_price][sp_to]" placeholder="YYYY-MM-DD"><div class="input-group-addon"><i class="material-icons">date_range</i></div></div>
      </fieldset>
    </div>
    <div class="col-md-2">
      <fieldset class="form-group">
        <label>À partir de</label>
        
        <div class="input-group">
          <input type="text" id="form_step2_specific_price_sp_from_quantity" name="form[step2][specific_price][sp_from_quantity]" class="form-control" value="1">
          <span class="input-group-addon">Unité(s)</span>
        </div>
      </fieldset>
    </div>
  </div>
  <br>

  <h4><b>Impact sur le prix</b></h4>
  <div class="row">
    <div class="col-md-3">
      <fieldset class="form-group">
        <label>Prix du produit (HT)</label>
        
        <div class="input-group money-type">
                            <span class="input-group-addon">€ </span>
        <input type="text" id="form_step2_specific_price_sp_price" name="form[step2][specific_price][sp_price]" disabled="disabled" class="price form-control">    </div>
      </fieldset>
    </div>
    <div class="col-md-3">
      <fieldset class="form-group">
        <label>&nbsp;</label>
        
        <div class="checkbox">                                        <label><input type="checkbox" id="form_step2_specific_price_leave_bprice" name="form[step2][specific_price][leave_bprice]" value="1" checked="checked">
Garder le prix initial</label>
    </div>
      </fieldset>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-2 col-lg-3">
      <fieldset class="form-group">
        <label>Appliquer une réduction de </label>
        
        <div class="input-group money-type">
                            <span class="input-group-addon">€ </span>
        <input type="text" id="form_step2_specific_price_sp_reduction" name="form[step2][specific_price][sp_reduction]" class="form-control" value="0.000000">    </div>
      </fieldset>
    </div>
    <div class="col-xl-2 col-lg-3">
      <fieldset class="form-group">
        <label>&nbsp;</label>
        
        <select id="form_step2_specific_price_sp_reduction_type" name="form[step2][specific_price][sp_reduction_type]" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true">            <option value="amount">€</option>            <option value="percentage">%</option></select><span class="select2 select2-container select2-container--prestakit" dir="ltr" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-form_step2_specific_price_sp_reduction_type-container"><span class="select2-selection__rendered" id="select2-form_step2_specific_price_sp_reduction_type-container" title="€">€</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
      </fieldset>
    </div>
    <div class="col-xl-2 col-lg-3">
      <fieldset class="form-group">
        <label>&nbsp;</label>
        
        <select id="form_step2_specific_price_sp_reduction_tax" name="form[step2][specific_price][sp_reduction_tax]" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true">            <option value="0">HT</option>            <option value="1" selected="selected">TTC</option></select><span class="select2 select2-container select2-container--prestakit" dir="ltr" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-form_step2_specific_price_sp_reduction_tax-container"><span class="select2-selection__rendered" id="select2-form_step2_specific_price_sp_reduction_tax-container" title="TTC">TTC</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
      </fieldset>
    </div>
  </div>
  <div class="col-md-12 text-xs-right">
    <button type="button" id="form_step2_specific_price_cancel" name="form[step2][specific_price][cancel]" class="btn-tertiary-outline js-cancel btn">Annuler</button>
    <button type="button" id="form_step2_specific_price_save" name="form[step2][specific_price][save]" class="btn-primary-outline js-save btn">Enregistrer</button>
  </div>
  <div class="clearfix"></div>
</div>

                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-12">
                        <h2>
                          Gestion des priorités
                          <span class="help-box" data-toggle="popover" data-content="Parfois un client peut être concerné par plusieurs règles de prix spécifiques à la fois. Les priorités vous permettent de décider quelle règle sera appliquée en premier." data-original-title="" title=""></span>
                        </h2>
                      </div>
                      <div class="col-md-3">
                        <fieldset class="form-group">
                          <label>Priorités</label>
                          
                          <select id="form_step2_specificPricePriority_0" name="form[step2][specificPricePriority_0]" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true">            <option value="id_shop" selected="selected">Boutique</option>            <option value="id_currency">Devise</option>            <option value="id_country">Pays</option>            <option value="id_group">Groupe</option></select><span class="select2 select2-container select2-container--prestakit" dir="ltr" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-form_step2_specificPricePriority_0-container"><span class="select2-selection__rendered" id="select2-form_step2_specificPricePriority_0-container" title="Boutique">Boutique</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                        </fieldset>
                      </div>
                      <div class="col-md-3">
                        <fieldset class="form-group">
                          <label>&nbsp;</label>
                          
                          <select id="form_step2_specificPricePriority_1" name="form[step2][specificPricePriority_1]" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true">            <option value="id_shop">Boutique</option>            <option value="id_currency" selected="selected">Devise</option>            <option value="id_country">Pays</option>            <option value="id_group">Groupe</option></select><span class="select2 select2-container select2-container--prestakit" dir="ltr" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-form_step2_specificPricePriority_1-container"><span class="select2-selection__rendered" id="select2-form_step2_specificPricePriority_1-container" title="Devise">Devise</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                        </fieldset>
                      </div>
                      <div class="col-md-3">
                        <fieldset class="form-group">
                          <label>&nbsp;</label>
                          
                          <select id="form_step2_specificPricePriority_2" name="form[step2][specificPricePriority_2]" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true">            <option value="id_shop">Boutique</option>            <option value="id_currency">Devise</option>            <option value="id_country" selected="selected">Pays</option>            <option value="id_group">Groupe</option></select><span class="select2 select2-container select2-container--prestakit" dir="ltr" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-form_step2_specificPricePriority_2-container"><span class="select2-selection__rendered" id="select2-form_step2_specificPricePriority_2-container" title="Pays">Pays</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                        </fieldset>
                      </div>
                      <div class="col-md-3">
                        <fieldset class="form-group">
                          <label>&nbsp;</label>
                          
                          <select id="form_step2_specificPricePriority_3" name="form[step2][specificPricePriority_3]" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true">            <option value="id_shop">Boutique</option>            <option value="id_currency">Devise</option>            <option value="id_country">Pays</option>            <option value="id_group" selected="selected">Groupe</option></select><span class="select2 select2-container select2-container--prestakit" dir="ltr" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-form_step2_specificPricePriority_3-container"><span class="select2-selection__rendered" id="select2-form_step2_specificPricePriority_3-container" title="Groupe">Groupe</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                        </fieldset>
                      </div>
                      <div class="col-md-12">
                        <div class="checkbox">                                        <label><input type="checkbox" id="form_step2_specificPricePriorityToAll" name="form[step2][specificPricePriorityToAll]" value="1">
Appliquer à tous les produits </label>
    </div>
                      </div>
                    </div>
                  </div>

                </div>
<?php
/**
* Plugin Name: Connecteur Anyleads
* Plugin URI: https://floriancourgey.com/#contact
* Description: Ajoute les prospects a une liste anyleads en provenance du contact form
* Version: 1.0
* Author: Florian Courgey
* Author URI: https://floriancourgey.com/
**/

/**
 * CALLBACK
 */
function callback($arg){
  if(!is_array($arg)){
    return;
  }

  try{
    $apiKey = get_option('my_option_name')['api_key'];
    $listeId = get_option('my_option_name')['list_id'];
    $url = 'https://api.anyleads.com/v1/your-data/create-contact';

    $post = [
      'api_key' => $apiKey,
      'data_create' => [
        'user_email' => $arg['email']['value'],
        'user_list_id' => $listeId,
        'user_city' => 'New York',
        'user_first_name' =>'NAME',
        'user_last_name' => '',
      ],
    ];

    $result = wp_remote_post($url, [
      'body' => json_encode($post),
      'headers'   => ['Content-Type' => 'application/json; charset=utf-8'],
    ]);
  } catch (\Exception $e){
    return;
  }

  // wp_mail('florian@floriancourgey.com', 'test callback', "working ??? <pre>".print_r($post, true).print_r($result, true).'</pre>');
}
add_action('com.floriancourgey.anyleads.callback', 'callback');

/**
 * MENU
 */
 class MySettingsPage {
   private $options;

   public function __construct() {
       add_action('admin_menu', array($this, 'add_plugin_page'));
       add_action('admin_init', array($this, 'page_init'));
   }

   /**
    * Add options page
    */
   public function add_plugin_page() {
       // This page will be under "Settings"
       add_options_page(
           'Settings Admin',
           'Anyleads (by fc.com)',
           'manage_options',
           'my-setting-admin',
           array($this, 'create_admin_page')
      );
   }

   /**
    * Options page callback
    */
   public function create_admin_page() {
       // Set class property
       $this->options = get_option('my_option_name');
       ?>
       <div class="wrap">
           <h1>Settings Anyleads connector <small>by floriancourgey.com</small> </h1>
           <form method="post" action="options.php">
           <?php
               // This prints out all hidden setting fields
               settings_fields('my_option_group');
               do_settings_sections('my-setting-admin');
               submit_button();
           ?>
           </form>
       </div>
       <?php
   }

   /**
    * Register and add settings
    */
   public function page_init() {
       register_setting(
           'my_option_group', // Option group
           'my_option_name', // Option name
           array($this, 'sanitize') // Sanitize
      );

       add_settings_section(
           'setting_section_id', // ID
           'Connection', // Title
           array($this, 'print_section_info'), // Callback
           'my-setting-admin' // Page
      );

     //  add_settings_field(
     //      'url',
     //      'URL endpoint',
     //      array($this, 'url_callback'),
     //      'my-setting-admin',
     //      'setting_section_id'
     // );

       add_settings_field(
           'api_key', // ID
           'API key', // Title
           array($this, 'api_key_callback'), // Callback
           'my-setting-admin', // Page
           'setting_section_id' // Section
      );

       add_settings_field(
           'list_id',
           'List id',
           array($this, 'list_id_callback'),
           'my-setting-admin',
           'setting_section_id'
      );
   }

   /**
    * Sanitize each setting field as needed
    *
    * @param array $input Contains all settings fields as array keys
    */
   public function sanitize($input) {
       $new_input = [];
       foreach ($input as $key => $value) {
         switch($key){
           case 'xxx':
           $new_input[$key] = 'xxx';
            break;
          default:
            $new_input[$key] = sanitize_text_field($value);
            break;
         }
       }
       return $new_input;
   }

   public function print_section_info() {
       // print 'Enter your settings below:';
   }

   public function url_callback() {
     printf(
         '<input type="text" id="url" name="my_option_name[url]" value="%s" />',
         isset($this->options['url']) ? esc_attr($this->options['url']) : ''
    );
   }
   public function api_key_callback() {
     printf(
         '<input type="text" id="api_key" name="my_option_name[api_key]" value="%s" />',
         isset($this->options['api_key']) ? esc_attr($this->options['api_key']) : ''
    );
   }
   public function list_id_callback() {
     printf(
         '<input type="text" id="list_id" name="my_option_name[list_id]" value="%s" />',
         isset($this->options['list_id']) ? esc_attr($this->options['list_id']) : ''
    );
   }
 }

 if(is_admin())
     $my_settings_page = new MySettingsPage();

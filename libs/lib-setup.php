<?php

/**
 * Initialize environment (settings/profiles)
 *
 * @since	2.5.0.1
 * @return  void
 */
function gde_setup() {
	// check for sufficient php version (minimum supports json_encode)
	if ( ! ( phpversion() >= '5.2.0' ) ) {
		wp_die( 'Your server is running PHP version ' . phpversion() . ' but this plugin requires at least 5.2.0' );
	}
	
	if ( GDE_DX_LOGGING > 0 ) {
		gde_dx_log("Dx log manually enabled in functions.php");
	}
	gde_dx_log("Activating...");
	
	// gather environment info
	$baseurl = gde_base_url();
	$default_lang = gde_get_locale();
	$pdata = gde_get_plugin_data();
	$apikey = gde_get_api_key( $pdata['Version'] );
	if ( empty( $apikey ) ) {
		gde_dx_log("Failed to set API key");
	} else {
		gde_dx_log("API Key: $apikey");
	}
	
	if ( is_multisite() ) {
		// define multisite "global" options
		$globalopts = array(
			'file_maxsize'			=>	'12',
			'beta_check'			=>	'yes',
			'api_key'				=>	$apikey
		);
		
		if ( ! $gdeglobals = get_site_option( 'gde_globals' ) ) {
			gde_dx_log("Writing multisite global options");
			update_site_option( 'gde_globals', $globalopts );
		}
	}
	
	// create/update profile db, if necessary
	if ( ! gde_db_tables() ) {
		gde_dx_log("Table creation failed; setup halted");
		wp_die( __("Setup wasn't able to create the required tables. Please reactivate GDE or perform a clean installation.", 'gde') );
	}
	
	// define default options (does not include multisite yet)
	$defopts = array(
		'ed_disable'			=>	'no',
		'ed_extend_upload'		=>	'yes',
		'ed_embed_sc'			=>	'yes',
		'file_maxsize'			=>	'12',
		'error_check'			=>	'yes',
		'error_display'			=>	'yes',
		'error_log'				=>	'no',
		'beta_check'			=>	'yes',
		'ga_enable'				=>	'no',
		'ga_category'			=>	$pdata['Name'],
		'ga_label'				=>	'url',
		'api_key'				=>	$apikey
	);
	
	// define default profile(s)
	$defpros = array(
		"default" => array(
			"desc"	=>	__('This is the default profile, used when no profile is specified.', 'gde'),
			"viewer"			=>	'standard',
			"default_width"		=>	'100%',
			"default_height"	=>	'500px',
			"tb_mobile"			=>	'default',
			"tb_flags"			=>	'',
			"tb_fullscr"		=>	'default',
			"tb_fullwin"		=>	'new',
			"tb_print"			=>	'no',
			"vw_bgcolor"		=>	'#EBEBEB',
			"vw_pbcolor"		=>	'#DADADA',
			"vw_css"			=>	'',
			"vw_flags"			=>	'',
			"language"			=>	$default_lang,
			"base_url"			=>	$baseurl,
			"link_show"			=>	'all',
			"link_mask"			=>	'no',
			"link_block"		=>	'no',
			"link_text"			=>	__('Download', 'gde') . ' (%TYPE, %SIZE)',
			"link_pos"			=>	'below',
			"link_force"		=>	'no',
			"cache"				=>	'on'
		),
		"max-doc-security" => array(
			"desc"	=>	__('Hide document location and text selection, prevent downloads', 'gde'),
			"viewer"			=>	'enhanced',
			"default_width"		=>	'100%',
			"default_height"	=>	'500px',
			"tb_mobile"			=>	'default',
			"tb_flags"			=>	'',
			"tb_fullscr"		=>	'viewer',
			"tb_fullwin"		=>	'new',
			"tb_print"			=>	'no',
			"vw_bgcolor"		=>	'#EBEBEB',
			"vw_pbcolor"		=>	'#DADADA',
			"vw_css"			=>	'',
			"vw_flags"			=>	'',
			"language"			=>	$default_lang,
			"base_url"			=>	$baseurl,
			"link_show"			=>	'none',
			"link_mask"			=>	'no',
			"link_block"		=>	'yes',
			"link_text"			=>	'',
			"link_pos"			=>	'below',
			"link_force"		=>	'no',
			"cache"				=>	'on'
		),
		"dark" => array(
			"desc"	=>	__('Dark-colored theme, example of custom CSS option', 'gde'),
			"viewer"			=>	'enhanced',
			"default_width"		=>	'100%',
			"default_height"	=>	'500px',
			"tb_mobile"			=>	'default',
			"tb_flags"			=>	'',
			"tb_fullscr"		=>	'viewer',
			"tb_fullwin"		=>	'new',
			"tb_print"			=>	'no',
			"vw_bgcolor"		=>	'',
			"vw_pbcolor"		=>	'',
			"vw_css"			=>	GDE_PLUGIN_URL . 'css/gde-dark.css',
			"vw_flags"			=>	'',
			"language"			=>	$default_lang,
			"base_url"			=>	$baseurl,
			"link_show"			=>	'all',
			"link_mask"			=>	'no',
			"link_block"		=>	'no',
			"link_text"			=>	__('Download', 'gde') . ' (%TYPE, %SIZE)',
			"link_pos"			=>	'below',
			"link_force"		=>	'no',
			"cache"				=>	'on'
		)
	);
	
	$upgrade = false;
	
	if ( ! $gdeoptions = get_option('gde_options') ) {
		// write default options
		gde_dx_log("Writing default options");
		update_option('gde_options', $defopts);
	} else {
		// check or upgrade options
		gde_dx_log("Options already exist");
		foreach ( $defopts as $k => $v ) {
			if ( ! array_key_exists( $k, $gdeoptions ) ) {
				$gdeoptions[$k] = $v;
				//gde_dx_log("New option $k added");
				$updated = true;
			}
			if ( isset( $updated ) ) {
				update_option('gde_options', $defopts);
			}
		}
		
		// set API key if empty (ie., failed on earlier attempt or first upgrade)
		if ( empty( $gdeoptions['api_key'] ) && ! empty( $apikey ) ) {
			$gdeoptions['api_key'] = $apikey;
			gde_dx_log("Updated API Key");
			update_option( 'gde_options', $gdeoptions );
		}
		
		if ( isset( $gdeoptions['default_width'] ) ) {
			// old (pre-2.5) settings exist - convert to profile
			gde_upgrade( $gdeoptions, $defopts, $defpros['default'] );
			$upgrade = true;
		}
	}
	
	// check for existence of default profile (re-activation?)
	if ( ! gde_get_profiles( 1 ) || $upgrade ) {
		// new activation - write profile(s)
		foreach ( $defpros as $key => $prodata ) {
			if ( $key == "default" ) {
				if ( $upgrade ) {
					// upgrade conversion handled this - skip
					continue;
				} else {
					// default profile is always ID 1
					$id = 1;
				}
			} else {
				$id = null;	// assign next id
			}
			
			// prepare profile
			$desc = $prodata['desc'];
			unset( $prodata['desc'] );
			
			// does profile already exist (check for new options)
			if ( $id !== null ) {
				if ( $current = gde_get_profiles( $id ) ) {
					$changed = false;
					foreach ( $prodata as $k => $v ) {
						if ( ! array_key_exists( $k, $current ) ) {
							$current[$k] = $v;
							$changed = true;
							gde_dx_log("Setting added: $k");
						}
					}
					foreach ( $current as $k => $v ) {
						if ( ! array_key_exists( $k, $prodata ) ) {
							unset( $current[$k] );
							$changed = true;
							gde_dx_log("Setting removed: $k");
						}
					}
				}
			}
			
			if ( isset( $current ) && $current && isset( $changed ) && $changed ) {
				// updating current profile
				$data = serialize( $current );
				$profile = array( $key, $desc, $data );
				if ( gde_write_profile( $profile, $id, true ) < 1 ) {
					gde_dx_log("Failed to update profile '$key'");
				}
			} elseif ( ! isset( $changed ) ) {
				// write new profile
				$data = serialize( $prodata );
				$profile = array( $key, $desc, $data );
				if ( gde_write_profile( $profile, $id ) < 1 ) {
					gde_dx_log("Failed to write profile '$key'");
				}
			}
		}
	} else {
		gde_dx_log("Profiles already exist - skip creation");
	}
	
	gde_dx_log("Activation complete.");
}

/**
 * Upgrade settings to profiles (for pre-2.5 installs)
 *
 * @since	2.5.0.1
 * @return  void
 */
function gde_upgrade( $gdeoptions, $defopts, $defaults ) {
	gde_dx_log("Old settings found - upgrade initiated");
	
	// reformat some settings
	if ( $gdeoptions['disable_proxy'] == "no" ) {
		$viewer = "enhanced";
	} else {
		$viewer = "standard";
	}
	if ( $gdeoptions['disable_caching'] == "no" ) {
		$cache = "on";
	} else {
		$cache = "off";
	}
	if ( strstr( $gdeoptions['restrict_tb'], 'm') !== false ) {
		$mobile = "always";
		$gdeoptions['restrict_tb'] = str_replace( "m", "", $gdeoptions['restrict_tb'] );
	} else {
		$mobile = "default";
	}
	if ( $gdeoptions['show_dl'] == "no" ) {
		$link_show = "none";
	} elseif ( $gdeoptions['restrict_dl'] == "yes" ) {
		$link_show = "users";
	} else {
		$link_show = "all";
	}
	if ( $gdeoptions['link_func'] == "force-mask" ) {
		$link_force = "yes";
		$link_mask = "yes";
	} elseif ( $gdeoptions['link_func'] == "force" ) {
		$link_force = "yes";
		$link_mask = "no";
	} else {
		$link_force = "no";
		$link_mask = "no";
	}
	
	// define new default profile - take default profile and override from old settings
	$profile = $defaults;
	$profile['viewer'] = $viewer;
	
	// height and width are now combined with their type
	$profile['default_width'] = $gdeoptions['default_width'].$gdeoptions['width_type'];
	$profile['default_width'] = str_replace( "pc", "%", $profile['default_width'] );
	$profile['default_height'] = $gdeoptions['default_height'].$gdeoptions['height_type'];
	$profile['default_height'] = str_replace( "pc", "%", $profile['default_height'] );
	
	$profile['tb_mobile'] = $mobile;
	$profile['tb_flags'] = $gdeoptions['restrict_tb'];
	$profile['language'] = $gdeoptions['default_lang'];
	$profile['base_url'] = $gdeoptions['base_url'];
	$profile['link_show'] = $link_show;
	$profile['link_mask'] = $link_mask;
	
	// download link replacements changed
	$profile['link_text'] = $gdeoptions['link_text'];
	$profile['link_text'] = str_replace( "%FN", "%FILE", $profile['link_text'] );
	$profile['link_text'] = str_replace( "%FT", "%TYPE", $profile['link_text'] );
	$profile['link_text'] = str_replace( "%FS", "%SIZE", $profile['link_text'] );
	
	$profile['link_pos'] = $gdeoptions['link_pos'];
	$profile['link_force'] = $link_force;
	$profile['cache'] = $cache;
	
	$default = array( 
		'default',
		__('This is the default profile, used when no profile is specified.', 'gde'),
		serialize($profile)
	);
	
	if ( gde_write_profile( $default, 1 ) > 0 ) {	// default profile is always ID 1
		// profile conversion successful; write new settings array
		$oldsets = print_r($gdeoptions, true);
		$newprofile = serialize($profile);
		//gde_dx_log("Converting old settings to default profile: \n\n $oldsets \n\n $newprofile \n\n");
		
		if ($gdeoptions['bypass_check'] == "yes") {
			$errorchk = "no";
		} else {
			$errorchk = "yes";
		}
		if ($gdeoptions['suppress_beta'] == "yes") {
			$betachk = "no";
		} else {
			$betachk = "yes";
		}
		if ($gdeoptions['enable_ga'] == "yes") {
			$ga = "compat";
		} else {
			$ga = "no";
		}
		
		// build new settings - start with default and overwrite
		$newopts = $defopts;
		
		$newopts['ed_disable'] = $gdeoptions['disable_editor'];
		if ( $newopts['ed_disable'] == "yes" ) {
			$newopts['ed_extend_upload'] = "yes";
			$newopts['ed_embed_sc'] = "yes";
		} else {
			$newopts['ed_extend_upload'] = $gdeoptions['ed_extend_upload'];
			$newopts['ed_embed_sc'] = $gdeoptions['ed_embed_sc'];
		}
		$newopts['error_check'] = $errorchk;
		$newopts['beta_check'] = $betachk;
		$newopts['ga_enable'] = $ga;
		$newopts['api_key'] = $gdeoptions['api_key'];
		
		update_option('gde_options', $newopts);
		
		gde_dx_log("Old settings converted");
	}
}

/**
 * Fetch Beta API Key
 *
 * @since   2.5.0.1
 * @return  string Stored or newly generated API key, or blank value.
 * @note	This should only run once on activation so no transient is necessary
 */
function gde_get_api_key( $ver ) {
	global $current_user;
	
	if ( is_multisite() ) {
		$gdeglobals = get_site_option( 'gde_globals' );
		$api = $gdeglobals['api_key'];
	} else {
		$gdeoptions = get_option( 'gde_options' );
		$api = $gdeoptions['api_key'];
	}
	
	if ( ! empty ( $api ) ) {
		gde_dx_log("API key already set");
		return $api;
	} else {
		gde_dx_log("Requesting new API key");
		get_currentuserinfo();
		$keystring = $current_user->user_login . $current_user->user_email;
		if ( empty($keystring) ) {
			$keystring = "John146JesussaidtohimIamthewaythetruthandthelifenoonecomestotheFatherexceptthroughMe";
		}
		$keystring = str_shuffle( preg_replace("/[^A-Za-z0-9]/", "", $keystring ) );
		
		// attempt get new key
		$api_url = GDE_BETA_API . "keygen/$keystring?p=gde&v=" . $ver;
		$response = wp_remote_get( $api_url );
		
		if ( is_wp_error( $response ) ) {
			$error = $result->get_error_message();
			gde_dx_log("API Error: " . $error);
			// can't get response
			return '';
		} else {
			if ( $json = json_decode( wp_remote_retrieve_body( $response ) ) ) {
				if ( isset( $json->api_key ) ) {
					$key = $json->api_key;
				}
				if ( ! empty( $key ) ) {
					return $key;
				} else {
					gde_dx_log("API returned empty response");
					// empty value response
					return '';
				}
			} else {
				// invalid response
				gde_dx_log("API returned invalid response");
				return '';
			}
		}
	}
}

/**
 * Create/update database table to store profile data
 *
 * @since   2.5.0.1
 * @return  bool Whether or not table creation/update was successful
 */
function gde_db_tables() {
	global $wpdb;
	
	// attempt to trap table creation failures
	$fails = 0;
	
	// check for missing required tables (clear db version)
	$table = $wpdb->prefix . 'gde_profiles';
	if ($wpdb->get_var( "SHOW TABLES LIKE '$table'" ) !== $table) {
		//gde_dx_log("profiles db failed health check");
		delete_site_option( 'gde_db_version' );
	}
	
	$table = $wpdb->prefix . 'gde_secure';
	if ($wpdb->get_var( "SHOW TABLES LIKE '$table'" ) !== $table) {
		//gde_dx_log("securedoc db failed health check");
		delete_site_option( 'gde_db_version' );
	}
	
	$db_ver_installed = get_site_option( 'gde_db_version', 0 );
	gde_dx_log("Installed DB ver: $db_ver_installed; This DB ver: " . GDE_DB_VER );
	if ( version_compare( GDE_DB_VER, $db_ver_installed, ">" ) ) {
		// install or upgrade profile table
		$table = $wpdb->prefix . 'gde_profiles';
	
		$sql = "CREATE TABLE " . $table . " (
		  profile_id mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
		  profile_name varchar(64) NOT NULL,
		  profile_desc varchar(255) NULL,
		  profile_data longtext NOT NULL,
		  UNIQUE KEY (profile_id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8; ";

		if ( isset( $sql ) ) {
			// write table or update to database
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);
			
			if ($wpdb->get_var( "SHOW TABLES LIKE '$table'" ) == $table ) {
				gde_dx_log("Profile table create/update successful");
			} else {
				gde_dx_log("Profile table create/update failed");
				$fails++;
			}
		}
		
		// install or upgrade securedoc table
		$table = $wpdb->prefix . 'gde_secure';
		$sql = "CREATE TABLE " . $table . " (
		  code varchar(10) NOT NULL,
		  url varchar(255) NOT NULL,
		  murl varchar(100) NOT NULL,
		  stamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  autoexpire enum('Y','N') NOT NULL DEFAULT 'N',
		  UNIQUE KEY code (code)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8; ";
		
		if ( isset( $sql ) ) {
			// write table or update to database
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) == $table ) {
				gde_dx_log("Secure doc table create/update successful");
			} else {
				gde_dx_log("Secure doc table create/update failed");
				$fails++;
			}
		}
	} else {
		gde_dx_log("Tables OK, nothing to do");
	}
	
	if ( $fails > 0 ) {
		delete_site_option( 'gde_db_version' );
		return false;
	} else {
		update_site_option( 'gde_db_version', GDE_DB_VER );
		return true;
	}
}

?>
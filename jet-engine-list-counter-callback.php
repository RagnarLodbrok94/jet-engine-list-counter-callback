<?php
/**
 * Plugin Name: JetEngine - Listing’s counter
 * Plugin URI: #
 * Description: Adds a new callback to the Dynamic Field widget that returns the number of the current element of the list.
 * Version:     1.0.0
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

use Jet_Engine\Query_Builder\Manager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function jet_engine_list_counter( $value, $query_id ) {
	return \Jet_Engine_List_Counter::get_index_of_listing_item( $value, $query_id );
}

class Jet_Engine_List_Counter {
	private static $counters = [];

	public function __construct() {
		add_filter( 'jet-engine/listings/allowed-callbacks', array( $this, 'list_counter_callback' ), 10, 2 );
		add_filter( 'jet-engine/listing/dynamic-field/callback-args', array(
			$this,
			'list_counter_callback_args'
		), 10, 3 );
		add_filter( 'jet-engine/listings/allowed-callbacks-args', array(
			$this,
			'list_counter_callback_controls'
		) );
	}


	public function list_counter_callback( $callbacks ) {
		$callbacks['jet_engine_list_counter'] = __( 'Listing’s counter', 'jet-engine-list-counter-callback' );

		return $callbacks;
	}

	public static function get_index_of_listing_item( $value, $query_id ) {
		$query = Manager::instance()->get_query_by_id( $query_id );

		if ( $query !== false ) {
			$arr = &self::$counters;

			if ( array_key_exists( $query_id, $arr ) ) {
				$arr[ $query_id ] = ++ $arr[ $query_id ];
			} else {
				$arr[ $query_id ] = 1;
			}

			if ( $query->get_items_page_count() < $arr[ $query_id ] ) {
				$arr[ $query_id ] = 1;
			}

			return $arr[ $query_id ];
		}
	}

	public function list_counter_callback_args( $args, $callback, $settings = array() ) {
		if ( 'jet_engine_list_counter' === $callback ) {
			$args[] = isset( $settings['list_counter'] ) ? $settings['list_counter'] : '';
		}

		return $args;
	}

	public function list_counter_callback_controls( $args = array() ) {
		$args['list_counter'] = array(
			'label'       => __( 'Enter query', 'jet-engine-list-counter-callback' ),
			'type'        => 'select',
			'description' => esc_html__( 'Select the query to use', 'jet_engine_list_counter' ),
			'options'     => Manager::instance()->get_queries_for_options(),
			'condition'   => array(
				'dynamic_field_filter' => 'yes',
				'filter_callback'      => array( 'jet_engine_list_counter' ),
			),
		);

		return $args;
	}
}

new Jet_Engine_List_Counter();
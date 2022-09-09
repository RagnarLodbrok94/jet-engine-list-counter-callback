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

function jet_engine_list_counter( $value, $query_id, $condition ) {
	return \Jet_Engine_List_Counter::get_index_of_list_item( $value, $query_id, $condition );
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
		add_action(
			'jet-engine/query-builder/query/before-get-items',
			function ( $query ) {

				if ( $query === false ) {
					return 1;
				}

				$current_items_page = $query->get_current_items_page();
				$items_per_page     = $query->get_items_per_page();

				self::$counters[ $query->id ] = ( -- $current_items_page ) * $items_per_page;
			} );
	}

	public function list_counter_callback( $callbacks ) {
		$callbacks['jet_engine_list_counter'] = __( 'Listing’s counter', 'jet-engine-list-counter-callback' );

		return $callbacks;
	}

	public static function  get_index_of_list_item( $value, $query_id, $condition ) {
		$query = Manager::instance()->get_query_by_id( $query_id );

		if ( $query === false ) {
			return 1;
		}

		$counters_item = &self::$counters[ $query_id ];

		if ( $counters_item === null ) {
			return 1;
		}

		if ( $condition === 'yes' ) {
			return $counters_item;
		}

		return ++ $counters_item;
	}

	public function list_counter_callback_args( $args, $callback, $settings = array() ) {
		if ( 'jet_engine_list_counter' === $callback ) {
			$args[] = $settings['list_counter_query'] ?? '';
			$args[] = $settings['list_counter_parent_count'] ?? '';
		}

		return $args;
	}

	public function list_counter_callback_controls( $args = array() ) {
		$args['list_counter_query'] = array(
			'label'       => __( 'Select query', 'jet-engine-list-counter-callback' ),
			'type'        => 'select',
			'description' => esc_html__( 'Select a query to count', 'jet_engine_list_counter' ),
			'options'     => Manager::instance()->get_queries_for_options(),
			'condition'   => array(
				'dynamic_field_filter' => 'yes',
				'filter_callback'      => array( 'jet_engine_list_counter' ),
			),
		);

		$args['list_counter_parent_count'] = array(
			'label'       => __( 'Parent counter', 'jet-engine-list-counter-callback' ),
			'type'        => 'switcher',
			'description' => esc_html__( 'If you need to create a nested list (like 1.1, 1.2, 1.3, ...) that will inherit the parent counter.', 'jet_engine_list_counter' ),
			'condition'   => array(
				'dynamic_field_filter' => 'yes',
				'filter_callback'      => array( 'jet_engine_list_counter' ),
			),
		);

		return $args;
	}
}

new Jet_Engine_List_Counter();